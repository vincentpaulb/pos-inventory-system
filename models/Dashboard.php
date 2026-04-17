<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/BaseModel.php';

class Dashboard extends BaseModel
{
    private const COMPLETED_STATUS = 'completed';

    public function stats(): array
    {
        $stats = [];
        $stats['total_products'] = (int) $this->db->query("SELECT COUNT(*) FROM products")->fetchColumn();
        $stats['low_stock_items'] = (int) $this->db->query("SELECT COUNT(*) FROM products WHERE stock_quantity <= " . (int) system_low_stock_threshold())->fetchColumn();
        $stats['daily_sales'] = (float) $this->db->query("SELECT COALESCE(SUM(total_amount), 0) FROM transactions WHERE status = '" . self::COMPLETED_STATUS . "' AND DATE(created_at) = CURDATE()")->fetchColumn();
        $stats['monthly_sales'] = (float) $this->db->query("SELECT COALESCE(SUM(total_amount), 0) FROM transactions WHERE status = '" . self::COMPLETED_STATUS . "' AND MONTH(created_at)=MONTH(CURDATE()) AND YEAR(created_at)=YEAR(CURDATE())")->fetchColumn();
        $stats['total_revenue'] = (float) $this->db->query("SELECT COALESCE(SUM(total_amount), 0) FROM transactions WHERE status = '" . self::COMPLETED_STATUS . "'")->fetchColumn();
        return $stats;
    }

    public function dailySalesSeries(int $days = 7): array
    {
        $days = max(1, $days);
        $stmt = $this->db->prepare("
            SELECT DATE(created_at) AS sale_date, COALESCE(SUM(total_amount), 0) AS total_sales
            FROM transactions
            WHERE status = :status
              AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY DATE(created_at)
            ORDER BY sale_date ASC
        ");
        $stmt->bindValue(':status', self::COMPLETED_STATUS);
        $stmt->bindValue(':days', $days - 1, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        $indexed = [];
        foreach ($rows as $row) {
            $indexed[$row['sale_date']] = (float) $row['total_sales'];
        }

        $series = [];
        $start = new DateTimeImmutable('-' . ($days - 1) . ' days');
        for ($i = 0; $i < $days; $i++) {
            $date = $start->modify('+' . $i . ' days');
            $key = $date->format('Y-m-d');
            $series[] = [
                'label' => $date->format('M d'),
                'value' => $indexed[$key] ?? 0.0,
            ];
        }

        return $series;
    }

    public function monthlySalesSeries(int $months = 6): array
    {
        $months = max(1, $months);
        $startMonth = new DateTimeImmutable('first day of -' . ($months - 1) . ' months');
        $stmt = $this->db->prepare("
            SELECT DATE_FORMAT(created_at, '%Y-%m-01') AS sale_month, COALESCE(SUM(total_amount), 0) AS total_sales
            FROM transactions
            WHERE status = :status
              AND created_at >= :start_month
            GROUP BY DATE_FORMAT(created_at, '%Y-%m-01')
            ORDER BY sale_month ASC
        ");
        $stmt->execute([
            'status' => self::COMPLETED_STATUS,
            'start_month' => $startMonth->format('Y-m-01 00:00:00'),
        ]);

        $rows = $stmt->fetchAll();
        $indexed = [];
        foreach ($rows as $row) {
            $indexed[$row['sale_month']] = (float) $row['total_sales'];
        }

        $series = [];
        for ($i = 0; $i < $months; $i++) {
            $month = $startMonth->modify('+' . $i . ' months');
            $key = $month->format('Y-m-01');
            $series[] = [
                'label' => $month->format('M Y'),
                'value' => $indexed[$key] ?? 0.0,
            ];
        }

        return $series;
    }
}
