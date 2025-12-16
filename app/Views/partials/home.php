<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

    <div class="row" style="display: flex; flex-wrap: wrap; gap: 20px;">

    <!-- ===== EBOOKS ===== -->
    <div style="flex: 1 1 calc(33.333% - 20px); box-sizing: border-box; padding: 15px; border: 1px solid #ddd; border-radius: 8px; background:bg-neutral-50;">
        <?php $pustaka = $ebooks_details['Pustaka'] ?? []; ?>

        <div style="display: flex; align-items: center; margin-bottom: 15px;">
           <iconify-icon icon="arcticons:books" width="40" height="40" style="margin-right: 10px; color: currentColor;"></iconify-icon>
            <div>
                <div style="font-size: 18px; font-weight: 600;">E-Books</div>
                <a href="<?= base_url('adminv4/bookDetails?type=ebook') ?>" style="font-size: 30px; color: inherit; text-decoration: none;">
                    <?= esc($pustaka['books_count'] ?? 0); ?>
                </a>
            </div>
        </div>

        <div style="display: flex; justify-content: space-around; margin-bottom: 15px;">
            <?php foreach (['active','inactive','withdrawn'] as $status): ?>
                <a href="<?= base_url('adminv4/bookDetails?type=ebook&status=' . $status) ?>"
                   style="text-align:center; flex:1; margin: 0 5px; color: inherit; text-decoration: none;">
                    <div style="font-size: 24px; font-weight: bold;"><?= esc($pustaka[$status] ?? 0); ?></div>
                    <div style="font-size: 14px; text-transform: capitalize;"><?= ucfirst($status) ?></div>
                </a>
            <?php endforeach; ?>
        </div>

        <button onclick="toggleSection('ebookBody', this)"
            style="width:100%; padding:8px; background:#007bff; border:none; text-align:left; cursor:pointer; font-weight:600; color:#fff;">
            View Channels ▼
        </button>

        <div id="ebookBody" style="display:none; margin-top:10px;">
            <table style="width:100%; border-collapse:collapse; font-size:14px;">
                <thead>
                    <tr>
                        <th style="border:1px solid #ccc; padding:6px; background:#333; color:#fff;">Channel</th>
                        <th style="border:1px solid #ccc; padding:6px; background:#333; color:#fff;">Count</th>
                    </tr>
                </thead>
                <tbody>
                   <?php foreach ($ebooks_details as $ebook): ?>
                        <tr>
                            <td style="border:1px solid #ccc; padding:6px;">
                                <?= esc($ebook['channel_name'] ?? '') ?>
                            </td>

                            <td style="border:1px solid #ccc; padding:6px;">
                                <?php if (($ebook['channel_name'] ?? '') === 'Pustaka'): ?>
                                    <?= esc($ebook['active'] ?? 0) ?>
                                <?php else: ?>
                                    <?= esc($ebook['books_count'] ?? 0) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ===== AUDIO BOOKS ===== -->
    <div style="flex: 1 1 calc(33.333% - 20px); box-sizing: border-box; padding: 15px; border: 1px solid #ddd; border-radius: 8px; background:bg-neutral-50;">
        <?php $audio = $audiobooks_details['audio'] ?? []; ?>

        <div style="display: flex; align-items: center; margin-bottom: 15px;">
              <iconify-icon icon="arcticons:panasonic-audio-connect" width="40" height="40" style="margin-right: 10px; color: currentColor;"></iconify-icon>
            <div>
                <div style="font-size: 18px; font-weight: 600;">Audio Books</div>
                <a href="<?= base_url('adminv4/bookDetails?type=audiobook') ?>" style="font-size: 30px; color: inherit; text-decoration: none;">
                    <?= esc($audio['books_count'] ?? 0); ?>
                </a>
            </div>
        </div>

        <div style="display: flex; justify-content: space-around; margin-bottom: 15px;">
            <?php foreach (['active','inactive','withdrawn'] as $status): ?>
                <a href="<?= base_url('adminv4/bookDetails?type=audiobook&status=' . $status) ?>"
                   style="text-align:center; flex:1; margin: 0 5px; color: inherit; text-decoration: none;">
                    <div style="font-size: 24px; font-weight: bold;"><?= esc($audio[$status] ?? 0); ?></div>
                    <div style="font-size: 14px; text-transform: capitalize;"><?= ucfirst($status) ?></div>
                </a>
            <?php endforeach; ?>
        </div>

        <button onclick="toggleSection('audioBody', this)"
            style="width:100%; padding:8px; background:#007bff; border:none; text-align:left; cursor:pointer; font-weight:600; color:#fff;">
            View Channels ▼
        </button>

        <div id="audioBody" style="display:none; margin-top:10px;">
            <table style="width:100%; border-collapse:collapse; font-size:14px;">
                <thead>
                    <tr>
                        <th style="border:1px solid #ccc; padding:6px; background:#333; color:#fff;">Channel</th>
                        <th style="border:1px solid #ccc; padding:6px; background:#333; color:#fff;">Count</th>
                    </tr>
                </thead>
                <tbody>
                   <?php foreach ($audiobooks_details as $ab): ?>
                        <?php if (($ab['channel_name'] ?? '') !== 'Audio'): ?>
                            <tr>
                                <td style="border:1px solid #ccc; padding:6px;"><?= esc($ab['channel_name'] ?? '') ?></td>
                                <td style="border:1px solid #ccc; padding:6px;"><?= esc($ab['books_count'] ?? 0) ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ===== PAPERBACK ===== -->
    <div style="flex: 1 1 calc(33.333% - 20px); box-sizing: border-box; padding: 15px; border: 1px solid #ddd; border-radius: 8px; background:bg-neutral-50;">
        <?php $paperback = $paperback_details['paperback'] ?? []; ?>

        <div style="display: flex; align-items: center; margin-bottom: 15px;">
           <iconify-icon icon="emojione-monotone:books" width="40" height="40" style="margin-right: 10px; color: currentColor;"></iconify-icon>
            <div>
                <div style="font-size: 18px; font-weight: 600;">Paperback Books</div>
                <a href="<?= base_url('adminv4/bookDetails?type=paperback') ?>" style="font-size: 30px; color: inherit; text-decoration: none;">
                    <?= esc($paperback['books_count'] ?? 0); ?>
                </a>
            </div>
        </div>

        <div style="display: flex; justify-content: space-around; margin-bottom: 15px;">
            <?php foreach (['active' => 'Active', 'rework' => 'Rework', 'pending' => 'Pending'] as $key => $label): ?>
                <a href="<?= base_url('adminv4/bookDetails?type=paperback&status=' . $key) ?>"
                   style="text-align:center; flex:1; margin: 0 5px; color: inherit; text-decoration: none;">
                    <div style="font-size: 24px; font-weight: bold;"><?= esc($paperback["{$key}_books"] ?? 0); ?></div>
                    <div style="font-size: 14px;"><?= $label ?></div>
                </a>
            <?php endforeach; ?>
        </div>

        <button onclick="toggleSection('paperbackBody', this)"
            style="width:100%; padding:8px; background:#007bff; border:none; text-align:left; cursor:pointer; font-weight:600; color:#fff;">
            View Channels ▼
        </button>

        <div id="paperbackBody" style="display:none; margin-top:10px;">
            <table style="width:100%; border-collapse:collapse; font-size:14px;">
                <thead>
                    <tr>
                        <th style="border:1px solid #ccc; padding:6px; background:#333; color:#fff;">Channel</th>
                        <th style="border:1px solid #ccc; padding:6px; background:#333; color:#fff;">Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paperback_details as $pb): ?>
                        <?php if (($pb['channel_name'] ?? '') !== 'Paperback'): ?>
                            <tr>
                                <td style="border:1px solid #ccc; padding:6px;"><?= esc($pb['channel_name'] ?? '') ?></td>
                                <td style="border:1px solid #ccc; padding:6px;"><?= esc($pb['books_count'] ?? 0) ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<br><br>
   <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 20px;">

        <!-- Authors Status -->
        <!-- <div class="author-status list-view" style="flex: 1; min-width: 300px; background-color:bg-success-300; padding: 15px; border-radius: 10px; box-shadow: 0 0 8px rgba(0,0,0,0.08); text-align: left;"> -->
        <div class="author-status list-view bg-success-100 "  style="flex: 1; min-width: 300px;  padding: 15px; border-radius: 10px; box-shadow: 0 0 8px rgba(0,0,0,0.08); text-align: left;" >
        <h6 style="margin-bottom: 15px; font-size: 20px; text-align: center;">Author Status</h6>

        <p style="font-size: 16px; text-align: center;">
            Total Authors: <?= isset($author_details['total_authors']) ? $author_details['total_authors'] : 0; ?>
        </p>

        <div style="display: flex; justify-content: space-between; margin-top: 15px;">
            <!-- Active -->
            <div style="background-color: #A0E7A0; border-radius: 8px; padding: 10px; width: 30%;">
                <p style="color: #2D862D; font-size: 16px; text-align: center;">Active</p>
                <p style="font-size: 20px; font-weight: bold; text-align: center;">
                    <?= isset($author_details['sum_active_auth_cnt']) ? $author_details['sum_active_auth_cnt'] : 0; ?>
                </p>
            </div>

            <!-- Inactive -->
            <div style="background-color: #FFE17D; border-radius: 8px; padding: 10px; width: 30%;">
                <p style="color: #C26A00; font-size: 16px; text-align: center;">Inactive</p>
                <p style="font-size: 20px; font-weight: bold; text-align: center;">
                    <?= isset($author_details['sum_inactive_auth_cnt']) ? $author_details['sum_inactive_auth_cnt'] : 0; ?>
                </p>
            </div>

            <!-- Withdrawn (status = 0) -->
            <div style="background-color: #F7A8A8; border-radius: 8px; padding: 10px; width: 30%;">
                <p style="color: #B03030; font-size: 16px; text-align: center;">Withdrawn</p>
                <p style="font-size: 20px; font-weight: bold; text-align: center;">
                    <?= isset($author_details['sum_withdraw_auth_cnt']) ? $author_details['sum_withdraw_auth_cnt'] : 0; ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Book Shop Status -->
    <div class="author-status list-view bg-info-focus "  style="flex: 1; min-width: 300px;  padding: 15px; border-radius: 10px; box-shadow: 0 0 8px rgba(0,0,0,0.08); text-align: left;" >
       <h6 style="margin-bottom: 15px; font-size: 20px; text-align: center;">Book Shop Status</h6>
        <?php $bookshop_status = $bookshop_details['bookshop_status'] ?? []; ?>
        <p style="font-size: 16px; text-align: center;">Total Bookshops: <?= $bookshop_status['total_bookshop'] ?? 0 ?></p>
        <div style="display: flex; justify-content: space-between; margin-top: 15px;">
            <div style="background-color: #A0DFFB; border-radius: 8px; padding: 10px; width: 30%;">
                <p style="color: #0077B6; font-size: 16px; text-align: center;">Active</p>
                <p style="font-size: 20px; font-weight: bold; text-align: center;"><?= $bookshop_status['active_bookshop'] ?? 0 ?></p>
            </div>
            <div style="background-color: #FFCD94; border-radius: 8px; padding: 10px; width: 30%;">
                <p style="color: #C26A00; font-size: 16px; text-align: center;">Inactive</p>
                <p style="font-size: 20px; font-weight: bold; text-align: center;"><?= $bookshop_status['pending_bookshop'] ?? 0 ?></p>
            </div>
            <div style="background-color: #FF9AA2; border-radius: 8px; padding: 10px; width: 30%;">
                <p style="color: #B03030; font-size: 16px; text-align: center;">Withdrawn</p>
                <p style="font-size: 20px; font-weight: bold; text-align: center;"><?= $bookshop_status['inactive_bookshop'] ?? 0 ?></p>
            </div>
        </div>
    </div>

    <!-- POD Publisher Status -->
   <div class="author-status list-view bg-purple-light "  style="flex: 1; min-width: 300px;  padding: 15px; border-radius: 10px; box-shadow: 0 0 8px rgba(0,0,0,0.08); text-align: left;" >
        <h6 style="margin-bottom: 15px; font-size: 20px; text-align: center;">POD Publisher Status</h6>
        <p style="font-size: 16px; text-align: center;">Total POD Publishers: <?= $pod_order_count['publisher_status']['total_podpublisher'] ?? 0; ?></p>
        <div style="display: flex; justify-content: space-between; margin-top: 15px;">
            <!-- Active Publisher Status -->
            <div style="background-color: #D3A4FF; border-radius: 8px; padding: 10px; width: 30%;">
                <p style="color: #6B2DC1; font-size: 16px; text-align: center;">Active</p>
                <p style="font-size: 20px; font-weight: bold; text-align: center;"><?= $pod_order_count['publisher_status']['active_podpublisher'] ?? 0; ?></p>
            </div>
            
            <!-- Inactive Publisher Status -->
            <div style="background-color: #FFE38C; border-radius: 8px; padding: 10px; width: 30%;">
                <p style="color: #C26A00; font-size: 16px; text-align: center;">Inactive</p>
                <p style="font-size: 20px; font-weight: bold; text-align: center;"><?= $pod_order_count['publisher_status']['inactive_podpublisher'] ?? 0; ?></p>
            </div>
            
            <!-- Withdrawn Publisher Status (Pending) -->
            <div style="background-color: #FFA8B8; border-radius: 8px; padding: 10px; width: 30%;">
                <p style="color: #B03030; font-size: 16px; text-align: center;">Withdrawn</p>
                <p style="font-size: 20px; font-weight: bold; text-align: center;"><?= $pod_order_count['publisher_status']['pending_podpublisher'] ?? 0; ?></p>
            </div>
        </div>
    </div>
