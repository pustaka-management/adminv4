<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid py-4">

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded-2 p-2 me-2">
                        <iconify-icon icon="mdi:chart-box-outline" class="fs-5 text-primary"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Prospective Management</h6>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('prospectivemanagement/addprospect'); ?>" class="btn btn-info btn-sm">
                        Add Prospect
                    </a>
                    <a href="<?= base_url('prospectivemanagement/addbook'); ?>" class="btn btn-success btn-sm">
                        Add Book
                    </a>
                </div>
            </div>
        </div>
    </div><br>

    <!-- Status Overview -->
<div class="row mb-4 g-3">

<?php 
$statusCards = [
    ['label' => 'In Progress', 'count' => $prospectCounts['inProgressCount'] ?? 0, 'color' => 'primary', 'icon' => 'mdi:progress-clock', 'link' => 'inprogress'],
    ['label' => 'Closed', 'count' => $prospectCounts['closedCount'] ?? 0, 'color' => 'success', 'icon' => 'mdi:check-circle-outline', 'link' => 'closed'],
    ['label' => 'Denied', 'count' => $prospectCounts['deniedCount'] ?? 0, 'color' => 'danger', 'icon' => 'mdi:close-circle-outline', 'link' => 'denied'],
    ['label' => 'Hold', 'count' => $prospectCounts['holdCount'] ?? 0, 'color' => 'warning', 'icon' => 'mdi:pause-circle-outline', 'link' => 'hold'],
];

foreach($statusCards as $card): ?>
    
    <div class="col-xl-3 col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-<?= $card['color']; ?> bg-opacity-10 rounded-3 p-3 me-3">
                        <iconify-icon icon="<?= $card['icon']; ?>" class="fs-3 text-<?= $card['color']; ?>"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="mb-1"><?= $card['label']; ?></h6>
                        <h6 class="fw-bold mb-0"><?= $card['count']; ?></h6>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="<?= base_url('prospectivemanagement/' . $card['link']); ?>" 
                   class="btn btn-outline-<?= $card['color']; ?> btn-sm w-100">
                    View Details
                </a>
            </div>
        </div>
    </div>

<?php endforeach; ?>
</div>
<br>

    <!-- Plans & Payments -->
    <div class="row g-4">

        <!-- Plans Purchased -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 pb-0 pt-4 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-2 me-3">
                            <iconify-icon icon="mdi:gift-outline" class="fs-4 text-warning"></iconify-icon>
                        </div>
                        <h6 class="fw-bold mb-0">Plans Purchased</h6>
                    </div>
                    <a href="<?= base_url('prospectivemanagement/planssummary'); ?>" class="btn btn-sm btn-outline-primary d-flex align-items-center">
                        <iconify-icon icon="mdi:eye-outline" class="me-2 fs-5"></iconify-icon>
                        Details
                    </a>
                </div>

                <div class="card-body">
                    <div class="row g-2 justify-content-center">
                        <?php 
                        $plans = [
                            ['name' => 'Silver', 'icon' => 'mdi:medal-outline', 'bg' => 'bg-light', 'text' => 'text-secondary'],
                            ['name' => 'Gold', 'icon' => 'mdi:crown-outline', 'bg' => 'bg-warning bg-opacity-25', 'text' => 'text-warning'],
                            ['name' => 'Platinum', 'icon' => 'mdi:diamond-stone', 'bg' => 'bg-secondary bg-opacity-25', 'text' => 'text-secondary'],
                            ['name' => 'Rhodium', 'icon' => 'mdi:crystal-ball', 'bg' => 'bg-info bg-opacity-25', 'text' => 'text-info'],
                            ['name' => 'Silver++', 'icon' => 'mdi:medal', 'bg' => 'bg-light', 'text' => 'text-warning'],
                            ['name' => 'Pearl', 'icon' => 'mdi:brightness-1', 'bg' => 'bg-success bg-opacity-25', 'text' => 'text-white'],
                            ['name' => 'Sapphire', 'icon' => 'mdi:diamond-stone', 'bg' => 'bg-primary bg-opacity-25', 'text' => 'text-primary'],
                        ];
                        foreach ($plans as $plan): ?>
                        <div class="col-4 col-md-3 col-lg-3">
                            <div class="card <?= $plan['bg']; ?> border-0 text-center py-2 px-1 h-100 rounded-3 shadow-sm plan-card">
                                <div class="d-flex justify-content-center align-items-center mb-1">
                                    <iconify-icon icon="<?= $plan['icon']; ?>" class="fs-6 <?= $plan['text']; ?>"></iconify-icon>
                                </div>
                                <h6 class="fw-semibold mb-0 small"><?= $plan['name']; ?></h6>
                                <div class="fw-bold fs-7"><?= $planCounts[$plan['name']] ?? 0; ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-0 pb-0 pt-4 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-3 p-2 me-3">
                            <iconify-icon icon="mdi:currency-inr" class="fs-4 text-info dark:text-blue-300"></iconify-icon>
                        </div>
                        <h6 class="fw-bold mb-0">Payments Summary</h6>
                    </div>
                    <a href="<?= base_url('prospectivemanagement/paymentdetails'); ?>" 
                    class="btn btn-sm btn-outline-primary d-flex align-items-center">
                        <iconify-icon icon="mdi:eye-outline" class="fs-5"></iconify-icon>Details
                    </a>
                </div><br>
            <div>
            <!-- Total Revenue (Count) -->
            <div class="card mb-4 border-0 shadow-sm p-4 text-center bg-success bg-opacity-10 dark:bg-transparent">
                <div class="mb-0 text-secondary dark:text-gray-300">Total Payments</div>
                <div class="fw-bold fs-2 text-dark dark:text-white">
                    <?= $paymentSummary['totalCount'] ?? 0; ?>
                </div>
            </div>

            <!-- Paid and Partial Counts -->
            <div class="row g-3">
                <?php 
                $statuses = [
                    [
                        'label' => 'Paid', 
                        'light_class' => 'bg-info bg-opacity-10',
                        'dark_class' => 'bg-warning',
                        'text_class' => 'text-info dark:text-blue-300',
                        'value' => $paymentSummary['paidCount'] ?? 0
                    ],
                    [
                        'label' => 'Partial', 
                        'light_class' => 'bg-warning bg-opacity-10',
                        'dark_class' => 'bg-danger',
                        'text_class' => 'text-warning dark:text-yellow-400',
                        'value' => $paymentSummary['partialCount'] ?? 0
                    ],
                ];
                foreach ($statuses as $status): ?>
                        <div class="col-6">
                            <div class="card border-0 shadow-sm h-100 text-center p-3 rounded-3 
                                        <?= $status['light_class']; ?> <?= $status['dark_class']; ?>">
                                <div class="fw-semibold small mb-1 <?= $status['text_class']; ?>">
                                    <?= esc($status['label']); ?>
                                </div>
                                <div class="fw-bold fs-4 text-dark dark:text-white">
                                    <?= esc($status['value']); ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

    <?= $this->section('script'); ?>
        <script>
        document.querySelectorAll('.plan-card').forEach(card => {
            card.addEventListener('mouseover', () => card.style.transform = 'scale(1.05)');
            card.addEventListener('mouseout', () => card.style.transform = 'scale(1)');
            card.style.transition = 'transform 0.2s ease-in-out';
        });
        </script>
    <?= $this->endSection(); ?>
