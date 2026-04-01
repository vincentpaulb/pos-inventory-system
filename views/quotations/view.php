<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Quotation Details</h1>
        <p class="page-header-desc">Review, print, or share the quotation with your customer.</p>
    </div>
    <div class="page-header-actions">
        <button type="button" class="btn btn-primary" onclick="window.print()">🖨 Print Quotation</button>
        <a class="btn btn-outline-secondary" href="<?= e(base_url('quotations')) ?>">← Back to Quotations</a>
    </div>
</div>

<div class="card printable-area">
    <div class="card-body" style="padding:28px">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div>
                <div style="font-size:1.35rem;font-weight:800;color:var(--text)"><?= e(APP_NAME) ?></div>
                <div class="small-muted">Heavy Equipment Parts Trading</div>
                <div class="small-muted">Quotation Document</div>
            </div>
            <div class="text-end">
                <div class="badge bg-soft-primary" style="font-size:.78rem"><?= e($quotation['quote_no']) ?></div>
                <div class="small-muted mt-2">Created: <?= e(format_datetime($quotation['created_at'])) ?></div>
                <div class="small-muted">Prepared by: <?= e($quotation['prepared_by'] ?: 'System') ?></div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card-soft h-100">
                    <div style="font-weight:700;margin-bottom:6px">Customer Information</div>
                    <div><strong><?= e($quotation['customer_name']) ?></strong></div>
                    <div class="small-muted"><?= e($quotation['customer_contact'] ?: 'No contact provided') ?></div>
                    <div class="small-muted" style="white-space:pre-line"><?= e($quotation['customer_address'] ?: 'No address provided') ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-soft h-100">
                    <div style="font-weight:700;margin-bottom:6px">Service Option</div>
                    <div><strong><?= e($quotation['service_option'] === 'with_service_repair' ? 'With Service Repair' : 'Without Service Repair') ?></strong></div>
                    <div class="small-muted"><?= e($quotation['service_description'] ?: 'No service repair description') ?></div>
                    <div class="small-muted">Valid Until: <?= e($quotation['valid_until'] ? format_date($quotation['valid_until']) : 'Not specified') ?></div>
                </div>
            </div>
        </div>

        <div style="border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;margin-bottom:22px">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Barcode</th>
                        <th style="width:90px">Qty</th>
                        <th style="width:120px">Unit Price</th>
                        <th style="width:120px">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $index => $item): ?>
                        <tr>
                            <td><?= (int) ($index + 1) ?></td>
                            <td style="font-weight:700;font-size:.82rem"><?= e($item['product_name'] ?: 'Deleted Product') ?></td>
                            <td class="small-muted"><?= e($item['barcode'] ?: '-') ?></td>
                            <td><?= (int) $item['quantity'] ?></td>
                            <td><?= e(format_currency($item['unit_price'])) ?></td>
                            <td><strong><?= e(format_currency($item['subtotal'])) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="row g-3">
            <div class="col-lg-7">
                <div class="card-soft h-100">
                    <div style="font-weight:700;margin-bottom:6px">Notes / Terms</div>
                    <div class="small-muted" style="white-space:pre-line"><?= e($quotation['notes'] ?: 'No additional notes.') ?></div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card-soft h-100">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Parts Subtotal</span>
                        <strong><?= e(format_currency($quotation['subtotal_amount'])) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Service Repair Fee</span>
                        <strong><?= e(format_currency($quotation['service_fee'])) ?></strong>
                    </div>
                    <hr style="border-color:var(--border)">
                    <div class="d-flex justify-content-between" style="font-size:1.02rem">
                        <span style="font-weight:800">Total Quotation Amount</span>
                        <strong style="color:var(--primary);font-size:1.18rem"><?= e(format_currency($quotation['total_amount'])) ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
