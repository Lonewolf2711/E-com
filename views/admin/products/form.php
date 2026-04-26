<?php
/**
 * Admin Product Add/Edit Form
 * ───────────────────────────
 * Reused for both create and update
 */
$product = $product ?? null;
$categories = $categories ?? [];
$images = $images ?? [];
$attributes = $attributes ?? [];
$isEdit = !empty($product);
$formAction = $isEdit ? url('/admin/products/update/' . $product['id']) : url('/admin/products/store');
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <h3><?= $isEdit ? 'Edit Product' : 'Add Product' ?></h3>
        <a href="<?= url('/admin/products') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="page-content">
    <form action="<?= $formAction ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="row">
            <!-- Main Fields -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Product Information</h4></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="<?= e($product['name'] ?? '') ?>" required>
                        </div>
                        
                        <!-- Part Code -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Part Code <span class="text-danger">*</span>
                                <span class="badge bg-info ms-2" style="font-size:0.65rem;">Unique ID</span>
                            </label>
                            <input type="text" class="form-control font-monospace"
                                   name="product_code"
                                   value="<?= e($product['product_code'] ?? '') ?>"
                                   placeholder="e.g. SP-BEAR-001"
                                   style="font-family:'Courier New',monospace;letter-spacing:0.05em;">
                            <div class="form-text">
                                Unique code customers use to search for this part.
                                Format suggestion: [TYPE]-[CATEGORY]-[NUMBER] e.g. SP-BEAR-001
                            </div>
                        </div>

                        <!-- Machine Name -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Machine Name</label>
                            <input type="text" class="form-control"
                                   name="machine_name"
                                   value="<?= e($product['machine_name'] ?? '') ?>"
                                   placeholder="e.g. Sulzer G6300 Rapier Loom">
                            <div class="form-text">The primary machine this part belongs to.</div>
                        </div>

                        <!-- Machine Model & Compatible Machines — side by side -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Machine Model</label>
                                <input type="text" class="form-control"
                                       name="machine_model"
                                       value="<?= e($product['machine_model'] ?? '') ?>"
                                       placeholder="e.g. G6300">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Compatible Machines</label>
                                <input type="text" class="form-control"
                                       name="compatible_machines"
                                       value="<?= e($product['compatible_machines'] ?? '') ?>"
                                       placeholder="e.g. G6300, G6200, P7100">
                                <div class="form-text">Comma-separated list of compatible models.</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Short Description</label>
                            <input type="text" class="form-control" name="short_description" value="<?= e($product['short_description'] ?? '') ?>" maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Full Description</label>
                            <textarea class="form-control" name="description" rows="6"><?= e($product['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Stock -->
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Pricing & Availability</h4></div>
                    <div class="card-body">
                        <div class="alert alert-info alert-sm py-2 mb-3" style="font-size:0.82rem;">
                            <i class="bi bi-info-circle me-1"></i>
                            Prices are <strong>internal only</strong> — customers do not see pricing on
                            the frontend. They submit an enquiry and receive a quotation.
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Internal Price (₹)</label>
                                <input type="number" class="form-control" name="price" value="<?= $product['price'] ?? '' ?>" step="0.01" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Quoted/Sale Price (₹)</label>
                                <input type="number" class="form-control" name="sale_price" value="<?= $product['sale_price'] ?? '' ?>" step="0.01" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Stock Quantity</label>
                                <input type="number" class="form-control" name="stock" value="<?= $product['stock'] ?? 0 ?>" min="0">
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label">Internal SKU / Reference Code</label>
                                <input type="text" class="form-control" name="sku" value="<?= e($product['sku'] ?? '') ?>">
                                <div class="form-text">Internal reference. Use 'Part Code' above for the customer-facing code.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Images</h4></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Main Image</label>
                            <?php if ($isEdit && $product['image']): ?>
                            <div class="mb-2"><img src="<?= upload_url(e($product['image'])) ?>" class="rounded border" style="width:100px;height:100px;object-fit:cover;" alt=""></div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gallery Images</label>
                            <?php if (!empty($images)): ?>
                            <div class="d-flex gap-2 mb-2 flex-wrap">
                                <?php foreach ($images as $img): ?>
                                <img src="<?= upload_url(e($img['image_path'])) ?>" class="rounded border" style="width:80px;height:80px;object-fit:cover;" alt="">
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="gallery[]" accept="image/*" multiple>
                            <small class="text-muted">You can select multiple images at once.</small>
                        </div>
                    </div>
                </div>

                <!-- Attributes -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Attributes</h4>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addAttribute()"><i class="bi bi-plus me-1"></i>Add</button>
                    </div>
                    <div class="card-body" id="attributes-container">
                        <?php if (!empty($attributes)): ?>
                            <?php foreach ($attributes as $attr): ?>
                            <div class="row g-2 mb-2 attr-row">
                                <div class="col-5"><input type="text" class="form-control" name="attr_name[]" value="<?= e($attr['attribute_name']) ?>" placeholder="Name (e.g. Color)"></div>
                                <div class="col-5"><input type="text" class="form-control" name="attr_value[]" value="<?= e($attr['attribute_value']) ?>" placeholder="Value (e.g. Red)"></div>
                                <div class="col-2"><button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.attr-row').remove()"><i class="bi bi-trash"></i></button></div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- SEO -->
                <div class="card">
                    <div class="card-header"><h4 class="card-title">SEO</h4></div>
                    <div class="card-body">
                        <div class="alert alert-secondary py-2 mb-3" style="font-size:0.8rem;">
                            <i class="bi bi-lightbulb me-1 text-warning"></i>
                            <strong>SEO tip:</strong> Include the Part Code and Machine Name in both
                            the title and description — this helps Google Lens and code-based searches
                            find this part.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" class="form-control" name="meta_title" value="<?= e($product['meta_title'] ?? '') ?>" maxlength="70" placeholder="e.g. SP-BEAR-001 Deep Groove Ball Bearing for Sulzer G6300">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea class="form-control" name="meta_description" rows="2" maxlength="160" placeholder="e.g. Buy SP-BEAR-001 bearing compatible with Sulzer G6300, G6200 looms. Enquire for pricing."><?= e($product['meta_description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Publish</h4></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="active" <?= ($product['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= ($product['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" <?= ($product['is_featured'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_featured">Featured Product</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_available_on_request" id="is_available_on_request" value="1" <?= ($product['is_available_on_request'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_available_on_request">Available on Request</label>
                            <div class="form-text">Shows "On Request" badge instead of "Available".</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-<?= $isEdit ? 'check-circle' : 'plus-circle' ?> me-2"></i><?= $isEdit ? 'Update Product' : 'Create Product' ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function addAttribute() {
    const container = document.getElementById('attributes-container');
    const row = document.createElement('div');
    row.className = 'row g-2 mb-2 attr-row';
    row.innerHTML = '<div class="col-5"><input type="text" class="form-control" name="attr_name[]" placeholder="Name (e.g. Color)"></div><div class="col-5"><input type="text" class="form-control" name="attr_value[]" placeholder="Value (e.g. Red)"></div><div class="col-2"><button type="button" class="btn btn-outline-danger w-100" onclick="this.closest(\'.attr-row\').remove()"><i class="bi bi-trash"></i></button></div>';
    container.appendChild(row);
}
</script>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
