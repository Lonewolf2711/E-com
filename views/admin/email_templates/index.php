<?php
/**
 * Admin Email Templates — Index View
 */
$templates = $templates ?? [];
$category  = $category  ?? '';
$cats = ['general' => 'General', 'enquiry' => 'Enquiry', 'notification' => 'Notification'];
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h3>Email Templates</h3>
      <p class="text-muted small mb-0">
        Reusable templates for enquiry replies. Use
        <code style="color:#E85D04;">{{customer_name}}</code>,
        <code style="color:#E85D04;">{{enquiry_number}}</code>,
        <code style="color:#E85D04;">{{store_name}}</code> as placeholders.
      </p>
    </div>
    <a href="<?= url('/admin/email-templates/create') ?>"
       class="btn btn-sm" style="background:#E85D04;color:#fff;font-weight:700;">
      <i class="bi bi-plus-lg me-1"></i>New Template
    </a>
  </div>
</div>

<div class="page-content">

  <!-- Category Filter Pills -->
  <div class="mb-3 d-flex gap-2 flex-wrap">
    <a href="<?= url('/admin/email-templates') ?>"
       class="btn btn-sm <?= $category === '' ? 'btn-primary' : 'btn-outline-secondary' ?>">All</a>
    <?php foreach ($cats as $key => $label): ?>
    <a href="<?= url('/admin/email-templates') ?>?category=<?= $key ?>"
       class="btn btn-sm <?= $category === $key ? 'btn-primary' : 'btn-outline-secondary' ?>">
      <?= $label ?>
    </a>
    <?php endforeach; ?>
  </div>

  <div class="row g-3">
    <?php if (empty($templates['data'])): ?>
    <div class="col-12">
      <div class="card">
        <div class="card-body text-center py-5">
          <i class="bi bi-envelope-paper" style="font-size:2.5rem;color:#555;"></i>
          <p class="text-muted mt-2 mb-0">No templates yet. Create your first one!</p>
        </div>
      </div>
    </div>
    <?php else: ?>
    <?php foreach ($templates['data'] as $tpl): ?>
    <div class="col-md-6 col-xl-4">
      <div class="card h-100" style="border-top:3px solid #E85D04;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 class="card-title mb-0 fw-bold"><?= e($tpl['name']) ?></h6>
            <span class="badge bg-secondary" style="font-size:0.6rem;">
              <?= e($cats[$tpl['category']] ?? $tpl['category']) ?>
            </span>
          </div>
          <p class="text-muted small mb-2">
            <i class="bi bi-envelope me-1"></i><?= e($tpl['subject']) ?>
          </p>
          <div class="small" style="color:#888;max-height:60px;overflow:hidden;
               position:relative;">
            <?= nl2br(e(mb_substr($tpl['body'], 0, 120))) ?>…
          </div>
        </div>
        <div class="card-footer d-flex gap-2 py-2">
          <a href="<?= url('/admin/email-templates/edit/' . $tpl['id']) ?>"
             class="btn btn-sm btn-outline-primary flex-fill">
            <i class="bi bi-pencil me-1"></i>Edit
          </a>
          <form action="<?= url('/admin/email-templates/delete/' . $tpl['id']) ?>"
                method="POST" onsubmit="return confirm('Delete this template?')"
                class="flex-fill">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
              <i class="bi bi-trash me-1"></i>Delete
            </button>
          </form>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>

</div>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
