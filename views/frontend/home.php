<?php
$store_name = $store_name ?? 'FoodMart';
$categories = $categories ?? [];
$featured_products = $featured_products ?? [];
$all_products = $all_products ?? [];
$hero_banners = $hero_banners ?? [];
$side_top_banners = $side_top ?? [];
$side_bottom_banners = $side_bottom ?? [];
?>
<?php require_once VIEW_PATH . '/frontend/partials/header.php'; ?>

<style>
  .hero-video-wrapper {
    position: relative;
    width: 100%;
    min-height: 400px;
    overflow: hidden;
    display: flex;
    align-items: center;
  }

  .hero-video-bg {
    position: absolute;
    top: 50%;
    left: 50%;
    min-width: 100%;
    min-height: 100%;
    width: auto;
    height: auto;
    transform: translate(-50%, -50%);
    z-index: 0;
    object-fit: cover;
  }

  .hero-video-frame {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    min-height: 400px;
    z-index: 0;
    pointer-events: none;
    border: none;
  }

  .hero-video-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.45);
    z-index: 1;
  }

  .video-slide .banner-content {
    position: relative;
    z-index: 2;
    min-height: 400px;
    align-items: center;
  }
</style>

<!-- Hero Banner Slider — Industrial Spare Parts -->
<style>
  /* ── HERO SECTION ─────────────────────────────────────────────── */
  .hero-section {
    position: relative;
    width: 100%;
    overflow: hidden;
    background: #0D1B2A;
  }

  .hero-swiper {
    width: 100%;
    height: 520px;
  }

  @media (max-width: 768px) {
    .hero-swiper {
      height: 400px;
    }
  }

  /* Image slide */
  .hero-slide-image {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    z-index: 0;
  }

  /* Video slide */
  .hero-video-bg {
    position: absolute;
    top: 50%;
    left: 50%;
    min-width: 100%;
    min-height: 100%;
    width: auto;
    height: auto;
    transform: translate(-50%, -50%);
    object-fit: cover;
    z-index: 0;
  }

  .hero-video-frame {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    border: none;
    pointer-events: none;
    z-index: 0;
  }

  /* Dark overlay on every slide */
  .hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(105deg,
        rgba(13, 27, 42, 0.88) 0%,
        rgba(13, 27, 42, 0.60) 55%,
        rgba(13, 27, 42, 0.25) 100%);
    z-index: 1;
  }

  /* Text content sits above overlay */
  .hero-content {
    position: relative;
    z-index: 2;
    height: 100%;
    display: flex;
    align-items: center;
    padding: 0 5%;
  }

  .hero-content-inner {
    max-width: 620px;
  }

  .hero-tag {
    display: inline-block;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: #E85D04;
    background: rgba(232, 93, 4, 0.12);
    border: 1px solid rgba(232, 93, 4, 0.35);
    padding: 4px 14px;
    border-radius: 20px;
    margin-bottom: 1rem;
  }

  .hero-title {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: clamp(2.2rem, 5vw, 3.6rem);
    font-weight: 800;
    color: #ffffff;
    line-height: 1.1;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }

  .hero-desc {
    font-family: 'DM Sans', sans-serif;
    font-size: 1rem;
    color: rgba(255, 255, 255, 0.72);
    line-height: 1.6;
    margin-bottom: 1.8rem;
    max-width: 480px;
  }

  .hero-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #E85D04;
    color: #fff;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 12px 28px;
    border-radius: 6px;
    text-decoration: none;
    border: 2px solid #E85D04;
    transition: background 0.2s, transform 0.15s;
    margin-right: 12px;
  }

  .hero-btn-primary:hover {
    background: #C44E02;
    border-color: #C44E02;
    color: #fff;
    transform: translateY(-2px);
  }

  .hero-btn-outline {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: transparent;
    color: #fff;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 12px 28px;
    border-radius: 6px;
    text-decoration: none;
    border: 2px solid rgba(255, 255, 255, 0.45);
    transition: border-color 0.2s, background 0.2s;
  }

  .hero-btn-outline:hover {
    border-color: #fff;
    background: rgba(255, 255, 255, 0.08);
    color: #fff;
  }

  /* Swiper pagination dots */
  .hero-swiper .swiper-pagination-bullet {
    background: rgba(255, 255, 255, 0.45);
    opacity: 1;
    width: 8px;
    height: 8px;
  }

  .hero-swiper .swiper-pagination-bullet-active {
    background: #E85D04;
    width: 24px;
    border-radius: 4px;
    transition: width 0.3s;
  }

  /* Swiper nav arrows */
  .hero-swiper .swiper-button-prev,
  .hero-swiper .swiper-button-next {
    color: rgba(255, 255, 255, 0.7);
    background: rgba(13, 27, 42, 0.45);
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: 1px solid rgba(255, 255, 255, 0.15);
    transition: background 0.2s;
  }

  .hero-swiper .swiper-button-prev::after,
  .hero-swiper .swiper-button-next::after {
    font-size: 14px;
    font-weight: 700;
  }

  .hero-swiper .swiper-button-prev:hover,
  .hero-swiper .swiper-button-next:hover {
    background: rgba(232, 93, 4, 0.7);
  }

  /* Bottom info bar */
  .hero-info-bar {
    background: #111E2E;
    border-top: 1px solid rgba(255, 255, 255, 0.07);
    padding: 14px 5%;
    display: flex;
    gap: 2.5rem;
    flex-wrap: wrap;
  }

  .hero-info-item {
    display: flex;
    align-items: center;
    gap: 10px;
    color: rgba(255, 255, 255, 0.65);
    font-family: 'DM Sans', sans-serif;
    font-size: 0.82rem;
  }

  .hero-info-item i {
    color: #E85D04;
    font-size: 1rem;
  }

  .hero-info-item strong {
    color: #fff;
    font-size: 0.88rem;
  }
