<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <?php 
    // Check if book data exists
    if (empty($book)): ?>
        <div class="alert alert-danger">
            <h4>Book Not Found</h4>
            <p>The requested book could not be found. Please check the book ID and try again.</p>
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                &larr; Go Back
            </a>
        </div>
    <?php else: ?>
    
    <!-- Back Button -->
    <div class="mb-3">
        <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
            &larr; Back
        </a>
    </div>

    <div class="page-header">
        <div class="page-title">
            <h6>View Book Id- <?= esc($book['book_id']) ?></h6>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <h6 class="mt-3">Book Details</h6>
            <input type="hidden" id="book_id" name="book_id" value="<?= esc($book['book_id']) ?>">
            
            <label class="mt-3">Publisher Name</label>
            <input type="text" value="Publisher ID: <?= esc($book['publisher_id']) ?>" class="form-control" readonly />

            <label class="mt-3">Publisher/Customer Name</label>
            <input type="text" value="<?= esc($book['custom_publisher_name'] ?? '') ?>" class="form-control" readonly />

            <label class="mt-3">Publisher Order/Reference No.</label>
            <input type="text" value="<?= esc($book['publisher_reference'] ?? '') ?>" class="form-control" readonly />

            <label class="mt-3">Book Title</label>
            <input type="text" value="<?= esc($book['book_title'] ?? '') ?>" class="form-control" readonly />

            <label class="mt-3">Number of Pages</label>
            <input type="text" value="<?= esc($book['total_num_pages'] ?? '') ?>" class="form-control" readonly />

            <label class="mt-3">Number of Copies</label>
            <input type="text" value="<?= esc($book['num_copies'] ?? '') ?>" class="form-control" readonly />

            <h6 class="mt-3">Book Specifications</h6>
        
            <div class="row">
                <div class="col-6">
                    <label class="mt-1">Book Size</label>
                    <input type="text" value="<?= esc($book['book_size'] ?? '') ?>" class="mt-1 form-control" readonly />
                </div>
                <div class="col-6">
                    <label class="mt-1">Custom Size</label>
                    <input type="text" class="mt-1 form-control" value="<?= esc($book['book_size'] ?? '') ?>" readonly />
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <label class="mt-3">Cover Paper Type</label>
                    <input type="text" value="<?= esc($book['cover_paper'] ?? '') ?>" class="form-control" readonly />
                </div>
                <div class="col-6">
                    <label class="mt-3">Custom Type</label>
                    <input type="text" class="form-control" value="<?= esc($book['cover_paper'] ?? '') ?>" readonly />
                </div>
            </div>

            <label class="mt-3">Cover GSM</label>
            <input type="text" value="<?= esc($book['cover_gsm'] ?? '') ?>" class="form-control" readonly />

            <div class="row">
                <div class="col-6">
                    <label class="mt-3">Content Paper Type</label>
                    <input type="text" value="<?= esc($book['content_paper'] ?? '') ?>" class="form-control" readonly />
                </div>
                <div class="col-6">
                    <label class="mt-3">Custom Type</label>
                    <input type="text" class="form-control" value="<?= esc($book['content_paper'] ?? '') ?>" readonly />
                </div>
            </div>

            <label class="mt-3">Content GSM</label>
            <input type="text" value="<?= esc($book['content_gsm'] ?? '') ?>" class="form-control" readonly />

            <label class="mt-3 fw-bold text-secondary">Content in colour?</label>
            <div class="d-flex align-items-center flex-wrap gap-3 mt-2">
                <div class="form-check checked-primary d-flex align-items-center gap-2">
                    <input class="form-check-input" type="radio" name="content_colour" value="N" 
                        <?= (($book['content_colour'] ?? 'N') == 'N') ? 'checked' : '' ?> disabled>
                    <label class="form-check-label fw-medium text-secondary-light">No</label>
                </div>
                <div class="form-check checked-success d-flex align-items-center gap-2">
                    <input class="form-check-input" type="radio" name="content_colour" value="Y" 
                        <?= (($book['content_colour'] ?? 'N') == 'Y') ? 'checked' : '' ?> disabled>
                    <label class="form-check-label fw-medium text-secondary-light">Yes</label>
                </div>
            </div>

            <label class="mt-3 fw-bold text-secondary">Lamination</label>
            <input type="text" value="<?= esc($book['lamination_type'] ?? '') ?>" class="form-control mt-1" readonly />

            <label class="mt-3 fw-bold text-secondary">Binding Type</label>
            <input type="text" value="<?= esc($book['binding_type'] ?? '') ?>" class="form-control" readonly />
        </div>

        <div class="col-6">
            <h6 class="mt-3">Quotation Details</h6>
            <div class="row">
                <div class="col-4">
                    <label class="mt-4">#Pages</label>
                    <input type="text" value="<?= esc($book['num_pages_quote1'] ?? '') ?>" class="form-control" readonly />
                </div>
                <div class="col-4">
                    <label class="mt-4">Cost/Page</label>
                    <input type="text" value="<?= esc($book['cost_per_page1'] ?? '') ?>" class="form-control" readonly />
                </div>
                <div class="col-4">
                    <label class="mt-4">#Pages x Cost/Book</label>
                    <input class="form-control" value="<?= number_format(($book['num_pages_quote1'] ?? 0) * ($book['cost_per_page1'] ?? 0), 2) ?>" readonly />
                </div>
            </div>

            <span>Price: 50 to 75 - 0.45, 76 to 100 - 0.40, 101 to 150 - 0.35, >150 - 0.31</span>
            
            <label class="mt-4">Fixed Charge/Book</label>
            <input type="text" value="<?= esc($book['fixed_charge_book'] ?? '') ?>" class="form-control" readonly />

            <div class="row">
                <div class="col-4">
                    <label class="mt-4">#Pages</label>
                    <input type="text" value="<?= esc($book['num_pages_quote2'] ?? '') ?>" class="form-control" readonly />
                </div>
                <div class="col-4">
                    <label class="mt-4">Cost/Page</label>
                    <input type="text" value="<?= esc($book['cost_per_page2'] ?? '') ?>" class="form-control" readonly />
                </div>
                <div class="col-4">
                    <label class="mt-4">#Pages x Cost/Book</label>
                    <input class="form-control" value="<?= number_format(($book['num_pages_quote2'] ?? 0) * ($book['cost_per_page2'] ?? 0), 2) ?>" readonly />
                </div>
            </div>
            
            <span>Use the above if partial content pages has different cost</span>        
            
            <div class="row">
                <div class="col-4">
                    <label class="mt-4">Cost/Book</label>
                    <?php
                    $content_cost1 = ($book['num_pages_quote1'] ?? 0) * ($book['cost_per_page1'] ?? 0);
                    $content_cost2 = ($book['num_pages_quote2'] ?? 0) * ($book['cost_per_page2'] ?? 0);
                    $cost_per_book = $content_cost1 + $content_cost2 + ($book['fixed_charge_book'] ?? 0);
                    ?>
                    <input class="form-control" value="<?= number_format($cost_per_book, 2) ?>" readonly />
                </div>
                <div class="col-4">
                    <label class="mt-4">#Copies</label>
                    <input class="form-control" value="<?= esc($book['num_copies'] ?? '') ?>" readonly />
                </div>
                <div class="col-4">
                    <label class="mt-4">Total Book Cost</label>
                    <input class="form-control" value="<?= number_format($cost_per_book * ($book['num_copies'] ?? 0), 2) ?>" readonly />
                </div>
            </div>

            <label class="mt-3">Transport Charges</label>
            <input type="text" value="<?= esc($book['transport_charges'] ?? '') ?>" class="form-control" readonly />

            <label class="mt-3">Invoice Number</label>
            <input type="text" value="<?= !empty($book['invoice_number']) ? esc($book['invoice_number']) : 'No Invoice Number Assigned' ?>" class="form-control text-danger fw-bold" readonly />

            <label class="mt-3">Design Charges</label>
            <input type="text" value="<?= esc($book['design_charges'] ?? '') ?>" class="form-control" readonly />

            <label class="mt-3">Content Location</label>
            <input type="text" value="<?= esc($book['content_location'] ?? '') ?>" class="form-control" readonly />

            <label class="mt-3">Delivery Date</label>
            <input type="text" value="<?= esc($book['delivery_date'] ?? '') ?>" class="form-control" readonly />
            
            <label class="mt-3">Remarks</label>
            <textarea rows="5" class="form-control" readonly><?= esc($book['remarks'] ?? '') ?></textarea>

            <?php 
            $bill_address = ($book['address'] ?? '') . "\nCity: " . ($book['city'] ?? '') . "\nContact: " . ($book['contact_person'] ?? '') . "\nMobile: " . ($book['contact_mobile'] ?? '');
            ?>

            <label class="mt-3">Billing Address</label>
            <textarea rows="5" class="form-control" readonly><?= htmlspecialchars($bill_address, ENT_QUOTES, 'UTF-8') ?></textarea>

            <label class="mt-3">Shipping Address</label>
            <textarea rows="5" class="form-control" readonly><?= esc($book['ship_address'] ?? '') ?></textarea>
        </div>
    </div>

    <?php endif; ?>
</div>

<?= $this->endSection() ?>