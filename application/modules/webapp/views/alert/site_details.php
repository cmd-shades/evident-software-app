<div class="row">
	<div class="col-md-6 col-sm-6 col-xs-12">
		<form id="update-site-form" method="post" >
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="site_id" value="<?php echo $site_details->site_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<div class="x_panel tile has-shadow">
				<legend>Update Site Details</legend>
				<div class="input-group form-group">
					<label class="input-group-addon">Site Name</label>
					<input name="site_name" class="form-control" type="text" placeholder="Site Name" value="<?php echo $site_details->site_name; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Site Reference</label>
					<input name="site_reference" class="form-control" type="text" placeholder="Site Reference" value="<?php echo $site_details->site_reference; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Site Postcode</label>
					<input name="site_postcodes" class="form-control" type="text" placeholder="Site Postcodes" value="<?php echo $site_details->site_postcodes; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Site Dwellings</label>
					<input name="total_dwellings" class="form-control" type="text" placeholder="Site Dwellings" value="<?php echo ( $site_details->total_dwellings > 0 ) ? strtoupper( $site_details->total_dwellings ) : ( !empty( $existing_dwellings ) ? count( $existing_dwellings ) : 0 ); ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Number of Floors</label>
					<input name="number_of_floors" class="form-control" type="text" placeholder="Number of Floors" value="<?php echo $site_details->number_of_floors; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Event Tracking ID</label>
					<input name="event_tracking_status_id" class="form-control" type="text" placeholder="Event Tracking ID" value="<?php echo $site_details->event_tracking_status_id; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Event Tracking Status</label>
					<select name="event_tracking_status_id" class="form-control">
						<option>Please select</option>
						<?php if( !empty( $event_statuses ) ) { foreach( $event_statuses as $k => $status ) { ?>
							<option value="<?php echo $status->event_tracking_status_id; ?>" <?php echo ( $site_details->event_tracking_status_id == $status->event_tracking_status_id ) ? 'selected=selected' : ''; ?> ><?php echo $status->event_tracking_status; ?></option>
						<?php } } ?>
					</select>	
				</div>
				<br/>

				<?php if( $this->user->is_admin || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
					<div class="row">
						<div class="col-md-6">
							<button id="update-site-btn" class="btn btn-block btn-flow btn-success btn-next" type="button" >Update Site</button>					
						</div>
					</div>
				<?php }else{ ?>
					<div class="row col-md-6">
						<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>					
					</div>
				<?php } ?>
			</div>
		</form>
	</div>
</div>

<script>
	$(document).ready(function(){
		
		//Submit form for processing
		$( '#update-site-btn' ).click( function( event ){
					
			event.preventDefault();
			var formData = $('#update-site-form').serialize();
			swal({
				title: 'Confirm site update?',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/site/update_site/'.$site_details->site_id ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){ 
									location.reload();
								} ,3000);							
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

