document.addEventListener('DOMContentLoaded', function () {
    // ── Dark Mode ──────────────────────────────────────────────
    const toggle = document.getElementById('darkModeToggle');
    const icon   = document.getElementById('darkModeIcon');
    const label  = document.getElementById('darkModeLabel');

    function applyDarkMode(enabled) {
        document.body.classList.toggle('dark-mode', enabled);
        if (icon)  icon.textContent  = enabled ? '☀️' : '🌙';
        if (label) label.textContent = enabled ? 'Light Mode' : 'Dark Mode';
        localStorage.setItem('rb_dark_mode', enabled ? '1' : '0');
    }

    // Apply saved preference immediately
    const saved = localStorage.getItem('rb_dark_mode') === '1';
    applyDarkMode(saved);

    if (toggle) {
        toggle.addEventListener('click', function () {
            applyDarkMode(!document.body.classList.contains('dark-mode'));
        });
    }

    // ── Mobile sidebar overlay ─────────────────────────────────
    document.addEventListener('click', function (e) {
        const sidebar = document.getElementById('sidebar');
        const menuBtn = document.querySelector('.mobile-menu-btn');
        if (!sidebar) return;
        if (
            sidebar.classList.contains('show') &&
            !sidebar.contains(e.target) &&
            menuBtn && !menuBtn.contains(e.target)
        ) {
            sidebar.classList.remove('show');
        }
    });

    // ── Auto-dismiss alerts ────────────────────────────────────
    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity .4s ease, max-height .4s ease, margin .4s ease';
            alert.style.opacity    = '0';
            alert.style.maxHeight  = '0';
            alert.style.overflow   = 'hidden';
            alert.style.margin     = '0';
            setTimeout(() => alert.remove(), 420);
        }, 4000);
    });
});
