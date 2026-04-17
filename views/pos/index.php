<?php
$vatLabel = system_vat_label();
$vatRate = system_vat_rate();
$jsProducts = json_encode(array_map(function ($p) {
    return [
        'id' => (int) $p['id'],
        'name' => (string) $p['name'],
        'price' => (float) $p['selling_price'],
        'stock' => (int) $p['stock_quantity'],
        'cat' => (string) ($p['category_name'] ?? ''),
        'fmt' => format_currency($p['selling_price']),
    ];
}, $products), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
?>

<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Point of Sale</h1>
        <p class="page-header-desc">Search inventory fast, build the cart, review VAT breakdown, and complete checkout with a cleaner cashier workflow.</p>
    </div>
    <div class="page-header-actions">
        <div class="topbar-chip"><i class="fas fa-shopping-bag"></i> <?= count($products) ?> products available</div>
        <div class="topbar-chip"><i class="fas fa-receipt"></i> VAT ready</div>
        <button type="button" class="btn btn-primary btn-sm pos-header-action-btn" id="btnDailySalesReport">
            <i class="fas fa-file-lines"></i> Daily Sales Report
        </button>
    </div>
</div>

<section class="pos-shell">
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card pos-card h-100">
            <div class="card-header">
                <span><i class="fas fa-search"></i> Product Search</span>
                <span class="small-muted">Tap a product card to add it to the cart instantly</span>
            </div>
            <div class="card-body pos-browser">
                <div class="pos-search-wrap">
                    <div class="pos-search-box">
                        <i class="fas fa-magnifying-glass pos-search-icon"></i>
                        <input
                            type="text"
                            id="posSearch"
                            class="form-control pos-search-input"
                            placeholder="Search by product name or barcode..."
                            autocomplete="off"
                        >
                    </div>
                    <div class="pos-search-meta">
                        <span class="badge bg-soft-primary" id="posCatalogCount"><?= count($products) ?> products</span>
                    </div>
                </div>
                <div id="posGrid" class="product-grid pos-product-grid"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card pos-card pos-cart-card h-100">
            <div class="card-header">
                <span><i class="fas fa-shopping-cart"></i> Cart</span>
                <span class="badge bg-soft-primary" id="cartCount">0 items</span>
            </div>
            <div class="card-body d-flex flex-column pos-cart-body" style="gap:14px">
                <form method="POST" action="<?= e(base_url('pos/checkout')) ?>" id="checkoutForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="items" id="cartItemsInput" value="[]">

                    <div class="card-soft">
                        <div class="section-title"><i class="fas fa-user"></i> Customer Information</div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Customer Name</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="customer_name"
                                    id="customerNameInput"
                                    value="<?= e((string) old('customer_name')) ?>"
                                    placeholder="Walk-in Customer"
                                >
                            </div>
                            <div class="col-12">
                                <label class="form-label">Customer Address</label>
                                <textarea
                                    class="form-control"
                                    name="customer_address"
                                    id="customerAddressInput"
                                    rows="2"
                                    placeholder="No Address Provided"
                                ><?= e((string) old('customer_address')) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="pos-cart-table-wrap">
                        <table class="table align-middle mb-0" style="font-size:.78rem">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th style="width:80px">Qty</th>
                                    <th style="width:90px">Price</th>
                                    <th style="width:90px">Sub</th>
                                    <th style="width:36px"></th>
                                </tr>
                            </thead>
                            <tbody id="cartBody"></tbody>
                        </table>
                    </div>

                    <div class="card-soft pos-summary-card">
                        <div class="pos-summary-row">
                            <span>Subtotal</span>
                            <span id="cartSubtotal" style="font-weight:600">&#8369;0.00</span>
                        </div>
                        <div class="pos-summary-row">
                            <span>Net Price</span>
                            <span id="cartNetPrice" style="font-weight:600">&#8369;0.00</span>
                        </div>
                        <div class="pos-summary-row">
                            <span><?= e($vatLabel) ?></span>
                            <span id="cartVatAmount" style="font-weight:600">&#8369;0.00</span>
                        </div>
                        <hr style="border-color:var(--border);margin:10px 0">
                        <div class="pos-summary-row pos-summary-total">
                            <span style="font-weight:700">Total Price</span>
                            <strong id="cartTotal">&#8369;0.00</strong>
                        </div>
                        <div class="pos-summary-row">
                            <span>Change</span>
                            <strong id="cartChange" class="pos-change-value">&#8369;0.00</strong>
                        </div>
                    </div>

                    <div class="pos-payment-block">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <?php $selectedPaymentMethod = (string) old('payment_method', 'Cash'); ?>
                                <select class="form-select" name="payment_method" id="paymentMethodInput" required>
                                    <?php foreach ($paymentMethods as $paymentMethod): ?>
                                        <option value="<?= e($paymentMethod) ?>" <?= $selectedPaymentMethod === $paymentMethod ? 'selected' : '' ?>><?= e($paymentMethod) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6" id="referenceFieldWrap" style="<?= $selectedPaymentMethod === 'Cash' ? 'display:none' : '' ?>">
                                <label class="form-label">Reference # <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="reference_no"
                                    id="referenceNoInput"
                                    value="<?= e((string) old('reference_no')) ?>"
                                    placeholder="Enter reference number"
                                >
                            </div>
                            <div class="col-12">
                                <label class="form-label">Payment Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="font-weight:800">&#8369;</span>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        class="form-control"
                                        name="payment_amount"
                                        id="paymentInput"
                                        placeholder="0.00"
                                        value="<?= e((string) old('payment_amount')) ?>"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 btn-lg">
                        <i class="fas fa-check"></i> Complete Sale
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <span><i class="fas fa-clock-rotate-left"></i> Purchase History</span>
        <span class="badge bg-soft-primary"><?= count($purchaseHistory) ?> records</span>
    </div>
    <div class="card-body border-bottom">
        <form method="GET" action="<?= e(base_url('pos')) ?>" class="row g-2 align-items-end" data-live-search="true" data-live-render="purchaseHistory">
            <div class="col-md-4">
                <label class="form-label">Search Purchase History</label>
                <input
                    type="text"
                    name="history_search"
                    class="form-control"
                    value="<?= e($historySearch ?? '') ?>"
                    placeholder="Invoice, customer, cashier, payment method, reference, or status"
                >
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Customer</th>
                    <th>Payment</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody
                id="purchaseHistoryTableBody"
                data-base-url="<?= e(base_url()) ?>"
                data-csrf-token="<?= e(csrf_token()) ?>"
            >
                <?php foreach ($purchaseHistory as $row): ?>
                    <?php
                    $statusClass = match ($row['status']) {
                        'voided' => 'bg-soft-warning',
                        'deleted' => 'bg-soft-danger',
                        default => 'bg-soft-success',
                    };
                    ?>
                    <tr>
                        <td><span class="badge bg-soft-primary"><?= e($row['invoice_no']) ?></span></td>
                        <td>
                            <div style="font-size:.82rem;font-weight:700"><?= e($row['customer_name'] ?: 'Walk-in Customer') ?></div>
                            <div class="small-muted"><?= e($row['customer_address'] ?: 'No address provided') ?></div>
                        </td>
                        <td>
                            <div style="font-size:.82rem;font-weight:700"><?= e($row['payment_method'] ?: 'Cash') ?></div>
                            <div class="small-muted"><?= e($row['reference_no'] ?: '-') ?></div>
                        </td>
                        <td><strong><?= e(format_currency($row['total_amount'])) ?></strong></td>
                        <td><span class="badge <?= $statusClass ?>"><?= e(ucfirst((string) $row['status'])) ?></span></td>
                        <td class="small-muted"><?= e(format_datetime($row['created_at'])) ?></td>
                        <td>
                            <div class="action-group">
                                <a class="btn btn-sm btn-outline-primary btn-icon" href="<?= e(base_url('pos/receipt?id=' . (int) $row['id'])) ?>" title="View receipt" aria-label="View receipt">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($row['status'] === 'completed'): ?>
                                    <form method="POST" action="<?= e(base_url('pos/void')) ?>" class="js-confirm-form" data-confirm-message="Void this transaction and restore its stock quantities?" data-confirm-button="Void">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                        <button class="btn btn-sm btn-outline-warning btn-icon" title="Void transaction" aria-label="Void transaction">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <?php if ($row['status'] === 'voided'): ?>
                                    <form method="POST" action="<?= e(base_url('pos/delete')) ?>" class="js-confirm-form" data-confirm-message="Delete this voided transaction from active history?" data-confirm-button="Delete">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete transaction" aria-label="Delete transaction">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$purchaseHistory): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4" style="font-size:.82rem">No purchase history yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</section>

<script>
(function () {
    var VAT_RATE = <?= json_encode($vatRate) ?>;
    var ALL_PRODUCTS = <?= $jsProducts ?>;
    var SEARCH_URL = <?= json_encode(base_url('pos/search')) ?>;
    var cart = [];

    var grid = document.getElementById('posGrid');
    var cartBody = document.getElementById('cartBody');
    var cartCountEl = document.getElementById('cartCount');
    var posCatalogCountEl = document.getElementById('posCatalogCount');
    var subtotalEl = document.getElementById('cartSubtotal');
    var netPriceEl = document.getElementById('cartNetPrice');
    var vatAmountEl = document.getElementById('cartVatAmount');
    var totalEl = document.getElementById('cartTotal');
    var changeEl = document.getElementById('cartChange');
    var paymentInput = document.getElementById('paymentInput');
    var paymentMethodInput = document.getElementById('paymentMethodInput');
    var referenceFieldWrap = document.getElementById('referenceFieldWrap');
    var referenceNoInput = document.getElementById('referenceNoInput');
    var customerNameInput = document.getElementById('customerNameInput');
    var itemsInput = document.getElementById('cartItemsInput');
    var form = document.getElementById('checkoutForm');
    var searchInput = document.getElementById('posSearch');
    var submitButton = form.querySelector('button[type="submit"]');

    function requiresExactPaymentMethod() {
        return paymentMethodInput && paymentMethodInput.value !== 'Cash';
    }

    function syncPaymentAmount(total) {
        if (!paymentInput) {
            return;
        }

        if (requiresExactPaymentMethod()) {
            paymentInput.value = Number(total || 0).toFixed(2);
            paymentInput.readOnly = true;
        } else {
            paymentInput.readOnly = false;
        }
    }

    function syncPaymentReferenceVisibility() {
        if (!paymentMethodInput || !referenceFieldWrap || !referenceNoInput) {
            return;
        }

        var requiresReference = requiresExactPaymentMethod();
        referenceFieldWrap.style.display = requiresReference ? '' : 'none';
        referenceNoInput.required = requiresReference;

        if (!requiresReference) {
            referenceNoInput.value = '';
        }
    }

    function fmt(n) {
        return '\u20b1' + Number(n || 0).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function cartTotal() {
        return cart.reduce(function (sum, item) {
            return sum + item.price * item.qty;
        }, 0);
    }

    function escHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function renderGrid(products) {
        if (posCatalogCountEl) {
            posCatalogCountEl.textContent = products.length + (products.length === 1 ? ' product' : ' products');
        }

        if (!products.length) {
            grid.innerHTML = '<div style="color:var(--muted);font-size:.80rem;grid-column:1/-1;padding:14px 0">No products found.</div>';
            return;
        }

        grid.innerHTML = '';
        products.forEach(function (product) {
            var card = document.createElement('div');
            card.className = 'product-card pos-product-card';
            card.innerHTML =
                '<div class="pos-product-top">' +
                    '<strong class="pos-product-name">' + escHtml(product.name) + '</strong>' +
                    '<span class="badge bg-soft-primary">Stock ' + product.stock + '</span>' +
                '</div>' +
                '<div class="small-muted">' + escHtml(product.cat) + '</div>' +
                '<div class="pos-product-bottom">' +
                    '<div class="product-card-price">' + fmt(product.price) + '</div>' +
                    '<div class="small-muted" style="font-size:.67rem">Tap to add</div>' +
                '</div>';
            card.addEventListener('click', function () {
                addToCart(product);
            });
            grid.appendChild(card);
        });
    }

    function addToCart(product) {
        var found = null;
        for (var i = 0; i < cart.length; i++) {
            if (cart[i].id === product.id) {
                found = cart[i];
                break;
            }
        }

        if (found) {
            if (found.qty < found.stock) {
                found.qty++;
            }
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                price: product.price,
                stock: product.stock,
                qty: 1
            });
        }

        renderCart();
    }

    function renderCart() {
        var total = cartTotal();
        var netPrice = total / (1 + VAT_RATE);
        var vatAmount = total - netPrice;

        syncPaymentAmount(total);

        cartCountEl.textContent = cart.length + (cart.length === 1 ? ' item' : ' items');

        if (cart.length === 0) {
            cartBody.innerHTML =
                '<tr><td colspan="5" style="text-align:center;color:var(--muted);padding:20px;font-size:.78rem">' +
                'No items yet - click a product to add it</td></tr>';
        } else {
            cartBody.innerHTML = '';
            cart.forEach(function (item, idx) {
                var tr = document.createElement('tr');
                tr.innerHTML =
                    '<td style="font-weight:600;word-break:break-word;max-width:120px">' + escHtml(item.name) + '</td>' +
                    '<td><input type="number" min="1" max="' + item.stock + '" value="' + item.qty + '" ' +
                        'data-idx="' + idx + '" class="qty-inp form-control form-control-sm" ' +
                        'style="width:62px;font-size:.76rem;padding:.26rem .44rem"></td>' +
                    '<td style="white-space:nowrap">' + fmt(item.price) + '</td>' +
                    '<td style="font-weight:700;white-space:nowrap">' + fmt(item.price * item.qty) + '</td>' +
                    '<td><button type="button" data-idx="' + idx + '" class="rm-btn" ' +
                        'style="background:none;border:1px solid var(--danger);color:var(--danger);' +
                        'border-radius:6px;width:26px;height:26px;cursor:pointer;font-size:14px;' +
                        'line-height:1;padding:0">x</button></td>';
                cartBody.appendChild(tr);
            });
        }

        var pay = parseFloat(paymentInput.value) || 0;
        subtotalEl.textContent = fmt(total);
        netPriceEl.textContent = fmt(netPrice);
        vatAmountEl.textContent = fmt(vatAmount);
        totalEl.textContent = fmt(total);
        changeEl.textContent = fmt(Math.max(pay - total, 0));

        itemsInput.value = JSON.stringify(cart.map(function (item) {
            return { product_id: item.id, quantity: item.qty };
        }));

        if (submitButton) {
            submitButton.disabled = cart.length === 0;
        }
    }

    cartBody.addEventListener('change', function (event) {
        if (!event.target.classList.contains('qty-inp')) {
            return;
        }

        var idx = parseInt(event.target.dataset.idx, 10);
        var quantity = Math.max(1, Math.min(parseInt(event.target.value, 10) || 1, cart[idx].stock));
        cart[idx].qty = quantity;
        event.target.value = quantity;
        renderCart();
    });

    cartBody.addEventListener('click', function (event) {
        var btn = event.target.closest('.rm-btn');
        if (!btn) {
            return;
        }

        cart.splice(parseInt(btn.dataset.idx, 10), 1);
        renderCart();
    });

    paymentInput.addEventListener('input', function () {
        if (requiresExactPaymentMethod()) {
            return;
        }

        var total = cartTotal();
        var pay = parseFloat(this.value) || 0;
        changeEl.textContent = fmt(Math.max(pay - total, 0));
    });

    if (paymentMethodInput) {
        paymentMethodInput.addEventListener('change', function () {
            syncPaymentReferenceVisibility();
            syncPaymentAmount(cartTotal());
            renderCart();
        });
    }

    form.addEventListener('submit', function (event) {
        if (cart.length === 0) {
            event.preventDefault();
            alert('Cart is empty.\nClick a product card to add it first.');
            return;
        }

        var total = cartTotal();
        var pay = parseFloat(paymentInput.value) || 0;

        if (pay <= 0) {
            event.preventDefault();
            alert('Please enter the payment amount.');
            paymentInput.focus();
            return;
        }

        if (pay < total) {
            event.preventDefault();
            alert('Payment (' + fmt(pay) + ') is less than the total (' + fmt(total) + ').');
            paymentInput.focus();
            return;
        }

        if (paymentMethodInput && paymentMethodInput.value !== 'Cash' && referenceNoInput && !referenceNoInput.value.trim()) {
            event.preventDefault();
            alert('Reference # is required for the selected payment method.');
            referenceNoInput.focus();
        }
    });

    var searchTimer;
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        var query = this.value.trim();

        if (query === '') {
            renderGrid(ALL_PRODUCTS);
            return;
        }

        searchTimer = setTimeout(function () {
            fetch(SEARCH_URL + '?search=' + encodeURIComponent(query), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    renderGrid(data.map(function (product) {
                        return {
                            id: product.id,
                            name: product.name,
                            price: parseFloat(product.selling_price),
                            stock: product.stock_quantity,
                            cat: product.category_name || ''
                        };
                    }));
                })
                .catch(function (error) {
                    console.error('Search error:', error);
                });
        }, 280);
    });

    renderGrid(ALL_PRODUCTS);
    syncPaymentReferenceVisibility();
    renderCart();
})();
</script>

