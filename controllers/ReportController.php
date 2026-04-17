<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/Transaction.php';
require_once BASE_PATH . '/models/DailySalesReport.php';

class ReportController
{
    private Transaction $transactions;
    private DailySalesReport $dailyReports;

    public function __construct()
    {
        $this->transactions = new Transaction();
        $this->dailyReports = new DailySalesReport();
    }

    public function index(): void
    {
        require_module_access('reports');

        $from = clean_input($_GET['from'] ?? '');
        $to   = clean_input($_GET['to']   ?? '');
        $tab  = clean_input($_GET['tab']  ?? 'daily-reports');

        view('reports/index', [
            'title'        => 'Sales Reports',
            'daily'        => $this->transactions->salesSummary('day'),
            'weekly'       => $this->transactions->salesSummary('week'),
            'monthly'      => $this->transactions->salesSummary('month'),
            'histories'    => $this->transactions->filteredHistory($from ?: null, $to ?: null),
            'dailyReports' => $this->dailyReports->all($from ?: null, $to ?: null),
            'from'         => $from,
            'to'           => $to,
            'tab'          => $tab,
        ]);
    }

    public function viewDailyReport(): void
    {
        require_module_access(['reports', 'my-reports']);

        $id     = (int) ($_GET['id'] ?? 0);
        $report = $this->dailyReports->find($id);

        if (!$report) {
            flash('error', 'Daily sales report not found.');
            redirect('reports');
            return;
        }

        view('reports/daily_report_view', [
            'title'  => 'Daily Sales Report — ' . $report['report_date'],
            'report' => $report,
        ]);
    }

    public function exportCsv(): void
    {
        require_module_access('reports');

        $from = clean_input($_GET['from'] ?? '');
        $to   = clean_input($_GET['to']   ?? '');
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

    public function myReports(): void
    {
        require_module_access('my-reports');

        $from = clean_input($_GET['from'] ?? '');
        $to   = clean_input($_GET['to']   ?? '');
        $role = current_role();

        $includeCashiers = $role === 'Sales Manager';

        view('reports/my_reports', [
            'title'          => 'My Daily Sales Reports',
            'reports'        => $this->dailyReports->forMyReports(
                (int) auth_user()['id'],
                $includeCashiers,
                $from ?: null,
                $to ?: null
            ),
            'from'           => $from,
            'to'             => $to,
            'canSeeOthers'   => $includeCashiers,
        ]);
    }

    public function deleteDailyReport(): void
    {
        require_module_access(['reports', 'my-reports']);
        verify_csrf();

        $id     = (int) ($_POST['id'] ?? 0);
        $report = $this->dailyReports->find($id);

        if (!$report) {
            flash('error', 'Daily sales report not found.');
            redirect('reports');
            return;
        }

        $this->dailyReports->delete($id);
        flash('success', 'Daily sales report deleted.');
        $redirectTo = clean_input($_POST['redirect'] ?? '') === 'my-reports' ? 'my-reports' : 'reports?tab=daily-reports';
        redirect($redirectTo);
    }

    public function exportDailyReportsCsv(): void
    {
        require_module_access('reports');

        $from = clean_input($_GET['from'] ?? '');
        $to   = clean_input($_GET['to']   ?? '');
        $rows = $this->dailyReports->all($from ?: null, $to ?: null);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="daily_sales_reports_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'Date', 'Employee', 'Gross Sales', 'Net Sales', system_vat_label('Tax') . ' Collected',
            'Transactions', 'Units Sold', 'Avg Transaction',
            'Cash', 'Credit Card', 'GCash/Maya', 'Bank Transfer',
            'Voids', 'Voided Amount', 'Expenses', 'Submitted At',
        ]);

        foreach ($rows as $row) {
            fputcsv($output, [
                $row['report_date'],
                $row['employee_name'],
                $row['gross_sales'],
                $row['net_sales'],
                $row['vat_collected'],
                $row['total_transactions'],
                $row['total_units_sold'],
                $row['average_transaction_value'],
                $row['cash_sales'],
                $row['credit_card_sales'],
                $row['gcash_maya_sales'],
                $row['bank_transfer_sales'],
                $row['total_voids'],
                $row['voided_amount'],
                $row['total_expenses'],
                $row['submitted_at'],
            ]);
        }

        fclose($output);
        exit;
    }
}
