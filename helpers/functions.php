<?php
declare(strict_types=1);

function base_url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return APP_URL . ($path ? '/' . $path : '');
}

function redirect(string $route): void
{
    header('Location: ' . base_url($route));
    exit;
}

function request_method(): string
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['old'][$key] ?? $default;
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $value = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $value;
}

function set_old(array $data): void
{
    $_SESSION['old'] = $data;
}

function clear_old(): void
{
    unset($_SESSION['old']);
}

function view(string $view, array $data = [], string $layout = 'main'): void
{
    extract($data);
    $viewFile = BASE_PATH . '/views/' . $view . '.php';

    if (!file_exists($viewFile)) {
        http_response_code(404);
        require BASE_PATH . '/views/errors/404.php';
        exit;
    }

    $contentView = $viewFile;
    require BASE_PATH . '/views/layouts/' . $layout . '.php';
}

function format_currency(float|int|string $amount): string
{
    return '₱' . number_format((float) $amount, 2);
}

function format_datetime(?string $datetime): string
{
    if (!$datetime) {
        return '-';
    }
    return date('M d, Y h:i A', strtotime($datetime));
}

function format_date(?string $datetime): string
{
    if (!$datetime) {
        return '-';
    }
    return date('M d, Y', strtotime($datetime));
}

function is_post(): bool
{
    return request_method() === 'POST';
}

function pagination_offset(int $perPage = 10): int
{
    $page = max(1, (int) ($_GET['page'] ?? 1));
    return ($page - 1) * $perPage;
}
