<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/Supplier.php';
require_once BASE_PATH . '/models/ActivityLog.php';

class SupplierController
{
    private Supplier $suppliers;
    private ActivityLog $logs;

    public function __construct()
    {
        $this->suppliers = new Supplier();
        $this->logs = new ActivityLog();
    }

    public function index(): void
    {
        require_auth();
        view('suppliers/index', [
            'title' => 'Suppliers',
            'suppliers' => $this->suppliers->all(),
        ]);
    }

    public function store(): void
    {
        require_auth();
        verify_csrf();

        $data = [
            'name' => clean_input($_POST['name'] ?? ''),
            'contact_person' => clean_input($_POST['contact_person'] ?? ''),
            'phone' => clean_input($_POST['phone'] ?? ''),
            'address' => clean_input($_POST['address'] ?? ''),
        ];

        if ($data['name'] === '') {
            flash('error', 'Supplier name is required.');
            redirect('suppliers');
        }

        $this->suppliers->create($data);
        $this->logs->log((int) auth_user()['id'], 'supplier_create', 'Added supplier: ' . $data['name']);

        flash('success', 'Supplier added successfully.');
        redirect('suppliers');
    }

    public function update(): void
    {
        require_auth();
        verify_csrf();

        $id = (int) ($_POST['id'] ?? 0);
        $data = [
            'name' => clean_input($_POST['name'] ?? ''),
            'contact_person' => clean_input($_POST['contact_person'] ?? ''),
            'phone' => clean_input($_POST['phone'] ?? ''),
            'address' => clean_input($_POST['address'] ?? ''),
        ];

        $this->suppliers->update($id, $data);
        $this->logs->log((int) auth_user()['id'], 'supplier_update', 'Updated supplier ID: ' . $id);

        flash('success', 'Supplier updated successfully.');
        redirect('suppliers');
    }

    public function delete(): void
    {
        require_role('Admin');
        verify_csrf();

        $id = (int) ($_POST['id'] ?? 0);
        $this->suppliers->delete($id);
        $this->logs->log((int) auth_user()['id'], 'supplier_delete', 'Deleted supplier ID: ' . $id);

        flash('success', 'Supplier deleted successfully.');
        redirect('suppliers');
    }
}
