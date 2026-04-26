<?php
/**
 * Admin Customer Detail View
 */
$customer = $customer ?? [];
$orders = $orders ?? ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
$customerStats = $customerStats ?? ['total_orders' => 0, 'total_spent' => 0];
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <h3>Customer Profile</h3>
        <a href="<?= url('/admin/customers') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back to List</a>
    </div>
</div>

<div class="page-content">
    <div class="row">
        <!-- Profile Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar avatar-xl bg-primary mb-3">
                        <span class="avatar-content fs-3"><?= strtoupper(substr($customer['name'], 0, 1)) ?></span>
                    </div>
                    <h4 class="card-title"><?= e($customer['name']) ?></h4>
                    <p class="text-muted"><?= e($customer['email']) ?></p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <?php if ($customer['status'] === 'active'): ?>
                            <span class="badge bg-success">Active Account</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Banned Account</span>
                        <?php endif; ?>
                        <span class="badge bg-secondary">Joined <?= date('M Y', strtotime($customer['created_at'])) ?></span>
                    </div>

                    <form action="<?= url('/admin/customers/' . $customer['id'] . '/status') ?>" method="POST" class="d-grid">
                        <?= csrf_field() ?>
                        <input type="hidden" name="status" value="<?= $customer['status'] === 'active' ? 'banned' : 'active' ?>">
                        <?php if ($customer['status'] === 'active'): ?>
                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to ban this customer?');"><i class="bi bi-slash-circle me-2"></i>Ban Customer</button>
                        <?php else: ?>
                            <button type="submit" class="btn btn-outline-success"><i class="bi bi-check-circle me-2"></i>Unban Customer</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title">Lifetime Value</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold">Total Orders</span>
                        <span class="fw-bold"><?= $customerStats['total_orders'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fw-bold">Total Spent</span>
                        <span class="fw-bold text-primary"><?= formatPrice($customerStats['total_spent']) ?></span>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title">Contact Info</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><i class="bi bi-envelope me-2 text-muted"></i> <a href="mailto:<?= e($customer['email']) ?>"><?= e($customer['email']) ?></a></p>
                    <p class="mb-0"><i class="bi bi-telephone me-2 text-muted"></i> <?= e($customer['phone'] ?? 'Not provided') ?></p>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Order History</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($orders['data'])): ?>
                        <p class="text-muted text-center py-4">This customer hasn't placed any orders yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders['data'] as $order): ?>
                                    <tr>
                                        <td class="fw-bold"><a href="<?= url('/admin/orders/' . $order['id']) ?>"><?= e($order['order_number'] ?? '#' . $order['id']) ?></a></td>
                                        <td class="text-muted small"><?= formatDate($order['created_at']) ?></td>
                                        <td class="fw-bold"><?= formatPrice($order['total_amount']) ?></td>
                                        <td>
                                            <?php $sb = orderStatusBadge($order['status']); ?>
                                            <span class="badge <?= $sb['class'] ?>"><?= $sb['label'] ?></span>
                                        </td>
                                        <td>
                                            <?php $pb = paymentStatusBadge($order['payment_status']); ?>
                                            <span class="badge <?= $pb['class'] ?>"><?= $pb['label'] ?></span>
                                        </td>
                                        <td>
                                            <a href="<?= url('/admin/orders/' . $order['id']) ?>" class="btn btn-sm btn-icon btn-light"><i class="bi bi-chevron-right"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if ($orders['pages'] > 1): ?>
                            <?= pagination_html($orders['current_page'], $orders['pages'], url('/admin/customers/' . $customer['id'])) ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
