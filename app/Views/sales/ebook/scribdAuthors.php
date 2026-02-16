<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <h4 class="mb-3 fw-bold">Scribd – Authors</h4>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Author ID</th>
                        <th>Author Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($authors)): $i=1; ?>
                        <?php foreach ($authors as $a): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= esc($a['author_id']) ?></td>
                                <td>
                                    <a href="<?= site_url('sales/scribdauthorbooks/'.$a['author_id']) ?>"
                                       class="text-primary fw-semibold">
                                       <?= esc($a['author_name']) ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-center text-muted py-3">No Authors Found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
