<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/ActivityLog.php';

class ActivityLogController
{
    private ActivityLog $logs;

    public function __construct()
    {
        $this->logs = new ActivityLog();
    }

    public function index(): void
    {
        require_module_access('activity-logs');

        $from    = clean_input($_GET['from']    ?? '');
        $to      = clean_input($_GET['to']      ?? '');
        $search  = clean_input($_GET['search']  ?? '');
        $page    = max(1, (int) ($_GET['page']  ?? 1));
        $perPage = 50;

        $total = $this->logs->filteredCount($from ?: null, $to ?: null, $search);

        view('activity-logs/index', [
            'title'      => 'Activity Logs',
            'logs'       => $this->logs->filtered($from ?: null, $to ?: null, $search, $page, $perPage),
            'from'       => $from,
            'to'         => $to,
            'search'     => $search,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalLogs'  => $total,
            'totalPages' => (int) ceil($total / $perPage),
        ]);
    }
}
