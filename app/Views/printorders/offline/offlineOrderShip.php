<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?> 
<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="page-header">
            <div class="page-title">
                <center><h6 class="text-center">Shipping and Tracking ID & Tracking URL</h6></center>
            </div>
        </div>
        <br>
        <div>
            <h6>Order Id: <?php echo $orderbooks['offline_order_id'] ?> </h6>
            <h6>Book Id: <?php echo $orderbooks['book_id'] ?> </h6>
            <h6>User Name: <?php echo $details['details']['customer_name']; ?></h6>
        </div>
        <br>
        <div class="row">
            <div class="col-md-6">
                <div class="card h-100 radius-12 bg-gradient-success">
                    <div class="card-header bg-gradient-success">
                        <h6>Shipping Address</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?php echo $details['details']['customer_name']; ?></p>
                        <p><strong>Address:</strong> <?php echo $details['details']['address']; ?></p>
                        <p><?php echo $details['details']['mobile_no']; ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 radius-12 bg-gradient-danger">
                    <div class="card-body">
                        <form id="shipForm">
                            <input type="hidden" class="form-control" id="book_id" name="book_id" value="<?php echo $orderbooks['book_id']; ?>">
                            <input type="hidden" class="form-control" id="offline_order_id" name="offline_order_id" value="<?php echo $orderbooks['offline_order_id']; ?>">

                            <div class="form-group">
                                <label for="tracking_id">Tracking Id</label>
                                <input type="text" class="form-control" id="tracking_id" name="tracking_id" value="<?php echo $orderbooks['tracking_id']; ?>">
                            </div>

                            <div class="form-group">
                                <label for="tracking_url">Tracking URL</label>
                                <input type="text" class="form-control" id="tracking_url" name="tracking_url" value="<?php echo $orderbooks['tracking_url']; ?>">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <center>
            <div class="field-wrapper">
                <button type="button" onclick="mark_ship('mail')" class="btn btn-success">
                    Ship with Mail
                </button>

                <button type="button" onclick="mark_ship('nomail')" class="btn btn-primary">
                    Ship without Mail
                </button>

                <a href="<?= base_url('paperback/offlineorderbooksstatus') ?>" class="btn btn-danger">
                    Cancel
                </a>
            </div>
        </center>
    </div>
</div>

<script>
var base_url = "<?= base_url(); ?>";

function mark_ship(ship_type){
    var offline_order_id = $('#offline_order_id').val();
    var book_id = $('#book_id').val();
    var tracking_id = $('#tracking_id').val();
    var tracking_url = $('#tracking_url').val();

    if (!offline_order_id || !book_id || !tracking_id || !tracking_url) {
        alert("Please fill all fields");
        return;
    }

    $.ajax({
        url: base_url + '/paperback/offlinemarkshipped',
        type: 'POST',
        data: {
            offline_order_id: offline_order_id,
            book_id: book_id,
            tracking_id: tracking_id,
            tracking_url: tracking_url,
            ship_type: ship_type 
        },
        success: function(res){
            if(res == 1){
                alert("Order shipped successfully!");
                window.location.href = base_url + "/paperback/offlineorderbooksstatus";
            }else{
                alert("Something went wrong!");
            }
        }
    });
}
</script>

<?= $this->endSection(); ?>
