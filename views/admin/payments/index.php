<?php
/**
 * Admin Payments List
 */
$payments = $payments ?? ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
$filters = $filters ?? [];
$distribution = $distribution ?? [];
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <h3>Payments</h3>
        <a href="<?= url('/admin/payments/transactions') ?>" class="btn btn-outline-primary"><i class="bi bi-list-ul me-2"></i>Transaction Log</a>
    </div>
</div>

<div class="page-content">
    <!-- Payment Method Distribution -->
    <?php if (!empty($distribution)): ?>
    <div class="row mb-4">
        <?php foreach ($distribution as $d): ?>
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase small"><?= e(strtoupper($d['method'])) ?></h6>
                    <h4 class="fw-bold text-primary"><?= formatPrice($d['total']) ?></h4>
                    <small class="text-muted"><?= $d['count'] ?> transactions</small>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="card">
        <div class="card-body">
            <form method="GET" action="<?= url('/admin/payments') ?>" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All</option>
                        <?php foreach (['pending','paid','success','failed','refunded'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Method</label>
                    <select class="form-select" name="method">
                        <option value="">All</option>
                        <?php foreach (['razorpay','stripe','paypal','cod'] as $m): ?>
                        <option value="<?= $m ?>" <?= ($filters['method'] ?? '') === $m ? 'selected' : '' ?>><?= strtoupper($m) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" class="form-control" name="date_from" value="<?= e($filters['date_from'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" class="form-control" name="date_to" value="<?= e($filters['date_to'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary me-1"><i class="bi bi-search"></i></button>
                    <a href="<?= url('/admin/payments') ?>" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-lg">
                    <thead><tr><th>Order #</th><th>Customer</th><th>Method</th><th>Amount</th><th>Transaction ID</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php foreach ($payments['data'] as $p): ?>
                        <tr>
                            <td><a href="<?= url('/admin/orders/' . $p['order_id']) ?>" class="fw-bold"><?= e($p['order_number'] ?? '#' . $p['order_id']) ?></a></td>
                            <td><?= e($p['customer_name'] ?? 'Guest') ?></td>
                            <td><span class="badge bg-light-primary text-uppercase"><?= e($p['method'] ?? 'COD') ?></span></td>
                            <td class="fw-bold"><?= formatPrice($p['amount'] ?? 0) ?></td>
                            <td><code class="small"><?= e($p['transaction_id'] ?? 'N/A') ?></code></td>
                            <td>
                                <?php $pb = paymentStatusBadge($p['payment_status'] ?? 'pending'); ?>
                                <span class="badge <?= $pb['class'] ?>"><?= $pb['label'] ?></span>
                            </td>
                            <td class="text-muted small"><?= formatDate($p['created_at'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($payments['data'])): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">No payments found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($payments['pages'] > 1): ?>
            <?= pagination_html($payments['current_page'], $payments['pages'], url('/admin/payments'), array_filter($filters)) ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
