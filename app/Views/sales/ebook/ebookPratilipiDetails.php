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
                'url'=>base_url('sales/pratilipititles')
            ],
            [
                'title'=>'Total Authors',
                'val'=>$summary['total_authors'] ?? 0,
                'icon'=>'mdi:account',
                'bg'=>'success',
                'url'=>base_url('sales/pratilipiauthors')
            ],
            [
                'title'=>'Total Orders',
                'val'=>number_format($summary['total_orders'] ?? 0),
                'icon'=>'mdi:clipboard-list',
                'bg'=>'info',
                'extra'=>true,
                'url'=>base_url('sales/pratilipiorders')
            ],
            [
                'title'=>'Revenue (INR)',
                'val'=>'₹ '.number_format($summary['total_revenue'] ?? 0,2),
                'icon'=>'mdi:cash',
                'bg'=>'warning',
                'extra'=>true,
                'url'=>base_url('sales/pratilipiorders')
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

    <!-- TOP AUTHORS + YEAR SALES -->
    <div class="row g-3">

        <!-- Top Authors -->
        <div class="col-lg-6">
            <div class="card radius-8 border shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Top Pratilipi Authors</h5>
                    <div class="table-responsive">
                        <table class="zero-config table table-hover mt-4">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Author</th>
                                    <th class="text-end">Units</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($topAuthors)): $i=1; foreach($topAuthors as $a): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= esc($a['author_name']) ?></td>
                                    <td class="text-end fw-semibold"><?= number_format($a['sales_count']) ?></td>
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

         <!-- Year Revenue -->
        <div class="col-lg-6">
            <div class="card radius-8 border shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Year Wise Revenue</h5>
                    <div style="height:300px"><canvas id="pratilipiYearChart"></canvas></div>
                </div>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const yearData = <?= json_encode($yearTransactions ?? []); ?>;

    if (yearData.length > 0) {
        const years = yearData.map(r => r.year);
        const totals = yearData.map(r => r.total_earning);

        new Chart(document.getElementById('pratilipiYearChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: years,
                datasets: [{
                    label: 'Revenue (INR)',
                    data: totals,
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                tension: 0.3,
                scales: { y: { beginAtZero: true } }
            }
        });
    }
</script>
<?= $this->endSection(); ?>

