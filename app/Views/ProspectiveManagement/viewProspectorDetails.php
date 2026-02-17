<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>
<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary mb-0">
            <i class="fa fa-user me-2"></i> Prospect Details
        </h4>
        <a href="<?= base_url('prospectivemanagement/dashboard'); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <!-- Basic Information -->
    <div class="card mb-3">
        <div class="card-header bg-light fw-semibold py-2">Prospect Basic Information</div>
        <div class="card-body py-3">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" value="<?= esc($prospect['name'] ?? ''); ?>" readonly>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" value="<?= esc($prospect['phone'] ?? ''); ?>" readonly>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="<?= esc($prospect['email'] ?? ''); ?>" readonly>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Source of Reference</label>
                    <input type="text" class="form-control" value="<?= esc($prospect['source_of_reference'] ?? ''); ?>" readonly>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Author Status</label>
                    <input type="text" class="form-control" value="<?= esc($prospect['author_status'] ?? ''); ?>" readonly>
                </div>
            </div>
        </div>
    </div>

    <!-- Communication & Recommended Plan -->
    <div class="card mb-3">
        <div class="card-header bg-light fw-semibold py-2">Communication & Recommended Plan</div>
        <div class="card-body py-3">
            <div class="row g-3 align-items-center">

                <!-- Email Sent -->
                <div class="col-md-4">
                    <label class="form-label d-block mb-1">Email Sent</label>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" disabled
                               <?= (isset($prospect['email_sent_flag']) && $prospect['email_sent_flag'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label">Yes</label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" disabled
                               <?= (isset($prospect['email_sent_flag']) && $prospect['email_sent_flag'] == 0) ? 'checked' : ''; ?>>
                        <label class="form-check-label">No</label>
                    </div>

                    <?php if (!empty($prospect['email_sent_flag']) && $prospect['email_sent_flag'] == 1): ?>
                        <input type="date" class="form-control mt-2" readonly
                               value="<?= !empty($prospect['email_sent_date']) ? date('Y-m-d', strtotime($prospect['email_sent_date'])) : ''; ?>">
                    <?php endif; ?>
                </div>

                <!-- Initial Call -->
                <div class="col-md-4">
                    <label class="form-label d-block mb-1">Initial Call</label>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" disabled
                               <?= (isset($prospect['initial_call_flag']) && $prospect['initial_call_flag'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label">Yes</label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" disabled
                               <?= (isset($prospect['initial_call_flag']) && $prospect['initial_call_flag'] == 0) ? 'checked' : ''; ?>>
                        <label class="form-check-label">No</label>
                    </div>

                    <?php if (!empty($prospect['initial_call_flag']) && $prospect['initial_call_flag'] == 1): ?>
                        <input type="date" class="form-control mt-2" readonly
                               value="<?= !empty($prospect['initial_call_date']) ? date('Y-m-d', strtotime($prospect['initial_call_date'])) : ''; ?>">
                    <?php endif; ?>
                </div>

                <!-- Recommended Plan -->
                <div class="col-md-4">
                    <label class="form-label">Recommended Plan</label>
                    <input type="text" class="form-control" readonly value="<?= esc($prospect['recommended_plan'] ?? ''); ?>">
                </div>

                <!-- Latest Remark -->
                <div class="col-md-12">
                    <label class="form-label">Latest Remark</label>

                    <?php if (!empty($latestRemark)): ?>
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr class="bg-secondary text-white">
                                    <th width="70%">Remark</th>
                                    <th width="30%">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?= esc($latestRemark['remarks'] ?? ''); ?></td>
                                    <td><?= !empty($latestRemark['create_date']) ? date('d-m-Y h:i A', strtotime($latestRemark['create_date'])) : ''; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted mb-0">No latest remark available.</p>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>


</div>
<?= $this->endSection(); ?>
