<?php
/**
 * Admin Customer Report
 */
$registrations = $registrations ?? [];
$topSpenders = $topSpenders ?? [];
$stats = $stats ?? ['total' => 0, 'active' => 0, 'this_month' => 0];
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
    <h3>Customer Report</h3>
</div>

<div class="page-content">
    <!-- Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted small text-uppercase">Total Customers</h6>
                    <h4 class="fw-bold"><?= $stats['total'] ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted small text-uppercase">Active Customers</h6>
                    <h4 class="fw-bold text-success"><?= $stats['active'] ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted small text-uppercase">New This Month</h6>
                    <h4 class="fw-bold text-primary"><?= $stats['this_month'] ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Chart -->
    <div class="card">
        <div class="card-header"><h5 class="card-title">Monthly Registrations (Last 12 Months)</h5></div>
        <div class="card-body">
            <canvas id="regChart" height="100"></canvas>
        </div>
    </div>

    <!-- Top Spenders -->
    <div class="card">
        <div class="card-header"><h5 class="card-title">Top Spending Customers</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>#</th><th>Customer</th><th>Email</th><th>Orders</th><th>Total Spent</th><th>Joined</th></tr></thead>
                    <tbody>
                        <?php foreach ($topSpenders as $i => $c): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-bold">
                                <a href="<?= url('/admin/customers/' . $c['id']) ?>"><?= e($c['name']) ?></a>
                            </td>
                            <td><?= e($c['email']) ?></td>
                            <td><?= $c['total_orders'] ?></td>
                            <td class="fw-bold text-success"><?= formatPrice($c['total_spent']) ?></td>
                            <td class="text-muted small"><?= formatDate($c['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const regData = <?= json_encode($registrations) ?>;
new Chart(document.getElementById('regChart'), {
    type: 'bar',
    data: {
        labels: regData.map(r => r.month),
        datasets: [{
            label: 'New Customers',
            data: regData.map(r => r.count),
            backgroundColor: '#435ebe'
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});
</script>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
