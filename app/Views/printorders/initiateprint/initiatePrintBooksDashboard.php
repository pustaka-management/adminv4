<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>
<div id="content" class="main-content">
	<div class="layout-px-spacing">
		<div class="page-header">
			<div class="page-title">
			</div>
		</div>
		<div class="page-header">
			<div class="page-title">
				<h6 class="text-center">Pustaka Paperback - Books List Selection</h6>
			</div>
		</div>
		<table class="zero-config table table-hover mt-4">
        	<thead>
                <th>S.No</th>
				<th>Book ID</th>
				<th>Title</th>
				<th>Regional Title</th>
				<th>Author</th>
				<th>Pustaka Paperback Cost</th>
				<th>PaperBack Pages</th>
				<th class="text-center">Actions</th>
			</thead>
			<tbody>
            <?php 
			$i=1;
			foreach($paperback_books['paperback_book'] as $orders) {?>
						<tr>
						            <td><?php echo $i++; ?></td>
									<td><?php echo $orders['book_id'] ?></a></td>
									<td><?php echo $orders['book_title'] ?></a></td>
									<td><?php echo $orders['regional_book_title'] ?></td>
									<td><?php echo $orders['author_name'] ?></td>
									<td><?php echo $orders['paper_back_inr'] ?></td>
									<td><?php echo $orders['number_of_page']?></td>
									<td class="text-center">
									<input type="button" 
									onclick="AddToBookList(<?php echo $orders['book_id']; ?>)" 
									value="Add" 
									style="background-color: #28a745; color: #fff; border-radius: 8px; padding: 6px 12px; border: none; font-size: 14px;">
									</td>
							</tr>
					<?php } ?>	
			</tbody>
		</table>
		<br><br>
		<div class="form-container outer">
			<div class="form-form">
				<div class="form-form-wrap">
					<div class="form-container">
						<div class="form-content">
							<form class="text-left" action="<?php echo base_url().'paperback/initiateprintbookslist'?>" target="_blank" method="POST">
								<div class="form">
									<div id="email-field" class="field-wrapper input">
										<label for="email">Selected Books:</label>
										<input id="selected_book_list" name="selected_book_list" class="form-control" placeholder="Selected Book Lists" required>
									</div>
									<br>
									<div class="d-sm-flex justify-content-between">
										<div class="field-wrapper">
											<button style="background-color: #77B748 !important; border-color: #77B748 !important;" type="submit" class="btn btn-primary" value="">Next</button>
										</div>
									</div>
								</div>
							</form>

						</div>                    
					</div>
				</div>
			</div>
		</div>
		<div class="page-header">
			<div class="page-title">
				<br>
				<a href="<?php echo base_url()."paperback/onlineorderbooksstatus" ?>" class="btn btn-danger">Cancel</a>
				<br><br>
			</div>
		</div>


	<br><br>
</div>
<script type="text/javascript">
    var base_url = window.location.origin ;
    function AddToBookList(book_id) {
		var existing_book_list = document.getElementById('selected_book_list').value;
		if (existing_book_list)
			document.getElementById('selected_book_list').value = book_id + ',' + existing_book_list;
		else
			document.getElementById('selected_book_list').value = book_id;
    }
</script>
<?= $this->endSection(); ?>