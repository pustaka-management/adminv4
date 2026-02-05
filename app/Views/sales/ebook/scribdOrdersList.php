<?= $this->extend('layout/layout1') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <h4 class="mb-4"><?= esc($title ?? 'Scribd Orders') ?></h4>

    <div class="card radius-8">
        <div class="card-body p-0">
            <table class="zero-config table table-hover mt-4">
                <thead>
                    <tr>
                        <th>Book ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>ISBN</th>
                        <th>Converted INR</th>
                        <th>Full INR</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $b): ?>
                        <tr>
                            <td>
                                <a href="<?= site_url('sales/scribdbookdetails/'.$b['book_id']) ?>"
                                   class="fw-semibold text-primary text-decoration-none">
                                    <?= esc($b['book_id']) ?>
                                </a>
                            </td>
                            <td><?= esc($b['title']) ?></td>
                            <td><?= esc($b['authors']) ?></td>
                            <td><?= esc($b['isbn']) ?></td>
                            <td><?= number_format($b['converted_inr'], 2) ?></td>
                            <td><?= number_format($b['converted_inr_full'], 2) ?></td>
                        </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-3 text-muted">
                                No Orders Found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
