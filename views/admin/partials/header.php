<?php
/**
 * Admin Layout — Header Partial
 * ──────────────────────────────
 * Based on Mazer Admin template
 * Includes: head, topbar, sidebar start
 */

$store_name = get_setting('general_store_name', 'Electro Store');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title ?? 'Dashboard') ?> — Admin | <?= e($store_name) ?></title>

    <!-- Mazer CSS (Dark Theme) -->
    <link rel="stylesheet" href="<?= asset('admin/compiled/css/app.css') ?>">
    <link rel="stylesheet" href="<?= asset('admin/compiled/css/app-dark.css') ?>">
    <link rel="stylesheet" href="<?= asset('admin/compiled/css/iconly.css') ?>">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />

    <style>
        .sidebar-wrapper .menu .sidebar-item.active > .sidebar-link {
            background-color: #E85D04 !important;
            color: #fff;
        }
        .sidebar-wrapper { border-right: 1px solid rgba(255,255,255,0.06); }
        .sidebar-item .sidebar-link:hover { color: #E85D04 !important; }
        .sidebar-title { color: #E85D04 !important; font-size: 0.65rem; letter-spacing: 0.12em; }
        /* Force dark theme globally */
        body { background-color: #151521 !important; color: #b5b5c3 !important; }
        .card { background-color: #1e1e2d !important; color: #b5b5c3 !important; border-color: #2d2d3f !important; }
        .table { color: #b5b5c3 !important; }
        .table thead th { color: #b5b5c3 !important; }
        .page-heading h3 { color: #fff !important; }
        .breadcrumb-item a, .breadcrumb-item.active { color: #b5b5c3 !important; }
        #main-content { background-color: #151521 !important; }
        .page-title { color: #fff !important; }
        .main-content { background-color: #151521 !important; }
        #main { background-color: #151521 !important; }
    </style>
</head>
<body class="theme-dark" data-bs-theme="dark">
<div id="app">
    <!-- Sidebar -->
    <div id="sidebar">
        <div class="sidebar-wrapper active">
            <div class="sidebar-header position-relative">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="logo">
                        <a href="<?= url('/admin') ?>" class="d-flex align-items-center gap-2 text-decoration-none">
                            <i class="fas fa-cog" style="color:#E85D04;font-size:1.2rem;"></i>
                            <span style="font-weight:800;font-size:1rem;color:#fff;letter-spacing:0.04em;">
                              <?= e($store_name) ?> <span style="color:#E85D04;font-size:0.65rem;
                              vertical-align:middle;font-weight:400;letter-spacing:0.1em;">ADMIN</span>
                            </span>
                        </a>
                    </div>
                    <div class="sidebar-toggler x">
                        <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                    </div>
                </div>
            </div>
            <div class="sidebar-menu">
                <ul class="menu">
                    <!-- MAIN -->
                    <li class="sidebar-title">Main</li>

                    <li class="sidebar-item <?= is_active('admin') || is_active('admin/dashboard') ? 'active' : '' ?>">
                        <a href="<?= url('/admin/dashboard') ?>" class="sidebar-link">
                            <i class="bi bi-grid-fill"></i><span>Dashboard</span>
                        </a>
                    </li>

                    <!-- ENQUIRIES -->
                    <li class="sidebar-title">Enquiries</li>

                    <?php $newEnquiries = (new Enquiry())->count("status = 'new'"); ?>
                    <li class="sidebar-item <?= starts_with('admin/enquiries') ? 'active' : '' ?>">
                        <a href="<?= url('/admin/enquiries') ?>" class="sidebar-link">
                            <i class="bi bi-clipboard-check"></i>
                            <span>All Enquiries</span>
                            <span class="badge ms-auto" style="background:#E85D04;font-size:0.6rem; <?= ($newEnquiries == 0) ? 'display:none;' : '' ?>"
                                  id="new-enquiry-count"><?= $newEnquiries ?></span>
                        </a>
                    </li>

                    <?php $unreadContacts = (new ContactMessage())->countUnread(); ?>
                    <li class="sidebar-item <?= starts_with('admin/contacts') ? 'active' : '' ?>">
                        <a href="<?= url('/admin/contacts') ?>" class="sidebar-link">
                            <i class="bi bi-envelope"></i>
                            <span>Contact Messages</span>
                            <?php if ($unreadContacts > 0): ?>
                            <span class="badge ms-auto" style="background:#6f42c1;font-size:0.6rem;"><?= $unreadContacts ?></span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <li class="sidebar-item <?= starts_with('admin/email-templates') ? 'active' : '' ?>">
                        <a href="<?= url('/admin/email-templates') ?>" class="sidebar-link">
                            <i class="bi bi-file-earmark-text"></i>
                            <span>Email Templates</span>
                        </a>
                    </li>

                    <!-- PARTS CATALOGUE -->
                    <li class="sidebar-title">Parts Catalogue</li>

                    <li class="sidebar-item <?= starts_with('admin/products') ? 'active' : '' ?>">
                        <a href="<?= url('/admin/products') ?>" class="sidebar-link">
                            <i class="bi bi-box-seam"></i><span>Parts / Products</span>
                        </a>
                    </li>

                    <li class="sidebar-item <?= starts_with('admin/categories') ? 'active' : '' ?>">
                        <a href="<?= url('/admin/categories') ?>" class="sidebar-link">
                            <i class="bi bi-tags"></i><span>Categories</span>
                        </a>
                    </li>

                    <li class="sidebar-item <?= starts_with('admin/inventory') ? 'active' : '' ?>">
                        <a href="<?= url('/admin/inventory') ?>" class="sidebar-link">
                            <i class="bi bi-boxes"></i><span>Stock / Inventory</span>
                        </a>
                    </li>

                    <!-- CONTENT -->
                    <li class="sidebar-title">Content</li>

                    <li class="sidebar-item <?= starts_with('admin/banners') ? 'active' : '' ?>">
                        <a href="<?= url('/admin/banners') ?>" class="sidebar-link">
                            <i class="bi bi-play-circle"></i><span>Hero Banners & Videos</span>
                        </a>
                    </li>

                    <!-- ANALYTICS -->
                    <li class="sidebar-title">Analytics</li>

                    <li class="sidebar-item has-sub <?= starts_with('admin/reports') ? 'active' : '' ?>">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-bar-chart"></i><span>Reports</span>
                        </a>
                        <ul class="submenu">
                            <li class="submenu-item"><a href="<?= url('/admin/reports/sales') ?>" class="submenu-link">Enquiry Report</a></li>
                            <li class="submenu-item"><a href="<?= url('/admin/reports/products') ?>" class="submenu-link">Parts Report</a></li>
                        </ul>
                    </li>

                    <!-- SYSTEM -->
                    <li class="sidebar-title">System</li>

                    <li class="sidebar-item <?= starts_with('admin/seo') ? 'active' : '' ?>">
                        <a href="<?= url('/admin/seo') ?>" class="sidebar-link">
                            <i class="bi bi-search"></i><span>SEO</span>
                        </a>
                    </li>

                    <li class="sidebar-item <?= starts_with('admin/settings') ? 'active' : '' ?>">
                        <a href="<?= url('/admin/settings') ?>" class="sidebar-link">
                            <i class="bi bi-gear"></i><span>Settings</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="<?= url('/') ?>" class="sidebar-link" target="_blank">
                            <i class="bi bi-globe"></i><span>View Website</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="<?= url('/logout') ?>" class="sidebar-link">
                            <i class="bi bi-box-arrow-left"></i><span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <!-- Flash Messages -->
        <?php $flash_success = Session::getFlash('success'); $flash_error = Session::getFlash('error'); ?>
        <?php if ($flash_success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= e($flash_success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <?php if ($flash_error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= e($flash_error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="page-heading">
            <h3><?= e($page_title ?? 'Dashboard') ?></h3>
        </div>
        <div class="page-content">
