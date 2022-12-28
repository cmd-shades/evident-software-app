<style>
	table tr th,table tr td{
		padding: 5px 0; 
	}
	
	@media (max-width: 480px) {
		.btn-info{
			margin-bottom:10px;
		}
	}
	
</style>

<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<?php if( !empty($site_details) ) { ?>
				<div class="row alert alert-ssid records-bar" role="alert">
					<div class="col-md-5 col-sm-5 col-xs-12">
						<div class="row">
							<legend>Building Details &nbsp;&nbsp;<span class="<?php echo empty( $this->user->is_admin ) ? 'hide' : ''; ?> clone-site pointer" title="Clone this Site" data-site_id="<?php echo $site_details->site_id; ?>" data-toggle="modal" data-target="#site-clone-modal-md" ><i class="fa fa-copy"></i></span></legend>
							<div class="rows">
								<div class="row profile_view">
									<div class="row col-sm-12">
										<div class="col-xs-12">
											<table style="width:100%;">
												<tr>
													<td width="30%"><i class="hide fa fa-user"></i> <label>Building Name</label></td>
													<td><?php echo ucwords($site_details->site_name); ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Building Address</label></td>
													<td width="60%"><?php echo !empty( $site_details->address_line_1 ) ? $site_details->address_line_1 : ''; ?> <?php echo !empty( $site_details->address_line_2 ) ? $site_details->address_line_2 : ''; ?> <?php echo !empty( $site_details->address_line_3 ) ? $site_details->address_line_3 : ''; ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Total Dwellings</label></td>
													<td width="60%"><?php echo !empty( $site_details->number_of_flats ) ? $site_details->number_of_flats : ( ( $site_details->total_dwellings > 0 ) ? strtoupper( $site_details->total_dwellings ) : ( !empty( $existing_dwellings ) ? count( $existing_dwellings ) : 0 ) ); ?></td>
												</tr> 
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Number of Floors</label></td>
													<td width="60%"><?php echo strtoupper( $site_details->number_of_floors ); ?></td>
												</tr>
												<?php /* ?><tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Building Reference</label></td>
													<td width="60%"><?php echo strtoupper($site_details->site_reference); ?></td>
												</tr>
												<?php */ ?>
											</table>
										</div>		
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-5 col-sm-5 col-xs-12">
						<div class="row">
							<legend>Building Overview</legend>
							<table style="width:100%;">
								<tr>
									<td width="40%"><i class="hide fa fa-user"></i> <label>Building Status</label></td>
									<td width="60%"><?php echo ucwords( $site_details->status_name ); ?></td>
								</tr>
								<?php /* ?>
								<tr>
									<td width="40%"><i class="hide fa fa-user"></i> <label>Connected Panels</label></td>
									<td width="60%"><?php echo ( !empty( $site_panels ) ) ? count( object_to_array( $site_panels ) ) : 0; ?></td>
								</tr>
								
								<tr>
									<td width="40%"><i class="hide fa fa-user"></i> <label>Event Tracking Status</label></td>
									<td width="60%"><?php echo ucwords( $site_details->event_tracking_status ); ?></td>
								</tr>
								<?php */ ?>
								<tr>
									<td width="40%"><i class="hide fa fa-user"></i> <label>Last Audit Date</label></td>
									<td width="60%"><?php echo ( valid_date( $site_details->last_audit_date ) ) ? date( 'd-m-Y H:i:s', strtotime( $site_details->last_audit_date ) ) : ''; ?></td>
								</tr>
								<tr>
									<td width="40%"><i class="hide fa fa-user"></i> <label>Audit Result Status</label></td>
									<td width="60%"><?php echo ( !empty( $site_details->result_status_alt ) ) ? ( ucwords( $site_details->result_status_alt ) ) : "" ; ?></td>
								</tr>								
								<tr>
									<td width="40%"><i class="hide fa fa-user"></i> <label>Next Audit Date</label></td>
									<td width="60%"><?php echo ( valid_date( $site_details->next_audit_date ) ) ? date( 'd-m-Y H:i:s', strtotime( $site_details->next_audit_date ) ) : ''; ?></td>
								</tr>
								<!-- <tr>
									<td width="30%"><i class="hide fa fa-user"></i> <label>Address line 1</label></td>
									<td><?php echo ucwords( $site_details->site_name ); ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Address line 2</label></td>
									<td width="60%"><?php echo $site_details->summaryline; ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Address Town</label></td>
									<td width="60%"><?php echo strtoupper($site_details->site_reference); ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Postcode(s)</label></td>
									<td width="60%"><?php echo strtoupper($site_details->site_reference); ?></td>
								</tr> -->
							</table>
						</div>
					</div>
					
					<div class="col-md-2 col-sm-2 col-xs-12">
						<div class="row">
							<legend class="pull-right"><span>Assets Summary <span class="pull-right">(<?php echo !empty( $total_assets ) ? $total_assets : 0; ?>)</span></span> </legend>
							<table style="width:100%;">
								<tr>
									<td>
										<?php if( !empty( $grouped_assets ) ){ ?>
											<table class="table-responsive pull-right " style="margin-bottom:0px;width:100%" >
												<?php foreach( $grouped_assets as $assets_by_category => $asset_category ){ ?>
													<tr>
														<td width="60%"><?php echo ucfirst( $asset_category->category_name ); ?></td>
														<td><span class="pull-right" style="font-size:16px;"><?php echo $asset_category->total_assets; ?></span></td>
													</tr>
												<?php } ?>
											</table>
										<?php } ?>
									</td>									
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="row">			
				<?php $this->load->view('webapp/_partials/tabs_loader'); ?>
				<?php include $include_page; ?>
			</div>
			<?php }else{ ?>
				<div class="row">
					<span><?php echo $this->config->item('no_records'); ?></span>
				</div>
			<?php } ?>			
		</div>
	</div>
