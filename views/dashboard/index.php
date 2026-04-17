<?php
$buildLineChart = static function (array $series): array {
    $width = 640;
    $height = 220;
    $paddingX = 28;
    $paddingTop = 24;
    $paddingBottom = 34;
    $count = max(count($series), 1);
    $maxValue = max(array_map(static fn(array $point): float => (float) $point['value'], $series));
    $maxValue = $maxValue > 0 ? $maxValue : 1.0;
    $usableWidth = $width - ($paddingX * 2);
    $usableHeight = $height - $paddingTop - $paddingBottom;
    $stepX = $count > 1 ? $usableWidth / ($count - 1) : 0;

    $points = [];
    foreach ($series as $index => $point) {
        $x = $paddingX + ($stepX * $index);
        $ratio = (float) $point['value'] / $maxValue;
        $y = $paddingTop + $usableHeight - ($usableHeight * $ratio);
        $points[] = ['x' => $x, 'y' => $y, 'label' => $point['label'], 'value' => (float) $point['value']];
    }

    $pointString = implode(' ', array_map(
        static fn(array $point): string => round($point['x'], 2) . ',' . round($point['y'], 2),
        $points
    ));

    $areaPoints = $pointString;
    if ($points !== []) {
        $areaPoints .= ' ' . round($points[count($points) - 1]['x'], 2) . ',' . ($height - $paddingBottom);
        $areaPoints .= ' ' . round($points[0]['x'], 2) . ',' . ($height - $paddingBottom);
    }

    return [
        'width' => $width,
        'height' => $height,
        'baselineY' => $height - $paddingBottom,
        'maxValue' => $maxValue,
        'points' => $points,
        'pointString' => $pointString,
        'areaPoints' => $areaPoints,
    ];
};

$dailyChart = $buildLineChart($dailySalesSeries);
$monthlyChart = $buildLineChart($monthlySalesSeries);
?>

<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Operations Overview</h1>
        <p class="page-header-desc">Track inventory performance, sales activity, and operational exceptions from a cleaner admin workspace.</p>
    </div>
</div>

<div class="kpi-grid">
    <div class="stat-card">
        <div class="stat-card-bar primary"></div>
        <div class="stat-icon primary"><i class="fas fa-cube"></i></div>
        <div class="stat-label">Total Products</div>
        <div class="stat-value"><?= (int) $stats['total_products'] ?></div>
        <div class="stat-meta">Active inventory items</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-bar danger"></div>
        <div class="stat-icon danger"><i class="fas fa-triangle-exclamation"></i></div>
        <div class="stat-label">Low Stock Items</div>
        <div class="stat-value text-danger"><?= (int) $stats['low_stock_items'] ?></div>
        <div class="stat-meta">Requires restocking attention</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-bar success"></div>
        <div class="stat-icon success"><i class="fas fa-money-bill-wave"></i></div>
        <div class="stat-label">Daily Sales</div>
        <div class="stat-value"><?= e(format_currency($stats['daily_sales'])) ?></div>
        <div class="stat-meta">Today's completed transactions</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-bar warning"></div>
        <div class="stat-icon warning"><i class="fas fa-chart-pie"></i></div>
        <div class="stat-label">Total Revenue</div>
        <div class="stat-value"><?= e(format_currency($stats['total_revenue'])) ?></div>
        <div class="stat-meta">All-time recorded revenue</div>
    </div>
</div>

