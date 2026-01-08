<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- ===================== SUMMARY CARDS ===================== -->
    <div class="d-flex flex-wrap gap-3 mb-4">

        <!-- Total Audio Titles -->
        <div class="flex-grow-1" style="flex:1 1 18%">
            <div class="card px-24 py-16 border h-100 bg-gradient-start-1">
                <div class="d-flex align-items-center gap-3">
                    <span class="w-40-px h-40-px bg-primary text-white radius-8 d-flex align-items-center justify-content-center">
                        <iconify-icon icon="mdi:headphones"></iconify-icon>
                    </span>
                    <div>
                        <span class="text-secondary">Total Audio Titles</span>
                        <h6 class="fw-bold mb-0"><?= $summary['total_titles'] ?? 0 ?></h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Creators -->
        <div class="flex-grow-1" style="flex:1 1 18%">
            <div class="card px-24 py-16 border h-100 bg-gradient-start-2">
                <div class="d-flex align-items-center gap-3">
                    <span class="w-40-px h-40-px bg-success text-white radius-8 d-flex align-items-center justify-content-center">
                        <iconify-icon icon="mdi:account-voice"></iconify-icon>
                    </span>
                    <div>
                        <span class="text-secondary">Total Creators</span>
                        <h6 class="fw-bold mb-0"><?= $summary['total_creators'] ?? 0 ?></h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders -->
        <div class="flex-grow-1" style="flex:1 1 18%">
            <div class="card px-24 py-16 border h-100 bg-gradient-start-5">
                <div class="d-flex align-items-center gap-3">
                    <span class="w-40-px h-40-px bg-danger text-white radius-8 d-flex align-items-center justify-content-center">
                        <iconify-icon icon="mdi:clipboard-list"></iconify-icon>
                    </span>
                    <div>
                        <span class="text-secondary">Total Orders</span>
                        <h6 class="fw-bold mb-0"><?= $summary['total_orders'] ?? 0 ?></h6>
                        <small>
                            Paid:
                            <span class="text-success fw-semibold">
                                <?= $summary['paid_orders'] ?? 0 ?>
                            </span>
                            |
                            Pending:
                            <span class="text-danger fw-semibold">
                                <?= $summary['pending_orders'] ?? 0 ?>
                            </span>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div class="flex-grow-1" style="flex:1 1 18%">
            <div class="card px-24 py-16 border h-100 bg-gradient-start-4">
                <div class="d-flex align-items-center gap-3">
                    <span class="w-40-px h-40-px bg-info text-white radius-8 d-flex align-items-center justify-content-center">
                        <iconify-icon icon="mdi:cash-multiple"></iconify-icon>
                    </span>
                    <div>
                        <span class="text-secondary">Audio Revenue</span>
                        <h6 class="fw-bold mb-0">
                            ₹ <?= number_format($summary['total_value'] ?? 0, 2) ?>
                        </h6>
                        <small>
                            Paid:
                            <span class="text-success fw-semibold">
                                ₹ <?= number_format($summary['paid_value'] ?? 0, 2) ?>
                            </span>
                            <br>
                            Pending:
                            <span class="text-warning fw-semibold">
                                ₹ <?= number_format($summary['outstanding_value'] ?? 0, 2) ?>
                            </span>
                        </small>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ===================== TABLE + CHART ===================== -->
    <div class="row g-3">

        <!-- Top Selling Books -->
        <div class="col-lg-6">
            <div class="card border h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Top Selling OverDrive Audiobooks</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Book ID</th>
                                    <th>Title</th>
                                    <th class="text-end">Orders</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($topBooks)): $i=1; foreach ($topBooks as $row): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= esc($row['book_id']) ?></td>
                                        <td><?= esc($row['title']) ?></td>
                                        <td class="text-end"><?= number_format($row['total_orders']) ?></td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No data found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Language Wise Sales -->
        <div class="col-lg-6">
            <div class="card border h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Language Wise Audio Sales</h5>
                    <div style="height:350px">
                        <canvas id="languageChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
$langLabels = array_column($languageSales ?? [], 'language_name');
$langValues = array_map('floatval', array_column($languageSales ?? [], 'total_sales'));
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('languageChart').getContext('2d');

/* Gradient like first UI */
const gradient = ctx.createLinearGradient(0, 0, 0, 350);
gradient.addColorStop(0, '#6366f1'); // indigo
gradient.addColorStop(1, '#22c55e'); // green

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($langLabels) ?>,
        datasets: [{
            data: <?= json_encode($langValues) ?>,
            backgroundColor: gradient,
            borderRadius: 12,
            barThickness: 40
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return ' ₹ ' + context.raw.toLocaleString();
                    }
                }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: {
                    autoSkip: false,
                    maxRotation: 45,
                    minRotation: 45
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₹ ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>


<?= $this->endSection(); ?>
