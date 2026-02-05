<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid py-4">

    <h4 class="mb-3">Return Order</h4>

    <form method="post" action="<?= base_url('paperback/saveReturn') ?>">

        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">

        <!-- ORDER CARD -->
        <div class="card shadow-sm mb-4">
            <div class="card-body row">
                <div class="col-md-3">
                    <b>Order ID</b><br><?= esc($order['order_id']) ?>
                </div>
                <div class="col-md-3">
                    <b>Bookshop</b><br><?= esc($order['bookshop_id']) ?>
                </div>
               <div class="col-md-3">
                    <b>Create Date</b><br>
                    <?= date('d-m-Y', strtotime($order['create_date'])) ?>
                </div>

               <div class="col-md-3">
                    <b>Sending Date</b><br>
                    <?= !empty($order['sending_date']) 
                        ? date('d-m-Y', strtotime($order['sending_date'])) 
                        : '-' ?>
                </div>

            </div>
        </div>

        <!-- BOOK TABLE -->
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Book Id</th>
                            <th>Book</th>
                            <th>Send Qty</th>
                            <th width="150">Return Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=1; foreach($books as $b): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= esc($b['book_id']) ?></td>
                            <td><?= esc($b['book_title']) ?></td>
                            <td><?= esc($b['send_qty']) ?></td>
                            <td>
                                <input type="number"
                                       name="return_qty[<?= $b['book_id'] ?>]"
                                       class="form-control"
                                       min="0"
                                       max="<?= esc($b['send_qty']) ?>">
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ORDER DISCOUNT -->
        <div class="row mt-3">
            <div class="col-md-3 ms-auto">
                <label class="fw-semibold">Discount (for entire order)</label>
                <input type="number" name="discount" class="form-control" value="0" min="0">
            </div>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="text-end mt-4">
            <a href="<?= base_url('paperback/ordersdashboard') ?>" class="btn btn-secondary px-4">Cancel</a>
            <button class="btn btn-success px-4 ms-2">Return</button>
        </div>

    </form>

</div>

<?= $this->endSection(); ?>
