<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? organization_name()) ?> - <?= e(organization_name()) ?></title>
    <link rel="stylesheet" href="<?= e(base_url('public/vendor/bootstrap/css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_url('public/vendor/fontawesome-free-7.2.0-web/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_url('public/css/app.css')) ?>">
</head>
<body>
<div class="login-shell">
    <div class="login-wrapper">
    <?php if ($message = flash('success')): ?>
        <div class="alert alert-success mb-4"><i class="fas fa-check-circle"></i> <?= e($message) ?></div>
    <?php endif; ?>
    <?php if ($message = flash('error')): ?>
        <div class="alert alert-danger mb-4"><i class="fas fa-exclamation-circle"></i> <?= e($message) ?></div>
    <?php endif; ?>
        <?php require $contentView; ?>
    </div>
</div>
<script src="<?= e(base_url('public/vendor/bootstrap/js/bootstrap.bundle.min.js')) ?>"></script>
</body>
</html>
