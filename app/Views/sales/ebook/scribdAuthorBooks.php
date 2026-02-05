<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <a href="<?= site_url('sales/scribd/authors') ?>" class="btn btn-outline-secondary btn-sm mb-3">Back</a>

    <h4 class="fw-bold mb-3">
        Books by <?= esc($author['author_name'] ?? 'Unknown') ?>
    </h4>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Book ID</th>
                        <th>Title</th>
                        <th>ISBN</th>
                        <th>Cost</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($books)): $i=1; ?>
                        <?php foreach ($books as $b): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td>
                                    <a href="<?= site_url('sales/scribdbookdetails/'.$b['book_id']) ?>"
                                       class="fw-semibold text-primary">
                                        <?= esc($b['book_id']) ?>
                                    </a>
                                </td>
                                <td><?= esc($b['title']) ?></td>
                                <td><?= esc($b['isbn_number']) ?></td>
                                <td><?= esc($b['cost']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center text-muted py-3">No Books Found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?= $this->endSection(); ?>