</style>

<section class="hero-section">

  <!-- Main Swiper Slider -->
  <div class="swiper hero-swiper main-swiper">
    <div class="swiper-wrapper">

      <?php if (!empty($hero_banners)): ?>
        <?php foreach ($hero_banners as $hb): ?>

          <?php
          $mediaType = $hb['media_type'] ?? 'image';
          $videoUrl = $hb['video_url'] ?? '';
          $imageUrl = !empty($hb['image']) ? upload_url(e($hb['image'])) : '';
          $isYouTube = $videoUrl && (
            str_contains($videoUrl, 'youtube.com') ||
            str_contains($videoUrl, 'youtu.be')
          );
          ?>

          <div class="swiper-slide" style="position:relative;overflow:hidden;">

            <?php if ($mediaType === 'video' && $videoUrl): ?>

              <?php if ($isYouTube): ?>
                <?php
                /* Extract YouTube video ID */
                preg_match(
                  '/(?:v=|youtu\.be\/|embed\/)([A-Za-z0-9_\-]{11})/',
                  $videoUrl,
                  $ytm
                );
                $ytId = $ytm[1] ?? '';
                $embedSrc = "https://www.youtube.com/embed/{$ytId}"
                  . "?autoplay=1&mute=1&loop=1&controls=0"
                  . "&playlist={$ytId}&rel=0&showinfo=0";
                ?>
                <iframe class="hero-video-frame" src="<?= $embedSrc ?>" allow="autoplay; encrypted-media" allowfullscreen>
                </iframe>
              <?php else: ?>
                <video class="hero-video-bg" autoplay muted loop playsinline>
                  <source src="<?= e($videoUrl) ?>" type="video/mp4">
                </video>
              <?php endif; ?>

            <?php else: ?>
              <!-- Image slide background -->
              <div class="hero-slide-image"
                style="background-image:url('<?= $imageUrl ?: asset('frontend/foodmart-images/background-pattern.jpg') ?>');">
              </div>
            <?php endif; ?>

            <!-- Dark overlay -->
            <div class="hero-overlay"></div>

            <!-- Text content -->
            <div class="hero-content">
              <div class="hero-content-inner">
                <?php if (!empty($hb['subtitle'])): ?>
                  <div class="hero-tag"><?= e($hb['subtitle']) ?></div>
                <?php endif; ?>
                <h1 class="hero-title"><?= e($hb['title'] ?: $store_name) ?></h1>
                <?php if (!empty($hb['description'])): ?>
                  <p class="hero-desc"><?= e($hb['description']) ?></p>
                <?php endif; ?>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                  <?php if (!empty($hb['button_text'])): ?>
                    <a href="<?= url($hb['button_link'] ?: '/shop') ?>" class="hero-btn-primary">
                      <i class="fas fa-cogs"></i>
                      <?= e($hb['button_text']) ?>
                    </a>
                  <?php endif; ?>
                  <a href="<?= url('/contact') ?>" class="hero-btn-outline">
                    <i class="fas fa-paper-plane"></i>
                    Send Enquiry
                  </a>
                </div>
              </div>
            </div>

          </div>
        <?php endforeach; ?>

      <?php else: ?>

        <!-- ── FALLBACK SLIDES (no banners in DB) ── -->

        <!-- Slide 1 -->
        <div class="swiper-slide" style="position:relative;overflow:hidden;">
          <div class="hero-slide-image" style="background-image:url('<?= asset('frontend/foodmart-images/background-pattern.jpg') ?>');
                      filter:brightness(0.35);">
          </div>
          <div class="hero-overlay"
            style="background:linear-gradient(105deg,rgba(13,27,42,0.97) 0%,rgba(13,27,42,0.7) 60%,rgba(232,93,4,0.15) 100%);">
          </div>
          <div class="hero-content">
            <div class="hero-content-inner">
              <div class="hero-tag">Industrial Spare Parts</div>
              <h1 class="hero-title">Precision Parts.<br>Fast Quotations.</h1>
              <p class="hero-desc">
                Source genuine and compatible spare parts for all major
                industrial machines. Send your requirement — get a quote within 24 hours.
              </p>
              <div class="d-flex flex-wrap gap-2">
                <a href="<?= url('/shop') ?>" class="hero-btn-primary">
                  <i class="fas fa-cogs"></i> Browse Parts
                </a>
                <a href="<?= url('/contact') ?>" class="hero-btn-outline">
                  <i class="fas fa-paper-plane"></i> Send Enquiry
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Slide 2 -->
        <div class="swiper-slide" style="position:relative;overflow:hidden;background:#0D1B2A;">
          <div class="hero-overlay"
            style="background:linear-gradient(105deg,rgba(13,27,42,0.98) 0%,rgba(27,46,69,0.9) 100%);">
          </div>
          <div class="hero-content">
            <div class="hero-content-inner">
              <div class="hero-tag">Search by Code or Machine</div>
              <h1 class="hero-title">Can't Find<br>Your Part?</h1>
              <p class="hero-desc">
                Search by product code, machine name, or even a photo.
                Our team identifies and sources the exact part you need.
              </p>
              <div class="d-flex flex-wrap gap-2">
                <a href="<?= url('/shop') ?>" class="hero-btn-primary">
                  <i class="fas fa-search"></i> Search Parts
                </a>
                <a href="<?= url('/contact') ?>" class="hero-btn-outline">
                  <i class="fab fa-whatsapp"></i> WhatsApp Us
                </a>
              </div>
            </div>
          </div>
        </div>

        <?php if (!empty($featured_products[0])): ?>
          <!-- Slide 3 — Featured Product -->
          <div class="swiper-slide" style="position:relative;overflow:hidden;">
            <div class="hero-slide-image" style="background-image:url('<?= $featured_products[0]['image'] ? upload_url(e($featured_products[0]['image'])) : asset('frontend/foodmart-images/background-pattern.jpg') ?>');
                      filter:brightness(0.3);">
            </div>
            <div class="hero-overlay"></div>
            <div class="hero-content">
              <div class="hero-content-inner">
                <div class="hero-tag">Featured Part</div>
                <h1 class="hero-title"><?= e(truncate($featured_products[0]['name'], 40)) ?></h1>
                <?php if (!empty($featured_products[0]['short_description'] ?? $featured_products[0]['description'])): ?>
                  <p class="hero-desc">
                    <?= e(truncate($featured_products[0]['short_description'] ?? $featured_products[0]['description'], 120)) ?>
                  </p>
                <?php endif; ?>
                <div class="d-flex flex-wrap gap-2">
                  <a href="<?= url('/product/' . e($featured_products[0]['slug'])) ?>" class="hero-btn-primary">
                    <i class="fas fa-eye"></i> View Part
                  </a>
                  <a href="<?= url('/cart') ?>" class="hero-btn-outline">
                    <i class="fas fa-clipboard-list"></i> Enquiry Cart
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>

      <?php endif; ?>

    </div><!-- /swiper-wrapper -->

    <!-- Pagination & Navigation -->
    <div class="swiper-pagination" style="bottom:20px;"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
  </div><!-- /hero-swiper -->

  <!-- Info bar below slider -->
  <div class="hero-info-bar">
    <div class="hero-info-item">
      <i class="fas fa-bolt"></i>
      <span><strong>Fast Sourcing</strong> — Parts dispatched 24–48 hrs</span>
    </div>
    <div class="hero-info-item">
      <i class="fas fa-cogs"></i>
      <span><strong>Genuine Parts</strong> — OEM &amp; compatible</span>
    </div>
    <div class="hero-info-item">
      <i class="fas fa-file-invoice"></i>
      <span><strong>Free Quotation</strong> — Response within 24 hours</span>
    </div>
    <div class="hero-info-item">
      <i class="fab fa-whatsapp"></i>
      <span><strong>WhatsApp Support</strong> — Send code or photo</span>
    </div>
  </div>

