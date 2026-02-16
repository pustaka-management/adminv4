<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<!-- Stats Cards -->
<div class="row g-4 mb-4">

    <!-- Total Orders -->
    <div class="col-md-3 col-sm-6">
        <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-1 left-line line-bg-primary position-relative overflow-hidden">
            <div class="d-flex align-items-center">
                <i class="bi bi-box-seam fs-4 me-3"></i>
                <div>
                    <small class="text-uppercase fw-semibold">Total Orders</small>
                    <h5 class="fw-bold mb-0"><?= count($orders) ?></h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Quantity -->
    <div class="col-md-3 col-sm-6">
        <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-2 left-line line-bg-lilac position-relative overflow-hidden">
            <div class="d-flex align-items-center">
                <i class="bi bi-cubes fs-4 me-3"></i>
                <div>
                    <small class="text-uppercase fw-semibold">Total Quantity</small>
                    <h5 class="fw-bold mb-0">
                        <?= array_sum(array_column($orders, 'total_quantity')) ?>
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Titles -->
    <div class="col-md-3 col-sm-6">
        <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-3 left-line line-bg-success position-relative overflow-hidden">
            <div class="d-flex align-items-center">
                <i class="bi bi-journal-bookmark-fill fs-4 me-3"></i>
                <div>
                    <small class="text-uppercase fw-semibold">Total Titles</small>
                    <h5 class="fw-bold mb-0">
                        <?= array_sum(array_column($orders, 'no_of_title')) ?>
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Bookshops -->
    <div class="col-md-3 col-sm-6">
        <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-4 left-line line-bg-warning position-relative overflow-hidden">
            <div class="d-flex align-items-center">
                <i class="bi bi-shop fs-4 me-3"></i>
                <div>
                    <small class="text-uppercase fw-semibold">Bookshops</small>
                    <h5 class="fw-bold mb-0">
                        <?= count(array_unique(array_column($orders, 'bookshop_name'))) ?>
                    </h5>
                </div>
            </div>
        </div>
    </div>
</div><br>

<!-- Data Grid -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 zero-config">
                <thead class="bg-light">
                    <tr>
                        <th width="70">#</th>
                        <th width="120">Order ID</th>
                        <th>Bookshop</th>
                        <th>Pack</th>
                        <th class="text-center">Titles</th>
                        <th class="text-center">Qty</th>
                        <th>Sending Date</th>
                        <th class="text-center" width="180">Actions</th>
                    </tr>
                </thead>
                <tbody>

                <?php $i = 1; ?>
                <?php foreach($orders as $row): ?>
                    <tr>

                        <td>
                            <span class="badge rounded-3 px-3 py-2 fw-normal"
                                  style="background: linear-gradient(145deg, #667eea20, #764ba220); color: #5a67d8;">
                                <?= sprintf('%02d', $i++) ?>
                            </span>
                        </td>

                        <td>
                            <a href="<?= base_url('combobookfair/bookfairbookshoporderdetails/'.$row['order_id']); ?>"
                               class="fw-semibold text-decoration-none"
                               style="color:#4158D0;">
                                #<?= esc($row['order_id']) ?>
                            </a>
                        </td>

                        <td>
                            <i class="bi bi-shop me-1 text-muted"></i>
                            <?= esc($row['bookshop_name']) ?>
                        </td>

                        <td>
                            <i class="bi bi-box-seam me-1 text-muted"></i>
                            <?= esc($row['pack_name'] ?? '—') ?>
                        </td>

                        <td class="text-center fw-bold">
                            <?= esc($row['no_of_title']) ?>
                        </td>

                        <td class="text-center fw-bold">
                            <?= esc($row['total_quantity']) ?>
                        </td>

                        <td>
                            <?= !empty($row['sending_date'])
                                ? date('d M, Y', strtotime($row['sending_date']))
                                : '—' ?>
                        </td>

                        <td class="text-center">
                            <div class="d-flex gap-2 justify-content-center">

                                <a href="<?= base_url('combobookfair/bookfairbookshoporderdetails/'.$row['order_id']); ?>"
                                   class="btn btn-outline-success-600 radius-8 px-20 py-11"
                                       style="padding: 4px 10px; font-size: 12px;"
                                       target="_blank">
                                    View
                                </a>
                            </div>
                        </td>

                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer Summary -->
    <div class="card-footer bg-white border-0 px-4 py-3">
        <div class="d-flex justify-content-between align-items-center">

            <small class="text-muted">
                Showing <?= count($orders) ?> orders
            </small>

            <div class="d-flex gap-2">
                <span class="badge px-4 py-2 rounded-3"
                      style="background:#eef2ff; color:#5a67d8;">
                    Total Qty: <?= array_sum(array_column($orders, 'total_quantity')) ?>
                </span>

                <span class="badge px-4 py-2 rounded-3"
                      style="background:#fde8f0; color:#c0556b;">
                    Total Titles: <?= array_sum(array_column($orders, 'no_of_title')) ?>
                </span>
            </div>

        </div>
    </div>
</div>

<!-- DataTable -->
<script>
$(document).ready(function() {
    $('.zero-config').DataTable({
        paging: true,
        ordering: true,
        info: false,
        searching: false,
        lengthChange: false,
        pageLength: 10,
        dom: 'rtip'
    });
});
</script>

<?= $this->endSection(); ?>
