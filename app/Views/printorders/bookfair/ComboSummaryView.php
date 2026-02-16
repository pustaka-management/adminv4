<?= $this->extend('layout/layout1'); ?>

<?= $this->section('script'); ?>
<style>
    /* Dark border */
    #selectAll.form-check-input {
        border-color: #444 !important;
    }

    /* Dark fill when checked */
    #selectAll.form-check-input:checked {
        background-color: #444 !important;
        border-color: #444 !important;
    }
</style>

<script>
// ===============================
// Toggle Section Open / Close
// ===============================
function toggleSection(sectionId, btn) {
    const section = document.getElementById(sectionId);

    if (section.style.display === "none" || section.style.display === "") {
        section.style.display = "block";
        btn.innerHTML = "Hide Matched Books ▲";
    } else {
        section.style.display = "none";
        btn.innerHTML = "View Matched Books ▼";
    }
}

// ===============================
// Select/Deselect all checkboxes
// ===============================
$("#selectAll").on("change", function() {
    const isChecked = $(this).prop("checked");
    $(".book-item .form-check-input").prop("checked", isChecked);
    $(".book-item").toggleClass("active", isChecked);

    const selectedCount = $(".book-item .form-check-input:checked").length;
    $(".move-button").toggleClass("d-none", selectedCount === 0);
});

// Individual checkbox toggle
$(document).on("change", ".book-item .form-check-input", function() {
    $(this).closest(".book-item").toggleClass("active", $(this).is(":checked"));

    const selectedCount = $(".book-item .form-check-input:checked").length;
    $(".move-button").toggleClass("d-none", selectedCount === 0);
});
</script>

<?= $this->endSection(); ?>



<?= $this->section('content'); ?> 

<div class="row gy-4">
    <div class="col-xxl-12">     
        <div class="d-flex justify-content-between align-items-center mb-4">
         <div class="d-flex align-items-center justify-content-center">
            <h5 class="fw-bold mb-0 text-center">Combo Pack Summary View :  <?= esc($combo_pack_name) ?></h5> 
        </div>


            <a href="<?= base_url('combobookfair/createcombo'); ?>" class="btn btn-outline-secondary btn-sm d-flex align-items-center shadow-sm">
                <iconify-icon icon="mdi:arrow-left" class="me-1 fs-5"></iconify-icon> Back
            </a>
        </div>
         <br>
         <div class="d-flex align-items-center gap-3">
            <div class="form-check style-check d-flex align-items-center">
                <input class="form-check-input radius-4 border" type="checkbox" id="selectAll">
                <label for="selectAll" class="ms-2 fw-medium">Select All</label>
            </div>

            <button type="submit" form="mismatchForm" class="move-button d-none btn btn-success text-sm px-3 py-1">
                Move Selected to Matched
            </button>
        </div>
        </div>


        <div class="card-body p-0">

            <?php if (!empty($totals)): ?>
            <div class="p-3 bg-light border-bottom">
                <h5 class="fw-bold">Summary</h5>
                <p><strong>Total Titles:</strong> <?= esc($totals['titles']) ?></p>
                <p><strong>Total Quantity:</strong> <?= esc($totals['quantity']) ?></p>
                <p><strong>Total Amount:</strong> ₹<?= number_format($totals['amount'], 2) ?></p>
            </div>
            <?php endif; ?>


            <?php if (!empty($mismatched)): ?>
            <h6 class=" mt-3 text-danger"> Mismatched Books</h6>

            <form id="mismatchForm" action="<?= base_url('combobookfair/updateAcceptBooks') ?>" method="post">
                  <input type="hidden" name="combo_pack_name" value="<?= esc($combo_pack_name) ?>">

                <table class="table table-striped mb-0">
                    <thead class="bg-base">
                        <tr>
                            <th>Select</th>
                            <th>Book ID</th>
                            <th>Excel Title</th>
                            <th>Database Title (Editable)</th>
                            <th>Quantity</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mismatched as $m): ?>
                        <tr class="book-item">
                            <td>
                                <div class="form-check style-check">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        name="selected[]"
                                        value="<?= esc($m['book_id']) ?>">
                                </div>
                            </td>
                            <td><?= esc($m['book_id']) ?></td>
                            <td><?= esc($m['excel_title']) ?></td>

                            <td>
                                <input type="text"
                                    name="book_title[<?= esc($m['book_id']) ?>]"
                                    value="<?= esc($m['db_title']) ?>"
                                    class="form-control form-control-sm"
                                    style="width: 250px;">
                            </td>

                            <td>
                                <input type="number"
                                    name="quantity[<?= esc($m['book_id']) ?>]"
                                    value="<?= esc($m['quantity']) ?>"
                                    class="form-control form-control-sm"
                                    style="width: 80px;">
                            </td>

                           
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>

            <?php else: ?>
            <p class="p-3">No mismatches found</p>
            <?php endif; ?>


            <hr class="my-4">
        </div>

         <br> <br>
        <h6> Total Matched Books Count: <?= $totalTitles ?></h6>
        <!-- Collapsible Button -->
       <button onclick="toggleSection('matchedBody', this)"
            style="width:100%; padding:8px; background:#6c757d; border:none;
                text-align:left; cursor:pointer; font-weight:600; color:#fff;">
            View Matched Books ▼
        </button>


        <!-- Collapsed Section -->
        <div id="matchedBody" style="display:none; margin-top:10px;">

            <?php    session()->set('accept_books', $matched);
            if (!empty($matched)): ?>
                
            <table class="table table-bordered table-striped table-hover zero-pagination px-4">
                <thead class="bg-base">
                    <tr>
                        <th># </th>
                        <th>Book ID</th>
                        <th>Title</th>
                        <th>Quantity</th>
                       
                    </tr>
                </thead>

                <tbody>
                    <?php 
                    $i =1;
                    foreach ($matched as $b): ?>
                    <tr>
                       <td><?= esc($i++) ?></td>
                        <td><?= esc($b['book_id']) ?></td>
                        <td><?= esc($b['title']) ?></td>
                        <td><?= esc($b['quantity']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php else: ?>
            <p class="p-3">No matched books yet.</p>
            <?php endif; ?>

        </div>


        <!-- Button is always visible -->
        <?php if (!empty($matched)): ?>
        <form action="<?= base_url('combobookfair/combopackupload') ?>" method="post" class="px-4 pb-4">
                <input type="hidden" name="combo_pack_name" value="<?= esc($combo_pack_name) ?>">


            <button type="submit" class="btn btn-primary mt-3">
                Create Combo Pack
            </button>
        </form>
        <?php endif; ?>
        </div>

    </div>
</div>

<?= $this->endSection(); ?>
