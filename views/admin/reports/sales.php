<?php
/**
 * Admin Sales Report
 */
$dailySales = $dailySales ?? [];
$summary = $summary ?? ['total_orders' => 0, 'total_revenue' => 0, 'avg_order_value' => 0];
$methodBreakdown = $methodBreakdown ?? [];
$period = $period ?? '30';
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <h3>Sales Report</h3>
        <form class="d-flex align-items-center" method="GET" action="<?= url('/admin/reports/sales') ?>">
            <select name="period" class="form-select w-auto me-2">
                <option value="7" <?= $period == '7' ? 'selected' : '' ?>>Last 7 Days</option>
                <option value="30" <?= $period == '30' ? 'selected' : '' ?>>Last 30 Days</option>
                <option value="90" <?= $period == '90' ? 'selected' : '' ?>>Last 90 Days</option>
                <option value="365" <?= $period == '365' ? 'selected' : '' ?>>Last 365 Days</option>
            </select>
            <button class="btn btn-primary">Apply</button>
        </form>
    </div>
</div>

<div class="page-content">
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase small">Total Revenue</h6>
                    <h3 class="fw-bold text-success"><?= formatPrice($summary['total_revenue']) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase small">Total Orders</h6>
                    <h3 class="fw-bold text-primary"><?= $summary['total_orders'] ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase small">Avg. Order Value</h6>
                    <h3 class="fw-bold text-info"><?= formatPrice($summary['avg_order_value']) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="card">
        <div class="card-header"><h5 class="card-title">Daily Revenue</h5></div>
        <div class="card-body">
            <canvas id="salesChart" height="100"></canvas>
        </div>
    </div>

    <!-- Payment Method Breakdown -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5 class="card-title">Payment Method Breakdown</h5></div>
                <div class="card-body">
                    <table class="table">
                        <thead><tr><th>Method</th><th>Transactions</th><th>Total</th></tr></thead>
                        <tbody>
                            <?php foreach ($methodBreakdown as $m): ?>
                            <tr>
                                <td class="text-uppercase fw-bold"><?= e($m['method']) ?></td>
                                <td><?= $m['count'] ?></td>
                                <td class="fw-bold"><?= formatPrice($m['total']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($methodBreakdown)): ?>
                            <tr><td colspan="3" class="text-muted text-center">No data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5 class="card-title">Payment Distribution</h5></div>
                <div class="card-body">
                    <canvas id="methodChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const salesData = <?= json_encode($dailySales) ?>;
new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: salesData.map(d => d.date),
        datasets: [{
            label: 'Revenue',
            data: salesData.map(d => d.revenue),
            borderColor: '#435ebe',
            backgroundColor: 'rgba(67,94,190,0.1)',
            fill: true,
            tension: 0.3
        }, {
            label: 'Orders',
            data: salesData.map(d => d.orders),
            borderColor: '#198754',
            backgroundColor: 'rgba(25,135,84,0.1)',
            fill: false,
            tension: 0.3,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true, position: 'left' },
            y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } }
        }
    }
});

// Method Chart
const methodData = <?= json_encode($methodBreakdown) ?>;
new Chart(document.getElementById('methodChart'), {
    type: 'doughnut',
    data: {
        labels: methodData.map(m => m.method.toUpperCase()),
        datasets: [{
            data: methodData.map(m => m.total),
            backgroundColor: ['#435ebe', '#198754', '#ffc107', '#dc3545', '#6c757d']
        }]
    }
});
</script>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
