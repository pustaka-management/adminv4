<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">OverDrive – Author Wise Books</h5>

        <a href="<?= site_url('sales/ebook/ebookOverdriveDetails') ?>"
           class="btn btn-outline-secondary btn-sm">
            <iconify-icon icon="mdi:arrow-left"></iconify-icon> Back to Dashboard
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
                            <th>Author</th>
                            <th class="text-end">Total Books</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($authors)): ?>
                            <?php $i = 1; foreach ($authors as $a): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td>
                                        <a href="<?= site_url('sales/overdriveauthorbooks/'.$a['author_id']) ?>"
                                        class="fw-semibold text-decoration-none text-primary">
                                            <?= esc($a['author_name']) ?>
                                        </a>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-primary-subtle text-primary px-3">
                                            <?= esc($a['total_books']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    No authors found
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
