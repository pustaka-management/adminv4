<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
    <a href="<?= base_url('sales/salesdashboard'); ?>" 
       class="btn btn-sm btn-success radius-8 d-inline-flex align-items-center gap-1">
        <iconify-icon icon="uil:arrow-left" class="text-xl"></iconify-icon>
        Back
    </a>
    <a href="<?= base_url('dashboard/bookshop'); ?>" target="_blank"
       class="btn btn-sm btn-warning radius-8 d-inline-flex align-items-center gap-1">
        <iconify-icon icon="mdi:book" class="text-xl"></iconify-icon>
        Book-Wise Revenue
    </a>
</div>

<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="page-header">
            <div class="page-title">
                <h6 class="text-center">Bookshop Paperback Dashboard</h6>
            </div>
        </div>
        <br><br>

        <div class="row gy-4 justify-content-start">
            <!-- Total Orders -->
            <div class="col-xxl-2 col-xl-3 col-lg-4 col-sm-6">
                <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-3">
                    <div class="card-body p-0 d-flex align-items-center">
                        <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-20">
                            <span class="w-40-px h-40-px bg-primary-600 text-white d-flex justify-content-center align-items-center radius-8">
                                <iconify-icon icon="mdi:cart"></iconify-icon>
                            </span>
                        </div>
                        <div>
                            <span class="fw-medium text-secondary-light">Total Orders</span>
                            <h6 class="fw-semibold my-1">
                                <?= esc($bookshop_sales['bookshop']['total_orders'] ?? 0); ?>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paid Orders -->
            <div class="col-xxl-2 col-xl-3 col-lg-4 col-sm-6">
                <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-2">
                    <div class="card-body p-0 d-flex align-items-center">
                        <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-20">
                            <span class="w-40-px h-40-px bg-success-main text-white d-flex justify-content-center align-items-center radius-8">
                                <iconify-icon icon="solar:wallet-bold"></iconify-icon>
                            </span>
                        </div>
                        <div>
                            <span class="fw-medium text-secondary-light">Paid Orders</span>
                            <h6 class="fw-semibold my-1">
                                <?= esc($bookshop_sales['bookshop']['total_paid'] ?? 0); ?>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Orders -->
            <div class="col-xxl-2 col-xl-3 col-lg-4 col-sm-6">
                <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-5">
                    <div class="card-body p-0 d-flex align-items-center">
                        <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-20">
                            <span class="w-40-px h-40-px bg-warning-main text-white d-flex justify-content-center align-items-center radius-8">
                                <iconify-icon icon="mdi:clock-outline"></iconify-icon>
                            </span>
                        </div>
                        <div>
                            <span class="fw-medium text-secondary-light">Pending Orders</span>
                            <h6 class="fw-semibold my-1">
                                <?= esc($bookshop_sales['bookshop']['total_pending'] ?? 0); ?>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Titles -->
            <div class="col-xxl-2 col-xl-3 col-lg-4 col-sm-6">
                <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-4">
                    <div class="card-body p-0 d-flex align-items-center">
                        <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-20">
                            <span class="w-40-px h-40-px bg-info-main text-white d-flex justify-content-center align-items-center radius-8">
                                <iconify-icon icon="mdi:book-open-page-variant"></iconify-icon>
                            </span>
                        </div>
                        <div>
                            <span class="fw-medium text-secondary-light">Total Titles</span>
                            <h6 class="fw-semibold my-1">
                                <?= esc($bookshop_sales['bookshop']['total_titles'] ?? 0); ?>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Sales -->
            <div class="col-xxl-2 col-xl-3 col-lg-4 col-sm-6">
                <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-1">
                    <div class="card-body p-0 d-flex align-items-center">
                        <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-20">
                            <span class="w-40-px h-40-px bg-purple text-white d-flex justify-content-center align-items-center radius-8">
                                <iconify-icon icon="fa6-solid:file-invoice-dollar"></iconify-icon>
                            </span>
                        </div>
                        <div>
                            <span class="fw-medium text-secondary-light">Total Sales</span>
                            <h6 class="fw-semibold my-1 text-break overflow-hidden font-10">
                                ₹ <?= number_format($bookshop_sales['bookshop']['total_amount'] ?? 0); ?>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Bookshops -->
            <div class="col-xxl-2 col-xl-3 col-lg-4 col-sm-6">
                <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-1">
                    <div class="card-body p-0 d-flex align-items-center">
                        <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-20">
                            <span class="w-40-px h-40-px bg-warning text-white d-flex justify-content-center align-items-center radius-8">
                                <iconify-icon icon="fa6-solid:book"></iconify-icon>
                            </span>
                        </div>
                        <div>
                            <span class="fw-medium text-secondary-light">Total Bookshop</span>
                            <h6 class="fw-semibold my-1 text-break overflow-hidden">
                                 <?= esc($bookshop_sales['bookshop']['total_bookshops'] ?? 0); ?>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br><br>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Top 10 Selling Books (Bookshop)</h5>
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
                                    <?php if (!empty($bookshop_sales['top_selling'])): ?>
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

                                        <?php foreach ($bookshop_sales['top_selling'] as $row): ?>
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
                        <h6 class="text-lg fw-semibold mb-0">Bookshop Sales Chart Summary</h6>
                        <form method="GET">
                            <select name="chart_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="all" <?= ($chart_filter=='all')?'selected':'' ?>>Month-wise (All)</option>
                                <option value="this_year" <?= ($chart_filter=='this_year')?'selected':'' ?>>Current FY</option>
                                <option value="prev_year" <?= ($chart_filter=='prev_year')?'selected':'' ?>>Previous FY</option>
                            </select>
                        </form>
                    </div>
                    <div class="card-body p-24">
                        <div id="bookshopChart" style="margin-left: 10px; margin-right: 10px;"></div>
                    </div>
                </div>
            </div>
            <br><br>
            <div class="col-md-6">
                <div class="card h-100 p-0">
                    <div class="card-header border-bottom bg-base py-16 px-24">
                        <h6 class="text-lg fw-semibold mb-0">Top 10 Genre Sales</h6>
                    </div>
                    <div class="card-body p-24 text-center">
                        <div id="pieChart" class="d-flex justify-content-center apexcharts-tooltip-z-none"></div>
                    </div>
                </div>
            </div>
            <?php
            $labels = [];
            $series = [];

            foreach ($bookshop_sales['language_sales'] as $row) {
                $labels[] = $row['language_name'];
                $series[] = (int)$row['bookshop_order_count'];
            }
            ?>
            <div class="col-md-6">
                <div class="card h-100 p-0">
                    <div class="card-body p-24 d-flex flex-column justify-content-between gap-8">
                        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-20">
                            <h6 class="mb-2 fw-bold text-lg mb-0">Bookshop Sales by Language</h6>
                            <select class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                                <option>Total</option>
                            </select>
                        </div>
                        <div id="userOverviewDonutChart" class="margin-16-minus y-value-left"></div>
                        <ul class="d-flex flex-wrap align-items-center justify-content-between mt-3 gap-3">
                            <?php foreach ($bookshop_sales['language_sales'] as $index => $row): ?>
                                <li class="d-flex flex-column gap-8">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="w-12-px h-12-px rounded-circle bg-primary-600"></span>
                                        <span class="text-secondary-light text-sm fw-semibold">
                                            <?= esc($row['language_name']) ?>
                                        </span>
                                    </div>
                                    <span class="text-primary-light fw-bold">
                                        <?= esc($row['bookshop_order_count']) ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>   
        </div>    
    </div>
