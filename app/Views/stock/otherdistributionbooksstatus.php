<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div id="content" class="main-content">
    <div class="layout-px-spacing">
        
        <div class="page-header">
            <div class="page-title row">
                <div class="col">
                    <h6 class="text-center">OtherDistribution Books Status Dashboard</h6>
                </div>
                <div class="col-3">
                    <a href="<?= base_url('stock/otherdistribution'); ?>" 
                       class="btn btn-info mb-2 mr-2" target="_blank">
                        Free Books Print
                    </a>
                </div>
            </div>
        </div>

        <br>

        <!-- Not Started Table -->
        <table class="zero-config table table-hover mt-4">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Created Date</th>
                    <th>Book Id</th>
                    <th>Title</th>
                    <th>Author Name</th>
                    <th>Copies</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody style="font-weight: normal;">
                <?php $i = 1; foreach ($print['book_not_start'] as $print_book) { ?>
                <tr>
                    <td><?= $i++; ?></td>

                    <td><?= date('d-m-Y', strtotime($print_book['order_date'])); ?></td>

                    <input type="hidden" id="id" name="id" 
                           value="<?= $print_book['id']; ?>">

                    <td><?= $print_book['book_id']; ?></td>
                    <td><?= $print_book['book_title']; ?></td>
                    <td><?= $print_book['author_name']; ?></td>
                    <td><?= $print_book['quantity']; ?></td>

                    <td>
                        <button class="btn btn-warning" 
                                onclick="mark_complete(<?= $print_book['id']; ?>)">
                            Send
                        </button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <br><br>

        <h5 class="text-center">
            Completed Books List
            <a href="<?= base_url('stock/totalfreebookscompleted'); ?>" 
               class="bs-tooltip" title="View all Completed Books" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                     viewBox="0 0 24 24" fill="none" stroke="blue" stroke-width="2" 
                     stroke-linecap="round" stroke-linejoin="round" 
                     class="feather feather-external-link">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                    <polyline points="15 3 21 3 21 9"></polyline>
                    <line x1="10" y1="14" x2="21" y2="3"></line>
                </svg>
            </a>
        </h5>

        <h6 class="text-center">(Shows for 30 days from date of completion)</h6>

        <!-- Completed Table -->
        <table class="zero-config table table-hover mt-4">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Created Date</th>
                    <th>Book Id</th>
                    <th>Title</th>
                    <th>Author Name</th>
                    <th>Copies</th>
                    <th>Completed Date</th>
                </tr>
            </thead>

            <tbody style="font-weight: normal;">
                <?php $i = 1; foreach ($print['completed'] as $print_book) { ?>
                <tr>
                    <td><?= $i++; ?></td>

                    <td><?= date('d-m-Y', strtotime($print_book['order_date'])); ?></td>

                    <input type="hidden" id="id" name="id" 
                           value="<?= $print_book['id']; ?>">

                    <td><?= $print_book['book_id']; ?></td>
                    <td><?= $print_book['book_title']; ?></td>
                    <td><?= $print_book['author_name']; ?></td>
                    <td><?= $print_book['quantity']; ?></td>

                    <td><?= date('d-m-Y', strtotime($print_book['ship_date'])); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>
</div>

<script type="text/javascript">
    var base_url = "<?= base_url(); ?>";

    function mark_complete(id) {
        $.ajax({
            url: base_url + 'stock/freemarkcompleted',
            type: 'POST',
            data: { id: id },
            dataType: "json",

            success: function(response) {
                if (response.status == 1 || response.status == "1") {
                    alert("Successfully completed!");
                    location.reload();
                } else {
                    alert("Unknown error! Try again.");
                }
            },

            error: function(xhr, status, error) {
                alert("AJAX Error: " + error);
                console.log(xhr.responseText);
            }
        });
    }
</script>

<?= $this->endSection(); ?>
