<?php
$profileName = $user['name'] ?? '';
$profileFirstName = (string) ($user['first_name'] ?? '');
$profileMiddleInitial = (string) ($user['middle_initial'] ?? '');
$profileLastName = (string) ($user['last_name'] ?? '');
$profileContact = (string) ($user['contact'] ?? '');
$profileUsername = $user['username'] ?? '';
$profileRole = $user['role'] ?? '';
$profileCreatedAt = $user['created_at'] ?? null;
$canEditProfile = (bool) ($canEditProfile ?? false);
?>

<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">My Profile</h1>
        <p class="page-header-desc">
            <?= e($canEditProfile ? 'Update your account information and keep your login credentials current.' : 'View your current account information. Profile updates are limited to Admin and Sales Manager accounts.') ?>
        </p>
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
                <div class="small-muted">
                    <?= e($canEditProfile ? 'Full name is generated automatically from first name, middle initial, and last name. Leave password fields blank if you only want to update account details.' : 'Only Admin and Sales Manager accounts can edit profile details.') ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-user-pen"></i> <?= e($canEditProfile ? 'Edit Profile' : 'Profile Details') ?></span>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= e(base_url('profile')) ?>">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input class="form-control" value="<?= e($profileName) ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input class="form-control" name="username" value="<?= e($profileUsername) ?>" required <?= $canEditProfile ? '' : 'readonly disabled' ?>>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                        <input class="form-control" name="first_name" value="<?= e($profileFirstName) ?>" required <?= $canEditProfile ? '' : 'readonly disabled' ?>>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Middle Initial</label>
                        <input class="form-control" name="middle_initial" maxlength="1" value="<?= e($profileMiddleInitial) ?>" <?= $canEditProfile ? '' : 'readonly disabled' ?>>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input class="form-control" name="last_name" value="<?= e($profileLastName) ?>" required <?= $canEditProfile ? '' : 'readonly disabled' ?>>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Contact</label>
                        <input class="form-control" name="contact" value="<?= e($profileContact) ?>" <?= $canEditProfile ? '' : 'readonly disabled' ?>>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-control" name="new_password" placeholder="Enter a new password" <?= $canEditProfile ? '' : 'disabled' ?>>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" name="confirm_password" placeholder="Re-enter the new password" <?= $canEditProfile ? '' : 'disabled' ?>>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 flex-wrap">
                    <a class="btn btn-outline-secondary" href="<?= e(base_url(authorized_home_route())) ?>">
                        <i class="fas fa-arrow-left"></i> Back to Workspace
                    </a>
                    <?php if ($canEditProfile): ?>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-floppy-disk"></i> Save Changes
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>
