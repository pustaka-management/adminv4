<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- ===================== SUMMARY CARDS ===================== -->
    <div class="row g-3 mb-4">

        <?php
        $cards = [
            [
                'title'=>'Total Titles',
                'val'=>$summary['total_titles'] ?? 0,
                'icon'=>'mdi:book-open-page-variant',
                'bg'=>'primary',
                'url'=>base_url('sales/storytelbooks')
            ],
            [
                'title'=>'Total Authors',
                'val'=>$summary['total_creators'] ?? 0,
                'icon'=>'mdi:account-group',
                'bg'=>'success',
                'url'=>base_url('sales/storytelauthors')
            ],
            [
                'title'=>'Total Orders',
                'val'=>number_format($summary['total_orders'] ?? 0),
                'icon'=>'mdi:clipboard-list',
                'bg'=>'info',
                'extra'=>true,
                'url'=>base_url('sales/storytelorders')
            ],
            [
                'title'=>'Revenue (INR)',
                'val'=>'₹ '.number_format($summary['total_revenue'] ?? 0,2),
                'icon'=>'mdi:cash',
                'bg'=>'warning',
                'extra'=>true
            ],
        ];
        ?>

        <?php foreach($cards as $c): ?>
        <div class="col-6 col-md-3 d-flex">
            <a href="<?= $c['url'] ?? 'javascript:void(0)' ?>" class="text-decoration-none flex-fill">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="bg-<?= $c['bg'] ?> text-white rounded p-3 d-flex justify-content-center align-items-center">
                            <iconify-icon icon="<?= $c['icon'] ?>" width="24" height="24"></iconify-icon>
                        </span>
                        <div class="flex-grow-1">
                            <div class="text-muted"><?= $c['title'] ?></div>
                            <h6 class="fw-bold mb-1"><?= $c['val'] ?></h6>

                            <?php if(!empty($c['extra'])): ?>
                            <small class="d-block text-sm">
                                <?php if($c['title']=='Total Orders'): ?>
                                    Paid: <span class="text-success"><?= number_format($summary['orders_paid'] ?? 0) ?></span> |
                                    Pending: <span class="text-danger"><?= number_format($summary['orders_pending'] ?? 0) ?></span>
                                <?php elseif($c['title']=='Revenue (INR)'): ?>
                                    Paid: <span class="text-success">₹ <?= number_format($summary['revenue_paid'] ?? 0,2) ?></span><br>
                                    Pending: <span class="text-danger">₹ <?= number_format($summary['revenue_pending'] ?? 0,2) ?></span>
                                <?php endif; ?>
                            </small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>

    </div>

    <!-- ===================== TOP BOOKS + LANGUAGE CHART ===================== -->
    <div class="row g-3">

        <!-- Top Books -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Top Storytel Books</h5>
                    <div class="table-responsive" style="max-height:360px">
                        <table class="zero-config table table-hover mt-4">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th class="text-end">Units</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($topBooks)): $i=1; foreach($topBooks as $b): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= esc($b['title']) ?></td>
                                    <td><?= esc($b['author']) ?></td>
                                    <td class="text-end fw-semibold"><?= number_format($b['total_units']) ?></td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No data</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Year Wise -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Yearly Sales</h5>
                    <div style="height:350px">
                        <canvas id="storytelYearChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ===================== GENRE + LANGUAGE ===================== -->
    <div class="row g-3 mt-3">

        <!-- Language Wise Chart -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Language Wise Sales</h5>
                    <div style="height:350px">
                        <canvas id="storytelLangChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Genre Wise Chart -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Genre Wise Sales</h5>
                    <div style="height:350px">
                        <canvas id="storytelGenreChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<?php
$langLabels  = array_column($languageSales ?? [], 'language_name');
$langValues  = array_map('floatval', array_column($languageSales ?? [], 'total_sales'));

$yearLabels  = array_column($yearSales ?? [], 'year');
$yearValues  = array_map('floatval', array_column($yearSales ?? [], 'total_sales'));

$genreLabels = array_column($genreSales ?? [], 'genre_name');
$genreValues = array_map('floatval', array_column($genreSales ?? [], 'total_sales'));
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Language Chart
new Chart(document.getElementById('storytelLangChart'), {
    type:'bar',
    data:{
        labels:<?= json_encode($langLabels) ?>,
        datasets:[{data:<?= json_encode($langValues) ?>, backgroundColor:'rgba(54,162,235,0.7)'}]
    },
    options:{responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}}
});

// Genre Chart
new Chart(document.getElementById('storytelGenreChart'), {
    type:'doughnut',
    data:{
        labels:<?= json_encode($genreLabels) ?>,
        datasets:[{data:<?= json_encode($genreValues) ?>, backgroundColor:['#0d6efd','#198754','#ffc107','#dc3545','#6c757d']}]
    },
    options:{responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}}}
});

// Year Chart
new Chart(document.getElementById('storytelYearChart'), {
    type:'line',
    data:{
        labels:<?= json_encode($yearLabels) ?>,
        datasets:[{data:<?= json_encode($yearValues) ?>, fill:true, borderColor:'#0d6efd', tension:0.3}]
    },
    options:{responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}}
});
</script>

<?= $this->endSection(); ?>
