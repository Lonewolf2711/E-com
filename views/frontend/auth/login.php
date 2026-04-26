<?php
/**
 * Login Page (FoodMart Theme)
 */
?>
<?php require_once VIEW_PATH . '/frontend/partials/header.php'; ?>

<div class="page-header-foodmart py-4">
    <div class="container-fluid">
        <h1 class="text-center text-white display-6">Login</h1>
        <nav><ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item active text-white">Login</li>
        </ol></nav>
    </div>
</div>

<section class="py-5">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="bg-light rounded-4 p-5">
                    <h3 class="mb-4">Login to Your Account</h3>

                    <form action="<?= url('/login') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-4">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                       placeholder="Enter your email"
                                       value="<?= e(old('email')) ?>" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="Enter your password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-dark rounded-1 py-3 px-5 w-100 mb-4">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </form>

                    <div class="text-center">
                        <p class="mb-0">Don't have an account?
                            <a href="<?= url('/register') ?>" class="fw-bold" style="color:#2d6a4f;">Register here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once VIEW_PATH . '/frontend/partials/footer.php'; ?>
