<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container mt-4">

    <?php if (!empty($plans)): ?>
        <table class="zero-config table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Plan Name</th>
                    <th>Payment Status</th>
                    <th>Amount</th>
                    <th>Payment Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($plans as $p): ?>
                    <tr>
                        <td><?= esc($p['plan_name']) ?></td>
                        <td><?= esc($p['payment_status_text']) ?></td>
                        <td><?= isset($p['payment_amount']) ? '₹' . number_format($p['payment_amount'], 2) : '-' ?></td>
                        <td><?= !empty($p['payment_date']) ? date('d-m-Y', strtotime($p['payment_date'])) : '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <hr>

        <!-- Remarks Section -->
        <h5 class="fw-bold mt-4 mb-3">Remarks</h5>

        <?php if (!empty($remarks)): ?>
            <?php 
                // Track displayed descriptions
                $displayedDescriptions = [];

                foreach ($remarks as $r): 
                    // If empty/null → assign default label "General"
                    $desc = $r['payment_description'] ?: 'General';

                    if (!in_array($desc, $displayedDescriptions)):
                        $displayedDescriptions[] = $desc;
            ?>
                <div class="mb-3 p-3 border rounded bg-light">
                    <div><strong>Description:</strong> <?= esc($desc) ?></div>

                    <!-- Show all remarks with same description -->
                    <?php foreach ($remarks as $r2): ?>
                        <?php if (($r2['payment_description'] ?: 'General') === $desc && !empty($r2['remarks'])): ?>
                            <div><strong>Remarks:</strong> <?= esc($r2['remarks']) ?></div>
                            <div class="text-muted small mt-1">
                                Created by <?= esc($r2['created_by']) ?> 
                                on <?= date('d-m-Y H:i', strtotime($r2['create_date'])) ?>
                                <?php if (!empty($r2['des_date'])): ?>
                                    (Des Date: <?= date('d-m-Y', strtotime($r2['des_date'])) ?>)
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

            <?php 
                    endif; 
                endforeach; 
            ?>

        <?php else: ?>
            <div class="alert alert-info">No remarks available.</div>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-warning">
            No paid / partial plans found for this book.
        </div>
    <?php endif; ?>

</div>

<?= $this->endSection(); ?>
