<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

<!-- ================= CARDS ================= -->
<div class="d-flex flex-wrap gap-3 mb-4">

<?php
$cards = [
    [
        'title'=>'Total Books',
        'value'=>$bookStats['total_books'],
        'icon'=>'mdi:book',
        'bg'=>'primary'
    ],
    [
        'title'=>'Total Authors',
        'value'=>$bookStats['total_authors'],
        'icon'=>'mdi:account',
        'bg'=>'success'
    ],
    [
        'title'=>'Content Earnings',
        'total'=>$txnSummary['content_total'],
        'paid'=>$txnSummary['content_paid'],
        'pending'=>$txnSummary['content_pending'],
        'icon'=>'mdi:cash',
        'bg'=>'warning'
    ],
    [
        'title'=>'Revenue Share',
        'total'=>$txnSummary['rev_total'],
        'paid'=>$txnSummary['rev_paid'],
        'pending'=>$txnSummary['rev_pending'],
        'icon'=>'mdi:chart-donut',
        'bg'=>'info'
    ]
];
?>

<?php foreach ($cards as $c): ?>
<div style="flex:1 1 22%">
    <div class="card px-24 py-16 shadow-sm h-100">
        <div class="d-flex gap-3 align-items-center">
            <span class="w-40-px h-40-px bg-<?= $c['bg'] ?> text-white radius-8 d-flex justify-content-center align-items-center">
                <iconify-icon icon="<?= $c['icon'] ?>"></iconify-icon>
            </span>
            <div>
                <span class="text-secondary"><?= $c['title'] ?></span>

                <?php if(isset($c['total'])): ?>
                    <h6 class="fw-bold mb-1">₹ <?= number_format($c['total'],2) ?></h6>
                    <small>
                        Paid: <span class="text-success">₹ <?= number_format($c['paid'],2) ?></span><br>
                        Pending: <span class="text-danger">₹ <?= number_format($c['pending'],2) ?></span>
                    </small>
                <?php else: ?>
                    <h6 class="fw-bold mb-1"><?= number_format($c['value']) ?></h6>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

</div>

<!-- ================= TABLE + CHART ================= -->
<div class="row g-3">

<!-- Top Books -->
<div class="col-lg-6">
    <div class="card shadow-sm h-100">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Top KukuFM Books</h5>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Show</th>
                        <th class="text-end">Content</th>
                        <th class="text-end">Rev Share</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($topBooks): $i=1; foreach($topBooks as $b): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= esc($b['show_name']) ?></td>
                        <td class="text-end">₹ <?= number_format($b['content_total'],2) ?></td>
                        <td class="text-end">₹ <?= number_format($b['rev_total'],2) ?></td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="4" class="text-center text-muted">No data</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Language Chart -->
<div class="col-lg-6">
    <div class="card shadow-sm h-100">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Language-wise Books</h5>
            <div style="height:320px">
                <canvas id="kukuLangChart"></canvas>
            </div>
        </div>
    </div>
</div>

</div>
</div>

<?php
$langs = array_column($languageCounts,'language_name');
$vals  = array_column($languageCounts,'total_books');
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('kukuLangChart'),{
    type:'bar',
    data:{
        labels:<?= json_encode($langs) ?>,
        datasets:[{
            data:<?= json_encode($vals) ?>,
            backgroundColor:'#8b5cf6',
            borderRadius:10,
            barThickness:40
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{legend:{display:false}},
        scales:{y:{beginAtZero:true}}
    }
});
</script>

<?= $this->endSection(); ?>
