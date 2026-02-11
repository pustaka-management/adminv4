<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>
<h6 class="text-center">Bookfair Bookshop Sale or Return Orders</h6>
<br><br>
<a href="<?= base_url('combobookfair/createcombo') ?>" class="btn btn-lilac-600 radius-8 px-20 py-11 justify-content-end">Create New Combo</a>
<br><br>
<div class="container-fluid py-4">
    <div class="row gy-4">   
        <!-- LEFT SECTION -->
        <div class="col-xxl-6 col-xl-6 col-lg-12">
            <div class="row g-4">
                <!-- IN PROGRESS CARD -->
                <div class="col-12 mb-3">
                    <div class="trail-bg h-100 text-center d-flex flex-column justify-content-between align-items-center p-4 radius-8"><br>
                        <h6 class="text-white text-xl">In Progress Orders</h6>
                        <div>
                            <h2 class="text-white fw-bold mt-3"><?= count($orders ?? []) ?></h2>
                            <p class="text-white mb-0">Inprogress Bookfair Orders</p>
                        </div>
                    </div>
                </div>

                <!-- ACTION CARDS -->
                <div class="col-12">
                    <div class="row g-4">

                        <!-- ADD ORDER -->
                        <div class="col-md-3 col-6 mb-3">
                            <a href="<?= base_url('combobookfair/addsaleorreturnorder') ?>" class="text-decoration-none">
                                <div class="radius-8 h-100 text-center p-3 bg-purple-light d-flex flex-column justify-content-center align-items-center" style="min-height: 140px;">
                                    <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-xl mb-3 bg-lilac-200 border border-lilac-400 text-lilac-600">
                                        <i class="ri-add-fill"></i>
                                    </span>
                                    <span class="text-neutral-700 d-block" style="font-size: 15px;">Add Order</span>
                                </div>
                            </a>
                        </div>

                        <!-- COMBO DETAILS -->
                        <div class="col-md-3 col-6 mb-3">
                            <a href="<?= base_url('combobookfair/bookfaircombodetails') ?>" class="text-decoration-none">
                                <div class="radius-8 h-100 text-center p-3 bg-danger-100 d-flex flex-column justify-content-center align-items-center" style="min-height: 140px;">
                                    <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-xl mb-3 bg-danger-200 border border-danger-400 text-danger-600">
                                        <i class="ri-box-3-fill"></i>
                                    </span>
                                    <span class="text-neutral-700 d-block" style="font-size: 15px;">Combo Details</span>
                                </div>
                            </a>
                        </div>

                        <!-- SHIPPED ORDERS -->
                        <div class="col-md-3 col-6 mb-3">
                            <a href="<?= base_url('combobookfair/bookfairbookshopshippedorders') ?>" class="text-decoration-none">
                                <div class="radius-8 h-100 text-center p-3 bg-success-100 d-flex flex-column justify-content-center align-items-center" style="min-height: 140px;">
                                    <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-xl mb-3 bg-success-200 border border-success-400 text-success-600">
                                        <i class="ri-truck-fill"></i>
                                    </span>
                                    <span class="text-neutral-700 d-block" style="font-size: 15px;">Shipped orders</span>
                                </div>
                            </a>
                        </div>

                        <!-- SOLD ORDERS -->
                        <div class="col-md-3 col-6 mb-3">
                            <a href="<?= base_url('combobookfair/bookfairbookshopsoldorders') ?>" class="text-decoration-none">
                                <div class="radius-8 h-100 text-center p-20 bg-info-focus d-flex flex-column justify-content-center align-items-center" style="min-height: 140px;">
                                    <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-xl mb-3 bg-info-200 border border-info-400 text-info-600">
                                        <i class="ri-shopping-cart-fill"></i>
                                    </span>
                                    <span class="text-neutral-700 d-block" style="font-size: 15px;">Sold orders</span>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <!-- END LEFT SECTION -->


        <!-- RIGHT SECTION - SALES CHART -->
        <div class="col-xxl-6 col-xl-6 col-lg-12 mb-3">
            <div class="card h-85">
                <div class="card-body">
                    <h6 class="mb-3">Sales Overview</h6>
                    <canvas id="salesChart" height="150"></canvas>
                </div>
            </div>
        </div>
        <!-- END RIGHT SECTION -->

    </div>


    <br><br>


    <!-- ================= PENDING ORDERS TABLE ================= -->
    <div id="content" class="main-content">
        <div class="layout-px-spacing">

            <h6 class="text-center">Bookfair Orders (Inprogress)</h6>
            <br>

            <table class="table zero-config">
                <thead>
                    <tr>
                        <th>S.NO</th>
                        <th>Order ID</th>
                        <th>Bookshop</th>
                        <th>Pack Name</th>
                        <th>No. of Titles</th>
                        <th>Send Quantity</th>
                        <th>Create Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody style="font-weight: normal;">
                    <?php if (!empty($bookfair_sales['bookfair_list'])): ?>
                        <?php $i = 1; foreach ($bookfair_sales['bookfair_list'] as $row): ?>
                            <tr>
                                <td><?= $i++; ?></td>

                                <td>
                                    <a href="<?= base_url('combobookfair/bookfairdetailsview/' . trim($row['order_id'])); ?>" target="_blank">
                                        <?= esc($row['order_id']); ?>
                                    </a>
                                </td>

                                <td><?= esc($row['bookshop_name']); ?></td>
                                <td><?= esc($row['pack_name']); ?></td>
                                <td><?= esc($row['no_of_titles']); ?></td>
                                <td><?= esc($row['send_qty']); ?></td>
                                <td><?= esc(date('d-m-Y', strtotime($row['create_date']))); ?></td>
                                <td>
                                    <a href="<?= base_url('combobookfair/bookfairdetailsview/' . trim($row['order_id'])); ?>"
                                       class="btn btn-outline-lilac-600 radius-8 px-20 py-11"
                                       style="padding: 4px 10px; font-size: 12px;"
                                       target="_blank">
                                        Ship
                                    </a>
                                    <a href="<?= base_url('combobookfair/bookfairdetailsview/' . trim($row['order_id'])); ?>"
                                       class="btn btn-outline-success-600 radius-8 px-20 py-11"
                                       style="padding: 4px 10px; font-size: 12px;"
                                       target="_blank">
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No records found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <br><br><br>

        </div>
    </div>
    <!-- END TABLE SECTION -->

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($salesData['labels'] ?? ['Jan','Feb','Mar','Apr']) ?>,
        datasets: [{
            label: 'Sales',
            data: <?= json_encode($salesData['values'] ?? [1000, 1500, 2000, 2500]) ?>,
            fill: true,
            backgroundColor: 'rgba(79, 70, 229, 0.2)',
            borderColor: 'rgba(79, 70, 229, 1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { mode: 'index', intersect: false }
        },
        scales: {
            x: { grid: { display: false } },
            y: { beginAtZero: true }
        }
    }
});
</script>

<?= $this->endSection(); ?>