<style>
	.width-50{
		width:50%;
	}

	.m-top-5{
		margin-top: 5px;
	}
	
	.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
		vertical-align: middle;
	}
	
</style>

<div class="row">
	<?php if ($this->user->is_admin || !empty($permissions->is_admin)) { ?>
		<div class="col-md-6 col-sm-6 col-xs-12">
			<form id="update-jobbase-rate-form" class="form-horizontal">
				<input type="hidden" name="page" value="details" />
				<input type="hidden" name="job_id" value="<?php echo $job_details->job_id; ?>" />
				<input type="hidden" name="site_id" value="<?php echo (!empty($job_details->site_id)) ? $job_details->site_id : ''; ?>" />
				<input type="hidden" name="customer_id" value="<?php echo (!empty($job_details->customer_id)) ? $job_details->customer_id : ''; ?>" />
				<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
				<input type="hidden" name="address_id" value="<?php echo (!empty($job_details->address_id)) ? $job_details->address_id : null; ?>" />
				<input type="hidden" name="job_type_id" id="hidden_job_type_id" value="<?php echo (!empty($job_details->job_type_id)) ? $job_details->job_type_id : null; ?>" />
				<input type="hidden" name="status_id" value="<?php echo (!empty($job_details->status_id)) ? $job_details->status_id : null; ?>" />
				<input type="hidden" name="job_tracking_id" value="<?php echo (!empty($job_details->job_tracking_id)) ? $job_details->job_tracking_id : null; ?>" />
				<div class="x_panel tile has-shadow">
					<legend>Job Base Rate</legend>

					<div class="row" >
						<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="input-group form-group" title="Default Job Base Rate as set in the Job Type configuration" >
								<label class="input-group-addon" >Default Rate (&pound;)</label>
								<input readonly class="form-control" type="text" placeholder="Default Job Base Rate" value="<?php echo (!empty($job_details->job_base_rate)) ? $job_details->job_base_rate : '0.00'; ?>" />
							</div>
						</div>
					</div>

					<div class="row" >
						<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="input-group form-group">
								<label class="input-group-addon">Adjustable Rate (&pound;)</label>
								<input id="adjustable-job-rate" <?php echo ($job_details->job_base_rate_adjustable == 1) ? 'name="base_rate"' : 'readonly'; ?> class="form-control adjustable-job-rate" type="text" placeholder="Adjustable Rate" value="<?php echo (!empty($job_details->base_rate)) ? $job_details->base_rate : '0.00'; ?>" />
							</div>
						</div>
					</div>

					<div class="row" >
					
						<div class="col-md-12 col-sm-12 col-xs-12" >							
							<?php if ($this->user->is_admin || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
								
								<?php if ($job_details->job_base_rate_adjustable == 1) { ?>
									<div class="row">
										<div class="col-md-6">
											<button class="update-job-base-rate-btn btn btn-sm btn-block btn-flow btn-success" type="button"  data-confirm_message="Base Rate"  >Adjust Base Rate</button>
										</div>
									</div>
								
								<?php } else { ?>
									<div class="row">
										<div class="col-md-6">
											<button disabled class="btn base-rate-not-adjustable btn-sm btn-block btn-flow btn-success" type="button" title="Please refer to the Job Type configuration to make this Adjustable"  >Base Rate Not Adjust</button>
										</div>
									</div>
								<?php } ?>
								
								
							<?php } else { ?>
								<div class="row col-md-6">
									<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-warning btn-next no-permissions" type="button" disabled >Insufficient permissions</button>
								</div>
							<?php } ?>						
						</div>
					</div>
					<br/><br/><br/>
				</div>
			</form>
		</div>
	<?php } ?>	

	<?php if ($this->user->is_admin || !empty($permissions->is_admin)) { ?>
		<div class="col-md-6 col-sm-6 col-xs-12">
			<form id="update-job-materials-rate-form" class="form-horizontal">
				<input type="hidden" name="page" value="details" />
				<input type="hidden" name="job_id" value="<?php echo $job_details->job_id; ?>" />
				<input type="hidden" name="site_id" value="<?php echo (!empty($job_details->site_id)) ? $job_details->site_id : ''; ?>" />
				<input type="hidden" name="customer_id" value="<?php echo (!empty($job_details->customer_id)) ? $job_details->customer_id : ''; ?>" />
				<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
				<input type="hidden" name="address_id" value="<?php echo (!empty($job_details->address_id)) ? $job_details->address_id : null; ?>" />
				<input type="hidden" name="job_type_id" id="hidden_job_type_id" value="<?php echo (!empty($job_details->job_type_id)) ? $job_details->job_type_id : null; ?>" />
				<input type="hidden" name="status_id" value="<?php echo (!empty($job_details->status_id)) ? $job_details->status_id : null; ?>" />
				<input type="hidden" name="job_tracking_id" value="<?php echo (!empty($job_details->job_tracking_id)) ? $job_details->job_tracking_id : null; ?>" />
				<div class="x_panel tile has-shadow">
					<legend>Additional Materials</legend>
					<div class="row" >
						<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="input-group form-group">
								<label class="input-group-addon">Additional Materials Rate (&pound;)</label>
								<input id="additional_materials_rate" name="additional_materials_rate" class="form-control adjustable-job-rate" type="text" placeholder="Additional Materials Rate" value="<?php echo (!empty($job_details->additional_materials_rate)) ? $job_details->additional_materials_rate : '0.00'; ?>" />
							</div>
						</div>
						<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="input-group form-group">
								<label class="input-group-addon">Additional Materials Details&nbsp;&nbsp;</label>
								<textarea name="additional_materials" type="text" class="form-control" rows="4"><?php echo (!empty($job_details->additional_materials)) ? $job_details->additional_materials : '' ?></textarea>
							</div>
						</div>
					</div>

					<div class="row" >
					
						<div class="col-md-12 col-sm-12 col-xs-12" >							
							<?php if ($this->user->is_admin || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
								
								<div class="row">
									<div class="col-md-6">
										<button class="update-job-additional-materials-btn btn btn-sm btn-block btn-flow btn-success" type="button" data-confirm_message="Additional Materials" >Update Job Additional Materials</button>
									</div>
								</div>
							
							<?php } else { ?>
								<div class="row col-md-6">
									<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-warning btn-next no-permissions" type="button" disabled >Insufficient permissions</button>
								</div>
							<?php } ?>						
						</div>
					</div>
				</div>
			</form>
		</div>
	<?php } ?>
