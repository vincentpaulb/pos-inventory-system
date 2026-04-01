<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Suppliers</h1>
        <p class="page-header-desc">Manage your supplier contacts, phone numbers, and addresses.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">➕ Add Supplier</div>
            <div class="card-body">
                <form method="POST" action="<?= e(base_url('suppliers/store')) ?>">
                    <?= csrf_field() ?>
                    <div class="mb-2">
                        <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                        <input class="form-control" name="name" placeholder="Company or business name" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Contact Person</label>
                        <input class="form-control" name="contact_person" placeholder="Full name">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Phone</label>
                        <input class="form-control" name="phone" placeholder="+63 ...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="3" placeholder="Business address"></textarea>
                    </div>
                    <button class="btn btn-primary w-100">Save Supplier</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <span>🏭 Supplier List</span>
                <span class="badge bg-soft-primary"><?= count($suppliers) ?> suppliers</span>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr><th>Name</th><th>Contact</th><th>Phone</th><th>Address</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($suppliers as $supplier): ?>
                        <tr>
                            <td style="font-weight:700;font-size:.82rem"><?= e($supplier['name']) ?></td>
                            <td class="small-muted"><?= e($supplier['contact_person']) ?></td>
                            <td class="small-muted"><?= e($supplier['phone']) ?></td>
                            <td class="small-muted"><?= e($supplier['address']) ?></td>
                            <td>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#sup<?= (int) $supplier['id'] ?>">Edit</button>
                                    <?php if (has_role('Admin')): ?>
                                    <form method="POST" action="<?= e(base_url('suppliers/delete')) ?>" onsubmit="return confirm('Delete this supplier?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $supplier['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <tr class="collapse" id="sup<?= (int) $supplier['id'] ?>">
                            <td colspan="5" style="background:var(--surface-2);padding:14px 18px">
                                <form method="POST" action="<?= e(base_url('suppliers/update')) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $supplier['id'] ?>">
                                    <div class="row g-2">
                                        <div class="col-md-3"><input class="form-control" name="name" value="<?= e($supplier['name']) ?>" placeholder="Name" required></div>
                                        <div class="col-md-3"><input class="form-control" name="contact_person" value="<?= e($supplier['contact_person']) ?>" placeholder="Contact person"></div>
                                        <div class="col-md-2"><input class="form-control" name="phone" value="<?= e($supplier['phone']) ?>" placeholder="Phone"></div>
                                        <div class="col-md-3"><input class="form-control" name="address" value="<?= e($supplier['address']) ?>" placeholder="Address"></div>
                                        <div class="col-md-1"><button class="btn btn-primary w-100">Save</button></div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$suppliers): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4" style="font-size:.82rem">No suppliers available.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
