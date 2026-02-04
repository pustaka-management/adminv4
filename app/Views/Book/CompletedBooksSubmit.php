<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="row">

            <!-- Left Column -->
            <div class="col-md-6">
                <form id="bookForm">

                    <div class="form-group mb-3">
                        <label for="book_id">Book ID & Title</label>
                        <input type="text" class="form-control" id="book_id" name="book_id"
                               value="<?= esc($completed['book_id'].' - '.$completed['book_title']) ?>" readonly>
                    </div>

                    <div class="form-group mb-3">
                        <label for="pages">No. of Pages</label>
                        <input type="number" class="form-control" id="pages" name="pages"
                               value="<?= esc($completed['paper_back_pages']) ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="price">Book Price (INR)</label>
                        <input type="number" class="form-control" id="price" name="price"
                               value="<?= esc($completed['paper_back_inr']) ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Rate Per Page</label>
                        <select id="rate_per_page" class="form-control">
                            <option value="">Select</option>
                            <?php for ($i = 11; $i <= 19; $i++):
                                $v = number_format($i / 10, 1); ?>
                                <option value="<?= $v ?>" <?= (($completed['rate_per_page'] ?? '') == $v) ? 'selected' : '' ?>>
                                    <?= $v ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="isbn">ISBN Number</label>
                        <input type="text" class="form-control" id="isbn" name="isbn"
                               value="<?= esc($completed['paper_back_isbn']) ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="royalty">Royalty Value (%)</label>
                        <input type="number" class="form-control" id="royalty" name="royalty"
                               value="<?= esc($completed['paper_back_royalty']) ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="copyright_owner">Copyright Owner</label>
                        <input type="text" class="form-control" id="copyright_owner" name="copyright_owner"
                               value="<?= esc($completed['paper_back_copyright_owner']) ?>" required>
                    </div>

                </form>
            </div>

            <!-- Right Column -->
            <div class="col-md-6">
                <form>

                    <div class="form-group mb-3">
                        <label for="paper_back_desc">Book Description</label>
                        <textarea id="paper_back_desc" rows="6" class="form-control"><?= esc($completed['paper_back_desc']) ?></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="paper_back_author_desc">Author Description</label>
                        <textarea id="paper_back_author_desc" rows="6" class="form-control"><?= esc($completed['paper_back_author_desc']) ?></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label>Price Remarks</label>
                        <textarea id="rate_remarks" class="form-control"></textarea>

                        <small>Previous Remarks</small>
                        <textarea class="form-control" readonly><?= $completed['rate_remarks'] ?? ''; ?></textarea>
                    </div>

                </form>
            </div>

        </div>

        <!-- Action Buttons -->
        <div class="text-center mt-4">
            <button type="button" class="btn btn-success me-2" onclick="mark_completed()">Submit</button>
            <a href="<?= base_url('book/paperbackdashboard') ?>" class="btn btn-danger">Close</a>
        </div>

    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>

var base_url = window.location.origin;

function mark_completed() {

    var book_id = document.getElementById('book_id').value;
    var pages = document.getElementById('pages').value;
    var price = document.getElementById('price').value;
    var isbn = document.getElementById('isbn').value;
    var royalty = document.getElementById('royalty').value;
    var copyright_owner = document.getElementById('copyright_owner').value;
    var paper_back_desc = document.getElementById('paper_back_desc').value;
    var paper_back_author_desc = document.getElementById('paper_back_author_desc').value;
    var rate_per_page = document.getElementById('rate_per_page').value;
    var rate_remarks = document.getElementById('rate_remarks').value;

    $.ajax({
        url: base_url + '/book/indesignmarkcompleted',
        type: 'POST',
        data: {
            book_id: book_id,
            pages: pages,
            price: price,
            isbn: isbn,
            royalty: royalty,
            copyright_owner: copyright_owner,
            paper_back_desc: paper_back_desc,
            paper_back_author_desc: paper_back_author_desc,
            rate_per_page: rate_per_page,
            rate_remarks: rate_remarks
        },
        success: function(data) {
            if (data == 1) {
                alert("Successfully added!!");
                location.href="<?= base_url('book/paperbackdashboard') ?>";
            } else {
                alert("Error");
            }
        }
    });

}

</script>
<?= $this->endSection(); ?>
