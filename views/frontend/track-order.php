<?php
/**
 * Track Order Page (FoodMart Theme)
 */
$order_number = $order_number ?? '';
$order = $order ?? null;
?>
<?php require_once VIEW_PATH . '/frontend/partials/header.php'; ?>

<div class="page-header-foodmart py-4">
    <div class="container-fluid">
        <h1 class="text-center text-white display-6">Track Order</h1>
        <nav><ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item active text-white">Track Order</li>
        </ol></nav>
    </div>
</div>

<section class="py-5">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="bg-light rounded-4 p-5 mb-4">
                    <h4 class="mb-3"><i class="fas fa-search me-2"></i>Enter Order Number</h4>
                    <form action="<?= url('/track-order') ?>" method="GET" class="d-flex gap-2">
                        <input type="text" class="form-control" name="q" placeholder="e.g. ORD-20260315-0001" value="<?= e($order_number) ?>" required>
                        <button type="submit" class="btn btn-dark rounded-1 px-4"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <?php if ($order_number && !$order): ?>
                <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>No order found with number "<strong><?= e($order_number) ?></strong>".</div>
                <?php endif; ?>

                <?php if ($order): ?>
                <div class="bg-light rounded-4 p-5">
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Order #<?= e($order['order_number']) ?></h5>
                        <?php $badge = orderStatusBadge($order['status']); ?>
                        <span class="badge <?= $badge['class'] ?> fs-6"><?= $badge['label'] ?></span>
                    </div>
                    <p class="text-muted">Placed on <?= formatDate($order['created_at']) ?></p>
                    <p><strong>Total:</strong> <?= formatPrice($order['total_amount']) ?></p>

                    <!-- Tracking Timeline -->
                    <?php if (!empty($order['tracking'])): ?>
                    <h5 class="mt-4 mb-3">Order Timeline</h5>
                    <div class="timeline">
                        <?php foreach ($order['tracking'] as $track): ?>
                        <div class="d-flex mb-3">
                            <div class="me-3"><i class="fas fa-circle" style="font-size:10px;color:#2d6a4f;"></i></div>
                            <div>
                                <strong><?= e($track['status']) ?></strong>
                                <p class="mb-0 text-muted small"><?= e($track['message']) ?></p>
                                <small class="text-muted"><?= formatDate($track['created_at'], true) ?></small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once VIEW_PATH . '/frontend/partials/footer.php'; ?>
