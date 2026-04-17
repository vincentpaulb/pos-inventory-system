<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Expenses</h1>
        <p class="page-header-desc">Track daily cash on hand and record operational expenses from sales or available cash funds.</p>
    </div>
</div>

<div class="kpi-grid">
    <div class="stat-card">
        <div class="stat-card-bar primary"></div>
        <div class="stat-icon primary"><i class="fas fa-wallet"></i></div>
        <div class="stat-label">Today's Cash on Hand</div>
        <div class="stat-value"><?= e(format_currency($cashOnHand['amount'] ?? 0)) ?></div>
        <div class="stat-meta">
            <?= $cashOnHand ? 'Last updated ' . e(format_datetime($cashOnHand['recorded_at'])) : 'No amount recorded for today yet' ?>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-bar danger"></div>
        <div class="stat-icon danger"><i class="fas fa-money-bill-wave"></i></div>
        <div class="stat-label">Today's Expenses</div>
        <div class="stat-value"><?= e(format_currency($summary['total_amount'] ?? 0)) ?></div>
        <div class="stat-meta"><?= (int) ($summary['total_entries'] ?? 0) ?> entries recorded today</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-bar success"></div>
        <div class="stat-icon success"><i class="fas fa-chart-line"></i></div>
        <div class="stat-label">Sales Funded</div>
        <div class="stat-value"><?= e(format_currency($summary['sales_funded_amount'] ?? 0)) ?></div>
        <div class="stat-meta">Expenses charged against sales</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-bar warning"></div>
        <div class="stat-icon warning"><i class="fas fa-coins"></i></div>
        <div class="stat-label">Cash on Hand Used</div>
        <div class="stat-value"><?= e(format_currency($summary['cash_funded_amount'] ?? 0)) ?></div>
        <div class="stat-meta">Expenses charged against available cash</div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-wallet"></i> Record Today's Cash on Hand</div>
            <div class="card-body">
                <div class="small-muted mb-3">Entry date: <?= e(format_date($today)) ?></div>
                <form method="POST" action="<?= e(base_url('expenses/cash-on-hand')) ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control"
                            name="amount"
                            value="<?= e((string) ($cashOnHand['amount'] ?? '0.00')) ?>"
                            required
                        >
                    </div>
                    <button class="btn btn-primary w-100" type="submit">
                        <i class="fas fa-save"></i> Save Cash on Hand
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span><i class="fas fa-clock-rotate-left"></i> Cash on Hand History</span>
                <span class="badge bg-soft-primary"><?= count($cashHistory) ?> days</span>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr><th>Date</th><th>Amount</th><th>Recorded By</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cashHistory as $row): ?>
                        <tr>
                            <td class="small-muted"><?= e(format_date($row['entry_date'])) ?></td>
                            <td><strong><?= e(format_currency($row['amount'])) ?></strong></td>
                            <td class="small-muted"><?= e($row['recorded_by_name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$cashHistory): ?>
                        <tr><td colspan="3" class="text-center text-muted py-4" style="font-size:.82rem">No cash-on-hand history yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-receipt"></i> Add Expense</div>
            <div class="card-body">
                <form method="POST" action="<?= e(base_url('expenses/store')) ?>" class="row g-3">
                    <?= csrf_field() ?>
                    <div class="col-md-6">
                        <label class="form-label">Expense <span class="text-danger">*</span></label>
                        <?php $selectedExpenseType = (string) old('expense_type'); ?>
                        <select name="expense_type" class="form-select" required>
                            <option value="">Select expense type</option>
                            <?php foreach ($expenseTypes as $type): ?>
                                <option value="<?= e($type) ?>" <?= $selectedExpenseType === $type ? 'selected' : '' ?>><?= e($type) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fund Resource <span class="text-danger">*</span></label>
                        <?php $selectedFundResource = (string) old('fund_resource'); ?>
                        <select name="fund_resource" class="form-select" required>
                            <option value="">Select resource</option>
                            <?php foreach ($fundResources as $resource): ?>
                                <option value="<?= e($resource) ?>" <?= $selectedFundResource === $resource ? 'selected' : '' ?>><?= e($resource) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control"
                            name="amount"
                            value="<?= e((string) old('amount')) ?>"
                            placeholder="0.00"
                            required
                        >
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Optional notes about this expense"><?= e((string) old('description')) ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Time</label>
                        <input
                            type="datetime-local"
                            class="form-control"
                            name="expense_time"
                            value="<?= e((string) old('expense_time')) ?>"
                        >
                        <div class="small-muted mt-2">If left blank, the current timestamp will be used automatically.</div>
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-save"></i> Save Expense
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span><i class="fas fa-list"></i> <?= $isFiltered ? 'Filtered Expenses' : 'Recent Expenses' ?></span>
                <span class="badge bg-soft-primary"><?= count($recentExpenses) ?> entries</span>
            </div>
            <div class="card-body border-bottom py-3">
                <form class="row g-2 align-items-end" method="GET" action="<?= e(base_url('expenses')) ?>">
                    <div class="col-md-3">
                        <label class="form-label">From</label>
                        <input type="date" class="form-control form-control-sm" name="from" value="<?= e($from) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To</label>
                        <input type="date" class="form-control form-control-sm" name="to" value="<?= e($to) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control form-control-sm" name="search" value="<?= e($search) ?>"
                               placeholder="Type, fund resource, description, user...">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button class="btn btn-primary btn-sm w-100" type="submit"><i class="fas fa-check"></i> Apply</button>
                        <a class="btn btn-outline-secondary btn-sm" href="<?= e(base_url('expenses')) ?>"><i class="fas fa-redo"></i></a>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr><th>Time</th><th>Expense</th><th>Fund Resource</th><th>Amount</th><th>Description</th><th>Recorded By</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recentExpenses as $expense): ?>
                        <tr>
                            <td class="small-muted"><?= e(format_datetime($expense['expense_time'])) ?></td>
                            <td style="font-weight:700;font-size:.82rem"><?= e($expense['expense_type']) ?></td>
                            <td><span class="badge <?= $expense['fund_resource'] === 'Sales' ? 'bg-soft-success' : 'bg-soft-warning' ?>"><?= e($expense['fund_resource']) ?></span></td>
                            <td><strong><?= e(format_currency($expense['amount'])) ?></strong></td>
                            <td class="small-muted"><?= e($expense['description'] ?: '—') ?></td>
                            <td class="small-muted"><?= e($expense['recorded_by_name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$recentExpenses): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4" style="font-size:.82rem">No expenses recorded yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
