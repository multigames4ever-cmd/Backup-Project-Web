<?php
date_default_timezone_set('Asia/Manila');
$beanStackManilaNow = new DateTimeImmutable('now', new DateTimeZone('Asia/Manila'));
$beanStackGreetingHour = (int)$beanStackManilaNow->format('G');
$beanStackGreeting = $beanStackGreetingHour < 12 ? 'morning' : ($beanStackGreetingHour < 18 ? 'afternoon' : 'evening');
require_once 'config.php';
require_login();

// Fallback audit logger: config.php may not define this helper in some installations.
// Keep audit logging failures from interrupting a successful stock transaction.
if (!function_exists('write_audit_log')) {
    function write_audit_log(PDO $pdo, string $action, string $details): void {
        try {
            $stmt = $pdo->prepare('INSERT INTO audit_logs (user_id, action, details, created_at) VALUES (?, ?, ?, NOW())');
            $stmt->execute([$_SESSION['user_id'] ?? null, $action, $details]);
        } catch (Throwable $ignored) {
            // Inventory updates should still succeed if the audit table is unavailable.
        }
    }
}

// Ensure message read-state exists even on databases imported from older BeanStack SQL dumps.
try {
    $readColumn = $pdo->query("SHOW COLUMNS FROM shift_handover_notes LIKE 'is_read'")->fetch(PDO::FETCH_ASSOC);
    if (!$readColumn) {
        $pdo->exec("ALTER TABLE shift_handover_notes ADD COLUMN is_read TINYINT(1) NOT NULL DEFAULT 0");
    }
} catch (Throwable $ignored) {
    // Keep the app usable if the DB account cannot perform this optional schema migration.
}

