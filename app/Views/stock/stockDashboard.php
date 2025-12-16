<?= $this->extend('layout/layout1'); ?>
<?= $this->section('script'); ?>
  <script src="<?= base_url('assets/js/homeFiveChart.js') ?>"></script>
<?= $this->endSection(); ?>
<?= $this->section('content'); ?>

<div class="row gy-4">
    <div class="d-flex gap-4 justify-content-end">
        <a href="<?= base_url('stock/addstock'); ?>">
            <span class="badge text-sm fw-semibold bg-lilac-600 px-20 py-9 radius-4 text-white">
                Add Stock
            </span>
        </a>
        <a href="<?= base_url('stock/otherdistributionbooksstatus'); ?>">
            <span class="badge text-sm fw-semibold bg-info-600 px-20 py-9 radius-4 text-white">Other Distribution</span>
        </a>
    </div>

    <!-- Stock In Hand -->
    <div class="col-xxl-3 col-sm-6">
        <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-3">
            <div class="card-body p-0">
                <div class="d-flex align-items-center">
                    <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-20">
                        <span class="w-40-px h-40-px bg-primary-600 text-white d-flex justify-content-center align-items-center radius-8">
                            <iconify-icon icon="mdi:book-open-variant"></iconify-icon>
                        </span>
                    </div>
                    <div>
                        <span class="mb-2 fw-medium text-secondary-light text-md">Stock</span>
                        <h6 class="fw-semibold my-1"><?= esc($details['stock_in_hand']->total_books ?? 0) ?></h6>
                        <p class="text-sm mb-1">
                            <!-- Stock In Hand: <span class="fw-semibold text-success"><?= esc($details['stock_in_hand']->total_stock ?? 0) ?></span><br> -->
                            No of Titles: <span class="fw-semibold text-primary"><?= esc($details['stock_in_hand']->total_titles ?? 0) ?></span>
                        </p>
                        <a href="getstockdetails" class="badge text-sm fw-semibold bg-neutral-800 px-20 py-9 radius-4 text-base">View</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Out of Stock -->
    <div class="col-xxl-3 col-sm-6">
        <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-2">
            <div class="card-body p-0">
                <div class="d-flex align-items-center">
                    <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-20">
                        <span class="w-40-px h-40-px bg-purple text-white d-flex justify-content-center align-items-center radius-8">
                            <iconify-icon icon="mdi:cart-off"></iconify-icon>
                        </span>
                    </div>
                    <div>
                        <span class="mb-2 fw-medium text-secondary-light text-md">Out of Stock</span>
                        <h6 class="fw-semibold my-1"><?= esc($details['out_of_stock']->out_of_stocks_titles ?? 0) ?></h6>
                        <p class="text-sm mb-1">
                            No of Titles: <span class="fw-semibold text-primary"><?= esc($details['out_of_stock']->out_of_stocks_titles ?? 0) ?></span>
                        </p>
                        <a href="outofstockdetails" class="badge text-sm fw-semibold bg-neutral-800 px-20 py-9 radius-4 text-base">View</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lost Books -->
    <div class="col-xxl-3 col-sm-6">
        <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-5">
            <div class="card-body p-0">
                <div class="d-flex align-items-center">
                    <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-20">
                        <span class="w-40-px h-40-px bg-red text-white d-flex justify-content-center align-items-center radius-8">
                            <iconify-icon icon="mdi:book-alert"></iconify-icon>
                        </span>
                    </div>
                    <div>
                        <span class="mb-2 fw-medium text-secondary-light text-md">Lost/Excess Books</span>
                        <h6 class="fw-semibold my-1"><?= esc($details['lost_books']->total_lost_books ?? 0) ?>/<?= esc($details['excess_books']->total_excess_books ?? 0) ?></h6>
                        <p class="text-sm mb-1">
                            No of Titles: <span class="fw-semibold text-primary"><?= esc($details['lost_books']->total_lost_titles ?? 0) ?>/<?= esc($details['excess_books']->total_excess_titles ?? 0) ?></span>
                        </p>
                        <a href="loststockdetails" class="badge text-sm fw-semibold bg-neutral-800 px-20 py-9 radius-4 text-base">View</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Outside Stocks -->
    <div class="col-xxl-3 col-sm-6">
        <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-4">
            <div class="card-body p-0">
                <div class="d-flex align-items-center">
                    <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-20">
                        <span class="w-40-px h-40-px bg-success-main text-white d-flex justify-content-center align-items-center radius-8">
                            <iconify-icon icon="mdi:truck-cargo-container"></iconify-icon>
                        </span>
                    </div>
                    <div>
                        <span class="mb-2 fw-medium text-secondary-light text-md">Outside Stocks</span>
                        <?php
                        $outsideStocks = $details['outside_stocks'] ?? (object)['total_books' => 0, 'total_title' => 0];

                        $totalBooks = $outsideStocks->total_books ?? 0;
                        $totalTitles = $outsideStocks->total_title ?? 0;
                        ?>
                        <h6 class="fw-semibold my-1"><?= esc($totalBooks) ?></h6>
                        No of Titles:<span class="fw-semibold text-primary"><?= esc($totalTitles) ?></span>
                        <br>
                        <a href="outsidestockdetails" class="badge text-sm fw-semibold bg-neutral-800 px-20 py-9 radius-4 text-base">View</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br><br>
    <div class="col-sm-12"> 
        <div class="card p-0 overflow-hidden position-relative radius-12">
            <div class="card-header py-16 px-24 bg-base border border-end-0 border-start-0 border-top-0">
                <h6 class="text-lg mb-0">Stock Details</h6>
            </div>
            <div class="card-body py-24 px-16 multiple-carousel dots-style-circle">
                
                <a href="<?= base_url('stock/paperbackledgerbooks'); ?>" target="_blank">
                    <div class="mx-8 mb-24 text-center">
                        <p class="form-control text-center mb-0" 
                        style="background-color: rgba(0, 123, 255, 0.2); border: 1px solid rgba(0, 123, 255, 0.4); border-radius: 8px; height: 100px; line-height: 60px;">
                        Paperback Ledger
                        </p>
                    </div>
                </a>

                <a href="<?= base_url('paperback/initiateprintbooksdashboard'); ?>" target="_blank">
                    <div class="mx-8 mb-24 text-center">
                        <p class="form-control text-center mb-0" 
                        style="background-color: rgba(255, 193, 7, 0.2); border: 1px solid rgba(255, 193, 7, 0.4); border-radius: 8px; height: 100px; line-height: 60px;">
                        Initiate Print
                        </p>
                    </div>
                </a>

                <a href="<?= base_url('paperback/paperbackprintstatus'); ?>" target="_blank">
                    <div class="mx-8 mb-24 text-center">
                        <p class="form-control text-center mb-0" 
                        style="background-color: rgba(40, 167, 69, 0.2); border: 1px solid rgba(40, 167, 69, 0.4); border-radius: 8px; height: 100px; line-height: 60px;">
                        Status Update
                        </p>
                    </div>
                </a>

                <a href="<?= base_url('stock/getpaperbackstock'); ?>" target="_blank">
                    <div class="mx-8 mb-24 text-center">
                        <p class="form-control text-center mb-0" 
                        style="background-color: rgba(255, 159, 67, 0.2); border: 1px solid rgba(255, 159, 67, 0.4); border-radius: 8px; height: 100px; line-height: 60px;">
                        View & Modify
                        </p>
                    </div>
                </a>
                <a href="<?= base_url('stock/lostexcessbooksstatus'); ?>" target="_blank">
                    <div class="mx-8 mb-24 text-center">
                        <p class="form-control text-center mb-0" 
                        style="background-color: rgba(220, 53, 69, 0.2); border: 1px solid rgba(220, 53, 69, 0.4); border-radius: 8px; height: 100px; line-height: 60px;">
                        Excess/Lost Books
                        </p>
                    </div>
                </a>
                <div class="mx-8 mb-24 text-center">
                    <p class="form-control text-center mb-0" 
                    style="background-color: rgba(157, 249, 108, 0.2); border: 1px solid rgba(171, 246, 51, 0.4); border-radius: 8px; height: 100px; line-height: 60px;">
                    Discrepancy Report
                    </p>
                </div>
                <a href="<?= base_url('stock/bookfairdashboard'); ?>" target="_blank">
                    <div class="mx-8 mb-24 text-center">
                        <p class="form-control text-center mb-0" 
                        style="background-color: rgba(239, 167, 247, 0.2); border: 1px solid rgba(228, 64, 239, 0.4); border-radius: 8px; height: 100px; line-height: 60px;">
                        BookFair Sales
                        </p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
