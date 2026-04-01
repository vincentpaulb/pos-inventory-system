<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/BaseModel.php';

class Dashboard extends BaseModel
{
    public function stats(): array
    {
        $stats = [];
        $stats['total_products'] = (int) $this->db->query("SELECT COUNT(*) FROM products")->fetchColumn();
        $stats['low_stock_items'] = (int) $this->db->query("SELECT COUNT(*) FROM products WHERE stock_quantity <= " . (int) LOW_STOCK_THRESHOLD)->fetchColumn();
        $stats['daily_sales'] = (float) $this->db->query("SELECT COALESCE(SUM(total_amount), 0) FROM transactions WHERE DATE(created_at) = CURDATE()")->fetchColumn();
        $stats['monthly_sales'] = (float) $this->db->query("SELECT COALESCE(SUM(total_amount), 0) FROM transactions WHERE MONTH(created_at)=MONTH(CURDATE()) AND YEAR(created_at)=YEAR(CURDATE())")->fetchColumn();
        $stats['total_revenue'] = (float) $this->db->query("SELECT COALESCE(SUM(total_amount), 0) FROM transactions")->fetchColumn();
        return $stats;
    }
}
