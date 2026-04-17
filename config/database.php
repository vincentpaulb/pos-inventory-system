<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
}

date_default_timezone_set('Asia/Manila');

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'rb_heavy_inventory');
define('DB_USER', 'root');
define('DB_PASS', '');
define('APP_NAME', "R'B Heavy Equipment Parts Trading");
define('APP_URL', get_app_url());
define('BASE_PATH', dirname(__DIR__));
define('LOW_STOCK_THRESHOLD', 5);

/**
 * Build the full base URL including scheme + host + sub-directory.
 * e.g.  http://localhost/rb_app   OR   https://mysite.com
 */
function get_app_url(): string
{
    // Scheme
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

    // Host (includes port if non-standard)
    $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';

    // Sub-directory: dirname of the entry-point script relative to web root
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $dir = dirname($scriptName);
    $dir = ($dir === '/' || $dir === '.') ? '' : $dir;

    return rtrim($scheme . '://' . $host . $dir, '/');
}

class Database
{
    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
        }
        return self::$instance;
    }
}
