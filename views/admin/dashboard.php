<?php
/**
 * Admin Dashboard View
 * ────────────────────
 * Uses Mazer admin template layout
 */

$stats = $stats ?? [];
$recent_orders = $recent_orders ?? [];
$sales_chart = $sales_chart ?? [];
$top_products = $top_products ?? [];
$low_stock = $low_stock ?? [];
$pending_reviews = $pending_reviews ?? 0;
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
    <h3>Dashboard</h3>
    <p class="text-subtitle text-muted">
        Spare Parts Admin — <?= date('l, d M Y') ?>
    </p>
</div>

<div class="page-content">
    <section class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-3">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <span class="text-muted me-2 fw-bold small">QUICK ACTIONS:</span>
                        <a href="<?= url('/admin/products/add') ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-plus-circle me-1"></i>Add New Part
                        </a>
                        <a href="<?= url('/admin/enquiries') ?>" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-clipboard-check me-1"></i>View Enquiries
                        </a>
                        <a href="<?= url('/admin/banners/add') ?>" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-play-circle me-1"></i>Add Hero Banner
                        </a>
                        <a href="<?= url('/admin/settings') ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-gear me-1"></i>Settings
                        </a>
                        <a href="<?= url('/') ?>" target="_blank" class="btn btn-sm btn-outline-light ms-auto">
                            <i class="bi bi-globe me-1"></i>View Live Website
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="row">
        <!-- Stats Cards Row -->
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon purple mb-2"><i class="bi bi-clipboard-check"></i></div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Total Enquiries</h6>
                            <h6 class="font-extrabold mb-0"><?= number_format($stats['total_orders'] ?? 0) ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon blue mb-2"><i class="bi bi-box-seam"></i></div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Parts Listed</h6>
                            <h6 class="font-extrabold mb-0"><?= number_format($stats['total_products'] ?? 0) ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon green mb-2"><i class="bi bi-tags"></i></div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Active Categories</h6>
                            <h6 class="font-extrabold mb-0"><?= $stats['total_categories'] ?? '-' ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon red mb-2"><i class="iconly-boldBookmark"></i></div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Total Parts</h6>
                            <h6 class="font-extrabold mb-0"><?= number_format($stats['total_products'] ?? 0) ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="row">
        <!-- Sales Chart -->
        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4>Monthly Enquiries</h4>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-12 col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4>Quick Stats</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="badge bg-warning rounded-circle p-2 me-3"><i class="bi bi-hourglass-split text-white"></i></div>
                        <div><span class="fw-bold"><?= $stats['pending_orders'] ?? 0 ?></span> New Enquiries</div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="badge bg-info rounded-circle p-2 me-3"><i class="bi bi-gear text-white"></i></div>
                        <div><span class="fw-bold"><?= $stats['processing_orders'] ?? 0 ?></span> In Progress</div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="badge bg-danger rounded-circle p-2 me-3"><i class="bi bi-exclamation-triangle text-white"></i></div>
                        <div><span class="fw-bold"><?= count($low_stock) ?></span> Low Stock Parts</div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="badge bg-primary rounded-circle p-2 me-3"><i class="bi bi-chat-dots text-white"></i></div>
                        <div><span class="fw-bold"><?= $pending_reviews ?></span> Parts Needing SEO</div>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="card">
                <div class="card-header"><h4>Most Enquired Parts</h4></div>
                <div class="card-body pb-0">
                    <?php foreach ($top_products as $tp): ?>
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?= $tp['image'] ? upload_url(e($tp['image'])) : asset('admin/images/faces/1.jpg') ?>" class="rounded me-3" style="width:40px;height:40px;object-fit:cover;" alt="">
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-bold"><?= e(truncate($tp['name'], 25)) ?></p>
                            <small class="text-muted font-monospace"><?= e($tp['sku'] ?? '—') ?></small>
                        </div>
                        <span class="badge" style="background:#E85D04;"><?= $tp['total_sold'] ?? 0 ?> sold</span>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($top_products)): ?>
                    <p class="text-muted text-center py-3">No sales data yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Orders -->
    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Recent Enquiries</h4>
                    <a href="<?= url('/admin/enquiries') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-lg">
                            <thead><tr><th>Enquiry #</th><th>Contact Name</th><th>Parts Count</th><th>Status</th><th>Date</th></tr></thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                <tr onclick="window.location='<?= url('/admin/enquiries/' . $order['id']) ?>'" style="cursor:pointer;">
                                    <td class="fw-bold"><?= e($order['order_number']) ?></td>
                                    <td><?= e($order['customer_name'] ?? 'Guest') ?></td>
                                    <td><?= $order['item_count'] ?? '—' ?> parts</td>
                                    <td><?php $ob = orderStatusBadge($order['status']); ?><span class="badge <?= $ob['class'] ?>"><?= $ob['label'] ?></span></td>
                                    <td class="text-muted"><?= formatDate($order['created_at']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($recent_orders)): ?>
                                <tr><td colspan="6" class="text-center text-muted py-4">No orders yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Low Stock Alerts -->
    <?php if (!empty($low_stock)): ?>
    <section class="row">
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-header bg-light-danger d-flex justify-content-between align-items-center">
                    <h4 class="text-danger mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Low Stock Alerts</h4>
                    <a href="<?= url('/admin/inventory/low-stock') ?>" class="btn btn-sm btn-danger">Manage Inventory</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Product</th><th>SKU</th><th>Stock</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php foreach ($low_stock as $item): ?>
                                <tr>
                                    <td><?= e($item['name']) ?></td>
                                    <td><code class="text-warning"><?= e($item['sku']) ?></code></td>
                                    <td><span class="badge bg-danger"><?= $item['stock'] ?></span></td>
                                    <td><a href="<?= url('/admin/products/edit/' . $item['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const salesData = <?= json_encode($sales_chart) ?>;
const labels = salesData.map(d => d.month);
const revenue = salesData.map(d => parseFloat(d.revenue));
const orders = salesData.map(d => parseInt(d.total_orders));

new Chart(document.getElementById('salesChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Enquiries',
            data: revenue,
            backgroundColor: 'rgba(232, 93, 4, 0.6)',
            borderColor: 'rgba(232, 93, 4, 1)',
            borderWidth: 1,
            borderRadius: 5,
            yAxisID: 'y',
        }, {
            label: 'Parts Viewed',
            data: orders,
            type: 'line',
            borderColor: '#198754',
            backgroundColor: 'rgba(25, 135, 84, 0.1)',
            tension: 0.4,
            fill: true,
            yAxisID: 'y1',
        }]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        scales: {
            y: { type: 'linear', position: 'left', beginAtZero: true },
            y1: { type: 'linear', position: 'right', beginAtZero: true, grid: { drawOnChartArea: false } }
        }
    }
});
</script>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
