<div class="login-grid">
    <!-- Left panel -->
    <section class="login-panel">
        <div class="login-panel-content">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="brand-mark">RB</div>
                <div>
                    <div style="font-size:.82rem;font-weight:700;color:#fff;line-height:1.3">R'B Heavy Equipment Parts Trading</div>
                    <div style="font-size:.68rem;color:rgba(255,255,255,.35)">Inventory &amp; POS System</div>
                </div>
            </div>

            <div class="login-badge">Business Platform</div>

            <h1 class="login-panel-title">Professional control for parts inventory &amp; retail sales.</h1>
            <p class="login-panel-desc">Track products, suppliers, stock movements, cashier transactions, and sales reports from one secure web-based system.</p>

            <div class="login-kpis">
                <div class="login-kpi">
                    <div class="login-kpi-label">Inventory</div>
                    <div class="login-kpi-value">Live Stock</div>
                </div>
                <div class="login-kpi">
                    <div class="login-kpi-label">Point of Sale</div>
                    <div class="login-kpi-value">Fast Checkout</div>
                </div>
                <div class="login-kpi">
                    <div class="login-kpi-label">Analytics</div>
                    <div class="login-kpi-value">Sales Insights</div>
                </div>
                <div class="login-kpi">
                    <div class="login-kpi-label">Security</div>
                    <div class="login-kpi-value">Role Access</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Right form -->
    <section>
        <div class="login-card-wrap">
            <div class="login-tag">Secure Login</div>
            <h2 class="login-title">Welcome back</h2>
            <p class="login-subtitle">Sign in to access the admin and cashier workspace.</p>

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
                <button class="btn btn-primary w-100 btn-lg" type="submit">Sign In to Dashboard</button>
            </form>

            <div class="card-soft mt-4">
                <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:8px">Default Credentials</div>
                <div class="d-flex gap-4">
                    <div class="small-muted">Username: <strong style="color:var(--text)">admin</strong></div>
                    <div class="small-muted">Password: <strong style="color:var(--text)">admin123</strong></div>
                </div>
            </div>
        </div>
    </section>
</div>
