<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/ActivityLog.php';

class AuthController
{
    private User $users;
    private ActivityLog $logs;

    public function __construct()
    {
        $this->users = new User();
        $this->logs = new ActivityLog();
    }

    public function showLogin(): void
    {
        if (is_logged_in()) {
            redirect(organization_is_setup_complete() ? authorized_home_route() : 'setup');
        }
        view('auth/login', ['title' => 'Login'], 'guest');
    }

    public function login(): void
    {
        verify_csrf();

        $username = clean_input($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        set_old(['username' => $username]);

        if (login_rate_limited($username)) {
            flash('error', 'Too many login attempts. Please wait 15 minutes and try again.');
            redirect('login');
        }

        if ($username === '' || $password === '') {
            flash('error', 'Username and password are required.');
            redirect('login');
        }

        $user = $this->users->findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            record_login_attempt($username, false);
            flash('error', 'Invalid login credentials.');
            redirect('login');
        }

        record_login_attempt($username, true);
        login_user($user);
        clear_old();

        $this->logs->log((int) $user['id'], 'login', 'User logged in.');
        flash('success', 'Welcome back, ' . $user['name'] . '!');
        redirect(organization_is_setup_complete() ? authorized_home_route($user['role']) : 'setup');
    }

    public function logout(): void
    {
        if (is_logged_in()) {
            $currentUser = auth_user();
            $existingUser = $currentUser ? $this->users->find((int) $currentUser['id']) : null;
            $this->logs->log($existingUser ? (int) $existingUser['id'] : null, 'logout', 'User logged out.');
        }
        logout_user();
        redirect('login');
    }
}
