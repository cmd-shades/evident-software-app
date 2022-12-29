<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row alert alert-ssid bg-blue" role="alert">
					<div class="profile-overview">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<legend>Sub Block Profile <span class="pull-right"><span class="edit-sub_block pointer hide" title="Click to edit this Sub Block profile"><i class="fas fa-pencil-alt"></i></span> &nbsp; <span class="delete-sub_block-btn pointer" data-sub_block_id="<?php echo $sub_block_details->sub_block_id; ?>" title="Click to delete this Sub Block profile" ><i class="far fa-trash-alt"></i></span></span></legend>
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Sub Block Name</label></td>
											<td width="85%"><?php echo ucwords(strtolower($sub_block_details->sub_block_name)); ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Sub Block Description</label></td>
											<td width="85%"><?php echo ucwords(strtolower($sub_block_details->sub_block_desc)); ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Sub Block Address</label></td>
											<td width="85%"><?php echo ucwords(strtolower($sub_block_details->sub_block_address)); ?> <?php echo (!empty($sub_block_details->sub_block_postcode)) ? strtoupper($sub_block_details->sub_block_postcode) : ''; ?></td>
										</tr>
									</table>							
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Status</label></td>
											<td width="85%"><?php echo ($sub_block_details->is_active == 1) ? 'Active <i class="far fa-check-circle"></i>' : 'Disabled <i class="far fa-times-circle text-red"></i>'; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Date Created</label></td>
											<td width="85%"><?php echo (valid_date($sub_block_details->date_created)) ? date('d-m-Y H:i:s', strtotime($sub_block_details->date_created)) : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Created By</label></td>
											<td width="85%"><?php echo (!empty($sub_block_details->record_created_by)) ? ucwords($sub_block_details->record_created_by) : 'Data not available'; ?></td>
										</tr>
									</table>							
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<?php include $include_page; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){

		$( '.update-sub_block-btn' ).click( function( event ){

			var formID = $( this ).closest( 'form' ).attr( 'id' );
			event.preventDefault();
			var formData = $('#'+formID ).serialize();
			swal({
				title: 'Confirm Sub Block update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/site/update_sub_block/'.$sub_block_details->sub_block_id); ?>",
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
		

		//DELETE SUB-BLOCK RESOURCE
		$( '.delete-sub_block-btn' ).click( function( event ){
			
			var siteId = "<?php echo $sub_block_details->site_id; ?>";
			var sub_blockId = $( this ).data( 'sub_block_id' );

			if( sub_blockId.length == 0 ){
				swal({
					type: 'error',
					html: 'Something went wrong, please refresh the page and try again!',
					showCancelButton: true,
					confirmButtonColor: '#5CB85C',
					cancelButtonColor: '#9D1919',
					confirmButtonText: 'Yes'
				});
				return false;
			}

			event.preventDefault();

			swal({
				type: 'warning',
				title: 'Confirm delete Sub Block?',
				html: 'This is an irreversible action and will affect associated Zones and Locations.!',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/site/delete_sub_block/'.$sub_block_details->site_id.'/'); ?>"+sub_blockId,
						method:"POST",
						data:{ page:'details' , xsrf_token: xsrfToken, site_id:siteId, sub_block_id:sub_blockId },
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2100
								})
								window.setTimeout(function(){
									window.location.href = "<?php echo base_url('webapp/site/profile/'); ?>"+siteId+"/sub_blocks";
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
	});
</script>