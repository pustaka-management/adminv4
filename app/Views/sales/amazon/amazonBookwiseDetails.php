<?= $this->extend('layout/layout1'); ?>

<?= $this->section('script'); ?>
<script>
    function printDetails() {
        var printContents = document.getElementById("invoice").innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
<?= $this->endSection(); ?>


<?= $this->section('content'); ?> 

<div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
    <a href="<?= base_url('dashboard/amazonpaperbackrevenue'); ?>" 
       class="btn btn-sm btn-success radius-8 d-inline-flex align-items-center gap-1">
        <iconify-icon icon="uil:arrow-left" class="text-xl"></iconify-icon>
        Back
    </a>

    <button type="button" 
            class="btn btn-sm btn-danger radius-8 d-inline-flex align-items-center gap-1" 
            onclick="printDetails()">
        <iconify-icon icon="basil:printer-outline" class="text-xl"></iconify-icon>
        Print
    </button>
</div>

<div class="card-body py-40">
    <div class="row justify-content-center" id="invoice">
        <div class="col-lg-8">
            <div class="shadow-4 border radius-8">
                <div class="py-28 px-20">
                    <div class="d-flex flex-column align-items-center text-center gap-3">
                        <h6 class="text-center">Amazon Book-Wise Summary</h6>
                        <div class="card p-3 shadow-2 radius-8 h-100 bg-gradient-end-1" style="width: fit-content;">
                            <div class="card-body p-0">
                                <div class="d-flex flex-wrap align-items-center justify-content-center gap-1 mb-8">

                                    <div class="d-flex align-items-center gap-2">
                                        <span
                                            class="mb-0 w-48-px h-48-px bg-success-100 text-success-600 flex-shrink-0
                                                   text-white d-flex justify-content-center align-items-center
                                                   rounded-circle h6 mb-0">
                                            <i class="ri-wallet-3-fill"></i>
                                        </span>

                                        <div>
                                            <h6 class="fw-semibold mb-2">
                                                Total Earnings :
                                                <?= isset($paperback_bookdetails['tot_count']['total_earnings'])
                                                    ? '₹' . number_format($paperback_bookdetails['tot_count']['total_earnings'], 2)
                                                    : '₹0.00'; ?>
                                            </h6>

                                            <span class="fw-medium text-secondary-light text-sm">
                                                Percentage :
                                                <?= isset($paperback_bookdetails['tot_count']['percentage_contribution'])
                                                    ? number_format($paperback_bookdetails['tot_count']['percentage_contribution'], 2) . '%'
                                                    : '0.00%'; ?>
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TOTAL SUMMARY -->
                    <div class="mt-24">
                        <div class="table-responsive scroll-sm">
                            <br><br>
                            <h6>Total Summary</h6>
                            <br>

                            <table class="table mb-4 contextual-table">
                                <thead>
                                    <tr class="table-warning">
                                        <th>Category</th>
                                        <th>Orders</th>
                                        <th>MRP Sales</th>
                                        <th>Shipping Credits</th>
                                        <th>TDS</th>
                                        <th>Selling Fees</th>
                                        <th>Other Transaction Fees</th>
                                        <th>Shipping Fees</th>
                                        <th>Royalty</th>
                                        <th>Total Earnings</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr class="table-info">
                                        <td>Total</td>
                                        <td><?= $paperback_bookdetails['total_earnings']['total_cnt']; ?></td>
                                        <td>₹<?= number_format($paperback_bookdetails['total_earnings']['total_sales'], 2); ?></td>
                                        <td>₹<?= number_format($paperback_bookdetails['total_earnings']['total_credits'], 2); ?></td>
                                        <td>₹<?= number_format($paperback_bookdetails['total_earnings']['total_tds'], 2); ?></td>
                                        <td>₹<?= number_format($paperback_bookdetails['total_earnings']['total_selling_fees'], 2); ?></td>
                                        <td>₹<?= number_format($paperback_bookdetails['total_earnings']['total_trans_fees'], 2); ?></td>
                                        <td>₹<?= number_format($paperback_bookdetails['total_earnings']['total_shipping_fees'], 2); ?></td>
                                        <td>₹<?= number_format($paperback_bookdetails['total_earnings']['total_royalty_value'], 2); ?></td>
                                        <td>₹<?= number_format($paperback_bookdetails['total_earnings']['total_earnings'], 2); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- MONTH-WISE SUMMARY -->
                        <br><br>
                        <div class="table-responsive scroll-sm">
                            <h6>Month-wise Summary</h6>
                            <br>

                            <table class="table mb-4 contextual-table">
                                <thead>
                                    <tr class="table-warning">
                                        <th>S.NO</th>
                                        <th>Month</th>
                                        <th>Total Orders</th>
                                        <th>Total Quantity</th>
                                        <th>Total Earnings</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php 
                                        $i = 1;
                                        $grand_total_orders = 0;
                                        $grand_total_quantity = 0;
                                        $grand_total_earnings = 0;
                                    ?>

                                    <?php foreach ($paperback_bookdetails['monthly_earnings'] as $row): ?>

                                        <?php 
                                            $grand_total_orders += $row['total_orders'];
                                            $grand_total_quantity += $row['total_quantity'];
                                            $grand_total_earnings += $row['monthly_total_earnings'];
                                        ?>

                                        <tr class="table-danger">
                                            <td><?= $i++; ?></td>
                                            <td><?= date('F Y', strtotime($row['month'])); ?></td>
                                            <td><?= number_format($row['total_orders']); ?></td>
                                            <td><?= number_format($row['total_quantity']); ?></td>
                                            <td><?= number_format($row['monthly_total_earnings'], 2); ?></td>
                                        </tr>

                                    <?php endforeach; ?>
                                </tbody>

                                <tfoot>
                                    <tr class="table-success">
                                        <th class="always-black">Total</th>
                                        <th></th>
                                        <th class="always-black"><?= number_format($grand_total_orders); ?></th>
                                        <th class="always-black"><?= number_format($grand_total_quantity); ?></th>
                                        <th class="always-black"><?= number_format($grand_total_earnings, 2); ?></th>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
