<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

<!-- ===================== SUMMARY CARDS ===================== -->
<div class="d-flex flex-wrap gap-3 mb-4">

    <!-- Total Titles -->
    <div class="flex-grow-1" style="flex:1 1 18%; cursor:pointer;"
             onclick="window.location.href='<?= site_url('sales/scribdbooks') ?>'">
        <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-1">
            <div class="card-body p-0 d-flex align-items-center gap-3">
                <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center">
                    <span class="w-40-px h-40-px bg-primary-600 text-white radius-8 d-flex align-items-center justify-content-center">
                        <iconify-icon icon="mdi:book-open-page-variant"></iconify-icon>
                    </span>
                </div>
                <div>
                    <span class="fw-medium text-secondary-light text-md">Total Titles</span>
                    <h6 class="fw-semibold my-1"><?= esc($summary['total_titles'] ?? 0) ?></h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Authors -->
     <div class="flex-grow-1" style="flex:1 1 18%; cursor:pointer;"
    onclick="window.location.href='<?= site_url('sales/scribdauthors') ?>'">
        <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-2">
            <div class="card-body p-0 d-flex align-items-center gap-3">
                <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center">
                    <span class="w-40-px h-40-px bg-success-600 text-white radius-8 d-flex align-items-center justify-content-center">
                        <iconify-icon icon="mdi:account-group"></iconify-icon>
                    </span>
                </div>
                <div>
                    <span class="fw-medium text-secondary-light text-md">Total Authors</span>
                    <h6 class="fw-semibold my-1"><?= esc($summary['total_creators'] ?? 0) ?></h6>
                </div>
            </div>
        </div>
    </div>
    </a>


    <!-- Total Orders -->
    <div class="flex-grow-1 flex-shrink-0" style="flex:1 1 18%; cursor:pointer;"
        onclick="window.location.href='<?= site_url('sales/scribdorders') ?>'">
        <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-5">
            <div class="card-body p-0 d-flex align-items-center gap-3">
                <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center">
                    <span class="w-40-px h-40-px bg-danger-600 text-white radius-8 d-flex align-items-center justify-content-center">
                        <iconify-icon icon="mdi:cart-outline"></iconify-icon>
                    </span>
                </div>
                <div>
                    <span class="fw-medium text-secondary-light text-md">Total Orders</span>
                    <h6 class="fw-semibold my-1"><?= number_format($summary['total_orders'] ?? 0) ?></h6>
                    <p class="text-sm mb-0">
                        Paid: <span class="fw-medium text-success-main"><?= number_format($summary['orders_paid'] ?? 0) ?></span>
                        &nbsp;|&nbsp;
                        Pending: <span class="fw-medium text-danger-main"><?= number_format($summary['orders_pending'] ?? 0) ?></span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="flex-grow-1 flex-shrink-0" style="flex:1 1 18%;">
        <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-4">
            <div class="card-body p-0 d-flex align-items-center gap-3">
                <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center">
                    <span class="w-40-px h-40-px bg-info-600 text-white radius-8 d-flex align-items-center justify-content-center">
                        <iconify-icon icon="mdi:cash-multiple"></iconify-icon>
                    </span>
                </div>
                <div>
                    <span class="fw-medium text-secondary-light text-md">Total Revenue (₹)</span>
                    <h6 class="fw-semibold my-1"><?= number_format($summary['total_revenue'] ?? 0, 2) ?></h6>
                    <p class="text-sm mb-0">
                        Paid: <span class="fw-medium text-success-main"><?= number_format($summary['revenue_paid'] ?? 0, 2) ?></span>
                        <br>
                        Pending: <span class="fw-medium text-danger-main"><?= number_format($summary['revenue_pending'] ?? 0, 2) ?></span>
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ===================== TOP BOOKS + LANGUAGE CHART ===================== -->
<div class="row g-3">

    <!-- Top Books Table -->
    <div class="col-lg-6">
        <div class="card radius-8 border shadow-sm h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Top Scribd Books</h5>
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th class="text-end">Reads</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($top_books): foreach ($top_books as $i=>$b): ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td><?= esc($b['title']) ?></td>
                            <td><?= esc($b['authors']) ?></td>
                            <td class="text-end fw-semibold"><?= number_format($b['total_reads']) ?></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="3" class="text-center text-muted">No data</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

     <!-- Year Wise -->
    <div class="col-lg-6">
        <div class="card radius-8 border shadow-sm h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Year Wise Sales</h5>
                <div style="height:360px">
                    <canvas id="yearChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ===================== YEAR & GENRE CHARTS ===================== -->
<div class="row g-3 mt-3">
     <!-- Language Chart -->
    <div class="col-lg-6">
        <div class="card radius-8 border shadow-sm h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Language Wise Sales</h5>
                <div style="height:360px">
                    <canvas id="languageChart"></canvas>
                </div>
            </div>
        </div>
    </div>
   

    <!-- Genre Wise -->
    <div class="col-lg-6">
        <div class="card radius-8 border shadow-sm h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Genre Wise Sales</h5>
                <div style="height:360px">
                    <canvas id="genreChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

</div>

<?php
$langLabels  = array_column($language_sales ?? [], 'language_name');
$langValues  = array_map('floatval', array_column($language_sales ?? [], 'total_sales'));

$yearLabels  = array_column($year_sales ?? [], 'year');
$yearValues  = array_map('floatval', array_column($year_sales ?? [], 'total_sales'));

$genreLabels = array_column($genre_sales ?? [], 'genre');
$genreValues = array_map('floatval', array_column($genre_sales ?? [], 'total_sales'));
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Language Chart
new Chart(document.getElementById('languageChart'), {
    type:'bar',
    data:{labels:<?= json_encode($langLabels) ?>, datasets:[{data:<?= json_encode($langValues) ?>, backgroundColor:'#4e73df', borderRadius:10, barThickness:36}]},
    options:{responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{x:{grid:{display:false}}, y:{ticks:{callback:v=>'₹ '+v.toLocaleString()}}}}
});

// Year Chart
new Chart(document.getElementById('yearChart'), {
    type:'line',
    data:{labels:<?= json_encode($yearLabels) ?>, datasets:[{data:<?= json_encode($yearValues) ?>, borderColor:'#1cc88a', backgroundColor:'rgba(28,200,138,0.2)', tension:0.4, fill:true}]},
    options:{responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{y:{ticks:{callback:v=>'₹ '+v.toLocaleString()}}}}
});

// Genre Chart
new Chart(document.getElementById('genreChart'), {
    type:'doughnut',
    data:{labels:<?= json_encode($genreLabels) ?>, datasets:[{data:<?= json_encode($genreValues) ?>, backgroundColor:['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796'], borderWidth:0}]},
    options:{responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}, tooltip:{callbacks:{label:c=>'₹ '+c.raw.toLocaleString()}}}}
});
</script>

<?= $this->endSection(); ?>
