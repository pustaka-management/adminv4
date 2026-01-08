<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

<!-- ================= SUMMARY CARDS ================= -->
<div class="d-flex flex-wrap gap-3 mb-4">

<?php
$cards = [
    [
        'title'=>'YouTube Revenue',
        'total'=>$summary['youtube_total'],
        'paid'=>$summary['youtube_paid'],
        'pending'=>$summary['youtube_pending'],
        'bg'=>'danger',
        'icon'=>'mdi:youtube'
    ],
    [
        'title'=>'Pustaka Earnings',
        'total'=>$summary['pustaka_total'],
        'paid'=>$summary['pustaka_paid'],
        'pending'=>$summary['pustaka_pending'],
        'bg'=>'success',
        'icon'=>'mdi:book-open-page-variant'
    ],
    [
        'title'=>'Grand Total',
        'total'=>$summary['grand_total'],
        'bg'=>'primary',
        'icon'=>'mdi:currency-inr'
    ]
];
?>

<?php foreach ($cards as $c): ?>
<div style="flex:1 1 30%">
    <div class="card px-24 py-16 shadow-sm h-100">
        <div class="d-flex gap-3 align-items-center">
            <span class="w-40-px h-40-px bg-<?= $c['bg'] ?> text-white radius-8 d-flex align-items-center justify-content-center">
                <iconify-icon icon="<?= $c['icon'] ?>"></iconify-icon>
            </span>
            <div>
                <span class="text-secondary"><?= $c['title'] ?></span>
                <h6 class="fw-bold mb-1">₹ <?= number_format($c['total'],2) ?></h6>

                <?php if(isset($c['paid'])): ?>
                <small>
                    Paid:
                    <span class="text-success">₹ <?= number_format($c['paid'],2) ?></span><br>
                    Pending:
                    <span class="text-danger">₹ <?= number_format($c['pending'],2) ?></span>
                </small>
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
            <h5 class="fw-bold mb-3">Top YouTube Books</h5>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Book</th>
                        <th>Author</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php if($topBooks): $i=1; foreach($topBooks as $b): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= esc($b['book_title']) ?></td>
                        <td><?= esc($b['author_name']) ?></td>
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
            <h5 class="fw-bold mb-3">Language Wise Revenue</h5>
            <div style="height:320px">
                <canvas id="ytLangChart"></canvas>
            </div>
        </div>
    </div>
</div>

</div>
</div>

<?php
$langs = array_column($languageSales,'language_name');
$vals  = array_column($languageSales,'total_sales');
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('ytLangChart').getContext('2d');

new Chart(ctx,{
    type:'bar',
    data:{
        labels:<?= json_encode($langs) ?>,
        datasets:[{
            data:<?= json_encode($vals) ?>,
            backgroundColor:'#ef4444',
            borderRadius:10,
            barThickness:40
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{ legend:{display:false} },
        scales:{
            y:{
                beginAtZero:true,
                ticks:{ callback:(v)=>'₹ '+v.toLocaleString() }
            }
        }
    }
});
</script>

<?= $this->endSection(); ?>
