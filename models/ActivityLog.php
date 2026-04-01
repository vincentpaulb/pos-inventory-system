<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/BaseModel.php';

class ActivityLog extends BaseModel
{
    public function log(?int $userId, string $action, string $details): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO activity_logs (user_id, action, details)
            VALUES (:user_id, :action, :details)
        ");
        $stmt->execute([
            'user_id' => $userId,
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
}
