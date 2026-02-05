<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>
<div class="d-flex justify-content-end gap-3 mb-3 pe-3">
    <a href="<?= base_url('paperback/addsaleorreturnorder'); ?>"
       class="btn btn-outline-lilac-600 radius-8 px-20 py-11 me-2">
        Add Order
    </a>

    <a href="<?= base_url('orders/ordersdashboard'); ?>" 
       class="btn btn-outline-neutral-900 radius-8 px-20 py-11 me-2">
        Back
    </a>

    <a href="<?= base_url('paperback/ordersdashboard') ?>" class="btn btn-outline-success-600 radius-8 px-20 py-11 me-2">
        View order
    </a>
</div>


<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <h6 class="text-center">Bookfair Sale / Return Orders</h6><br>
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
        <br><br><br>
        <h6 class="text-center">Bookfair Orders (Pending)</h6><br>

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <!-- Orders Table -->
        <div class="card shadow-sm">
            <div class="card-body">

                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Order ID</th>
                            <th>Bookshop</th>
                            <th>Book Fair</th>
                            <th>Create Date</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php $i = 1; foreach ($orders as $o): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= esc($o['order_id']) ?></td>
                                    <td><?= esc($o['bookshop_id']) ?></td>
                                    <td><?= esc($o['book_fair_name']) ?></td>
                                    <td><?= esc($o['create_date']) ?></td>
                                    <td>
                                        <a href="<?= base_url('paperback/return/'.$o['order_id']) ?>"
                                        class="btn btn-primary btn-sm">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No pending orders</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
