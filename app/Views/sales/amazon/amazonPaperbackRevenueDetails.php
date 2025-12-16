<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>
<div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
    <a href="<?= base_url('dashboard/amazonpaperback'); ?>" 
       class="btn btn-sm btn-success radius-8 d-inline-flex align-items-center gap-1">
        <iconify-icon icon="uil:arrow-left" class="text-xl"></iconify-icon>
        Back
    </a>
</div>
<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="page-header">
            <div class="page-title">
                <h6 class="text-center">Amazon Paperback Books</h6>
            </div>
        </div>
        <table class="table table-hover mt-4 zero-config">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Book ID</th>
                    <th>ASIN</th>
                    <th>Price</th>
                    <th>Copyright Owner</th>
                    <th>Author ID</th>
                    <th>Language</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paperback_revenue as $row): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td>
                        <a href="<?= base_url('dashboard/amazonpbkbookdetails/'.$row['book_id']); ?>" target="_blank">
                            <?= $row['book_id']; ?>
                        </a>
                    </td>
                    <td><?= $row['asin']; ?></td>
                    <td><?= $row['price']; ?></td>
                    <td><?= $row['copyright_owner']; ?></td>
                    <td><?= $row['author_id']; ?></td>
                    <td><?= $row['language']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection(); ?>
