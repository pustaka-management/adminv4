<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid py-4">

    <h6 class="fw-bold mb-3">Combo Orders</h6>

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="table-responsive">

                <table class="zero-config table table-hover align-middle">

                    <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Order ID</th>
                        <th>Bookshop</th>
                        <th>Combo</th>
                        <th>Bookfair</th>
                        <th>Sending Date</th>
                        <th>Status</th>
                    </tr>
                    </thead>

                    <tbody>

                    <?php if(!empty($orders)): ?>
                    <?php $i=1; foreach($orders as $row): ?>

                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= esc($row['order_id']) ?></td>
                        <td><?= esc($row['bookshop_name']) ?></td>
                        <td><?= esc($row['combo_name']) ?></td>
                        <td><?= esc($row['book_fair_name']) ?></td>

                        <td>
                            <?= !empty($row['sending_date']) ? date('d-m-Y',strtotime($row['sending_date'])) : '-' ?>
                        </td>

                        <td>
                            <?php
                                if($row['status']==0) echo '<span class="badge bg-warning">Pending</span>';
                                elseif($row['status']==1) echo '<span class="badge bg-primary">Shipped</span>';
                                else echo '<span class="badge bg-success">Sold</span>';
                            ?>
                        </td>

                    </tr>

                    <?php endforeach; ?>

                    <?php else: ?>

                    <tr>
                        <td colspan="7" class="text-muted py-3">
                            No Orders Found
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
