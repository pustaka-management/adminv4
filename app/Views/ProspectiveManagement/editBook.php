<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container py-4">
    <h6 class="fw-bold mb-4">Edit Book Details</h6>

    <form method="post" action="<?= base_url('prospectivemanagement/updatebook/' . $book['prospector_id'] . '/' . $book['id']); ?>">

        <input type="hidden" name="book_id" value="<?= $book['id']; ?>">
        <input type="hidden" name="prospector_id" value="<?= $book['prospector_id']; ?>">

        <!-- Row 1: Prospector Name + Book Title -->
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Prospect Name</label>
                <input type="text" class="form-control" value="<?= esc($book['prospector_name']); ?>" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Book Title</label>
                <input type="text" name="title" class="form-control" value="<?= esc($book['title']); ?>" required>
            </div>
        </div>

        <!-- Row 2: Plan Name + Plan Amount -->
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Plan Name</label>
                <select name="plan_name" id="plan_name" class="form-select" required>
                    <option value="">Select Plan</option>
                    <?php foreach ($plans as $p): ?>
                        <option value="<?= esc($p['plan_name']); ?>" 
                            data-cost="<?= esc($p['cost']); ?>"
                            <?= (isset($book['plan_name']) && strcasecmp($book['plan_name'], $p['plan_name']) === 0) ? 'selected' : '' ?> >
                            <?= esc($p['plan_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Plan Amount (₹)</label>
                <input type="number" name="plan_cost" id="plan_cost" class="form-control" step="0.01" 
                       value="<?= esc($book['plan_cost'] ?? ''); ?>" readonly required>
            </div>
        </div>

        <!-- Row 3: Payment Amount + Payment Date -->
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Payment Status</label>
                <select name="payment_status" class="form-select">
                    <?php 
                    $statuses = ['Paid', 'Partial']; 
                    foreach ($statuses as $status): ?>
                        <option value="<?= $status ?>" <?= strcasecmp($book['payment_status'], $status) === 0 ? 'selected' : '' ?>>
                            <?= $status ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Payment Amount (₹)</label>
                 <input type="number" name="payment_amount" step="0.01" 
           class="form-control">

    <!-- LABEL BELOW THE TEXTBOX -->
    <small class="d-block mt-1">
       Already Paid: ₹<?= esc($book['payment_amount'] ?? ''); ?>
    </small>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Payment Date</label>
                <input type="date" name="payment_date" class="form-control" 
                    value="<?= !empty($book['payment_date']) ? date('Y-m-d', strtotime($book['payment_date'])) : ''; ?>" required>
            </div>
        </div>

        <!-- Row 4: Payment Description + Remarks -->
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Payment Description</label>
                <textarea class="form-control" name="payment_description" rows="3" placeholder="Enter Payment Description"><?= esc($book['payment_description'] ?? ''); ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Remarks</label>
                <textarea class="form-control" name="remarks" rows="3" placeholder="Enter Remarks (if any)"><?= esc($book['remarks'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-success px-4">Update</button>
            <a href="<?= base_url('prospectivemanagement/dashboard'); ?>" class="btn btn-danger px-4">Cancel</a>
        </div>

    </form>
</div>

<?= $this->endSection(); ?>
<?= $this->section('script'); ?>
<script>
    // Auto-fill plan amount when plan changes
    const planSelect = document.getElementById('plan_name');
    const planAmountInput = document.getElementById('plan_cost');

    planSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const cost = selectedOption.dataset.cost || '';
        planAmountInput.value = cost;
    });
</script>
<?= $this->endSection(); ?>
