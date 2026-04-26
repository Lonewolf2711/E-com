<?php
/**
 * Admin Inventory Management
 */
$products = $products ?? ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
$search = $search ?? '';
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <h3>Inventory</h3>
        <a href="<?= url('/admin/inventory/low-stock') ?>" class="btn btn-outline-warning"><i class="bi bi-exclamation-triangle me-2"></i>Low Stock Alerts</a>
    </div>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">Manage Stock</h5>
            <form action="<?= url('/admin/inventory') ?>" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control" placeholder="Search by name or SKU..." value="<?= e($search) ?>">
                <button class="btn btn-primary ms-2" type="submit"><i class="bi bi-search"></i></button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Current Stock</th>
                            <th>Status</th>
                            <th>Quick Adjust</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products['data'] as $p): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($p['image'])): ?>
                                        <img src="<?= asset('uploads/products/' . $p['image']) ?>" class="rounded me-3" width="40" height="40" style="object-fit:cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center text-muted" style="width:40px;height:40px;"><i class="bi bi-image"></i></div>
                                    <?php endif; ?>
                                    <a href="<?= url('/admin/products/edit/' . $p['id']) ?>" class="fw-bold text-dark"><?= e($p['name']) ?></a>
                                </div>
                            </td>
                            <td><code class="text-muted"><?= e($p['sku']) ?></code></td>
                            <td>
                                <?php if ($p['stock'] <= 0): ?>
                                    <span class="badge bg-danger fs-6"><?= $p['stock'] ?></span>
                                <?php elseif ($p['stock'] <= $p['low_stock_threshold']): ?>
                                    <span class="badge bg-warning text-dark fs-6"><?= $p['stock'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success fs-6"><?= $p['stock'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['status'] === 'active'): ?>
                                    <span class="badge bg-light-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-light-secondary"><?= ucfirst($p['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="min-width: 250px;">
                                <form action="<?= url('/admin/inventory/adjust/' . $p['id']) ?>" method="POST" class="d-flex">
                                    <?= csrf_field() ?>
                                    <select name="type" class="form-select form-select-sm w-auto me-2">
                                        <option value="add">+</option>
                                        <option value="subtract">-</option>
                                        <option value="set">=</option>
                                    </select>
                                    <input type="number" name="quantity" class="form-control form-control-sm me-2" style="width: 70px;" value="1" min="0" required>
                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($products['data'])): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No products found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($products['pages'] > 1): ?>
                <?= pagination_html($products['current_page'], $products['pages'], url('/admin/inventory'), ['search' => $search]) ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
