<?php
/**
 * Admin Enquiries List
 */
$enquiries = $enquiries ?? ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
$filters   = $filters   ?? [];
?>
<?php require_once VIEW_PATH . '/admin/partials/header.php'; ?>

<div class="page-heading">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h3>Enquiries</h3>
      <p class="text-subtitle text-muted">Customer spare parts enquiries awaiting quotation</p>
    </div>
    <div class="d-flex gap-2">
      <a href="<?= url('/admin/enquiries?status=new') ?>"
         class="btn btn-sm btn-outline-warning">
        <i class="bi bi-exclamation-circle me-1"></i>New Only
      </a>
      <a href="<?= url('/admin/enquiries') ?>"
         class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
      </a>
    </div>
  </div>
</div>

<div class="page-content">

  <!-- Status Summary Pills -->
  <div class="d-flex gap-2 mb-4 flex-wrap">
    <?php
    $statusCounts = $status_counts ?? [];
    $statuses = [
      'all'           => ['label' => 'All',          'color' => 'secondary'],
      'new'           => ['label' => 'New',           'color' => 'primary'],
      'acknowledged'  => ['label' => 'Acknowledged',  'color' => 'warning'],
      'quoted'        => ['label' => 'Quoted',         'color' => 'success'],
      'closed'        => ['label' => 'Closed',         'color' => 'light'],
    ];
    foreach ($statuses as $key => $info):
      $count = $key === 'all' ? ($enquiries['total'] ?? 0) : ($statusCounts[$key] ?? 0);
      $isActive = ($filters['status'] ?? 'all') === $key;
    ?>
    <a href="<?= url('/admin/enquiries' . ($key !== 'all' ? '?status=' . $key : '')) ?>"
       class="btn btn-sm btn-<?= $isActive ? '' : 'outline-' ?><?= $info['color'] ?>">
      <?= $info['label'] ?>
      <span class="badge bg-dark ms-1"><?= $count ?></span>
    </a>
    <?php endforeach; ?>
  </div>

  <!-- Filters -->
  <div class="card mb-3">
    <div class="card-body py-3">
      <form method="GET" action="<?= url('/admin/enquiries') ?>"
            class="row g-3 align-items-end">
        <div class="col-md-4">
          <input type="text" class="form-control" name="q"
                 value="<?= e($filters['search'] ?? '') ?>"
                 placeholder="Search by name, phone, or ENQ number...">
        </div>
        <div class="col-md-2">
          <input type="date" class="form-control" name="date_from"
                 value="<?= e($filters['date_from'] ?? '') ?>">
        </div>
        <div class="col-md-2">
          <input type="date" class="form-control" name="date_to"
                 value="<?= e($filters['date_to'] ?? '') ?>">
        </div>
        <div class="col-md-4 d-flex gap-2">
          <button type="submit" class="btn btn-outline-primary">
            <i class="bi bi-search me-1"></i>Search
          </button>
          <a href="<?= url('/admin/enquiries') ?>"
             class="btn btn-outline-secondary">Clear</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Enquiries Table -->
  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr class="border-bottom">
              <th class="ps-4">Enquiry #</th>
              <th>Date</th>
              <th>Customer</th>
              <th>Phone</th>
              <th>Parts</th>
              <th>Status</th>
              <th class="pe-4">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach (($enquiries['data'] ?? []) as $enq): ?>
            <?php
              $statusColors = [
                'new'          => 'primary',
                'acknowledged' => 'warning',
                'quoted'       => 'success',
                'closed'       => 'secondary',
              ];
              $sc = $statusColors[$enq['status'] ?? 'new'] ?? 'secondary';
            ?>
            <tr onclick="window.location='<?= url('/admin/enquiries/' . $enq['id']) ?>'"
                style="cursor:pointer;"
                class="<?= ($enq['status'] ?? '') === 'new' ? 'table-active' : '' ?>">
              <td class="ps-4">
                <code style="color:#E85D04;font-size:0.85rem;">
                  <?= e($enq['enquiry_number'] ?? 'ENQ-' . $enq['id']) ?>
                </code>
              </td>
              <td class="text-muted small"><?= formatDate($enq['created_at'] ?? '') ?></td>
              <td>
                <p class="fw-bold mb-0"><?= e($enq['customer_name'] ?? '—') ?></p>
                <small class="text-muted"><?= e($enq['customer_company'] ?? '') ?></small>
              </td>
              <td class="text-muted small"><?= e($enq['customer_phone'] ?? '—') ?></td>
              <td>
                <span class="badge bg-dark">
                  <?= (int)($enq['parts_count'] ?? 0) ?> parts
                </span>
              </td>
              <td>
                <span class="badge bg-<?= $sc ?>">
                  <?= ucfirst($enq['status'] ?? 'new') ?>
                </span>
              </td>
              <td class="pe-4">
                <a href="<?= url('/admin/enquiries/' . $enq['id']) ?>"
                   class="btn btn-sm btn-outline-primary me-1"
                   onclick="event.stopPropagation()">
                  <i class="bi bi-eye"></i> View
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($enquiries['data'])): ?>
            <tr>
              <td colspan="7" class="text-center text-muted py-5">
                <i class="bi bi-clipboard-x fs-2 d-block mb-2"></i>
                No enquiries found.
              </td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Pagination -->
  <?php if (($enquiries['pages'] ?? 0) > 1): ?>
  <nav class="mt-4">
    <ul class="pagination justify-content-center">
      <?php for ($i = 1; $i <= $enquiries['pages']; $i++): ?>
      <li class="page-item <?= $i === $enquiries['current_page'] ? 'active' : '' ?>">
        <a class="page-link" href="?page=<?= $i ?><?= !empty($filters['status']) ? '&status=' . e($filters['status']) : '' ?>">
          <?= $i ?>
        </a>
      </li>
      <?php endfor; ?>
    </ul>
  </nav>
  <?php endif; ?>

</div>

<?php require_once VIEW_PATH . '/admin/partials/footer.php'; ?>
