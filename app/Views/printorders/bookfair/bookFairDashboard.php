<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid py-4">

<div class="row gy-4">

<!-- ================= RIGHT DASHBOARD CARDS ================= -->
<div class="col-xxl-6 col-xl-6 col-lg-12">
    <div class="row g-4">

        <!-- IN PROGRESS CARD -->
        <div class="col-md-14 mb-3">
            <div class="trail-bg h-100 text-center d-flex flex-column justify-content-between align-items-center p-16 radius-8">
                <h6 class="text-white text-xl">In Progress Orders</h6>
                <div class="">
                    <h2 class="text-white fw-bold mt-3"><?= count($orders ?? []) ?></h2>
                    <p class="text-white mb-0">Pending Bookfair Orders</p>
                </div>
            </div>
        </div>

        <!-- ADD ORDER -->
        <div class="col-sm-6 col-xs-6">
            <a href="<?= base_url('paperback/addsaleorreturnorder') ?>" class="text-decoration-none">
                <div class="radius-8 h-100 text-center p-20 bg-purple-light">
                    <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-xl mb-12 bg-lilac-200 border border-lilac-400 text-lilac-600">
                        <i class="ri-add-fill"></i>
                    </span>
                    <span class="text-neutral-700 d-block">Add Order</span>
                </div>
            </a>
        </div>

        <!-- SHIPPED ORDERS -->
        <div class="col-sm-6 col-xs-6">
            <a href="<?= base_url('paperback/bookfairbookshopshippedorders') ?>" class="text-decoration-none">
                <div class="radius-8 h-100 text-center p-20 bg-success-100">
                    <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-xl mb-12 bg-success-200 border border-success-400 text-success-600">
                        <i class="ri-truck-fill"></i>
                    </span>
                    <span class="text-neutral-700 d-block">Shipped Orders</span>
                </div>
            </a>
        </div>

        <!-- SOLD ORDERS -->
        <div class="col-sm-6 col-xs-6">
            <a href="<?= base_url('paperback/bookfairbookshopsoldorders') ?>" class="text-decoration-none">
                <div class="radius-8 h-100 text-center p-20 bg-info-100">
                    <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-xl mb-12 bg-info-200 border border-info-400 text-info-600">
                        <i class="ri-shopping-cart-fill"></i>
                    </span>
                    <span class="text-neutral-700 d-block">Sold Orders</span>
                </div>
            </a>
        </div>

        <!-- COMBO DETAILS -->
        <div class="col-sm-6 col-xs-6">
            <a href="<?= base_url('paperback/bookfaircombodetails') ?>" class="text-decoration-none">
                <div class="radius-8 h-100 text-center p-20 bg-danger-100">
                    <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-xl mb-12 bg-danger-200 border border-danger-400 text-danger-600">
                        <i class="ri-box-3-fill"></i>
                    </span>
                    <span class="text-neutral-700 d-block">Combo Details</span>
                </div>
            </a>
        </div>

    </div>
</div>

<!-- ================= LEFT SALES CHART ================= -->
<div class="col-xxl-6 col-xl-6 col-lg-12 mb-3">
    <div class="card h-100">
        <div class="card-body">
            <h6 class="mb-3">Sales Overview</h6>
            <canvas id="salesChart" height="200"></canvas>
        </div>
    </div>
</div>
</div>

<!-- ================= PENDING ORDERS TABLE ================= -->
<h6 class="mb-3 mt-4">Bookfair Orders (Pending)</h6>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="zero-config table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Order ID</th>
                    <th>Bookshop</th>
                    <th>Pack Name</th>
                    <th>No Of Titles</th>
                    <th>Qty / Title</th>
                    <th>Total Qty</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($orders)): ?>
                    <?php $i=1; foreach($orders as $row): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td>
                                <a href="<?= base_url('paperback/bookfairshippedorderdetails/'.$row['order_id']); ?>">
                                    <?= esc($row['order_id']) ?>
                                </a>
                            </td>
                            <td><?= esc($row['bookshop_name']) ?></td>
                            <td><?= esc($row['pack_name'] ?? '-') ?></td>
                            <td><?= esc($row['no_of_titles']) ?></td>
                            <td><?= (int)$row['qty_per_title'] ?></td>
                            <td><?= esc($row['total_qty']) ?></td>
                            <td>
                                <a class="btn btn-sm btn-primary"
                                href="<?= base_url('paperback/bookfairshippedorderdetails/'.$row['order_id']); ?>">
                                View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-danger">
                            No Pending Orders
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div>

<!-- ================= CHART.JS SCRIPT ================= -->
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
