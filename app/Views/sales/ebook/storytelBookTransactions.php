<?= $this->extend('layout/layout1') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

<a href="<?= site_url('sales/storytel/books') ?>" class="btn btn-outline-secondary btn-sm mb-3">Back</a>

<h4 class="mb-3"><?= esc($book['book_title'] ?? '') ?></h4>

<!-- BOOK DETAILS CARD -->
<div class="card mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Book Details</h6>
        <p><strong>Book ID:</strong> <?= esc($book['book_id']) ?></p>
        <p><strong>Title:</strong> <?= esc($book['book_title']) ?></p>
        <p><strong>Regional Title:</strong> <?= esc($book['regional_book_title']) ?></p>
        <p><strong>Author:</strong> <?= esc($book['author_name']) ?></p>
        <p><strong>ISBN:</strong> <?= esc($book['isbn_number']) ?></p>
        <p><strong>Cost:</strong> ₹<?= number_format($book['cost']) ?></p>
    </div>
</div>

<!-- TRANSACTIONS TABLE -->
<div class="card">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Storytel Transaction History</h6>
        <table class="zero-config table table-hover mt-4">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Remuneration (₹)</th>
                    <th>Final Royalty (₹)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if($transactions): foreach($transactions as $t): ?>
                <tr>
                    <td><?= esc($t['transaction_date']) ?></td>
                    <td><?= number_format($t['remuneration_inr'], 2) ?></td>
                    <td><?= number_format($t['final_royalty_value'], 2) ?></td>
                    <td><?= esc($t['status']) ?></td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="4" class="text-center text-muted">No transactions.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div>

<?= $this->endSection() ?>
