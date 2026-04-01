<?php
/* Pre-encode product data as a safe JS array — avoids any quote/apostrophe issues in onclick */
$jsProducts = json_encode(array_map(function($p) {
    return [
        'id'    => (int)    $p['id'],
        'name'  => (string) $p['name'],
        'price' => (float)  $p['selling_price'],
        'stock' => (int)    $p['stock_quantity'],
        'cat'   => (string) ($p['category_name'] ?? ''),
        'fmt'   => format_currency($p['selling_price']),
    ];
}, $products), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
?>

<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Point of Sale</h1>
        <p class="page-header-desc">Search products, build your cart, accept payment, and complete retail transactions.</p>
    </div>
    <div class="page-header-actions">
        <div class="topbar-chip">🛍 <?= count($products) ?> products available</div>
    </div>
</div>

<div class="row g-4">
    <!-- Product Search -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header">
                <span>🔍 Product Search</span>
                <span class="small-muted">Click a product to add it to the cart</span>
            </div>
            <div class="card-body">
                <input type="text" id="posSearch" class="form-control mb-3"
                       placeholder="Search by product name or barcode..."
                       autocomplete="off">
                <div id="posGrid" class="product-grid"></div>
            </div>
        </div>
    </div>

    <!-- Cart -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <span>🛒 Cart</span>
                <span class="badge bg-soft-primary" id="cartCount">0 items</span>
            </div>
            <div class="card-body d-flex flex-column" style="gap:14px">
                <form method="POST" action="<?= e(base_url('pos/checkout')) ?>" id="checkoutForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="items" id="cartItemsInput" value="[]">

                    <div style="border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;min-height:80px">
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

                    <div class="card-soft">
                        <div style="display:flex;justify-content:space-between;font-size:.84rem;margin-bottom:6px">
                            <span>Subtotal</span>
                            <span id="cartSubtotal" style="font-weight:600">₱0.00</span>
                        </div>
                        <hr style="border-color:var(--border);margin:6px 0">
                        <div style="display:flex;justify-content:space-between;font-size:.94rem;margin-bottom:6px">
                            <span style="font-weight:700">Total</span>
                            <strong id="cartTotal" style="color:var(--primary);font-size:1.1rem">₱0.00</strong>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:.84rem">
                            <span>Change</span>
                            <strong id="cartChange" style="color:var(--success)">₱0.00</strong>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Payment Amount</label>
                        <div class="input-group">
                            <span class="input-group-text" style="font-weight:800">₱</span>
                            <input type="number" step="0.01" min="0" class="form-control"
                                   name="payment_amount" id="paymentInput" placeholder="0.00">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 btn-lg">✓ Complete Sale</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    /* ── Data from PHP ─────────────────────────── */
    var ALL_PRODUCTS  = <?= $jsProducts ?>;
    var SEARCH_URL    = <?= json_encode(base_url('pos/search')) ?>;

    /* ── Cart state ────────────────────────────── */
    var cart = [];

    /* ── DOM refs ──────────────────────────────── */
    var grid         = document.getElementById('posGrid');
    var cartBody     = document.getElementById('cartBody');
    var cartCountEl  = document.getElementById('cartCount');
    var subtotalEl   = document.getElementById('cartSubtotal');
    var totalEl      = document.getElementById('cartTotal');
    var changeEl     = document.getElementById('cartChange');
    var paymentInput = document.getElementById('paymentInput');
    var itemsInput   = document.getElementById('cartItemsInput');
    var form         = document.getElementById('checkoutForm');
    var searchInput  = document.getElementById('posSearch');

    /* ── Helpers ───────────────────────────────── */
    function fmt(n) {
        return '₱' + Number(n || 0).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function cartTotal() {
        return cart.reduce(function(s, i) { return s + i.price * i.qty; }, 0);
    }

    /* ── Render product grid ───────────────────── */
    function renderGrid(products) {
        if (!products.length) {
            grid.innerHTML = '<div style="color:var(--muted);font-size:.80rem;grid-column:1/-1;padding:14px 0">No products found.</div>';
            return;
        }
        grid.innerHTML = '';
        products.forEach(function(p) {
            var card = document.createElement('div');
            card.className = 'product-card';
            card.innerHTML =
                '<strong style="font-size:.80rem;font-weight:700;display:block;line-height:1.3">' + escHtml(p.name) + '</strong>' +
                '<div class="small-muted">' + escHtml(p.cat) + '</div>' +
                '<div class="product-card-price">' + fmt(p.price) + '</div>' +
                '<div class="small-muted" style="font-size:.67rem">' + p.stock + ' in stock</div>';
            /* use closure to capture p */
            card.addEventListener('click', (function(prod) {
                return function() { addToCart(prod); };
            })(p));
            grid.appendChild(card);
        });
    }

    /* ── Add to cart ───────────────────────────── */
    function addToCart(p) {
        var found = null;
        for (var i = 0; i < cart.length; i++) {
            if (cart[i].id === p.id) { found = cart[i]; break; }
        }
        if (found) {
            if (found.qty < found.stock) found.qty++;
        } else {
            cart.push({ id: p.id, name: p.name, price: p.price, stock: p.stock, qty: 1 });
        }
        renderCart();
    }

    /* ── Render cart ───────────────────────────── */
    function renderCart() {
        var total = cartTotal();

        cartCountEl.textContent = cart.length + (cart.length === 1 ? ' item' : ' items');

        if (cart.length === 0) {
            cartBody.innerHTML =
                '<tr><td colspan="5" style="text-align:center;color:var(--muted);padding:20px;font-size:.78rem">' +
                'No items yet — click a product to add it</td></tr>';
        } else {
            cartBody.innerHTML = '';
            cart.forEach(function(item, idx) {
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
                         'line-height:1;padding:0">×</button></td>';
                cartBody.appendChild(tr);
            });
        }

        /* update totals */
        var pay    = parseFloat(paymentInput.value) || 0;
        subtotalEl.textContent = fmt(total);
        totalEl.textContent    = fmt(total);
        changeEl.textContent   = fmt(Math.max(pay - total, 0));

        /* serialise */
        itemsInput.value = JSON.stringify(
            cart.map(function(i) { return { product_id: i.id, quantity: i.qty }; })
        );
    }

    /* ── Cart delegation ───────────────────────── */
    cartBody.addEventListener('change', function(e) {
        if (!e.target.classList.contains('qty-inp')) return;
        var idx = parseInt(e.target.dataset.idx, 10);
        var n   = Math.max(1, Math.min(parseInt(e.target.value, 10) || 1, cart[idx].stock));
        cart[idx].qty  = n;
        e.target.value = n;
        renderCart();
    });

    cartBody.addEventListener('click', function(e) {
        var btn = e.target.closest('.rm-btn');
        if (!btn) return;
        cart.splice(parseInt(btn.dataset.idx, 10), 1);
        renderCart();
    });

    paymentInput.addEventListener('input', function() {
        var total  = cartTotal();
        var pay    = parseFloat(this.value) || 0;
        changeEl.textContent = fmt(Math.max(pay - total, 0));
    });

    /* ── Form validation ───────────────────────── */
    form.addEventListener('submit', function(e) {
        if (cart.length === 0) {
            e.preventDefault();
            alert('Cart is empty.\nClick a product card to add it first.');
            return;
        }
        var total = cartTotal();
        var pay   = parseFloat(paymentInput.value) || 0;
        if (pay <= 0) {
            e.preventDefault();
            alert('Please enter the payment amount.');
            paymentInput.focus();
            return;
        }
        if (pay < total) {
            e.preventDefault();
            alert('Payment (' + fmt(pay) + ') is less than the total (' + fmt(total) + ').');
            paymentInput.focus();
        }
    });

    /* ── Live search ───────────────────────────── */
    var searchTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimer);
        var q = this.value.trim();

        if (q === '') {
            renderGrid(ALL_PRODUCTS);
            return;
        }

        searchTimer = setTimeout(function() {
            fetch(SEARCH_URL + '?search=' + encodeURIComponent(q), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                renderGrid(data.map(function(p) {
                    return { id: p.id, name: p.name, price: parseFloat(p.selling_price),
                             stock: p.stock_quantity, cat: p.category_name || '' };
                }));
            })
            .catch(function(err) { console.error('Search error:', err); });
        }, 280);
    });

    /* ── Escape HTML ───────────────────────────── */
    function escHtml(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;')
                        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    /* ── Init ──────────────────────────────────── */
    renderGrid(ALL_PRODUCTS);
    renderCart();

})();
</script>