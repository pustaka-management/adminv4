<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>
<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="page-header">
            <div class="page-title row">
                <div class="col">
                </div>
                <div class="col-3">
                    <a href="paperbackamazonorder" class="btn btn-info mb-2 mr-2">Create New Amazon Orders</a>
                    <a href="<?= base_url('orders/ordersdashboard'); ?>" 
                        class="btn btn-outline-secondary btn-sm float-end">
                            ← Back
                    </a>
                </div>
            </div>
        </div>
        <br><br>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4 h-100">
                    <div class="card-header border-bottom bg-base py-16 px-24 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Amazon Orders Summary</h5>
                    </div>
                    <div class="card-body p-3">
                        <table class="table colored-row-table mb-0">
                            <thead>
                                <tr>
                                    <th class="bg-base">Status</th>
                                    <th class="bg-base">Total Orders</th>
                                    <th class="bg-base">Total Titles</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="bg-primary-light">In Progress</td>
                                    <td class="bg-primary-light">
                                        <?= $amazon_summary['in_progress'][0]['completed_orders'] ?? 0 ?>
                                    </td>
                                    <td class="bg-primary-light">
                                        <?= $amazon_summary['in_progress'][0]['total_titles'] ?? 0 ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-success-focus">Completed</td>
                                    <td class="bg-success-focus">
                                        <?= $amazon_summary['completed'][0]['completed_orders'] ?? 0 ?>
                                    </td>
                                    <td class="bg-success-focus">
                                        <?= $amazon_summary['completed'][0]['total_titles'] ?? 0 ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-danger-focus">Cancelled</td>
                                    <td class="bg-danger-focus">
                                        <?= $amazon_summary['cancelled'][0]['cancel_orders'] ?? 0 ?>
                                    </td>
                                    <td class="bg-danger-focus">
                                        <?= $amazon_summary['cancelled'][0]['total_titles'] ?? 0 ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-warning-focus">Returned</td>
                                    <td class="bg-warning-focus">
                                        <?= $amazon_summary['return'][0]['return_orders'] ?? 0 ?>
                                    </td>
                                    <td class="bg-warning-focus">
                                        <?= $amazon_summary['return'][0]['total_titles'] ?? 0 ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>    
                    </div>
                </div></div>
                <div class="col-md-6">
                    <div class="card h-100 p-0">
                        <div class="card-header border-bottom bg-base py-16 px-24 d-flex justify-content-between align-items-center">
                            <h6 class="text-lg fw-semibold mb-0">Amazon Orders Month-wise</h6>
                            <form method="get">
                                <select name="filter" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="all" <?= ($filter=='all'?'selected':'') ?>>Monthwise (All)</option>
                                    <option value="current_fy" <?= ($filter=='current_fy'?'selected':'') ?>>Current FY</option>
                                    <option value="previous_fy" <?= ($filter=='previous_fy'?'selected':'') ?>>Previous FY</option>
                                </select>
                            </form>
                        </div>
                        <div class="card-body p-24">
                            <div id="amazonChart" style="margin-left: 10px; margin-right: 10px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br><br>
        <h6 class="text-center"><u>Amazon: In progress Orders</u></h6>
        <table class="table zero-config">
            <thead>
                <tr>
                    <th>S.NO</th>
                    <th>Order id</th>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Copies</th>
                    <th>Author name</th>
                    <th>Ship Date</th>
                    <th>Stock In Hand</th>
                    <th>Qty Details</th>
                    <th>Stock state</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody style="font-weight: normal;">
                <?php
                $i = 1;
                foreach ($amazon_orderbooks['in_progress'] as $order_books) {
                    ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                            <a href="<?= base_url('paperback/amazonorderdetails/' . $order_books['amazon_order_id']) ?>" target="_blank">
                                <?php echo $order_books['amazon_order_id'] ?>
                            </a>
                        </td>
                        <td><?php echo $order_books['book_id'] ?></td>
                        <td><?php echo $order_books['book_title'] ?></td>
                        <td><?php echo $order_books['quantity'] ?></td>
                        <td><?php echo $order_books['author_name'] ?></td>
                        <td><?php echo date('d-m-Y', strtotime($order_books['ship_date'])) ?></td>
                        <td><?php echo $order_books['stock_in_hand'] ?></td>
                        <td>
                            Ledger: <?php echo $order_books['qty'] ?><br>
                            Fair / Store: <?php echo ($order_books['bookfair'] + $order_books['bookfair2'] + $order_books['bookfair3'] + $order_books['bookfair4'] + $order_books['bookfair5']) ?><br>
                                <span style="color:#008000;">Excess: <?php echo abs($order_books['excess_qty']) ?></span><br>
                                <span style="color:#ff0000;">Lost: <?php echo $order_books['lost_qty'] ?><br></span>
                        </td>
                        <?php
                        $stockStatus = ($order_books['quantity'] <= ($order_books['stock_in_hand'] )) ? 'IN STOCK' : 'OUT OF STOCK';
                        $recommendationStatus = "";
                        if ($stockStatus == 'IN STOCK') {
                    
                        } else {
                            // OUT OF STOCK
                            if ($order_books['excess_qty'] > 0) {
                                $recommendationStatus =
                                    "Print using <span style='color:#008000;'>EXCESS</span> Qty! Initiate Print Also";
                            } 
                            else if ($order_books['lost_qty'] > 0) {
                                $recommendationStatus =
                                    "Print using <span style='color:#ff0000;'>LOST</span> Qty! No Initiate to Print";
                            } 
                            else {
                                $recommendationStatus =
                                    "";
                            }
                        }

                        ?>
                        <td>
                            <?php echo $stockStatus ?>
                            <br><span style="color:#0000ff;">
                                <?php echo $recommendationStatus; ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($stockStatus == 'OUT OF STOCK') { ?>
                                <a href="<?php echo base_url() . "paperback/paperbackprintstatus" ?>"
                                    style="background-color: purple; color: white; border: none; padding: 3px 8px; font-size: 12px; text-decoration: none; display: inline-block;"
                                    target="_blank">Status</a>
                                <br><br>
                                <a href="<?php echo base_url() . "paperback/initiateprintdashboard/" . $order_books['book_id'] ?>"
                                    style="background-color: #ffc107; color: black; border: none; padding: 3px 8px; font-size: 12px; text-decoration: none; display: inline-block;"
                                    target="_blank">Print</a>
                            <?php } else { ?>
                                <a href="" onclick="mark_ship('<?php echo $order_books['amazon_order_id'] ?>','<?php echo $order_books['book_id'] ?>')"
                                    style="background-color: #28a745; color: white; border: none; padding: 3px 8px; font-size: 12px; text-decoration: none; display: inline-block;">Ship</a>
                                <br><br>
                                <a href="" onclick="mark_cancel('<?php echo $order_books['amazon_order_id'] ?>','<?php echo $order_books['book_id'] ?>')"
                                    style="background-color: #dc3545; color: white; border: none; padding: 3px 8px; font-size: 12px; text-decoration: none; display: inline-block;">Cancel</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <br><br>
        <h6 class="text-center"><u>Amazon: Completed Orders</u>
            <a href="<?php echo base_url(); ?>paperback/totalamazonordercompleted" class="bs-tooltip " title="<?php echo 'View all Completed Books' ?>" target=_blank>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="blue" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-external-link">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                    <polyline points="15 3 21 3 21 9"></polyline>
                    <line x1="10" y1="14" x2="21" y2="3"></line>
                </svg>
            </a>
        </h6>
        <h6 class="text-center">( Shows for 30 days from date of shipment )</h6>
        <table class="table table-hover mb-4 zero-config">
            <thead>
                <tr>
                    <th>S.NO</th>
                    <th>Order id</th>
                    <th>Book ID</th>
                    <th>title</th>
                    <th>Author name</th>
                    <th>Shipped Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody style="font-weight: normal;">
                <?php $i = 1;
                foreach ($amazon_orderbooks['completed'] as $order_books) { ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                            <a href="<?= base_url('paperback/amazonorderdetails/' . $order_books['amazon_order_id']) ?>" target="_blank">
                                <?php echo $order_books['amazon_order_id'] ?>
                            </a>
                        </td>
                        <td><?php echo $order_books['book_id'] ?></td>
                        <td><?php echo $order_books['book_title'] ?></td>
                        <td><?php echo $order_books['author_name'] ?></td>
                        <td><?php echo date('d-m-Y', strtotime($order_books['ship_date'])) ?></td>
                        <td style="text-align: center;">
                            <a href="" onclick="mark_return('<?php echo $order_books['amazon_order_id'] ?>','<?php echo $order_books['book_id'] ?>')" class="btn btn-primary mb-2 mr-2">Return</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <br><br>
        <table class="table table-hover mb-4 zero-config">
            <thead>
                <h6 class="text-center"><u>Amazon: Cancel Orders</u></h6>
                <tr>
                    <th>S.NO</th>
                    <th>Order id</th>
                    <th>Book ID</th>
                    <th>title</th>
                    <th>Author name</th>
                    <th>Cancel Date</th>
                </tr>
            </thead>
            <tbody style="font-weight: normal;">
                <?php $i = 1;
                foreach ($amazon_orderbooks['cancel'] as $order_books) { ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                            <a href="<?= base_url('paperback/amazonorderdetails/' . $order_books['amazon_order_id']) ?>" target="_blank">
                                <?php echo $order_books['amazon_order_id'] ?>
                            </a>
                        </td>
                        <td><?php echo $order_books['book_id'] ?></td>
                        <td><?php echo $order_books['book_title'] ?></td>
                        <td><?php echo $order_books['author_name'] ?></td>
                        <td>
                            <?php
                            if ($order_books['date'] == NULL) {
                                echo "";
                            } else {
                                echo date('d-m-Y', strtotime($order_books['date']));
                            } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <br><br>
        <table class="table table-hover mb-4 zero-config">
            <thead>
                <h6 class="text-center"><u>Amazon: Return Orders</u></h6>
                <tr>
                    <th>S.NO</th>
                    <th>Order id</th>
                    <th>Book ID</th>
                    <th>title</th>
                    <th>Author name</th>
                    <th>Return Date</th>
                </tr>
            </thead>
            <tbody style="font-weight: normal;">
                <?php $i = 1;
                foreach ($amazon_orderbooks['return'] as $order_books) { ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                            <a href="<?= base_url('paperback/amazonorderdetails/' . $order_books['amazon_order_id']) ?>" target="_blank">
                                <?php echo $order_books['amazon_order_id'] ?>
                            </a>
                        </td>
                        <td><?php echo $order_books['book_id'] ?></td>
                        <td><?php echo $order_books['book_title'] ?></td>
                        <td><?php echo $order_books['author_name'] ?></td>
                        <td>
                            <?php
                            if ($order_books['date'] == NULL) {
                                echo "";
                            } else {
                                echo date('d-m-Y', strtotime($order_books['date']));
                            } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Data from PHP
            const chartData = <?= json_encode($amazon_summary['chart']); ?>;

            // Extract arrays
            const months = chartData.map(item => item.order_month);
            const totalTitles = chartData.map(item => parseInt(item.total_titles));
            const totalMrp = chartData.map(item => parseFloat(item.total_mrp));

            var options = {
                chart: {
                    type: 'bar',
                    height: 400,
                    stacked: false,
                    toolbar: { show: false }
                },
                series: [
                    {
                        name: "Total Titles",
                        type: 'column',
                        data: totalTitles
                    }
                ],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '40%',
                        endingShape: 'rounded'
                    }
                },
                xaxis: {
                    categories: months,
                    title: { text: 'Order Month' }
                },
                yaxis: [
                    {
                        show: false,
                        title: { text: "" },
                        labels: {
                            formatter: function (val) { return val.toLocaleString(); }
                        }
                    }
                ],
                dataLabels: {
                    enabled: false
                },
                colors: ['#1E90FF'],
                tooltip: {
                    shared: true,
                    intersect: false,
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'center'
                }
            };

            var chart = new ApexCharts(document.querySelector("#amazonChart"), options);
            chart.render();
        });
    var base_url = "<?= base_url(); ?>";

    function mark_ship(amazon_order_id, book_id) {
        $.ajax({
            url: base_url + 'paperback/markshipped',
            type: 'POST',
            data: {
                "amazon_order_id": amazon_order_id,
                "book_id": book_id,
            },
            success: function(data) {
                if (data == 1) {
                    alert("Completed Successfully!!");
                } else {
                    alert("Unknown error!! Check again!");
                }
            }
        });
    }

    function mark_cancel(amazon_order_id, book_id) {
        $.ajax({
            url: base_url + 'paperback/markcancel',
            type: 'POST',
            data: {
                "amazon_order_id": amazon_order_id,
                "book_id": book_id,
            },
            success: function(data) {
                if (data == 1) {
                    alert("Shipping Cancelled!!");
                } else {
                    alert("Unknown error!! Check again!");
                }
            }
        });
    }

    function mark_return(amazon_order_id, book_id) {
        $.ajax({
            url: base_url + 'paperback/markreturn',
            type: 'POST',
            data: {
                "amazon_order_id": amazon_order_id,
                "book_id": book_id,
            },
            success: function(data) {
                if (data == 1) {
                    alert("Restored Successfully!!");
                } else {
                    alert("Unknown error!! Check again!");
                }
            }
        });
    }
</script>
<?= $this->endSection(); ?>