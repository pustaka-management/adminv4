<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h6 class="fw-bold mb-0">Add Book Details</h6>
        <a href="<?= base_url('prospectivemanagement'); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <form method="post" action="<?= base_url('prospectivemanagement/savebookdetails'); ?>">

                <!-- Row 1: Prospector + Book Title -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Prospect Name</label>
                        <select class="form-select" name="prospector_id" required>
                            <option value="">Select Prospect</option>
                            <?php foreach ($prospectors as $p): ?>
                                <option value="<?= $p['id']; ?>"><?= esc($p['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Book Title</label>
                        <input type="text" class="form-control" name="title" placeholder="Enter Book Title" required>
                    </div>
                </div>

                <!-- Row 2: Plan Name + Plan Amount -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Plan Name</label>
                        <select class="form-select" id="plan_name" name="plan_name" required>
                            <option value="">Select Plan</option>
                            <?php foreach ($plans as $plan): ?>
                                <option value="<?= esc($plan['plan_name']); ?>" data-amount="<?= esc($plan['cost']); ?>">
                                    <?= esc($plan['plan_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Plan Amount</label>
                        <input type="text" id="cost" class="form-control" readonly placeholder="Plan Amount">
                    </div>
                </div>

                <!-- Row 3: Payment Status + Amount + Date -->
                <div class="row g-3 mb-3">
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
                        <input type="number" class="form-control" name="payment_amount" step="0.01" placeholder="Enter Amount" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Payment Date</label>
                        <input type="date" class="form-control" name="payment_date" required>
                    </div>
                </div>

                <!-- Row 4: Payment Description + Remarks -->
                <div class="row g-3 mb-3">
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
                    <a href="<?= base_url('prospectors/inprogress'); ?>" class="btn btn-danger px-4">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
    // Auto-fill Plan Amount when a plan is selected
    const planSelect = document.getElementById('plan_name');
    const planAmountInput = document.getElementById('cost');

    planSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const amount = selectedOption.dataset.amount || '';
        planAmountInput.value = amount;
    });

    document.querySelector('form').addEventListener('submit', function(e){
    const title = document.querySelector('[name="title"]').value.trim();
    const planName = document.querySelector('[name="plan_name"]').value.trim();
    const planCost = document.querySelector('[name="plan_cost"]').value.trim();
    const paymentStatus = document.querySelector('[name="payment_status"]').value.trim();
    const paymentAmount = document.querySelector('[name="payment_amount"]').value.trim();
    const paymentDate = document.querySelector('[name="payment_date"]').value.trim();

    if(!title || !planName || !planCost || !paymentStatus || !paymentAmount || !paymentDate){
        alert('Please fill all required fields before submitting.');
        e.preventDefault(); // prevent form submission
    }
});
</script>
<?= $this->endSection(); ?>
