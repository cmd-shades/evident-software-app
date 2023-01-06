<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Connected Panels</legend>
			<?php if( !empty( $site_panels )){ ?>
				<div class="row">
					<div class="accordion" id="panel-accordion" role="tablist" aria-multiselectable="true">
						<?php foreach( $site_panels as $k => $panel ){ $k++; ?>
							<div class="panel">
								<a style="color:<?php echo $panel->hex_color; ?>" class="panel-heading collapsed" role="tab" id="heading<?php echo number_to_words( $k ); ?><?php echo $k; ?>" data-toggle="collapse" data-parent="#panel-accordion" href="#collapse<?php echo number_to_words( $k ); ?><?php echo $k; ?>" aria-expanded="false" aria-controls="collapse<?php echo number_to_words( $k ); ?>">
									<h4 class="panel-title"><span title="Asset ID #<?php echo $panel->alarm_panel_code; ?>" >Panel Code <?php echo $panel->alarm_panel_code; ?></span> - <?php echo $panel->asset_name; ?> <?php echo ( !empty( $panel->asset_model ) ) ? ' - '.$panel->asset_model : ''; ?> <small><?php echo ( !empty( $panel->connected_assets ) ) ? '<em>('.count( object_to_array( $panel->connected_assets ) ).' device'.( ( count( object_to_array( $panel->connected_assets ) ) > 1 ) ? 's' : '' ).')</em>' : ''; ?></small><span class="pull-right" title="This Panel is <?php echo strtoupper( $panel->event_tracking_status ); ?>"><?php echo $panel->icon_class; ?></span></h4>
								</a>
								<div id="collapse<?php echo number_to_words( $k ); ?><?php echo $k; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<?php echo number_to_words( $k ); ?>">
									<div class="panel-body">
										<p><strong><?php echo ( !empty( $panel->connected_assets ) ) ? 'Connected assets (devices)' : '<em>No connected assets (devices)</em>'; ?></strong> <span class="pull-right"><a href="<?php echo base_url('/webapp/asset/profile/'.$panel->panel_id ); ?>">Open panel <i class="fas fa-door-open"></i></a></span></p>
										<?php if( !empty( $panel->connected_assets ) ){ ?>
											<div class="dwelling-records table-responsive">
												<table class="table">
													<thead>
														<th width="40%">Asset Name</th>
														<th width="50%">Location</th>
														<th width="10%"><span class="pull-right">Action</span></th>
													</thead>
													<tbody>
														<?php foreach( $panel->connected_assets as $asset ){ ?>
															<tr>
																<td><?php echo $asset->asset_name; ?> - <?php echo $asset->asset_model; ?></td>
																<td><?php echo $asset->location_name; ?></td>
																<td><a href="<?php echo base_url('/webapp/asset/profile/'.$asset->asset_id ); ?>"><span class="pull-right"><i class="far fa-edit text-blue pointer"></i> &nbsp;view</span></a></td>
															</tr>
														<?php } ?>
													</tbody>
												</table>
											</div>
										<?php } ?>	
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } else{ ?>
				<div><?php echo $this->config->item("no_records"); ?></div>
			<?php } ?>
		</div>
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

