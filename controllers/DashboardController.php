<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/Dashboard.php';
require_once BASE_PATH . '/models/Transaction.php';
require_once BASE_PATH . '/models/Product.php';
require_once BASE_PATH . '/models/ActivityLog.php';

class DashboardController
{
    public function index(): void
    {
        require_auth();

        $dashboard = new Dashboard();
        $transactions = new Transaction();
        $products = new Product();
        $logs = new ActivityLog();

        view('dashboard/index', [
            'title' => 'Dashboard',
            'stats' => $dashboard->stats(),
            'dailySalesSeries' => $dashboard->dailySalesSeries(7),
            'monthlySalesSeries' => $dashboard->monthlySalesSeries(6),
            'recentTransactions' => $transactions->recent(8),
            'lowStock' => $products->lowStock(),
            'activities' => $logs->recent(10),
        ]);
    }
}
