<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <a href="<?= base_url('prospectivemanagement/completedbooks') ?>"
    class="btn btn-outline-secondary btn-sm mb-3">
        Back
    </a>

    <?php if(empty($book)): ?>
        <div class="alert alert-danger">Book not found</div>
        <?php return; ?>
    <?php endif; ?>

    <!-- BOOK SUMMARY -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold text-primary mb-4">
                <i class="fas fa-book me-2"></i>Book Summary
            </h5>
            <div class="row g-3">
                <?php
                    $fields = [
                        'Prospector ID'    => $book['prospector_id'],
                        'Plan Name'        => $book['plan_name'],
                        'Payment Status'   => $book['payment_status'],
                        'Payment Amount'   => '₹'.number_format($book['payment_amount'],2),
                        'Payment Date'     => $book['payment_date'],
                        'Target Date'      => $book['target_date'],
                        'Agreement Sent'   => $book['agreement_send_date'],
                        'Agreement Signed' => $book['agreement_signed_date']
                    ];
                    ?>

                    <?php foreach($fields as $k=>$v): ?>
                        <div class="col-md-3">
                            <div class="p-3 rounded bg-light h-100">
                                <small class="text-muted"><?= $k ?></small>
                                <div class="fw-semibold">
                                    <?= !empty($v) ? (strtotime($v)?date('d-m-Y',strtotime($v)):$v) : 'N/A'; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                </div>
            </div>
        </div>

    <!-- PLAN STATUS -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold text-primary mb-4">
                <i class="fas fa-layer-group me-2"></i>Plan Status
            </h5>
            <?php if(!empty($book['plan_status_arr'])): ?>
                <div class="row g-3 mb-4">
                    <!-- Production -->
                    <div class="col-md-3">
                        <div class="p-3 bg-primary bg-opacity-10 rounded h-100">
                            <h6 class="fw-bold mb-3">Production</h6>

                            <?php foreach($book['plan_status_arr']['plan_detail']['Production'] ?? [] as $k=>$v): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span><?= ucfirst(str_replace('_',' ',$k)) ?></span>
                                    <span class="badge bg-<?= $v?'success':'secondary' ?>">
                                        <?= $v?'Done':'Pending' ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Ownership Support -->
                    <div class="col-md-3">
                        <div class="p-3 bg-success bg-opacity-10 rounded h-100">
                            <h6 class="fw-bold mb-3">Ownership Support</h6>

                            <?php foreach($book['plan_status_arr']['ownership_support'] ?? [] as $k=>$v): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span><?= ucfirst(str_replace('_',' ',$k)) ?></span>
                                    <span class="text-<?= $v=='completed'?'success':'warning' ?>">
                                        <?= ucfirst($v) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Complementary -->
                    <div class="col-md-3">
                        <div class="p-3 bg-warning bg-opacity-10 rounded h-100">
                            <h6 class="fw-bold mb-3">Complementary</h6>

                            <?php foreach($book['plan_status_arr']['complementary'] ?? [] as $k=>$v): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span><?= ucfirst(str_replace('_',' ',$k)) ?></span>
                                    <span><?= esc($v) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                <!-- Distribution -->
                <div class="col-md-3">
                    <div class="p-3 bg-info bg-opacity-10 rounded h-100">
                        <h6 class="fw-bold mb-3">Distribution</h6>
                        <?php foreach($book['plan_status_arr']['distribution'] ?? [] as $section=>$values): ?>

                            <strong><?= ucfirst($section) ?></strong>

                            <?php foreach($values as $k=>$v): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span><?= ucfirst(str_replace('_',' ',$k)) ?></span>
                                    <span class="badge bg-<?= ($v=='Yes'||$v=='Completed')?'success':'secondary' ?>">
                                        <?= $v ?: 'No' ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="alert alert-warning text-center">No Plan Status</div>
        <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
