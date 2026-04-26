<?php
/**
 * Admin Coupon Campaigns Profile
 */
$coupons = $coupons ?? ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
$stats = $stats ?? [];
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <h3>Coupon Performance</h3>
        <a href="<?= url('/admin/coupons') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back to Coupons</a>
    </div>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Campaign Revenue Tracking</h5>
            <p class="text-muted mb-0">Track how much revenue each active or past coupon code has successfully driven through paid orders.</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Campaign Code</th>
                            <th>Times Used</th>
                            <th>Total Revenue Driven</th>
                            <th>Avg. Order Value</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($coupons['data'] as $c): ?>
                            <?php 
                            $rev = $stats[$c['id']] ?? 0;
                            $aov = $c['used_count'] > 0 ? $rev / $c['used_count'] : 0;
                            ?>
                        <tr>
                            <td class="fw-bold"><span class="badge bg-light-primary fs-6"><?= e($c['code']) ?></span></td>
                            <td><?= $c['used_count'] ?> / <?= $c['max_uses'] > 0 ? $c['max_uses'] : '∞' ?></td>
                            <td class="fw-bold text-success"><?= formatPrice($rev) ?></td>
                            <td class="text-muted"><?= formatPrice($aov) ?></td>
                            <td>
                                <?php if (strtotime($c['expiry_date']) < time()): ?>
                                    <span class="badge bg-light-danger">Expired</span>
                                <?php elseif ($c['status'] === 'active'): ?>
                                    <span class="badge bg-light-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-light-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($coupons['data'])): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No campaigns found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($coupons['pages'] > 1): ?>
                <?= pagination_html($coupons['current_page'], $coupons['pages'], url('/admin/coupons/campaigns')) ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