</div>

<div id="site-clone-modal-md" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form id="site-clone-form">
				<input type="hidden" name="page" value="details" />
				<input type="hidden" name="cloned_site_id" value="<?php echo $site_details->site_id; ?>" />
				<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
				<input type="hidden" name="site_postcodes" value="<?php echo $site_details->site_postcodes; ?>" id="site_postcodes" />

				<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title" id="myModalLabel">Clone Building</h4>
					<small class="text-blue" >* The Site name and address must be different from the source record.</small>
				</div>
				<div class="modal-body">
					<div class="input-group form-group">
						<label class="input-group-addon">Building Name</label>
						<input name="site_name" class="form-control" type="text" placeholder="Site Name" value="<?php echo $site_details->site_name; ?>" />
					</div>

					<div class="hide input-group form-group">
						<label class="input-group-addon">3rd Party Site Ref</label>
						<input name="external_site_ref" class="form-control" type="text" placeholder="3rd Party/External Site Ref" value="<?php echo !empty( $site_details->external_site_ref ) ? $site_details->external_site_ref : ''; ?>" />
					</div>
					
					<div class="input-group form-group">
						<label class="input-group-addon">Address (Site Entrance)</label>
						<select id="site_address_id" name="site_address_id" class="form-control">
							<option value="">Please select the address</option>
							<?php if( !empty( $postcode_addresses ) ) { foreach( $postcode_addresses as $k => $address ) { ?>
								<option value="<?php echo $address['main_address_id']; ?>" <?php echo ( $site_details->site_address_id == $address['main_address_id'] ) ? 'selected=selected' : ''; ?> data-site_postcodes="<?php echo $address['postcode']; ?>" ><?php echo $address['summaryline']; ?></option>
							<?php } } ?>
						</select>
					</div>					
				</div>
				
				<div class="modal-footer">
					<div class="row">
						<?php if( $this->user->is_admin || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
							<div class="col-md-4 col-sm-4 col-xs-12">
								<button id="clone-site-btn" class="btn btn-block btn-sm btn-success pull-right" type="button" >Clone Site</button>
							</div>
						<?php } else {?>
							<div class="col-md-4 col-sm-4 col-xs-12">
								<button class="btn btn-block btn-sm btn-danger" type="button" disabled >Insufficient Permissions</button>							
							</div>
						<?php } ?>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){
		
		/* $( '#site_address_id' ).change(function( e ){
			var postCode = $( 'option:selected', this ).data( 'site_postcodes' );
			if( postCode ){
				$( '#site_postcodes' ).val( postCode );
			} else {
				$( '#site_postcodes' ).val( '' );
			}
		}); */
		
		//Clone Site
		$( '#clone-site-btn' ).click(function( e ){
			
			e.preventDefault();
			
			var siteId 	 = $( '#site_address_id option:selected' ).val();

			if( siteId.length == 0 ){
				swal({
					type: 'error',
					title: 'Please select the Site Addresss!',
				});
				return false;
			}
 
			var formData = $( '#site-clone-form' ).serialize();
			
			swal({
				title: 'Confirm Clone Site?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/site/clone_site' ); ?>",
						method:"POST",
						data: formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 && ( data.site_id !== '' ) ){
								var newSiteId = data.site_id;
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){
									location.href = "<?php echo base_url('webapp/site/profile/'); ?>"+newSiteId;
								} ,3000);							
							} else {
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
		
	})
</script>