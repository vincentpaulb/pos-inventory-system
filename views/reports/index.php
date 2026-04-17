<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Sales Reports</h1>
        <p class="page-header-desc">Analyze daily, weekly, and monthly revenue with full transaction history and CSV export.</p>
    </div>
    <div class="page-header-actions">
        <a class="btn btn-outline-success btn-sm" href="<?= e(base_url('reports/export?from=' . urlencode($from) . '&to=' . urlencode($to))) ?>"><i class="fas fa-download"></i> Export CSV</a>
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
            <div class="col-md-3">
                <label class="form-label">From</label>
                <input type="date" class="form-control" name="from" value="<?= e($from) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">To</label>
                <input type="date" class="form-control" name="to" value="<?= e($to) ?>">
            </div>
            <div class="col-md-6 d-flex gap-2 align-items-end">
                <button class="btn btn-primary" type="submit"><i class="fas fa-check"></i> Apply Filter</button>
                <a class="btn btn-outline-secondary" href="<?= e(base_url('reports')) ?>"><i class="fas fa-redo"></i> Reset</a>
                <a class="btn btn-outline-success" href="<?= e(base_url('reports/export?from=' . urlencode($from) . '&to=' . urlencode($to))) ?>"><i class="fas fa-download"></i> Export CSV</a>
            </div>
        </form>
    </div>
</div>

<!-- History Table -->
<div class="card">
    <div class="card-header">
        <span>🧾 Sales History</span>
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
