document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggleButton = document.getElementById('sidebarToggleButton');
    const sidebarExpandButton = document.getElementById('sidebarExpandButton');
    const sidebarExpandLabel = document.getElementById('sidebarExpandLabel');
    const sidebarCollapseToggle = document.getElementById('sidebarCollapseToggle');
    const sidebarCollapseLabel = document.getElementById('sidebarCollapseLabel');
    const sidebarStorageKey = 'rb_sidebar_hidden';
    const toggle = document.getElementById('darkModeToggle');
    const label = document.getElementById('darkModeLabel');
    const storageKey = 'rb_theme';

    function isMobileViewport() {
        return window.matchMedia('(max-width: 991px)').matches;
    }

    function applySidebarState(hidden) {
        if (!sidebar) {
            return;
        }

        document.body.classList.toggle('sidebar-hidden', hidden);
        sidebar.classList.remove('show');

        if (sidebarCollapseToggle) {
            const iconEl = sidebarCollapseToggle.querySelector('i');
            const nextActionLabel = hidden ? 'Show sidebar' : 'Hide sidebar';

            if (iconEl) {
                iconEl.className = hidden ? 'fas fa-chevron-right' : 'fas fa-chevron-left';
            }

            sidebarCollapseToggle.setAttribute('title', nextActionLabel);
            sidebarCollapseToggle.setAttribute('aria-label', nextActionLabel);
        }

        if (sidebarCollapseLabel) {
            sidebarCollapseLabel.textContent = hidden ? 'Show sidebar' : 'Hide sidebar';
        }

        if (sidebarToggleButton && !isMobileViewport()) {
            const topbarActionLabel = hidden ? 'Show sidebar' : 'Hide sidebar';
            sidebarToggleButton.setAttribute('title', topbarActionLabel);
            sidebarToggleButton.setAttribute('aria-label', topbarActionLabel);
        }

        if (sidebarExpandButton) {
            sidebarExpandButton.setAttribute('title', hidden ? 'Show sidebar' : 'Hide sidebar');
            sidebarExpandButton.setAttribute('aria-label', hidden ? 'Show sidebar' : 'Hide sidebar');
        }

        if (sidebarExpandLabel) {
            sidebarExpandLabel.textContent = hidden ? 'Show sidebar' : 'Hide sidebar';
        }

        localStorage.setItem(sidebarStorageKey, hidden ? 'hidden' : 'visible');
    }

    function applyDarkMode(enabled) {
        document.body.classList.toggle('dark-mode', enabled);
        document.documentElement.setAttribute('data-theme', enabled ? 'dark' : 'light');

        const iconEl = toggle ? toggle.querySelector('i') : null;
        if (iconEl) {
            iconEl.className = enabled ? 'fas fa-sun' : 'fas fa-moon';
        }

        if (label) {
            label.textContent = enabled ? 'Light' : 'Dark';
        }

        if (toggle) {
            const toggleTitle = enabled ? 'Switch to light mode' : 'Switch to dark mode';
            toggle.setAttribute('title', toggleTitle);
            toggle.setAttribute('aria-label', toggleTitle);
        }

        localStorage.setItem(storageKey, enabled ? 'dark' : 'light');
    }

    applyDarkMode(localStorage.getItem(storageKey) === 'dark');
    applySidebarState(localStorage.getItem(sidebarStorageKey) === 'hidden');

    if (toggle) {
        toggle.addEventListener('click', function () {
            applyDarkMode(!document.body.classList.contains('dark-mode'));
        });
    }

    if (sidebarCollapseToggle) {
        sidebarCollapseToggle.addEventListener('click', function () {
            if (isMobileViewport()) {
                sidebar.classList.remove('show');
                return;
            }

            applySidebarState(!document.body.classList.contains('sidebar-hidden'));
        });
    }

    if (sidebarToggleButton) {
        sidebarToggleButton.addEventListener('click', function () {
            if (!sidebar) {
                return;
            }

            if (isMobileViewport()) {
                sidebar.classList.toggle('show');
                return;
            }

            if (document.body.classList.contains('sidebar-hidden')) {
                applySidebarState(false);
            } else {
                applySidebarState(true);
            }
        });
    }

    if (sidebarExpandButton) {
        sidebarExpandButton.addEventListener('click', function () {
            if (!sidebar || isMobileViewport()) {
                return;
            }

            applySidebarState(false);
        });
    }

    window.addEventListener('resize', function () {
        if (!sidebar) {
            return;
        }

        if (!isMobileViewport()) {
            sidebar.classList.remove('show');
        }
    });

    document.addEventListener('click', function (e) {
        if (!sidebar) {
            return;
        }

        if (
            sidebar.classList.contains('show') &&
            !sidebar.contains(e.target) &&
            sidebarToggleButton &&
            !sidebarToggleButton.contains(e.target)
        ) {
            sidebar.classList.remove('show');
        }
    });

    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity .4s ease, max-height .4s ease, margin .4s ease';
            alert.style.opacity = '0';
            alert.style.maxHeight = '0';
            alert.style.overflow = 'hidden';
            alert.style.margin = '0';
            setTimeout(function () {
                alert.remove();
            }, 420);
        }, 4000);
    });

    document.querySelectorAll('[title]').forEach(function (element) {
        if (!element.getAttribute('data-bs-toggle')) {
            element.setAttribute('data-bs-toggle', 'tooltip');
        }
    });

    if (window.bootstrap && typeof window.bootstrap.Tooltip === 'function') {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (element) {
            new window.bootstrap.Tooltip(element);
        });
    }

    var confirmModalEl = document.getElementById('confirmActionModal');
    var confirmMessageEl = document.getElementById('confirmActionMessage');
    var confirmSubmitEl = document.getElementById('confirmActionSubmit');
    var activeConfirmForm = null;
    var confirmModal = confirmModalEl && window.bootstrap && typeof window.bootstrap.Modal === 'function'
        ? new window.bootstrap.Modal(confirmModalEl)
        : null;

    if (confirmModal && confirmSubmitEl && confirmMessageEl) {
        document.addEventListener('submit', function (event) {
            var form = event.target;
            if (!(form instanceof HTMLFormElement) || !form.matches('.js-confirm-form')) {
                return;
            }

            if (form.dataset.confirmed === '1') {
                form.dataset.confirmed = '0';
                return;
            }

            event.preventDefault();
            activeConfirmForm = form;
            confirmMessageEl.textContent = form.dataset.confirmMessage || 'Are you sure you want to continue?';
            confirmSubmitEl.textContent = form.dataset.confirmButton || 'Confirm';
            confirmModal.show();
        });

        confirmSubmitEl.addEventListener('click', function () {
            if (!activeConfirmForm) {
                return;
            }

            activeConfirmForm.dataset.confirmed = '1';
            confirmModal.hide();

            if (typeof activeConfirmForm.requestSubmit === 'function') {
                activeConfirmForm.requestSubmit();
                return;
            }

            activeConfirmForm.submit();
        });

        confirmModalEl.addEventListener('hidden.bs.modal', function () {
            activeConfirmForm = null;
        });
    }

    function escHtml(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatCurrency(value) {
        return '&#8369;' + Number(value || 0).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function formatDate(value) {
        if (!value) {
            return '-';
        }

        var parsed = new Date(String(value).replace(' ', 'T'));
        if (Number.isNaN(parsed.getTime())) {
            return escHtml(value);
        }

        return parsed.toLocaleDateString('en-PH', {
            month: 'short',
            day: '2-digit',
            year: 'numeric'
        });
    }

    function formatDateTime(value) {
        if (!value) {
            return '-';
        }

        var parsed = new Date(String(value).replace(' ', 'T'));
        if (Number.isNaN(parsed.getTime())) {
            return escHtml(value);
        }

        return parsed.toLocaleString('en-PH', {
            month: 'short',
            day: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function renderSuppliers(items) {
        var tbody = document.getElementById('supplierTableBody');
        var badge = document.getElementById('supplierCountBadge');
        if (!tbody || !badge) {
            return;
        }

        var baseUrl = tbody.dataset.baseUrl;
        var csrfToken = tbody.dataset.csrfToken;
        var canDelete = tbody.dataset.canDelete === '1';

        badge.textContent = items.length + ' suppliers';

        if (!items.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4" style="font-size:.82rem">No suppliers available.</td></tr>';
            return;
        }

        tbody.innerHTML = items.map(function (supplier) {
            var deleteForm = canDelete
                ? '<form method="POST" action="' + escHtml(baseUrl + '/suppliers/delete') + '" class="js-confirm-form" data-confirm-message="Delete this supplier?" data-confirm-button="Delete">' +
                    '<input type="hidden" name="csrf_token" value="' + escHtml(csrfToken) + '">' +
                    '<input type="hidden" name="id" value="' + Number(supplier.id) + '">' +
                    '<button class="btn btn-sm btn-outline-danger btn-icon" title="Delete supplier" aria-label="Delete supplier"><i class="fas fa-trash-alt"></i></button>' +
                '</form>'
                : '';

            return '<tr>' +
                '<td style="font-weight:700;font-size:.82rem">' + escHtml(supplier.name) + '</td>' +
                '<td class="small-muted">' + escHtml(supplier.contact_person) + '</td>' +
                '<td class="small-muted">' + escHtml(supplier.phone) + '</td>' +
                '<td class="small-muted">' + escHtml(supplier.address) + '</td>' +
                '<td><div class="action-group">' +
                    '<button class="btn btn-sm btn-outline-success btn-icon" data-bs-toggle="collapse" data-bs-target="#sup' + Number(supplier.id) + '" title="Edit supplier" aria-label="Edit supplier"><i class="fas fa-pen"></i></button>' +
                    deleteForm +
                '</div></td>' +
            '</tr>' +
            '<tr class="collapse" id="sup' + Number(supplier.id) + '">' +
                '<td colspan="5" style="background:var(--surface-2);padding:14px 18px">' +
                    '<form method="POST" action="' + escHtml(baseUrl + '/suppliers/update') + '">' +
                        '<input type="hidden" name="csrf_token" value="' + escHtml(csrfToken) + '">' +
                        '<input type="hidden" name="id" value="' + Number(supplier.id) + '">' +
                        '<div class="row g-2">' +
                            '<div class="col-md-3"><input class="form-control" name="name" value="' + escHtml(supplier.name) + '" placeholder="Name" required></div>' +
                            '<div class="col-md-3"><input class="form-control" name="contact_person" value="' + escHtml(supplier.contact_person) + '" placeholder="Contact person"></div>' +
                            '<div class="col-md-2"><input class="form-control" name="phone" value="' + escHtml(supplier.phone) + '" placeholder="Phone"></div>' +
                            '<div class="col-md-3"><input class="form-control" name="address" value="' + escHtml(supplier.address) + '" placeholder="Address"></div>' +
                            '<div class="col-md-1"><button class="btn btn-primary w-100"><i class="fas fa-save"></i></button></div>' +
                        '</div>' +
                    '</form>' +
                '</td>' +
            '</tr>';
        }).join('');
    }

    function renderCategories(items) {
        var tbody = document.getElementById('categoryTableBody');
        var badge = document.getElementById('categoryCountBadge');
        if (!tbody || !badge) {
            return;
        }

        var baseUrl = tbody.dataset.baseUrl;
        var csrfToken = tbody.dataset.csrfToken;
        var canDelete = tbody.dataset.canDelete === '1';

        badge.textContent = items.length + ' categories';

        if (!items.length) {
            tbody.innerHTML = '<tr><td colspan="2" class="text-center text-muted py-4" style="font-size:.82rem">No categories available.</td></tr>';
            return;
        }

        tbody.innerHTML = items.map(function (category) {
            var deleteForm = canDelete
                ? '<form method="POST" action="' + escHtml(baseUrl + '/categories/delete') + '" class="js-confirm-form" data-confirm-message="Delete this category?" data-confirm-button="Delete">' +
                    '<input type="hidden" name="csrf_token" value="' + escHtml(csrfToken) + '">' +
                    '<input type="hidden" name="id" value="' + Number(category.id) + '">' +
                    '<button class="btn btn-sm btn-outline-danger btn-icon" title="Delete category" aria-label="Delete category"><i class="fas fa-trash-alt"></i></button>' +
                '</form>'
                : '';

            return '<tr>' +
                '<td style="font-weight:600;font-size:.82rem">' + escHtml(category.name) + '</td>' +
                '<td><div class="action-group justify-content-end">' +
                    '<button class="btn btn-sm btn-outline-success btn-icon" data-bs-toggle="collapse" data-bs-target="#cat' + Number(category.id) + '" title="Edit category" aria-label="Edit category"><i class="fas fa-pen"></i></button>' +
                    deleteForm +
                '</div></td>' +
            '</tr>' +
            '<tr class="collapse" id="cat' + Number(category.id) + '">' +
                '<td colspan="2" style="background:var(--surface-2);padding:14px 18px">' +
                    '<form method="POST" action="' + escHtml(baseUrl + '/categories/update') + '" class="d-flex gap-2">' +
                        '<input type="hidden" name="csrf_token" value="' + escHtml(csrfToken) + '">' +
                        '<input type="hidden" name="id" value="' + Number(category.id) + '">' +
                        '<input type="text" class="form-control" name="name" value="' + escHtml(category.name) + '" required>' +
                        '<button class="btn btn-primary"><i class="fas fa-save"></i> Update</button>' +
                    '</form>' +
                '</td>' +
            '</tr>';
        }).join('');
    }

    function renderProducts(items) {
        var tbody = document.getElementById('productTableBody');
        var badge = document.getElementById('productCountBadge');
        if (!tbody || !badge) {
            return;
        }

        var baseUrl = tbody.dataset.baseUrl;
        var csrfToken = tbody.dataset.csrfToken;
        var canDelete = tbody.dataset.canDelete === '1';
        var lowStockThreshold = Number(tbody.dataset.lowStockThreshold || 0);

        badge.textContent = items.length + ' items';

        if (!items.length) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4" style="font-size:.82rem">No products found.</td></tr>';
            return;
        }

        tbody.innerHTML = items.map(function (product) {
            var deleteForm = canDelete
                ? '<form method="POST" action="' + escHtml(baseUrl + '/products/delete') + '" class="js-confirm-form" data-confirm-message="Delete this product?" data-confirm-button="Delete">' +
                    '<input type="hidden" name="csrf_token" value="' + escHtml(csrfToken) + '">' +
                    '<input type="hidden" name="id" value="' + Number(product.id) + '">' +
                    '<button class="btn btn-sm btn-outline-danger btn-icon" title="Delete product" aria-label="Delete product"><i class="fas fa-trash-alt"></i></button>' +
                '</form>'
                : '';

            var stockBadge = Number(product.stock_quantity) <= lowStockThreshold ? 'bg-soft-danger' : 'bg-soft-success';

            return '<tr>' +
                '<td><div style="font-size:.82rem;font-weight:700">' + escHtml(product.name) + '</div><div class="small-muted mt-1">' + escHtml(product.description) + '</div></td>' +
                '<td><span class="badge bg-soft-primary">' + escHtml(product.category_name) + '</span></td>' +
                '<td class="small-muted">' + escHtml(product.supplier_name || '-') + '</td>' +
                '<td class="small-muted">' + formatCurrency(product.buying_price) + '</td>' +
                '<td><strong>' + formatCurrency(product.selling_price) + '</strong></td>' +
                '<td><span class="badge ' + stockBadge + '">' + Number(product.stock_quantity) + '</span></td>' +
                '<td class="small-muted">' + escHtml(product.unit_type || 'PC') + '</td>' +
                '<td class="small-muted">' + escHtml(product.barcode || '-') + '</td>' +
                '<td><div class="action-group">' +
                    '<a class="btn btn-sm btn-outline-success btn-icon" href="' + escHtml(baseUrl + '/products/edit?id=' + Number(product.id)) + '" title="Edit product" aria-label="Edit product"><i class="fas fa-pen"></i></a>' +
                    '<a class="btn btn-sm btn-outline-secondary btn-icon" href="' + escHtml(baseUrl + '/products/stock?id=' + Number(product.id)) + '" title="Adjust stock" aria-label="Adjust stock"><i class="fas fa-warehouse"></i></a>' +
                    deleteForm +
                '</div></td>' +
            '</tr>';
        }).join('');
    }

    function renderQuotations(items) {
        var tbody = document.getElementById('quotationTableBody');
        var badge = document.getElementById('quotationCountBadge');
        var chip = document.getElementById('quotationCountChip');
        if (!tbody || !badge || !chip) {
            return;
        }

        var baseUrl = tbody.dataset.baseUrl;
        var csrfToken = tbody.dataset.csrfToken;
        badge.textContent = items.length + ' records';
        chip.innerHTML = '<i class="fas fa-receipt"></i> ' + items.length + ' recent quotations';

        if (!items.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4" style="font-size:.82rem">No quotations yet.</td></tr>';
            return;
        }

        tbody.innerHTML = items.map(function (quote) {
            var serviceLabel = quote.service_option === 'with_service_repair' ? 'With Service Repair' : 'Without Service Repair';

            return '<tr>' +
                '<td><div><span class="badge bg-soft-primary">' + escHtml(quote.quote_no) + '</span></div><div class="small-muted">' + formatDate(quote.created_at) + '</div></td>' +
                '<td><div style="font-weight:700;font-size:.82rem">' + escHtml(quote.customer_name) + '</div><div class="small-muted">' + escHtml(serviceLabel) + '</div></td>' +
                '<td><strong>' + formatCurrency(quote.total_amount) + '</strong></td>' +
                '<td><div class="action-group">' +
                    '<a class="btn btn-sm btn-outline-primary btn-icon" href="' + escHtml(baseUrl + '/quotations/view?id=' + Number(quote.id)) + '" title="View quotation" aria-label="View quotation"><i class="fas fa-eye"></i></a>' +
                    '<a class="btn btn-sm btn-outline-success btn-icon" href="' + escHtml(baseUrl + '/quotations/edit?id=' + Number(quote.id)) + '" title="Edit quotation" aria-label="Edit quotation"><i class="fas fa-pen"></i></a>' +
                    '<form method="POST" action="' + escHtml(baseUrl + '/quotations/delete') + '" class="js-confirm-form" data-confirm-message="Delete this quotation?" data-confirm-button="Delete">' +
                        '<input type="hidden" name="csrf_token" value="' + escHtml(csrfToken) + '">' +
                        '<input type="hidden" name="id" value="' + Number(quote.id) + '">' +
                        '<button class="btn btn-sm btn-outline-danger btn-icon" title="Delete quotation" aria-label="Delete quotation"><i class="fas fa-trash-alt"></i></button>' +
                    '</form>' +
                '</div></td>' +
            '</tr>';
        }).join('');
    }

    function renderPurchaseHistory(items) {
        var tbody = document.getElementById('purchaseHistoryTableBody');
        if (!tbody) {
            return;
        }

        var card = tbody.closest('.card');
        var badge = card ? card.querySelector('.card-header .badge') : null;
        var baseUrl = tbody.dataset.baseUrl;
        var csrfToken = tbody.dataset.csrfToken;

        if (badge) {
            badge.textContent = items.length + ' records';
        }

        if (!items.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4" style="font-size:.82rem">No purchase history yet.</td></tr>';
            return;
        }

        tbody.innerHTML = items.map(function (row) {
            var status = String(row.status || 'completed');
            var statusLabel = status.charAt(0).toUpperCase() + status.slice(1);
            var statusClass = status === 'voided'
                ? 'bg-soft-warning'
                : (status === 'deleted' ? 'bg-soft-danger' : 'bg-soft-success');

            var actions = '<a class="btn btn-sm btn-outline-primary btn-icon" href="' + escHtml(baseUrl + '/pos/receipt?id=' + Number(row.id)) + '" title="View receipt" aria-label="View receipt">' +
                '<i class="fas fa-eye"></i>' +
            '</a>';

            if (status === 'completed') {
                actions += '<form method="POST" action="' + escHtml(baseUrl + '/pos/void') + '" class="js-confirm-form" data-confirm-message="Void this transaction and restore its stock quantities?" data-confirm-button="Void">' +
                    '<input type="hidden" name="csrf_token" value="' + escHtml(csrfToken) + '">' +
                    '<input type="hidden" name="id" value="' + Number(row.id) + '">' +
                    '<button class="btn btn-sm btn-outline-warning btn-icon" title="Void transaction" aria-label="Void transaction">' +
                        '<i class="fas fa-ban"></i>' +
                    '</button>' +
                '</form>';
            }

            if (status === 'voided') {
                actions += '<form method="POST" action="' + escHtml(baseUrl + '/pos/delete') + '" class="js-confirm-form" data-confirm-message="Delete this voided transaction from active history?" data-confirm-button="Delete">' +
                    '<input type="hidden" name="csrf_token" value="' + escHtml(csrfToken) + '">' +
                    '<input type="hidden" name="id" value="' + Number(row.id) + '">' +
                    '<button class="btn btn-sm btn-outline-danger btn-icon" title="Delete transaction" aria-label="Delete transaction">' +
                        '<i class="fas fa-trash-alt"></i>' +
                    '</button>' +
                '</form>';
            }

            return '<tr>' +
                '<td><span class="badge bg-soft-primary">' + escHtml(row.invoice_no) + '</span></td>' +
                '<td><div style="font-size:.82rem;font-weight:700">' + escHtml(row.customer_name || 'Walk-in Customer') + '</div><div class="small-muted">' + escHtml(row.customer_address || 'No Address Provided') + '</div></td>' +
                '<td><div style="font-size:.82rem;font-weight:700">' + escHtml(row.payment_method || 'Cash') + '</div><div class="small-muted">' + escHtml(row.reference_no || '-') + '</div></td>' +
                '<td><strong>' + formatCurrency(row.total_amount) + '</strong></td>' +
                '<td><span class="badge ' + statusClass + '">' + escHtml(statusLabel) + '</span></td>' +
                '<td class="small-muted">' + formatDateTime(row.created_at) + '</td>' +
                '<td><div class="action-group">' + actions + '</div></td>' +
            '</tr>';
        }).join('');
    }

    var liveRenderers = {
        suppliers: renderSuppliers,
        categories: renderCategories,
        products: renderProducts,
        quotations: renderQuotations,
        purchaseHistory: renderPurchaseHistory
    };

    document.querySelectorAll('form[data-live-search="true"]').forEach(function (form) {
        var timer;
        var rendererName = form.dataset.liveRender;
        var renderer = liveRenderers[rendererName];
        var searchInputs = form.querySelectorAll('input:not([type="hidden"]):not([type="submit"]):not([type="button"]):not([type="reset"]), textarea');
        var selectInputs = form.querySelectorAll('select');

        if (typeof renderer !== 'function') {
            return;
        }

        function buildUrl() {
            var url = new URL(form.action, window.location.origin);
            var formData = new FormData(form);

            Array.from(formData.entries()).forEach(function (entry) {
                if (entry[1] !== '') {
                    url.searchParams.set(entry[0], entry[1]);
                } else {
                    url.searchParams.delete(entry[0]);
                }
            });

            return url;
        }

        function fetchAndRender() {
            var url = buildUrl();

            fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Live search request failed');
                    }

                    return response.json();
                })
                .then(function (items) {
                    renderer(items);
                    window.history.replaceState({}, '', url.pathname + url.search);
                })
                .catch(function (error) {
                    console.error('Live search error:', error);
                });
        }

        function scheduleFetch() {
            clearTimeout(timer);
            timer = setTimeout(fetchAndRender, 280);
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            fetchAndRender();
        });

        searchInputs.forEach(function (input) {
            input.setAttribute('autocomplete', 'off');
            input.addEventListener('input', scheduleFetch);
        });

        selectInputs.forEach(function (select) {
            select.addEventListener('change', fetchAndRender);
        });
    });
});
