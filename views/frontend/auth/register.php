<?php
/**
 * Register Page (FoodMart Theme)
 */
?>
<?php require_once VIEW_PATH . '/frontend/partials/header.php'; ?>

<div class="page-header-foodmart py-4">
    <div class="container-fluid">
        <h1 class="text-center text-white display-6">Create Account</h1>
        <nav><ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item active text-white">Register</li>
        </ol></nav>
    </div>
</div>

<section class="py-5">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="bg-light rounded-4 p-5">
                    <h3 class="mb-4">Create Your Account</h3>

                    <form action="<?= url('/register') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-4">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="name" name="name"
                                       placeholder="Enter your full name"
                                       value="<?= e(old('name')) ?>" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                       placeholder="Enter your email"
                                       value="<?= e(old('email')) ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="form-label">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                       placeholder="Enter your phone number"
                                       value="<?= e(old('phone')) ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password"
                                           placeholder="Min 6 characters" required minlength="6">
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                           placeholder="Repeat password" required minlength="6">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-dark rounded-1 py-3 px-5 w-100 mb-4">
                            <i class="fas fa-user-plus me-2"></i>Create Account
                        </button>
                    </form>

                    <div class="text-center">
                        <p class="mb-0">Already have an account?
                            <a href="<?= url('/login') ?>" class="fw-bold" style="color:#2d6a4f;">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once VIEW_PATH . '/frontend/partials/footer.php'; ?>
