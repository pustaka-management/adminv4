<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center mb-3">
            <iconify-icon icon="mdi:book-plus-outline" class="text-primary fs-4 me-2"></iconify-icon>
            <h6 class="fw-bold mb-0">Add Book Details</h6>
        </div>

        <a href="<?= base_url('prospectivemanagement/dashboard'); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <form method="post" action="<?= base_url('prospectivemanagement/savebookdetails'); ?>">

                <div class="row g-3 mb-3">
                    <!-- Row 1: Prospector + Book Title -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Prospect Name</label>
                        <input type="text" class="form-control" value="<?= esc($prospect['name']); ?>" readonly>
                        <input type="hidden" name="prospector_id" value="<?= $prospect['id']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Book Title</label>
                        <input type="text" class="form-control" name="title" placeholder="Enter Book Title" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <!-- Row 2: Plan Name + Plan Amount -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Recommended Plan</label>
                        <input type="text" class="form-control" name="plan_name" value="<?= esc($prospect['recommended_plan']); ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Plan Amount</label>
                        <input type="text" class="form-control" name="plan_cost" 
                         value="<?= esc($prospect['plan_cost'] ?? ''); ?>" readonly>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <!-- Row 3: Payment Status + Amount + Date -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Payment Status</label>
                        <select class="form-select" name="payment_status" required>
                            <option value="">Select Status</option>
                            <option value="Paid">Paid</option>
                            <option value="Partial">Partial</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Payment Amount</label>
                        <input type="number" class="form-control" name="payment_amount" step="0.01" 
                               placeholder="Enter Amount" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Payment Date</label>
                        <input type="date" class="form-control" name="payment_date" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <!-- Row 4: Payment Description + Remarks -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Payment Description</label>
                        <textarea class="form-control" name="payment_description" rows="3" placeholder="Enter Payment Description"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Remarks</label>
                        <textarea class="form-control" name="remarks" rows="3" placeholder="Enter Remarks (if any)"></textarea>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success px-4">Save</button>
                    <a href="<?= base_url('prospectivemanagement/dashboard'); ?>" class="btn btn-danger px-4">Cancel</a>
                </div>

            </form>

        </div>
    </div>

</div>

<?= $this->endSection(); ?>
