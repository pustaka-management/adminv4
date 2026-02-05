<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid py-4">

    <!-- Header -->
    <div class="mb-4 px-3">
        <h6 class="fw-bold mb-0">Bookfair Combo Packs</h6>
    </div>

    <!-- Card Wrapper -->
    <div class="row px-3">
        <div class="col-12">

            <div class="card shadow-sm rounded-3">

                <div class="card-body p-4">

                    <div class="table-responsive">

                        <!-- TABLE START -->
                        <table class="zero-config table table-hover align-middle">

                            <thead class="table-light">
                                <tr>
                                    <th width="60">#</th>
                                    <th class="text-start">Pack Name</th>
                                    <th width="120">No Of Books</th>
                                    <th width="150">Default Quantity</th>
                                    <th width="120">Action</th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php if(!empty($combos)): ?>
                            <?php $i=1; foreach($combos as $row): ?>

                                <tr>
                                    <td><?= $i++ ?></td>

                                    <td class="text-start align-middle">
                                        <a href="<?= base_url('paperback/bookfaircombobooks/' . $row['combo_id']) ?>"
                                        class="btn btn-secondary-600 btn-sm">
                                            <?= esc($row['pack_name']) ?>
                                        </a>
                                    </td>
                                    <td><?= esc($row['book_count']) ?></td>

                                    <td>
                                        <span class="badge bg-secondary px-3 py-2">
                                            <?= esc($row['default_value']) ?>
                                        </span>
                                    </td>
                                     <td class="text-start">
                                        <a href="<?= base_url('paperback/comboorderdetails/'.$row['combo_id']) ?>"
                                           class="fw-semibold text-decoration-none">
                                            view
                                        </a>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                            <?php else: ?>

                                <tr>
                                    <td colspan="4" class="text-muted py-4">
                                        No Combo Packs Found
                                    </td>
                                </tr>

                            <?php endif; ?>

                            </tbody>

                        </table>
                        <!-- TABLE END -->

                    </div>

                </div>

            </div>

        </div>
    </div>

</div>

<?= $this->endSection(); ?>
