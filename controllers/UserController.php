<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/ActivityLog.php';

class UserController
{
    private User $users;
    private ActivityLog $logs;

    public function __construct()
    {
        $this->users = new User();
        $this->logs = new ActivityLog();
    }

    public function index(): void
    {
        require_role('Admin');
        view('users/index', [
            'title' => 'Users',
            'users' => $this->users->all(),
        ]);
    }

    public function store(): void
    {
        require_role('Admin');
        verify_csrf();

        $data = [
            'name' => clean_input($_POST['name'] ?? ''),
            'username' => clean_input($_POST['username'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'role' => clean_input($_POST['role'] ?? 'Cashier'),
        ];

        if ($data['name'] === '' || $data['username'] === '' || $data['password'] === '') {
            flash('error', 'Name, username, password, and role are required.');
            redirect('users');
        }

        if ($this->users->usernameExists($data['username'])) {
            flash('error', 'Username already exists.');
            redirect('users');
        }

        $this->users->create($data);
        $this->logs->log((int) auth_user()['id'], 'user_create', 'Created user: ' . $data['username']);

        flash('success', 'User created successfully.');
        redirect('users');
    }

    public function update(): void
    {
        require_role('Admin');
        verify_csrf();

        $id = (int) ($_POST['id'] ?? 0);
        $data = [
            'name' => clean_input($_POST['name'] ?? ''),
            'username' => clean_input($_POST['username'] ?? ''),
            'role' => clean_input($_POST['role'] ?? 'Cashier'),
        ];

        if ($this->users->usernameExists($data['username'], $id)) {
            flash('error', 'Username already exists.');
            redirect('users');
        }

        $this->users->update($id, $data);
        $this->logs->log((int) auth_user()['id'], 'user_update', 'Updated user ID: ' . $id);

        flash('success', 'User updated successfully.');
        redirect('users');
    }

    public function resetPassword(): void
    {
        require_role('Admin');
        verify_csrf();

        $id = (int) ($_POST['id'] ?? 0);
        $password = $_POST['new_password'] ?? '';

        if ($password === '') {
            flash('error', 'New password is required.');
            redirect('users');
        }

        $this->users->resetPassword($id, $password);
        $this->logs->log((int) auth_user()['id'], 'user_password_reset', 'Reset password for user ID: ' . $id);

        flash('success', 'Password reset successfully.');
        redirect('users');
    }

    public function delete(): void
    {
        require_role('Admin');
        verify_csrf();

        $id = (int) ($_POST['id'] ?? 0);
        if ((int) auth_user()['id'] === $id) {
            flash('error', 'You cannot delete your own account.');
            redirect('users');
        }

        $this->users->delete($id);
        $this->logs->log((int) auth_user()['id'], 'user_delete', 'Deleted user ID: ' . $id);

        flash('success', 'User deleted successfully.');
        redirect('users');
    }
}
