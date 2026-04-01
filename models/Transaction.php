<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/BaseModel.php';

class Transaction extends BaseModel
{
    public function createSale(array $saleData, array $items): int|false
    {
        try {
            $this->db->beginTransaction();

            /* ── 1. Insert transaction header ── */
            $stmt = $this->db->prepare("
                INSERT INTO transactions
                    (invoice_no, user_id, total_amount, payment_amount, change_amount)
                VALUES
                    (:invoice_no, :user_id, :total_amount, :payment_amount, :change_amount)
            ");
            $stmt->execute([
                'invoice_no'     => $saleData['invoice_no'],
                'user_id'        => (int) $saleData['user_id'],
                'total_amount'   => (float) $saleData['total_amount'],
                'payment_amount' => (float) $saleData['payment_amount'],
                'change_amount'  => (float) $saleData['change_amount'],
            ]);
            $transactionId = (int) $this->db->lastInsertId();

            /* ── 2. Prepare child statements ── */
            $itemStmt = $this->db->prepare("
                INSERT INTO transaction_items
                    (transaction_id, product_id, quantity, unit_price, subtotal)
                VALUES
                    (:transaction_id, :product_id, :quantity, :unit_price, :subtotal)
            ");

            $stockStmt = $this->db->prepare("
                UPDATE products
                SET stock_quantity = stock_quantity - :quantity
                WHERE id = :product_id
                  AND stock_quantity >= :quantity2
            ");

            $movementStmt = $this->db->prepare("
                INSERT INTO stock_movements
                    (product_id, user_id, movement_type, quantity, remarks)
                VALUES
                    (:product_id, :user_id, 'out', :quantity, :remarks)
            ");

            /* ── 3. Process each item ── */
            foreach ($items as $item) {
                $productId = (int) $item['product_id'];
                $quantity  = (int) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];
                $subtotal  = (float) $item['subtotal'];

                /* Insert line item */
                $itemStmt->execute([
                    'transaction_id' => $transactionId,
                    'product_id'     => $productId,
                    'quantity'       => $quantity,
                    'unit_price'     => $unitPrice,
                    'subtotal'       => $subtotal,
                ]);

                /* Deduct stock — note: two separate params for the same value
                   because PDO named params can't be reused in one execute() */
                $stockStmt->execute([
                    'quantity'   => $quantity,
                    'product_id' => $productId,
                    'quantity2'  => $quantity,
                ]);

                if ($stockStmt->rowCount() === 0) {
                    throw new \RuntimeException(
                        "Insufficient stock for product ID {$productId}."
                    );
                }

                /* Stock movement log */
                $movementStmt->execute([
                    'product_id' => $productId,
                    'user_id'    => (int) $saleData['user_id'],
                    'quantity'   => $quantity,
                    'remarks'    => 'POS Sale ' . $saleData['invoice_no'],
                ]);
            }

            $this->db->commit();
            return $transactionId;

        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            /* Log the real error so you can see it in PHP error_log */
            error_log('[Transaction::createSale] ' . $e->getMessage());
            return false;
        }
    }

    public function recent(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT t.*, u.name AS cashier_name
            FROM transactions t
            LEFT JOIN users u ON u.id = t.user_id
            ORDER BY t.id DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT t.*, u.name AS cashier_name
            FROM transactions t
            LEFT JOIN users u ON u.id = t.user_id
            WHERE t.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function items(int $transactionId): array
    {
        $stmt = $this->db->prepare("
            SELECT ti.*, p.name AS product_name
            FROM transaction_items ti
            JOIN products p ON p.id = ti.product_id
            WHERE ti.transaction_id = :transaction_id
        ");
        $stmt->execute(['transaction_id' => $transactionId]);
        return $stmt->fetchAll();
    }

    public function salesSummary(string $period = 'day'): array
    {
        $where = match ($period) {
            'week'  => "WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)",
            'month' => "WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())",
            default => "WHERE DATE(created_at) = CURDATE()",
        };

        $stmt = $this->db->query("
            SELECT
                COUNT(*) AS total_transactions,
                COALESCE(SUM(total_amount), 0) AS total_sales
            FROM transactions
            {$where}
        ");
        return $stmt->fetch() ?: ['total_transactions' => 0, 'total_sales' => 0];
    }

    public function totalRevenue(): float
    {
        return (float) $this->db
            ->query("SELECT COALESCE(SUM(total_amount), 0) FROM transactions")
            ->fetchColumn();
    }

    public function filteredHistory(?string $from, ?string $to): array
    {
        $sql    = "
            SELECT t.*, u.name AS cashier_name
            FROM transactions t
            LEFT JOIN users u ON u.id = t.user_id
            WHERE 1=1
        ";
        $params = [];

        if ($from) {
            $sql .= " AND DATE(t.created_at) >= :from_date";
            $params['from_date'] = $from;
        }
        if ($to) {
            $sql .= " AND DATE(t.created_at) <= :to_date";
            $params['to_date'] = $to;
        }

        $sql .= " ORDER BY t.id DESC";
        $stmt  = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
