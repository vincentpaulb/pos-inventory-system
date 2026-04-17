<?php $canDeleteCategories = has_role('Admin'); ?>

<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Categories</h1>
        <p class="page-header-desc">Manage product categories to keep your inventory organized and searchable.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="fas fa-plus"></i> Add Category</div>
            <div class="card-body">
                <form method="POST" action="<?= e(base_url('categories/store')) ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="e.g. Brake System" required>
                    </div>
                    <button class="btn btn-primary w-100"><i class="fas fa-save"></i> Save Category</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <span><i class="fas fa-folder-open"></i> Category List</span>
                <span class="badge bg-soft-primary" id="categoryCountBadge"><?= count($categories) ?> categories</span>
            </div>
            <div class="card-body" style="border-bottom:1px solid var(--border)">
                <form class="row g-2 align-items-end" method="GET" action="<?= e(base_url('categories')) ?>" data-live-search="true" data-live-render="categories">
                    <div class="col-md-5">
                        <label class="form-label">Search Categories</label>
                        <input type="text" name="search" class="form-control" placeholder="Search category name" value="<?= e($search ?? '') ?>">
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr><th>Name</th><th style="text-align:right">Actions</th></tr>
                    </thead>
                    <tbody id="categoryTableBody"
                        data-base-url="<?= e(base_url()) ?>"
                        data-csrf-token="<?= e(csrf_token()) ?>"
                        data-can-delete="<?= $canDeleteCategories ? '1' : '0' ?>">
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td style="font-weight:600;font-size:.82rem"><?= e($category['name']) ?></td>
                            <td>
                                <div class="action-group justify-content-end">
                                    <button class="btn btn-sm btn-outline-success btn-icon" data-bs-toggle="collapse" data-bs-target="#cat<?= (int) $category['id'] ?>" title="Edit category" aria-label="Edit category"><i class="fas fa-pen"></i></button>
                                    <?php if (has_role('Admin')): ?>
                                    <form method="POST" action="<?= e(base_url('categories/delete')) ?>" class="js-confirm-form" data-confirm-message="Delete this category?" data-confirm-button="Delete">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger btn-icon" title="Delete category" aria-label="Delete category"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <tr class="collapse" id="cat<?= (int) $category['id'] ?>">
                            <td colspan="2" style="background:var(--surface-2);padding:14px 18px">
                                <form method="POST" action="<?= e(base_url('categories/update')) ?>" class="d-flex gap-2">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                                    <input type="text" class="form-control" name="name" value="<?= e($category['name']) ?>" required>
                                    <button class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$categories): ?>
                        <tr><td colspan="2" class="text-center text-muted py-4" style="font-size:.82rem">No categories available.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
