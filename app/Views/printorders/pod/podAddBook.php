<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

    <div class="layout-px-spacing">
    <div class="row">
        <div class="col-6">
            <h6 class="mt-3">Book Details</h6>
            <label class="mt-3">Select Publisher</label>
            <select name="publisher_id" id="publisher_id" class="form-control">
                <?php if (isset($publisher_list['publisher']))
                {
                    for($i=0; $i<count($publisher_list['publisher']); $i++)
                    {
                        ?>
                        <option value="<?php echo $publisher_list['publisher'][$i]['id'];?>" 
                            data-name="<?php echo $publisher_list['publisher'][$i]['publisher_name']; ?>"
                            data-address="<?php echo $publisher_list['publisher'][$i]['address']; ?>"
                            data-city="<?php echo $publisher_list['publisher'][$i]['city']; ?>"
                            data-contact_person="<?php echo $publisher_list['publisher'][$i]['contact_person']; ?>"
                            data-contact_mobile="<?php echo $publisher_list['publisher'][$i]['contact_mobile'];?>"> 
                        
                        <?php echo $publisher_list['publisher'][$i]['publisher_name']; ?></option>
                <?php } } ?>
                <option value="0" data-name="Other">Other</option>
            </select>

            <label class="mt-3">Publisher/Customer Name (Optional - only if Other is selected)</label>
            <input class="form-control" name="custom_publisher_name" id="custom_publisher_name" />
            <label class="mt-3">Publisher Order/Reference No.</label>
            <input class="form-control" name="publisher_reference" id="publisher_reference" />
            <label class="mt-3">Book Title</label>
            <input class="form-control" name="book_title" id="book_title" />
            <label class="mt-3">Number of Pages</label>
            <input class="form-control" name="num_pages" onInput="populate_quotation_data()" id="num_pages" required />
            <label class="mt-3">Number of Copies</label>
            <input class="form-control" name="num_copies" onInput="populate_quotation_data()" id="num_copies" />
            <h6 class="mt-3">Book Specifications</h6>
        
            <div class="row">
                <div class="col-6">
                    <label class="mt-1">Book Size</label>
                    <select name="book_size" id="book_size" class="mt-1 form-control">
                        <option value="Demy" data-name="Demy">Demy</option>
                        <option value="A4" data-name="Demy">A4</option>
                        <option value="A5" data-name="Demy">A5</option>
                        <option value="Custom" data-name="Demy">Custom</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="mt-1">Custom Size</label>
                    <input class="mt-1 form-control" name="custom_book_size" placeholder="Only for Custom Size" id="custom_book_size" />
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <label class="mt-3">Cover Paper Type</label>
                    <select name="cover_paper" id="cover_paper" class="form-control">
                        <option value="Art" data-name="Demy">Art</option>
                        <option value="Texture" data-name="Texture">Texture</option>
                        <option value="Custom" data-name="Custom">Custom</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="mt-3">Custom Type</label>
                    <input class="form-control" name="custom_cover_paper" placeholder="Only for Custom Type" id="custom_cover_paper" />
                </div>
            </div>
            <label class="mt-3">Cover GSM</label>
            <select name="cover_gsm" id="cover_gsm" class="form-control">
                <option value="300 GSM" data-name="300gsm">300 GSM</option>
                <option value="250 GSM" data-name="250gsm">250 GSM</option>
                <option value="170 GSM" data-name="170gsm">170 GSM</option>
                <option value="130 GSM" data-name="130gsm">130 GSM</option>
            </select>
            <div class="row">
                <div class="col-6">
                    <label class="mt-3">Content Paper Type</label>
                    <select name="content_paper" id="content_paper" class="form-control">
                        <option value="NS Maplitho" data-name="NS Maplitho">NS Maplitho</option>
                        <option value="Book Print" data-name="Book Print">Book Print</option>
                        <option value="Stora" data-name="Stora">Stora</option>
                        <option value="Index" data-name="Index">Index</option>
                        <option value="Art" data-name="Art">Art</option>
                        <option value="Custom" data-name="Custom">Custom</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="mt-3">Custom Type</label>
                    <input class="form-control" name="custom_content_paper" placeholder="Only for Custom Type" id="custom_content_paper" />
                </div>
            </div>
            <label class="mt-3">Content GSM</label>
            <select name="content_gsm" id="content_gsm" class="form-control">
                <option value="70 GSM" data-name="70gsm">70 GSM</option>
                <option value="80 GSM" data-name="80gsm">80 GSM</option>
                <option value="65 GSM" data-name="65gsm">65 GSM</option>
                <option value="130 GSM" data-name="130gsm">130 GSM</option>
            </select>

                <label class="mt-3 fw-bold text-secondary">Content in colour?</label>
                <div class="d-flex align-items-center flex-wrap gap-3 mt-2">
                    <div class="form-check checked-primary d-flex align-items-center gap-2">
                        <input class="form-check-input" type="radio" name="content_colour" id="content_colour_no" value="N" checked>
                        <label class="form-check-label fw-medium text-secondary-light" for="content_colour_no">No</label>
                    </div>
                    <div class="form-check checked-success d-flex align-items-center gap-2">
                        <input class="form-check-input" type="radio" name="content_colour" id="content_colour_yes" value="Y">
                        <label class="form-check-label fw-medium text-secondary-light" for="content_colour_yes">Yes</label>
                    </div>
                </div>

                <label class="mt-3 fw-bold text-secondary">Lamination</label>
                <select name="lamination" id="lamination" class="form-control mt-1">
                    <option value="Matt">Matt</option>
                    <option value="Glossy">Glossy</option>
                    <option value="Velvet">Velvet</option>
                </select>

                <label class="mt-3 fw-bold text-secondary">Binding Type</label>
                <div class="d-flex align-items-center flex-wrap gap-3 mt-2">
                    <div class="form-check checked-primary d-flex align-items-center gap-2">
                        <input class="form-check-input" type="radio" name="binding" id="binding_perfect" value="Perfect" checked>
                        <label class="form-check-label fw-medium text-secondary-light" for="binding_perfect">Perfect</label>
                    </div>
                    <div class="form-check checked-warning d-flex align-items-center gap-2">
                        <input class="form-check-input" type="radio" name="binding" id="binding_stapler" value="Stapler">
                        <label class="form-check-label fw-medium text-secondary-light" for="binding_stapler">Stapler</label>
                    </div>
                </div>
                    </div>
    
        <div class="col-6">
        <h6 class="mt-3">Quotation Details</h6>
        <div class="row">
            <div class="col-4">
                <label class="mt-4">#Pages</label>
                <input class="form-control" name="num_pages_quote" onInput="fill_quotation_data()" id="num_pages_quote" />
            </div>
            <div class="col-4">
                <label class="mt-4">Cost/Page</label>
                <input class="form-control" name="cost_per_page" onInput="fill_quotation_data()" id="cost_per_page" />
            </div>
            <div class="col-4">
                <label class="mt-4">#Pages x Cost/Book</label>
                <input class="form-control" name="content_cost" id="content_cost" readonly />
            </div>
        </div>
        <span style="font-size: 15px;">Price: <=50 - 0.55, 50 to 75 - 0.5, 76 to 100 - 0.45, 101 to 150 - 0.41, >150 - 0.38</span>
        <label class="mt-4">Fixed Charge/Book</label>
        <input class="form-control" name="fixed_charge" onInput="fill_quotation_data()" id="fixed_charge" />

        <div class="row">
            <div class="col-4">
                <label class="mt-4">#Pages</label>
                <input class="form-control" name="num_pages_quote1" onInput="fill_quotation_data()" id="num_pages_quote1" />
            </div>
            <div class="col-4">
                <label class="mt-4">Cost/Page</label>
                <input class="form-control" name="cost_per_page1" onInput="fill_quotation_data()" id="cost_per_page1" />
            </div>
            <div class="col-4">
                <label class="mt-4">#Pages x Cost/Book</label>
                <input class="form-control" name="content_cost1" id="content_cost1" readonly />
            </div>
        </div>
        <span style="font-size: 15px;">Use the above if partial content pages has different cost</span>        
        <div class="row">
            <div class="col-4">
                <label class="mt-4">Cost/Book</label>
                <input class="form-control" name="cost_per_book" id="cost_per_book" readonly />
            </div>
            <div class="col-4">
                <label class="mt-4">#Copies</label>
                <input class="form-control" name="num_copies_quote" id="num_copies_quote" readonly />
            </div>
            <div class="col-4">
                <label class="mt-4">Total Book Cost</label>
                <input class="form-control" name="total_book_cost" id="total_book_cost" readonly />
            </div>
        </div>
        <label class="mt-3">Transport Charges (Optional)</label>
        <input class="form-control" name="transport_charges" id="transport_charges" />
        <label class="mt-3">Design Charges (One time)</label>
        <input class="form-control" name="design_charges" id="design_charges" />
        <label class="mt-3">Content Location</label>
        <input class="form-control" name="content_location" id="content_location" />
        <label class="mt-3">Delivery Date</label>
        <input type="date" id="delivery_date" name="delivery_date"><br>
        <label class="mt-3">Remarks</label>
        <textarea name="" id="remarks" rows="5" class="form-control" placeholder="Add any other remarks here..."></textarea>
        <!-- Display this for Billing Address -->
        <label class="mt-3">Billing Address</label>
        <textarea name="" id="bill_addr" rows="5" class="form-control" readonly style="color: black;">
        </textarea>
        <label class="mt-3">Shipping Address</label>
        <textarea name="" id="ship_address" rows="5" class="form-control">
        </textarea>
    </div>
