<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="<?= site_url('sales/ebookoverdrivedetails') ?>"
           class="btn btn-outline-secondary btn-sm">
            Back
        </a>
    </div>

    <!-- TABLE -->
    <div class="card radius-8 border">
        <div class="card-body">
            <div class="table-responsive">
                <table class="zero-config table table-hover mt-4">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Book ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php $i = 1; foreach ($orders as $o): ?>
                                <tr>
                                    <td><?= $i++ ?></td>

                                    <td>
                                        <a href="<?= site_url('sales/overdrivebooktransactions/'.$o['book_id']) ?>"
                                           class="text-primary fw-semibold">
                                            <?= esc($o['book_id']) ?>
                                        </a>
                                    </td>

                                    <td><?= esc($o['book_title']) ?></td>
                                    <td><?= esc($o['author_name'] ?? '-') ?></td>

                                    <td>
                                        <?php if ($o['status'] === 'P'): ?>
                                            <span class="badge bg-success">Paid</span>
                                        <?php elseif ($o['status'] === 'O'): ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No orders found
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
