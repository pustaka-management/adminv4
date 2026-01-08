<?= $this->extend('layout/layout1'); ?>

<?= $this->section('script'); ?>
<script>
    let table = new DataTable("#dataTable");

    function selectChannel(channel) {
        document.getElementById("channelBtn").innerText = channel;
        document.getElementById("sales_channel").value = channel;
    }

    function AddToBookList(book_id) {
        let selectedInput = document.getElementById('selected_book_list');
        let existingList = selectedInput.value;
        let updatedList = existingList ? book_id + ',' + existingList : book_id;
        selectedInput.value = updatedList;
    }
   function handleChannelChange(select) {
    let wrapper     = select.closest('.channel-wrapper');
    let textBox     = wrapper.querySelector('.channel-extra');
    let hiddenInput = wrapper.querySelector('input[name="sales_channel[]"]');
    let channel     = select.value;

    // Channels that need textbox
    let needsText = ["Book Fair", "Others"];

    if (needsText.includes(channel)) {
        textBox.classList.remove("d-none");
        textBox.addEventListener("input", function() {
            hiddenInput.value = "Book Fair - " + this.value;
        });
        hiddenInput.value = ""; // wait for user to type
    } else {
        textBox.classList.add("d-none");
        textBox.value = "";
        hiddenInput.value = channel;
    }
}
</script>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?> 

<div class="card basic-data-table">
    <div class="card-body">
        <h5 class="card-title mb-0"><i class="fas fa-book-open-reader me-2"></i>Publisher Selected Books List</h5>
    </div>

    <div class="card-body">
        <form action="<?= base_url('tppublisher/tppublisherorderpost') ?>" method="POST">
            <input type="hidden" name="num_of_books" value="<?= count($tppublisher_selected_books_data); ?>">
            <input type="hidden" name="selected_book_list" id="selected_book_list" value="<?= $tppublisher_selected_book_id; ?>">

            <?php if (!empty($author_id)): ?>
                <input type="hidden" name="author_id" value="<?= $author_id; ?>">
            <?php endif; ?>
            <?php if (!empty($publisher_id)): ?>
                <input type="hidden" name="publisher_id" value="<?= $publisher_id; ?>">
            <?php endif; ?>

            <div class="card-body p-4">
                <table class="zero-config table table-hover mt-4" id="dataTable" data-page-length="10"> 
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Sku No</th>
                            <th>Author Name</th>
                            <th>Title</th>
                            <th>Price</th>
                            <th>Pages</th>
                            <th>Stock In Hand</td>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
<?php foreach ($tppublisher_selected_books_data as $i => $book): ?>
<tr>
    <td><?= $i + 1 ?></td>
    <td><?= $book['sku_no'] ?></td>
    <td><?= esc($book['author_name']) ?></td>
    <td><?= esc($book['book_title']) ?></td>
    <td>
        <?= esc($book['price']) ?>
        <input type="hidden" name="price<?= $i + 1 ?>" value="<?= esc($book['price']) ?>">
    </td>
    <td><?= esc($book['number_of_page']) ?></td>
    <td><?= esc($book['stock_in_hand'] ?? '-') ?></td>
    <td>
        <input type="number" name="bk_qty<?= $i + 1 ?>" class="form-control form-control-sm" placeholder="0" required>
    </td>
   
</tr>
<?php endforeach; ?>
<td>
    <select class="form-select form-select-sm" onchange="handleChannelChange(this)" required style="width:250px";>
        <option value="">Select Channel</option>
        <option value="Pustaka">Pustaka</option>
        <option value="Book Fair">Book Fair</option>
        <option value="Amazon">Amazon</option>
        <option value="Others">Others</option>
    </select>

    <!-- Book Fair textbox (hidden by default) -->
    <input type="text" class="form-control form-control-sm mt-2 d-none"
           placeholder="(eg)ChennaiJune2025">

        <!-- Hidden input that will store final value -->
        <input type="hidden" name="sales_channel[]" value="">
    </div>
</td>

</div>
</tbody>

                </table>
            </div>
            <!-- Buttons -->
            <div class="d-flex justify-content-end px-4 pb-4">
                <button type="submit" class="btn btn-success me-2">
                    <i class="fas fa-arrow-right me-2"></i>Next
                </button>
                <a href="<?= base_url('tppublisherdashboard/tppublisherdashboard') ?>" class="btn btn-danger">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>

