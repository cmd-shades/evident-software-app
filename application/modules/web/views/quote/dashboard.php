<?php
$router = $method = false;
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

<style>
table tr th,table tr td{
	padding: 5px 0;
}

@media (max-width: 480px) {
	.btn-info{
		margin-bottom:10px;
	}
}


.feedback{
	color: red;
	font-size: 24px;
	font-weight: 600;
	border: 1px solid red;
	padding: 10px 5px;
}

.money{
	text-align: right;
}

.btn-new_quote,
.btn-new_quote:hover,
.btn-new_quote:focus,
.btn-new_quote:active{
	color: #fff;
	border: none;
	margin-top: -6px;
	background: <?php echo $bckgr_colour; ?>;
	font-size: 16px;
}

.indic_line_2{
	font-size: 30px;
}

.row.alert.alert-ssid{
    padding-bottom: 30px;
}


.search_field{
	float: left;
    width: 80%;
    padding: 6px;
    border: 1px solid <?php echo $bckgr_colour; ?>;
    border-radius: 6px;
    font-size: 16px;
	padding-left: 20px;
}

.search_submit{
	padding: 6px 20px;
    font-size: 16px;
    border-radius: 6px;
    margin-left: -6px;
    border: 1px solid <?php echo $bckgr_colour; ?>;
    background-color: <?php echo $bckgr_colour; ?>;
    color: #fff;
}
</style>

<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row alert alert-ssid" role="alert">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="row">
							<div class="rows">
								<legend>Quote Manager</legend>
							</div>
							<div class="rows">
								<div class="row col-sm-3">
									<div class="rows text-center">
										<span class="indic_line_1">New Quotes</span>
										<br />
										<span class="indic_line_2"><?php echo $quick_stats->New; ?></span>
									</div>
								</div>
								
								<div class="row col-sm-3">
									<div class="rows text-center">
										<span class="indic_line_1">Sent Quotes</span>
										<br />
										<span class="indic_line_2"><?php echo $quick_stats->Sent; ?></span>
									</div>
								</div>
								<div class="row col-sm-3">
									<div class="rows text-center">
										<span class="indic_line_1">Accepted Quotes</span>
										<br />
										<span class="indic_line_2"><?php echo $quick_stats->Accepted; ?></span>
									</div>
								</div>
								<div class="row col-sm-3">
									<div class="rows text-center">
										<span class="indic_line_1">Declined Quotes</span>
										<br />
										<span class="indic_line_2"><?php echo $quick_stats->Declined; ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			
				<div class="row alert">
					<div class="col-md-4 col-sm-4 col-xs-12">
					<form action="" method="post">
						<input type="text" class="search_field" name="search" placeholder="Search for..." />
						<input type="submit" class="search_submit" value="Go" id="submit">
					</form>
					</div>
				</div>
				
				<div class="row alert alert-ssid" role="alert">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="row">
							<div class="rows">
								<div class="row profile_view">
									<div class="row col-sm-12">
										<div class="right col-xs-12">
											<?php
                                            if (!empty($quotes)) { ?>
												<table class="table sortable" id="">
													<thead>
														<tr>
															<th>Business Name</th>
															<th>Customer Name</th>
															<th>Site Id</th>
															<th>Quote Status</th>
															<th>Created On</th>
															<th>Number Items</th>
															<th class="money">Quote Value</th>
														</tr>
													</thead>
													<tbody>
														<?php
                                                        foreach ($quotes as $row) {
                                                            $link = "<a style='font-weight: 800; color: ".$bckgr_colour."' href=".base_url("webapp/quote/profile/".$row->quote_id).">"; ?>
															<tr class="c_<?php echo $row->customer_id; ?>" data-id="<?php echo $row->quote_id; ?>">
																<td data-label="Business Name"><?php echo $link;
																echo $row->business_name; ?></a></td>
																<td data-label="Customer Name"><?php echo $row->customer_first_name.' '.$row->customer_last_name; ?></td>
																<td data-label="Site Id"><?php echo $row->site_id; ?></td>
																<td data-label="Quote Status"><?php echo $row->quote_status; ?></td>
																<td data-label="Created On"><?php echo $row->created_on; ?></td>
																<td data-label="Quote Items"><?php echo (!empty($row->quote_items)) ? sizeof(array_filter($row->quote_items)) : "No quote items" ; ?> </td>
																<td data-label="Quote Value" class="money"><?php echo (!empty($row->quote_items)) ? "£".number_format(array_sum(array_column($row->quote_items, "quoted_value")), 2, '.', ' ') : "£0.00" ; ?></td>
															</tr>
														<?php
                                                        } ?>
													</tbody>
												</table>
											<?php
                                            } else { ?>
												<div class="row">
													<h1 class="mt-4 mb-3" style="color:#d66204;"><small>There is no data in the system</small></h1>
												</div>
											<?php
                                            } ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			
			<div class="row alert">
				<div class="col-md-2 col-sm-3 col-xs-4">
					<a class="btn btn-sm btn-block btn-new_quote" href="<?php echo base_url("/webapp/quote/add_quote"); ?>"><span class="hidden-xs">Create </span>new</a>
				</div>
			</div>
			
			<div class="row">
				<div class="floating-pallet">
					<div class="row">
						<div class="col-md-2 col-sm-3 col-xs-4">
						</div>
						
						<!-- <div class="col-md-2 col-sm-3 col-xs-4">
							<a class="btn btn-sm btn-orange btn-block" href="http://localhost/SimplySID_CMS/webapp/site/profile/3/jobs" role="button">Site Jobs</a>
						</div>
						<div class="col-md-2 col-sm-3 col-xs-4">
							<a class="btn btn-sm btn-orange btn-block" href="http://localhost/SimplySID_CMS/webapp/site/profile/3/dwellings" role="button">Dwellings</a>
						</div>
						<div class="col-md-2 col-sm-3 col-xs-4">
							<a class="btn btn-sm btn-orange btn-block" href="http://localhost/SimplySID_CMS/webapp/site/profile/3/documents" role="button">Documents</a>
						</div>
						<div class="col-md-2 col-sm-3 col-xs-4">
							<a class="btn btn-sm btn-orange btn-block" href="http://localhost/SimplySID_CMS/webapp/site/profile/3/contracts" role="button"><span class="hidden-xs">Linked </span>Contracts</a>
						</div> -->
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){

		//Submit form for processing
		/* $( '#update-site-btn' ).click( function( event ){

			event.preventDefault();
			var formData = $('#update-site-form').serialize();
			swal({
				title: 'Confirm site update?',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then((result) => {
				$.ajax({
					url:"http://localhost/SimplySID_CMS/webapp/site/update_site/3",
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
			}).catch(swal.noop)
		}); */
	});
</script>

<div class="row">
</div>

