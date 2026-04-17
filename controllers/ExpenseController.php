<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/Expense.php';
require_once BASE_PATH . '/models/ActivityLog.php';

class ExpenseController
{
    private const FUND_RESOURCES = ['Sales', 'Cash on Hand'];
    private const EXPENSE_TYPES = [
        'Cash Advance',
        'Miscellaneous - Food',
        'Miscellaneous - Supplies',
        'Rental',
        'Bill - Electricity',
        'Bill - Water',
        'Bill - Internet',
        'Travel - Gasoline',
        'Travel - Food',
    ];

    private Expense $expenses;
    private ActivityLog $logs;

    public function __construct()
    {
        $this->expenses = new Expense();
        $this->logs = new ActivityLog();
    }

    public function index(): void
    {
        require_module_access('expenses');

        $today  = date('Y-m-d');
        $from   = clean_input($_GET['from']   ?? '');
        $to     = clean_input($_GET['to']     ?? '');
        $search = clean_input($_GET['search'] ?? '');

        $isFiltered = $from !== '' || $to !== '' || $search !== '';

        view('expenses/index', [
            'title'          => 'Expenses',
            'today'          => $today,
            'expenseTypes'   => self::EXPENSE_TYPES,
            'fundResources'  => self::FUND_RESOURCES,
            'cashOnHand'     => $this->expenses->cashOnHandForDate($today),
            'summary'        => $this->expenses->summaryForDate($today),
            'recentExpenses' => $isFiltered
                ? $this->expenses->filteredExpenses($from ?: null, $to ?: null, $search)
                : $this->expenses->recentExpenses(20),
            'cashHistory'    => $this->expenses->recentCashOnHandRecords(7),
            'from'           => $from,
            'to'             => $to,
            'search'         => $search,
            'isFiltered'     => $isFiltered,
        ]);
    }

    public function saveCashOnHand(): void
    {
        require_module_access('expenses');
        verify_csrf();

        $amount = sanitize_number($_POST['amount'] ?? 0);
        $date = date('Y-m-d');

        if ($error = validate_positive_number('amount', $amount, 'Cash on hand amount')) {
            flash('error', $error);
            redirect('expenses');
        }

        if (!$this->expenses->upsertCashOnHand($date, $amount, (int) auth_user()['id'])) {
            flash('error', 'Unable to save cash on hand. Please try again.');
            redirect('expenses');
        }

        $this->logs->log((int) auth_user()['id'], 'cash_on_hand_update', 'Updated cash on hand for ' . $date . ' to ' . format_currency($amount));

        flash('success', 'Cash on hand saved successfully.');
        redirect('expenses');
    }

    public function store(): void
    {
        require_module_access('expenses');
        verify_csrf();

        $payload = [
            'expense_type' => clean_input($_POST['expense_type'] ?? ''),
            'fund_resource' => clean_input($_POST['fund_resource'] ?? ''),
            'amount_input' => clean_input($_POST['amount'] ?? ''),
            'amount' => sanitize_number($_POST['amount'] ?? 0),
            'description' => clean_input($_POST['description'] ?? ''),
            'expense_time_input' => clean_input($_POST['expense_time'] ?? ''),
        ];

        set_old([
            'expense_type' => $payload['expense_type'],
            'fund_resource' => $payload['fund_resource'],
            'amount' => $payload['amount_input'],
            'description' => $payload['description'],
            'expense_time' => $payload['expense_time_input'],
        ]);

        $errors = validate_required(
            [
                'expense_type' => $payload['expense_type'],
                'fund_resource' => $payload['fund_resource'],
                'amount' => $payload['amount_input'],
            ],
            [
                'expense_type' => 'Expense',
                'fund_resource' => 'Fund resource',
                'amount' => 'Amount',
            ]
        );

        if (!in_array($payload['expense_type'], self::EXPENSE_TYPES, true)) {
            $errors['expense_type'] = 'Selected expense type is invalid.';
        }

        if (!in_array($payload['fund_resource'], self::FUND_RESOURCES, true)) {
            $errors['fund_resource'] = 'Selected fund resource is invalid.';
        }

        if ($error = validate_positive_number('amount', $payload['amount'], 'Amount')) {
            $errors['amount'] = $error;
        }

        $expenseTime = $this->normalizeExpenseTime($payload['expense_time_input']);
        if ($expenseTime === null) {
            $errors['expense_time'] = 'Expense time must be a valid date and time.';
        }

        if ($errors) {
            flash('error', implode(' ', $errors));
            redirect('expenses');
        }

        $data = [
            'user_id' => (int) auth_user()['id'],
            'expense_type' => $payload['expense_type'],
            'fund_resource' => $payload['fund_resource'],
            'amount' => $payload['amount'],
            'description' => $payload['description'],
            'expense_time' => $expenseTime,
        ];

        if (!$this->expenses->createExpense($data)) {
            flash('error', 'Unable to save expense. Please try again.');
            redirect('expenses');
        }

        clear_old();
        $this->logs->log(
            (int) auth_user()['id'],
            'expense_create',
            'Added expense ' . $data['expense_type'] . ' from ' . $data['fund_resource'] . ' for ' . format_currency($data['amount'])
        );

        flash('success', 'Expense recorded successfully.');
        redirect('expenses');
    }

    private function normalizeExpenseTime(string $value): ?string
    {
        if ($value === '') {
            return date('Y-m-d H:i:s');
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return null;
        }

        return date('Y-m-d H:i:s', $timestamp);
    }
}