</div>

<?= $this->endSection(); ?>
<?= $this->section('script'); ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const chartData = <?= json_encode($bookshop_sales['chart']); ?>;
    const months = chartData.map(item => item.order_month);
    const totalTitles = chartData.map(item => parseInt(item.total_titles));
    const totalMrp = chartData.map(item => parseInt(item.total_mrp));

        var options = {
        chart: {
            type: 'bar',
            height: 400,
            stacked: false,
            toolbar: { show: false }
        },
        series: [
            { name: "Total Titles", type: 'column', data: totalTitles },
            { name: "Total MRP", type: 'column', data: totalMrp }
        ],
        plotOptions: {
            bar: { horizontal: false, columnWidth: '40%', endingShape: 'rounded' }
        },
        xaxis: { categories: months, title: { text: 'Order Month' } },
        yaxis: [
            {
                show: false,
                title: { text: " " },
                labels: { formatter: val => val.toLocaleString() }
            },
            {
                show: false,
                opposite: true,
                title: { text: "" },
                labels: { formatter: val => val.toLocaleString() }
            }
        ],
        dataLabels: { enabled: false },
        colors: ['#ebde2dff', '#341bd6ff'],
        tooltip: {
            shared: true, intersect: false,
            y: { formatter: val => val.toLocaleString() }
        },
        legend: { position: 'top', horizontalAlign: 'center' }
    };


    var chart = new ApexCharts(document.querySelector("#bookshopChart"), options);
    chart.render();
});
document.addEventListener("DOMContentLoaded", function () {

    const genreData = <?= json_encode($bookshop_sales['genre_sales'] ?? []); ?>;

    if (!genreData.length) {
        document.querySelector("#pieChart").innerHTML =
            "<p class='text-muted'>No genre sales data available</p>";
        return;
    }

    const genreLabels = genreData.map(item => item.genre_name);
    const genreSales  = genreData.map(item => parseInt(item.total_sales));

    const options = {
        chart: {
            type: 'pie',
            height: 320
        },
        labels: genreLabels,
        series: genreSales,
        legend: { position: 'bottom' },
        dataLabels: { enabled: true },
        tooltip: {
            y: {
                formatter: val => val + " sales"
            }
        }
    };

    new ApexCharts(document.querySelector("#pieChart"), options).render();
});
 var options = {
        series: <?= json_encode($series) ?>,
        labels: <?= json_encode($labels) ?>,
        colors: ["#FF9F29", "#487FFF", "#22C55E", "#A855F7", "#F43F5E"],
        legend: {
            show: false
        },
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
            enabled: false
        }
    };

    var chart = new ApexCharts(
        document.querySelector("#userOverviewDonutChart"),
        options
    );
    chart.render();
</script>
<?= $this->endSection(); ?>

