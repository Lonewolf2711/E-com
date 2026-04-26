<?php
/**
 * Admin SEO Management
 */
$entries = $entries ?? [];
$products = $products ?? [];
$categories = $categories ?? [];
$gscVal = get_setting('gsc_verification_tag', '');
$gaVal = get_setting('ga_measurement_id', '');
$seoTitleTemplate = get_setting('seo_default_title', '{product_code} — {name} for {machine_name}');
$seoDescTemplate = get_setting('seo_default_description', '');
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
    <h3>SEO Management</h3>
    <p class="text-subtitle text-muted">
      Optimise part pages for Google search, part code lookups, and Google Lens image discovery.
    </p>
</div>

<div class="page-content">
    <!-- Add/Edit SEO Meta Form -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Edit/Add SEO Meta</h5>
            <p class="text-muted mb-0">Enter part code and machine name in titles and descriptions to improve searchability. Google indexes part codes directly.</p>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="seoTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tabProduct">Product SEO</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tabCategory">Category SEO</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tabGlobal">Global Settings (GA/GSC)</a>
                </li>
            </ul>
            <div class="tab-content mt-3">
                <!-- Product SEO Tab -->
                <div class="tab-pane active" id="tabProduct">
                    <form action="" method="POST" id="productSeoForm">
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Select Part</label>
                                <select class="form-select" id="seoProductSelect" required>
                                    <option value="">Choose a product...</option>
                                    <?php foreach ($products as $p): ?>
                                    <option value="<?= $p['id'] ?>">
                                      [<?= e($p['product_code'] ?? $p['sku'] ?? '-') ?>] <?= e($p['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Meta Title</label>
                                <input type="text" class="form-control" name="meta_title" maxlength="70" placeholder="Page title (max 70 chars)">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Search Keywords (part codes, machine names)</label>
                                <input type="text" class="form-control" name="meta_keywords" placeholder="SP-BEAR-001, deep groove bearing, Sulzer G6300, loom bearing">
                                <div class="form-text">Include the part code, machine model names, and part type.</div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Meta Description</label>
                                <textarea class="form-control" name="meta_description" rows="3" maxlength="160" placeholder="Brief description (max 160 chars)"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">OG Image URL</label>
                                <input type="text" class="form-control" name="og_image" placeholder="https://...">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Save Product SEO</button>
                            </div>
                        </div>
                    </form>
                    <div class="card mt-3">
                      <div class="card-header" style="cursor:pointer;"
                           data-bs-toggle="collapse" data-bs-target="#seoTips">
                        <h6 class="mb-0">
                          <i class="bi bi-lightbulb text-warning me-2"></i>
                          SEO Tips for Spare Parts
                          <i class="bi bi-chevron-down float-end"></i>
                        </h6>
                      </div>
                      <div id="seoTips" class="collapse">
                        <div class="card-body">
                          <ul class="mb-0" style="font-size:0.85rem;line-height:2;">
                            <li><strong>Part Code in URL slug</strong> - the product URL should contain the part code (e.g. /product/sp-bear-001-deep-groove-bearing)</li>
                            <li><strong>Google Lens</strong> - use descriptive alt text on all product images: "SP-BEAR-001 Deep Groove Ball Bearing for Sulzer G6300"</li>
                            <li><strong>Meta title formula</strong> - [Part Code] [Part Name] for [Machine Name] | [Store Name]</li>
                            <li><strong>Meta description formula</strong> - Buy [Part Name] (Part Code: [Code]) compatible with [Machine Models]. Enquire for pricing and availability.</li>
                            <li><strong>Machine model targeting</strong> - list all compatible machine models in keywords to capture model-specific searches.</li>
                          </ul>
                        </div>
                      </div>
                    </div>
                </div>
                <!-- Category SEO Tab -->
                <div class="tab-pane" id="tabCategory">
                    <form action="" method="POST" id="categorySeoForm">
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Select Category</label>
                                <select class="form-select" id="seoCategorySelect" required>
                                    <option value="">Choose a category...</option>
                                    <?php foreach ($categories as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Meta Title</label>
                                <input type="text" class="form-control" name="meta_title" maxlength="70">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control" name="meta_keywords">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Meta Description</label>
                                <textarea class="form-control" name="meta_description" rows="3" maxlength="160"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">OG Image URL</label>
                                <input type="text" class="form-control" name="og_image">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Save Category SEO</button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Global Settings Tab -->
                <div class="tab-pane" id="tabGlobal">
                    <form action="<?= url('/admin/settings/update') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Google Search Console Verification Tag</label>
                                <input type="text" class="form-control" name="settings[gsc_verification_tag]" value="<?= e($gscVal) ?>" placeholder="e.g. the content value of the meta verification tag">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Google Analytics Measurement ID</label>
                                <input type="text" class="form-control" name="settings[ga_measurement_id]" value="<?= e($gaVal) ?>" placeholder="e.g. G-XXXXXXX">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Default Meta Title Template</label>
                                <input type="text" class="form-control" name="settings[seo_default_title]" value="<?= e($seoTitleTemplate) ?>" placeholder="{product_code} — {name} for {machine_name}">
                                <div class="form-text">Placeholders: {product_code}, {name}, {machine_name}</div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Default Meta Description Template</label>
                                <textarea class="form-control" name="settings[seo_default_description]" rows="3"><?= e($seoDescTemplate) ?></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Save Global SEO Settings</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Existing Entries -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">All SEO Entries</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>Type</th><th>Page</th><th>Part Code</th><th>Meta Title</th><th>Meta Description</th><th>Keywords</th></tr></thead>
                    <tbody>
                        <?php foreach ($entries as $e_item): ?>
                        <tr>
                            <td><span class="badge bg-light-primary text-uppercase"><?= e($e_item['page_type']) ?></span></td>
                            <td class="fw-bold"><?= e($e_item['page_title'] ?? '-') ?></td>
                            <td><code class="text-warning" style="font-size:0.75rem;">
                                <?= e($e_item['product_code'] ?? $e_item['sku'] ?? '-') ?>
                            </code></td>
                            <td><?= e(truncate($e_item['meta_title'] ?? '-', 40)) ?></td>
                            <td class="text-muted small"><?= e(truncate($e_item['meta_description'] ?? '-', 60)) ?></td>
                            <td class="text-muted small"><?= e(truncate($e_item['meta_keywords'] ?? '-', 30)) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($entries)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No SEO entries yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('productSeoForm').addEventListener('submit', function(e) {
    const id = document.getElementById('seoProductSelect').value;
    if (!id) { e.preventDefault(); alert('Select a product first.'); return; }
    this.action = '<?= url('/admin/seo/update/product/') ?>' + id;
});
document.getElementById('categorySeoForm').addEventListener('submit', function(e) {
    const id = document.getElementById('seoCategorySelect').value;
    if (!id) { e.preventDefault(); alert('Select a category first.'); return; }
    this.action = '<?= url('/admin/seo/update/category/') ?>' + id;
});
</script>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
