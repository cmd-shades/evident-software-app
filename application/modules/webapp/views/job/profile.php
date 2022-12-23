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
			<?php if( !empty($job_details) ) { ?>
			<div class="profile-details-container">
				<div class="row alert alert-ssid records-bar" role="alert">
					<div class="col-md-4 col-sm-4 col-xs-12">
						<div class="row">
							<legend>Job Details ID: <?php echo $job_details->job_id; ?></legend>
							<div class="rows">
								<div class="row profile_view">
									<div class="row col-sm-12">
										<div class="right col-xs-12">
											<table style="width:100%;">
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Job Type</label></td>
													<td width="60%"><?php echo ucwords($job_details->job_type); ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Job date</label></td>
													<td width="60%"><?php echo ( validate_date( $job_details->job_date ) ) ? date('D, jS M Y', strtotime( $job_details->job_date ) ) : ''; ?></td>
												</tr>												
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Job Status</label></td>
													<td width="60%"><?php echo ucwords( $job_details->job_status ); ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Job Assignee</label></td>
													<td width="60%"><?php echo ucwords( $job_details->assignee ); ?></td>
												</tr>
											</table>
										</div>		
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-12">
						<div class="row">
							<legend>Job Address</legend>
							<table style="width:100%;display:block" class="">
								<tr>
									<td width="30%"><i class="hide fa fa-user"></i> <label>Address 1</label></td>
									<td><?php echo ucwords($job_details->address_line_1); ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Address 2</label></td>
									<td width="60%"><?php echo $job_details->address_city; ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Town</label></td>
									<td width="60%"><?php echo ucwords($job_details->address_county); ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Postcode(s)</label></td>
									<td width="60%"><?php echo strtoupper($job_details->address_postcode); ?></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-12">
						<div class="row">
							<?php if( !empty( $job_details->site_details ) ){
								$link = 'Linked Site Details <span  class="coming-soon pointer" href="'.base_url( 'webapp/site/profile/'.$job_details->site_details->site_id ).'">('.$job_details->site_details->site_id.')</span>';
							}else if( !empty( $job_details->customer_details ) ){
								$link = 'Linked Customer Details <span class="coming-soon pointer" href="'.base_url( 'webapp/customer/profile/'.$job_details->customer_details->customer_id ).'">('.$job_details->customer_details->customer_id.')</span>';
							} ?>
							
							<legend><?php echo ( !empty( $link  ) ) ? $link  : 'Job not linked'; ?></legend>
							<table style="width:100%;">
								
								<?php if( !empty( $job_details->site_details ) ){ ?>
									<tr>
										<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Site Reference</label></td>
										<td width="60%"><a href="<?php echo base_url( 'webapp/site/profile/'.$job_details->site_details->site_id ); ?>"><?php echo strtoupper($job_details->site_details->site_reference); ?></a></td>
									</tr>
									<tr>
										<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Site Name</label></td>
										<td width="60%"><?php echo $job_details->site_details->site_name; ?></td>
									</tr>
									<tr>
										<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Site Address 1</label></td>
										<td width="60%"><?php echo $job_details->site_details->address_line_1; ?> <?php echo $job_details->site_details->address_line_2; ?></td>
									</tr>
									<tr>
										<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Site Address 2</label></td>
										<td width="60%"><?php echo $job_details->site_details->address_city.', '; ?> <?php echo strtoupper($job_details->address_postcode); ?></td>
									</tr>									
								<?php }else if( !empty( $job_details->customer_details ) ){ ?>
									<tr>
										<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Customer Name</label></td>
										<td width="60%"><?php echo ucwords( $job_details->customer_details->customer_first_name ); ?> <?php echo ucwords( $job_details->customer_details->customer_last_name ); ?></td>
									</tr>
									<tr>
										<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Customer Email</label></td>
										<td width="60%"><?php echo $job_details->customer_details->customer_email; ?></td>
									</tr>
									<tr>
										<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Contact Numbers</label></td>
										<td width="60%"><?php echo $job_details->customer_details->customer_mobile; ?> &nbsp;&nbsp; <?php echo  $job_details->customer_details->customer_telephone; ?></td>
									</tr>
								<?php } ?>
							
							</table>
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
		$( '.coming-soon' ).click( function(){
			swal({
				title: 'Content coming soon!',
				text: 'This link is not available yet',
			})
		});
	});
</script>


