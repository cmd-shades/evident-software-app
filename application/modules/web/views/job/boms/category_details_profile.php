<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row alert alert-ssid bg-blue" role="alert">
					<div class="profile-overview">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<legend>BOM Category Profile <span class="pull-right"><span class="edit-categories pointer hide" title="Click to edit thie Job Type profile"><i class="fas fa-pencil-alt"></i></span> &nbsp; <span data-bom_category_id="<?php echo $bom_category_details->bom_category_id; ?>" class="delete-bom-category-item-btn pointer" title="Click to delete this Job Type profile" ><i class="far fa-trash-alt"></i></span></span></legend>
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Status</label></td>
											<td width="85%"><?php echo ($bom_category_details->is_active == 1) ? 'Active <i class="far fa-check-circle"></i>' : 'Disabled <i class="far fa-times-circle text-red"></i>'; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Date Created</label></td>
											<td width="85%"><?php echo (valid_date($bom_category_details->date_created)) ? date('d-m-Y H:i:s', strtotime($bom_category_details->date_created)) : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Created By</label></td>
											<td width="85%"><?php echo (!empty($bom_category_details->record_created_by)) ? ucwords($bom_category_details->record_created_by) : 'Data not available'; ?></td>
										</tr>
									</table>							
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">

									</table>							
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<div class="x_panel tile has-shadow">
								<form id="update-bom-category-profile-form" class="form-horizontal">
									<input type="hidden" name="page" value="details" />
									<input type="hidden" name="bom_category_id" value="<?php echo $bom_category_details->bom_category_id; ?>" />
									<legend>Category Details</legend>
									<div class="input-group form-group">
										<label class="input-group-addon">Category Name</label>
										<input id="category_name" name="bom_category_name" class="form-control" type="text" placeholder="BOM Category" value="<?php echo $bom_category_details->bom_category_name; ?>" />
									</div>									
									<div class="input-group form-group">
										<label class="input-group-addon">Category Group</label>
										<input id="category_group" class="form-control" type="text" placeholder="BOM Category Reference" readonly value="<?php echo strtoupper($bom_category_details->bom_category_group); ?>" />
									</div>
									<div class="input-group">
										<label class="input-group-addon">Category Description</label>
										<textarea id="category_description" name="bom_category_description" type="text" class="form-control" rows="3"><?php echo (!empty($bom_category_details->bom_category_description)) ? $bom_category_details->bom_category_description : '' ?></textarea>     
									</div>
									<br/>
									<div class="input-group form-group">
										<button type="button" class="update-bom-category-btn btn btn-sm btn-success">Save Changes</button>
									</div>
								</form>
							</div>
						</div>
						
						<div class="col-md-6 col-sm-6 col-xs-12 hide">
							<div class="x_panel tile has-shadow">
								<legend>Linked Job Types (<?php echo !empty($linked_job_types) ? count($linked_job_types, 1) : 0; ?>)</legend>
								<?php if (!empty($linked_job_types)) { ?>
									<div class="row">
										<?php foreach ($linked_job_types as $job_type) { ?>
											<div class="col-md-4 col-sm-4 col-xs-12">
												<ul class="to_do">
													<li>H1 <p><?php echo $bom_category_details->category_text; ?> <span class="pull-right"><span class="remove-categories pointer" data-job_type_id="<?php echo $job_type->job_type_id; ?>" data-bom_category_id="<?php echo $bom_category_details->bom_category_id; ?>" title="Click to remove this BOM Category from this Job type" ><i class="far fa-trash-alt text-red"></i></span></span></p></li>
												</ul>
											</div>
										<?php } ?>								
									</div>
								<?php } ?>								
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){
	
		$( '.update-bom-category-btn' ).click( function( event ){

			var formID = $( this ).closest( 'form' ).attr( 'id' );
			event.preventDefault();
			var formData = $('#'+formID ).serialize();
			swal({
				title: 'Confirm BOM Category update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/update_bom_category/'.$bom_category_details->bom_category_id); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2000
								})
								window.setTimeout(function(){ 
									location.reload();
								} ,1000);							
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}		
						}
					});
				}
			}).catch(swal.noop)
		});
		
		
		//Delete Category Item from
		$('.delete-bom-category-item-btn').click(function(){

			var categoryId = $(this).data( 'bom_category_id' );
			swal({
				title: 'Confirm delete BOM Category?',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/delete_bom_category/'.$bom_category_details->bom_category_id); ?>",
						method:"POST",
						data:{'page':'details', bom_category_id:categoryId},
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 1500
								})
								window.setTimeout(function(){
									window.location.href = "<?php echo base_url('webapp/job/bom_categories'); ?>";
								} ,1500);
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						}
					});
				}
			}).catch( swal.noop )
		});
		
	});
</script>