$page = $_GET['page'] ?? 'dashboard';
// Stock movement history is now part of Reports; keep old links working.
if ($page === 'transactions') $page = 'reports';
$allowed = ['dashboard','ingredients','transactions','alerts','reports','users','settings','audit_logs'];
if (!in_array($page, $allowed, true)) $page = 'dashboard';
if (in_array($page, ['users','audit_logs'], true)) require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'save_product') {
            require_admin();
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $category = ($_POST['category_id'] ?? '') !== '' ? (int)$_POST['category_id'] : null;
            $unit = trim($_POST['unit'] ?? 'pcs');
            $minimum = max(0, (float)($_POST['minimum_stock'] ?? 0));
            $cost = max(0, (float)($_POST['unit_cost'] ?? 0));
            if ($name === '') throw new RuntimeException('Product name is required.');
            $adjustedStock = max(0, (float)($_POST['adjusted_stock'] ?? 0));
            if ($id) {
                require_admin();
                $pdo->beginTransaction();
                $lock = $pdo->prepare("SELECT current_stock FROM ingredients WHERE id=? FOR UPDATE");
                $lock->execute([$id]);
                $oldStockValue = $lock->fetchColumn();
                if ($oldStockValue === false) throw new RuntimeException('Ingredient not found.');
                $oldStock = (float)$oldStockValue;
                $stmt = $pdo->prepare("UPDATE ingredients SET name=?,category_id=?,unit=?,current_stock=?,minimum_stock=?,unit_cost=? WHERE id=?");
                $stmt->execute([$name,$category,$unit,$adjustedStock,$minimum,$cost,$id]);
                // Allow editing the expiration date of the earliest active batch from the Edit Product modal.
                if (array_key_exists('edit_expiration_date', $_POST)) {
                    $editExpiration = trim($_POST['edit_expiration_date'] ?? '');
                    if ($editExpiration !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $editExpiration)) {
                        throw new RuntimeException('Enter a valid expiration date.');
                    }
                    $activeBatch = $pdo->prepare("SELECT id FROM stock_batches WHERE ingredient_id=? AND remaining_quantity>0 ORDER BY (expiration_date IS NULL), expiration_date, received_at, id LIMIT 1 FOR UPDATE");
                    $activeBatch->execute([$id]);
                    $activeBatchId = $activeBatch->fetchColumn();
                    if ($activeBatchId !== false) {
                        $pdo->prepare("UPDATE stock_batches SET expiration_date=? WHERE id=?")
                            ->execute([$editExpiration !== '' ? $editExpiration : null, $activeBatchId]);
                    } elseif ($adjustedStock > 0 && $editExpiration !== '') {
                        // If existing stock has no batch record yet, create one to track its expiration.
                        $pdo->prepare("INSERT INTO stock_batches (ingredient_id,received_quantity,remaining_quantity,expiration_date,received_at,received_by) VALUES (?,?,?,?,NOW(),?)")
                            ->execute([$id,$adjustedStock,$adjustedStock,$editExpiration,$_SESSION['user_id']]);
                    }
                }
                if (abs($adjustedStock - $oldStock) > 0.00001) {
                    $difference = abs($adjustedStock - $oldStock);
                    $direction = $adjustedStock > $oldStock ? 'increase' : 'decrease';
                    $note = 'Stock Adjustment (' . $direction . '; previous stock: ' . $oldStock . '; actual count: ' . $adjustedStock . ').';
                    $pdo->prepare("INSERT INTO inventory_transactions (ingredient_id,user_id,transaction_type,quantity,remarks) VALUES (?,?,?,?,?)")
                        ->execute([$id,$_SESSION['user_id'],'Adjustment',$difference,$note]);
                }
                $pdo->commit();
            } else {
                // New ingredients start at zero stock; use Stock In to add quantity.
                $stmt = $pdo->prepare("INSERT INTO ingredients (name,category_id,unit,current_stock,minimum_stock,unit_cost) VALUES (?,?,?,?,?,?)");
                $stmt->execute([$name,$category,$unit,0,$minimum,$cost]);
            }
            write_audit_log($pdo, $id ? 'Product Updated' : 'Product Created', 'Product: ' . $name . ' (ID: ' . ($id ?: (int)$pdo->lastInsertId()) . ').');
            flash($id ? 'Ingredient updated.' : 'Ingredient added.');
            redirect('index.php?page=ingredients');
        }
        if ($action === 'delete_product') {
            require_admin();
            $id = (int)$_POST['id'];
            $check = $pdo->prepare("SELECT COUNT(*) FROM inventory_transactions WHERE ingredient_id=?");
            $check->execute([$id]);
            if ((int)$check->fetchColumn() > 0) throw new RuntimeException('This item has transaction history and cannot be deleted. You can keep it in the inventory instead.');
            $ingredientstmt = $pdo->prepare('SELECT name FROM ingredients WHERE id=?');
            $ingredientstmt->execute([$id]);
            $deletedProduct = $ingredientstmt->fetch();
            $pdo->prepare("DELETE FROM ingredients WHERE id=?")->execute([$id]);
            write_audit_log($pdo, 'Product Deleted', 'Product: ' . ($deletedProduct['name'] ?? 'Unknown') . ' (ID: ' . $id . ').');
            flash('Ingredient deleted.');
            redirect('index.php?page=ingredients');
        }
        if ($action === 'transaction') {
            $productId = (int)($_POST['product_id'] ?? 0);
            $type = $_POST['transaction_type'] ?? '';
            if ($type === 'Stock Out' && ($_POST['remove_stock_reason'] ?? '') === 'waste') { $type = 'Waste'; }
            $quantity = (float)($_POST['quantity'] ?? 0);
            $remarks = trim($_POST['remarks'] ?? '');
            $validTypes = ['Stock In','Stock Out','Waste','Stock Adjustment'];
            if (!in_array($type, $validTypes, true) || $quantity < 0 || ($type !== 'Stock Adjustment' && $quantity <= 0)) {
                throw new RuntimeException('Enter a valid quantity. Add Stock, Remove Stock, and Waste must be greater than zero.');
            }
            if ($type === 'Stock Adjustment') {
                require_admin();
            }
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("SELECT * FROM ingredients WHERE id=? FOR UPDATE");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();
            if (!$product) throw new RuntimeException('Ingredient not found.');
            $oldStock = (float)$product['current_stock'];
            if ($type === 'Stock Adjustment') {
                $newStock = $quantity;
                $loggedQuantity = abs($newStock - $oldStock);
                if ($loggedQuantity == 0) throw new RuntimeException('The counted stock matches the current stock; no adjustment is needed.');
                $direction = $newStock > $oldStock ? 'increase' : 'decrease';
                $remarks = 'Stock Adjustment (' . $direction . '; actual count: ' . $newStock . ').' . ($remarks !== '' ? ' ' . $remarks : '');
            } elseif ($type === 'Stock In') {
                $newStock = $oldStock + $quantity;
                $loggedQuantity = $quantity;
                $expirationDate = trim($_POST['expiration_date'] ?? '');
                if ($expirationDate !== '' && !preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $expirationDate)) {
                    throw new RuntimeException('Enter a valid expiration date.');
                }
                 $pdo->prepare("INSERT INTO stock_batches (ingredient_id,received_quantity,remaining_quantity,expiration_date,received_at,received_by) VALUES (?,?,?,?,NOW(),?)")
                    ->execute([$productId,$quantity,$quantity,$expirationDate !== '' ? $expirationDate : null,$_SESSION['user_id']]);
            } else {
                if ($quantity > $oldStock) throw new RuntimeException('Not enough stock available for this transaction.');
                $newStock = $oldStock - $quantity;
                $loggedQuantity = $quantity;
                // Reduce tracked batches in earliest-expiry-first order when stock is used or wasted.
                $batchStmt = $pdo->prepare("SELECT id, remaining_quantity FROM stock_batches WHERE ingredient_id=? AND remaining_quantity>0 ORDER BY (expiration_date IS NULL), expiration_date, received_at, id FOR UPDATE");
                $batchStmt->execute([$productId]);
                $remainingToDeduct = $quantity;
                foreach ($batchStmt->fetchAll() as $batch) {
                    if ($remainingToDeduct <= 0) break;
                    $take = min($remainingToDeduct, (float)$batch['remaining_quantity']);
                    $pdo->prepare("UPDATE stock_batches SET remaining_quantity=remaining_quantity-? WHERE id=?")->execute([$take,$batch['id']]);
                    $remainingToDeduct -= $take;
                }
            }
            $pdo->prepare("UPDATE ingredients SET current_stock=? WHERE id=?")->execute([$newStock,$productId]);
            $dbType = $type === 'Stock Adjustment' ? 'Adjustment' : $type;
            $pdo->prepare("INSERT INTO inventory_transactions (ingredient_id,user_id,transaction_type,quantity,remarks) VALUES (?,?,?,?,?)")
                ->execute([$productId,$_SESSION['user_id'],$dbType,$loggedQuantity,$remarks]);
            $pdo->commit();
            write_audit_log($pdo, 'Inventory Transaction', $type . ' · Product: ' . $product['name'] . ' (ID: ' . $productId . ') · Quantity: ' . $loggedQuantity . ($remarks !== '' ? ' · Remarks: ' . $remarks : ''));
            flash($type === 'Stock In' ? 'Stock added.' : ($type === 'Waste' ? 'Waste recorded.' : 'Stock removed.'));
            $returnPage = $_POST['return_page'] ?? 'transactions';
            if (!in_array($returnPage, ['ingredients','transactions','alerts'], true)) $returnPage = 'transactions';
            redirect('index.php?page=' . $returnPage);
        }
        if ($action === 'mark_handover_read') {
            $currentUserId = (int)$_SESSION['user_id'];
            $stmt = $pdo->prepare('UPDATE shift_handover_notes SET is_read=1 WHERE recipient_user_id=? AND is_read=0');
            $stmt->execute([$currentUserId]);
            $returnPage = (string)($_POST['return_page'] ?? 'dashboard');
            if (!in_array($returnPage, ['dashboard','ingredients','transactions','alerts','reports','users','settings','audit_logs'], true)) $returnPage = 'dashboard';
            redirect('index.php?page=' . urlencode($returnPage) . '&show_handover=1');
        }
        if ($action === 'save_handover_note') {
            if (is_admin()) throw new RuntimeException('Only Staff can send handover notes.');
            $note = trim((string)($_POST['message_body'] ?? $_POST['handover_note'] ?? ''));
            $recipientId = (int)($_POST['recipient_user_id'] ?? 0);
            if ($note === '') throw new RuntimeException('Please enter a message before sending.');
            if (mb_strlen($note) > 2000) throw new RuntimeException('Messages must be 2,000 characters or fewer.');
            $recipientCheck = $pdo->prepare("SELECT id, full_name, role FROM users WHERE id=? AND id<>? LIMIT 1");
            $recipientCheck->execute([$recipientId, (int)$_SESSION['user_id']]);
            $recipient = $recipientCheck->fetch(PDO::FETCH_ASSOC);
            if (!$recipient) throw new RuntimeException('Please choose a valid recipient other than yourself.');
            $stmt = $pdo->prepare("INSERT INTO shift_handover_notes (user_id, recipient_user_id, full_name, shift_label, note, is_read) VALUES (?, ?, ?, 'Message', ?, 0)");
            $stmt->execute([(int)$_SESSION['user_id'], $recipientId, (string)$_SESSION['full_name'], $note]);
            write_audit_log($pdo, 'Message Sent', 'Message sent by ' . (string)$_SESSION['full_name'] . ' to ' . $recipient['full_name'] . '.');
            flash('Message sent to ' . $recipient['full_name'] . '.');
            redirect('index.php?page=dashboard&show_handover=1');
        }
        if ($action === 'save_user') {
            require_admin();
            $name = trim($_POST['full_name'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = ($_POST['role'] ?? 'staff') === 'admin' ? 'admin' : 'staff';
            if ($name === '' || $username === '' || strlen($password) < 8) throw new RuntimeException('Enter a name, username, and password with at least 8 characters.');
            $stmt = $pdo->prepare("INSERT INTO users (full_name,username,password_hash,role) VALUES (?,?,?,?)");
            $stmt->execute([$name,$username,password_hash($password,PASSWORD_DEFAULT),$role]);
            write_audit_log($pdo, 'User Created', 'Created account for ' . $name . ' (username: ' . $username . ', role: ' . $role . ').');
            flash('User account created.');
            redirect('index.php?page=users' . (!empty($_POST['popup_mode']) ? '&popup=1' : ''));
        }
        if ($action === 'delete_user') {
            require_admin();
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) throw new RuntimeException('Invalid user selected.');
            if ($id === (int)($_SESSION['user_id'] ?? 0)) throw new RuntimeException('You cannot delete the account you are currently using.');
            $stmt = $pdo->prepare('SELECT full_name, username, role FROM users WHERE id=?');
            $stmt->execute([$id]);
            $deletedUser = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$deletedUser) throw new RuntimeException('User account not found.');
            $delete = $pdo->prepare('DELETE FROM users WHERE id=?');
            $delete->execute([$id]);
            write_audit_log($pdo, 'User Deleted', 'Deleted account for ' . $deletedUser['full_name'] . ' (username: ' . $deletedUser['username'] . ', role: ' . $deletedUser['role'] . ', ID: ' . $id . ').');
            flash('User account deleted.');
            redirect('index.php?page=users' . (!empty($_POST['popup_mode']) ? '&popup=1' : ''));
        }
        if ($action === 'change_password') {
            $current = $_POST['current_password'] ?? '';
            $new = $_POST['new_password'] ?? '';
            $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id=?");
            $stmt->execute([$_SESSION['user_id']]);
            $hash = $stmt->fetchColumn();
            if (!$hash || !password_verify($current, $hash)) throw new RuntimeException('Current password is incorrect.');
            if (strlen($new) < 8) throw new RuntimeException('New password must be at least 8 characters.');
            $pdo->prepare("UPDATE users SET password_hash=? WHERE id=?")->execute([password_hash($new,PASSWORD_DEFAULT),$_SESSION['user_id']]);
            write_audit_log($pdo, 'Password Changed', 'User changed their own password.');
            flash('Password updated successfully.');
            redirect('index.php?page=settings' . (!empty($_POST['popup_mode']) ? '&popup=1' : ''));
        }
    } catch (Throwable $ex) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        flash($ex instanceof PDOException ? 'Unable to save. Check for duplicate usernames or other database constraints.' : $ex->getMessage(), 'danger');
        redirect('index.php?page=' . $page . (!empty($_POST['popup_mode']) && in_array($page, ['users','settings'], true) ? '&popup=1' : ''));
    }
}
$flashMessage = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$totalingredients = (int)$pdo->query("SELECT COUNT(*) FROM ingredients")->fetchColumn();
$totalValue = (float)$pdo->query("SELECT COALESCE(SUM(current_stock * unit_cost),0) FROM ingredients")->fetchColumn();
$lowCount = (int)$pdo->query("SELECT COUNT(*) FROM ingredients WHERE current_stock <= minimum_stock")->fetchColumn();
$dashboardLowCount = (int)$pdo->query("SELECT COUNT(*) FROM ingredients WHERE current_stock <= minimum_stock AND current_stock > 0")->fetchColumn();
$outOfStockCount = (int)$pdo->query("SELECT COUNT(*) FROM ingredients WHERE current_stock <= 0")->fetchColumn();
$expiringSoonCount = (int)$pdo->query("SELECT COUNT(DISTINCT ingredient_id) FROM stock_batches WHERE remaining_quantity > 0 AND expiration_date IS NOT NULL AND expiration_date >= CURDATE() AND expiration_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)")->fetchColumn();
$dashboardExpiry = $pdo->query("SELECT p.id, p.name, p.unit, MIN(b.expiration_date) AS expiration_date FROM stock_batches b JOIN ingredients p ON p.id=b.ingredient_id WHERE b.remaining_quantity>0 AND b.expiration_date IS NOT NULL AND b.expiration_date >= CURDATE() AND b.expiration_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) GROUP BY p.id,p.name,p.unit ORDER BY MIN(b.expiration_date) ASC LIMIT 5")->fetchAll();
$todayCount = (int)$pdo->query("SELECT COUNT(*) FROM inventory_transactions WHERE DATE(created_at)=CURDATE()")->fetchColumn();
$ingredients = $pdo->query("SELECT p.*, c.name AS category, sb.expiration_date AS next_expiration FROM ingredients p LEFT JOIN categories c ON c.id=p.category_id LEFT JOIN (SELECT ingredient_id, MIN(expiration_date) AS expiration_date FROM stock_batches WHERE remaining_quantity>0 AND expiration_date IS NOT NULL GROUP BY ingredient_id) sb ON sb.ingredient_id=p.id ORDER BY p.name")->fetchAll();
$lowItems = $pdo->query("SELECT * FROM ingredients WHERE current_stock <= minimum_stock ORDER BY current_stock ASC LIMIT 8")->fetchAll();
$recent = $pdo->query("SELECT t.*,p.name AS product_name,p.unit,u.full_name FROM inventory_transactions t JOIN ingredients p ON p.id=t.ingredient_id LEFT JOIN users u ON u.id=t.user_id ORDER BY t.created_at DESC LIMIT 7")->fetchAll();
$lastInventoryUpdate = $pdo->query("SELECT MAX(created_at) FROM inventory_transactions")->fetchColumn();
$transactions = $pdo->query("SELECT t.*,p.name AS product_name,p.unit,u.full_name FROM inventory_transactions t JOIN ingredients p ON p.id=t.ingredient_id LEFT JOIN users u ON u.id=t.user_id ORDER BY t.created_at DESC LIMIT 100")->fetchAll();
$todayTransactions = $pdo->query("SELECT t.*,p.name AS product_name,p.unit,u.full_name FROM inventory_transactions t JOIN ingredients p ON p.id=t.ingredient_id LEFT JOIN users u ON u.id=t.user_id WHERE t.created_at >= CURDATE() AND t.created_at < CURDATE() + INTERVAL 1 DAY ORDER BY t.created_at DESC")->fetchAll();
$handoverReceivedNotes = [];
$handoverSentNotes = [];
$handoverRecipients = [];
$handoverUnreadCount = 0;
try {
    $currentUserId = (int)$_SESSION['user_id'];
    $receivedStmt = $pdo->prepare('SELECT id, user_id, recipient_user_id, full_name, shift_label, note, created_at, is_read FROM shift_handover_notes WHERE recipient_user_id = ? ORDER BY created_at DESC, id DESC LIMIT 50');
    $receivedStmt->execute([$currentUserId]);
    $handoverReceivedNotes = $receivedStmt->fetchAll(PDO::FETCH_ASSOC);
    $sentStmt = $pdo->prepare('SELECT m.id, m.user_id, m.recipient_user_id, m.full_name, m.shift_label, m.note, m.created_at, m.is_read, u.full_name AS recipient_name, u.role AS recipient_role FROM shift_handover_notes m LEFT JOIN users u ON u.id=m.recipient_user_id WHERE m.user_id = ? ORDER BY m.created_at DESC, m.id DESC LIMIT 50');
    $sentStmt->execute([$currentUserId]);
    $handoverSentNotes = $sentStmt->fetchAll(PDO::FETCH_ASSOC);
    $countStmt = $pdo->prepare('SELECT COUNT(*) FROM shift_handover_notes WHERE recipient_user_id=? AND is_read=0');
    $countStmt->execute([$currentUserId]);
    $handoverUnreadCount = (int)$countStmt->fetchColumn();
    if (!is_admin()) {
        // Include every existing admin and staff account except the currently logged-in user.
        $recipientStmt = $pdo->prepare('SELECT id, full_name, username, role FROM users WHERE id <> ? ORDER BY role ASC, full_name, username');
        $recipientStmt->execute([$currentUserId]);
        $handoverRecipients = $recipientStmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Throwable $ignored) {
    $handoverReceivedNotes = [];
    $handoverSentNotes = [];
    $handoverRecipients = [];
    $handoverUnreadCount = 0;
}
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$users = is_admin() ? $pdo->query("SELECT id,full_name,username,role,created_at FROM users ORDER BY created_at DESC")->fetchAll() : [];
$stockIn = (float)$pdo->query("SELECT COALESCE(SUM(quantity),0) FROM inventory_transactions WHERE transaction_type='Stock In' AND created_at >= CURDATE() - INTERVAL 7 DAY")->fetchColumn();
$stockOut = (float)$pdo->query("SELECT COALESCE(SUM(quantity),0) FROM inventory_transactions WHERE transaction_type IN ('Stock Out','Waste') AND created_at >= CURDATE() - INTERVAL 7 DAY")->fetchColumn();
$chartRows = $pdo->query("SELECT DATE(created_at) AS day, SUM(CASE WHEN transaction_type='Stock In' THEN quantity ELSE 0 END) AS stock_in, SUM(CASE WHEN transaction_type IN ('Stock Out','Waste') THEN quantity ELSE 0 END) AS stock_out FROM inventory_transactions WHERE created_at >= CURDATE() - INTERVAL 6 DAY GROUP BY DATE(created_at) ORDER BY day")->fetchAll();
$chart = [];
for ($i=6; $i>=0; $i--) { $day=date('Y-m-d',strtotime("-$i days")); $chart[$day]=['in'=>0,'out'=>0]; }
foreach ($chartRows as $r) { if (isset($chart[$r['day']])) {$chart[$r['day']]['in']=(float)$r['stock_in'];$chart[$r['day']]['out']=(float)$r['stock_out'];} }
$chartLabels = array_map(fn($d)=>date('D',strtotime($d)),array_keys($chart));
$chartIn = array_column(array_values($chart),'in');
$chartOut = array_column(array_values($chart),'out');
$titles = ['dashboard'=>'Dashboard','ingredients'=>'Ingredients','transactions'=>'Stock In / Stock Out','alerts'=>'Low Stock Alerts','reports'=>'Inventory Reports','users'=>'User Management','settings'=>'Settings','audit_logs'=>'Audit Logs'];
