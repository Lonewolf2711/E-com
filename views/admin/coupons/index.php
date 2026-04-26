<?php
/**
 * Admin Coupons Management
 **/
$coupons = $coupons ?? ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <h3>Coupons</h3>
        <div>
            <a href="<?= url('/admin/coupons/campaigns') ?>" class="btn btn-outline-info me-2"><i class="bi bi-bar-chart-line me-2"></i>Performance</a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCouponModal">
                <i class="bi bi-plus-circle me-2"></i>Add Coupon
            </button>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Discount</th>
                            <th>Min Order</th>
                            <th>Usage Limit</th>
                            <th>Used</th>
                            <th>Expires On</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($coupons['data'] as $c): ?>
                        <tr>
                            <td class="fw-bold"><span class="badge bg-light-secondary text-dark fs-6"><?= e($c['code']) ?></span></td>
                            <td>
                                <?php if ($c['type'] === 'percent'): ?>
                                    <?= floatval($c['discount_value']) ?>% 
                                    <?php if ($c['max_discount'] > 0): ?>
                                    <small class="text-muted text-nowrap">(Max <?= formatPrice($c['max_discount']) ?>)</small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?= formatPrice($c['discount_value']) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= $c['min_order_amount'] > 0 ? formatPrice($c['min_order_amount']) : 'None' ?></td>
                            <td><?= $c['max_uses'] > 0 ? $c['max_uses'] : 'Unlimited' ?></td>
                            <td><?= $c['used_count'] ?></td>
                            <td>
                                <?php if (strtotime($c['expiry_date']) < time()): ?>
                                    <span class="text-danger fw-bold"><i class="bi bi-x-circle me-1"></i>Expired</span>
                                <?php else: ?>
                                    <?= date('M d, Y', strtotime($c['expiry_date'])) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($c['status'] === 'active'): ?>
                                    <span class="badge bg-light-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-light-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form action="<?= url('/admin/coupons/delete/' . $c['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this coupon?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($coupons['data'])): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">No coupons created yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($coupons['pages'] > 1): ?>
                <?= pagination_html($coupons['current_page'], $coupons['pages'], url('/admin/coupons')) ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Coupon Modal -->
<div class="modal fade" id="addCouponModal" tabindex="-1" aria-labelledby="addCouponModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" action="<?= url('/admin/coupons/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title" id="addCouponModalLabel">Create New Coupon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Coupon Code (Uppercase)</label>
                        <input type="text" class="form-control" name="code" placeholder="SUMMER20" required style="text-transform: uppercase;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Discount Type</label>
                        <select class="form-select" name="type" required>
                            <option value="percent">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (<?= APP_CURRENCY_SYMBOL ?>)</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Discount Value</label>
                        <input type="number" class="form-control" name="discount_value" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Max Discount (for %)</label>
                        <input type="number" class="form-control" name="max_discount" value="0" min="0" step="0.01" title="Leave 0 for no limit">
                        <div class="form-text">0 = No limit</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Min Order Amount</label>
                        <input type="number" class="form-control" name="min_order_amount" value="0" min="0" step="0.01">
                        <div class="form-text">0 = No minimum</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Max Uses (Total)</label>
                        <input type="number" class="form-control" name="max_uses" value="0" min="0">
                        <div class="form-text">0 = Unlimited uses</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Expiry Date</label>
                        <input type="datetime-local" class="form-control" name="expiry_date" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Coupon</button>
            </div>
        </form>
    </div>
</div>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>