</section>

<script>
  // ── Hero Swiper Init (runs after footer.php loads jQuery + Swiper) ──
  document.addEventListener('DOMContentLoaded', function () {
    var heroSwiper = new Swiper('.hero-swiper', {
      loop: true,
      autoplay: {
        delay: 5500,
        disableOnInteraction: false,
        pauseOnMouseEnter: true,
      },
      pagination: {
        el: '.hero-swiper .swiper-pagination',
        clickable: true,
      },
      navigation: {
        nextEl: '.hero-swiper .swiper-button-next',
        prevEl: '.hero-swiper .swiper-button-prev',
      },
      effect: 'fade',
      fadeEffect: { crossFade: true },
      speed: 800,
      on: {
        slideChangeTransitionStart: function () {
          // Pause any playing video on the outgoing slide
          var prevVideo = this.slides[this.previousIndex]
            ?.querySelector('video');
          if (prevVideo) prevVideo.pause();
        },
        slideChangeTransitionEnd: function () {
          // Play video on the active slide
          var activeVideo = this.slides[this.activeIndex]
            ?.querySelector('video');
          if (activeVideo) {
            activeVideo.play().catch(function () { });
          }
        },
      },
    });
  });
</script>
<!-- Category Carousel -->
<?php if (!empty($categories)): ?>
  <section class="py-5 overflow-hidden">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="section-header d-flex flex-wrap justify-content-between mb-5">
            <h2 class="section-title">Browse by Category</h2>
            <div class="d-flex align-items-center">
              <a href="<?= url('/shop') ?>" class="btn-link text-decoration-none">All Part Categories →</a>
              <div class="swiper-buttons">
                <button class="swiper-prev category-carousel-prev btn btn-yellow">❮</button>
                <button class="swiper-next category-carousel-next btn btn-yellow">❯</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="category-carousel swiper">
            <div class="swiper-wrapper">
              <?php foreach ($categories as $cat): ?>
                <a href="<?= url('/category/' . e($cat['slug'])) ?>" class="nav-link category-item swiper-slide">
                  <img
                    src="<?= !empty($cat['image']) ? upload_url(e($cat['image'])) : asset('frontend/foodmart-images/icon-vegetables-broccoli.png') ?>"
                    alt="<?= e($cat['name']) ?>">
                  <h3 class="category-title"><?= e($cat['name']) ?></h3>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>

