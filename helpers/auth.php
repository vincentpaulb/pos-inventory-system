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

function has_role(string|array $roles): bool
{
    if (!is_logged_in()) {
        return false;
    }

    $roles = (array) $roles;
    return in_array($_SESSION['user']['role'], $roles, true);
}

function require_auth(): void
{
    if (!is_logged_in()) {
        flash('error', 'Please log in to continue.');
        redirect('login');
    }
    regenerate_session();
}

function require_role(string|array $roles): void
{
    require_auth();
    if (!has_role($roles)) {
        flash('error', 'You are not authorized to access that page.');
        redirect('dashboard');
    }
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