<!-- Daily Sales Report Modal -->
<div class="modal fade" id="dailySalesReportModal" tabindex="-1" aria-labelledby="dsrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dsrModalLabel"><i class="fas fa-file-lines"></i> Daily Sales Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="dsrModalBody">
                <div class="text-center py-5 text-muted" id="dsrLoading">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i><br>Loading report data...
                </div>
                <div id="dsrContent" style="display:none"></div>
            </div>
            <div class="modal-footer" id="dsrModalFooter" style="display:none">
                <form method="POST" action="<?= e(base_url('pos/daily-report')) ?>" id="dsrSubmitForm">
                    <?= csrf_field() ?>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <div style="flex:1;min-width:220px">
                            <input type="text" class="form-control form-control-sm" name="notes"
                                placeholder="Optional notes for this report..."
                                style="font-size:.82rem">
                        </div>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Submit Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var DSR_PREVIEW_URL = <?= json_encode(base_url('pos/daily-report')) ?>;
    var VAT_LABEL = <?= json_encode($vatLabel) ?>;

    function fmt(n) {
        return '\u20b1' + Number(n || 0).toLocaleString('en-PH', {
            minimumFractionDigits: 2, maximumFractionDigits: 2
        });
    }

    function escHtml(s) {
        return String(s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function buildReportHtml(d) {
        var now = new Date();
        var timeStr = now.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

        var productsRows = '';
        if (d.products && d.products.length) {
            d.products.forEach(function (p) {
                productsRows +=
                    '<tr>' +
                    '<td>' + escHtml(p.name) + '</td>' +
                    '<td class="text-center">' + escHtml(p.unit_type) + '</td>' +
                    '<td class="text-center">' + escHtml(p.qty_sold) + '</td>' +
                    '<td class="text-end">' + fmt(p.total_revenue) + '</td>' +
                    '</tr>';
            });
        } else {
            productsRows = '<tr><td colspan="4" class="text-center text-muted py-3" style="font-size:.82rem">No sales recorded today.</td></tr>';
        }

        return '<div style="font-size:.82rem">' +

            '<div class="card mb-3"><div class="card-header"><i class="fas fa-circle-info"></i> Header Information</div>' +
            '<div class="card-body"><div class="row g-2">' +
            '<div class="col-md-4"><span class="text-muted">Report Date</span><div style="font-weight:700">' + escHtml(d.report_date) + '</div></div>' +
            '<div class="col-md-4"><span class="text-muted">Employee</span><div style="font-weight:700">' + escHtml(d.employee_name) + ' (ID #' + escHtml(d.employee_id) + ')</div></div>' +
            '<div class="col-md-4"><span class="text-muted">Submission Time</span><div style="font-weight:700">' + timeStr + '</div></div>' +
            '</div></div></div>' +

            '<div class="row g-3 mb-3">' +
            '<div class="col-md-6"><div class="card h-100"><div class="card-header"><i class="fas fa-chart-bar"></i> Sales Performance</div>' +
            '<div class="card-body"><table class="table table-sm mb-0">' +
            '<tr><td class="text-muted">Total Transactions</td><td class="text-end fw-bold">' + escHtml(d.total_transactions) + '</td></tr>' +
            '<tr><td class="text-muted">Total Units Sold</td><td class="text-end fw-bold">' + escHtml(d.total_units_sold) + '</td></tr>' +
            '<tr><td class="text-muted">Gross Sales</td><td class="text-end fw-bold">' + fmt(d.gross_sales) + '</td></tr>' +
            '<tr><td class="text-muted">Net Sales (excl. ' + escHtml(VAT_LABEL) + ')</td><td class="text-end fw-bold">' + fmt(d.net_sales) + '</td></tr>' +
            '<tr><td class="text-muted">' + escHtml(VAT_LABEL) + ' Collected</td><td class="text-end fw-bold">' + fmt(d.vat_collected) + '</td></tr>' +
            '<tr><td class="text-muted">Avg Transaction Value</td><td class="text-end fw-bold">' + fmt(d.average_transaction_value) + '</td></tr>' +
            '</table></div></div></div>' +

            '<div class="col-md-6"><div class="card h-100"><div class="card-header"><i class="fas fa-credit-card"></i> Payment Breakdown</div>' +
            '<div class="card-body"><table class="table table-sm mb-0">' +
            '<tr><td class="text-muted">Cash</td><td class="text-end fw-bold">' + fmt(d.cash_sales) + '</td></tr>' +
            '<tr><td class="text-muted">Credit Card</td><td class="text-end fw-bold">' + fmt(d.credit_card_sales) + '</td></tr>' +
            '<tr><td class="text-muted">GCash / Maya</td><td class="text-end fw-bold">' + fmt(d.gcash_maya_sales) + '</td></tr>' +
            '<tr><td class="text-muted">Bank Transfer</td><td class="text-end fw-bold">' + fmt(d.bank_transfer_sales) + '</td></tr>' +
            '</table></div></div></div>' +
            '</div>' +

            '<div class="row g-3 mb-3">' +
            '<div class="col-md-6"><div class="card h-100"><div class="card-header"><i class="fas fa-rotate-left"></i> Adjustments &amp; Tax</div>' +
            '<div class="card-body"><table class="table table-sm mb-0">' +
            '<tr><td class="text-muted">Voided Transactions</td><td class="text-end fw-bold">' + escHtml(d.total_voids) + '</td></tr>' +
            '<tr><td class="text-muted">Voided Amount</td><td class="text-end fw-bold text-warning">' + fmt(d.voided_amount) + '</td></tr>' +
            '<tr><td class="text-muted">Discounts Applied</td><td class="text-end fw-bold">N/A</td></tr>' +
            '<tr><td class="text-muted">Total ' + escHtml(VAT_LABEL) + '</td><td class="text-end fw-bold">' + fmt(d.vat_collected) + '</td></tr>' +
            '</table></div></div></div>' +

            '<div class="col-md-6"><div class="card h-100"><div class="card-header"><i class="fas fa-money-bill-wave"></i> Financial Summary</div>' +
            '<div class="card-body"><table class="table table-sm mb-0">' +
            '<tr><td class="text-muted">Total Daily Expenses</td><td class="text-end fw-bold text-danger">' + fmt(d.total_expenses) + '</td></tr>' +
            '<tr><td class="text-muted">Stock Units Deducted</td><td class="text-end fw-bold">' + escHtml(d.total_units_sold) + ' units</td></tr>' +
            '<tr><td class="text-muted">Net Revenue (after expenses)</td><td class="text-end fw-bold text-success">' + fmt(Math.max(0, d.gross_sales - d.total_expenses)) + '</td></tr>' +
            '</table></div></div></div>' +
            '</div>' +

            '<div class="card"><div class="card-header"><i class="fas fa-list"></i> Items Sold Today</div>' +
            '<div class="table-responsive"><table class="table table-sm mb-0">' +
            '<thead><tr><th>Product</th><th class="text-center">Unit</th><th class="text-center">Qty Sold</th><th class="text-end">Revenue</th></tr></thead>' +
            '<tbody>' + productsRows + '</tbody>' +
            '</table></div></div>' +

            '</div>';
    }

    document.getElementById('btnDailySalesReport').addEventListener('click', function () {
        var modal = new bootstrap.Modal(document.getElementById('dailySalesReportModal'));
        var loadingEl = document.getElementById('dsrLoading');
        var contentEl = document.getElementById('dsrContent');
        var footerEl  = document.getElementById('dsrModalFooter');

        loadingEl.style.display = '';
        contentEl.style.display = 'none';
        contentEl.innerHTML = '';
        footerEl.style.display = 'none';

        modal.show();

        fetch(DSR_PREVIEW_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                loadingEl.style.display = 'none';
                contentEl.innerHTML = buildReportHtml(data);
                contentEl.style.display = '';
                footerEl.style.display = '';
            })
            .catch(function () {
                loadingEl.innerHTML = '<i class="fas fa-triangle-exclamation fa-2x text-danger mb-2"></i><br>Failed to load report data.';
            });
    });
})();
</script>
