<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/ActivityLog.php';

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user']);
}

function available_roles(): array
{
    return ['Admin', 'Supply Manager', 'Sales Manager', 'Cashier'];
}

function is_valid_role(string $role): bool
{
    return in_array($role, available_roles(), true);
}

function role_permissions(): array
{
    return [
        'Admin'          => ['dashboard', 'products', 'categories', 'suppliers', 'pos', 'expenses', 'quotations', 'reports', 'activity-logs', 'stock-movements', 'organization', 'users', 'profile'],
        'Supply Manager' => ['dashboard', 'products', 'categories', 'suppliers', 'stock-movements', 'profile'],
        'Sales Manager'  => ['dashboard', 'pos', 'expenses', 'quotations', 'my-reports', 'profile'],
        'Cashier'        => ['pos', 'expenses', 'my-reports', 'profile'],
    ];
}

function current_role(): ?string
{
    return is_logged_in() ? (string) ($_SESSION['user']['role'] ?? '') : null;
}

function has_role(string|array $roles): bool
{
    if (!is_logged_in()) {
        return false;
    }

    $roles = (array) $roles;
    return in_array($_SESSION['user']['role'], $roles, true);
}

function can_access_module(string $module): bool
{
    if (!is_logged_in()) {
        return false;
    }

    $permissions = role_permissions();
    $role = current_role();

    return $role !== null
        && isset($permissions[$role])
        && in_array($module, $permissions[$role], true);
}

function authorized_home_route(?string $role = null): string
{
    $role ??= current_role() ?? 'Cashier';
    $permissions = role_permissions();
    $allowedModules = $permissions[$role] ?? [];

    foreach (['dashboard', 'pos', 'expenses', 'products', 'categories', 'suppliers', 'quotations', 'reports', 'my-reports', 'activity-logs', 'stock-movements', 'organization', 'users', 'profile'] as $module) {
        if (in_array($module, $allowedModules, true)) {
            return $module;
        }
    }

    return 'login';
}

function role_badge_class(string $role): string
{
    return match ($role) {
        'Admin' => 'bg-soft-primary',
        'Supply Manager' => 'bg-soft-warning',
        'Sales Manager' => 'bg-soft-success',
        default => 'bg-soft-info',
    };
}

function require_auth(): void
{
    if (!is_logged_in()) {
        flash('error', 'Please log in to continue.');
        redirect('login');
    }

    $currentUser = auth_user();
    $users = new User();
    $existingUser = $currentUser ? $users->find((int) $currentUser['id']) : null;

    if ($existingUser === null) {
        logout_user();
        flash('error', 'Your session is no longer valid. Please log in again.');
        redirect('login');
    }

    regenerate_session();
}

function require_role(string|array $roles): void
{
    require_auth();
    if (!has_role($roles)) {
        flash('error', 'You are not authorized to access that page.');
        redirect(authorized_home_route());
    }
}

function require_module_access(string|array $modules): void
{
    require_auth();

    foreach ((array) $modules as $module) {
        if (can_access_module($module)) {
            return;
        }
    }

    flash('error', 'You are not authorized to access that page.');
    redirect(authorized_home_route());
}

function login_user(array $user): void
{
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'name' => $user['name'],
        'username' => $user['username'],
        'role' => $user['role'],
    ];
    regenerate_session();
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) ($params['secure'] ?? false), (bool) ($params['httponly'] ?? true));
    }
    session_destroy();
}

function login_rate_limited(string $username): bool
{
    $key = 'login_attempts_' . md5(strtolower($username) . ($_SERVER['REMOTE_ADDR'] ?? 'cli'));
    $record = $_SESSION[$key] ?? ['count' => 0, 'time' => time()];

    if ((time() - $record['time']) > 900) {
        unset($_SESSION[$key]);
        return false;
    }

    return $record['count'] >= 5;
}

function record_login_attempt(string $username, bool $success = false): void
{
    $key = 'login_attempts_' . md5(strtolower($username) . ($_SERVER['REMOTE_ADDR'] ?? 'cli'));

    if ($success) {
        unset($_SESSION[$key]);
        return;
    }

    $record = $_SESSION[$key] ?? ['count' => 0, 'time' => time()];
    if ((time() - $record['time']) > 900) {
        $record = ['count' => 0, 'time' => time()];
    }

    $record['count']++;
    $_SESSION[$key] = $record;
}
