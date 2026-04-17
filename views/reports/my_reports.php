<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">My Daily Sales Reports</h1>
        <p class="page-header-desc">
            <?php if ($canSeeOthers): ?>
                Your submitted reports and all Cashier reports.
            <?php else: ?>
                Your submitted daily sales reports.
            <?php endif; ?>
        </p>
    </div>
</div>

<!-- Date Filter -->
<div class="card mb-4">
    <div class="card-header"><i class="fas fa-calendar"></i> Date Filter</div>
    <div class="card-body">
        <form class="row g-3 align-items-end" method="GET" action="<?= e(base_url('my-reports')) ?>">
            <div class="col-md-3">
                <label class="form-label">From</label>
                <input type="date" class="form-control" name="from" value="<?= e($from) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">To</label>
                <input type="date" class="form-control" name="to" value="<?= e($to) ?>">
            </div>
            <div class="col-md-6 d-flex gap-2 align-items-end flex-wrap">
                <button class="btn btn-primary" type="submit"><i class="fas fa-check"></i> Apply Filter</button>
                <a class="btn btn-outline-secondary" href="<?= e(base_url('my-reports')) ?>"><i class="fas fa-redo"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Reports Table -->
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-file-lines"></i> Daily Sales Reports</span>
        <span class="badge bg-soft-primary"><?= count($reports) ?> records</span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Date</th>
                    <?php if ($canSeeOthers): ?><th>Employee</th><?php endif; ?>
                    <th>Gross Sales</th>
                    <th>Net Sales</th>
                    <th>VAT</th>
                    <th>Transactions</th>
                    <th>Units Sold</th>
                    <th>Expenses</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($reports as $row): ?>
                <tr>
                    <td><span class="badge bg-soft-primary"><?= e($row['report_date']) ?></span></td>
                    <?php if ($canSeeOthers): ?>
                        <td style="font-weight:600;font-size:.82rem">
                            <?= e($row['employee_name']) ?>
                            <div class="small-muted"><?= e($row['employee_role']) ?></div>
                        </td>
                    <?php endif; ?>
                    <td><strong><?= e(format_currency($row['gross_sales'])) ?></strong></td>
                    <td class="small-muted"><?= e(format_currency($row['net_sales'])) ?></td>
                    <td class="small-muted"><?= e(format_currency($row['vat_collected'])) ?></td>
                    <td class="text-center"><?= (int) $row['total_transactions'] ?></td>
                    <td class="text-center"><?= (int) $row['total_units_sold'] ?></td>
                    <td class="small-muted text-danger"><?= e(format_currency($row['total_expenses'])) ?></td>
                    <td class="small-muted"><?= e(format_datetime($row['submitted_at'])) ?></td>
                    <td>
                        <div class="action-group">
                            <a href="<?= e(base_url('reports/daily-report?id=' . (int) $row['id'])) ?>"
                               class="btn btn-sm btn-outline-primary btn-icon" title="View full report">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if ((int) $row['user_id'] === (int) auth_user()['id']): ?>
                                <form method="POST" action="<?= e(base_url('reports/delete-dsr')) ?>"
                                      class="js-confirm-form"
                                      data-confirm-message="Delete this daily sales report? This cannot be undone."
                                      data-confirm-button="Delete">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                    <input type="hidden" name="redirect" value="my-reports">
                                    <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete report">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$reports): ?>
                <tr>
                    <td colspan="<?= $canSeeOthers ? 10 : 9 ?>" class="text-center text-muted py-4" style="font-size:.82rem">
                        No daily sales reports found. Use the <strong>Daily Sales Report</strong> button on the POS page to submit one.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
