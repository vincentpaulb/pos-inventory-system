<?php
$seedAdmin = $admin ?? [];
?>

<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Initial Setup</h1>
        <p class="page-header-desc">Step 2 of 2. Replace the default admin credentials and complete the first-run configuration.</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span><i class="fas fa-user-shield"></i> Admin Account Setup</span>
    </div>
    <div class="card-body">
        <div class="card-soft mb-4">
            <div class="small-muted">The seeded admin account will be overwritten after this step.</div>
            <div style="margin-top:8px;font-weight:700">Current default login: <code>admin</code> / <code>admin</code></div>
        </div>

        <form method="POST" action="<?= e(base_url('setup/admin')) ?>" class="row g-3">
            <?= csrf_field() ?>

            <div class="col-md-4">
                <label class="form-label">First Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="first_name" value="<?= e((string) old('first_name', (string) ($seedAdmin['first_name'] ?? ''))) ?>" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Middle Initial</label>
                <input type="text" class="form-control" name="middle_initial" maxlength="1" value="<?= e((string) old('middle_initial', (string) ($seedAdmin['middle_initial'] ?? ''))) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="last_name" value="<?= e((string) old('last_name', (string) ($seedAdmin['last_name'] ?? ''))) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Contact</label>
                <input type="text" class="form-control" name="contact" value="<?= e((string) old('contact', (string) ($seedAdmin['contact'] ?? ''))) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Username <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="username" value="<?= e((string) old('username', (string) ($seedAdmin['username'] ?? 'admin'))) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Role</label>
                <input type="text" class="form-control" value="Admin" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label">Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control" name="confirm_password" required>
            </div>
            <div class="col-12 d-flex justify-content-between flex-wrap gap-2">
                <a class="btn btn-outline-secondary" href="<?= e(base_url('setup/organization')) ?>">
                    <i class="fas fa-arrow-left"></i> Back to Organization Info
                </a>
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-check"></i> Complete Setup
                </button>
            </div>
        </form>
    </div>
</div>
