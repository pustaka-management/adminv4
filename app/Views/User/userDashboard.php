<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>
<div class="container py-2">
  <!-- Search Header -->
  <div class="col-12">
    <div class="p-16 bg-info-50 radius-8 border-start-width-3-px border-info border-top-0 border-end-width-3-px border-bottom-0">
      <p class="fw-medium text-sm-light mb-1">Find users by ID, email or phone</p><br>
      <form action="<?= base_url('user/getuserdetails') ?>" method="post">
        <?= csrf_field() ?>
        <div class="input-group mb-3">
          <span class="input-group-text bg-light">
            <iconify-icon icon="carbon:user-search"></iconify-icon>
          </span>
          <input id="t-text" onchange="format_email()" type="text" name="identifier" 
                 placeholder="Enter User Id, Email or Phone" 
                 class="form-control" required>
        </div>
        <div class="d-flex justify-content-center">
          <button type="submit" class="btn btn-primary-400 text-white rounded-pill px-20 py-11">
            Search User
          </button>
        </div>
      </form>
    </div>
  </div>
  <br>

  <!-- Cards -->
  <div class="col-12">
    <div class="card radius-12 shadow-none border">
        <div class="card-body p-4">
            <div class="row gy-4">

                <?php 
                $total = $total_registration > 0 ? $total_registration : 1;
                $percentAddressPhone = ($users_with_address_and_phone / $total) * 100;
                $percentOtp = ($users_with_otp / $total) * 100;
                $percentGoogle = ($users_with_google / $total) * 100;
                ?>

                <!-- Total Users -->
                <div class="col-xxl-3 col-xl-4 col-sm-6 mb-4">
                    <div class="card shadow-none border bg-gradient-start-1 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div>
                                    <p class="fw-medium text-primary-light mb-1">Total Users</p>
                                    <h6 class="mb-0"><?= $total_registration ?> users</h6>
                                </div>
                                <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                                    <iconify-icon icon="ri:user-3-line" class="text-white text-2xl mb-0"></iconify-icon>
                                </div>
                            </div>
                            <div class="progress h-8-px w-100 bg-primary-50 mt-3 mb-2">
                                <div class="progress-bar animated-bar rounded-pill bg-primary-600" style="width: 100%"></div>
                            </div>
                            <p class="fw-medium text-sm text-primary-light mb-0 d-flex align-items-center gap-2">
                                100% of total users
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Users With Address & Phone -->
                <div class="col-xxl-3 col-xl-4 col-sm-6 mb-4">
                    <div class="card shadow-none border bg-gradient-start-2 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div>
                                    <p class="fw-medium text-purple mb-1">Address & Phone</p>
                                    <h6 class="mb-0"><?= $users_with_address_and_phone ?> users</h6>
                                </div>
                                <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                                    <iconify-icon icon="mdi:home-account" class="text-white text-2xl mb-0"></iconify-icon>
                                </div>
                            </div>
                            <div class="progress h-8-px w-100 bg-purple mt-3 mb-2" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= round($percentAddressPhone, 1) ?>">
                                <div class="progress-bar rounded-pill bg-purple" style="width: <?= round($percentAddressPhone, 1) ?>%"></div>
                            </div>
                            <p class="fw-medium text-sm text-purple mb-0">
                                <?= round($percentAddressPhone, 1) ?>% from total users
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Users With OTP -->
                <div class="col-xxl-3 col-xl-4 col-sm-6 mb-4">
                    <div class="card shadow-none border bg-gradient-start-3 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div>
                                    <p class="fw-medium text-primary-light mb-1">OTP</p>
                                    <h6 class="mb-0"><?= $users_with_otp ?> users</h6>
                                </div>
                                <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                                    <iconify-icon icon="ri:shield-keyhole-line" class="text-white text-2xl mb-0"></iconify-icon>
                                </div>
                            </div>
                            <div class="progress h-8-px w-100 bg-success-100 mt-3 mb-2" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= round($percentOtp, 1) ?>">
                                <div class="progress-bar animated-bar rounded-pill bg-success-600" style="width: <?= round($percentOtp, 1) ?>%"></div>
                            </div>
                            <p class="fw-medium text-sm text-primary-light mb-0 d-flex align-items-center gap-2">
                                <?= round($percentOtp, 1) ?>% from total users
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Users With Google -->
                <div class="col-xxl-3 col-xl-4 col-sm-6 mb-4">
                    <div class="card shadow-none border bg-gradient-start-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div>
                                    <p class="fw-medium text-primary-light mb-1">Google</p>
                                    <h6 class="mb-0"><?= $users_with_google ?> users</h6>
                                </div>
                                <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                                    <iconify-icon icon="ri:google-fill" class="text-white text-2xl mb-0"></iconify-icon>
                                </div>
                            </div>
                            <div class="progress h-8-px w-100 bg-warning-100 mt-3 mb-2" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= round($percentGoogle, 1) ?>">
                                <div class="progress-bar animated-bar rounded-pill bg-warning-600" style="width: <?= round($percentGoogle, 1) ?>%"></div>
                            </div>
                            <p class="fw-medium text-sm text-primary-light mb-0 d-flex align-items-center gap-2">
                                <?= round($percentGoogle, 1) ?>% from total users
                            </p>
                        </div>
                    </div>
                </div>

            </div> 
        </div> 
    </div>
