<?php
/**
 * Frontend Layout — Footer Partial (FoodMart Theme)
 * ──────────────────────────────────────────────────
 * Based on FoodMart template footer
 */

$store_name = get_setting('general_store_name', 'FoodMart');
$tagline = get_setting('general_tagline', 'Reliable spare parts for industrial machinery');
?>

    <!-- Features Strip -->
    <section class="py-5">
      <div class="container-fluid">
        <div class="row row-cols-1 row-cols-sm-3 row-cols-lg-5">
          <div class="col">
            <div class="card mb-3 border-0 feature-strip-card">
              <div class="row align-items-center">
                <div class="col-md-2 feature-strip-icon"><i class="fas fa-bolt fa-2x feature-strip-icon"></i></div>
                <div class="col-md-10">
                  <div class="card-body p-0">
                    <h5 class="mb-1">Fast Sourcing</h5>
                    <p class="card-text text-muted mb-0">Parts dispatched within 24–48 hours</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card mb-3 border-0 feature-strip-card">
              <div class="row align-items-center">
                <div class="col-md-2 feature-strip-icon"><i class="fas fa-cogs fa-2x feature-strip-icon"></i></div>
                <div class="col-md-10">
                  <div class="card-body p-0">
                    <h5 class="mb-1">Genuine Parts</h5>
                    <p class="card-text text-muted mb-0">OEM & compatible parts guaranteed</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card mb-3 border-0 feature-strip-card">
              <div class="row align-items-center">
                <div class="col-md-2 feature-strip-icon"><i class="fas fa-file-invoice fa-2x feature-strip-icon"></i></div>
                <div class="col-md-10">
                  <div class="card-body p-0">
                    <h5 class="mb-1">Bulk Quotations</h5>
                    <p class="card-text text-muted mb-0">Volume pricing for trade buyers</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card mb-3 border-0 feature-strip-card">
              <div class="row align-items-center">
                <div class="col-md-2 feature-strip-icon"><i class="fas fa-headset fa-2x feature-strip-icon"></i></div>
                <div class="col-md-10">
                  <div class="card-body p-0">
                    <h5 class="mb-1">Expert Support</h5>
                    <p class="card-text text-muted mb-0">Technical help on part selection</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card mb-3 border-0 feature-strip-card">
              <div class="row align-items-center">
                <div class="col-md-2 feature-strip-icon"><i class="fas fa-award fa-2x feature-strip-icon"></i></div>
                <div class="col-md-10">
                  <div class="card-body p-0">
                    <h5 class="mb-1">Quality Assured</h5>
                    <p class="card-text text-muted mb-0">Every part tested before dispatch</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="py-5" style="background-color: #2d2d2d; color: #ccc;">
    <style>
      footer .nav-link, footer .text-muted, footer .menu-list a, footer ul li { color: #bbb !important; }
      footer .nav-link:hover, footer .menu-list a:hover { color: #fff !important; }
      footer .widget-title { color: #fff !important; }
      footer i.fas, footer i.fab { color: #bbb; }
    </style>
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="footer-menu">
              <h4 class="text-white mb-3">
                <i class="fas fa-cog me-2" style="color:var(--brand-orange);"></i>
                <?= e($store_name) ?>
              </h4>
              <p class="text-muted"><?= e($tagline) ?></p>
              <div class="social-links mt-4">
                <ul class="d-flex list-unstyled gap-2">
                  <li><a href="#" class="btn btn-outline-light btn-sm"><i class="fab fa-facebook-f"></i></a></li>
                  <li><a href="#" class="btn btn-outline-light btn-sm"><i class="fab fa-twitter"></i></a></li>
                  <li><a href="#" class="btn btn-outline-light btn-sm"><i class="fab fa-instagram"></i></a></li>
                  <li><a href="#" class="btn btn-outline-light btn-sm"><i class="fab fa-youtube"></i></a></li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-md-2 col-sm-6">
            <div class="footer-menu">
              <h5 class="widget-title text-white mb-3">Quick Links</h5>
              <ul class="menu-list list-unstyled">
                <li class="mb-2"><a href="<?= url('/') ?>" class="nav-link text-muted">Home</a></li>
                <li class="mb-2"><a href="<?= url('/shop') ?>" class="nav-link text-muted">Browse Parts</a></li>
                <li class="mb-2"><a href="<?= url('/cart') ?>" class="nav-link text-muted">Enquiry Cart</a></li>
                <li class="mb-2"><a href="<?= url('/contact') ?>" class="nav-link text-muted">Contact Us</a></li>
              </ul>
            </div>
          </div>

          <div class="col-md-2 col-sm-6">
            <div class="footer-menu">
              <h5 class="widget-title text-white mb-3">Support</h5>
              <ul class="menu-list list-unstyled">
                <li class="mb-2"><a href="<?= url('/cart') ?>" class="nav-link text-muted">Send Enquiry</a></li>
                <li class="mb-2"><a href="<?= url('/contact') ?>" class="nav-link text-muted">Get a Quote</a></li>
                <li class="mb-2"><a href="<?= url('/shop') ?>" class="nav-link text-muted">Part Search</a></li>
                <li class="mb-2"><a target="_blank" href="https://wa.me/<?= e(get_setting('contact_whatsapp','#')) ?>" class="nav-link text-muted">WhatsApp Us</a></li>
              </ul>
            </div>
          </div>

          <div class="col-lg-2 col-md-6 col-sm-6">
            <div class="footer-menu">
              <h5 class="widget-title text-white mb-3">Contact</h5>
              <ul class="list-unstyled">
                <li class="mb-2 text-muted"><i class="fas fa-map-marker-alt me-2"></i><?= e(get_setting('general_address', '123 Street, City')) ?></li>
                <li class="mb-2 text-muted"><i class="fas fa-envelope me-2"></i><?= e(get_setting('general_email', 'info@example.com')) ?></li>
                <li class="mb-2 text-muted"><i class="fas fa-phone me-2"></i><?= e(get_setting('general_phone', '+91 9876543210')) ?></li>
              </ul>
            </div>
          </div>

          <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="footer-menu">
              <h5 class="widget-title text-white mb-3">Stay Updated</h5>
              <p class="text-muted">Get notified about new parts and industry updates.</p>
              <form class="d-flex mt-3 gap-0">
                <input class="form-control rounded-start rounded-0 bg-dark text-white border-secondary" type="email" placeholder="Email Address">
                <button class="btn btn-fm-primary rounded-end rounded-0" type="button">Subscribe</button>
              </form>
            </div>
          </div>

        </div>
      </div>
    </footer>
    <div id="footer-bottom" style="background-color: #222; padding: 1rem 0;">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-6 text-muted">
            <p class="mb-0">© <?= date('Y') ?> <?= e($store_name) ?>. All rights reserved.</p>
          </div>
          <div class="col-md-6 text-start text-md-end text-muted">
            <p class="mb-0">Built for <span style="color:var(--brand-orange);">Industrial Excellence</span></p>
          </div>
        </div>
      </div>
    </div>

    <!-- JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('frontend/foodmart-js/plugins.js') ?>"></script>
    <script src="<?= asset('frontend/foodmart-js/script.js') ?>"></script>
    <!-- Video Hero Swiper Enhancement -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Attempt to grab the main swiper instance registered by plugins.js or script.js
        // We hook into Swiper's slideChange event after a short delay to let Swiper init first
        setTimeout(function() {
            const swiperEl = document.querySelector('.main-swiper');
            if (!swiperEl || !swiperEl.swiper) return;

            const swiper = swiperEl.swiper;

            // Enable pauseOnMouseEnter
            swiper.params.autoplay.pauseOnMouseEnter = true;
            swiper.autoplay.start();

            function handleSlideChange() {
                const activeSlide = swiper.slides[swiper.activeIndex];
                if (!activeSlide) return;

                if (activeSlide.classList.contains('video-slide')) {
                    // Pause Swiper autoplay while video-slide is active
                    swiper.autoplay.stop();

                    // Resume after 8 seconds (covers most short promo videos)
                    const vid = activeSlide.querySelector('video');
                    if (vid) {
                        vid.onended = function () { swiper.autoplay.start(); };
                        // Fallback timer in case video loops without ending
                        setTimeout(function() { swiper.autoplay.start(); }, 8000);
                    } else {
                        // YouTube iframe — resume after 10s
                        setTimeout(function() { swiper.autoplay.start(); }, 10000);
                    }
                }
            }

            swiper.on('slideChange', handleSlideChange);
            // Check on load in case first slide is a video
            handleSlideChange();
        }, 800);
    });
    </script>
</body>
</html>
