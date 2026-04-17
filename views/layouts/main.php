<?php
$user = auth_user();
$organizationName = organization_name();
$organizationLogoUrl = organization_logo_url();
$currentRole = current_role();

function is_active_menu(string $needle): bool
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
    $base = trim(parse_url(APP_URL, PHP_URL_PATH) ?? '', '/');

    if ($base !== '' && str_starts_with(trim($path, '/'), $base)) {
        $path = trim(substr(trim($path, '/'), strlen($base)), '/');
    } else {
        $path = trim($path, '/');
    }

    return $path === $needle || str_starts_with($path, $needle . '/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? $organizationName) ?> - <?= e($organizationName) ?></title>
    <link rel="stylesheet" href="<?= e(base_url('public/vendor/bootstrap/css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_url('public/vendor/fontawesome-free-7.2.0-web/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_url('public/css/app.css')) ?>">
</head>
<body>

<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-inner">
            <div class="brand-block">
                <div class="brand-mark">
                    <?php if ($organizationLogoUrl): ?>
                        <img
                            class="brand-logo"
                            src="<?= e($organizationLogoUrl) ?>"
                            alt="<?= e($organizationName) ?> logo"
                        >
                    <?php else: ?>
                        <span class="brand-logo-fallback"><?= e(organization_initials()) ?></span>
                    <?php endif; ?>
                </div>
                <div class="brand-copy">
                    <p class="brand-title"><?= nl2br(e($organizationName)) ?></p>
                </div>
                <button
                    class="sidebar-collapse-btn"
                    id="sidebarCollapseToggle"
                    type="button"
                    aria-label="Hide sidebar"
                    title="Hide sidebar"
                >
                    <i class="fas fa-chevron-left"></i>
                    <span class="visually-hidden" id="sidebarCollapseLabel">Hide sidebar</span>
                </button>
            </div>

            <div class="sidebar-section-label">Operations</div>
            <nav class="nav flex-column">
                <?php if (can_access_module('dashboard')): ?>
                    <a class="nav-link <?= is_active_menu('dashboard') ? 'active' : '' ?>" href="<?= e(base_url('dashboard')) ?>" title="Dashboard" aria-label="Dashboard">
                        <span class="nav-icon"><i class="fas fa-chart-line"></i></span> Dashboard
                    </a>
                <?php endif; ?>
                <?php if (can_access_module('pos')): ?>
                    <a class="nav-link <?= is_active_menu('pos') ? 'active' : '' ?>" href="<?= e(base_url('pos')) ?>" title="Point of Sale" aria-label="Point of Sale">
                        <span class="nav-icon"><i class="fas fa-cash-register"></i></span> Point of Sale
                    </a>
                <?php endif; ?>
                <?php if (can_access_module('expenses')): ?>
                    <a class="nav-link <?= is_active_menu('expenses') ? 'active' : '' ?>" href="<?= e(base_url('expenses')) ?>" title="Expenses" aria-label="Expenses">
                        <span class="nav-icon"><i class="fas fa-wallet"></i></span> Expenses
                    </a>
                <?php endif; ?>
                <?php if (can_access_module('products')): ?>
                <?php $inventoryActive = is_active_menu('products'); ?>
                <div class="nav-item-group <?= $inventoryActive ? 'is-open' : '' ?>">
                    <a class="nav-link <?= $inventoryActive ? 'active' : '' ?>" href="<?= e(base_url('products')) ?>" title="Inventory" aria-label="Inventory">
                        <span class="nav-icon"><i class="fas fa-cube"></i></span>
                        Inventory
                        <i class="fas fa-chevron-right nav-chevron"></i>
                    </a>
                    <div class="nav-sub">
                        <a class="nav-sub-link <?= is_active_menu('products') && !is_active_menu('products/movements') ? 'active' : '' ?>"
                           href="<?= e(base_url('products')) ?>" title="Products" aria-label="Products">
                            <i class="fas fa-box" style="font-size:.7rem;width:12px"></i> Products
                        </a>
                        <?php if (can_access_module('stock-movements')): ?>
                        <a class="nav-sub-link <?= is_active_menu('products/movements') ? 'active' : '' ?>"
                           href="<?= e(base_url('products/movements')) ?>" title="Stock Movements" aria-label="Stock Movements">
                            <i class="fas fa-arrows-up-down" style="font-size:.7rem;width:12px"></i> Stock Movements
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (can_access_module('categories')): ?>
                    <a class="nav-link <?= is_active_menu('categories') ? 'active' : '' ?>" href="<?= e(base_url('categories')) ?>" title="Categories" aria-label="Categories">
                        <span class="nav-icon"><i class="fas fa-folder-open"></i></span> Categories
                    </a>
                <?php endif; ?>
                <?php if (can_access_module('suppliers')): ?>
                    <a class="nav-link <?= is_active_menu('suppliers') ? 'active' : '' ?>" href="<?= e(base_url('suppliers')) ?>" title="Suppliers" aria-label="Suppliers">
                        <span class="nav-icon"><i class="fas fa-industry"></i></span> Suppliers
                    </a>
                <?php endif; ?>
                <?php if (can_access_module('quotations')): ?>
                    <a class="nav-link <?= is_active_menu('quotations') ? 'active' : '' ?>" href="<?= e(base_url('quotations')) ?>" title="Quotations" aria-label="Quotations">
                        <span class="nav-icon"><i class="fas fa-receipt"></i></span> Quotations
                    </a>
                <?php endif; ?>
                <?php if (can_access_module('reports')): ?>
                    <a class="nav-link <?= is_active_menu('reports') ? 'active' : '' ?>" href="<?= e(base_url('reports')) ?>" title="Sales Reports" aria-label="Sales Reports">
                        <span class="nav-icon"><i class="fas fa-chart-bar"></i></span> Sales Reports
                    </a>
                <?php endif; ?>
                <?php if (can_access_module('my-reports')): ?>
                    <a class="nav-link <?= is_active_menu('my-reports') ? 'active' : '' ?>" href="<?= e(base_url('my-reports')) ?>" title="Daily Sales Reports" aria-label="Daily Sales Reports">
                        <span class="nav-icon"><i class="fas fa-file-lines"></i></span> Daily Sales Reports
                    </a>
                <?php endif; ?>
                <?php if (can_access_module('activity-logs')): ?>
                    <a class="nav-link <?= is_active_menu('activity-logs') ? 'active' : '' ?>" href="<?= e(base_url('activity-logs')) ?>" title="Activity Logs" aria-label="Activity Logs">
                        <span class="nav-icon"><i class="fas fa-list-check"></i></span> Activity Logs
                    </a>
                <?php endif; ?>
                <?php if (has_role('Admin')): ?>
                    <div class="sidebar-section-label">Administration</div>
                    <a class="nav-link <?= is_active_menu('organization') ? 'active' : '' ?>" href="<?= e(base_url('organization')) ?>" title="Organization Info" aria-label="Organization Info">
                        <span class="nav-icon"><i class="fas fa-building"></i></span> Organization Info
                    </a>
                    <a class="nav-link <?= is_active_menu('users') ? 'active' : '' ?>" href="<?= e(base_url('users')) ?>" title="User Management" aria-label="User Management">
                        <span class="nav-icon"><i class="fas fa-users"></i></span> User Management
                    </a>
                <?php endif; ?>
                <div class="sidebar-section-label">Account</div>
                <a class="nav-link <?= is_active_menu('profile') ? 'active' : '' ?>" href="<?= e(base_url('profile')) ?>" title="My Profile" aria-label="My Profile">
                    <span class="nav-icon"><i class="fas fa-id-badge"></i></span> My Profile
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-panel">
                    <div class="user-panel-simple">
                        <div class="user-avatar"><?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?></div>
                        <div class="user-panel-meta">
                            <div class="user-name"><?= e($user['name'] ?? '') ?></div>
                            <div class="user-status">@<?= e($user['username'] ?? '') ?></div>
                        </div>
                        <a class="user-panel-logout" href="<?= e(base_url('logout')) ?>" title="Sign out" aria-label="Sign out">
                            <i class="fas fa-right-from-bracket"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <main class="content">
        <div class="topbar">
            <div class="topbar-left">
                <button class="mobile-menu-btn" id="sidebarToggleButton" type="button" aria-label="Toggle sidebar" title="Toggle sidebar"><i class="fas fa-bars"></i></button>
                <button class="topbar-sidebar-btn" id="sidebarExpandButton" type="button" aria-label="Show sidebar" title="Show sidebar">
                    <i class="fas fa-bars"></i>
                    <span class="visually-hidden" id="sidebarExpandLabel">Show sidebar</span>
                </button>
                <div>
                    <div class="topbar-title"><?= e($title ?? 'Dashboard') ?></div>
                    <div class="topbar-subtitle"><?= e($currentRole ? $currentRole . ' workspace' : 'Workspace') ?></div>
                </div>
            </div>
            <div class="topbar-actions">
                <div class="topbar-chip"><i class="fas fa-calendar-day"></i> <?= date('M d, Y') ?></div>
                <div class="topbar-chip hide-mobile"><i class="fas fa-clock"></i> <?= date('h:i A') ?></div>
                <button class="topbar-chip topbar-icon-btn" id="darkModeToggle" type="button" aria-label="Toggle theme" title="Toggle theme">
                    <i class="fas fa-moon"></i>
                    <span class="visually-hidden" id="darkModeLabel">Dark</span>
                </button>
            </div>
        </div>

        <div class="page-content">
            <?php if ($message = flash('success')): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= e($message) ?></div>
            <?php endif; ?>

            <?php if ($message = flash('error')): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= e($message) ?></div>
            <?php endif; ?>

            <?php if (can_access_module('products')): ?>
                <?php
                    require_once BASE_PATH . '/models/Product.php';
                    $__lowStockItems = (new Product())->lowStock();
                    $__lowCount = count($__lowStockItems);
                    if ($__lowCount > 0):
                        $__critical = array_filter($__lowStockItems, fn($p) => (int)$p['stock_quantity'] === 0);
                        $__isCritical = count($__critical) > 0;
                ?>
                <div class="low-stock-toast <?= $__isCritical ? 'low-stock-toast--critical' : '' ?>" id="lowStockToast">
                    <div class="low-stock-toast-header">
                        <span class="low-stock-toast-icon">
                            <i class="fas fa-triangle-exclamation"></i>
                        </span>
                        <div class="low-stock-toast-title">
                            <strong>Low Stock Alert</strong>
                            <span class="low-stock-toast-count"><?= $__lowCount ?> item<?= $__lowCount > 1 ? 's' : '' ?></span>
                        </div>
                        <button class="low-stock-toast-close" onclick="dismissLowStockToast()" aria-label="Dismiss">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>
                    <ul class="low-stock-toast-list">
                        <?php foreach (array_slice($__lowStockItems, 0, 5) as $__p): ?>
                            <li>
                                <span class="low-stock-toast-name"><?= e($__p['name']) ?></span>
                                <span class="low-stock-toast-qty <?= (int)$__p['stock_quantity'] === 0 ? 'qty-zero' : '' ?>">
                                    <?= (int)$__p['stock_quantity'] ?> left
                                </span>
                            </li>
                        <?php endforeach; ?>
                        <?php if ($__lowCount > 5): ?>
                            <li class="low-stock-toast-more">+<?= $__lowCount - 5 ?> more...</li>
                        <?php endif; ?>
                    </ul>
                    <label class="low-stock-toast-snooze">
                        <input type="checkbox" id="lowStockSnooze"> Do not show this for today
                    </label>
                    <a href="<?= e(base_url('products')) ?>" class="low-stock-toast-btn">
                        <i class="fas fa-arrow-right"></i> View Inventory
                    </a>
                </div>
                <script>
                    (function() {
                        var today = new Date().toISOString().slice(0, 10);
                        if (localStorage.getItem('lowStockSnoozed') === today) {
                            var t = document.getElementById('lowStockToast');
                            if (t) t.style.display = 'none';
                        }
                    })();
                    function dismissLowStockToast() {
                        var t = document.getElementById('lowStockToast');
                        if (!t) return;
                        if (document.getElementById('lowStockSnooze').checked) {
                            var today = new Date().toISOString().slice(0, 10);
                            localStorage.setItem('lowStockSnoozed', today);
                        }
                        t.classList.add('is-hiding');
                        setTimeout(function() { t.style.display = 'none'; }, 400);
                    }
                    setTimeout(dismissLowStockToast, 10000);
                </script>

                <?php endif; ?>
            <?php endif; ?>

            <?php require $contentView; ?>
        </div>
    </main>
</div>

<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-labelledby="confirmActionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5 mb-0" id="confirmActionLabel">Confirm Action</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="small-muted" id="confirmActionMessage">Are you sure you want to continue?</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmActionSubmit">Delete</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= e(base_url('public/js/app.js')) ?>"></script>
<script src="<?= e(base_url('public/vendor/bootstrap/js/bootstrap.bundle.min.js')) ?>"></script>
</body>
</html>
