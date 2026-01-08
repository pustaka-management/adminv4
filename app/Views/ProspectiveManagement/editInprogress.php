<?= $this->extend('layout/layout1'); ?>

<?= $this->section('script'); ?>
<script>
$(document).ready(function () {

    // Toggle date fields
    function toggleDateFields() {
        if ($("input[name='email_sent_flag']:checked").val() == "1") {
            $("#emailSentDate").removeClass("d-none");
        } else {
            $("#emailSentDate").addClass("d-none");
            $("#email_sent_date").val("");
        }

        if ($("input[name='initial_call_flag']:checked").val() == "1") {
            $("#initialCallDate").removeClass("d-none");
        } else {
            $("#initialCallDate").addClass("d-none");
            $("#initial_call_date").val("");
        }
    }
    $("input[name='email_sent_flag'], input[name='initial_call_flag']").change(toggleDateFields);
    toggleDateFields();

    // Accept / Reject / Hold buttons AJAX
    // Accept / Reject / Hold buttons AJAX
$(".actionBtn").click(function () {
    let status = $(this).data("status");
    let actionText = status == 1 ? "accept" : (status == 2 ? "deny" : "hold");

    if (!confirm(`Are you sure you want to ${actionText} this prospect?`)) return;

    let formData = $("#prospectForm").serialize() + "&prospectors_status=" + status;

    $.ajax({
        url: "<?= base_url('prospectivemanagement/updateinprogress/' . $prospect['id']); ?>",
        type: "POST",
        data: formData,
        dataType: "json",
        success: function (response) {
            alert(response.message);

            if (response.success) {
                if (status == 1 || status == 2) {
                    // Accept / Reject → redirect to Add Book
                    setTimeout(() => {
                        window.location.href = "<?= base_url('prospectivemanagement/addProspectBook/' . $prospect['id']); ?>";
                    }, 1000);
                } else if (status == 3) {
                    // Hold → redirect to Dashboard
                    setTimeout(() => {
                        window.location.href = "<?= base_url('prospectivemanagement/dashboard'); ?>";
                    }, 1000);
                }
            }
        },
        error: function (xhr) {
            alert("Server Error: " + xhr.statusText);
        }
    });
});


});
</script>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary mb-0">
            <i class="fa fa-user-edit me-2"></i> Edit Prospect
        </h4>
        <a href="<?= base_url('prospectivemanagement/dashboard'); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <form id="prospectForm" method="post" action="<?= base_url('prospectivemanagement/updateprospector/' . $prospect['id']); ?>">
        <?= csrf_field() ?>

        <!-- Basic Info -->
        <div class="card mb-3">
            <div class="card-header fw-semibold py-2">Prospect Basic Information</div>
            <div class="card-body py-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" value="<?= esc($prospect['name'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Phone *</label>
                        <input type="text" name="phone" class="form-control" value="<?= esc($prospect['phone'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= esc($prospect['email'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Source of Reference</label>
                        <input type="text" name="source_of_reference" class="form-control" value="<?= esc($prospect['source_of_reference'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Author Status</label>
                        <input type="text" name="author_status" class="form-control" value="<?= esc($prospect['author_status'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Communication & Plan -->
        <div class="card mb-3">
            <div class="card-header fw-semibold py-2">Communication & Recommended Plan</div>
            <div class="card-body py-3">
                <div class="row g-3 align-items-center">

                    <!-- Email Sent -->
                    <div class="col-md-4">
                        <label class="form-label d-block">Email Sent</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="email_sent_flag" value="1" <?= ($prospect['email_sent_flag'] ?? 0) == 1 ? 'checked' : '' ?>>
                            <label class="form-check-label">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="email_sent_flag" value="0" <?= ($prospect['email_sent_flag'] ?? 0) == 0 ? 'checked' : '' ?>>
                            <label class="form-check-label">No</label>
                        </div>

                        <div id="emailSentDate" class="mt-2 <?= ($prospect['email_sent_flag'] ?? 0) == 1 ? '' : 'd-none' ?>">
                            <input type="date" name="email_sent_date" class="form-control mb-1" value="<?= !empty($prospect['email_sent_date']) ? date('Y-m-d', strtotime($prospect['email_sent_date'])) : '' ?>">
                        </div>
                    </div>

                    <!-- Initial Call -->
                    <div class="col-md-4">
                        <label class="form-label d-block">Initial Call</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="initial_call_flag" value="1" <?= ($prospect['initial_call_flag'] ?? 0) == 1 ? 'checked' : '' ?>>
                            <label class="form-check-label">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="initial_call_flag" value="0" <?= ($prospect['initial_call_flag'] ?? 0) == 0 ? 'checked' : '' ?>>
                            <label class="form-check-label">No</label>
                        </div>

                        <div id="initialCallDate" class="mt-2 <?= ($prospect['initial_call_flag'] ?? 0) == 1 ? '' : 'd-none' ?>">
                            <input type="date" name="initial_call_date" class="form-control" value="<?= !empty($prospect['initial_call_date']) ? date('Y-m-d', strtotime($prospect['initial_call_date'])) : '' ?>">
                        </div>
                    </div>

                    <!-- Recommended Plan -->
                    <div class="col-md-4">
                        <label class="form-label">Recommended Plan</label>
                        <select name="recommended_plan" class="form-select">
                            <option value="">-- Select Plan --</option>
                            <?php foreach (['Silver','Gold','Platinum','Rhodium','Silver++','Pearl','Sapphire'] as $plan): ?>
                                <option value="<?= $plan ?>" <?= ($prospect['recommended_plan'] ?? '') == $plan ? 'selected' : '' ?>><?= $plan ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Remarks -->
                    <div class="col-md-12">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2"><?= esc($prospect['remarks'] ?? '') ?></textarea>
                    </div>

                </div>
            </div>
        </div>

        <!-- Latest Remark -->
        <div class="card mb-3">
            <div class="card-header py-2">Latest Remark</div>
            <div class="card-body py-3">
                <?php if (!empty($latestRemark)): ?>
                    <p><strong>Remark:</strong> <?= esc($latestRemark['remarks']) ?></p>
                    <p class="mb-1"><strong>Date:</strong> <?= date('d-m-Y', strtotime($latestRemark['create_date'])) ?></p>
                    <p class="mb-0"><strong>Added By:</strong> <?= esc($latestRemark['created_by']) ?></p>
                <?php else: ?>
                    <p class="mb-0">No remarks available yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Buttons -->
        <div class="text-center mt-4">
            <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
                <!-- Update Prospect -->
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa fa-save me-2"></i> Update Prospect
                </button>

                <!-- Accept -->
                <button type="button" class="btn btn-success actionBtn px-4" data-status="1">
                    <i class="fa fa-check me-2"></i> Accept & Add Book
                </button>

                <!-- Reject -->
                <button type="button" class="btn btn-danger actionBtn px-4" data-status="2">
                    <i class="fa fa-times me-2"></i> Reject & Denied
                </button>

                <!-- Hold -->
                <button type="button" class="btn btn-warning actionBtn px-4" data-status="3">
                    <i class="fa fa-pause me-2"></i> Hold
                </button>
            </div>
        </div>

    </form>
</div>
<?= $this->endSection(); ?>
