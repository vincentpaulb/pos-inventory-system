<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/Transaction.php';

class ReportController
{
    private Transaction $transactions;

    public function __construct()
    {
        $this->transactions = new Transaction();
    }

    public function index(): void
    {
        require_auth();

        $from = clean_input($_GET['from'] ?? '');
        $to = clean_input($_GET['to'] ?? '');

        view('reports/index', [
            'title' => 'Sales Reports',
            'daily' => $this->transactions->salesSummary('day'),
            'weekly' => $this->transactions->salesSummary('week'),
            'monthly' => $this->transactions->salesSummary('month'),
            'histories' => $this->transactions->filteredHistory($from ?: null, $to ?: null),
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function exportCsv(): void
    {
        require_auth();

        $from = clean_input($_GET['from'] ?? '');
        $to = clean_input($_GET['to'] ?? '');
        $rows = $this->transactions->filteredHistory($from ?: null, $to ?: null);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="sales_report_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Invoice No', 'Cashier', 'Total', 'Payment', 'Change', 'Date']);

        foreach ($rows as $row) {
            fputcsv($output, [
                $row['invoice_no'],
                $row['cashier_name'],
                $row['total_amount'],
                $row['payment_amount'],
                $row['change_amount'],
                $row['created_at'],
            ]);
        }

        fclose($output);
        exit;
    }
}
