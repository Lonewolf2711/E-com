<?php
/**
 * Admin Products List
 */
$products = $products ?? ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
$categories = $categories ?? [];
$filters = $filters ?? [];
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <h3>Products</h3>
        <a href="<?= url('/admin/products/add') ?>" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Add Product</a>
    </div>
</div>

<div class="page-content">
    <!-- Filters -->
    <div class="card">
        <div class="card-body">
            <form method="GET" action="<?= url('/admin/products') ?>" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" name="q" value="<?= e($filters['search'] ?? '') ?>" placeholder="Part name, code, or machine...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Category</label>
                    <select class="form-select" name="category">
                        <option value="">All</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($filters['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Machine</label>
                    <input type="text" class="form-control" name="machine"
                           value="<?= e($filters['machine'] ?? '') ?>"
                           placeholder="Machine name...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All</option>
                        <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sort</label>
                    <select class="form-select" name="sort">
                        <option value="newest" <?= ($filters['sort'] ?? '') === 'newest' ? 'selected' : '' ?>>Newest</option>
                        <option value="price_low" <?= ($filters['sort'] ?? '') === 'price_low' ? 'selected' : '' ?>>Price ↑</option>
                        <option value="price_high" <?= ($filters['sort'] ?? '') === 'price_high' ? 'selected' : '' ?>>Price ↓</option>
                        <option value="name_asc" <?= ($filters['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>>A-Z</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary me-2"><i class="bi bi-search me-1"></i>Filter</button>
                    <a href="<?= url('/admin/products') ?>" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card">
        <div class="card-body">
            <p class="text-muted mb-3">Showing <strong><?= count($products['data']) ?></strong> of <?= $products['total'] ?> parts</p>
            <div class="table-responsive">
                <table class="table table-hover table-lg">
                    <thead>
                        <tr>
                            <th style="width:50px;"></th>
                            <th>Part Name</th>
                            <th>Part Code</th>
                            <th>Machine</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th style="width:120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products['data'] as $product): ?>
                        <tr>
                            <td><img src="<?= $product['image'] ? upload_url(e($product['image'])) : asset('admin/images/faces/1.jpg') ?>" class="rounded" style="width:40px;height:40px;object-fit:cover;" alt=""></td>
                            <td>
                                <p class="fw-bold mb-0"><?= e($product['name']) ?></p>
                                <small class="text-muted"><?= e($product['category_name'] ?? '—') ?></small>
                            </td>
                            <td>
                                <code class="text-warning" style="font-size:0.8rem;letter-spacing:0.04em;">
                                    <?= e($product['product_code'] ?? $product['sku'] ?? '—') ?>
                                </code>
                            </td>
                            <td>
                                <span style="font-size:0.85rem;">
                                    <?= e($product['machine_name'] ?? '—') ?>
                                </span>
                                <?php if (!empty($product['machine_model'])): ?>
                                <br><small class="text-muted"><?= e($product['machine_model']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($product['stock'] <= 0): ?>
                                    <span class="badge bg-danger">Out of stock</span>
                                <?php elseif ($product['stock'] <= 5): ?>
                                    <span class="badge bg-warning"><?= $product['stock'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?= $product['stock'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge <?= $product['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>"><?= ucfirst(e($product['status'])) ?></span></td>
                            <td>
                                <a href="<?= url('/admin/products/edit/' . $product['id']) ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form action="<?= url('/admin/products/delete/' . $product['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($products['data'])): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">No products found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($products['pages'] > 1): ?>
            <?= pagination_html($products['current_page'], $products['pages'], url('/admin/products'), array_filter($filters)) ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
