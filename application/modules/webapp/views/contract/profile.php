<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<?php if( !empty( $profile_data ) ) { ?>
			<div class="profile-details-container">
				<div class="row alert alert-ssid" role="alert">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<?php if( !empty( $profile_data ) ){ ?>
								<legend>Contract Details</legend>
								<div class="rows">
									<div class="row profile_view">
										<div class="row col-sm-12">
											<div class="col-xs-12">
												<table style="width:100%;">
													<tr>
														<td width="30%"><i class="hide fa fa-user"></i><strong>Name</strong></td>
														<td><?php echo ( !empty( $profile_data[0]->contract_name ) ) ? ( ucwords( $profile_data[0]->contract_name ) ) : '' ; ?></td>
													</tr>
													<!-- <tr>
														<td width="30%"><i class="hide fa fa-at"></i> <strong>Reference Number</strong></td>
														<td width="60%"><?php echo ( !empty( $profile_data[0]->contract_ref ) ) ? ( ucwords( $profile_data[0]->contract_ref ) ) : '' ; ?></td>
													</tr> -->
													<tr>
														<td width="30%"><i class="hide fa fa-at"></i> <strong>Status</strong></td>
														<td width="60%"><?php echo ( !empty( $profile_data[0]->status_name ) ) ? ( ucwords( $profile_data[0]->status_name ) ) : '' ; ?></td>
													</tr>
													<tr>
														<td width="30%"><i class="hide fa fa-at"></i> <strong>Lead Person</strong></td>
														<td width="60%"><?php echo ( !empty( $profile_data[0]->contract_lead_name ) ) ? ( ucwords( $profile_data[0]->contract_lead_name ) ) : '' ; ?></td>
													</tr>
												</table>
											</div>
										</div>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<legend>&nbsp; <!-- Quote Details <i class="fas fa-caret-down"></i> --></legend>
							<div class="row col-sm-6">
								<div class="rows text-center">
									<span class="indic_line_1">Action Items</span>
									<br>
									<span class="indic_line_2"><?php echo ( !empty( $workflows ) ) ? sizeof( $workflows ) : '0' ;  ?></span>
								</div>
							</div>
							<div class="row col-sm-6">
								<div class="rows text-center">
									<span class="indic_line_1">Linked Sites</span>
									<br>
									<span class="indic_line_2"><?php echo ( !empty( $linked_sites ) ) ? sizeof( $linked_sites ) : '0' ;  ?></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
				<div class="row">
					<?php $this->load->view('webapp/_partials/tabs_loader') ?>				
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

<script>
$( document ).ready( function(){
	<?php 
	if( !empty( $feedback ) ){ ?>
		swal({ 
			<?php
			if( $feedback == "Workflow has been created successfully." ){ ?>
				type: 'success',
			<?php } else { ?>
				type: 'info',
			<?php } ?>
			title: "<?php echo $feedback; ?>",
			showConfirmButton: false,
			timer: 3000
		})
	<?php
	} ?>
	
	$( '.datetimepicker' ).datetimepicker({
		formatDate: 'd/m/Y',
		timepicker:false,
		format:'d/m/Y',
	});
	
	$( ".createWFButton, #linkSitesWFButton" ).on( "click", function(){
		$( ".table_body" ).slideToggle( 300 );
		$( ".create_wf_form, .link_sites" ).slideToggle( 300 );
		$( this ).children( '.fas' ).toggleClass( 'fa-chevron-down fa-chevron-up' );
	});
});
</script>