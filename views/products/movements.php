<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Stock Movements</h1>
        <p class="page-header-desc">Complete history of all stock-in and stock-out movements across inventory.</p>
    </div>
    <div class="page-header-actions">
        <a href="<?= e(base_url('products')) ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Inventory
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header"><i class="fas fa-filter"></i> Filter</div>
    <div class="card-body">
        <form class="row g-3 align-items-end" method="GET" action="<?= e(base_url('products/movements')) ?>">
            <div class="col-md-2">
                <label class="form-label">From</label>
                <input type="date" class="form-control" name="from" value="<?= e($from) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">To</label>
                <input type="date" class="form-control" name="to" value="<?= e($to) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Type</label>
                <select class="form-select" name="type">
                    <option value="">All</option>
                    <option value="in"  <?= $type === 'in'  ? 'selected' : '' ?>>Stock In</option>
                    <option value="out" <?= $type === 'out' ? 'selected' : '' ?>>Stock Out</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" class="form-control" name="search" value="<?= e($search) ?>"
                       placeholder="Product name, user, or remarks...">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary w-100" type="submit"><i class="fas fa-check"></i> Apply</button>
                <a class="btn btn-outline-secondary" href="<?= e(base_url('products/movements')) ?>"><i class="fas fa-redo"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span><i class="fas fa-arrows-up-down"></i> Movement History</span>
        <span class="badge bg-soft-primary"><?= count($movements) ?> records</span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th style="width:155px">Date &amp; Time</th>
                    <th>Product</th>
                    <th style="width:90px">Type</th>
                    <th style="width:80px">Quantity</th>
                    <th>Remarks</th>
                    <th style="width:140px">Recorded By</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($movements as $row): ?>
                <tr>
                    <td class="small-muted"><?= e(format_datetime($row['created_at'])) ?></td>
                    <td style="font-weight:600;font-size:.82rem"><?= e($row['product_name']) ?></td>
                    <td>
                        <?php if ($row['movement_type'] === 'in'): ?>
                            <span class="badge bg-soft-success"><i class="fas fa-arrow-down"></i> Stock In</span>
                        <?php else: ?>
                            <span class="badge bg-soft-danger"><i class="fas fa-arrow-up"></i> Stock Out</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center fw-bold"><?= (int) $row['quantity'] ?></td>
                    <td class="small-muted"><?= e($row['remarks'] ?: '—') ?></td>
                    <td class="small-muted"><?= e($row['user_name']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$movements): ?>
                <tr><td colspan="6" class="text-center text-muted py-4" style="font-size:.82rem">No stock movements found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
