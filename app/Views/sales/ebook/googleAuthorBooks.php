<?= $this->extend('layout/layout1') ?>
<?= $this->section('content') ?>

<table class="zero-config table table-hover mt-4">
    <thead>
        <tr>
            <th>#</th>
            <th>Book ID</th>
            <th>Book Title</th>
        </tr>
    </thead>
    <tbody>
        <?php $i=1; foreach($books as $b): ?>
        <tr>
            <td><?= $i++ ?></td>

            <!-- BOOK ID normal -->
            <td><a href="<?= site_url('sales/googletitledetails/'.$b['book_id']) ?>"
                   style="color:#0d6efd;" target="_blank"><?= esc($b['book_id']) ?></td></a>

            <!-- BOOK TITLE clickable -->
            <td><?= esc($b['title']) ?>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?= $this->endSection() ?>
