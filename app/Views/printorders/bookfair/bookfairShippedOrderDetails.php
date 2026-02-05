<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<a href="<?= base_url('paperback/bookshoporderbooksstatus'); ?>" 
   class="btn btn-outline-secondary btn-sm float-end mb-3">
    ← Back
</a>

<div id="content" class="main-content">
    <div class="layout-px-spacing">

        <div class="row g-4">
            <!-- Bookshop Details -->
            <div class="col-md-6">
                <div class="card h-100 radius-12 bg-gradient-purple text-center">
                    <div class="card-body p-24">
                        <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center 
                            bg-lilac-600 text-white mb-16 radius-12">
                            <iconify-icon icon="ri:store-3-fill" class="h5 mb-0"></iconify-icon>
                        </div>
                        <h6 class="mb-16">Bookshop Details</h6>
                        <div class="text-start d-inline-block">
                            <?php if (!empty($order['details'])): ?>
                                <p class="mb-2"><strong>Bookshop:</strong> <?= esc($order['details']['bookshop_name']) ?></p>
                                <p class="mb-2"><strong>Contact Person:</strong> <?= esc($order['details']['contact_person_name']) ?></p>
                                <p class="mb-2"><strong>Mobile No:</strong> <?= esc($order['details']['mobile']) ?></p>
                                <p class="mb-2">
                                    <strong>Transport:</strong> 
                                    <?= esc($order['details']['preferred_transport']) . " - " . esc($order['details']['preferred_transport_name']) ?>
                                </p>
                                <?php if (!empty($order['details']['ship_address'])): ?>
                                    <p class="mb-0"><strong>Address:</strong> <?= esc($order['details']['ship_address']) ?></p>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="text-danger mb-0">⚠ No bookshop details found for this order.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order & Shipping Details -->
            <div class="col-md-6">
                <div class="card h-100 radius-12 bg-gradient-success text-center">
                    <div class="card-body p-24">
                        <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center 
                            bg-success-600 text-white mb-16 radius-12">
                            <iconify-icon icon="ri:truck-fill" class="h5 mb-0"></iconify-icon>
                        </div>
                        <h6 class="mb-16">Order & Shipping Details</h6>
                        <div class="text-start">
                            <?php if (!empty($order['details'])): ?>
                                <p><strong>Order ID:</strong> <?= esc($order['details']['order_id']) ?></p>
                                <p><strong>Combo Pack:</strong> <?= esc($order['details']['pack_name'] ?? '-') ?></p>
                                <p><strong>Sending Date:</strong>
                                    <?= !empty($order['details']['sending_date']) ? date('d-m-Y', strtotime($order['details']['sending_date'])) : '-' ?>
                                </p>
                                <?php if (!empty($order['details']['vendor_po_order_number'])): ?>
                                    <p><strong>Buyer's Order No:</strong> <?= esc($order['details']['vendor_po_order_number']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($order['details']['transport_payment'])): ?>
                                    <p><strong>Transport Payment:</strong> <?= esc($order['details']['transport_payment']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($order['details']['tracking_url'])): ?>
                                    <p>
                                        <a href="<?= esc($order['details']['tracking_url']) ?>" target="_blank">
                                            <?= esc($order['details']['tracking_id']) ?>
                                        </a>
                                    </p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br><br>

        <!-- Book List Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="text-center mb-3">List of Books</h6>
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>S.No</th>
                            <th>Book ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Language</th>
                            <th>Qty</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $i = 1; 
                            $totalAmount = 0;
                        ?>
                        <?php if (!empty($order['list'])): ?>
                            <?php foreach ($order['list'] as $book): ?>
                                <?php $totalAmount += $book['total_amount']; ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= esc($book['book_id']) ?></td>
                                    <td><?= esc($book['book_title']) ?></td>
                                    <td><?= esc($book['author_name']) ?></td>
                                    <td><?= esc($book['language_name']) ?></td>
                                    <td><?= esc($book['send_qty']) ?></td>
                                    <td><?= number_format($book['book_price'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="fw-bold">
                                <td colspan="7" class="text-end">Grand Total</td>
                                <td><?= number_format($totalAmount, 2) ?></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-danger">⚠ No books found for this order.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection(); ?>
