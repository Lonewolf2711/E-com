<?php
/**
 * Product Detail Page (FoodMart Theme)
 * ────────────────────────────────────
 * Single product with images, attributes, reviews, and related products
 */

$product = $product ?? [];
$images = $images ?? [];
$attributes = $attributes ?? [];
$reviews = $reviews ?? [];
$avg_rating = $avg_rating ?? 0;
$rating_breakdown = $rating_breakdown ?? [5=>0,4=>0,3=>0,2=>0,1=>0];
$related = $related ?? [];
$in_wishlist = $in_wishlist ?? false;
$has_reviewed = $has_reviewed ?? false;
$effective_price = $product['sale_price'] ?: $product['price'];
$discount_pct = $product['sale_price'] ? round((($product['price'] - $product['sale_price']) / $product['price']) * 100) : 0;
$total_reviews = array_sum($rating_breakdown);
$store_name = get_setting('general_store_name', 'FoodMart');
$page_title = ($product['product_code'] ?? '') . ' — ' . $product['name'] . ' for ' . ($product['machine_name'] ?? '');
$meta_description = 'Buy ' . $product['name'] . ' (Part Code: ' . ($product['product_code'] ?? '') . ') compatible with ' . ($product['machine_name'] ?? '') . ', ' . ($product['compatible_machines'] ?? '') . '. Enquire now for pricing and availability.';
$og_title = $page_title;
$og_description = $meta_description;
$og_image = $product['image'] ? upload_url($product['image']) : '';
$og_type = 'product';
$canonical_url = current_url();
?>
<?php require_once VIEW_PATH . '/frontend/partials/header.php'; ?>

<!-- JSON-LD Structured Data for Product -->
<script type="application/ld+json">
<?php
$structuredData = [
    '@context' => 'https://schema.org/',
    '@type' => 'Product',
    'name' => $product['name'],
    'description' => $product['description'] ?? '',
    'sku' => $product['product_code'] ?? ($product['sku'] ?? ''),
    'image' => $product['image'] ? upload_url($product['image']) : asset('frontend/foodmart-images/thumb-bananas.png'),
    'brand' => ['@type' => 'Brand', 'name' => $store_name],
    'offers' => [
        '@type' => 'Offer',
        'url' => url('/product/' . $product['slug']),
        'priceCurrency' => 'INR',
        'price' => number_format($effective_price, 2, '.', ''),
        'availability' => $product['stock'] > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
        'itemCondition' => 'https://schema.org/NewCondition',
        'seller' => ['@type' => 'Organization', 'name' => $store_name]
    ],
];
if ($total_reviews > 0) {
    $structuredData['aggregateRating'] = [
        '@type' => 'AggregateRating',
        'ratingValue' => number_format($avg_rating, 1),
        'reviewCount' => $total_reviews,
        'bestRating' => '5',
        'worstRating' => '1',
    ];
}
if (!empty($product['category_name'])) {
    $structuredData['category'] = $product['category_name'];
}
echo json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
</script>

<!-- Page Header -->
<div class="page-header-foodmart py-4">
    <div class="container-fluid">
        <h1 class="text-center text-white display-6"><?= e($product['name']) ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= url('/shop') ?>">Shop</a></li>
                <?php if (!empty($product['category_name'])): ?>
                <li class="breadcrumb-item"><a href="<?= url('/category/' . e($product['category_slug'] ?? '')) ?>"><?= e($product['category_name']) ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active text-white"><?= e(truncate($product['name'], 30)) ?></li>
            </ol>
        </nav>
    </div>
</div>

