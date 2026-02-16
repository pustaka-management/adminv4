<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <!-- BASIC INFO -->
    <div class="card mb-4">
        <div class="card-body">
            <p class="mb-1"><strong>Author ID:</strong> <?= $info['author_id'] ?></p>
            <p class="mb-0"><strong>Name:</strong> <?= esc($info['author_name']) ?></p>
        </div>
    </div>

    <!-- TRANSACTIONS -->
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Transactions</h6>

            <div class="table-responsive">
                <table class="zero-config table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Earning</th>
                            <th>Royalty</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($transactions as $t): ?>
                        <tr>
                            <td><?= !empty($t['transaction_date']) ? date('d-m-Y', strtotime($t['transaction_date'])) : '-' ?></td>
                            <td>₹ <?= number_format($t['earning'], 2) ?></td>
                            <td>₹ <?= number_format($t['final_royalty_value'], 2) ?></td>
                            <td><?= ucfirst($t['status']) ?></td>
                        </tr>
                        <?php endforeach; ?>

                        <?php if(empty($transactions)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No transactions found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>


<?= $this->endSection(); ?>
