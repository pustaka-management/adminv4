<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?> 

<div class="row gy-4">
    <div class="d-flex justify-content-end mb-2">
        <a href="<?= base_url('orders/uploadForm') ?>" 
            class="btn btn-sm btn-warning radius-8">
            Create Bulk Order
        </a>
       
<a href="<?= base_url('paperback/ordersdashboard') ?>" class="btn btn-primary btn-sm">
 View order
</a>
    </div>
    <form method="GET">
        <select name="fy" onchange="this.form.submit()" class="form-select" style="width:200px;">
            <option value="all" <?= ($fy=='all')?'selected':'' ?>>ALL</option>
            <option value="current" <?= ($fy=='current')?'selected':'' ?>>Current FY</option>
            <option value="previous" <?= ($fy=='previous')?'selected':'' ?>>Previous FY</option>
        </select>
    </form>

    <div class="col-xxl-8">
        <div class="row gy-4">
            <!-- Online -->
            <div class="col-xxl-4 col-sm-6">
                <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-1">
                    <a href="<?= route_to('paperback/onlineorderbooksstatus') ?>">
                        <div class="card-body p-0">

                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8 position-relative">
                                
                                <!-- Tooltip Button -->
                                <button type="button" 
                                    class="btn btn-success-100 text-success-600 radius-8 px-32 py-11 tooltip-button"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    data-bs-custom-class="tooltip-success"
                                    data-bs-title="create order"
                                    style="position:absolute; top:0; right:0;">
                                    +
                                </button>

                                <div class="d-flex align-items-center gap-2">
                                    <span class="mb-0 w-48-px h-48-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                        <iconify-icon icon="mingcute:user-follow-fill" class="icon"></iconify-icon>
                                    </span>
                                    <div>
                                        <span class="mb-2 fw-medium text-secondary-light text-sm">Online</span>
                                        <h6 class="fw-semibold"><?= $orders_dashboard['online']['units']; ?></h6>
                                    </div>
                                </div>
                            </div>

                            <p class="text-sm mb-0">
                                <?php $formatter = new NumberFormatter("en_IN", NumberFormatter::DECIMAL);
                                $salesFormatted = $formatter->format($orders_dashboard['online']['sales']); ?>

                                <strong>Titles:</strong>
                                <span class="bg-warning-focus px-1 rounded-2 fw-medium text-warning-main text-sm">
                                    <?= $orders_dashboard['online']['titles']; ?>
                                </span><br>

                                <strong>Sales:</strong>
                                <span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm">
                                    ₹<?= $salesFormatted; ?>
                                </span><br>

                                <strong>Total Orders:</strong>
                                <span class="bg-info-focus px-1 rounded-2 fw-medium text-info-main text-sm">
                                    <?= $orders_dashboard['online']['total_orders']; ?>
                                </span><br>
                            </p>

                        </div>
                    </a>
                </div>
            </div>
            <!-- Offline -->
            <div class="col-xxl-4 col-sm-6">
                <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-2"
                    onclick="window.location.href='<?= route_to('paperback/offlineorderbooksstatus') ?>'"
                    style="cursor: pointer; position: relative;">
                    

                    <!-- PLUS BUTTON -->
                    <button type="button"
                        class="btn btn-success-100 text-success-600 radius-8 px-32 py-11 tooltip-button"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        data-bs-custom-class="tooltip-success"
                        data-bs-title="create order"
                        style="position: absolute; top: 15px; right: 10px; z-index: 10;"
                        onclick="event.stopPropagation(); window.location.href='<?= base_url('paperback/offlineorderbooksdashboard'); ?>'">
                        +
                    </button>


                    <div class="card-body p-0">

                        <div class="d-flex align-items-center gap-2 mb-8">
                            <span class="w-48-px h-48-px bg-success-main text-white d-flex justify-content-center align-items-center rounded-circle h6">
                                <iconify-icon icon="mingcute:user-follow-fill" class="icon"></iconify-icon>
                            </span>
                            <div>
                                <span class="text-secondary-light text-sm">Offline</span>
                                <h6 class="fw-semibold"><?= $orders_dashboard['offline']['units']; ?></h6>
                            </div>
                        </div>

                        <p class="text-sm mb-0">
                            <?php $salesFormatted = $formatter->format($orders_dashboard['offline']['sales']); ?>

                            <strong>Titles:</strong>
                            <span class="bg-warning-focus px-1 rounded-2 fw-medium text-warning-main text-sm">
                                <?= $orders_dashboard['offline']['titles']; ?>
                            </span><br>

                            <strong>Sales:</strong>
                            <span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm">
                                ₹<?= $salesFormatted; ?>
                            </span><br>

                            <strong>Total Orders:</strong>
                            <span class="bg-info-focus px-1 rounded-2 fw-medium text-info-main text-sm">
                                <?= $orders_dashboard['offline']['total_orders']; ?>
                            </span><br>
                        </p>

                    </div>
                </div>
            </div>
            <!-- Amazon -->
            <div class="col-xxl-4 col-sm-6">
                <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-3"
                    onclick="window.location.href='<?= route_to('paperback/amazonorderbooksstatus') ?>'"
                    style="cursor:pointer; position:relative;">

                    <!-- PLUS BUTTON -->
                    <button type="button"
                        class="btn btn-success-100 text-success-600 radius-8 px-32 py-11 tooltip-button"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        data-bs-custom-class="tooltip-success"
                        data-bs-title="create order"
                        style="position:absolute; top:15px; right:10px; z-index:10;"
                        onclick="event.stopPropagation(); window.location.href='<?= base_url('paperback/paperbackamazonorder'); ?>'">
                        +
                    </button>

                    <div class="card-body p-0">

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8 position-relative">

                            <div class="d-flex align-items-center gap-2">
                                <span class="mb-0 w-48-px h-48-px bg-yellow text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6">
                                    <iconify-icon icon="iconamoon:discount-fill" class="icon"></iconify-icon>
                                </span>
                                <div>
                                    <span class="mb-2 fw-medium text-secondary-light text-sm">Amazon</span>
                                    <h6 class="fw-semibold"><?= $orders_dashboard['amazon']['units']; ?></h6>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm mb-0">
                            <strong>Titles:</strong>
                            <span class="bg-warning-focus px-1 rounded-2 fw-medium text-warning-main text-sm">
                                <?= $orders_dashboard['amazon']['titles']; ?>
                            </span><br>

                            <strong>Total Orders:</strong>
                            <span class="bg-info-focus px-1 rounded-2 fw-medium text-info-main text-sm">
                                <?= $orders_dashboard['amazon']['total_orders']; ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            <!-- Flipkart -->
            <div class="col-xxl-4 col-sm-6">
                <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-4"
                    onclick="window.location.href='<?= route_to('paperback/flipkartorderbooksstatus') ?>'"
                    style="cursor:pointer; position:relative;">

                    <!-- PLUS BUTTON -->
                    <button type="button"
                        class="btn btn-success-100 text-success-600 radius-8 px-32 py-11 tooltip-button"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        data-bs-custom-class="tooltip-success"
                        data-bs-title="create order"
                        style="position:absolute; top:15px; right:10px; z-index:10;"
                        onclick="event.stopPropagation(); window.location.href='<?= base_url('paperback/paperbackflipkartorder'); ?>'">
                        +
                    </button>
                    <div class="card-body p-0">

                        <div class="d-flex align-items-center gap-2 mb-8">
                            <span class="w-48-px h-48-px bg-purple text-white d-flex justify-content-center align-items-center rounded-circle h6">
                                <iconify-icon icon="mdi:message-text" class="icon"></iconify-icon>
                            </span>
                            <div>
                                <span class="text-secondary-light text-sm mb-2">Flipkart</span>
                                <h6 class="fw-semibold"><?= $orders_dashboard['flipkart']['units']; ?></h6>
                            </div>
                        </div>
                        <p class="text-sm mb-0">
                            <strong>Titles:</strong>
                            <span class="bg-warning-focus px-1 rounded-2 fw-medium text-warning-main text-sm">
                                <?= $orders_dashboard['flipkart']['titles']; ?>
                            </span><br>

                            <strong>Total Orders:</strong>
                            <span class="bg-info-focus px-1 rounded-2 fw-medium text-info-main text-sm">
                                <?= $orders_dashboard['flipkart']['total_orders']; ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            <!-- Author -->
            <div class="col-xxl-4 col-sm-6">
                <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-5"
                    onclick="window.location.href='<?= route_to('paperback/authororderbooksstatus') ?>'"
                    style="cursor:pointer; position:relative;">

                    <!-- PLUS BUTTON -->
                    <button type="button"
                        class="btn btn-success-100 text-success-600 radius-8 px-32 py-11 tooltip-button"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        data-bs-custom-class="tooltip-success"
                        data-bs-title="create order"
                        style="position:absolute; top:15px; right:10px; z-index:10;"
                        onclick="event.stopPropagation(); window.location.href='<?= base_url('paperback/authorlistdetails'); ?>'">
                        +
                    </button>

                    <div class="card-body p-0">

                        <div class="d-flex align-items-center gap-2 mb-8">
                            <span class="w-48-px h-48-px bg-pink text-white d-flex justify-content-center align-items-center rounded-circle h6">
                                <iconify-icon icon="mdi:leads" class="icon"></iconify-icon>
                            </span>
                            <div>
                                <span class="text-secondary-light text-sm mb-2">Author</span>
                                <h6 class="fw-semibold"><?= $orders_dashboard['author']['units']; ?></h6>
                            </div>
                        </div>

                        <p class="text-sm mb-0">
                            <?php $salesFormatted = $formatter->format($orders_dashboard['author']['sales']); ?>

                            <strong>Titles:</strong>
                            <span class="bg-warning-focus px-1 rounded-2 fw-medium text-warning-main text-sm">
                                <?= $orders_dashboard['author']['titles']; ?>
                            </span><br>

                            <strong>Sales:</strong>
                            <span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm">
                                ₹<?= $salesFormatted; ?>
                            </span><br>

                        <strong>Total Orders:</strong>
                        <span class="bg-info-focus px-1 rounded-2 fw-medium text-info-main text-sm">
                            <?= $orders_dashboard['author']['total_orders']; ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
        <!-- BookShop -->
        <div class="col-xxl-4 col-sm-6">
            <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-6"
                onclick="window.location.href='<?= route_to('paperback/bookshoporderbooksstatus') ?>'"
                style="cursor:pointer; position:relative;">

                    <!-- PLUS BUTTON -->
                    <button type="button"
                        class="btn btn-success-100 text-success-600 radius-8 px-32 py-11 tooltip-button"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        data-bs-custom-class="tooltip-success"
                        data-bs-title="create order"
                        style="position:absolute; top:15px; right:10px; z-index:10;"
                        onclick="event.stopPropagation(); window.location.href='<?= base_url('paperback/bookshopordersdashboard'); ?>'">
                        +
                    </button>

                    <div class="card-body p-0">

                        <div class="d-flex align-items-center gap-2 mb-8">
                            <span class="w-48-px h-48-px bg-cyan text-white d-flex justify-content-center align-items-center rounded-circle h6">
                                <iconify-icon icon="streamline:bag-dollar-solid" class="icon"></iconify-icon>
                            </span>
                            <div>
                                <span class="text-secondary-light text-sm mb-2">BookShop</span>
                                <h6 class="fw-semibold"><?= $orders_dashboard['bookshop']['units']; ?></h6>
                            </div>
                        </div>
                        <p class="text-sm mb-0">
                            <?php $salesFormatted = $formatter->format($orders_dashboard['bookshop']['sales']); ?>

                            <strong>Titles:</strong>
                            <span class="bg-warning-focus px-1 rounded-2 fw-medium text-warning-main text-sm">
                                <?= $orders_dashboard['bookshop']['titles']; ?>
                            </span><br>

                            <strong>Sales:</strong>
                            <span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm">
                                ₹<?= $salesFormatted; ?>
                            </span><br>

                            <strong>Total Orders:</strong>
                            <span class="bg-info-focus px-1 rounded-2 fw-medium text-info-main text-sm">
                                <?= $orders_dashboard['bookshop']['total_orders']; ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            <!-- BookFair -->
            <div class="col-xxl-6 col-sm-6">
                <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-5">

                    <a href="<?= base_url('combobookfair/bookfairbookshoppendingorders') ?>" class="text-decoration-none text-dark">

                        <div class="card-body p-0">

                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8 position-relative">

                                <!-- Tooltip Button -->
                                <button type="button"
                                    class="btn btn-success-100 text-success-600 radius-8 px-32 py-11 tooltip-button"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    data-bs-custom-class="tooltip-success"
                                    data-bs-title="create order"
                                    style="position:absolute; top:0; right:0;">
                                    +
                                </button>

                                <div class="d-flex align-items-center gap-2">
                                    <span class="mb-0 w-48-px h-48-px bg-danger-500 flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                        <iconify-icon icon="mingcute:user-follow-fill" class="icon"></iconify-icon>
                                    </span>
                                    <div>
                                        <span class="mb-2 fw-medium text-secondary-light text-sm">BookFair</span>
                                        <h6 class="fw-semibold"><?= $orders_dashboard['bookfair']['units']; ?></h6>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm mb-0">
                                <strong>Titles:</strong>
                                <span class="bg-warning-focus px-1 rounded-2 fw-medium text-warning-main text-sm">
                                    <?= $orders_dashboard['bookfair']['titles']; ?>
                                </span><br>

                                <strong>Sales:</strong>
                                <span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm">
                                    -
                                </span>
                            </p>
                        </div>
                    </a>
                </div>
            </div>
            <!-- Library -->
            <div class="col-xxl-6 col-sm-6">
                <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-3">
                    <a href="#">
                        <div class="card-body p-0">

                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8 position-relative">

                                <!-- Tooltip Button -->
                                <button type="button"
                                    class="btn btn-success-100 text-success-600 radius-8 px-32 py-11 tooltip-button"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    data-bs-custom-class="tooltip-success"
                                    data-bs-title="create order"
                                    style="position:absolute; top:0; right:0;">
                                    +
                                </button>

                                <div class="d-flex align-items-center gap-2">
                                    <span class="mb-0 w-48-px h-48-px bg-warning-500 flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                        <iconify-icon icon="mingcute:user-follow-fill" class="icon"></iconify-icon>
                                    </span>
                                    <div>
                                        <span class="mb-2 fw-medium text-secondary-light text-sm">Library</span>
                                        <h6 class="fw-semibold"><?= $orders_dashboard['library']['units']; ?></h6>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm mb-0">
                                <strong>Titles:</strong>
                                <span class="bg-warning-focus px-1 rounded-2 fw-medium text-warning-main text-sm">
                                    <?= $orders_dashboard['library']['titles']; ?>
                                </span><br>

                                <strong>Sales:</strong>
                                <span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm">
                                    -
                                </span>
                            </p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?= $this->extend('layout/layout1'); ?>

    <?= $this->section('script'); ?>
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]'); 
        const tooltipList = [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));

        $(document).ready(function() {
            $('.tooltip-button').each(function () {
                var tooltipButton = $(this);
                var tooltipContent = "Success Tooltip";

                tooltipButton.tooltip({
                    title: tooltipContent,
                    trigger: 'hover',
                    html: true
                });
            });
        });
    </script>
    <?= $this->endSection(); ?>
    <!-- Revenue Growth -->
    <div class="col-xxl-4 col-sm-12 mb-3 ">
        <div class="card h-100 radius-8 border-0 overflow-hidden">
            <div class="card-body p-24 bg-gradient-success">
                <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                    <h6 class="mb-2 fw-bold text-lg">POD Orders</h6>
                    <div>
                        <a href="<?= base_url('pod/podbookadd') ?>" class="btn btn-sm btn-primary radius-8">
                            Create order
                        </a>
                    </div>
                </div>
                <br>
                <div class="d-flex flex-wrap align-items-center mt-3">
                    <ul class="flex-shrink-0">
                        <li class="d-flex align-items-center gap-1 mb-20">
                            <span class="w-12-px h-12-px rounded-circle bg-danger-main"></span>
                            <span class="text-secondary-light text-sm fw-medium">
                                Not Started: <?= $pending_books['NotStarted']; ?>
                            </span>
                        </li>
                        <li class="d-flex align-items-center gap-1 mb-20">
                            <span class="w-12-px h-12-px rounded-circle bg-warning-main"></span>
                            <span class="text-secondary-light text-sm fw-medium">
                                In Progress: <?= $pending_books['PendingCount']; ?>
                            </span>
                        </li>
                        <li class="d-flex align-items-center gap-1 mb-20">
                            <span class="w-12-px h-12-px rounded-circle bg-primary-600"></span>
                            <span class="text-secondary-light text-sm fw-medium">
                                Pending Invoice: <?= $dashboard['invoice']['pending']; ?>
                            </span>
                        </li>
                    </ul>
                    <br>
                    <br>
                    <div id="donutChart" class="flex-grow-1 apexcharts-tooltip-z-none title-style circle-none"></div>
                </div>
            </div>
            <a href="<?= base_url('pod/dashboard') ?>" 
                class="text-decoration-none d-block bg-light text-center py-3 fw-semibold text-primary-600">
                    View Full Dashboard →
            </a>
        </div>
    </div>
