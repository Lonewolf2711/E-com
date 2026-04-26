<?php
/**
 * Admin Product Report
 */
$bestSellers = $bestSellers ?? [];
$categoryPerf = $categoryPerf ?? [];
$reviewStats = $reviewStats ?? ['total' => 0, 'avg_rating' => 0, 'pending' => 0];
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
    <h3>Product Report</h3>
</div>

<div class="page-content">
    <!-- Review Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted small text-uppercase">Total Reviews</h6>
                    <h4 class="fw-bold"><?= $reviewStats['total'] ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted small text-uppercase">Average Rating</h6>
                    <h4 class="fw-bold text-warning"><?= round($reviewStats['avg_rating'], 1) ?> <i class="bi bi-star-fill"></i></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted small text-uppercase">Pending Reviews</h6>
                    <h4 class="fw-bold text-danger"><?= $reviewStats['pending'] ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Best Sellers -->
    <div class="card">
        <div class="card-header"><h5 class="card-title">Best Selling Products</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>#</th><th>Product</th><th>SKU</th><th>Category</th><th>Price</th><th>Units Sold</th><th>Revenue</th><th>Stock</th></tr></thead>
                    <tbody>
                        <?php foreach ($bestSellers as $i => $p): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-bold"><?= e($p['name']) ?></td>
                            <td><code><?= e($p['sku']) ?></code></td>
                            <td><?= e($p['category_name'] ?? '-') ?></td>
                            <td><?= formatPrice($p['price']) ?></td>
                            <td><span class="badge bg-primary"><?= $p['units_sold'] ?></span></td>
                            <td class="fw-bold text-success"><?= formatPrice($p['revenue']) ?></td>
                            <td>
                                <?php if ($p['stock'] <= 0): ?>
                                    <span class="badge bg-danger"><?= $p['stock'] ?></span>
                                <?php else: ?>
                                    <span class="text-muted"><?= $p['stock'] ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Category Performance -->
    <div class="card">
        <div class="card-header"><h5 class="card-title">Category Performance</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>Category</th><th>Products</th><th>Units Sold</th><th>Revenue</th></tr></thead>
                    <tbody>
                        <?php foreach ($categoryPerf as $c): ?>
                        <tr>
                            <td class="fw-bold"><?= e($c['name']) ?></td>
                            <td><?= $c['product_count'] ?></td>
                            <td><?= $c['units_sold'] ?></td>
                            <td class="fw-bold text-success"><?= formatPrice($c['revenue']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
