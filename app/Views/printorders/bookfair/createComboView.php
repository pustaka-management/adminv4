<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container my-5" style="max-width: 900px;">
    <!-- Page Header -->
    <div class="text-center mb-4">
        <h4 class="fw-bold">Create Combo Pack</h4>
        <p class="text-muted mb-0">
            Create Combo Pack using manual entry or Excel upload
        </p>
    </div>

    <!-- Alerts -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Sample Excel Format (Shown only for Excel Upload) -->
    <div class="card border-0 shadow-sm mb-4 d-none" id="sampleExcelSection">
        <div class="card-body text-center">
            <h6 class="fw-semibold mb-3">Sample Excel Format</h6>
            <img src="<?= base_url('assets/images/bulk-stock-sample.png') ?>"
                 class="img-fluid rounded shadow-sm"
                 style="max-width: 650px"
                 alt="Sample Excel Format">
            <p class="text-muted small mt-2 mb-0">
                Follow this format strictly to avoid upload errors
            </p>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="<?= base_url('combobookfair/upload'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>

                 <div class="mb-3" style="">
        <label class="form-label fw-semibold">
            Combo Pack Name <span class="text-danger">*</span>
        </label>
        <input
            type="text"
            name="combo_pack_name"
            class="form-control"
            placeholder="Enter combo pack name"
            required
        >
    </div>
                <!-- Upload Method -->
                <div class="mb-4">
                    <label class="fw-semibold mb-2 d-block">Choose Upload Method</label>

                    <div class="row g-3">
                        <!-- Manual -->
                        <div class="col-md-6">
                            <label class="w-100">
                                <input type="radio" class="d-none" name="upload_type"
                                       id="manualOption" value="manual" checked>
                                <div class="border rounded p-3 upload-card active" id="manualCard">
                                    <h6 class="mb-1 d-flex align-items-center gap-2">
                                        <iconify-icon icon="streamline-ultimate:paper-write"></iconify-icon>
                                        Manual Entry
                                    </h6>
                                    <small class="text-muted">Quick update for few books</small>
                                </div>
                            </label>
                        </div>

                        <!-- Excel -->
                        <div class="col-md-6">
                            <label class="w-100">
                                <input type="radio" class="d-none" name="upload_type"
                                       id="excelOption" value="excel">
                                <div class="border rounded p-3 upload-card" id="excelCard">
                                    <h6 class="mb-1 d-flex align-items-center gap-2">
                                        <iconify-icon icon="vscode-icons:file-type-excel2"></iconify-icon>
                                        Excel Upload
                                    </h6>
                                    <small class="text-muted">Bulk stock update</small>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Manual Entry Section -->
                <div id="manualSection">
                    <label class="fw-semibold mb-1">Stock Data</label>
                    <textarea
                        name="manual_stock"
                        class="form-control"
                        rows="4"
                        placeholder="101-5, 102-10, 205-3"></textarea>
                    <small class="text-muted">
                        Format: <b>bookId-quantity</b> separated by commas
                    </small>
                </div>

                <!-- Excel Upload Section -->
                <div id="excelSection" class="d-none">
                    <label class="fw-semibold mb-1">Upload Excel File</label>
                    <input type="file" name="excel_file" class="form-control">
                    <small class="text-muted">
                        Required columns: <b>book_id, qty_add</b>, qty_lost (optional)
                    </small>
                </div>

                <!-- Submit -->
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                      Upload & Process
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Styles -->
<style>
.upload-card {
    cursor: pointer;
    transition: all 0.2s ease;
}
.upload-card:hover {
    border-color: #0d6efd;
}
.upload-card.active {
    border-color: #0d6efd;
    background-color: #f4f8ff;
}
</style>

<!-- Toggle Script -->
<script>
const manualOption  = document.getElementById('manualOption');
const excelOption   = document.getElementById('excelOption');
const manualSection = document.getElementById('manualSection');
const excelSection  = document.getElementById('excelSection');
const sampleExcel   = document.getElementById('sampleExcelSection');
const manualCard    = document.getElementById('manualCard');
const excelCard     = document.getElementById('excelCard');

manualOption.addEventListener('change', () => {
    manualSection.classList.remove('d-none');
    excelSection.classList.add('d-none');
    sampleExcel.classList.add('d-none');

    manualCard.classList.add('active');
    excelCard.classList.remove('active');
});

excelOption.addEventListener('change', () => {
    excelSection.classList.remove('d-none');
    manualSection.classList.add('d-none');
    sampleExcel.classList.remove('d-none');

    excelCard.classList.add('active');
    manualCard.classList.remove('active');
});
</script>

<?= $this->endSection(); ?>
