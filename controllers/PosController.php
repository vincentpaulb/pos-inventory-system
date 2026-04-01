<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/Product.php';
require_once BASE_PATH . '/models/Transaction.php';
require_once BASE_PATH . '/models/ActivityLog.php';

class PosController
{
    private Product $products;
    private Transaction $transactions;
    private ActivityLog $logs;

    public function __construct()
    {
        $this->products     = new Product();
        $this->transactions = new Transaction();
        $this->logs         = new ActivityLog();
    }

    public function index(): void
    {
        require_auth();
        view('pos/index', [
            'title'    => 'Point of Sale',
            'products' => $this->products->allForPos(),
        ]);
    }

    /**
     * AJAX endpoint — always returns JSON, never HTML.
     * Called by the POS search box via fetch().
     */
    public function searchProducts(): void
    {
        // Kill any output buffering so no stray HTML/whitespace leaks out
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Auth check — return JSON 401 instead of a redirect (which would break fetch)
        if (!is_logged_in()) {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Unauthenticated']);
            exit;
        }

        $search   = trim((string) ($_GET['search'] ?? ''));
        $products = $this->products->allForPos($search);

        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        // Prevent browser / proxy caching of search results
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        echo json_encode($products, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function checkout(): void
    {
        require_auth();
        verify_csrf();

        $rawItems = $_POST['items'] ?? '';
        $payment  = (float) ($_POST['payment_amount'] ?? 0);

        // Validate items JSON
        $items = json_decode($rawItems, true);
        if (!is_array($items) || count($items) === 0) {
            flash('error', 'Cart is empty. Please add products before checking out.');
            redirect('pos');
            return;
        }

        $productCache = [];
        $finalItems   = [];
        $total        = 0.0;

        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity  = (int) ($item['quantity']   ?? 0);

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

            $unitPrice  = (float) $product['selling_price'];
            $subtotal   = $unitPrice * $quantity;
            $total     += $subtotal;

            $finalItems[] = [
                'product_id' => $productId,
                'quantity'   => $quantity,
                'unit_price' => $unitPrice,
                'subtotal'   => $subtotal,
            ];
        }

        if (count($finalItems) === 0) {
            flash('error', 'Cart is empty after validation.');
            redirect('pos');
            return;
        }

        if ($payment < $total) {
            flash('error', sprintf(
                'Payment (₱%s) is less than the total (₱%s).',
                number_format($payment, 2),
                number_format($total, 2)
            ));
            redirect('pos');
            return;
        }

        $invoiceNo = 'INV-' . date('YmdHis') . '-' . random_int(100, 999);

        $transactionId = $this->transactions->createSale([
            'invoice_no'     => $invoiceNo,
            'user_id'        => (int) auth_user()['id'],
            'total_amount'   => $total,
            'payment_amount' => $payment,
            'change_amount'  => $payment - $total,
        ], $finalItems);

        if (!$transactionId) {
            flash('error', 'Unable to complete sale. Please try again.');
            redirect('pos');
            return;
        }

        $this->logs->log(
            (int) auth_user()['id'],
            'sale_create',
            'Completed sale ' . $invoiceNo . ' — ₱' . number_format($total, 2)
        );

        redirect('pos/receipt?id=' . $transactionId);
    }

    public function receipt(): void
    {
        require_auth();
        $id          = (int) ($_GET['id'] ?? 0);
        $transaction = $this->transactions->find($id);

        if (!$transaction) {
            flash('error', 'Receipt not found.');
            redirect('pos');
            return;
        }

        view('pos/receipt', [
            'title'       => 'Receipt — ' . $transaction['invoice_no'],
            'transaction' => $transaction,
            'items'       => $this->transactions->items($id),
        ]);
    }
}
