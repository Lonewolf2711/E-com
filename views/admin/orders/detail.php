<?php
/**
 * Admin Order Detail
 */
$order = $order ?? [];
$items = $items ?? [];
$tracking = $tracking ?? [];
$payment = $payment ?? null;
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3>Order #<?= e($order['order_number'] ?? '') ?></h3>
            <p class="text-muted mb-0">Placed on <?= formatDateTime($order['created_at'] ?? '') ?></p>
        </div>
        <a href="<?= url('/admin/orders') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="page-content">
    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Order Items -->
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Order Items</h4></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead><tr><th></th><th>Product</th><th>Price</th><th>Qty</th><th>Total</th></tr></thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><img src="<?= $item['product_image'] ? upload_url(e($item['product_image'])) : asset('admin/images/faces/1.jpg') ?>" class="rounded" style="width:40px;height:40px;object-fit:cover;" alt=""></td>
                                    <td>
                                        <p class="fw-bold mb-0"><?= e($item['product_name']) ?></p>
                                        <small class="text-muted">SKU: <?= e($item['product_sku'] ?? 'N/A') ?></small>
                                    </td>
                                    <td><?= formatPrice($item['price']) ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td class="fw-bold"><?= formatPrice($item['total']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Subtotal:</td>
                                    <td class="fw-bold"><?= formatPrice($order['subtotal'] ?? 0) ?></td>
                                </tr>
                                <?php if (($order['discount_amount'] ?? 0) > 0): ?>
                                <tr class="text-success">
                                    <td colspan="4" class="text-end fw-bold">Discount:</td>
                                    <td class="fw-bold">-<?= formatPrice($order['discount_amount']) ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Shipping:</td>
                                    <td><?= ($order['shipping_fee'] ?? 0) > 0 ? formatPrice($order['shipping_fee']) : 'Free' ?></td>
                                </tr>
                                <tr class="table-primary">
                                    <td colspan="4" class="text-end fw-bold fs-5">Total:</td>
                                    <td class="fw-bold fs-5"><?= formatPrice($order['total_amount'] ?? 0) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tracking Timeline -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Tracking Timeline</h4>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addTrackingModal"><i class="bi bi-plus me-1"></i>Add Entry</button>
                </div>
                <div class="card-body">
                    <?php if (!empty($tracking)): ?>
                    <div class="timeline-list">
                        <?php foreach ($tracking as $t): ?>
                        <div class="d-flex mb-4">
                            <div class="me-3 mt-1">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                    <i class="bi bi-check2 text-white small"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <?php $tb = orderStatusBadge($t['status']); ?>
                                    <span class="badge <?= $tb['class'] ?>"><?= $tb['label'] ?></span>
                                    <small class="text-muted"><?= formatDateTime($t['created_at']) ?></small>
                                </div>
                                <p class="text-muted mb-0 mt-1"><?= e($t['message']) ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-muted text-center py-3">No tracking entries yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Status Update -->
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Update Status</h4></div>
                <div class="card-body">
                    <?php $currentBadge = orderStatusBadge($order['status'] ?? 'pending'); ?>
                    <p class="mb-3">Current: <span class="badge <?= $currentBadge['class'] ?> fs-6"><?= $currentBadge['label'] ?></span></p>
                    <form action="<?= url('/admin/orders/' . $order['id'] . '/status') ?>" method="POST">
                        <?= csrf_field() ?>
                        <select class="form-select mb-3" name="status">
                            <?php foreach (['pending','confirmed','packed','shipped','delivered','cancelled'] as $s): ?>
                            <option value="<?= $s ?>" <?= ($order['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Update order status?')"><i class="bi bi-check-circle me-2"></i>Update Status</button>
                    </form>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Customer</h4></div>
                <div class="card-body">
                    <p class="fw-bold mb-1"><?= e($order['customer_name'] ?? 'Guest') ?></p>
                    <p class="text-muted mb-1"><?= e($order['customer_email'] ?? '') ?></p>
                    <p class="text-muted mb-0"><?= e($order['customer_phone'] ?? '') ?></p>
                    <?php if (!empty($order['user_id'])): ?>
                    <a href="<?= url('/admin/customers/' . $order['user_id']) ?>" class="btn btn-sm btn-outline-primary mt-2"><i class="bi bi-person me-1"></i>View Profile</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Shipping Address</h4></div>
                <div class="card-body">
                    <p class="mb-1"><?= e($order['shipping_name'] ?? '') ?></p>
                    <p class="mb-1"><?= e($order['shipping_address'] ?? '') ?></p>
                    <p class="mb-1"><?= e($order['shipping_city'] ?? '') ?>, <?= e($order['shipping_state'] ?? '') ?></p>
                    <p class="mb-1"><?= e($order['shipping_pincode'] ?? '') ?></p>
                    <p class="mb-0"><i class="bi bi-telephone me-1"></i><?= e($order['shipping_phone'] ?? '') ?></p>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Payment</h4></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Method:</span>
                        <span class="fw-bold text-uppercase"><?= e($order['payment_method'] ?? 'COD') ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Status:</span>
                        <?php $payBadge = paymentStatusBadge($order['payment_status'] ?? 'pending'); ?>
                        <span class="badge <?= $payBadge['class'] ?>"><?= $payBadge['label'] ?></span>
                    </div>
                    <?php if ($payment): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Transaction:</span>
                        <span class="text-muted small"><?= e($payment['transaction_id'] ?? 'N/A') ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Amount:</span>
                        <span class="fw-bold"><?= formatPrice($payment['amount'] ?? 0) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($order['coupon_code'])): ?>
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Coupon</h4></div>
                <div class="card-body">
                    <span class="badge bg-info fs-6"><?= e($order['coupon_code']) ?></span>
                    <p class="text-muted mt-2 mb-0">Saved <?= formatPrice($order['discount_amount'] ?? 0) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Tracking Modal -->
<div class="modal fade" id="addTrackingModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= url('/admin/orders/' . $order['id'] . '/tracking') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Tracking Entry</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status Label <span class="text-danger">*</span></label>
                        <select class="form-select" name="tracking_status" required>
                            <?php foreach (['pending','confirmed','packed','shipped','out_for_delivery','delivered','cancelled','returned'] as $ts): ?>
                            <option value="<?= $ts ?>"><?= ucfirst(str_replace('_',' ',$ts)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="tracking_message" rows="3" required placeholder="e.g. Package has been dispatched from warehouse"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Add Entry</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