</div>

    <br>


        <!-- Subscription Counts -->
    <div class="subscription-widget">
            <h6 style="text-align:center; font-weight:400; margin-bottom:12px;"><b>Subscription Plan Counts</b></h6>
        <div class="table-responsive">
            <table class="subscription-table table table-borderless text-center">
                <thead>
                    <tr>
                        <th>Duration</th>
                        <th>Quarterly</th>
                        <th>Annual</th>
                        <th>Audiobooks</th>
                        <th>INR Amount</th>
                        <th>USD Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($subscription_count['subscriptions_summary'])): ?>
                        <?php foreach ($subscription_count['subscriptions_summary'] as $subscription): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        <?= esc($subscription['time_period'] ?? 'Unknown'); ?>
                                    </span>
                                </td>
                                <td><?= esc($subscription['quarterly'] ?? 0); ?></td>
                                <td><?= esc($subscription['annual'] ?? 0); ?></td>
                                <td><?= esc($subscription['audiobooks'] ?? 0); ?></td>
                                <td>
                                    <?= ($user_type == 4)
                                        ? '  ' . indian_format((float)($subscription['inr_amount'] ?? 0), 2)
                                        : '# ...'; ?>
                                </td>
                                <td>
                                    <?= ($user_type == 4)
                                        ? '$' . number_format((float)($subscription['usd_amount'] ?? 0), 2)
                                        : '# ...'; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No subscription data available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <br>
    <h6 style="text-align:center; font-weight:400; margin-bottom:12px;"><b>Subscription Counts</b></h6>
    <div class="row g-2">

    <?php foreach ($plans as $p): ?>
    <div class="col-6 col-md-4 col-lg-4">
        <a href="<?= base_url('adminv4/viewusers/'.$p['plan_id']) ?>" style="text-decoration:none;">
            <div class="p-3 shadow-sm rounded" style="border-left:4px solid #5A5AF0;">
                <h6 class="fw-bold"><?= $p['plan_name']; ?></h6>

                <div>Today: <strong><?= $p['today_count']; ?></strong></div>
                <div>This Month: <strong><?= $p['month_count']; ?></strong></div>
                <div>Previous Month: <strong><?= $p['prev_month_count']; ?></strong></div>
            </div>
        </a>
    </div>
