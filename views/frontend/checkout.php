<?php
/**
 * Checkout Page (FoodMart Theme)
 */
$cart = $cart ?? ['items' => [], 'subtotal' => 0];
$user = $user ?? [];
$discount = $discount ?? 0;
$coupon = $coupon ?? null;
$total = $cart['subtotal'] - $discount;
?>
<?php require_once VIEW_PATH . '/frontend/partials/header.php'; ?>

<div class="page-header-foodmart py-4">
    <div class="container-fluid">
        <h1 class="text-center text-white display-6">Checkout</h1>
        <nav><ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/cart') ?>">Cart</a></li>
            <li class="breadcrumb-item active text-white">Checkout</li>
        </ol></nav>
    </div>
</div>

<section class="py-5">
    <div class="container-fluid">
        <form action="<?= url('/checkout/process') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="row g-5">
                <!-- Shipping Details -->
                <div class="col-lg-7">
                    <h4 class="mb-4"><i class="fas fa-truck me-2"></i>Shipping Details</h4>
                    <div class="bg-light rounded-4 p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="shipping_name" value="<?= e($user['name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="shipping_phone" value="<?= e($user['phone'] ?? '') ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="shipping_address" rows="3" required placeholder="House/Flat No., Street, Landmark"><?= e($user['address'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="shipping_city" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">State <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="shipping_state" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pincode <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="shipping_pincode" required pattern="[0-9]{6}" maxlength="6">
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3"><i class="fas fa-credit-card me-2"></i>Payment Method</h5>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" value="cod" id="cod" checked>
                            <label class="form-check-label" for="cod"><i class="fas fa-money-bill-wave me-2"></i>Cash on Delivery</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" value="razorpay" id="razorpay">
                            <label class="form-check-label" for="razorpay"><i class="fas fa-credit-card me-2"></i>Razorpay (Online Payment)</label>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-5">
                    <h4 class="mb-4"><i class="fas fa-receipt me-2"></i>Order Summary</h4>
                    <div class="bg-light rounded-4 p-4">
                        <?php foreach ($cart['items'] as $item): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <img src="<?= $item['product_image'] ? upload_url(e($item['product_image'])) : asset('frontend/foodmart-images/thumb-bananas.png') ?>" class="rounded me-2" style="width:40px;height:40px;object-fit:cover;" alt="">
                                <div>
                                    <small class="d-block"><?= e(truncate($item['product_name'], 25)) ?></small>
                                    <small class="text-muted">×<?= $item['quantity'] ?></small>
                                </div>
                            </div>
                            <span><?= formatPrice($item['line_total']) ?></span>
                        </div>
                        <?php endforeach; ?>
                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span><?= formatPrice($cart['subtotal']) ?></span>
                        </div>
                        <?php if ($discount > 0): ?>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Discount <?= $coupon ? '(' . e($coupon['code']) . ')' : '' ?></span>
                            <span>-<?= formatPrice($discount) ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping</span>
                            <span class="text-success">Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <h5>Total</h5>
                            <h5 style="color:#2d6a4f;"><?= formatPrice($total) ?></h5>
                        </div>

                        <!-- Coupon -->
                        <?php if (!$coupon): ?>
                        <div class="mb-4">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Coupon Code" form="coupon-form" name="coupon_code">
                                <button type="submit" class="btn btn-outline-dark" form="coupon-form">Apply</button>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-success py-2 mb-4">
                            <i class="fas fa-tag me-2"></i>Coupon <strong><?= e($coupon['code']) ?></strong> applied! Saving <?= formatPrice($discount) ?>
                        </div>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-dark rounded-1 w-100 py-3 btn-lg">
                            <i class="fas fa-lock me-2"></i>Place Order — <?= formatPrice($total) ?>
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Coupon form (separate for proper nesting) -->
        <form id="coupon-form" action="<?= url('/checkout/apply-coupon') ?>" method="POST" style="display:none;">
            <?= csrf_field() ?>
        </form>
    </div>
</section>

<?php require_once VIEW_PATH . '/frontend/partials/footer.php'; ?>
