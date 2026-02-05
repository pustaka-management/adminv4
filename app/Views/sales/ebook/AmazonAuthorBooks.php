<?= $this->extend('layout/layout1') ?>
<?= $this->section('content') ?>

<a href="<?= base_url('sales/amazonauthors') ?>" class="btn btn-sm btn-outline-secondary mb-3">
    ← Back
</a>
<table class="zero-config table table-hover">
    <thead class="table-light">
        <tr>
            <th>Book ID</th>
            <th>Title</th>
            <th>View</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($books as $b): ?>
        <tr>
            <td><?= $b['book_id'] ?></td>
            <td><?= esc($b['title']) ?></td>
            <td>
                <a href="<?= base_url('sales/getamazonbookdetails/'.$b['book_id']) ?>"
                   class="btn btn-sm btn-primary">
                    View Details
                </a>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?= $this->endSection() ?>
