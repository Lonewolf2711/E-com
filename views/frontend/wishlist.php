<?php
/**
 * Wishlist Page (FoodMart Theme)
 */
$items = $items ?? [];
?>
<?php require_once VIEW_PATH . '/frontend/partials/header.php'; ?>

<div class="page-header-foodmart py-4">
    <div class="container-fluid">
        <h1 class="text-center text-white display-6">My Wishlist</h1>
        <nav><ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item active text-white">Wishlist</li>
        </ol></nav>
    </div>
</div>

<section class="py-5">
    <div class="container-fluid">
        <?php if (!empty($items)): ?>
        <div class="product-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
            <?php foreach ($items as $index => $item): ?>
            <div class="col">
              <div class="product-item">
                <?php if ($item['stock'] <= 0): ?>
                <span class="badge bg-danger position-absolute m-3">Out of Stock</span>
                <?php endif; ?>
                <figure>
                  <a href="<?= url('/product/' . e($item['product_slug'])) ?>">
                    <img src="<?= $item['image'] ? upload_url(e($item['image'])) : asset('frontend/foodmart-images/thumb-bananas.png') ?>" class="tab-image" alt="<?= e($item['product_name']) ?>">
                  </a>
                </figure>
                <h3><?= e(truncate($item['product_name'], 35)) ?></h3>
                <span class="qty"><?= e($item['category_name'] ?? 'General') ?></span>
                <?php if ($item['sale_price']): ?>
                <span class="price"><del class="text-muted me-1"><?= formatPrice($item['price']) ?></del> <?= formatPrice($item['sale_price']) ?></span>
                <?php else: ?>
                <span class="price"><?= formatPrice($item['price']) ?></span>
                <?php endif; ?>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <form action="<?= url('/wishlist/toggle') ?>" method="POST" class="d-inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-1"><svg width="14" height="14"><use xlink:href="#trash"></use></svg> Remove</button>
                    </form>
                    <?php if ($item['stock'] > 0): ?>
                    <a href="<?= url('/product/' . e($item['product_slug'])) ?>" class="btn btn-sm btn-dark rounded-1">View</a>
                    <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <svg width="80" height="80" class="text-muted mb-4"><use xlink:href="#heart"></use></svg>
            <h3 class="text-muted">Your wishlist is empty</h3>
            <a href="<?= url('/shop') ?>" class="btn btn-dark rounded-1 py-3 px-5 mt-3"><i class="fas fa-shopping-bag me-2"></i>Browse Products</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once VIEW_PATH . '/frontend/partials/footer.php'; ?>
