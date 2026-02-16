<?= $this->extend('layout/layout1') ?>
<?= $this->section('content') ?>

<h4 class="mb-3"><?= $book['book_title'] ?></h4>

<div class="row">
    <div class="col-md-6">
        <div class="card p-3 mb-3">
            <h5>Book Details</h5>
            <p><strong>Book ID:</strong> <?= $book['book_id'] ?></p>
            <p><strong>Title:</strong> <?= $book['book_title'] ?></p>
            <p><strong>Regional Title:</strong> <?= $book['regional_book_title'] ?></p>
            <p><strong>Author:</strong> <?= $book['author_name'] ?></p>
            <p><strong>ISBN:</strong> <?= $book['isbn_number'] ?></p>
            <p><strong>Cost:</strong> ₹ <?= number_format($book['cost'],2) ?></p>
        </div>
    </div>
</div>

<table class="zero-config table table-hover mt-4">
    <thead>
        <tr>
            <th>Date</th>
            <th>INR Value</th>
            <th>Royalty</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $total = 0;
        foreach($transactions as $t): 
            $total += $t['inr_value'];
        ?>
        <tr>
            <td><?= $t['earnings_date'] ?></td>
            <td>₹ <?= number_format($t['inr_value'],2) ?></td>
            <td>₹ <?= number_format($t['final_royalty_value'],2) ?></td>
            <td>
                <?php if($t['status']=='p'): ?>
                    <span class="badge bg-success">Paid</span>
                <?php else: ?>
                    <span class="badge bg-warning text-dark">Pending</span>
                <?php endif ?>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>
<?= $this->endSection() ?>
