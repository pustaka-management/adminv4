<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

    <?php
    $orderData = $order;
    $booksList = $books;
    ?>
<form method="post" action="<?= base_url('combobookfair/saveReturn') ?>">
    <input type="hidden" name="order_id" value="<?= esc($orderData['order_id']) ?>">

    <div class="container-fluid py-4 px-3">
        <!-- BACK -->
        <div class="text-end mb-3">
            <a href="<?= base_url('combobookfair/bookfairbookshopshippedorders'); ?>"
            class="btn btn-outline-secondary btn-sm">
                Back
            </a>
        </div>

        <!-- ===== TOP CARDS ===== -->
        <div class="row g-4 mb-4">
            <!-- BOOKSHOP -->
            <div class="col-md-6">
                <div class="card h-100 radius-12 bg-gradient-purple shadow-sm">
                    <div class="card-body p-4">
                        <h6 class="mb-3 fw-bold">Bookshop Details</h6>

                        <p><strong>Bookshop ID:</strong> <?= esc($orderData['bookshop_id']) ?></p>
                        <p><strong>Bookshop Name:</strong> <?= esc($orderData['bookshop_name']) ?></p>
                        <p><strong>Pack Name:</strong> <?= esc($orderData['pack_name']) ?></p>
                        <p><strong>Bookfair Name:</strong> <?= esc($orderData['book_fair_name']) ?></p>
                        <p><strong>Create Date:</strong> <?= date('d-m-Y', strtotime($orderData['create_date'])) ?></p>
                    </div>
                </div>
            </div>

            <!-- ORDER -->
            <div class="col-md-6">
                <div class="card h-100 radius-12 bg-gradient-success shadow-sm">
                    <div class="card-body p-4">
                        <h6 class="mb-3 fw-bold">Order Details</h6>

                        <p><strong>Sending Date:</strong>
                            <?= !empty($orderData['sending_date']) ? date('d-m-Y', strtotime($orderData['sending_date'])) : '-' ?>
                        </p>
                        <p><strong>Order ID:</strong> <?= esc($orderData['order_id']) ?></p>
                        <p><strong>Remarks:</strong> <?= esc($orderData['remark']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== RETURN TABLE ===== -->
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <h6 class="text-center fw-bold mb-4">Return Book List</h6>
                    <div class="table-responsive">
                        <table class="zero-config table table-hover align-middle">
                        <thead class="table-light">
                        <tr>
                        <th>#</th>
                        <th>Book ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Send Qty</th>
                        <th width="150">Return Qty</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php $i=1; foreach($booksList as $b): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= esc($b['book_id']) ?></td>
                            <td class="text-start"><?= esc($b['book_title']) ?></td>
                            <td><?= esc($b['author_name']) ?></td>
                            <td><?= esc($b['send_qty']) ?></td>

                            <td>
                                <input type="number"
                                    name="return_qty[<?= $b['book_id'] ?>]"
                                    class="form-control text-center"
                                    min="0"
                                    max="<?= esc($b['send_qty']) ?>">
                            </td>
                        </tr>
                        <?php endforeach ?>
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <br>

            <!-- ===== DISCOUNT ===== -->
            <div class="row mb-4">
                <div class="col-md-3 ms-auto">
                    <label class="fw-semibold mb-1">Discount</label>
                    <input type="number" name="discount" class="form-control" value="0">
                </div>
            </div>

            <!-- ===== BUTTONS ===== -->
            <div class="text-end">
                <a href="<?= base_url('combobookfair/ordersdashboard') ?>"
                class="btn btn-warning-600 radius-8 px-20 py-11">
                    Cancel
                </a>

                <button class="btn btn-success-600 radius-8 px-20 py-11">
                    Return
                </button>
            </div>
    </div>
</form>
<?= $this->endSection(); ?>
