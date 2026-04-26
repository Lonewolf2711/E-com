<?php
/**
 * Admin Contact Message — Detail View
 */
$message = $message ?? [];
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="<?= url('/admin/contacts') ?>">Contact Messages</a>
    </li>
    <li class="breadcrumb-item active">
      Message from <?= e($message['name'] ?? '') ?>
    </li>
  </ol>
</nav>

<div class="page-heading">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h3>Message from <?= e($message['name'] ?? '') ?></h3>
      <p class="text-muted small mb-0">Received: <?= formatDate($message['created_at'] ?? '') ?></p>
    </div>
    <div class="d-flex gap-2">
      <!-- Toggle Read -->
      <form action="<?= url('/admin/contacts/toggle-read/' . ($message['id'] ?? 0)) ?>" method="POST">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-sm btn-outline-secondary">
          <?php if ($message['is_read']): ?>
            <i class="bi bi-envelope me-1"></i>Mark Unread
          <?php else: ?>
            <i class="bi bi-envelope-check me-1"></i>Mark Read
          <?php endif; ?>
        </button>
      </form>
      <!-- Delete -->
      <form action="<?= url('/admin/contacts/delete/' . ($message['id'] ?? 0)) ?>"
            method="POST" onsubmit="return confirm('Delete this message?')">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-sm btn-outline-danger">
          <i class="bi bi-trash me-1"></i>Delete
        </button>
      </form>
      <a href="<?= url('/admin/contacts') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
      </a>
    </div>
  </div>
</div>

<div class="page-content">
  <div class="row g-4">

    <!-- Message Body -->
    <div class="col-lg-8">
      <div class="card" style="border-top: 3px solid #E85D04;">
        <div class="card-header d-flex align-items-center gap-2">
          <i class="bi bi-envelope-open" style="color:#E85D04;"></i>
          <h5 class="card-title mb-0">
            <?= e($message['subject'] ?: '(No Subject)') ?>
          </h5>
        </div>
        <div class="card-body">
          <div class="p-3 rounded" style="background:rgba(255,255,255,0.04);
               border-left:3px solid #E85D04;font-size:0.95rem;line-height:1.7;white-space:pre-wrap;">
            <?= nl2br(e($message['message'] ?? '')) ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Sender Info + Quick Reply -->
    <div class="col-lg-4">
      <div class="card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
          <i class="bi bi-person-circle" style="color:#E85D04;"></i>
          <h5 class="card-title mb-0">Sender Details</h5>
        </div>
        <div class="card-body">
          <dl class="row mb-0 small">
            <dt class="col-4 text-muted">Name</dt>
            <dd class="col-8 fw-bold"><?= e($message['name'] ?? '—') ?></dd>

            <dt class="col-4 text-muted">Email</dt>
            <dd class="col-8">
              <a href="mailto:<?= e($message['email'] ?? '') ?>" class="text-warning">
                <?= e($message['email'] ?? '—') ?>
              </a>
            </dd>

            <dt class="col-4 text-muted">Subject</dt>
            <dd class="col-8"><?= e($message['subject'] ?: '—') ?></dd>

            <dt class="col-4 text-muted">Status</dt>
            <dd class="col-8">
              <span class="badge <?= $message['is_read'] ? 'bg-secondary' : '' ?>"
                    style="<?= !$message['is_read'] ? 'background:#E85D04;' : '' ?>">
                <?= $message['is_read'] ? 'Read' : 'Unread' ?>
              </span>
            </dd>

            <dt class="col-4 text-muted">Received</dt>
            <dd class="col-8"><?= formatDate($message['created_at'] ?? '') ?></dd>
          </dl>
        </div>
      </div>

      <!-- Quick Reply Button -->
      <div class="card">
        <div class="card-header">
          <h6 class="card-title mb-0">Quick Reply</h6>
        </div>
        <div class="card-body d-flex flex-column gap-2">
          <a href="mailto:<?= e($message['email'] ?? '') ?>?subject=Re: <?= urlencode($message['subject'] ?? 'Your enquiry') ?>"
             class="btn btn-sm w-100"
             style="background:#E85D04;color:#fff;font-weight:700;">
            <i class="bi bi-reply me-2"></i>Reply via Email
          </a>
        </div>
      </div>
    </div>

  </div>
</div>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
