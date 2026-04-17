<?php
$loginHeaderUrl = organization_header_url();
?>

<div class="login-grid">
    <div class="login-card-header-image">
        <?php if ($loginHeaderUrl): ?>
            <img
                src="<?= e($loginHeaderUrl) ?>"
                alt="<?= e(organization_name()) ?>"
            >
        <?php else: ?>
            <div class="login-brand-fallback"><?= e(organization_name()) ?></div>
        <?php endif; ?>
    </div>
    <section>
        <div class="login-card-wrap">
            <h2 class="login-title">Welcome back</h2>

            <form method="POST" action="<?= e(base_url('login')) ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" value="<?= e((string) old('username')) ?>" placeholder="Enter your username" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Enter your password" required>
                </div>
                <button class="btn btn-primary w-100 btn-lg" type="submit">Sign In</button>
            </form>
        </div>
    </section>
</div>
