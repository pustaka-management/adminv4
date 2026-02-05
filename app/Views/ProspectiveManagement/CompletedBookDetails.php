<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- Back Button -->
    <a href="<?= base_url('prospectivemanagement/completedbooks') ?>" 
       class="btn btn-outline-secondary btn-sm mb-3">
        Back
    </a>

    <?php if(empty($book)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>Book not found
        </div>
        <?php return; ?>
    <?php endif; ?>

    <!-- Book Details Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h5 class="mb-0 text-primary">
                <i class="fas fa-book me-2"></i>Book Details
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <?php
                $fields = [
                    'Prospector ID'    => $book['prospector_id'],
                    'Plan Name'        => $book['plan_name'],
                    'Payment Status'   => '<span class="badge bg-' . ($book['payment_status'] === 'Paid' ? 'success' : 'warning') . '">' . $book['payment_status'] . '</span>',
                    'Payment Amount'   => '₹' . number_format($book['payment_amount'], 2),
                    'Payment Date'     => !empty($book['payment_date']) ? date('d-m-Y', strtotime($book['payment_date'])) : '<span>N/A</span>',
                    'Created Date'     => !empty($book['create_date']) ? date('d-m-Y', strtotime($book['create_date'])) : '<span class="text-muted">N/A</span>',
                    'Agreement Sent'   => !empty($book['agreement_send_date']) ? date('d-m-Y', strtotime($book['agreement_send_date'])) : '<span class="text-muted">Pending</span>',
                    'Agreement Signed' => !empty($book['agreement_signed_date']) ? date('d-m-Y', strtotime($book['agreement_signed_date'])) : '<span class="text-muted">Pending</span>',
                    'Target Date'      => !empty($book['target_date']) ? date('d-m-Y', strtotime($book['target_date'])) : '<span class="text-muted">N/A</span>',
                    'Completed Status' => $book['completed_status'] 
                                            ? '<span class="badge bg-success">Completed</span>' 
                                            : '<span class="badge bg-secondary">In Progress</span>'
                ];
                ?>
                <?php foreach($fields as $label => $value): ?>
                    <div class="col-md-4 col-lg-3">
                        <div class="border-start border-3 border-primary px-3 py-2 bg-white">
                            <small class="text-muted d-block mb-1">
                                <i class="fas fa-circle small me-1"></i><?= $label ?>
                            </small>
                            <div class="fw-semibold text-dark"><?= $value ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Plan Status Card -->
    <div class="card shadow-sm">
        <div class="card-header py-3">
            <h5 class="mb-0 text-primary">
                <i class="fas fa-tasks me-2"></i>Plan Status
            </h5>
        </div>
        <div class="card-body">
            <?php if(!empty($book['plan_status_arr'])): ?>

                <div class="row g-4 mb-4">

                    <!-- Production -->
                    <div class="col-md-4">
                        <div class="card border h-100">
                            <div class="card-header bg-primary bg-opacity-10 py-2">
                                <h6 class="mb-0 text-primary">
                                    <i class="fas fa-industry me-2"></i>Production
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <ul class="list-unstyled mb-0">
                                    <?php foreach(($book['plan_status_arr']['plan_detail']['Production'] ?? []) as $k => $v): ?>
                                        <li class="py-2 border-bottom">
                                            <span class="text-muted"><?= ucfirst(str_replace('_', ' ', $k)) ?></span>
                                            <span class="badge float-end bg-<?= $v ? 'success' : 'light text-dark' ?>">
                                                <?= $v ? '✓' : '✗' ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Ownership Support -->
                    <div class="col-md-4">
                        <div class="card border h-100">
                            <div class="card-header bg-primary bg-opacity-10 py-2">
                                <h6 class="mb-0 text-primary">
                                    <i class="fas fa-handshake me-2"></i>Ownership Support
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <ul class="list-unstyled mb-0">
                                    <?php foreach(($book['plan_status_arr']['ownership_support'] ?? []) as $k => $v): ?>
                                        <li class="py-2 border-bottom">
                                            <span class="text-muted"><?= ucfirst(str_replace('_', ' ', $k)) ?></span>
                                            <span class="float-end fw-semibold text-<?= 
                                                $v === 'completed' ? 'success' : 
                                                ($v === 'in progress' ? 'warning' : 'secondary') 
                                            ?>">
                                                <?= ucfirst($v) ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Complementary -->
                    <div class="col-md-4">
                        <div class="card border h-100">
                            <div class="card-header bg-primary bg-opacity-10 py-2">
                                <h6 class="mb-0 text-primary">
                                    <i class="fas fa-gift me-2"></i>Complementary
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <ul class="list-unstyled mb-0">
                                    <?php foreach(($book['plan_status_arr']['complementary'] ?? []) as $k => $v): ?>
                                        <li class="py-2 border-bottom">
                                            <span class="text-muted"><?= ucfirst(str_replace('_', ' ', $k)) ?></span>
                                            <span class="float-end fw-semibold"><?= esc($v) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Distribution Section -->
                <div class="card border">
                    <div class="card-header bg-primary bg-opacity-10 py-2">
                        <h6 class="mb-0 text-primary">
                            <i class="fas fa-truck me-2"></i>Distribution
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <?php foreach(($book['plan_status_arr']['distribution'] ?? []) as $section => $values): ?>
                                <div class="col-md-4">
                                    <div class="card h-100 border">
                                        <div class="card-header py-2">
                                            <strong class="text-dark">
                                                <i class="fas fa-folder me-2"></i><?= ucfirst($section) ?>
                                            </strong>
                                        </div>
                                        <div class="card-body p-3">
                                            <ul class="list-unstyled mb-0">
                                                <?php foreach($values as $k => $v): ?>
                                                    <li class="py-2 border-bottom">
                                                        <span class="text-muted"><?= ucfirst(str_replace('_', ' ', $k)) ?></span>
                                                        <span class="float-end fw-semibold text-<?= 
                                                            $v === 'Yes' || $v === 'Completed' ? 'success' : 'dark'
                                                        ?>">
                                                            <?= $v ?: 'No' ?>
                                                        </span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <div class="alert alert-warning text-center py-3">
                    <i class="fas fa-info-circle me-2"></i>
                    No plan status information available.
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<?= $this->endSection(); ?>