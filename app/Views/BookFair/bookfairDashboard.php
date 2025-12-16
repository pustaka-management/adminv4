<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<style>
    .section-card { border-radius: 10px; }
    .table thead th { font-size: 13px; font-weight: 600; }
    .table tbody td { font-size: 13px; }
    .badge { font-size: 11px; }
</style>

<div class="container-fluid mt-3">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">BookFair Dashboard</h4>

        <div class="d-flex gap-2">
            <button onclick="downloadExcel()" class="btn btn-success btn-sm" target="_blank">
                <i class="fas fa-file-excel me-1"></i> Download Excel
            </button>

            <a href="<?= base_url('stock/bookfairdashboard') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- PRIORITY TABS -->
    <div class="card section-card mb-4 shadow-sm">
        <div class="card-body p-3 d-flex flex-wrap gap-4">

            <label class="form-check d-flex align-items-center">
                <input class="form-check-input prioritySelector" 
                       type="radio" name="priority" value="high">
                <span class="ms-2 fw-semibold">High Priority</span>
            </label>

            <label class="form-check d-flex align-items-center">
                <input class="form-check-input prioritySelector" 
                       type="radio" name="priority" value="highmedium">
                <span class="ms-2 fw-semibold">High + Medium</span>
            </label>

            <label class="form-check d-flex align-items-center">
                <input class="form-check-input prioritySelector" 
                       type="radio" name="priority" value="all" checked>
                <span class="ms-2 fw-semibold">All Authors</span>
            </label>

        </div>
    </div>

    <!-- TABLE BOXES -->
    <?php 
    $boxes = [
        'high' => ['data' => $high, 'title' => 'High Priority Authors'],
        'highmedium' => ['data' => $highmedium, 'title' => 'High + Medium Authors'],
        'all' => ['data' => $all, 'title' => 'All Authors']
    ];

    foreach($boxes as $key => $box): 
    ?>
    <div id="<?= $key ?>Box" class="priority-box <?= $key=='all'?'':'d-none' ?>">

        <div class="card section-card mb-4 shadow-sm">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 fw-bold"><?= $box['title'] ?> (<?= count($box['data']) ?>)</h6>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0" id="<?= $key ?>Table">
                        <thead>
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Author</th>
                                <th>Book</th>
                                <th class="text-center">BookFairs</th>
                                <th class="text-center">Allocated</th>
                                <th class="text-center">Sold</th>
                                <th class="text-center">Recommended</th>
                                <th class="text-center pe-3">Stock</th>
                                <th class="text-center pe-3">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php 
                        $i=1; 
                        foreach($box['data'] as $row): 
                            $recommended = ($row['no_of_bookfairs']>0)
                                ? ceil($row['sales_qty']/$row['no_of_bookfairs'])
                                : 0;

                            // ---- ROW COLOR CONDITIONS ----
                            if ($row['stock_in_hand'] < 0) {
                                $rowClass = "table-danger"; // Red
                            } elseif ($row['no_of_bookfairs'] == 0) {
                                $rowClass = "table-warning"; // Yellow
                            } else {
                                $rowClass = "";
                            }
                        ?>
                            <tr class="<?= $rowClass ?>">
                                <td class="ps-3 fw-medium"><?= $i++ ?></td>

                                <td><?= esc($row['author_name']) ?></td>

                                <td>
                                    <?= esc($row['book_title']) ?>

                                    <?php if($row['priority_code']==1): ?>
                                        <span class="badge bg-danger ms-1">High</span>
                                    <?php elseif($row['priority_code']==2): ?>
                                      <span class="badge bg-warning-subtle border border-warning text-dark fw-semibold ms-1">Med</span>

                                    <?php endif; ?>
                                </td>

                                <td class="text-center"><?= $row['no_of_bookfairs'] ?></td>

                                <td class="text-center fw-semibold"><?= $row['allocated_qty'] ?></td>

                                <td class="text-center">
                                    <span class="badge bg-success"><?= $row['sales_qty'] ?></span>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-primary"><?= $recommended ?></span>
                                </td>

                                <td class="text-center fw-bold pe-3">
                                    <?= $row['stock_in_hand'] ?>
                                </td>
                                 <td class="text-center pe-3">
                                    <a href="<?= base_url('stock/bookfairview/'.$row['book_id']) ?>" 
                                       class="btn btn-sm btn-outline-primary" target="_blank">
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>
            </div>

        </div>
    </div>
    <?php endforeach; ?>

</div>

<!-- SCRIPT -->
<script>
function showBox(id) {
    document.querySelectorAll(".priority-box").forEach(b => b.classList.add("d-none"));
    document.getElementById(id).classList.remove("d-none");
}

let selectedType = "all";

document.querySelectorAll(".prioritySelector").forEach(btn => {
    btn.addEventListener("change", () => {
        selectedType = btn.value;

        showBox(
            btn.value === "high" 
                ? "highBox" 
                : btn.value === "highmedium" 
                    ? "highmediumBox" 
                    : "allBox"
        );
    });
});

showBox("allBox");

$(document).ready(function() {
    $('#highTable, #highmediumTable, #allTable').DataTable({
        "order": [],
        "pageLength": 10,
        "searching": true
    });
});

function downloadExcel() {
    window.location.href = "<?= base_url('stock/exportExcel') ?>?priority=" + selectedType;
}
</script>

<?= $this->endSection(); ?>
