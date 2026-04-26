<?php
/**
 * Admin Contact Messages — Index View
 */
$messages     = $messages     ?? [];
$unread_count = $unread_count ?? 0;
$filters      = $filters      ?? [];
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h3>Contact Messages</h3>
      <p class="text-muted small mb-0">Messages submitted via the website contact form</p>
    </div>
    <?php if ($unread_count > 0): ?>
    <span class="badge fs-6" style="background:#E85D04;">
      <?= $unread_count ?> Unread
    </span>
    <?php endif; ?>
  </div>
</div>

<div class="page-content">

  <!-- Filter Bar -->
  <div class="card mb-4">
    <div class="card-body py-3">
      <form method="GET" action="<?= url('/admin/contacts') ?>" class="row g-2 align-items-end">
        <div class="col-md-6">
          <input type="text" name="q" class="form-control form-control-sm"
                 placeholder="Search by name, email, or subject..."
                 value="<?= e($filters['search'] ?? '') ?>">
        </div>
        <div class="col-md-3">
          <select name="filter" class="form-select form-select-sm">
            <option value="">All Messages</option>
            <option value="unread" <?= ($filters['filter'] ?? '') === 'unread' ? 'selected' : '' ?>>Unread Only</option>
            <option value="read"   <?= ($filters['filter'] ?? '') === 'read'   ? 'selected' : '' ?>>Read Only</option>
          </select>
        </div>
        <div class="col-auto d-flex gap-2">
          <button type="submit" class="btn btn-sm btn-primary">
            <i class="bi bi-search me-1"></i>Search
          </button>
          <a href="<?= url('/admin/contacts') ?>" class="btn btn-sm btn-outline-secondary">Clear</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Messages Table -->
  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table mb-0">
          <thead>
            <tr>
              <th class="ps-3" style="width:40px;"></th>
              <th>Name</th>
              <th>Email</th>
              <th>Subject</th>
              <th>Date</th>
              <th class="text-end pe-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($messages['data'])): ?>
            <tr>
              <td colspan="6" class="text-center py-5">
                <i class="bi bi-envelope-x" style="font-size:2.5rem;color:#555;"></i>
                <p class="text-muted mt-2 mb-0">No contact messages found.</p>
              </td>
            </tr>
            <?php else: ?>
            <?php foreach ($messages['data'] as $msg): ?>
            <tr style="<?= !$msg['is_read'] ? 'border-left: 3px solid #E85D04;' : '' ?>">
              <td class="ps-3">
                <?php if (!$msg['is_read']): ?>
                <span class="badge" style="background:#E85D04;font-size:0.55rem;">NEW</span>
                <?php else: ?>
                <i class="bi bi-check2 text-muted"></i>
                <?php endif; ?>
              </td>
              <td class="<?= !$msg['is_read'] ? 'fw-bold' : '' ?>">
                <?= e($msg['name']) ?>
              </td>
              <td>
                <a href="mailto:<?= e($msg['email']) ?>" class="text-warning small">
                  <?= e($msg['email']) ?>
                </a>
              </td>
              <td class="small text-muted"><?= e($msg['subject'] ?: '(no subject)') ?></td>
              <td class="small text-muted"><?= formatDate($msg['created_at']) ?></td>
              <td class="text-end pe-3">
                <a href="<?= url('/admin/contacts/' . $msg['id']) ?>"
                   class="btn btn-sm btn-outline-primary py-0 px-2">
                  <i class="bi bi-eye me-1"></i>View
                </a>
                <form action="<?= url('/admin/contacts/delete/' . $msg['id']) ?>"
                      method="POST" class="d-inline"
                      onsubmit="return confirm('Delete this message?')">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <?php if (!empty($messages['last_page']) && $messages['last_page'] > 1): ?>
    <div class="card-footer d-flex justify-content-center">
      <nav>
        <ul class="pagination pagination-sm mb-0">
          <?php for ($p = 1; $p <= $messages['last_page']; $p++): ?>
          <li class="page-item <?= $p === $messages['current_page'] ? 'active' : '' ?>">
            <a class="page-link"
               href="<?= url('/admin/contacts') ?>?page=<?= $p ?>&q=<?= urlencode($filters['search'] ?? '') ?>&filter=<?= urlencode($filters['filter'] ?? '') ?>">
              <?= $p ?>
            </a>
          </li>
          <?php endfor; ?>
        </ul>
      </nav>
    </div>
    <?php endif; ?>
  </div>

</div>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
