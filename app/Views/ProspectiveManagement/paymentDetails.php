<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">
                    <i class="fa fa-credit-card text-primary me-2"></i> Prospect - Payment Details
                </h4>
                <a href="<?= base_url('prospectivemanagement/dashboard'); ?>" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-2"></i> Back
                </a>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs" id="paymentTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="paid-tab" data-bs-toggle="tab" data-bs-target="#paid" type="button" role="tab">
                        Paid
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="partial-tab" data-bs-toggle="tab" data-bs-target="#partial" type="button" role="tab">
                        Partial
                    </button>
                </li>
            </ul>

            <div class="tab-content mt-4" id="paymentTabsContent">

                <!-- Paid Tab -->
                <div class="tab-pane fade show active" id="paid" role="tabpanel">
                    <div class="table-responsive">
                       <table class="zero-config table table-hover mt-4 align-middle">
                            <thead class="table-success">
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Author Status</th>
                                    <th>Plan</th>
                                    <th>Title</th>
                                    <th>Payment Status</th>
                                    <th>Amount (₹)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1; 
                                foreach ($prospects as $p): 
                                    if (strtolower($p['payment_status']) === 'paid'): 
                                ?>
                                    <tr>
                                        <td><?= $i++; ?></td>
                                        <td><?= esc($p['prospector_id']); ?></td>
                                        <td><?= esc($p['name']); ?></td>
                                        <td><?= esc($p['phone']); ?></td>
                                        <td><?= esc($p['author_status'] ?: '-'); ?></td>
                                        <td><?= esc($p['recommended_plan'] ?: '-'); ?></td>
                                        <td><?= esc($p['title'] ?: '-'); ?></td>
                                        <td>Paid</span></td>
                                        <td><strong class="text-success"><?= indian_format($p['payment_amount'], 2); ?></strong></td>
                                       <td>
                                            <a href="<?= base_url('prospectivemanagement/view/' . $p['prospector_id']); ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                        </td>
                                        </td>
                                    </tr>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Partial Tab -->
                <div class="tab-pane fade" id="partial" role="tabpanel">
                    <div class="table-responsive">
                        <table class="zero-config table table-hover mt-4 align-middle">
                            <thead class="table-info">
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Author Status</th>
                                    <th>Plan</th>
                                    <th>Title</th>
                                    <th>Payment Status</th>
                                    <th>Amount (₹)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1; 
                                foreach ($prospects as $p): 
                                    if (strtolower($p['payment_status']) === 'partial'): 
                                ?>
                                    <tr>
                                        <td><?= $i++; ?></td>
                                        <td><?= esc($p['prospector_id']); ?></td>
                                        <td><?= esc($p['name']); ?></td>
                                        <td><?= esc($p['phone']); ?></td>
                                        <td><?= esc($p['author_status'] ?: '-'); ?></td>
                                        <td><?= esc($p['recommended_plan'] ?: '-'); ?></td>
                                        <td><?= esc($p['title'] ?: '-'); ?></td>
                                        <td>Partial</span></td>
                                        <td><strong class="text-primary"><?= indian_format($p['payment_amount'], 2); ?></strong></td>
                                        <td>
                                            <a href="<?= base_url('prospectivemanagement/view/' . $p['prospector_id']); ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
$(document).ready(function () {
    new DataTable('#paidTable', { pageLength: 10 });
    new DataTable('#partialTable', { pageLength: 10 });
});
</script>
<?= $this->endSection(); ?>
