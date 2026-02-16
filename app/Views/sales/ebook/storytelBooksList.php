<?= $this->extend('layout/layout1') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

<h6 class="mb-3">Storytel Uploaded Books</h6>

<div class="card">
    <div class="card-body table-responsive">
        <table class="zero-config table table-hover mt-4">
            <thead>
            <tr>
                <th>#</th>
                <th>Book ID</th>
                <th>Title</th>
                <th>Author</th>
            </tr>
            </thead>
            <tbody>
            <?php if($books): $i=1; foreach($books as $b): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td>
                    <a href="<?= site_url('sales/storytelbookdetails/'.$b['book_id']) ?>"
                       class="text-primary fw-semibold">
                        <?= esc($b['book_id']) ?>
                    </a>
                </td>
                <td><?= esc($b['title']) ?></td>
                <td><?= esc($b['author_name']) ?></td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="4" class="text-center text-muted py-3">No books found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div>
<?= $this->endSection() ?>
