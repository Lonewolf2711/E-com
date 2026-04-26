<?php
/**
 * Admin Banners — List View
 * ─────────────────────────
 * Displays all homepage banners with management options.
 */
$banners = $banners ?? [];

$positionLabels = [
    'hero'        => ['label' => 'Hero Slider', 'badge' => 'primary'],
    'hero_static' => ['label' => 'Hero Static', 'badge' => 'info'],
];
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted mb-0">Manage hero sliders and video backgrounds on the homepage.</p>
    </div>
    <a href="<?= url('/admin/banners/add') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add Banner
    </a>
</div>

<?php if (empty($banners)): ?>
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-image fs-1 mb-3 d-block text-muted"></i>
        <h5 class="text-muted">No banners yet</h5>
        <p class="text-muted">Add your first homepage banner to get started.</p>
        <a href="<?= url('/admin/banners/add') ?>" class="btn btn-primary mt-2">
            <i class="bi bi-plus-lg me-1"></i>Add First Banner
        </a>
    </div>
</div>
<?php else: ?>

<!-- Hero Banners -->
<?php
$grouped = [];
foreach ($banners as $b) {
    $grouped[$b['position']][] = $b;
}
?>

<?php foreach ($positionLabels as $posKey => $posInfo): ?>
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <span class="badge bg-<?= $posInfo['badge'] ?> me-2"><?= $posInfo['label'] ?></span>
            <small class="text-muted">(<?= count($grouped[$posKey] ?? []) ?> banner<?= count($grouped[$posKey] ?? []) !== 1 ? 's' : '' ?>)</small>
        </h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($grouped[$posKey])): ?>
        <div class="text-center text-muted py-4">
            <i class="bi bi-image me-1"></i>No banners in this position.
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:80px">Media</th>
                        <th>Title</th>
                        <th>Subtitle</th>
                        <th>Button</th>
                        <th style="width:80px">Order</th>
                        <th style="width:90px">Status</th>
                        <th style="width:140px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($grouped[$posKey] as $banner): ?>
                    <tr>
                        <td>
                            <?php if (($banner['media_type'] ?? 'image') === 'video'): ?>
                                <div class="rounded bg-dark text-white d-flex align-items-center justify-content-center" style="width:60px;height:40px;">
                                    <i class="bi bi-play-circle fs-4"></i>
                                </div>
                            <?php elseif (!empty($banner['image'])): ?>
                            <img src="<?= upload_url(e($banner['image'])) ?>" 
                                 alt="Banner" 
                                 class="rounded" 
                                 style="width:60px;height:40px;object-fit:cover;">
                            <?php else: ?>
                            <div class="rounded bg-secondary d-flex align-items-center justify-content-center" style="width:60px;height:40px;">
                                <i class="bi bi-image text-muted"></i>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= e($banner['title'] ?: '—') ?></strong>
                            <?php if (!empty($banner['description'])): ?>
                            <br><small class="text-muted"><?= e(substr($banner['description'], 0, 60)) ?><?= strlen($banner['description']) > 60 ? '...' : '' ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= e($banner['subtitle'] ?: '—') ?></td>
                        <td>
                            <?php if (!empty($banner['button_text'])): ?>
                            <span class="badge bg-light text-dark"><?= e($banner['button_text']) ?></span>
                            <br><small class="text-muted"><?= e($banner['button_link'] ?: '#') ?></small>
                            <?php else: ?>
                            <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?= (int) $banner['sort_order'] ?></td>
                        <td>
                            <?php if ($banner['status'] === 'active'): ?>
                            <span class="badge bg-success">Active</span>
                            <?php else: ?>
                            <span class="badge bg-secondary">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= url('/admin/banners/edit/' . $banner['id']) ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="<?= url('/admin/banners/delete/' . $banner['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this banner?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>

<?php endif; ?>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
