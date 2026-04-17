<?php
$jsProducts = json_encode(array_map(function ($p) {
    return [
        'id' => (int) $p['id'],
        'name' => (string) $p['name'],
        'price' => (float) $p['selling_price'],
        'stock' => (int) $p['stock_quantity'],
        'cat' => (string) ($p['category_name'] ?? ''),
        'code' => (string) ($p['barcode'] ?? ''),
    ];
}, $products), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
?>

<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Quotation</h1>
        <p class="page-header-desc">Create customer quotations for parts with an optional service repair line item.</p>
    </div>
    <div class="page-header-actions">
        <div class="topbar-chip" id="quotationCountChip"><i class="fas fa-receipt"></i> <?= count($quotations) ?> recent quotations</div>
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#quotationBuilderModal">
            <i class="fas fa-plus"></i> New Quotation
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span><i class="fas fa-clock"></i> Recent Quotations</span>
        <span class="badge bg-soft-primary" id="quotationCountBadge"><?= count($quotations) ?> records</span>
    </div>
    <div class="card-body" style="border-bottom:1px solid var(--border)">
        <form class="row g-2 align-items-end" method="GET" action="<?= e(base_url('quotations')) ?>" data-live-search="true" data-live-render="quotations">
            <div class="col-md-4">
                <label class="form-label">Search Quotations</label>
                <input type="text" name="search" class="form-control" placeholder="Search by quote no., customer, or contact" value="<?= e($search ?? '') ?>">
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Quote #</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="quotationTableBody" data-base-url="<?= e(base_url()) ?>" data-csrf-token="<?= e(csrf_token()) ?>">
                <?php foreach ($quotations as $quote): ?>
                    <tr>
                        <td>
                            <div><span class="badge bg-soft-primary"><?= e($quote['quote_no']) ?></span></div>
                            <div class="small-muted"><?= e(format_date($quote['created_at'])) ?></div>
                        </td>
                        <td>
                            <div style="font-weight:700;font-size:.82rem"><?= e($quote['customer_name']) ?></div>
                            <div class="small-muted"><?= e($quote['service_option'] === 'with_service_repair' ? 'With Service Repair' : 'Without Service Repair') ?></div>
                        </td>
                        <td><strong><?= e(format_currency($quote['total_amount'])) ?></strong></td>
                        <td>
                            <div class="action-group">
                                <a class="btn btn-sm btn-outline-primary btn-icon" href="<?= e(base_url('quotations/view?id=' . (int) $quote['id'])) ?>" title="View quotation" aria-label="View quotation"><i class="fas fa-eye"></i></a>
                                <a class="btn btn-sm btn-outline-success btn-icon" href="<?= e(base_url('quotations/edit?id=' . (int) $quote['id'])) ?>" title="Edit quotation" aria-label="Edit quotation"><i class="fas fa-pen"></i></a>
                                <form method="POST" action="<?= e(base_url('quotations/delete')) ?>" class="js-confirm-form" data-confirm-message="Delete this quotation?" data-confirm-button="Delete">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $quote['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete quotation" aria-label="Delete quotation"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$quotations): ?>
                    <tr><td colspan="4" class="text-center text-muted py-4" style="font-size:.82rem">No quotations yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade quotation-modal" id="quotationBuilderModal" tabindex="-1" aria-labelledby="quotationBuilderLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title h4 mb-1" id="quotationBuilderLabel">Create Quotation</h2>
                    <div class="small-muted">Fill in customer details, add products, and save the quotation.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?= e(base_url('quotations/store')) ?>" id="quotationForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="items" id="quotationItemsInput" value="[]">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <input class="form-control" name="customer_name" placeholder="Customer or company name" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Contact No.</label>
                            <input class="form-control" name="customer_contact" placeholder="09xxxxxxxxx">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Valid Until</label>
                            <input type="date" class="form-control" name="valid_until" min="<?= e(date('Y-m-d')) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="customer_address" rows="2" placeholder="Customer address"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Service Repair Option</label>
                            <select class="form-select" name="service_option" id="serviceOptionSelect">
                                <option value="without_service_repair">Without Service Repair</option>
                                <option value="with_service_repair">With Service Repair</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Service Repair Fee</label>
                            <div class="input-group">
                                <span class="input-group-text">&#8369;</span>
                                <input type="number" min="0" step="0.01" class="form-control" id="serviceFeeInput" name="service_fee" value="0.00" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Service Description</label>
                            <input class="form-control" name="service_description" id="serviceDescriptionInput" placeholder="Repair / labor details" disabled>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="2" placeholder="Optional remarks, terms, or customer request details"></textarea>
                        </div>
                    </div>

                    <hr style="border-color:var(--border);margin:18px 0">

                    <div class="row g-3 quotation-modal-workspace">
                        <div class="col-lg-7">
                            <div class="card-soft quotation-product-panel">
                                <div class="d-flex justify-content-between align-items-center mb-3 gap-2 flex-wrap">
                                    <div>
                                        <div style="font-weight:700">Select Products</div>
                                        <div class="small-muted">Search by product name or barcode.</div>
                                    </div>
                                    <div class="badge bg-soft-primary"><?= count($products) ?> products loaded</div>
                                </div>
                                <input type="text" id="quotationSearch" class="form-control mb-3" placeholder="Search parts...">
                                <div id="quotationProductGrid" class="product-grid quotation-product-grid"></div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="card-soft d-flex flex-column quotation-cart-panel" style="gap:12px">
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    <div>
                                        <div style="font-weight:700">Quotation Items</div>
                                        <div class="small-muted">Adjust quantities before saving.</div>
                                    </div>
                                    <span class="badge bg-soft-primary" id="quotationCartCount">0 items</span>
                                </div>

                                <div class="quotation-cart-table-wrap" style="border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;min-height:90px;background:var(--surface)">
                                    <table class="table mb-0 align-middle" style="font-size:.78rem">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th style="width:70px">Qty</th>
                                                <th style="width:88px">Price</th>
                                                <th style="width:88px">Sub</th>
                                                <th style="width:34px"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="quotationCartBody"></tbody>
                                    </table>
                                </div>

                                <div class="card-soft quotation-total-card" style="background:var(--surface)">
                                    <div style="display:flex;justify-content:space-between;font-size:.84rem;margin-bottom:6px">
                                        <span>Parts Subtotal</span>
                                        <strong id="quotationPartsSubtotal">&#8369;0.00</strong>
                                    </div>
                                    <div style="display:flex;justify-content:space-between;font-size:.84rem;margin-bottom:6px">
                                        <span>Service Repair</span>
                                        <strong id="quotationServiceAmount">&#8369;0.00</strong>
                                    </div>
                                    <hr style="border-color:var(--border);margin:8px 0">
                                    <div style="display:flex;justify-content:space-between;font-size:.98rem">
                                        <span style="font-weight:800">Quotation Total</span>
                                        <strong id="quotationGrandTotal" style="color:var(--primary);font-size:1.15rem">&#8369;0.00</strong>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100">Save Quotation</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var ALL_PRODUCTS = <?= $jsProducts ?>;
    var SEARCH_URL = <?= json_encode(base_url('quotations/search')) ?>;
    var cart = [];

    var grid = document.getElementById('quotationProductGrid');
    var searchInput = document.getElementById('quotationSearch');
    var cartBody = document.getElementById('quotationCartBody');
    var cartCountEl = document.getElementById('quotationCartCount');
    var itemsInput = document.getElementById('quotationItemsInput');
    var partsSubtotalEl = document.getElementById('quotationPartsSubtotal');
    var serviceAmountEl = document.getElementById('quotationServiceAmount');
    var grandTotalEl = document.getElementById('quotationGrandTotal');
    var serviceOptionSelect = document.getElementById('serviceOptionSelect');
    var serviceFeeInput = document.getElementById('serviceFeeInput');
    var serviceDescriptionInput = document.getElementById('serviceDescriptionInput');
    var form = document.getElementById('quotationForm');

    function fmt(n) {
        return '\u20b1' + Number(n || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function partsSubtotal() {
        return cart.reduce(function (sum, item) { return sum + item.price * item.qty; }, 0);
    }

    function serviceFee() {
        if (serviceOptionSelect.value !== 'with_service_repair') return 0;
        return Math.max(parseFloat(serviceFeeInput.value) || 0, 0);
    }

    function renderGrid(products) {
        if (!products.length) {
            grid.innerHTML = '<div style="color:var(--muted);font-size:.80rem;grid-column:1/-1;padding:14px 0">No products found.</div>';
            return;
        }

        grid.innerHTML = '';
        products.forEach(function (p) {
            var card = document.createElement('div');
            card.className = 'product-card';
            card.innerHTML =
                '<strong style="font-size:.80rem;font-weight:700;display:block;line-height:1.3">' + escHtml(p.name) + '</strong>' +
                '<div class="small-muted">' + escHtml(p.cat || '') + '</div>' +
                '<div class="product-card-price">' + fmt(p.price) + '</div>' +
                '<div class="small-muted" style="font-size:.67rem">Stock: ' + p.stock + '</div>';
            card.addEventListener('click', function () {
                addToCart(p);
            });
            grid.appendChild(card);
        });
    }

    function addToCart(p) {
        var existing = cart.find(function (item) { return item.id === p.id; });
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({ id: p.id, name: p.name, price: Number(p.price), qty: 1, stock: Number(p.stock) });
        }
        renderCart();
    }

    function renderCart() {
        var subtotal = partsSubtotal();
        var service = serviceFee();
        var total = subtotal + service;

        cartCountEl.textContent = cart.length + (cart.length === 1 ? ' item' : ' items');

        if (cart.length === 0) {
            cartBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4" style="font-size:.78rem">No parts selected yet.</td></tr>';
        } else {
            cartBody.innerHTML = '';
            cart.forEach(function (item, idx) {
                var tr = document.createElement('tr');
                tr.innerHTML =
                    '<td style="font-weight:600;word-break:break-word;max-width:120px">' + escHtml(item.name) + '</td>' +
                    '<td><input type="number" min="1" value="' + item.qty + '" data-idx="' + idx + '" class="quote-qty form-control form-control-sm" style="width:62px;font-size:.76rem;padding:.26rem .44rem"></td>' +
                    '<td style="white-space:nowrap">' + fmt(item.price) + '</td>' +
                    '<td style="font-weight:700;white-space:nowrap">' + fmt(item.price * item.qty) + '</td>' +
                    '<td><button type="button" data-idx="' + idx + '" class="quote-rm btn btn-sm btn-outline-danger" style="padding:.2rem .46rem;line-height:1">x</button></td>';
                cartBody.appendChild(tr);
            });
        }

        partsSubtotalEl.textContent = fmt(subtotal);
        serviceAmountEl.textContent = fmt(service);
        grandTotalEl.textContent = fmt(total);
        itemsInput.value = JSON.stringify(cart.map(function (item) {
            return { product_id: item.id, quantity: item.qty };
        }));
    }

    function syncServiceFields() {
        var enabled = serviceOptionSelect.value === 'with_service_repair';
        serviceFeeInput.disabled = !enabled;
        serviceDescriptionInput.disabled = !enabled;
        if (!enabled) {
            serviceFeeInput.value = '0.00';
            serviceDescriptionInput.value = '';
        }
        renderCart();
    }

    cartBody.addEventListener('change', function (e) {
        if (!e.target.classList.contains('quote-qty')) return;
        var idx = parseInt(e.target.dataset.idx, 10);
        var n = Math.max(1, parseInt(e.target.value, 10) || 1);
        cart[idx].qty = n;
        e.target.value = n;
        renderCart();
    });

    cartBody.addEventListener('click', function (e) {
        var btn = e.target.closest('.quote-rm');
        if (!btn) return;
        cart.splice(parseInt(btn.dataset.idx, 10), 1);
        renderCart();
    });

    serviceOptionSelect.addEventListener('change', syncServiceFields);
    serviceFeeInput.addEventListener('input', renderCart);

    var searchTimer;
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        var q = this.value.trim();
        searchTimer = setTimeout(async function () {
            try {
                var res = await fetch(SEARCH_URL + '?search=' + encodeURIComponent(q), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) return;
                var products = await res.json();
                renderGrid(products);
            } catch (err) {
                console.error('Quotation search error:', err);
            }
        }, 250);
    });

    form.addEventListener('submit', function (e) {
        if (cart.length === 0) {
            e.preventDefault();
            alert('Please add at least one product to the quotation.');
            return;
        }
    });

    renderGrid(ALL_PRODUCTS);
    syncServiceFields();
    renderCart();
})();
</script>