</div>
<br><br><br>
<div class="page-title">
    <h6 class="text-center">Amazon, Flipkart, Offline and Online Order </h6>
    <table class="zero-config table table-hover mt-4">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Order Id</th>
                <th>BookId</th>
                <th>Channel</th>
                <th>Title</th>
                <th>Copies</th>
                <th>Author</th>
                <th>Order Date</th>
                <th>Ship Date</th>
                <th>Stock In Hand</th>
                <th>Qty Details</th>
                <th>Stock state</th>
            </tr>
        </thead>
        <tbody style="font-weight: normal;">
            <?php
            $i = 1;
            $tmp_order_id = null;
            $tmp_order_id_count = 0;
            foreach ($pending as $books_details) {
            ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td>
                        <?php
                        if ($books_details['channel'] == 'Offline') {
                        ?>
                            <a href="<?php echo base_url() . "paperback/offlineorderdetails/" . $books_details['order_id']?>" target="_blank">
                                <?php echo $books_details['order_id'] ?>
                            </a>
                        <?php
                        } else if ($books_details['channel'] == 'Online') {
                        ?>
                            <a href="<?php echo base_url() . "paperback/onlineorderdetails/" . $books_details['order_id'] ?>"target="_blank">
                                <?php echo $books_details['order_id'] ?>
                            </a>
                        <?php
                        } else if ($books_details['channel'] == 'Amazon') {
                        ?>
                            <a href="<?php echo base_url() . "paperback/amazonorderdetails/" . $books_details['order_id'] ?>"target="_blank">
                                <?php echo $books_details['order_id'] ?>
                            </a>
                        <?php
                        }else if ($books_details['channel'] == 'Flipkart') {
                        ?>
                            <a href="<?php echo base_url() . "paperback/flipkartorderdetails/" . $books_details['order_id'] ?>"target="_blank">
                                <?php echo $books_details['order_id'] ?>
                            </a>
                        <?php
                        }else{?>
                        <?php echo $books_details['order_id'] ?>
                        <?php
                        }
                        ?>
                        <br>
                        <?php echo $books_details['customer_name'] ?>
                    </td>
                    <td><?php echo $books_details['book_id'] ?></td>
                    <td><?php echo $books_details['channel'] ?></td>
                    <td><?php echo $books_details['book_title'] ?></td>
                    <td><?php echo $books_details['quantity'] ?></td>
                    <td><?php echo $books_details['author_name'] ?></td>

                    <td>
                        <?php
                        if ($books_details['order_date'] == NULL) {
                            echo '';
                        } else {
                            echo date('d-m-Y', strtotime($books_details['order_date']));
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($books_details['ship_date'] == NULL) {
                            echo '';
                        } else {
                            echo date('d-m-Y', strtotime($books_details['ship_date']));
                        }
                        ?>
                    </td>
                    <td><?php echo $books_details['stock_in_hand'] ?></td>
                    <td>
                        Ledger: <?php echo $books_details['qty'] ?><br>
                        Fair / Store: <?php echo $books_details['bookfair'] ?><br>
                    <?php if ($books_details['lost_qty'] < 0) { ?>
                        <span style="color:#008000;">Excess: <?php echo abs($books_details['lost_qty']) ?></span><br>
                    <?php } elseif ($books_details['lost_qty'] > 0) { ?>
                        <span style="color:#ff0000;">Lost: <?php echo $books_details['lost_qty'] ?><br></span>
                    <?php } ?>
                    </td>
                    
                    <?php
                    $stockStatus = $books_details['quantity'] <= ($books_details['stock_in_hand']+$books_details['lost_qty']) ? 'IN STOCK' : 'OUT OF STOCK';
                    $recommendationStatus = "";
                            
                    if ($books_details['quantity'] <= ($books_details['stock_in_hand']+$books_details['lost_qty']))
                    {
                        $stockStatus = "IN STOCK";
                        // Stock is available; check whether it is from lost qty
                        if ($books_details['quantity'] <= $books_details['stock_in_hand']) {
                            $stockStatus = "IN STOCK";
                            $recommendationStatus ="";
                        } else {
                            $stockStatus = "IN STOCK";
                            $recommendationStatus = "Print using </span><span style='color:#ff0000;'>LOST</span><span style='color:#0000ff;'> Qty! No Initiate to Print";
                        }
                    } else {
                        $stockStatus = "OUT OF STOCK";
                        // Stock not available; Check whether it is from excess qty
                        if ($books_details['quantity'] <= $books_details['stock_in_hand']) {
                            $stockStatus = "OUT OF STOCK";
                            $recommendationStatus = "Print using </span><span style='color:#008000;'>EXCESS</span><span style='color:#0000ff;'> Qty! Initiate Print Also";
                        } else {
                            $stockStatus = "OUT OF STOCK";
                            $recommendationStatus ="";
                        }
                    }
                    
                    ?>
                    <td>
                        <center>
                        <?php echo $stockStatus; ?>
                        <br><span style="color:#0000ff;"><?php 
                            if (($stockStatus == 'OUT OF STOCK') && ($recommendationStatus == '')) {
                            ?><br><br>
                                <div class="d-flex justify-content-between  gap-2">
                                    <a href="<?php echo base_url() . "paperback/paperbackprintstatus" ?>" 
                                    class="btn btn-success-800 radius-6 small px-8 py-2" target="_blank">
                                    Status
                                    </a>
                                    <a href="<?php echo base_url() . "paperback/initiateprintdashboard/" . $books_details['book_id'] ?>" 
                                    class="btn btn-warning-800 radius-6 small px-8 py-2" target="_blank">
                                    Print
                                    </a>
                                </div>

                            <?php 
                                } else {
                                    echo $recommendationStatus;
                                } 
                            ?></span>
                        </center>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>
<br><br><br>
<div class="page-title">
    <h6 class="text-center">Author Order & Bookshop Order </h6>
    <table class="zero-config table table-hover mt-4">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Order Id</th>
                <th>Order Date</th>
                <th>Channel</th>
                <th>No.Of.Title</th>
                <th>Invoice Number</th>
                <th>Ship Date</th>
            </tr>
        </thead>
        <tbody style="font-weight: normal;">
            <?php
            $i = 1;
            foreach ($orders as $books_details) {
            ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td>
                        <?php
                        if ($books_details['channel'] == 'Author Order') {
                        ?>
                            <a href="<?php echo base_url() . "paperback/authororderdetails/" . $books_details['order_id']?>" target="_blank">
                                <?php echo $books_details['order_id'] ?>
                            </a>
                        <?php
                        } else if ($books_details['channel'] == 'Bookshop Order') {
                        ?>
                            <a href="<?php echo base_url() . "paperback/bookshoporderdetails/" . $books_details['order_id'] ?>"target="_blank">
                                <?php echo $books_details['order_id'] ?>
                            </a>
                        <?php
                        }else{?>
                        <?php echo $books_details['order_id'] ?>
                        <?php
                        }
                        ?>
                        <br>
                        <?php echo $books_details['customer_name'] ?>
                    </td>
                    <td>
                        <?php
                        if ($books_details['order_date'] == NULL) {
                            echo '';
                        } else {
                            echo date('d-m-Y', strtotime($books_details['order_date']));
                        }
                        ?>
                    </td>
                    <td><?php echo $books_details['channel'] ?></td>
                    <td><?php echo $books_details['no_of_title'] ?></td>

                    <td>
                    <?php
                        if ($books_details['invoice_number'] == NULL) {
                        echo 'Not Avaliable';
                    } else {
                        echo $books_details['invoice_number'] ;
                    }
                    ?></td>
                    <td>
                        <?php
                        if ($books_details['ship_date'] == NULL) {
                            echo '';
                        } else {
                            echo date('d-m-Y', strtotime($books_details['ship_date']));
                        }
                        ?>
                    </td>   
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>
<?= $this->endSection(); ?>
<?= $this->section('script'); ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var options = {
            chart: {
                type: 'donut',
                height: 250
            },
            labels: ['Not Started', 'In Progress', 'Pending Invoice'],
            series :[<?php echo $pending_books['NotStarted'];?>,<?php echo $pending_books['PendingCount'];?>,<?php echo $dashboard['invoice']['pending'];?>],
        
            colors: ['#d14747ff', '#ffc107', '#0d6efd'],
            legend: {
                show: false
            },
            dataLabels: {
                enabled: false
            },
            plotOptions: {
                pie: {
                    startAngle: -90,
                    endAngle: 90,
                    offsetY: 10
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#donutChart"), options);
        chart.render();
    });
</script>
<?= $this->endSection(); ?>
