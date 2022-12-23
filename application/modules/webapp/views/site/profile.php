<?php
if( $site_details ){ ?>
	<div id="site-profile" class="row">
		<div class="x_panel">
			<div class="x_content">
				<div class="profile-details-container">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="profile-header">
								<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
									<h2 style="display: inline-block;">
										<span class="text-bold"><?php echo ( !empty( $site_details->site_name ) ) ? $site_details->site_name  : '' ; ?></span> <?php echo ( !empty( $site_details->site_id ) ) ? '(ID:<span class="text-bold">'.$site_details->site_id.'</span>)' : '' ; ?>
									</h2>
									<h3 class="pull-right" style="display: inline-block;">Site Monthly Value: <span class="monthly_value"><?php echo ( !empty( $site_monthly_value ) ) ? $site_monthly_value : 0 ; ?> <?php echo ( !empty( $site_details->invoice_currency_name ) ) ? ucwords( $site_details->invoice_currency_name ) : '' ; ?></span></h3>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
									<div class="pull-right">
										<span>
											<a class="edit-site" href="#" data-toggle="modal" data-target="#disableSite" title="Click to disable this Site" >
												<i class="fas fa-ban"></i>
											</a>&nbsp;
										</span>

										<span>
											<a class="edit-site" id="duplicate-site" href="#" data-site_id="<?php echo ( !empty( $site_details->site_id ) ) ? $site_details->site_id  : '' ; ?>" title="Click to duplicate this Site record" >
												<i class="fas fa-copy"></i>
											</a>&nbsp;
										</span>
										<span>
											<a class="edit-site" href="#" data-toggle="modal" data-target="#editSite"><i class="fas fa-edit"></i></a>
										</span>
										<span class="delete_container">
											<a href="#"><i class="fas fa-trash-alt"></i></a>
										</span>
										<form id="generate_stats" method="post" style="display: inline;" action="<?php echo base_url( "webapp/site/generate_viewing_stats" ); ?>">
											<input type="hidden" name="site_id" value="<?php echo ( !empty( $site_details->site_id ) ) ? ( int ) $site_details->site_id : '' ; ?>" />
											<div class="generate_stats" title="Generate Site Viewing Stats"><a href="#"><i class="fas fa-chart-bar"></i></a></div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
					if( !empty( $site_details ) ){ ?>
						<div class="row records-bar panel-primary" role="alert">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="row">
									<!-- <legend>Site Details</legend> -->
									<div class="row profile_view">
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<table style="width:100%;">
												<tr>
													<td width="30%"><i class="hide fa fa-user"></i> <label>Site Name</label></td>
													<td width="60%"><?php echo ( !empty( $site_details->site_name ) ) ? ucwords( $site_details->site_name ) : '' ; ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-user"></i> <label>Site ID</label></td>
													<td width="60%"><?php echo ( !empty( $site_details->site_id ) ) ? ucwords( $site_details->site_id ) : '' ; ?></td>
												</tr>

												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Address</label></td>
													<td width="60%"><?php echo ( !empty( $site_details->fulladdress ) ) ? ( trim( $site_details->fulladdress ) ) : '' ; ?></td>
												</tr>

												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Country</label></td>
													<td width="60%"><?php echo ( !empty( $site_details->country ) ) ? $site_details->country : '' ; ?></td>
												</tr>
												<tr>
													<td width="30%"><label>Contact Name</label></td>
													<td width="60%"><?php echo ( !empty( $site_details->contact_full_name ) ) ? ucwords( $site_details->contact_full_name ) : '' ; ?></td>
												</tr>
												<tr>
													<td width="30%"><label>Contact Number</label></td>
													<td width="60%"><?php echo ( !empty( $site_details->telephone_number ) ) ? ucwords( $site_details->telephone_number ) : '' ; ?></td>
												</tr>
												<tr>
													<td width="30%"><label>Contact Email</label></td>
													<td width="60%"><?php echo ( !empty( $site_details->email ) ) ? ( $site_details->email ) : '' ; ?></td>
												</tr>
												<tr>
													<td width="30%"><label>Contact Skype</label></td>
													<td width="60%"><?php echo ( !empty( $site_details->skype ) ) ? ( $site_details->skype ) : '' ; ?></td>
												</tr>
												<tr>
													<td width="30%"><label>Site Note</label></td>
													<td width="60%" style="padding-right: 20px;"><?php echo ( !empty( $site_details->site_notes ) ) ? ( $site_details->site_notes ) : '' ; ?></td>
												</tr>
												<tr>
													<td width="30%"><label>&nbsp;</label></td>
													<td width="60%" style="padding-right: 20px;">&nbsp;</td>
												</tr>

												<tr>
													<td width="30%"><i class="hide fa fa-user"></i> <label>Airtime Active</label></td>
													<td width="60%"><?php echo ( !empty( $site_details->is_airtime_active ) && ( $site_details->is_airtime_active == true ) ) ? 'Active' : 'Inactive' ; ?></td>
												</tr>
											</table>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<div class="row">
									<!-- <legend>Site Overview</legend> -->
									<table style="width:100%;">
										<tr>
											<td width="30%"><label>Distribution Group</label></td>
											<td width="60%"><?php echo ( !empty( $site_details->distribution_group_name ) ) ? ucwords( $site_details->distribution_group_name ) : '' ; ?></td>
										</tr>

										<tr>
											<td width="30%"><label>Content Territory</label></td>
											<td width="60%"><?php echo ( !empty( $site_details->content_territory_name ) ) ? ucwords( $site_details->content_territory_name ) : '' ; ?></td>
										</tr>
										<tr>
											<td width="30%"><label>Time Zone</label></td>
											<td width="60%"><?php echo ( !empty( $site_details->time_zone_name ) ) ? $site_details->time_zone_name : '' ; ?></td>
										</tr>
										<tr>
											<td width="30%"><label>Invoice Currency</label></td>
											<td width="60%"><?php echo ( !empty( $site_details->invoice_currency_name ) ) ? ucwords( $site_details->invoice_currency_name ) : '' ; ?></td>
										</tr>
										<tr>
											<td width="30%"><label>Invoice To</label></td>
											<td width="60%"><?php echo ( !empty( $site_details->invoice_to ) ) ? ucwords( $site_details->invoice_to ) : '' ; ?></td>
										</tr>
										<tr>
											<td width="30%"><label>Charge Frequency</label></td>
											<td width="60%"><?php echo ( !empty( $site_details->charge_frequency_name ) ) ? ucwords( $site_details->charge_frequency_name ) : '' ; ?></td>
										</tr>
										<tr>
											<td width="30%"><label>System Integrator</label></td>
											<td width="60%"><?php echo ( !empty( $site_details->system_integrator_name ) ) ? ucwords( $site_details->system_integrator_name ) : '' ; ?></td>
										</tr>
										<tr>
											<td width="30%"><label>Operating Company</label></td>
											<td width="60%"><?php echo ( !empty( $site_details->operating_company_name ) ) ? ucwords( $site_details->operating_company_name ) : '' ; ?></td>
										</tr>
										<tr>
											<td width="30%"><label>System Type</label></td>
											<td width="60%"><?php echo ( !empty( $site_details->system_type_name ) ) ? ucwords( $site_details->system_type_name ) : '' ; ?></td>
										</tr>
										<tr>
											<td width="30%"><label>Number of Rooms</label></td>
											<td width="60%"><?php echo ( !empty( $site_details->number_of_rooms ) ) ? ( int ) $site_details->number_of_rooms : '' ; ?></td>
										</tr>
										<tr>
											<td width="30%"><label>Restrictions/Age Rating</label></td>
											<td width="60%"><?php
												if( !empty( $site_details->site_restrictions ) ){
													foreach( $site_details->site_restrictions as $key => $rest ){
														echo ( $key == 0 ) ? '' : ', ';
														foreach( $age_rating as $rating ){
															echo  ( $rating->age_rating_id == $rest ) ? $rating->age_rating_name : '' ;
														}
													}
												} ?>
											</td>
										</tr>
										<tr>
											<td width="30%"><label>Site Status</label></td>
											<td width="60%"><?php echo ( !empty( $site_details->status_name ) ) ? ( ( strtolower( $site_details->status_name ) != "active" ) ? '<span class="alert" style="padding-left: 0;">'.$site_details->status_name.'</span>' : $site_details->status_name ) : '' ; ?></td>
										</tr>
										
										<tr>
											<td width="30%"><label>Contract Signed</label></td>
											<td width="60%"><?php echo ( !empty( $site_details->is_signed ) && ( $site_details->is_signed == true ) ) ? 'Yes' : 'No' ; ?></td>
										</tr>
										
										<tr>
											<td width="30%"><label>Number of Devices</label></td>
											<td width="60%"><?php echo ( !empty( $site_details->number_of_devices ) ) ? ( ( ( ( int ) $site_details->number_of_devices > ( int ) $site_details->number_of_rooms ) && ( !isset( $site_details->is_device_flag_overriden ) || ( $site_details->is_device_flag_overriden != true ) ) ) ? '<span style="color: #b7001f;">'.( ( int ) $site_details->number_of_devices ).' <i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>' : '<span class="black">'.( ( int ) $site_details->number_of_devices ).'</span>' ) : 0 ; ?></td>
										</tr>

										<?php if( !empty( $site_details->disable_site_date ) ){ ?>
										<tr>
											<td width="30%"><label>Disable Site Date</label></td>
											<td width="60%"><?php echo ( validate_date( $site_details->disable_site_date ) ) ? format_date_client( $site_details->disable_site_date ) : '' ; ?></td>
										</tr>
										<?php } ?>
									</table>
								</div>
							</div>
						</div>
						<div class="row">
							<?php include $include_page; ?>
						</div>
					<?php
					} else { ?>
						<div class="row">
							<span><?php echo $this->config->item( 'no_records' ); ?></span>
						</div>
					<?php
					} ?>
				</div>
			</div>
		</div>
	</div>
<?php 
} else { ?>
	<div id="site-profile" class="row">
		<div class="x_panel">
			<div class="x_content">
				<div class="profile-details-container">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="profile-header">
								<div class="row">
									<span><?php echo $this->config->item( 'no_data' ); ?></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
} ?>

<script>
	$( document ).ready( function(){

		$( '#duplicate-site' ).click( function(){

			var siteId = $( this ).data( 'site_id' );

			swal({
				title: 'Confirm Site Duplicate?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ){
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url( 'webapp/site/duplicate_site/' ); ?>",
						method:"POST",
						data:{ page: 'details', site_id: siteId },
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 && ( data.site !== '' ) ){

								var newSiteId = data.site.site_id;

								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 1500
								})
								window.setTimeout(function(){
									location.href = "<?php echo base_url('webapp/site/profile/'); ?>"+newSiteId;
								} ,2000);
							} else {
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