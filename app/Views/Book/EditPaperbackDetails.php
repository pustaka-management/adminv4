<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
  <div class="page-header">
    <div class="page-title">
      <h6>
        Edit Paperback Details - <?= esc($book_details['book_title']) ?> (<?= esc($book_details['book_id']) ?>)
      </h6>
      <h6>Current State - <?= ($book_details['status'] == 0) ? "Inactive" : "Active"; ?></h6>
    </div>
  </div>

  <div class="row">
    <!-- Left column -->
    <div class="col-md-6">

      <!-- Paperback Agreement -->
   <div class="mb-3">
    <label class="form-label d-block">Paperback Enabled?</label>
    <div class="d-flex align-items-center flex-wrap gap-3">
        <div class="form-check checked-primary d-flex align-items-center gap-2">
            <input class="form-check-input" type="radio" name="paper_back_flag" id="paper_back_yes" value="1" 
                <?= ($book_details['paper_back_flag'] == 1) ? 'checked' : '' ?>>
            <label class="form-check-label fw-medium text-secondary-light" for="paper_back_yes">Yes</label>
        </div>
        <div class="form-check checked-warning d-flex align-items-center gap-2">
            <input class="form-check-input" type="radio" name="paper_back_flag" id="paper_back_no" value="0"
                <?= ($book_details['paper_back_flag'] == 0) ? 'checked' : '' ?>>
            <label class="form-check-label fw-medium text-secondary-light" for="paper_back_no">No</label>
        </div>
    </div>
</div>

    <div class="mb-3">
        <label class="form-label d-block">Agreement Signed?</label>
        <div class="d-flex align-items-center flex-wrap gap-3">
            <div class="form-check checked-primary d-flex align-items-center gap-2">
                <input class="form-check-input" type="radio" name="paper_back_agreement_flag" id="agreement_yes" value="1" 
                    <?= ($book_details['paper_back_agreement_flag'] == 1) ? 'checked' : '' ?>>
                <label class="form-check-label fw-medium text-secondary-light" for="agreement_yes">Yes</label>
            </div>
            <div class="form-check checked-warning d-flex align-items-center gap-2">
                <input class="form-check-input" type="radio" name="paper_back_agreement_flag" id="agreement_no" value="0"
                    <?= ($book_details['paper_back_agreement_flag'] == 0) ? 'checked' : '' ?>>
                <label class="form-check-label fw-medium text-secondary-light" for="agreement_no">No</label>
            </div>
        </div>
    </div>


      <!-- Royalty -->
      <div class="mb-3">
        <label for="paper_back_royalty" class="form-label">Paperback Royalty</label>
        <input type="text" id="paper_back_royalty" name="paper_back_royalty" 
               value="<?= esc($book_details['paper_back_royalty']) ?>" 
               class="form-control">
      </div>

      <!-- Copyright Owner -->
      <div class="mb-3">
        <label for="paper_back_copyright_owner" class="form-label">Paperback Copyright Owner</label>
        <p class="text-muted small">
          Current Owner: <strong><?= esc($book_details['copyright_owner']); ?>.</strong>
          Change only if needed else keep the same copyright owner id.
        </p>
        <input type="text" id="paper_back_copyright_owner" name="paper_back_copyright_owner"
               value="<?= esc($book_details['paper_back_copyright_owner']) ?>" 
               class="form-control">
      </div>

      <!-- ISBN -->
      <div class="mb-3">
        <label for="paper_back_isbn" class="form-label">Paperback ISBN Number</label>
        <input type="text" id="paper_back_isbn" name="paper_back_isbn" 
               value="<?= esc($book_details['paper_back_isbn']) ?>" 
               class="form-control">
      </div>

      <!-- Cost -->
      <div class="mb-3">
        <label for="paper_back_inr" class="form-label">Paperback Cost</label>
        <input type="text" id="paper_back_inr" name="paper_back_inr" 
               value="<?= esc($book_details['paper_back_inr']) ?>" 
               class="form-control">
      </div>

      <!-- Pages -->
      <div class="mb-3">
        <label for="paper_back_pages" class="form-label">No. of Pages</label>
        <input type="text" id="paper_back_pages" name="paper_back_pages" 
               value="<?= esc($book_details['paper_back_pages']) ?>" 
               class="form-control">
      </div>
      
      <div class="mb-3">
        <label>Rate Per Page</label>
        <select id="rate_per_page" class="form-control">
        <option value="">Select</option>
        <?php
        $sel=$book_details['rate_per_page']??'';
        for($i=1.1;$i<=1.9;$i+=0.1){
        $v=number_format($i,1);
        ?>
        <option value="<?= $v ?>" <?=($sel==$v)?'selected':''?>><?= $v ?></option>
        <?php } ?>
        </select>
      </div>

      <!-- Paperback Readiness -->
