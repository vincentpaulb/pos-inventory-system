<?php
$user = auth_user();
function is_active_menu(string $needle): bool {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
    return str_contains($path, $needle);
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
    <link rel="stylesheet" href="<?= e(base_url('public/css/app.css')) ?>">
</head>
<body>

<div class="app-shell">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-inner">
            <!-- Brand -->
            <div class="brand-block">
                <div class="brand-mark">RB</div>
                <div>
                    <p class="brand-title">R'B Heavy Equipment<br>Parts Trading</p>
                    <div class="brand-subtitle">Inventory &amp; POS System</div>
                </div>
            </div>

            <!-- Nav -->
            <div class="sidebar-section-label">Operations</div>
            <nav class="nav flex-column">
                <a class="nav-link <?= is_active_menu('dashboard') ? 'active' : '' ?>" href="<?= e(base_url('dashboard')) ?>">
                    <span class="nav-icon">📊</span> Dashboard
                </a>
                <a class="nav-link <?= is_active_menu('products') ? 'active' : '' ?>" href="<?= e(base_url('products')) ?>">
                    <span class="nav-icon">📦</span> Inventory
                </a>
                <a class="nav-link <?= is_active_menu('categories') ? 'active' : '' ?>" href="<?= e(base_url('categories')) ?>">
                    <span class="nav-icon">🗂</span> Categories
                </a>
                <a class="nav-link <?= is_active_menu('suppliers') ? 'active' : '' ?>" href="<?= e(base_url('suppliers')) ?>">
                    <span class="nav-icon">🏭</span> Suppliers
                </a>
                <a class="nav-link <?= is_active_menu('pos') ? 'active' : '' ?>" href="<?= e(base_url('pos')) ?>">
                    <span class="nav-icon">🛒</span> Point of Sale
                </a>
                <a class="nav-link <?= is_active_menu('quotations') ? 'active' : '' ?>" href="<?= e(base_url('quotations')) ?>">
                    <span class="nav-icon">🧾</span> Quotations
                </a>
                <a class="nav-link <?= is_active_menu('reports') ? 'active' : '' ?>" href="<?= e(base_url('reports')) ?>">
                    <span class="nav-icon">📈</span> Sales Reports
                </a>
                <?php if (has_role('Admin')): ?>
                    <div class="sidebar-section-label">Administration</div>
                    <a class="nav-link <?= is_active_menu('users') ? 'active' : '' ?>" href="<?= e(base_url('users')) ?>">
                        <span class="nav-icon">👥</span> User Management
                    </a>
                <?php endif; ?>
            </nav>

            <!-- Footer -->
            <div class="sidebar-footer">
            <!-- User -->
                <div class="user-panel">
                    <div class="user-panel-row">
                        <div class="user-avatar"><?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?></div>
                        <div>
                            <div class="user-name"><?= e($user['name'] ?? '') ?></div>
                            <div class="user-status">Operations workspace</div>
                        </div>
                    </div>
                    <div class="user-role-badge"><?= e($user['role'] ?? '') ?></div>
                </div>
                <button class="btn-sidebar-toggle" id="darkModeToggle" type="button">
                    <span id="darkModeIcon">🌙</span> <span id="darkModeLabel">Dark Mode</span>
                </button>
                <a class="btn-sidebar-logout" href="<?= e(base_url('logout')) ?>">
                    🚪 Sign Out
                </a>
            </div>
        </div>
    </aside>

    <!-- Main -->
    <main class="content">
        <!-- Topbar -->
        <div class="topbar">
            <div class="topbar-left">
                <button class="mobile-menu-btn" onclick="document.getElementById('sidebar').classList.toggle('show')">☰</button>
                <div>
                    <div class="topbar-title"><?= e($title ?? 'Dashboard') ?></div>
                </div>
            </div>
            <div class="topbar-actions">
                <div class="topbar-chip">📅 <?= date('M d, Y') ?></div>
                <div class="topbar-chip hide-mobile">🕒 <?= date('h:i A') ?></div>
            </div>
        </div>

        <!-- Page content -->
        <div class="page-content">
            <?php if ($message = flash('success')): ?>
                <div class="alert alert-success">✓ <?= e($message) ?></div>
            <?php endif; ?>

            <?php if ($message = flash('error')): ?>
                <div class="alert alert-danger">⚠ <?= e($message) ?></div>
            <?php endif; ?>

            <?php require $contentView; ?>
        </div>
    </main>
</div>

<script src="<?= e(base_url('public/js/app.js')) ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
