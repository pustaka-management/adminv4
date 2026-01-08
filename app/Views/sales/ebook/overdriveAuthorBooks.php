<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">
            OverDrive Books – Author ID: <?= esc($authorId) ?>
        </h5>

        <a href="<?= site_url('sales/overdriveauthors') ?>"
           class="btn btn-outline-secondary btn-sm">
            <iconify-icon icon="mdi:arrow-left"></iconify-icon>
            Back
        </a>
    </div>

    <!-- CARD -->
    <div class="card radius-8 border">
        <div class="card-body">

            <div class="table-responsive">
                <table class="zero-config table table-hover mt-4">
                    <thead class="table-light">
                        <tr>
                            <th width="60">#</th>
                            <th>Book ID</th>
                            <th>Book Title</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($books)): ?>
                            <?php $i=1; foreach ($books as $b): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                   <td>
                                        <a href="<?= site_url('sales/overdrivebooktransactions/'.$b['book_id']) ?>"
                                        class="text-primary fw-semibold">
                                            <?= esc($b['book_id']) ?>
                                        </a></td>
                                    <td><?= esc($b['book_title']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    No books found for this author
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
