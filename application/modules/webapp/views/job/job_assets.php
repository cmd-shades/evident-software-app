<div class="row">
	<?php if( $this->user->is_admin || !empty( $permissions->can_add ) || !empty( $permissions->can_view ) || !empty( $permissions->is_admin ) ){ ?>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel tile has-shadow">
				<legend>Assets to be checked <span id="add-asset-to-check" class="pull-right pointer <?php echo in_array( strtolower( $job_details->job_status ), ['successful', 'failed', 'cancelled'] ) ? 'hide' : ''; ?>" title="Add Asset to be checked" ><i class="fas fa-plus text-green"></i></span></legend>
				
				<div class="accordion" id="accordion" role="tablist" aria-multiselectable="true">
					<?php if( !empty( $job_assets )){ $counter = 1; ?>
						<?php foreach( $job_assets as $asset_type_id => $asset_type_data ){ $counter++; ?>
							<div class="panel">
								<div class="section-container-bar panel-heading collapsed bg-grey pointer no-radius" role="tab" id="heading<?php echo number_to_words( $counter ); ?>" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo number_to_words( $counter ); ?>" aria-expanded="true" aria-controls="collapse<?php echo number_to_words( $counter ); ?>">
									<h4 class="panel-title"><i class="caret-icon fas fa-caret-down text-yellow"></i> <img src="<?php echo !empty( $asset_type_data->discipline_image_url ) ? $asset_type_data->discipline_image_url : ''; ?>" width="10px" >&nbsp;<?php echo !empty( $asset_type_data->asset_type ) ? ucwords( $asset_type_data->asset_type ) : '<span class="text-yellow">DISCIPLIN NOT SET</span>'; ?> <span class="pull-right">(<?php echo !is_array( $asset_type_data->assets ) ? count( object_to_array( $asset_type_data->assets ) ) : count( $asset_type_data->assets ) ; ?>)<span></h4>
								</div>
								
								<div id="collapse<?php echo number_to_words( $counter ); ?>" class="panel-collapse collapse no-bg no-background" role="tabpanel" aria-labelledby="heading<?php echo number_to_words( $counter ); ?>" >
									<div class="panel-body">
										<table style="width:100%">
											<tr>
												<th width="15%">Asset Unique ID</th>
												<th width="25%">Zone - Location</th>
												<th width="25%">Evidoc Type</th>
												<th width="15%">Due Date</th>
												<th width="10%" class="text-center" >Checked</th>
												<th width="10%"><span class="pull-right">Action</span></th>
											</tr>
											<?php if( !empty( $asset_type_data->assets ) ){ foreach( $asset_type_data->assets as $asset_record ) { ?>
												<tr>
													<td><a href="<?php echo base_url( 'webapp/asset/profile/'.$asset_record->asset_id ); ?>"><?php echo $asset_record->asset_unique_id; ?></a></td>
													<td><?php echo $asset_record->zone_name; ?> <?php echo !empty( $asset_record->location_name ) ? ' - '.$asset_record->location_name : ''; ?></td>
													<td><?php echo !empty( $asset_record->audit_type ) ? $asset_record->audit_type : ''; ?></td>
													<td><?php echo ( valid_date( $asset_record->due_date ) ) ? date('d-m-Y', strtotime( $asset_record->due_date ) ) : ''; ?></td>
													<td class="text-center" ><i class="far <?php echo ( !empty( $asset_record->audit_id ) ) ? ' fa-check-circle text-green ' : ' fa-times-circle text-red'; ?>"></i></td>
													<td class="pull-right" >
														<span>
															<?php if( ( $this->user->is_admin || !empty( $permissions->can_delete ) || !empty( $permissions->is_admin ) ) && empty( $asset_record->audit_id ) ){ ?>
																<span class="text-red text-bold"><i title="Click here to remove this asset from Job" class="remove-asset-btn fas fa-trash-alt text-red pointer" data-job_id="<?php echo $asset_record->job_id; ?>" data-id = "<?php echo $asset_record->id; ?>" data-asset_unique_id="<?php echo $asset_record->asset_unique_id; ?>" data-location_details="<?php echo $asset_record->zone_name; ?> <?php echo !empty( $asset_record->location_name ) ? ' - '.$asset_record->location_name : ''; ?>" ></i></span>
															<?php } ?>
														</span>
													</td>
												</tr>
											<?php } }else{ ?>
												<tr>
													<td colspan="6"><?php echo $this->config->item('no_records'); ?></td>
													<td colspan="6"><?php echo $this->config->item('no_records'); ?></td>
												</tr>
											<?php } ?>
										</table>
									</div>
								</div>
							</div>
						<?php } ?>
					<?php } ?>
				</div>
			</div>		
		</div>
	<?php } ?>
	
	<div id="asset-to-be-checked-modal-md" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title" id="myAssetModalLabel">Add new Asset to be Checked</h4>
				</div>
				<div class="modal-body">
					<div class="">
						<form id="asset-addition-form" enctype="multipart/form-data" >
							<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
							<input type="hidden"  name="site_id" value="<?php echo $job_details->site_id; ?>"/>
							<input type="hidden"  name="job_id" value="<?php echo $job_details->job_id; ?>"/>
							<input type="hidden"  name="page" value="details"/>
							
							<h4>Available asset for this Discipline</h4>
							
							<div class="asset_to_be_checked_container">
								<div class="row">
									<div class="col-md-12 col-sm-12 col-xs-12">
										<div class="form-group has-shadow margin-bottom-15" >
											<select id="site_assets_id" name="assets_to_check[]" multiple="multiple" class="form-control" style="width:100%; display:none; margin-bottom:10px;" data-label_text="Available assets by Discipline" >
												<option value="" >Search / Select assets</option>
												<?php if( !empty( $site_assets ) ) { foreach( $site_assets as $k => $asset ) { ?>
													<?php if( !in_array( $asset->asset_id, $linked_assets ) ) { ?>
														<option value="<?php echo $asset->asset_id; ?>" ><?php echo ucwords( $asset->asset_unique_id ); ?><?php echo !empty( $asset->zone_name ) ? ucwords( ' - '.$asset->zone_name ) : ''; ?><?php echo !empty( $asset->location_name ) ? ucwords( ' - '.$asset->location_name ) : ''; ?></option>
													<?php } ?>
												<?php } } ?>
											</select>
										</div>
									</div>
								</div>
							</div>
							
							<?php if( $this->user->is_admin || !empty( $permissions->can_add ) || !empty( $permissions->is_admin ) || !empty( $permissions->is_module_admin ) ){ ?>
								<div class="row">
									<div class="col-md-12">
										<button id="add-assets-to-check-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="button" >Add selected Assets</button>					
									</div>
								</div>
							<?php }else{ ?>
								<div class="row col-md-6">
									<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>					
								</div>
							<?php } ?>
							
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	
</div>