<?php endforeach; ?>

</div><br>


    <div class="row d-flex">
        <!-- Pustaka Paperback Orders Section -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <div class="widget widget-table-two">
                <div class="widget-heading text-center">
                    <h6>Paperback Shipped Orders 
                        <!-- <a href="<?= base_url(); ?>author/author_royalty_list" class="bs-tooltip" title="Author Royalty" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="blue" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 13v6a2 2 0 0 1-2 2h6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2p"></path>
                                <polyline points="15 3 21 3 21 9"></polyline>
                                <line x1="10" y1="14" x2="21" y2="3"></line>
                            </svg>
                        </a> -->
                    </h6>
                </div>

                <!-- Order Summary Row -->
                <div class="row text-center mt-3 g-3">
                    <!-- Today -->
                  <div class="col-md-3 col-sm-6">
                        <div class="order-card p-3 rounded shadow-sm border bg-gradient-purple" style="min-height: 100%;">

                            <!-- Header -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-primary px-3 py-2">Today</span>
                            </div>

                            <!-- Order Summary -->
                            <div class="text-uppercase small text-secondary fw-bold mb-2">Order Summary</div>
                            <div class="ms-1 mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Online</span>
                                     <span class="fw-bold"><?= $order_count['online']['date_quantity'] ?? 0 ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Offline</span>
                                     <span class="fw-bold"><?= $order_count['offline']['date_quantity'] ?? 0 ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>BookShop</span>
                                     <span class="fw-bold"><?= $order_count['bookshop']['date_quantity'] ?? 0 ?></span>
                                </div>
                            </div>

                            <!-- Total Amount -->
                            <div class="py-2 px-2 rounded mb-3">
                                <div class="text-secondary small fw-semibold">Total Amount</div>
                                <div class="fw-bold fs-4 text-success">
                                    <?= indian_format(
                                        ($order_count['online']['date_total'] ?? 0) +
                                        ($order_count['offline']['date_total'] ?? 0) +
                                        ($order_count['bookshop']['date_total'] ?? 0) 
                                    , 2) ?>
                                </div>
                            </div>

                            <hr class="my-3">

                            <!-- Platforms -->
                            <div class="text-uppercase small text-secondary fw-bold mb-2">Other Channels</div>
                            <div class="ms-1 mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Amazon</span>
                                    <span class="fw-bold"><?= $order_count['amazon']['date_quantity'] ?? 0 ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Flipkart</span>
                                    <span class="fw-bold"><?= $order_count['flipkart']['date_quantity'] ?? 0 ?></span>
                                </div>
                            </div>

                            <hr class="my-3">

                            <!-- Stock Added -->
                            <div class="text-uppercase small text-secondary fw-bold mb-2">Stock Added</div>

                            <div class="fw-bold text-primary ms-1" style="font-size: 16px;">
                                <?= $order_count['stock']['today_count'] ?? 0 ?>  titles /
                                <?= $order_count['stock']['today_stock_in'] ?? 0 ?> cps
                            </div>

                            <!-- Royalty -->
                            <div class="p-2 rounded text-center fw-semibold mt-4">
                                Royalty:
                                <?= ($user_type == 4)
                                    ? '  ' . indian_format((float)($order_count['author']['date_royalty'] ?? 0), 2)
                                    : '#' ?>
                            </div>

                        </div>
                    </div>



                    <!-- Weekly -->
                    <div class="col-md-3 col-sm-6">
                        <div class="order-card p-3 rounded shadow-sm border bg-gradient-purple" style="min-height: 100%;">

                            <!-- Header -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge  bg-success px-3 py-2">Weekly</span>
                            </div>

                            <!-- Order Summary -->
                            <div class="text-uppercase small text-secondary fw-bold mb-2">Order Summary</div>
                            <div class="ms-1 mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Online</span>
                                     <span class="fw-bold"><?= $order_count['online']['week_quantity'] ?? 0 ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Offline</span>
                                     <span class="fw-bold"><?= $order_count['offline']['week_quantity'] ?? 0 ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>BookShop</span>
                                     <span class="fw-bold"><?= $order_count['bookshop']['week_quantity'] ?? 0 ?></span>
                                </div>
                            </div>

                            <!-- Total Amount -->
                            <div class="py-2 px-2 rounded mb-3">
                                <div class="text-secondary small fw-semibold">Total Amount</div>
                                <div class="fw-bold fs-4 text-success">
                                    <?= indian_format(
                                        ($order_count['online']['week_total'] ?? 0) +
                                        ($order_count['offline']['week_total'] ?? 0) +
                                        ($order_count['bookshop']['week_total'] ?? 0) 
                                    , 2) ?>
                                </div>
                            </div>

                            <hr class="my-3">

                            <!-- Platforms -->
                            <div class="text-uppercase small text-secondary fw-bold mb-2">Other Channels</div>
                            <div class="ms-1 mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Amazon</span>
                                    <span class="fw-bold"><?= $order_count['amazon']['week_quantity'] ?? 0 ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Flipkart</span>
                                    <span class="fw-bold"><?= $order_count['flipkart']['week_quantity'] ?? 0 ?></span>
                                </div>
                            </div>

                            <hr class="my-3">

                            <!-- Stock Added -->
                            <div class="text-uppercase small text-secondary fw-bold mb-2">Stock Added</div>

                            <div class="fw-bold text-primary ms-1" style="font-size: 16px;">
                                <?= $order_count['stock']['week_count'] ?? 0 ?>  titles /
                                <?= $order_count['stock']['week_stock_in'] ?? 0 ?> cps
                            </div>

                            <!-- Royalty -->
                            <div class="p-2 rounded text-center fw-semibold mt-4">
                                Royalty:
                                <?= ($user_type == 4)
                                    ? '  ' . indian_format((float)($order_count['author']['week_royalty'] ?? 0), 2)
                                    : '#' ?>
                            </div>

                        </div>

                    </div>

                    <!-- Current Month -->
                    <div class="col-md-3 col-sm-6">
                        <div class="order-card p-3 rounded shadow-sm border bg-gradient-purple" style="min-height: 100%;">

                            <!-- Header -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-danger px-3 py-2">Current Month</span>
                            </div>

                            <!-- Order Summary -->
                            <div class="text-uppercase small text-secondary fw-bold mb-2">Order Summary</div>
                            <div class="ms-1 mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Online</span>
                                     <span class="fw-bold"><?= $order_count['online']['month_quantity'] ?? 0 ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Offline</span>
                                     <span class="fw-bold"><?= $order_count['offline']['month_quantity'] ?? 0 ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>BookShop</span>
                                     <span class="fw-bold"><?= $order_count['bookshop']['month_quantity'] ?? 0 ?></span>
                                </div>
                            </div>

                            <!-- Total Amount -->
                            <div class="py-2 px-2 rounded mb-3">
                                <div class="text-secondary small fw-semibold">Total Amount</div>
                                <div class="fw-bold fs-4 text-success">
                                    <?= indian_format(
                                        ($order_count['online']['month_total'] ?? 0) +
                                        ($order_count['offline']['month_total'] ?? 0) +
                                        ($order_count['bookshop']['month_total'] ?? 0) 
                                    , 2) ?>
                                </div>
                            </div>

                            <hr class="my-3">

                            <!-- Platforms -->
                            <div class="text-uppercase small text-secondary fw-bold mb-2">Other Channels</div>
                            <div class="ms-1 mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Amazon</span>
                                    <span class="fw-bold"><?= $order_count['amazon']['month_quantity'] ?? 0 ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Flipkart</span>
                                    <span class="fw-bold"><?= $order_count['flipkart']['month_quantity'] ?? 0 ?></span>
                                </div>
                            </div>

                            <hr class="my-3">

                            <!-- Stock Added -->
                            <div class="text-uppercase small text-secondary fw-bold mb-2">Stock Added</div>

                            <div class="fw-bold text-primary ms-1" style="font-size: 16px;">
                                <?= $order_count['stock']['month_count'] ?? 0 ?>  titles /
                                <?= $order_count['stock']['month_stock_in'] ?? 0 ?> cps
                            </div>

                            <!-- Royalty -->
                            <div class="p-2 rounded text-center fw-semibold mt-4">
                                Royalty:
                                <?= ($user_type == 4)
                                    ? '  ' . indian_format((float)($order_count['author']['month_royalty'] ?? 0), 2)
                                    : '#' ?>
                            </div>

                        </div>
                    </div>

                    <!-- Previous Month -->
                    <div class="col-md-3 col-sm-6">
                        <div class="order-card p-3 rounded shadow-sm border bg-gradient-purple" style="min-height: 100%;">

                            <!-- Header -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-secondary px-3 py-2">Prev Month</span>
                            </div>

                            <!-- Order Summary -->
                            <div class="text-uppercase small text-secondary fw-bold mb-2">Order Summary</div>
                            <div class="ms-1 mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Online</span>
                                     <span class="fw-bold"><?= $order_count['online']['prev_month'] ?? 0 ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Offline</span>
                                     <span class="fw-bold"> <?= $order_count['offline']['prev_month'] ?? 0 ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>BookShop</span>
                                     <span class="fw-bold"><?= $order_count['bookshop']['prev_month'] ?? 0 ?></span>
                                </div>
                            </div>

                            <!-- Total Amount -->
                            <div class="py-2 px-2 rounded mb-3">
                                <div class="text-secondary small fw-semibold">Total Amount</div>
                                <div class="fw-bold fs-4 text-success">
                                    <?= indian_format(
                                        ($order_count['online']['prev_month_total'] ?? 0) +
                                        ($order_count['offline']['prev_month_total'] ?? 0) +
                                        ($order_count['bookshop']['prev_month_total'] ?? 0) 
                                    , 2) ?>
                                </div>
                            </div>

                            <hr class="my-3">

                            <!-- Platforms -->
                            <div class="text-uppercase small text-secondary fw-bold mb-2">Other Channels</div>
                            <div class="ms-1 mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Amazon</span>
                                    <span class="fw-bold"><?= $order_count['amazon']['prev_month'] ?? 0 ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Flipkart</span>
                                    <span class="fw-bold"><?= $order_count['flipkart']['prev_month'] ?? 0 ?></span>
                                </div>
                            </div>

                            <hr class="my-3">

                            <!-- Stock Added -->
                            <div class="text-uppercase small text-secondary fw-bold mb-2">Stock Added</div>

                            <div class="fw-bold text-primary ms-1" style="font-size: 16px;">
                                <?= $order_count['stock']['previous_month_count'] ?? 0 ?>  titles /
                                <?= $order_count['stock']['previous_month_stock_in'] ?? 0 ?> cps
                            </div>

                            <!-- Royalty -->
                            <div class="p-2 rounded text-center fw-semibold mt-4">
                                Royalty:
                                <?= ($user_type == 4)
                                    ? '  ' . indian_format((float)($order_count['author']['prev_month_royalty'] ?? 0), 2)
                                    : '#' ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- POD Paperback Orders Summary Section -->
            <div class="row mt-5">
                <div class="widget-heading text-center">
                    <h6>POD & Author  Orders</h6><br>
                </div>

                <!-- POD Today -->
                <div class="col-md-3 col-sm-6">
                    <div class="order-card border p-3 rounded text-center bg-gradient-primary shadow-sm">

                        <!-- Today Badge -->
                        <span class="badge bg-primary mb-3 px-3 py-2">Today</span>

                        <!-- Publisher -->
                        <div class="fw-bold mb-2">Publisher</div>

                        <!-- Header -->
                        <div class="row text-center fw-bold small mb-2">
                            <div class=" col-3">Orders</div>
                            <div class=" col-3">Copies</div>
                            <div class=" col-4">Amount</div>
                        </div>

                        <!-- Values -->
                        <div class="row text-center fw-semibold fs-6 mb-2">
                            <div class=" col-3"><?= $pod_order_count['pod_order']['date_orders'] ?? 0 ?></div>
                            <div class=" col-3"><?= $pod_order_count['pod_order']['date_quantity'] ?? 0 ?></div>
                            <div class=" col-5"><?= indian_format($pod_order_count['pod_order']['date_price'] ?? 0, 2) ?></div>
                        </div>

                        <hr>

                        <!-- Author -->
                        <div class="fw-bold mb-2">Author</div>

                        <div class="row text-center fw-bold small mb-2">
                            <div class=" col-2">Orders</div>
                            <div class=" col-3">Titles</div>
                            <div class=" col-2">Copies</div>
                            <div class="col-4">Amount</div>
                        </div>

                        <div class="row text-center fw-semibold fs-6">
                            <div class=" col-2"><?= $pod_order_count['author_order']['today_orders'] ?? 0 ?></div>
                            <div class=" col-3"><?= $pod_order_count['author_order']['today_titles'] ?? 0 ?></div>
                            <div class=" col-2"><?= $pod_order_count['author_order']['date_quantity'] ?? 0 ?></div>
                            <div class="col-4"><?= indian_format($pod_order_count['author_order']['date_price'] ?? 0, 2) ?></div>
                        </div>

                    </div>
                </div>


                <!-- POD Weekly -->
                <div class="col-md-3 col-sm-6">
                    <div class="order-card border p-3 rounded text-center bg-gradient-primary shadow-sm">

                        <!-- Today Badge -->
                         <span class="badge bg-success mb-3 px-3 py-2">Weekly</span>

                        <!-- Publisher -->
                        <div class="fw-bold mb-2">Publisher</div>

                        <!-- Header -->
                        <div class="row text-center fw-bold small mb-2">
                            <div class=" col-3">Orders</div>
                            <div class=" col-3">Copies</div>
                            <div class=" col-4">Amount</div>
                        </div>

                        <!-- Values -->
                        <div class="row text-center fw-semibold fs-6 mb-2">
                            <div class=" col-3"><?= $pod_order_count['pod_order']['week_orders'] ?? 0 ?></div>
                            <div class=" col-3"><?= $pod_order_count['pod_order']['week_quantity'] ?? 0 ?></div>
                            <div class=" col-5"><?= indian_format($pod_order_count['pod_order']['week_price'] ?? 0, 2) ?></div>
                        </div>

                        <hr>

                        <!-- Author -->
                        <div class="fw-bold mb-2">Author</div>

                        <div class="row text-center fw-bold small mb-2">
                            <div class=" col-2">Orders</div>
                            <div class=" col-3">Titles</div>
                            <div class=" col-2">Copies</div>
                            <div class="col-4">Amount</div>
                        </div>

                        <div class="row text-center fw-semibold fs-6">
                            <div class=" col-2"><?= $pod_order_count['author_order']['week_orders'] ?? 0 ?></div>
                            <div class=" col-3"><?= $pod_order_count['author_order']['week_titles'] ?? 0 ?></div>
                            <div class=" col-2"><?= $pod_order_count['author_order']['week_quantity'] ?? 0 ?></div>
                            <div class="col-5"><?= indian_format($pod_order_count['author_order']['week_price'] ?? 0, 2) ?></div>
                        </div>

                    </div>
                </div>

                <!-- POD Current Month -->
                <div class="col-md-3 col-sm-6">
                    <div class="order-card border p-3 rounded text-center bg-gradient-primary shadow-sm">

                        <!-- Today Badge -->
                        <span class="badge bg-danger mb-3 px-3 py-2">Current Month</span>

                        <!-- Publisher -->
                        <div class="fw-bold mb-2">Publisher</div>

                        <!-- Header -->
                        <div class="row text-center fw-bold small mb-2">
                            <div class=" col-3">Orders</div>
                            <div class=" col-3">Copies</div>
                            <div class=" col-4">Amount</div>
                        </div>

                        <!-- Values -->
                        <div class="row text-center fw-semibold fs-6 mb-2">
                            <div class=" col-3"><?= $pod_order_count['pod_order']['month_orders'] ?? 0 ?></div>
                            <div class=" col-3"><?= $pod_order_count['pod_order']['month_quantity'] ?? 0 ?></div>
                            <div class=" col-5"><?= indian_format($pod_order_count['pod_order']['month_price'] ?? 0, 2) ?></div>
                        </div>

                        <hr>

                        <!-- Author -->
                        <div class="fw-bold mb-2">Author</div>

                        <div class="row text-center fw-bold small mb-2">
                            <div class=" col-2">Orders</div>
                            <div class=" col-3">Titles</div>
                            <div class=" col-2">Copies</div>
                            <div class="col-4">Amount</div>
                        </div>

                        <div class="row text-center fw-semibold fs-6">
                            <div class=" col-2"><?= $pod_order_count['author_order']['month_orders'] ?? 0 ?></div>
                            <div class=" col-3"><?= $pod_order_count['author_order']['month_titles'] ?? 0 ?></div>
                            <div class=" col-2"><?= $pod_order_count['author_order']['month_quantity'] ?? 0 ?></div>
                            <div class="col-4"><?= indian_format($pod_order_count['author_order']['month_price'] ?? 0, 2) ?></div>
                        </div>

                    </div>
                </div>


                <!-- POD Previous Month -->
                <div class="col-md-3 col-sm-6">
                    <div class="order-card border p-3 rounded text-center bg-gradient-primary shadow-sm">

                        <!-- Today Badge -->
                        <span class="badge bg-secondary mb-3 px-3 py-2">Prev Month</span>

                        <!-- Publisher -->
                        <div class="fw-bold mb-2">Publisher</div>

                        <!-- Header -->
                        <div class="row text-center fw-bold small mb-2">
                            <div class=" col-3">Orders</div>
                            <div class=" col-3">Copies</div>
                            <div class=" col-4">Amount</div>
                        </div>

                        <!-- Values -->
                        <div class="row text-center fw-semibold fs-6 mb-2">
                            <div class=" col-3"><?= $pod_order_count['pod_order']['prev_month_orders'] ?? 0 ?></div>
                            <div class=" col-3"><?= $pod_order_count['pod_order']['prev_month'] ?? 0 ?></div>
                            <div class=" col-5"><?= indian_format($pod_order_count['pod_order']['prev_month_price'] ?? 0, 2) ?></div>
                        </div>

                        <hr>

                        <!-- Author -->
                        <div class="fw-bold mb-2">Author</div>

                        <div class="row text-center fw-bold small mb-2">
                            <div class=" col-2">Orders</div>
                            <div class=" col-3">Titles</div>
                            <div class=" col-2">Copies</div>
                            <div class="col-4">Amount</div>
                        </div>

                        <div class="row text-center fw-semibold fs-6">
                            <div class=" col-2"><?= $pod_order_count['author_order']['prev_month_orders'] ?? 0 ?></div>
                            <div class=" col-3"><?= $pod_order_count['author_order']['prev_month_titles'] ?? 0 ?></div>
                            <div class=" col-2"><?= $pod_order_count['author_order']['prev_month'] ?? 0 ?></div>
                            <div class="col-4"><?= indian_format($pod_order_count['author_order']['prev_month_price'] ?? 0, 2) ?></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    </br>

    <div class="mt-4">
    <h6 class="text-center fw-semibold mb-4">
        Prospect Plans Summary
    </h6><br>

    <div class="row justify-content-center g-3">
        <?php foreach ($plan_summary as $p): ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <!-- Color accent header -->
                    <div class="card-header border-0 py-2" style="background-color: rgba(90, 90, 240, 0.08); border-left: 4px solid #5A5AF0;">
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Plan name with proper truncation -->
                            <h6 class="m-0 fw-semibold text-truncate" style="max-width: 70%;">
                                <?= htmlspecialchars($p['plan_name']); ?>
                            </h6>
                            
                            <!-- Title count badge -->
                            <span class="badge rounded-pill bg-primary">
                                <?= $p['total_titles']; ?> Titles
                            </span>
                        </div>
                    </div>

                    <!-- Card body -->
                    <div class="card-body p-3">
                        <!-- Monthly comparison section -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small">This Month</span>
                                <span class="fw-semibold text-success">
                                    <?= $p['month_count']; ?>
                                </span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small">Previous Month</span>
                                <span class="fw-semibold text-secondary">
                                    <?= $p['prev_month_count']; ?>
                                </span>
                            </div>
                        </div>

                        <!-- Divider -->
                        <hr class="my-2">

                        <!-- Payment section -->
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small">Total Cost</span>
                                <strong>
                                    <?= indian_format($p['plan_cost'], 2); ?>
                                </strong>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small">Amount Paid</span>
                                <strong class="text-success">
                                    <?= indian_format($p['total_paid'], 2); ?>
                                </strong>
                            </div>
                        </div>
                        
                        <!-- Optional: Progress bar for payment -->
                        <?php if ($p['plan_cost'] > 0): ?>
                            <?php $payment_percentage = min(100, ($p['total_paid'] / $p['plan_cost']) * 100); ?>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>Payment Progress</small>
                                    <small class="fw-semibold"><?= round($payment_percentage, 0); ?>%</small>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: <?= $payment_percentage; ?>%;" 
                                         aria-valuenow="<?= $payment_percentage; ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
