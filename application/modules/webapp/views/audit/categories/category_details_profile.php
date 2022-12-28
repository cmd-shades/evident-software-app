<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row alert alert-ssid bg-blue" role="alert">
					<div class="profile-overview">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<legend>Evidoc Category Profile <span class="pull-right"><span class="edit-categories pointer hide" title="Click to edit thie Job Type profile"><i class="fas fa-pencil-alt"></i></span> &nbsp; <span data-category_id="<?php echo $category_details->category_id; ?>" class="delete-category-item-btn pointer" title="Click to delete this Job Type profile" ><i class="far fa-trash-alt"></i></span></span></legend>
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Status</label></td>
											<td width="85%"><?php echo ( $category_details->is_active == 1 ) ? 'Active <i class="far fa-check-circle"></i>' : 'Disabled <i class="far fa-times-circle text-red"></i>'; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Date Created</label></td>
											<td width="85%"><?php echo ( valid_date( $category_details->date_created ) ) ? date( 'd-m-Y H:i:s', strtotime( $category_details->date_created ) ) : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Created By</label></td>
											<td width="85%"><?php echo ( !empty( $category_details->record_created_by ) ) ? ucwords( $category_details->record_created_by ) : 'Data not available'; ?></td>
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
								<form id="update-category-profile-form" class="form-horizontal">
									<input type="hidden" name="page" value="details" />
									<input type="hidden" name="category_id" value="<?php echo $category_details->category_id; ?>" />
									<legend>Category Details</legend>
									<div class="input-group form-group">
										<label class="input-group-addon">Category Reference</label>
										<input id="category_ref" class="form-control" type="text" placeholder="Evidoc Category Reference" readonly value="<?php echo strtoupper( $category_details->category_ref ); ?>" />
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Category Name</label>
										<input id="category_name" name="category_name" class="form-control" type="text" placeholder="Evidoc Category" value="<?php echo $category_details->category_name; ?>" />
									</div>									
									<div class="input-group form-group">
										<label class="input-group-addon">Category Group</label>
										<input id="category_group" class="form-control" type="text" placeholder="Evidoc Category Reference" readonly value="<?php echo strtoupper( $category_details->category_group ); ?>" />
									</div>
									<div class="input-group">
										<label class="input-group-addon">Category Description</label>
										<textarea id="category_description" name="description" type="text" class="form-control" rows="3"><?php echo ( !empty( $category_details->description ) ) ? $category_details->description : '' ?></textarea>     
									</div>
									<br/>
									<div class="input-group form-group">
										<button type="button" class="update-category-btn btn btn-sm btn-success">Save Changes</button>
									</div>
								</form>
							</div>
						</div>
						
						<div class="col-md-6 col-sm-6 col-xs-12 hide">
							<div class="x_panel tile has-shadow">
								<legend>Linked Evidoc Types (<?php echo !empty( $linked_audit_types ) ? count( $linked_audit_types, 1 ) : 0; ?>)</legend>
								<?php if( !empty( $linked_audit_types ) ){ ?>
									<div class="row">
										<?php foreach( $linked_audit_types as $job_type ){ ?>
											<div class="col-md-4 col-sm-4 col-xs-12">
												<ul class="to_do">
													<li>H1 <p><?php echo $category_details->category_text; ?> <span class="pull-right"><span class="remove-categories pointer" data-job_type_id="<?php echo $job_type->job_type_id; ?>" data-category_id="<?php echo $category_details->category_id; ?>" title="Click to remove this Evidoc Category from this Job type" ><i class="far fa-trash-alt text-red"></i></span></span></p></li>
												</ul>
											</div>
										<?php } ?>								
									</div>
								<?php } ?>								
							</div>
						</div>
						
						<div class="col-md-6 col-sm-6 col-xs-12 hide">
							<div class="x_panel tile has-shadow">
								<legend>Linked Asset Types (<?php echo !empty( $linked_asset_types ) ? count( $linked_asset_types, 1 ) : 0; ?>)</legend>
								<?php if( !empty( $linked_asset_types ) ){ ?>
									<div class="row">
										<?php foreach( $linked_asset_types as $asset_type ){ ?>
											<div class="col-md-4 col-sm-4 col-xs-12">
												<ul class="to_do">
													<li>H1 <p><?php echo $asset_type->asset_type; ?> <span class="pull-right hide"><span class="remove-asset_type-link pointer" data-asset_type_id="<?php echo $asset_type->asset_type_id; ?>" data-category_id="<?php echo $category_details->category_id; ?>" title="Click to remove this Asset Type from this Job type" ><i class="far fa-trash-alt text-red"></i></span></span></p></li>
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
	
		$( '.update-category-btn' ).click( function( event ){

			var formID = $( this ).closest( 'form' ).attr( 'id' );
			event.preventDefault();
			var formData = $('#'+formID ).serialize();
			swal({
				title: 'Confirm Evidoc Category update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/audit/update_category/'.$category_details->category_id ); ?>",
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
		$('.delete-category-item-btn').click(function(){

			var categoryId = $(this).data( 'category_id' );
			swal({
				title: 'Confirm delete Evidoc Category?',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url( 'webapp/audit/delete_category/'.$category_details->category_id ); ?>",
						method:"POST",
						data:{'page':'details', category_id:categoryId},
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
									window.location.href = "<?php echo base_url('webapp/audit/categories'); ?>";
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