<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- ===================== SUMMARY CARDS ===================== -->
    <div class="row g-3 mb-4">

        <!-- Titles & Authors -->
        <!-- Amazon Titles -->
<div class="col-xxl-3 col-sm-6">
    <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-1">
        <div class="card-body p-0 d-flex align-items-center gap-3">
            <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center">
                <span class="w-40-px h-40-px bg-primary-600 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                    <iconify-icon icon="mdi:book-open-page-variant"></iconify-icon>
                </span>
            </div>
            <div>
                <span class="fw-medium text-secondary-light text-md">Amazon Titles</span>
                <h6 class="fw-semibold my-1"><?= esc($summary['total_titles']) ?></h6>
            </div>
        </div>
    </div>
</div>
<!-- Authors -->
<div class="col-xxl-3 col-sm-6">
    <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-2">
        <div class="card-body p-0 d-flex align-items-center gap-3">
            <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center">
                <span class="w-40-px h-40-px bg-success-600 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                    <iconify-icon icon="mdi:account-multiple"></iconify-icon>
                </span>
            </div>
            <div>
                <span class="fw-medium text-secondary-light text-md">Authors</span>
                <h6 class="fw-semibold my-1"><?= esc($summary['total_authors']) ?></h6>
            </div>
        </div>
    </div>
</div>



        <!-- Units -->
        <!-- <div class="col-xxl-3 col-sm-6">
            <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-2">
                <div class="card-body p-0 d-flex align-items-center gap-3">
                    <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center">
                        <span class="w-40-px h-40-px bg-success-600 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                            <iconify-icon icon="mdi:package-variant-closed"></iconify-icon>
                        </span>
                    </div>
                    <div>
                        <span class="fw-medium text-secondary-light text-md">Units Sold</span>
                        <h6 class="fw-semibold my-1"><?= esc($summary['total_units_sold']) ?></h6>
                        <p class="text-sm mb-0">
                            Refunded : <span class="fw-medium text-danger-main"><?= esc($summary['total_units_refunded']) ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- INR Paid -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-4">
                <div class="card-body p-0 d-flex align-items-center gap-3">
                    <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center">
                        <span class="w-40-px h-40-px bg-info-600 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                            <iconify-icon icon="mdi:cash-multiple"></iconify-icon>
                        </span>
                    </div>
                    <div>
                        <span class="fw-medium text-secondary-light text-md">Sales - Paid</span>
                        <h6 class="fw-semibold my-1">₹ <?= number_format($summary['p_inr_total'], 2) ?></h6>
                        <p class="text-sm mb-0">
                            Outstanding : <span class="fw-medium text-warning-main">₹ <?= number_format($summary['o_inr_total'], 2) ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Royalty -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-3">
                <div class="card-body p-0 d-flex align-items-center gap-3">
                    <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center">
                        <span class="w-40-px h-40-px bg-warning-600 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                            <iconify-icon icon="mdi:currency-inr"></iconify-icon>
                        </span>
                    </div>
                    <div>
                        <span class="fw-medium text-secondary-light text-md">Royalty - Paid</span>
                        <h6 class="fw-semibold my-1">₹ <?= number_format($summary['p_royalty_total'], 2) ?></h6>
                        <p class="text-sm mb-0">
                            Outstanding : <span class="fw-medium text-warning-main">₹ <?= number_format($summary['o_royalty_total'], 2) ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ===================== TOP SELLING & RETURNED BOOKS ===================== -->
    <div class="card radius-8 border shadow-none">
        <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2">
            <iconify-icon icon="mdi:book-multiple"></iconify-icon>
            <h6 class="mb-0 fw-semibold">Top Selling & Returned Books</h6>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>BookId</th>
                            <th>Title</th>
                            <th class="text-success">Units Sold</th>
                            <th>BookId</th>
                            <th>Title</th>
                            <th class="text-danger">Units Refunded</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $maxRows = max(count($top_selling_books), count($top_returned_books));
                        for ($i = 0; $i < $maxRows; $i++):
                            $sell = $top_selling_books[$i] ?? null;
                            $ret  = $top_returned_books[$i] ?? null;
                        ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td><?= $sell['book_id'] ?? '' ?></td>
                            <td><?= $sell['title'] ?? '' ?></td>
                            <td class="fw-semibold text-success"><?= isset($sell['total_units_sold']) ? (int)$sell['total_units_sold'] : '' ?></td>
                            <td><?= $ret['book_id'] ?? '' ?></td>
                            <td><?= $ret['title'] ?? '' ?></td>
                            <td class="fw-semibold text-danger"><?= isset($ret['total_units_refunded']) ? (int)$ret['total_units_refunded'] : '' ?></td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection(); ?>
