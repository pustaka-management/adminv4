<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <h3 class="mb-3">Books</h3>

    <a href="<?= site_url('sales/storytelauthors') ?>" class="btn btn-secondary mb-3">← Back</a>

    <table class="zero-config table table-hover mt-4">
        <thead>
            <tr>
                <th>#</th>
                <th>Book ID</th>
                <th>Title</th>
                <th>Author Name</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
        <?php $i = 1; foreach($books as $b): ?>
            <tr>
                <td><?= $i++; ?></td>
                <td>
                    <a href="<?= site_url('sales/storytelbookdetails/'.$b['book_id']) ?>"
                       class="text-primary fw-semibold">
                        <?= esc($b['book_id']) ?>
                    </a>
                </td>
                <td><?= $b['title']; ?></td>
                <td><?= $b['author_name']; ?></td>
                <td>
                    <a href="<?= site_url('sales/storytelbookdetails/'.$b['book_id']) ?>" 
                       class="btn btn-info btn-sm">
                        View
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection(); ?>
