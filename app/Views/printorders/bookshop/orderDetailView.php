<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>
<a href="<?= base_url('paperback/bookshoporderbooksstatus'); ?>" 
    class="btn btn-outline-secondary btn-sm float-end">
     ← Back
</a>
<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="row g-4">
            <!-- Bookshop Details -->
            <div class="col-md-6">
                <div class="card h-100 radius-12 bg-gradient-purple text-center">
                    <div class="card-body p-24">
                        <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center 
                            bg-lilac-600 text-white mb-16 radius-12">
                            <iconify-icon icon="ri:store-3-fill" class="h5 mb-0"></iconify-icon>
                        </div>
                        <h6 class="mb-16">Bookshop Details</h6>
                        <div class="text-start d-inline-block">
                            <?php if (!empty($orderbooks['details'])): ?>
                                <p class="mb-2"><strong>Bookshop:</strong> <?= esc($orderbooks['details']['bookshop_name']) ?></p>
                                <p class="mb-2"><strong>Contact Person:</strong> <?= esc($orderbooks['details']['contact_person_name']) ?></p>
                                <p class="mb-2"><strong>Mobile No:</strong> <?= esc($orderbooks['details']['mobile']) ?></p>
                                <p class="mb-2">
                                    <strong>Transport:</strong> 
                                    <?= esc($orderbooks['details']['preferred_transport']) . " - " . esc($orderbooks['details']['preferred_transport_name']) ?>
                                </p>
                                <p class="mb-0"><strong>Address:</strong> <?= esc($orderbooks['details']['ship_address']) ?></p>
                            <?php else: ?>
                                <p class="text-danger mb-0">⚠ No bookshop details found for this order.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Order & Shipping Details -->
            <div class="col-md-6">
                <div class="card h-100 radius-12 bg-gradient-success text-center">
                    <div class="card-body p-24">
                        <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center 
                            bg-success-600 text-white mb-16 radius-12">
                            <iconify-icon icon="ri:truck-fill" class="h5 mb-0"></iconify-icon>
                        </div>
                        <h6 class="mb-16">Order & Shipping Details</h6>
                        <div class="text-start">
                            <?php if (!empty($orderbooks['details'])): ?>
                                <p><strong>Buyer's Order No:</strong> <?= esc($orderbooks['details']['vendor_po_order_number']) ?></p>
                                <p class="mb-2"><strong>Transport Payment:</strong> <?= esc($orderbooks['details']['transport_payment']) ?></p>

                                <p class="mb-2"><strong>Order Date:</strong>
                                    <?php if (!empty($orderbooks['details']['order_date'])): ?>
                                        <?= date('d-m-Y', strtotime($orderbooks['details']['order_date'])) ?>
                                    <?php endif; ?>
                                </p>

                                <p class="mb-2"><strong>Ship Date:</strong>
                                    <?php if (!empty($orderbooks['details']['ship_date'])): ?>
                                        <?= date('d-m-Y', strtotime($orderbooks['details']['ship_date'])) ?>
                                    <?php endif; ?>
                                </p>

                                <?php if (!empty($orderbooks['details']['tracking_url'])): ?>
                                    <p class="mb-0">
                                        <a href="<?= esc($orderbooks['details']['tracking_url']) ?>" target="_blank">
                                            <?= esc($orderbooks['details']['tracking_id']) ?>
                                        </a>
                                    </p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br><br>
        
        <div class="d-flex justify-content-end mb-3">
            <input type="text" id="tableSearch"
                class="form-control w-25"
                placeholder="Search...">
        </div>
        <table class="table table-bordered mb-4" id="booksTable">
            <thead>
                <h6 class="text-center">List of Books</h6><br>
                <tr>
                    <th>S.No</th> 
                    <th>BookId</th>  
                    <th>Title</th>
                    <th>PaperBack ISBN</th>
                    <th>Author</th>
                    <th>Quantity</th>
                    <th>Stock In Hand</th>
                    <th>Qty Details</th>
                    <th>Book Price</th>
                    <th>Discount %</th>
                    <th>Final amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody style="font-weight: normal;">
                <?php if (!empty($orderbooks['list'])): ?>
                    <?php
                        $totalValue = 0;
                        $i = 1;
                        foreach ($orderbooks['list'] as $books_details):
                            $totalValue += $books_details['total_amount'];
                            $formatted_isbn = str_replace('-', '', $books_details['paper_back_isbn']);
                    ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= esc($books_details['book_id']) ?></td>
                            <td>
                                <?= esc($books_details['book_title']) ?><br>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="feather feather-copy"
                                    onclick="copyToClipboard(this, '<?= addslashes($books_details['book_title']) ?>')">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                </svg>
                            </td>
                            <td>
                                <?= esc($formatted_isbn) ?><br>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="feather feather-copy"
                                    onclick="copyToClipboard(this, '<?= $formatted_isbn ?>')">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                </svg>
                            </td>
                            <td><?= esc($books_details['author_name']) ?></td>
                            <td><?= esc($books_details['quantity']) ?></td>
                            <td class="table-success"><?= esc($books_details['stock_in_hand']) ?></td>
                            <td class="table-warning">
                                Ledger: <?= esc($books_details['qty']) ?><br>
                                Fair / Store:
                                <?= ($books_details['bookfair']+$books_details['bookfair2']+$books_details['bookfair3']+$books_details['bookfair4']+$books_details['bookfair5']) ?><br>
                                <?php if ($books_details['lost_qty'] < 0): ?>
                                    <span style="color:green;">Excess: <?= abs($books_details['lost_qty']) ?></span>
                                <?php elseif ($books_details['lost_qty'] > 0): ?>
                                    <span style="color:red;">Lost: <?= $books_details['lost_qty'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($books_details['book_price']) ?></td>
                            <td><?= esc($books_details['discount']) ?></td>
                            <td><?= esc($books_details['total_amount']) ?></td>
                            <td>
                                <?php 
                                    if ($books_details['ship_status'] == 0) echo "In Progress";
                                    elseif ($books_details['ship_status'] == 1) echo "Shipped";
                                    elseif ($books_details['ship_status'] == 2) echo "Cancelled";
                                    elseif ($books_details['ship_status'] == 3) echo "Return";
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="10" style="text-align:right;font-weight:bold;color:blue;">
                            Total amount
                        </td>
                        <td style="font-weight:bold;color:blue;">
                            <?= number_format($totalValue, 2) ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="12" class="text-center text-danger">
                            ⚠ No books found for this order.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function copyToClipboard(icon, text) {
        navigator.clipboard.writeText(text);
        icon.style.color = "blue";
        setTimeout(function() {
            icon.style.color = "#000";
        }, 1000);
    }
    document.getElementById("tableSearch").addEventListener("keyup", function () {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll("#booksTable tbody tr");

    rows.forEach(function (row) {
        row.style.display = row.textContent.toLowerCase().includes(value)
            ? ""
            : "none";
    });
});
</script>

<?= $this->endSection(); ?>
