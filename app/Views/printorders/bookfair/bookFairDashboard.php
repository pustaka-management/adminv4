<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid py-4">

    <!-- Navigation Buttons -->
    <div class="mb-3">

         <a href="<?= base_url('paperback/addsaleorreturnorder') ?>" class="btn btn-primary btn-sm">
            Add Order
            </a>
        <a href="<?= base_url('paperback/bookfairbookshopshippedorders'); ?>"
           class="btn btn-sm btn-primary me-1">
            Shipped Orders
        </a>

        <a href="<?= base_url('paperback/bookfairbookshopsoldorders'); ?>"
           class="btn btn-sm btn-success">
            Sold Orders
        </a>
    </div>

    <h4 class="mb-3">Bookfair Orders (Pending)</h4>

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

<?= $this->endSection(); ?>
