<?= $this->extend('layout/layout1') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <table class="zero-config table table-hover mt-3">
        <thead>
        <tr>
            <th>Author Id</th>
            <th>Author Name</th>
            <th>Total Books</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($authors as $a): ?>
            <tr>
                <td><?= $a['author_id'] ?></td>
                <td>
                    
                    <a href="<?= base_url('sales/pratilipiauthordetails/'.$a['author_id']) ?>">
                        <?= $a['author_name'] ?>
                    </a>
                </td>
                <td><?= $a['book_count'] ?></td>
                <td>
                    <a href="<?= base_url('sales/pratilipiauthorbooks/'.$a['author_id']) ?>"
                       class="btn btn-primary btn-sm">
                        View Books
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
