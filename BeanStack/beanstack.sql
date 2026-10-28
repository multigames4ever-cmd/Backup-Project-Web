-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2026 at 03:54 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `beanstack`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `details`, `created_at`) VALUES
(1, 1, 'Inventory Transaction', 'Stock In · Product: Caramel Sauce (ID: 8) · Quantity: 1', '2026-10-26 05:55:55'),
(2, 1, 'User Deleted', 'Deleted account for Clyde (username: clydesheesh, role: staff, ID: 2).', '2026-10-26 06:54:52'),
(3, 1, 'User Created', 'Created account for Clyde (username: clyde@gmail.com, role: staff).', '2026-10-26 07:03:19'),
(4, 1, 'User Deleted', 'Deleted account for Clyde (username: clyde@gmail.com, role: staff, ID: 3).', '2026-10-26 07:20:24'),
(5, 1, 'User Created', 'Created account for Clyde (username: Clyde, role: staff).', '2026-10-26 07:20:53'),
(6, 1, 'User Created', 'Created account for Elixir (username: Elixir, role: staff).', '2026-10-26 07:21:14'),
(7, 1, 'User Created', 'Created account for Micford (username: Micford, role: staff).', '2026-10-26 07:21:38'),
(8, 1, 'User Deleted', 'Deleted account for Micford (username: Micford, role: staff, ID: 6).', '2026-10-26 07:21:44'),
(9, 4, 'Inventory Transaction', 'Stock Out · Product: Caramel Sauce (ID: 8) · Quantity: 1', '2026-10-26 07:32:29'),
(10, 4, 'Inventory Transaction', 'Stock Out · Product: Paper Cups (Large) (ID: 6) · Quantity: 30', '2026-10-26 07:37:04'),
(11, 4, 'Inventory Transaction', 'Waste · Product: Vanilla Syrup (ID: 3) · Quantity: 2', '2026-10-26 07:37:13'),
(12, 1, 'User Deleted', 'Deleted account for Elixir (username: Elixir, role: staff, ID: 5).', '2026-10-26 07:38:39'),
(13, 1, 'User Created', 'Created account for Elixir (username: Elixir, role: staff).', '2026-10-26 07:39:04'),
(14, 1, 'Inventory Transaction', 'Stock In · Product: Caramel Sauce (ID: 8) · Quantity: 30', '2026-10-26 07:42:18'),
(15, 1, 'User Deleted', 'Deleted account for Elixir (username: Elixir, role: staff, ID: 7).', '2026-10-26 07:46:36'),
(16, 1, 'User Created', 'Created account for Elixir (username: Elixir, role: staff).', '2026-10-26 07:47:41'),
(17, 1, 'Inventory Transaction', 'Stock In · Product: Paper Cups (Large) (ID: 6) · Quantity: 1', '2026-10-28 04:27:36'),
(18, 4, 'Shift Handover Note Sent', 'Mid Shift note sent by Clyde to BeanStack Administrator.', '2026-10-28 05:13:35'),
(19, 1, 'Inventory Transaction', 'Stock In · Product: Caramel Sauce (ID: 8) · Quantity: 3', '2026-10-28 06:27:43'),
(20, 1, 'Inventory Transaction', 'Stock Out · Product: Caramel Sauce (ID: 8) · Quantity: 1', '2026-10-28 06:27:59'),
(21, 1, 'Product Updated', 'Product: Coffee Beans (Arabica) (Code: P001, ID: 1).', '2026-10-28 06:43:40'),
(22, 1, 'Inventory Transaction', 'Stock In · Product: Caramel Sauce (ID: 8) · Quantity: 2', '2026-10-28 06:51:04'),
(23, 1, 'Inventory Transaction', 'Stock Out · Product: Caramel Sauce (ID: 8) · Quantity: 3', '2026-10-28 06:51:14'),
(24, 1, 'Inventory Transaction', 'Stock In · Product: Caramel Sauce (ID: 8) · Quantity: 1', '2026-10-28 06:51:50'),
(25, 1, 'Inventory Transaction', 'Stock In · Product: Caramel Sauce (ID: 8) · Quantity: 1', '2026-10-28 06:52:49'),
(26, 1, 'Inventory Transaction', 'Stock In · Product: Caramel Sauce (ID: 8) · Quantity: 2', '2026-10-28 06:55:23'),
(27, 1, 'Inventory Transaction', 'Stock In · Product: Caramel Sauce (ID: 8) · Quantity: 1', '2026-10-28 07:02:23'),
(28, 1, 'Inventory Transaction', 'Stock Out · Product: Caramel Sauce (ID: 8) · Quantity: 1', '2026-10-28 07:02:33'),
(29, 1, 'Product Updated', 'Product: Caramel Sauce (Code: P008, ID: 8).', '2026-10-28 07:02:40'),
(30, 1, 'Product Updated', 'Product: Caramel Sauce (Code: P008, ID: 8).', '2026-10-28 07:17:33'),
(31, 1, 'Product Updated', 'Product: Caramel Sauce (Code: P008, ID: 8).', '2026-10-28 07:53:45'),
(32, 1, 'Product Updated', 'Product: Caramel Sauce (Code: P008, ID: 8).', '2026-10-28 08:40:39');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Coffee Beans'),
(2, 'Dairy'),
(6, 'Other'),
(5, 'Packaging'),
(3, 'Syrup'),
(4, 'Toppings');

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `unit` varchar(30) NOT NULL DEFAULT 'pcs',
  `current_stock` decimal(12,2) NOT NULL DEFAULT 0.00,
  `minimum_stock` decimal(12,2) NOT NULL DEFAULT 0.00,
  `unit_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`id`, `name`, `category_id`, `unit`, `current_stock`, `minimum_stock`, `unit_cost`, `created_at`) VALUES
(1, 'Coffee Beans (Arabica)', 1, 'kg', 6.00, 15.00, 420.00, '2026-10-11 10:45:51'),
(2, 'Milk (1L)', 2, 'liter', 3.00, 10.00, 95.00, '2026-10-11 10:45:51'),
(3, 'Vanilla Syrup', 3, 'bottle', 20.00, 5.00, 180.00, '2026-10-11 10:45:51'),
(4, 'Chocolate Syrup', 3, 'bottle', 4.00, 10.00, 190.00, '2026-10-11 10:45:51'),
(5, 'Paper Cups (Small)', 5, 'pcs', 8.00, 20.00, 2.50, '2026-10-11 10:45:51'),
(6, 'Paper Cups (Large)', 5, 'pcs', 21.00, 20.00, 3.50, '2026-10-11 10:45:51'),
(7, 'Whipped Cream', 2, 'can', 12.00, 5.00, 110.00, '2026-10-11 10:45:51'),
(8, 'Caramel Sauce', 3, 'bottle', 41.00, 30.00, 180.00, '2026-10-11 10:45:51');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_transactions`
--

