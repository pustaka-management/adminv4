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

    <div class="card shadow-sm radius-8">
        <div class="card-body p-3">

            <div class="table-responsive">

                <table class="zero-config table table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Order ID</th>
                            <th class="text-start">Book</th>
                            <th class="text-start">Author</th>
                            <th width="120">Sending Date</th>
                            <th width="90">Send Qty</th>
                            <th width="90">Sold Qty</th>
                            <th width="90">Price</th>
                            <th width="90">Discount</th>
                            <th width="110">Total</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if(!empty($orders)): ?>
                    <?php $i=1; foreach($orders as $row): ?>

                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= esc($row['order_id']) ?></td>

                            <td class="text-start">
                                <?= esc($row['book_title']) ?>
                            </td>

                            <td class="text-start">
                                <?= esc($row['author_name']) ?>
                            </td>

                            <td>
                                <?= date('d-m-Y',strtotime($row['sending_date'])) ?>
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
