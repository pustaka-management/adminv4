<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- ===================== SUMMARY CARDS (All in One Row) ===================== -->
    <div class="d-flex flex-wrap gap-3 mb-4">

        <!-- Card 1 : Total Titles -->
        <div class="flex-grow-1 flex-shrink-0" style="flex: 1 1 18%;">
            <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-1">
                <div class="card-body p-0 d-flex align-items-center gap-3">
                    <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center">
                        <span class="w-40-px h-40-px bg-primary-600 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                            <iconify-icon icon="mdi:book-open-page-variant"></iconify-icon>
                        </span>
                    </div>
                    <div>
                        <span class="fw-medium text-secondary-light text-md">Total Titles</span>
                        <h6 class="fw-semibold my-1"><?= esc($summary['total_titles']) ?></h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2 : Total Creators -->
        <div class="flex-grow-1 flex-shrink-0" style="flex: 1 1 18%;">
            <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-2">
                <div class="card-body p-0 d-flex align-items-center gap-3">
                    <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center">
                        <span class="w-40-px h-40-px bg-success-600 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                            <iconify-icon icon="mdi:account-group"></iconify-icon>
                        </span>
                    </div>
                    <div>
                        <span class="fw-medium text-secondary-light text-md">Total Authors</span>
                        <h6 class="fw-semibold my-1"><?= esc($summary['total_creators']) ?></h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3 : Total Retailers -->
        <div class="flex-grow-1 flex-shrink-0" style="flex: 1 1 18%;">
            <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-3">
                <div class="card-body p-0 d-flex align-items-center gap-3">
                    <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center">
                        <span class="w-40-px h-40-px bg-warning-600 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                            <iconify-icon icon="mdi:store"></iconify-icon>
                        </span>
                    </div>
                    <div>
                        <span class="fw-medium text-secondary-light text-md">Total Retailers</span>
                        <h6 class="fw-semibold my-1"><?= esc($summary['total_retailers']) ?></h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4 : Total Sales -->
        <div class="flex-grow-1 flex-shrink-0" style="flex: 1 1 18%;">
            <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-4">
                <div class="card-body p-0 d-flex align-items-center gap-3">
                    <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center">
                        <span class="w-40-px h-40-px bg-info-600 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                            <iconify-icon icon="mdi:cash-multiple"></iconify-icon>
                        </span>
                    </div>
                    <div>
                        <span class="fw-medium text-secondary-light text-md">Total Sales (INR)</span>
                        <h6 class="fw-semibold my-1 fs-6">₹ <?= number_format($summary['sales_paid'], 2) ?></h6>
                        <p class="text-sm mb-0">
                            Outstanding: <span class="fw-medium text-warning-main">₹ <?= number_format($summary['sales_outstanding'], 2) ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 5 : Total Royalty -->
        <div class="flex-grow-1 flex-shrink-0" style="flex: 1 1 18%;">
            <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-5">
                <div class="card-body p-0 d-flex align-items-center gap-3">
                    <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center">
                        <span class="w-40-px h-40-px bg-danger-600 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                            <iconify-icon icon="mdi:currency-inr"></iconify-icon>
                        </span>
                    </div>
                    <div>
                        <span class="fw-medium text-secondary-light text-md">Total Royalty (INR)</span>
                        <h6 class="fw-semibold my-1 fs-6">₹ <?= number_format($summary['royalty_paid'], 2) ?></h6>
                        <p class="text-sm mb-0">
                            Pending: <span class="fw-medium text-danger-main">₹ <?= number_format($summary['royalty_pending'], 2) ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ===================== TABLES ROW ===================== -->
<div class="row g-3">

    <!-- ===================== TOP SELLING BOOKS ===================== -->
    <div class="col-xxl-6 col-lg-6 col-md-12">
        <div class="card shadow-sm radius-8 border h-100">
            <div class="card-body p-3">
                <h5 class="fw-bold mb-3">Top Selling OverDrive Books</h5>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Book ID</th>
                                <th>Title</th>
                                <th class="text-end">Units</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($topBooks)): ?>
                                <?php $i = 1; foreach ($topBooks as $book): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= esc($book['book_id']) ?></td>
                                        <td><?= esc($book['title']) ?></td>
                                        <td class="text-end fw-semibold">
                                            <?= number_format($book['total_orders']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        No data available
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- ===================== TOP RETAILERS ===================== -->
    <div class="col-xxl-6 col-lg-6 col-md-12">
        <div class="card shadow-sm radius-8 border h-100">
            <div class="card-body p-3">
                <h5 class="fw-bold mb-3">Top OverDrive Retailers</h5>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Retailer</th>
                                <th class="text-end">Units</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($topRetailers)): ?>
                                <?php $i = 1; foreach ($topRetailers as $row): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= esc($row['retailer']) ?></td>
                                        <td class="text-end fw-semibold">
                                            <?= number_format($row['total_orders']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        No data available
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</div>


<?= $this->endSection(); ?>