CREATE TABLE `inventory_transactions` (
  `id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `transaction_type` enum('Stock In','Stock Out','Waste','Adjustment') NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `remarks` varchar(255) DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_transactions`
--

INSERT INTO `inventory_transactions` (`id`, `ingredient_id`, `user_id`, `transaction_type`, `quantity`, `remarks`, `created_at`) VALUES
(1, 8, 1, 'Waste', 5.00, 'sadfasdf', '2026-10-25 05:42:58'),
(2, 3, 1, 'Stock In', 20.00, 'asd', '2026-10-25 05:44:14'),
(3, 8, 1, 'Stock In', 6.00, 'hghghg', '2026-10-25 07:35:28'),
(4, 8, 1, 'Stock In', 10.00, '', '2026-10-26 04:34:54'),
(5, 8, 1, 'Stock In', 1.00, '', '2026-10-26 04:36:47'),
(6, 8, 1, 'Adjustment', 21.00, 'Stock Adjustment (decrease; previous stock: 21; actual count: 0).', '2026-10-26 05:53:02'),
(7, 8, 1, 'Stock In', 1.00, '', '2026-10-26 05:55:55'),
(8, 8, 4, 'Stock Out', 1.00, '', '2026-10-26 07:32:29'),
(9, 6, 4, 'Stock Out', 30.00, '', '2026-10-26 07:37:04'),
(10, 3, 4, 'Waste', 2.00, '', '2026-10-26 07:37:13'),
(11, 8, 1, 'Stock In', 30.00, '', '2026-10-26 07:42:18'),
(12, 6, 1, 'Stock In', 1.00, '', '2026-10-28 04:27:36'),
(13, 8, 1, 'Stock Out', 5.00, '', '2026-10-28 06:19:54'),
(14, 8, 1, 'Stock In', 5.00, '', '2026-10-28 06:23:15'),
(15, 8, 1, 'Stock In', 3.00, '', '2026-10-28 06:27:43'),
(16, 8, 1, 'Stock Out', 1.00, '', '2026-10-28 06:27:59'),
(17, 8, 1, 'Stock In', 2.00, '', '2026-10-28 06:51:04'),
(18, 8, 1, 'Stock Out', 3.00, '', '2026-10-28 06:51:14'),
(19, 8, 1, 'Stock In', 1.00, '', '2026-10-28 06:51:50'),
(20, 8, 1, 'Stock In', 1.00, '', '2026-10-28 06:52:49'),
(21, 8, 1, 'Stock In', 2.00, '', '2026-10-28 06:55:23'),
(22, 8, 1, 'Stock In', 1.00, '', '2026-10-28 07:02:23'),
(23, 8, 1, 'Stock Out', 1.00, '', '2026-10-28 07:02:33'),
(24, 8, 1, 'Adjustment', 5.00, 'Stock Adjustment (increase; previous stock: 35; actual count: 40).', '2026-10-28 07:53:45'),
(25, 8, 1, 'Adjustment', 1.00, 'Stock Adjustment (increase; previous stock: 40; actual count: 41).', '2026-10-28 08:40:39');

-- --------------------------------------------------------

--
-- Table structure for table `shift_handover_notes`
--

CREATE TABLE `shift_handover_notes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recipient_user_id` int(11) DEFAULT NULL,
  `full_name` varchar(150) NOT NULL,
  `shift_label` varchar(30) NOT NULL,
  `note` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shift_handover_notes`
--

INSERT INTO `shift_handover_notes` (`id`, `user_id`, `recipient_user_id`, `full_name`, `shift_label`, `note`, `created_at`, `is_read`) VALUES
(1, 4, 1, 'Clyde', 'Mid Shift', 'Sahod ko', '2026-10-28 13:13:35', 1);

-- --------------------------------------------------------

--
-- Table structure for table `stock_batches`
--

CREATE TABLE `stock_batches` (
  `id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `received_quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `remaining_quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `expiration_date` date DEFAULT NULL,
  `received_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `received_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_batches`
--

INSERT INTO `stock_batches` (`id`, `ingredient_id`, `received_quantity`, `remaining_quantity`, `expiration_date`, `received_at`, `received_by`) VALUES
(1, 1, 6.00, 6.00, '2026-11-27', '2026-10-26 05:53:20', 1),
(2, 8, 1.00, 0.00, '2026-10-31', '2026-10-26 05:55:55', 1),
(3, 8, 30.00, 20.00, NULL, '2026-10-26 07:42:18', 1),
(4, 6, 1.00, 1.00, NULL, '2026-10-28 04:27:36', 1),
(5, 8, 5.00, 5.00, NULL, '2026-10-28 06:23:15', 1),
(6, 8, 3.00, 3.00, NULL, '2026-10-28 06:27:43', 1),
(7, 8, 2.00, 2.00, NULL, '2026-10-28 06:51:04', 1),
(8, 8, 1.00, 1.00, NULL, '2026-10-28 06:51:50', 1),
(9, 8, 1.00, 1.00, NULL, '2026-10-28 06:52:49', 1),
(10, 8, 2.00, 2.00, NULL, '2026-10-28 06:55:23', 1),
(11, 8, 1.00, 1.00, NULL, '2026-10-28 07:02:23', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `username` varchar(60) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','staff') NOT NULL DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `password_hash`, `role`, `created_at`) VALUES
(1, 'BeanStack Administrator', 'admin', '$2y$10$GDTm4382lH53NMElCmQTGOTmRiPB/yVe7rKl8jI664NkhytKfoZGW', 'admin', '2026-10-11 10:45:51'),
(4, 'Clyde', 'Clyde', '$2y$10$BOG5SdtvkmRIqqgnvMqu3eQjiHS7g1A7.1eo35lam6sbzY9jG7o/u', 'staff', '2026-10-26 07:20:53'),
(8, 'Elixir', 'Elixir', '$2y$10$mKpCY5NlCl0tj.GuGOR.duutkBy5admQ3X34h1N8HcZjubRTK6ULm', 'staff', '2026-10-26 07:47:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_created` (`created_at`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`ingredient_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `shift_handover_notes`
--
ALTER TABLE `shift_handover_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_handover_created_at` (`created_at`);

--
-- Indexes for table `stock_batches`
--
ALTER TABLE `stock_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_batch_expiration` (`expiration_date`),
  ADD KEY `idx_batch_product` (`ingredient_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `shift_handover_notes`
--
ALTER TABLE `shift_handover_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stock_batches`
--
ALTER TABLE `stock_batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD CONSTRAINT `ingredients_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD CONSTRAINT `inventory_transactions_ibfk_1` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`),
  ADD CONSTRAINT `inventory_transactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
