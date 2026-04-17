<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/BaseModel.php';

class Product extends BaseModel
{
    public function all(string $search = '', string $categoryId = ''): array
    {
        $sql = "
            SELECT p.*, c.name AS category_name, s.name AS supplier_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            LEFT JOIN suppliers s ON s.id = p.supplier_id
            WHERE 1=1
        ";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (p.name LIKE :search_name OR p.description LIKE :search_description OR p.barcode LIKE :search_barcode OR p.unit_type LIKE :search_unit_type)";
            $searchTerm = '%' . $search . '%';
            $params['search_name'] = $searchTerm;
            $params['search_description'] = $searchTerm;
            $params['search_barcode'] = $searchTerm;
            $params['search_unit_type'] = $searchTerm;
        }

        if ($categoryId !== '') {
            $sql .= " AND p.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        $sql .= " ORDER BY p.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function allForPos(string $search = ''): array
    {
        $sql = "
            SELECT p.id, p.name, p.barcode, p.unit_type, p.selling_price, p.stock_quantity, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.stock_quantity > 0
        ";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (p.name LIKE :search_name OR p.barcode LIKE :search_barcode)";
            $searchTerm = '%' . $search . '%';
            $params['search_name'] = $searchTerm;
            $params['search_barcode'] = $searchTerm;
        }

        $sql .= " ORDER BY p.name ASC LIMIT 20";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }


    public function allForQuotation(string $search = ''): array
    {
        $sql = "
            SELECT p.id, p.name, p.barcode, p.unit_type, p.selling_price, p.stock_quantity, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE 1=1
        ";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (p.name LIKE :search_name OR p.barcode LIKE :search_barcode)";
            $searchTerm = '%' . $search . '%';
            $params['search_name'] = $searchTerm;
            $params['search_barcode'] = $searchTerm;
        }

        $sql .= " ORDER BY p.name ASC LIMIT 100";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function lowStock(): array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.stock_quantity <= :threshold
            ORDER BY p.stock_quantity ASC, p.name ASC
        ");
        $stmt->execute(['threshold' => LOW_STOCK_THRESHOLD]);
        return $stmt->fetchAll();
    }

    public function count(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM products")->fetchColumn();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO products
            (name, category_id, description, buying_price, selling_price, stock_quantity, supplier_id, barcode, unit_type)
            VALUES
            (:name, :category_id, :description, :buying_price, :selling_price, :stock_quantity, :supplier_id, :barcode, :unit_type)
        ");
        return $stmt->execute($data);
    }

    public function update(int $id, array $data): bool
    {
        $data['id'] = $id;
        $stmt = $this->db->prepare("
            UPDATE products
            SET name = :name,
                category_id = :category_id,
                description = :description,
                buying_price = :buying_price,
                selling_price = :selling_price,
                stock_quantity = :stock_quantity,
                supplier_id = :supplier_id,
                barcode = :barcode,
                unit_type = :unit_type
            WHERE id = :id
        ");
        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function adjustStock(int $productId, int $quantity, string $type, string $remarks, int $userId): bool
    {
        $this->db->beginTransaction();

        try {
            $product = $this->find($productId);
            if (!$product) {
                throw new Exception('Product not found.');
            }

            $newQty = $type === 'in'
                ? ((int) $product['stock_quantity'] + $quantity)
                : ((int) $product['stock_quantity'] - $quantity);

            if ($newQty < 0) {
                throw new Exception('Insufficient stock.');
            }

            $stmt1 = $this->db->prepare("UPDATE products SET stock_quantity = :qty WHERE id = :id");
            $stmt1->execute(['qty' => $newQty, 'id' => $productId]);

            $stmt2 = $this->db->prepare("
                INSERT INTO stock_movements (product_id, user_id, movement_type, quantity, remarks)
                VALUES (:product_id, :user_id, :movement_type, :quantity, :remarks)
            ");
            $stmt2->execute([
                'product_id' => $productId,
                'user_id' => $userId,
                'movement_type' => $type,
                'quantity' => $quantity,
                'remarks' => $remarks,
            ]);

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function recentMovements(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT sm.*, p.name AS product_name, u.name AS user_name
            FROM stock_movements sm
            JOIN products p ON p.id = sm.product_id
            JOIN users u ON u.id = sm.user_id
            ORDER BY sm.id DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
