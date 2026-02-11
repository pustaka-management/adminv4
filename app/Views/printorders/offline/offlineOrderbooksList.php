<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?> 

<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="d-flex justify-content-end mb-3">
            <a href="<?= base_url('paperback/offlineorderbooksstatus'); ?>" 
            class="btn btn-outline-secondary btn-sm d-flex align-items-center shadow-sm">
                <iconify-icon icon="mdi:arrow-left" class="me-1 fs-5"></iconify-icon> Back
            </a>
        </div>

        <div class="page-header">
            <div class="page-title">
                <h6 class="text-center">Offline Paparback Selected Books List</h6>
                <br><br>
            </div>
        </div>

        <div class="form-container outer">
            <div class="form-form">
                <div class="form-form-wrap">
                    <div class="form-container">
                        <div class="form-content">

                            <form class="text-left" action="<?= base_url('paperback/offlineorderstock') ?>" method="POST">
                                <div class="form">

                                    <input type="hidden" 
                                           value="<?= count($offline_selected_books_data); ?>" 
                                           name="num_of_books">

                                    <input type="hidden" 
                                           value="<?= $offline_selected_book_id; ?>" 
                                           name="selected_book_list">

                                    <table class="table table-bordered mb-4"> 
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>S.No</th>
                                                <th>Book ID</th>
                                                <th>Title</th>
                                                <th>Regional Title</th>
                                                <th>Author</th>
                                                <th>Paperback Cost</th>
                                                <th>PaperBack Pages</th>
                                                <th>Quantity</th>
                                                <th>Discount (%)</th>
                                                <th>Final Amount</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            $i = 1;
                                            $j = 1;
                                            foreach ($offline_selected_books_data as $selected_books) { ?>
                                                <tr>
                                                    <td><?= $i++; ?></td>

                                                    <td>
                                                        <input type="text" class="form-control"
                                                            value="<?= $selected_books['bookID'] ?>"
                                                            name="book_id<?= $j; ?>" readonly>
                                                    </td>

                                                    <td><?= $selected_books['book_title'] ?></td>
                                                    <td><?= $selected_books['regional_book_title'] ?></td>
                                                    <td><?= $selected_books['author_name'] ?></td>

                                                    <td>
                                                        <input type="text" class="form-control"
                                                            value="<?= $selected_books['paper_back_inr'] ?>"
                                                            name="bk_inr<?= $j; ?>" 
                                                            id="bk_inr<?= $j; ?>" readonly>
                                                    </td>

                                                    <td><?= $selected_books['number_of_page'] ?></td>

                                                    <td>
                                                        <input type="number" class="form-control original-amount"
                                                            placeholder="0" id="bk_qty<?= $j; ?>"
                                                            required name="bk_qty<?= $j; ?>"
                                                            onInput="calculateTotalAmount(<?= $j; ?>)">
                                                    </td>

                                                    <td>
                                                        <input type="number" class="form-control"
                                                            placeholder="0" id="bk_dis<?= $j; ?>"
                                                            required name="bk_dis<?= $j; ?>"
                                                            onInput="calculateTotalAmount(<?= $j; ?>)">
                                                    </td>

                                                    <td>
                                                        <input type="text" class="form-control final-amount"
                                                            placeholder="0" id="tot_amt<?= $j; ?>"
                                                            name="tot_amt<?= $j; ?>" required readonly>
                                                    </td>
                                                </tr>
                                            <?php $j++; } ?>
                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <td colspan="9" class="text-end fw-bold">Books Total</td>
                                                <td class="fw-bold" id="grand_total">0.00</td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    <br><br>

                                    <div class="row">
                                        <div class="col-3">
                                            <label>Courier Charges</label>
                                            <input type="text" id="courier_charges" name="courier_charges" class="form-control" required>
                                        </div>

                                        <div class="col-4">
                                            <label>Shipping Date</label>
                                            <input type="date" id="ship_date" name="ship_date" class="form-control" required>
                                        </div>

                                        <div class="col-2">
                                            <label>Payment Type</label>
                                            <div class="form-check">
                                                <input type="radio" id="account" name="payment_type"
                                                    class="form-check-input" value="Account" checked>
                                                <label for="account">Account</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" id="VPP" name="payment_type"
                                                    class="form-check-input" value="VPP">
                                                <label for="VPP">VPP</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" id="UPI" name="payment_type"
                                                    class="form-check-input" value="UPI">
                                                <label for="UPI">UPI</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" id="cash" name="payment_type"
                                                    class="form-check-input" value="Cash">
                                                <label for="cash">Cash</label>
                                            </div>
                                        </div>

                                        <div class="col-3">
                                            <label>Payment Status</label>
                                            <div class="form-check">
                                                <input type="radio" id="pending" name="payment_status"
                                                    class="form-check-input" value="Pending" checked>
                                                <label for="pending">Pending</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" id="paid" name="payment_status"
                                                    class="form-check-input" value="Paid">
                                                <label for="paid">Paid</label>
                                            </div>
                                        </div>
                                    </div>

                                    <br><br>

                                    <h6 class="text-center">Shipping Address details</h6>
                                    <br>

                                    <div class="row">
                                        <div class="col">
                                            <label>Customer Name</label>
                                            <input type="text" id="customer_name" name="customer_name" class="form-control" required>
                                        </div>

                                        <div class="col">
                                            <label>City</label>
                                            <input type="text" id="city" name="city" class="form-control" required>
                                        </div>

                                        <div class="col">
                                            <label>Mobile No</label>
                                            <input type="text" id="mobile_no" name="mobile_no" class="form-control" required>
                                        </div>

                                        <div class="col">
                                            <label>Email</label>
                                            <input type="email" id="email" name="email" class="form-control" required>
                                        </div>
                                        
                                    </div>

                                    <br>

                                    <div class="row">
                                        <div class="col-10">
                                            <label>Address</label>
                                            <textarea id="address" name="address" rows="5" class="form-control"
                                                      placeholder="Enter Your Address Here"></textarea>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row mt-3">
                                        <div class="col-10">
                                            <label>Remarks</label>
                                            <textarea id="remarks" name="remarks" rows="3" class="form-control"></textarea>
                                        </div>
                                    </div>

                                    <br>

                                    <div>
                                        <button class="btn btn-primary"
                                            style="background-color:#77B748; border-color:#77B748;"
                                            type="submit">Next</button>
                                    </div>

                                </div>
                            </form>

                        </div>                    
                    </div>
                </div>
            </div>

            <div class="page-title">
                <br>
                <a href="<?= base_url('paperback/offlineorderbooksdashboard'); ?>" 
                   class="btn btn-danger">Cancel</a>
                <br><br>
            </div>

        </div>
    </div>
    <br><br>
</div>

<script type="text/javascript">
    var base_url = window.location.origin;

    function AddToBookList(book_id) {
        var existing_book_list = document.getElementById('selected_book_list').value;
        document.getElementById('selected_book_list').value =
            existing_book_list ? (book_id + ',' + existing_book_list) : book_id;
    }

    function calculateTotalAmount(i) {
        var bk_inr = parseFloat(document.getElementById("bk_inr" + i).value) || 0;
        var bk_qty = parseFloat(document.getElementById("bk_qty" + i).value) || 0;
        var bk_dis = parseFloat(document.getElementById("bk_dis" + i).value) || 0;

        var total = bk_inr * bk_qty;
        var discount = (total * bk_dis) / 100;
        var finalAmount = total - discount;

        document.getElementById("tot_amt" + i).value = finalAmount.toFixed(2);
        updateGrandTotal();
    }

    function updateGrandTotal() {
        var total = 0;
        document.querySelectorAll(".final-amount").forEach(function (input) {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById("grand_total").innerText = total.toFixed(2);
    }
</script>

<?= $this->endSection(); ?>
