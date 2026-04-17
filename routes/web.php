<?php
declare(strict_types=1);

require_once BASE_PATH . '/controllers/AuthController.php';
require_once BASE_PATH . '/controllers/DashboardController.php';
require_once BASE_PATH . '/controllers/ProductController.php';
require_once BASE_PATH . '/controllers/CategoryController.php';
require_once BASE_PATH . '/controllers/SupplierController.php';
require_once BASE_PATH . '/controllers/UserController.php';
require_once BASE_PATH . '/controllers/PosController.php';
require_once BASE_PATH . '/controllers/ExpenseController.php';
require_once BASE_PATH . '/controllers/QuotationController.php';
require_once BASE_PATH . '/controllers/ReportController.php';
require_once BASE_PATH . '/controllers/SetupController.php';
require_once BASE_PATH . '/controllers/ActivityLogController.php';

$path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$base = trim(parse_url(APP_URL, PHP_URL_PATH), '/');

if ($base !== '' && str_starts_with($path, $base)) {
    $path = trim(substr($path, strlen($base)), '/');
}

$path = $path === ''
    ? (is_logged_in() && organization_setup_step() === 'complete' ? authorized_home_route() : 'dashboard')
    : $path;

$setupRoutes = ['logout', 'setup', 'setup/organization', 'setup/admin'];

if (is_logged_in()) {
    $setupStep = organization_setup_step();

    if ($setupStep !== 'complete' && !in_array($path, $setupRoutes, true)) {
        redirect('setup/' . $setupStep);
    }

    if ($setupStep === 'complete' && ($path === 'setup' || str_starts_with($path, 'setup/'))) {
        redirect(authorized_home_route());
    }
}

$routes = [
    'login' => [AuthController::class, request_method() === 'POST' ? 'login' : 'showLogin'],
    'logout' => [AuthController::class, 'logout'],

    'setup' => [SetupController::class, 'index'],
    'setup/organization' => [SetupController::class, request_method() === 'POST' ? 'saveOrganization' : 'organizationForm'],
    'setup/admin' => [SetupController::class, request_method() === 'POST' ? 'saveAdmin' : 'adminForm'],

    'dashboard' => [DashboardController::class, 'index'],

    'products' => [ProductController::class, 'index'],
    'products/create' => [ProductController::class, request_method() === 'POST' ? 'store' : 'create'],
    'products/edit' => [ProductController::class, 'edit'],
    'products/update' => [ProductController::class, 'update'],
    'products/delete' => [ProductController::class, 'delete'],
    'products/stock' => [ProductController::class, request_method() === 'POST' ? 'stockAdjust' : 'stockForm'],
    'products/movements' => [ProductController::class, 'movements'],

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
    'organization' => [SetupController::class, request_method() === 'POST' ? 'updateOrganization' : 'manage'],

    'pos' => [PosController::class, 'index'],
    'pos/search' => [PosController::class, 'searchProducts'],
    'pos/checkout' => [PosController::class, 'checkout'],
    'pos/receipt' => [PosController::class, 'receipt'],
    'pos/void' => [PosController::class, 'voidSale'],
    'pos/delete' => [PosController::class, 'deleteSale'],
    'pos/daily-report' => [PosController::class, request_method() === 'POST' ? 'submitDailyReport' : 'previewDailyReport'],

    'expenses' => [ExpenseController::class, 'index'],
    'expenses/cash-on-hand' => [ExpenseController::class, 'saveCashOnHand'],
    'expenses/store' => [ExpenseController::class, 'store'],

    'quotations' => [QuotationController::class, 'index'],
    'quotations/search' => [QuotationController::class, 'searchProducts'],
    'quotations/store' => [QuotationController::class, 'store'],
    'quotations/edit' => [QuotationController::class, 'edit'],
    'quotations/update' => [QuotationController::class, 'update'],
    'quotations/delete' => [QuotationController::class, 'delete'],
    'quotations/view' => [QuotationController::class, 'view'],

    'reports' => [ReportController::class, 'index'],
    'my-reports' => [ReportController::class, 'myReports'],
    'activity-logs' => [ActivityLogController::class, 'index'],
    'reports/export' => [ReportController::class, 'exportCsv'],
    'reports/delete-dsr' => [ReportController::class, 'deleteDailyReport'],
    'reports/export-dsr' => [ReportController::class, 'exportDailyReportsCsv'],
    'reports/daily-report' => [ReportController::class, 'viewDailyReport'],
];

if (!isset($routes[$path])) {
    http_response_code(404);
    require BASE_PATH . '/views/errors/404.php';
    exit;
}

[$controllerClass, $method] = $routes[$path];
$controller = new $controllerClass();
$controller->$method();
