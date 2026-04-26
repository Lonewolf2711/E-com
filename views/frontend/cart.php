<?php
/**
 * Cart Page (FoodMart Theme)
 */
$cart = $cart ?? ['items' => [], 'subtotal' => 0, 'count' => 0];
?>
<?php require_once VIEW_PATH . '/frontend/partials/header.php'; ?>

<div class="page-header-foodmart py-4">
    <div class="container-fluid">
        <h1 class="text-center text-white display-6">Enquiry Cart</h1>
        <nav><ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item active text-white">Enquiry Cart</li>
        </ol></nav>
    </div>
</div>

<section class="py-5">
    <div class="container-fluid">
        <?php if (!empty($cart['items'])): ?>
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr><th>Part Details</th><th>Qty</th><th></th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart['items'] as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= $item['product_image'] ? upload_url(e($item['product_image'])) : asset('frontend/foodmart-images/thumb-bananas.png') ?>" class="rounded me-3" style="width:60px;height:60px;object-fit:cover;" alt="">
                                        <div>
                                            <a href="<?= url('/product/' . e($item['product_slug'])) ?>" class="fw-bold text-dark"><?= e($item['product_name']) ?></a>
                                            <?php if (!empty($item['product_sku'])): ?>
                                            <br><span class="part-code-badge" style="font-size:0.65rem;">
                                              <?= e($item['product_sku']) ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td style="width:140px;">
                                    <form action="<?= url('/cart/update') ?>" method="POST" class="d-flex">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                        <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['product_stock'] ?>" class="form-control form-control-sm text-center" style="width:70px;" onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td>
                                    <form action="<?= url('/cart/remove') ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle">
                                            <svg width="16" height="16"><use xlink:href="#trash"></use></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="<?= url('/shop') ?>" class="btn btn-outline-dark rounded-1"><i class="fas fa-arrow-left me-2"></i>Continue Shopping</a>
            </div>
            <div class="col-lg-4">
              <div class="card border-0 rounded-3" style="background:var(--bg-page);">
                <div class="card-body p-4">
                  <div class="enquiry-cart-header mb-4">
                    <h5 style="font-family:'Barlow Condensed',sans-serif;font-size:1.3rem; color:var(--brand-navy);">
                      Enquiry Summary
                    </h5>
                  </div>
                  <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Total Parts</span>
                    <strong><?= $cart['count'] ?? 0 ?> items</strong>
                  </div>
                  <div class="d-flex justify-content-between mb-4">
                    <span class="text-muted">Response Time</span>
                    <strong style="color:var(--success);">Within 24 hours</strong>
                  </div>
                  <div class="alert alert-light border mb-4" style="font-size:0.82rem;">
                    <i class="fas fa-info-circle me-1" style="color:var(--brand-orange);"></i>
                    Submit your enquiry and we'll respond with availability, pricing, and lead time for all listed parts.
                  </div>
                  <button type="button" class="btn btn-enquiry w-100 py-3 mb-3"
                          data-bs-toggle="modal" data-bs-target="#enquiryModal"
                          style="font-family:'Barlow Condensed',sans-serif;font-size:1.1rem; letter-spacing:0.05em;">
                    <i class="fas fa-paper-plane me-2"></i>SUBMIT ENQUIRY
                  </button>
                  <a href="<?= url('/shop') ?>" class="btn btn-outline-secondary w-100">
                    ← Continue Browsing
                  </a>
                </div>
              </div>
            </div>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-clipboard-list fa-3x mb-3" style="color:var(--text-muted);"></i>
            <h4 style="color:var(--text-muted);">Your enquiry cart is empty</h4>
            <p class="text-muted mb-4">
                Browse our parts catalogue and add items you'd like a quotation for.
            </p>
            <a href="<?= url('/shop') ?>" class="btn btn-enquiry px-5 py-2">
                Browse Parts
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Enquiry Modal -->
<div class="modal fade" id="enquiryModal" tabindex="-1" aria-labelledby="enquiryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enquiryModalLabel">Send Enquiry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="enquiryForm">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="customer_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone / WhatsApp Number <span class="text-danger">*</span></label>
                        <input type="text" name="customer_phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="customer_company" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message / Special Requirements</label>
                        <textarea class="form-control" name="message" rows="4"></textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="wants_whatsapp" id="wantsWhatsapp" value="1" checked>
                        <label class="form-check-label" for="wantsWhatsapp">
                            Send me a confirmation on WhatsApp
                        </label>
                    </div>
                    <p class="text-muted small">Our team will respond within 24 hours with a quotation.</p>
                    <div id="enquiryAlert" class="alert d-none"></div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success py-2" id="submitEnquiryBtn">
                            Submit Enquiry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('enquiryForm');
    const alertDiv = document.getElementById('enquiryAlert');
    const btn = document.getElementById('submitEnquiryBtn');

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Submitting...';
            alertDiv.classList.add('d-none');
            
            const formData = new FormData(form);

            try {
                const response = await fetch('<?= url('/enquiry/submit') ?>', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    const modalEl = document.getElementById('enquiryModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    
                    // Show a toast or update the page
                    document.querySelector('.page-header-foodmart').insertAdjacentHTML('afterend', `
                        <div class="container-fluid mt-4">
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                Enquiry <strong>${data.enquiry_number}</strong> submitted! Check your email.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    `);
                    window.scrollTo(0, 0);

                    if (data.whatsapp_url) {
                        setTimeout(() => {
                            window.open(data.whatsapp_url, '_blank');
                            window.location.href = '<?= url('/shop') ?>';
                        }, 1500);
                    } else {
                        setTimeout(() => {
                            window.location.href = '<?= url('/shop') ?>';
                        }, 1500);
                    }
                } else {
                    alertDiv.classList.remove('d-none');
                    alertDiv.classList.add('alert-danger');
                    alertDiv.textContent = data.message || 'An error occurred';
                    btn.disabled = false;
                    btn.textContent = 'Submit Enquiry';
                }
            } catch (err) {
                alertDiv.classList.remove('d-none');
                alertDiv.classList.add('alert-danger');
                alertDiv.textContent = 'Network or server error.';
                btn.disabled = false;
                btn.textContent = 'Submit Enquiry';
            }
        });
    }
});
</script>

<?php require_once VIEW_PATH . '/frontend/partials/footer.php'; ?>
