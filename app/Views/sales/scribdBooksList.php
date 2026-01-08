<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Scribd Books</h4>
        <a href="<?= site_url('scribd') ?>" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>

    <div class="card radius-8 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="zero-config table table-hover mt-4">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Book ID</th>
                            <th>Title</th>
                            <th>Author</th>
                           
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($books)): ?>
                            <?php $i=1; foreach ($books as $b): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= esc($b['book_id']) ?></td>
                                    <td><?= esc($b['title']) ?></td>
                                    <td><?= esc($b['author_name']) ?></td>
                                    
                                    <td>
                                        <a href="<?= site_url('sales/scribdbookdetails/'.$b['book_id']) ?>"
                                           class="btn btn-sm btn-primary">
                                            View Sales
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-3 text-muted">No Books Found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection(); ?>