<script>

	$(document).ready(function(){
		
		$( '#site_assets_id' ).select2({
			
		});
		
		$( '#add-asset-to-check' ).click( function(){
			
			//Fetch available assets
			
			$( '#asset-to-be-checked-modal-md' ).modal( 'show' );
			
		});
		
		$( '.asset-types-toggle' ).click( function(){
			var typeRef = $( this ).data( 'asset_type_ref' );
			$( '.atype'+typeRef ).closest( 'tr' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');
			$( '.'+typeRef ).slideToggle();
		});
		
		$( '.asset-categories-toggle' ).click( function(){
			var typeRef = $( this ).data( 'asset_category_ref' );
			$( '.acategory'+typeRef ).closest( 'tr' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');
			$( '.'+typeRef ).slideToggle();
		});
		
		$( '.section-container-bar' ).click( function(){
			$( this ).closest( 'div' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');			
		});
		
		//Add selected assets to be added for checking
		$( '#add-assets-to-check-btn' ).click(function( e ){
			
			e.preventDefault();
			
			var formData = $( '#asset-addition-form' ).serialize();
			
			swal({
				title: 'Add selected assets?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/link_assets_to_job' ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 && ( data.assets !== '' ) ){
								$( "#new-job-modal-md" ).modal( 'hide' );
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){
									location.reload();
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
			}).catch(swal.noop)
		});
		
		
		//Remove asset from Job
		$('.remove-asset-btn').click(function(){

			var linkID  		= $(this).data( 'id' ),
				jobId			= $(this).data( 'job_id' ),
				assetUniqueId	= $(this).data( 'asset_unique_id' ),
				locationDetails	= $(this).data( 'location_details' );
				
			swal({
				title: 'Confirm unlink Asset?',
				html: '<strong>' + assetUniqueId + '</strong> / ' + locationDetails,
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/unlink_asset_from_job/'.$job_details->job_id ); ?>",
						method:"POST",
						data:{job_id: jobId, id:linkID},
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