<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>
<div class="d-flex justify-content-end align-items-center my-3">
  <a href="<?= base_url('user/userdashboard'); ?>" 
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
</div>

<div class="container-fluid py-4">
    <!-- HORIZONTAL TABS NAVIGATION - TOP -->
     <div class="row mb-2">
    <div class="col-12">
        <h6 class="fw-bold mb-1"><?= esc($display['user_name']) ?></h6>
       <span id="userEmail" style="cursor: pointer; user-select: all;" onclick="copyToClipboard('<?= esc($display['user_email']) ?>')">
    <?= esc($display['user_email']) ?>
</span>
    </div>
</div>
<br>
    <div class="row mb-4">
        <div class="col-12">
            
            <div class="card p-0 overflow-hidden position-relative radius-12 h-100">
                <div class="card-body p-0">
                    <ul class="nav focus-tab nav-pills mb-0" id="v-pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold text-primary-light radius-4 px-16 py-10 active" id="v-pills-details-tab" data-bs-toggle="pill" data-bs-target="#v-pills-details" type="button" role="tab" aria-controls="v-pills-details" aria-selected="true">Details</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold text-primary-light radius-4 px-16 py-10" id="v-pills-subscriptions-tab" data-bs-toggle="pill" data-bs-target="#v-pills-subscriptions" type="button" role="tab" aria-controls="v-pills-subscriptions" aria-selected="false">Subscriptions</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold text-primary-light radius-4 px-16 py-10" id="v-pills-purchased-books-tab" data-bs-toggle="pill" data-bs-target="#v-pills-purchased-books" type="button" role="tab" aria-controls="v-pills-purchased-books" aria-selected="false">Purchased Books</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold text-primary-light radius-4 px-16 py-10" id="v-pills-purchased-paperback-tab" data-bs-toggle="pill" data-bs-target="#v-pills-purchased-paperback" type="button" role="tab" aria-controls="v-pills-purchased-paperback" aria-selected="false">Paperback</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold text-primary-light radius-4 px-16 py-10" id="v-pills-free-books-tab" data-bs-toggle="pill" data-bs-target="#v-pills-free-books" type="button" role="tab" aria-controls="v-pills-free-books" aria-selected="false">Free Books</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold text-primary-light radius-4 px-16 py-10" id="v-pills-gift-books-tab" data-bs-toggle="pill" data-bs-target="#v-pills-gift-books" type="button" role="tab" aria-controls="v-pills-gift-books" aria-selected="false">Author Gift Books</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold text-primary-light radius-4 px-16 py-10" id="v-pills-devices-tab" data-bs-toggle="pill" data-bs-target="#v-pills-devices" type="button" role="tab" aria-controls="v-pills-devices" aria-selected="false">Devices</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold text-primary-light radius-4 px-16 py-10" id="v-pills-add-plan-tab" data-bs-toggle="pill" data-bs-target="#v-pills-add-plan" type="button" role="tab" aria-controls="v-pills-add-plan" aria-selected="false">Add Plan</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold text-primary-light radius-4 px-16 py-10" id="v-pills-wallet-tab" data-bs-toggle="pill" data-bs-target="#v-pills-wallet" type="button" role="tab" aria-controls="v-pills-wallet" aria-selected="false">Wallet</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB CONTENT AREA - BELOW TABS -->
    <div class="row">
        <div class="col-12">
            <div class="tab-content" id="v-pills-tabContent">
                <!-- DETAILS TAB CONTENT -->
                <div class="tab-pane fade show active" id="v-pills-details" role="tabpanel" aria-labelledby="v-pills-details-tab">
                    <!-- User Profile Card -->
                    <div class="card mb-4">
                        <div class="card-body text-center bg-gradient-purple">
                            <!-- Profile Avatar -->
                            <?php if (!empty($display['profile_image'])): ?>
                                <img src="<?= base_url('uploads/profiles/' . esc($display['profile_image'])) ?>" alt="Profile Image" class="border br-white border-width-2-px w-200-px h-200-px rounded-circle object-fit-cover">
                            <?php else: ?>
                               <div class="bg-success text-white w-100-px h-100-px rounded-circle d-flex align-items-center justify-content-center fs-1 fw-bold mx-auto">
                                    <?= esc(strtoupper(substr($display['user_name'], 0, 1))) ?>
                                </div>
                            <?php endif; ?>
                            <h6 class="mb-0 mt-3"><?= esc($display['user_name']); ?></h6>
                            <span class="text-secondary-light mb-2 d-block"><?= esc($display['user_email']); ?></span>

                            <!-- Personal Info -->
                            <div class="mt-4 text-start">
                                <h6 class="text-xl mb-3">Personal Info</h6>
                                <ul class="list-unstyled">
                                    <li><strong>User ID:</strong> <?= esc($display['user_id']); ?></li>
                                    <li><strong>Mobile:</strong> <?= esc($display['phone'] ?? 'N/A'); ?></li>
                                    <li><strong>Login:</strong> <?= esc($display['channel'] ?? 'N/A'); ?></li>
                                    <li><strong>User Type:</strong>
                                        <?= ($display['user_type'] == 1) ? 'Public User' : (($display['user_type'] == 2) ? 'Author' : 'Unknown'); ?>
                                    </li>
                                    <li><strong>Joined:</strong> <?= esc($display['user_join_date']); ?></li>
                                    <li><strong>Bio:</strong> <?= !empty($display['bio']) ? esc($display['bio']) : 'N/A'; ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ACTIVITY SUMMARY -->
                    <?php
                    $totalNetAmount = 0;
                    $ebookCount = 0;
                    $audiobookCount = 0;
                    $ebookTotalAmount = 0;
                    $audiobookTotalAmount = 0;
                    $totalBooks = 0;
                    $totalQuantity = 0;
                    $totalAmount = 0;
                    $totalPaperbackAmount = 0;

                    $subscriptions = $display['subscriptions'] ?? [];
                    $purchased_paperbacks = $display['purchased_paperbacks'] ?? [];

                    foreach ($subscriptions as $sub) {
                        $totalNetAmount += floatval($sub['net_total']);
                        if ($sub['plan_type'] == 1) {
                            $ebookCount++;
                            $ebookTotalAmount += $sub['net_total'];
                        } elseif ($sub['plan_type'] == 2) {
                            $audiobookCount++;
                            $audiobookTotalAmount += $sub['net_total'];
                        }
                    }

                    foreach ($purchased_paperbacks as $pb) {
                        $bookTotal = $pb['price'] * $pb['quantity'];
                        $totalPaperbackAmount += $bookTotal;
                        $totalBooks++;
                        $totalQuantity += $pb['quantity'];
                        $totalAmount += $bookTotal;
                    }

                    $totalAmount += $totalNetAmount;

                    $devices = 0;
                    if (!empty($display['device_id1'])) $devices++;
                    if (!empty($display['device_id2'])) $devices++;
                    if (!empty($display['device_id3'])) $devices++;
                    ?>

                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-gradient-primary text-white">
                            <h5 class="card-title mb-0">Activity Summary</h5>
                        </div>
                        <div class="card-body p-3">
                            <!-- Table Summary -->
                            <table class="table table-bordered table-striped table-hover align-middle text-center">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>Subscriptions</th>
                                        <th>Purchased</th>
                                        <th>Paperback</th>
                                        <th>Free Books</th>
                                        <th>Gifted</th>
                                        <th>Devices</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-light ">
                                        <td><span class="badge bg-info fs-6"><?= count($subscriptions) ?></span></td>
                                        <td><span class="badge bg-success fs-6"><?= isset($display['purchased_books']) ? count($display['purchased_books']) : 0 ?></span></td>
                                        <td><span class="badge bg-warning fs-6"><?= count($purchased_paperbacks) ?></span></td>
                                        <td><span class="badge bg-secondary fs-6"><?= isset($display['free_books']) ? count($display['free_books']) : 0 ?></span></td>
                                        <td><span class="badge bg-danger fs-6"><?= isset($display['author_books']) ? count($display['author_books']) : 0 ?></span></td>
                                        <td><span class="badge bg-dark fs-6"><?= $devices ?></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Breakdown Cards -->
                    <div class="row mt-4">
                        <!-- Ebook Card -->
                        <div class="col-md-4 mb-3">
                            <div class="card p-3 radius-8 shadow-none bg-gradient-end-5">
                                <div class="card-body p-0">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-0">
                                        <div class="d-flex align-items-center gap-2 mb-12">
                                            <span class="mb-0 w-48-px h-48-px bg-base text-pink text-2xl flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6">
                                                <i class="ri-book-2-fill"></i>
                                            </span>
                                            <div>
                                                <span class="mb-0 fw-medium text-secondary-light text-lg">Ebooks</span>
                                                <p class="fw-medium text-secondary-light mb-0"><?= $ebookCount ?> plans</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-8">
                                        <h6 class="fw-semibold mb-0 fs-10"><?= indian_format($ebookTotalAmount, 2) ?></h6>
                                        <span class="text-success-main text-sm">Total</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Audiobook Card -->
                        <div class="col-md-4 mb-3">
                            <div class="card p-3 radius-8 shadow-none bg-gradient-end-3">
                                <div class="card-body p-0">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-0">
                                        <div class="d-flex align-items-center gap-2 mb-12">
                                            <span class="mb-0 w-48-px h-48-px bg-base text-purple text-2xl flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6">
                                                <i class="ri-headphone-fill"></i>
                                            </span>
                                            <div>
                                                <span class="mb-0 fw-medium text-secondary-light text-lg">Audiobooks</span>
                                                <p class="fw-medium text-secondary-light mb-0"><?= $audiobookCount ?> plans</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-8">
                                        <h6 class="fw-semibold mb-0"><?= indian_format($audiobookTotalAmount, 2) ?></h6>
                                        <span class="text-success-main text-sm">Total</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Paperback Card -->
                        <div class="col-md-4 mb-3">
                            <div class="card p-3 radius-8 shadow-none bg-gradient-end-4">
                                <div class="card-body p-0">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-0">
                                        <div class="d-flex align-items-center gap-2 mb-12">
                                            <span class="mb-0 w-48-px h-48-px bg-base text-info text-2xl flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6">
                                                <i class="ri-book-read-fill"></i>
                                            </span>
                                            <div>
                                                <span class="mb-0 fw-medium text-secondary-light text-lg">Paperbacks</span>
                                                <p class="fw-medium text-secondary-light mb-0"><?= $totalQuantity ?> items</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-8">
                                        <h6 class="fw-semibold mb-0"><?= indian_format($totalPaperbackAmount, 2) ?></h6>
                                        <span class="text-success-main text-sm">Total</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grand Total -->
                    <div class="text-center mt-4 fs-5 fw-semibold">
                        Total Spent: <?= indian_format($totalAmount, 2) ?>
                    </div>
                </div>

                <!-- SUBSCRIPTIONS TAB CONTENT -->
                <div class="tab-pane fade" id="v-pills-subscriptions" role="tabpanel" aria-labelledby="v-pills-subscriptions-tab">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <?php if (empty($display['subscriptions']) || count($display['subscriptions']) == 0): ?>
                                <div class="no-subscriptions text-center py-4">
                                    <h5>No Subscriptions</h5>
                                </div>
                            <?php else: ?>
                                <div class="card basic-data-table">
                                    <div class="table-responsive">
                                        <table class="zero-config table table-hover mt-4" id="subscriptions">
                                            <thead class="subscriptions-thead">
                                                <tr class="bg-primary-600">
                                                    <th scope="col">Order ID</th>
                                                    <th scope="col">Start Date</th>
                                                    <th scope="col">End Date</th>
                                                    <th scope="col">Plan</th>
                                                    <th scope="col">Total Books</th>
                                                    <th scope="col">Books Taken</th>
                                                    <th scope="col">Amount</th>
                                                    <th scope="col">Status</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php
                                                $totalNetAmount = 0;
                                                $ebookCount = 0;
                                                $audiobookCount = 0;
                                                $ebookTotalAmount = 0;
                                                $audiobookTotalAmount = 0;

                                                foreach ($display['subscriptions'] as $subscription):

                                                    // Total amount calculation
                                                    $totalNetAmount += $subscription['net_total'];

                                                    // Plan type calculation
                                                    if ($subscription['plan_type'] == 1) {
                                                        $planTypeLabel = "Ebook";
                                                        $ebookCount++;
                                                        $ebookTotalAmount += $subscription['net_total'];
                                                    } elseif ($subscription['plan_type'] == 2) {
                                                        $planTypeLabel = "Audiobook";
                                                        $audiobookCount++;
                                                        $audiobookTotalAmount += $subscription['net_total'];
                                                    } else {
                                                        $planTypeLabel = "Unknown";
                                                    }

                                                    // Books logic
                                                    $booksTaken = count($subscription['books']);
                                                    $totalBooks  = (int)$subscription['total_books'];

                                                    // Date logic
                                                    $endDate = new DateTime($subscription['end_subscribed']);
                                                    $today   = new DateTime();

                                                    // Final status logic
                                                    $status = "Active";

                                                    // Rule 1: Expired
                                                    if ($endDate < $today) {
                                                        $status = "Inactive";
                                                    }

                                                    // Rule 2: All books taken
                                                    if ($booksTaken >= $totalBooks) {
                                                        $status = "Inactive";
                                                    }
                                                ?>

                                                    <tr>
                                                        <td><?= $subscription['order_id'] ?></td>

                                                        <td><?= date('Y-m-d', strtotime($subscription['date_subscribed'])) ?></td>
                                                        <td><?= date('Y-m-d', strtotime($subscription['end_subscribed'])) ?></td>

                                                        <td><?= $subscription['plan_name'] . ' (' . $planTypeLabel . ')' ?></td>

                                                        <td><?= $totalBooks ?></td>
                                                        <td><?= $booksTaken ?></td>

                                                        <td><?= indian_format($subscription['net_total'], 2) ?></td>

                                                        <td>
                                                            <?php if ($status === "Active"): ?>
                                                                <span class="badge bg-success"><?= $status ?></span>
                                                            <?php else: ?>
                                                                <span class="badge bg-danger"><?= $status ?></span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>

                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>


                                    <!-- Totals Summary -->
                                    <div class="row mt-4">
                                        <!-- Ebook Subscriptions -->
                                        <div class="col-md-4">
                                            <div class="card p-3 radius-8 shadow-none bg-gradient-dark-start-1 mb-12">
                                                <div class="card-body p-0">
                                                    <div class="d-flex align-items-center gap-2 mb-12">
                                                        <span class="mb-0 w-48-px h-48-px bg-base text-info text-2xl flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6">
                                                            <i class="ri-book-read-fill"></i>
                                                        </span>
                                                        <div>
                                                            <span class="mb-0 fw-medium text-secondary-light text-lg">Ebook Subscriptions</span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-8">
                                                        <h5 class="fw-semibold mb-0"><?= $ebookCount ?></h5>
                                                        <p class="text-sm mb-0 d-flex align-items-center gap-8">
                                                            <span class="text-white px-2 py-1 rounded-2 fw-medium bg-success-main"><?= indian_format($ebookTotalAmount, 2) ?></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Audiobook Subscriptions -->
                                        <div class="col-md-4">
                                            <div class="card p-3 radius-8 shadow-none bg-gradient-dark-start-2 mb-12">
                                                <div class="card-body p-0">
                                                    <div class="d-flex align-items-center gap-2 mb-12">
                                                        <span class="mb-0 w-48-px h-48-px bg-base text-warning text-2xl flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6">
                                                            <i class="ri-mic-fill"></i>
                                                        </span>
                                                        <div>
                                                            <span class="mb-0 fw-medium text-secondary-light text-lg">Audiobook Subscriptions</span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-8">
                                                        <h5 class="fw-semibold mb-0"><?= $audiobookCount ?></h5>
                                                        <p class="text-sm mb-0 d-flex align-items-center gap-8">
                                                            <span class="text-white px-2 py-1 rounded-2 fw-medium bg-success-main"><?= indian_format($audiobookTotalAmount, 2) ?></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Overall Total -->
                                        <div class="col-md-4">
                                            <div class="card p-3 radius-8 shadow-none bg-gradient-dark-start-3 mb-0">
                                                <div class="card-body p-0">
                                                    <div class="d-flex align-items-center gap-2 mb-12">
                                                        <span class="mb-0 w-48-px h-48-px bg-base text-success text-2xl flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6">
                                                            <i class="ri-money-rupee-circle-fill"></i>
                                                        </span>
                                                        <div>
                                                            <span class="mb-0 fw-medium text-secondary-light text-lg">Overall Total</span>
                                                        </div>
                                                    </div>
                                                   <div class="d-flex align-items-center justify-content-between flex-wrap gap-8">
                                                        <h5 class="mb-0 fs-5"> 
                                                            <span class="mb-0 fw-medium text-secondary-light text-center fs-3 d-block">
                                                                <?= indian_format($totalNetAmount, 2) ?>
                                                            </span>
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                                <h5 class="mt-5"><strong>Subscription Book Details</strong></h5>
                                <div id="toggleAccordion">
                                    <?php foreach ($display['subscriptions'] as $i => $subscription): ?>
                                        <div class="card mb-2">
                                            <div class="card-header" id="heading<?= $i ?>">
                                                <h5 class="mb-0">
                                                    <button class="btn btn-link w-100 text-start d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#collapse<?= $i ?>" aria-expanded="false" aria-controls="collapse<?= $i ?>">
                                                        <div>
                                                            <strong>Order ID:</strong> <?= $subscription['order_id'] ?> |
                                                            <strong>Plan:</strong> <?= $subscription['plan_name'] ?>
                                                        </div>
                                                        <i class="fas fa-chevron-down"></i>
                                                    </button>
                                                </h5>
                                            </div>

                                            <div id="collapse<?= $i ?>" class="collapse" aria-labelledby="heading<?= $i ?>" data-parent="#toggleAccordion">
                                                <div class="card-body">
                                                    <?php if (empty($subscription['books']) || count($subscription['books']) == 0): ?>
                                                        <p class="text-muted text-center">No books taken in this subscription.</p>
                                                    <?php else: ?>
                                                        <!-- Show the first 3 books initially -->
                                                        <div class="table-responsive">
                                                             <table class="zero-config table table-hover mt-4" data-page-length="10">
                                                                <thead class="books-thead">
                                                                    <tr>
                                                                        <th>Book ID</th>
                                                                        <th>Book Name</th>
                                                                        <th>Author</th>
                                                                        <th>Date</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach (array_slice($subscription['books'], 0, 3) as $book): ?>
                                                                        <tr>
                                                                            <td><?= $book['book_id'] ?></td>
                                                                            <td><?= $book['book_name'] ?></td>
                                                                            <td><?= $book['author_name'] ?></td>
                                                                            <td><?= date('d-m-y', strtotime($book['order_date'])) ?></td>

                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        <?php if (count($subscription['books']) > 3): ?>
                                                            <!-- Show More Button -->
                                                            <button class="btn btn-link p-0" data-bs-toggle="collapse" data-bs-target="#fullBooks<?= $i ?>" aria-expanded="false" aria-controls="fullBooks<?= $i ?>">
                                                                Show More
                                                            </button>

                                                            <div id="fullBooks<?= $i ?>" class="collapse">
                                                                <div class="table-responsive">
                                                                    <table class="zero-config table table-hover mt-4" data-page-length="10">
                                                                        <thead class="books-thead">
                                                                            <tr>
                                                                                <th>Book ID</th>
                                                                                <th>Book Name</th>
                                                                                <th>Author</th>
                                                                                <th>Date</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php foreach (array_slice($subscription['books'], 3) as $book): ?>
                                                                                <tr>
                                                                                    <td><?= $book['book_id'] ?></td>
                                                                                    <td><?= $book['book_name'] ?></td>
                                                                                    <td><?= $book['author_name'] ?></td>
                                                                                    <td><?= date('d/m/Y', strtotime($book['order_date'])) ?></td>
                                                                                </tr>
                                                                            <?php endforeach; ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- PURCHASED BOOKS TAB CONTENT -->
                <div class="tab-pane fade" id="v-pills-purchased-books" role="tabpanel" aria-labelledby="v-pills-purchased-books-tab">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <?php if (empty($display['purchased_books']) || count($display['purchased_books']) == 0): ?>
                               <div class="p-16 bg-success-50 radius-8 border-start-width-3-px border-success-main border-top-0 border-end-0 border-bottom-0">
                                    <h5>No Purchased Books</h5>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                     <table class="zero-config table table-hover mt-4" data-page-length="10">
                                        <thead class="purchased-books-thead">
                                            <tr>
                                                <th scope="col">Sl no.</th>
                                                <th scope="col">Book Name</th>
                                                <th scope="col">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($display['purchased_books'] as $index => $purchased_book): ?>
                                                <tr>
                                                    <th scope="row"><?= $index + 1 ?></th>
                                                    <td><?= $purchased_book['purchased_book_title'] ?></td>
                                                    <td><?= date('d-m-y', strtotime($purchased_book['date_purchased'])) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- PURCHASED PAPERBACK TAB CONTENT -->
                <div class="tab-pane fade" id="v-pills-purchased-paperback" role="tabpanel" aria-labelledby="v-pills-purchased-paperback-tab">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <?php if (empty($display['purchased_paperbacks']) || count($display['purchased_paperbacks']) == 0): ?>
                                <div class="no-paperbacks text-center py-4">
                                    <h5>No Purchased Paperback</h5>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                     <table class="zero-config table table-hover mt-4">
                                        <thead class="paperbacks-thead">
                                            <tr>
                                                <th scope="col" class="text-center">Sl No.</th>
                                                <th scope="col" class="text-center">Order Date</th>
                                                <th scope="col" class="text-center">Order ID</th>
                                                <th scope="col" class="text-center">Book Name</th>
                                                <th scope="col" class="text-center">Book Price (₹)</th>
                                                <th scope="col" class="text-center">Quantity</th>
                                                <th scope="col" class="text-center">Tracking ID</th>
                                                <th scope="col" class="text-center">Tracking URL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $totalBooks = 0;
                                                $totalQuantity = 0;
                                                $totalAmount = 0;

                                                foreach ($display['purchased_paperbacks'] as $index => $purchased_paperback):
                                                    $totalBooks++;
                                                    $totalQuantity += $purchased_paperback['quantity'];
                                                    $totalAmount += $purchased_paperback['price'] * $purchased_paperback['quantity'];
                                            ?>
                                                <tr>
                                                    <td scope="row"><?= $index + 1 ?></td>
                                                    <td><?= date('d-m-y', strtotime($purchased_paperback['purchased_date'])) ?></td>
                                                    <td><?= $purchased_paperback['order_id'] ?></td>
                                                    <td><?= $purchased_paperback['purchased_paperback_title'] ?></td>
                                                    <td><?= indian_format($purchased_paperback['price'], 2) ?></td>
                                                    <td><?= $purchased_paperback['quantity'] ?></td>
                                                    <td><?= $purchased_paperback['tracking_id'] ?></td>
                                                    <td>
                                                        <?php if (!empty($purchased_paperback['tracking_url'])): ?>
                                                            <a href="<?= $purchased_paperback['tracking_url'] ?>" target="_blank" class="tracking-link">Track</a>
                                                        <?php else: ?>
                                                            <span class="text-muted">N/A</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card h-100 p-0">
                            <div class="card-header border-bottom bg-base py-16 px-24">
                                <h6 class="text-lg fw-semibold mb-0">Purchase Summary</h6>
                            </div>
                            <div class="card-body p-24">
                                <div class="row gy-4 mb-4">
                                    <div class="col-lg-4 col-sm-6">
                                        <div class="p-16 bg-info-50 radius-8 border-start-width-3-px border-info border-top-0 border-end-0 border-bottom-0">
                                            <h6 class="text-primary-light text-md mb-8">Total Books</h6>
                                            <span class="text-info mb-0 fs-4 fw-bold"><?= $totalBooks ?></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-6">
                                        <div class="p-16 bg-success-50 radius-8 border-start-width-3-px border-success-main border-top-0 border-end-0 border-bottom-0">
                                            <h6 class="text-primary-light text-md mb-8">Total Quantity</h6>
                                            <span class="text-success mb-0 fs-4 fw-bold"><?= $totalQuantity ?></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-6">
                                        <div class="p-16 bg-warning-50 radius-8 border-start-width-3-px border-warning border-top-0 border-end-0 border-bottom-0">
                                            <h6 class="text-primary-light text-md mb-8">Total Amount</h6>
                                            <span class="text-warning mb-0 fs-4 fw-bold"><?= indian_format($totalAmount, 2) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>   

                <!-- FREE BOOKS TAB CONTENT -->
                <div class="tab-pane fade" id="v-pills-free-books" role="tabpanel" aria-labelledby="v-pills-free-books-tab">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <?php if (empty($display['free_books']) || count($display['free_books']) == 0): ?>
                                <div class="p-16 bg-danger-50 radius-8 border-start-width-3-px border-danger-main border-top-0 border-end-0 border-bottom-0">
                                    <h5>No Free Books</h5>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                     <table class="zero-config table table-hover mt-4" data-page-length="10">
                                        <thead class="free-books-thead">
                                            <tr>
                                                <th scope="col">Sl no.</th>
                                                <th scope="col">Book Name</th>
                                                <th scope="col">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($display['free_books'] as $i => $free_book): ?>
                                                <tr>
                                                    <td scope="row"><?= $i + 1 ?></td>
                                                    <td><?= $free_book['free_book_title'] ?></td>
                                                    <td><?= date('d-m-y', strtotime($free_book['date'])) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- AUTHOR GIFT BOOKS -->
                <div class="tab-pane fade" id="v-pills-gift-books" role="tabpanel" aria-labelledby="v-pills-gift-books-tab">
                    <div class="card shadow-sm border-0">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Gifted Books</h5>
                        </div>
                        <div class="card-body">
                            <?php if (count($display['author_books']) == 0): ?>
                                <div class="p-16 bg-info-50 radius-8 border-start-width-3-px border-info border-top-0 border-end-0 border-bottom-0" role="alert">
                                        <div class="d-flex align-items-center gap-2">
                                    <h5>No Gifted Books Found</h5>
                                </div>
                            </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr class="bg-base">
                                                <th scope="col">SL No.</th>
                                                <th scope="col">Author Name</th>
                                                <th scope="col">Book Title</th>
                                                <th scope="col">Gift Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($display['author_books'] as $i => $author_book): ?>
                                                <tr>
                                                    <td><?= $i + 1 ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <?php if (!empty($author_book['author_image'])): ?>
                                                                <img src="<?= base_url('uploads/authors/' . esc($author_book['author_image'])) ?>" 
                                                                    alt="<?= esc($author_book['author_name']) ?>" 
                                                                    class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12 overflow-hidden">
                                                            <?php else: ?>
                                                                <div class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12 d-flex align-items-center justify-content-center bg-light ">
                                                                    <?= strtoupper(substr($author_book['author_name'], 0, 1)) ?>
                                                                </div>
                                                            <?php endif; ?>
                                                            <h6 class="text-md mb-0 fw-medium flex-grow-1"><?= htmlspecialchars($author_book['author_name']) ?></h6>
                                                        </div>
                                                    </td>
                                                    <td><?= htmlspecialchars($author_book['book_title']) ?></td>
                                                    <td><?= htmlspecialchars(date('d-m-y', strtotime($author_book['gift_date']))) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- DEVICES TAB CONTENT -->
                <div class="tab-pane fade" id="v-pills-devices" role="tabpanel" aria-labelledby="v-pills-devices-tab">
                <div class="card shadow-sm border-0">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Registered Devices</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr class="bg-base">
                                        <th scope="col">Sno</th>
                                        <th scope="col">Device ID</th>
                                        <th scope="col">Device Info</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $devices = [
                                        ['id' => $display['device_id1'] ?? '', 'info' => $display['device_info1'] ?? ''],
                                        ['id' => $display['device_id2'] ?? '', 'info' => $display['device_info2'] ?? ''],
                                        ['id' => $display['device_id3'] ?? '', 'info' => $display['device_info3'] ?? '']
                                    ];
                                    foreach ($devices as $index => $device):
                                        if (!empty($device['id'])): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= htmlspecialchars($device['id']) ?></td>
                                                <td><?= htmlspecialchars($device['info']) ?></td>
                                            </tr>
                                        <?php endif;
                                    endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if (empty($display['device_id1']) && empty($display['device_id2']) && empty($display['device_id3'])): ?>
                            <div class="card shadow-none border bg-gradient-start-4">
                                <h6>No devices registered</h6>
                            </div>
                        <?php else: ?>
                            <div class="mt-3 text-end">
                                <button onclick="clear_user_devices(<?= $display['user_id'] ?>)" class="btn btn-danger">
                                    Clear All Devices
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

                <!-- ADD PLAN TAB CONTENT -->
            <div class="tab-pane fade" id="v-pills-add-plan" role="tabpanel" aria-labelledby="v-pills-add-plan-tab">
                <div class="alert alert-success bg-success-100 text-success-600 border-success-600 border-start-width-4-px border-top-0 border-end-2 border-bottom-0 px-24 py-13 mb-0 fw-semibold text-lg radius-4 d-flex align-items-center justify-content-between" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <h5>Add Subscription Plan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="mb-4">
                                    <label for="add_plan">Select Plan</label>
                                    <select id="add_plan" class="form-control">
                                        <option value="">-- Select Plan --</option>
                                        <?php foreach ($plans as $plan): ?>
                                            <option value="<?= $plan['plan_id'] ?>"><?= esc($plan['plan_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-outline-success" onclick="add_plan(<?= $display['user_id'] ?>)">
                                Add Plan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

                <!-- WALLET TAB CONTENT -->
                <div class="tab-pane fade" id="v-pills-wallet" role="tabpanel" aria-labelledby="v-pills-wallet-tab">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <?php if (empty($display['wallet_detail'])): ?>
                                <div class="no-wallet text-center py-4">
                                    <h5>No Wallet Amount</h5>
                                </div>
                            <?php else: ?>
                                <div class="wallet-balances">
                                    <div class="row g-4 mb-4">
                            <!-- INR Balance Card -->
                            <div class="col-md-6">
                            <div class="card shadow-none border bg-gradient-start-2">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="text-muted mb-2">INR Balance</h6>
                                                <h5 class="mb-0" style="font-size: 14px;"> <?= indian_format($display['wallet_detail'][0]['balance_inr'], 2) ?></h5>
                                            </div>
                                            <div class="bg-light-primary p-3 rounded">
                                                <i class="bi bi-currency-rupee fs-4 text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="mt-3 pt-2 border-top">
                                            <small class="text-muted">
                                                <i class="bi bi-info-circle me-1"></i> Indian Rupees
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- USD Balance Card -->
                            <div class="col-md-6">
                                <div class="card shadow-none border bg-gradient-start-3">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="text-muted mb-2">USD Balance</h6>
                                                <h5 class="mb-0" style="font-size: 14px;">$ <?= number_format($display['wallet_detail'][0]['balance_usd'], 2) ?></h5>
                                            </div>
                                            <div class="bg-light-success p-3 rounded">
                                                <i class="bi bi-currency-dollar fs-4 text-success"></i>
                                            </div>
                                        </div>
                                        <div class="mt-3 pt-2 border-top">
                                            <small class="text-muted">
                                                <i class="bi bi-info-circle me-1"></i> US Dollars
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="transactions-table">
                            <div class="table-responsive">
                                <table class="zero-config table table-hover mt-4" data-page-length="10">
                                    <thead class="transactions-thead">
                                        <tr>
                                            <th scope="col" class="text-center">Sl no.</th>
                                            <th scope="col" class="text-center">Currency</th>
                                            <th scope="col" class="text-center">Amount</th>
                                            <th scope="col" class="text-center">Transaction Type</th>
                                            <th scope="col" class="text-center">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($display['transaction_detail'] as $index => $wallet): ?>
                                            <tr>
                                                <td scope="row" class="text-center"><?= $index + 1 ?></td>
                                                <td class="text-center"><?= htmlspecialchars($wallet['currency']) ?></td>
                                                <td class="text-center"> 
                                                    <?= htmlspecialchars($wallet['currency']) === 'INR' ? '' : '$' ?><?= indian_format($wallet['amount'], 2) ?>
                                                </td>
                                                <td class="text-center"><?= htmlspecialchars($wallet['transaction_value']) ?></td>
                                                <td class="text-center">
                                                <?= htmlspecialchars(date('d-m-y', strtotime($wallet['date']))) ?>
                                                </td>

                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
    <script>
        var base_url = "<?= base_url() ?>";

        function clear_user_devices(user_id) {
            $.ajax({
                url: base_url + "user/clearuserdevices",
                type: "POST",
                dataType: "json",
                data: {
                    user_id: user_id
                },
                success: function(response) {
                    if (response.status === 1) {
                        location.reload();
                    } else {
                        alert("Error occurred, please try again");
                    }
                },
                error: function(xhr, status, error) {
                    alert("Server error: " + xhr.responseText);
                }
            });
        }


            function add_plan(user_id) {
                var plan_id = document.getElementById("add_plan").value;

                if (!plan_id) {
                    alert("Please select a plan first.");
                    return;
                }

                $.ajax({
                    url: base_url + "user/addplanforuser",
                    type: "POST",
                    dataType: "json",  // Expecting JSON response
                    data: {
                        "user_id": user_id,
                        "plan_id": plan_id
                    },
                    success: function(response) {
                        if (response.status == 1) {
                            alert("Successfully added plan");
                            location.reload();
                        } else {
                            alert(response.message || "Error occurred, try again");
                        }
                    },
                    error: function(xhr, status, error) {
                        alert("Server error: " + xhr.responseText);
                    }
                });
            }
            function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Email copied to clipboard: ' + text);
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        }
        $(document).ready(function() {
            $('#subscriptions').DataTable({
                "order": [[1, "desc"]],
                "paging": false,       
                "info": false,       
                "searching": false 
            });
        });


    </script>
<?= $this->endSection(); ?>

