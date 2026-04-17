<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/Product.php';
require_once BASE_PATH . '/models/Transaction.php';
require_once BASE_PATH . '/models/ActivityLog.php';
require_once BASE_PATH . '/models/DailySalesReport.php';
require_once BASE_PATH . '/models/Expense.php';

class PosController
{
    private const PAYMENT_METHODS = ['Cash', 'Credit Card', 'GCash/Maya', 'Bank Transfer'];
    private const DEFAULT_CUSTOMER_NAME = 'Walk-in Customer';
    private const DEFAULT_CUSTOMER_ADDRESS = 'No Address Provided';

    private Product $products;
    private Transaction $transactions;
    private ActivityLog $logs;
    private DailySalesReport $dailyReports;
    private Expense $expenses;

    public function __construct()
    {
        $this->products = new Product();
        $this->transactions = new Transaction();
        $this->logs = new ActivityLog();
        $this->dailyReports = new DailySalesReport();
        $this->expenses = new Expense();
    }

    public function index(): void
    {
        require_module_access('pos');
        $historySearch = clean_input($_GET['history_search'] ?? '');
        $purchaseHistory = $this->transactions->purchaseHistory(20, $historySearch);

        if ($this->isAjaxRequest()) {
            while (ob_get_level()) {
                ob_end_clean();
            }

            http_response_code(200);
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Pragma: no-cache');

            echo json_encode($purchaseHistory, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        view('pos/index', [
            'title' => 'Point of Sale',
            'products' => $this->products->allForPos(),
            'paymentMethods' => self::PAYMENT_METHODS,
            'purchaseHistory' => $purchaseHistory,
            'historySearch' => $historySearch,
        ]);
    }

    private function isAjaxRequest(): bool
    {
        return strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
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

        if (!can_access_module('pos')) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }

        $search = trim((string) ($_GET['search'] ?? ''));
        $products = $this->products->allForPos($search);

        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        echo json_encode($products, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function checkout(): void
    {
        require_module_access('pos');
        verify_csrf();

        $rawItems = $_POST['items'] ?? '';
        $payment = (float) ($_POST['payment_amount'] ?? 0);
        $customerName = clean_input($_POST['customer_name'] ?? '');
        $customerAddress = clean_input($_POST['customer_address'] ?? '');
        $paymentMethod = clean_input($_POST['payment_method'] ?? 'Cash');
        $referenceNo = clean_input($_POST['reference_no'] ?? '');

        set_old($_POST);

        $items = json_decode($rawItems, true);
        if (!is_array($items) || count($items) === 0) {
            flash('error', 'Cart is empty. Please add products before checking out.');
            redirect('pos');
            return;
        }

        $customerName = $customerName !== '' ? $customerName : self::DEFAULT_CUSTOMER_NAME;
        $customerAddress = $customerAddress !== '' ? $customerAddress : self::DEFAULT_CUSTOMER_ADDRESS;

        if (!in_array($paymentMethod, self::PAYMENT_METHODS, true)) {
            flash('error', 'Please select a valid payment method.');
            redirect('pos');
            return;
        }

        if ($paymentMethod !== 'Cash' && $referenceNo === '') {
            flash('error', 'Reference # is required for the selected payment method.');
            redirect('pos');
            return;
        }

        $productCache = [];
        $finalItems = [];
        $total = 0.0;

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
                flash('error', 'A product in your cart no longer exists.');
                redirect('pos');
                return;
            }

            if ((int) $product['stock_quantity'] < $quantity) {
                flash('error', 'Insufficient stock for: ' . $product['name']);
                redirect('pos');
                return;
            }

            $unitPrice = (float) $product['selling_price'];
            $subtotal = $unitPrice * $quantity;
            $total += $subtotal;

            $finalItems[] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ];
        }

        if (count($finalItems) === 0) {
            flash('error', 'Cart is empty after validation.');
            redirect('pos');
            return;
        }

        if ($payment < $total) {
            flash('error', sprintf(
                'Payment (%s) is less than the total (%s).',
                format_currency($payment),
                format_currency($total)
            ));
            redirect('pos');
            return;
        }

        $invoiceNo = 'INV-' . date('YmdHis') . '-' . random_int(100, 999);

        $transactionId = $this->transactions->createSale([
            'invoice_no' => $invoiceNo,
            'user_id' => (int) auth_user()['id'],
            'total_amount' => $total,
            'payment_amount' => $payment,
            'change_amount' => $payment - $total,
            'customer_name' => $customerName,
            'customer_address' => $customerAddress,
            'payment_method' => $paymentMethod,
            'reference_no' => $referenceNo !== '' ? $referenceNo : null,
        ], $finalItems);

        if (!$transactionId) {
            flash('error', 'Unable to complete sale. Please try again.');
            redirect('pos');
            return;
        }

        clear_old();
        $this->logs->log(
            (int) auth_user()['id'],
            'sale_create',
            'Completed sale ' . $invoiceNo . ' for ' . $customerName . ' - ' . format_currency($total)
        );

        redirect('pos/receipt?id=' . $transactionId);
    }

