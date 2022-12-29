<?php
$router = false;
$router = $this->router->class;
$method = $this->router->method;
#F89C1C, #5eddbb

switch($router) {
    case "quote": $bckgr_colour = '#5eddbb';
    break;
    case "site": $bckgr_colour = '#f89c1c';
    break;

    default: $bckgr_colour = '#f89c1c';
} ?>

<style type="text/css">
table tr th,table tr td{
	padding: 5px 0; 
}

@media (max-width: 480px) {
	.btn-info{
		margin-bottom:10px;
	}
}
	
.btn-quote,
.btn-quote:hover,
.btn-quote:focus,
.btn-quote:active{
	color: #fff;
	border: none;
	margin-top: -6px;
	background: <?php echo $bckgr_colour; ?>;
	font-size: 16px;
}
</style>
<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<?php if (!empty($quote_data)) { ?>
				<div class="row alert alert-ssid records-bar" role="alert">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<div class="row">
						
							<?php
                            if (!empty($customer_data)) { ?>
						
								<legend>Customer Details</legend>
								<div class="rows">
									<div class="row profile_view">
										<div class="row col-sm-12">
											<div class="right col-xs-12">
												<table style="width:100%;">
													<tr>
														<td width="30%"><i class="hide fa fa-user"></i><strong>Full Name</strong></td>
														<td><?php echo (!empty($customer_data[0]->customer_full_name)) ? (ucwords($customer_data[0]->customer_full_name)) : '' ; ?></td>
													</tr>
													<tr>
														<td width="30%"><i class="hide fa fa-briefcase"></i> <strong>Email</strong></td>
														<td width="60%"><?php echo (!empty($customer_data[0]->customer_email)) ? (ucwords($customer_data[0]->customer_email)) : '' ; ?></td>
													</tr>
													<tr>
														<td width="30%"><i class="hide fa fa-at text-bold"></i> <strong>Mobile</strong></td>
														<td width="60%"><?php echo (!empty($customer_data[0]->customer_mobile)) ? (ucwords($customer_data[0]->customer_mobile)) : '' ; ?></td>
													</tr>
													<tr>
														<td width="30%"><i class="hide fa fa-at text-bold"></i> <strong>Business Name</strong></td>
														<td width="60%"><?php echo (!empty($customer_data[0]->business_name)) ? (ucwords($customer_data[0]->business_name)) : '' ; ?></td>
													</tr>												
													<tr>
														<td width="30%"><i class="hide fa fa-at text-bold"></i> <strong>Vat Number</strong></td>
														<td width="60%"><?php echo (!empty($customer_data[0]->vat_number)) ? (ucwords($customer_data[0]->vat_number)) : '' ; ?></td>
													</tr>												
													<tr>
														<td width="30%"><i class="hide fa fa-at text-bold"></i> <strong>Company Number</strong></td>
														<td width="60%"><?php echo (!empty($customer_data[0]->company_number)) ? (ucwords($customer_data[0]->company_number)) : '' ; ?></td>
													</tr>												
												</table>
											</div>		
										</div>
									</div>
								</div>
							
							<?php
                            } elseif (!empty($site_data)) { ?>
								<legend>Site Details</legend>
								<div class="rows">
									<div class="row profile_view">
										<div class="row col-sm-12">
											<div class="right col-xs-12">
												<table style="width:100%;">
													<tr>
														<td width="30%"><i class="hide fa fa-user"></i><strong>Full Name</strong></td>
														<td><?php echo (!empty($site_data[0]->customer_full_name)) ? (ucwords($site_data[0]->customer_full_name)) : '' ; ?></td>
													</tr>
													<tr>
														<td width="30%"><i class="hide fa fa-briefcase"></i> <strong>Email</strong></td>
														<td width="60%"><?php echo (!empty($site_data[0]->customer_email)) ? (ucwords($site_data[0]->customer_email)) : '' ; ?></td>
													</tr>
													<tr>
														<td width="30%"><i class="hide fa fa-at text-bold"></i> <strong>Mobile</strong></td>
														<td width="60%"><?php echo (!empty($site_data[0]->customer_mobile)) ? (ucwords($site_data[0]->customer_mobile)) : '' ; ?></td>
													</tr>
													<tr>
														<td width="30%"><i class="hide fa fa-at text-bold"></i> <strong>Business Name</strong></td>
														<td width="60%"><?php echo (!empty($site_data[0]->business_name)) ? (ucwords($site_data[0]->business_name)) : '' ; ?></td>
													</tr>												
													<tr>
														<td width="30%"><i class="hide fa fa-at text-bold"></i> <strong>Vat Number</strong></td>
														<td width="60%"><?php echo (!empty($site_data[0]->vat_number)) ? (ucwords($site_data[0]->vat_number)) : '' ; ?></td>
													</tr>												
													<tr>
														<td width="30%"><i class="hide fa fa-at text-bold"></i> <strong>Company Number</strong></td>
														<td width="60%"><?php echo (!empty($site_data[0]->company_number)) ? (ucwords($site_data[0]->company_number)) : '' ; ?></td>
													</tr>												
												</table>
											</div>		
										</div>
									</div>
								</div>
							<?php
                            } ?>
						</div>

					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<legend>Quote Details <i class="fas fa-caret-down"></i></legend>
							<table style="width:100%;display:block" class="">
								<tr>
									<td width="30%"><i class="hide fa fa-user"></i> <strong>Status</strong></td>
									<td><?php echo ucwords($quote_data->quote_status); ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-briefcase"></i> <strong>Created Date</strong></td>
									<td width="60%"><?php echo date('d/m/Y H:i:s', strtotime($quote_data->created_on)); ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-briefcase"></i> <strong>Notes</strong></td>
									<td width="60%"><?php echo $quote_data->quote_notes; ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <strong>Number of items</strong></td>
									<td width="60%"><?php echo (!empty($quote_data->quote_items)) ? sizeof(array_filter($quote_data->quote_items)) : 'No items' ; ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <strong>Total Value</strong></td>
									<td width="60%"><?php echo (!empty($quote_data->quote_items)) ? "£".number_format(array_sum(array_column($quote_data->quote_items, "quoted_value")), 2, '.', ' ') : "£0.00" ; ?></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<div class="floating-pallet">
					<div class="row">
						<div class="col-md-2 col-sm-3 col-xs-4">
							<a class="btn btn-sm btn-quote btn-block" href="<?php echo base_url("webapp/quote/profile/".$this->uri->segment(4)."/quote_items"); ?>" role="button"><span class="hidden-xs">Quote </span>Items</a>
						</div>
						<div class="col-md-2 col-sm-3 col-xs-4">
							<a class="btn btn-sm btn-quote btn-block" href="<?php echo base_url("webapp/quote/profile/".$this->uri->segment(4)."/customer_details"); ?>" role="button">Customer Details</a>
						</div>
						<!-- <div class="col-md-2 col-sm-3 col-xs-4">
							<a class="btn btn-sm btn-quote btn-block" href="<?php echo base_url("webapp/quote/profile/".$this->uri->segment(4)."/notes"); ?>" role="button">Notes</a>
						</div>
						<div class="col-md-2 col-sm-3 col-xs-4">
							<a class="btn btn-sm btn-quote btn-block" href="<?php echo base_url("webapp/quote/profile/".$this->uri->segment(4)."/review"); ?>" role="button">Review</a>
						</div> -->
						<div class="col-md-2 col-sm-3 col-xs-4">
							<a class="btn btn-sm btn-quote btn-block" href="<?php echo base_url("webapp/quote/profile/".$this->uri->segment(4)."/export_quote"); ?>" role="button">Export Quote</a>
						</div>
						<div class="col-md-2 col-sm-3 col-xs-4">
							<a class="btn btn-sm btn-quote btn-block" href="<?php echo base_url("webapp/quote/profile/".$this->uri->segment(4)."/quote_2job"); ?>" role="button">Quote into Job</a>
						</div>
					</div>
					<div class="clear"></div>
				</div>
				
				<?php include $include_page; ?>

				<?php } else { ?>
					<span><?php echo $this->config->item('no_records'); ?></span>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<?php ## debug( $quote_data, "print", false )?>;
</div>