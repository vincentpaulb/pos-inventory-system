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

    public function profile(): void
    {
        require_auth();

        $currentUser = auth_user();
        $user = $this->users->find((int) $currentUser['id']);

        if ($user === null) {
            logout_user();
            redirect('login');
        }

        view('users/profile', [
            'title' => 'My Profile',
            'user' => $user,
            'canEditProfile' => has_role(['Admin', 'Sales Manager']),
        ]);
    }

    public function store(): void
    {
        require_role('Admin');
        verify_csrf();

        $data = [
            'first_name' => clean_input($_POST['first_name'] ?? ''),
            'middle_initial' => clean_input($_POST['middle_initial'] ?? ''),
            'last_name' => clean_input($_POST['last_name'] ?? ''),
            'contact' => clean_input($_POST['contact'] ?? ''),
            'username' => clean_input($_POST['username'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'role' => clean_input($_POST['role'] ?? 'Cashier'),
        ];

        set_old($data);

        if ($data['first_name'] === '' || $data['last_name'] === '' || $data['username'] === '' || $data['password'] === '') {
            flash('error', 'First name, last name, username, password, and role are required.');
            redirect('users');
        }

        if (!is_valid_role($data['role'])) {
            flash('error', 'Selected role is invalid.');
            redirect('users');
        }

        if ($this->users->usernameExists($data['username'])) {
            flash('error', 'Username already exists.');
            redirect('users');
        }

        $this->users->create($data);
        clear_old();
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
            'first_name' => clean_input($_POST['first_name'] ?? ''),
            'middle_initial' => clean_input($_POST['middle_initial'] ?? ''),
            'last_name' => clean_input($_POST['last_name'] ?? ''),
            'contact' => clean_input($_POST['contact'] ?? ''),
            'username' => clean_input($_POST['username'] ?? ''),
            'role' => clean_input($_POST['role'] ?? 'Cashier'),
        ];

        if ($data['first_name'] === '' || $data['last_name'] === '' || $data['username'] === '') {
            flash('error', 'First name, last name, username, and role are required.');
            redirect('users');
        }

        if (!is_valid_role($data['role'])) {
            flash('error', 'Selected role is invalid.');
            redirect('users');
        }

        if ($this->users->usernameExists($data['username'], $id)) {
            flash('error', 'Username already exists.');
            redirect('users');
        }

        $this->users->update($id, $data);
        $isCurrentUser = (int) auth_user()['id'] === $id;

        if ($isCurrentUser) {
            $updatedUser = $this->users->find($id);

            if ($updatedUser !== null) {
                $_SESSION['user']['name'] = $updatedUser['name'];
                $_SESSION['user']['username'] = $updatedUser['username'];
                $_SESSION['user']['role'] = $updatedUser['role'];
            }
        }

        $this->logs->log((int) auth_user()['id'], 'user_update', 'Updated user ID: ' . $id);

        flash('success', 'User updated successfully.');
        redirect($isCurrentUser && !has_role('Admin') ? authorized_home_route($data['role']) : 'users');
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

    public function updateProfile(): void
    {
        require_role(['Admin', 'Sales Manager']);
        verify_csrf();

        $currentUser = auth_user();
        $id = (int) $currentUser['id'];
        $data = [
            'first_name' => clean_input($_POST['first_name'] ?? ''),
            'middle_initial' => clean_input($_POST['middle_initial'] ?? ''),
            'last_name' => clean_input($_POST['last_name'] ?? ''),
            'contact' => clean_input($_POST['contact'] ?? ''),
            'username' => clean_input($_POST['username'] ?? ''),
        ];
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($data['first_name'] === '' || $data['last_name'] === '' || $data['username'] === '') {
            flash('error', 'First name, last name, and username are required.');
            redirect('profile');
        }

        if ($this->users->usernameExists($data['username'], $id)) {
            flash('error', 'Username already exists.');
            redirect('profile');
        }

        if ($newPassword !== '' || $confirmPassword !== '') {
            if ($newPassword === '' || $confirmPassword === '') {
                flash('error', 'Both password fields are required to change your password.');
                redirect('profile');
            }

            if ($newPassword !== $confirmPassword) {
                flash('error', 'New password and confirmation do not match.');
                redirect('profile');
            }

            $this->users->resetPassword($id, $newPassword);
            $this->logs->log($id, 'profile_password_update', 'Updated own account password.');
        }

        $this->users->updateProfile($id, $data);

        $updatedUser = $this->users->find($id);
        if ($updatedUser !== null) {
            $_SESSION['user']['name'] = $updatedUser['name'];
            $_SESSION['user']['username'] = $updatedUser['username'];
        }

        $this->logs->log($id, 'profile_update', 'Updated own account details.');

        flash('success', 'Profile updated successfully.');
        redirect('profile');
    }
}
