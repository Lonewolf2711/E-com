<?php
/**
 * Admin Settings
 */
$grouped = $grouped ?? [];
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
    <h3>Store Settings</h3>
    <p class="text-subtitle text-muted">
      Configure your spare parts store, notifications, and integrations.
    </p>
</div>

<div class="page-content">
    <form action="<?= url('/admin/settings/update') ?>" method="POST">
        <?= csrf_field() ?>

        <?php if (empty($grouped)): ?>
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-gear fs-1 mb-3 d-block"></i>
                <p>No settings found. Add some settings to the database to get started.</p>
            </div>
        </div>
        <?php endif; ?>

        <?php foreach ($grouped as $group => $settings): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title"><?= e(ucwords(str_replace('_', ' ', $group))) ?></h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach ($settings as $s): ?>
                    <div class="col-md-6">
                        <label class="form-label fw-bold"><?= e(ucwords(str_replace('_', ' ', $s['setting_key']))) ?></label>
                        <?php
                        // Detect textarea-worthy values
                        $isLong = strlen($s['setting_value'] ?? '') > 100;
                        ?>
                        <?php if ($isLong): ?>
                            <textarea class="form-control" name="settings[<?= e($s['setting_key']) ?>]" rows="3"><?= e($s['setting_value'] ?? '') ?></textarea>
                        <?php else: ?>
                            <input type="text" class="form-control" name="settings[<?= e($s['setting_key']) ?>]" value="<?= e($s['setting_value'] ?? '') ?>">
                        <?php endif; ?>
                        <?php if (!empty($s['description'])): ?>
                            <div class="form-text"><?= e($s['description']) ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (!empty($grouped)): ?>
        
        <!-- 🟠 SECTION: Notifications 🟠 -->
        <div class="card mb-4">
          <div class="card-header d-flex align-items-center gap-2">
            <i class="bi bi-bell" style="color:#E85D04;"></i>
            <h5 class="card-title mb-0">Enquiry Notifications</h5>
          </div>
          <div class="card-body">
            <p class="text-muted small mb-3">
              These details are used when customers submit an enquiry -
              the admin receives email + WhatsApp notifications.
            </p>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-bold">Admin Notification Email</label>
                <input type="email" class="form-control"
                       name="settings[admin_email]"
                       value="<?= e(get_setting('admin_email', '')) ?>"
                       placeholder="admin@yourcompany.com">
                <div class="form-text">
                  New enquiry notifications are sent to this address.
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">Admin WhatsApp Number</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-whatsapp text-success"></i></span>
                  <input type="text" class="form-control"
                         name="settings[admin_whatsapp]"
                         value="<?= e(get_setting('admin_whatsapp', '')) ?>"
                         placeholder="919876543210">
                </div>
                <div class="form-text">
                  Include country code, no spaces or + sign. e.g. 919876543210
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">Contact Phone (shown to customers)</label>
                <input type="text" class="form-control"
                       name="settings[contact_phone]"
                       value="<?= e(get_setting('contact_phone', get_setting('general_phone', ''))) ?>"
                       placeholder="+91 98765 43210">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">Contact WhatsApp (for customers)</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-whatsapp text-success"></i></span>
                  <input type="text" class="form-control"
                         name="settings[contact_whatsapp]"
                         value="<?= e(get_setting('contact_whatsapp', '')) ?>"
                         placeholder="919876543210">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 🟠 SECTION: AI Integration 🟠 -->
        <div class="card mb-4">
          <div class="card-header d-flex align-items-center gap-2">
            <i class="bi bi-stars" style="color:#E85D04;"></i>
            <h5 class="card-title mb-0">AI Quotation Generator (Groq)</h5>
          </div>
          <div class="card-body">
            <p class="text-muted small mb-3">
              Groq AI generates professional quotation email drafts from enquiry data.
              Get a free API key at <a href="https://console.groq.com" target="_blank"
              class="text-warning">console.groq.com</a>.
            </p>
            <div class="row g-3">
              <div class="col-md-8">
                <label class="form-label fw-bold">Groq API Key</label>
                <div class="input-group">
                  <input type="password" class="form-control font-monospace"
                         id="groqApiKey"
                         name="settings[groq_api_key]"
                         value="<?= e(get_setting('groq_api_key', '')) ?>"
                         placeholder="gsk_...">
                  <button class="btn btn-outline-secondary" type="button"
                          onclick="const inp = document.getElementById('groqApiKey'); inp.type = inp.type === 'password' ? 'text' : 'password';">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 🟠 SECTION: SMTP Email Config 🟠 -->
        <div class="card mb-4">
          <div class="card-header d-flex align-items-center gap-2">
            <i class="bi bi-envelope-at" style="color:#E85D04;"></i>
            <h5 class="card-title mb-0">SMTP Email Configuration</h5>
          </div>
          <div class="card-body">
            <p class="text-muted small mb-3">
              Configure SMTP so emails (enquiry notifications, quotes) are delivered reliably.
              On localhost, use <strong>Gmail</strong> with an
              <a href="https://myaccount.google.com/apppasswords" target="_blank" class="text-warning">App Password</a>.
              Leave blank to use PHP <code>mail()</code> (usually broken on localhost).
            </p>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-bold">SMTP Host</label>
                <input type="text" class="form-control"
                       name="settings[smtp_host]"
                       value="<?= e(get_setting('smtp_host', '')) ?>"
                       placeholder="smtp.gmail.com">
                <div class="form-text">Use <code>smtp.gmail.com</code> for Gmail, <code>smtp.office365.com</code> for Outlook.</div>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">SMTP Port</label>
                <input type="number" class="form-control"
                       name="settings[smtp_port]"
                       value="<?= e(get_setting('smtp_port', '587')) ?>"
                       placeholder="587">
                <div class="form-text">587 for TLS, 465 for SSL.</div>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Encryption</label>
                <select class="form-select" name="settings[smtp_encryption]">
                  <option value="tls"  <?= get_setting('smtp_encryption','tls') === 'tls'  ? 'selected' : '' ?>>TLS (recommended)</option>
                  <option value="ssl"  <?= get_setting('smtp_encryption','tls') === 'ssl'  ? 'selected' : '' ?>>SSL</option>
                  <option value=""     <?= get_setting('smtp_encryption','tls') === ''     ? 'selected' : '' ?>>None</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">SMTP Username</label>
                <input type="email" class="form-control"
                       name="settings[smtp_username]"
                       value="<?= e(get_setting('smtp_username', '')) ?>"
                       placeholder="yourname@gmail.com">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">SMTP Password / App Password</label>
                <div class="input-group">
                  <input type="password" class="form-control" id="smtpPass"
                         name="settings[smtp_password]"
                         value="<?= e(get_setting('smtp_password', '')) ?>"
                         placeholder="xxxx xxxx xxxx xxxx">
                  <button class="btn btn-outline-secondary" type="button"
                          onclick="const i=document.getElementById('smtpPass');i.type=i.type==='password'?'text':'password';">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
                <div class="form-text">
                  For Gmail: enable 2FA → create an <a href="https://myaccount.google.com/apppasswords" target="_blank" class="text-warning">App Password</a> → paste it here.
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">From Email</label>
                <input type="email" class="form-control"
                       name="settings[smtp_from_email]"
                       value="<?= e(get_setting('smtp_from_email', '')) ?>"
                       placeholder="noreply@yourcompany.com">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">From Name</label>
                <input type="text" class="form-control"
                       name="settings[smtp_from_name]"
                       value="<?= e(get_setting('smtp_from_name', '')) ?>"
                       placeholder="Your Company Name">
              </div>
            </div>
          </div>
        </div>

        <div class="text-end mb-4">
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-lg me-2"></i>Save Settings</button>
        </div>
        <?php endif; ?>
    </form>
</div>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
