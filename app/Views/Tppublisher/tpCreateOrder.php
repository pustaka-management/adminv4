<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<h6 class="card-title mb-4 d-flex justify-content-between align-items-center">
    Selected Titles
    <button type="button" class="btn btn-sm btn-primary" onclick="AddAllBooks()">Select All</button>
</h6>

<div class="card-body">
    <table class="zero-config table table-hover mt-4" id="dataTable" data-page-length="10">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Sku No</th>
                <th>Book Title</th>
                <th>Rate</th>
                <th>Stock in Hand</th>
                <th>Add to Cart</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($books)): ?>
                <?php 
                    $i = 1; 
                    $availableSkus = [];
                    $zeroStockSkus = [];
                ?>
                <?php foreach ($books as $row): ?>
                    <?php
                        $sku = trim($row['sku_no']);
                        $available = (int)($row['available_stock'] ?? 0);
                        $rawStock = (int)($row['raw_stock'] ?? 0);
                        
                        if (!empty($sku)) {
                            $availableSkus[] = $sku;
                            
                            if ($available <= 0) {
                                $zeroStockSkus[] = $sku;
                            }
                        }
                    ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td class="sku-no"><?= esc($sku) ?></td>
                        <td><?= esc($row['book_title']) ?></td>
                        <td><?= esc($row['mrp']) ?></td>
                        <td class="stock-cell">
                            <?php 
                                if ($available < 0) {
                                    echo '<span class="badge bg-danger">' . esc($available) . ' (Negative)</span>';
                                } elseif ($available == 0) {
                                    echo '<span class="badge bg-warning">0 (Zero Stock)</span>';
                                } else {
                                    echo '<span class="badge bg-success">' . esc($available) . '</span>';
                                }
                            ?>
                        </td>
                        <td class="text-center">
                            <button 
                                onclick="AddToBookList('<?= esc($sku) ?>')" 
                                class="w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center add-to-cart-btn"
                                title="Add to Cart"
                                data-sku="<?= esc($sku) ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M7 18c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm10 0c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm-12.83-2l1.716-8h13.114l1.716 8h-16.546zm-1.17-10h18v2h-18v-2z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">No records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Hidden fields for JS -->
    <input type="hidden" id="all_available_skus" value="<?= implode(',', $availableSkus ?? []); ?>">
    <input type="hidden" id="zero_stock_skus" value="<?= implode(',', $zeroStockSkus ?? []); ?>">

    <!-- Selected Book List Form -->
    <div class="mt-4">
        <form class="text-left" action="<?= base_url('tppublisher/tppublishersorder') ?>" method="POST">
            <input type="hidden" name="publisher_id" value="<?= esc($publisher_id); ?>">
            <input type="hidden" name="author_id" value="<?= esc($author_id); ?>">
            <div class="form-group">
                <label for="selected_book_list">Selected Books:</label>
                <input type="text" id="selected_book_list" name="selected_book_list" 
                       class="form-control" placeholder="Selected Book Lists" required readonly>
                <small class="form-text text-muted">Selected SKUs: <span id="selected-count">0</span></small>
            </div>
            
            <!-- <div class="alert alert-warning mt-2">
                <i class="ri-alert-line"></i> Note: Orders can be created even for books with zero stock.
            </div>
             -->
            <div class="d-flex justify-content-center gap-3 mt-3">
                <button type="submit" class="btn rounded-pill btn-outline-success-600 radius-8 px-20 py-11">
                    Next
                </button>

                <a href="<?= base_url('tppublisher/tppublisherdashboard') ?>" 
                   class="btn rounded-pill btn-outline-danger-600 radius-8 px-20 py-11">
                   Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    $.fn.dataTable.ext.errMode = 'none'; 
    new DataTable("#dataTable");
    updateSelectedCount();
});

function AddToBookList(sku_no) {
    const inputField = document.getElementById('selected_book_list');
    let bookList = inputField.value.split(',').map(id => id.trim()).filter(id => id !== '');

    if (!bookList.includes(String(sku_no))) {
        bookList.push(sku_no);
        inputField.value = bookList.join(',');
        updateSelectedCount();
    }
}

function AddAllBooks() {
    const inputField = document.getElementById('selected_book_list');
    const allAvailableSkus = document.getElementById('all_available_skus').value.split(',').map(s => s.trim()).filter(s => s !== '');
    const zeroStockSkus = document.getElementById('zero_stock_skus').value.split(',').map(s => s.trim()).filter(s => s !== '');
    
    let currentList = inputField.value.split(',').map(id => id.trim()).filter(id => id !== '');
    
    allAvailableSkus.forEach(sku => {
        if (!currentList.includes(sku)) {
            currentList.push(sku);
        }
    });

    inputField.value = currentList.join(',');
    updateSelectedCount();

    let message = `${allAvailableSkus.length} books added to selection!`;
    if (zeroStockSkus.length > 0) {
        message += `\n${zeroStockSkus.length} books have zero stock. Orders can still be created for these books.`;
    }

    alert(message);
}

function updateSelectedCount() {
    const inputField = document.getElementById('selected_book_list');
    const bookList = inputField.value.split(',').map(id => id.trim()).filter(id => id !== '');
    document.getElementById('selected-count').textContent = bookList.length;
}
</script>
<?= $this->endSection(); ?>