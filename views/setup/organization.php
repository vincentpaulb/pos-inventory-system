<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Initial Setup</h1>
        <p class="page-header-desc">Step 1 of 2. Enter the organization details that will be used across the system.</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span><i class="fas fa-building"></i> Organization Information</span>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= e(base_url('setup/organization')) ?>" enctype="multipart/form-data" class="row g-4">
            <?= csrf_field() ?>

            <div class="col-lg-7">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Organization/Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="company_name" value="<?= e($organization['company_name']) ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="company_address" rows="3" required><?= e($organization['company_address']) ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact Number</label>
                        <input type="text" class="form-control" name="company_contact" value="<?= e($organization['company_contact']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="company_email" value="<?= e($organization['company_email']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">VAT Rate (%)</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            max="100"
                            class="form-control"
                            name="vat_rate_percent"
                            value="<?= e($organization['vat_rate_percent']) ?>"
                            placeholder="12.00"
                        >
                        <div class="form-hint">Default sales tax used in POS, receipts, and daily sales reports.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Low Stock Threshold</label>
                        <input
                            type="number"
                            min="0"
                            step="1"
                            class="form-control"
                            name="low_stock_threshold"
                            value="<?= e($organization['low_stock_threshold']) ?>"
                            placeholder="5"
                        >
                        <div class="form-hint">Products at or below this quantity are flagged as low stock.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Logo</label>
                        <input type="file" class="form-control" name="logo" accept=".png,.jpg,.jpeg,.webp">
                        <div class="form-hint">Optional. Used in the sidebar and compact branding areas.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Header/Banner</label>
                        <input type="file" class="form-control" name="header" accept=".png,.jpg,.jpeg,.webp">
                        <div class="form-hint">Optional. Used on login and quotation layouts.</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card-soft h-100">
                    <div class="section-title">Owner Information</div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Owner's Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="owner_name" value="<?= e($organization['owner_name']) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Owner's Address</label>
                            <textarea class="form-control" name="owner_address" rows="3"><?= e($organization['owner_address']) ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Contact Number</label>
                            <input type="text" class="form-control" name="owner_contact" value="<?= e($organization['owner_contact']) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="owner_email" value="<?= e($organization['owner_email']) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card-soft">
                    <div class="section-title">Current Brand Preview</div>
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <div class="small-muted mb-2">Logo</div>
                            <?php if (organization_logo_url()): ?>
                                <img src="<?= e(organization_logo_url()) ?>" alt="<?= e(organization_name()) ?> logo" style="max-width:90px;max-height:90px;object-fit:contain">
                            <?php else: ?>
                                <div class="brand-mark"><?= e(organization_initials()) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <div class="small-muted mb-2">Header</div>
                            <?php if (organization_header_url()): ?>
                                <img src="<?= e(organization_header_url()) ?>" alt="<?= e(organization_name()) ?> header" style="max-width:100%;max-height:96px;object-fit:contain">
                            <?php else: ?>
                                <div style="font-size:1.25rem;font-weight:700;letter-spacing:-0.04em"><?= e(organization_name()) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 d-flex justify-content-end">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-arrow-right"></i> Continue to Admin Setup
                </button>
            </div>
        </form>
    </div>
</div>
