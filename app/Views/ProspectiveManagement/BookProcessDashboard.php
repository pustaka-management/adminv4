<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid px-4">
    <!-- Gradient Header with Plan Name -->
    <div class="p-4 mb-4 rounded-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div>
                    <span class="small text-uppercase">Current Plan</span>
                    <h6 class="fw-bold mb-0"><?= esc($book['plan_name'] ?? '') ?></h6>
                </div>
            </div>
            <div class="text-end">
    <span class="small text-uppercase"></span>
                    <h6 class="fw-bold mb-0"><?= esc($book['title'] ?? '') ?></h6>
    </span>
</div>
        </div>
    </div>

    <form method="post" action="<?= base_url('prospectivemanagement/savePlan/'.$book['id']) ?>">
        <div class="accordion" id="planAccordion">
            <!-- Production -->
            <?php if(!empty($plan['Production'])): ?>
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
                <h2 class="accordion-header">
                    <button class="accordion-button bg-white fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProduction">
                        <i class="bi bi-film text-primary me-2"></i> Production
                    </button>
                </h2>
                <div id="collapseProduction" class="accordion-collapse collapse show" data-bs-parent="#planAccordion">
                    <div class="accordion-body bg-white">
                        <div class="row g-3">
                            <?php foreach($plan['Production'] as $k=>$v): ?>
                            <div class="col-md-3 col-sm-6">
                                <div class="bg-light p-3 rounded-3">
                                    <span class="text-muted small d-block"><?= ucfirst($k) ?></span>
                                    <span class="badge <?= $v ? 'bg-success' : 'bg-secondary' ?> mt-2 px-3 py-2">
                                        <i class="bi <?= $v ? 'bi-check-circle' : 'bi-x-circle' ?> me-1"></i>
                                        <?= $v ? 'Yes' : 'No' ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Ownership -->
            <?php if(!empty($planTemplate['ownership_support'])): ?>
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
                <h2 class="accordion-header">
                    <button class="accordion-button bg-white fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOwnership">
                        <i class="bi bi-file-earmark-text text-purple me-2" style="color: #5f2c82;"></i> Ownership Support
                    </button>
                </h2>
                <div id="collapseOwnership" class="accordion-collapse collapse" data-bs-parent="#planAccordion">
                    <div class="accordion-body bg-white">
                        <div class="row g-3">
                            <?php foreach($planTemplate['ownership_support'] as $k=>$v): 
                                $val = $planStatus['ownership_support'][$k] ?? $v ?? ''; ?>
                            <div class="col-md-4">
                                <label class="form-label text-muted small">
                                    <i class="bi bi-pencil-square me-1"></i> <?= ucfirst($k) ?>
                                </label>
                                <input class="form-control bg-light border-0" 
                                       name="ownership_support[<?= $k ?>]" 
                                       value="<?= esc($val) ?>"
                                       placeholder="Enter <?= ucfirst($k) ?>">
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Distribution -->
            <?php if(!empty($planTemplate['distribution'])): ?>
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
                <h2 class="accordion-header">
                    <button class="accordion-button bg-white fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDistribution">
                        <i class="bi bi-truck text-teal me-2" style="color: #49a09d;"></i> Distribution
                    </button>
                </h2>
                <div id="collapseDistribution" class="accordion-collapse collapse" data-bs-parent="#planAccordion">
                    <div class="accordion-body bg-white">
                        <?php foreach($planTemplate['distribution'] as $type=>$channels): ?>
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3">
                                <i class="bi bi-arrow-right-circle me-1" style="color: #49a09d;"></i>
                                <?= ucfirst($type) ?>
                            </h6>
                            <div class="row g-3">
                                <?php foreach($channels as $c=>$v): 
                                    $saved = $planStatus['distribution'][$type][$c] ?? $v ?? ''; ?>
                                <div class="col-md-4">
                                    <label class="form-label text-muted small">
                                        <i class="bi bi-link me-1"></i> <?= ucfirst($c) ?>
                                    </label>
                                    <input class="form-control bg-light border-0" 
                                           name="distribution[<?= $type ?>][<?= $c ?>]" 
                                           value="<?= esc($saved) ?>"
                                           placeholder="Enter <?= ucfirst($c) ?>">
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Complementary -->
            <?php if(!empty($planTemplate['complementary'])): ?>
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
                <h2 class="accordion-header">
                    <button class="accordion-button bg-white fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseComplementary">
                        <i class="bi bi-gift text-pink me-2" style="color: #f5576c;"></i> Complementary
                    </button>
                </h2>
                <div id="collapseComplementary" class="accordion-collapse collapse" data-bs-parent="#planAccordion">
                    <div class="accordion-body bg-white">
                        <div class="row g-3">
                            <?php foreach($planTemplate['complementary'] as $k=>$v): 
                                $saved = $planStatus['complementary'][$k] ?? $v ?? ''; ?>
                            <div class="col-md-4">
                                <label class="form-label text-muted small">
                                    <i class="bi bi-plus-circle me-1"></i> <?= ucfirst($k) ?>
                                </label>
                                <input class="form-control bg-light border-0" 
                                       name="complementary[<?= $k ?>]" 
                                       value="<?= esc($saved) ?>"
                                       placeholder="Enter <?= ucfirst($k) ?>">
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Additional -->
            <div class="accordion-item border-0 rounded-4 shadow-sm overflow-hidden">
                <h2 class="accordion-header">
                    <button class="accordion-button bg-white fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdditional">
                        <i class="bi bi-gear text-pink me-2" style="color: #C850C0;"></i> Additional Information
                    </button>
                </h2>
                <div id="collapseAdditional" class="accordion-collapse collapse" data-bs-parent="#planAccordion">
                    <div class="accordion-body bg-white">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="bg-light p-3 rounded-3">
                                    <span class="text-muted small d-block">
                                        <i class="bi bi-building me-1"></i> Ownership
                                    </span>
                                    <span class="fw-medium d-block mt-2 <?= !empty($plan['isownership']) ? 'text-success' : 'text-secondary' ?>">
                                        <i class="bi <?= !empty($plan['isownership']) ? 'bi-check-circle-fill' : 'bi-x-circle-fill' ?> me-1"></i>
                                        <?= !empty($plan['isownership']) ? 'Yes' : 'No' ?>
                                    </span>
                                </div>
                            </div>

                            <?php if(!empty($plan['isdistribution'])): ?>
                            <div class="col-12">
                                <div class="bg-light p-3 rounded-3">
                                    <span class="text-muted small d-block mb-2">
                                        <i class="bi bi-diagram-3 me-1"></i> Distribution Channels
                                    </span>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php foreach($plan['isdistribution'] as $k=>$v): ?>
                                        <span class="badge <?= $v ? 'bg-success' : 'bg-secondary' ?> px-3 py-2">
                                            <i class="bi bi-<?= $k == 'digital' ? 'laptop' : ($k == 'physical' ? 'box' : 'share') ?> me-1"></i>
                                            <?= ucfirst($k) ?>
                                        </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="col-md-3">
                                <div class="bg-light p-3 rounded-3">
                                    <span class="text-muted small d-block">
                                        <i class="bi bi-gift me-1"></i> Complementary
                                    </span>
                                    <span class="fw-medium d-block mt-2 <?= !empty($plan['iscomplementary']) ? 'text-success' : 'text-secondary' ?>">
                                        <i class="bi <?= !empty($plan['iscomplementary']) ? 'bi-check-circle-fill' : 'bi-x-circle-fill' ?> me-1"></i>
                                        <?= !empty($plan['iscomplementary']) ? 'Yes' : 'No' ?>
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="bg-light p-3 rounded-3">
                                    <span class="text-muted small d-block">
                                        <i class="bi bi-megaphone me-1"></i> Promotions
                                    </span>
                                    <span class="fw-medium d-block mt-2 <?= !empty($plan['ispromotions']) ? 'text-success' : 'text-secondary' ?>">
                                        <i class="bi <?= !empty($plan['ispromotions']) ? 'bi-check-circle-fill' : 'bi-x-circle-fill' ?> me-1"></i>
                                        <?= !empty($plan['ispromotions']) ? 'Yes' : 'No' ?>
                                    </span>
                                </div>
                            </div>

                            <?php if(isset($plan['add_on'])): ?>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">
                                    <i class="bi bi-plus-square me-1"></i> Add On
                                </label>
                                <input class="form-control bg-light border-0" 
                                       name="add_on" 
                                       value="<?= esc($planStatus['plan_detail']['add_on'] ?? $plan['add_on'] ?? '') ?>"
                                       placeholder="Enter add on details">
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="d-flex justify-content-end mt-4 mb-4">
            <button type="submit" class="btn btn-lg px-5 py-3 rounded-3 border-0 shadow-lg"
                    style="background: linear-gradient(145deg, #667eea, #764ba2);">
                <i class="bi bi-check-circle me-2"></i> Save Plan
            </button>
        </div>
    </form>
</div>

<?= $this->endSection(); ?>