<!-- Trending Products -->
<section class="py-5">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="bootstrap-tabs product-tabs">
          <div class="tabs-header d-flex justify-content-between border-bottom my-5">
            <h3>Parts Catalogue</h3>
            <nav>
              <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a href="#" class="nav-link text-uppercase fs-6 active" id="nav-all-tab" data-bs-toggle="tab"
                  data-bs-target="#nav-all">ALL</a>
                <a href="#" class="nav-link text-uppercase fs-6" id="nav-featured-tab" data-bs-toggle="tab"
                  data-bs-target="#nav-featured">NEW ARRIVALS</a>
              </div>
            </nav>
          </div>
          <div class="tab-content" id="nav-tabContent">

            <!-- All Products Tab -->
            <div class="tab-pane fade show active" id="nav-all" role="tabpanel">
              <div class="product-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5">
                <?php if (!empty($all_products)): ?>
                  <?php foreach ($all_products as $index => $product): ?>
                    <div class="col">
                      <div class="product-item">
                        <?php if ($product['sale_price']): ?>
                          <span class="badge bg-warning text-dark position-absolute m-3">NEW</span>
                        <?php elseif ($product['is_featured']): ?>
                          <span class="badge bg-info text-white position-absolute m-3">Featured</span>
                        <?php endif; ?>
                        <figure>
                          <a href="<?= url('/product/' . e($product['slug'])) ?>" title="<?= e($product['name']) ?>">
                            <img
                              src="<?= $product['image'] ? upload_url(e($product['image'])) : asset('frontend/foodmart-images/thumb-bananas.png') ?>"
                              class="tab-image"
                              alt="<?= e($product['product_code'] ?? ($product['sku'] ?? 'N/A')) ?> <?= e($product['name']) ?> spare part">
                          </a>
                        </figure>
                        <h3><?= e(truncate($product['name'], 35)) ?></h3>
                        <span class="product-category-label"><?= e($product['category_name'] ?? 'General') ?></span>
                        <span
                          class="badge bg-secondary font-monospace"><?= e($product['product_code'] ?: ($product['sku'] ?? 'N/A')) ?></span>
                        <?php if (!empty($product['machine_name'])): ?>
                          <small class="machine-compat d-block mt-2">For: <?= e($product['machine_name']) ?></small>
                        <?php endif; ?>
                        <span class="part-code-badge"><?= e($product['sku'] ?? 'REF: —') ?></span>
                        <div class="d-flex align-items-center justify-content-between mt-2">
                          <a href="<?= url('/product/' . e($product['slug'])) ?>" class="text-decoration-none fw-600"
                            style="color:var(--brand-orange);">View Details →</a>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="col-12 text-center py-5">
                    <h4 class="text-muted">No products available yet.</h4>
                    <p>Products will appear here once added.</p>
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <!-- Featured Products Tab -->
            <div class="tab-pane fade" id="nav-featured" role="tabpanel">
              <div class="product-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5">
                <?php if (!empty($featured_products)): ?>
                  <?php foreach ($featured_products as $index => $product): ?>
                    <div class="col">
                      <div class="product-item">
                        <span class="badge bg-info text-white position-absolute m-3">Featured</span>
                        <figure>
                          <a href="<?= url('/product/' . e($product['slug'])) ?>">
                            <img
                              src="<?= $product['image'] ? upload_url(e($product['image'])) : asset('frontend/foodmart-images/thumb-bananas.png') ?>"
                              class="tab-image"
                              alt="<?= e($product['product_code'] ?? ($product['sku'] ?? 'N/A')) ?> <?= e($product['name']) ?> spare part">
                          </a>
                        </figure>
                        <h3><?= e(truncate($product['name'], 35)) ?></h3>
                        <span class="product-category-label"><?= e($product['category_name'] ?? 'General') ?></span>
                        <span
                          class="badge bg-secondary font-monospace"><?= e($product['product_code'] ?: ($product['sku'] ?? 'N/A')) ?></span>
                        <?php if (!empty($product['machine_name'])): ?>
                          <small class="machine-compat d-block mt-2">For: <?= e($product['machine_name']) ?></small>
                        <?php endif; ?>
                        <span class="part-code-badge"><?= e($product['sku'] ?? 'REF: —') ?></span>
                        <div class="d-flex align-items-center justify-content-between mt-2">
                          <a href="<?= url('/product/' . e($product['slug'])) ?>" class="text-decoration-none fw-600"
                            style="color:var(--brand-orange);">View Details →</a>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="col-12 text-center py-5">
                    <h4 class="text-muted">No featured products yet.</h4>
                  </div>
                <?php endif; ?>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA Banner -->
