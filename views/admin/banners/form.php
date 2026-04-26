<?php
/**
 * Admin Banners — Add/Edit Form
 * ──────────────────────────────
 * Create or update a homepage banner.
 */
$banner = $banner ?? null;
$isEdit = $banner !== null;
$formAction = $isEdit
    ? url('/admin/banners/update/' . $banner['id'])
    : url('/admin/banners/store');
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="<?= url('/admin/banners') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back to Banners
        </a>
    </div>
</div>

<form action="<?= $formAction ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row">
        <!-- Left Column: Main Details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-type me-2"></i>Banner Content</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Title <span class="text-muted fw-normal">(main heading)</span></label>
                            <input type="text" class="form-control" name="title" 
                                   value="<?= e($banner['title'] ?? '') ?>" 
                                   placeholder="e.g. Precision Parts. Fast Quotations.">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Subtitle <span class="text-muted fw-normal">(small text above title)</span></label>
                            <input type="text" class="form-control" name="subtitle" 
                                   value="<?= e($banner['subtitle'] ?? '') ?>"
                                   placeholder="e.g. Industrial Spare Parts">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Description <span class="text-muted fw-normal">(paragraph text, optional)</span></label>
                            <textarea class="form-control" name="description" rows="3" 
                                      placeholder="e.g. Source genuine parts for all major industrial machines."><?= e($banner['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-link-45deg me-2"></i>Call to Action</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Button Text</label>
                            <input type="text" class="form-control" name="button_text" 
                                   value="<?= e($banner['button_text'] ?? '') ?>"
                                   placeholder="e.g. Browse Parts">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Button Link</label>
                            <input type="text" class="form-control" name="button_link" 
                                   value="<?= e($banner['button_link'] ?? '') ?>"
                                   placeholder="e.g. /shop or /category/bearings">
                            <div class="form-text">Relative path starting with /</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-play-circle me-2"></i>Media</h5>
                </div>
                <div class="card-body">

                    <!-- Media Type Toggle -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Media Type</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="media_type" id="mediaImage" value="image" 
                                    <?= ($banner['media_type'] ?? 'image') === 'image' ? 'checked' : '' ?> onchange="toggleMediaType('image')">
                                <label class="form-check-label" for="mediaImage">
                                    <i class="bi bi-image me-1"></i> Image Slide
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="media_type" id="mediaVideo" value="video" 
                                    <?= ($banner['media_type'] ?? '') === 'video' ? 'checked' : '' ?> onchange="toggleMediaType('video')">
                                <label class="form-check-label" for="mediaVideo">
                                    <i class="bi bi-play-circle me-1"></i> Video Slide
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- IMAGE section -->
                    <div id="imageSection" style="<?= ($banner['media_type'] ?? 'image') === 'video' ? 'display:none;' : '' ?>">
                        <?php if ($isEdit && !empty($banner['image'])): ?>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Current Image:</label>
                            <div>
                                <img src="<?= upload_url(e($banner['image'])) ?>" class="rounded border" style="max-width:280px;max-height:160px;object-fit:cover;">
                            </div>
                        </div>
                        <?php endif; ?>
                        <label class="form-label fw-bold"><?= $isEdit ? 'Replace Image' : 'Upload Image' ?></label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <div class="form-text">
                            Recommended size: 1920×600px. PNG or JPG. The hero is full-width with a dark overlay — high-contrast images work best.
                        </div>
                    </div>

                    <!-- VIDEO section -->
                    <div id="videoSection" style="<?= ($banner['media_type'] ?? 'image') !== 'video' ? 'display:none;' : '' ?>">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Video URL</label>
                            <input type="url" class="form-control" name="video_url" 
                                   value="<?= e($banner['video_url'] ?? '') ?>" 
                                   placeholder="https://www.youtube.com/watch?v=... or https://cdn.example.com/video.mp4">
                            <div class="form-text">
                                Paste a <strong>YouTube URL</strong> (e.g. https://youtu.be/XXXXXXXXXXX) or a direct <strong>.mp4 file URL</strong>. YouTube videos auto-play muted with no controls.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Upload MP4 File <span class="text-muted fw-normal">(optional — or use URL above)</span></label>
                            <input type="file" class="form-control" name="video_file" accept=".mp4,.webm">
                            <div class="form-text">Max 50MB. MP4 (H.264) recommended for best compatibility.</div>
                        </div>
                        <div class="alert alert-secondary py-2" style="font-size:0.8rem;">
                            <i class="bi bi-info-circle me-1"></i> All video slides include a dark overlay automatically so text remains readable. Videos play muted and looped — no audio.
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Right Column: Settings -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-gear me-2"></i>Settings</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Position <span class="text-danger">*</span></label>
                        <select class="form-select" name="position" required>
                            <option value="hero" <?= ($banner['position'] ?? 'hero') === 'hero' ? 'selected' : '' ?>>
                                🎬 Hero Slider — Full-width scrolling slide
                            </option>
                            <option value="hero_static" <?= ($banner['position'] ?? '') === 'hero_static' ? 'selected' : '' ?>>
                                🖼️ Hero Static — Fixed background (no slide)
                            </option>
                        </select>
                        <div class="form-text">Where this banner appears on the homepage.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" 
                               value="<?= (int) ($banner['sort_order'] ?? 0) ?>" min="0">
                        <div class="form-text">Lower number = appears first. Hero banners with multiple entries become a slider.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Background Color</label>
                        <input type="text" class="form-control" name="bg_color" 
                               value="<?= e($banner['bg_color'] ?? '') ?>"
                               placeholder="e.g. bg-info or #f0f8ff">
                        <div class="form-text">CSS class or hex color for the banner background.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" name="status">
                            <option value="active" <?= ($banner['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($banner['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Position Preview -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-layout-wtf me-2"></i>Position Guide</h5>
                </div>
                <div class="card-body p-3">
                    <div class="border rounded p-3" style="font-size:11px;background:rgba(255,255,255,0.04);">
                        <div class="border rounded p-3 text-center mb-2" style="background:rgba(232,93,4,0.12);min-height:70px;">
                            <strong>🎬 Hero Slider</strong><br>
                            <small class="text-muted">Full-width. Supports image + video slides.<br>Multiple banners = automatic slideshow.</small>
                        </div>
                        <div class="text-muted text-center" style="font-size:10px;">
                            ↑ This is the only position on the new homepage hero section.
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-lg me-2"></i><?= $isEdit ? 'Update Banner' : 'Create Banner' ?>
                </button>
            </div>
        </div>
    </div>
</form>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>

<script>
function toggleMediaType(type) {
    document.getElementById('imageSection').style.display = type === 'image' ? '' : 'none';
    document.getElementById('videoSection').style.display = type === 'video' ? '' : 'none';
}
// Run on page load in case editing a video banner
document.addEventListener('DOMContentLoaded', function() {
    const checked = document.querySelector('input[name="media_type"]:checked');
    if (checked) toggleMediaType(checked.value);
});
</script>
