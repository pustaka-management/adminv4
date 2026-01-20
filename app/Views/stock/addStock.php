<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

       
       <div class="d-flex justify-content-end mb-2">
            <a href="<?= base_url('stock/bulkupload') ?>" 
            class="btn btn-sm btn-warning radius-8">
                 Bulk Stock Upload
            </a>
        </div>
        <br>

<div class="card basic-data-table">
    <div class="card-header">
        <h5 class="card-title mb-0">Available Paperback Books</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
           
            <table class="zero-config table table-hover mt-4"> 
               <thead>
                    <tr>
                        <th style="width: 20px; text-align: center;">S.No</th>
                        <th style="width: 40px; text-align: center;">Book ID</th>
                        <th style="width: 40%;">Title</th>
                        <th style="width: 20%;">Regional Title</th>
                        <th style="width: 15%;">Author</th>
                        <th style="width: 10%;">Cost (INR)</th>
                        <th style="width: 10%;">Pages</th>
                        <th style="width: 10%;">Stock In Hand</th>
                        <th style="width: 80px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach ($paperback_books['details'] as $book): ?>
                        <tr>
                            <td style="text-align: center;"><?= $i++; ?></td>
                            <td style="text-align: center;"><?= esc($book['book_id']); ?></td>
                            <td style="word-wrap: break-word; white-space: normal;"><?= esc($book['book_title']); ?></td>
                            <td style="word-wrap: break-word; white-space: normal;"><?= esc($book['regional_book_title']); ?></td>
                            <td><?= esc($book['author']); ?></td>
                            <td style="text-align: center;"><?= esc($book['paper_back_inr']); ?></td>
                            <td style="text-align: center;"><?= esc($book['number_of_page']); ?></td>
                            <td style="text-align: center;"><?= esc($book['stock_in_hand']); ?></td>
                            <td style="text-align: center;">
                                <a href="<?= base_url('stock/bookslist'); ?>?selected_book_list=<?= esc($book['book_id']); ?>"
                                target="_blank"
                                class="badge text-sm fw-semibold bg-success-600 px-20 py-9 radius-4 text-white">
                                Add
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        new DataTable('#dataTable');
    });
</script>
<?= $this->endSection(); ?>
