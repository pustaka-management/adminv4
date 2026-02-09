<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<a href="<?= base_url('combobookfair/bookshoporderbooksstatus'); ?>" 
   class="btn btn-outline-secondary btn-sm float-end mb-3">
     Back
</a>

<div id="content" class="main-content">
    <div class="layout-px-spacing">

        <div class="row g-4">

            <!-- Bookshop Details -->
            <div class="col-md-6">
                <div class="card h-100 radius-12 bg-gradient-purple">
                    <div class="card-body p-24">
                        <h6 class="mb-16">Bookshop Details</h6>

                        <?php if (!empty($order['details'])): ?>
                            <p><strong>Bookshop:</strong> <?= esc($order['details']['bookshop_name'] ?? '-') ?></p>
                            <p><strong>Contact:</strong> <?= esc($order['details']['contact_person_name'] ?? '-') ?></p>
                            <p><strong>Mobile:</strong> <?= esc($order['details']['mobile'] ?? '-') ?></p>
                            <p><strong>Address:</strong> <?= esc($order['details']['ship_address'] ?? '-') ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Order Details -->
            <div class="col-md-6">
                <div class="card h-100 radius-12 bg-gradient-success">
                    <div class="card-body p-24">
                        <h6 class="mb-16">Order Details</h6>

                        <?php if (!empty($order['details'])): ?>
                            <p><strong>Order ID:</strong> <?= esc($order['details']['order_id']) ?></p>
                            <p><strong>Combo Pack:</strong> <?= esc($order['details']['pack_name'] ?? '-') ?></p>
                            <p><strong>Sending Date:</strong>
                                <?= !empty($order['details']['sending_date']) 
                                    ? date('d-m-Y', strtotime($order['details']['sending_date'])) 
                                    : '-' ?>
                            </p>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        </div>

        <br>

        <!-- Book List -->

        <div class="card shadow-sm">
            <div class="card-body">

                <h6 class="text-center mb-3">List of Books</h6>

                <table class="table table-bordered align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>S.No</th>
                            <th>Book ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Language</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
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
                                <td><?= $i++ ?></td>
                                <td><?= esc($book['book_id']) ?></td>
                                <td><?= esc($book['book_title']) ?></td>
                                <td><?= esc($book['author_name']) ?></td>
                                <td><?= esc($book['language_name'] ?? '-') ?></td>
                                <td><?= esc($book['send_qty']) ?></td>
                                <td><?= number_format($book['book_price'],2) ?></td>
                                <td><?= number_format($rowTotal,2) ?></td>
                            </tr>

                        <?php endforeach; ?>

                        <!-- GRAND TOTAL -->

                        <tr class="fw-bold bg-light">
                            <td colspan="7" class="text-end">Grand Total</td>
                            <td><?= number_format($totalAmount,2) ?></td>
                        </tr>

                    <?php else: ?>

                        <tr>
                            <td colspan="8" class="text-center text-danger">
                                No books found
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>
        </div>

    </div>
</div>

<?= $this->endSection(); ?>
