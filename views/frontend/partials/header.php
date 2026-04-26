<?php
/**
 * Frontend Layout — Header Partial (FoodMart Theme)
 * ──────────────────────────────────────────────────
 * Includes: head, topbar, navbar, offcanvas search/cart
 * Based on FoodMart template — Swiper + Bootstrap 5.3
 *
 * Variables expected: $page_title, $categories (optional)
 */

$store_name = get_setting('general_store_name', 'FoodMart');
$phone = get_setting('general_phone', '+91 9876543210');

// Load categories for nav if not passed
if (!isset($categories)) {
    try {
        $catModel = new Category();
        $categories = $catModel->getCategoriesWithCount();
    } catch (Exception $e) {
        $categories = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <?php $gsc = get_setting('gsc_verification_tag', ''); if ($gsc): ?>
    <meta name="google-site-verification" content="<?= e($gsc) ?>" />
    <?php endif; ?>
    <?php $ga = get_setting('ga_measurement_id', ''); if ($ga): ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($ga) ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?= e($ga) ?>');
    </script>
    <?php endif; ?>
    <title><?= e($page_title ?? 'Home') ?> | <?= e($store_name) ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="<?= e($meta_description ?? get_setting('seo_default_description', '')) ?>" name="description">
    <?php if (!empty($meta_keywords)): ?>
    <meta name="keywords" content="<?= e($meta_keywords) ?>">
    <?php endif; ?>
    <link rel="canonical" href="<?= e($canonical_url ?? (BASE_URL . '/' . ltrim($_SERVER['REQUEST_URI'], '/'))) ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?= e($og_type ?? 'website') ?>">
    <meta property="og:title" content="<?= e($og_title ?? $page_title ?? 'Home') ?> | <?= e($store_name) ?>">
    <meta property="og:description" content="<?= e($og_description ?? $meta_description ?? get_setting('seo_default_description', '')) ?>">
    <meta property="og:url" content="<?= e($canonical_url ?? (BASE_URL . '/' . ltrim($_SERVER['REQUEST_URI'], '/'))) ?>">
    <?php if (!empty($og_image)): ?>
    <meta property="og:image" content="<?= e($og_image) ?>">
    <?php endif; ?>
    <meta property="og:site_name" content="<?= e($store_name) ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($og_title ?? $page_title ?? 'Home') ?> | <?= e($store_name) ?>">
    <meta name="twitter:description" content="<?= e($og_description ?? $meta_description ?? get_setting('seo_default_description', '')) ?>">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <!-- FoodMart Vendor + Theme CSS -->
    <link rel="stylesheet" href="<?= asset('frontend/css/vendor.css') ?>">
    <link rel="stylesheet" href="<?= asset('frontend/foodmart-style.css') ?>">

    <style>
      /* ── DESIGN TOKENS ─────────────────────────────────────── */
      :root {
        --brand-navy:      #0D1B2A;
        --brand-navy-mid:  #1B2E45;
        --brand-orange:    #E85D04;
        --brand-orange-dk: #C44E02;
        --brand-steel:     #4A5568;
        --bg-page:         #F0F2F5;
        --bg-card:         #FFFFFF;
        --text-primary:    #0D1B2A;
        --text-muted:      #6B7280;
        --border:          #DDE1E7;
        --success:         #16A34A;
        --radius-card:     10px;
        --radius-btn:      6px;
        --shadow-card:     0 2px 8px rgba(0,0,0,0.07);
        --shadow-hover:    0 6px 20px rgba(0,0,0,0.13);
        --font-display:    'Barlow Condensed', sans-serif;
        --font-body:       'DM Sans', sans-serif;
        --font-mono:       'Courier New', Courier, monospace;
      }

      /* ── GLOBAL RESETS ──────────────────────────────────────── */
      body { font-family: var(--font-body); background: var(--bg-page);
             color: var(--text-primary); }
      h1, h2, h3, h4 { font-family: var(--font-display); font-weight: 700;
                        letter-spacing: 0.01em; }

      /* ── PAGE HEADER BANNER (replaces .page-header-foodmart) ── */
      .page-header-foodmart {
        background: linear-gradient(135deg, var(--brand-navy) 0%,
                    var(--brand-navy-mid) 60%, #1e3a5f 100%);
        padding: 2.5rem 0;
        position: relative;
        overflow: hidden;
      }
      .page-header-foodmart::after {
        content: '';
        position: absolute; top: 0; right: 0;
        width: 300px; height: 100%;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.4;
        pointer-events: none;
      }
      .page-header-foodmart h1 { color: #fff; font-size: 2rem; margin: 0; }
      .page-header-foodmart .breadcrumb-item a { color: rgba(255,255,255,0.65); }
      .page-header-foodmart .breadcrumb-item.active { color: var(--brand-orange); }
      .page-header-foodmart .breadcrumb-separator { color: rgba(255,255,255,0.4); }

      /* ── BUTTONS ─────────────────────────────────────────────── */
      .btn-fm-primary, .btn-enquiry {
        background: var(--brand-orange);
        border-color: var(--brand-orange);
        color: #fff;
        font-family: var(--font-display);
        font-weight: 600;
        letter-spacing: 0.04em;
        border-radius: var(--radius-btn);
        transition: background 0.2s, transform 0.15s;
      }
      .btn-fm-primary:hover, .btn-enquiry:hover {
        background: var(--brand-orange-dk);
        border-color: var(--brand-orange-dk);
        color: #fff;
        transform: translateY(-1px);
      }
      .btn-navy {
        background: var(--brand-navy);
        border-color: var(--brand-navy);
        color: #fff;
        border-radius: var(--radius-btn);
      }
      .btn-navy:hover { background: var(--brand-navy-mid); color: #fff; }

      /* ── PRODUCT CARDS ───────────────────────────────────────── */
      .product-item {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-card);
        transition: box-shadow 0.2s, border-color 0.2s, transform 0.2s;
        overflow: hidden;
        position: relative;
      }
      .product-item:hover {
        box-shadow: var(--shadow-hover);
        border-color: var(--brand-orange);
        transform: translateY(-3px);
      }

      /* Part Code badge — monospace pill */
      .part-code-badge {
        display: inline-block;
        font-family: var(--font-mono);
        font-size: 0.7rem;
        background: var(--brand-navy);
        color: #fff;
        padding: 2px 8px;
        border-radius: 4px;
        letter-spacing: 0.06em;
        margin-bottom: 4px;
      }

      /* Machine compatibility line */
      .machine-compat {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-style: italic;
      }

      /* ── HEADER ──────────────────────────────────────────────── */
      header { background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
      header .border-bottom { border-color: var(--border) !important; }
      .main-logo a h3 { font-family: var(--font-display); letter-spacing: 0.05em; }

      /* ── SEARCH BAR ──────────────────────────────────────────── */
      .search-bar { background: var(--bg-page) !important; border: 1px solid var(--border); }
      .search-bar input:focus { outline: none; box-shadow: none; }
      .search-bar select { color: var(--text-primary); }

      /* ── SECTION TITLES ──────────────────────────────────────── */
      .section-title {
        font-family: var(--font-display);
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--brand-navy);
        position: relative;
        padding-bottom: 0.5rem;
      }
      .section-title::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0;
        width: 48px; height: 3px;
        background: var(--brand-orange);
        border-radius: 2px;
      }

      /* ── FEATURE STRIP (footer top) ──────────────────────────── */
      .feature-strip-card { border: none !important; background: transparent; }
      .feature-strip-icon { color: var(--brand-orange); }
      .feature-strip-card h5 { font-family: var(--font-display); font-size: 1rem;
                                font-weight: 700; color: var(--brand-navy); }

      /* ── FOOTER ─────────────────────────────────────────────── */
      footer { background: var(--brand-navy) !important; }
      footer a, footer .text-muted, footer p { color: #94A3B8 !important; }
      footer a:hover { color: var(--brand-orange) !important; }
      footer .widget-title { color: #fff !important; font-family: var(--font-display);
                              letter-spacing: 0.06em; font-size: 0.9rem;
                              text-transform: uppercase; }
      #footer-bottom { background: #060E18 !important; }

      /* ── SIDEBAR FILTERS ─────────────────────────────────────── */
      .filter-sidebar { background: #fff; border: 1px solid var(--border);
                        border-radius: var(--radius-card); }
      .filter-sidebar h5 { font-family: var(--font-display); font-size: 0.8rem;
                            text-transform: uppercase; letter-spacing: 0.08em;
                            color: var(--brand-navy); font-weight: 700; }

      /* ── ENQUIRY CART ────────────────────────────────────────── */
      .enquiry-cart-header { border-left: 4px solid var(--brand-orange); padding-left: 1rem; }

      /* ── CONTACT PAGE ────────────────────────────────────────── */
      .contact-info-icon { color: var(--brand-orange); }
    </style>
</head>
<body>

    <!-- SVG Icons -->
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
      <defs>
        <symbol id="heart" viewBox="0 0 24 24"><path fill="currentColor" d="M20.16 4.61A6.27 6.27 0 0 0 12 4a6.27 6.27 0 0 0-8.16 9.48l7.45 7.45a1 1 0 0 0 1.42 0l7.45-7.45a6.27 6.27 0 0 0 0-8.87Zm-1.41 7.46L12 18.81l-6.75-6.74a4.28 4.28 0 0 1 3-7.3a4.25 4.25 0 0 1 3 1.25a1 1 0 0 0 1.42 0a4.27 4.27 0 0 1 6 6.05Z"/></symbol>
        <symbol id="cart" viewBox="0 0 24 24"><path fill="currentColor" d="M8.5 19a1.5 1.5 0 1 0 1.5 1.5A1.5 1.5 0 0 0 8.5 19ZM19 16H7a1 1 0 0 1 0-2h8.491a3.013 3.013 0 0 0 2.885-2.176l1.585-5.55A1 1 0 0 0 19 5H6.74a3.007 3.007 0 0 0-2.82-2H3a1 1 0 0 0 0 2h.921a1.005 1.005 0 0 1 .962.725l.155.545v.005l1.641 5.742A3 3 0 0 0 7 18h12a1 1 0 0 0 0-2Zm-1.326-9l-1.22 4.274a1.005 1.005 0 0 1-.963.726H8.754l-.255-.892L7.326 7ZM16.5 19a1.5 1.5 0 1 0 1.5 1.5a1.5 1.5 0 0 0-1.5-1.5Z"/></symbol>
        <symbol id="search" viewBox="0 0 24 24"><path fill="currentColor" d="M21.71 20.29L18 16.61A9 9 0 1 0 16.61 18l3.68 3.68a1 1 0 0 0 1.42 0a1 1 0 0 0 0-1.39ZM11 18a7 7 0 1 1 7-7a7 7 0 0 1-7 7Z"/></symbol>
        <symbol id="user" viewBox="0 0 24 24"><path fill="currentColor" d="M15.71 12.71a6 6 0 1 0-7.42 0a10 10 0 0 0-6.22 8.18a1 1 0 0 0 2 .22a8 8 0 0 1 15.9 0a1 1 0 0 0 1 .89h.11a1 1 0 0 0 .88-1.1a10 10 0 0 0-6.25-8.19ZM12 12a4 4 0 1 1 4-4a4 4 0 0 1-4 4Z"/></symbol>
        <symbol id="arrow-right" viewBox="0 0 24 24"><path fill="currentColor" d="M17.92 11.62a1 1 0 0 0-.21-.33l-5-5a1 1 0 0 0-1.42 1.42l3.3 3.29H7a1 1 0 0 0 0 2h7.59l-3.3 3.29a1 1 0 0 0 0 1.42a1 1 0 0 0 1.42 0l5-5a1 1 0 0 0 .21-.33a1 1 0 0 0 0-.76Z"/></symbol>
        <symbol id="star-solid" viewBox="0 0 15 15"><path fill="currentColor" d="M7.953 3.788a.5.5 0 0 0-.906 0L6.08 5.85l-2.154.33a.5.5 0 0 0-.283.843l1.574 1.613l-.373 2.284a.5.5 0 0 0 .736.518l1.92-1.063l1.921 1.063a.5.5 0 0 0 .736-.519l-.373-2.283l1.574-1.613a.5.5 0 0 0-.283-.844L8.921 5.85l-.968-2.062Z"/></symbol>
        <symbol id="star-outline" viewBox="0 0 15 15"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M7.5 9.804L5.337 11l.413-2.533L4 6.674l2.418-.37L7.5 4l1.082 2.304l2.418.37l-1.75 1.793L9.663 11L7.5 9.804Z"/></symbol>
        <symbol id="minus" viewBox="0 0 24 24"><path fill="currentColor" d="M19 11H5a1 1 0 0 0 0 2h14a1 1 0 0 0 0-2Z"/></symbol>
        <symbol id="plus" viewBox="0 0 24 24"><path fill="currentColor" d="M19 11h-6V5a1 1 0 0 0-2 0v6H5a1 1 0 0 0 0 2h6v6a1 1 0 0 0 2 0v-6h6a1 1 0 0 0 0-2Z"/></symbol>
        <symbol id="trash" viewBox="0 0 24 24"><path fill="currentColor" d="M10 18a1 1 0 0 0 1-1v-6a1 1 0 0 0-2 0v6a1 1 0 0 0 1 1ZM20 6h-4V5a3 3 0 0 0-3-3h-2a3 3 0 0 0-3 3v1H4a1 1 0 0 0 0 2h1v11a3 3 0 0 0 3 3h8a3 3 0 0 0 3-3V8h1a1 1 0 0 0 0-2ZM10 5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v1h-4Zm7 14a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V8h10Zm-3-1a1 1 0 0 0 1-1v-6a1 1 0 0 0-2 0v6a1 1 0 0 0 1 1Z"/></symbol>
      </defs>
    </svg>

    <!-- Preloader -->
    <div class="preloader-wrapper">
      <div class="preloader"></div>
    </div>

    <!-- Offcanvas Search -->
    <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasSearch" aria-labelledby="Search">
      <div class="offcanvas-header justify-content-center">
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <h4 class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-primary">Search</span>
        </h4>
        <form action="<?= url('/search') ?>" method="GET" class="d-flex mt-3 gap-0">
          <input class="form-control rounded-start rounded-0 bg-light" type="text" name="q" placeholder="Search by part code, name, or machine..." value="<?= e($_GET['q'] ?? '') ?>">
          <button class="btn btn-dark rounded-end rounded-0" type="submit">Search</button>
        </form>
      </div>
    </div>

    <!-- Header -->
    <header>
      <div class="container-fluid">
        <div class="row py-3 border-bottom">

          <div class="col-sm-4 col-lg-3 text-center text-sm-start">
            <div class="main-logo">
              <a href="<?= url('/') ?>" class="text-decoration-none d-flex align-items-center gap-2">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                  <circle cx="16" cy="16" r="15" fill="#0D1B2A"/>
                  <path d="M16 8l2.5 5 5.5.8-4 3.9.9 5.5L16 20.5l-4.9 2.7.9-5.5-4-3.9 5.5-.8z"
                        fill="#E85D04"/>
                </svg>
                <span style="font-family:'Barlow Condensed',sans-serif;font-size:1.5rem;
                             font-weight:800;color:#0D1B2A;letter-spacing:0.05em;">
                  <?= e($store_name) ?>
                </span>
              </a>
            </div>
          </div>

          <div class="col-sm-6 offset-sm-2 offset-md-0 col-lg-5 d-none d-lg-block">
            <form action="<?= url('/search') ?>" method="GET">
              <div class="search-bar row bg-light p-2 my-2 rounded-4">
                <div class="col-md-4 d-none d-md-block">
                  <select class="form-select border-0 bg-transparent" name="category" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= e($cat['slug']) ?>"><?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-11 col-md-7">
                  <input type="text" class="form-control border-0 bg-transparent" name="q" placeholder="Search by part code, name, or machine..." value="<?= e($_GET['q'] ?? '') ?>">
                </div>
                <div class="col-1">
                  <button type="submit" class="btn p-0 border-0 bg-transparent">
                    <svg width="24" height="24" viewBox="0 0 24 24"><use xlink:href="#search"></use></svg>
                  </button>
                </div>
              </div>
            </form>
          </div>

          <div class="col-sm-8 col-lg-4 d-flex justify-content-end gap-5 align-items-center mt-4 mt-sm-0 justify-content-center justify-content-sm-end">
            <div class="support-box text-end d-none d-xl-block">
              <span class="fs-6 text-muted">Need a Quote?</span>
              <h5 class="mb-0"><?= e($phone) ?></h5>
            </div>

            <ul class="d-flex justify-content-end list-unstyled m-0">

              <li>
                <a href="<?= url('/cart') ?>" class="rounded-circle bg-light p-2 mx-1 position-relative" title="Enquiry Cart">
                  <svg width="24" height="24" viewBox="0 0 24 24"><use xlink:href="#cart"></use></svg>
                  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill"
                        style="background:var(--brand-orange);font-size:0.6rem;">
                    <?= isset($cart_count) ? (int)$cart_count : '' ?>
                  </span>
                </a>
              </li>
              <li class="d-lg-none">
                <a href="#" class="rounded-circle bg-light p-2 mx-1" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSearch">
                  <svg width="24" height="24" viewBox="0 0 24 24"><use xlink:href="#search"></use></svg>
                </a>
              </li>
            </ul>

          </div>

        </div>
      </div>
      <div class="container-fluid">
        <div class="row py-3">
          <div class="d-flex justify-content-center justify-content-sm-between align-items-center">
            <nav class="main-menu d-flex navbar navbar-expand-lg">
              <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar">
                <div class="offcanvas-header justify-content-center">
                  <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                  <ul class="navbar-nav justify-content-end menu-list list-unstyled d-flex gap-md-3 mb-0">
                    <li class="nav-item <?= is_active('') ? 'active' : '' ?>">
                      <a href="<?= url('/') ?>" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item <?= starts_with('shop') || starts_with('product') || starts_with('category') ? 'active' : '' ?>">
                      <a href="<?= url('/shop') ?>" class="nav-link">Shop</a>
                    </li>
                    <li class="nav-item">
                      <a href="<?= url('/cart') ?>" class="nav-link">Enquiry Cart</a>
                    </li>
                    <li class="nav-item">
                      <a href="<?= url('/contact') ?>" class="nav-link">Contact</a>
                    </li>
                    <li class="nav-item">
                      <a href="<?= url('/contact') ?>" class="nav-link btn btn-sm ms-2 px-3 text-white"
                         style="background:var(--brand-orange);border-radius:6px;">
                        Get a Quote
                      </a>
                    </li>

                  </ul>
                </div>
              </div>
            </nav>

            <div class="d-none d-lg-block">
              <a href="tel:<?= e($phone) ?>" class="text-decoration-none text-dark">
                <i class="fab fa-whatsapp me-2 text-success"></i><i class="fas fa-phone-alt me-2"></i><?= e($phone) ?>
              </a>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Flash Messages -->
    <?php $flash_success = Session::getFlash('success'); $flash_error = Session::getFlash('error'); ?>
    <?php if ($flash_success): ?>
    <div class="container mt-3"><div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= e($flash_success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div></div>
    <?php endif; ?>
    <?php if ($flash_error): ?>
    <div class="container mt-3"><div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= e($flash_error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div></div>
    <?php endif; ?>