<div class="chart-grid">
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-wave-square"></i> Daily Sales Trend</span>
            <span class="badge bg-soft-info">Last 7 days</span>
        </div>
        <div class="card-body">
            <div class="chart-summary-row">
                <div>
                    <div class="section-title mb-1">This Week Snapshot</div>
                    <div class="small-muted">Use this to see short-term sales movement before checking the full report.</div>
                </div>
                <div class="chart-summary-value"><?= e(format_currency(array_sum(array_column($dailySalesSeries, 'value')))) ?></div>
            </div>
            <div class="sales-chart">
                <svg viewBox="0 0 <?= (int) $dailyChart['width'] ?> <?= (int) $dailyChart['height'] ?>" role="img" aria-label="Daily sales chart">
                    <defs>
                        <linearGradient id="dailyAreaGradient" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="rgba(147,197,253,0.55)"></stop>
                            <stop offset="100%" stop-color="rgba(147,197,253,0.04)"></stop>
                        </linearGradient>
                    </defs>
                    <line x1="28" y1="<?= (int) $dailyChart['baselineY'] ?>" x2="<?= (int) ($dailyChart['width'] - 28) ?>" y2="<?= (int) $dailyChart['baselineY'] ?>" class="chart-axis"></line>
                    <polygon points="<?= e($dailyChart['areaPoints']) ?>" class="chart-area"></polygon>
                    <polyline points="<?= e($dailyChart['pointString']) ?>" class="chart-line chart-line-daily"></polyline>
                    <?php foreach ($dailyChart['points'] as $point): ?>
                        <circle cx="<?= e((string) round($point['x'], 2)) ?>" cy="<?= e((string) round($point['y'], 2)) ?>" r="4.5" class="chart-dot chart-dot-daily"></circle>
                    <?php endforeach; ?>
                </svg>
            </div>
            <div class="chart-labels">
                <?php foreach ($dailySalesSeries as $point): ?>
                    <div class="chart-label">
                        <span><?= e($point['label']) ?></span>
                        <strong><?= e(format_currency($point['value'])) ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-chart-column"></i> Monthly Sales Trend</span>
            <span class="badge bg-soft-primary">Last 6 months</span>
        </div>
        <div class="card-body">
            <div class="chart-summary-row">
                <div>
                    <div class="section-title mb-1">Monthly Performance</div>
                    <div class="small-muted">This shows the broader sales direction and seasonality across recent months.</div>
                </div>
                <div class="chart-summary-value"><?= e(format_currency(array_sum(array_column($monthlySalesSeries, 'value')))) ?></div>
            </div>
            <div class="sales-chart">
                <svg viewBox="0 0 <?= (int) $monthlyChart['width'] ?> <?= (int) $monthlyChart['height'] ?>" role="img" aria-label="Monthly sales chart">
                    <defs>
                        <linearGradient id="monthlyAreaGradient" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="rgba(127,156,245,0.50)"></stop>
                            <stop offset="100%" stop-color="rgba(127,156,245,0.04)"></stop>
                        </linearGradient>
                    </defs>
                    <line x1="28" y1="<?= (int) $monthlyChart['baselineY'] ?>" x2="<?= (int) ($monthlyChart['width'] - 28) ?>" y2="<?= (int) $monthlyChart['baselineY'] ?>" class="chart-axis"></line>
                    <polygon points="<?= e($monthlyChart['areaPoints']) ?>" class="chart-area chart-area-monthly"></polygon>
                    <polyline points="<?= e($monthlyChart['pointString']) ?>" class="chart-line chart-line-monthly"></polyline>
                    <?php foreach ($monthlyChart['points'] as $point): ?>
                        <circle cx="<?= e((string) round($point['x'], 2)) ?>" cy="<?= e((string) round($point['y'], 2)) ?>" r="4.5" class="chart-dot chart-dot-monthly"></circle>
                    <?php endforeach; ?>
                </svg>
            </div>
            <div class="chart-labels">
                <?php foreach ($monthlySalesSeries as $point): ?>
                    <div class="chart-label">
                        <span><?= e($point['label']) ?></span>
                        <strong><?= e(format_currency($point['value'])) ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-grid mt-4">
    <div class="card h-100">
        <div class="card-header">
            <span><i class="fas fa-receipt"></i> Recent Transactions</span>
            <a class="btn btn-outline-primary btn-sm btn-icon" href="<?= e(base_url('reports')) ?>" title="View reports" aria-label="View reports"><i class="fas fa-arrow-up-right-from-square"></i></a>
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

    <div class="card dashboard-side-column">
        <div class="card-header">
            <span><i class="fas fa-calendar-check"></i> Current Month</span>
        </div>
        <div class="card-body compact-card-body">
            <div class="summary-stack">
                <div class="profile-meta-item">
                    <div class="profile-meta-label">Month To Date Sales</div>
                    <div class="profile-meta-value"><?= e(format_currency($stats['monthly_sales'])) ?></div>
                </div>
                <div class="profile-meta-item">
                    <div class="profile-meta-label">Today</div>
                    <div class="profile-meta-value"><?= e(format_currency($stats['daily_sales'])) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <span><i class="fas fa-history"></i> Recent Activity Logs</span>
        <a class="btn btn-outline-primary btn-sm btn-icon" href="<?= e(base_url('activity-logs')) ?>" title="View all logs" aria-label="View all logs">
            <i class="fas fa-arrow-up-right-from-square"></i>
        </a>
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
