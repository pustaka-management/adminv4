<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<!-- Stats Cards with Gradients -->
<div class="row g-4 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-1 left-line line-bg-primary position-relative overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 p-3 me-3">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                    <div>
                        <span class="text-opacity-90 small text-uppercase fw-semibold">Total Orders</span>
                        <h5 class="fw-bold mb-0"><?= count($orders) ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-2 left-line line-bg-lilac position-relative overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 p-3 me-3">
                        <i class="bi bi-cubes fs-4"></i>
                    </div>
                    <div>
                        <span class="text-opacity-90 small text-uppercase fw-semibold">Total Quantity</span>
                        <h5 class="fw-bold mb-0"><?= array_sum(array_column($orders, 'total_quantity')) ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-3 left-line line-bg-success position-relative overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 p-3 me-3">
                        <i class="bi bi-journal-bookmark-fill fs-4"></i>
                    </div>
                    <div>
                        <span class="text-opacity-90 small text-uppercase fw-semibold">Total Titles</span>
                        <h5 class="fw-bold mb-0"><?= array_sum(array_column($orders, 'no_of_title')) ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-4 left-line line-bg-warning position-relative overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 p-3 me-3">
                        <i class="bi bi-shop fs-4"></i>
                    </div>
                    <div>
                        <span class="text-opacity-90 small text-uppercase fw-semibold">Bookshops</span>
                        <h5 class="fw-bold mb-0"><?= count(array_unique(array_column($orders, 'bookshop_name'))) ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Data Grid - ZERO CONFIG TABLE -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 zero-config">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3" width="80">#</th>
                        <th class="py-3" width="120">ORDER ID</th>
                        <th class="py-3" width="200">BOOKSHOP</th>
                        <th class="py-3" width="180">PACK</th>
                        <th class="py-3 text-center" width="100">TITLES</th>
                        <th class="py-3 text-center" width="100">QTY</th>
                        <th class="py-3" width="150">SHIPPED DATE</th>
                        <th class="py-3 text-center" width="200">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach($orders as $row): ?>
                    <tr style="<?= $i % 2 == 0 ? 'background-color: #fafbff;' : '' ?>">
                        <td class="px-4">
                            <span class="badge rounded-3 px-3 py-2 fw-normal" 
                                  style="background: linear-gradient(145deg, #667eea20, #764ba220); color: #5a67d8;">
                                <?= sprintf('%02d', $i++) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= base_url('combobookfair/bookfairshippedorderdetails/'.$row['order_id']); ?>" 
                               class="fw-semibold text-decoration-none" 
                               style="color: #4158D0;">
                                #<?= esc($row['order_id']) ?>
                            </a>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                    <i class="bi bi-shop text-white small"></i>
                                </div>
                                <span class="fw-medium"><?= esc($row['bookshop_name']) ?></span>
                            </div>
                        </td>
                        <td>
                            <span>
                                <i class="bi bi-box-seam me-1" style="color: #667eea;"></i>
                                <?= esc($row['pack_name'] ?? '—') ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold" style="color: #5f2c82;"><?= esc($row['no_of_title']) ?></span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold" style="color: #49a09d;"><?= esc($row['total_quantity']) ?></span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar3 me-2" style="color: #667eea;"></i>
                                <span class="small text-muted">
                                    <?= !empty($row['sending_date'])
                                        ? date('d M, Y', strtotime($row['sending_date']))
                                        : '—' ?>
                                </span>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="<?= base_url('combobookfair/bookfairshippedorderdetails/'.$row['order_id']); ?>" 
                                   class="btn btn-outline-success-600 radius-8 px-20 py-11"
                                       style="padding: 4px 10px; font-size: 12px;">View
                                </a>
                                <a href="<?= base_url('combobookfair/return/'.$row['order_id']); ?>" 
                                    class="btn btn-outline-lilac-600 radius-8 px-20 py-11"
                                       style="padding: 4px 10px; font-size: 12px;"
                                       target="_blank">Return
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 px-4 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <span class="small text-muted">
                Showing <span class="fw-semibold" style="color: #5a67d8;">1-<?= count($orders) ?></span> of 
                <span class="fw-semibold" style="color: #5a67d8;"><?= count($orders) ?></span> orders
            </span>
            <div class="d-flex gap-2">
                <span class="badge px-4 py-2 rounded-3" 
                      style="background: linear-gradient(145deg, #667eea15, #764ba215); color: #5a67d8;">
                    <i class="bi bi-box-seam me-1"></i>
                    Total Qty: <?= array_sum(array_column($orders, 'total_quantity')) ?>
                </span>
                <span class="badge px-4 py-2 rounded-3"
                      style="background: linear-gradient(145deg, #f093fb15, #f5576c15); color: #c0556b;">
                    <i class="bi bi-journal-bookmark-fill me-1"></i>
                    Total Titles: <?= array_sum(array_column($orders, 'no_of_title')) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Zero Config Table Script -->
<script>
    $(document).ready(function() {
        $('.zero-config').DataTable({
            "paging": true,
            "ordering": true,
            "info": false,
            "searching": false,
            "lengthChange": false,
            "pageLength": 10,
            "language": {
                "paginate": {
                    "previous": "<i class='bi bi-chevron-left'>",
                    "next": "<i class='bi bi-chevron-right'>"
                }
            },
            "dom": 'rtip'
        });
    });
</script>

<?= $this->endSection(); ?>