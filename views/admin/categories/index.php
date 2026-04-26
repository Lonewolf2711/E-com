<?php
/**
 * Admin Categories Management
 * ───────────────────────────
 * Tree view with inline create/edit modals
 */
$categories = $categories ?? [];
$flat_categories = $flat_categories ?? [];
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <h3>Categories</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal"><i class="bi bi-plus-circle me-2"></i>Add Category</button>
    </div>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-lg">
                    <thead><tr><th>Category</th><th>Slug</th><th>Parent</th><th>Status</th><th style="width:140px;">Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($flat_categories as $cat): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($cat['image'])): ?>
                                    <img src="<?= upload_url(e($cat['image'])) ?>" class="rounded me-2" style="width:35px;height:35px;object-fit:cover;" alt="">
                                    <?php else: ?>
                                    <div class="rounded bg-light d-flex align-items-center justify-content-center me-2" style="width:35px;height:35px;"><i class="bi bi-folder text-muted"></i></div>
                                    <?php endif; ?>
                                    <span class="fw-bold"><?= e($cat['name']) ?></span>
                                </div>
                            </td>
                            <td class="text-muted"><?= e($cat['slug']) ?></td>
                            <td>
                                <?php
                                $parentName = '-';
                                if ($cat['parent_id']) {
                                    foreach ($flat_categories as $fc) {
                                        if ($fc['id'] == $cat['parent_id']) { $parentName = $fc['name']; break; }
                                    }
                                }
                                echo e($parentName);
                                ?>
                            </td>
                            <td><span class="badge <?= ($cat['status'] ?? 'active') === 'active' ? 'bg-success' : 'bg-secondary' ?>"><?= ucfirst(e($cat['status'] ?? 'active')) ?></span></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                    data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                                    data-id="<?= $cat['id'] ?>"
                                    data-name="<?= e($cat['name']) ?>"
                                    data-parent="<?= $cat['parent_id'] ?? '' ?>"
                                    data-description="<?= e($cat['description'] ?? '') ?>"
                                    data-status="<?= e($cat['status'] ?? 'active') ?>"
                                    title="Edit"><i class="bi bi-pencil"></i></button>
                                <form action="<?= url('/admin/categories/delete/' . $cat['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete \'<?= e($cat['name']) ?>\'?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($flat_categories)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No categories yet. Click "Add Category" to create one.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= url('/admin/categories/store') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Category</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Parent Category</label>
                        <select class="form-select" name="parent_id">
                            <option value="">None (Top Level)</option>
                            <?php foreach ($flat_categories as $fc): ?>
                            <option value="<?= $fc['id'] ?>"><?= e($fc['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Create</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editCategoryForm" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Category</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Parent Category</label>
                        <select class="form-select" name="parent_id" id="edit_parent_id">
                            <option value="">None (Top Level)</option>
                            <?php foreach ($flat_categories as $fc): ?>
                            <option value="<?= $fc['id'] ?>"><?= e($fc['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="edit_status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('editCategoryModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editCategoryForm');
    form.action = '<?= url('/admin/categories/update/') ?>' + btn.dataset.id;
    document.getElementById('edit_name').value = btn.dataset.name;
    document.getElementById('edit_parent_id').value = btn.dataset.parent || '';
    document.getElementById('edit_description').value = btn.dataset.description || '';
    document.getElementById('edit_status').value = btn.dataset.status || 'active';
});
</script>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
