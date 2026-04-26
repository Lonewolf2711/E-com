<?php
/**
 * Admin Enquiry Detail View
 */
$enquiry      = $enquiry      ?? [];
$cart_items   = $cart_items   ?? [];   // decoded from cart_snapshot JSON
$statusColors = [
  'new'          => 'primary',
  'acknowledged' => 'warning',
  'quoted'       => 'success',
  'closed'       => 'secondary',
];
$currentStatus = $enquiry['status'] ?? 'new';
$sc = $statusColors[$currentStatus] ?? 'secondary';
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="<?= url('/admin/enquiries') ?>">Enquiries</a>
    </li>
    <li class="breadcrumb-item active">
      <?= e($enquiry['enquiry_number'] ?? 'ENQ-' . ($enquiry['id'] ?? '')) ?>
    </li>
  </ol>
</nav>

<div class="page-heading">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h3>
        <code style="color:#E85D04;">
          <?= e($enquiry['enquiry_number'] ?? '') ?>
        </code>
        <span class="badge bg-<?= $sc ?> ms-2" style="font-size:0.7rem;">
          <?= ucfirst($currentStatus) ?>
        </span>
      </h3>
      <p class="text-muted small mb-0">
        Received: <?= formatDate($enquiry['created_at'] ?? '') ?>
      </p>
    </div>
    <div class="d-flex gap-2">
      <!-- Status update form -->
      <form action="<?= url('/admin/enquiries/status/' . ($enquiry['id'] ?? 0)) ?>"
            method="POST" class="d-flex gap-2 align-items-center">
        <?= csrf_field() ?>
        <select class="form-select form-select-sm" name="status" style="width:160px;">
          <?php foreach (['new','acknowledged','quoted','closed'] as $s): ?>
          <option value="<?= $s ?>" <?= $currentStatus === $s ? 'selected' : '' ?>>
            <?= ucfirst($s) ?>
          </option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-sm btn-outline-primary">
          Update Status
        </button>
      </form>
      <a href="<?= url('/admin/enquiries') ?>"
         class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
      </a>
    </div>
  </div>
</div>

