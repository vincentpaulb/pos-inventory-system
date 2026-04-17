<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/OrganizationSetting.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/ActivityLog.php';

class SetupController
{
    private OrganizationSetting $organizationSettings;
    private User $users;
    private ActivityLog $logs;

    public function __construct()
    {
        $this->organizationSettings = new OrganizationSetting();
        $this->users = new User();
        $this->logs = new ActivityLog();
    }

    private function organizationFormData(): array
    {
        $organization = organization_info();

        return [
            'company_name' => old('company_name', $organization['has_record'] ? $organization['company_name'] : ''),
            'company_address' => old('company_address', $organization['company_address']),
            'company_contact' => old('company_contact', $organization['company_contact']),
            'company_email' => old('company_email', $organization['company_email']),
            'vat_rate_percent' => old('vat_rate_percent', number_format((float) $organization['vat_rate'] * 100, 2, '.', '')),
            'low_stock_threshold' => old('low_stock_threshold', (string) $organization['low_stock_threshold']),
            'owner_name' => old('owner_name', $organization['owner_name']),
            'owner_address' => old('owner_address', $organization['owner_address']),
            'owner_contact' => old('owner_contact', $organization['owner_contact']),
            'owner_email' => old('owner_email', $organization['owner_email']),
        ];
    }

    private function organizationPayload(): array
    {
        return [
            'company_name' => clean_input($_POST['company_name'] ?? ''),
            'company_address' => clean_input($_POST['company_address'] ?? ''),
            'company_contact' => clean_input($_POST['company_contact'] ?? ''),
            'company_email' => clean_input($_POST['company_email'] ?? ''),
            'vat_rate_percent' => clean_input($_POST['vat_rate_percent'] ?? ''),
            'low_stock_threshold' => clean_input($_POST['low_stock_threshold'] ?? ''),
            'owner_name' => clean_input($_POST['owner_name'] ?? ''),
            'owner_address' => clean_input($_POST['owner_address'] ?? ''),
            'owner_contact' => clean_input($_POST['owner_contact'] ?? ''),
            'owner_email' => clean_input($_POST['owner_email'] ?? ''),
        ];
    }

    private function parseVatRate(string $value): float
    {
        if ($value === '') {
            return DEFAULT_VAT_RATE;
        }

        if (!is_numeric($value)) {
            throw new RuntimeException('VAT rate must be a valid number.');
        }

        $percent = (float) $value;
        if ($percent < 0 || $percent > 100) {
            throw new RuntimeException('VAT rate must be between 0 and 100 percent.');
        }

        return round($percent / 100, 4);
    }

    private function parseLowStockThreshold(string $value): int
    {
        if ($value === '') {
            return DEFAULT_LOW_STOCK_THRESHOLD;
        }

        if (!preg_match('/^\d+$/', $value)) {
            throw new RuntimeException('Low stock threshold must be a whole number.');
        }

        $threshold = (int) $value;
        if ($threshold > 9999) {
            throw new RuntimeException('Low stock threshold is too large.');
        }

        return $threshold;
    }

    private function validateOptionalEmail(string $value, string $label): ?string
    {
        if ($value === '') {
            return null;
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return $label . ' must be a valid email address.';
        }

        return null;
    }

    private function uploadImage(string $field, string $prefix, string $currentPath = ''): string
    {
        if (!isset($_FILES[$field]) || !is_array($_FILES[$field]) || (int) ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $currentPath;
        }

        $file = $_FILES[$field];
        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($error !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Unable to upload ' . str_replace('_', ' ', $field) . '.');
        }

        $tmpName = (string) ($file['tmp_name'] ?? '');
        $size = (int) ($file['size'] ?? 0);

        if ($size <= 0 || $size > 4 * 1024 * 1024) {
            throw new RuntimeException(ucfirst(str_replace('_', ' ', $field)) . ' must be 4MB or smaller.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmpName) ?: '';
        $allowed = [
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'image/webp' => 'webp',
        ];

        if (!isset($allowed[$mime])) {
            throw new RuntimeException(ucfirst(str_replace('_', ' ', $field)) . ' must be a PNG, JPG, or WEBP image.');
        }

        $uploadDir = BASE_PATH . '/public/uploads/organization';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            throw new RuntimeException('Unable to prepare the upload directory.');
        }

        $filename = $prefix . '_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
        $targetPath = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($tmpName, $targetPath)) {
            throw new RuntimeException('Unable to save ' . str_replace('_', ' ', $field) . '.');
        }

        if ($currentPath !== '') {
            $existing = BASE_PATH . '/' . ltrim($currentPath, '/');
            if (is_file($existing)) {
                @unlink($existing);
            }
        }

        return 'public/uploads/organization/' . $filename;
    }

