<?php
/**
 * Order Detail Page (FoodMart Theme)
 */
$order = $order ?? [];
?>
<?php require_once VIEW_PATH . '/frontend/partials/header.php'; ?>

<div class="page-header-foodmart py-4">
    <div class="container-fluid">
        <h1 class="text-center text-white display-6">Order #<?= e($order['order_number'] ?? '') ?></h1>
        <nav><ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/my-orders') ?>">My Orders</a></li>
            <li class="breadcrumb-item active text-white">#<?= e($order['order_number'] ?? '') ?></li>
        </ol></nav>
    </div>
</div>

<section class="py-5">
    <div class="container-fluid">
        <div class="row g-5">
            <!-- Order Items -->
            <div class="col-lg-8">
                <div class="bg-light rounded-4 p-4 mb-4">
                    <h5 class="mb-3">Order Items</h5>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th></tr></thead>
                            <tbody>
                                <?php foreach ($order['items'] ?? [] as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?= $item['product_image'] ? upload_url(e($item['product_image'])) : asset('frontend/foodmart-images/thumb-bananas.png') ?>" class="rounded me-2" style="width:40px;height:40px;object-fit:cover;" alt="">
                                            <a href="<?= url('/product/' . e($item['product_slug'] ?? '')) ?>"><?= e($item['product_name']) ?></a>
                                        </div>
                                    </td>
                                    <td><?= formatPrice($item['price']) ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td class="fw-bold"><?= formatPrice($item['total']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tracking -->
                <?php if (!empty($order['tracking'])): ?>
                <div class="bg-light rounded-4 p-4">
                    <h5 class="mb-3">Order Timeline</h5>
                    <?php foreach ($order['tracking'] as $track): ?>
                    <div class="d-flex mb-3">
                        <div class="me-3 mt-1"><i class="fas fa-circle" style="font-size:8px;color:#2d6a4f;"></i></div>
                        <div>
                            <strong><?= e($track['status']) ?></strong>
                            <p class="mb-0 small text-muted"><?= e($track['message']) ?></p>
                            <small class="text-muted"><?= formatDate($track['created_at'], true) ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Summary -->
            <div class="col-lg-4">
                <div class="bg-light rounded-4 p-4 mb-4">
                    <h5 class="mb-3">Order Summary</h5>
                    <?php $badge = orderStatusBadge($order['status']); ?>
                    <div class="mb-3"><span class="badge <?= $badge['class'] ?> fs-6"><?= $badge['label'] ?></span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><span><?= formatPrice($order['subtotal'] ?? 0) ?></span></div>
                    <?php if (($order['discount_amount'] ?? 0) > 0): ?>
                    <div class="d-flex justify-content-between mb-2 text-success"><span>Discount</span><span>-<?= formatPrice($order['discount_amount']) ?></span></div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between mb-2"><span>Shipping</span><span><?= ($order['shipping_fee'] ?? 0) > 0 ? formatPrice($order['shipping_fee']) : 'Free' ?></span></div>
                    <hr>
                    <div class="d-flex justify-content-between"><h5>Total</h5><h5 style="color:#2d6a4f;"><?= formatPrice($order['total_amount'] ?? 0) ?></h5></div>
                </div>

                <div class="bg-light rounded-4 p-4 mb-4">
                    <h5 class="mb-3">Shipping Address</h5>
                    <p class="mb-1"><?= e($order['shipping_name'] ?? '') ?></p>
                    <p class="mb-1"><?= e($order['shipping_address'] ?? '') ?></p>
                    <p class="mb-1"><?= e($order['shipping_city'] ?? '') ?>, <?= e($order['shipping_state'] ?? '') ?> — <?= e($order['shipping_pincode'] ?? '') ?></p>
                    <p class="mb-0"><?= e($order['shipping_phone'] ?? '') ?></p>
                </div>

                <div class="bg-light rounded-4 p-4">
                    <h5 class="mb-3">Payment</h5>
                    <p class="mb-1"><strong>Method:</strong> <?= strtoupper(e($order['payment_method'] ?? 'COD')) ?></p>
                    <?php $payBadge = paymentStatusBadge($order['payment_status'] ?? 'pending'); ?>
                    <p class="mb-0"><strong>Status:</strong> <span class="badge <?= $payBadge['class'] ?>"><?= $payBadge['label'] ?></span></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once VIEW_PATH . '/frontend/partials/footer.php'; ?>