<section class="py-5">
  <div class="container-fluid">
    <div class="row g-4">
      <div class="col-md-6">
        <div class="rounded-3 p-5" style="background:linear-gradient(135deg,#0D1B2A,#1B2E45);">
          <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;
                           color:var(--brand-orange);font-weight:700;">
            Can't Find Your Part?
          </span>
          <h3 class="text-white mt-2 mb-3" style="font-family:'Barlow Condensed',sans-serif;">
            Search by Code or Send a Photo
          </h3>
          <p class="text-white-50 mb-4">
            Our team identifies parts from product codes, machine models,
            or even a photo using Google Lens.
          </p>
          <a href="<?= url('/contact') ?>" class="btn btn-fm-primary px-4 py-2">
            <i class="fas fa-paper-plane me-2"></i>Send Enquiry
          </a>
        </div>
      </div>
      <div class="col-md-6">
        <div class="rounded-3 p-5" style="background:linear-gradient(135deg,#E85D04,#C44E02);">
          <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;
                           color:rgba(255,255,255,0.7);font-weight:700;">
            Bulk Orders Welcome
          </span>
          <h3 class="text-white mt-2 mb-3" style="font-family:'Barlow Condensed',sans-serif;">
            Trade & Wholesale Pricing
          </h3>
          <p style="color:rgba(255,255,255,0.8);" class="mb-4">
            Manufacturers, maintenance teams and machine operators get
            dedicated quotations and priority dispatch.
          </p>
          <a href="<?= url('/shop') ?>" class="btn btn-light text-dark px-4 py-2 fw-bold">
            Browse All Parts →
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once VIEW_PATH . '/frontend/partials/footer.php'; ?>