</div><br>
    <!-- Wallet Info Card -->
    <div class="row justify-content-center">
    <div class="col-xl-6 col-lg-6 col-md-8 col-sm-10 col-12">
        <div class="card shadow-lg mb-3 rounded-3">
            <div class="card-body p-4">
                <p class="text-center fw-bold mb-3">Wallet Info</p>

                <ul class="nav nav-tabs justify-content-center" id="walletTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="inr-tab" data-bs-toggle="tab" data-bs-target="#inr" type="button" role="tab">INR</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="usd-tab" data-bs-toggle="tab" data-bs-target="#usd" type="button" role="tab">USD</button>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="walletTabContent">
                    <div class="tab-pane fade show active text-center" id="inr" role="tabpanel">
                        <p class="mb-1"><i class="fa fa-inr"></i> Balance in INR</p>
                        <p class="fw-bold">
                            <?= ($user_type == 4) ? '₹ ' . ($subscription_count['wallet_total']['balance_inr'] ?? '#') : '#' ?>
                        </p>
                    </div>
                    <div class="tab-pane fade text-center" id="usd" role="tabpanel">
                        <p class="mb-1"><i class="fa fa-dollar-sign"></i> Balance in USD</p>
                        <p class="fw-bold">
                            <?= ($user_type == 4) ? '$' . ($subscription_count['wallet_total']['balance_usd'] ?? '#') : '#' ?>
                        </p>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-3 mt-4">
                  <a href="<?= base_url('adminv4/walletdetails') ?>" class="btn btn-sm btn-dark px-4">Summary</a>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?= $this->endSection(); ?>
<?= $this->section('script'); ?>
<script>
function toggleSection(id, btn) {
    const section = document.getElementById(id);
    const isVisible = section.style.display === 'block';
    section.style.display = isVisible ? 'none' : 'block';
    btn.innerHTML = isVisible ? 'View Channels ▼' : 'Hide Channels ▲';
}
</script>
<?= $this->endSection(); ?>