<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Sold Order Details</h6>

        <a href="javascript:history.back()" 
           class="btn btn-outline-secondary btn-sm">
            Back
        </a>
    </div>

    <!-- ================= BOOK CARD ================= -->

    <?php if(!empty($orders)): ?>
    <?php $book = $orders[0]; ?>

    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">

            <h6 class="fw-bold mb-1"><?= esc($book['book_title']) ?></h6>

            <div class="row small">

                <div class="col-md-4">
                    <strong>Book ID:</strong> <?= esc($book['book_id']) ?>
                </div>

                <div class="col-md-4">
                    <strong>Author:</strong> <?= esc($book['author_name']) ?>
                </div>

            </div>

        </div>
    </div>
    <?php endif; ?>

    <!-- ================= TABLE ================= -->

    <div class="card shadow-sm radius-8">
        <div class="card-body p-3">

            <div class="table-responsive">

                <table class="zero-config table table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th width="40">#</th>
                            <th>Order ID</th>
                            <th>Bookshop</th>
                            <th>BookFair</th>
                            <th width="120">Sending Date</th>
                            <th width="80">Send Qty</th>
                            <th width="80">Sold Qty</th>
                            <th width="80">Price</th>
                            <th width="80">Discount</th>
                            <th width="100">Total</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if(!empty($orders)): ?>
                    <?php $i=1; foreach($orders as $row): ?>

                        <tr>
                            <td><?= $i++ ?></td>

                            <td><?= esc($row['order_id']) ?></td>

                            <td><?= esc($row['bookshop_name']) ?></td>

                            <td><?= esc($row['book_fair_name']) ?></td>

                            <td>
                                <?= !empty($row['sending_date'])
                                    ? date('d-m-Y', strtotime($row['sending_date']))
                                    : '-' ?>
                            </td>

                            <td><?= esc($row['send_qty']) ?></td>

                            <td><?= esc($row['sale_qty']) ?></td>

                            <td><?= esc($row['book_price']) ?></td>

                            <td><?= esc($row['discount']) ?></td>

                            <td><?= esc($row['total_amount']) ?></td>

                        </tr>

                    <?php endforeach; else: ?>

                        <tr>
                            <td colspan="10" class="text-center text-muted py-3">
                                No Sold Orders Found
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
