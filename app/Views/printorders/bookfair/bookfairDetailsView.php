<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<?php if(session()->getFlashdata('success')): ?>
<script>
    alert("<?= session()->getFlashdata('success'); ?>");
    setTimeout(function(){
        window.location.href = "<?= base_url('combobookfair/bookfairbookshoppendingorders'); ?>";
    }, 3000);
</script>
<?php endif; ?>

<a href="<?= base_url('combobookfair/bookfairbookshoppendingorders'); ?>" 
   class="btn btn-outline-secondary btn-sm float-end">
    ← Back
</a>

<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="row g-4">

            <?php if (!empty($bookfair_details['bookfair'])): ?>

                <?php 
                    $bookfair = $bookfair_details['bookfair'][0];
                    $combo = $bookfair_details['bookfair_combo'][0] ?? null;
                ?>

                <!-- Book Fair Details -->
                <div class="col-md-6">
                    <div class="card h-100 radius-12 bg-gradient-purple text-center">
                        <div class="card-body p-24">

                            <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center 
                                bg-lilac-600 text-white mb-16 radius-12">
                                <iconify-icon icon="ri:book-open-fill" class="h5 mb-0"></iconify-icon>
                            </div>

                            <h6 class="mb-16">Book Fair Details</h6>

                            <div class="text-start d-inline-block">
                                <p class="mb-2">
                                    <strong>Book Fair Name:</strong>
                                    <?= esc($bookfair['book_fair_name']); ?>
                                </p>

                                <p class="mb-2">
                                    <strong>Contact Person:</strong>
                                    <?= esc($bookfair['contact_person_name']); ?>
                                </p>

                                <p class="mb-2">
                                    <strong>Mobile No:</strong>
                                    <?= esc($bookfair['mobile']); ?>
                                </p>

                                <p class="mb-0">
                                    <strong>Address:</strong>
                                    <?= esc($bookfair['address']); ?>
                                </p>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Order & Combo Details -->
                <div class="col-md-6">
                    <div class="card h-100 radius-12 bg-gradient-success text-center">
                        <div class="card-body p-24">

                            <div class="w-64-px h-64-px d-inline-flex align-items-center justify-content-center 
                                bg-success-600 text-white mb-16 radius-12">
                                <iconify-icon icon="ri:truck-fill" class="h5 mb-0"></iconify-icon>
                            </div>

                            <h6 class="mb-16">Order & Combo Details</h6>

                            <?php if ($combo): ?>
                                <div class="text-start d-inline-block w-100">
                                    <p class="mb-2">
                                        <strong>Combo Pack Name:</strong>
                                        <?= esc($combo['pack_name']); ?>
                                    </p>

                                    <p class="mb-2">
                                        <strong>Transport Name:</strong>
                                        <?= esc($combo['preferred_transport_name']); ?>
                                    </p>

                                    <p class="mb-2">
                                        <strong>Create Date:</strong>
                                        <?= !empty($combo['create_date']) 
                                            ? date('d-m-Y', strtotime(explode(' ', $combo['create_date'])[0])) 
                                            : '-' ?>
                                    </p>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-start">No combo details found.</p>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

                <br><br><br>

                <!-- Remarks -->
                <?php if (!empty($combo['remark'])): ?>
                <div class="d-flex justify-content-center">
                    <div class="col-lg-4 col-sm-6">
                        <div class="p-16 bg-warning-50 radius-8 
                            border-start-width-3-px border-warning-main border-top-0 border-end-0 border-bottom-0">
                            <h6 class="text-primary-light text-md mb-8">Remarks</h6>
                            <span class="text-success-main mb-0">
                                <?= esc($combo['remark']); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <br><br><br>

                <!-- Book List Table -->
                <h6 class="text-center">List of Books</h6><br>
                 <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="<?= base_url('combobookfair/downloadbookfairexcel/'.$order_id); ?>"
                        class="btn btn-success btn-sm">
                        ⬇ Download Excel
                    </a>
                </div>

                <table class="table table-bordered mb-4" id="bookfairTable">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Book ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Language</th>
                            <th>Send QTY</th>
                            <th>Book Price</th>
                            <th>Create Date</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>

                    <tbody style="font-weight: normal;">
                        <?php if (!empty($bookfair_details['bookfair_details'])): ?>
                            <?php
                                $i = 1;
                                $grandTotal = 0;
                                foreach ($bookfair_details['bookfair_details'] as $row):
                                    $total = $row['send_qty'] * $row['book_price'];
                                    $grandTotal += $total;
                            ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= esc($row['book_id']) ?></td>
                                    <td><?= esc($row['book_title']) ?></td>
                                    <td><?= esc($row['author_name']) ?></td>
                                    <td><?= esc($row['language_name']) ?></td>
                                    <td><?= esc($row['send_qty']) ?></td>
                                    <td><?= number_format($row['book_price'], 2) ?></td>
                                    <td><?= date('d-m-Y', strtotime($row['create_date'])) ?></td>
                                    <td><?= number_format($total, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>

                            <tr>
                                <td colspan="8" class="text-end fw-bold text-primary">
                                    Grand Total
                                </td>
                                <td class="fw-bold text-primary">
                                    <?= number_format($grandTotal, 2) ?>
                                </td>
                            </tr>

                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-danger">
                                    ⚠ No book fair details found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Actions -->
                <div class="d-flex justify-content-center mt-4 mb-5">
                    <a href="<?= base_url('combobookfair/ship/'.$order_id); ?>"
                       onclick="return confirm('Are you sure you want to ship this order?');"
                       class="btn btn-outline-success-600 radius-8 px-20 py-11 me-3">
                        Ship
                    </a>

                    <a href="<?= base_url('combobookfair/bookfairbookshoppendingorders'); ?>"
                       class="btn btn-outline-danger-600 radius-8 px-20 py-11">
                        Cancel
                    </a>
                </div>

            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        No book fair details found for this order.
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?= $this->endSection(); ?>