<?php
$user = auth_user();
function is_active_menu(string $needle): bool {
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
    <title><?= e($title ?? APP_NAME) ?> — <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= e(base_url('public/css/app.css')) ?>">
</head>
<body>

<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-inner">
            <div class="brand-block">
                <div class="brand-mark">
                    <img
                        class="brand-logo"
                        src="<?= e(base_url('public/images/logo_inversed.png')) ?>"
                        alt="R'B Heavy Equipment Parts Trading logo"
                    >
                </div>
                <div class="brand-copy">
                    <p class="brand-title">R'B Heavy Equipment<br>Parts Trading</p>
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
                <a class="nav-link <?= is_active_menu('dashboard') ? 'active' : '' ?>" href="<?= e(base_url('dashboard')) ?>" title="Dashboard" aria-label="Dashboard">
                    <span class="nav-icon"><i class="fas fa-chart-line"></i></span> Dashboard
                </a>
                <a class="nav-link <?= is_active_menu('pos') ? 'active' : '' ?>" href="<?= e(base_url('pos')) ?>" title="Point of Sale" aria-label="Point of Sale">
                    <span class="nav-icon"><i class="fas fa-cash-register"></i></span> Point of Sale
                </a>
                <a class="nav-link <?= is_active_menu('products') ? 'active' : '' ?>" href="<?= e(base_url('products')) ?>" title="Inventory" aria-label="Inventory">
                    <span class="nav-icon"><i class="fas fa-cube"></i></span> Inventory
                </a>
                <a class="nav-link <?= is_active_menu('categories') ? 'active' : '' ?>" href="<?= e(base_url('categories')) ?>" title="Categories" aria-label="Categories">
                    <span class="nav-icon"><i class="fas fa-folder-open"></i></span> Categories
                </a>
                <a class="nav-link <?= is_active_menu('suppliers') ? 'active' : '' ?>" href="<?= e(base_url('suppliers')) ?>" title="Suppliers" aria-label="Suppliers">
                    <span class="nav-icon"><i class="fas fa-industry"></i></span> Suppliers
                </a>
                <a class="nav-link <?= is_active_menu('quotations') ? 'active' : '' ?>" href="<?= e(base_url('quotations')) ?>" title="Quotations" aria-label="Quotations">
                    <span class="nav-icon"><i class="fas fa-receipt"></i></span> Quotations
                </a>
                <a class="nav-link <?= is_active_menu('reports') ? 'active' : '' ?>" href="<?= e(base_url('reports')) ?>" title="Sales Reports" aria-label="Sales Reports">
                    <span class="nav-icon"><i class="fas fa-chart-bar"></i></span> Sales Reports
                </a>
                <?php if (has_role('Admin')): ?>
                    <div class="sidebar-section-label">Administration</div>
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
                    <div class="topbar-subtitle">Light mode default admin workspace</div>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
