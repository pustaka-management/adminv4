<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
    <a href="<?= base_url('sales/salesdashboard'); ?>" 
       class="btn btn-sm btn-success radius-8 d-inline-flex align-items-center gap-1">
        <iconify-icon icon="uil:arrow-left" class="text-xl"></iconify-icon>
        Back
    </a>
    <a href="<?= base_url('dashboard/offlinesale'); ?>" target="_blank"
       class="btn btn-sm btn-warning radius-8 d-inline-flex align-items-center gap-1">
        <iconify-icon icon="mdi:book" class="text-xl"></iconify-icon>
        Book-Wise Revenue
    </a>
</div>
<?php
$genres = $offline_sales['genre_sales'] ?? [];

// Prepare chart data
$labels = [];
$series = [];
$totalSales = 0;

foreach ($genres as $row) {
    $labels[] = $row['genre_name'];
    $series[] = (int) $row['total_sales'];
    $totalSales += (int) $row['total_sales'];
}
?>
<?php
    $languageSales = $offline_sales['language_sales'];

    // Total orders
    $totalOrders = array_sum(array_column($languageSales, 'offline_order_count'));

    // Gradient classes (rotate colors)
    $gradients = [
        'bg-primary-gradient',
        'bg-success-gradient',
        'bg-info-gradient',
        'bg-warning-gradient',
        'bg-danger-gradient'
    ];
?>

