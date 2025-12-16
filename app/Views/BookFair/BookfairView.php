<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid mt-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Bookfair Details</h4>

        <a href="<?= base_url('adminv4/bookfairlist') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <!-- BOOK INFO -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h6 class="fw-bold mb-0">General Information</h6>
        </div>
        <div class="card-body">

            <div class="row mb-2">
                <div class="col-md-3 fw-semibold">Book Title:</div>
                <div class="col-md-9"><?= esc($book['book_title']) ?></div>
            </div>

            <div class="row mb-2">
                <div class="col-md-3 fw-semibold">ISBN:</div>
                <div class="col-md-9"><?= esc($book['paper_back_isbn']) ?></div>
            </div>

            <div class="row mb-2">
                <div class="col-md-3 fw-semibold">Author:</div>
                <div class="col-md-9"><?= esc($book['author_name']) ?></div>
            </div>

        </div>
    </div>

    <!-- ALLOCATION TABLE -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h6 class="fw-bold mb-0">BookFair Allocated Books</h6>
        </div>

        <div class="card-body p-0">
            <table class="zero-config table table-hover mt-4">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>BookFair</th>
                        <th>Allocated Qty</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($allocations)): ?>
                    <?php $i=1; foreach ($allocations as $a): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= esc($a['bookfair_name']) ?></td>
                            <td class="fw-bold"><?= $a['quantity'] ?></td>
                            <td><?= date('Y-m-d', strtotime($a['created_date'])) ?></td>
                        </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-2">No allocation data found</td>
                    </tr>
                <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- SALES TABLE -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h6 class="fw-bold mb-0">BookFair Sales</h6>
        </div>

        <div class="card-body p-0">
            <table class="zero-config table table-hover mt-4">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>BookFair</th>
                        <th>Sold Qty</th>
                        <th>Date</th>
                        
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($sales)): ?>
                    <?php $i=1; foreach ($sales as $s): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= esc($s['book_fair_name']) ?></td>
                            <td class="fw-bold text-success"><?= $s['quantity'] ?></td>
                           <td><?= date('Y-m-d', strtotime($s['book_fair_start_date'])) ?></td>
                        </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-2">No sales data found</td>
                    </tr>
                <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?= $this->endSection(); ?>
