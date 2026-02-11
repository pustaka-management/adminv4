<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<a href="<?= base_url('combobookfair/bookfairbookshoppendingorders') ?>" 
   class="btn btn-outline-secondary btn-sm mb-3">
    Back
</a>

<div class="container-fluid py-3">

    <div class="mb-3">
        <h5 class="fw-bold">Bookfair Combo Packs</h5>
    </div>

    <div class="card shadow-sm radius-8">
        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Pack Name</th>
                            <th class="text-center">No of Titles</th>
                            <th class="text-center">Total Qty</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if(!empty($combos)): ?>
                        <?php $i=1; foreach($combos as $row): ?>

                        <tr>

                            <td><?= $i++ ?></td>

                            <td>
                                <a href="<?= base_url('combobookfair/bookfaircombobooks/'.$row['combo_id']) ?>" 
                                   class="btn btn-outline-primary btn-sm">
                                    <?= esc($row['pack_name']) ?>
                                </a>
                            </td>

                            <td class="text-center">
                                <span class="badge bg-info">
                                    <?= esc($row['book_count']) ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="badge bg-secondary px-3">
                                    <?= esc($row['total_quantity'] ?? '-') ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <a href="<?= base_url('combobookfair/comboorderdetails/'.$row['combo_id']) ?>"
                                   class="btn btn-success btn-sm">
                                    Order Details
                                </a>
                            </td>

                        </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No Combo Packs Found
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</div>

<?= $this->endSection(); ?>
