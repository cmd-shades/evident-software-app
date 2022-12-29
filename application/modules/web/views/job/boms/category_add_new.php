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
			<div class="bom_category_creation_panel1 col-md-12">
				<div class="x_panel tile has-shadow">
					<legend>Create New BOM Category <span class="pull-right"><a href="<?php echo base_url('webapp/job/bom_categories/'); ?>"><i class="fas fa-list"></i> BOM Categories List</a></span></legend>
					<div class="input-group form-group">
						<label class="input-group-addon">Category Name</label>
						<input id="bom_category_name" name="bom_category_name" class="form-control" type="text" placeholder="Category" value="" />
					</div>
					
					<div class="form-group">
						<div class="input-group">
							<label class="input-group-addon">Category Description</label>
							<textarea id="description" name="bom_category_description" type="text" class="form-control" rows="3" placeholder="BOM Category Description" ></textarea>     
						</div>
					</div>

					<hr>
					<div class="row form-group">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button id="create-bom-category-btn" class="btn btn-sm btn-success btn-next" type="button" >Create BOM Category</button>					
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
		$( '#create-bom-category-btn' ).click(function( e ){
			e.preventDefault();
			var formData = $('#category-creation-form').serialize();
			
			swal({
				title: 'Confirm new BOM Category creation?',
				showCancelButton: 	true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: 	'#9D1919',
				confirmButtonText: 	'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/add_bom_category/'); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 && ( data.bom_category !== '' ) ){
								
								var newCategoryId = data.bom_category.bom_category_id;
								
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

