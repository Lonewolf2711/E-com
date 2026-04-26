<?php
/**
 * Admin Transaction Log
 */
$payments = $payments ?? ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <h3>Transaction Log</h3>
        <a href="<?= url('/admin/payments') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-body">
            <p class="text-muted mb-3">Total: <?= $payments['total'] ?> transactions</p>
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead><tr><th>#</th><th>Order</th><th>Method</th><th>Amount</th><th>Transaction ID</th><th>Status</th><th>Gateway Response</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php foreach ($payments['data'] as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><a href="<?= url('/admin/orders/' . $p['order_id']) ?>"><?= e($p['order_number'] ?? '#' . $p['order_id']) ?></a></td>
                            <td class="text-uppercase small fw-bold"><?= e($p['method'] ?? '') ?></td>
                            <td><?= formatPrice($p['amount'] ?? 0) ?></td>
                            <td><code class="small"><?= e($p['transaction_id'] ?? '-') ?></code></td>
                            <td>
                                <?php $pb = paymentStatusBadge($p['payment_status'] ?? 'pending'); ?>
                                <span class="badge <?= $pb['class'] ?>"><?= $pb['label'] ?></span>
                            </td>
                            <td>
                                <?php if (!empty($p['gateway_response'])): ?>
                                <button class="btn btn-sm btn-outline-secondary" onclick="alert(this.dataset.json)" data-json="<?= e($p['gateway_response']) ?>"><i class="bi bi-code-slash"></i></button>
                                <?php else: ?>—<?php endif; ?>
                            </td>
                            <td class="text-muted small"><?= formatDateTime($p['created_at'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($payments['pages'] > 1): ?>
            <?= pagination_html($payments['current_page'], $payments['pages'], url('/admin/payments/transactions')) ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
