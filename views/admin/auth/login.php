<?php
/**
 * Admin Login View
 * ────────────────
 * Based on Mazer admin template auth-login.html
 */

$store_name = get_setting('general_store_name', 'Electro Store');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — <?= e($store_name) ?></title>
    <link rel="stylesheet" href="<?= asset('admin/compiled/css/app.css') ?>">
    <link rel="stylesheet" href="<?= asset('admin/compiled/css/auth.css') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <script src="<?= asset('admin/static/js/initTheme.js') ?>"></script>
    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <div class="auth-logo">
                        <a href="<?= url('/') ?>">
                            <h3><i class="fas fa-shopping-bag text-primary me-2"></i><?= e($store_name) ?></h3>
                        </a>
                    </div>
                    <h1 class="auth-title">Admin Login</h1>
                    <p class="auth-subtitle mb-5">Log in with your admin credentials.</p>

                    <?php $flash_error = Session::getFlash('error'); $flash_success = Session::getFlash('success'); ?>
                    <?php if ($flash_error): ?>
                    <div class="alert alert-danger"><?= e($flash_error) ?></div>
                    <?php endif; ?>
                    <?php if ($flash_success): ?>
                    <div class="alert alert-success"><?= e($flash_success) ?></div>
                    <?php endif; ?>

                    <form action="<?= url('/admin/login') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="email" class="form-control form-control-xl" name="email"
                                   placeholder="Email" value="<?= e(old('email')) ?>" required autofocus>
                            <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" class="form-control form-control-xl" name="password"
                                   placeholder="Password" required>
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-block btn-lg shadow-lg mt-5" type="submit">Log in</button>
                    </form>
                    <div class="text-center mt-5 text-lg fs-4">
                        <p class="text-gray-600"><a href="<?= url('/') ?>" class="font-bold">← Back to Store</a></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right"></div>
            </div>
        </div>
    </div>
</body>
</html>
