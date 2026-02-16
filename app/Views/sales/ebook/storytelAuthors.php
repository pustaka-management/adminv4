<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <h3 class="mb-3">Storytel Authors</h3>

    <table class="zero-config table table-hover mt-4">
        <thead>
            <tr>
                <th>#</th>
                <th>Author ID</th>
                <th>Author</th>
                <th>Total Books</th>
            </tr>
        </thead>
        <tbody>
        <?php $i = 1; foreach($authors as $a): ?>
            <tr>
                <td><?= $i++; ?></td>
                <td><?= $a['author_id']; ?></td>
                <td>
                    <a href="<?= site_url('sales/storytelbooksbyauthor/'.$a['author_id']) ?>">
                        <?= esc($a['author_name']); ?>
                    </a>
                </td>
                <td><?= $a['total_books']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection(); ?>
