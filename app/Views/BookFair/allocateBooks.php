<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container mt-4">

    <h6 class="mb-4">Bookfair Allocation</h6>

    <!-- Bookfair Name -->
    <div class="card p-3 mb-4">
        <label class="form-label fw-bold">Bookfair Name</label>
        <input type="text" id="global_bookfair" class="form-control" placeholder="Enter Bookfair Name">
    </div>

    <!-- Base Books Table -->
<table class="zero-config table table-hover mt-3">
    <thead>
        <tr>
            <th>Book ID</th>
            <th>Book Title</th>
            <th>Author</th>
            <th>Stock</th>
            <th>Add</th>
        </tr>
    </thead>
    <tbody>

        <?php
        // Sort by priority_code: High → Medium → Others
        usort($books, function($x, $y) {
            return ($x['priority_code'] ?? 99) <=> ($y['priority_code'] ?? 99);
        });
        ?>

        <?php foreach ($books as $b): ?>
            <tr id="row-<?= $b['book_id'] ?>">
                <td><?= $b['book_id'] ?></td>
                <td><?= esc($b['book_title']) ?></td>
                <td><?= esc($b['author_name']) ?></td>
                <td><?= $b['stock_in_hand'] ?></td>
                <td>
                    <button class="btn btn-primary btn-sm"
                        onclick="addToList(
                            <?= $b['book_id'] ?>,
                            '<?= esc($b['book_title']) ?>',
                            <?= $b['author_id'] ?>,
                            '<?= esc($b['author_name']) ?>',
                            <?= $b['stock_in_hand'] ?>
                        )">
                        Add
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>

    </tbody>
</table>


    <!-- Selected Books Section -->
    <div id="allocationSection" class="mt-5 d-none">

        <h4 class="mb-3">Selected Books</h4>

        <!-- GLOBAL QTY INPUT -->
        <div class="mb-3">
            <label class="fw-bold">Apply Quantity to All</label>
            <input type="number" id="global_qty" class="form-control" 
                placeholder="Enter Qty for all books"
                min="1" oninput="applyQtyToAll(this.value)">
        </div>

       <table class="zero-config table table-hover mt-3">
            <thead class="table-secondary">
                <tr>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Stock In Hand</th>
                    <th>Qty</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody id="allocationList"></tbody>
        </table>

        <button class="btn btn-success mt-3" onclick="finalSubmit()">Submit Allocation</button>
    </div>

</div>


<script>
let selectedBooks = [];

function addToList(book_id, title, author_id, author_name, stock) {
    let bookfair = document.getElementById('global_bookfair').value.trim();
    if (bookfair === "") {
        alert("Please enter Bookfair Name first!");
        return;
    }

    document.getElementById("allocationSection").classList.remove("d-none");

    let exists = selectedBooks.find(b => b.book_id == book_id);
    if (exists) {
        alert("This book already added!");
        return;
    }

    selectedBooks.push({
        book_id,
        title,
        author_id,
        author_name,
        stock_in_hand: stock,
        qty: ""
    });

    renderList();
}

function renderList() {
    let html = "";

    selectedBooks.forEach((b, i) => {
        let qtyValue = b.qty ? b.qty : "";

        html += `
            <tr>
                <td>${b.book_id}</td>
                <td>${b.title}</td>
                <td>${b.author_name}</td>
                <td>${b.stock_in_hand}</td>

                <td>
                    <input type="number" class="form-control"
                        min="1"
                        value="${qtyValue}"
                        oninput="updateQty(${i}, this.value)">
                </td>

                <td>
                    <button class="btn btn-danger btn-sm" onclick="removeRow(${i})">X</button>
                </td>
            </tr>
        `;
    });

    document.getElementById("allocationList").innerHTML = html;
}

function updateQty(index, qty) {
    selectedBooks[index].qty = qty;
}

// APPLY SAME QTY TO ALL SELECTED BOOKS
function applyQtyToAll(qty) {
    qty = parseInt(qty);
    if (!qty || qty <= 0) return;

    selectedBooks = selectedBooks.map(b => ({
        ...b,
        qty: qty
    }));

    renderList();
}

function removeRow(index) {
    selectedBooks.splice(index, 1);
    renderList();

    if (selectedBooks.length === 0) {
        document.getElementById("allocationSection").classList.add("d-none");
    }
}

function finalSubmit() {
    let bookfair = document.getElementById('global_bookfair').value.trim();

    if (bookfair === "") {
        alert("Enter Bookfair Name");
        return;
    }

    for (let b of selectedBooks) {
        if (!b.qty || b.qty <= 0) {
            alert("Enter qty for all books");
            return;
        }
    }

    let payload = {
        books: selectedBooks.map(b => ({
            book_id: b.book_id,
            author_id: b.author_id,
            qty: b.qty,
            bookfair_name: bookfair
        }))
    };

    fetch("<?= base_url('stock/saveallocation') ?>", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(r => {
        if (r.status === "success") {
            alert("Saved Successfully!");
            location.reload();
        } else {
            alert("Saving Error!");
        }
    })
    .catch(() => alert("Network Error"));
}
</script>

<?= $this->endSection(); ?>
