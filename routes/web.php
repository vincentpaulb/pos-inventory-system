<?php
declare(strict_types=1);

require_once BASE_PATH . '/controllers/AuthController.php';
require_once BASE_PATH . '/controllers/DashboardController.php';
require_once BASE_PATH . '/controllers/ProductController.php';
require_once BASE_PATH . '/controllers/CategoryController.php';
require_once BASE_PATH . '/controllers/SupplierController.php';
require_once BASE_PATH . '/controllers/UserController.php';
require_once BASE_PATH . '/controllers/PosController.php';
require_once BASE_PATH . '/controllers/QuotationController.php';
require_once BASE_PATH . '/controllers/ReportController.php';

$path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$base = trim(parse_url(APP_URL, PHP_URL_PATH), '/');

if ($base !== '' && str_starts_with($path, $base)) {
    $path = trim(substr($path, strlen($base)), '/');
}

$path = $path === '' ? 'dashboard' : $path;

$routes = [
    'login' => [AuthController::class, request_method() === 'POST' ? 'login' : 'showLogin'],
    'logout' => [AuthController::class, 'logout'],

    'dashboard' => [DashboardController::class, 'index'],

    'products' => [ProductController::class, 'index'],
    'products/create' => [ProductController::class, request_method() === 'POST' ? 'store' : 'create'],
    'products/edit' => [ProductController::class, 'edit'],
    'products/update' => [ProductController::class, 'update'],
    'products/delete' => [ProductController::class, 'delete'],
    'products/stock' => [ProductController::class, request_method() === 'POST' ? 'stockAdjust' : 'stockForm'],

    'categories' => [CategoryController::class, 'index'],
    'categories/store' => [CategoryController::class, 'store'],
    'categories/update' => [CategoryController::class, 'update'],
    'categories/delete' => [CategoryController::class, 'delete'],

    'suppliers' => [SupplierController::class, 'index'],
    'suppliers/store' => [SupplierController::class, 'store'],
    'suppliers/update' => [SupplierController::class, 'update'],
    'suppliers/delete' => [SupplierController::class, 'delete'],

    'users' => [UserController::class, 'index'],
    'users/store' => [UserController::class, 'store'],
    'users/update' => [UserController::class, 'update'],
    'users/reset-password' => [UserController::class, 'resetPassword'],
    'users/delete' => [UserController::class, 'delete'],
    'profile' => [UserController::class, request_method() === 'POST' ? 'updateProfile' : 'profile'],

    'pos' => [PosController::class, 'index'],
    'pos/search' => [PosController::class, 'searchProducts'],
    'pos/checkout' => [PosController::class, 'checkout'],
    'pos/receipt' => [PosController::class, 'receipt'],
    'pos/void' => [PosController::class, 'voidSale'],
    'pos/delete' => [PosController::class, 'deleteSale'],

    'quotations' => [QuotationController::class, 'index'],
    'quotations/search' => [QuotationController::class, 'searchProducts'],
    'quotations/store' => [QuotationController::class, 'store'],
    'quotations/edit' => [QuotationController::class, 'edit'],
    'quotations/update' => [QuotationController::class, 'update'],
    'quotations/delete' => [QuotationController::class, 'delete'],
    'quotations/view' => [QuotationController::class, 'view'],

    'reports' => [ReportController::class, 'index'],
    'reports/export' => [ReportController::class, 'exportCsv'],
];

if (!isset($routes[$path])) {
    http_response_code(404);
    require BASE_PATH . '/views/errors/404.php';
    exit;
}

[$controllerClass, $method] = $routes[$path];
$controller = new $controllerClass();
$controller->$method();