    public function receipt(): void
    {
        require_module_access('pos');
        $id = (int) ($_GET['id'] ?? 0);
        $transaction = $this->transactions->find($id);

        if (!$transaction) {
            flash('error', 'Receipt not found.');
            redirect('pos');
            return;
        }

        view('pos/receipt', [
            'title' => 'Receipt — ' . $transaction['invoice_no'],
            'transaction' => $transaction,
            'items' => $this->transactions->items($id),
        ]);
    }

    public function voidSale(): void
    {
        require_module_access('pos');
        verify_csrf();

        $transactionId = (int) ($_POST['id'] ?? 0);
        $transaction = $this->transactions->find($transactionId);

        if (!$transaction) {
            flash('error', 'Transaction not found.');
            redirect('pos');
            return;
        }

        if (!$this->transactions->void($transactionId, (int) auth_user()['id'])) {
            flash('error', 'Unable to void this transaction. Only completed sales can be voided.');
            redirect('pos');
            return;
        }

        $this->logs->log((int) auth_user()['id'], 'sale_void', 'Voided sale ' . $transaction['invoice_no']);
        flash('success', 'Transaction voided successfully.');
        redirect('pos');
    }

    public function deleteSale(): void
    {
        require_module_access('pos');
        verify_csrf();

        $transactionId = (int) ($_POST['id'] ?? 0);
        $transaction = $this->transactions->find($transactionId);

        if (!$transaction) {
            flash('error', 'Transaction not found.');
            redirect('pos');
            return;
        }

        if (!$this->transactions->markDeleted($transactionId)) {
            flash('error', 'Only voided transactions can be deleted.');
            redirect('pos');
            return;
        }

        $this->logs->log((int) auth_user()['id'], 'sale_delete', 'Deleted voided sale ' . $transaction['invoice_no']);
        flash('success', 'Transaction deleted successfully.');
        redirect('pos');
    }

    public function previewDailyReport(): void
    {
        require_module_access('pos');

        while (ob_get_level()) {
            ob_end_clean();
        }

        $userId = (int) auth_user()['id'];
        $date   = date('Y-m-d');

        $data                   = $this->dailyReports->getSalesData($userId, $date);
        $expenseSummary         = $this->expenses->summaryForDate($date, $userId);
        $data['total_expenses'] = $expenseSummary['total_amount'];
        $data['report_date']    = $date;
        $data['employee_name']  = auth_user()['name'];
        $data['employee_id']    = $userId;

        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function submitDailyReport(): void
    {
        require_module_access('pos');
        verify_csrf();

        $userId = (int) auth_user()['id'];
        $date   = date('Y-m-d');
        $notes  = clean_input($_POST['notes'] ?? '');

        $data           = $this->dailyReports->getSalesData($userId, $date);
        $expenseSummary = $this->expenses->summaryForDate($date, $userId);

        $reportId = $this->dailyReports->create([
            'user_id'                   => $userId,
            'report_date'               => $date,
            'total_transactions'        => $data['total_transactions'],
            'total_units_sold'          => $data['total_units_sold'],
            'gross_sales'               => $data['gross_sales'],
            'net_sales'                 => $data['net_sales'],
            'vat_collected'             => $data['vat_collected'],
            'average_transaction_value' => $data['average_transaction_value'],
            'cash_sales'                => $data['cash_sales'],
            'credit_card_sales'         => $data['credit_card_sales'],
            'gcash_maya_sales'          => $data['gcash_maya_sales'],
            'bank_transfer_sales'       => $data['bank_transfer_sales'],
            'total_voids'               => $data['total_voids'],
            'voided_amount'             => $data['voided_amount'],
            'total_expenses'            => $expenseSummary['total_amount'],
            'notes'                     => $notes,
        ]);

        if (!$reportId) {
            flash('error', 'Failed to submit daily sales report. Please try again.');
            redirect('pos');
            return;
        }

        $this->logs->log($userId, 'daily_report_submit', 'Submitted daily sales report for ' . $date);
        flash('success', 'Daily Sales Report submitted successfully.');
        redirect('reports');
    }
}