</div>
<div class="d-flex justify-content-between mt-3">
    <a href="javascript:void(0)" onclick="add_publisher_book()" class="btn rounded-pill btn-success-600 radius-6 px-16 py-11">
        Submit
    </a>
    <a href="javascript:history.back()" class="btn rounded-pill btn-secondary radius-6 px-16 py-11">
        Back
    </a>
</div>

<script type="text/javascript">
    // ✅ CI4 SAFE base_url
    var base_url = "<?= base_url(); ?>";

    /* ---------------------------
       Publisher change validation
    ----------------------------*/
    function validateForm() {
        var select = document.getElementById('publisher_id');
        var publisher_id = select.value;
        var selectedOption = select.options[select.selectedIndex];

        var address = selectedOption.getAttribute('data-address') || '';
        var city = selectedOption.getAttribute('data-city') || '';
        var contact = selectedOption.getAttribute('data-contact_person') || '';
        var mobile = selectedOption.getAttribute('data-contact_mobile') || '';

        document.getElementById('bill_addr').value =
            address + '\nCity: ' + city + '\nContact: ' + contact + '\nMobile: ' + mobile;

        // Shipping address will be handled by separate logic

        if (publisher_id == 0) {
            var customName = document.getElementById('custom_publisher_name').value;
            if (!customName) {
                alert("Publisher name required for Custom");
                return false;
            }
        }
        return true;
    }

    // ---------------------------
    // Shipping address auto-fill logic
    // ---------------------------
    const shipTextarea = document.getElementById('ship_address');

    document.getElementById('publisher_id').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const address = selectedOption.getAttribute('data-address') || '';
        const city = selectedOption.getAttribute('data-city') || '';
        const contact = selectedOption.getAttribute('data-contact_person') || '';
        const mobile = selectedOption.getAttribute('data-contact_mobile') || '';

        // Only auto-fill shipping address if user hasn't typed
        if (!shipTextarea.dataset.userEdited) {
            shipTextarea.value =
                address + '\nCity: ' + city + '\nContact: ' + contact + '\nMobile: ' + mobile;
        }

        // Billing address (optional) can also update here
        document.getElementById('bill_addr').value =
            address + '\nCity: ' + city + '\nContact: ' + contact + '\nMobile: ' + mobile;
    });

    // Track user edits
    shipTextarea.addEventListener('input', function () {
        this.dataset.userEdited = this.value.trim() ? "1" : "";
    });


    /* ---------------------------
       ADD POD BOOK (AJAX)
    ----------------------------*/
    function add_publisher_book() {
        if (!validateForm()) return;

        $.ajax({
            url: base_url + "/pod/podbookpost",
            type: "POST",
            dataType: "json",
            data: {
                "<?= csrf_token() ?>": "<?= csrf_hash() ?>",
                publisher_id: $('#publisher_id').val(),
                custom_publisher_name:
                    ($('#publisher_id').val() == 0)
                        ? $('#custom_publisher_name').val()
                        : 'None',
                publisher_reference: $('#publisher_reference').val(),
                book_title: $('#book_title').val(),
                total_num_pages: $('#num_pages').val(),
                num_copies: $('#num_copies').val(),
                book_size: ($('#book_size').val() == 'Custom') ? $('#custom_book_size').val() : $('#book_size').val(),
                cover_paper: ($('#cover_paper').val() == 'Custom') ? $('#custom_cover_paper').val() : $('#cover_paper').val(),
                cover_gsm: $('#cover_gsm').val(),
                content_paper: ($('#content_paper').val() == 'Custom') ? $('#custom_content_paper').val() : $('#content_paper').val(),
                content_gsm: $('#content_gsm').val(),
                content_colour: $('input[name="content_colour"]:checked').val(),
                lamination_type: $('#lamination').val(),
                binding_type: $('input[name="binding"]:checked').val(),
                content_location: $('#content_location').val(),
                num_pages_quote1: $('#num_pages_quote').val(),
                cost_per_page1: $('#cost_per_page').val(),
                num_pages_quote2: $('#num_pages_quote1').val() || 0,
                cost_per_page2: $('#cost_per_page1').val() || 0,
                fixed_charge_book: $('#fixed_charge').val() || 0,
                transport_charges: $('#transport_charges').val() || 0,
                design_charges: $('#design_charges').val() || 0,
                delivery_date: $('#delivery_date').val(),
                remarks: $('#remarks').val(),
                ship_address: $('#ship_address').val() // <-- user's address or auto-filled
            },
            success: function (res) {
                if (res.status == 1) {
                    alert("✅ Successfully added book!");
                    location.reload();
                } else {
                    alert("❌ Book not added!");
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert("⚠️ Server Error");
            }
        });
    }

    /* ---------------------------
       QUOTATION CALCULATION
    ----------------------------*/
    function fill_quotation_data() {
        var p1 = Number($('#num_pages_quote').val());
        var c1 = Number($('#cost_per_page').val());
        var p2 = Number($('#num_pages_quote1').val() || 0);
        var c2 = Number($('#cost_per_page1').val() || 0);
        var fixed = Number($('#fixed_charge').val() || 0);
        var copies = Number($('#num_copies_quote').val() || 0);

        var cost1 = p1 * c1;
        var cost2 = p2 * c2;

        $('#content_cost').val(cost1);
        $('#content_cost1').val(cost2);

        var cost_per_book = cost1 + cost2 + fixed;
        $('#cost_per_book').val(cost_per_book);
        $('#total_book_cost').val(cost_per_book * copies);
    }

    function populate_quotation_data() {
        var pages = Number($('#num_pages').val());
        var copies = Number($('#num_copies').val());

        $('#num_pages_quote').val(pages);
        $('#num_copies_quote').val(copies);

        if (pages >= 50 && pages <= 75) $('#cost_per_page').val(0.5);
        else if (pages >= 76 && pages <= 100) $('#cost_per_page').val(0.45);
        else if (pages >= 101 && pages <= 150) $('#cost_per_page').val(0.41);
        else if (pages >= 151) $('#cost_per_page').val(0.38);

        fill_quotation_data();
    }
</script>

<?= $this->endSection(); ?>