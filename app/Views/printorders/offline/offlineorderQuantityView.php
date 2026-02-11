<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?> 
<div class="radius-8 h-100 text-center p-20 bg-purple-light">
    <form action="<?php echo base_url().'paperback/offlineorderbookssubmit'?>" method="POST">
        <input type="hidden" value="<?php echo count($offline_paperback_stock); ?>" name="num_of_books">
        <input type="hidden" value="<?php echo $offline_selected_book_id; ?>" name="selected_book_list">
        <input type="hidden" name="ship_date" value="<?php echo $ship_date; ?>">
        <input type="hidden" name="courier_charges" value="<?php echo $courier_charges; ?>">
        <input type="hidden" name="payment_type" value="<?php echo $payment_type; ?>">
        <input type="hidden" name="payment_status" value="<?php echo $payment_status; ?>">
        <input type="hidden" name="customer_name" value="<?php echo $customer_name; ?>">
        <input type="hidden" name="address" value="<?php echo $address; ?>">
        <input type="hidden" name="mobile_no" value="<?php echo $mobile_no; ?>">
        <input type="hidden" name="email" value="<?php echo $email; ?>">
        <input type="hidden" name="remarks" value="<?php echo $remarks; ?>">
        <input type="hidden" name="city" value="<?php echo $city; ?>">

        <table class="table table-hover mt-4"> 
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author Name</th>
                    <th>Quantity</th>
                    <th>Stock Status</th>
                    <th>In Progress</th>
                    <th>Stock In Hand</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                $j = 0;
                foreach ($offline_paperback_stock as $orders) {
                    $quantity_details = $book_qtys[$j];
                    $book_discount = $book_dis[$j];
                    $total_amount = $tot_amt[$j];
                ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                            <input type="text" class="form-control" value="<?php echo $orders['bookID'] ?>" name="book_id<?php echo $j; ?>">
                        </td>
                        <td><?php echo $orders['book_title']; ?></td>
                        <td><?php echo $orders['author_name']; ?></td>
                        <td>
                            <input type="hidden" name="bk_qty<?php echo $j; ?>" value="<?php echo $quantity_details; ?>">
                            <?php echo $quantity_details; ?>
                        </td>
                        <input type="hidden" name="book_dis<?php echo $j; ?>" value="<?php echo $book_discount; ?>">
                        <input type="hidden" name="tot_amt<?php echo $j; ?>" value="<?php echo $total_amount; ?>">
                        <?php
                        $stockStatus = $quantity_details <= $orders['stock_in_hand'] ? 'IN STOCK' : 'OUT OF STOCK';
                        ?>
                        <td>
                            <?php echo $stockStatus; ?><br>
                            <?php if ($orders['paper_back_readiness_flag'] == 1) { ?>
                                <?php if ($stockStatus == 'OUT OF STOCK') { ?>
                                    <a href="<?php echo base_url() . "paperback/initiateprintdashboard/" . $orders['bookID']; ?>" class="btn btn-warning mb-1 mr-1" target="_blank">Initiate Print</a>
                                <?php } ?> 
                            <?php } else { ?>
                                <a href="<?php echo base_url() . "paperback/initiateindesigndashboard/" . $orders['bookID']; ?>" class="btn btn-info mb-1 mr-1" target="_blank">Initiate Indesign</a>
                            <?php } ?>  
                        </td>
                        <td>
                            <?php if ($orders['paper_back_readiness_flag'] == 1) { ?>
                                <?php if ($stockStatus == 'OUT OF STOCK') { ?>
                                    <?php echo $orders['Qty']; ?>
                                <?php } ?> 
                            <?php } else { ?>
                                <?php echo $orders['indesign_status']; ?>
                            <?php } ?>  
                        </td>
                        <td><center><?php echo $orders['stock_in_hand']; ?></center></td>
                    </tr>   
                <?php $j++; } ?>    
            </tbody>
        </table>

        <br>
        <div class="radius-8 h-100 text-center p-20 bg-danger-light">
            <button style="background-color: #4f209bff; border-color: #9b48b7ff;" type="submit" class="btn btn-primary">Submit</button>
            <a href="<?php echo base_url()."paperback/offlineorderbooksdashboard"  ?>" class="btn btn-danger ml-2">Cancel</a>
        </div>
    </form>
</div>
<?= $this->endSection(); ?>

