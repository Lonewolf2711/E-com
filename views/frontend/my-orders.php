<?php
/**
 * My Orders Page (FoodMart Theme)
 */
$orders = $orders ?? ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
?>
<?php require_once VIEW_PATH . '/frontend/partials/header.php'; ?>

<div class="page-header-foodmart py-4">
    <div class="container-fluid">
        <h1 class="text-center text-white display-6">My Orders</h1>
        <nav><ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/account') ?>">Account</a></li>
            <li class="breadcrumb-item active text-white">My Orders</li>
        </ol></nav>
    </div>
</div>

<section class="py-5">
    <div class="container-fluid">
        <?php if (!empty($orders['data'])): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr><th>Order #</th><th>Date</th><th>Items</th><th>Total</th><th>Payment</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($orders['data'] as $order): ?>
                    <tr>
                        <td class="fw-bold"><?= e($order['order_number']) ?></td>
                        <td><?= formatDate($order['created_at']) ?></td>
                        <td>—</td>
                        <td class="fw-bold"><?= formatPrice($order['total_amount']) ?></td>
                        <td><?php $payBadge = paymentStatusBadge($order['payment_status']); ?>
                            <span class="badge <?= $payBadge['class'] ?>"><?= $payBadge['label'] ?></span></td>
                        <td><?php $badge = orderStatusBadge($order['status']); ?>
                            <span class="badge <?= $badge['class'] ?>"><?= $badge['label'] ?></span></td>
                        <td><a href="<?= url('/my-orders/' . $order['id']) ?>" class="btn btn-sm btn-dark rounded-1">View</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($orders['pages'] > 1): ?>
        <nav class="mt-4"><ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $orders['pages']; $i++): ?>
            <li class="page-item <?= $i === $orders['current_page'] ? 'active' : '' ?>">
                <a class="page-link rounded-1 mx-1" href="<?= url('/my-orders?page=' . $i) ?>"><?= $i ?></a>
            </li>
            <?php endfor; ?>
        </ul></nav>
        <?php endif; ?>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-4x text-muted mb-4"></i>
            <h3 class="text-muted">No orders yet</h3>
            <a href="<?= url('/shop') ?>" class="btn btn-dark rounded-1 py-3 px-5 mt-3"><i class="fas fa-shopping-bag me-2"></i>Start Shopping</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once VIEW_PATH . '/frontend/partials/footer.php'; ?>
