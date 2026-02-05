<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!--  SUMMARY CARDS  -->
    <div class="d-flex flex-wrap gap-3 mb-4">

        <!-- Total Titles -->
        <div class="flex-grow-1" style="flex:1 1 18%; cursor:pointer;"
             onclick="window.location.href='<?= site_url('sales/overdrivebooks') ?>'">
            <div class="card px-24 py-16 radius-8 border bg-gradient-start-1 hover-shadow">
                <div class="card-body p-0 d-flex align-items-center gap-3">
                    <span class="w-40-px h-40-px bg-primary-600 text-white radius-8 d-flex align-items-center justify-content-center">
                        <iconify-icon icon="mdi:book-open-page-variant"></iconify-icon>
                    </span>
                    <div>
                        <span class="text-secondary-light">Total Titles</span>
                        <h6><?= esc($summary['total_titles'] ?? 0) ?></h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Authors -->
        <div class="flex-grow-1" style="flex:1 1 18%; cursor:pointer;"
             onclick="window.location.href='<?= site_url('sales/overdriveauthors') ?>'">
            <div class="card px-24 py-16 radius-8 border bg-gradient-start-2 hover-shadow">
                <div class="card-body p-0 d-flex align-items-center gap-3">
                    <span class="w-40-px h-40-px bg-success-600 text-white radius-8 d-flex align-items-center justify-content-center">
                        <iconify-icon icon="mdi:account-group"></iconify-icon>
                    </span>
                    <div>
                        <span class="text-secondary-light">Total Authors</span>
                        <h6><?= esc($summary['total_creators'] ?? 0) ?></h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="flex-grow-1" style="flex:1 1 18%; cursor:pointer;"
             onclick="window.location.href='<?= site_url('sales/overdriveorders') ?>'">
            <div class="card px-24 py-16 radius-8 border bg-gradient-start-5">
                <div class="card-body p-0">
                    <span class="text-secondary-light">Total Orders</span>
                    <h6><?= number_format($summary['total_orders'] ?? 0) ?></h6>
                    <small>
                        Paid:
                        <span class="text-success"><?= $summary['paid_orders'] ?? 0 ?></span>
                        |
                        Pending:
                        <span class="text-danger"><?= $summary['pending_orders'] ?? 0 ?></span>
                    </small>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="flex-grow-1" style="flex:1 1 18%;">
            <div class="card px-24 py-16 radius-8 border bg-gradient-start-4">
                <div class="card-body p-0">
                    <span class="text-secondary-light">Total Revenue</span>
                    <h6 class="fw-bold mt-1">₹ <?= number_format($summary['sales_total'] ?? 0, 2) ?></h6>

                    <small class="d-block mt-1">
                        Paid:
                        <span class="text-success fw-semibold">
                            ₹ <?= number_format($summary['sales_paid'] ?? 0, 2) ?>
                        </span>
                    </small>

                    <small class="d-block">
                        Pending:
                        <span class="text-warning fw-semibold">
                            ₹ <?= number_format($summary['sales_outstanding'] ?? 0, 2) ?>
                        </span>
                    </small>
                </div>
            </div>
        </div>

    </div>

    <!--  TOP BOOKS + YEAR CHART  -->
    <div class="row g-3">

        <!-- Top Books -->
        <div class="col-lg-6">
            <div class="card radius-8 border h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Top Selling Overdrive Books</h5>
                    <div class="table-responsive" style="max-height:360px">
                        <table class="zero-config table table-hover mt-4">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Book ID</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th class="text-end">Orders</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($topBooks)): $i=1; foreach($topBooks as $b): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= esc($b['book_id']) ?></td>
                                    <td><?= esc($b['title']) ?></td>
                                    <td><?= esc($b['author']) ?></td>
                                    <td class="text-end"><?= number_format($b['total_orders']) ?></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No data</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Year Wise Sales -->
        <div class="col-lg-6">
            <div class="card radius-8 border h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Year Wise Sales</h5>
                    <div style="height:350px">
                        <canvas id="yearWiseChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- GENRE + LANGUAGE  -->
    <div class="row g-3 mt-3">

        <div class="col-lg-6">
            <div class="card radius-8 border h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Genre Wise Sales</h5>
                    <div style="height:350px">
                        <canvas id="genreWiseChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card radius-8 border h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Language Wise Sales</h5>
                    <div style="height:350px">
                        <canvas id="languageSalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<?php
$langLabels  = array_column($languageSales ?? [], 'language_name');
$langValues  = array_map('floatval', array_column($languageSales ?? [], 'total_sales'));

$yearLabels  = array_column($yearWiseSales ?? [], 'sales_year');
$yearValues  = array_map('floatval', array_column($yearWiseSales ?? [], 'total_sales'));

$genreLabels = array_column($genreWiseSales ?? [], 'genre_name');
$genreValues = array_map('floatval', array_column($genreWiseSales ?? [], 'total_sales'));
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('languageSalesChart'),{
    type:'bar',
    data:{labels:<?= json_encode($langLabels) ?>,datasets:[{data:<?= json_encode($langValues) ?>}]},
    options:{responsive:true,maintainAspectRatio:false}
});

new Chart(document.getElementById('yearWiseChart'),{
    type:'line',
    data:{labels:<?= json_encode($yearLabels) ?>,datasets:[{data:<?= json_encode($yearValues) ?>,fill:true,tension:.4}]},
    options:{responsive:true,maintainAspectRatio:false}
});

new Chart(document.getElementById('genreWiseChart'),{
    type:'doughnut',
    data:{labels:<?= json_encode($genreLabels) ?>,datasets:[{data:<?= json_encode($genreValues) ?>}]},
    options:{responsive:true,maintainAspectRatio:false}
});
</script>

<?= $this->endSection(); ?>
