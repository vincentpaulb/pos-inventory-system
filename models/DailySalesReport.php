<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/BaseModel.php';

class DailySalesReport extends BaseModel
{
    public function getSalesData(int $userId, string $date): array
    {
        $stmt = $this->db->prepare("
            SELECT
                COUNT(DISTINCT t.id)                                          AS total_transactions,
                COALESCE(SUM(ti.quantity), 0)                                 AS total_units_sold,
                COALESCE(SUM(t.total_amount), 0)                              AS gross_sales,
                CASE WHEN COUNT(DISTINCT t.id) > 0
                     THEN COALESCE(SUM(t.total_amount) / COUNT(DISTINCT t.id), 0)
                     ELSE 0 END                                               AS average_transaction_value
            FROM transactions t
            INNER JOIN transaction_items ti ON ti.transaction_id = t.id
            WHERE t.user_id = :user_id
              AND DATE(t.created_at) = :report_date
              AND t.status = 'completed'
        ");
        $stmt->execute(['user_id' => $userId, 'report_date' => $date]);
        $sales = $stmt->fetch() ?: [
            'total_transactions' => 0, 'total_units_sold' => 0,
            'gross_sales' => 0, 'average_transaction_value' => 0,
        ];

        $taxBreakdown = sales_tax_breakdown((float) ($sales['gross_sales'] ?? 0));
        $sales['net_sales'] = $taxBreakdown['net'];
        $sales['vat_collected'] = $taxBreakdown['vat'];

        $stmt = $this->db->prepare("
            SELECT
                COALESCE(SUM(CASE WHEN payment_method = 'Cash'          THEN total_amount ELSE 0 END), 0) AS cash_sales,
                COALESCE(SUM(CASE WHEN payment_method = 'Credit Card'   THEN total_amount ELSE 0 END), 0) AS credit_card_sales,
                COALESCE(SUM(CASE WHEN payment_method = 'GCash/Maya'    THEN total_amount ELSE 0 END), 0) AS gcash_maya_sales,
                COALESCE(SUM(CASE WHEN payment_method = 'Bank Transfer' THEN total_amount ELSE 0 END), 0) AS bank_transfer_sales
            FROM transactions
            WHERE user_id = :user_id
              AND DATE(created_at) = :report_date
              AND status = 'completed'
        ");
        $stmt->execute(['user_id' => $userId, 'report_date' => $date]);
        $payments = $stmt->fetch() ?: [
            'cash_sales' => 0, 'credit_card_sales' => 0,
            'gcash_maya_sales' => 0, 'bank_transfer_sales' => 0,
        ];

        $stmt = $this->db->prepare("
            SELECT
                COUNT(*)                              AS total_voids,
                COALESCE(SUM(total_amount), 0)        AS voided_amount
            FROM transactions
            WHERE user_id = :user_id
              AND DATE(created_at) = :report_date
              AND status IN ('voided', 'deleted')
        ");
        $stmt->execute(['user_id' => $userId, 'report_date' => $date]);
        $voided = $stmt->fetch() ?: ['total_voids' => 0, 'voided_amount' => 0];

        $stmt = $this->db->prepare("
            SELECT p.name, p.unit_type,
                   SUM(ti.quantity) AS qty_sold,
                   SUM(ti.subtotal) AS total_revenue
            FROM transaction_items ti
            INNER JOIN transactions t ON t.id = ti.transaction_id
            INNER JOIN products p ON p.id = ti.product_id
            WHERE t.user_id = :user_id
              AND DATE(t.created_at) = :report_date
              AND t.status = 'completed'
            GROUP BY ti.product_id, p.name, p.unit_type
            ORDER BY qty_sold DESC
            LIMIT 20
        ");
        $stmt->execute(['user_id' => $userId, 'report_date' => $date]);
        $products = $stmt->fetchAll();

        return array_merge($sales, $payments, $voided, ['products' => $products]);
    }

    public function create(array $data): int|false
    {
        $stmt = $this->db->prepare("
            INSERT INTO daily_sales_reports
                (user_id, report_date, submitted_at, total_transactions, total_units_sold,
                 gross_sales, net_sales, vat_collected, average_transaction_value,
                 cash_sales, credit_card_sales, gcash_maya_sales, bank_transfer_sales,
                 total_voids, voided_amount, total_expenses, notes)
            VALUES
                (:user_id, :report_date, NOW(), :total_transactions, :total_units_sold,
                 :gross_sales, :net_sales, :vat_collected, :average_transaction_value,
                 :cash_sales, :credit_card_sales, :gcash_maya_sales, :bank_transfer_sales,
                 :total_voids, :voided_amount, :total_expenses, :notes)
            ON DUPLICATE KEY UPDATE
                submitted_at               = NOW(),
                total_transactions         = VALUES(total_transactions),
                total_units_sold           = VALUES(total_units_sold),
                gross_sales                = VALUES(gross_sales),
                net_sales                  = VALUES(net_sales),
                vat_collected              = VALUES(vat_collected),
                average_transaction_value  = VALUES(average_transaction_value),
                cash_sales                 = VALUES(cash_sales),
                credit_card_sales          = VALUES(credit_card_sales),
                gcash_maya_sales           = VALUES(gcash_maya_sales),
                bank_transfer_sales        = VALUES(bank_transfer_sales),
                total_voids                = VALUES(total_voids),
                voided_amount              = VALUES(voided_amount),
                total_expenses             = VALUES(total_expenses),
                notes                      = VALUES(notes)
        ");

        $ok = $stmt->execute([
            'user_id'                   => $data['user_id'],
            'report_date'               => $data['report_date'],
            'total_transactions'        => (int)   $data['total_transactions'],
            'total_units_sold'          => (int)   $data['total_units_sold'],
            'gross_sales'               => (float) $data['gross_sales'],
            'net_sales'                 => (float) $data['net_sales'],
            'vat_collected'             => (float) $data['vat_collected'],
            'average_transaction_value' => (float) $data['average_transaction_value'],
            'cash_sales'                => (float) $data['cash_sales'],
            'credit_card_sales'         => (float) $data['credit_card_sales'],
            'gcash_maya_sales'          => (float) $data['gcash_maya_sales'],
            'bank_transfer_sales'       => (float) $data['bank_transfer_sales'],
            'total_voids'               => (int)   $data['total_voids'],
            'voided_amount'             => (float) $data['voided_amount'],
            'total_expenses'            => (float) $data['total_expenses'],
            'notes'                     => $data['notes'] !== '' ? $data['notes'] : null,
        ]);

        return $ok ? (int) $this->db->lastInsertId() : false;
    }

    public function all(?string $from, ?string $to): array
    {
        $sql = "
            SELECT dsr.*, u.name AS employee_name
            FROM daily_sales_reports dsr
            INNER JOIN users u ON u.id = dsr.user_id
            WHERE 1=1
        ";
        $params = [];

        if ($from) {
            $sql .= " AND dsr.report_date >= :from_date";
            $params['from_date'] = $from;
        }
        if ($to) {
            $sql .= " AND dsr.report_date <= :to_date";
            $params['to_date'] = $to;
        }

        $sql .= " ORDER BY dsr.report_date DESC, dsr.submitted_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT dsr.*, u.name AS employee_name
            FROM daily_sales_reports dsr
            INNER JOIN users u ON u.id = dsr.user_id
            WHERE dsr.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function forMyReports(int $userId, bool $includeCashiers, ?string $from, ?string $to): array
    {
        $sql = "
            SELECT dsr.*, u.name AS employee_name, u.role AS employee_role
            FROM daily_sales_reports dsr
            INNER JOIN users u ON u.id = dsr.user_id
            WHERE (dsr.user_id = :user_id
        ";

        if ($includeCashiers) {
            $sql .= " OR u.role = 'Cashier'";
        }

        $sql .= ")";
        $params = ['user_id' => $userId];

        if ($from) {
            $sql .= " AND dsr.report_date >= :from_date";
            $params['from_date'] = $from;
        }
        if ($to) {
            $sql .= " AND dsr.report_date <= :to_date";
            $params['to_date'] = $to;
        }

        $sql .= " ORDER BY dsr.report_date DESC, dsr.submitted_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM daily_sales_reports WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
