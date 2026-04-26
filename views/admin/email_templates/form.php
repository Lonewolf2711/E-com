<?php
/**
 * Admin Email Templates — Create / Edit Form
 */
$template = $template ?? [];
$is_edit  = $is_edit  ?? false;
$cats     = ['general' => 'General', 'enquiry' => 'Enquiry', 'notification' => 'Notification'];
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="<?= url('/admin/email-templates') ?>">Email Templates</a>
    </li>
    <li class="breadcrumb-item active">
      <?= $is_edit ? 'Edit Template' : 'New Template' ?>
    </li>
  </ol>
</nav>

<div class="page-heading">
  <h3><?= $is_edit ? 'Edit Template' : 'New Email Template' ?></h3>
</div>

<div class="page-content">
  <div class="row justify-content-center">
    <div class="col-lg-9">

      <div class="card" style="border-top:3px solid #E85D04;">
        <div class="card-body">

          <form action="<?= $is_edit
              ? url('/admin/email-templates/update/' . ($template['id'] ?? 0))
              : url('/admin/email-templates/store') ?>"
                method="POST">
            <?= csrf_field() ?>

            <div class="row g-3">
              <!-- Name -->
              <div class="col-md-8">
                <label class="form-label fw-bold">Template Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control"
                       placeholder="e.g. Acknowledgement, Follow-up, Quotation Ready"
                       value="<?= e($template['name'] ?? '') ?>" required>
              </div>

              <!-- Category -->
              <div class="col-md-4">
                <label class="form-label fw-bold">Category</label>
                <select name="category" class="form-select">
                  <?php foreach ($cats as $key => $label): ?>
                  <option value="<?= $key ?>"
                    <?= ($template['category'] ?? 'general') === $key ? 'selected' : '' ?>>
                    <?= $label ?>
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Subject -->
              <div class="col-12">
                <label class="form-label fw-bold">Email Subject <span class="text-danger">*</span></label>
                <input type="text" name="subject" class="form-control"
                       placeholder="e.g. Your Quotation is Ready – {{enquiry_number}}"
                       value="<?= e($template['subject'] ?? '') ?>" required>
              </div>

              <!-- Body -->
              <div class="col-12">
                <label class="form-label fw-bold">Email Body <span class="text-danger">*</span></label>
                <div class="mb-1 d-flex gap-2 flex-wrap">
                  <?php foreach (['{{customer_name}}', '{{enquiry_number}}', '{{store_name}}'] as $token): ?>
                  <button type="button" class="btn btn-sm btn-outline-warning py-0"
                          onclick="insertToken('<?= $token ?>')">
                    <code style="font-size:0.7rem;"><?= $token ?></code>
                  </button>
                  <?php endforeach; ?>
                  <span class="text-muted small align-self-center">← Click to insert placeholder</span>
                </div>
                <textarea name="body" id="templateBody" class="form-control" rows="16"
                          placeholder="Write your email here. Use {{placeholders}} for dynamic data."
                          required><?= e($template['body'] ?? '') ?></textarea>
              </div>

              <!-- Actions -->
              <div class="col-12 d-flex gap-2 justify-content-end">
                <a href="<?= url('/admin/email-templates') ?>"
                   class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"
                        style="background:#E85D04;border-color:#E85D04;font-weight:700;">
                  <i class="bi bi-check-lg me-1"></i>
                  <?= $is_edit ? 'Update Template' : 'Save Template' ?>
                </button>
              </div>
            </div>
          </form>

        </div>
      </div>

      <!-- Token Reference -->
      <div class="card mt-3">
        <div class="card-header">
          <h6 class="card-title mb-0">
            <i class="bi bi-info-circle me-2" style="color:#E85D04;"></i>Available Placeholders
          </h6>
        </div>
        <div class="card-body">
          <table class="table table-sm mb-0">
            <thead>
              <tr>
                <th>Placeholder</th>
                <th>Replaced With</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><code style="color:#E85D04;">{{customer_name}}</code></td>
                <td class="text-muted small">Customer's full name from the enquiry</td>
              </tr>
              <tr>
                <td><code style="color:#E85D04;">{{enquiry_number}}</code></td>
                <td class="text-muted small">Enquiry reference number (e.g. ENQ-20260419-001)</td>
              </tr>
              <tr>
                <td><code style="color:#E85D04;">{{store_name}}</code></td>
                <td class="text-muted small">Your store name from Settings</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
function insertToken(token) {
  const ta = document.getElementById('templateBody');
  const start = ta.selectionStart;
  const end   = ta.selectionEnd;
  const text  = ta.value;
  ta.value = text.slice(0, start) + token + text.slice(end);
  ta.selectionStart = ta.selectionEnd = start + token.length;
  ta.focus();
}
</script>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
