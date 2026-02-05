<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- ===================== SUMMARY CARDS ===================== -->
    <div class="d-flex flex-wrap gap-3 mb-4">

        <?php
        $cards = [
            [
                'title'=>'Total Books',
                'val'=>number_format($counts['total_books'] ?? 0),
                'icon'=>'mdi:book-open',
                'bg'=>'primary',
                'url'=>base_url('sales/getamazonbooks')
            ],
            [
                'title'=>'Total Authors',
                'val'=>number_format($counts['total_authors'] ?? 0),
                'icon'=>'mdi:account',
                'bg'=>'success',
                'url'=>base_url('sales/amazonauthors')
            ],
            [
                'title'=>'Total Orders',
                'val'=>number_format($orders['total_orders'] ?? 0),
                'icon'=>'mdi:clipboard-list',
                'bg'=>'info',
                'extra'=>true,
                'url'=>base_url('amazon/titles')
            ],
            [
                'title'=>'Revenue (INR)',
                'val'=>'₹ '.number_format($amounts['total_amount'] ?? 0,2),
                'icon'=>'mdi:cash',
                'bg'=>'warning',
                'extra'=>true,
                'url'=>base_url('amazon/titles')
            ],
        ];
        ?>

        <?php foreach ($cards as $c): ?>
        <div style="flex:1 1 22%; cursor:pointer;"
             onclick="window.location.href='<?= esc($c['url']) ?>'">
            <div class="card px-24 py-16 radius-8 border shadow-sm h-100">
                <div class="d-flex align-items-center gap-3">
                    <span class="w-40-px h-40-px bg-<?= $c['bg'] ?> text-white radius-8 d-flex justify-content-center align-items-center">
                        <iconify-icon icon="<?= $c['icon'] ?>"></iconify-icon>
                    </span>
                    <div>
                        <span class="text-secondary-light"><?= $c['title'] ?></span>
                        <h6 class="fw-bold mb-1"><?= $c['val'] ?></h6>

                        <?php if(!empty($c['extra'])): ?>
                            <p class="mb-0 text-sm">
                                <?php if($c['title']=='Total Orders'): ?>
                                    Paid:
                                    <span class="text-success"><?= number_format($orders['paid_orders'] ?? 0) ?></span> |
                                    Pending:
                                    <span class="text-danger"><?= number_format($orders['pending_orders'] ?? 0) ?></span>
                                <?php elseif($c['title']=='Revenue (INR)'): ?>
                                    Paid:
                                    <span class="text-success">₹ <?= number_format($amounts['paid_amount'] ?? 0,2) ?></span><br>
                                    Pending:
                                    <span class="text-danger">₹ <?= number_format($amounts['pending_amount'] ?? 0,2) ?></span>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

    </div>

    <!-- ===================== TOP BOOKS + YEAR SALES ===================== -->
    <div class="row g-3">

        <!-- Top Books -->
        <div class="col-lg-6">
            <div class="card radius-8 border shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Top Selling Amazon Books</h5>
                    <div class="table-responsive">
                        <table class="table table-hover mt-4">
                            <thead>
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
                                    <td colspan="4" class="text-center text-muted">No data</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Year-wise Revenue -->
        <div class="col-lg-6">
            <div class="card radius-8 border shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Year Wise Revenue</h5>
                    <div style="height:300px">
                        <canvas id="amazonYearChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ===================== LANGUAGE + GENRE ===================== -->
    <div class="row g-3 mt-3">

        <div class="col-lg-6">
            <div class="card radius-8 border shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Language Wise Sales</h5>
                    <div style="height:320px">
                        <canvas id="amazonLangChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card radius-8 border shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Genre Wise Sales</h5>
                    <div style="height:300px">
                        <canvas id="amazonGenreChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
$yearLabels  = array_column($yearSales ?? [], 'year');
$yearVals    = array_map('floatval', array_column($yearSales ?? [], 'total'));

$langLabels  = array_column($langSales ?? [], 'language_name');
$langVals    = array_map('floatval', array_column($langSales ?? [], 'total'));

$genreLabels = array_column($genreSales ?? [], 'genre_name');
$genreVals   = array_map('floatval', array_column($genreSales ?? [], 'total'));
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('amazonLangChart'),{
    type:'bar',
    data:{ labels:<?= json_encode($langLabels) ?>, datasets:[{ data:<?= json_encode($langVals) ?> }]},
    options:{ responsive:true, maintainAspectRatio:false }
});

new Chart(document.getElementById('amazonGenreChart'),{
    type:'doughnut',
    data:{ labels:<?= json_encode($genreLabels) ?>, datasets:[{ data:<?= json_encode($genreVals) ?> }]},
    options:{ responsive:true, maintainAspectRatio:false }
});

new Chart(document.getElementById('amazonYearChart'),{
    type:'line',
    data:{ labels:<?= json_encode($yearLabels) ?>, datasets:[{ data:<?= json_encode($yearVals) ?> }]},
    options:{ responsive:true, maintainAspectRatio:false }
});
</script>

<?= $this->endSection(); ?>
