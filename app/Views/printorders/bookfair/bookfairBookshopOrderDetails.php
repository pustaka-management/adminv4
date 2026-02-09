<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<?php 
$d = $order['details'];  // order header
$books = $order['list']; // books list
?>

<a href="<?= base_url('combobookfair/bookfairbookshopsoldorders'); ?>" class="btn btn-outline-secondary btn-sm float-end mb-3">
    Back
</a>

<div id="content" class="main-content">
    <div class="layout-px-spacing">

        <div class="row g-4">

            <!-- BOOKSHOP DETAILS -->
            <div class="col-md-6">
                <div class="card h-100 radius-12 bg-gradient-purple text-center">
                    <div class="card-body p-24">
                        <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center 
                            bg-lilac-600 text-white mb-16 radius-12">
                            <iconify-icon icon="ri:store-3-fill" class="h5 mb-0"></iconify-icon>
                        </div>
                        <h6 class="mb-16">Bookshop Details</h6>
                        <div class="text-start">
                            <p class="mb-2"><strong>Name:</strong> <?= esc($d['bookshop_name']) ?></p>
                            <p class="mb-2"><strong>Contact:</strong> <?= esc($d['contact_person_name']) ?></p>
                            <p class="mb-2"><strong>Mobile:</strong> <?= esc($d['mobile']) ?></p>
                            <p class="mb-2"><strong>Transport:</strong> <?= esc($d['preferred_transport_name']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ORDER DETAILS -->
            <div class="col-md-6">
                <div class="card h-100 radius-12 bg-gradient-success text-center">
                    <div class="card-body p-24">
                        <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center 
                            bg-success-600 text-white mb-16 radius-12">
                            <iconify-icon icon="ri:truck-fill" class="h5 mb-0"></iconify-icon>
                        </div>
                        <h6 class="mb-16">Order Details</h6>
                        <div class="text-start">
                            <p><strong>Order ID:</strong> <?= esc($d['order_id']) ?></p>
                            <p><strong>Combo Pack:</strong> <?= esc($d['pack_name'] ?? '-') ?></p>
                            <p><strong>Sending Date:</strong> 
                                <?= !empty($d['sending_date']) ? date('d-m-Y', strtotime($d['sending_date'])) : '-' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <br>

        <!-- BOOK LIST -->
        <div class="card shadow-sm mt-3">
            <div class="card-body">
                <h6 class="text-center mb-3">Book List</h6>
                <table class="zero-config table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Book ID</th>
                            <th>Book Name</th>
                            <th>Author</th>
                            <th>Language</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Sale Qty</th>
                            <th>Discount</th>
                            <th>Total Amount</th>
                            <th>Sending Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1; 
                        $grand = 0;
                        ?>
                        <?php foreach($books as $row): ?>
                            <?php $grand += $row['total_amount']; ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= esc($row['book_id']) ?></td>
                                <td><?= esc($row['book_title']) ?></td>
                                <td><?= esc($row['author_name']) ?></td>
                                <td><?= esc($row['language_name']) ?></td>
                                <td><?= esc($row['send_qty']) ?></td>
                                <td><?= number_format($row['book_price'],2) ?></td>
                                <td><?= esc($row['sale_qty']) ?></td>
                               <td><?= esc($row['discount'] ?? 0) ?> %</td>
                                <td><?= number_format($row['total_amount'],2) ?></td>
                                <td><?= !empty($row['sending_date']) ? date('d-m-Y', strtotime($row['sending_date'])) : '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="fw-bold">
                            <td colspan="9" class="text-end">Grand Total</td>
                            <td colspan="2"><?= number_format($grand,2) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection(); ?>
