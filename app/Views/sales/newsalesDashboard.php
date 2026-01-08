<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<?php
// Indian currency formatting helper function
function formatIndianCurrency($number) {
    $decimal = '';
    $number = (string) $number;

    if (strpos($number, '.') !== false) {
        list($number, $decimal) = explode('.', number_format((float)$number, 2, '.', ''));
    }

    $lastThree = substr($number, -3);
    $restUnits = substr($number, 0, -3);

    if ($restUnits != '') {
        $restUnits = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits);
        $formatted = $restUnits . "," . $lastThree;
    } else {
        $formatted = $lastThree;
    }

    if ($decimal !== '') {
        $formatted .= "." . $decimal;
    }

    return $formatted;
}
?>

<div class="row gy-4">
    <div class="col-12">
        <!-- Overall Sales Card -->
        <div class="trail-bg h-10 text-center d-flex flex-column justify-content-between align-items-center p-16 radius-8 "
            style="background: linear-gradient(135deg, #a4dceaff, #ecbce8ff); color: #dfbef5ff;">
            <div class="card-body">
                <h6 class="mb-3">Overall Sales</h6>
                <a href="salesreports" class="btn btn-primary rounded-pill" target="_blank">
                    View Reports
                </a>
            </div>
        </div>

        <?php 
            $total_row = null;
            foreach ($total['total'] as $row) {
                if ($row['fy'] === 'Total') {
                    $total_row = $row;
                    break;
                }
            }
        ?>
        <!-- Revenue Summary Cards -->
        <div class="row g-4">
            <div class="row g-4">
                <!-- Ebook Revenue -->
                <div class="col-md-3">
                    <div class="radius-8 h-100 text-center p-20 bg-purple-light shadow-sm">
                        <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-xl mb-12 bg-lilac-200 border border-lilac-400 text-lilac-600">
                            <i class="ri-book-2-fill"></i>
                        </span>
                        <span class="text-neutral-700 d-block">Ebook Revenue</span>
                        <h6 class="mb-0 mt-4">₹<?= formatIndianCurrency($total_row['ebook_revenue'] ?? 0); ?></h6>
                        <a href="ebooksales" class="btn py-8 rounded-pill w-100 bg-gradient-blue-warning text-sm mt-3" target="_blank">
                            View Details
                        </a>
                    </div>
                </div>

                <!-- Audiobook Revenue -->
                <div class="col-md-3">
                    <div class="radius-8 h-100 text-center p-20 bg-success-100 shadow-sm">
                        <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-xl mb-12 bg-success-200 border border-success-400 text-success-600">
                            <i class="ri-headphone-fill"></i>
                        </span>
                        <span class="text-neutral-700 d-block">Audiobook Revenue</span>
                        <h6 class="mb-0 mt-4">₹<?= formatIndianCurrency($total_row['audiobook_revenue'] ?? 0); ?></h6>
                        <a href="audiobooksales" class="btn py-8 rounded-pill w-100 bg-gradient-blue-warning text-sm mt-3" target="_blank">
                            View Details
                        </a>
                    </div>
                </div>

                <!-- Paperback Revenue -->
                <div class="col-md-3">
                    <div class="radius-8 h-100 text-center p-20 bg-danger-100">
                        <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-xl mb-12 bg-warning-200 border border-warning-400 text-warning-700">
                            <i class="ri-file-paper-2-fill"></i>
                        </span>
                        <span class="text-neutral-700 d-block">Paperback Revenue</span>
                        <h6 class="mb-0 mt-4">₹<?= formatIndianCurrency($total_row['paperback_revenue'] ?? 0); ?></h6>
                        <a href="paperbacksales" class="btn py-8 rounded-pill w-100 bg-gradient-blue-warning text-sm mt-3" target="_blank">
                            View Details
                        </a>
                    </div>
                </div>

                <!-- POD -->
                <div class="col-md-3">
                    <div class="radius-8 h-100 text-center p-20 bg-info-focus">
                        <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-xl mb-12 bg-success-200 border border-success-400 text-success-600">
                            <i class="ri-printer-fill"></i>
                        </span>
                        <span class="text-neutral-700 d-block">POD</span>
                        <h6 class="mb-0 mt-4">₹<?= formatIndianCurrency($pod['pod_overall']['pod_total'] ?? 0); ?></h6>
                        <a href="/pod/pod_publisher_dashboard" class="btn py-8 rounded-pill w-100 bg-gradient-blue-warning text-sm mt-3" target="_blank">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            <!-- Ebook Revenue Summary Table -->
           <?php
                $channelwise = $channelwise ?? $data['channelwise'] ?? [];

                $ebook_channels = [
                    'pustaka',
                    'amazon',
                    'overdrive',
                    'scribd',
                    'storytel',
                    'google',
                    'pratilipi',
                    'kobo'
                ];

                $ebook_totals = [];

                foreach ($ebook_channels as $channel) {
                    $yearwise = $channelwise[$channel . '_yearwise'] ?? [];
                    $ebook_totals[$channel] = array_sum(array_column($yearwise, 'revenue') ?: [0]);
                }

                $grand_total_ebook = array_sum($ebook_totals);

                $ebook_routes = [
                    'pustaka'   => 'dashboard/pustakaebook',
                    'amazon'    => 'sales/ebookamazondetails',
                    'overdrive' => 'sales/ebookoverdrivedetails',
                    'scribd'    => 'sales/ebookscribddetails',
                    'storytel'  => 'sales/ebookstoryteldetails',
                    'google'    => 'sales/ebookgoogledetails',
                    'pratilipi' => 'dashboard/pratilipi',
                    'kobo'      => 'dashboard/kobo',
                ];
                ?>

                <div class="mt-5">
                    <h6 class="text-center mb-3">E-Book Revenue Summary</h6>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center">
                            <thead class="table-primary">
                                <tr>
                                    <?php foreach ($ebook_channels as $channel): ?>
                                        <th><?= ucfirst($channel) ?></th>
                                    <?php endforeach; ?>
                                    <th>Grand Total</th>
                                </tr>
                            </thead>

                            <tbody>
                                <!-- Totals Row -->
                                <tr>
                                    <?php foreach ($ebook_channels as $channel): ?>
                                        <td><?= formatIndianCurrency($ebook_totals[$channel]) ?></td>
                                    <?php endforeach; ?>
                                    <td><strong><?= formatIndianCurrency($grand_total_ebook) ?></strong></td>
                                </tr>

                                <!-- Individual Dashboard Buttons Row -->
                                <tr>
                                    <?php foreach ($ebook_channels as $channel): ?>
                                        <td>
                                            <a href="<?= base_url($ebook_routes[$channel]) ?>"
                                            class="btn btn-sm btn-primary">
                                                Dashboard
                                            </a>
                                        </td>
                                    <?php endforeach; ?>
                                    <td>
                                        <a href="<?= base_url('dashboard/ebook') ?>"
                                        class="btn btn-sm btn-success">
                                            View All
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>


            <!-- Audiobook Revenue Summary Table -->
           <?php
            $audiobook_channels = [
                'pustaka_aud',
                'audible',
                'overdrive_aud',
                'google_aud',
                'storytel_aud',
                'youtube_aud',
                'kukufm_aud'
            ];

            /* 🔗 Audiobook Routes */
            $audiobook_routes = [
                'pustaka_aud'   => 'dashboard/pustakaaudiobook',
                'audible'       => 'sales/audibleaudiobookdetails',
                'overdrive_aud' => 'sales/audiobookoverdrivedetails',
                'google_aud'    => 'sales/audiobookgoogledetails',
                'storytel_aud'  => 'sales/audiobookstoryteldetails',
                'youtube_aud'   => 'sales/youtubedetails',
                'kukufm_aud'    => 'sales/kukufmdetails',
            ];

            $audiobook_totals = [];

            foreach ($audiobook_channels as $channel) {
                $sum = 0;
                $yearwise = $channelwise[$channel . '_yearwise'] ?? [];

                foreach ($yearwise as $row) {
                    $sum += $row['revenue'] ?? 0;
                }
                $audiobook_totals[$channel] = $sum;
            }

            $grand_total_audiobook = array_sum($audiobook_totals);
            ?>

            <div class="mt-5">
                <h6 class="text-center mb-3">Audiobook Revenue Summary</h6>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-primary">
                            <tr>
                                <?php foreach ($audiobook_channels as $channel): ?>
                                    <th><?= ucfirst(str_replace('_aud', '', $channel)) ?></th>
                                <?php endforeach; ?>
                                <th>Grand Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            <!-- Revenue Row -->
                            <tr>
                                <?php foreach ($audiobook_channels as $channel): ?>
                                    <td><?= formatIndianCurrency($audiobook_totals[$channel]) ?></td>
                                <?php endforeach; ?>
                                <td><strong><?= formatIndianCurrency($grand_total_audiobook) ?></strong></td>
                            </tr>

                            <!-- Dashboard Buttons Row -->
                            <tr>
                                <?php foreach ($audiobook_channels as $channel): ?>
                                    <td>
                                        <a href="<?= base_url($audiobook_routes[$channel]) ?>"
                                        class="btn btn-sm btn-primary">
                                            Dashboard
                                        </a>
                                    </td>
                                <?php endforeach; ?>
                                <td>
                                    <a href="<?= base_url('dashboard/audiobook') ?>"
                                    class="btn btn-sm btn-success">
                                        View All
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Paperback Revenue Summary Table -->
            <?php
                $flipkart = $pustaka['flipkart']['flipkart_order'] ?? 0.0;
                $amazon = $pustaka['amazon']['amazon_order'] ?? 0.0;
                $bookshop = $pustaka['bookshop']['bookshop_order'] ?? 0.0;
                $pustakaOrder = $pustaka['pustaka']['pustaka_order'] ?? 0.0;
                $offline = $pustaka['online']['offline_order'] ?? 0.0;
                $bookfair = $pustaka['bookfair']['book_fair'] ?? 0.0;

                $total = $flipkart + $amazon + $bookshop + $pustakaOrder + $offline + $bookfair;
            ?>

        <div class="mt-5">
            <h6 class="text-center mb-3">Paperback Revenue Summary</h6>
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>Flipkart</th>
                            <th>Amazon</th>
                            <th>Bookshop</th>
                            <th>Pustaka</th>
                            <th>Offline</th>
                            <th>Book Fair</th>
                            <th>Grand Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Totals Row -->
                        <tr>
                            <td><?= formatIndianCurrency($flipkart) ?></td>
                            <td><?= formatIndianCurrency($amazon) ?></td>
                            <td><?= formatIndianCurrency($bookshop) ?></td>
                            <td><?= formatIndianCurrency($pustakaOrder) ?></td>
                            <td><?= formatIndianCurrency($offline) ?></td>
                            <td><?= formatIndianCurrency($bookfair) ?></td>
                            <td><strong><?= formatIndianCurrency($total) ?></strong></td>
                        </tr>

                        <!-- Buttons Row -->
                        <tr>
                            <td>
                                <a href="<?= base_url('dashboard/flipkart') ?>" class="btn btn-sm btn-primary" target="_blank">Dashboard</a>
                            </td>
                            <td>
                                <a href="<?= base_url('dashboard/amazonpaperback') ?>" class="btn btn-sm btn-primary" target="_blank">Dashboard</a>
                            </td>
                            <td>
                                <a href="<?= base_url('dashboard/bookshopsales') ?>" class="btn btn-sm btn-primary" target="_blank">Dashboard</a>
                            </td>
                            <td>
                                <a href="<?= base_url('dashboard/pustaka') ?>" class="btn btn-sm btn-primary" target="_blank">Dashboard</a>
                            </td>
                            <td>
                                <a href="<?= base_url('dashboard/offlinesales') ?>" class="btn btn-sm btn-primary" target="_blank">Dashboard</a>
                            </td>
                            <td>
                                <a href="<?= base_url('dashboard/bookfair') ?>" class="btn btn-sm btn-primary" target="_blank">Dashboard</a>
                            </td>
                            <td>
                                <a href="<?= base_url('dashboard') ?>" class="btn btn-sm btn-success" target="_blank">View All</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-5">
            <h6 class="text-center mb-3">POD Revenue Summary</h6>
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>Publisher Order</th>
                            <th>Author Order</th>
                            <th>Total POD Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>₹<?= formatIndianCurrency($pod['publisher_order']['invoice_value'] ?? 0); ?></td>
                            <td>₹<?= formatIndianCurrency($pod['author_order']['author_order'] ?? 0); ?></td>
                            <td>₹<?= formatIndianCurrency($pod['pod_overall']['pod_total'] ?? 0); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?= $this->endSection(); ?>
