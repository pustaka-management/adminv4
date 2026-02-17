<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h6 class="mb-0">OverDrive Transactions – Book ID: <?= esc($bookId) ?></h6>

        <a href="<?= site_url('sales/overdriveBooks') ?>"
           class="btn btn-outline-secondary btn-sm">
            Back
        </a>
    </div>

    <?php if (!empty($transactions)): 
        $book = $transactions[0]; // book info same for all rows
    ?>

    <!-- ================= BOOK DETAILS CARD ================= -->
    <div class="card radius-8 border mb-4">
        <div class="card-body">
            <h6 class="mb-3">Book Details</h6>

            <div class="row g-3">
                <div class="col-md-3">
                    <small class="text-secondary-light">Book ID</small>
                    <div class="fw-semibold"><?= esc($book['book_id']) ?></div>
                </div>

                <div class="col-md-3">
                    <small class="text-secondary-light">Title</small>
                    <div class="fw-semibold"><?= esc($book['book_title']) ?></div>
                </div>

                <div class="col-md-3">
                    <small class="text-secondary-light">Regional Title</small>
                    <div class="fw-semibold"><?= esc($book['regional_book_title'] ?? '-') ?></div>
                </div>

                <div class="col-md-3">
                    <small class="text-secondary-light">Author</small>
                    <div class="fw-semibold"><?= esc($book['author_name'] ?? '-') ?></div>
                </div>

                <div class="col-md-3">
                    <small class="text-secondary-light">ISBN</small>
                    <div class="fw-semibold"><?= esc($book['isbn_number'] ?? '-') ?></div>
                </div>

                <div class="col-md-3">
                    <small class="text-secondary-light">Pages</small>
                    <div class="fw-semibold"><?= esc($book['number_of_page'] ?? '-') ?></div>
                </div>

                <div class="col-md-3">
                    <small class="text-secondary-light">Cost</small>
                    <div class="fw-semibold">₹ <?= number_format($book['cost'] ?? 0, 2) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= TRANSACTIONS TABLE ================= -->
    <div class="card radius-8 border">
        <div class="card-body">
            <h6 class="mb-3">Transaction Details</h6>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Retailer</th>
                            <th>INR Value</th>
                            <th>Royalty</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($transactions as $row): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= date('d-m-Y', strtotime($row['transaction_date'])) ?></td>
                                <td><?= esc($row['retailer']) ?></td>
                                <td>₹ <?= number_format($row['inr_value'], 2) ?></td>
                                <td>₹ <?= number_format($row['final_royalty_value'], 2) ?></td>
                                <td>
                                    <?php if ($row['status'] === 'p'): ?>
                                        <span class="badge bg-success">Paid</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Outstanding</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <?php else: ?>
        <div class="alert alert-warning">No transactions found for this book.</div>
    <?php endif; ?>

</div>

<?= $this->endSection(); ?>
