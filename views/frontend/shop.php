<?php
/**
 * Shop Page (FoodMart Theme)
 * ─────────────────────────
 * Reused for Shop, Search, and Category views
 */

$products = $products ?? ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
$filters = $filters ?? [];
$search_mode = $search_mode ?? false;
$category_mode = $category_mode ?? false;
$current_category = $current_category ?? null;
$sub_categories = $sub_categories ?? [];
?>
<?php require_once VIEW_PATH . '/frontend/partials/header.php'; ?>

<!-- Page Header -->
<div class="page-header-foodmart py-4">
    <div class="container-fluid">
        <h1 class="text-center text-white display-6"><?= e($page_title ?? 'Shop') ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
                <?php if ($category_mode && $current_category): ?>
                    <li class="breadcrumb-item"><a href="<?= url('/shop') ?>">Shop</a></li>
                    <li class="breadcrumb-item active text-white"><?= e($current_category['name']) ?></li>
                <?php elseif ($search_mode): ?>
                    <li class="breadcrumb-item active text-white">Search Results</li>
                <?php else: ?>
                    <li class="breadcrumb-item active text-white">Shop</li>
                <?php endif; ?>
            </ol>
        </nav>
    </div>
</div>

<!-- Shop Content -->
<section class="py-5">
    <div class="container-fluid">
        <div class="row g-4">
            <!-- Sidebar Filters -->
            <div class="col-lg-3">
                <div class="filter-sidebar p-4 mb-4">
                    <h5 class="mb-3"><i class="fas fa-filter me-2" style="color:var(--brand-orange);"></i>Filter Parts</h5>
                    <form action="<?= $category_mode ? url('/category/' . e($current_category['slug'])) : url('/shop') ?>" method="GET">
                        <?php if (!empty($filters['search'])): ?>
                            <input type="hidden" name="q" value="<?= e($filters['search']) ?>">
                        <?php endif; ?>

                        <!-- Sort -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Sort Parts By</label>
                            <select name="sort" class="form-select" onchange="this.form.submit()">
                                <option value="newest" <?= ($filters['sort'] ?? '') === 'newest' ? 'selected' : '' ?>>Newest</option>
                                <option value="price_low" <?= ($filters['sort'] ?? '') === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                                <option value="price_high" <?= ($filters['sort'] ?? '') === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                                <option value="name_asc" <?= ($filters['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>>Name: A-Z</option>
                                <option value="name_desc" <?= ($filters['sort'] ?? '') === 'name_desc' ? 'selected' : '' ?>>Name: Z-A</option>
                                <option value="popular" <?= ($filters['sort'] ?? '') === 'popular' ? 'selected' : '' ?>>Popular</option>
                            </select>
                        </div>



                        <button type="submit" class="btn btn-navy rounded-1 w-100">
                            <i class="fas fa-search me-2"></i>Apply Filters
                        </button>

                        <?php if (!empty($filters['min_price']) || !empty($filters['max_price']) || !empty($filters['search'])): ?>
                        <a href="<?= $category_mode ? url('/category/' . e($current_category['slug'])) : url('/shop') ?>" class="btn btn-outline-secondary rounded-1 w-100 mt-2">
                            <i class="fas fa-times me-2"></i>Clear Filters
                        </a>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Categories -->
                <div class="filter-sidebar p-4">
                    <h5 class="mb-3"><i class="fas fa-list me-2" style="color:var(--brand-orange);"></i>Categories</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <a href="<?= url('/shop') ?>" class="text-dark <?= !$category_mode ? 'fw-bold' : '' ?>" <?= !$category_mode ? 'style="color:var(--brand-orange) !important;"' : '' ?>>
                                All Parts
                            </a>
                        </li>
                        <?php foreach ($categories as $cat): ?>
                        <li class="mb-2">
                            <a href="<?= url('/category/' . e($cat['slug'])) ?>" class="text-dark <?= ($current_category && $current_category['id'] == $cat['id']) ? 'fw-bold' : '' ?>" <?= ($current_category && $current_category['id'] == $cat['id']) ? 'style="color:var(--brand-orange) !important;"' : '' ?>>
                                <?= e($cat['name']) ?> <span class="text-muted">(<?= (int)($cat['product_count'] ?? 0) ?>)</span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <?php if ($category_mode && !empty($sub_categories)): ?>
                <div class="bg-light rounded-4 p-4 mt-4">
                    <h5 class="mb-3">Sub-categories</h5>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($sub_categories as $sub): ?>
                        <li class="mb-2">
                            <a href="<?= url('/category/' . e($sub['slug'])) ?>" class="text-dark"><?= e($sub['name']) ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>

            <!-- Product Grid -->
            <div class="col-lg-9">
                <!-- Results Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <p class="text-muted mb-0">
                        Showing <strong><?= count($products['data']) ?></strong> of <strong><?= $products['total'] ?></strong> parts
                        <?php if (!empty($filters['search'])): ?>
                            for "<strong><?= e($filters['search']) ?></strong>"
                        <?php endif; ?>
                    </p>
                </div>

                <!-- Products -->
                <div class="product-grid row row-cols-1 row-cols-sm-2 row-cols-md-3">
                    <?php if (!empty($products['data'])): ?>
                        <?php foreach ($products['data'] as $index => $product): ?>
                        <div class="col">
                          <div class="product-item">
                            <?php if ($product['sale_price']): ?>
                            <span class="badge bg-warning position-absolute m-3">Sale</span>
                            <?php elseif ($product['is_featured']): ?>
                            <span class="badge bg-info text-white position-absolute m-3">Featured</span>
                            <?php endif; ?>
                            <figure>
                              <a href="<?= url('/product/' . e($product['slug'])) ?>">
                                <img src="<?= $product['image'] ? upload_url(e($product['image'])) : asset('frontend/foodmart-images/thumb-bananas.png') ?>" class="tab-image" alt="<?= e($product['product_code'] ?? ($product['sku'] ?? 'N/A')) ?> <?= e($product['name']) ?>">
                              </a>
                            </figure>
                            <?php if (!empty($product['sku'])): ?>
                            <div class="px-3 pt-2">
                              <span class="part-code-badge"><?= e($product['sku']) ?></span>
                            </div>
                            <?php endif; ?>
                            <h3 title="<?= e($product['name']) ?>"><?= e(truncate($product['name'], 40)) ?></h3>
                            <span class="qty"><?= e($product['category_name'] ?? 'General') ?></span>
                            <span class="badge bg-secondary font-monospace"><?= e($product['product_code'] ?: ($product['sku'] ?? 'N/A')) ?></span>
                            <?php if (!empty($product['machine_name'])): ?>
                            <small class="text-muted d-block mt-2">For: <?= e($product['machine_name']) ?></small>
                            <?php endif; ?>
                            <div class="d-flex align-items-center justify-content-between mt-2">
                              <form action="<?= url('/cart/add') ?>" method="POST" class="w-100">
                                  <?= csrf_field() ?>
                                  <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                  <input type="hidden" name="quantity" value="1">
                                  <button type="submit" class="btn btn-enquiry btn-sm w-100"><i class="fas fa-plus me-1"></i> Add to Enquiry</button>
                              </form>
                            </div>
                          </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                          <i class="fas fa-cogs fa-3x mb-3" style="color:var(--text-muted);"></i>
                          <h4 style="color:var(--text-muted);">No parts found</h4>
                          <p class="text-muted">Try a different category, or
                            <a href="<?= url('/contact') ?>" style="color:var(--brand-orange);">
                              send us your requirement directly</a>.
                          </p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($products['pages'] > 1): ?>
                <nav class="mt-5">
                    <ul class="pagination justify-content-center">
                        <?php
                        $baseUrl = strtok($_SERVER['REQUEST_URI'], '?');
                        $queryParams = $_GET;
                        for ($i = 1; $i <= $products['pages']; $i++):
                            $queryParams['page'] = $i;
                            $pageUrl = $baseUrl . '?' . http_build_query($queryParams);
                        ?>
                        <li class="page-item <?= $i === $products['current_page'] ? 'active' : '' ?>">
                            <a class="page-link rounded-1 mx-1" href="<?= $pageUrl ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once VIEW_PATH . '/frontend/partials/footer.php'; ?>
