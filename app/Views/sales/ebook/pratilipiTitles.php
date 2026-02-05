<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <div class="card border shadow-sm radius-8">
        <div class="card-body">
            <div class="table-responsive">
                <table class="zero-config table table-hover mt-3">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Book ID</th>
                            <th>Title</th>
                            <th>Author</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $i = 1; foreach($books as $b): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= $b['book_id'] ?></td>
                            <td><?= esc($b['content_titles']) ?></td>
                            <td>
                                <a href="<?= base_url('sales/pratilipiauthordetails/'.$b['author_id']) ?>">
                                    <?= esc($b['author_name']) ?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <?php if(empty($books)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No Data</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection(); ?>
