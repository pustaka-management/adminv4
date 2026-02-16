<?= $this->extend('layout/layout1') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <!-- Back -->
    <a href="<?= base_url('sales/amazontitles') ?>" class="btn btn-sm btn-outline-secondary mb-3">
        ← Back to Books
    </a>

    <!-- Title -->
    <h4 class="mb-3">
        <?= esc($book['title']) ?>
    </h4>

    <!-- ================= BOOK DETAILS ================= -->
    <div class="row">
        <div class="col-md-6">
            <div class="card p-3 mb-4 shadow-sm">

                <h5 class="mb-3">Book Details</h5>

                <p><strong>Book ID:</strong> <?= esc($book['book_id']) ?></p>
                <p><strong>Title:</strong> <?= esc($book['title']) ?></p>
                <p><strong>Regional Title:</strong> <?= esc($book['regional_title'] ?? '-') ?></p>
                <p><strong>Author:</strong> <?= esc($book['author']) ?></p>
                <p><strong>ISBN:</strong> <?= esc($book['isbn'] ?? '-') ?></p>
                <p>
                    <strong>Cost:</strong>
                    ₹ <?= number_format($book['cost'] ?? 0, 2) ?>
                </p>

            </div>
        </div>
    </div>

    <!-- ================= AMAZON TRANSACTIONS ================= -->
    <div class="card p-3 shadow-sm">
        <h5 class="mb-3">Amazon Transactions</h5>

        <div class="table-responsive">
            <table class="zero-config table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th class="text-end">INR Value</th>
                        <th class="text-end">Royalty</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>

                <?php
                $total = 0;
                if (!empty($transactions)):
                    foreach ($transactions as $t):
                        $total += $t['inr_value'];
                        $status = $t['status'] ?? 'n';
                ?>
                    <tr>
                        <td>
                            <?= !empty($t['original_invoice_date'])
                                ? date('d-m-Y', strtotime($t['original_invoice_date']))
                                : '-' ?>
                        </td>
                        <td class="text-end">
                            ₹ <?= number_format($t['inr_value'], 2) ?>
                        </td>
                        <td class="text-end">
                            ₹ <?= number_format($t['final_royalty_value'], 2) ?>
                        </td>
                        <td>
                            <?php if ($status === 'P'): ?>
                                <span class="badge bg-success">Paid</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            No transactions found
                        </td>
                    </tr>
                <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
