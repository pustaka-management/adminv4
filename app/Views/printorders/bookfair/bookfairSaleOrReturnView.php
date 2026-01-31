<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <h6 class="text-center">Bookfair Sale / Return Orders</h6>
        <br>

        <table class="table zero-config">
            <thead>
                <tr>
                    <th>S.NO</th>
                    <th>Order ID</th>
                    <th>Bookshop</th>
                    <th>Pack Name</th>
                    <th>No. of Titles</th>
                    <th>Qty / Title</th>
                    <th>Total Qty</th>
                    <th>Sending Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody style="font-weight: normal;">
                <?php if (!empty($bookfair_sales['bookfair_list'])): ?>
                    <?php $i = 1; foreach ($bookfair_sales['bookfair_list'] as $row): ?>
                        <tr>
                            <td><?= $i++; ?></td>

                            <td>
                                <a href="<?= base_url('paperback/bookfairdetailsview/' . trim($row['order_id'])); ?>" 
                                   target="_blank">
                                    <?= esc($row['order_id']); ?>
                                </a>
                            </td>

                            <td><?= esc($row['bookshop_name']); ?></td>
                            <td><?= esc($row['pack_name']); ?></td>
                            <td><?= esc($row['no_of_titles']); ?></td>
                            <td><?= esc($row['quantity_per_title']); ?></td>
                            <td><?= esc($row['total_quantity']); ?></td>
                            <td><?= date('d-m-Y', strtotime($row['sending_date'])); ?></td>

                            <td>
                                <a href="<?= base_url('paperback/bookfairdetailsview/' . trim($row['order_id'])); ?>"
                                   class="btn btn-outline-success-600 radius-8 px-20 py-11"
                                   style="padding: 4px 10px; font-size: 12px;"
                                   target="_blank">
                                    View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">No records found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection(); ?>
