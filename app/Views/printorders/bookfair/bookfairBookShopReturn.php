<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<a href="<?= base_url('prospectivemanagement/bookfairbookshopshippedorders') ?>"
            class="btn btn-outline-secondary btn-sm mb-3">
                Back
            </a>

<?php
$orderData = $order;
$booksList = $books;
?>

<!-- Gradient Header -->
<div class="trail-bg h-100 text-center d-flex flex-column justify-content-between align-items-center p-4 radius-8 mb-4">
    <div class="d-flex justify-content-between align-items-center w-100">
        <div class="text-start">
            <h4 class="fw-semibold mb-1 text-white"> Process Return</h4>
            <p class="text-white opacity-75 mb-0 small">Order #<?= esc($orderData['order_id']) ?></p>
        </div>
        

    </div>
</div>

<form method="post" action="<?= base_url('combobookfair/saveReturn') ?>">
    <input type="hidden" name="order_id" value="<?= esc($orderData['order_id']) ?>">

    <!-- Info Cards -->
    <div class="row g-4 mb-4">
        <!-- Bookshop Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-2 me-2" 
                             style="background: linear-gradient(145deg, #667eea20, #764ba220);">
                            <i class="bi bi-shop fs-6" style="color: #5a67d8;"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Bookshop Information</h6>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Bookshop ID</span>
                            <span class="fw-medium"><?= esc($orderData['bookshop_id']) ?></span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Bookshop Name</span>
                            <span class="fw-medium"><?= esc($orderData['bookshop_name']) ?></span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Pack Name</span>
                            <span class="badge bg-light text-dark px-3 py-2 rounded-3 fw-normal">
                                <i class="bi bi-box me-1" style="color: #667eea;"></i>
                                <?= esc($orderData['pack_name']) ?>
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Bookfair</span>
                            <span class="fw-medium"><?= esc($orderData['book_fair_name']) ?></span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted small d-block">Create Date</span>
                            <span class="fw-medium"><?= date('d M Y', strtotime($orderData['create_date'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-2 me-2" 
                             style="background: linear-gradient(145deg, #49a09d20, #5f2c8220);">
                            <i class="bi bi-box-seam fs-6" style="color: #49a09d;"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Order Information</h6>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Order ID</span>
                            <span class="fw-bold" style="color: #4158D0;">#<?= esc($orderData['order_id']) ?></span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Sending Date</span>
                            <span class="fw-medium">
                                <i class="bi bi-calendar-check me-1" style="color: #49a09d;"></i>
                                <?= !empty($orderData['sending_date']) ? date('d M Y', strtotime($orderData['sending_date'])) : '-' ?>
                            </span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted small d-block">Remarks</span>
                            <div class="bg-light p-3 rounded-3 small">
                                <?= esc($orderData['remark']) ?: 'No remarks' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Return Table Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle p-2 me-2" 
                         style="background: linear-gradient(145deg, #f093fb20, #f5576c20);">
                        <i class="bi bi-arrow-return-left fs-6" style="color: #f5576c;"></i>
                    </div>
                    <h6 class="fw-semibold mb-0"> Return Book List</h6>
                </div>
                <span class="badge px-4 py-2 rounded-3" 
                      style="background: linear-gradient(145deg, #667eea15, #764ba215); color: #5a67d8;">
                    <i class="bi bi-box-seam me-1"></i>
                    Total Books: <?= count($booksList) ?>
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3" width="60">#</th>
                            <th class="py-3">Book ID</th>
                            <th class="py-3">Title</th>
                            <th class="py-3">Author</th>
                            <th class="py-3 text-center" width="100">Send Qty</th>
                            <th class="py-3 text-center" width="180">Return Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=1; foreach($booksList as $b): ?>
                        <tr>
                            <td class="px-4">
                                <span class="badge rounded-3 px-3 py-2 fw-normal"
                                      style="background: linear-gradient(145deg, #667eea10, #764ba210); color: #5a67d8;">
                                    <?= sprintf('%02d', $i++) ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-medium" style="color: #4158D0;"><?= esc($b['book_id']) ?></span>
                            </td>
                            <td>
                                <span class="fw-medium"><?= esc($b['book_title']) ?></span>
                            </td>
                            <td>
                                <span class="text-muted">
                                    <i class="bi bi-pencil me-1 small"></i>
                                    <?= esc($b['author_name']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold" style="color: #49a09d;"><?= esc($b['send_qty']) ?></span>
                            </td>
                            <td class="text-center">
                                <input type="number"
                                       name="return_qty[<?= $b['book_id'] ?>]"
                                       class="form-control form-control-sm text-center border-0 bg-light rounded-3"
                                       style="max-width: 120px; margin: 0 auto;"
                                       min="0"
                                       max="<?= esc($b['send_qty']) ?>"
                                       placeholder="Enter qty">
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Discount Section -->
    <div class="row mb-4">
        <div class="col-md-4 ms-auto">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-tag fs-5 me-2" style="color: #f5576c;"></i>
                        <label class="fw-semibold mb-0">Discount</label>
                    </div>
                    <div class="input-group">
                        <input type="number" name="discount" class="form-control border-0 bg-light" value="0" min="0">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-end gap-3 mt-4">
        <a href="<?= base_url('combobookfair/ordersdashboard') ?>"
           class="btn btn-light border-0 px-5 py-3 rounded-3">
            <i class="bi bi-x-circle me-2"></i>Cancel
        </a>
        <button type="submit" class="btn px-5 py-3 rounded-3 text-white border-0 shadow"
                style="background: linear-gradient(145deg, #667eea, #764ba2);">
            <i class="bi bi-check-circle me-2"></i>Process Return
        </button>
    </div>
</form>

<?= $this->endSection(); ?>