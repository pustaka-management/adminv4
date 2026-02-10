<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>
<a href="<?= base_url('combobookfair/bookfairbookshoppendingorders') ?>" 
        class="btn btn-outline-secondary btn-sm mb-3">Back
    </a>

<table class="zero-config table table-hover align-middle">
    <thead>
        <tr>
            <th>#</th>
            <th>Order ID</th>
            <th>Bookshop</th>
            <th>Pack Name</th>
            <th>No Of Titles</th>
            <th>Qty/Titles</th>
            <th>Total Qty</th>
            <th>Sending Date</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php $i = 1; foreach($orders as $row): ?>
            <tr>
                <td><?= $i++ ?></td>

                <td>
                    <a href="<?= base_url('combobookfair/bookfairshippedorderdetails/'.$row['order_id']); ?>">
                        <?= esc($row['order_id']) ?>
                    </a>
                </td>

                <td><?= esc($row['bookshop_name']) ?></td>
                <td><?= esc($row['pack_name'] ?? '-') ?></td>
                <td><?= esc($row['no_of_titles']) ?></td>
                <td><?= (int)$row['qty_per_title'] ?></td>
                <td><?= esc($row['total_qty']) ?></td>

                <td>
                    <?= !empty($row['sending_date'])
                        ? date('d-m-Y', strtotime($row['sending_date']))
                        : '-' ?>
                </td>

                <td>
                    <a class="btn btn-sm btn-primary"
                       href="<?= base_url('combobookfair/bookfairshippedorderdetails/'.$row['order_id']); ?>">
                        View
                    </a>

                    <a class="btn btn-sm btn-primary"
                       href="<?= base_url('combobookfair/return/'.$row['order_id']); ?>">
                        Return
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->endSection(); ?>