</div>
<br>
<h6 class="text-center"> User Subscription Summary</h6>
  <!-- Subscription Summary Cards -->
      <div class="col-xxl-12">
        <div class="row gy-4 align-items-stretch">
            <div class="col-xxl-4 col-md-6">
               <a href="" class="d-block text-decoration-none">
                    <div class="radius-8 h-100 text-center p-20 bg-info-focus">
                        <div class="card-body d-flex flex-column justify-content-between p-0">
                            <div class="d-flex align-items-center gap-2 mb-12">
                                <span class="w-48-px h-48-px bg-base text-info text-2xl d-flex justify-content-center align-items-center rounded-circle h6">
                                     <i class="ri-book-2-fill"></i>
                                </span>
                                <div>
                                    <span class="fw-medium text-secondary-light text-lg">Ebooks</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-8">
                              
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xxl-4 col-md-6">
                <a href="" class="d-block text-decoration-none">
                    <div class="radius-8 h-100 text-center p-20 bg-success-100">
                        <div class="card-body d-flex flex-column justify-content-between p-0">
                            <div class="d-flex align-items-center gap-2 mb-12">
                                <span class="w-48-px h-48-px bg-base text-success text-2xl d-flex justify-content-center align-items-center rounded-circle h6">
                                   <i class="ri-headphone-fill"></i>

                                </span>
                                <div>
                                    <center><span class="fw-medium text-secondary-light text-lg">Audiobooks</span></center>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-8">
                               
                            </div>
                        </div>
                    </div>
                </a>
            </div>

             <div class="col-xxl-4 col-md-6">
              <a href="<?= base_url().'user/cancelsubscription' ?>" class="d-block text-decoration-none position-relative">
                  <div class="radius-8 h-100 text-center p-20 bg-danger-100 position-relative overflow-hidden">
                      <div class="card-body d-flex flex-column justify-content-between p-0">
                          <div class="d-flex align-items-center gap-2 mb-12 position-relative">
                              <span class="w-48-px h-48-px bg-base text-danger text-2xl d-flex justify-content-center align-items-center rounded-circle h6 position-relative">
                                  <i class="ri-user-3-fill"></i>
                                  <!-- 🔴 Badge -->
                                 <?php $cancel_count = session()->get('cancel_count') ?? 0; ?>

                                  <?php if ($cancel_count == 0): ?>
                                      <!-- Green tick -->
                                      <span class="text-success">
                                          <i class="bi bi-check-circle-fill"></i> <!-- Using Bootstrap icon -->
                                      </span>
                                  <?php else: ?>
                                      <!-- Red badge with count -->
                                      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger-600 border-0">
                                          <?= esc($cancel_count) ?>
                                      </span>
                                  <?php endif; ?>

                              </span>

                              <div>
                                  <span class="fw-medium text-secondary-light text-lg">Cancel & Renewal</span>
                              </div>
                          </div>

                          <div class="d-flex align-items-center justify-content-between flex-wrap gap-8">
                              <!-- your other content here -->
                          </div>
                      </div>
                  </div>
              </a>
          </div>
      </div>
  </div>
  <br>
  <!-- Daily Register Summary -->
  <div class="col-lg-12 mt-4">
  <div class="card">
    <div class="card-header py-16 px-24 bg-base border border-end-0 border-start-0 border-top-0">
      <h5 class="card-title mb-0">Daily Register Summary (Last 7 Days)</h5>
    </div>
    <div class="card-body">
      <?php if (!empty($login_summary)) : ?>
        <?php
          $summary_data = [];
          $today = date('Y-m-d');
          $seven_days_ago = date('Y-m-d', strtotime('-6 days'));

          foreach ($login_summary as $item) {
            $date = $item['login_date'];
            $type = $item['login_type'];
            $count = $item['login_count'];

            // ✅ Only include last 7 days
            if ($date >= $seven_days_ago && $date <= $today) {
              if (!isset($summary_data[$date])) {
                $summary_data[$date] = [
                  'email_with_google' => 0,
                  'email_with_password' => 0,
                  'mobile_with_otp' => 0,
                  'total' => 0
                ];
              }

              $summary_data[$date][$type] = $count;
              $summary_data[$date]['total'] += $count;
            }
          }

          krsort($summary_data); // Sort newest first
        ?>

        <?php if (!empty($summary_data)) : ?>
          <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
            <table class="table vertical-striped-table mb-0 text-center align-middle">
              <thead class="sticky-top bg-primary-100">
                <tr>
                  <th>Date</th>
                  <th>Google ID</th>
                  <th>Email with Password</th>
                  <th>Mobile with OTP</th>
                  <th>Total Registration</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($summary_data as $date => $counts): ?>
                  <tr>
                    <td>
                      <h6 class="text-md mb-0 fw-normal"><?= date('d M Y', strtotime($date)); ?></h6>
                      <span class="text-sm text-secondary-light fw-normal"><?= date('l', strtotime($date)); ?></span>
                    </td>
                    <td>
                      <span class="bg-info-100 text-info-600 px-3 py-1 rounded-pill fw-medium text-sm"><?= $counts['email_with_google']; ?></span>
                    </td>
                    <td>
                      <span class="bg-lilac-100 text-lilac-700 px-3 py-1 rounded-pill fw-medium text-sm"><?= $counts['email_with_password']; ?></span>
                    </td>
                    <td>
                      <span class="bg-success-100 text-success-700 px-3 py-1 rounded-pill fw-medium text-sm"><?= $counts['mobile_with_otp']; ?></span>
                    </td>
                    <td>
                      <span class="bg-primary-100 text-primary-700 px-4 py-1 rounded-pill fw-semibold"><?= $counts['total']; ?></span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else : ?>
          <p class="text-center my-3">No registered data found for the last 7 days.</p>
        <?php endif; ?>
      <?php else : ?>
        <p class="text-center my-3">No registered data found for the last 7 days.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

  <br>
  <div class="layout-px-spacing">
    <div class="page-header">
        <div class="page-title">
           <h6 class="text-center">Contact US Queries</h6>
        </div>
    </div>
    
    <table id="contact_us" class="zero-config table table-hover mt-4 small">
        <thead class="thead-dark">
            <th>Id</th>
            <th>Date</th>
            <th>Name</th>
            <th>Email</th>
            <th>Subject</th>
            <th>Message</th>
            <th class="text-center">Actions</th>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < count($contact_us); $i++) { ?>
                <tr>
                    <td><?php echo $contact_us[$i]['id'] ?></td>
                    <td><?php echo $contact_us[$i]['date_created'] ?></td>
                    <td><?php echo $contact_us[$i]['username'] ?></td>
                    <td><?php echo $contact_us[$i]['email'] ?></td>
                    <td><?php echo $contact_us[$i]['subject'] ?></td>
                    <td><?php echo $contact_us[$i]['message'] ?></td>
                    <td>
                        <a href="<?= base_url('user/deletecontactus/' . $contact_us[$i]['id']); ?>" 
                           class="btn btn-danger-100 text-danger-600 radius-8 px-14 py-6 text-sm"
                           onclick="return confirm('Are you sure you want to delete this contact?')">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

 
  <!-- Summary Cards -->
  <div class="col-lg-12 mt-4">
  <div class="card">
    <div class="card-header text-center">
      <h5 class="card-title mb-0">User Counts</h5>
    </div>
    <div class="card-body">
      <div class="row mt-3 mb-3">
        <div class="col-xxl-4 col-sm-6 mb-3">
          <div class="card h-100 radius-12 bg-gradient-success text-center">
            <div class="card-body p-24">
              <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-success-600 text-white mb-16 radius-12">
                <iconify-icon icon="fa6-solid:calendar-day" width="40" height="40"></iconify-icon>
              </div>
              <h5 class="info-heading fw-bold mb-0 fs-6">
                <?= date('M'); ?> : <?= esc($monthly_registration); ?>
              </h5>
            </div>
          </div>
        </div>

        <div class="col-xxl-4 col-sm-6 mb-3">
          <div class="card h-100 radius-12 bg-gradient-danger text-center">
            <div class="card-body p-24">
              <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-danger-600 text-white mb-16 radius-12">
                <iconify-icon icon="fa6-solid:calendar-week" width="40" height="40"></iconify-icon>
              </div>
              <h5 class="info-heading fw-bold mb-0 fs-6">
                Week : <?= esc($weekly_registration); ?>
              </h5>
            </div>
          </div>
        </div>

        <div class="col-xxl-4 col-sm-6 mb-3">
          <div class="card h-100 radius-12 bg-gradient-primary text-center">
            <div class="card-body p-24">
              <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-primary-600 text-white mb-16 radius-12">
                <iconify-icon icon="fa6-solid:calendar" width="40" height="40"></iconify-icon>
              </div>
              <h5 class="info-heading fw-bold mb-0 fs-6">
                Total : <?= esc($total_registration); ?>
              </h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
  <br>

  <!-- Yearly Chart Section -->
  <div class="col-md-12">
    <div class="card h-100 shadow-none border">
        <div class="card-header border-bottom bg-gradient-start-1 py-16 px-24">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="text-lg fw-semibold mb-0">Yearly Registered Users</h6>
                <div class="dropdown">
                    <button class="btn btn-sm btn-icon-only text-white" type="button" data-bs-toggle="dropdown">
                        <i class="ri-more-2-fill"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="ri-download-line me-2"></i> Download</a></li>
                        <li><a class="dropdown-item" href="#"><i class="ri-refresh-line me-2"></i> Refresh</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body p-20">
            <div id="zoomAbleLineChart" style="min-height: 300px;"></div>
            <div class="mt-16 pt-16 border-top">
                <div class="row text-center">
                    
                    
                    
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
  <br>
   <!-- Register Users Table -->
  <div class="col-lg-12 mt-4">
  <div class="card">
    <div class="card-header">
      <h5 class="card-title mb-0 text-center">Registered Users - Last 7 Days</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
        <table class="table basic-border-table table-bordered table-hover text-center align-middle mb-0">
          <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
            <tr>
              <th>Sl.No</th>
              <th>User ID</th>
              <th>Name</th>
              <th>Phone</th>
              <th>OTP</th>
              <th>Email</th> 
              <th>Channel</th>
              <th>User Type</th> 
              <th>Register Date</th>
            </tr>
          </thead>
          <tbody>
            <?php 
              if (!empty($login_users)) :
                $today = date('Y-m-d');
                $seven_days_ago = date('Y-m-d', strtotime('-6 days'));
                $filtered_users = [];

                // ✅ Filter users created within last 7 days
                foreach ($login_users as $user) {
                  $created_at = date('Y-m-d', strtotime($user['created_at']));
                  if ($created_at >= $seven_days_ago && $created_at <= $today) {
                    $filtered_users[] = $user;
                  }
                }
            ?>

            <?php if (!empty($filtered_users)) : ?>
              <?php foreach ($filtered_users as $index => $user) : ?>
                <tr>
                  <td><?= $index + 1; ?></td>
                  <td><?= esc($user['user_id']); ?></td>
                  <td><?= esc($user['username']); ?></td>
                  <td><?= esc($user['phone']); ?></td>
                  <td><?= esc($user['otp']); ?></td>
                  <td><?= esc($user['email']); ?></td>
                  <td><?= esc($user['channel']); ?></td>
                  <td>
                    <?= ($user['user_type'] == 1) ? "Public" : (($user['user_type'] == 2) ? "Author" : "Unknown"); ?>
                  </td>
                  <td><?= date('d M Y, h:i A', strtotime($user['created_at'])); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr>
                <td colspan="10">No registered users found for the last 7 days.</td>
              </tr>
            <?php endif; ?>
            <?php else : ?>
              <tr>
                <td colspan="10">No registered users found for the last 7 days.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
  <br>

  
</div>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const years = <?= json_encode($year) ?>;
    const userCounts = <?= json_encode($user_registration_cnt) ?>;
    
    if (!Array.isArray(years) || !Array.isArray(userCounts) || years.length === 0) {
        showFallback('No chart data available');
        return;
    }

    const options = {
        chart: {
            type: 'bar',
            height: '100%',
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: true,
                    zoom: true,
                    pan: true,
                    reset: true
                }
            },
            animations: { 
                enabled: true,
                easing: 'easeinout',
                speed: 800
            }
        },
        series: [{
            name: 'Registered Users',
            data: userCounts
        }],
        xaxis: {
            categories: years,
            labels: { 
                style: { 
                    colors: '#6c757d',
                    fontSize: '11px'
                }
            }
        },
        yaxis: {
            labels: { 
                formatter: (val) => Math.round(val),
                style: {
                    colors: '#6c757d',
                    fontSize: '11px'
                }
            }
        },
        colors: ['#a44fdcff'],
        plotOptions: {
            bar: {
                borderRadius: 6,
                columnWidth: '60%'
            }
        },
        dataLabels: {
            enabled: true,
            formatter: (val) => val.toLocaleString(),
            offsetY: -20,
            style: {
                fontSize: '11px',
                colors: ['#fff'],
                fontWeight: 'bold'
            }
        },
        tooltip: {
            enabled: true,
            y: { 
                formatter: (val) => `${val.toLocaleString()} users`
            }
        },
        grid: {
            borderColor: '#f1f1f1'
        }
    };

    try {
        const chart = new ApexCharts(document.querySelector("#zoomAbleLineChart"), options);
        chart.render();
        
        new ResizeObserver(() => {
            chart.updateOptions({
                chart: {
                    height: Math.max(300, document.querySelector("#zoomAbleLineChart").offsetHeight - 50)
                }
            }, false, true);
        }).observe(document.querySelector("#zoomAbleLineChart"));
        
    } catch (err) {
        console.error('Chart initialization failed:', err);
        showFallback('Chart initialization failed');
    }

    function showFallback(message) {
        document.getElementById('zoomAbleLineChart').innerHTML = `
            <div class="alert alert-warning mb-3">${message}</div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Users</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${years.map((year, i) => `
                            <tr>
                                <td>${year}</td>
                                <td>${userCounts[i] || 0}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>`;
    }
});
</script>
<?= $this->endSection(); ?>