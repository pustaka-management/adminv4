<?= $this->extend('layout/layout1') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <a href="<?= base_url('sales/pratilipiauthors') ?>" class="btn btn-secondary btn-sm mb-3">
         Back
    </a>

    <table class="zero-config table table-hover mt-3">
        <thead>
        <tr>
            <th>Book ID</th>
            <th>Title</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($books as $b): ?>
            <tr>
                <td><?= $b['book_id'] ?></td>
                <td><?= $b['title'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
