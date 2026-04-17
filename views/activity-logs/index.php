<?php
$actionLabels = [
    'login'                  => ['label' => 'Login',                    'badge' => 'bg-soft-primary'],
    'logout'                 => ['label' => 'Logout',                   'badge' => 'bg-soft-primary'],
    'product_create'         => ['label' => 'Added Product',            'badge' => 'bg-soft-success'],
    'product_update'         => ['label' => 'Updated Product',          'badge' => 'bg-soft-warning'],
    'product_delete'         => ['label' => 'Deleted Product',          'badge' => 'bg-soft-danger'],
    'stock_adjust'           => ['label' => 'Stock Adjusted',           'badge' => 'bg-soft-warning'],
    'category_create'        => ['label' => 'Added Category',           'badge' => 'bg-soft-success'],
    'category_update'        => ['label' => 'Updated Category',         'badge' => 'bg-soft-warning'],
    'category_delete'        => ['label' => 'Deleted Category',         'badge' => 'bg-soft-danger'],
    'supplier_create'        => ['label' => 'Added Supplier',           'badge' => 'bg-soft-success'],
    'supplier_update'        => ['label' => 'Updated Supplier',         'badge' => 'bg-soft-warning'],
    'supplier_delete'        => ['label' => 'Deleted Supplier',         'badge' => 'bg-soft-danger'],
    'user_create'            => ['label' => 'Added User',               'badge' => 'bg-soft-success'],
    'user_update'            => ['label' => 'Updated User',             'badge' => 'bg-soft-warning'],
    'user_delete'            => ['label' => 'Deleted User',             'badge' => 'bg-soft-danger'],
    'sale_complete'          => ['label' => 'Sale Completed',           'badge' => 'bg-soft-success'],
    'sale_void'              => ['label' => 'Sale Voided',              'badge' => 'bg-soft-danger'],
    'daily_report_submit'    => ['label' => 'Submitted Daily Report',   'badge' => 'bg-soft-success'],
    'daily_report_delete'    => ['label' => 'Deleted Daily Report',     'badge' => 'bg-soft-danger'],
    'quotation_create'       => ['label' => 'Created Quotation',        'badge' => 'bg-soft-success'],
    'quotation_update'       => ['label' => 'Updated Quotation',        'badge' => 'bg-soft-warning'],
    'quotation_delete'       => ['label' => 'Deleted Quotation',        'badge' => 'bg-soft-danger'],
    'expense_create'         => ['label' => 'Added Expense',            'badge' => 'bg-soft-success'],
    'expense_delete'         => ['label' => 'Deleted Expense',          'badge' => 'bg-soft-danger'],
    'cash_on_hand_record'    => ['label' => 'Recorded Cash on Hand',    'badge' => 'bg-soft-warning'],
    'organization_update'    => ['label' => 'Updated Organization Info','badge' => 'bg-soft-warning'],
    'profile_update'         => ['label' => 'Updated Profile',          'badge' => 'bg-soft-warning'],
];

function format_action(string $action, array $labels): array {
    if (isset($labels[$action])) {
        return $labels[$action];
    }
    $label = ucwords(str_replace('_', ' ', $action));
    $badge = match(true) {
        str_contains($action, 'delete') || str_contains($action, 'void') => 'bg-soft-danger',
        str_contains($action, 'create') || str_contains($action, 'submit') || str_contains($action, 'complete') => 'bg-soft-success',
        str_contains($action, 'update') || str_contains($action, 'adjust') => 'bg-soft-warning',
        default => 'bg-soft-primary',
    };
    return ['label' => $label, 'badge' => $badge];
}
?>

<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Activity Logs</h1>
        <p class="page-header-desc">Full audit trail of all user actions across the system.</p>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header"><i class="fas fa-filter"></i> Filter</div>
    <div class="card-body">
        <form class="row g-3 align-items-end" method="GET" action="<?= e(base_url('activity-logs')) ?>">
            <div class="col-md-3">
                <label class="form-label">From</label>
                <input type="date" class="form-control" name="from" value="<?= e($from) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">To</label>
                <input type="date" class="form-control" name="to" value="<?= e($to) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" class="form-control" name="search" value="<?= e($search) ?>"
                       placeholder="Action, details, or user name...">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary w-100" type="submit"><i class="fas fa-check"></i> Apply</button>
                <a class="btn btn-outline-secondary" href="<?= e(base_url('activity-logs')) ?>"><i class="fas fa-redo"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span><i class="fas fa-list-check"></i> Logs</span>
        <span class="badge bg-soft-primary"><?= number_format($totalLogs) ?> records</span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th style="width:160px">Date &amp; Time</th>
                    <th style="width:160px">User</th>
                    <th style="width:200px">Action</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($logs as $row): ?>
                <?php $actionInfo = format_action($row['action'], $actionLabels); ?>
                <tr>
                    <td class="small-muted"><?= e(format_datetime($row['created_at'])) ?></td>
                    <td style="font-weight:600;font-size:.82rem"><?= e($row['user_name'] ?? 'System') ?></td>
                    <td>
                        <span class="badge <?= $actionInfo['badge'] ?>" style="font-size:.72rem"><?= e($actionInfo['label']) ?></span>
                    </td>
                    <td style="font-size:.82rem"><?= e($row['details']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$logs): ?>
                <tr><td colspan="4" class="text-center text-muted py-4" style="font-size:.82rem">No activity logs found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="card-footer d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div class="small-muted">
            Showing <?= number_format(($page - 1) * $perPage + 1) ?>–<?= number_format(min($page * $perPage, $totalLogs)) ?> of <?= number_format($totalLogs) ?> records
        </div>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
                        <i class="fas fa-chevron-left" style="font-size:.65rem"></i>
                    </a>
                </li>
                <?php
                $start = max(1, $page - 2);
                $end   = min($totalPages, $page + 2);
                if ($start > 1): ?>
                    <li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>">1</a></li>
                    <?php if ($start > 2): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                <?php endif; ?>
                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($end < $totalPages): ?>
                    <?php if ($end < $totalPages - 1): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                    <li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $totalPages])) ?>"><?= $totalPages ?></a></li>
                <?php endif; ?>
                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
                        <i class="fas fa-chevron-right" style="font-size:.65rem"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>
