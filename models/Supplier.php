<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/BaseModel.php';

class Supplier extends BaseModel
{
    public function all(string $search = ''): array
    {
        $search = trim($search);

        if ($search === '') {
            return $this->db->query("SELECT * FROM suppliers ORDER BY name ASC")->fetchAll();
        }

        $stmt = $this->db->prepare("
            SELECT *
            FROM suppliers
            WHERE name LIKE :search_name
               OR contact_person LIKE :search_contact
               OR phone LIKE :search_phone
               OR address LIKE :search_address
            ORDER BY name ASC
        ");
        $stmt->execute([
            'search_name' => '%' . $search . '%',
            'search_contact' => '%' . $search . '%',
            'search_phone' => '%' . $search . '%',
            'search_address' => '%' . $search . '%',
        ]);

        return $stmt->fetchAll();
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
