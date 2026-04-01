<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/BaseModel.php';

class Quotation extends BaseModel
{
    public function create(array $quoteData, array $items): int|false
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO quotations
                    (quote_no, user_id, customer_name, customer_contact, customer_address,
                     service_option, service_description, service_fee, subtotal_amount,
                     total_amount, valid_until, notes)
                VALUES
                    (:quote_no, :user_id, :customer_name, :customer_contact, :customer_address,
                     :service_option, :service_description, :service_fee, :subtotal_amount,
                     :total_amount, :valid_until, :notes)
            ");
            $stmt->execute([
                'quote_no'             => $quoteData['quote_no'],
                'user_id'              => (int) $quoteData['user_id'],
                'customer_name'        => $quoteData['customer_name'],
                'customer_contact'     => $quoteData['customer_contact'] ?: null,
                'customer_address'     => $quoteData['customer_address'] ?: null,
                'service_option'       => $quoteData['service_option'],
                'service_description'  => $quoteData['service_description'] ?: null,
                'service_fee'          => (float) $quoteData['service_fee'],
                'subtotal_amount'      => (float) $quoteData['subtotal_amount'],
                'total_amount'         => (float) $quoteData['total_amount'],
                'valid_until'          => $quoteData['valid_until'] ?: null,
                'notes'                => $quoteData['notes'] ?: null,
            ]);

            $quotationId = (int) $this->db->lastInsertId();

            $itemStmt = $this->db->prepare("
                INSERT INTO quotation_items
                    (quotation_id, product_id, quantity, unit_price, subtotal)
                VALUES
                    (:quotation_id, :product_id, :quantity, :unit_price, :subtotal)
            ");

            foreach ($items as $item) {
                $itemStmt->execute([
                    'quotation_id' => $quotationId,
                    'product_id'   => (int) $item['product_id'],
                    'quantity'     => (int) $item['quantity'],
                    'unit_price'   => (float) $item['unit_price'],
                    'subtotal'     => (float) $item['subtotal'],
                ]);
            }

            $this->db->commit();
            return $quotationId;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('[Quotation::create] ' . $e->getMessage());
            return false;
        }
    }

    public function recent(int $limit = 20): array
    {
        $stmt = $this->db->prepare("
            SELECT q.*, u.name AS prepared_by
            FROM quotations q
            LEFT JOIN users u ON u.id = q.user_id
            ORDER BY q.id DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT q.*, u.name AS prepared_by
            FROM quotations q
            LEFT JOIN users u ON u.id = q.user_id
            WHERE q.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function items(int $quotationId): array
    {
        $stmt = $this->db->prepare("
            SELECT qi.*, p.name AS product_name, p.barcode
            FROM quotation_items qi
            LEFT JOIN products p ON p.id = qi.product_id
            WHERE qi.quotation_id = :quotation_id
            ORDER BY qi.id ASC
        ");
        $stmt->execute(['quotation_id' => $quotationId]);
        return $stmt->fetchAll();
    }
}
