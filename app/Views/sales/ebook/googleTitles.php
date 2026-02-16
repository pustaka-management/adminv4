<?= $this->extend('layout/layout1') ?>
<?= $this->section('content') ?>

<table class="zero-config table table-hover mt-4">
    <thead>
        <tr>
            <th>#</th>
            <th>Book ID</th>
            <th>Book Title</th>
            <th>Author</th>
        </tr>
    </thead>
    <tbody>
        <?php $i=1; foreach($books as $b): ?>
        <tr>
            <td><?= $i++ ?></td>
           <td>
            <a href="<?= site_url('sales/googletitledetails/'.$b['book_id']) ?>" 
            style="color:#0d6efd;" 
            target="_blank">
                <?= esc($b['book_id']) ?>
            </a>
            </td>
            <td><?= esc($b['title']) ?></td>
            <td><?= esc($b['author_name']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
