<style>
	button, .buttons, .btn, .modal-footer .btn+.btn {
		margin-bottom: 5px;
		margin-right: 0px;
	}
</style>


<div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12">
	<form id="category-creation-form" >
		<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
		<input type="hidden" name="page" value="details"/>
		<div class="row">
			<div class="category_creation_panel1 col-md-12">
				<div class="x_panel tile has-shadow">
					<legend>Create New Category <span class="pull-right"><a href="<?php echo base_url('webapp/audit/categories/'); ?>"><i class="fas fa-list"></i> Evidoc Categories List</a></span></legend>
					<div class="input-group form-group">
						<label class="input-group-addon">Category Name</label>
						<select id="category_name" name="category_name" class="form-control required">
							<option value="">Please select Category</option>
							<?php if (!empty($disciplines)) {
							    foreach ($disciplines as $k => $discipline) { ?>
								<option value="<?php echo $discipline->account_discipline_name; ?>" ><?php echo $discipline->account_discipline_name; ?></option>
							<?php }
							    } ?>
						</select>
					</div>
					
					<div class="form-group">
						<div class="input-group">
							<label class="input-group-addon">Category Description</label>
							<textarea id="description" name="description" type="text" class="form-control" rows="3"></textarea>     
						</div>
					</div>
					
					<hr>
					<div class="row form-group">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button id="create-category-btn" class="btn btn-sm btn-success btn-next" type="button" >Create Evidoc Category</button>					
						</div>
					</div>
				</div>						
			</div>	

		</div>
	</form>
</div>

<script>
	$( document ).ready( function(){
	
		//Submit category form
		$( '#create-category-btn' ).click(function( e ){
			e.preventDefault();
			var formData = $('#category-creation-form').serialize();
			
			swal({
				title: 'Confirm new Category creation?',
				showCancelButton: 	true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: 	'#9D1919',
				confirmButtonText: 	'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/audit/add_category/'); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 && ( data.category !== '' ) ){
								
								var newCategoryId = data.category.category_id;
								
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){ 
									location.reload();
									//location.href = "<?php echo base_url('webapp/audit/manage_categories/'); ?>"+newCategoryId;
								} ,3000);							
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}		
						}
					});
				}else{
					return false;
				}
			}).catch( swal.noop )
		});
		
	});
</script>