<?= $this->extend('layout/layout1'); ?>

<?= $this->section('script'); ?>
    <script>
        var rtlDirection = $("html").attr("dir") === "rtl";
    
        // ================================ Default Slider Start ================================ 
        $(".default-carousel").slick({
            infinite: true,
            slidesToShow: 1,
            slidesToScroll: 1, 
            arrows: false, 
            dots: false,
            infinite: true,
            autoplay: false,
            autoplaySpeed: 2000,
            speed: 600,
            rtl: rtlDirection
        });

        // Arrow Carousel
        $(".arrow-carousel").slick({
            infinite: true,
            slidesToShow: 1,
            slidesToScroll: 1, 
            arrows: true, 
            dots: false,
            infinite: true,
            autoplay: false,
            autoplaySpeed: 2000,
            speed: 600,
            prevArrow: "<button type=\'button\' class=\'slick-prev\'><iconify-icon icon=\'ic:outline-keyboard-arrow-left\' class=\'menu-icon\'></iconify-icon></button>",
            nextArrow: "<button type=\'button\' class=\'slick-next\'><iconify-icon icon=\'ic:outline-keyboard-arrow-right\' class=\'menu-icon\'></iconify-icon></button>",
            rtl: rtlDirection
        });

        // Pagination Carousel
        $(".pagination-carousel").slick({
            infinite: true,
            slidesToShow: 1,
            slidesToScroll: 1, 
            arrows: false, 
            dots: true,
            infinite: true,
            autoplay: false,
            autoplaySpeed: 2000,
            speed: 600,
            prevArrow: "<button type=\'button\' class=\'slick-prev\'><iconify-icon icon=\'ic:outline-keyboard-arrow-left\' class=\'menu-icon\'></iconify-icon></button>",
            nextArrow: "<button type=\'button\' class=\'slick-next\'><iconify-icon icon=\'ic:outline-keyboard-arrow-right\' class=\'menu-icon\'></iconify-icon></button>",
            rtl: rtlDirection
        });

        // Multiple Carousel
        $(".multiple-carousel").slick({
            infinite: true,
            slidesToShow: 4,
            slidesToScroll: 1, 
            arrows: false, 
            dots: true,
            infinite: true,
            autoplay: false,
            autoplaySpeed: 2000,
            speed: 600,
            gap: 24,
            prevArrow: "<button type=\'button\' class=\'slick-prev\'><iconify-icon icon=\'ic:outline-keyboard-arrow-left\' class=\'menu-icon\'></iconify-icon></button>",
            nextArrow: "<button type=\'button\' class=\'slick-next\'><iconify-icon icon=\'ic:outline-keyboard-arrow-right\' class=\'menu-icon\'></iconify-icon></button>",
            rtl: rtlDirection,
            responsive: [
                {
                    breakpoint: 1199,
                    settings: {
                        slidesToShow: 3,
                    }
                },
                {
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 2,
                    }
                },
                {
                    breakpoint: 575,
                    settings: {
                        slidesToShow: 1,
                    }
                },
            ]
        });

        // Carousel with Progress Bar
        jQuery(document).ready(function($) {
            var sliderTimer = 5000;
            var beforeEnd = 500;
            var $imageSlider = $(".progress-carousel");
            $imageSlider.slick({
                autoplay: true,
                autoplaySpeed: sliderTimer,
                speed: 1000,
                arrows: false,
                dots: false,
                adaptiveHeight: true,
                pauseOnFocus: false,
                pauseOnHover: false,
                rtl: rtlDirection
            });

            function progressBar(){
                $(".slider-progress").find("span").removeAttr("style");
                $(".slider-progress").find("span").removeClass("active");
                setTimeout(function(){
                    $(".slider-progress").find("span").css("transition-duration", (sliderTimer/1000)+"s").addClass("active");
                }, 100);
            }
            progressBar();
            $imageSlider.on("beforeChange", function(e, slick) {
                progressBar();
            });
            $imageSlider.on("afterChange", function(e, slick, nextSlide) {
                titleAnim(nextSlide);
            });

            // Title Animation JS
            function titleAnim(ele){
                $imageSlider.find(".slick-current").find("h1").addClass("show");
                setTimeout(function(){
                    $imageSlider.find(".slick-current").find("h1").removeClass("show");
                }, sliderTimer - beforeEnd);
            }
            titleAnim();
        });

        // ================================ Default Slider End ================================ 
    </script>
<?= $this->endSection(); ?>