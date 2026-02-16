<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<!-- Header with Gradient -->
<div class="trail-bg h-100 text-center d-flex flex-column p-16 radius-8">
    <div class="d-flex justify-content-between align-items-center">

        <!-- Left Section -->
        <div class="d-flex align-items-center">
            <a href="<?= base_url('combobookfair/bookshoporderbooksstatus'); ?>"
               class="me-3 opacity-75"
               style="transition: opacity 0.2s;">
                <i class="bi bi-arrow-left fs-5 text-white"></i>
            </a>
            <div>
                <h6 class="fw-semibold mb-1 text-white">Order Details</h6>
            </div>
        </div>

        <!-- Right Section -->
        <div class="d-flex align-items-center gap-3">

            <span class="badge bg-opacity-25 px-4 py-2 rounded-3"
                  style="background: rgba(255,255,255,0.2);">
                <i class="bi bi-receipt me-2"></i>
                Order #<?= esc($order['details']['order_id'] ?? 'N/A') ?>
            </span>

            <a href="<?= base_url('combobookfair/exportSingleShippedOrder/'.$order['details']['order_id']) ?>"
               class="btn btn-sm rounded-3 px-3 py-2 shadow-sm"
               style="background: rgba(255,255,255,0.95); color:#4158D0; font-weight:500;">
                <i class="bi bi-download me-2"></i>Export Excel
            </a>

        </div>
    </div>
</div><br>

<!-- Info Cards Row -->
<div class="row g-4 mb-4">

    <!-- Bookshop Card -->
    <div class="col-md-6">
        <div class="card border-0 rounded-4 h-100 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h6 class="fw-semibold mb-0">
                    <i class="bi bi-shop me-2 text-primary"></i>
                    Bookshop Information
                </h6>
            </div>
            <div class="card-body px-4 pt-3 pb-4">
                <?php if (!empty($order['details'])): ?>
                <div class="row g-3">

                    <div class="col-sm-6">
                        <small class="text-muted">Bookshop Name</small>
                        <div class="fw-medium"><?= esc($order['details']['bookshop_name'] ?? '-') ?></div>
                    </div>

                    <div class="col-sm-6">
                        <small class="text-muted">Contact Person</small>
                        <div><?= esc($order['details']['contact_person_name'] ?? '-') ?></div>
                    </div>

                    <div class="col-sm-6">
                        <small class="text-muted">Mobile Number</small>
                        <div>
                            <i class="bi bi-telephone me-1 text-primary"></i>
                            <?= esc($order['details']['mobile'] ?? '-') ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <small class="text-muted">Shipping Address</small>
                        <div class="p-3 bg-light rounded-3 small">
                            <i class="bi bi-geo-alt me-1 text-primary"></i>
                            <?= esc($order['details']['ship_address'] ?? '-') ?>
                        </div>
                    </div>

                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Order Info Card -->
    <div class="col-md-6">
        <div class="card border-0 rounded-4 h-100 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h6 class="fw-semibold mb-0">
                    <i class="bi bi-box-seam me-2 text-success"></i>
                    Order Information
                </h6>
            </div>
            <div class="card-body px-4 pt-3 pb-4">
                <?php if (!empty($order['details'])): ?>
                <div class="row g-3">

                    <div class="col-sm-6">
                        <small class="text-muted">Order ID</small>
                        <div class="fw-bold text-primary">
                            #<?= esc($order['details']['order_id']) ?>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <small class="text-muted">Combo Pack</small>
                        <div><?= esc($order['details']['pack_name'] ?? '-') ?></div>
                    </div>

                    <div class="col-sm-6">
                        <small class="text-muted">Sending Date</small>
                        <div>
                            <i class="bi bi-calendar-check me-1 text-success"></i>
                            <?= !empty($order['details']['sending_date'])
                                ? date('d F, Y', strtotime($order['details']['sending_date']))
                                : '-' ?>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <small class="text-muted">Order Status</small>
                        <div>
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-3">
                                <i class="bi bi-check-circle me-1"></i>Shipped
                            </span>
                        </div>
                    </div>

                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Books Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-transparent border-0 pt-4 px-4">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="fw-semibold mb-0">
                <i class="bi bi-journal-bookmark-fill me-2 text-danger"></i>
                Books in this Order
            </h6>
            <span class="badge bg-light text-dark px-4 py-2 rounded-3">
                Total Items: <?= count($order['list'] ?? []) ?>
            </span>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="70">#</th>
                        <th width="100">Book ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Language</th>
                        <th class="text-center" width="80">Qty</th>
                        <th class="text-end" width="100">Price</th>
                        <th class="text-end" width="120">Total</th>
                    </tr>
                </thead>
                <tbody>

                <?php
                $i = 1;
                $totalAmount = 0;
                ?>

                <?php if (!empty($order['list'])): ?>
                    <?php foreach ($order['list'] as $book): ?>
                        <?php
                        $rowTotal = $book['total_amount'] ?? ($book['send_qty'] * $book['book_price']);
                        $totalAmount += $rowTotal;
                        ?>
                        <tr>
                            <td><?= sprintf('%02d', $i++) ?></td>
                            <td class="fw-medium text-primary"><?= esc($book['book_id']) ?></td>
                            <td><?= esc($book['book_title']) ?></td>
                            <td><?= esc($book['author_name']) ?></td>
                            <td>
                                <span class="badge bg-light text-dark rounded-3">
                                    <?= esc($book['language_name'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="text-center fw-bold"><?= esc($book['send_qty']) ?></td>
                            <td class="text-end">₹ <?= number_format($book['book_price'], 2) ?></td>
                            <td class="text-end fw-bold">₹ <?= number_format($rowTotal, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Grand Total -->
                    <tr class="border-top border-2">
                        <td colspan="7" class="text-end fw-semibold">
                            Grand Total
                        </td>
                        <td class="text-end fw-bold fs-6">
                            ₹ <?= number_format($totalAmount, 2) ?>
                        </td>
                    </tr>

                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            No books found in this order
                        </td>
                    </tr>
                <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
