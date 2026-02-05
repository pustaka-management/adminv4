<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid py-4">

    <div class="col-12 col-lg-12">

        <!-- HEADER -->
        <div class="bg-gradient-primary rounded-2 p-3 mb-4 shadow-sm text-white text-center">
            <h4 class="fw-bold mb-1">
                <i class="fa fa-book me-2"></i>New Bookfair Order
            </h4>
            <p class="mb-0">Create Sale / Return Order</p>
        </div>

        <!-- FORM CARD -->
        <div class="card shadow-lg border-0">
            <div class="card-body px-5 py-4">

                <!-- FLASH MESSAGES -->
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form method="post"
                      action="<?= base_url('paperback/savesaleorreturnorder') ?>"
                      class="needs-validation"
                      novalidate>

                    <?= csrf_field() ?>

                    <div class="row g-4">

                        <!-- BOOKSHOP -->
                        <div class="col-md-6">
                            <label class="fw-semibold mb-1">Bookshop *</label>
                            <select name="bookshop_id"
                                    id="bookshop_id"
                                    class="form-select"
                                    required>

                                <option value="">Select Bookshop</option>

                                <?php foreach ($bookshops as $b) : ?>
                                    <option value="<?= $b['bookshop_id'] ?>">
                                        <?= esc($b['bookshop_name']) ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <!-- BOOK FAIR -->
                        <div class="col-md-6">
                            <label class="fw-semibold mb-1">Book Fair Name</label>
                            <input type="text"
                                   name="book_fair_name"
                                   class="form-control">
                        </div>

                        <!-- TRANSPORT TYPE -->
                        <div class="col-md-4">
                            <label class="fw-semibold mb-1">Transport Type</label>
                            <input type="text"
                                   id="preferred_transport"
                                   name="preferred_transport"
                                   class="form-control">
                        </div>

                        <!-- TRANSPORT NAME -->
                        <div class="col-md-4">
                            <label class="fw-semibold mb-1">Transport Name</label>
                            <input type="text"
                                   id="preferred_transport_name"
                                   name="preferred_transport_name"
                                   class="form-control">
                        </div>

                        <!-- COMBO -->
                        <div class="col-md-4">
                            <label class="fw-semibold mb-1">Combo Pack *</label>
                            <select name="combo_id"
                                    class="form-select"
                                    required>

                                <option value="">Select Combo</option>

                                <?php foreach ($combos as $c) : ?>
                                    <option value="<?= $c['combo_id'] ?>">
                                        <?= esc($c['pack_name']) ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                    </div>

                    <!-- REMARKS -->
                    <div class="mt-4">
                        <label class="fw-semibold mb-1">Remarks</label>
                        <textarea name="remark"
                                  class="form-control"
                                  rows="3"></textarea>
                    </div>

                    <!-- BUTTONS -->
                    <div class="text-end mt-4">

                        <a href="<?= base_url('paperback/bookfairsaleorreturnview') ?>"
                           class="btn btn-danger me-2">
                            Back
                        </a>

                        <button type="submit"
                                class="btn btn-primary">
                            Save Order
                        </button>

                    </div>

                </form>

            </div>
        </div>

    </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>

$('#bookshop_id').change(function () {

    let id = $(this).val();

    if (id === '') return;

    $.post("<?= base_url('paperback/getBookshopTransport') ?>", {
        bookshop_id: id,
        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
    }, function (res) {

        $('#preferred_transport').val(res.preferred_transport);
        $('#preferred_transport_name').val(res.preferred_transport_name);

    }, 'json');

});

// Bootstrap validation
(() => {

    'use strict';

    const forms = document.querySelectorAll('.needs-validation');

    Array.from(forms).forEach(form => {

        form.addEventListener('submit', event => {

            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');

        }, false);

    });

})();

</script>

<?= $this->endSection(); ?>
