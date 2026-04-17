<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/BaseModel.php';

class ActivityLog extends BaseModel
{
    private function normalizeUserId(?int $userId): ?int
    {
        if ($userId === null || $userId <= 0) {
            return null;
        }

        $stmt = $this->db->prepare('SELECT id FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $userId]);

        return $stmt->fetchColumn() !== false ? $userId : null;
    }

    public function log(?int $userId, string $action, string $details): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO activity_logs (user_id, action, details)
            VALUES (:user_id, :action, :details)
        ");
        $stmt->execute([
            'user_id' => $this->normalizeUserId($userId),
            'action' => $action,
            'details' => $details,
        ]);
    }

    public function recent(int $limit = 15): array
    {
        $stmt = $this->db->prepare("
            SELECT al.*, u.name AS user_name
            FROM activity_logs al
            LEFT JOIN users u ON u.id = al.user_id
            ORDER BY al.id DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function filtered(?string $from, ?string $to, string $search = '', int $page = 1, int $perPage = 50): array
    {
        [$sql, $params] = $this->buildFilterQuery($from, $to, $search);
        $offset = ($page - 1) * $perPage;
        $sql .= " ORDER BY al.id DESC LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function filteredCount(?string $from, ?string $to, string $search = ''): int
    {
        [$sql, $params] = $this->buildFilterQuery($from, $to, $search);
        $countSql = "SELECT COUNT(*) FROM activity_logs al LEFT JOIN users u ON u.id = al.user_id WHERE 1=1" . substr($sql, strpos($sql, 'WHERE 1=1') + 9);
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    private function buildFilterQuery(?string $from, ?string $to, string $search): array
    {
        $sql = "
            SELECT al.*, u.name AS user_name
            FROM activity_logs al
            LEFT JOIN users u ON u.id = al.user_id
            WHERE 1=1
        ";
        $params = [];

        if ($from) {
            $sql .= " AND DATE(al.created_at) >= :from_date";
            $params['from_date'] = $from;
        }
        if ($to) {
            $sql .= " AND DATE(al.created_at) <= :to_date";
            $params['to_date'] = $to;
        }
        if ($search !== '') {
            $sql .= " AND (al.action LIKE :search OR al.details LIKE :search2 OR COALESCE(u.name,'') LIKE :search3)";
            $term = '%' . $search . '%';
            $params['search']  = $term;
            $params['search2'] = $term;
            $params['search3'] = $term;
        }

        return [$sql, $params];
    }
}
