<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
<div class="card radius-8 border shadow-sm">
<div class="card-body">

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
        <?php if(!empty($books)): $i=1; foreach($books as $b): ?>
        <tr>
            <td><?= $i++ ?></td>
            <td>
                <a href="<?= base_url('sales/getamazonbookdetails/'.$b['book_id']) ?>"
                   class="fw-semibold text-primary">
                   <?= esc($b['book_id']) ?>
                </a>
            </td>
            <td><?= esc($b['title']) ?></td>
            <td><?= esc($b['author']) ?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr>
            <td colspan="4" class="text-center text-muted">No data</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

</div>
</div>
</div>

<?= $this->endSection(); ?>