<div class="mb-3">
    <label class="form-label d-block">Paperback Readiness (InDesign)</label>
    <div class="d-flex align-items-center flex-wrap gap-3">
        <div class="form-check checked-success d-flex align-items-center gap-2">
            <input class="form-check-input" type="radio" name="paper_back_readiness_flag" id="readiness_yes" value="1"
                <?= ($book_details['paper_back_readiness_flag'] == 1) ? 'checked' : '' ?>>
            <label class="form-check-label fw-medium text-secondary-light" for="readiness_yes">Yes</label>
        </div>
        <div class="form-check checked-danger d-flex align-items-center gap-2">
            <input class="form-check-input" type="radio" name="paper_back_readiness_flag" id="readiness_no" value="0"
                <?= ($book_details['paper_back_readiness_flag'] == 0) ? 'checked' : '' ?>>
            <label class="form-check-label fw-medium text-secondary-light" for="readiness_no">No</label>
        </div>
    </div>
</div>

      <!-- Weight -->
      <div class="mb-3">
        <label for="paper_back_weight" class="form-label">Paperback Weight (Per book)</label>
        <input type="text" id="paper_back_weight" name="paper_back_weight"
               value="<?= esc($book_details['paper_back_weight']) ?>" 
               class="form-control">
      </div>

      <!-- Remarks -->
      <div class="mb-3">
        <label for="paper_back_remarks" class="form-label">Remarks</label>
        <textarea id="paper_back_remarks" name="paper_back_remarks" rows="4" 
                  class="form-control" oninput="count_chars()"><?= esc($book_details['paper_back_remarks']); ?></textarea>
        <small class="form-text text-muted">Characters: <span id="num_chars">0</span></small>
      </div>
    </div>
    

    <!-- Right column -->
    <div class="col-md-6">
      <!-- NEW PRICE REMARKS -->
<div class="mb-3">
<label>New Price Remarks</label>
<textarea id="rate_remarks" class="form-control"></textarea>
</div>

<div class="mb-3">
<small>Previous Price Remarks</small>
<textarea class="form-control" readonly><?= esc($book_details['rate_remarks'] ?? '') ?></textarea>
</div>

      <?php if ($book_details['paper_back_flag'] == 1): ?>
        <div class="mb-3">
          <label for="paper_back_desc" class="form-label">Book Description</label>
          <textarea id="paper_back_desc" rows="6" class="form-control" 
                    placeholder="Paperback BackCover Description"><?= esc($book_details['paper_back_desc']) ?></textarea>
        </div>

        <div class="mb-3">
          <label for="paper_back_author_desc" class="form-label">Author Description</label>
          <textarea id="paper_back_author_desc" rows="6" class="form-control" 
                    placeholder="Paperback BackCover Author Description"><?= esc($book_details['paper_back_author_desc']) ?></textarea>
        </div>
      <?php else: ?>
        <input type="hidden" id="paper_back_desc" value="">
        <input type="hidden" id="paper_back_author_desc" value="">
      <?php endif; ?>
    </div>

    <!-- Submit button -->
    <div class="col-12 mt-4">
      <button onclick="edit_paperback_details()" class="btn btn-primary btn-lg">Save Changes</button>
    </div>
  </div>
</div>

<script>
var base_url = "<?= base_url() ?>";

function edit_paperback_details() {
  var payload = {
    book_id: <?= (int)$book_details['book_id'] ?>,
    paper_back_flag: document.querySelector('input[name="paper_back_flag"]:checked').value,
    paper_back_agreement_flag: document.querySelector('input[name="paper_back_agreement_flag"]:checked').value,
    paper_back_royalty: document.getElementById('paper_back_royalty').value,
    paper_back_copyright_owner: document.getElementById('paper_back_copyright_owner').value,
    paper_back_isbn: document.getElementById('paper_back_isbn').value,
    paper_back_inr: document.getElementById('paper_back_inr').value,
    paper_back_pages: document.getElementById('paper_back_pages').value,
    paper_back_readiness_flag: document.querySelector('input[name="paper_back_readiness_flag"]:checked').value,
    paper_back_weight: document.getElementById('paper_back_weight').value,
    paper_back_remarks: document.getElementById('paper_back_remarks').value,
    paper_back_desc: document.getElementById('paper_back_desc').value,
    paper_back_author_desc: document.getElementById('paper_back_author_desc').value,
    rate_per_page: document.getElementById('rate_per_page').value,
    rate_remarks: document.getElementById('rate_remarks').value,
  };

  $.post(base_url + '/book/editbookpaperbackdetailspost', payload, function(data) {
    if (data == 1 || data === '1') {
      alert("Edited Book Details Successfully!");
      location.reload();
    } else {
      alert("Error Occurred!");
    }
  }).fail(function(xhr) {
    alert("Error: " + (xhr.responseText || "Unexpected error"));
  });
}

function count_chars() {
  var num_chars = document.getElementById('paper_back_remarks').value.length;
  document.getElementById('num_chars').textContent = num_chars;
}
</script>

<?= $this->endSection(); ?>
