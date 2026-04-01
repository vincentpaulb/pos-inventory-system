<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Sales Receipt</h1>
        <p class="page-header-desc">Transaction completed successfully. Print or review the receipt below.</p>
    </div>
    <div class="page-header-actions">
        <a class="btn btn-outline-secondary btn-sm" href="<?= e(base_url('pos')) ?>">← Back to POS</a>
        <button class="btn btn-primary btn-sm" onclick="window.print()">🖨 Print Receipt</button>
    </div>
</div>

<div class="receipt-card">
    <div id="printArea" class="receipt-paper">
        <!-- Header -->
        <div style="text-align:center;border-bottom:2px solid #111;padding-bottom:16px;margin-bottom:16px">
            <div style="font-size:1.1rem;font-weight:800;letter-spacing:-.02em">R'B Heavy Equipment Parts Trading</div>
            <div style="font-size:.78rem;color:#555;margin-top:4px">Heavy Equipment Parts Shop</div>
        </div>

        <!-- Meta -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-bottom:16px;font-size:.78rem">
            <div><span style="color:#666">Invoice:</span> <strong><?= e($transaction['invoice_no']) ?></strong></div>
            <div><span style="color:#666">Date:</span> <strong><?= e(format_datetime($transaction['created_at'])) ?></strong></div>
            <div><span style="color:#666">Cashier:</span> <strong><?= e($transaction['cashier_name']) ?></strong></div>
        </div>

        <!-- Items table -->
        <table class="table table-sm" style="font-size:.78rem">
            <thead style="background:#f5f5f5">
                <tr><th style="padding:8px 6px">Item</th><th style="padding:8px 6px;text-align:center">Qty</th><th style="padding:8px 6px;text-align:right">Price</th><th style="padding:8px 6px;text-align:right">Subtotal</th></tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td style="padding:8px 6px"><?= e($item['product_name']) ?></td>
                    <td style="padding:8px 6px;text-align:center"><?= (int) $item['quantity'] ?></td>
                    <td style="padding:8px 6px;text-align:right"><?= e(format_currency($item['unit_price'])) ?></td>
                    <td style="padding:8px 6px;text-align:right;font-weight:700"><?= e(format_currency($item['subtotal'])) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Totals -->
        <div style="border-top:2px solid #111;padding-top:12px;margin-top:4px">
            <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:.82rem">
                <span>Total</span><strong style="font-size:1rem"><?= e(format_currency($transaction['total_amount'])) ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:.78rem;color:#555">
                <span>Payment Received</span><span><?= e(format_currency($transaction['payment_amount'])) ?></span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:.82rem;background:#f5f5f5;padding:8px 0;border-top:1px dashed #ccc">
                <span style="font-weight:700">Change</span><strong><?= e(format_currency($transaction['change_amount'])) ?></strong>
            </div>
        </div>

        <!-- Footer -->
        <div style="text-align:center;margin-top:20px;padding-top:16px;border-top:1px dashed #ccc;font-size:.74rem;color:#666">
            Thank you for your purchase!<br>
            <span style="margin-top:4px;display:block"><?= date('F d, Y — h:i A') ?></span>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        <button class="btn btn-primary" onclick="window.print()">🖨 Print Receipt</button>
        <a class="btn btn-outline-secondary" href="<?= e(base_url('pos')) ?>">← Back to POS</a>
    </div>
</div>
