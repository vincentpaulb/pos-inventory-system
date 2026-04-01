<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/BaseModel.php';

class Category extends BaseModel
{
    public function all(): array
    {
        return $this->db->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $name): bool
    {
        $stmt = $this->db->prepare("INSERT INTO categories (name) VALUES (:name)");
        return $stmt->execute(['name' => $name]);
    }

    public function update(int $id, string $name): bool
    {
        $stmt = $this->db->prepare("UPDATE categories SET name = :name WHERE id = :id");
        return $stmt->execute(['name' => $name, 'id' => $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
