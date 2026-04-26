<?php
/**
 * Admin Low Stock Alerts
 */
$products = $products ?? ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <h3>Low Stock Alerts</h3>
        <a href="<?= url('/admin/inventory') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back to Inventory</a>
    </div>
</div>

<div class="page-content">
    <div class="card border-warning border-top border-4">
        <div class="card-header">
            <h5 class="card-title text-warning"><i class="bi bi-exclamation-triangle-fill me-2"></i>Items requiring restock (<?= $products['total'] ?>)</h5>
            <p class="text-muted mb-0">Products in this list have stock levels equal to or below their low stock threshold.</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Current Stock</th>
                            <th>Threshold</th>
                            <th>Category</th>
                            <th>Quick Restock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products['data'] as $p): ?>
                        <tr>
                            <td class="fw-bold"><a href="<?= url('/admin/products/edit/' . $p['id']) ?>" class="text-dark"><?= e($p['name']) ?></a></td>
                            <td><code class="text-muted"><?= e($p['sku']) ?></code></td>
                            <td>
                                <?php if ($p['stock'] <= 0): ?>
                                    <span class="badge bg-danger fs-6"><?= $p['stock'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark fs-6"><?= $p['stock'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted"><?= $p['low_stock_threshold'] ?></td>
                            <td><?= e($p['category_name'] ?? '-') ?></td>
                            <td style="min-width: 200px;">
                                <form action="<?= url('/admin/inventory/adjust/' . $p['id']) ?>" method="POST" class="d-flex">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="type" value="add">
                                    <input type="number" name="quantity" class="form-control form-control-sm me-2" style="width: 80px;" placeholder="+Qty" min="1" required>
                                    <button type="submit" class="btn btn-sm btn-success">Restock</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($products['data'])): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No low stock items. Everything is fully stocked!</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($products['pages'] > 1): ?>
                <?= pagination_html($products['current_page'], $products['pages'], url('/admin/inventory/low-stock')) ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
