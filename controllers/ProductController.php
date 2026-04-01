<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/Product.php';
require_once BASE_PATH . '/models/Category.php';
require_once BASE_PATH . '/models/Supplier.php';
require_once BASE_PATH . '/models/ActivityLog.php';

class ProductController
{
    private Product $products;
    private Category $categories;
    private Supplier $suppliers;
    private ActivityLog $logs;

    public function __construct()
    {
        $this->products = new Product();
        $this->categories = new Category();
        $this->suppliers = new Supplier();
        $this->logs = new ActivityLog();
    }

    public function index(): void
    {
        require_auth();
        $search = clean_input($_GET['search'] ?? '');
        $categoryId = clean_input($_GET['category_id'] ?? '');

        view('products/index', [
            'title' => 'Products',
            'products' => $this->products->all($search, $categoryId),
            'categories' => $this->categories->all(),
            'search' => $search,
            'categoryId' => $categoryId,
            'movements' => $this->products->recentMovements(10),
        ]);
    }

    public function create(): void
    {
        require_auth();
        view('products/create', [
            'title' => 'Add Product',
            'categories' => $this->categories->all(),
            'suppliers' => $this->suppliers->all(),
        ]);
    }

    public function store(): void
    {
        require_auth();
        verify_csrf();

        $supplierId = (int) ($_POST['supplier_id'] ?? 0);
        $data = [
            'name' => clean_input($_POST['name'] ?? ''),
            'category_id' => (int) ($_POST['category_id'] ?? 0),
            'description' => clean_input($_POST['description'] ?? ''),
            'buying_price' => sanitize_number($_POST['buying_price'] ?? 0),
            'selling_price' => sanitize_number($_POST['selling_price'] ?? 0),
            'stock_quantity' => (int) ($_POST['stock_quantity'] ?? 0),
            'supplier_id' => $supplierId > 0 ? $supplierId : null,
            'barcode' => clean_input($_POST['barcode'] ?? ''),
        ];

        set_old($_POST);

        $errors = validate_required(
            [
                'name' => $data['name'],
                'category_id' => (string) $data['category_id'],
                'selling_price' => (string) $data['selling_price'],
                'stock_quantity' => (string) $data['stock_quantity'],
            ],
            [
                'name' => 'Product name',
                'category_id' => 'Category',
                'selling_price' => 'Selling price',
                'stock_quantity' => 'Stock quantity',
            ]
        );

        if ($error = validate_positive_number('buying_price', $data['buying_price'], 'Buying price')) {
            $errors['buying_price'] = $error;
        }
        if ($error = validate_positive_number('selling_price', $data['selling_price'], 'Selling price')) {
            $errors['selling_price'] = $error;
        }
        if ($error = validate_integer('stock_quantity', $data['stock_quantity'], 'Stock quantity')) {
            $errors['stock_quantity'] = $error;
        }

        if ($data['supplier_id'] !== null && !$this->suppliers->find((int) $data['supplier_id'])) {
            $errors['supplier_id'] = 'Selected supplier is invalid.';
        }

        if ($errors) {
            flash('error', implode(' ', $errors));
            redirect('products/create');
        }

        if (!$this->products->create($data)) {
            flash('error', 'Unable to create product. Please try again.');
            redirect('products/create');
        }

        clear_old();
        $this->logs->log((int) auth_user()['id'], 'product_create', 'Added product: ' . $data['name']);

        flash('success', 'Product created successfully.');
        redirect('products');
    }

    public function edit(): void
    {
        require_auth();
        $id = (int) ($_GET['id'] ?? 0);
        $product = $this->products->find($id);

        if (!$product) {
            flash('error', 'Product not found.');
            redirect('products');
        }

        view('products/edit', [
            'title' => 'Edit Product',
            'product' => $product,
            'categories' => $this->categories->all(),
            'suppliers' => $this->suppliers->all(),
        ]);
    }

    public function update(): void
    {
        require_auth();
        verify_csrf();

        $id = (int) ($_POST['id'] ?? 0);
        $supplierId = (int) ($_POST['supplier_id'] ?? 0);

        $data = [
            'name' => clean_input($_POST['name'] ?? ''),
            'category_id' => (int) ($_POST['category_id'] ?? 0),
            'description' => clean_input($_POST['description'] ?? ''),
            'buying_price' => sanitize_number($_POST['buying_price'] ?? 0),
            'selling_price' => sanitize_number($_POST['selling_price'] ?? 0),
            'stock_quantity' => (int) ($_POST['stock_quantity'] ?? 0),
            'supplier_id' => $supplierId > 0 ? $supplierId : null,
            'barcode' => clean_input($_POST['barcode'] ?? ''),
        ];

        if ($data['supplier_id'] !== null && !$this->suppliers->find((int) $data['supplier_id'])) {
            flash('error', 'Selected supplier is invalid.');
            redirect('products/edit?id=' . $id);
        }

        if (!$this->products->update($id, $data)) {
            flash('error', 'Unable to update product. Please try again.');
            redirect('products/edit?id=' . $id);
        }

        $this->logs->log((int) auth_user()['id'], 'product_update', 'Updated product ID: ' . $id);

        flash('success', 'Product updated successfully.');
        redirect('products');
    }

    public function delete(): void
    {
        require_role('Admin');
        verify_csrf();

        $id = (int) ($_POST['id'] ?? 0);
        $this->products->delete($id);
        $this->logs->log((int) auth_user()['id'], 'product_delete', 'Deleted product ID: ' . $id);

        flash('success', 'Product deleted successfully.');
        redirect('products');
    }

    public function stockForm(): void
    {
        require_auth();
        $id = (int) ($_GET['id'] ?? 0);
        $product = $this->products->find($id);

        if (!$product) {
            flash('error', 'Product not found.');
            redirect('products');
        }

        view('products/stock', [
            'title' => 'Adjust Stock',
            'product' => $product,
        ]);
    }

    public function stockAdjust(): void
    {
        require_auth();
        verify_csrf();

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 0);
        $type = clean_input($_POST['movement_type'] ?? '');
        $remarks = clean_input($_POST['remarks'] ?? '');

        if (!in_array($type, ['in', 'out'], true) || $quantity <= 0) {
            flash('error', 'Invalid stock adjustment details.');
            redirect('products');
        }

        $ok = $this->products->adjustStock($productId, $quantity, $type, $remarks, (int) auth_user()['id']);

        if (!$ok) {
            flash('error', 'Unable to adjust stock. Check the quantity and try again.');
            redirect('products');
        }

        $this->logs->log((int) auth_user()['id'], 'stock_adjust', strtoupper($type) . ' stock on product ID: ' . $productId);
        flash('success', 'Stock adjusted successfully.');
        redirect('products');
    }
}
