<div class="row">
	<div class="col-md-6 col-sm-6 col-xs-12 hide">
		<div class="x_panel tile has-shadow">
			<legend>Add New Contract</legend>
			<div class="row">
				
			</div>
		</div>		
	</div>

	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Existing Contracts On this Site</legend>
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12 table-responsive">								
					
					<table style="width:100%">
						<!-- <tr>
							<th width="80%">CONTRACT TYPE</th>
							<th width="20%"><span class="pull-right">COUNT</span></span></th>							
						</tr> -->
						<?php if (!empty($site_contracts)) {
						    foreach ($site_contracts as $contract_type=>$contract_details) { ?>
							<tr class="contract-toggle pointer" data-class_grp="<?php echo preg_replace('/[^A-Za-z0-9\-]/', '', $contract_type); ?>" >
								<td style="width:80%"><strong><?php echo ucwords($contract_type); ?></strong></td>
								<td><span class="pull-right"><strong>Total (<?php echo count($contract_details); ?>)</strong></span></td>
							</tr>

							<tr class="<?php echo preg_replace('/[^A-Za-z0-9\-]/', '', $contract_type); ?>" style="display:none">
								<td colspan="2" style="width:100%">
									<table class="table" style="width:100%">
										<tr style="font-size:90%">
											<th width="10%">Id</th>
											<th width="40%">Contract Name</th>
											<th width="25%">Contract Status</th>
											<th width="25%"><span class="pull-right">Lead Person</span></th>
										</tr>
										<?php foreach ($contract_details as $k=>$contract) { ?>
											<tr>
												<td><a href="<?php echo base_url('/webapp/contract/profile/'.$contract->contract_id); ?>" ><?php echo $contract->contract_id; ?></a></span></td>
												<td><a href="<?php echo base_url('/webapp/contract/profile/'.$contract->contract_id); ?>" ><?php echo ucwords($contract->contract_name); ?></a></td>
												<td><?php echo $contract->status_name; ?></td>
												<td><span class="pull-right"><?php echo $contract->lead_person; ?></span></td>
											</tr>
										<?php } ?>	
									</table>
								</td>
							</tr>
						<?php }
						    } else { ?>
							<tr>
							<td colspan="2"><?php echo $this->config->item('no_records'); ?></td>
						</tr>
						<?php } ?>
					</table>
				</div>
			</div>
		</div>		
	</div>
</div>

<script>
	$(document).ready(function(){
		$('.contract-toggle').click(function(){
			var classGrp = $(this).data( 'class_grp' );
			$( '.'+classGrp ).slideToggle();
		});
	});
</script>