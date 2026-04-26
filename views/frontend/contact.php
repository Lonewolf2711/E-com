<?php
/**
 * Contact Page (FoodMart Theme)
 */
?>
<?php require_once VIEW_PATH . '/frontend/partials/header.php'; ?>

<div class="page-header-foodmart py-4">
    <div class="container-fluid">
        <h1 class="text-center text-white display-6">Contact Us</h1>
        <nav><ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item active text-white">Contact</li>
        </ol></nav>
    </div>
</div>

<section class="py-5">
    <div class="container-fluid">
        <div class="row g-5">
            <div class="col-lg-7">
                <h3 class="mb-2">Send Us Your Requirement</h3>
                <p class="text-muted mb-4">
                  Fill in the form below with your enquiry. Include part codes, machine
                  model, or attach a photo — our team will respond within 24 hours.
                </p>
                <form action="<?= url('/contact') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Your Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required value="<?= is_logged_in() ? e(Auth::name()) : '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" required value="<?= is_logged_in() ? e(Auth::user()['email'] ?? '') : '' ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Part Code / Machine Model (if known)</label>
                            <input type="text" class="form-control" name="subject">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Your Requirement <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="message" rows="5" required></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-enquiry">
                                <i class="fas fa-paper-plane me-2"></i>Submit Enquiry
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-5">
                <div class="bg-light rounded-4 p-5 h-100">
                    <h4 class="mb-4">Contact Info</h4>
                    <div class="d-flex mb-4">
                        <i class="fas fa-map-marker-alt fa-2x me-3 mt-1 contact-info-icon" style="color:var(--brand-orange);"></i>
                        <div>
                            <h5>Address</h5>
                            <p class="mb-0"><?= e(get_setting('general_address', '123 Street, City, Country')) ?></p>
                        </div>
                    </div>
                    <div class="d-flex mb-4">
                        <i class="fas fa-phone fa-2x me-3 mt-1 contact-info-icon" style="color:var(--brand-orange);"></i>
                        <div>
                            <h5>Phone</h5>
                            <p class="mb-0"><?= e(get_setting('general_phone', '+91 9876543210')) ?></p>
                        </div>
                    </div>
                    <div class="d-flex mb-4">
                        <i class="fab fa-whatsapp fa-2x me-3 mt-1 contact-info-icon" style="color:var(--success);"></i>
                        <div>
                          <h5>WhatsApp</h5>
                          <p class="mb-0">
                            <a href="https://wa.me/<?= e(get_setting('contact_whatsapp', str_replace(['+', ' ', '-'], '', get_setting('general_phone', '')))) ?>" target="_blank" class="text-decoration-none" style="color:var(--brand-orange);">
                              Chat with us on WhatsApp
                            </a>
                          </p>
                        </div>
                    </div>
                    <div class="d-flex mb-4">
                        <i class="fas fa-envelope fa-2x me-3 mt-1 contact-info-icon" style="color:var(--brand-orange);"></i>
                        <div>
                            <h5>Email</h5>
                            <p class="mb-0"><?= e(get_setting('general_email', 'info@example.com')) ?></p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <i class="fas fa-clock fa-2x me-3 mt-1 contact-info-icon" style="color:var(--brand-orange);"></i>
                        <div>
                            <h5>Working Hours</h5>
                            <p class="mb-0">Mon — Sat: 9:00 AM — 6:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once VIEW_PATH . '/frontend/partials/footer.php'; ?>
