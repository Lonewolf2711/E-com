<?php
/**
 * Admin Customers List
 */
$customers = $customers ?? ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
$stats = $stats ?? ['total' => 0, 'active' => 0, 'this_month' => 0];
$search = $search ?? '';
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
    <h3>Customers</h3>
</div>

<div class="page-content">
    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon purple mb-2">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Total Customers</h6>
                            <h6 class="font-extrabold mb-0"><?= $stats['total'] ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon green mb-2">
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Active Customers</h6>
                            <h6 class="font-extrabold mb-0"><?= $stats['active'] ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon blue mb-2">
                                <i class="bi bi-person-plus-fill"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">New This Month</h6>
                            <h6 class="font-extrabold mb-0"><?= $stats['this_month'] ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">Customer List</h5>
            <form action="<?= url('/admin/customers') ?>" method="GET" class="d-flex position-relative">
                <input type="text" name="search" class="form-control" placeholder="Search name or email..." value="<?= e($search) ?>">
                <button class="btn btn-primary ms-2" type="submit"><i class="bi bi-search"></i></button>
                <?php if ($search): ?>
                <a href="<?= url('/admin/customers') ?>" class="btn btn-outline-secondary ms-2">Clear</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Joined</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers['data'] as $user): ?>
                        <tr>
                            <td class="fw-bold">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md bg-light-primary me-3">
                                        <span class="avatar-content"><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
                                    </div>
                                    <?= e($user['name']) ?>
                                </div>
                            </td>
                            <td><?= e($user['email']) ?></td>
                            <td><?= e($user['phone'] ?? '-') ?></td>
                            <td class="text-muted small"><?= formatDate($user['created_at']) ?></td>
                            <td>
                                <?php if ($user['status'] === 'active'): ?>
                                    <span class="badge bg-light-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-light-danger">Banned</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= url('/admin/customers/' . $user['id']) ?>" class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($customers['data'])): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No customers found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($customers['pages'] > 1): ?>
                <?= pagination_html($customers['current_page'], $customers['pages'], url('/admin/customers'), ['search' => $search]) ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
