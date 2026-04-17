<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/Category.php';
require_once BASE_PATH . '/models/ActivityLog.php';

class CategoryController
{
    private Category $categories;
    private ActivityLog $logs;

    public function __construct()
    {
        $this->categories = new Category();
        $this->logs = new ActivityLog();
    }

    public function index(): void
    {
        require_auth();

        $search = clean_input($_GET['search'] ?? '');
        $categories = $this->categories->all($search);

        if (is_ajax_request()) {
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            echo json_encode($categories, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        view('categories/index', [
            'title' => 'Categories',
            'search' => $search,
            'categories' => $categories,
        ]);
    }

    public function store(): void
    {
        require_auth();
        verify_csrf();
        $name = clean_input($_POST['name'] ?? '');

        if ($name === '') {
            flash('error', 'Category name is required.');
            redirect('categories');
        }

        $this->categories->create($name);
        $this->logs->log((int) auth_user()['id'], 'category_create', 'Added category: ' . $name);
        flash('success', 'Category added successfully.');
        redirect('categories');
    }

    public function update(): void
    {
        require_auth();
        verify_csrf();
        $id = (int) ($_POST['id'] ?? 0);
        $name = clean_input($_POST['name'] ?? '');

        $this->categories->update($id, $name);
        $this->logs->log((int) auth_user()['id'], 'category_update', 'Updated category ID: ' . $id);
        flash('success', 'Category updated successfully.');
        redirect('categories');
    }

    public function delete(): void
    {
        require_role('Admin');
        verify_csrf();
        $id = (int) ($_POST['id'] ?? 0);

        $this->categories->delete($id);
        $this->logs->log((int) auth_user()['id'], 'category_delete', 'Deleted category ID: ' . $id);
        flash('success', 'Category deleted successfully.');
        redirect('categories');
    }
}
