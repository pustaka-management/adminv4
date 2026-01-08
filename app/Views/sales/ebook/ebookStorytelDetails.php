<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- ===================== SUMMARY CARDS ===================== -->
    <div class="d-flex flex-wrap gap-3 mb-4">

        <?php
        $cards = [
            [
                'title'=>'Total Titles',
                'val'=>$summary['total_books'] ?? 0,
                'icon'=>'mdi:book',
                'bg'=>'primary'
            ],
            [
                'title'=>'Total Authors',
                'val'=>$summary['total_authors'] ?? 0,
                'icon'=>'mdi:account',
                'bg'=>'success'
            ],
            [
                'title'=>'Total Orders',
                'val'=>number_format($summary['total_orders'] ?? 0),
                'icon'=>'mdi:clipboard-list',
                'bg'=>'info',
                'extra'=>true
            ],
            [
                'title'=>'Revenue (INR)',
                'val'=>'₹ '.number_format($summary['total_net_sales'] ?? 0,2),
                'icon'=>'mdi:cash',
                'bg'=>'warning',
                'extra'=>true
            ],
        ];
        ?>

        <?php foreach ($cards as $c): ?>
        <div style="flex:1 1 22%">
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
                                <span class="text-success">
                                    <?= number_format($summary['orders_paid'] ?? 0) ?>
                                </span> |
                                Pending:
                                <span class="text-danger">
                                    <?= number_format($summary['orders_pending'] ?? 0) ?>
                                </span>

                            <?php elseif($c['title']=='Revenue (INR)'): ?>
                                Paid:
                                <span class="text-success">
                                    ₹ <?= number_format($summary['revenue_paid'] ?? 0,2) ?>
                                </span><br>
                                Pending:
                                <span class="text-danger">
                                    ₹ <?= number_format($summary['revenue_pending'] ?? 0,2) ?>
                                </span>
                            <?php endif; ?>
                        </p>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

    </div>

    <!-- ===================== TABLE + CHART ===================== -->
    <div class="row g-3">

        <!-- Top Books -->
        <div class="col-lg-6">
            <div class="card radius-8 border shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Top Audible Books</h5>
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th class="text-end">Units</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($topBooks)): $i=1; foreach ($topBooks as $b): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= esc($b['title']) ?></td>
                                <td class="text-end fw-semibold">
                                    <?= number_format($b['total_units']) ?>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    No data
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Language Chart -->
        <div class="col-lg-6">
            <div class="card radius-8 border shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Language Wise Sales</h5>
                    <div style="height:320px">
                        <canvas id="audibleLangChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
$langs = array_column($languageSales ?? [], 'language_name');
$vals  = array_map('floatval', array_column($languageSales ?? [], 'total_sales'));
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('audibleLangChart').getContext('2d');
const grad = ctx.createLinearGradient(0,0,0,320);
grad.addColorStop(0,'#6366f1');   // Storytel style colors
grad.addColorStop(1,'#22c55e');

new Chart(ctx,{
    type:'bar',
    data:{
        labels:<?= json_encode($langs) ?>,
        datasets:[{
            data:<?= json_encode($vals) ?>,
            backgroundColor:grad,
            borderRadius:12,
            barThickness:40
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{
            legend:{display:false},
            tooltip:{
                callbacks:{
                    label:(c)=>' ₹ '+c.raw.toLocaleString()
                }
            }
        },
        scales:{
            x:{grid:{display:false}},
            y:{
                beginAtZero:true,
                ticks:{callback:(v)=>'₹ '+v.toLocaleString()}
            }
        }
    }
});
</script>

<?= $this->endSection(); ?>
