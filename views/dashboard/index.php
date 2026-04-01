<!-- Page header -->
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Business Overview</h1>
        <p class="page-header-desc">Monitor inventory health, revenue, low stock alerts, and daily operations.</p>
    </div>
    <div class="page-header-actions">
        <a class="btn btn-outline-secondary btn-sm" href="<?= e(base_url('reports')) ?>">📈 Reports</a>
        <a class="btn btn-success btn-sm" href="<?= e(base_url('pos')) ?>">🛒 Open POS</a>
        <a class="btn btn-primary btn-sm" href="<?= e(base_url('products/create')) ?>">+ Add Product</a>
    </div>
</div>

<!-- KPI Cards -->
<div class="kpi-grid">
    <div class="stat-card">
        <div class="stat-card-bar primary"></div>
        <div class="stat-icon primary">📦</div>
        <div class="stat-label">Total Products</div>
        <div class="stat-value"><?= (int) $stats['total_products'] ?></div>
        <div class="stat-meta">Active inventory items</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-bar danger"></div>
        <div class="stat-icon danger">⚠️</div>
        <div class="stat-label">Low Stock Items</div>
        <div class="stat-value text-danger"><?= (int) $stats['low_stock_items'] ?></div>
        <div class="stat-meta">Requires restocking attention</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-bar success"></div>
        <div class="stat-icon success">💰</div>
        <div class="stat-label">Daily Sales</div>
        <div class="stat-value"><?= e(format_currency($stats['daily_sales'])) ?></div>
        <div class="stat-meta">Today's completed transactions</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-bar warning"></div>
        <div class="stat-icon warning">📊</div>
        <div class="stat-label">Total Revenue</div>
        <div class="stat-value"><?= e(format_currency($stats['total_revenue'])) ?></div>
        <div class="stat-meta">All-time recorded revenue</div>
    </div>
</div>

<!-- Main panels -->
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header">
                <span>🧾 Recent Transactions</span>
                <a class="btn btn-outline-secondary btn-sm" href="<?= e(base_url('reports')) ?>">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Cashier</th>
                            <th>Total</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recentTransactions as $row): ?>
                        <tr>
                            <td><span class="badge bg-soft-primary"><?= e($row['invoice_no']) ?></span></td>
                            <td class="fw-600"><?= e($row['cashier_name']) ?></td>
                            <td><strong><?= e(format_currency($row['total_amount'])) ?></strong></td>
                            <td class="small-muted"><?= e(format_datetime($row['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$recentTransactions): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4" style="font-size:.82rem">No transactions yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <span>🔴 Low Stock Alerts</span>
                <a class="btn btn-outline-secondary btn-sm" href="<?= e(base_url('products')) ?>">Manage</a>
            </div>
            <div class="card-body">
                <ul class="list-clean">
                    <?php foreach ($lowStock as $row): ?>
                        <li>
                            <div>
                                <div style="font-size:.82rem;font-weight:700"><?= e($row['name']) ?></div>
                                <div class="small-muted"><?= e($row['category_name']) ?></div>
                            </div>
                            <span class="badge bg-soft-danger"><?= (int) $row['stock_quantity'] ?> pcs</span>
                        </li>
                    <?php endforeach; ?>
                    <?php if (!$lowStock): ?>
                        <li style="color:var(--muted);font-size:.80rem">✅ All stocks are healthy.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <span>🕓 Recent Activity Logs</span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Details</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($activities as $row): ?>
                        <tr>
                            <td style="font-weight:600"><?= e($row['user_name'] ?? 'System') ?></td>
                            <td><span class="badge bg-soft-primary"><?= e($row['action']) ?></span></td>
                            <td class="small-muted"><?= e($row['details']) ?></td>
                            <td class="small-muted"><?= e(format_datetime($row['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$activities): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4" style="font-size:.82rem">No activity yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
