<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- SUMMARY CARDS -->
    <div class="d-flex flex-wrap gap-3 mb-4">

       <?php
        $cards = [
            [
                'title'=>'Total Titles',
                'val'=>$summary['total_titles'] ?? 0,
                'icon'=>'mdi:book',
                'bg'=>'primary',
                'url'=>base_url('sales/googletitles')
            ],
            [
                'title'=>'Total Authors',
                'val'=>$summary['total_creators'] ?? 0,
                'icon'=>'mdi:account',
                'bg'=>'success',
                'url'=>base_url('sales/googleauthors')
            ],
            [
                'title'=>'Total Orders',
                'val'=>number_format($summary['total_orders'] ?? 0),
                'icon'=>'mdi:clipboard-list',
                'bg'=>'info',
                'extra'=>true,
                'url'=>base_url('google/titles') // orders by title
            ],
            [
                'title'=>'Revenue (INR)',
                'val'=>'₹ '.number_format($summary['total_revenue'] ?? 0,2),
                'icon'=>'mdi:cash',
                'bg'=>'warning',
                'extra'=>true,
                'url'=>base_url('google/titles') 
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
                                Paid: <span class="text-success"><?= number_format($summary['orders_paid'] ?? 0) ?></span> |
                                Pending: <span class="text-danger"><?= number_format($summary['orders_pending'] ?? 0) ?></span>
                            <?php elseif($c['title']=='Revenue (INR)'): ?>
                                Paid: <span class="text-success">₹ <?= number_format($summary['revenue_paid'] ?? 0,2) ?></span><br>
                                Pending: <span class="text-danger">₹ <?= number_format($summary['revenue_pending'] ?? 0,2) ?></span>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    </div>

    <!-- TOP BOOKS + YEAR SALES -->
    <div class="row g-3">
        <!-- Top Books -->
        <div class="col-lg-6">
            <div class="card radius-8 border shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Top Selling Google Books</h5>
                    <div class="table-responsive">
                        <table class="zero-config table table-hover mt-4">
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
                                    <td class="text-end fw-semibold"><?= number_format($b['total_orders']) ?></td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr><td colspan="3" class="text-center text-muted">No data</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-lg-6">
            <div class="card radius-8 border shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Year Wise Revenue</h5>
                    <div style="height:300px"><canvas id="googleYearChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <!-- GENRE + YEAR CHART -->
    <div class="row g-3 mt-3">



    <!-- Language Chart -->
        <div class="col-lg-6">
            <div class="card radius-8 border shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Language Wise Sales</h5>
                    <div style="height:320px">
                        <canvas id="googleLangChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card radius-8 border shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Genre Wise Sales</h5>
                    <div style="height:300px"><canvas id="googleGenreChart"></canvas></div>
                </div>
            </div>
        </div>

    </div>
</div>

    <?php
    $langLabels  = array_column($lang_sales ?? [], 'language_name');
    $langVals    = array_map('floatval', array_column($lang_sales ?? [], 'total'));

    $genreLabels = array_column($genre_sales ?? [], 'genre_name');
    $genreVals   = array_map('floatval', array_column($genre_sales ?? [], 'total'));

    $yearLabels  = array_column($year_sales ?? [], 'year');
    $yearVals    = array_map('floatval', array_column($year_sales ?? [], 'total'));
    ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    new Chart(document.getElementById('googleLangChart'),{
        type:'bar',
        data:{labels:<?= json_encode($langLabels) ?>,datasets:[{data:<?= json_encode($langVals) ?>}]},
        options:{responsive:true,maintainAspectRatio:false}
    });

    new Chart(document.getElementById('googleGenreChart'),{
        type:'doughnut',
        data:{labels:<?= json_encode($genreLabels) ?>,datasets:[{data:<?= json_encode($genreVals) ?>}]},
        options:{responsive:true,maintainAspectRatio:false}
    });

    new Chart(document.getElementById('googleYearChart'),{
        type:'line',
        data:{labels:<?= json_encode($yearLabels) ?>,datasets:[{data:<?= json_encode($yearVals) ?>}]},
        options:{responsive:true,maintainAspectRatio:false}
    });
    </script>

    <?= $this->endSection(); ?>
