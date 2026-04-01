<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/BaseModel.php';

class Supplier extends BaseModel
{
    public function all(): array
    {
        return $this->db->query("SELECT * FROM suppliers ORDER BY name ASC")->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM suppliers WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO suppliers (name, contact_person, phone, address)
            VALUES (:name, :contact_person, :phone, :address)
        ");
        return $stmt->execute($data);
    }

    public function update(int $id, array $data): bool
    {
        $data['id'] = $id;
        $stmt = $this->db->prepare("
            UPDATE suppliers
            SET name = :name, contact_person = :contact_person, phone = :phone, address = :address
            WHERE id = :id
        ");
        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM suppliers WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
