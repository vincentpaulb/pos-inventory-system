<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Stock Adjustment</h1>
        <p class="page-header-desc">Record stock-in or stock-out transactions for <strong><?= e($product['name']) ?></strong>.</p>
    </div>
    <div class="page-header-actions">
        <a class="btn btn-outline-secondary btn-sm" href="<?= e(base_url('products')) ?>">← Back to Inventory</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">📦 Current Stock</div>
            <div class="card-body d-flex flex-column justify-content-center" style="gap:12px">
                <div>
                    <div class="stat-label">Product</div>
                    <div style="font-size:.95rem;font-weight:700;margin-top:4px"><?= e($product['name']) ?></div>
                </div>
                <div>
                    <div class="stat-label">Available Quantity</div>
                    <div class="stat-value" style="font-size:2.2rem"><?= (int) $product['stock_quantity'] ?></div>
                    <div class="stat-meta">pieces in stock</div>
                </div>
                <div class="card-soft" style="margin-top:6px">
                    <div class="small-muted">Use this screen to keep a proper stock movement history for accurate inventory tracking.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">⚖️ New Adjustment</div>
            <div class="card-body">
                <form method="POST" action="<?= e(base_url('products/stock')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Movement Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="movement_type" required>
                                <option value="in">↑ Stock In</option>
                                <option value="out">↓ Stock Out</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" min="1" name="quantity" placeholder="e.g. 10" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Remarks</label>
                            <input type="text" class="form-control" name="remarks" placeholder="Optional note">
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-primary">Save Adjustment</button>
                        <a class="btn btn-outline-secondary" href="<?= e(base_url('products')) ?>">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
