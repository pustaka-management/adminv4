<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>
    <div class="layout-px-spacing">
    <?php $pod_publisher_book = $pod_publisher_book_details['pod_publisher_book'];
        $pod_publishers = $pod_publisher_book_details['pod_publishers'];?>
    <div class="page-header">
        <div class="page-title">
        <h6>Edit Book Id- <?php echo $pod_publisher_book['book_id']; ?></h6>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <h6 class="mt-3">Book Details</h6>
            <input type="hidden" id="book_id" name="book_id" value="<?php echo $pod_publisher_book['book_id']; ?>">
            <label class="mt-3">Publisher Name</label>
            <select name="publisher_id" id="publisher_id" class="form-control">
            <?php if (isset($pod_publishers))
                    {
                        for($i=0; $i<count($pod_publishers); $i++)
                        {
                            if ($pod_publisher_book['publisher_id'] == $pod_publishers[$i]['id']) { ?>
                                <option selected value="<?php echo $pod_publishers[$i]['id'];?>" data-name="<?php echo $pod_publishers[$i]['publisher_name']; ?>"><?php echo $pod_publishers[$i]['publisher_name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo  $pod_publishers[$i]['id'];?>" data-name="<?php echo $pod_publishers[$i]['publisher_name']; ?>"><?php echo $pod_publishers[$i]['publisher_name']; ?></option>
                 <?php  } } } ?>
                     <option value="0" data-name="Other">Other</option>
            </select>

            <label class="mt-3">Publisher/Customer Name </label>
            <input type="text" value="<?php echo $pod_publisher_book['custom_publisher_name'];?>"class="form-control" name="custom_publisher_name" id="custom_publisher_name" />

            <label class="mt-3">Publisher Order/Reference No.</label>
            <input type="text" value="<?php echo $pod_publisher_book['publisher_reference'];?>" class="form-control" name="publisher_reference" id="publisher_reference" />

            <label class="mt-3">Book Title</label>
            <input  type="text" value="<?php echo $pod_publisher_book['book_title'];?>"  class="form-control" name="book_title" id="book_title" />

            <label class="mt-3">Number of Pages</label>
            <input type="text" value="<?php echo $pod_publisher_book['total_num_pages'];?>"  class="form-control" name="num_pages" onInput="populate_quotation_data()" id="num_pages"/>

            <label class="mt-3">Number of Copies</label>
            <input type="text" value="<?php echo $pod_publisher_book['num_copies'];?>" class="form-control" name="num_copies" onInput="populate_quotation_data()" id="num_copies" />

            <h6 class="mt-3">Book Specifications</h6>
        
            <div class="row">
                <div class="col-6">
                    <label class="mt-1">Book Size</label>
                    <select name="book_size" id="book_size" class="mt-1 form-control">
                        <option value="Demy" data-name="Demy">Demy</option>
                        <option value="A4" data-name="Demy">A4</option>
                        <option value="A5" data-name="Demy">A5</option>
                        <option value="Custom" data-name="Demy">Custom</option>

                        <?php if ($pod_publisher_book['book_size'] == 'Demy') { ?>
                        <option value="Demy" selected>Demy</option>
                        <?php } else if ($pod_publisher_book['book_size'] == 'A4') { ?>
                            <option value="A4" selected>A4</option>
                        <?php } else if ($pod_publisher_book['book_size'] == 'A5') { ?>
                            <option value="A5" selected>A5</option>
                        <?php } else  { ?>
                            <option value="Custom" selected>Custom</option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-6">
                    <label class="mt-1">Custom Size</label>
                    <input type="text" class="mt-1 form-control" name="custom_book_size" value="<?php echo $pod_publisher_book['book_size'];?>" id="custom_book_size" />
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <label class="mt-3">Cover Paper Type</label>
                    <select name="cover_paper" id="cover_paper" class="form-control">
                        <option value="Art" data-name="Demy">Art</option>
                        <option value="Texture" data-name="Texture">Texture</option>
                        <option value="Custom" data-name="Custom">Custom</option>

                        <?php if ($pod_publisher_book['cover_paper'] == 'Art') { ?>
                        <option value="Demy" selected>Art</option>
                        <?php } else if ($pod_publisher_book['cover_paper'] == 'Texture') { ?>
                            <option value="A4" selected>Texture</option>
                        <?php } else  { ?>
                            <option value="Custom" selected>Custom</option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-6">
                    <label class="mt-3">Custom Type</label>
                    <input type="text" class="form-control" name="custom_cover_paper" value="<?php echo $pod_publisher_book['cover_paper'];?>" id="custom_cover_paper" />
                </div>
            </div>
            <label class="mt-3">Cover GSM</label>
            <select name="cover_gsm" id="cover_gsm" class="form-control">
                <option value="300 GSM" data-name="300gsm">300 GSM</option>
                <option value="250 GSM" data-name="250gsm">250 GSM</option>
                <option value="170 GSM" data-name="170gsm">170 GSM</option>
                <option value="130 GSM" data-name="130gsm">130 GSM</option>

                <?php if ($pod_publisher_book['cover_gsm']== '300 GSM') { ?>
                        <option value="300 GSM" selected>300 GSM</option>
                        <?php } else if ($pod_publisher_book['cover_gsm'] == '250 GSM') { ?>
                            <option value="250 GSM" selected>250 GSM</option>
                        <?php } else if ($pod_publisher_book['cover_gsm'] == '170 GSM') { ?>
                            <option value="170 GSM" selected>170 GSM</option>
                        <?php } else if ($pod_publisher_book['cover_gsm'] == '130 GSM')  { ?>
                            <option value="130 GSM" selected>130 GSM</option>
                <?php } ?>
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

                        <?php if ($pod_publisher_book['content_paper'] == 'NS Maplitho') { ?>
                        <option value="NS Maplitho" selected>NS Maplitho</option>
                        <?php } else if ($pod_publisher_book['content_paper'] == 'Book Print') { ?>
                            <option value="Book Print" selected>Book Print</option>
                        <?php } else if ($pod_publisher_book['content_paper'] == 'Stora') { ?>
                            <option value="Stora" selected>Stora</option>
                        <?php }else if ($pod_publisher_book['content_paper'] == 'Index') { ?>
                            <option value="Index" selected>Index</option>
                        <?php }else if ($pod_publisher_book['content_paper'] == 'Art') { ?>
                            <option value="Art" selected>Art</option>
                        <?php }else{ ?>
                            <option value="Custom" selected>Custom</option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-6">
                    <label class="mt-3">Custom Type</label>
                    <input type="text" class="form-control" name="custom_content_paper" value="<?php echo $pod_publisher_book['content_paper'];?>" id="custom_content_paper" />
                </div>
            </div>

            <label class="mt-3">Content GSM</label>
            <select name="content_gsm" id="content_gsm" class="form-control">
                <option value="70 GSM" data-name="70gsm">70 GSM</option>
                <option value="80 GSM" data-name="80gsm">80 GSM</option>
                <option value="65 GSM" data-name="65gsm">65 GSM</option>
                <option value="130 GSM" data-name="130gsm">130 GSM</option>

                <?php if ($pod_publisher_book['content_gsm']== '70 GSM') { ?>
                        <option value="70 GSM" selected>70 GSM</option>
                        <?php } else if ($pod_publisher_book['content_gsm'] == '80 GSM') { ?>
                            <option value="80 GSM" selected>80 GSM</option>
                        <?php } else if ($pod_publisher_book['content_gsm'] == '65 GSM') { ?>
                            <option value="65 GSM" selected>65 GSM</option>
                        <?php } else if ($pod_publisher_book['content_gsm'] == '130 GSM')  { ?>
                            <option value="130 GSM" selected>130 GSM</option>
                <?php } ?>
            </select>

            <label class="mt-3 fw-bold text-secondary">Content in colour?</label>
<div class="d-flex align-items-center flex-wrap gap-3 mt-2">
    <div class="form-check checked-primary d-flex align-items-center gap-2">
        <input class="form-check-input" type="radio" name="content_colour" id="content_colour_no" value="N" <?= ($pod_publisher_book['content_colour'] == 'N') ? 'checked' : '' ?>>
        <label class="form-check-label fw-medium text-secondary-light" for="content_colour_no">No</label>
    </div>
    <div class="form-check checked-success d-flex align-items-center gap-2">
        <input class="form-check-input" type="radio" name="content_colour" id="content_colour_yes" value="Y" <?= ($pod_publisher_book['content_colour'] == 'Y') ? 'checked' : '' ?>>
        <label class="form-check-label fw-medium text-secondary-light" for="content_colour_yes">Yes</label>
    </div>
</div>

<label class="mt-3 fw-bold text-secondary">Lamination</label>
<select name="lamination" id="lamination" class="form-control mt-1">
    <?php
    $laminations = ['Matt', 'Glossy', 'Velvet'];
    foreach ($laminations as $lam) {
        $selected = ($pod_publisher_book['lamination_type'] == $lam) ? 'selected' : '';
        echo "<option value='$lam' $selected>$lam</option>";
    }
    ?>
</select>

<label class="mt-3 fw-bold text-secondary">Binding Type</label>
<div class="d-flex align-items-center flex-wrap gap-3 mt-2">
    <div class="form-check checked-primary d-flex align-items-center gap-2">
        <input class="form-check-input" type="radio" name="binding" id="binding_perfect" value="Perfect" <?= ($pod_publisher_book['binding_type'] == 'Perfect') ? 'checked' : '' ?>>
        <label class="form-check-label fw-medium text-secondary-light" for="binding_perfect">Perfect</label>
    </div>
    <div class="form-check checked-warning d-flex align-items-center gap-2">
        <input class="form-check-input" type="radio" name="binding" id="binding_stapler" value="Stapler" <?= ($pod_publisher_book['binding_type'] == 'Stapler') ? 'checked' : '' ?>>
        <label class="form-check-label fw-medium text-secondary-light" for="binding_stapler">Stapler</label>
    </div>
</div>
</div>

        <div class="col-6">
        <h6 class="mt-3">Quotation Details</h6>
        <div class="row">
            <div class="col-4">
                <label class="mt-4">#Pages</label>
                <input type="text" value="<?php echo $pod_publisher_book['num_pages_quote1'];?>" class="form-control" name="num_pages_quote" onInput="fill_quotation_data()" id="num_pages_quote" />
            </div>
            <div class="col-4">
                <label class="mt-4">Cost/Page</label>
                <input ype="text" value="<?php echo $pod_publisher_book['cost_per_page1'];?>" class="form-control" name="cost_per_page" onInput="fill_quotation_data()" id="cost_per_page" />
            </div>
            <div class="col-4">
                <label class="mt-4">#Pages x Cost/Book</label>
                <input class="form-control" name="content_cost" id="content_cost" readonly />
            </div>
        </div>

        <span>Price: 50 to 75 - 0.45, 76 to 100 - 0.40, 101 to 150 - 0.35, >150 - 0.31</span>
        <label class="mt-4">Fixed Charge/Book</label>
        <input type="text" value="<?php echo $pod_publisher_book['fixed_charge_book'];?>" class="form-control" name="fixed_charge" onInput="fill_quotation_data()" id="fixed_charge" />

        <div class="row">
            <div class="col-4">
                <label class="mt-4">#Pages</label>
                <input type="text" value="<?php echo $pod_publisher_book['num_pages_quote2'];?>" class="form-control" name="num_pages_quote1" onInput="fill_quotation_data()" id="num_pages_quote1" />
            </div>
            <div class="col-4">
                <label class="mt-4">Cost/Page</label>
                <input type="text" value="<?php echo $pod_publisher_book['cost_per_page2'];?>" class="form-control" name="cost_per_page1" onInput="fill_quotation_data()" id="cost_per_page1" />
            </div>
            <div class="col-4">
                <label class="mt-4">#Pages x Cost/Book</label>
                <input class="form-control" name="content_cost1" id="content_cost1" readonly />
            </div>
        </div>
        <span>Use the above if partial content pages has different cost</span>        
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

        <label class="mt-3">Transport Charges </label>
        <input type="text" value="<?php echo $pod_publisher_book['transport_charges'];?>" class="form-control" name="transport_charges" id="transport_charges" />

        <label class="mt-3">Design Charges </label>
        <input  type="text" value="<?php echo $pod_publisher_book['design_charges'];?>" class="form-control" name="design_charges" id="design_charges" />

        <label class="mt-3">Content Location</label>
        <input type="text" value="<?php echo $pod_publisher_book['content_location'];?>" class="form-control" name="content_location" id="content_location" />

        <label class="mt-3">Delivery Date</label>
        <input type="text" value="<?php echo $pod_publisher_book['delivery_date'];?>" id="delivery_date" name="delivery_date" class="form-control" ><br>
        
        <label class="mt-3">Remarks</label>
        <input type="textarea" name="" id="remarks" rows="5" class="form-control" value="<?php echo $pod_publisher_book['remarks'];?>"/>

        <?php 
        $bill_address = $pod_publisher_book['address'] . "\nCity: " . $pod_publisher_book['city'] . "\nContact: " . $pod_publisher_book['contact_person'] . "\nMobile: " . $pod_publisher_book['contact_mobile'];
        ?>

        <label class="mt-3">Billing Address</label>
        <textarea name="" id="bill_addr" rows="5" class="form-control" readonly style="color: black;"><?php echo htmlspecialchars($bill_address, ENT_QUOTES, 'UTF-8'); ?></textarea>


        <label class="mt-3">Shipping Address</label>
       <textarea name="ship_address" id="ship_address" rows="5" class="form-control" style="color: black;"><?= esc($pod_publisher_book['ship_address']) ?>
</textarea>

    </div>
</div>
 <a href="#" onclick="edit_publisher_book()" class="ml-3 mt-3 mb-5 btn btn-outline-secondary btn-lg">Modify</a>

<script type="text/javascript">
    var base_url = window.location.origin;

    function validateForm() {
        var tmp = document.getElementById('publisher_id');
        var publisher_id = tmp.options[tmp.selectedIndex].value;
        if (publisher_id == 0){
            var custom_publisher_name = document.getElementById('custom_publisher_name').value;
            if (!custom_publisher_name)
                alert("Publisher name required for Custom");
                return false;
        }
        
        document.getElementById('bill_addr').value = selectedAddress + '\nCity: ' + selectedcity +'\nContact: '+selectedcontact+'\nMobile: '+selectedmobile;
    }
    // Storing all values from form into variables
    function edit_publisher_book() {

        var book_size = document.getElementById('book_size').value;

        if (book_size == 'Custom') {
            book_size = document.getElementById('custom_book_size').value;
        }

        var data = {
            "publisher_id": document.getElementById('publisher_id').value,
            "book_id": document.getElementById('book_id').value,
            "custom_publisher_name": document.getElementById('custom_publisher_name').value,
            "publisher_reference": document.getElementById('publisher_reference').value,
            "book_title": document.getElementById('book_title').value,
            "total_num_pages": document.getElementById('num_pages').value,
            "num_copies": document.getElementById('num_copies').value,

            "book_size": book_size, 

            "cover_paper": document.getElementById('cover_paper').value,
            "cover_gsm": document.getElementById('cover_gsm').value,
            "content_paper": document.getElementById('content_paper').value,
            "content_gsm": document.getElementById('content_gsm').value,
            "content_colour": document.querySelector('input[name="content_colour"]:checked').value,
            "lamination_type": document.getElementById('lamination').value,
            "binding_type": document.querySelector('input[name="binding"]:checked').value,
            "content_location": document.getElementById('content_location').value,
            "num_pages_quote1": document.getElementById('num_pages_quote').value,
            "cost_per_page1": document.getElementById('cost_per_page').value,
            "num_pages_quote2": document.getElementById('num_pages_quote1').value || 0,
            "cost_per_page2": document.getElementById('cost_per_page1').value || 0,
            "fixed_charge_book": document.getElementById('fixed_charge').value || 0,
            "delivery_date": document.getElementById('delivery_date').value,
            "transport_charges": document.getElementById('transport_charges').value || 0,
            "design_charges": document.getElementById('design_charges').value || 0,
            "remarks": document.getElementById('remarks').value,
            "ship_address": document.getElementById('ship_address').value
        };

        $.post(base_url + '/pod/podpublisherbookedit', data, function(response) {
            if(response == 1) alert('Book modification successful!');
            else alert('Book not modified. Check again!');
        });
    }
    function fill_quotation_data() {
        var num_pages = document.getElementById('num_pages_quote').value;
        var num_copies = document.getElementById('num_copies_quote').value;
        var cost_per_page = document.getElementById('cost_per_page').value;
        var fixed_charge = document.getElementById('fixed_charge').value;
        var num_pages1 = document.getElementById('num_pages_quote1').value;
        var cost_per_page1 = document.getElementById('cost_per_page1').value;

        var tmp = cost_per_page * num_pages;
        document.getElementById('content_cost').value = tmp;
        var tmp1 = cost_per_page1 * num_pages1;
        document.getElementById('content_cost1').value = tmp1;
        var cost_per_book = Number(tmp) + Number(tmp1) + Number(fixed_charge);
        document.getElementById('cost_per_book').value = cost_per_book;
        var total_cost = cost_per_book * num_copies;
        document.getElementById('total_book_cost').value = total_cost;
    }

    function populate_quotation_data() {
        var num_pages = document.getElementById('num_pages').value;
        var num_copies = document.getElementById('num_copies').value;
        document.getElementById('num_pages_quote').value = num_pages;
        document.getElementById('num_copies_quote').value = num_copies;
        if (num_pages >= 50 && num_pages <=75)
            document.getElementById('cost_per_page').value = 0.45;
        if (num_pages >= 76 && num_pages <=100)
            document.getElementById('cost_per_page').value = 0.40;
        if (num_pages >= 101 && num_pages <=150)
            document.getElementById('cost_per_page').value = 0.35;
        if (num_pages >= 101 && num_pages >=151)
            document.getElementById('cost_per_page').value = 0.31;
        var cost_per_page = document.getElementById('cost_per_page').value;            
        var tmp = cost_per_page * num_pages;
        document.getElementById('content_cost').value = tmp;
        var cost_per_book = Number(tmp);
        document.getElementById('cost_per_book').value = cost_per_book;
        var total_cost = cost_per_book * num_copies;
        document.getElementById('total_book_cost').value = total_cost;

    }
</script>
<?= $this->endSection() ?>