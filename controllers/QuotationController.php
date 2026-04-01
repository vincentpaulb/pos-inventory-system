<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/Quotation.php';
require_once BASE_PATH . '/models/Product.php';
require_once BASE_PATH . '/models/ActivityLog.php';

class QuotationController
{
    private Quotation $quotations;
    private Product $products;
    private ActivityLog $logs;

    public function __construct()
    {
        $this->quotations = new Quotation();
        $this->products   = new Product();
        $this->logs       = new ActivityLog();
    }

    public function index(): void
    {
        require_auth();

        view('quotations/index', [
            'title'      => 'Quotation',
            'products'   => $this->products->allForQuotation(),
            'quotations' => $this->quotations->recent(),
        ]);
    }

    public function searchProducts(): void
    {
        while (ob_get_level()) {
            ob_end_clean();
        }

        if (!is_logged_in()) {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Unauthenticated']);
            exit;
        }

        $search = trim((string) ($_GET['search'] ?? ''));
        $products = $this->products->allForQuotation($search);

        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        echo json_encode($products, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function store(): void
    {
        require_auth();
        verify_csrf();

        $customerName       = clean_input($_POST['customer_name'] ?? '');
        $customerContact    = clean_input($_POST['customer_contact'] ?? '');
        $customerAddress    = clean_input($_POST['customer_address'] ?? '');
        $serviceOption      = clean_input($_POST['service_option'] ?? 'without_service_repair');
        $serviceDescription = clean_input($_POST['service_description'] ?? '');
        $serviceFee         = sanitize_number($_POST['service_fee'] ?? 0);
        $validUntil         = clean_input($_POST['valid_until'] ?? '');
        $notes              = clean_input($_POST['notes'] ?? '');
        $rawItems           = $_POST['items'] ?? '';

        if ($customerName === '') {
            flash('error', 'Customer name is required.');
            redirect('quotations');
            return;
        }

        if (!in_array($serviceOption, ['without_service_repair', 'with_service_repair'], true)) {
            $serviceOption = 'without_service_repair';
        }

        if ($serviceOption === 'without_service_repair') {
            $serviceDescription = '';
            $serviceFee = 0.0;
        }

        $items = json_decode($rawItems, true);
        if (!is_array($items) || count($items) === 0) {
            flash('error', 'Please add at least one product to the quotation.');
            redirect('quotations');
            return;
        }

        $productCache = [];
        $finalItems = [];
        $subtotal = 0.0;

        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);

            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }

            if (!isset($productCache[$productId])) {
                $productCache[$productId] = $this->products->find($productId);
            }
            $product = $productCache[$productId];

            if (!$product) {
                flash('error', 'One of the selected products no longer exists.');
                redirect('quotations');
                return;
            }

            $unitPrice = (float) $product['selling_price'];
            $lineSubtotal = $unitPrice * $quantity;
            $subtotal += $lineSubtotal;

            $finalItems[] = [
                'product_id' => $productId,
                'quantity'   => $quantity,
                'unit_price' => $unitPrice,
                'subtotal'   => $lineSubtotal,
            ];
        }

        if (count($finalItems) === 0) {
            flash('error', 'No valid quotation items were found.');
            redirect('quotations');
            return;
        }

        $total = $subtotal + max(0, $serviceFee);
        $quoteNo = 'QT-' . date('YmdHis') . '-' . random_int(100, 999);

        $quotationId = $this->quotations->create([
            'quote_no'            => $quoteNo,
            'user_id'             => (int) auth_user()['id'],
            'customer_name'       => $customerName,
            'customer_contact'    => $customerContact,
            'customer_address'    => $customerAddress,
            'service_option'      => $serviceOption,
            'service_description' => $serviceDescription,
            'service_fee'         => max(0, $serviceFee),
            'subtotal_amount'     => $subtotal,
            'total_amount'        => $total,
            'valid_until'         => $validUntil !== '' ? $validUntil : null,
            'notes'               => $notes,
        ], $finalItems);

        if (!$quotationId) {
            flash('error', 'Unable to save quotation. Please try again.');
            redirect('quotations');
            return;
        }

        $this->logs->log(
            (int) auth_user()['id'],
            'quotation_create',
            'Created quotation ' . $quoteNo . ' for ' . $customerName . ' — ₱' . number_format($total, 2)
        );

        flash('success', 'Quotation created successfully.');
        redirect('quotations/view?id=' . $quotationId);
    }

    public function view(): void
    {
        require_auth();

        $id = (int) ($_GET['id'] ?? 0);
        $quotation = $this->quotations->find($id);

        if (!$quotation) {
            flash('error', 'Quotation not found.');
            redirect('quotations');
            return;
        }

        view('quotations/view', [
            'title'     => 'Quotation — ' . $quotation['quote_no'],
            'quotation' => $quotation,
            'items'     => $this->quotations->items($id),
        ]);
    }
}
