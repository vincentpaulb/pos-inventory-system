<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="<?= e(base_url('public/css/app.css')) ?>">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .error-wrap { text-align: center; padding: 40px 24px; max-width: 420px; }
        .error-code { font-size: 6rem; font-weight: 900; letter-spacing: -.06em; color: var(--primary); line-height: 1; margin-bottom: 8px; }
        .error-title { font-size: 1.35rem; font-weight: 800; margin-bottom: 10px; }
        .error-desc { color: var(--muted); font-size: .85rem; margin-bottom: 28px; }
    </style>
</head>
<body>
    <div class="error-wrap">
        <div class="error-code">404</div>
        <div class="error-title">Page Not Found</div>
        <p class="error-desc">The page you're looking for doesn't exist or has been moved.</p>
        <a href="<?= e(base_url('dashboard')) ?>" class="btn btn-primary">← Back to Dashboard</a>
    </div>
</body>
</html>
