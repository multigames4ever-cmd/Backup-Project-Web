<?php
require_once __DIR__ . '/actions.php';
?>

<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= e($titles[$page]) ?> | BeanStack</title><link rel="stylesheet" href="assets/style.css"><script src="https://cdn.jsdelivr.net/npm/chart.js" onerror="var fallback=document.createElement('script');fallback.src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js';document.head.appendChild(fallback);"></script><script defer src="assets/app.js"></script></head>
<style>
.sidebar-user{gap:10px;}
.sidebar-user-info{min-width:0;flex:1;display:flex;flex-direction:column;}
.sidebar-user-info strong,.sidebar-user-info small{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.logout-icon-button{width:38px;height:38px;flex:0 0 38px;display:inline-flex;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,.16);border-radius:10px;background:rgba(255,255,255,.06);color:inherit;text-decoration:none;transition:background .18s ease,color .18s ease,border-color .18s ease;}
.logout-icon-button:hover,.logout-icon-button:focus-visible{background:#b42318;color:#fff;border-color:#b42318;outline:none;}

.dashboard-summary-grid{grid-template-columns:repeat(4,minmax(0,1fr))}
.dashboard-summary-grid .stat-card{min-height:112px}
.dashboard-summary-grid a.stat-card{display:flex;color:inherit;text-decoration:none;transition:transform .15s ease,box-shadow .15s ease}
.dashboard-summary-grid a.stat-card:hover,.dashboard-summary-grid a.stat-card:focus-visible{transform:translateY(-4px);box-shadow:0 12px 26px rgba(75,48,36,.16);border-color:rgba(111,78,55,.58);outline:2px solid rgba(176,137,104,.28);outline-offset:2px}
.dashboard-summary-grid a.stat-card:active{transform:translateY(-1px);box-shadow:0 4px 12px rgba(75,48,36,.14)}
.stock-bars{display:grid;gap:14px;padding:12px 4px}.stock-bar-row{display:grid;grid-template-columns:minmax(76px,100px) 1fr;gap:12px;align-items:center}.stock-bar-label{font-size:12px;color:var(--muted,#6b7280)}.stock-bar-track{display:grid;gap:5px}.stock-bar-line{height:10px;min-width:2px;border-radius:5px}.stock-bar-in{background:#6f4e37}.stock-bar-out{background:#c08a5b}.stock-bar-values{display:flex;gap:12px;font-size:11px;color:var(--muted,#6b7280)}
.dashboard-quick-actions{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
.dashboard-quick-actions .quick-action-link{display:inline-flex;align-items:center;gap:8px;padding:11px 17px;border:1px solid #3d7052;border-radius:10px;background:linear-gradient(135deg,#4f8a68,#3d7052);color:#fff;text-decoration:none;font-weight:700;box-shadow:0 3px 8px rgba(61,112,82,.18);transition:transform .18s ease,box-shadow .18s ease,background .18s ease}
.dashboard-quick-actions .quick-action-link:hover,.dashboard-quick-actions .quick-action-link:focus-visible{background:linear-gradient(135deg,#3d7052,#2f5c40);transform:translateY(-2px);box-shadow:0 7px 16px rgba(61,112,82,.25);outline:2px solid rgba(79,138,104,.2);outline-offset:2px}
.dashboard-quick-actions .quick-action-link:active{transform:translateY(0);box-shadow:0 2px 5px rgba(61,112,82,.2)}
.dashboard-last-updated{display:inline-flex;align-items:center;gap:7px;margin-top:12px;padding:6px 10px;border-radius:999px;background:rgba(255,255,255,.13);font-size:12px;color:inherit}
.dashboard-attention-panel .attention-subheading{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted,#6b7280);margin:12px 0 4px}
.dashboard-attention-panel .expiry-subheading{margin-top:18px}
.dashboard-attention-panel .compact-empty{padding:12px 8px;font-size:13px}
.dashboard-attention-panel .expiry-dot{background:#fff0d5;color:#9a6700}
@media(max-width:1200px){.dashboard-summary-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:900px){.dashboard-summary-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:560px){.dashboard-summary-grid{grid-template-columns:1fr}}

/* Ensure native hidden states work for product filtering and conditional form fields. */
#ingredientsDataTable tr[hidden], #ingredientsDataTable [hidden], #productTransactionModal [hidden], #productModal [hidden] { display: none !important; }

/* Compact expiring-soon indicator for the minimal dashboard. */
.dashboard-expiry-indicator{margin:0 0 18px;display:flex;justify-content:flex-end}.dashboard-expiry-indicator>a{display:flex;align-items:center;gap:10px;padding:10px 14px;border:1px solid var(--border-color,#e5e7eb);border-radius:12px;background:var(--card-bg,#fff);color:inherit;text-decoration:none;transition:border-color .18s ease,transform .18s ease,box-shadow .18s ease}.dashboard-expiry-indicator>a:hover,.dashboard-expiry-indicator>a:focus-visible{border-color:#8b5e3c;transform:translateY(-1px);box-shadow:0 5px 16px rgba(0,0,0,.06);outline:none}.expiry-indicator-icon{font-size:20px;color:#b7791f}.dashboard-expiry-indicator strong,.dashboard-expiry-indicator small{display:block}.dashboard-expiry-indicator strong{font-size:13px}.dashboard-expiry-indicator small{font-size:11px;color:var(--muted,#6b7280);margin-top:2px}.expiry-indicator-arrow{font-size:12px;font-weight:700;color:#6f4e37;margin-left:8px;white-space:nowrap}@media(max-width:560px){.dashboard-expiry-indicator{justify-content:stretch}.dashboard-expiry-indicator>a{width:100%;box-sizing:border-box;flex-wrap:wrap}.expiry-indicator-arrow{margin-left:auto}}


/* Compact handover inbox button in the top bar. */
.handover-mail-button{position:relative;display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;flex:0 0 40px;border:1px solid var(--border-color,#e5e7eb);border-radius:12px;background:var(--card-bg,#fff);color:var(--text-color,#374151);cursor:pointer;transition:background .18s ease,border-color .18s ease,transform .18s ease}
.handover-mail-button:hover,.handover-mail-button:focus-visible{background:rgba(79,138,104,.08);border-color:#8b5e3c;transform:translateY(-1px);outline:none}
.handover-mail-button svg{width:19px;height:19px}
.handover-unread-badge{position:absolute;top:-5px;right:-5px;min-width:17px;height:17px;padding:0 4px;display:inline-flex;align-items:center;justify-content:center;border-radius:999px;background:#dc3545;color:#fff;font-size:10px;font-weight:800;line-height:1;border:2px solid var(--card-bg,#fff);box-sizing:border-box}.handover-note-name{display:block;font-size:14px;font-weight:800;color:var(--text-color,#222);margin-bottom:5px}.handover-note-shift{display:inline-flex;align-items:center;padding:3px 8px;border-radius:999px;background:rgba(79,138,104,.10);color:var(--primary,#4f8a68);font-size:11px;font-weight:700}.handover-note.is-unread{border-color:rgba(79,138,104,.5);background:rgba(79,138,104,.045)}.handover-note-unread{font-size:10px;font-weight:800;color:#b42318;text-transform:uppercase;letter-spacing:.04em}
.topbar-user-group{display:flex;align-items:center;gap:12px;margin-left:auto}
.handover-open-button{border:1px solid var(--border-color,#e5e7eb);background:transparent!important;color:var(--text-color,#374151)!important;margin-left:8px;cursor:pointer;font:inherit;font-weight:700}
.handover-open-button:hover,.handover-open-button:focus-visible{background:rgba(79,138,104,.08)!important;border-color:#8b5e3c;outline:none}
.handover-modal-backdrop{position:fixed;inset:0;z-index:10000;background:rgba(18,25,20,.56);backdrop-filter:blur(3px);padding:20px;align-items:center;justify-content:center;box-sizing:border-box}
.handover-modal-backdrop.open{display:flex!important}
.handover-modal-card{width:min(100%,620px);max-height:min(88vh,800px);overflow:auto;background:var(--card-bg,#fff);color:var(--text-color,#222);border:1px solid var(--border-color,#e5e7eb);border-radius:16px;padding:22px;box-shadow:0 24px 80px rgba(0,0,0,.24)}
.handover-modal-card .panel-heading{align-items:flex-start;margin-bottom:12px}
.handover-modal-card .panel-heading h2{margin:3px 0 5px}
.handover-modal-card .panel-heading p{margin:0;color:var(--muted,#6b7280);font-size:13px}
.handover-modal-content{display:grid;gap:8px}
.handover-modal-content .handover-form{margin:8px 0 14px}
@media(max-width:560px){.handover-modal-backdrop{padding:10px}.handover-modal-card{padding:16px;max-height:92vh}.handover-open-button{margin:8px 0 0}}


/* Low stock alerts use the same topbar button and centered modal scale as handover notes. */
.low-stock-alert-button{margin-right:2px}
.low-stock-alert-button svg{width:19px;height:19px}
#lowStockAlertsModal.open{display:flex!important}
#lowStockAlertsModal .handover-modal-card{width:min(100%,620px);max-height:min(88vh,800px)}

/* Shift handover notes panel. */
.handover-panel{min-width:0}.handover-form{display:grid;gap:8px;margin:4px 0 20px}.handover-form label{font-size:13px;font-weight:700}.handover-form select,.handover-form textarea{width:100%;border:1px solid var(--border-color,#e5e7eb);border-radius:9px;padding:10px 12px;background:var(--card-bg,#fff);color:var(--text-color,inherit);font:inherit;box-sizing:border-box}.handover-form textarea{resize:vertical;min-height:82px}.handover-form-footer{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap}.handover-notes-heading{font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--muted,#6b7280);margin:12px 0 8px}.handover-note-list{display:grid;gap:10px}.handover-note{padding:12px;border:1px solid var(--border-color,#e5e7eb);border-radius:10px;background:var(--surface,#faf9f7)}.handover-note-meta{display:flex;align-items:flex-start;justify-content:space-between;gap:8px;flex-wrap:wrap}.handover-note-meta strong{font-size:13px}.handover-note-meta span,.handover-note>small{font-size:11px;color:var(--muted,#6b7280)}.handover-note p{margin:8px 0;white-space:normal;overflow-wrap:anywhere;font-size:13px;line-height:1.5}

/* Users page delete-button hover and centered confirmation popup animation. */
.delete-icon-button{display:inline-flex;align-items:center;justify-content:center;color:#dc2626;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;width:34px;height:34px;cursor:pointer;transition:background .18s ease,color .18s ease,border-color .18s ease,transform .18s ease,box-shadow .18s ease;}
.delete-icon-button:hover,.delete-icon-button:focus-visible{background:#dc2626;color:#fff;border-color:#dc2626;transform:translateY(-2px) scale(1.04);box-shadow:0 5px 12px rgba(220,38,38,.22);outline:none;}
.delete-icon-button:active{transform:translateY(0) scale(.97);}
#deleteUserConfirmModal{backdrop-filter:blur(3px);}
#deleteUserConfirmModal.open{animation:deleteUserBackdropIn .18s ease-out both;}
#deleteUserConfirmModal .modal-card{box-shadow:0 22px 70px rgba(0,0,0,.25);animation:deleteUserPopIn .22s cubic-bezier(.2,.8,.2,1) both;}
#deleteUserConfirmModal .modal-heading h2{margin-top:4px;}
#confirmUserDelete{transition:background .18s ease,transform .18s ease,box-shadow .18s ease;}
#confirmUserDelete:hover,#confirmUserDelete:focus-visible{background:#b91c1c!important;border-color:#b91c1c!important;transform:translateY(-1px);box-shadow:0 5px 12px rgba(220,38,38,.2);outline:none;}
#cancelUserDelete:hover{transform:translateY(-1px);}
@keyframes deleteUserBackdropIn{from{opacity:0}to{opacity:1}}
@keyframes deleteUserPopIn{from{opacity:0;transform:translateY(12px) scale(.94)}to{opacity:1;transform:translateY(0) scale(1)}}
@media(prefers-reduced-motion:reduce){.delete-icon-button,#confirmUserDelete,#cancelUserDelete,#deleteUserConfirmModal.open,#deleteUserConfirmModal .modal-card{animation:none!important;transition:none!important}}



/* Reports page spacing, responsive cards, and readable report sections. */
.report-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;margin:0 0 24px;align-items:stretch}
.report-stats .stat-card{min-width:0;min-height:122px;padding:20px;display:flex;align-items:flex-start;gap:14px;border-radius:14px}
.report-stats .stat-card>div:last-child{min-width:0;display:flex;flex-direction:column;gap:6px}
.report-stats .stat-card strong{overflow-wrap:anywhere;font-size:clamp(20px,2vw,28px)}
.report-grid{gap:24px;margin-bottom:36px;align-items:stretch}
.report-grid>.panel{min-width:0;margin:0;border-radius:14px;overflow:hidden}
.report-table-stack{display:grid;gap:28px;margin-top:0;padding-bottom:12px}
.report-table-stack>.panel{min-width:0;margin:0;border-radius:14px;overflow:hidden;box-shadow:0 4px 18px rgba(50,35,25,.045)}
.report-table-stack .panel-heading,.report-grid .panel-heading{padding-bottom:14px;gap:12px;flex-wrap:wrap}
.report-table-stack .table-scroll{width:100%;overflow-x:auto}
.report-table-stack table{min-width:760px}
.report-table-stack .table-toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;padding:0 22px 18px}
.report-filter-controls{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.report-filter-controls input,.report-filter-controls select{min-height:40px;border:1px solid var(--border-color,#e5e7eb);border-radius:9px;padding:9px 12px;background:var(--card-bg,#fff);color:var(--text-color,inherit);font:inherit;box-sizing:border-box}
.report-filter-controls input{min-width:min(260px,100%)}
.report-filter-controls select{min-width:150px}
.report-results-count{font-size:12px;color:var(--muted,#6b7280);white-space:nowrap}
.report-table-stack .panel-heading{padding:22px 22px 16px}
.report-table-stack .table-scroll table thead th{white-space:nowrap}
.logout-confirm-backdrop{display:none;align-items:center;justify-content:center;position:fixed;inset:0;z-index:10000;padding:20px;background:rgba(15,23,42,.56);backdrop-filter:blur(4px)}
.logout-confirm-backdrop.open{display:flex;animation:deleteUserBackdropIn .18s ease-out both}
.logout-confirm-card{width:min(100%,420px);padding:26px;border-radius:16px;background:var(--panel,#fff);color:var(--text,#1f2937);box-shadow:0 24px 80px rgba(0,0,0,.26);animation:deleteUserPopIn .22s cubic-bezier(.2,.8,.2,1) both}
.logout-confirm-icon{width:46px;height:46px;display:grid;place-items:center;margin-bottom:14px;border-radius:14px;background:#fff1f0;color:#b42318;font-size:22px}
.logout-confirm-card h2{margin:0 0 8px;font-size:21px}
.logout-confirm-card p{margin:0 0 22px;color:var(--muted,#6b7280);line-height:1.55}
.logout-confirm-actions{display:flex;justify-content:flex-end;gap:10px;flex-wrap:wrap}
.logout-confirm-actions .btn{min-width:104px}

/* Clickable account menu in the top bar. */
.top-user-menu{position:relative;margin-left:2px}
.top-user-trigger{display:flex;align-items:center;gap:10px;padding:5px 9px;border:1px solid transparent;border-radius:12px;background:transparent;color:inherit;text-align:left;font:inherit;cursor:pointer;max-width:260px}
.top-user-trigger:hover,.top-user-trigger:focus-visible,.top-user-menu.open .top-user-trigger{background:rgba(79,138,104,.08);border-color:var(--border-color,#e5e7eb);outline:none}
.top-user-trigger .top-user-copy{display:flex;flex-direction:column;min-width:0}
.top-user-trigger strong,.top-user-trigger small{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.top-user-trigger strong{font-size:13px}.top-user-trigger small{font-size:11px;color:var(--muted,#6b7280);margin-top:2px}
.top-user-chevron{font-size:12px;transition:transform .18s ease}.top-user-menu.open .top-user-chevron{transform:rotate(180deg)}
.top-user-dropdown{display:none;position:absolute;right:0;top:calc(100% + 8px);width:210px;padding:7px;background:var(--card-bg,#fff);color:var(--text-color,#222);border:1px solid var(--border-color,#e5e7eb);border-radius:13px;box-shadow:0 16px 42px rgba(0,0,0,.16);z-index:10001}
.top-user-menu.open .top-user-dropdown{display:grid;animation:deleteUserPopIn .16s ease-out both}
.top-user-dropdown a{display:flex;align-items:center;gap:10px;padding:10px 11px;border-radius:8px;color:inherit;text-decoration:none;font-size:13px;font-weight:650}
.top-user-dropdown a:hover,.top-user-dropdown a:focus-visible{background:rgba(79,138,104,.1);outline:none}
.top-user-dropdown a.dropdown-logout{color:#b42318}.top-user-dropdown .dropdown-divider{height:1px;background:var(--border-color,#e5e7eb);margin:5px 2px}
.top-user-dropdown .account-modal-link{display:flex;align-items:center;gap:10px;width:100%;padding:10px 11px;border:0;border-radius:8px;background:transparent;color:inherit;text-align:left;font:inherit;font-size:13px;font-weight:650;cursor:pointer}
.top-user-dropdown .account-modal-link:hover,.top-user-dropdown .account-modal-link:focus-visible{background:rgba(79,138,104,.1);outline:none}
.account-page-modal{display:none;position:fixed;inset:0;z-index:10020;align-items:center;justify-content:center;padding:22px;background:rgba(15,23,42,.56);backdrop-filter:blur(4px)}
.account-page-modal.open{display:flex;animation:deleteUserBackdropIn .18s ease-out both}
.account-page-modal-card{position:relative;width:min(100%,900px);height:min(78vh,700px);min-height:300px;background:var(--card-bg,#fff);border:1px solid var(--border-color,#e5e7eb);border-radius:16px;overflow:hidden;box-shadow:0 24px 80px rgba(0,0,0,.28);animation:deleteUserPopIn .2s ease-out both}
.account-page-modal-header{height:58px;display:flex;align-items:center;justify-content:space-between;padding:0 18px;border-bottom:1px solid var(--border-color,#e5e7eb);background:var(--card-bg,#fff)}
.account-page-modal-header strong{font-size:15px}.account-page-modal-close{width:34px;height:34px;border:0;border-radius:9px;background:transparent;color:inherit;font-size:25px;cursor:pointer}.account-page-modal-close:hover{background:rgba(0,0,0,.07)}
.account-page-modal iframe{display:block;width:100%;height:calc(100% - 58px);border:0;background:var(--page-bg,#f8faf8)}
.account-modal-link{font-family:inherit}
@media(max-width:640px){.account-page-modal{padding:10px}.account-page-modal-card{width:100%;height:min(88vh,760px);border-radius:13px}.account-page-modal-header{height:52px;padding:0 13px}.account-page-modal iframe{height:calc(100% - 52px)}}

@media(max-width:600px){.top-user-trigger{padding:4px;gap:7px;max-width:175px}.top-user-trigger strong{max-width:105px}.top-user-dropdown{right:0;width:190px}}
@media(min-width:1400px){.report-stats{grid-template-columns:repeat(5,minmax(0,1fr))}}
@media(max-width:1100px){.report-stats{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:600px){.report-stats{grid-template-columns:1fr;gap:12px}.report-stats .stat-card{min-height:0;padding:16px}.report-grid{gap:16px;margin-bottom:26px}.report-table-stack{gap:20px;padding-bottom:8px}.report-filter-controls{width:100%}.report-filter-controls input,.report-filter-controls select{width:100%;min-width:0}.logout-confirm-card{padding:22px}}
@media print{.report-filter-controls,.page-heading>.btn,.logout-confirm-backdrop{display:none!important}.report-stats,.report-grid,.report-table-stack{break-inside:avoid;gap:12px}.report-table-stack>.panel{box-shadow:none}}


/* Audit Logs filters: compact controls with a single grouped date-range field */
.audit-filter-controls{display:flex!important;align-items:flex-end;gap:10px;padding:12px 14px;margin:0 0 16px;border:1px solid var(--border,#eadfd4);border-radius:12px;background:var(--panel-soft,#fcf8f4);flex-wrap:wrap}
.audit-filter-controls>div{display:flex;flex-direction:column;gap:5px;min-width:0}
.audit-filter-controls label{font-size:11px!important;font-weight:600;color:var(--muted,#6b625b);line-height:1.2}
.audit-filter-controls input,.audit-filter-controls select{width:auto!important;min-width:0!important;min-height:30px!important;height:30px!important;padding:4px 7px!important;border:1px solid var(--border,#d8c9bb);border-radius:6px;background:var(--panel,#fff);color:var(--text,#332820);font:inherit;font-size:12px!important;box-sizing:border-box}
.audit-date-range{width:330px;max-width:100%}
.audit-date-range .audit-range-heading{display:block;margin-bottom:1px}
.audit-date-inputs{display:flex;align-items:center;gap:7px;padding:5px 7px;border:1px solid var(--border,#d8c9bb);border-radius:7px;background:var(--panel,#fff);width:100%;box-sizing:border-box}
.audit-date-inputs>div{display:flex;flex:1 1 0;flex-direction:column;gap:3px;min-width:0}
.audit-date-inputs input{width:100%!important;min-width:0!important;border:0!important;background:transparent!important;padding:2px 0!important;height:25px!important;min-height:25px!important}
.audit-date-separator{font-size:11px;color:var(--muted,#6b625b);padding-top:13px;flex:0 0 auto}
.audit-staff-filter{width:190px;max-width:100%}
.audit-staff-filter select{width:100%!important}
.audit-filter-actions{display:flex;gap:6px;flex-direction:row!important;align-items:center;flex-wrap:wrap}
.audit-filter-actions .btn{min-height:30px!important;height:30px;padding:5px 10px;font-size:12px;white-space:nowrap}
@media(max-width:650px){.audit-filter-controls{align-items:stretch}.audit-date-range,.audit-staff-filter{width:100%;max-width:none}.audit-filter-actions{width:100%}.audit-date-inputs{max-width:100%}}
@media print{.audit-filter-controls,.audit-print-button{display:none!important}.table-scroll{overflow:visible!important}.table-scroll table{width:100%;font-size:10pt}.panel{box-shadow:none!important;border:1px solid #ddd!important;break-inside:auto}.table-scroll tr{break-inside:avoid}}

/* Keep chart areas visible even if the external stylesheet or Chart.js fails. */
.chart-wrap{position:relative;width:100%;height:380px;min-height:350px;padding:14px 14px 22px;box-sizing:border-box}
.chart-wrap canvas{display:block!important;width:100%!important;height:350px!important;max-width:100%}
.chart-fallback{display:block;position:absolute;inset:12px;overflow:auto;background:var(--panel,#fff);z-index:1}
.chart-fallback-row{display:grid;grid-template-columns:52px minmax(0,1fr) 88px;align-items:center;gap:10px;margin:9px 0;font-size:12px}
.chart-fallback-track{display:flex;gap:4px;align-items:center;min-width:0}
.chart-fallback-bar{height:12px;min-width:2px;border-radius:4px}
.chart-fallback-in{background:#6f4e37}.chart-fallback-out{background:#c08a5b}.chart-fallback-category{background:#a87952}
/* Dashboard interaction polish: warm coffee-brown accents. */
.dashboard-grid .chart-panel,.dashboard-grid>.panel{transition:box-shadow .2s ease,border-color .2s ease}
.dashboard-grid .chart-panel:hover,.dashboard-grid>.panel:hover{box-shadow:0 8px 24px rgba(75,48,36,.10)}
.dashboard-expiry-indicator>a:hover,.dashboard-expiry-indicator>a:focus-visible{background:#fbfcf8}
.dashboard-expiry-indicator .expiry-indicator-icon{color:#9a7445}
.dashboard-expiry-indicator .expiry-indicator-arrow{color:#3d7052}
.dashboard-summary-grid .stat-card .stat-icon{transition:transform .2s ease,background .2s ease}
.dashboard-summary-grid a.stat-card:hover .stat-icon{transform:scale(1.07);background:rgba(111,78,55,.13);color:#4b3024}
@media(prefers-reduced-motion:reduce){.dashboard-summary-grid a.stat-card,.dashboard-quick-actions .quick-action-link,.dashboard-grid .chart-panel,.dashboard-grid>.panel,.dashboard-summary-grid .stat-card .stat-icon{transition:none!important}}
.chart-fallback-values{white-space:nowrap;color:var(--muted,#6b7280);font-size:11px}


/* Coffee-house dashboard theme: espresso, mocha, caramel and latte. */
:root{--bean-coffee:#6f4e37;--bean-espresso:#4b3024;--bean-caramel:#c08a5b;--bean-latte:#f5eee5}
.dashboard-quick-actions .quick-action-link{border-color:#6f4e37!important;background:linear-gradient(135deg,#8b5e3c,#4b3024)!important;box-shadow:0 3px 8px rgba(75,48,36,.20)!important;transition:transform .18s ease,box-shadow .18s ease,filter .18s ease!important}
.dashboard-quick-actions .quick-action-link:hover,.dashboard-quick-actions .quick-action-link:focus-visible{background:linear-gradient(135deg,#6f4e37,#352219)!important;transform:translateY(-2px);box-shadow:0 8px 18px rgba(75,48,36,.28)!important;outline:2px solid rgba(176,137,104,.28);outline-offset:2px}
.dashboard-grid .chart-panel:hover,.dashboard-grid>.panel:hover{box-shadow:0 8px 24px rgba(75,48,36,.10)!important;border-color:rgba(111,78,55,.28)!important}
.dashboard-expiry-indicator>a:hover,.dashboard-expiry-indicator>a:focus-visible{background:#f8f0e7!important;border-color:#8b5e3c!important}
.dashboard-expiry-indicator .expiry-indicator-arrow{color:#6f4e37!important}
.dashboard-summary-grid a.stat-card:hover .stat-icon{transform:scale(1.07);background:rgba(111,78,55,.13)!important;color:#4b3024!important}
.handover-mail-button:hover,.handover-mail-button:focus-visible,.handover-open-button:hover,.handover-open-button:focus-visible{background:rgba(111,78,55,.09)!important;border-color:#8b5e3c!important}
.handover-note-shift{background:rgba(111,78,55,.10)!important;color:#6f4e37!important}
.btn:not(.btn-danger):not(.btn-delete):hover,.btn:not(.btn-danger):not(.btn-delete):focus-visible{border-color:#6f4e37;box-shadow:0 4px 12px rgba(75,48,36,.14);transition:background .18s ease,border-color .18s ease,color .18s ease,box-shadow .18s ease,transform .18s ease}
.btn:not(.btn-danger):not(.btn-delete):active{transform:translateY(0)}
.chart-fallback-values{color:#756253!important}
@media(prefers-reduced-motion:reduce){.dashboard-quick-actions .quick-action-link,.dashboard-grid .chart-panel,.dashboard-grid>.panel,.dashboard-summary-grid .stat-card .stat-icon{transition:none!important}}

/* Ingredients: category dropdown and coffee-brown edit hover */
.product-category-filter-wrap{display:flex;align-items:center;min-width:0;flex:0 0 180px;white-space:nowrap}
.product-category-filter-wrap select{min-width:0;width:100%;height:38px;padding:8px 30px 8px 12px;border:1px solid #d8c5b3;border-radius:10px;background:#fffaf5;color:#4b3024;font:inherit;font-size:13px;line-height:20px;cursor:pointer;outline:none;transition:border-color .18s ease,box-shadow .18s ease,background .18s ease}
.product-category-filter-wrap select:hover,.product-category-filter-wrap select:focus{border-color:#8b5e3c;box-shadow:0 0 0 3px rgba(139,94,60,.13);background:#fff}
.icon-button.edit-product{color:#6f4e37;background:#fffaf5;border:1px solid #d8c5b3;border-radius:8px;width:34px;height:34px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:background .18s ease,color .18s ease,border-color .18s ease,transform .18s ease,box-shadow .18s ease}
.icon-button.edit-product:hover,.icon-button.edit-product:focus-visible{background:#6f4e37;color:#fff;border-color:#4b3024;transform:translateY(-2px);box-shadow:0 5px 12px rgba(75,48,36,.22);outline:2px solid rgba(176,137,104,.25);outline-offset:2px}
.icon-button.edit-product:active{transform:translateY(0) scale(.97)}
@media(max-width:700px){.product-category-filter-wrap{width:100%;min-width:0;flex:1 1 100%}.product-category-filter-wrap select{flex:1;max-width:none}.product-search-actions{width:100%}}
@media(prefers-reduced-motion:reduce){.product-category-filter-wrap select,.icon-button.edit-product{transition:none!important}}


/* Remarks textarea: same field border and label style, only slightly taller than a single-line input */
#transactionRemarks{width:100%;min-height:92px;height:92px;resize:vertical;box-sizing:border-box;line-height:1.5;border:1px solid #d8c5b3;border-radius:10px;padding:10px 12px;color:var(--text,#4b3024);background:var(--panel,#fffaf5);font:inherit}
#transactionRemarks:focus{outline:none;border-color:#8b5e3c;box-shadow:0 0 0 3px rgba(139,94,60,.12)}
/* Coffee-brown centered transaction success popup */
.transaction-success-backdrop{position:fixed;inset:0;z-index:20000;display:flex;align-items:center;justify-content:center;padding:20px;background:rgba(35,24,18,.48);backdrop-filter:blur(3px);animation:txFadeIn .18s ease-out both}
.transaction-success-card{width:min(100%,390px);box-sizing:border-box;text-align:center;background:#fffaf5;border:1px solid #d8c5b3;border-radius:18px;padding:30px 26px;box-shadow:0 24px 70px rgba(43,29,20,.28);color:#4b3024;animation:txPopIn .24s cubic-bezier(.2,.8,.2,1) both}
.transaction-success-icon{width:58px;height:58px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;background:#e9f3e8;color:#386641;font-size:30px;font-weight:700}
.transaction-success-card h2{margin:0 0 8px;font-size:21px;color:#4b3024}.transaction-success-card p{margin:0 0 22px;color:#765d4b;font-size:14px;line-height:1.5}.transaction-success-card button{min-width:110px;background:#6f4e37;border-color:#6f4e37;color:white}
@keyframes txFadeIn{from{opacity:0}to{opacity:1}}@keyframes txPopIn{from{opacity:0;transform:translateY(8px) scale(.96)}to{opacity:1;transform:translateY(0) scale(1)}}
@media(prefers-reduced-motion:reduce){.transaction-success-backdrop,.transaction-success-card{animation:none!important}}
body.popup-mode{min-height:100%;height:auto;overflow:auto!important;background:var(--page-bg,#f8faf8)!important}
body.popup-mode .sidebar,body.popup-mode .topbar,body.popup-mode .footer{display:none!important}
body.popup-mode .app-shell{display:block!important;min-height:0!important;height:auto!important}
body.popup-mode .main-area{margin:0!important;width:100%!important;min-width:0!important}
body.popup-mode .page-content{padding:18px!important;max-width:none!important;width:auto!important}
body.popup-mode .page-heading{margin:0 0 16px!important;gap:12px;align-items:flex-start}
body.popup-mode .page-heading h1{font-size:22px!important;margin:0 0 5px!important}
body.popup-mode .page-heading p{font-size:12px!important;margin:0!important}
body.popup-mode .permission-note{margin-bottom:14px!important;padding:12px!important}
body.popup-mode .panel{box-shadow:none!important}


/* Reliable responsive collapsible navigation. */
.sidebar{transition:transform .24s ease,width .24s ease;will-change:transform;}
.main-area{transition:margin-left .24s ease,width .24s ease;min-width:0;}
body.sidebar-collapsed .sidebar{transform:translateX(calc(-100% - 2px))!important;visibility:hidden;pointer-events:none;}
body.sidebar-collapsed .main-area{margin-left:0!important;width:100%!important;max-width:none!important;}
body.sidebar-mobile-open::after{content:"";position:fixed;inset:0;background:rgba(24,18,14,.42);z-index:999;}
@media(max-width:800px){
  .sidebar{position:fixed!important;left:0;top:0;bottom:0;height:100dvh;overflow-y:auto;z-index:1000;transform:translateX(calc(-100% - 2px));visibility:hidden;pointer-events:none;}
  .main-area{margin-left:0!important;width:100%!important;max-width:none!important;}
  body.sidebar-collapsed .sidebar{transform:translateX(calc(-100% - 2px))!important;visibility:hidden;pointer-events:none;}
  body.sidebar-mobile-open .sidebar{transform:translateX(0)!important;visibility:visible;pointer-events:auto;z-index:1000;box-shadow:12px 0 32px rgba(0,0,0,.18);}
  body.sidebar-mobile-open .main-area{margin-left:0!important;width:100%!important;}
}
@media(prefers-reduced-motion:reduce){.sidebar,.main-area{transition:none!important}}
</style>
<body class="<?= (($_GET['popup'] ?? '') === '1' && in_array($page, ['users','settings'], true)) ? 'popup-mode' : '' ?>">
<div class="app-shell">
<aside class="sidebar" id="sidebar">
  <a class="brand" href="index.php"><span class="brand-cup">☕</span><strong>BeanStack</strong><small>Inventory Management System</small></a>
  <div class="side-label">WORKSPACE</div>
  <nav class="nav-links">
    <a class="<?= $page==='dashboard'?'active':'' ?>" href="index.php"><span>⌂</span>Dashboard</a>
    <a class="<?= $page==='ingredients'?'active':'' ?>" href="index.php?page=ingredients"><span>▦</span>Ingredients</a>
    <a class="<?= $page==='reports'?'active':'' ?>" href="index.php?page=reports"><span>▥</span>Reports</a>
    <?php if(is_admin()): ?><a class="<?= $page==='audit_logs'?'active':'' ?>" href="index.php?page=audit_logs"><span>▤</span>Audit Logs</a><?php endif; ?>
  </nav>
  <div class="sidebar-bottom"><div class="leaf-decoration">❧</div></div>
</aside>
<main class="main-area">
<header class="topbar"><button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">☰</button><div class="breadcrumbs">Café Marieta <span>/</span> <?= e($titles[$page]) ?></div><div class="topbar-user-group"><button type="button" class="handover-mail-button" data-handover-open title="<?= is_admin() ? 'Open handover inbox' : 'Open messages and send a message' ?>" aria-label="<?= is_admin() ? 'Open handover inbox' : 'Open messages and send a message' ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg><?php if($handoverUnreadCount>0): ?><span class="handover-unread-badge" aria-label="<?= $handoverUnreadCount ?> unread messages"><?= $handoverUnreadCount>99?'99+':$handoverUnreadCount ?></span><?php endif; ?></button><button type="button" class="handover-mail-button low-stock-alert-button" data-low-stock-open title="Low stock alerts" aria-label="Open low stock alerts"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.3 4.3 2.8 17.2A1.8 1.8 0 0 0 4.4 20h15.2a1.8 1.8 0 0 0 1.6-2.8L13.7 4.3a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4m0 3h.01"/></svg><?php if($lowCount>0): ?><span class="handover-unread-badge"><?= $lowCount>99?'99+':$lowCount ?></span><?php endif; ?></button><div class="top-user-menu" id="topUserMenu"><button type="button" class="top-user-trigger" id="topUserMenuToggle" aria-expanded="false" aria-controls="topUserDropdown"><span class="avatar"><?= e(strtoupper(substr($_SESSION['full_name'],0,1))) ?></span><span class="top-user-copy"><strong><?= e($_SESSION['full_name']) ?></strong><small><?= e(ucfirst($_SESSION['role'])) ?></small></span><span class="top-user-chevron" aria-hidden="true">▾</span></button><nav class="top-user-dropdown" id="topUserDropdown" aria-label="Account menu"><?php if(is_admin()): ?><button type="button" class="account-modal-link" data-account-page="users"><span aria-hidden="true">♙</span>Users</button><?php endif; ?><button type="button" class="account-modal-link" data-account-page="settings"><span aria-hidden="true">⚙</span>Settings</button><div class="dropdown-divider" role="separator"></div><a class="dropdown-logout" href="logout.php" data-logout-confirm><span aria-hidden="true">↪</span>Logout</a></nav></div></div></header>
<div class="page-content">
<?php $flashMessage = $flashMessage ?? ($_SESSION['flash'] ?? null); unset($_SESSION['flash']); ?>
<?php $centerPopupMessages = ['Stock added.','Stock removed.','Waste recorded.','Ingredient added.','Ingredient updated.','Ingredient deleted.']; ?>
<?php if ($flashMessage && !in_array(($flashMessage['message'] ?? ''), $centerPopupMessages, true)): ?><div class="alert alert-<?= e($flashMessage['type']) ?>"><?= e($flashMessage['message']) ?><button class="alert-close" data-dismiss>×</button></div><?php endif; ?>
<?php if ($flashMessage && in_array(($flashMessage['message'] ?? ''), ['Stock added.','Stock removed.','Waste recorded.','Ingredient added.','Ingredient updated.','Ingredient deleted.'], true)): ?>
<div class="transaction-success-backdrop" id="transactionSuccessPopup" role="dialog" aria-modal="true" aria-labelledby="transactionSuccessTitle"><section class="transaction-success-card"><div class="transaction-success-icon" aria-hidden="true">✓</div><h2 id="transactionSuccessTitle"><?= e($flashMessage['message'] ?? 'Stock updated.') ?></h2><p><?= in_array(($flashMessage['message'] ?? ''), ['Ingredient added.','Ingredient updated.','Ingredient deleted.'], true) ? 'Changes saved.' : 'Inventory updated.' ?></p><button type="button" class="btn" id="closeTransactionSuccess">Okay</button></section></div>
<script>(function(){var popup=document.getElementById('transactionSuccessPopup');var close=document.getElementById('closeTransactionSuccess');function dismiss(){if(popup)popup.remove()}if(close)close.addEventListener('click',dismiss);if(popup)popup.addEventListener('click',function(e){if(e.target===popup)dismiss()});document.addEventListener('keydown',function(e){if(e.key==='Escape')dismiss()});})();</script>
<?php endif; ?>

<div class="handover-modal-backdrop" id="handoverNotesModal" role="dialog" aria-modal="true" aria-labelledby="handoverModalTitle" aria-hidden="true" style="display:none">
  <section class="handover-modal-card">
    <div class="panel-heading"><div><span class="eyebrow">MESSAGES</span><h2 id="handoverModalTitle"><?= is_admin() ? 'Message Inbox' : 'Messages' ?></h2><p><?= is_admin() ? 'Messages sent to your administrator account.' : 'Send a message to an administrator or another staff member.' ?></p></div><button type="button" class="icon-button" data-handover-close aria-label="Close messages">×</button></div>
    <div class="handover-modal-content">
      <?php if(!is_admin()): ?>
      <form method="post" class="handover-form"><input type="hidden" name="action" value="save_handover_note"><label for="handoverRecipientModal">Choose recipient</label><select name="recipient_user_id" id="handoverRecipientModal" required><option value="">Choose recipient</option><?php foreach($handoverRecipients as $recipientOption): ?><option value="<?= (int)$recipientOption['id'] ?>"><?= e($recipientOption['full_name']) ?> (<?= e(ucfirst((string)$recipientOption['role'])) ?> · <?= e($recipientOption['username']) ?>)</option><?php endforeach; ?></select><?php if(!$handoverRecipients): ?><small class="negative">No other user accounts found. Check the Users table.</small><?php endif; ?><label for="handoverMessageBody">Message</label><textarea name="message_body" id="handoverMessageBody" rows="3" maxlength="2000" placeholder="Write your message here..." required></textarea><div class="handover-form-footer"><small class="muted">Up to 2,000 characters.</small><button class="btn" type="submit" <?= !$handoverRecipients ? 'disabled' : '' ?>>Send message</button></div></form>
      <?php endif; ?>
      <div class="handover-notes-heading">Messages received</div>
      <?php if(!$handoverReceivedNotes): ?><div class="empty-state compact-empty">No messages received yet.</div><?php else: ?><div class="handover-note-list"><?php foreach($handoverReceivedNotes as $hn): ?><article class="handover-note <?= empty($hn['is_read']) ? 'is-unread' : '' ?>"><div class="handover-note-meta"><strong>From: <?= e($hn['full_name']) ?></strong><span><?= e(date('M d, Y · h:i A', strtotime($hn['created_at']))) ?></span></div><?php if(empty($hn['is_read'])): ?><div class="handover-note-unread">Unread</div><?php endif; ?><p><?= nl2br(e($hn['note'])) ?></p></article><?php endforeach; ?></div><?php endif; ?>
      <?php if(!is_admin()): ?>
      <div class="handover-notes-heading">Messages sent</div>
      <?php if(!$handoverSentNotes): ?><div class="empty-state compact-empty">You haven't sent any messages yet.</div><?php else: ?><div class="handover-note-list"><?php foreach($handoverSentNotes as $hn): ?><article class="handover-note"><div class="handover-note-meta"><strong>To: <?= e($hn['recipient_name'] ?? 'Deleted user') ?><?= !empty($hn['recipient_role']) ? ' (' . e(ucfirst((string)$hn['recipient_role'])) . ')' : '' ?></strong><span><?= e(date('M d, Y · h:i A', strtotime($hn['created_at']))) ?></span></div><p><?= nl2br(e($hn['note'])) ?></p></article><?php endforeach; ?></div><?php endif; ?>
      <?php endif; ?>
    </div>
  </section>
</div>
<div class="handover-modal-backdrop" id="lowStockAlertsModal" role="dialog" aria-modal="true" aria-labelledby="lowStockModalTitle" aria-hidden="true" style="display:none">
  <section class="handover-modal-card">
    <div class="panel-heading"><div><span class="eyebrow">ATTENTION REQUIRED</span><h2 id="lowStockModalTitle">Low Stock Alerts</h2><p>Ingredients at or below their minimum stock level.</p></div><button type="button" class="icon-button" data-low-stock-close aria-label="Close low stock alerts">×</button></div>
    <div class="handover-modal-content">
      <?php if(!$lowItems): ?><div class="empty-state compact-empty">No low-stock items. Everything is above its minimum level.</div><?php else: ?>
      <div class="table-scroll"><table><thead><tr><th>Item</th><th>Current</th><th>Minimum</th><th>Status</th></tr></thead><tbody><?php foreach($lowItems as $lp): ?><tr><td><strong><?= e($lp['name']) ?></strong></td><td><?= number_format((float)$lp['current_stock'],0) ?> <?= e(ucfirst((string)$lp['unit'])) ?></td><td><?= number_format((float)$lp['minimum_stock'],0) ?> <?= e(ucfirst((string)$lp['unit'])) ?></td><td><span class="status <?= (float)$lp['current_stock']<=0?'status-low':'status-warning' ?>"><?= (float)$lp['current_stock']<=0?'Out of stock':'Restock soon' ?></span></td></tr><?php endforeach; ?></tbody></table></div>
      <?php endif; ?>
      <div class="handover-form-footer"><span class="status status-low"><?= (int)$lowCount ?> alerts</span><a class="btn" href="index.php?page=ingredients">Manage Ingredients</a></div>
    </div>
  </section>
</div>


<?php if($page==='dashboard'): ?>
  <section class="hero-banner"><div><span class="eyebrow light">CAFÉ MARIETA · INVENTORY</span><h1>Good <?= e($beanStackGreeting) ?>, <?= e(trim(explode(' ', trim((string)($_SESSION['full_name'] ?? 'User')))[0])) ?>!</h1><p>A quick look at what needs attention today.</p><span class="dashboard-last-updated">◷ Inventory last updated: <?= $lastInventoryUpdate ? e(date('M d, Y · h:i A',strtotime($lastInventoryUpdate))) : 'No stock movements yet' ?></span></div><div class="hero-coffee">☕</div></section>
<div class="stat-grid dashboard-summary-grid">
 <a class="stat-card" href="index.php?page=ingredients" aria-label="View all ingredients"><div class="stat-icon">◉</div><div><span>Total ingredients</span><strong><?= $totalingredients ?></strong><small class="muted">View ingredients →</small></div></a>
 <a class="stat-card" href="index.php?page=alerts" aria-label="View low stock alerts"><div class="stat-icon danger-icon">⚠</div><div><span>Low Stock</span><strong><?= $dashboardLowCount ?></strong><small class="<?= $dashboardLowCount?'negative':'positive' ?>">View alerts →</small></div></a>
 <a class="stat-card" href="index.php?page=ingredients" aria-label="View ingredients and out of stock items"><div class="stat-icon">▣</div><div><span>Out of Stock</span><strong><?= $outOfStockCount ?></strong><small class="<?= $outOfStockCount?'negative':'positive' ?>">View ingredients →</small></div></a>
 <a class="stat-card" href="index.php?page=reports" aria-label="View today's inventory transactions"><div class="stat-icon">↻</div><div><span>Today's Transactions</span><strong><?= $todayCount ?></strong><small class="muted">View stock history →</small></div></a>
</div>
<div class="dashboard-expiry-indicator"><a href="index.php?page=ingredients" aria-label="View ingredients with upcoming expiration"><span class="expiry-indicator-icon">◷</span><span><strong><?= (int)$expiringSoonCount ?> expiring soon</strong><small>Items expiring within 30 days</small></span><span class="expiry-indicator-arrow">View ingredients →</span></a></div>
<div class="dashboard-grid"><section class="panel chart-panel"><div class="panel-heading"><div><h2>Stock Overview</h2><p>Stock In and Stock Out · last 7 days</p></div><span class="legend"><i class="legend-in"></i> Stock In <i class="legend-out"></i> Stock Out</span></div><div class="chart-wrap"><canvas id="stockChart"></canvas><div class="chart-fallback" data-chart-fallback="stock"><div style="font-weight:600;margin-bottom:12px">Stock In / Stock Out · last 7 days</div><?php $maxChartValue=max(array_merge($chartIn,$chartOut,[1])); foreach($chartLabels as $chartIndex=>$chartLabel): ?><div class="chart-fallback-row"><span><?= e($chartLabel) ?></span><div class="chart-fallback-track"><span class="chart-fallback-bar chart-fallback-in" style="width:<?= max(1,round(($chartIn[$chartIndex]??0)/$maxChartValue*100)) ?>%" title="Stock In"></span><span class="chart-fallback-bar chart-fallback-out" style="width:<?= max(1,round(($chartOut[$chartIndex]??0)/$maxChartValue*100)) ?>%" title="Stock Out / Waste"></span></div><span class="chart-fallback-values"><?= number_format((float)($chartIn[$chartIndex]??0),1) ?> / <?= number_format((float)($chartOut[$chartIndex]??0),1) ?></span></div><?php endforeach; ?><div class="chart-fallback-values">Mocha: Stock In · Caramel: Stock Out / Waste</div></div></div></section>
<section class="panel dashboard-quick-actions"><div><h2>Inventory Reports</h2><p class="muted">View detailed stock movement and inventory summaries.</p><a class="quick-action-link" href="index.php?page=reports">▥ View Reports <span aria-hidden="true">→</span></a></div></section>
<section class="panel activity-panel"><div class="panel-heading"><div><h2>Recent Activity</h2><p>Latest inventory movements</p></div><a class="text-link" href="index.php?page=reports">View all →</a></div><?php if(!$recent): ?><div class="empty-state">No transactions recorded yet.</div><?php else: ?><div class="activity-list"><?php foreach(array_slice($recent,0,5) as $t): ?><div class="activity-item"><div class="activity-icon <?= $t['transaction_type']==='Stock In'?'activity-in':'activity-out' ?>"><?= $t['transaction_type']==='Stock In'?'↙':'↗' ?></div><div><strong><?= e($t['transaction_type']) ?> · <?= e($t['product_name']) ?></strong><small><?= e(date('M d, Y · h:i A',strtotime($t['created_at']))) ?> · <?= e($t['full_name']??'Unknown user') ?></small></div></div><?php endforeach; ?></div><?php endif; ?></section>

</div>

<?php elseif($page==='ingredients'): ?>
<div class="page-heading ingredients-page-heading"><div><span class="eyebrow">INVENTORY CATALOG</span><h1>Ingredients</h1><p>Manage the ingredients and supplies used by Café Marieta.</p></div><?php if(is_admin()): ?><button class="btn add-product-heading" data-modal-open="productModal">＋ Add Ingredient</button><?php endif; ?></div>
<section class="panel table-panel"><div class="table-toolbar product-table-toolbar"><div class="product-search-actions"><div class="search-box">⌕ <input id="ingredientsearch" type="search" placeholder="Search by ingredient name, category, or unit..." autocomplete="off"><button type="button" id="clearingredientsearch" class="icon-button" aria-label="Clear search" title="Clear search" hidden>×</button></div><label class="product-category-filter-wrap" for="productCategoryFilter"><select id="productCategoryFilter" aria-label="Filter ingredients by category"><option value="all">All Categories</option><?php $dropdownCategories = $categories; usort($dropdownCategories, function($a, $b) { $aOther = in_array(mb_strtolower(trim($a['name'])), ['other','others'], true); $bOther = in_array(mb_strtolower(trim($b['name'])), ['other','others'], true); if ($aOther !== $bOther) return $aOther ? 1 : -1; return strcasecmp($a['name'], $b['name']); }); foreach($dropdownCategories as $categoryOption): ?><option value="<?= e(mb_strtolower($categoryOption['name'])) ?>"><?= e($categoryOption['name']) ?></option><?php endforeach; ?></select></label><div class="product-filter-chips" id="ingredientstockFilters" role="group" aria-label="Filter ingredients by stock status">
<button type="button" class="product-filter-chip active" data-stock-filter="all" aria-pressed="true">All ingredients <span class="filter-chip-count">0</span></button>
<button type="button" class="product-filter-chip" data-stock-filter="in" aria-pressed="false"><span class="filter-status-dot dot-in"></span> In Stock <span class="filter-chip-count">0</span></button>
<button type="button" class="product-filter-chip" data-stock-filter="low" aria-pressed="false"><span class="filter-status-dot dot-low"></span> Low Stock <span class="filter-chip-count">0</span></button>
<button type="button" class="product-filter-chip" data-stock-filter="out" aria-pressed="false"><span class="filter-status-dot dot-out"></span> Out of Stock <span class="filter-chip-count">0</span></button>
</div></div><span class="muted" id="productResultCount"><?= count($ingredients) ?> ingredients</span></div><div class="table-scroll"><table id="ingredientsDataTable"><thead><tr><th>Ingredients</th><th>Category</th><th>Unit</th><th>Current Stock</th><th>Minimum Stock Level</th><th>Expiration Date</th><th>Expiry Status</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php foreach($ingredients as $p): $low=(float)$p['current_stock']<=(float)$p['minimum_stock']; $out=(float)$p['current_stock']<=0; $stockStatus=$out?'out':($low?'low':'in'); ?><tr data-stock-status="<?= $stockStatus ?>" data-category="<?= e(mb_strtolower((string)($p['category'] ?? 'uncategorized'))) ?>"><td><strong><?= e($p['name']) ?></strong></td><td><?= e($p['category']??'Uncategorized') ?></td><td><?= e(ucfirst((string)$p['unit'])) ?></td><td><?= number_format((float)$p['current_stock'],0) ?></td><td><?= number_format((float)$p['minimum_stock'],0) ?></td><?php $expiry=$p['next_expiration']??null; $expiryStatus=!$expiry?'Not set':($expiry<date('Y-m-d')?'Expired':($expiry<=date('Y-m-d',strtotime('+30 days'))?'Expiring soon':'Valid')); ?><td><?= $expiry ? e(date('M d, Y',strtotime($expiry))) : '—' ?></td><td><span class="status <?= $expiryStatus==='Expired'?'status-low':($expiryStatus==='Expiring soon'?'status-warning':($expiryStatus==='Valid'?'status-ok':'')) ?>"><?= e($expiryStatus) ?></span></td><td><span class="status <?= $low?'status-low':'status-ok' ?>"><?= $out?'Out of stock':($low?'Low stock':'In stock') ?></span></td><td><div class="action-group"><button type="button" class="btn btn-small product-stock-action" data-product-id="<?= (int)$p['id'] ?>" data-product-name="<?= e($p['name']) ?>" data-product-stock="<?= e(number_format((float)$p['current_stock'], 0, '.', '')) ?>" data-product-unit="<?= e(ucfirst((string)$p['unit'])) ?>" data-stock-type="Stock In">Add Stock</button><button type="button" class="btn btn-small btn-outline product-stock-action" data-product-id="<?= (int)$p['id'] ?>" data-product-name="<?= e($p['name']) ?>" data-product-stock="<?= e(number_format((float)$p['current_stock'], 0, '.', '')) ?>" data-product-unit="<?= e(ucfirst((string)$p['unit'])) ?>" data-stock-type="Stock Out">Remove Stock</button><?php if(is_admin()): ?><button class="icon-button edit-product" title="Edit" data-product="<?= e(json_encode($p,JSON_HEX_APOS|JSON_HEX_QUOT)) ?>">✎</button><form method="post" class="delete-product-form" data-product-name="<?= e($p['name']) ?>"><input type="hidden" name="action" value="delete_product"><input type="hidden" name="id" value="<?= $p['id'] ?>"><button type="submit" class="icon-button delete-icon-button" title="Delete product" aria-label="Delete product"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/><path d="M10 11v5M14 11v5"/></svg></button></form><?php endif; ?></div></td></tr><?php endforeach; ?><tr id="productNoResults" hidden><td colspan="9" class="empty-cell">No matching ingredients. Try another keyword or stock filter.</td></tr></tbody></table></div></section>
<?php if(is_admin()): ?><div class="modal-backdrop" id="deleteProductConfirmModal" role="dialog" aria-modal="true" aria-labelledby="deleteProductConfirmTitle" style="display:none;align-items:center;justify-content:center;position:fixed;inset:0;z-index:9999;background:rgba(20,20,20,.55);padding:20px;"><div class="modal-card" style="width:min(100%,440px);margin:auto;"><div class="modal-heading"><div><span class="eyebrow">CONFIRM DELETE</span><h2 id="deleteProductConfirmTitle">Delete ingredient?</h2></div><button type="button" class="icon-button" id="deleteProductClose" aria-label="Close confirmation">×</button></div><p style="margin:12px 0 22px;color:var(--muted,#6b7280);">Are you sure you want to delete <strong id="deleteProductConfirmName">this ingredient</strong>? This action cannot be undone.</p><div class="modal-actions" style="display:flex;justify-content:flex-end;gap:10px;"><button type="button" class="btn btn-outline" id="cancelProductDelete">Cancel</button><button type="button" class="btn" id="confirmProductDelete" style="background:#dc2626;border-color:#dc2626;">Delete ingredient</button></div></div></div><?php endif; ?>
<?php if(is_admin()): ?><div class="modal-backdrop" id="productModal"><div class="modal-card"><div class="modal-heading"><div><span class="eyebrow">CATALOG ENTRY</span><h2 id="productModalTitle">Add ingredient</h2></div><button class="icon-button" data-modal-close>×</button></div><form method="post" class="form-grid" id="productForm"><input type="hidden" name="action" value="save_product"><input type="hidden" name="id" id="productId"><div><label>Product name</label><input name="name" id="productName" required placeholder="Ingredient name"></div><div><label>Category</label><select name="category_id" id="productCategory" required><?php foreach($categories as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select></div><div><label>Unit</label><select name="unit" id="productUnit"><?php foreach(['kg','g','liter','ml','bottle','can','pcs','pack','box'] as $u): ?><option value="<?= e($u) ?>"><?= e(ucfirst($u)) ?></option><?php endforeach; ?></select></div><div><label>Stock on hand</label><input type="number" min="0" step="1" name="adjusted_stock" id="ingredientstock" value="0" step="1" required><small class="muted">Actual quantity available. Changes are recorded in Stock Movement History.</small></div><div><label>Minimum Stock Level</label><input type="number" min="0" step="1" name="minimum_stock" id="productMinimum" value="0" step="1" required></div><div class="full-field" id="editExpirationField" hidden><label for="productEditExpiration">Expiration Date (earliest active batch)</label><input type="date" name="edit_expiration_date" id="productEditExpiration"><small class="muted">For existing stock, this edits the earliest active batch. For new deliveries, use Stock In to record each batch separately.</small></div><div class="full-field"><label>Unit cost (₱)</label><input type="number" min="0" step="1" name="unit_cost" id="productCost" value="0" step="any" required></div><div class="modal-actions full-field"><button type="button" class="btn btn-outline" data-modal-close>Cancel</button><button class="btn">Save product</button></div></form></div></div><?php endif; ?>
<div class="modal-backdrop" id="productTransactionModal"><div class="modal-card"><div class="modal-heading"><div><span class="eyebrow">UPDATE INVENTORY</span><h2 id="productTransactionTitle">Stock In / Stock Out / Waste / Adjustment</h2></div><button type="button" class="icon-button" data-modal-close>×</button></div><form method="post" class="form-grid" id="productTransactionForm"><input type="hidden" name="action" value="transaction"><input type="hidden" name="return_page" value="ingredients"><input type="hidden" name="ingredient_id" id="productTransactionProductId" required><div class="full-field"><label>Ingredients</label><input type="text" id="productTransactionProductName" readonly required><small class="muted" id="productTransactionAvailable"></small></div><input type="hidden" name="transaction_type" id="productTransactionType" value="Stock In"><div class="full-field"><label id="productTransactionQuantityLabel">Quantity</label><input name="quantity" id="productTransactionQuantity" type="number" min="0.01" step="1" required></div><div class="full-field" id="removeStockReasonField" hidden><label for="removeStockReason">Type</label><select name="remove_stock_reason" id="removeStockReason"><option value="usage">Used</option><option value="waste">Discarded</option></select></div><div class="full-field" id="stockInExpirationField" hidden><label for="stockInExpirationDate">Expiration Date (optional)</label><input type="date" name="expiration_date" id="stockInExpirationDate"><small class="muted">For deliveries with an expiration date. Leave blank if not applicable.</small></div><div class="full-field"><label for="transactionRemarks">Remarks</label><textarea id="transactionRemarks" name="remarks" maxlength="255" rows="6" placeholder="Add details about this stock movement..."></textarea></div><p class="form-note full-field">Saving records the movement and automatically updates current stock.</p><div class="modal-actions full-field"><button type="button" class="btn btn-outline" data-modal-close>Cancel</button><button class="btn">Save</button></div></form></div></div>
<style>
.ingredients-page-heading{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}.add-product-heading{white-space:nowrap;flex-shrink:0}.product-table-toolbar{align-items:center;flex-wrap:wrap;gap:12px}.product-search-actions{display:flex;align-items:stretch;gap:8px;flex:1 1 100%;min-width:0;flex-wrap:wrap}.product-search-actions .product-filter-chips{margin:0;display:flex;align-items:stretch;flex:1 1 100%;gap:8px;flex-wrap:wrap}.product-search-actions .search-box{flex:1 1 260px;min-width:220px;height:38px;min-height:38px;box-sizing:border-box;display:flex;align-items:center}.product-search-actions .search-box input{height:100%;min-height:0;box-sizing:border-box}.product-search-actions .product-category-filter-wrap{height:38px;flex:0 1 190px;min-width:160px}.product-search-actions .product-category-filter-wrap select{height:38px;box-sizing:border-box}.product-search-actions .product-filter-chip{height:30px;min-height:30px;box-sizing:border-box;padding:3px 7px;display:inline-flex;align-items:center;justify-content:center;gap:5px;white-space:nowrap;font-size:10px;line-height:14px;flex:0 1 auto}.product-search-actions .filter-chip-count{height:15px;min-width:15px;display:inline-flex;align-items:center;justify-content:center}
.add-product-toolbar{white-space:nowrap;flex-shrink:0}
.ingredients-page-heading{margin-bottom:18px}.ingredients-page-heading h1{margin-bottom:6px}.delete-icon-button{display:inline-flex;align-items:center;justify-content:center;color:#dc2626;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;width:34px;height:34px;cursor:pointer;transition:background .18s ease,color .18s ease,transform .18s ease}
.delete-icon-button:hover{background:#dc2626;color:#fff;border-color:#dc2626;transform:translateY(-1px)}
@media(max-width:1100px){.product-search-actions{flex-wrap:wrap}.product-search-actions .product-filter-chips{flex-basis:100%}.product-search-actions .product-filter-chip{flex:1 1 130px}}
@media(max-width:640px){.ingredients-page-heading .add-product-heading{width:100%;justify-content:center}.product-search-actions{width:100%;flex-wrap:wrap}.product-search-actions .search-box{min-width:100%}.add-product-toolbar{width:100%;justify-content:center}.product-search-actions .product-filter-chips{flex-basis:100%}}
.product-filter-chips{display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin:10px 0 2px}
.product-filter-chip{display:inline-flex;align-items:center;justify-content:center;gap:7px;border:1px solid var(--border-color,#e5e7eb);background:var(--card-bg,#fff);color:var(--text-color,#374151);border-radius:999px;padding:8px 12px;font:inherit;font-size:13px;line-height:1.2;cursor:pointer;transition:background .16s ease,border-color .16s ease,transform .16s ease;}
.product-filter-chip:hover{border-color:var(--primary,#8b6b4f);transform:translateY(-1px)}
.product-filter-chip.active{background:var(--primary,#6f4e37);border-color:var(--primary,#6f4e37);color:#fff;box-shadow:0 2px 7px rgba(0,0,0,.09)}
.filter-chip-count{display:inline-flex;min-width:22px;height:22px;align-items:center;justify-content:center;border-radius:999px;background:rgba(127,127,127,.13);font-size:11px;font-weight:700;padding:0 5px}
.product-filter-chip.active .filter-chip-count{background:rgba(255,255,255,.2);color:#fff}
.filter-status-dot{width:7px;height:7px;border-radius:50%;display:inline-block}.dot-in{background:#35a56a}.dot-low{background:#e0a12b}.dot-out{background:#d9534f}
.product-filter-chip.active .filter-status-dot{box-shadow:0 0 0 2px rgba(255,255,255,.25)}
@media(max-width:700px){.product-filter-chips{gap:6px}.product-search-actions .product-filter-chip{height:30px;min-height:30px;padding:3px 7px;font-size:10px}}

.number-stepper{display:flex;align-items:stretch;gap:6px;width:100%}.number-stepper input{flex:1;min-width:0}.stepper-btn{min-width:48px;border:1px solid var(--border,#d8d8d8);border-radius:8px;background:var(--surface,#f7f7f7);color:inherit;font-weight:700;cursor:pointer;padding:8px 10px}.stepper-btn:hover{filter:brightness(.96)}
/* FINAL Reports spacing override: deliberately visible separation between chart and tables. */
.report-grid.report-grid{margin-bottom:0!important;}
.report-grid + .report-table-stack{margin-top:44px!important;}
.report-table-stack.report-table-stack{gap:34px!important;padding-bottom:24px!important;}
.report-table-stack > .panel + .panel{margin-top:0!important;}
@media(max-width:600px){
  .report-grid + .report-table-stack{margin-top:28px!important;}
  .report-table-stack.report-table-stack{gap:24px!important;padding-bottom:18px!important;}
}
</style>
<script>
  (function() {
  var search = document.getElementById('ingredientsearch');
  var clear = document.getElementById('clearingredientsearch');
  var filters = document.getElementById('ingredientstockFilters');
  var categoryFilter = document.getElementById('productCategoryFilter');
  var count = document.getElementById('productResultCount');
  var table = document.getElementById('ingredientsDataTable');
  if (search && clear && filters && count && table) {
    var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr[data-stock-status]'));
    var chips = Array.prototype.slice.call(filters.querySelectorAll('[data-stock-filter]'));
    var noResults = document.getElementById('productNoResults');
    var activeStatus = 'all';
    function applyProductFilters() {
      var query = search.value.trim().toLocaleLowerCase();
      var visible = 0;
      var counts = {all: 0, in: 0, low: 0, out: 0};
      var selectedCategory = categoryFilter ? categoryFilter.value : 'all';
      rows.forEach(function(row) {
        var matchesText = row.textContent.toLocaleLowerCase().indexOf(query) !== -1;
        var matchesCategory = selectedCategory === 'all' || row.dataset.category === selectedCategory;
        if (matchesText && matchesCategory) {
          counts.all++;
          if (counts[row.dataset.stockStatus] !== undefined) counts[row.dataset.stockStatus]++;
        }
        var matchesStatus = activeStatus === 'all' || row.dataset.stockStatus === activeStatus;
        var show = matchesText && matchesStatus && matchesCategory;
        row.hidden = !show;
        if (show) visible++;
      });
       chips.forEach(function(chip) {
        var status = chip.dataset.stockFilter;
        var selected = status === activeStatus;
        chip.classList.toggle('active', selected);
        chip.setAttribute('aria-pressed', selected ? 'true' : 'false');
        var badge = chip.querySelector('.filter-chip-count');
        if (badge) badge.textContent = counts[status];
      });
      if (noResults) noResults.hidden = visible !== 0;
      count.textContent = visible + (visible === 1 ? ' product found' : ' ingredients found');
      clear.hidden = search.value.length === 0;
    }
    search.addEventListener('input', applyProductFilters);
    if (categoryFilter) categoryFilter.addEventListener('change', applyProductFilters);
    chips.forEach(function(chip) {
      chip.addEventListener('click', function() {
        activeStatus = chip.dataset.stockFilter;
        applyProductFilters();
      });
    });
    clear.addEventListener('click', function() {
      search.value = '';
      activeStatus = 'all';
      if (categoryFilter) categoryFilter.value = 'all';
      applyProductFilters();
      search.focus();
    });
    applyProductFilters();
  }
})();
// Numeric +1 / -1 controls in the product form.
document.querySelectorAll('.stepper-btn').forEach(function(button) {
  button.addEventListener('click', function() {
    var input = document.getElementById(button.dataset.stepTarget);
    if (!input) return;
    var increment = Number(button.dataset.step) || 0;
    var current = Number(input.value) || 0;
    var next = Math.max(Number(input.min) || 0, current + increment);
    input.value = String(Math.round(next * 100) / 100);
    input.dispatchEvent(new Event('change', {bubbles:true}));
  });
});
document.querySelectorAll('[data-modal-open="productModal"]').forEach(function(button) {
  button.addEventListener('click', function() {
    document.getElementById('editExpirationField').hidden = true;
    document.getElementById('productEditExpiration').value = '';
    document.getElementById('productModalTitle').textContent = 'Add ingredient';
    document.getElementById('productForm').reset();
    document.getElementById('productId').value = '';
  });
});

// Populate the shared product form when Edit is clicked.
['ingredientstock','productMinimum','productCost'].forEach(function(id) {
  var field = document.getElementById(id);
  if (!field) return;
  field.addEventListener('blur', function() {
    var value = String(field.value || '').trim();
    if (value === '') return;
    var number = Number(value);
    if (Number.isFinite(number)) field.value = number.toString();
  });
});

document.querySelectorAll('.edit-product').forEach(function(button) {
  button.addEventListener('click', function() {
    var p;
    try { p = JSON.parse(button.dataset.product); } catch (e) { return; }
    document.getElementById('productModalTitle').textContent = 'Edit product';
    document.getElementById('productId').value = p.id || '';
    document.getElementById('productName').value = p.name || '';
    document.getElementById('productCategory').value = p.category_id || '';
    document.getElementById('productUnit').value = String(p.unit || 'pcs').toLowerCase();
    function displayNumber(value, integerOnly) {
      var n = Number(String(value == null ? '0' : value).replace(/,/g, '').trim());
      if (!Number.isFinite(n)) return '0';
      if (integerOnly) n = Math.trunc(n);
      return String(n);
    }
    document.getElementById('ingredientstock').value = displayNumber(p.current_stock, true);
    document.getElementById('productMinimum').value = displayNumber(p.minimum_stock, true);
    document.getElementById('productCost').value = displayNumber(p.unit_cost, false);
    ['ingredientstock','productMinimum','productCost'].forEach(function(id) {
      var field = document.getElementById(id);
      if (field) field.setAttribute('value', field.value || '0');
    });
    document.getElementById('editExpirationField').hidden = false;
    document.getElementById('productEditExpiration').value = p.next_expiration || '';
    var modal = document.getElementById('productModal');
    modal.classList.add('open'); modal.style.display = 'flex';
  });
});
document.querySelectorAll('.product-stock-action').forEach(function(button) {
  button.addEventListener('click', function() {
    var modal = document.getElementById('productTransactionModal');
    var productId = document.getElementById('productTransactionProductId');
    var productName = document.getElementById('productTransactionProductName');
    var available = document.getElementById('productTransactionAvailable');
    var type = document.getElementById('productTransactionType');
    var title = document.getElementById('productTransactionTitle');
    var quantityInput = document.getElementById('productTransactionQuantity');
    var quantityLabel = document.getElementById('productTransactionQuantityLabel');
    var expirationField = document.getElementById('stockInExpirationField');
    var expirationInput = document.getElementById('stockInExpirationDate');
    var removeReasonField = document.getElementById('removeStockReasonField');
    var removeReasonSelect = document.getElementById('removeStockReason');
    productId.value = button.dataset.productId;
    productName.value = button.dataset.productName;
    var availableStock = Number(String(button.dataset.productStock || '0').replace(/,/g, ''));
    available.textContent = 'Available stock: ' + (Number.isFinite(availableStock) ? String(Math.trunc(availableStock)) : '0') + ' ' + button.dataset.productUnit;
    type.value = button.dataset.stockType;
    title.textContent = button.dataset.stockType === 'Stock In' ? 'Add Stock' : (button.dataset.stockType === 'Stock Out' ? 'Remove Stock' : button.dataset.stockType);
    if (expirationField) expirationField.hidden = button.dataset.stockType !== 'Stock In';
    if (removeReasonField) removeReasonField.hidden = button.dataset.stockType !== 'Stock Out';
    if (removeReasonSelect && button.dataset.stockType !== 'Stock Out') removeReasonSelect.value = 'usage';
    if (expirationInput && button.dataset.stockType !== 'Stock In') expirationInput.value = '';
    if (button.dataset.stockType === 'Stock Adjustment') {
      quantityLabel.textContent = 'Actual counted stock (' + button.dataset.productUnit + ')';
      quantityInput.min = '0';
      quantityInput.step = '1';
      quantityInput.value = button.dataset.productStock;
    } else {
      quantityLabel.textContent = 'Quantity (' + button.dataset.productUnit + ')';
      quantityInput.min = '0';
      quantityInput.step = '1';
      quantityInput.value = '';
    }
    modal.classList.add('open');
    modal.style.display = 'flex';
  });
});
// Centered confirmation modal for deleting ingredients; keep the browser's native confirm dialog out of this flow.
(function() {
  var modal = document.getElementById('deleteProductConfirmModal');
  if (!modal) return;
  var pendingForm = null;
  var nameLabel = document.getElementById('deleteProductConfirmName');
  var confirmButton = document.getElementById('confirmProductDelete');
  function closeDeleteModal() {
    modal.classList.remove('open');
    modal.style.display = 'none';
    pendingForm = null;
  }
  document.querySelectorAll('.delete-product-form').forEach(function(form) {
    form.addEventListener('submit', function(event) {
      event.preventDefault();
      pendingForm = form;
      nameLabel.textContent = form.dataset.productName || 'this ingredient';
      modal.classList.add('open');
      modal.style.display = 'flex';
      document.getElementById('cancelProductDelete').focus();
    });
  });
  confirmButton.addEventListener('click', function() {
    if (!pendingForm) return;
    var formToSubmit = pendingForm;
    pendingForm = null;
    HTMLFormElement.prototype.submit.call(formToSubmit);
  });
  document.getElementById('cancelProductDelete').addEventListener('click', closeDeleteModal);
  document.getElementById('deleteProductClose').addEventListener('click', closeDeleteModal);
  modal.addEventListener('click', function(event) {
    if (event.target === modal) closeDeleteModal();
  });
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && modal.classList.contains('open')) closeDeleteModal();
  });
})();

// Centered confirmation popup for deleting user accounts.
(function() {
  var modal = document.getElementById('deleteUserConfirmModal');
  if (!modal) return;
  var pendingForm = null;
  var nameLabel = document.getElementById('deleteUserConfirmName');
  var confirmButton = document.getElementById('confirmUserDelete');
  var cancelButton = document.getElementById('cancelUserDelete');
  var closeButton = document.getElementById('deleteUserClose');
  if (!confirmButton || !cancelButton || !closeButton || !nameLabel) return;

  function closeModal() {
    modal.classList.remove('open');
    modal.style.display = 'none';
    pendingForm = null;
  }

  document.querySelectorAll('.delete-user-form').forEach(function(form) {
    form.addEventListener('submit', function(event) {
      event.preventDefault();
      pendingForm = form;
      nameLabel.textContent = form.dataset.userName || 'this user';
      modal.classList.add('open');
      modal.style.display = 'flex';
      cancelButton.focus();
    });
  });
  confirmButton.addEventListener('click', function() {
    if (!pendingForm) return;
    var formToSubmit = pendingForm;
    pendingForm = null;
    HTMLFormElement.prototype.submit.call(formToSubmit);
  });
  cancelButton.addEventListener('click', closeModal);
  closeButton.addEventListener('click', closeModal);
  modal.addEventListener('click', function(event) {
    if (event.target === modal) closeModal();
  });
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && modal.classList.contains('open')) closeModal();
  });
})();

document.querySelectorAll('[data-modal-open]').forEach(function(button) {
  button.addEventListener('click', function() {
    var modalId = button.getAttribute('data-modal-open');
    var modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.add('open');
      modal.style.display = 'flex';
    }
  });
});
document.querySelectorAll('[data-modal-close]').forEach(function(button) {
  button.addEventListener('click', function(event) {
    event.preventDefault();
    var modal = button.closest('.modal-backdrop');
    if (modal) {
      modal.classList.remove('open');
      modal.style.display = 'none';
    }
  });
});
document.querySelectorAll('.modal-backdrop').forEach(function(modal) {
  modal.addEventListener('click', function(event) {
    if (event.target === modal) {
      modal.classList.remove('open');
      modal.style.display = 'none';
    }
  });
});
document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    document.querySelectorAll('.modal-backdrop.open').forEach(function(modal) {
      modal.classList.remove('open');
      modal.style.display = 'none';
    });
  }
});
</script>

<?php elseif($page==='transactions'): ?>
<div class="page-heading"><div><span class="eyebrow">STOCK MOVEMENT LOG</span><h1>Stock Movement History</h1><p>Review recorded stock movements. <?php if(is_admin()): ?>Record incoming deliveries from the ingredients page.<?php else: ?>Record stock usage and waste from the ingredients page.<?php endif; ?></p></div><a class="btn" href="index.php?page=ingredients">Go to ingredients</a></div>
<div class="quick-stats"><div class="quick-stat"><span>Stock In · 7 days</span><strong><?= number_format($stockIn,2) ?></strong></div><div class="quick-stat"><span>Stock Out / Waste · 7 days</span><strong><?= number_format($stockOut,2) ?></strong></div><div class="quick-stat"><span>Total logged transactions</span><strong><?= count($transactions) ?></strong></div></div>
<section class="panel table-panel"><div class="table-toolbar"><div class="search-box">⌕ <input id="tableSearch" placeholder="Search item, type, staff, remarks..."></div><span class="muted">Latest 100 records</span></div><div class="table-scroll"><table id="dataTable"><thead><tr><th>Date & Time</th><th>Type</th><th>Item</th><th>Quantity</th><th>Remarks</th><th>Recorded by</th></tr></thead><tbody><?php foreach($transactions as $t): ?><tr><td><?= e(date('M d, Y · h:i A',strtotime($t['created_at']))) ?></td><td><span class="status <?= $t['transaction_type']==='Stock In'?'status-ok':'status-low' ?>"><?= e($t['transaction_type']) ?></span></td><td><strong><?= e($t['product_name']) ?></strong></td><td><?= e($t['quantity']) ?> <?= e(ucfirst((string)$t['unit'])) ?></td><td><?= e($t['remarks']?:'—') ?></td><td><?= e($t['full_name']??'Unknown') ?></td></tr><?php endforeach; ?></tbody></table></div></section>


<?php elseif($page==='alerts'): ?>
<div class="page-heading"><div><span class="eyebrow">ATTENTION REQUIRED</span><h1>Low Stock Alerts</h1><p>Items that may need restocking before the next café shift.</p></div><a class="btn btn-outline" href="index.php?page=ingredients">View all ingredients</a></div>
<section class="panel table-panel"><div class="table-toolbar"><div><h2>Stock level check</h2><p class="muted">Items are flagged when current stock is at or below the minimum stock level.</p></div><span class="status status-low"><?= $lowCount ?> alerts</span></div><div class="table-scroll"><table><thead><tr><th>Item</th><th>Current Stock</th><th>Minimum Stock Level</th><th>Shortage</th><th>Status</th><th>Action</th></tr></thead><tbody><?php foreach($lowItems as $p): ?><tr><td><strong><?= e($p['name']) ?></strong></td><td><?= number_format((float)$p['current_stock'],0) ?> <?= e(ucfirst((string)$p['unit'])) ?></td><td><?= number_format((float)$p['minimum_stock'],0) ?> <?= e(ucfirst((string)$p['unit'])) ?></td><td><?= number_format(max(0,(float)$p['minimum_stock']-(float)$p['current_stock']),0) ?> <?= e(ucfirst((string)$p['unit'])) ?></td><td><span class="status <?= (float)$p['current_stock']<=0?'status-low':'status-warning' ?>"><?= (float)$p['current_stock']<=0?'Out of stock':'Restock soon' ?></span></td><td><a class="btn btn-small" href="index.php?page=ingredients">Manage in ingredients</a></td></tr><?php endforeach; ?><?php if(!$lowItems): ?><tr><td colspan="6" class="empty-cell">No low-stock items. Everything is above its minimum level.</td></tr><?php endif; ?></tbody></table></div></section>


<?php elseif($page==='reports'): ?>
<div class="page-heading"><div><span class="eyebrow">INVENTORY INSIGHTS</span><h1>Inventory Reports</h1><p>A snapshot of stock value, movement, and ingredient levels.</p></div><button class="btn btn-outline" onclick="window.print()">⇩ Print / Save PDF</button></div>
<div class="stat-grid report-stats"><article class="stat-card"><div class="stat-icon">▣</div><div><span>Total Stock Value</span><strong>₱<?= number_format($totalValue,2) ?></strong><small class="muted">Estimated at recorded unit cost</small></div></article><article class="stat-card"><div class="stat-icon">▦</div><div><span>Ingredients Tracked</span><strong><?= $totalingredients ?></strong><small class="muted">All categories</small></div></article><article class="stat-card"><div class="stat-icon danger-icon">⚠</div><div><span>Needs Attention</span><strong><?= $lowCount ?></strong><small class="negative">Low / out of stock</small></div></article><article class="stat-card"><div class="stat-icon">↻</div><div><span>Today's Transactions</span><strong><?= $todayCount ?></strong><small class="muted">Records logged today</small></div></article><article class="stat-card"><div class="stat-icon">⇄</div><div><span>Transactions · 7 days</span><strong><?= number_format($stockIn+$stockOut,2) ?></strong><small class="muted">Total units moved</small></div></article></div>
<div class="dashboard-grid report-grid"><section class="panel chart-panel"><div class="panel-heading"><div><h2>Stock Movement</h2><p>Quantity moved over the last 7 days · hover for details</p></div></div><div class="chart-wrap"><canvas id="stockChart"></canvas><div class="chart-fallback" data-chart-fallback="stock"><div style="font-weight:600;margin-bottom:12px">Stock In / Stock Out · last 7 days</div><?php $maxChartValue=max(array_merge($chartIn,$chartOut,[1])); foreach($chartLabels as $chartIndex=>$chartLabel): ?><div class="chart-fallback-row"><span><?= e($chartLabel) ?></span><div class="chart-fallback-track"><span class="chart-fallback-bar chart-fallback-in" style="width:<?= max(1,round(($chartIn[$chartIndex]??0)/$maxChartValue*100)) ?>%" title="Stock In"></span><span class="chart-fallback-bar chart-fallback-out" style="width:<?= max(1,round(($chartOut[$chartIndex]??0)/$maxChartValue*100)) ?>%" title="Stock Out / Waste"></span></div><span class="chart-fallback-values"><?= number_format((float)($chartIn[$chartIndex]??0),1) ?> / <?= number_format((float)($chartOut[$chartIndex]??0),1) ?></span></div><?php endforeach; ?><div class="chart-fallback-values">Mocha: Stock In · Caramel: Stock Out / Waste</div></div></div></section><section class="panel"><div class="panel-heading"><div><h2>Stock by Category</h2><p>Current quantity grouped by category</p></div></div><div class="chart-wrap"><canvas id="categoryChart"></canvas><div class="chart-fallback" data-chart-fallback="category"><div style="font-weight:600;margin-bottom:12px">Current stock by category</div><?php $categoryFallbackRows=$pdo->query("SELECT c.name, COALESCE(SUM(p.current_stock),0) AS total FROM categories c LEFT JOIN ingredients p ON p.category_id=c.id GROUP BY c.id,c.name ORDER BY total DESC")->fetchAll(); $maxCategoryValue=max(array_merge(array_map(fn($r)=>(float)$r['total'],$categoryFallbackRows),[1])); foreach($categoryFallbackRows as $categoryFallbackRow): ?><div class="chart-fallback-row"><span><?= e($categoryFallbackRow['name']) ?></span><div class="chart-fallback-track"><span class="chart-fallback-bar chart-fallback-category" style="width:<?= max(1,round((float)$categoryFallbackRow['total']/$maxCategoryValue*100)) ?>%"></span></div><span class="chart-fallback-values"><?= number_format((float)$categoryFallbackRow['total'],2) ?></span></div><?php endforeach; ?><?php if(!$categoryFallbackRows): ?><p class="muted">No category data yet.</p><?php endif; ?></div></div></section></div>
<div class="report-table-stack">
<section class="panel table-panel"><div class="panel-heading"><div><h2>Inventory Valuation</h2><p>Current quantity multiplied by unit cost.</p></div></div><div class="table-scroll"><table><thead><tr><th>Ingredient</th><th>Category</th><th>Current Stock</th><th>Unit Cost</th><th>Stock Value</th><th>Status</th></tr></thead><tbody><?php foreach($ingredients as $p): ?><tr><td><strong><?= e($p['name']) ?></strong></td><td><?= e($p['category']??'Uncategorized') ?></td><td><?= number_format((float)$p['current_stock'],0) ?> <?= e(ucfirst((string)$p['unit'])) ?></td><td>₱<?= number_format((float)$p['unit_cost'],2) ?></td><td><strong>₱<?= number_format((float)$p['current_stock']*(float)$p['unit_cost'],2) ?></strong></td><td><span class="status <?= (float)$p['current_stock']<=(float)$p['minimum_stock']?'status-low':'status-ok' ?>"><?= (float)$p['current_stock']<=(float)$p['minimum_stock']?'Low stock':'In stock' ?></span></td></tr><?php endforeach; ?></tbody></table></div></section>

<section class="panel table-panel"><div class="panel-heading"><div><h2>Today's Transactions</h2><p>All inventory movements recorded today.</p></div><span class="status status-ok"><?= count($todayTransactions) ?> records</span></div><div class="table-toolbar"><div class="report-filter-controls"><input type="search" id="todayTransactionsSearch" placeholder="Search item, staff, remarks..." aria-label="Search today's transactions"><select id="todayTransactionsType" aria-label="Filter today's transactions by type"><option value="all">All transaction types</option><option value="Stock In">Stock In</option><option value="Stock Out">Stock Out</option><option value="Waste">Waste</option><option value="Adjustment">Adjustment</option></select></div><span class="report-results-count" id="todayTransactionsCount"><?= count($todayTransactions) ?> records shown</span></div><div class="table-scroll"><table id="todayTransactionsTable"><thead><tr><th>Date &amp; Time</th><th>Type</th><th>Item</th><th>Quantity</th><th>Remarks</th><th>Recorded by</th></tr></thead><tbody><?php foreach($todayTransactions as $t): ?><tr data-transaction-type="<?= e($t['transaction_type']) ?>"><td><?= e(date('M d, Y · h:i A',strtotime($t['created_at']))) ?></td><td><span class="status <?= $t['transaction_type']==='Stock In'?'status-ok':'status-low' ?>"><?= e($t['transaction_type']) ?></span></td><td><strong><?= e($t['product_name']) ?></strong></td><td><?= e($t['quantity']) ?> <?= e(ucfirst((string)$t['unit'])) ?></td><td><?= e($t['remarks']?:'—') ?></td><td><?= e($t['full_name']??'Unknown') ?></td></tr><?php endforeach; ?><?php if(!$todayTransactions): ?><tr><td colspan="6" class="empty-cell">No transactions recorded today.</td></tr><?php endif; ?></tbody></table></div></section>

<section class="panel table-panel"><div class="panel-heading"><div><h2>Stock Movement History</h2><p>Latest 100 stock-in, stock-out, waste, and adjustment records.</p></div></div><div class="table-toolbar"><div class="search-box">⌕ <input id="tableSearch" placeholder="Search item, type, staff, remarks..."></div><span class="muted">Latest 100 records</span></div><div class="table-scroll"><table id="dataTable"><thead><tr><th>Date &amp; Time</th><th>Type</th><th>Item</th><th>Quantity</th><th>Remarks</th><th>Recorded by</th></tr></thead><tbody><?php foreach($transactions as $t): ?><tr><td><?= e(date('M d, Y · h:i A',strtotime($t['created_at']))) ?></td><td><span class="status <?= $t['transaction_type']==='Stock In'?'status-ok':'status-low' ?>"><?= e($t['transaction_type']) ?></span></td><td><strong><?= e($t['product_name']) ?></strong></td><td><?= e($t['quantity']) ?> <?= e(ucfirst((string)$t['unit'])) ?></td><td><?= e($t['remarks']?:'—') ?></td><td><?= e($t['full_name']??'Unknown') ?></td></tr><?php endforeach; ?><?php if(!$transactions): ?><tr><td colspan="6" class="empty-cell">No stock movements recorded yet.</td></tr><?php endif; ?></tbody></table></div></section>
</div>

<?php elseif($page==='users'): ?>
<div class="page-heading"><div><span class="eyebrow">ACCESS CONTROL</span><h1>User Management</h1><p>Create accounts and assign staff or administrator access.</p></div><button class="btn" data-modal-open="userModal">＋ Add User</button></div>
<div class="permission-note"><strong>Role permissions</strong><span><b>Administrator:</b> manage ingredients, users, reports, and all inventory activity.</span><span><b>Staff:</b> view ingredients, record Stock Out and Waste, and view stock history and low-stock alerts.</span></div>
<section class="panel table-panel"><div class="table-scroll"><table><thead><tr><th>Name</th><th>Username</th><th>Role</th><th>Created</th><th>Action</th></tr></thead><tbody><?php foreach($users as $u): ?><tr><td><strong><?= e($u['full_name']) ?></strong></td><td><?= e($u['username']) ?></td><td><span class="status <?= $u['role']==='admin'?'status-admin':'status-ok' ?>"><?= e(ucfirst($u['role'])) ?></span></td><td><?= e(date('M d, Y',strtotime($u['created_at']))) ?></td><td><?php if((int)$u['id'] !== (int)($_SESSION['user_id'] ?? 0)): ?><form method="post" class="delete-user-form" data-user-name="<?= e($u['full_name']) ?>"><input type="hidden" name="popup_mode" value="<?= (($_GET['popup'] ?? '') === '1') ? '1' : '0' ?>"><input type="hidden" name="action" value="delete_user"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><button type="submit" class="icon-button delete-icon-button" title="Delete user" aria-label="Delete <?= e($u['full_name']) ?>"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/><path d="M10 11v5M14 11v5"/></svg></button></form><?php else: ?><span class="muted" title="You cannot delete the account you are using">Current user</span><?php endif; ?></td></tr><?php endforeach; ?><?php if(!$users): ?><tr><td colspan="5" class="empty-cell">No user accounts found.</td></tr><?php endif; ?></tbody></table></div></section>
<div class="modal-backdrop" id="deleteUserConfirmModal" role="dialog" aria-modal="true" aria-labelledby="deleteUserConfirmTitle" style="display:none;align-items:center;justify-content:center;position:fixed;inset:0;z-index:9999;background:rgba(20,20,20,.55);padding:20px;"><div class="modal-card" style="width:min(100%,440px);margin:auto;"><div class="modal-heading"><div><span class="eyebrow">CONFIRM DELETE</span><h2 id="deleteUserConfirmTitle">Delete user?</h2></div><button type="button" class="icon-button" id="deleteUserClose" aria-label="Close confirmation">×</button></div><p style="margin:12px 0 22px;color:var(--muted,#6b7280);">Are you sure you want to delete <strong id="deleteUserConfirmName">this user</strong>? This action cannot be undone.</p><div class="modal-actions" style="display:flex;justify-content:flex-end;gap:10px;"><button type="button" class="btn btn-outline" id="cancelUserDelete">Cancel</button><button type="button" class="btn" id="confirmUserDelete" style="background:#dc2626;border-color:#dc2626;">Delete user</button></div></div></div>
<div class="modal-backdrop" id="userModal"><div class="modal-card"><div class="modal-heading"><div><span class="eyebrow">NEW ACCOUNT</span><h2>Add a user</h2></div><button class="icon-button" data-modal-close>×</button></div><form method="post" class="form-grid"><input type="hidden" name="popup_mode" value="<?= (($_GET['popup'] ?? '') === '1') ? '1' : '0' ?>"><input type="hidden" name="action" value="save_user"><div class="full-field"><label>Full name</label><input name="full_name" required></div><div class="full-field"><label>Username</label><input name="username" required></div><div class="full-field"><label>Temporary password (8+ characters)</label><input type="password" name="password" minlength="8" required></div><div class="full-field"><label>Role</label><select name="role"><option value="staff">Staff</option><option value="admin">Administrator</option></select></div><div class="modal-actions full-field"><button type="button" class="btn btn-outline" data-modal-close>Cancel</button><button class="btn">Create account</button></div></form></div></div>

<?php elseif($page==='audit_logs'): ?>
<?php
$auditStartDate = trim((string)($_GET['audit_start_date'] ?? ''));
$auditEndDate = trim((string)($_GET['audit_end_date'] ?? ''));
$auditStaffId = trim((string)($_GET['audit_staff_id'] ?? ''));
$auditStaff = $pdo->query("SELECT id, full_name, username FROM users ORDER BY full_name ASC")->fetchAll();
$auditWhere = [];
$auditParams = [];
if ($auditStartDate !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $auditStartDate)) {
    $auditWhere[] = 'a.created_at >= ?';
    $auditParams[] = $auditStartDate . ' 00:00:00';
}
if ($auditEndDate !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $auditEndDate)) {
    $auditWhere[] = 'a.created_at < DATE_ADD(?, INTERVAL 1 DAY)';
    $auditParams[] = $auditEndDate;
}
if ($auditStaffId !== '' && ctype_digit($auditStaffId)) {
    $auditWhere[] = 'a.user_id = ?';
    $auditParams[] = (int)$auditStaffId;
}
$auditSql = "SELECT a.id, a.action, a.details, a.created_at, u.full_name, u.username FROM audit_logs a LEFT JOIN users u ON u.id=a.user_id";
if ($auditWhere) $auditSql .= ' WHERE ' . implode(' AND ', $auditWhere);
$auditSql .= ' ORDER BY a.created_at DESC, a.id DESC LIMIT 500';
$auditStmt = $pdo->prepare($auditSql);
$auditStmt->execute($auditParams);
$auditLogs = $auditStmt->fetchAll();
?>
<div class="page-heading"><div><span class="eyebrow">SECURITY & ACCOUNTABILITY</span><h1>Audit Logs</h1><p>Review important changes and inventory actions performed in BeanStack.</p></div><button class="btn btn-outline audit-print-button" type="button" onclick="window.print()">⇩ Print / Save PDF</button></div>
<section class="panel table-panel"><div class="table-toolbar"><div><h2>Audit activity</h2><p class="muted">Filter by date and staff. Showing up to 500 matching events.</p></div><span class="status status-admin"><?= count($auditLogs) ?> records</span></div>
<form method="get" action="index.php" class="audit-filter-controls"><input type="hidden" name="page" value="audit_logs"><div class="audit-date-range"><label class="audit-range-heading">Date range</label><div class="audit-date-inputs"><div><label for="auditStartDate">From</label><input type="date" id="auditStartDate" name="audit_start_date" value="<?= e($auditStartDate) ?>"></div><span class="audit-date-separator" aria-hidden="true">to</span><div><label for="auditEndDate">To</label><input type="date" id="auditEndDate" name="audit_end_date" value="<?= e($auditEndDate) ?>"></div></div></div><div class="audit-staff-filter"><label for="auditStaffId">Staff / User</label><select id="auditStaffId" name="audit_staff_id"><option value="">All users</option><?php foreach($auditStaff as $staffOption): ?><option value="<?= (int)$staffOption['id'] ?>" <?= $auditStaffId !== '' && (string)$staffOption['id'] === $auditStaffId ? 'selected' : '' ?>><?= e($staffOption['full_name']) ?> (<?= e($staffOption['username']) ?>)</option><?php endforeach; ?></select></div><div class="audit-filter-actions"><button type="submit" class="btn">Apply Filters</button><a class="btn btn-outline" href="index.php?page=audit_logs">Clear</a></div></form>
<div class="table-scroll"><table><thead><tr><th>Date &amp; Time</th><th>User</th><th>Action</th><th>Details</th></tr></thead><tbody><?php foreach($auditLogs as $log): ?><tr><td><?= e(date('M d, Y · h:i A', strtotime($log['created_at']))) ?></td><td><?= e($log['full_name'] ?? 'Unknown user') ?><br><small class="muted"><?= e($log['username'] ?? '') ?></small></td><td><span class="status status-admin"><?= e($log['action']) ?></span></td><td><?= e($log['details']) ?></td></tr><?php endforeach; ?><?php if(!$auditLogs): ?><tr><td colspan="4" class="empty-cell">No audit events match the selected filters.</td></tr><?php endif; ?></tbody></table></div></section>

<?php elseif($page==='settings'): ?>
  <div class="page-heading"><div><span class="eyebrow">ACCOUNT PREFERENCES</span><h1>Settings</h1><p>Manage your sign-in credentials and account details.</p></div></div>
<div class="settings-grid"><section class="panel settings-profile"><div class="large-avatar"><?= e(strtoupper(substr($_SESSION['full_name'],0,1))) ?></div><h2><?= e($_SESSION['full_name']) ?></h2><p><?= e($_SESSION['username']) ?></p><span class="status <?= is_admin()?'status-admin':'status-ok' ?>"><?= e(ucfirst($_SESSION['role'])) ?></span><hr><p class="muted">BeanStack · Café Marieta Inventory System</p></section><section class="panel"><div class="panel-heading"><div><h2>Change password</h2><p>Use at least 8 characters.</p></div></div><form method="post" class="stack-form"><input type="hidden" name="popup_mode" value="<?= (($_GET['popup'] ?? '') === '1') ? '1' : '0' ?>"><input type="hidden" name="action" value="change_password"><label>Current password</label><input type="password" name="current_password" required><label>New password</label><input type="password" name="new_password" minlength="8" required><button class="btn">Update password</button></form></section></div>
<?php endif; ?>
<div class="account-page-modal" id="accountPageModal" role="dialog" aria-modal="true" aria-labelledby="accountPageModalTitle" aria-hidden="true"><section class="account-page-modal-card"><header class="account-page-modal-header"><strong id="accountPageModalTitle">Account</strong><button type="button" class="account-page-modal-close" id="accountPageModalClose" aria-label="Close account window">×</button></header><iframe id="accountPageModalFrame" title="Account settings" src="about:blank"></iframe></section></div>
<div class="logout-confirm-backdrop" id="logoutConfirmModal" role="dialog" aria-modal="true" aria-labelledby="logoutConfirmTitle" aria-describedby="logoutConfirmDescription"><div class="logout-confirm-card"><div class="logout-confirm-icon" aria-hidden="true">↪</div><h2 id="logoutConfirmTitle">Log out of BeanStack?</h2><p id="logoutConfirmDescription">Are you sure you want to log out? Make sure you have finished your current work.</p><div class="logout-confirm-actions"><button type="button" class="btn btn-outline" id="cancelLogout">Stay logged in</button><a class="btn" id="confirmLogout" href="logout.php" style="background:#b42318;border-color:#b42318;color:#fff;text-decoration:none;display:inline-flex;align-items:center;justify-content:center">Yes, log out</a></div></div></div>
<footer class="footer"><span>© <?= date('Y') ?> BeanStack · Café Marieta</span><span>Inventory made clearer, one cup at a time.</span></footer>
</div></main></div>
<script>
  window.beanStackData = {
 labels: <?= json_encode($chartLabels) ?>,
 stockIn: <?= json_encode($chartIn) ?>,
 stockOut: <?= json_encode($chartOut) ?>,
 categoryLabels: <?= json_encode(array_column($pdo->query("SELECT c.name, COALESCE(SUM(p.current_stock),0) AS total FROM categories c LEFT JOIN ingredients p ON p.category_id=c.id GROUP BY c.id,c.name ORDER BY total DESC")->fetchAll(),'name')) ?>,
 categoryValues: <?= json_encode(array_map('floatval',array_column($pdo->query("SELECT c.name, COALESCE(SUM(p.current_stock),0) AS total FROM categories c LEFT JOIN ingredients p ON p.category_id=c.id GROUP BY c.id,c.name ORDER BY total DESC")->fetchAll(),'total'))) ?>
};
function initializeBeanStackCharts() {
 if (typeof Chart === 'undefined' || !window.beanStackData) return false;
 const stockCanvas=document.getElementById('stockChart');
 if (stockCanvas) {
   let stockChart=Chart.getChart(stockCanvas);
   if (!stockChart) {
     stockChart=new Chart(stockCanvas,{type:'bar',data:{labels:window.beanStackData.labels||[],datasets:[
       {label:'Stock In',data:window.beanStackData.stockIn||[],backgroundColor:'#6f4e37',hoverBackgroundColor:'#6f4e37',borderColor:'#6f4e37',hoverBorderColor:'#6f4e37',borderWidth:1,borderRadius:7,borderSkipped:false,maxBarThickness:34},
       {label:'Stock Out / Waste',data:window.beanStackData.stockOut||[],backgroundColor:'#c08a5b',hoverBackgroundColor:'#c08a5b',borderColor:'#c08a5b',hoverBorderColor:'#c08a5b',borderWidth:1,borderRadius:7,borderSkipped:false,maxBarThickness:34}
     ]},options:{responsive:true,maintainAspectRatio:false,animation:{duration:650,easing:'easeOutQuart'},interaction:{mode:'nearest',intersect:true,axis:'xy'},plugins:{legend:{position:'bottom',align:'center',labels:{usePointStyle:true,pointStyle:'circle',boxWidth:8,padding:14,color:'#5f665d',font:{family:'inherit',size:12}}},tooltip:{mode:'nearest',intersect:true,filter:function(item){return item.datasetIndex===item.chart.getActiveElements()[0]?.datasetIndex;},callbacks:{label:function(ctx){return ' '+ctx.dataset.label+': '+Number(ctx.parsed.y||0).toLocaleString();}}}},scales:{x:{grid:{display:false}},y:{beginAtZero:true,ticks:{precision:0},grid:{color:'rgba(120,130,120,.13)'}}}}});
     const stockFallback=stockCanvas.parentElement.querySelector('[data-chart-fallback="stock"]'); if(stockFallback)stockFallback.style.display='none';
   }
 }
 const categoryCanvas=document.getElementById('categoryChart');
 if(categoryCanvas && !Chart.getChart(categoryCanvas)) { const labels=window.beanStackData.categoryLabels||[]; const values=window.beanStackData.categoryValues||[]; new Chart(categoryCanvas,{type:'doughnut',data:{labels:labels.length?labels:['No categories yet'],datasets:[{label:'Current Stock',data:labels.length?values:[1],backgroundColor:['#6f4e37','#8b5e3c','#b08968','#c08a5b','#d6b995','#9b7653','#5b4636','#e3d2bd'],hoverOffset:8,borderColor:'#fffdf8',borderWidth:3,hoverBorderColor:'#fffdf8'}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{usePointStyle:true,pointStyle:'circle',boxWidth:8,padding:12,color:'#5f665d',font:{family:'inherit',size:12}}},tooltip:{enabled:labels.length>0,backgroundColor:'#4b3024',titleColor:'#fffdf8',bodyColor:'#fffdf8',padding:12,cornerRadius:8}}}}); const categoryFallback=categoryCanvas.parentElement.querySelector('[data-chart-fallback="category"]'); if(categoryFallback)categoryFallback.style.display='none'; }
 return true;
}
function startBeanStackCharts(){if(initializeBeanStackCharts())return;let attempts=0;const retry=window.setInterval(function(){attempts++;if(initializeBeanStackCharts()||attempts>=30)window.clearInterval(retry);},200);}
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',startBeanStackCharts,{once:true});else startBeanStackCharts();
</script>
<script>

// Messages inbox / compose modal. Staff can send and review received/sent messages; admins view received messages.
(function(){
 const modal=document.getElementById('handoverNotesModal');
 if(!modal) return;
 const openButtons=document.querySelectorAll('[data-handover-open]');
 const closeButtons=modal.querySelectorAll('[data-handover-close]');
 let previousFocus=null;
 function openModal(){previousFocus=document.activeElement;modal.style.display='flex';modal.classList.add('open');modal.setAttribute('aria-hidden','false');const field=modal.querySelector('select,textarea,button[data-handover-close]');if(field)field.focus();}
 function closeModal(){modal.classList.remove('open');modal.style.display='none';modal.setAttribute('aria-hidden','true');if(previousFocus&&previousFocus.focus)previousFocus.focus();}
 openButtons.forEach(btn=>btn.addEventListener('click',function(){
   const unreadCount=<?= (int)$handoverUnreadCount ?>;
   if(unreadCount>0 && !window.__handoverMarkedRead){
     window.__handoverMarkedRead=true;
     const form=document.createElement('form');form.method='post';form.action=window.location.pathname+window.location.search;
     const returnPage=document.createElement('input');returnPage.type='hidden';returnPage.name='return_page';returnPage.value=(new URLSearchParams(window.location.search).get('page')||'dashboard');form.appendChild(returnPage);
     const action=document.createElement('input');action.type='hidden';action.name='action';action.value='mark_handover_read';form.appendChild(action);
     document.body.appendChild(form);form.submit();return;
   }
   openModal();
 }));
 closeButtons.forEach(btn=>btn.addEventListener('click',closeModal));
 modal.addEventListener('click',event=>{if(event.target===modal)closeModal();});
 document.addEventListener('keydown',event=>{if(event.key==='Escape'&&modal.classList.contains('open'))closeModal();});
 const params=new URLSearchParams(window.location.search);if(params.get('show_handover')==='1')openModal();
})();


// Low stock alert topbar modal, sized like the handover notes modal.
(function(){
 const modal=document.getElementById('lowStockAlertsModal'); if(!modal)return;
 const openButtons=document.querySelectorAll('[data-low-stock-open]');
 const closeButtons=modal.querySelectorAll('[data-low-stock-close]'); let previousFocus=null;
 function openModal(){previousFocus=document.activeElement;modal.style.display='flex';modal.classList.add('open');modal.setAttribute('aria-hidden','false');const b=modal.querySelector('[data-low-stock-close]');if(b)b.focus();}
 function closeModal(){modal.classList.remove('open');modal.style.display='none';modal.setAttribute('aria-hidden','true');if(previousFocus&&previousFocus.focus)previousFocus.focus();}
 openButtons.forEach(b=>b.addEventListener('click',openModal));closeButtons.forEach(b=>b.addEventListener('click',closeModal));
 modal.addEventListener('click',e=>{if(e.target===modal)closeModal()});document.addEventListener('keydown',e=>{if(e.key==='Escape'&&modal.classList.contains('open'))closeModal()});
})();
(function(){
  var modal=document.getElementById('accountPageModal');
  var frame=document.getElementById('accountPageModalFrame');
  var closeButton=document.getElementById('accountPageModalClose');
  var title=document.getElementById('accountPageModalTitle');
  var lastFocus=null;
  function closeAccountModal(){if(!modal)return;modal.classList.remove('open');modal.setAttribute('aria-hidden','true');if(frame)frame.src='about:blank';if(lastFocus&&lastFocus.focus)lastFocus.focus();}
  document.querySelectorAll('[data-account-page]').forEach(function(button){button.addEventListener('click',function(){if(!modal||!frame)return;var page=button.getAttribute('data-account-page');if(!['users','settings'].includes(page))return;lastFocus=button;var menu=document.getElementById('topUserMenu');var toggle=document.getElementById('topUserMenuToggle');if(menu)menu.classList.remove('open');if(toggle)toggle.setAttribute('aria-expanded','false');title.textContent=page==='users'?'User Management':'Settings';frame.src='index.php?page='+encodeURIComponent(page)+'&popup=1';modal.classList.add('open');modal.setAttribute('aria-hidden','false');closeButton.focus();});});
  if(closeButton)closeButton.addEventListener('click',closeAccountModal);
  if(modal)modal.addEventListener('click',function(event){if(event.target===modal)closeAccountModal();});
  document.addEventListener('keydown',function(event){if(event.key==='Escape'&&modal&&modal.classList.contains('open'))closeAccountModal();});
})();

(function(){
  var logoutLinks=document.querySelectorAll('[data-logout-confirm]');
  var modal=document.getElementById('logoutConfirmModal');
  var cancel=document.getElementById('cancelLogout');
  var menu=document.getElementById('topUserMenu');
  var menuToggle=document.getElementById('topUserMenuToggle');
  function closeMenu(){if(menu){menu.classList.remove('open');if(menuToggle)menuToggle.setAttribute('aria-expanded','false');}}
  if(menu && menuToggle){
    menuToggle.addEventListener('click',function(event){event.stopPropagation();var isOpen=menu.classList.toggle('open');menuToggle.setAttribute('aria-expanded',isOpen?'true':'false');});
    document.addEventListener('click',function(event){if(!menu.contains(event.target))closeMenu();});
    menu.querySelectorAll('a').forEach(function(link){link.addEventListener('click',function(){if(!link.hasAttribute('data-logout-confirm'))closeMenu();});});
  }
  if(modal){
    function closeLogoutModal(){modal.classList.remove('open');modal.setAttribute('aria-hidden','true');if(cancel)cancel.focus();}
    logoutLinks.forEach(function(logoutLink){logoutLink.addEventListener('click',function(event){if(event.button!==0||event.ctrlKey||event.metaKey||event.shiftKey||event.altKey)return;event.preventDefault();closeMenu();modal.classList.add('open');modal.setAttribute('aria-hidden','false');if(cancel)cancel.focus();});});
    if(cancel)cancel.addEventListener('click',closeLogoutModal);
    modal.addEventListener('click',function(event){if(event.target===modal)closeLogoutModal();});
    document.addEventListener('keydown',function(event){if(event.key==='Escape'){closeMenu();if(modal.classList.contains('open'))closeLogoutModal();}});
  }
})();

// Live search and transaction-type filter for today's report table.
(function(){
  var table=document.getElementById('todayTransactionsTable');
  var search=document.getElementById('todayTransactionsSearch');
  var type=document.getElementById('todayTransactionsType');
  var count=document.getElementById('todayTransactionsCount');
  if(!table||!search||!type||!count)return;
  var rows=Array.from(table.querySelectorAll('tbody tr[data-transaction-type]'));
  function filterRows(){
    var query=search.value.trim().toLocaleLowerCase();var selected=type.value;var visible=0;
    rows.forEach(function(row){var matchesText=row.textContent.toLocaleLowerCase().includes(query);var matchesType=selected==='all'||row.dataset.transactionType===selected;var show=matchesText&&matchesType;row.hidden=!show;if(show)visible++;});
    count.textContent=visible+' of '+rows.length+' records shown';
  }
  search.addEventListener('input',filterRows);type.addEventListener('change',filterRows);filterRows();
})();
(function(){
  var button=document.getElementById('menuToggle');
  var sidebar=document.getElementById('sidebar');
  if(!button||!sidebar)return;
  var mobile=window.matchMedia('(max-width: 800px)');
  function syncAria(){
    var expanded=mobile.matches?document.body.classList.contains('sidebar-mobile-open'):!document.body.classList.contains('sidebar-collapsed');
    button.setAttribute('aria-expanded',expanded?'true':'false');
    button.setAttribute('aria-controls','sidebar');
  }
  function toggle(event){
    if(event){event.preventDefault();event.stopPropagation();}
    if(mobile.matches){
      document.body.classList.toggle('sidebar-mobile-open');
      document.body.classList.remove('sidebar-collapsed');
    }else{
      document.body.classList.toggle('sidebar-collapsed');
      document.body.classList.remove('sidebar-mobile-open');
    }
    syncAria();
  }
  button.setAttribute('type','button');
  button.addEventListener('click',toggle);
  document.addEventListener('click',function(event){
    if(mobile.matches&&document.body.classList.contains('sidebar-mobile-open')&&!sidebar.contains(event.target)&&!button.contains(event.target)){
      document.body.classList.remove('sidebar-mobile-open');syncAria();
    }
  });
  document.addEventListener('keydown',function(event){
    if(event.key==='Escape'&&document.body.classList.contains('sidebar-mobile-open')){document.body.classList.remove('sidebar-mobile-open');syncAria();}
  });
  function handleBreakpoint(){document.body.classList.remove('sidebar-mobile-open');document.body.classList.remove('sidebar-collapsed');syncAria();}
  if(mobile.addEventListener)mobile.addEventListener('change',handleBreakpoint);else if(mobile.addListener)mobile.addListener(handleBreakpoint);
  syncAria();
})();

// Keep internal BeanStack navigation in the current tab, even if a link has target="_blank".
// Modifier clicks (Ctrl/Cmd/Shift-click) remain browser-native.
(function(){
  function isInternal(link){ try { return new URL(link.href, window.location.href).origin === window.location.origin; } catch(e){ return false; } }
  document.querySelectorAll('a[href][target="_blank"]').forEach(function(link){ if(isInternal(link)) link.removeAttribute('target'); });
  document.addEventListener('click', function(event){
    const link = event.target.closest('a[href]');
    if (!link || !isInternal(link) || link.hasAttribute('data-logout-confirm') || link.hasAttribute('download') || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
    event.preventDefault();
    window.location.assign(link.href);
  }, true);
})();
</script>
</body></html>
