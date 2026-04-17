<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/BaseModel.php';

class Expense extends BaseModel
{
    public function cashOnHandForDate(string $date): ?array
    {
        $stmt = $this->db->prepare("
            SELECT dch.*, u.name AS recorded_by_name
            FROM daily_cash_on_hand dch
            INNER JOIN users u ON u.id = dch.user_id
            WHERE dch.entry_date = :entry_date
            LIMIT 1
        ");
        $stmt->execute(['entry_date' => $date]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function upsertCashOnHand(string $date, float $amount, int $userId): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO daily_cash_on_hand (entry_date, amount, user_id, recorded_at)
            VALUES (:entry_date, :amount, :user_id, NOW())
            ON DUPLICATE KEY UPDATE
                amount = VALUES(amount),
                user_id = VALUES(user_id),
                recorded_at = NOW()
        ");

        return $stmt->execute([
            'entry_date' => $date,
            'amount' => $amount,
            'user_id' => $userId,
        ]);
    }

    public function createExpense(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO expenses (user_id, expense_type, fund_resource, amount, description, expense_time)
            VALUES (:user_id, :expense_type, :fund_resource, :amount, :description, :expense_time)
        ");

        return $stmt->execute([
            'user_id' => $data['user_id'],
            'expense_type' => $data['expense_type'],
            'fund_resource' => $data['fund_resource'],
            'amount' => $data['amount'],
            'description' => $data['description'] !== '' ? $data['description'] : null,
            'expense_time' => $data['expense_time'],
        ]);
    }

    public function recentExpenses(int $limit = 20): array
    {
        $stmt = $this->db->prepare("
            SELECT e.*, u.name AS recorded_by_name
            FROM expenses e
            INNER JOIN users u ON u.id = e.user_id
            ORDER BY e.expense_time DESC, e.id DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function summaryForDate(string $date, ?int $userId = null): array
    {
        $sql = "
            SELECT
                COUNT(*) AS total_entries,
                COALESCE(SUM(amount), 0) AS total_amount,
                COALESCE(SUM(CASE WHEN fund_resource = 'Sales' THEN amount ELSE 0 END), 0) AS sales_funded_amount,
                COALESCE(SUM(CASE WHEN fund_resource = 'Cash on Hand' THEN amount ELSE 0 END), 0) AS cash_funded_amount
            FROM expenses
            WHERE DATE(expense_time) = :entry_date
        ";
        $params = ['entry_date' => $date];

        if ($userId !== null) {
            $sql .= " AND user_id = :user_id";
            $params['user_id'] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: [
            'total_entries' => 0,
            'total_amount' => 0,
            'sales_funded_amount' => 0,
            'cash_funded_amount' => 0,
        ];
    }

    public function filteredExpenses(?string $from, ?string $to, string $search = ''): array
    {
        $sql = "
            SELECT e.*, u.name AS recorded_by_name
            FROM expenses e
            INNER JOIN users u ON u.id = e.user_id
            WHERE 1=1
        ";
        $params = [];

        if ($from) {
            $sql .= " AND DATE(e.expense_time) >= :from_date";
            $params['from_date'] = $from;
        }
        if ($to) {
            $sql .= " AND DATE(e.expense_time) <= :to_date";
            $params['to_date'] = $to;
        }
        if ($search !== '') {
            $sql .= " AND (e.expense_type LIKE :search OR e.fund_resource LIKE :search2 OR COALESCE(e.description,'') LIKE :search3 OR u.name LIKE :search4)";
            $term = '%' . $search . '%';
            $params['search']  = $term;
            $params['search2'] = $term;
            $params['search3'] = $term;
            $params['search4'] = $term;
        }

        $sql .= " ORDER BY e.expense_time DESC, e.id DESC LIMIT 500";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function recentCashOnHandRecords(int $limit = 7): array
    {
        $stmt = $this->db->prepare("
            SELECT dch.*, u.name AS recorded_by_name
            FROM daily_cash_on_hand dch
            INNER JOIN users u ON u.id = dch.user_id
            ORDER BY dch.entry_date DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
