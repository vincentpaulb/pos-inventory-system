document.addEventListener('DOMContentLoaded', function () {

    /* ── State ─────────────────────────────────────── */
    const cart = [];

    /* ── DOM refs ──────────────────────────────────── */
    const results        = document.getElementById('productResults');
    const searchInput    = document.getElementById('productSearch');
    const cartBody       = document.querySelector('#cartTable tbody');
    const cartTotalEl    = document.getElementById('cartTotal');
    const cartSubtotalEl = document.getElementById('cartSubtotal');
    const cartCountEl    = document.getElementById('cartCount');
    const paymentInput   = document.getElementById('paymentAmount');
    const changeEl       = document.getElementById('changeAmount');
    const cartItemsInput = document.getElementById('cartItemsInput');
    const checkoutForm   = document.getElementById('checkoutForm');

    /* ── Helpers ───────────────────────────────────── */
    function peso(value) {
        return new Intl.NumberFormat('en-PH', {
            style: 'currency', currency: 'PHP'
        }).format(Number(value) || 0);
    }

    function cartTotal() {
        return cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
    }

    /* ── Render cart ───────────────────────────────── */
    function renderCart() {
        cartBody.innerHTML = '';
        const total = cartTotal();

        cart.forEach((item, index) => {
            const subtotal = item.price * item.quantity;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="font-size:.78rem;font-weight:600;max-width:130px;word-break:break-word">${escHtml(item.name)}</td>
                <td>
                    <input type="number" min="1" max="${item.stock}"
                        value="${item.quantity}" data-index="${index}"
                        class="form-control form-control-sm qty-input"
                        style="width:68px;font-size:.78rem;padding:.28rem .5rem">
                </td>
                <td style="font-size:.78rem;white-space:nowrap">${peso(item.price)}</td>
                <td style="font-size:.78rem;font-weight:700;white-space:nowrap">${peso(subtotal)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-btn"
                        data-index="${index}"
                        style="padding:.22rem .48rem;font-size:.76rem;line-height:1">×</button>
                </td>`;
            cartBody.appendChild(tr);
        });

        /* totals */
        const payment = parseFloat(paymentInput.value) || 0;
        const change  = Math.max(payment - total, 0);

        if (cartTotalEl)    cartTotalEl.textContent    = peso(total);
        if (cartSubtotalEl) cartSubtotalEl.textContent = peso(total);
        if (changeEl)       changeEl.textContent       = peso(change);
        if (cartCountEl)    cartCountEl.textContent    = cart.length + (cart.length === 1 ? ' item' : ' items');

        /* serialise for form submit */
        cartItemsInput.value = JSON.stringify(
            cart.map(i => ({ product_id: i.id, quantity: i.quantity }))
        );
    }

    /* ── Add product to cart ───────────────────────── */
    function addToCart(product) {
        const existing = cart.find(i => i.id === product.id);
        if (existing) {
            if (existing.quantity < existing.stock) existing.quantity++;
        } else {
            cart.push({ ...product, quantity: 1 });
        }
        renderCart();

        /* brief flash on the card */
        const card = results.querySelector(`[data-id="${product.id}"]`);
        if (card) {
            card.style.transition = 'transform .12s ease, border-color .12s ease';
            card.style.transform  = 'scale(.96)';
            card.style.borderColor = 'var(--primary)';
            setTimeout(() => {
                card.style.transform   = '';
                card.style.borderColor = '';
            }, 200);
        }
    }

    /* ── Escape HTML (for dynamic card injection) ── */
    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    /* ── Build product card HTML ───────────────────── */
    function buildCard(p) {
        const stockBadge = p.stock_quantity <= 5
            ? `<div style="font-size:.65rem;color:var(--danger);font-weight:700">${p.stock_quantity} left — low stock</div>`
            : `<div class="small-muted" style="font-size:.67rem">${p.stock_quantity} in stock</div>`;
        return `
            <button type="button" class="product-card"
                data-id="${p.id}"
                data-name="${escHtml(p.name)}"
                data-price="${p.selling_price}"
                data-stock="${p.stock_quantity}">
                <strong style="font-size:.80rem;font-weight:700;line-height:1.3;display:block">${escHtml(p.name)}</strong>
                <div class="small-muted">${escHtml(p.category_name ?? '')}</div>
                <div class="product-card-price">${peso(p.selling_price)}</div>
                ${stockBadge}
            </button>`;
    }

    /* ── Attach click events to all product cards ── */
    function attachCardEvents() {
        results.querySelectorAll('.product-card').forEach(card => {
            /* remove previous listener to avoid doubles */
            card.replaceWith(card.cloneNode(true));
        });
        results.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', function () {
                addToCart({
                    id:    Number(this.dataset.id),
                    name:  this.dataset.name,
                    price: Number(this.dataset.price),
                    stock: Number(this.dataset.stock),
                });
            });
        });
    }

    /* Initial attach for server-rendered cards */
    attachCardEvents();

    /* ── Cart event delegation ─────────────────────── */
    cartBody.addEventListener('input', function (e) {
        if (!e.target.classList.contains('qty-input')) return;
        const idx = Number(e.target.dataset.index);
        const qty = Math.max(1, Math.min(
            Number(e.target.value) || 1,
            cart[idx].stock
        ));
        cart[idx].quantity = qty;
        e.target.value = qty;
        renderCart();
    });

    cartBody.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-btn');
        if (!btn) return;
        cart.splice(Number(btn.dataset.index), 1);
        renderCart();
    });

    paymentInput.addEventListener('input', renderCart);

    /* ── Live product search ───────────────────────── */
    let searchTimer;
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        const q = this.value.trim();

        searchTimer = setTimeout(async () => {
            try {
                const url = window.POS_SEARCH_URL + '?search=' + encodeURIComponent(q);
                const res = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!res.ok) {
                    console.error('POS search returned HTTP', res.status);
                    return;
                }

                const contentType = res.headers.get('content-type') || '';
                if (!contentType.includes('application/json')) {
                    /* Got HTML back — likely a login redirect or PHP error */
                    const text = await res.text();
                    console.error('POS search: expected JSON, got:', text.substring(0, 300));
                    results.innerHTML = '<div class="text-danger" style="font-size:.80rem;padding:12px">Search error — check console (F12).</div>';
                    return;
                }

                const products = await res.json();

                if (!products.length) {
                    results.innerHTML = '<div class="small-muted" style="font-size:.80rem;grid-column:1/-1;padding:16px 0">No products found.</div>';
                    return;
                }

                results.innerHTML = products.map(buildCard).join('');
                attachCardEvents();

            } catch (err) {
                console.error('POS search fetch error:', err);
            }
        }, 280);
    });

    /* ── Checkout validation ───────────────────────── */
    checkoutForm.addEventListener('submit', function (e) {
        if (cart.length === 0) {
            e.preventDefault();
            alert('Your cart is empty.\nPlease click on a product to add it to the cart first.');
            return;
        }

        const total   = cartTotal();
        const payment = parseFloat(paymentInput.value) || 0;

        if (payment <= 0) {
            e.preventDefault();
            alert('Please enter the payment amount.');
            paymentInput.focus();
            return;
        }

        if (payment < total) {
            e.preventDefault();
            alert(
                'Payment (' + peso(payment) + ') is less than the total (' + peso(total) + ').\n' +
                'Please enter a sufficient amount.'
            );
            paymentInput.focus();
            return;
        }
    });

    /* Initial render (empty cart) */
    renderCart();
});
