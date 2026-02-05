<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>


<div class="container-fluid">
    

    <a href="<?= site_url('scribd/scribdbooks') ?>" class="btn btn-outline-secondary btn-sm mb-3">Back</a>
    <?php if(empty($book)): ?>
<div class="alert alert-danger">Book not found</div>
<?php return; endif; ?>

    <!-- BOOK DETAILS -->
    <div class="card shadow-sm mb-4 p-4">
        <h4 class="mb-3 text-primary"><?= esc($book['book_title']) ?></h4>
        <div class="row g-3">
            <div class="col-md-3"><strong>Book ID:</strong> <?= esc($book['book_id']) ?></div>
            <div class="col-md-3"><strong>Author:</strong> <?= esc($book['author_name']) ?></div>
            <div class="col-md-3"><strong>Regional Title:</strong> <?= esc($book['regional_book_title']) ?></div>
            <div class="col-md-3"><strong>ISBN:</strong> <?= esc($book['isbn_number']) ?></div>
            <div class="col-md-3"><strong>Pages:</strong> <?= esc($book['number_of_page']) ?></div>
            <div class="col-md-3"><strong>Cost:</strong> ₹<?= esc($book['cost']) ?></div>
        </div>
    </div>

    <!-- TRANSACTIONS TABLE -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Transactions</h5>
        </div>

        <div class="table-responsive">
           <table class="zero-config table table-hover mt-4">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Month</th>
                        <th>Country</th>
                        <th>Amount (₹)</th>
                        <th>Full Royalty (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($txns)) { $i=1; ?>
                        <?php foreach ($txns as $t): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= esc($t['payment_month']) ?></td>
                            <td><?= esc($t['country_of_reader']) ?></td>
                            <td><?= number_format($t['converted_inr'], 2) ?></td>
                            <td><?= number_format($t['converted_inr_full'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No transactions found.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?= $this->endSection(); ?>
