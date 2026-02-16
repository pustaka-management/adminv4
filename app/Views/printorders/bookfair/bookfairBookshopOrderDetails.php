<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<?php 
$d = $order['details'];
$books = $order['list'];
?>

<!-- Gradient Header -->
<div class="trail-bg h-100 text-center d-flex flex-column p-16 radius-8">

    <div class="d-flex justify-content-between align-items-center">

        <div class="d-flex align-items-center">
            <a href="<?= base_url('combobookfair/bookfairbookshopsoldorders'); ?>"
               class="me-3 opacity-75">
                <i class="bi bi-arrow-left fs-5 text-white"></i>
            </a>
            <h6 class="fw-semibold mb-0 text-white">Order Details</h6>
        </div>

        <div class="d-flex align-items-center gap-3">

            <span class="badge px-4 py-2 rounded-3"
                  style="background: rgba(255,255,255,0.25);">
                <i class="bi bi-receipt me-2"></i>
                Order #<?= esc($d['order_id']) ?>
            </span>

            <a href="<?= base_url('combobookfair/exportBookshopOrderExcel/'.$order['details']['order_id']) ?>"
                class="btn btn-sm rounded-3 px-3 py-2 shadow-sm" style="background: rgba(255,255,255,0.95); color:#4158D0; font-weight:500;">
                Export Excel
                </a>


        </div>
    </div>
</div><br>


<!-- Info Cards -->
<div class="row g-4 mb-4">

    <!-- Bookshop Info -->
    <div class="col-md-6">
        <div class="card border-0 rounded-4 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h6 class="fw-semibold mb-0">
                    <i class="bi bi-shop me-2 text-primary"></i>
                    Bookshop Details
                </h6>
            </div>
            <div class="card-body px-4 pt-3 pb-4">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted">Name</small>
                        <div class="fw-medium"><?= esc($d['bookshop_name']) ?></div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted">Contact</small>
                        <div><?= esc($d['contact_person_name']) ?></div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted">Mobile</small>
                        <div><?= esc($d['mobile']) ?></div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted">Transport</small>
                        <div><?= esc($d['preferred_transport_name'] ?? '-') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Info -->
    <div class="col-md-6">
        <div class="card border-0 rounded-4 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h6 class="fw-semibold mb-0">
                    <i class="bi bi-box-seam me-2 text-success"></i>
                    Order Information
                </h6>
            </div>
            <div class="card-body px-4 pt-3 pb-4">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted">Order ID</small>
                        <div class="fw-bold text-primary">
                            #<?= esc($d['order_id']) ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted">Combo Pack</small>
                        <div><?= esc($d['pack_name'] ?? '-') ?></div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted">Sending Date</small>
                        <div>
                            <?= !empty($d['sending_date']) 
                                ? date('d-m-Y', strtotime($d['sending_date'])) 
                                : '-' ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted">Status</small>
                        <div>
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-3">
                                <i class="bi bi-check-circle me-1"></i>Shipped
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<!-- Books Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">

    <div class="card-header bg-transparent border-0 pt-4 px-4">
        <h6 class="fw-semibold mb-0">
            <i class="bi bi-journal-bookmark-fill me-2 text-danger"></i>
            Books in this Order
        </h6>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Book ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Language</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Price</th>
                        <th class="text-center">Sale Qty</th>
                        <th class="text-center">Discount</th>
                        <th class="text-end">Total</th>
                        <th>Sending Date</th>
                    </tr>
                </thead>

                <tbody>
                <?php 
                $i = 1; 
                $grand = 0; 
                ?>

                <?php foreach($books as $row): 
                    $grand += $row['total_amount'];
                ?>
                    <tr>
                        <td><?= sprintf('%02d', $i++) ?></td>
                        <td class="fw-medium text-primary"><?= esc($row['book_id']) ?></td>
                        <td><?= esc($row['book_title']) ?></td>
                        <td><?= esc($row['author_name']) ?></td>
                        <td>
                            <span class="badge bg-light text-dark rounded-3">
                                <?= esc($row['language_name']) ?>
                            </span>
                        </td>
                        <td class="text-center fw-bold"><?= esc($row['send_qty']) ?></td>
                        <td class="text-end">₹ <?= number_format($row['book_price'],2) ?></td>
                        <td class="text-center"><?= esc($row['sale_qty']) ?></td>
                        <td class="text-center"><?= esc($row['discount'] ?? 0) ?>%</td>
                        <td class="text-end fw-bold">₹ <?= number_format($row['total_amount'],2) ?></td>
                        <td>
                            <?= !empty($row['sending_date']) 
                                ? date('d-m-Y', strtotime($row['sending_date'])) 
                                : '-' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <tr class="border-top border-2">
                    <td colspan="9" class="text-end fw-semibold">
                        Grand Total
                    </td>
                    <td colspan="2" class="text-end fw-bold fs-6">
                        ₹ <?= number_format($grand,2) ?>
                    </td>
                </tr>

                </tbody>
            </table>

        </div>
    </div>
</div>

<?= $this->endSection(); ?>
