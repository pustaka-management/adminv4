<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<?php 
$Book_id = $book_id;  
$totalStockIn = 0;
$totalStockOut = 0;
$totalStock = 0; 
foreach ($details['list'] as $books_details) {
    $totalStockIn += $books_details['stock_in'];
    $totalStockOut += $books_details['stock_out'];
    $totalStock += $books_details['stock_in'] - $books_details['stock_out'];
}
?>

<div id="content" class="main-content">
    <div class="layout-px-spacing">

        <!-- ======= WIDER CARD LAYOUT START ======= -->
        <div class="row gy-4">

            <!-- Book Details Card -->
            <div class="col-xxl-6 col-lg-6 col-md-12">
                <div class="card shadow-none border bg-gradient-start-1 h-100">
                    <div class="card-body p-20">
                        <h5 class="text-center mb-3">Book Details</h5>
                        <p><strong>Book ID:</strong> <?= $Book_id ?></p>
                        <p><strong>Book Title:</strong> <?= $details['books']['book_title'] ?></p>
                        <p><strong>Regional Title:</strong> <?= $details['books']['regional_book_title'] ?></p>
                        <p><strong>Book Price:</strong> ₹<?= $details['books']['paper_back_inr'] ?></p>
                        <p><strong>Author ID:</strong> <?= $details['books']['author_id'] ?></p>
                        <p><strong>Author Name:</strong> <?= $details['books']['author_name'] ?></p>
                        <p><strong>Copyright Owner:</strong> <?= $details['books']['paper_back_copyright_owner'] ?></p>
                    </div>
                </div>
            </div>

            <!-- Stock Details Card -->
            <div class="col-xxl-6 col-lg-6 col-md-12">
                <div class="card shadow-none border bg-gradient-start-2 h-100">
                    <div class="card-body p-20">
                        <h5 class="text-center mb-3">Stock Details</h5>
                        <p><strong>Total Quantity:</strong> 
                            <?php if ($details['books']['quantity'] == $details['books']['bookfair'] + $details['books']['stock_in_hand']) { ?>
                                <span class="text-success"><?= $details['books']['quantity']; ?></span>
                            <?php } else { ?>
                                <?= $details['books']['quantity']; ?>
                            <?php } ?>
                        </p>
                        <p><strong>Book Fair / Store:</strong> 
                            <?= ($details['books']['bookfair']+$details['books']['bookfair2']+$details['books']['bookfair3']+$details['books']['bookfair4']+$details['books']['bookfair5']+
                            $details['books']['bookfair6']+$details['books']['bookfair7']+$details['books']['bookfair8']+$details['books']['bookfair9']) ?>
                        </p>
                        <p><strong>Lost Qty:</strong> <?= $details['books']['lost_qty'] ?></p>
                        <p><strong>Excess Qty:</strong> <?= $details['books']['excess_qty'] ?></p>
                        <p><strong>Current Stock:</strong> <?= $details['books']['stock_in_hand'] ?></p>

                        <hr class="my-2">
                        <p class="fw-bold text-center">(From pustaka_paperback_stock_ledger Table)</p>
                        <p><strong>Total Stock In:</strong> <?= $totalStockIn ?></p>
                        <p><strong>Total Stock Out:</strong> <?= $totalStockOut ?></p>
                        <p><strong>Available Stock:</strong> <?= $totalStock ?></p>

                        <?php
                        if ($details['books']['quantity'] == $totalStock) {
                            echo '<div class="alert alert-success text-center mt-3"><strong>Matched!</strong> Total Quantity equals Available Stock.</div>';
                        } else if ($details['books']['quantity'] > $totalStock) {
                            echo '<div class="alert alert-danger text-center mt-3"><strong>Mismatch!</strong> Total Quantity greater than Available Stock.</div>';
                        } else {
                            echo '<div class="alert alert-danger text-center mt-3"><strong>Mismatch!</strong> Total Quantity less than Available Stock.</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <br><br>
        <table class="table table-bordered mb-4 zero-config">
            <thead>
                <h6 class="text-center">Book Fair / Store Distribution</h6>
                <tr>
                    <th>Book Fair</th>
                    <th>Book Fair 2</th>
                    <th>Book Fair 3</th>
                    <th>Book Fair 4</th>
                    <th>Book Fair 5</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $details['books']['bookfair'] ?></td>
                    <td><?= $details['books']['bookfair2'] ?></td>
                    <td><?= $details['books']['bookfair3'] ?></td>
                    <td><?= $details['books']['bookfair4'] ?></td>
                    <td><?= $details['books']['bookfair5'] ?></td>
                </tr>
            </tbody>
        </table>

        <br>
        <table class="table table-bordered mb-4 zero-config">
            <thead>
                <h6 class="text-center">Book Stock Ledger</h6>
                <tr>
                    <th>S.No</th> 
                    <th>Order ID</th>
                    <th>Copyright Owner</th>
                    <th>Channel Type</th>
                    <th>Description</th>
                    <th>Stock In</th>
                    <th>Stock Out</th>
                    <th>Transaction Date</th>
                </tr>
            </thead>
            <tbody style="font-weight: normal;">
                <?php
                $i = 1;
                foreach ($details['list'] as $books_details) { ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= $books_details['order_id'] ?></td>
                        <td><?= $books_details['copyright_owner'] ?></td>
                        <td><?= $books_details['channel_type']?></td>
                        <td><?= $books_details['description'] ?></td>
                        <td><?= $books_details['stock_in'] ?></td>
                        <td><?= $books_details['stock_out'] ?></td>
                        <td><?= date('d-m-Y', strtotime($books_details['transaction_date'])); ?></td>  
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <br><br>
        <table class="table table-bordered mb-4 zero-config">
            <thead>
                <h6 class="text-center">Author Transaction</h6>
                <tr>
                    <th>S.No</th> 
                    <th>Order Date</th>
                    <th>Royalty Value</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody style="font-weight: normal;">
                <?php $i = 1; foreach ($details['author'] as $books_details) { ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= date('d-m-Y', strtotime($books_details['order_date']));?></td>
                        <td><?= $books_details['book_final_royalty_value_inr'] ?></td>
                        <td><?= $books_details['comments']?></td> 
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <br>
        <table class="table table-bordered mb-4 zero-config">
            <thead>
                <h6 class="text-center">Author Transaction (Before Sep 30)</h6>
                <tr>
                    <th>S.No</th> 
                    <th>Order Date</th>
                    <th>Channel</th>
                    <th>Royalty Value</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody style="font-weight: normal;">
                <?php $i = 1; foreach ($details['old_details'] as $books_details) { ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= date('d-m-Y', strtotime($books_details['order_date']));?></td>
                        <td><?= $books_details['channel'] ?></td>
                        <td><?= $books_details['book_final_royalty_value_inr'] ?></td>
                        <td><?= $books_details['comments']?></td> 
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <br><br>
        <h6 class="text-center">Book Fair Details</h6>
        <table class="table table-bordered mb-4 zero-config">
            <thead>
                <tr>
                    <th>S.No</th> 
                    <th>Order Date</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Royalty Value</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody style="font-weight: normal;">
                <?php $i = 1; foreach ($details['book_fair'] as $books_details) { ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= date('d-m-Y', strtotime($books_details['order_date']));?></td>
                        <td><?= $books_details['price'] ?></td>
                        <td><?= $books_details['quantity']?></td> 
                        <td><?= $books_details['final_royalty_value'] ?></td>
                        <td><?= $books_details['comments']?></td> 
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection(); ?>