    public function index(): void
    {
        require_auth();

        $step = organization_setup_step();
        if ($step === 'complete') {
            redirect('dashboard');
        }

        redirect('setup/' . $step);
    }

    public function organizationForm(): void
    {
        require_auth();

        if (organization_is_setup_complete()) {
            redirect('dashboard');
        }

        view('setup/organization', [
            'title' => 'Setup Organization',
            'organization' => $this->organizationFormData(),
            'currentOrganization' => organization_info(),
        ]);
    }

    public function saveOrganization(): void
    {
        require_auth();
        verify_csrf();

        $payload = $this->organizationPayload();
        set_old($payload);

        $errors = validate_required(
            [
                'company_name' => $payload['company_name'],
                'company_address' => $payload['company_address'],
                'owner_name' => $payload['owner_name'],
            ],
            [
                'company_name' => 'Organization/Company Name',
                'company_address' => 'Address',
                'owner_name' => "Owner's Name",
            ]
        );

        foreach ([
            $this->validateOptionalEmail($payload['company_email'], 'Company email'),
            $this->validateOptionalEmail($payload['owner_email'], 'Owner email'),
        ] as $error) {
            if ($error !== null) {
                flash('error', $error);
                redirect('setup/organization');
                return;
            }
        }

        if ($errors !== []) {
            flash('error', implode(' ', array_values($errors)));
            redirect('setup/organization');
            return;
        }

        try {
            $payload['vat_rate'] = $this->parseVatRate($payload['vat_rate_percent']);
            $payload['low_stock_threshold'] = $this->parseLowStockThreshold($payload['low_stock_threshold']);
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
            redirect('setup/organization');
            return;
        }

        $existing = $this->organizationSettings->get();

        try {
            $payload['logo_path'] = $this->uploadImage('logo', 'logo', (string) ($existing['logo_path'] ?? ''));
            $payload['header_path'] = $this->uploadImage('header', 'header', (string) ($existing['header_path'] ?? ''));
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
            redirect('setup/organization');
            return;
        }

        if (!$this->organizationSettings->save($payload)) {
            flash('error', 'Unable to save organization information right now.');
            redirect('setup/organization');
            return;
        }

        organization_info(true);
        clear_old();

        $this->logs->log((int) auth_user()['id'], 'setup_organization', 'Saved organization information.');

        flash('success', 'Organization information saved. Continue with the admin account setup.');
        redirect('setup/admin');
    }

    public function adminForm(): void
    {
        require_auth();

        if (organization_setup_step() === 'organization') {
            redirect('setup/organization');
        }

        if (organization_is_setup_complete()) {
            redirect('dashboard');
        }

        $admin = $this->users->firstAdmin();

        view('setup/admin', [
            'title' => 'Setup Admin Account',
            'admin' => $admin,
        ]);
    }