<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="page-header">
            <div class="page-title">
                <h6 class="text-center">Offline Paperback Dashboard</h6>
            </div>
        </div>
        <br><br>

        <div class="col-xxxl-12">
            <div class="row gy-4">

                <!-- Total Orders Card -->
                <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-6 col-sm-12">
                    <div class="card p-4 shadow-2 radius-8 bg-gradient-end-6" style="height: 150px; justify-content: center;">
                        <div class="card-body p-0 d-flex flex-column align-items-center justify-content-center">
                            <span class="mb-12 w-44-px h-44-px text-primary-600 bg-primary-light border border-primary-light-white flex-shrink-0 d-flex justify-content-center align-items-center radius-8 h6 mb-12">
                                <iconify-icon icon="fa-solid:box-open" class="icon" style="font-size: 28px;"></iconify-icon>
                            </span>
                            <h6 style="font-weight: 700; margin-bottom: 5px;"><?= $offline_sales['offline']['total_orders'] ?></h6>
                            <span style="font-weight: 500; color: #6c757d;">Total Orders</span>
                        </div>
                    </div>
                </div>

                <!-- Total Titles Card -->
                <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-6 col-sm-12">
                    <div class="card p-4 shadow-2 radius-8 bg-gradient-end-3" style="height: 150px; justify-content: center;">
                        <div class="card-body p-0 d-flex flex-column align-items-center justify-content-center">
                            <span class="mb-12 w-44-px h-44-px text-info-600 bg-info-light border border-info-light-white flex-shrink-0 d-flex justify-content-center align-items-center radius-8 h6 mb-12">
                                <iconify-icon icon="fa-solid:book" class="icon" style="font-size: 28px;"></iconify-icon>
                            </span>
                            <h6 style="font-weight: 700; margin-bottom: 5px;"><?= $offline_sales['offline']['total_titles'] ?></h6>
                            <span style="font-weight: 500; color: #6c757d;">Total Titles</span>
                        </div>
                    </div>
                </div>

                <!-- Paid Orders Card -->
                <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-6 col-sm-12">
                    <div class="card p-4 shadow-2 radius-8 bg-gradient-end-1" style="height: 150px; width: 100%; justify-content: center;">
                        <div class="card-body p-0 d-flex flex-column align-items-center justify-content-center">
                            <span class="mb-12 w-44-px h-44-px text-warning-600 bg-warning-light border border-warning-light-white flex-shrink-0 d-flex justify-content-center align-items-center radius-8 h6 mb-12">
                                <iconify-icon icon="fa-solid:check" class="icon" style="font-size: 28px;"></iconify-icon>
                            </span>
                            <h6 style="font-weight: 700; margin-bottom: 5px;"><?= $offline_sales['offline']['total_paid_orders'] ?></h6>
                            <span style="font-weight: 500; color: #6c757d;">Paid Orders</span>
                        </div>
                    </div>
                </div>

                <!-- Pending Orders Card -->
                <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-6 col-sm-12">
                    <div class="card p-4 shadow-2 radius-8 bg-gradient-end-4" style="height: 150px; justify-content: center;">
                        <div class="card-body p-0 d-flex flex-column align-items-center justify-content-center">
                            <span class="mb-12 w-44-px h-44-px text-lilac-600 bg-lilac-light border border-lilac-light-white flex-shrink-0 d-flex justify-content-center align-items-center radius-8 h6 mb-12">
                                <iconify-icon icon="fa-solid:clock" class="icon" style="font-size: 28px;"></iconify-icon>
                            </span>
                            <h6 style="font-weight: 700; margin-bottom: 5px;"><?= $offline_sales['offline']['total_pending_orders'] ?></h6>
                            <span style="font-weight: 500; color: #6c757d;">Pending Orders</span>
                        </div>
                    </div>
                </div>

                <!-- Total Sales Card -->
                <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-6 col-sm-12">
                    <div class="card p-4 shadow-2 radius-8 bg-gradient-end-1" style="height: 150px; justify-content: center;">
                        <div class="card-body p-0 d-flex flex-column align-items-center justify-content-center">
                            <span class="mb-3 w-60-px h-60-px bg-success-100 text-success-600 d-flex justify-content-center align-items-center rounded-circle h1">
                                <i class="ri-wallet-3-fill" style="font-size: 28px;"></i>
                            </span>
                            <h6 style="font-weight: 700; margin-bottom: 5px;"><?= number_format($offline_sales['offline']['total_sales'], 2) ?></h6>
                            <span style="font-weight: 500; color: #6c757d;">Total Sales</span>
                        </div>
                    </div>
                </div>
            </div>
            <br><br>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Top 10 Selling Books (Offline)</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table colored-row-table mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="bg-base">S.L</th>
                                            <th scope="col" class="bg-base">Book ID</th>
                                            <th scope="col" class="bg-base">Book Title</th>
                                            <th scope="col" class="bg-base">Sold</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php if (!empty($offline_sales['top_selling'])): ?>
                                            <?php 
                                                $i = 1;
                                                // background classes to rotate
                                                $bgClasses = [
                                                    'bg-primary-light',
                                                    'bg-success-focus',
                                                    'bg-info-focus',
                                                    'bg-warning-focus',
                                                    'bg-danger-focus'
                                                ];
                                            ?>

                                            <?php foreach ($offline_sales['top_selling'] as $row): ?>
                                                <?php $bg = $bgClasses[($i - 1) % count($bgClasses)]; ?>
                                                <tr>
                                                    <td class="<?= $bg; ?>"><?= $i++; ?></td>
                                                    <td class="<?= $bg; ?>"><?= esc($row['book_id']); ?></td>
                                                    <td class="<?= $bg; ?>"><?= esc($row['book_title']); ?></td>
                                                    <td class="<?= $bg; ?>"><?= esc($row['total_sold']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>

                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center">No data found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div><!-- card end -->
                </div>
                <div class="col-md-6">
                    <div class="card h-100 p-0">
                        <div class="card-header border-bottom bg-base py-16 px-24 d-flex justify-content-between align-items-center">
                            <h6 class="text-lg fw-semibold mb-0">Offline Sales Chart Summary</h6>
                            <form method="GET">
                                <select name="chart_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="all" <?= ($chart_filter=='all')?'selected':'' ?>>Month-wise (All)</option>
                                    <option value="this_year" <?= ($chart_filter=='this_year')?'selected':'' ?>>Current FY</option>
                                    <option value="prev_year" <?= ($chart_filter=='prev_year')?'selected':'' ?>>Previous FY</option>
                                </select>
                            </form>
                        </div>
                        <div class="card-body p-24">
                            <div id="offlineOrdersChart" style="margin-left: 10px; margin-right: 10px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <br><br>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100 p-0">
                        <div class="card-header border-bottom bg-base py-16 px-24">
                            <h6 class="text-lg fw-semibold mb-0">Offline Sales by Genre</h6>
                        </div>

                        <div class="card-body p-24 text-center d-flex flex-wrap align-items-start gap-5 justify-content-center">
                            <!-- Donut Chart -->
                            <div class="position-relative">
                                <div id="offlineGenreDonutChart" class="w-auto d-inline-block apexcharts-tooltip-z-none"></div>
                                <div class="position-absolute start-50 top-50 translate-middle">
                                    <span class="text-lg text-secondary-light fw-medium">Total Sales</span>
                                    <h4 class="mb-0"><?= $totalSales ?></h4>
                                </div>
                            </div>

                            <!-- Legend -->
                            <div class="max-w-290-px w-100">
                                <div class="d-flex align-items-center justify-content-between gap-12 border pb-12 mb-12 border-end-0 border-top-0 border-start-0">
                                    <span class="text-primary-light fw-medium text-sm">Genre</span>
                                    <span class="text-primary-light fw-medium text-sm">Sales</span>
                                    <span class="text-primary-light fw-medium text-sm">%</span>
                                </div>

                                <?php
                                $colors = [
                                    'bg-success-600',
                                    'bg-primary-600',
                                    'bg-info-600',
                                    'bg-danger-600',
                                    'bg-orange',
                                    'bg-warning'
                                ];
                                $i = 0;

                                foreach ($genres as $row):
                                    $percent = $totalSales > 0
                                        ? round(($row['total_sales'] / $totalSales) * 100, 1)
                                        : 0;
                                ?>
                                    <div class="d-flex align-items-center justify-content-between gap-12 mb-12">
                                        <span class="text-primary-light fw-medium text-sm d-flex align-items-center gap-12">
                                            <span class="w-12-px h-12-px <?= $colors[$i % count($colors)] ?> rounded-circle"></span>
                                            <?= esc($row['genre_name']) ?>
                                        </span>
                                        <span class="text-primary-light fw-medium text-sm">
                                            <?= $row['total_sales'] ?>
                                        </span>
                                        <span class="text-primary-light fw-medium text-sm">
                                            <?= $percent ?>%
                                        </span>
                                    </div>
                                <?php
                                    $i++;
                                endforeach;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card p-0 overflow-hidden radius-12">
                        <div class="card-header py-16 px-24">
                            <h6 class="text-lg mb-0">Offline Orders Language Wise</h6>
                        </div>

                        <div class="card-body p-24">
                            <div class="d-flex flex-column gap-4">

                                <?php foreach ($languageSales as $i => $row): 
                                    $percentage = $totalOrders > 0 
                                        ? round(($row['offline_order_count'] / $totalOrders) * 100) 
                                        : 0;

                                    $gradient = $gradients[$i % count($gradients)];
                                ?>

                                <div class="position-relative">
                                    <!-- Label -->
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fw-500"><?= esc($row['language_name']); ?></span>
                                        <span class="text-muted">
                                            <?= $row['offline_order_count']; ?> orders
                                        </span>
                                    </div>

                                    <!-- Progress bar -->
                                    <div class="progress h-8-px bg-light"
                                        role="progressbar"
                                        aria-valuenow="<?= $percentage; ?>"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="<?= esc($row['language_name']); ?> - <?= $row['offline_order_count']; ?> orders">

                                        <div class="progress-bar rounded-pill <?= $gradient; ?>"
                                            style="width: <?= $percentage; ?>%">
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br><br>
            <?php
            $authorNames   = [];
            $authorRevenue = [];
            $authorOrders  = [];

            foreach ($offline_sales['author_sales'] as $row) {
                $authorNames[]   = $row['author_name'];
                $authorRevenue[] = (float) $row['total_revenue'];
                $authorOrders[]  = (int) $row['total_orders'];
            }
            ?>

            <div class="col-md-6">
                <div class="card h-100 p-0">
                    <div class="card-header border-bottom bg-base py-16 px-24">
                        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                            <h6 class="mb-2 fw-bold text-lg">Top 10 Authors Offline Sales</h6>
                        </div>
                    </div>

                    <div class="card-body p-24">
                        <div class="row align-items-center">

                            <!-- LEFT : DONUT CHART -->
                            <div class="col-md-6">
                                <div id="authorRevenueDonut" class="mx-auto apexcharts-tooltip-z-none"></div>
                            </div>

                            <!-- RIGHT : AUTHOR LIST WITH ORDERS -->
                            <div class="col-md-6">
                                <div class="d-flex flex-column gap-16">

                                    <?php foreach ($offline_sales['author_sales'] as $row): ?>
                                        <div class="d-flex align-items-center gap-12">
                                            <span class="w-12-px h-12-px radius-2 bg-primary-600"></span>

                                            <span class="text-secondary-light fw-medium">
                                                <?= esc($row['author_name']) ?>
                                                <span class="text-muted">
                                                    (<?= esc($row['total_orders']) ?> orders)
                                                </span>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
<?= $this->section('script'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chartData = <?= json_encode($offline_sales['chart']); ?>;
        const months = chartData.map(item => item.order_month);
        const totalTitles = chartData.map(item => parseInt(item.total_titles));
        const totalMrp = chartData.map(item => parseFloat(item.total_mrp));

        var options = {
        chart: { type: 'line', height: 400, stacked: false, toolbar: { show: false } },
        series: [
            { name: "Total Titles", type: 'column', data: totalTitles },
            { name: "Total MRP", type: 'column', data: totalMrp }
        ],
        plotOptions: { line: { horizontal: false, columnWidth: '40%', endingShape: 'rounded' } },
        xaxis: { categories: months, title: { text: 'Sales Month' } },
        yaxis: [
            {
                show: false, 
            },
            {
                show: false,
                opposite: false,
                title: { text: "Total MRP (₹)" },
                min: 0,
                labels: { formatter: val => "₹" + val.toLocaleString() }
            }
        ],
        dataLabels: { enabled: false },
        colors: ['#a0d812ff', '#030ea5ff'],
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function(val, opts){
                    return opts.seriesIndex===1 ? "₹" + val.toLocaleString() : val.toLocaleString();
                }
            }
        },
        legend: { position: 'top', horizontalAlign: 'center' }
    };

    var chart = new ApexCharts(document.querySelector("#offlineOrdersChart"), options);
    chart.render();
    });
    document.addEventListener("DOMContentLoaded", function () {
    var options = {
        chart: {
            type: 'donut',
            height: 280
        },
        series: <?= json_encode($series) ?>,
        labels: <?= json_encode($labels) ?>,
        legend: {
            show: false
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            width: 0
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%'
                }
            }
        }
    };

    var chart = new ApexCharts(
        document.querySelector("#offlineGenreDonutChart"),
        options
    );

    chart.render();
    const authorNames   = <?= json_encode($authorNames); ?>;
    const authorOrders  = <?= json_encode($authorOrders); ?>;
    const authorRevenue = <?= json_encode($authorRevenue); ?>;

    var options = {
        series: authorRevenue,
        labels: authorNames,
        colors: [
            "#487FFF", "#FF9F29", "#45B369", "#9935FE",
            "#EF4444", "#22C55E", "#EAB308", "#6366F1",
            "#EC4899", "#14B8A6"
        ],
        chart: {
            type: "donut",
            height: 270,
            sparkline: {
                enabled: true
            }
        },
        stroke: {
            width: 0
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(1) + "%";
            }
        },
        tooltip: {
            custom: function({ seriesIndex }) {
                return `
                    <div class="p-12">
                        <strong>${authorNames[seriesIndex]}</strong><br>
                        <span> Orders: <b>${authorOrders[seriesIndex]}</b></span><br>
                        <span> Revenue: <b>₹ ${authorRevenue[seriesIndex].toLocaleString()}</b></span>
                    </div>
                `;
            }
        }
    };

    var chart = new ApexCharts(
        document.querySelector("#authorRevenueDonut"),
        options
    );
    chart.render();
});
</script>
<?= $this->endSection(); ?>
