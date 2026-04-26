<?php
/**
 * Order Success Page (FoodMart Theme)
 */
$order = $order ?? [];
?>
<?php require_once VIEW_PATH . '/frontend/partials/header.php'; ?>

<section class="py-5">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="bg-light rounded-4 p-5">
                    <i class="fas fa-check-circle fa-5x mb-4" style="color:#2d6a4f;"></i>
                    <h2 class="mb-3" style="color:#2d6a4f;">Order Placed Successfully!</h2>
                    <p class="fs-5">Thank you for your order. Your order number is:</p>
                    <h3 class="mb-4" style="color:#2d6a4f;"><?= e($order['order_number'] ?? '') ?></h3>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="border rounded-4 p-3 bg-white">
                                <small class="text-muted">Total Amount</small>
                                <h5 class="mb-0" style="color:#2d6a4f;"><?= formatPrice($order['total_amount'] ?? 0) ?></h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-4 p-3 bg-white">
                                <small class="text-muted">Payment Method</small>
                                <h5 class="mb-0"><?= strtoupper(e($order['payment_method'] ?? 'COD')) ?></h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-4 p-3 bg-white">
                                <small class="text-muted">Status</small>
                                <h5 class="mb-0"><?= ucfirst(e($order['status'] ?? 'pending')) ?></h5>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <a href="<?= url('/my-orders/' . ($order['id'] ?? '')) ?>" class="btn btn-dark rounded-1 py-2 px-4"><i class="fas fa-eye me-2"></i>View Order</a>
                        <a href="<?= url('/shop') ?>" class="btn btn-outline-dark rounded-1 py-2 px-4"><i class="fas fa-shopping-bag me-2"></i>Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once VIEW_PATH . '/frontend/partials/footer.php'; ?>