<div class="page-content">
  <div class="row g-4">

    <!-- LEFT: Customer Info + Parts -->
    <div class="col-lg-7">

      <!-- Customer Info -->
      <div class="card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
          <i class="bi bi-person-circle" style="color:#E85D04;"></i>
          <h5 class="card-title mb-0">Customer Details</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label text-muted small">FULL NAME</label>
              <p class="fw-bold mb-0"><?= e($enquiry['customer_name'] ?? '—') ?></p>
            </div>
            <div class="col-md-6">
              <label class="form-label text-muted small">COMPANY</label>
              <p class="fw-bold mb-0"><?= e($enquiry['customer_company'] ?? '—') ?></p>
            </div>
            <div class="col-md-6">
              <label class="form-label text-muted small">EMAIL</label>
              <p class="mb-0">
                <a href="mailto:<?= e($enquiry['customer_email'] ?? '') ?>"
                   class="text-warning">
                  <?= e($enquiry['customer_email'] ?? '—') ?>
                </a>
              </p>
            </div>
            <div class="col-md-6">
              <label class="form-label text-muted small">PHONE / WHATSAPP</label>
              <p class="mb-0 d-flex align-items-center gap-2">
                <a href="tel:<?= e($enquiry['customer_phone'] ?? '') ?>"
                   class="text-warning">
                  <?= e($enquiry['customer_phone'] ?? '—') ?>
                </a>
                <?php if (!empty($enquiry['customer_phone'])): ?>
                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $enquiry['customer_phone']) ?>"
                   target="_blank" class="btn btn-sm btn-outline-success py-0">
                  <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
                <?php endif; ?>
              </p>
            </div>
            <?php if (!empty($enquiry['message'])): ?>
            <div class="col-12">
              <label class="form-label text-muted small">CUSTOMER MESSAGE</label>
              <div class="p-3 rounded" style="background:rgba(255,255,255,0.04);
                   border-left:3px solid #E85D04;font-size:0.9rem;">
                <?= nl2br(e($enquiry['message'])) ?>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Enquired Parts Table -->
      <div class="card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
          <i class="bi bi-box-seam" style="color:#E85D04;"></i>
          <h5 class="card-title mb-0">
            Enquired Parts
            <span class="badge bg-dark ms-2"><?= count($cart_items) ?> items</span>
          </h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table mb-0">
              <thead>
                <tr>
                  <th class="ps-3">Part Name</th>
                  <th>Part Code</th>
                  <th>Qty</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($cart_items as $item): ?>
                <tr>
                  <td class="ps-3"><?= e($item['product_name'] ?? '—') ?></td>
                  <td>
                    <code style="color:#E85D04;font-size:0.8rem;">
                      <?= e($item['product_code'] ?? $item['sku'] ?? '—') ?>
                    </code>
                  </td>
                  <td><?= (int)($item['quantity'] ?? 1) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($cart_items)): ?>
                <tr>
                  <td colspan="3" class="text-center text-muted py-3">
                    No items in this enquiry.
                  </td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Status Timeline -->
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">Enquiry Progress</h5>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center position-relative">
            <div style="position:absolute;top:16px;left:10%;right:10%;
                        height:2px;background:rgba(255,255,255,0.1);z-index:0;"></div>
            <?php
            $steps = ['new' => 'New', 'acknowledged' => 'Acknowledged',
                      'quoted' => 'Quoted', 'closed' => 'Closed'];
            $stepOrder = array_keys($steps);
            $currentIdx = array_search($currentStatus, $stepOrder);
            foreach ($steps as $key => $label):
              $idx = array_search($key, $stepOrder);
              $done = $idx < $currentIdx;
              $active = $idx === $currentIdx;
            ?>
            <div class="text-center" style="z-index:1;flex:1;">
              <div class="rounded-circle mx-auto mb-1 d-flex align-items-center
                           justify-content-center"
                   style="width:32px;height:32px;
                          background:<?= $active ? '#E85D04' : ($done ? '#198754' : 'rgba(255,255,255,0.1)') ?>;
                          font-size:0.7rem;font-weight:700;color:#fff;">
                <?= $done ? '✓' : ($active ? '●' : ($idx + 1)) ?>
              </div>
              <small class="text-muted" style="font-size:0.7rem;">
                <?= $label ?>
              </small>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

    </div>

    <!-- RIGHT: Template Picker + AI Quote Generator -->
    <div class="col-lg-5">

      <!-- Template Picker Card -->
      <?php
        $emailTemplates = (new EmailTemplate())->getList('enquiry');
      ?>
      <?php if (!empty($emailTemplates)): ?>
      <div class="card mb-4" style="border-top:3px solid #6f42c1;">
        <div class="card-header d-flex align-items-center gap-2">
          <i class="bi bi-file-earmark-text" style="color:#6f42c1;"></i>
          <h5 class="card-title mb-0">Load from Template</h5>
          <a href="<?= url('/admin/email-templates') ?>" class="ms-auto btn btn-sm btn-outline-secondary py-0"
             style="font-size:0.7rem;">Manage</a>
        </div>
        <div class="card-body py-3">
          <div class="d-flex gap-2">
            <select class="form-select form-select-sm" id="templateSelect">
              <option value="">— Choose a template —</option>
              <?php foreach ($emailTemplates as $tpl): ?>
              <option value="<?= $tpl['id'] ?>"><?= e($tpl['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="button" class="btn btn-sm btn-outline-primary text-nowrap"
                    onclick="loadTemplate()">
              <i class="bi bi-arrow-down-circle me-1"></i>Load
            </button>
          </div>
          <p class="text-muted small mt-2 mb-0">
            Placeholders like <code style="color:#E85D04;">{{customer_name}}</code> are filled in automatically.
          </p>
        </div>
      </div>
      <?php endif; ?>

      <!-- AI Generator Card -->
      <div class="card" style="border-top:3px solid #E85D04;">
        <div class="card-header d-flex align-items-center gap-2">
          <i class="bi bi-stars" style="color:#E85D04;"></i>
          <h5 class="card-title mb-0">Compose / Generate Email</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label fw-bold small">Tone (for AI generation)</label>
            <select class="form-select form-select-sm" id="aiTone">
              <option value="formal">Formal & Professional</option>
              <option value="friendly">Friendly & Helpful</option>
              <option value="followup">Follow-up / Reminder</option>
              <option value="urgent">Urgent / Time-sensitive</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold small">Additional Notes</label>
            <textarea class="form-control form-control-sm" id="aiNotes" rows="3"
                      placeholder="e.g. Price valid 7 days, delivery 3-5 days, minimum order 10 units...">
            </textarea>
          </div>
          <button type="button" class="btn btn-sm w-100 mb-3"
                  style="background:#E85D04;color:#fff;font-weight:700;"
                  id="generateAiBtn" onclick="generateQuoteEmail()">
            <i class="bi bi-stars me-1"></i>
            Generate with Groq AI
          </button>

          <!-- Result area (hidden until generated) -->
          <div id="aiResult" style="display:none;">
            <hr>
            <div class="mb-2">
              <label class="form-label fw-bold small">Email Subject</label>
              <input type="text" class="form-control form-control-sm"
                     id="aiSubject">
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold small">Email Body</label>
              <textarea class="form-control form-control-sm" id="aiBody"
                        rows="10" style="font-size:0.8rem;"></textarea>
            </div>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-sm btn-outline-secondary flex-fill"
                      onclick="copyEmail()">
                <i class="bi bi-clipboard me-1"></i>Copy
              </button>
              <form action="<?= url('/admin/enquiries/send-email/' . ($enquiry['id'] ?? 0)) ?>"
                    method="POST" class="flex-fill">
                <?= csrf_field() ?>
                <input type="hidden" name="enquiry_id" value="<?= (int)($enquiry['id'] ?? 0) ?>">
                <input type="hidden" name="subject" id="sendSubject">
                <input type="hidden" name="body" id="sendBody">
                <button type="submit" class="btn btn-sm btn-success w-100"
                        onclick="prepareEmail()">
                  <i class="bi bi-send me-1"></i>Send to Customer
                </button>
              </form>
            </div>
          </div>

          <!-- Loading state -->
          <div id="aiLoading" style="display:none;" class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-warning mb-2"></div>
            <p class="text-muted small mb-0">Generating with Groq AI...</p>
          </div>
        </div>
      </div>

      <!-- Quick Contact Card -->
      <div class="card mt-4">
        <div class="card-header">
          <h6 class="card-title mb-0">Quick Contact</h6>
        </div>
        <div class="card-body py-3">
          <div class="d-flex flex-column gap-2">
            <a href="mailto:<?= e($enquiry['customer_email'] ?? '') ?>"
               class="btn btn-sm btn-outline-secondary">
              <i class="bi bi-envelope me-2"></i>
              Email: <?= e($enquiry['customer_email'] ?? '—') ?>
            </a>
            <?php if (!empty($enquiry['customer_phone'])): ?>
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $enquiry['customer_phone']) ?>"
               target="_blank" class="btn btn-sm btn-outline-success">
              <i class="fab fa-whatsapp me-2"></i>
              WhatsApp: <?= e($enquiry['customer_phone']) ?>
            </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
async function generateQuoteEmail() {
  const btn = document.getElementById('generateAiBtn');
  const loading = document.getElementById('aiLoading');
  const result = document.getElementById('aiResult');
  btn.style.display = 'none';
  loading.style.display = 'block';
  result.style.display = 'none';

  try {
    const resp = await fetch('<?= url('/admin/ai/generate-quote') ?>', {
      method: 'POST',
      headers: {'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'},
      body: JSON.stringify({
        enquiry_id: <?= (int)($enquiry['id'] ?? 0) ?>,
        tone:       document.getElementById('aiTone').value,
        notes:      document.getElementById('aiNotes').value,
        _token:     '<?= Session::get('csrf_token', '') ?>'
      })
    });
    const data = await resp.json();
    if (data.subject && data.body) {
      document.getElementById('aiSubject').value = data.subject;
      document.getElementById('aiBody').value    = data.body;
      result.style.display = 'block';
    } else {
      alert('AI generation failed: ' + (data.error || 'Unknown error'));
      btn.style.display = 'block';
    }
  } catch(e) {
    alert('Request failed. Check Groq API key in Settings.');
    btn.style.display = 'block';
  } finally {
    loading.style.display = 'none';
  }
}

function copyEmail() {
  const subj = document.getElementById('aiSubject').value;
  const body = document.getElementById('aiBody').value;
  navigator.clipboard.writeText('Subject: ' + subj + '\n\n' + body);
  alert('Copied to clipboard!');
}

function prepareEmail() {
  document.getElementById('sendSubject').value = document.getElementById('aiSubject').value;
  document.getElementById('sendBody').value    = document.getElementById('aiBody').value;
}

async function loadTemplate() {
  const sel = document.getElementById('templateSelect');
  const id  = sel.value;
  if (!id) { alert('Please select a template first.'); return; }

  try {
    const url = '<?= url('/admin/email-templates/preview') ?>/' + id
              + '?enquiry_id=<?= (int)($enquiry['id'] ?? 0) ?>';
    const resp = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const data = await resp.json();
    if (data.success) {
      document.getElementById('aiSubject').value = data.subject;
      document.getElementById('aiBody').value    = data.body;
      document.getElementById('aiResult').style.display = 'block';
      sel.value = ''; // reset dropdown
    } else {
      alert('Failed to load template.');
    }
  } catch(e) {
    alert('Error loading template.');
  }
}
</script>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
