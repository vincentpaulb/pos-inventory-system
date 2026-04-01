<?php $action = "products/create"; $buttonText = "Save Product"; $product = []; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Add New Product</h1>
        <p class="page-header-desc">Create a new inventory item with category, supplier, pricing, barcode, and initial stock.</p>
    </div>
    <div class="page-header-actions">
        <a class="btn btn-outline-secondary btn-sm" href="<?= e(base_url('products')) ?>">← Back to Inventory</a>
    </div>
</div>

<div class="card">
    <div class="card-header">📦 Product Details</div>
    <div class="card-body">
        <form method="POST" action="<?= e(base_url($action)) ?>">
            <?= csrf_field() ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required value="<?= e($product['name'] ?? (string) old('name')) ?>" placeholder="e.g. Heavy Duty Brake Pad">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Barcode</label>
                            <input type="text" class="form-control" name="barcode" value="<?= e($product['barcode'] ?? (string) old('barcode')) ?>" placeholder="Optional">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category_id" required>
                                <option value="">Select category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= (int) $category['id'] ?>" <?= ((string)($product['category_id'] ?? old('category_id')) === (string)$category['id']) ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Supplier</label>
                            <select class="form-select" name="supplier_id">
                                <option value="">Select supplier</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?= (int) $supplier['id'] ?>" <?= ((string)($product['supplier_id'] ?? old('supplier_id')) === (string)$supplier['id']) ? 'selected' : '' ?>><?= e($supplier['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="4" placeholder="Enter product details, fitment, notes, or brand information"><?= e($product['description'] ?? (string) old('description')) ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card-soft">
                        <div class="section-title">💰 Pricing &amp; Stock</div>
                        <div class="mb-3">
                            <label class="form-label">Buying Price</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" step="0.01" min="0" class="form-control" name="buying_price" value="<?= e((string)($product['buying_price'] ?? old('buying_price', '0.00'))) ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Selling Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" step="0.01" min="0" class="form-control" name="selling_price" required value="<?= e((string)($product['selling_price'] ?? old('selling_price', '0.00'))) ?>">
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control" name="stock_quantity" required value="<?= e((string)($product['stock_quantity'] ?? old('stock_quantity', '0'))) ?>">
                            <div class="form-hint">Starting quantity available for sale.</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 d-flex gap-2 pt-2">
                    <button class="btn btn-primary" type="submit"><?= e($buttonText) ?></button>
                    <a class="btn btn-outline-secondary" href="<?= e(base_url('products')) ?>">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
