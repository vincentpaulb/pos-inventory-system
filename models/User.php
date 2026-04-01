<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/BaseModel.php';

class User extends BaseModel
{
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function all(): array
    {
        return $this->db->query("SELECT id, name, username, role, created_at FROM users ORDER BY id DESC")->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT id, name, username, role FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (name, username, password, role)
            VALUES (:name, :username, :password, :role)
        ");
        return $stmt->execute([
            'name' => $data['name'],
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'],
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET name = :name, username = :username, role = :role
            WHERE id = :id
        ");
        return $stmt->execute([
            'name' => $data['name'],
            'username' => $data['username'],
            'role' => $data['role'],
            'id' => $id,
        ]);
    }

    public function resetPassword(int $id, string $password): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id");
        return $stmt->execute([
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function usernameExists(string $username, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
        $params = ['username' => $username];

        if ($excludeId !== null) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool) $stmt->fetchColumn();
    }
}
