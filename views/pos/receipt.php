<?php
$receiptPrintedAt = format_datetime($transaction['created_at']);
$receiptTax = sales_tax_breakdown((float) $transaction['total_amount']);
$receiptNetPrice = $receiptTax['net'];
$receiptVatAmount = $receiptTax['vat'];
$receiptVatLabel = system_vat_label();
$organization = organization_info();
$receiptCompanyName = organization_name();
$receiptCompanyLine = organization_secondary_line();
?>

<style>
@media print {
    @page {
        size: 80mm auto;
        margin: 0;
    }

    html,
    body {
        width: 80mm !important;
        max-width: 80mm !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
    }

    .receipt-card,
    #printArea,
    .receipt-paper {
        width: 80mm !important;
        max-width: 80mm !important;
        margin: 0 auto !important;
    }
}
</style>

<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Sales Receipt</h1>
        <p class="page-header-desc">Transaction completed successfully. Print or review the receipt below.</p>
    </div>
    <div class="page-header-actions">
        <a class="btn btn-outline-secondary btn-sm" href="<?= e(base_url('pos')) ?>">
            <i class="fas fa-arrow-left"></i> Back to POS
        </a>
    </div>
</div>

<div class="receipt-card">
    <div id="printArea" class="receipt-paper">
        <div class="receipt-thermal">
            <div class="receipt-thermal-header">
                <div class="receipt-thermal-title"><?= e($receiptCompanyName) ?></div>
                <?php if ($receiptCompanyLine !== ''): ?>
                    <div class="receipt-thermal-subtitle"><?= e($receiptCompanyLine) ?></div>
                <?php endif; ?>
                <div class="receipt-thermal-subtitle">Sales Receipt</div>
            </div>

            <div class="receipt-thermal-divider"></div>

            <div class="receipt-thermal-meta">
                <div class="receipt-thermal-meta-row">
                    <span>Invoice:</span>
                    <strong><?= e($transaction['invoice_no']) ?></strong>
                </div>
                <div class="receipt-thermal-meta-row">
                    <span>Date:</span>
                    <strong><?= e($receiptPrintedAt) ?></strong>
                </div>
                <div class="receipt-thermal-meta-row">
                    <span>Cashier:</span>
                    <strong><?= e($transaction['cashier_name']) ?></strong>
                </div>
                <div class="receipt-thermal-meta-row">
                    <span>Customer:</span>
                    <strong><?= e($transaction['customer_name'] ?: 'Walk-in Customer') ?></strong>
                </div>
                <div class="receipt-thermal-meta-row">
                    <span>Payment:</span>
                    <strong><?= e($transaction['payment_method'] ?: 'Cash') ?></strong>
                </div>
                <?php if (!empty($transaction['reference_no'])): ?>
                    <div class="receipt-thermal-meta-row">
                        <span>Ref #:</span>
                        <strong><?= e($transaction['reference_no']) ?></strong>
                    </div>
                <?php endif; ?>
                <?php if (($transaction['status'] ?? 'completed') !== 'completed'): ?>
                    <div class="receipt-thermal-meta-row">
                        <span>Status:</span>
                        <strong><?= e(strtoupper((string) $transaction['status'])) ?></strong>
                    </div>
                <?php endif; ?>
            </div>

            <div class="receipt-thermal-divider"></div>

            <table class="receipt-thermal-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th style="width:34px;text-align:center">Qty</th>
                        <th style="width:46px;text-align:center">Unit</th>
                        <th style="width:84px;text-align:right">Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="receipt-thermal-item-name"><?= e($item['product_name']) ?></td>
                            <td style="text-align:center"><?= (int) $item['quantity'] ?></td>
                            <td style="text-align:center"><?= e($item['unit_type'] ?: 'PC') ?></td>
                            <td style="text-align:right"><?= e(format_currency($item['subtotal'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="receipt-thermal-divider"></div>

            <div class="receipt-thermal-summary">
                <div class="receipt-thermal-total-row">
                    <span>Sub Total</span>
                    <strong><?= e(format_currency($transaction['total_amount'])) ?></strong>
                </div>
                <div class="receipt-thermal-summary-row">
                    <span>Net Price</span>
                    <strong><?= e(format_currency($receiptNetPrice)) ?></strong>
                </div>
                <div class="receipt-thermal-summary-row">
                    <span><?= e($receiptVatLabel) ?></span>
                    <strong><?= e(format_currency($receiptVatAmount)) ?></strong>
                </div>
                <div class="receipt-thermal-summary-row">
                    <span>Cash</span>
                    <strong><?= e(format_currency($transaction['payment_amount'])) ?></strong>
                </div>
                <div class="receipt-thermal-summary-row">
                    <span>Change</span>
                    <strong><?= e(format_currency($transaction['change_amount'])) ?></strong>
                </div>
            </div>

            <div class="receipt-thermal-divider receipt-thermal-divider-short"></div>

            <div class="receipt-thermal-barcode" aria-hidden="true"></div>
            <div class="receipt-thermal-barcode-label"><?= e($transaction['invoice_no']) ?></div>

            <div class="receipt-thermal-footer">
                <div>THANK YOU!</div>
                <div>Glad to see you again!</div>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center gap-2">
        <button class="btn btn-primary" type="button" onclick="window.print()">
            <i class="fas fa-print"></i> Print Receipt
        </button>
    </div>
</div>
