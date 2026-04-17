<?php
$profileName = $user['name'] ?? '';
$profileUsername = $user['username'] ?? '';
$profileRole = $user['role'] ?? '';
$profileCreatedAt = $user['created_at'] ?? null;
?>

<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">My Profile</h1>
        <p class="page-header-desc">Update your account information and keep your login credentials current.</p>
    </div>
</div>

<div class="profile-grid">
    <div class="profile-summary">
        <div class="card">
            <div class="card-body">
                <div class="profile-avatar"><?= strtoupper(substr($profileName ?: 'U', 0, 1)) ?></div>
                <div style="margin-top:16px">
                    <div style="font-size:1.2rem;font-weight:700;letter-spacing:-.04em"><?= e($profileName) ?></div>
                    <div class="small-muted">@<?= e($profileUsername) ?></div>
                </div>

                <div class="profile-meta" style="margin-top:18px">
                    <div class="profile-meta-item">
                        <div class="profile-meta-label">Role</div>
                        <div class="profile-meta-value"><?= e($profileRole) ?></div>
                    </div>
                    <div class="profile-meta-item">
                        <div class="profile-meta-label">Member Since</div>
                        <div class="profile-meta-value"><?= e($profileCreatedAt ? format_date($profileCreatedAt) : 'N/A') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span><i class="fas fa-shield-halved"></i> Account Notes</span>
            </div>
            <div class="card-body">
                <div class="small-muted">Leave the password fields blank if you only want to update your name or username.</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-user-pen"></i> Edit Profile</span>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= e(base_url('profile')) ?>">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input class="form-control" name="name" value="<?= e($profileName) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input class="form-control" name="username" value="<?= e($profileUsername) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-control" name="new_password" placeholder="Enter a new password">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" name="confirm_password" placeholder="Re-enter the new password">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 flex-wrap">
                    <a class="btn btn-outline-secondary" href="<?= e(base_url('dashboard')) ?>">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-floppy-disk"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
