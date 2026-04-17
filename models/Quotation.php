<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/BaseModel.php';

class Quotation extends BaseModel
{
    private function insertItems(int $quotationId, array $items): void
    {
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
    }

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
            $this->insertItems($quotationId, $items);

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

    public function update(int $id, array $quoteData, array $items): bool
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE quotations
                SET customer_name = :customer_name,
                    customer_contact = :customer_contact,
                    customer_address = :customer_address,
                    service_option = :service_option,
                    service_description = :service_description,
                    service_fee = :service_fee,
                    subtotal_amount = :subtotal_amount,
                    total_amount = :total_amount,
                    valid_until = :valid_until,
                    notes = :notes
                WHERE id = :id
            ");
            $stmt->execute([
                'id' => $id,
                'customer_name' => $quoteData['customer_name'],
                'customer_contact' => $quoteData['customer_contact'] ?: null,
                'customer_address' => $quoteData['customer_address'] ?: null,
                'service_option' => $quoteData['service_option'],
                'service_description' => $quoteData['service_description'] ?: null,
                'service_fee' => (float) $quoteData['service_fee'],
                'subtotal_amount' => (float) $quoteData['subtotal_amount'],
                'total_amount' => (float) $quoteData['total_amount'],
                'valid_until' => $quoteData['valid_until'] ?: null,
                'notes' => $quoteData['notes'] ?: null,
            ]);

            $deleteItems = $this->db->prepare("DELETE FROM quotation_items WHERE quotation_id = :quotation_id");
            $deleteItems->execute(['quotation_id' => $id]);

            $this->insertItems($id, $items);

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('[Quotation::update] ' . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $this->db->beginTransaction();

            $deleteItems = $this->db->prepare("DELETE FROM quotation_items WHERE quotation_id = :quotation_id");
            $deleteItems->execute(['quotation_id' => $id]);

            $deleteQuote = $this->db->prepare("DELETE FROM quotations WHERE id = :id");
            $deleteQuote->execute(['id' => $id]);

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('[Quotation::delete] ' . $e->getMessage());
            return false;
        }
    }

    public function recent(int $limit = 20, string $search = ''): array
    {
        $sql = "
            SELECT q.*, u.name AS prepared_by
            FROM quotations q
            LEFT JOIN users u ON u.id = q.user_id
            WHERE 1=1
        ";
        $params = [];

        if ($search !== '') {
            $sql .= "
                AND (
                    q.quote_no LIKE :search_quote
                    OR q.customer_name LIKE :search_customer
                    OR COALESCE(q.customer_contact, '') LIKE :search_contact
                )
            ";
            $searchTerm = '%' . $search . '%';
            $params['search_quote'] = $searchTerm;
            $params['search_customer'] = $searchTerm;
            $params['search_contact'] = $searchTerm;
        }

        $sql .= " ORDER BY q.id DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }
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
            SELECT qi.*, p.name AS product_name, p.barcode, p.unit_type, p.stock_quantity, c.name AS category_name
            FROM quotation_items qi
            LEFT JOIN products p ON p.id = qi.product_id
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE qi.quotation_id = :quotation_id
            ORDER BY qi.id ASC
        ");
        $stmt->execute(['quotation_id' => $quotationId]);
        return $stmt->fetchAll();
    }
}