</div>
<?php if ($this->user->is_admin || !empty($tab_permissions->can_view)  || !empty($tab_permissions->is_admin)) { ?>
<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Consumed Items<span class="pull-right"><?php if (!empty($job_details->consumed_items)) { ?> <span style="display:<?php echo (in_array(strtolower($job_details->job_tracking_status), [ 'call completed', 'invoice paid', 'job invoice'])) ? 'block' : 'none';  ?>" class="pointer download-stock-boms"><a href="<?php echo base_url('webapp/job/profile/'.$job_details->job_id.'/stock?action=download'); ?>" target="_blank"><i class="far fa-file-pdf text-red" title="Click to download this as a PDF" ></i></a></span> &nbsp; <?php } ?> &nbsp;<?php if ($this->user->is_admin || !empty($tab_permissions->is_admin)) { ?><small class="<?php echo (!in_array(strtolower($job_details->job_tracking_status), [ 'call completed', 'invoice paid', 'job invoice'])) ? '' : 'hide';  ?>" ><span title="Add consumed BOM Items" class="pointer add-consumed-bom-item"><i class="fas fa-plus" ></i> BOMs</span>&nbsp; | &nbsp;<span title="Add consumed Stock Items" class="pointer add-consumed-stock-item"><i class="fas fa-plus" ></i> Stock</span></small><?php } ?></span></legend>
			<?php $stock_total = $boms_total = 0.00; ?>
			<div class="accordion" id="accordion1" role="tablist" aria-multiselectable="true">
				<div class="panel">
					<div class="section-container-bar panel-heading collapsed bg-grey pointer no-radius" role="tab" id="headingOne" data-toggle="collapse" data-parent="#accordion1" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
						<h4 class="panel-title"> Stock Items (<?php echo (!empty($job_details->consumed_items->stock)) ? count($job_details->consumed_items->stock) : 0  ?>) <?php if ($this->user->is_admin || !empty($tab_permissions->is_admin)) { ?><span class="pull-right"><span class="pull-left"><strong><span id="stock_total" >0.00</span></strong></span><?php } ?><span class="pull-right"> &nbsp;<i class="caret-icon fas fa-caret-down text-yellow"></i></span></h4>
					</div>
					<div id="collapseOne" class="panel-collapse no-bg no-background <?php echo (!empty($toggled_section) && (strtolower($toggled_section) == 'stock')) ? 'show_toggled' : 'collapse'; ?>" role="tabpanel" aria-labelledby="headingOne" >
						<div class="panel-body">
							<div class="row table-responsive" style="width:100%; overflow:auto">
								<table class="table" style="font-size:90%; overflow:hidden">
									<thead>
										<tr>
											<!-- <th width="10%">Job ID</th> -->
											<th width="10%">Item Code</th>
											<th width="30%">Item Name</th>
											<th width="10%">Item Type</th>
											<?php if ($this->user->is_admin || !empty($tab_permissions->is_admin)) { ?>
												<th width="12%">Confirmed</th>
												<th width="12%">Qty</th>
												<th width="14%">Unit Price</th>
												<th width="10%"><span class="pull-right">Total</span></th>
												<th width="2%">&nbsp;</th>
											<?php } ?>
										</tr>
									</thead>
									<tbody>
										<?php if (!empty($job_details->consumed_items->stock)) { ?>
											<?php foreach ($job_details->consumed_items->stock as $key => $stock_item) { ?>
												<tr data-record_id="<?php echo $stock_item->id; ?>" data-toggle_section="stock" >
													<!-- <td><a href="<?php echo base_url("webapp/job/profile/".$stock_item->job_id.'/stock'); ?>" ><?php echo (!empty($current_job) && ($current_job == $stock_item->job_id)) ? '' : $stock_item->job_id; ?></a></td> -->
													<td><a href="<?php echo base_url("webapp/job/stock?item_code=".$stock_item->item_code); ?>" ><?php echo $stock_item->item_code; ?></a></td>
													<td><?php echo $stock_item->item_name; ?></td>
													<td><?php echo (!empty($stock_item->item_type)) ? strtoupper($stock_item->item_type) : 'BOM'; ?></td>
													
													<?php if ($this->user->is_admin || !empty($tab_permissions->is_admin)) { ?>
														<td><?php echo ($stock_item->is_confirmed == 1) ? '<i class="far fa-check-circle text-green"></i> Yes' : '<i class="far fa-times-circle text-red"></i> No' ; ?></td>
														<?php if (($this->user->is_admin || !empty($tab_permissions->is_admin)) && $stock_item->is_confirmed != 1) { ?>
															<td>
																<span class="form-group">
																	<input type="number" value="<?php echo $stock_item->item_qty; ?>" id="item_qty<?php echo $stock_item->id ?>" title="Adjust Quantity" data-prev_qty="<?php echo $stock_item->item_qty; ?>" data-col_type="qty" class="form-control editable-item"  />
																</span>
																<small class ="m-top-5 pull-left tiny-fdbck-msg qty-fdback-msg<?php echo $stock_item->id ?>"></small>
															</td>													
															<td>
																<span>
																	<span class="form-group">
																		<input type="text" value="<?php echo(number_format($stock_item->price, 2)); ?>" id="item_price<?php echo $stock_item->id ?>" title="Adjust Item Price" data-prev_price="<?php echo $stock_item->price; ?>" data-col_type="price" class="form-control numbers-only editable-item"  />
																	</span>
																	<small class ="m-top-5 pull-left tiny-fdbck-msg price-fdback-msg<?php echo $stock_item->id ?>"></small>
																</span>															
															</td>
															<td><span class="pull-right"><?php echo(number_format($stock_item->price*$stock_item->item_qty, 2)); ?></span></td>
															<td class="text-center" >&nbsp; <span data-record_id="<?php echo $stock_item->id; ?>" class="delete-consumed-item pointer" title="Click to delete this item from the list" ><i class="fas fa-times text-red"></i></span></span></td>
														<?php } else { ?>
															<td><?php echo $stock_item->item_qty; ?></td>
															<td><?php echo(number_format($stock_item->price, 2)); ?></td>
															<td><span class="pull-right"><?php echo(number_format($stock_item->price*$stock_item->item_qty, 2)); ?></span></td>
															<td>&nbsp;</td>
														<?php } ?>
													<?php } ?>
												</tr>
											<?php $stock_total += ($stock_item->price*$stock_item->item_qty);
											$current_job = $stock_item->job_id;
											} ?>
											<?php if ($this->user->is_admin || !empty($tab_permissions->is_admin)) { ?>
												<tr>
													<th colspan="6">Stock Total</th>
													<th id="stock_total_tbl" ><span class="pull-right">&pound;<?php echo number_format(($stock_total), 2); ?></span></th>
													<th>&nbsp;</th>
												</tr>
											<?php } ?>
										<?php } else { ?>
											<tr>
												<td colspan="8" >There's currently no data to display</td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<br/>
			<div class="accordion" id="accordion2" role="tablist" aria-multiselectable="true">
				<div class="panel">
					<div class="section-container-bar panel-heading collapsed bg-grey pointer no-radius" role="tab" id="headingTwo" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
						<h4 class="panel-title">BOMs (<?php echo (!empty($job_details->consumed_items->boms)) ? count($job_details->consumed_items->boms) : 0  ?>) <?php if ($this->user->is_admin || !empty($tab_permissions->is_admin)) { ?><span class="pull-right"><span class="pull-left"><strong><span id="bom_total" >0.00</span></strong><?php } ?> </span><span class="pull-right">&nbsp;<i class="caret-icon fas fa-caret-down text-yellow"></i></span></h4>
					</div>
					<div id="collapseTwo" class="panel-collapse no-bg no-background <?php echo (!empty($toggled_section) && (strtolower($toggled_section) == 'boms')) ? 'show_toggled' : 'collapse'; ?>" role="tabpanel" aria-labelledby="headingTwo" data-toggled="false" >
						<div class="panel-body">
							<div class="row table-responsive" style="width:100%; overflow:auto">
								<table class="table" style="font-size:90%; overflow:hidden">
									<thead>
										<tr>
											<!-- <th width="10%">Job ID</th> -->
											<th width="10%">Item Code</th>
											<th width="30%">Item Name</th>
											<th width="10%">Item Type</th>
											<?php if ($this->user->is_admin || !empty($tab_permissions->is_admin)) { ?>
												<th width="12%">Confirmed</th>
												<th width="12%">Qty</th>
												<th width="14%">Unit Price</th>
												<th width="10%"><span class="pull-right">Total</span></th>
												<th width="2%">&nbsp;</th>
											<?php } ?>
										</tr>
									</thead>
									<tbody>
										<?php if (!empty($job_details->consumed_items->boms)) { ?>
											<?php foreach ($job_details->consumed_items->boms as $key => $bom_item) { ?>
												<tr data-record_id="<?php echo $bom_item->id; ?>"  data-toggle_section="boms" >
													<!-- <td><a href="<?php echo base_url("webapp/job/profile/".$bom_item->job_id.'/stock'); ?>" ><?php echo (!empty($curr_job) && ($curr_job == $bom_item->job_id)) ? '' : $bom_item->job_id; ?></a></td> -->
													<td><a href="<?php echo base_url("webapp/job/boms?item_code=".$bom_item->item_code); ?>" ><?php echo $bom_item->item_code; ?></a></td>
													<td><?php echo $bom_item->item_name; ?></td>
													<td><?php echo (!empty($bom_item->item_type)) ? strtoupper($bom_item->item_type) : 'BOM'; ?></td>
													<?php if ($this->user->is_admin || !empty($tab_permissions->is_admin)) { ?>
														<td><?php echo ($bom_item->is_confirmed == 1) ? '<i class="far fa-check-circle text-green"></i> Yes' : '<i class="far fa-times-circle text-red"></i> No' ; ?></td>
														<?php if (($this->user->is_admin || !empty($tab_permissions->is_admin)) && $bom_item->is_confirmed != 1) { ?>
															<td>
																<span class="form-group">
																	<input type="number" value="<?php echo $bom_item->item_qty; ?>" id="item_qty<?php echo $bom_item->id ?>" title="Adjust Quantity" data-prev_qty="<?php echo $bom_item->item_qty; ?>" data-col_type="qty" class="form-control editable-item"  />
																</span>
																<small class ="m-top-5 pull-left tiny-fdbck-msg qty-fdback-msg<?php echo $bom_item->id ?>"></small>
															</td>													
															<td>
																<span>
																	<span class="form-group">
																		<input type="text" value="<?php echo(number_format($bom_item->price, 2)); ?>" id="item_price<?php echo $bom_item->id ?>" title="Adjust Item Price" data-prev_price="<?php echo $bom_item->price; ?>" data-col_type="price" class="form-control numbers-only editable-item"  />
																	</span>
																	<small class ="m-top-5 pull-left tiny-fdbck-msg price-fdback-msg<?php echo $bom_item->id ?>"></small>
																</span>															
															</td>
															<td><span class="pull-right"><?php echo(number_format($bom_item->price*$bom_item->item_qty, 2)); ?></span></td>
															<td class="text-center">&nbsp; <span data-record_id="<?php echo $bom_item->id; ?>" class="delete-consumed-item pointer" title="Click to delete this item from the list" ><i class="fas fa-times text-red"></i></span></span></td>
														<?php } else { ?> 
															<td><?php echo $bom_item->item_qty; ?></td>
															<td><?php echo(number_format($bom_item->price, 2)); ?></td>
															<td><span class="pull-right"><?php echo(number_format($bom_item->price*$bom_item->item_qty, 2)); ?></span></td>
															<td>&nbsp;</td>
														<?php } ?>
													<?php } ?>
												</tr>
											<?php $boms_total += ($bom_item->price*$bom_item->item_qty);
											$curr_job = $bom_item->job_id;
											} ?>
											<?php if ($this->user->is_admin || !empty($tab_permissions->is_admin)) { ?>
												<tr>
													<th colspan="6">BOMs Total</th>
													<th id="bom_total_tbl" ><span class="pull-right">&pound;<?php echo number_format(($boms_total), 2); ?></span></th>
													<th>&nbsp;</th>
												</tr>
											<?php } ?>
										<?php } else { ?>
											<tr>
												<td colspan="8" >There's currently no data to display</td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<br/>
			<div class="accordion" id="accordion3" role="tablist" aria-multiselectable="true">
				<?php if ($this->user->is_admin || !empty($tab_permissions->is_admin)) { ?>
					<div class="panel">
						<div class="section-container-bar panel-heading collapsed bg-grey no-radius" role="tab" id="headingThree" data-toggle="collapse" data-parent="#accordion3" href="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
							<?php
                                $job_base_rate 			= (!empty($job_details->base_rate)) ? $job_details->base_rate : '0.00';
				    $additional_materials 	= (!empty($job_details->additional_materials_rate)) ? $job_details->additional_materials_rate : '0.00';
				    ?>
							<h4 class="panel-title"> Grand Total <span class="pull-right"><span class="pull-left"><strong><span id="bom_total" >&pound; <?php echo number_format(($stock_total + $boms_total + $job_base_rate + $additional_materials), 2); ?></span></strong>&nbsp;&nbsp;&nbsp;&nbsp;</span></h4>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
	
	<?php /*<div class="col-md-6 col-sm-6 col-xs-12">
        <div class="x_panel tile has-shadow">
            <legend>Consumed Stock</legend>
            <div class="required_items">
                <?php if( !empty( $job_details->required_items ) ) { ?>
                <table class="table table-responsive" style="width:100%">
                    <tr>
                        <th width="20%">Item Code</th>
                        <th width="53%">Item Name</th>
                        <th width="12%" class="text-center">Quantity</th>
                        <th width="15%"><span class="pull-right">Action</span></th>
                    </tr>
                    <?php if( !empty( $job_details->required_items ) ){ foreach( $job_details->required_items as $req_item ) { ?>
                        <tr>
                            <td><?php echo $req_item->item_code; ?></td>
                            <td><?php echo $req_item->item_name; ?></td>
                            <td class="text-center" data-record_id="<?php echo $req_item->id; ?>" >
                                <span class="form-group"><input type="number" value="<?php echo $req_item->item_qty; ?>" id="quntity<?php echo $req_item->id ?>" title="Change this number to update Quantity instantly" data-prev_qty="<?php echo $req_item->item_qty; ?>" class="form-control change-qty"  /></span>
                                <small class ="tiny-fdbck-msg fdback-msg<?php echo $req_item->id ?>"></small>
                            </td>
                            <td class="text-center" data-record_id="<?php echo $req_item->id; ?>" ><span class="pull-right"><span class="edit-item-qty pointer hide" title="Click to edit this Qty for this item"><i class="fas fa-pencil-alt text-blue"></i></span> &nbsp;  &nbsp;  &nbsp; <span class="delete-stock-item pointer" title="Click to delete this item from the list" ><i class="far fa-trash-alt text-red"></i></span></span></td>
                        </tr>
                    <?php } } ?>
                </table>
            <?php }else{ ?>
                <?php echo $this->config->item('no_records'); ?>
            <?php } ?>
            </div>
        </div>
    </div> */ ?>
	
	
	<!-- Modal for adding Stock Items -->
	<div class="modal fade add-consumed-stock-items-modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title" id="myAddStockItemModalLabel">Add Consumed Stock Items</h4>						
				</div>
				<div class="modal-body" id="stock-items-modal-container" >
					<div class="col-md-12 col-sm-12 col-xs-12 form-group top_search right">
						<!-- Search bar -->
						<div class="input-group" style="width: 100%;">
							<input type="text" id="stock_search" class="form-control <?php echo $module_identier; ?>-search_input" id="search_term" value="" placeholder="Search stock items">
						</div>
						<div id="no-stock-warning" class="text-red" style="display:none;">You do not currently have searchable Stock items on the system!</div>
					</div>
					<div id="stock-items-container" style="display:none" >
						<input type="hidden" name="page" value="details" />
						<input type="hidden" name="job_id" value="<?php echo $job_details->job_id; ?>" />
						<input type="hidden" name="item_type" value="stock" />
						<input type="hidden" name="add_type" value="consumed_items" />
						<div class="row">
							<div class="col-md-8 col-sm-8 col-xs-8 form-group"><strong>Item Name</strong></div>
							<div class="col-md-2 col-sm-2 col-xs-2 form-group"><strong>Qty</strong></div>
							<div class="col-md-2 col-sm-2 col-xs-2 form-group text-center"><strong>Remove</strong></div>
							<hr/>
						</div>
						<div id="append-stock-items"></div>						
					</div>
				</div>
				
				<div class="modal-footer">
					<button id="add-stock-items-btn" data-item_type="stock" class="add-consumed-items-btn btn btn-success btn-sm">Add Items</button>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Modal for adding Stock Items -->
	<div class="modal fade add-consumed-bom-items-modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title" id="myAddBOMItemModalLabel">Add Consumed BOM Items</h4>						
				</div>
				<div class="modal-body" id="bom-items-modal-container" >
					<div class="col-md-12 col-sm-12 col-xs-12 form-group top_search right">
						<!-- Search bar -->
						<div class="input-group" style="width: 100%;">
							<input type="text" id="bom_search" class="form-control <?php echo $module_identier; ?>-search_input" id="search_term" value="" placeholder="Search BOM items">
						</div>
						<div id="no-bom-warning" class="text-red" style="display:none;">You do not currently have searchable BOM items on the system!</div>
					</div>
					<div id="bom-items-container" style="display:none" >
						<input type="hidden" name="page" value="details" />
						<input type="hidden" name="job_id" value="<?php echo $job_details->job_id; ?>" />
						<input type="hidden" name="item_type" value="bom" />
						<input type="hidden" name="add_type" value="consumed_items" />
						<div class="row">
							<div class="col-md-8 col-sm-8 col-xs-8 form-group"><strong>Item Name</strong></div>
							<div class="col-md-2 col-sm-2 col-xs-2 form-group"><strong>Qty</strong></div>
							<div class="col-md-2 col-sm-2 col-xs-2 form-group text-center"><strong>Remove</strong></div>
							<hr/>
						</div>
						<div id="append-bom-items"></div>						
					</div>
				</div>
				
				<div class="modal-footer">
					<button id="add-bom-items-btn" data-item_type="bom" class="add-consumed-items-btn btn btn-success btn-sm">Add Items</button>
				</div>
			</div>
		</div>
	</div>
	
</div>

<?php } else {?>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel tile has-shadow">
				<legend>Consumed Items</legend>
				<p>You do not have permissions to view this page!</p>
			</div>
		</div>
	</div>
<?php } ?>

<script>
	$( document ).ready(function(){
		
		$( '.item-container-bar' ).click( function(){
			$( this ).closest( 'i' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');			
		});
		
		if( $( '#stock_total_tbl' ).text().length > 0 ){
			$( '#stock_total' ).text( $( '#stock_total_tbl' ).text() );
		}
		
		if( $( '#bom_total_tbl' ).text().length > 0 ){
			$( '#bom_total' ).text( $( '#bom_total_tbl' ).text() );
		}
		
		$( '.stock-header' ).click( function(){
			$( '.stock-table' ).slideToggle( 'slow' );
		} );
		
		$( '.bom-header' ).click( function(){
			$( '.bom-table' ).slideToggle( 'slow' );
		} );
			
		$( '.download-stock-boms' ).click( function(){
			$( '#download_consumed_items' ).submit();
		});
		
		var jobId = "<?php echo $job_details->job_id; ?>";
		
		//Update Item Qty
		$( '.editable-item' ).change( function(){
			$( '.tiny-fdbck-msg' ).text( '' );
			
			var recordId 		= $( this ).closest( 'tr' ).data( 'record_id' ),
				toggledSection 	= $( this ).closest( 'tr' ).data( 'toggle_section' ),
				colType 		= $( this ).data( 'col_type' );
			
			var qty			= $( '#item_qty'+recordId ).val();
			var itemPrice 	= $( '#item_price'+recordId ).val();
			
			var prevQty  	= $( this ).data( 'prev_qty' );
			var prevPrice  	= $( this ).data( 'prev_price' );
			
			if( qty < 1 ){
				swal({
					type: 'warning',
					title: 'Quantity must be atleast 1 unit',
					text: 'If you want to remove the item entirely, please use the delete button'
				});
				$( '#item_qty' + recordId ).val( prevQty );
				return false;
			}

			//Fire-off update Qty api
			$.ajax({
				url:"<?php echo base_url('webapp/job/update_consumed_items/'); ?>",
				method:"post",
				data:{ job_id: jobId, id: recordId, item_qty:qty, price:itemPrice, toggled:toggledSection },
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						$( '.'+colType+'-fdback-msg'+recordId ).html( '<span class="text-green">'+( ucwords( colType ) )+' adjusted successfully</span>' ).delay(2000).fadeOut();
						window.setTimeout(function(){
							var new_url = window.location.href.split('?')[0];
							window.location.href = new_url + "?toggled=" + toggledSection;
						},2001);
						
					}else{
						swal({
							type: 'error',
							title: data.status_msg
						})
					}		
				}
			});
			
		});
		
		$('#stock_search').blur( function(){
			$( '#no-stock-warning' ).hide();
		});
		
		$('#bom_search').blur( function(){
			$( '#no-bom-warning' ).hide();
		});
		
		$( '.add-consumed-items-btn' ).click( function(){
			
			var itemType = $( this ).data( 'item_type' );

			var formData = $( "#"+itemType+"-items-container :input").serialize();

			
			$.ajax({
				url:"<?php echo base_url('webapp/job/add_job_consumed_items/'); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						
						$( '.add-consumed-'+itemType+'items-modal' ).modal( 'hide' );
						
						$( '.modal-backdrop' ).remove();
						
						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 3000
						})
						
						window.setTimeout(function(){ 
							location.reload();
						} ,1000);
					} else {
						swal({
							type: 'error',
							title: data.status_msg
						})
					}		
				}
			});
			return false;
		});
		
		var stockItems 	= <?php echo !empty($stock_items) ? $stock_items : []; ?>;
		var bomItems 	= <?php echo !empty($bom_items) ? $bom_items : []; ?>;
		
		//======= BEGIN STOCK ITEMS SEARCH ======
		var i   	  = 1,
			maxFields = 10;
		if( stockItems.length > 0 ){
			
			$( '#no-stock-warning' ).hide();
			$('#stock_search').each( function( i, e ) {
				var dataList = $(e);
				dataList.autocomplete({
					//source: stockItems,
					source: function(request, response){
						var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
						response( $.grep( stockItems, function( value ) {
							return matcher.test( value.label ) || matcher.test( value.value ) || matcher.test( value.item_category ) || matcher.test( value.item_name );
						}));
					},
					select: function( event , data ) {
						$( '#stock-items-container' ).show();
						var itemCode 	  = escapeHtml( data.item.value ),  
							itemClassName = escapeHtml( data.item.value ),
							selectedItem  = escapeHtml( data.item.label ),
							buyPrice  	  = escapeHtml( data.item.buy_price ),
							sellPrice  	  = escapeHtml( data.item.sell_price );
							
							buyPrice  	  = ( buyPrice  != undefined ) ? parseFloat( buyPrice ).toFixed( 2 ) : null;
							sellPrice  	  = ( sellPrice != undefined ) ? parseFloat( sellPrice ).toFixed( 2 ) : null;
							
						if( i < maxFields ){							
							var numItems = parseInt( $('div .'+itemClassName).length );
							if( numItems > 0 ){
								swal({
									text: 'This Stock Item is already selected! Please update Quantity.'
								})
								return false;
							} else {
								$( this ).val( '' ); 
								i++;
								var appendItem = '<div class="row new-item '+itemClassName+'" id="'+itemClassName+'">';
										appendItem += '<div class="col-md-8 col-sm-8 col-xs-8 form-group">';
											appendItem += '<input type="hidden" name="consumed_items['+itemCode+'][job_id]" value="'+jobId+'" />';
											appendItem += '<input type="hidden" name="consumed_items['+itemCode+'][item_code]" value="'+itemCode+'" />';
											appendItem += '<input type="hidden" name="consumed_items['+itemCode+'][price]" value="'+buyPrice+'" />';
											appendItem += '<input type="hidden" name="consumed_items['+itemCode+'][price_adjusted]" value="'+buyPrice+'" />';
											appendItem += '<input type="text"   value="'+selectedItem+'" readonly class="form-control" />';
										appendItem += '</div>';
										appendItem += '<div class="col-md-2 col-sm-2 col-xs-2 form-group">';
											appendItem += '<input type="number" name="consumed_items['+itemCode+'][item_qty]" value="1" min="1" class="form-control" title="You can reduce existing quantities by setting this to a minus (-) value" />';
										appendItem += '</div>';
										appendItem += '<div class="col-md-2 col-sm-2 col-xs-2 text-center pointer removeme" data-content_wrapper="'+itemClassName+'" title="Click to remove this item">';
											appendItem += '<i class="fas fa-times fa-2x text-red"></i>';
										appendItem += '</div>';
									appendItem += '</div>';
								
								$( '#append-stock-items' ).prepend( appendItem );
							}						
						}
						return false;
					}
				});
			});
		} else {
			$('#stock_search').focus( function(){
				$( '#no-stock-warning' ).show( );
			});
		}
		
		//Remove item from list
		$( '#append-stock-items' ).on( 'click', '.removeme',function(){
			var classId = $( this ).data( 'content_wrapper' );
			$( '#'+classId ).remove();			
			var numItems = parseInt( $( 'div .new-item' ).length );
			if( numItems == 0 ){
				$( '#stock-items-container' ).hide();
			}
		});
		
		$( '.add-consumed-stock-item' ).click( function(){
			$( ".add-consumed-stock-items-modal" ).modal("show");
			$( "#stock_search" ).autocomplete( "option", "appendTo", "#stock-items-modal-container" );
		} );

		//======= END STOCK ITEMS SEARCH ======


		//======= BEGIN BOM ITEMS SEARCH ======
		var i   	  	 = 1,
			maxBomFields = 10;
		if( bomItems.length > 0 ){
			
			$( '#no-bom-warning' ).hide();
			$( '#bom_search').each( function( i, e ) {
				var dataList = $(e);
				dataList.autocomplete({
					//source: bomItems,
					source: function(request, response){
						var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
						response( $.grep( bomItems, function( value ) {
							return matcher.test( value.label ) || matcher.test( value.value ) || matcher.test( value.item_category ) || matcher.test( value.item_name );
						}));
					},
					select: function( event , data ) {
						$( '#bom-items-container' ).show();
						var itemCode 	  = escapeHtml( data.item.value ),  
							itemClassName = escapeHtml( data.item.value ),
							selectedItem  = escapeHtml( data.item.label ),
							buyPrice  	  = escapeHtml( data.item.buy_price ),
							sellPrice  	  = escapeHtml( data.item.sell_price );
							
							buyPrice  	  = ( buyPrice  != undefined ) ? parseFloat( buyPrice ).toFixed( 2 ) : null;
							sellPrice  	  = ( sellPrice != undefined ) ? parseFloat( sellPrice ).toFixed( 2 ) : null;
							
						if( i < maxBomFields ){							
							var numItems = parseInt( $('div .'+itemClassName).length );
							if( numItems > 0 ){
								swal({
									text: 'This bom Item is already selected! Please update Quantity.'
								})
								return false;
							} else {
								$( this ).val( '' ); 
								i++;
								var appendItem = '<div class="row new-item '+itemClassName+'" id="'+itemClassName+'">';
										appendItem += '<div class="col-md-8 col-sm-8 col-xs-8 form-group">';
											appendItem += '<input type="hidden" name="consumed_items['+itemCode+'][job_id]" value="'+jobId+'" />';
											appendItem += '<input type="hidden" name="consumed_items['+itemCode+'][item_code]" value="'+itemCode+'" />';
											appendItem += '<input type="hidden" name="consumed_items['+itemCode+'][price]" value="'+buyPrice+'" />';
											appendItem += '<input type="hidden" name="consumed_items['+itemCode+'][price_adjusted]" value="'+buyPrice+'" />';
											appendItem += '<input type="text"   value="'+selectedItem+'" readonly class="form-control" />';
										appendItem += '</div>';
										appendItem += '<div class="col-md-2 col-sm-2 col-xs-2 form-group">';
											appendItem += '<input type="number" name="consumed_items['+itemCode+'][item_qty]" value="1" min="1" class="form-control" title="You can reduce existing quantities by setting this to a minus (-) value" />';
										appendItem += '</div>';
										appendItem += '<div class="col-md-2 col-sm-2 col-xs-2 text-center pointer removeme" data-content_wrapper="'+itemClassName+'" title="Click to remove this item">';
											appendItem += '<i class="fas fa-times fa-2x text-red"></i>';
										appendItem += '</div>';
									appendItem += '</div>';
								
								$( '#append-bom-items' ).prepend( appendItem );
							}						
						}
						return false;
					}
				});
			});
		} else {
			$('#bom_search').focus( function(){
				$( '#no-bom-warning' ).show( );
			});
		}
		
		//Remove BOM item from list
		$( '#append-bom-items' ).on( 'click', '.removeme',function(){
			var classId = $( this ).data( 'content_wrapper' );
			$( '#'+classId ).remove();			
			var numItems = parseInt( $( 'div .new-item' ).length );
			if( numItems == 0 ){
				$( '#bom-items-container' ).hide();
			}
		});
		
		$( '.add-consumed-bom-item' ).click( function(){
			$( ".add-consumed-bom-items-modal" ).modal( "show" );
			$( "#bom_search" ).autocomplete( "option", "appendTo", "#bom-items-modal-container" );
		} );
		
		//======= END BOM ITEMS SEARCH ======
		
		//Ecape HTML special chars
		function escapeHtml( text ) {
			var map = {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;'
			};

			//return text.replace(/[&<>"']/g, function(m) { return map[m]; });
			return text;
		}

		//Delete Consumed Item from
		$( '.delete-consumed-item' ).click( function( e ){
			e.preventDefault();
			var itemId = $( this ).data( 'record_id' ),
				toggledSection 	= $( this ).closest( 'tr' ).data( 'toggle_section' );
				
			swal({
				title: 'Confirm delete Item?',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url: "<?php echo base_url('webapp/job/delete_consumed_item/'); ?>",
						method: "POST",
						data:{ 'page':'details', job_id:jobId, id:itemId },
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 1500
								})
								window.setTimeout( function(){
									var new_url = window.location.href.split('?')[0];
									window.location.href = new_url + "?toggled=" + toggledSection;
								}, 1500 );
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
		
		
		$( '.base-rate-not-adjustable' ).click( function(){
			swal({
				title: 'Job Rate Not Adjustable!',
				text: 'Please refer to the Job Type configuration to make this Adjustable'
			})
		});
		
		//Submit form for processing
		$( '.update-job-base-rate-btn, .update-job-additional-materials-btn' ).click( function( event ){

			var	confirmMessage = $( this ).data( 'confirm_message' );
			var jobTypeId = $( '#hidden_job_type_id' ).val();
			
			if( jobTypeId.length == 0 || jobTypeId === undefined ){
				swal({
					type: 'error',
					title: 'Something went wrong!',
					text: 'Please resubmit the form'
				});
				return false;
			}

			var formID = $( this ).closest( 'form' ).attr( 'id' );
			event.preventDefault();
			var formData = $('#'+formID ).serialize();

			swal({
				title: ( confirmMessage ) ? 'Confirm Adjust Job '+confirmMessage : 'Confirm Adjust Job Rate details?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/update_job/').$job_details->job_id; ?>",
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
			}).catch( swal.noop )
		});
	});
</script>