    public function saveAdmin(): void
    {
        require_auth();
        verify_csrf();

        if (organization_setup_step() === 'organization') {
            redirect('setup/organization');
        }

        $admin = $this->users->firstAdmin();
        if ($admin === null) {
            flash('error', 'No admin account was found for setup.');
            redirect('login');
            return;
        }

        $payload = [
            'first_name' => clean_input($_POST['first_name'] ?? ''),
            'middle_initial' => clean_input($_POST['middle_initial'] ?? ''),
            'last_name' => clean_input($_POST['last_name'] ?? ''),
            'contact' => clean_input($_POST['contact'] ?? ''),
            'username' => clean_input($_POST['username'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
        ];

        set_old([
            'first_name' => $payload['first_name'],
            'middle_initial' => $payload['middle_initial'],
            'last_name' => $payload['last_name'],
            'contact' => $payload['contact'],
            'username' => $payload['username'],
        ]);

        $errors = validate_required(
            [
                'first_name' => $payload['first_name'],
                'last_name' => $payload['last_name'],
                'username' => $payload['username'],
                'password' => $payload['password'],
                'confirm_password' => $payload['confirm_password'],
            ],
            [
                'first_name' => 'First Name',
                'last_name' => 'Last Name',
                'username' => 'Username',
                'password' => 'Password',
                'confirm_password' => 'Confirm Password',
            ]
        );

        if ($errors !== []) {
            flash('error', implode(' ', array_values($errors)));
            redirect('setup/admin');
            return;
        }

        if ($payload['password'] !== $payload['confirm_password']) {
            flash('error', 'Password and confirmation do not match.');
            redirect('setup/admin');
            return;
        }

        if ($this->users->usernameExists($payload['username'], (int) $admin['id'])) {
            flash('error', 'Username already exists.');
            redirect('setup/admin');
            return;
        }

        if (!$this->users->setupAdmin((int) $admin['id'], $payload)) {
            flash('error', 'Unable to finalize the admin account setup.');
            redirect('setup/admin');
            return;
        }

        $this->organizationSettings->markSetupComplete();
        organization_info(true);

        $updatedAdmin = $this->users->find((int) $admin['id']);
        if ($updatedAdmin !== null) {
            login_user($updatedAdmin);
        }

        clear_old();
        $this->logs->log((int) $admin['id'], 'setup_admin', 'Completed first-time admin account setup.');

        flash('success', 'Setup completed successfully.');
        redirect('dashboard');
    }

    public function manage(): void
    {
        require_role('Admin');

        view('organization/index', [
            'title' => 'Organization Info',
            'organization' => $this->organizationFormData(),
            'currentOrganization' => organization_info(),
        ]);
    }

    public function updateOrganization(): void
    {
        require_role('Admin');
        verify_csrf();

        $payload = $this->organizationPayload();
        set_old($payload);

        $errors = validate_required(
            [
                'company_name' => $payload['company_name'],
                'company_address' => $payload['company_address'],
                'owner_name' => $payload['owner_name'],
            ],
            [
                'company_name' => 'Organization/Company Name',
                'company_address' => 'Address',
                'owner_name' => "Owner's Name",
            ]
        );

        foreach ([
            $this->validateOptionalEmail($payload['company_email'], 'Company email'),
            $this->validateOptionalEmail($payload['owner_email'], 'Owner email'),
        ] as $error) {
            if ($error !== null) {
                flash('error', $error);
                redirect('organization');
                return;
            }
        }

        if ($errors !== []) {
            flash('error', implode(' ', array_values($errors)));
            redirect('organization');
            return;
        }

        try {
            $payload['vat_rate'] = $this->parseVatRate($payload['vat_rate_percent']);
            $payload['low_stock_threshold'] = $this->parseLowStockThreshold($payload['low_stock_threshold']);
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
            redirect('organization');
            return;
        }

        $existing = $this->organizationSettings->get();

        try {
            $payload['logo_path'] = $this->uploadImage('logo', 'logo', (string) ($existing['logo_path'] ?? ''));
            $payload['header_path'] = $this->uploadImage('header', 'header', (string) ($existing['header_path'] ?? ''));
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
            redirect('organization');
            return;
        }

        if (!$this->organizationSettings->save($payload)) {
            flash('error', 'Unable to update organization information right now.');
            redirect('organization');
            return;
        }

        organization_info(true);
        clear_old();

        $this->logs->log((int) auth_user()['id'], 'organization_update', 'Updated organization information.');
        flash('success', 'Organization information updated successfully.');
        redirect('organization');
    }
}
