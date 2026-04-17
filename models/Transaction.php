<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/BaseModel.php';

class Transaction extends BaseModel
{
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_VOIDED = 'voided';
    public const STATUS_DELETED = 'deleted';

    public function createSale(array $saleData, array $items): int|false
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO transactions
                    (invoice_no, user_id, total_amount, payment_amount, change_amount, customer_name, customer_address, payment_method, reference_no, status)
                VALUES
                    (:invoice_no, :user_id, :total_amount, :payment_amount, :change_amount, :customer_name, :customer_address, :payment_method, :reference_no, :status)
            ");
            $stmt->execute([
                'invoice_no' => $saleData['invoice_no'],
                'user_id' => (int) $saleData['user_id'],
                'total_amount' => (float) $saleData['total_amount'],
                'payment_amount' => (float) $saleData['payment_amount'],
                'change_amount' => (float) $saleData['change_amount'],
                'customer_name' => $saleData['customer_name'],
                'customer_address' => $saleData['customer_address'],
                'payment_method' => $saleData['payment_method'],
                'reference_no' => $saleData['reference_no'],
                'status' => self::STATUS_COMPLETED,
            ]);
            $transactionId = (int) $this->db->lastInsertId();

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

            foreach ($items as $item) {
                $productId = (int) $item['product_id'];
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];
                $subtotal = (float) $item['subtotal'];

                $itemStmt->execute([
                    'transaction_id' => $transactionId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                $stockStmt->execute([
                    'quantity' => $quantity,
                    'product_id' => $productId,
                    'quantity2' => $quantity,
                ]);

                if ($stockStmt->rowCount() === 0) {
                    throw new RuntimeException("Insufficient stock for product ID {$productId}.");
                }

                $movementStmt->execute([
                    'product_id' => $productId,
                    'user_id' => (int) $saleData['user_id'],
                    'quantity' => $quantity,
                    'remarks' => 'POS Sale ' . $saleData['invoice_no'],
                ]);
            }

            $this->db->commit();
            return $transactionId;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
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
            WHERE t.status = :status
            ORDER BY t.id DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':status', self::STATUS_COMPLETED);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function purchaseHistory(int $limit = 20, string $search = ''): array
    {
        $sql = "
            SELECT t.*, u.name AS cashier_name
            FROM transactions t
            LEFT JOIN users u ON u.id = t.user_id
            WHERE 1=1
        ";

        $params = [];
        if ($search !== '') {
            $sql .= "
                AND (
                    t.invoice_no LIKE :search_invoice
                    OR COALESCE(t.customer_name, '') LIKE :search_customer_name
                    OR COALESCE(t.customer_address, '') LIKE :search_customer_address
                    OR COALESCE(t.payment_method, '') LIKE :search_payment_method
                    OR COALESCE(t.reference_no, '') LIKE :search_reference_no
                    OR COALESCE(t.status, '') LIKE :search_status
                    OR COALESCE(u.name, '') LIKE :search_cashier
                )
            ";
            $searchTerm = '%' . $search . '%';
            $params['search_invoice'] = $searchTerm;
            $params['search_customer_name'] = $searchTerm;
            $params['search_customer_address'] = $searchTerm;
            $params['search_payment_method'] = $searchTerm;
            $params['search_reference_no'] = $searchTerm;
            $params['search_status'] = $searchTerm;
            $params['search_cashier'] = $searchTerm;
        }

        $sql .= " ORDER BY t.id DESC LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
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
            SELECT ti.*, p.name AS product_name, p.unit_type
            FROM transaction_items ti
            JOIN products p ON p.id = ti.product_id
            WHERE ti.transaction_id = :transaction_id
        ");
        $stmt->execute(['transaction_id' => $transactionId]);
        return $stmt->fetchAll();
    }

    public function void(int $transactionId, int $userId): bool
    {
        $this->db->beginTransaction();

        try {
            $transaction = $this->find($transactionId);
            if (!$transaction || $transaction['status'] !== self::STATUS_COMPLETED) {
                throw new RuntimeException('Only completed transactions can be voided.');
            }

            $items = $this->items($transactionId);
            $stockStmt = $this->db->prepare("
                UPDATE products
                SET stock_quantity = stock_quantity + :quantity
                WHERE id = :product_id
            ");
            $movementStmt = $this->db->prepare("
                INSERT INTO stock_movements
                    (product_id, user_id, movement_type, quantity, remarks)
                VALUES
                    (:product_id, :user_id, 'in', :quantity, :remarks)
            ");

            foreach ($items as $item) {
                $stockStmt->execute([
                    'quantity' => (int) $item['quantity'],
                    'product_id' => (int) $item['product_id'],
                ]);

                $movementStmt->execute([
                    'product_id' => (int) $item['product_id'],
                    'user_id' => $userId,
                    'quantity' => (int) $item['quantity'],
                    'remarks' => 'Voided sale ' . $transaction['invoice_no'],
                ]);
            }

            $updateStmt = $this->db->prepare("
                UPDATE transactions
                SET status = :status,
                    voided_at = NOW()
                WHERE id = :id
            ");
            $updateStmt->execute([
                'status' => self::STATUS_VOIDED,
                'id' => $transactionId,
            ]);

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('[Transaction::void] ' . $e->getMessage());
            return false;
        }
    }

    public function markDeleted(int $transactionId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE transactions
            SET status = :status,
                deleted_at = NOW()
            WHERE id = :id
              AND status = :from_status
        ");
        $stmt->execute([
            'status' => self::STATUS_DELETED,
            'id' => $transactionId,
            'from_status' => self::STATUS_VOIDED,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function salesSummary(string $period = 'day'): array
    {
        $where = match ($period) {
            'week' => "WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)",
            'month' => "WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())",
            default => "WHERE DATE(created_at) = CURDATE()",
        };

        $stmt = $this->db->query("
            SELECT
                COUNT(*) AS total_transactions,
                COALESCE(SUM(total_amount), 0) AS total_sales
            FROM transactions
            {$where} " . (str_contains($where, 'WHERE') ? ' AND ' : ' WHERE ') . " status = '" . self::STATUS_COMPLETED . "'
        ");
        return $stmt->fetch() ?: ['total_transactions' => 0, 'total_sales' => 0];
    }

    public function totalRevenue(): float
    {
        return (float) $this->db
            ->query("SELECT COALESCE(SUM(total_amount), 0) FROM transactions WHERE status = '" . self::STATUS_COMPLETED . "'")
            ->fetchColumn();
    }

    public function filteredHistory(?string $from, ?string $to): array
    {
        $sql = "
            SELECT t.*, u.name AS cashier_name
            FROM transactions t
            LEFT JOIN users u ON u.id = t.user_id
            WHERE t.status = :status
        ";
        $params = ['status' => self::STATUS_COMPLETED];

        if ($from) {
            $sql .= " AND DATE(t.created_at) >= :from_date";
            $params['from_date'] = $from;
        }
        if ($to) {
            $sql .= " AND DATE(t.created_at) <= :to_date";
            $params['to_date'] = $to;
        }

        $sql .= " ORDER BY t.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
