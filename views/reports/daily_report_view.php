<?php
$r = $report;
$netRevenue = max(0, (float) $r['gross_sales'] - (float) $r['total_expenses']);
$vatLabel = system_vat_label();
?>
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Daily Sales Report</h1>
        <p class="page-header-desc"><?= e($r['report_date']) ?> &mdash; <?= e($r['employee_name']) ?></p>
    </div>
    <div class="page-header-actions">
        <?php if ($currentRole === 'Admin'): ?>
        <a href="<?= e(base_url('reports?tab=daily-reports')) ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
        <?php elseif ($currentRole === 'Sales Manager' || $currentRole === 'Cashier'): ?>
        <a href="<?= e(base_url('my-reports')) ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to My Reports
        </a>
        <?php endif; ?>

        <button onclick="window.print()" class="btn btn-outline-primary">
            <i class="fas fa-print"></i> Print
        </button>
    </div>
</div>

<style>
@media print {
    .page-header-actions, .sidebar, .topbar, nav { display: none !important; }
    .card { break-inside: avoid; }
}
</style>

<!-- Header Info -->
<div class="card mb-4">
    <div class="card-header"><i class="fas fa-circle-info"></i> Header Information</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-muted" style="font-size:.76rem">Report Date</div>
                <div style="font-weight:700"><?= e($r['report_date']) ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted" style="font-size:.76rem">Employee</div>
                <div style="font-weight:700"><?= e($r['employee_name']) ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted" style="font-size:.76rem">Employee ID</div>
                <div style="font-weight:700">#<?= (int) $r['user_id'] ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted" style="font-size:.76rem">Submitted At</div>
                <div style="font-weight:700"><?= e(format_datetime($r['submitted_at'])) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Sales Performance -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><i class="fas fa-chart-bar"></i> Sales Performance Metrics</div>
            <div class="card-body p-0">
                <table class="table mb-0" style="font-size:.82rem">
                    <tbody>
                        <tr>
                            <td class="text-muted ps-3">Total Transactions</td>
                            <td class="text-end pe-3 fw-bold"><?= (int) $r['total_transactions'] ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Total Units Sold</td>
                            <td class="text-end pe-3 fw-bold"><?= (int) $r['total_units_sold'] ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Gross Sales (Total Revenue)</td>
                            <td class="text-end pe-3 fw-bold"><?= e(format_currency($r['gross_sales'])) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Net Sales (excl. <?= e($vatLabel) ?>)</td>
                            <td class="text-end pe-3 fw-bold"><?= e(format_currency($r['net_sales'])) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Average Transaction Value</td>
                            <td class="text-end pe-3 fw-bold"><?= e(format_currency($r['average_transaction_value'])) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment Breakdown -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><i class="fas fa-credit-card"></i> Payment Breakdown</div>
            <div class="card-body p-0">
                <table class="table mb-0" style="font-size:.82rem">
                    <tbody>
                        <tr>
                            <td class="text-muted ps-3"><i class="fas fa-money-bill-wave" style="width:16px"></i> Cash</td>
                            <td class="text-end pe-3 fw-bold"><?= e(format_currency($r['cash_sales'])) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3"><i class="fas fa-credit-card" style="width:16px"></i> Credit Card</td>
                            <td class="text-end pe-3 fw-bold"><?= e(format_currency($r['credit_card_sales'])) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3"><i class="fas fa-mobile-screen" style="width:16px"></i> GCash / Maya</td>
                            <td class="text-end pe-3 fw-bold"><?= e(format_currency($r['gcash_maya_sales'])) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3"><i class="fas fa-building-columns" style="width:16px"></i> Bank Transfer</td>
                            <td class="text-end pe-3 fw-bold"><?= e(format_currency($r['bank_transfer_sales'])) ?></td>
                        </tr>
                        <tr class="table-light">
                            <td class="ps-3 fw-bold">Total Collected</td>
                            <td class="text-end pe-3 fw-bold"><?= e(format_currency($r['gross_sales'])) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Adjustments -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><i class="fas fa-rotate-left"></i> Adjustments &amp; Tax</div>
            <div class="card-body p-0">
                <table class="table mb-0" style="font-size:.82rem">
                    <tbody>
                        <tr>
                            <td class="text-muted ps-3">Refunds / Voided Transactions</td>
                            <td class="text-end pe-3 fw-bold"><?= (int) $r['total_voids'] ?> transactions</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Voided Amount</td>
                            <td class="text-end pe-3 fw-bold text-warning"><?= e(format_currency($r['voided_amount'])) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Discounts Applied</td>
                            <td class="text-end pe-3 fw-bold">N/A</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Total <?= e($vatLabel) ?> Collected</td>
                            <td class="text-end pe-3 fw-bold"><?= e(format_currency($r['vat_collected'])) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Financial & Inventory -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><i class="fas fa-money-bill-wave"></i> Financial &amp; Inventory</div>
            <div class="card-body p-0">
                <table class="table mb-0" style="font-size:.82rem">
                    <tbody>
                        <tr>
                            <td class="text-muted ps-3">Total Daily Expenses</td>
                            <td class="text-end pe-3 fw-bold text-danger"><?= e(format_currency($r['total_expenses'])) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Inventory Units Deducted</td>
                            <td class="text-end pe-3 fw-bold"><?= (int) $r['total_units_sold'] ?> units</td>
                        </tr>
                        <tr class="table-light">
                            <td class="ps-3 fw-bold">Net Revenue (after expenses)</td>
                            <td class="text-end pe-3 fw-bold text-success"><?= e(format_currency($netRevenue)) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($r['notes'])): ?>
<div class="card mb-4">
    <div class="card-header"><i class="fas fa-note-sticky"></i> Notes</div>
    <div class="card-body" style="font-size:.82rem"><?= e($r['notes']) ?></div>
</div>
<?php endif; ?>
