<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">User Management</h1>
        <p class="page-header-desc">Create, update, and manage admin and cashier accounts with role-based access.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">👤 Create User</div>
            <div class="card-body">
                <form method="POST" action="<?= e(base_url('users/store')) ?>">
                    <?= csrf_field() ?>
                    <div class="mb-2">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input class="form-control" name="name" placeholder="Full name" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input class="form-control" name="username" placeholder="Unique username" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" placeholder="Set password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="Admin">Admin</option>
                            <option value="Cashier">Cashier</option>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100">Create User</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <span>👥 User Accounts</span>
                <span class="badge bg-soft-primary"><?= count($users) ?> users</span>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr><th>Name</th><th>Username</th><th>Role</th><th>Created</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td style="font-weight:700;font-size:.82rem"><?= e($user['name']) ?></td>
                            <td class="small-muted"><?= e($user['username']) ?></td>
                            <td>
                                <span class="badge <?= $user['role'] === 'Admin' ? 'bg-soft-primary' : 'bg-soft-success' ?>">
                                    <?= e($user['role']) ?>
                                </span>
                            </td>
                            <td class="small-muted"><?= e(format_date($user['created_at'])) ?></td>
                            <td>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#user<?= (int) $user['id'] ?>">Edit</button>
                                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="collapse" data-bs-target="#pass<?= (int) $user['id'] ?>">Password</button>
                                    <form method="POST" action="<?= e(base_url('users/delete')) ?>" onsubmit="return confirm('Delete this user?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr class="collapse" id="user<?= (int) $user['id'] ?>">
                            <td colspan="5" style="background:var(--surface-2);padding:14px 18px">
                                <form method="POST" action="<?= e(base_url('users/update')) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                    <div class="row g-2">
                                        <div class="col-md-4"><input class="form-control" name="name" value="<?= e($user['name']) ?>" placeholder="Full name" required></div>
                                        <div class="col-md-3"><input class="form-control" name="username" value="<?= e($user['username']) ?>" placeholder="Username" required></div>
                                        <div class="col-md-3">
                                            <select class="form-select" name="role">
                                                <option <?= $user['role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                                                <option <?= $user['role'] === 'Cashier' ? 'selected' : '' ?>>Cashier</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2"><button class="btn btn-primary w-100">Save</button></div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        <tr class="collapse" id="pass<?= (int) $user['id'] ?>">
                            <td colspan="5" style="background:var(--surface-2);padding:14px 18px">
                                <form method="POST" action="<?= e(base_url('users/reset-password')) ?>" class="d-flex gap-2">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                    <input type="password" class="form-control" name="new_password" placeholder="New password" required>
                                    <button class="btn btn-warning" style="white-space:nowrap">Reset Password</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$users): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4" style="font-size:.82rem">No users found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
