<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<h5 class="text-center mb-3">Bookfair Sold Orders</h5>

<table class="zero-config table table-hover align-middle">
    <thead>
        <tr>
            <th>#</th>
            <th>Order ID</th>
            <th>Bookshop</th>
            <th>Pack Name</th>
            <th>No Of Titles</th>
            <th>Qty / Titles</th>
            <th>Total Qty</th>
            <th>Sending Date</th>
            <th>Discount</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php $i = 1; foreach ($orders as $row): ?>
            <tr>
                <td><?= $i++ ?></td>

                <td>
                    <a href="<?= base_url('paperback/bookfairbookshoporderdetails/'.$row['order_id']); ?>">
                        <?= esc($row['order_id']) ?>
                    </a>
                </td>

                <td><?= esc($row['bookshop_name']) ?></td>
                <td><?= esc($row['pack_name'] ?? '-') ?></td>
                <td><?= esc($row['no_of_titles'] ?? 0) ?></td>
                <td><?= (int)$row['qty_per_title'] ?></td>
                <td><?= esc($row['total_qty'] ?? 0) ?></td>

                <td>
                    <?= !empty($row['sending_date'])
                        ? date('d-m-Y', strtotime($row['sending_date']))
                        : '-' ?>
                </td>
                <td><?= esc($row['discount'] ?? 0) ?></td>

                <td>
                    <a class="btn btn-sm btn-primary"
                       href="<?= base_url('paperback/bookfairbookshoporderdetails/'.$row['order_id']); ?>">
                        View
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->endSection(); ?>
