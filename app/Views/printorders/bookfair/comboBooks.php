<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid py-4">

    <h6 class="mb-3">Combo Book Details</h6>
    <a href="<?= base_url('paperback/bookfaircombodetails') ?>" 
        class="btn btn-outline-secondary btn-sm mb-3">Back
    </a>

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="zero-config table table-hover align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Book ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Language</th>
                        <th>price</th>
                        <th>Stock</th>
                    </tr>
                    </thead>

                <tbody>

                <?php if(!empty($books)): ?>
                <?php $i=1; foreach($books as $b): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td>
                        <a href="<?= base_url('paperback/combobookorders/'.$b['book_id']) ?>" 
                        class="fw-semibold text-decoration-none">
                        <?= esc($b['book_id']) ?>
                        </a>
                    </td>
                    <td><?= esc($b['book_title']) ?></td>
                    <td><?= esc($b['author_name']) ?></td>
                    <td><?= esc($b['language_name']) ?></td>
                    <td><?= esc($b['paper_back_inr']) ?></td>
                    <td><?= esc($b['stock']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>

                <tr>
                    <td colspan="6" class="text-center text-danger">
                        No Books Found For This Combo
                    </td>
                </tr>

                <?php endif; ?>

                </tbody>
            </table>

        </div>
    </div>

</div>

<?= $this->endSection(); ?>
