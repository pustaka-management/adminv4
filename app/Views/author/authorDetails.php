<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="col-xxl-14">
    <!-- Tab Buttons -->
    <ul class="nav button-tab nav-pills mb-16" id="pills-tab-four" role="tablist">
        <li class="nav-item" role="presentation">
            <button
                class="nav-link d-flex align-items-center gap-2 fw-semibold text-primary-light radius-4 px-16 py-10 active"
                id="pills-button-icon-home-tab"
                data-bs-toggle="pill"
                data-bs-target="#basic_author_details"
                type="button"
                role="tab"
                aria-controls="basic_author_details"
                aria-selected="true"
            >
                <iconify-icon icon="solar:home-smile-angle-outline" class="text-xl"></iconify-icon>
                <span class="line-height-1">Basic</span>
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button
                class="nav-link d-flex align-items-center gap-2 fw-semibold text-primary-light radius-4 px-16 py-10"
                id="pills-button-icon-details-tab"
                data-bs-toggle="pill"
                data-bs-target="#books_details"
                type="button"
                role="tab"
                aria-controls="books_details"
                aria-selected="false"
            >
                <iconify-icon icon="mdi:book-open-page-variant" class="text-xl"></iconify-icon>
                <span class="line-height-1">Books</span>
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button
                class="nav-link d-flex align-items-center gap-2 fw-semibold text-primary-light radius-4 px-16 py-10"
                id="pills-button-icon-profile-tab"
                data-bs-toggle="pill"
                data-bs-target="#channel_details"
                type="button"
                role="tab"
                aria-controls="channel_details"
                aria-selected="false"
            >
                <iconify-icon icon="mdi:account-group" class="text-xl"></iconify-icon>
                <span class="line-height-1">Channel</span>
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button
                class="nav-link d-flex align-items-center gap-2 fw-semibold text-primary-light radius-4 px-16 py-10"
                id="pills-button-icon-settings-tab"
                data-bs-toggle="pill"
                data-bs-target="#royalty_details"
                type="button"
                role="tab"
                aria-controls="royalty_details"
                aria-selected="false"
            >
                <iconify-icon icon="mdi:crown-outline" class="text-xl"></iconify-icon>
                <span class="line-height-1">Royalty</span>
            </button>
        </li>
    </ul>
    <br>
    <div class="tab-content" id="pills-tabContent">

        <!-- Basic Tab Content -->
        <div class="tab-pane fade show active" id="basic_author_details" role="tabpanel" aria-labelledby="pills-button-icon-home-tab">
            <div class="row gy-4">
                <!-- Left Profile Card -->
                <div class="col-lg-4">
                    <div class="user-grid-card position-relative border radius-16 overflow-hidden bg-base h-100">
                        <img src="<?= base_url('assets/images/cover.png') ?>" alt="" class="w-100 object-fit-cover">
                        <div class="pb-24 ms-16 mb-24 me-16" style="margin-top: -100px;">
                            <div class="text-center border border-top-0 border-start-0 border-end-0">
                                <?php
                                    $author_img_url = 'https://pustaka-assets.s3.ap-south-1.amazonaws.com/';
                                    $author_image = $author_details['basic_author_details']['author_image'] ?? '';
                                    $default_image = base_url('assets/images/default_author.png');
                                    $final_image = $author_image ? $author_img_url . $author_image : $default_image;
                                ?>

                                <img
                                    src="<?= esc($final_image) ?>"
                                    alt="Author Image"
                                    class="border br-white border-width-20-px w-200-px h-200-px rounded-circle object-fit-cover"
                                >

                                <h6 class="mb-0 mt-16"><?= $author_details['basic_author_details']['author_name'] ?></h6>
                                <span class="text-secondary-light mb-16"><?= $author_details['basic_author_details']['email'] ?></span>
                            </div>
                            <div class="mt-24">
                                <h6 class="text-xl mb-16">Personal Info</h6>
                                <ul>
                                    <li class="d-flex align-items-center gap-1 mb-12">
                                        <span class="w-30 text-md fw-semibold text-primary-light">Mobile</span>
                                        <span class="w-70 text-secondary-light fw-medium">: <?= $author_details['basic_author_details']['mobile'] ?></span>
                                    </li>

                                    <li class="d-flex align-items-center gap-1 mb-12">
                                        <span class="w-30 text-md fw-semibold text-primary-light">Password</span>
                                        <span class="w-70 text-secondary-light fw-medium">: <?= $author_details['user_password'] ?></span>
                                    </li>

                                    <li class="d-flex align-items-center gap-1 mb-12">
                                        <span class="w-30 text-md fw-semibold text-primary-light">Address</span>
                                        <span class="w-70 text-secondary-light fw-medium">: <?= $author_details['basic_author_details']['author_address'] ?></span>
                                    </li>

                                    <li class="d-flex align-items-center gap-1 mb-12">
                                        <span class="w-30 text-md fw-semibold text-primary-light">Copyright Owner</span>
                                        <span class="w-70 text-secondary-light fw-medium">: <?= $author_details['basic_author_details']['copy_right_owner_name'] ?></span>
                                    </li>

                                    <li class="d-flex align-items-center gap-1 mb-12">
                                        <span class="w-30 text-md fw-semibold text-primary-light">Publisher Name</span>
                                        <span class="w-70 text-secondary-light fw-medium">: <?= $author_details['basic_author_details']['publisher_names'] ?></span>
                                    </li>
                                    <li class="d-flex align-items-center gap-1 mb-12">
                                        <span class="w-30 text-md fw-semibold text-primary-light">Created Date</span>
                                        <span class="w-70 text-secondary-light fw-medium">
                                            : <span class="badge bg-danger">
                                                <?= $author_details['basic_author_details']['formatted_created_at'] ?>
                                            </span>
                                        </span>
                                    </li>
                                    
                                    <li class="d-flex align-items-center gap-1 mb-12">
                                        <span class="w-30 text-md fw-semibold text-primary-light">Activate Date</span>
                                        <span class="w-70 text-secondary-light fw-medium">
                                            : <span class="badge bg-success">
                                                <?= date("d M Y", strtotime($author_details['basic_author_details']['activated_at'])) ?>
                                            </span>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side -->
                <div class="col-lg-8">
                    <div class="card h-100">
                        <div class="user-grid-card position-relative border radius-16 overflow-hidden bg-base h-100">
                            <div class="card-body p-24">
                                <h6 class="text-xl mb-16 text-center">General Info</h6>
                                <ul>
                                    <li class="d-flex align-items-center gap-1 mb-12">
                                        <span class="w-30 text-md fw-semibold text-primary-light">Bank Acc</span>
                                        <span class="w-70 text-secondary-light fw-medium">: <?= $author_details['basic_author_details']['bank_acc_no'] ?></span>
                                    </li>

                                    <li class="d-flex align-items-center gap-1 mb-12">
                                        <span class="w-30 text-md fw-semibold text-primary-light">IFSC</span>
                                        <span class="w-70 text-secondary-light fw-medium">: <?= $author_details['basic_author_details']['ifsc_code'] ?></span>
                                    </li>

                                    <li class="d-flex align-items-center gap-1 mb-12">
                                        <span class="w-30 text-md fw-semibold text-primary-light">PAN</span>
                                        <span class="w-70 text-secondary-light fw-medium">: <?= $author_details['basic_author_details']['pan_number'] ?></span>
                                    </li>

                                    <li class="d-flex align-items-center gap-1 mb-12">
                                        <span class="w-30 text-md fw-semibold text-primary-light">Social</span>
                                        <span class="w-70 text-secondary-light fw-medium">
                                            : FB: <?= $author_details['basic_author_details']['fb_url'] ?>,
                                            Twitter: <?= $author_details['basic_author_details']['twitter_url'] ?>,
                                            Blog: <?= $author_details['basic_author_details']['blog_url'] ?>
                                        </span>
                                    </li>

                                    <li class="d-flex align-items-center gap-1 mb-12">
                                        <span class="w-30 text-md fw-semibold text-primary-light">Agreement</span>
                                        <span class="w-70 text-secondary-light fw-medium">: <?= $author_details['basic_author_details']['agreement_details'] ?></span>
                                    </li>

                                    <li class="d-flex align-items-center gap-1">
                                        <span class="w-30 text-md fw-semibold text-primary-light">Description</span>
                                        <span class="w-70 text-secondary-light fw-medium">: <?= $author_details['basic_author_details']['description'] ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Books Tab -->
        <div class="tab-pane fade" id="books_details" role="tabpanel" aria-labelledby="pills-button-icon-details-tab">
            <div class="user-grid-card position-relative border radius-16 overflow-hidden bg-base h-100">
                <div class="card-header py-16 px-24 bg-base border border-end-0 border-start-0 border-top-0">
                    <h6 class="text-lg mb-0">Books Overview</h6>
                </div>

                <div class="card-body p-24 pt-10">
                    <ul class="nav focus-tab nav-pills mb-16" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link fw-semibold text-primary-light radius-4 px-16 py-10 active"
                                id="pills-ebooks-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#books_tab"
                                type="button"
                                role="tab"
                                aria-controls="books_tab"
                                aria-selected="true"
                            >
                                <iconify-icon icon="mdi:book-open-page-variant" class="me-1"></iconify-icon>
                                E-Books
                            </button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link fw-semibold text-primary-light radius-4 px-16 py-10"
                                id="pills-audio-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#audio_books_tab"
                                type="button"
                                role="tab"
                                aria-controls="audio_books_tab"
                                aria-selected="false"
                            >
                                <iconify-icon icon="mdi:headphones" class="me-1"></iconify-icon>
                                Audio Books
                            </button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link fw-semibold text-primary-light radius-4 px-16 py-10"
                                id="pills-paper-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#paperback_books_tab"
                                type="button"
                                role="tab"
                                aria-controls="paperback_books_tab"
                                aria-selected="false"
                            >
                                <iconify-icon icon="mdi:book" class="me-1"></iconify-icon>
                                Paperbacks
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="books-pills-tabContent">
                        <!-- E-Books -->
                        <div class="tab-pane fade show active" id="books_tab" role="tabpanel" aria-labelledby="pills-ebooks-tab">
                            <div class="row gy-4">
                                <div class="col-xxl-3 col-sm-6">
                                    <div class="card h-100 radius-12 bg-gradient-purple text-center">
                                        <div class="card-body p-24">
                                            <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-lilac-600 text-white mb-16 radius-12">
                                                <iconify-icon icon="mdi:book-open-page-variant" class="h5 mb-0"></iconify-icon>
                                            </div>
                                            <h6 class="mb-8">Total E-Books</h6>
                                            <h4><?= $ebook_count['total_count']['ebook_count']; ?></h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xxl-3 col-sm-6">
                                    <div class="card h-100 radius-12 bg-gradient-primary text-center">
                                        <div class="card-body p-24">
                                            <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-primary-600 text-white mb-16 radius-12">
                                                <iconify-icon icon="mdi:book-check" class="h5 mb-0"></iconify-icon>
                                            </div>
                                            <h6 class="mb-8">Active</h6>
                                            <h4><?= $ebook_count['active']['ebook_count']; ?></h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xxl-3 col-sm-6">
                                    <div class="card h-100 radius-12 bg-gradient-success text-center">
                                        <div class="card-body p-24">
                                            <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-success-600 text-white mb-16 radius-12">
                                                <iconify-icon icon="mdi:book-remove" class="h5 mb-0"></iconify-icon>
                                            </div>
                                            <h6 class="mb-8">Inactive</h6>
                                            <h4><?= $ebook_count['inactive']['ebook_count']; ?></h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xxl-3 col-sm-6">
                                    <div class="card h-100 radius-12 bg-gradient-danger text-center">
                                        <div class="card-body p-24">
                                            <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-danger-600 text-white mb-16 radius-12">
                                                <iconify-icon icon="mdi:book-lock" class="h5 mb-0"></iconify-icon>
                                            </div>
                                            <h6 class="mb-8">Suspended</h6>
                                            <h4><?= $ebook_count['suspended']['ebook_count']; ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Audio Books -->
                        <div class="tab-pane fade" id="audio_books_tab" role="tabpanel" aria-labelledby="pills-audio-tab">
                            <?php if ($audio_count['total_count']['audio_count'] > 0) { ?>
                                <div class="row gy-4">
                                    <div class="col-xxl-3 col-sm-6">
                                        <div class="card h-100 radius-12 bg-gradient-purple text-center">
                                            <div class="card-body p-24">
                                                <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-lilac-600 text-white mb-16 radius-12">
                                                    <iconify-icon icon="mdi:headphones" class="h5 mb-0"></iconify-icon>
                                                </div>
                                                <h6 class="mb-8">Total Audio Books</h6>
                                                <h4><?= $audio_count['total_count']['audio_count']; ?></h4>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xxl-3 col-sm-6">
                                        <div class="card h-100 radius-12 bg-gradient-primary text-center">
                                            <div class="card-body p-24">
                                                <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-primary-600 text-white mb-16 radius-12">
                                                    <iconify-icon icon="mdi:headphones-check" class="h5 mb-0"></iconify-icon>
                                                </div>
                                                <h6 class="mb-8">Active</h6>
                                                <h4><?= $audio_count['active']['audio_count']; ?></h4>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xxl-3 col-sm-6">
                                        <div class="card h-100 radius-12 bg-gradient-success text-center">
                                            <div class="card-body p-24">
                                                <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-success-600 text-white mb-16 radius-12">
                                                    <iconify-icon icon="mdi:headphones-off" class="h5 mb-0"></iconify-icon>
                                                </div>
                                                <h6 class="mb-8">Inactive</h6>
                                                <h4><?= $audio_count['inactive']['audio_count']; ?></h4>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xxl-3 col-sm-6">
                                        <div class="card h-100 radius-12 bg-gradient-danger text-center">
                                            <div class="card-body p-24">
                                                <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-danger-600 text-white mb-16 radius-12">
                                                    <iconify-icon icon="mdi:headphones-pause" class="h5 mb-0"></iconify-icon>
                                                </div>
                                                <h6 class="mb-8">Suspended</h6>
                                                <h4><?= $audio_count['suspended']['audio_count']; ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <center><h3>No Audio Books</h3></center>
                            <?php } ?>
                        </div>

                        <!-- Paperbacks -->
                        <div class="tab-pane fade" id="paperback_books_tab" role="tabpanel" aria-labelledby="pills-paper-tab">
                            <?php if (isset($paperback['total_counts']) && $paperback['total_counts']['paperback_count'] > 0) { ?>
                                <div class="row gy-4">
                                    <div class="col-xxl-3 col-sm-6">
                                        <div class="card h-100 radius-12 bg-gradient-purple text-center">
                                            <div class="card-body p-24">
                                                <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-lilac-600 text-white mb-16 radius-12">
                                                    <iconify-icon icon="mdi:book" class="h5 mb-0"></iconify-icon>
                                                </div>
                                                <h6 class="mb-8">Total Paperbacks</h6>
                                                <h4><?= $paperback['total_counts']['paperback_count']; ?></h4>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xxl-3 col-sm-6">
                                        <div class="card h-100 radius-12 bg-gradient-primary text-center">
                                            <div class="card-body p-24">
                                                <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-primary-600 text-white mb-16 radius-12">
                                                    <iconify-icon icon="mdi:book-check" class="h5 mb-0"></iconify-icon>
                                                </div>
                                                <h6 class="mb-8">Active</h6>
                                                <h4><?= $paperback['active']['paperback_count']; ?></h4>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xxl-3 col-sm-6">
                                        <div class="card h-100 radius-12 bg-gradient-success text-center">
                                            <div class="card-body p-24">
                                                <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-success-600 text-white mb-16 radius-12">
                                                    <iconify-icon icon="mdi:book-remove" class="h5 mb-0"></iconify-icon>
                                                </div>
                                                <h6 class="mb-8">Inactive</h6>
                                                <h4><?= $paperback['inactive']['paperback_count']; ?></h4>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xxl-3 col-sm-6">
                                        <div class="card h-100 radius-12 bg-gradient-danger text-center">
                                            <div class="card-body p-24">
                                                <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-danger-600 text-white mb-16 radius-12">
                                                    <iconify-icon icon="mdi:book-lock" class="h5 mb-0"></iconify-icon>
                                                </div>
                                                <h6 class="mb-8">Suspended</h6>
                                                <h4><?= $paperback['suspended']['paperback_count']; ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <center><h3>No Paperbacks</h3></center>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Channel Tab -->
        <div class="tab-pane fade" id="channel_details" role="tabpanel" aria-labelledby="pills-button-icon-profile-tab">
            <div class="row gy-4">

                <!-- Pustaka -->
                <div class="col-xxl-3 col-sm-6">
                    <div class="card h-100 radius-12 bg-gradient-purple text-center">
                        <div class="card-body p-24">
                        <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-lilac-600 text-white mb-16 radius-12">
                            <iconify-icon icon="mdi:book-open-page-variant" class="h5 mb-0"></iconify-icon>
                        </div>
                        <h6 class="mb-8">Pustaka</h6>
                        <h3 class="mb-8"><?php echo $author_details['channel_wise_cnt']['pustaka']; ?></h3>
                        <p class="card-text mb-8 text-secondary-light">Books available on Pustaka platform.</p>
                        <a href="<?php echo base_url()."author/authorpustakadetails/".$author_id ?>" class="btn text-lilac-600 hover-text-lilac px-0 py-0 mt-16 d-inline-flex align-items-center gap-2">
                            View Details <iconify-icon icon="iconamoon:arrow-right-2" class="text-xl"></iconify-icon>
                        </a>
                        </div>
                    </div>
                </div>

                <!-- Amazon -->
                <div class="col-xxl-3 col-sm-6">
                    <div class="card h-100 radius-12 bg-gradient-primary text-center">
                        <div class="card-body p-24">
                        <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-primary-600 text-white mb-16 radius-12">
                            <iconify-icon icon="mdi:amazon" class="h5 mb-0"></iconify-icon>
                        </div>
                        <h6 class="mb-8">Amazon</h6>
                        <h3 class="mb-8"><?php echo $author_details['channel_wise_cnt']['amazon']; ?></h3>
                        <p class="card-text mb-8 text-secondary-light">Available titles on Amazon store.</p>
                        <a href="<?php echo base_url()."author/authoramazondetails/".$author_id ?>" class="btn text-primary-600 hover-text-primary px-0 py-10 d-inline-flex align-items-center gap-2">
                            View Details <iconify-icon icon="iconamoon:arrow-right-2" class="text-xl"></iconify-icon>
                        </a>
                        </div>
                    </div>
                </div>

                <!-- Google -->
                <div class="col-xxl-3 col-sm-6">
                    <div class="card h-100 radius-12 bg-gradient-success text-center">
                        <div class="card-body p-24">
                        <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-success-600 text-white mb-16 radius-12">
                            <iconify-icon icon="mdi:google" class="h5 mb-0"></iconify-icon>
                        </div>
                        <h6 class="mb-8">Google Books</h6>
                        <h3 class="mb-8"><?php echo $author_details['channel_wise_cnt']['google']; ?></h3>
                        <p class="card-text mb-8 text-secondary-light">Books available through Google Books.</p>
                        <a href="<?php echo base_url()."author/authorsgoogledetails/".$author_id ?>" class="btn text-success-600 hover-text-success px-0 py-10 d-inline-flex align-items-center gap-2">
                            View Details <iconify-icon icon="iconamoon:arrow-right-2" class="text-xl"></iconify-icon>
                        </a>
                        </div>
                    </div>
                </div>

                <!-- Overdrive -->
                <div class="col-xxl-3 col-sm-6">
                    <div class="card h-100 radius-12 bg-gradient-danger text-center">
                        <div class="card-body p-24">
                        <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-danger-600 text-white mb-16 radius-12">
                            <iconify-icon icon="mdi:book-account" class="h5 mb-0"></iconify-icon>
                        </div>
                        <h6 class="mb-8">Overdrive</h6>
                        <h3 class="mb-8"><?php echo $author_details['channel_wise_cnt']['overdrive']; ?></h3>
                        <p class="card-text mb-8 text-secondary-light">eBooks available via Overdrive.</p>
                        <a href="<?php echo base_url()."author/authoroverdrivedetails/".$author_id ?>" class="btn text-danger-600 hover-text-danger px-0 py-10 d-inline-flex align-items-center gap-2">
                            View Details <iconify-icon icon="iconamoon:arrow-right-2" class="text-xl"></iconify-icon>
                        </a>
                        </div>
                    </div>
                </div>

                <!-- Scribd -->
                <div class="col-xxl-3 col-sm-6">
                    <div class="card h-100 radius-12 bg-gradient-success text-center">
                        <div class="card-body p-24">
                        <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-warning-600 text-white mb-16 radius-12">
                            <iconify-icon icon="mdi:file-document-outline" class="h5 mb-0"></iconify-icon>
                        </div>
                        <h6 class="mb-8">Scribd</h6>
                        <h3 class="mb-8"><?php echo $author_details['channel_wise_cnt']['scribd']; ?></h3>
                        <p class="card-text mb-8 text-secondary-light">Audiobooks and eBooks on Scribd.</p>
                        <a href="<?php echo base_url()."author/authorscribddetails/".$author_id ?>" class="btn text-warning-600 hover-text-warning px-0 py-10 d-inline-flex align-items-center gap-2">
                            View Details <iconify-icon icon="iconamoon:arrow-right-2" class="text-xl"></iconify-icon>
                        </a>
                        </div>
                    </div>
                </div>

                <!-- Storytel -->
                <div class="col-xxl-3 col-sm-6">
                    <div class="card h-100 radius-12 bg-gradient-danger text-center">
                        <div class="card-body p-24">
                        <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-info-600 text-white mb-16 radius-12">
                            <iconify-icon icon="mdi:headphones" class="h5 mb-0"></iconify-icon>
                        </div>
                        <h6 class="mb-8">Storytel</h6>
                        <h3 class="mb-8"><?php echo $author_details['channel_wise_cnt']['storytel']; ?></h3>
                        <p class="card-text mb-8 text-secondary-light">Audiobooks on Storytel platform.</p>
                        <a href="<?php echo base_url()."author/authorstoryteldetails/".$author_id ?>" class="btn text-info-600 hover-text-info px-0 py-10 d-inline-flex align-items-center gap-2">
                            View Details <iconify-icon icon="iconamoon:arrow-right-2" class="text-xl"></iconify-icon>
                        </a>
                        </div>
                    </div>
                </div>

                <!-- Pratilipi -->
                <div class="col-xxl-3 col-sm-6">
                    <div class="card h-100 radius-12 bg-gradient-purple text-center">
                        <div class="card-body p-24">
                        <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center bg-lilac-600 text-white mb-16 radius-12">
                            <iconify-icon icon="mdi:library" class="h5 mb-0"></iconify-icon>
                        </div>
                        <h6 class="mb-8">Pratilipi</h6>
                        <h3 class="mb-8"><?php echo $author_details['channel_wise_cnt']['pratilipi']; ?></h3>
                        <p class="card-text mb-8 text-secondary-light">Stories published on Pratilipi.</p>
                        <a href="<?php echo base_url()."author/authorpratilipidetails/".$author_id ?>" class="btn text-lilac-600 hover-text-lilac px-0 py-10 d-inline-flex align-items-center gap-2">
                            View Details <iconify-icon icon="iconamoon:arrow-right-2" class="text-xl"></iconify-icon>
                        </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Royalty Tab -->
        <div class="tab-pane fade" id="royalty_details" role="tabpanel" aria-labelledby="pills-button-icon-settings-tab">
            <?php 
            $allowed_user_type = 4; 
            $user_type = $_SESSION['user_type'] ?? null;
            ?>
            <div class="row row-cols-xxxl-3 row-cols-lg-3 row-cols-sm-2 row-cols-1 gy-4">
                <!-- Total Revenue Card -->
                <div class="col">
                    <div class="card shadow-none border bg-gradient-start-1 h-100">
                        <div class="card-body p-20">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div>
                                    <p class="fw-medium text-primary-light mb-1">Total Revenue</p>
                                    <h6 class="mb-0">
                                        <?php echo ($user_type == $allowed_user_type) 
                                            ? indian_format($royalty['details'][0]['total_revenue'] ?? 0, 2) 
                                            : '###'; ?>
                                    </h6>
                                </div>
                                <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                                    <iconify-icon icon="feather:trending-up" class="text-white text-2xl mb-0"></iconify-icon>
                                </div>
                            </div>
                            <p class="fw-medium text-sm text-primary-light mt-12 mb-0">
                                <span class="d-inline-flex align-items-center gap-1 text-info">
                                    <iconify-icon icon="bxs:info-circle" class="text-xs"></iconify-icon>
                                </span>
                                Note: Pustaka revenue is not included
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Total Royalty Card -->
                <div class="col">
                    <div class="card shadow-none border bg-gradient-start-2 h-100">
                        <div class="card-body p-20">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div>
                                    <p class="fw-medium text-primary-light mb-1">Total Royalty</p>
                                    <h6 class="mb-0">
                                        <?php echo ($user_type == $allowed_user_type) 
                                            ? indian_format($royalty['details'][0]['total_royalty'] ?? 0, 2) 
                                            : '###'; ?>
                                    </h6>
                                </div>
                                <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                                    <iconify-icon icon="fa-solid:award" class="text-white text-2xl mb-0"></iconify-icon>
                                </div>
                            </div>
                            <div class="mt-3">
                                <p class="fw-medium text-primary-light mb-1">Royalty Percentage</p>
                                <h6 class="mb-0">
                                    <?php 
                                    if ($user_type == $allowed_user_type) {
                                        $total_revenue = $royalty['details'][0]['total_revenue'] ?? 0;
                                        $total_royalty = $royalty['details'][0]['total_royalty'] ?? 0;
                                        $profit_percentage = ($total_revenue > 0) ? ($total_royalty / $total_revenue) * 100 : 0;
                                        echo indian_format($profit_percentage, 2) . '%';
                                    } else {
                                        echo '###';
                                    }
                                    ?>
                                </h6>
                            </div>

                            <p class="fw-medium text-sm text-primary-light mt-12 mb-0">
                                <span class="d-inline-flex align-items-center gap-1 text-info">
                                    <iconify-icon icon="bxs:info-circle" class="text-xs"></iconify-icon>
                                </span>
                                Note: Paperback royalty not included
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Royalty Settlement Card -->
                <div class="col">
                    <div class="card shadow-none border bg-gradient-start-3 h-100">
                        <div class="card-body p-20">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <p class="fw-medium text-primary-light mb-0">
                                Royalty Settlement
                                </p>
                                <div class="w-40-px h-40-px bg-success-main rounded-circle d-flex justify-content-center align-items-center ms-2">
                                    <iconify-icon 
                                        icon="fa6-solid:file-invoice-dollar" 
                                        class="text-white text-xl">
                                    </iconify-icon>
                                </div>
                            </div>

                            <!-- Settlement Details -->
                            <?php 
                                if ($user_type == $allowed_user_type) {
                                $total_settlement  = $author['total'][0]['total_settlement'] ?? 0;
                                $total_bonus_value = $author['total'][0]['total_bonus'] ?? 0;
                                $final_settlement  = $total_settlement - $total_bonus_value;
                            ?>
                                <h6 class="mb-0">Settlement: <?php echo indian_format($total_settlement, 2); ?></h6>
                                <p class="mb-0 text-sm">Bonus: <?php echo indian_format($total_bonus_value, 2); ?></p>
                                <h6 class="mt-2 mb-0">(Final) <?php echo indian_format($final_settlement, 2); ?></h6>
                            <?php 
                                } else { 
                                echo '<h6>###</h6>'; 
                                } 
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <br><br>
            <?php 
            $allowed_user_type = 4; 
            $user_type = $_SESSION['user_type'] ?? null;
            ?>
            <div class="table-responsive mt-4">
                <table class="table contextual-table">
                    <thead>
                        <tr class="table-secondary">
                            <th class="text-center">#</th>
                            <th>Type</th>
                            <th>Total Revenue</th>
                            <th>Total Royalty</th>
                            <th>Revenue Share %</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $types = ['ebook' => 'table-primary', 'audiobook' => 'table-success', 'paperback' => 'table-warning'];
                        $count = 1;
                        $grand_total_revenue = 0;
                        $grand_total_royalty = 0;

                        foreach ($types as $type => $row_class) {
                            $total_revenue = $royalty[$type][0]['total_revenue'] ?? 0;
                            $total_royalty = $royalty[$type][0]['total_royalty'] ?? 0;
                            $profit_percentage = ($total_revenue > 0) ? ($total_royalty / $total_revenue) * 100 : 0;

                            $grand_total_revenue += $total_revenue;
                            $grand_total_royalty += $total_royalty;
                        ?>
                            <tr class="<?php echo $row_class; ?>">
                                <td class="text-center"><?php echo $count++; ?></td>
                                <td><?php echo ucfirst($type); ?></td>
                                <td><?php echo ($user_type == $allowed_user_type) ? indian_format($total_revenue, 2) : '#'; ?></td>
                                <td><?php echo ($user_type == $allowed_user_type) ? indian_format($total_royalty, 2) : '#'; ?></td>
                                <td><?php echo ($user_type == $allowed_user_type) ? indian_format($profit_percentage, 2) . '%' : '#'; ?></td>
                            </tr>
                        <?php } ?>
                        <tr class="table-danger">
                            <td class="text-center" colspan="2"><strong>Total</strong></td>
                            <td><strong><?php echo ($user_type == $allowed_user_type) ?  indian_format($grand_total_revenue, 2) : '#'; ?></strong></td>
                            <td><strong><?php echo ($user_type == $allowed_user_type) ?  indian_format($grand_total_royalty, 2) : '#'; ?></strong></td>
                            <td><strong><?php echo ($grand_total_revenue > 0) ? indian_format(($grand_total_royalty / $grand_total_revenue) * 100, 2) . '%' : '#'; ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br><br>
            <div class="card-body p-24">
                <!-- Tabs Navigation -->
                <div class="d-flex flex-wrap align-items-center gap-1 justify-content-between mb-16">
                    <ul class="nav border-gradient-tab nav-pills mb-0" id="pendingTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center active" 
                                id="tab-revenue-tab" 
                                data-bs-toggle="pill" 
                                data-bs-target="#tab-revenue" 
                                type="button" role="tab" 
                                aria-controls="tab-revenue" 
                                aria-selected="true">
                                Pending Revenue & Royalty
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center" 
                                id="tab-channel-tab" 
                                data-bs-toggle="pill" 
                                data-bs-target="#tab-channel" 
                                type="button" role="tab" 
                                aria-controls="tab-channel" 
                                aria-selected="false">
                                Channel Wise Pending
                            </button>
                        </li>
                    </ul>
                </div><br>
                <div class="tab-content" id="pendingTabsContent">
                    <!-- Pending Revenue & Royalty -->
                    <div class="tab-pane fade show active" id="tab-revenue" role="tabpanel" aria-labelledby="tab-revenue-tab">
                        <div class="table-responsive scroll-sm">
                            <table class="table bordered-table sm-table mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">Pending Revenue</th>
                                        <th scope="col" class="text-center">Pending Royalty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center fw-semibold">
                                            <?php 
                                                if (isset($user_type) && $user_type == 4) {
                                                    echo indian_format($pending['author_pending'][0]['total_revenue'] ?? 0, 2);
                                                } else {
                                                    echo "#";
                                                }
                                            ?>
                                        </td>
                                        <td class="text-center fw-semibold">
                                            <?php 
                                                if (isset($user_type) && $user_type == 4) {
                                                    echo  indian_format($pending['author_pending'][0]['total_royalty'] ?? 0, 2);
                                                } else {
                                                    echo "#";
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--Channel Wise Pending -->
                    <div class="tab-pane fade" id="tab-channel" role="tabpanel" aria-labelledby="tab-channel-tab">
                        <div class="table-responsive scroll-sm">
                            <table class="table bordered-table sm-table mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Channel</th>
                                        <th scope="col" class="text-center">Total Pending Revenue</th>
                                        <th scope="col" class="text-center">Total Pending Royalty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($pending['channel_pending'])) { ?>
                                        <?php foreach ($pending['channel_pending'] as $row) { ?>
                                            <tr>
                                                <td class="fw-medium"><?php echo ucfirst($row['channel']); ?></td>
                                                <td class="text-center">
                                                    <?php echo ($user_type == 4) ? indian_format($row['total_pending_revenue'], 2) : '#'; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php echo ($user_type == 4) ? indian_format($row['total_pending_royalty'], 2) : '#'; ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-secondary-light">No pending data available.</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?= $this->section('script'); ?>
                <script src="<?= base_url('assets/js/homeOneChart.js') ?>"></script>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const triggerTabList = [].slice.call(document.querySelectorAll('#pendingTabs button'))
                        triggerTabList.forEach(function(tabEl) {
                            tabEl.addEventListener('click', function(event) {
                                event.preventDefault()
                                const tabTrigger = new bootstrap.Tab(tabEl)
                                tabTrigger.show()
                            })
                        })
                    });
                </script>
            <?= $this->endSection(); ?>
            <br><br>
            <div class="container">
                <div class="layout-px-spacing">
                    <div class="card p-0 overflow-hidden position-relative radius-12 h-100">
                        <div class="card-header py-16 px-24 bg-base border border-end-0 border-start-0 border-top-0">
                            <h5 class="text-lg mb-0 text-center">Year-Wise Revenue & Royalty</h5>
                        </div>
                        <div class="card-body p-24 pt-10">
                            <ul class="nav button-tab nav-pills mb-16 justify-content-center" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-semibold text-primary-light radius-4 px-16 py-10 active"
                                            id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#ebooks_tab"
                                            type="button" role="tab" aria-controls="ebooks_tab" aria-selected="true"
                                            style="display: flex; align-items: center; gap: 6px;">
                                        <span class="iconify" data-icon="mdi:book-open-page-variant" data-width="20" data-height="20"></span>
                                        E-Books
                                    </button>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-semibold text-primary-light radius-4 px-16 py-10"
                                            id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#audiobooks_tab"
                                            type="button" role="tab" aria-controls="audiobooks_tab" aria-selected="false"
                                            style="display: flex; align-items: center; gap: 6px;">
                                        <span class="iconify" data-icon="mdi:headphones" data-width="20" data-height="20"></span>
                                        Audio Books
                                    </button>
                                </li>
                            </ul>
                            <script src="https://code.iconify.design/3/3.1.1/iconify.min.js"></script>
                            <style>
                                .table-bordered th, .table-bordered td {
                                    border: 1px solid #000 !important;
                                    padding: 10px;
                                    text-align: center;
                                }

                                .table thead th {
                                    background-color: #DCEEF2;
                                    color: green;
                                    text-align: center;
                                }

                                .table-container {
                                    max-height: 1200px;
                                    width: 100%;
                                    overflow-x: auto;
                                    overflow-y: auto;
                                    white-space: nowrap;
                                }
                            </style>
                            <div class="tab-content" id="pills-tabContent">
                                <?php 
                                $ebook_details = $channel_wise['ebook_details'] ?? []; 
                                $audiobook_details = $channel_wise['audiobook_details'] ?? [];

                                function format_currency($amount, $user_type) {
                                    return ($user_type == 4) ? indian_format($amount, 2) : '#';
                                }
                                ?>
                                <!-- EBOOK TAB -->
                                <div class="tab-pane fade show active" id="ebooks_tab" role="tabpanel" aria-labelledby="pills-home-tab">
                                    <div class="table-container">
                                        <h9 class="text-center">E-Books - Channel-Wise Revenue & Royalty</h9><br><br>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">FY</th>
                                                    <th rowspan="2">Total Revenue</th>
                                                    <th rowspan="2">Total Royalty</th>
                                                    <?php $ebook_channels = ['pustaka', 'amazon', 'overdrive', 'scribd', 'google', 'storytel', 'pratilipi', 'kobo']; ?>
                                                    <?php foreach ($ebook_channels as $channel): ?>
                                                        <th colspan="2"><?php echo ucfirst(htmlspecialchars($channel)); ?></th>
                                                    <?php endforeach; ?>
                                                </tr>
                                                <tr>
                                                    <?php foreach ($ebook_channels as $channel): ?>
                                                        <th>Revenue</th>
                                                        <th>Royalty</th>
                                                    <?php endforeach; ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $fy_list = !empty($ebook_details) ? array_unique(array_column($ebook_details, 'fy')) : [];
                                                sort($fy_list);

                                                foreach ($fy_list as $fy):
                                                    $data = array_filter($ebook_details, function($d) use ($fy) {
                                                        return $d['fy'] === $fy;
                                                    });
                                                    $data = reset($data) ?: [];
                                                ?>
                                                    <tr style="background-color: #f0f0f0;">
                                                        <td><?php echo htmlspecialchars($fy); ?></td>
                                                        <td><?php echo format_currency($data["full_total_revenue"] ?? 0, $_SESSION['user_type'] ?? null); ?></td>
                                                        <td><?php echo format_currency($data["full_total_royalty"] ?? 0, $_SESSION['user_type'] ?? null); ?></td>
                                                        <?php foreach ($ebook_channels as $channel): ?>
                                                            <td><?php echo format_currency($data["total_{$channel}_revenue"] ?? 0, $_SESSION['user_type'] ?? null); ?></td>
                                                            <td><?php echo format_currency($data["total_{$channel}_royalty"] ?? 0, $_SESSION['user_type'] ?? null); ?></td>
                                                        <?php endforeach; ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- AUDIOBOOK TAB -->
                                <div class="tab-pane fade" id="audiobooks_tab" role="tabpanel" aria-labelledby="pills-contact-tab">
                                    <div class="table-container">
                                        <h9 class="text-center">Audiobooks - Channel-Wise Revenue & Royalty</h9><br><br>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">FY</th>
                                                    <th rowspan="2">Total Revenue</th>
                                                    <th rowspan="2">Total Royalty</th>
                                                    <?php $audiobook_channels = ['pustaka', 'overdrive', 'google', 'storytel', 'audible', 'kukufm', 'youtube']; ?>
                                                    <?php foreach ($audiobook_channels as $channel): ?>
                                                        <th colspan="2"><?php echo ucfirst(htmlspecialchars($channel)); ?></th>
                                                    <?php endforeach; ?>
                                                </tr>
                                                <tr>
                                                    <?php foreach ($audiobook_channels as $channel): ?>
                                                        <th>Revenue</th>
                                                        <th>Royalty</th>
                                                    <?php endforeach; ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $fy_list = !empty($audiobook_details) ? array_unique(array_column($audiobook_details, 'fy')) : [];
                                                sort($fy_list);

                                                foreach ($fy_list as $fy):
                                                    $data = array_filter($audiobook_details, function($d) use ($fy) {
                                                        return $d['fy'] === $fy;
                                                    });
                                                    $data = reset($data) ?: [];
                                                    $total_revenue = 0;
                                                    $total_royalty = 0;

                                                    foreach ($audiobook_channels as $channel) {
                                                        $total_revenue += $data["total_{$channel}_revenue"] ?? 0;
                                                        $total_royalty += $data["total_{$channel}_royalty"] ?? 0;
                                                    }
                                                ?>
                                                    <tr style="background-color: #f0f0f0;">
                                                        <td><?php echo htmlspecialchars($fy); ?></td>
                                                        <td><?php echo format_currency($total_revenue, $_SESSION['user_type'] ?? null); ?></td>
                                                        <td><?php echo format_currency($total_royalty, $_SESSION['user_type'] ?? null); ?></td>
                                                        <?php foreach ($audiobook_channels as $channel): ?>
                                                            <td><?php echo format_currency($data["total_{$channel}_revenue"] ?? 0, $_SESSION['user_type'] ?? null); ?></td>
                                                            <td><?php echo format_currency($data["total_{$channel}_royalty"] ?? 0, $_SESSION['user_type'] ?? null); ?></td>
                                                        <?php endforeach; ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>            
            </div>
            <br><br>
            <?php
            $user_type = $_SESSION['user_type'] ?? 0;

            function getMonthName($monthNumber) {
                $months = [
                    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 
                    5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 
                    9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
                ];
                return $months[$monthNumber] ?? ''; 
            }
            ?>
           <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-center">Month Wise Royalty Settlement</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="author_table" class="table striped-table mb-0 table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Year</th>
                                    <th>Pustaka E-Books</th>
                                    <th>Pustaka Audiobooks</th>
                                    <th>Pustaka Paperback</th>
                                    <th>Pustaka Consolidated</th>  
                                    <th>Amazon</th>
                                    <th>Kobo</th>
                                    <th>Scribd</th>
                                    <th>Google Ebooks</th>
                                    <th>Google Audiobooks</th>
                                    <th>Overdrive Ebooks</th>
                                    <th>Overdrive Audiobooks</th>
                                    <th>Storytel Ebooks</th>
                                    <th>Storytel Audiobooks</th>
                                    <th>Pratilipi Ebooks</th>
                                    <th>Audible</th>
                                    <th>Kukufm Audiobooks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($author['details'])): ?>
                                    <?php foreach ($author['details'] as $row): ?>
                                        <tr>
                                            <td><?= htmlspecialchars(getMonthName($row['month'] ?? '')); ?></td>
                                            <td><?= htmlspecialchars($row['year'] ?? ''); ?></td>
                                            <td class="text-center"><?= $user_type == 4 ? htmlspecialchars($row['pustaka_ebooks'] ?: '-') : '#'; ?></td>
                                            <td class="text-center"><?= $user_type == 4 ? htmlspecialchars($row['pustaka_audiobooks'] ?: '-') : '#'; ?></td>
                                            <td class="text-center"><?= $user_type == 4 ? htmlspecialchars($row['pustaka_paperback'] ?: '-') : '#'; ?></td>
                                            <td class="text-center"><?= $user_type == 4 ? htmlspecialchars($row['pustaka_consolidated'] ?: '-') : '#'; ?></td>
                                            <td class="text-center"><?= $user_type == 4 ? htmlspecialchars($row['amazon'] ?: '-') : '#'; ?></td>
                                            <td class="text-center"><?= $user_type == 4 ? htmlspecialchars($row['kobo'] ?: '-') : '#'; ?></td>
                                            <td class="text-center"><?= $user_type == 4 ? htmlspecialchars($row['scribd'] ?: '-') : '#'; ?></td>
                                            <td class="text-center"><?= $user_type == 4 ? htmlspecialchars($row['google_ebooks'] ?: '-') : '#'; ?></td>
                                            <td class="text-center"><?= $user_type == 4 ? htmlspecialchars($row['google_audiobooks'] ?: '-') : '#'; ?></td>
                                            <td class="text-center"><?= $user_type == 4 ? htmlspecialchars($row['overdrive_ebooks'] ?: '-') : '#'; ?></td>
                                            <td class="text-center"><?= $user_type == 4 ? htmlspecialchars($row['overdrive_audiobooks'] ?: '-') : '#'; ?></td>
                                            <td class="text-center"><?= $user_type == 4 ? htmlspecialchars($row['storytel_ebooks'] ?: '-') : '#'; ?></td>
                                            <td class="text-center"><?= $user_type == 4 ? htmlspecialchars($row['storytel_audiobooks'] ?: '-') : '#'; ?></td>
                                            <td class="text-center"><?= $user_type == 4 ? htmlspecialchars($row['pratilipi_ebooks'] ?: '-') : '#'; ?></td>
                                            <td class="text-center"><?= $user_type == 4 ? htmlspecialchars($row['audible'] ?: '-') : '#'; ?></td>
                                            <td class="text-center"><?= $user_type == 4 ? htmlspecialchars($row['kukufm_audiobooks'] ?: '-') : '#'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="18" class="text-center">No records found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div><br><br>
            <?php 
            $user_type = $_SESSION['user_type'] ?? 0; 
            ?>
            <div class="container mt-3">
                <h6 class="text-center">Bookwise Royalty Details (E-Book)</h6>
                <table class="zero-config table table-hover mt-4">
                    <thead>
                        <tr>
                            <th>Book ID</th>
                            <th>Book Title</th>
                            <th>Total Royalty</th>
                            <th>Pustaka Royalty</th>
                            <th>Amazon Royalty</th>
                            <th>Google Royalty</th>
                            <th>Overdrive Royalty</th>
                            <th>Scribd Royalty</th>
                            <th>Storytel Royalty</th>
                            <th>Pratilipi Royalty</th>
                            <th>Kobo Royalty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($bookwise['ebook'])): ?>
                            <?php foreach ($bookwise['ebook'] as $ebook): ?>
                                <tr>
                                    <td><?= $ebook['book_id'] ?></td>
                                    <td><?= $ebook['book_title'] ?></td>
                                    <td><?= ($user_type == 4) ? indian_format($ebook['total'] ?? 0, 2) : '#' ?></td>
                                    <td><?= ($user_type == 4) ? indian_format($ebook['pustaka_royalty'] ?? 0, 2) : '#' ?></td>
                                    <td><?= ($user_type == 4) ? indian_format($ebook['amazon_royalty'] ?? 0, 2) : '#' ?></td>
                                    <td><?= ($user_type == 4) ? indian_format($ebook['google_royalty'] ?? 0, 2) : '#' ?></td>
                                    <td><?= ($user_type == 4) ? indian_format($ebook['overdrive_royalty'] ?? 0, 2) : '#' ?></td>
                                    <td><?= ($user_type == 4) ? indian_format($ebook['scribd_royalty'] ?? 0, 2) : '#' ?></td>
                                    <td><?= ($user_type == 4) ? indian_format($ebook['storytel_royalty'] ?? 0, 2) : '#' ?></td>
                                    <td><?= ($user_type == 4) ? indian_format($ebook['pratilipi_royalty'] ?? 0, 2) : '#' ?></td>
                                    <td><?= ($user_type == 4) ? indian_format($ebook['kobo_royalty'] ?? 0, 2) : '#' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center">No data available</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <br>
            <div class="container mt-3">
                <h6 class="text-center">Bookwise Royalty Details (Audio-Book)</h6>
                <table class="zero-config table table-hover mt-4">
                    <thead>
                        <tr>
                            <th>Book ID</th>
                            <th>Book Title</th>
                            <th>Total Royalty</th>
                            <th>Pustaka Royalty</th>
                            <th>Audible Royalty</th>
                            <th>Google Royalty</th>
                            <th>Overdrive Royalty</th>
                            <th>Storytel Royalty</th>
                            <th>Kukufm Royalty</th>
                            <th>Youtube Royalty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($bookwise['audiobook'])): ?>
                            <?php foreach ($bookwise['audiobook'] as $book): ?>
                                <tr>
                                    <td><?= $book['book_id'] ?></td>
                                    <td><?= $book['book_title'] ?></td>
                                    <td><?= ($user_type == 4) ? indian_format($book['total_royalty'] ?? 0, 2) : '#' ?></td>
                                    <td><?= ($user_type == 4) ? indian_format($book['pustaka_royalty'] ?? 0, 2) : '#' ?></td>
                                    <td><?= ($user_type == 4) ? indian_format($book['audible_royalty'] ?? 0, 2) : '#' ?></td>
                                    <td><?= ($user_type == 4) ? indian_format($book['google_royalty'] ?? 0, 2) : '#' ?></td>
                                    <td><?= ($user_type == 4) ? indian_format($book['overdrive_royalty'] ?? 0, 2) : '#' ?></td>
                                    <td><?= ($user_type == 4) ? indian_format($book['storytel_royalty'] ?? 0, 2) : '#' ?></td>
                                    <td><?= ($user_type == 4) ? indian_format($book['kukufm_royalty'] ?? 0, 2) : '#' ?></td>
                                    <td><?= ($user_type == 4) ? indian_format($book['youtube_royalty'] ?? 0, 2) : '#' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No data available</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <br>
            <div class="container mt-3">
                <h6 class="text-center">Bookwise Royalty Details (Paperback)</h6>
                <table class="zero-config table table-hover mt-3">
                    <thead class="thead-dark">
                        <tr>
                            <th>Book ID</th>
                            <th>Book Title</th>
                            <th>Pustaka Royalty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($bookwise['paperback'])): ?>
                            <?php foreach ($bookwise['paperback'] as $books): ?>
                                <tr>
                                    <td><?= $books['book_id'] ?></td>
                                    <td><?= $books['book_title'] ?></td>
                                    <td><?= ($user_type == 4) ? indian_format($books['pustaka_royalty'] ?? 0, 2) : '#' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">No data available</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <br><br>
           <div class="card h-100 p-0">
                <div class="card-header border-bottom bg-base py-16 px-24">
                    <h6 class="text-lg fw-semibold mb-0">Year-Wise Royalty Settlement</h6>
                </div>
                <div class="card-body p-24">
                    <canvas id="royaltyChart" class="w-100" style="height: 350px;"></canvas>
                </div>
            </div>
            <?= $this->section('script'); ?>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        let chartData = <?php echo json_encode($author['chart']); ?>;
                        let userType = <?php echo json_encode($user_type); ?>;

                        if (!chartData || !Array.isArray(chartData) || chartData.length === 0) {
                            console.warn("No chart data found.");
                            return;
                        }

                        let years = chartData.map(item => item.fy);
                        let settlements = chartData.map(item => item.total_settlement);

                        const ctx = document.getElementById("royaltyChart").getContext("2d");

                        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, "rgba(54, 162, 235, 0.5)");
                        gradient.addColorStop(1, "rgba(54, 162, 235, 0)");

                        new Chart(ctx, {
                            type: "line",
                            data: {
                                labels: years,
                                datasets: [{
                                    label: "Total Settlement",
                                    data: settlements,
                                    fill: true,
                                    backgroundColor: gradient,
                                    borderColor: "rgba(54, 162, 235, 1)",
                                    borderWidth: 2,
                                    tension: 0.4,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                }],
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    x: { grid: { display: false } },
                                    y: { beginAtZero: true, grid: { color: "rgba(200, 200, 200, 0.2)" } },
                                },
                                plugins: {
                                    tooltip: {
                                        callbacks: {
                                            label: function (context) {
                                                return userType == 4 ? context.raw : '#';
                                            },
                                        },
                                    },
                                },
                            },
                        });
                    });
                </script>
            <?= $this->endSection(); ?>
            <br><br>
            <div class="card p-0 overflow-hidden position-relative radius-12 h-100">
                <div class="card-header py-16 px-24 bg-base border border-end-0 border-start-0 border-top-0">
                    <h6 class="text-lg mb-0">Month-Wise Chart</h6>
                </div>
                <div class="card-body p-24 pt-10">
                    <ul class="nav button-tab nav-pills mb-16" id="channelTabs" role="tablist">
                        <?php 
                        $channels = ['pustaka', 'amazon', 'overdrive', 'scribd', 'google', 'storytel', 'pratilipi', 'audible', 'kobo', 'kukufm', 'youtube'];
                        foreach ($channels as $index => $channel): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center gap-2 fw-semibold text-primary-light radius-4 px-16 py-10 <?php echo ($index === 0) ? 'active' : ''; ?>" 
                                        id="<?php echo htmlspecialchars($channel); ?>-tab" 
                                        data-bs-toggle="pill" 
                                        data-bs-target="#<?php echo htmlspecialchars($channel); ?>" 
                                        type="button" 
                                        role="tab" 
                                        aria-controls="<?php echo htmlspecialchars($channel); ?>" 
                                        aria-selected="<?php echo ($index === 0) ? 'true' : 'false'; ?>">
                                    <span class="line-height-1"><?php echo ucfirst(htmlspecialchars($channel)); ?></span>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="tab-content mt-3" id="channelTabsContent">
                        <?php 
                        $user_type = $_SESSION['user_type'] ?? null; 
                        foreach ($channels as $index => $channel): ?>
                            <div class="tab-pane fade <?php echo ($index === 0) ? 'show active' : ''; ?>" 
                                id="<?php echo htmlspecialchars($channel); ?>" 
                                role="tabpanel" 
                                aria-labelledby="<?php echo htmlspecialchars($channel); ?>-tab">
                                
                                <div class="text-center my-4">
                                    <p class="text-secondary-light">Revenue and Royalty for <?php echo ucfirst($channel); ?></p>
                                </div>
                                
                                <div class="chart-container mt-4">
                                    <canvas id="<?php echo htmlspecialchars($channel); ?>Chart"></canvas>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <!-- Scripts -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
            <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
            <script>
                let chartData = <?php echo json_encode($channel_chart); ?>;
                let userType = <?php echo json_encode($user_type); ?>; 
                let charts = {}; 

                function loadChart(channelName) {
                    if (charts[channelName]) return; 

                    let ctx = document.getElementById(channelName + "Chart").getContext('2d');
                    let months = [], revenues = [], royalties = [];

                    if (chartData[channelName] && chartData[channelName].length > 0) {
                        chartData[channelName].forEach(row => {
                            let period = row.year + '-' + (row.month < 10 ? '0' + row.month : row.month);
                            months.push(period);
                            revenues.push(parseFloat(row[channelName + "_revenue"] || 0));
                            royalties.push(parseFloat(row[channelName + "_royalty"] || 0));
                        });
                    } else {
                        months = ["No Data"];
                        revenues = [0];
                        royalties = [0];
                    }

                    charts[channelName] = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: months,
                            datasets: [
                                {
                                    label: "Revenue",
                                    borderColor: "green",
                                    backgroundColor: "transparent",
                                    fill: false,
                                    data: revenues,
                                    tension: 0.1,
                                },
                                {
                                    label: "Royalty",
                                    borderColor: "blue",
                                    backgroundColor: "transparent",
                                    fill: false,
                                    data: royalties,
                                    tension: 0.1,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: { beginAtZero: true }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let value = context.raw;
                                            return userType == 4 ? value : '#';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                document.addEventListener("DOMContentLoaded", function () {
                    let firstChannel = "<?php echo $channels[0]; ?>";
                    loadChart(firstChannel);

                    document.querySelectorAll('.nav-link').forEach(tab => {
                        tab.addEventListener('click', function () {
                            let channelName = this.getAttribute('id').replace('-tab', '');
                            loadChart(channelName);
                        });
                    });
                });
            </script>
        </div> 
    </div>
</div>
<?= $this->endSection(); ?>
<?= $this->section('script'); ?>
<script>
    // ======================== Upload Image Start =====================
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $("#imagePreview").css("background-image", "url(" + e.target.result + ")");
                $("#imagePreview").hide();
                $("#imagePreview").fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#imageUpload").change(function () {
        readURL(this);
    });
    // ======================== Upload Image End =====================

    // ================== Password Show Hide Js Start ================
    function initializePasswordToggle(toggleSelector) {
        $(toggleSelector).on("click", function () {
            $(this).toggleClass("ri-eye-off-line");
            var input = $($(this).attr("data-toggle"));
            if (input.attr("type") === "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    }
    // Call the function
    initializePasswordToggle(".toggle-password");
    // ========================= Password Show Hide Js End ===========================
</script>
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
<?= $this->endSection(); ?>
