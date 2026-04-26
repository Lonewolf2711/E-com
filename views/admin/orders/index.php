<?php
/**
 * Admin Orders List
 */
$orders = $orders ?? ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
$filters = $filters ?? [];
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
    <h3>Orders</h3>
</div>

<div class="page-content">
    <!-- Filters -->
    <div class="card">
        <div class="card-body">
            <form method="GET" action="<?= url('/admin/orders') ?>" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" name="q" value="<?= e($filters['search'] ?? '') ?>" placeholder="Order #, name...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All</option>
                        <?php foreach (['pending','confirmed','packed','shipped','delivered','cancelled'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Payment</label>
                    <select class="form-select" name="payment_status">
                        <option value="">All</option>
                        <?php foreach (['pending','paid','failed','refunded'] as $ps): ?>
                        <option value="<?= $ps ?>" <?= ($filters['payment_status'] ?? '') === $ps ? 'selected' : '' ?>><?= ucfirst($ps) ?></option>
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
                    <a href="<?= url('/admin/orders') ?>" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-body">
            <p class="text-muted mb-3">Showing <?= count($orders['data']) ?> of <?= $orders['total'] ?> orders</p>
            <div class="table-responsive">
                <table class="table table-hover table-lg">
                    <thead>
                        <tr><th>Order #</th><th>Customer</th><th>Items</th><th>Total</th><th>Payment</th><th>Method</th><th>Status</th><th>Date</th><th style="width:80px;"></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders['data'] as $order): ?>
                        <tr>
                            <td class="fw-bold"><?= e($order['order_number']) ?></td>
                            <td>
                                <p class="mb-0"><?= e($order['customer_name'] ?? 'Guest') ?></p>
                                <small class="text-muted"><?= e($order['customer_email'] ?? '') ?></small>
                            </td>
                            <td><span class="badge bg-light-secondary"><?= $order['item_count'] ?? '—' ?></span></td>
                            <td class="fw-bold"><?= formatPrice($order['total_amount']) ?></td>
                            <td>
                                <?php $pb = paymentStatusBadge($order['payment_status']); ?>
                                <span class="badge <?= $pb['class'] ?>"><?= $pb['label'] ?></span>
                            </td>
                            <td><span class="text-uppercase small fw-bold"><?= e($order['payment_method'] ?? 'COD') ?></span></td>
                            <td>
                                <?php $ob = orderStatusBadge($order['status']); ?>
                                <span class="badge <?= $ob['class'] ?>"><?= $ob['label'] ?></span>
                            </td>
                            <td class="text-muted small"><?= formatDate($order['created_at']) ?></td>
                            <td>
                                <a href="<?= url('/admin/orders/' . $order['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($orders['data'])): ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">No orders found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($orders['pages'] > 1): ?>
            <?= pagination_html($orders['current_page'], $orders['pages'], url('/admin/orders'), array_filter($filters)) ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
