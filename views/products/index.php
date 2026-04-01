<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Inventory Management</h1>
        <p class="page-header-desc">Search, review, and manage product pricing, stock levels, supplier assignments, and stock movements.</p>
    </div>
    <div class="page-header-actions">
        <a class="btn btn-primary btn-sm" href="<?= e(base_url('products/create')) ?>">+ Add New Product</a>
    </div>
</div>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-header">🔍 Filter Products</div>
    <div class="card-body">
        <form class="row g-3 align-items-end" method="GET" action="<?= e(base_url('products')) ?>">
            <div class="col-lg-5">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Product name, description, or barcode" value="<?= e($search) ?>">
            </div>
            <div class="col-lg-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int) $category['id'] ?>" <?= $categoryId == $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-4 d-flex gap-2">
                <button class="btn btn-primary" type="submit">Apply Filter</button>
                <a class="btn btn-outline-secondary" href="<?= e(base_url('products')) ?>">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card mb-4">
    <div class="card-header">
        <span>📦 Products</span>
        <span class="badge bg-soft-primary"><?= count($products) ?> items</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Supplier</th>
                    <th>Buying Price</th>
                    <th>Selling Price</th>
                    <th>Stock</th>
                    <th>Barcode</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <div style="font-size:.82rem;font-weight:700"><?= e($product['name']) ?></div>
                            <div class="small-muted mt-1"><?= e($product['description']) ?></div>
                        </td>
                        <td><span class="badge bg-soft-primary"><?= e($product['category_name']) ?></span></td>
                        <td class="small-muted"><?= e($product['supplier_name'] ?: '—') ?></td>
                        <td class="small-muted"><?= e(format_currency($product['buying_price'])) ?></td>
                        <td><strong><?= e(format_currency($product['selling_price'])) ?></strong></td>
                        <td>
                            <span class="badge <?= (int)$product['stock_quantity'] <= LOW_STOCK_THRESHOLD ? 'bg-soft-danger' : 'bg-soft-success' ?>">
                                <?= (int) $product['stock_quantity'] ?> pcs
                            </span>
                        </td>
                        <td class="small-muted"><?= e($product['barcode'] ?: '—') ?></td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                <a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('products/edit?id=' . $product['id'])) ?>">Edit</a>
                                <a class="btn btn-sm btn-outline-secondary" href="<?= e(base_url('products/stock?id=' . $product['id'])) ?>">Stock</a>
                                <?php if (has_role('Admin')): ?>
                                    <form method="POST" action="<?= e(base_url('products/delete')) ?>" onsubmit="return confirm('Delete this product?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$products): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4" style="font-size:.82rem">No products found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Stock Movements -->
<div class="card">
    <div class="card-header">📋 Recent Stock Movements</div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr><th>Product</th><th>Type</th><th>Qty</th><th>Remarks</th><th>User</th><th>Date</th></tr>
            </thead>
            <tbody>
            <?php foreach ($movements as $movement): ?>
                <tr>
                    <td style="font-weight:600"><?= e($movement['product_name']) ?></td>
                    <td>
                        <span class="badge <?= $movement['movement_type'] === 'in' ? 'bg-soft-success' : 'bg-soft-warning' ?>">
                            <?= $movement['movement_type'] === 'in' ? '↑ IN' : '↓ OUT' ?>
                        </span>
                    </td>
                    <td><strong><?= (int) $movement['quantity'] ?></strong></td>
                    <td class="small-muted"><?= e($movement['remarks']) ?></td>
                    <td class="small-muted"><?= e($movement['user_name']) ?></td>
                    <td class="small-muted"><?= e(format_datetime($movement['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$movements): ?>
                <tr><td colspan="6" class="text-center text-muted py-4" style="font-size:.82rem">No stock movements yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
