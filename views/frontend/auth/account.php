<?php
/**
 * Account / Profile Page (FoodMart Theme)
 * Customer dashboard with profile edit, recent orders, and quick links
 */
?>
<?php require_once VIEW_PATH . '/frontend/partials/header.php'; ?>

<div class="page-header-foodmart py-4">
    <div class="container-fluid">
        <h1 class="text-center text-white display-6">My Account</h1>
        <nav><ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item active text-white">My Account</li>
        </ol></nav>
    </div>
</div>

<section class="py-5">
    <div class="container-fluid">
        <div class="row g-5">
            <!-- Profile Info + Edit -->
            <div class="col-lg-6">
                <div class="bg-light rounded-4 p-5">
                    <h4 class="mb-4"><i class="fas fa-user-circle me-2" style="color:#2d6a4f;"></i>Profile Information</h4>

                    <form action="<?= url('/account/update') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="<?= e($user['name'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" value="<?= e($user['email'] ?? '') ?>" disabled>
                            <small class="text-muted">Email cannot be changed</small>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                   value="<?= e($user['phone'] ?? '') ?>">
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Change Password</h5>
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password">
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password"
                                   minlength="6" placeholder="Min 6 characters">
                        </div>

                        <button type="submit" class="btn btn-dark rounded-1 py-2 px-5">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Links + Recent Orders -->
            <div class="col-lg-6">
                <!-- Quick Links -->
                <div class="bg-light rounded-4 p-5 mb-4">
                    <h4 class="mb-4"><i class="fas fa-link me-2" style="color:#2d6a4f;"></i>Quick Links</h4>
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="<?= url('/my-orders') ?>" class="btn btn-outline-dark w-100 py-3 rounded-1">
                                <i class="fas fa-shopping-bag me-2"></i>My Orders
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= url('/wishlist') ?>" class="btn btn-outline-dark w-100 py-3 rounded-1">
                                <i class="fas fa-heart me-2"></i>Wishlist (<?= $wishlist_count ?? 0 ?>)
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= url('/track-order') ?>" class="btn btn-outline-dark w-100 py-3 rounded-1">
                                <i class="fas fa-truck me-2"></i>Track Order
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= url('/logout') ?>" class="btn btn-outline-danger w-100 py-3 rounded-1">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-light rounded-4 p-5">
                    <h4 class="mb-4"><i class="fas fa-history me-2" style="color:#2d6a4f;"></i>Recent Orders</h4>
                    <?php if (!empty($recent_orders['data'])): ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr><th>Order #</th><th>Date</th><th>Total</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders['data'] as $order): ?>
                                <tr>
                                    <td><a href="<?= url('/my-orders/' . $order['id']) ?>"><?= e($order['order_number']) ?></a></td>
                                    <td><?= formatDate($order['created_at']) ?></td>
                                    <td><?= formatPrice($order['total_amount']) ?></td>
                                    <td>
                                        <?php $badge = orderStatusBadge($order['status']); ?>
                                        <span class="badge <?= $badge['class'] ?>"><?= $badge['label'] ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="<?= url('/my-orders') ?>" class="btn btn-sm btn-dark rounded-1">View All Orders →</a>
                    <?php else: ?>
                        <p class="text-muted mb-0">No orders yet. <a href="<?= url('/shop') ?>">Start shopping!</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Account Info -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="bg-light rounded-4 p-4 text-center">
                    <small class="text-muted">
                        Member since <?= formatDate($user['created_at'] ?? date('Y-m-d')) ?> ·
                        Account status: <span class="badge bg-success"><?= ucfirst($user['status'] ?? 'active') ?></span>
                    </small>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once VIEW_PATH . '/frontend/partials/footer.php'; ?>
