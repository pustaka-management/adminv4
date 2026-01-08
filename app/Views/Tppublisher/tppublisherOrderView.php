<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="card basic-data-table mb-5">
    <div class="card-body">
        <form action="<?= base_url('tppublisher/tppublisherordersubmit') ?>" method="post">
            <?= csrf_field() ?>
            
            <!-- Hidden Inputs -->
            <input type="hidden" name="num_of_books" value="<?= is_array($tppublisher_order) ? count($tppublisher_order) : 0; ?>">
            <input type="hidden" name="selected_book_list" value="<?= esc($tppublisher_selected_book_id); ?>">
            <input type="hidden" name="publisher_id" value="<?= esc($publisher_id) ?>">
            <input type="hidden" name="author_id" value="<?= esc($author_id) ?>">
            <input type="hidden" name="paid_status" value="paid">

            <?php 
                $i = 1;
                $grand_total = 0;
                $grand_discount = 0;
                $grand_final = 0;
                $grand_author_share = 0;
                $total_quantity = 0;
            ?>

            <div class="card-body p-4">
                <table class="zero-config table table-hover mt-4" id="dataTable" data-page-length="10">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Sku No</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Discounted</th>
                            <th>Author Share</th>
                            <th>Channel</th>
                            <th>Stock Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tppublisher_order as $j => $orders):
                            $quantity = isset($book_qtys[$j]) ? (int) $book_qtys[$j] : 0;
                            $price = isset($book_prices[$j]) ? (float) $book_prices[$j] : 0;
                            $channel = isset($sales_channel[$j]) ? esc($sales_channel[$j]) : '';

                            $total = $quantity * $price;
                            $discount = $total * 0.40;
                            $final_total = $total - $discount;
                            $author_share = $total * 0.60; 

                            $grand_total += $total;
                            $grand_discount += $discount;
                            $grand_final += $final_total;
                            $grand_author_share += $author_share;
                            $total_quantity += $quantity; 

                            $stock_in_hand = $orders['stock_in_hand'] ?? 0;
                            $stockStatus = $quantity <= $stock_in_hand ? 'IN STOCK' : 'OUT OF STOCK';
                            $rowClass = $stockStatus === 'IN STOCK' ? 'bg-success-light' : 'bg-danger-light';
                        ?>
                        <tr class="<?= $rowClass ?>">
                            <td><?= $i++ ?></td>
                            <td><?= esc($orders['sku_no']) ?></td>
                            <td><?= esc($orders['book_title']) ?></td>
                            <td><?= esc($orders['author_name']) ?></td>
                            <td><?= $quantity ?></td>
                            <td><?= indian_format($price, 2) ?></td>
                            <td><?= indian_format($total, 2) ?></td>
                            <td><?= indian_format($discount, 2) ?></td>
                            <td><?= indian_format($author_share, 2) ?></td>
                            <td><?= esc($sales_channel) ?></td>
                            <input type="hidden" name="sales_channel<?= $j ?>" value="<?= esc($sales_channel) ?>">
                            <td><?= $stockStatus ?></td>

                            <!-- Hidden Inputs for Submission -->
                            <input type="hidden" name="book_ids[]" value="<?= esc($orders['book_id']) ?>">
                            <input type="hidden" name="qtys[]" value="<?= $quantity ?>">
                            <input type="hidden" name="mrps[]" value="<?= $price ?>">
                            <input type="hidden" name="sales_channel[]" value="<?= esc($sales_channel) ?>">
                        </tr>
                        <?php endforeach; ?>

                        <tr class="bg-warning-light fw-bold">
                            <td colspan="4" class="text-end">Total Quantity</td>
                            <td><?= $total_quantity ?></td>
                            <td class="text-end">Total Order Amount</td>
                            <td><?= indian_format($grand_total, 2) ?></td>
                            <td><?= indian_format($grand_discount, 2) ?></td>
                            <td><?= indian_format($grand_author_share, 2) ?></td>
                            <td colspan="3"></td>
                        </tr>
                        <tr class="bg-success-light fw-bold">
                            <td colspan="6" class="text-end">Final Payable Amount</td>
                            <td colspan="2"><?= indian_format($grand_final, 2) ?></td>
                            <td colspan="3"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Grand Totals for Submission -->
            <input type="hidden" name="author_amount" value="<?= indian_format($grand_author_share, 2, '.', '') ?>">
            <input type="hidden" name="final_amount" value="<?= indian_format($grand_final, 2, '.', '') ?>">
            <input type="hidden" name="paid_status" id="paid_status" value="pending">

            <div class="d-flex justify-content-center gap-3 mt-4">
                <button type="submit" class="btn btn-success">Confirm Order</button>
                <a href="<?= base_url('tppublisher/tpsalesadd') ?>" class="btn btn-danger">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>
