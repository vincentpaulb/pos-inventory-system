<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Sales Reports</h1>
        <p class="page-header-desc">Analyze daily, weekly, and monthly revenue. Submit and review daily sales reports.</p>
    </div>
</div>

<!-- KPI Summaries -->
<div class="kpi-grid">
    <div class="stat-card">
        <div class="stat-card-bar primary"></div>
        <div class="stat-icon primary"><i class="fas fa-calendar-day"></i></div>
        <div class="stat-label">Daily Sales</div>
        <div class="stat-value"><?= e(format_currency($daily['total_sales'])) ?></div>
        <div class="stat-meta"><?= (int) $daily['total_transactions'] ?> transactions today</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-bar success"></div>
        <div class="stat-icon success"><i class="fas fa-calendar-week"></i></div>
        <div class="stat-label">Weekly Sales</div>
        <div class="stat-value"><?= e(format_currency($weekly['total_sales'])) ?></div>
        <div class="stat-meta"><?= (int) $weekly['total_transactions'] ?> transactions this week</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-bar warning"></div>
        <div class="stat-icon warning"><i class="fas fa-calendar-alt"></i></div>
        <div class="stat-label">Monthly Sales</div>
        <div class="stat-value"><?= e(format_currency($monthly['total_sales'])) ?></div>
        <div class="stat-meta"><?= (int) $monthly['total_transactions'] ?> transactions this month</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-bar danger"></div>
        <div class="stat-icon danger"><i class="fas fa-chart-line"></i></div>
        <div class="stat-label">Filtered Total</div>
        <div class="stat-value"><?= e(format_currency(array_sum(array_column($histories, 'total_amount')))) ?></div>
        <div class="stat-meta">Based on current date filter</div>
    </div>
</div>

<!-- Date Filter -->
<div class="card mb-4">
    <div class="card-header"><i class="fas fa-calendar"></i> Date Filter</div>
    <div class="card-body">
        <form class="row g-3 align-items-end" method="GET" action="<?= e(base_url('reports')) ?>">
            <input type="hidden" name="tab" value="<?= e($tab) ?>">
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
                <a class="btn btn-outline-secondary" href="<?= e(base_url('reports?tab=' . urlencode($tab))) ?>"><i class="fas fa-redo"></i> Reset</a>
                <?php if ($tab === 'daily-reports'): ?>
                    <a class="btn btn-outline-success" href="<?= e(base_url('reports/export-dsr?from=' . urlencode($from) . '&to=' . urlencode($to))) ?>"><i class="fas fa-download"></i> Export CSV</a>
                <?php else: ?>
                    <a class="btn btn-outline-success" href="<?= e(base_url('reports/export?from=' . urlencode($from) . '&to=' . urlencode($to))) ?>"><i class="fas fa-download"></i> Export CSV</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-0" id="reportsTabs" role="tablist" style="border-bottom:0">
    <li class="nav-item" role="presentation">
        <button
            class="nav-link <?= $tab === 'daily-reports' ? 'active' : '' ?>"
            type="button"
            onclick="window.location='<?= e(base_url('reports?tab=daily-reports&from=' . urlencode($from) . '&to=' . urlencode($to))) ?>'"
        >
            <i class="fas fa-file-lines"></i> Daily Sales Reports
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button
            class="nav-link <?= $tab === 'history' ? 'active' : '' ?>"
            type="button"
            onclick="window.location='<?= e(base_url('reports?tab=history&from=' . urlencode($from) . '&to=' . urlencode($to))) ?>'"
        >
            <i class="fas fa-clock-rotate-left"></i> Transaction History
        </button>
    </li>
</ul>

<?php if ($tab === 'daily-reports'): ?>
<!-- Daily Sales Reports Table -->
<div class="card" style="border-top-left-radius:0">
    <div class="card-header">
        <span><i class="fas fa-file-lines"></i> Daily Sales Reports</span>
        <span class="badge bg-soft-primary"><?= count($dailyReports) ?> records</span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Employee</th>
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
            <?php foreach ($dailyReports as $row): ?>
                <tr>
                    <td><span class="badge bg-soft-primary"><?= e($row['report_date']) ?></span></td>
                    <td style="font-weight:600;font-size:.82rem"><?= e($row['employee_name']) ?></td>
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
                            <form method="POST" action="<?= e(base_url('reports/delete-dsr')) ?>"
                                  class="js-confirm-form"
                                  data-confirm-message="Delete this daily sales report? This cannot be undone."
                                  data-confirm-button="Delete">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete report">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$dailyReports): ?>
                <tr><td colspan="10" class="text-center text-muted py-4" style="font-size:.82rem">No daily sales reports submitted yet. Use the <strong>Daily Sales Report</strong> button on the POS page to submit one.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php else: ?>
<!-- Transaction History Table -->
<div class="card" style="border-top-right-radius:0">
    <div class="card-header">
        <span><i class="fas fa-clock-rotate-left"></i> Transaction History</span>
        <span class="badge bg-soft-primary"><?= count($histories) ?> records</span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr><th>Invoice</th><th>Cashier</th><th>Total</th><th>Payment</th><th>Change</th><th>Date</th></tr>
            </thead>
            <tbody>
            <?php foreach ($histories as $row): ?>
                <tr>
                    <td><span class="badge bg-soft-primary"><?= e($row['invoice_no']) ?></span></td>
                    <td style="font-weight:600;font-size:.82rem"><?= e($row['cashier_name']) ?></td>
                    <td><strong><?= e(format_currency($row['total_amount'])) ?></strong></td>
                    <td class="small-muted"><?= e(format_currency($row['payment_amount'])) ?></td>
                    <td class="small-muted"><?= e(format_currency($row['change_amount'])) ?></td>
                    <td class="small-muted"><?= e(format_datetime($row['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$histories): ?>
                <tr><td colspan="6" class="text-center text-muted py-4" style="font-size:.82rem">No sales found for the selected range.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