<!-- Product Detail -->
<section class="py-5">
    <div class="container-fluid">
        <div class="row g-5">
            <!-- Product Images -->
            <div class="col-lg-6">
                <div class="border rounded-4 bg-light p-4">
                    <img id="mainImage" itemprop="image" src="<?= $product['image'] ? upload_url(e($product['image'])) : asset('frontend/foodmart-images/thumb-bananas.png') ?>" class="img-fluid w-100 rounded" alt="<?= e($product['product_code'] ?? '') ?> <?= e($product['name']) ?> spare part for <?= e($product['machine_name'] ?? '') ?>">
                </div>
                <?php if (!empty($images)): ?>
                <div class="d-flex mt-3 gap-2 flex-wrap">
                    <div class="border rounded p-1 cursor-pointer product-thumb active" onclick="changeImage(this, '<?= $product['image'] ? upload_url(e($product['image'])) : asset('frontend/foodmart-images/thumb-bananas.png') ?>')">
                        <img src="<?= $product['image'] ? upload_url(e($product['image'])) : asset('frontend/foodmart-images/thumb-bananas.png') ?>" class="img-fluid" style="width:80px;height:80px;object-fit:cover;" alt="Main">
                    </div>
                    <?php foreach ($images as $img): ?>
                    <div class="border rounded p-1 cursor-pointer product-thumb" onclick="changeImage(this, '<?= upload_url(e($img['image_path'])) ?>')">
                        <img src="<?= upload_url(e($img['image_path'])) ?>" class="img-fluid" style="width:80px;height:80px;object-fit:cover;" alt="<?= e($img['alt_text'] ?? '') ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6">
                <h2 class="fw-bold mb-3"><?= e($product['name']) ?></h2>

                <?php if (!empty($product['sku'])): ?>
                <div class="mb-3">
                  <span class="part-code-badge" style="font-size:0.85rem;padding:4px 12px;">
                    Part Code: <?= e($product['sku']) ?>
                  </span>
                </div>
                <?php endif; ?>

                <div class="d-flex align-items-center gap-2 mb-3">
                  <?php if ($product['stock'] > 0): ?>
                  <span class="badge" style="background:var(--success);font-size:0.8rem;">
                    <i class="fas fa-check-circle me-1"></i>Available
                  </span>
                  <?php else: ?>
                  <span class="badge bg-secondary" style="font-size:0.8rem;">
                    <i class="fas fa-clock me-1"></i>On Request
                  </span>
                  <?php endif; ?>
                  <span class="text-muted" style="font-size:0.8rem;">
                    Pricing provided in quotation
                  </span>
                </div>

                <!-- Short Description -->
                <?php if (!empty($product['short_description'])): ?>
                <p class="mb-4"><?= e($product['short_description']) ?></p>
                <?php endif; ?>

                <!-- Part Details -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm mb-0">
                        <tbody>
                            <tr>
                                <th class="table-light" style="width: 40%;">Product Code</th>
                                <td><span class="font-monospace fw-bold"><?= e($product['product_code'] ?: ($product['sku'] ?? 'N/A')) ?></span></td>
                            </tr>
                            <?php if (!empty($product['machine_name'])): ?>
                            <tr>
                                <th class="table-light">Machine Name</th>
                                <td><?= e($product['machine_name']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($product['compatible_machines'])): ?>
                            <tr>
                                <th class="table-light">Compatible With</th>
                                <td><?= e($product['compatible_machines']) ?></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Attributes -->
                <?php if (!empty($attributes)): ?>
                <div class="mb-4">
                    <?php foreach ($attributes as $attr): ?>
                    <span class="badge bg-light text-dark border me-2 mb-2 py-2 px-3">
                        <strong><?= e($attr['attribute_name']) ?>:</strong> <?= e($attr['attribute_value']) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Add to Cart -->
                <?php if ($product['stock'] > 0): ?>
                <form action="<?= url('/cart/add') ?>" method="POST" class="d-flex align-items-center mb-2">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <div class="input-group product-qty me-3" style="width: 140px;">
                        <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus" onclick="changeQty(-1)">
                          <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                        </button>
                        <input type="number" class="form-control input-number text-center" id="qty" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>">
                        <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus" onclick="changeQty(1)">
                          <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                        </button>
                    </div>
                    <button type="submit" class="btn btn-enquiry btn-lg w-100">
                        <i class="fas fa-clipboard-list me-2"></i>Add to Enquiry
                    </button>
                </form>
                <div class="mb-4 mt-3 p-3 rounded-3" style="background:var(--bg-page); border-left:3px solid var(--brand-orange);">
                  <small class="text-muted">
                    <i class="fas fa-info-circle me-1" style="color:var(--brand-orange);"></i>
                    No payment required. Add to your enquiry cart and we'll respond with availability and pricing within 24 hours.
                  </small>
                </div>
                <?php else: ?>
                <div class="alert alert-warning mb-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>This product is currently out of stock.
                </div>
                <?php endif; ?>

                <!-- Service badges -->
                <div class="row g-3 mt-4">
                    <div class="col-4 text-center">
                        <i class="fas fa-truck text-success fs-4"></i>
                        <p class="small mb-0 mt-1">Free Shipping</p>
                    </div>
                    <div class="col-4 text-center">
                        <i class="fas fa-undo text-success fs-4"></i>
                        <p class="small mb-0 mt-1">30 Day Returns</p>
                    </div>
                    <div class="col-4 text-center">
                        <i class="fas fa-shield-alt text-success fs-4"></i>
                        <p class="small mb-0 mt-1">Secure Payment</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">Product Description</h3>
                <div class="border rounded p-4 bg-light">
                    <?= nl2br(e($product['description'] ?? 'No description available.')) ?>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($related)): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">Other Parts You May Need</h3>
            </div>
            <div class="product-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
            <?php foreach ($related as $index => $rel): ?>
            <div class="col">
              <div class="product-item">
                <figure>
                  <a href="<?= url('/product/' . e($rel['slug'])) ?>">
                    <img src="<?= $rel['image'] ? upload_url(e($rel['image'])) : asset('frontend/foodmart-images/thumb-bananas.png') ?>" class="tab-image" alt="<?= e($rel['name']) ?>">
                  </a>
                </figure>
                <h3><?= e(truncate($rel['name'], 40)) ?></h3>
                
                <?php if (!empty($rel['sku'])): ?>
                <div class="mb-2">
                  <span class="part-code-badge" style="font-size:0.75rem;padding:2px 8px;">
                    Part Code: <?= e($rel['sku']) ?>
                  </span>
                </div>
                <?php endif; ?>

                <div class="d-flex align-items-center justify-content-between mt-2">
                  <form action="<?= url('/cart/add') ?>" method="POST" class="w-100">
                      <?= csrf_field() ?>
                      <input type="hidden" name="product_id" value="<?= $rel['id'] ?>">
                      <input type="hidden" name="quantity" value="1">
                      <button type="submit" class="btn btn-enquiry btn-sm w-100"><i class="fas fa-plus me-1"></i> Add to Enquiry</button>
                  </form>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Inline Styles & Scripts -->
<style>
.product-thumb { cursor: pointer; opacity: 0.6; transition: opacity 0.3s; }
.product-thumb.active, .product-thumb:hover { opacity: 1; border-color: #2d6a4f !important; }
.star-rating-input { display: flex; flex-direction: row-reverse; gap: 5px; }
.star-rating-input input { display: none; }
.star-rating-input label { cursor: pointer; font-size: 1.5rem; color: #ddd; transition: color 0.2s; }
.star-rating-input input:checked ~ label, .star-rating-input label:hover, .star-rating-input label:hover ~ label { color: #ffc107; }
</style>
<script>
function changeImage(thumb, src) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.product-thumb').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
}
function changeQty(delta) {
    const input = document.getElementById('qty');
    let val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    if (val > parseInt(input.max)) val = parseInt(input.max);
    input.value = val;
}
</script>

<?php require_once VIEW_PATH . '/frontend/partials/footer.php'; ?>
