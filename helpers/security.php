<?php
declare(strict_types=1);

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(419);
        exit('Invalid CSRF token.');
    }
}

function clean_input(?string $value): string
{
    return trim((string) $value);
}

function sanitize_number(mixed $value): float
{
    return (float) preg_replace('/[^0-9.\-]/', '', (string) $value);
}

function regenerate_session(): void
{
    if (!isset($_SESSION['last_regenerated']) || (time() - (int) $_SESSION['last_regenerated']) > 300) {
        session_regenerate_id(true);
        $_SESSION['last_regenerated'] = time();
    }
}
