<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">User Management</h1>
        <p class="page-header-desc">Create, update, and manage administrator, supply, sales, and cashier accounts with role-based access.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="fas fa-user-plus"></i> Create User</div>
            <div class="card-body">
                <form method="POST" action="<?= e(base_url('users/store')) ?>">
                    <?= csrf_field() ?>
                    <div class="row g-2">
                        <div class="col-md-5">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input class="form-control" name="first_name" value="<?= e((string) old('first_name')) ?>" placeholder="First name" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Middle Initial</label>
                            <input class="form-control" name="middle_initial" maxlength="1" value="<?= e((string) old('middle_initial')) ?>" placeholder="M">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input class="form-control" name="last_name" value="<?= e((string) old('last_name')) ?>" placeholder="Last name" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Contact</label>
                        <input class="form-control" name="contact" value="<?= e((string) old('contact')) ?>" placeholder="Optional contact number">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input class="form-control" name="username" value="<?= e((string) old('username')) ?>" placeholder="Unique username" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" placeholder="Set password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <?php $selectedRole = (string) old('role', 'Admin'); ?>
                            <?php foreach (available_roles() as $role): ?>
                                <option value="<?= e($role) ?>" <?= $selectedRole === $role ? 'selected' : '' ?>><?= e($role) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100"><i class="fas fa-save"></i> Create User</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <span><i class="fas fa-users"></i> User Accounts</span>
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
                                <span class="badge <?= e(role_badge_class($user['role'])) ?>">
                                    <?= e($user['role']) ?>
                                </span>
                            </td>
                            <td class="small-muted"><?= e(format_date($user['created_at'])) ?></td>
                            <td>
                                <div class="action-group">
                                    <button class="btn btn-sm btn-outline-success btn-icon" data-bs-toggle="collapse" data-bs-target="#user<?= (int) $user['id'] ?>" title="Edit user" aria-label="Edit user"><i class="fas fa-pen"></i></button>
                                    <button class="btn btn-sm btn-outline-warning btn-icon" data-bs-toggle="collapse" data-bs-target="#pass<?= (int) $user['id'] ?>" title="Reset password" aria-label="Reset password"><i class="fas fa-key"></i></button>
                                    <form method="POST" action="<?= e(base_url('users/delete')) ?>" class="js-confirm-form" data-confirm-message="Delete this user?" data-confirm-button="Delete">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete user" aria-label="Delete user"><i class="fas fa-trash-alt"></i></button>
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
                                        <div class="col-md-3"><input class="form-control" name="first_name" value="<?= e((string) ($user['first_name'] ?? '')) ?>" placeholder="First name" required></div>
                                        <div class="col-md-1"><input class="form-control" name="middle_initial" maxlength="1" value="<?= e((string) ($user['middle_initial'] ?? '')) ?>" placeholder="M"></div>
                                        <div class="col-md-3"><input class="form-control" name="last_name" value="<?= e((string) ($user['last_name'] ?? '')) ?>" placeholder="Last name" required></div>
                                        <div class="col-md-2"><input class="form-control" name="contact" value="<?= e((string) ($user['contact'] ?? '')) ?>" placeholder="Contact"></div>
                                        <div class="col-md-2"><input class="form-control" name="username" value="<?= e($user['username']) ?>" placeholder="Username" required></div>
                                        <div class="col-md-1">
                                            <select class="form-select" name="role">
                                                <?php foreach (available_roles() as $role): ?>
                                                    <option value="<?= e($role) ?>" <?= $user['role'] === $role ? 'selected' : '' ?>><?= e($role) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-12"><button class="btn btn-primary">Save</button></div>
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
