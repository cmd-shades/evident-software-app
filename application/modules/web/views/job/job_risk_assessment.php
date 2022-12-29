<style>
	.accordion .panel-heading {
		background: transparent;
		padding: 13px;
		width: 100%;
		display: block
	}
	.accordion .panel:hover {
		background: transparent
	}
</style>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Risks &amp; Method Statements</legend>
			<?php if (!empty($ra_records[0])) { ?>
				
				<div class="panel">
					<div class="no-radius">
						<div class="row" >
							<div class="col-md-6 col-sm-6 col-xs-12">
								<table style="width:50%">
									<tr>
										<td width="30%" class="strong" >Assessment ID:</td>
										<td><?php echo $ra_records[0]->assessment_id; ?></td>
									</tr>
									<tr>
										<td width="30%" class="strong" >Date Submitted:</td>
										<td><?php echo date('d-m-Y H:i a', strtotime($ra_records[0]->date_created)); ?></td>
									</tr>
									<tr>
										<td width="30%" class="strong" >Submitted By:</td>
										<td><?php echo !empty($ra_records[0]->created_by) ? $ra_records[0]->created_by : ''; ?></td>
									</tr>
								</table>
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<table style="width:50%">
									<tr>
										<td width="30%" class="strong" >Expected Risks:</td>
										<td><?php echo $ra_records[0]->ra_expected_risks; ?></td>
									</tr>
									<tr>
										<td width="30%" class="strong" >Completed Risks:</td>
										<td><?php echo (!empty($ra_records[0]->ra_responses)) ? count($ra_records[0]->ra_responses) : 0; ?></td>
									</tr>
									<tr>
										<td width="30%" class="strong" >Status:</td>
										<td><?php echo $ra_records[0]->status; ?> <i class="far <?php echo ($ra_records[0]->risks_completed == 1) ? ' fa-check-circle text-green ' : ' fas fa-spinner text-orange pointer'; ?>"></i></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
			
			<div class="accordion" id="accordion1" role="tablist" aria-multiselectable="true">
				<div class="panel">
					<div class="section-container-bar panel-heading collapsed bg-grey pointer no-radius" role="tab" id="headingOne" data-toggle="collapse" data-parent="#accordion1" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
						<h4 class="panel-title"> Associated Risks (<?php echo (!empty($job_details->associate_risks)) ? count($job_details->associate_risks) : 0  ?>) <span class="pull-right"><i class="caret-icon fas fa-caret-down text-yellow"></i></span></h4>
					</div>
					<div id="collapseOne" class="panel-collapse collapse no-bg no-background" role="tabpanel" aria-labelledby="headingOne" >
						<div class="panel-body">
							<div class="row table-responsive">
								<table class="table" style="font-size:90%; overflow:hidden">
									<thead>
										<tr>
											<th width="35%">Risk Text</th>
											<th width="30%">Harm</th>
											<th width="10%">Rating</th>
											<th width="15%">Residual Risk</th>
											<th width="10%">Is Risk Present?</th>
										</tr>
									</thead>
									<tbody>
										<?php if (!empty($job_details->associate_risks)) {
										    foreach ($job_details->associate_risks as $key => $ass_risk) { ?>

											<?php if (!empty($ra_responses)) {
											    foreach ($ra_responses as $k => $resp) {
											        if ($resp->risk_id == $ass_risk->risk_id) {
											            $response = $resp;
											            break;
											        }
											    }
											} ?>
											
											<tr>
												<td><?php echo $ass_risk->risk_text; ?></td>
												<td><?php echo $ass_risk->risk_harm; ?></td>
												<td><?php echo $ass_risk->risk_rating; ?></td>
												<td><?php echo $ass_risk->residual_risk; ?> </td>
												<td><?php echo (!empty($response->risk_response)) ? $response->risk_response : ''; ?> <span class="pull-right"><i class="far fa-check-circle text-green"></i></span></td>
											</tr>
										<?php }
										    } else { ?>
											<tr>
												<td colspan="5" >There's currently no Risks associated with this type of Job. <a href="<?php echo base_url('webapp/job/job_types/'.$job_details->job_type_id); ?>" >Click here <i class="fas fa-plus text-green 2x"></i></a> to edit the attached Job type</td>
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
						<h4 class="panel-title"> Dynamic Risks (<?php echo (!empty($job_details->dynamic_risks)) ? count($job_details->dynamic_risks) : 0  ?>) <span class="pull-right"><i class="caret-icon fas fa-caret-down text-yellow"></i></span></h4>
					</div>
					<div id="collapseTwo" class="panel-collapse collapse no-bg no-background" role="tabpanel" aria-labelledby="headingTwo" >
						<div class="panel-body">
							<div class="row table-responsive">
								<table class="table" style="font-size:90%; overflow:hidden">
									<thead>
										<tr>
											<th width="35%">Risk Text</th>
											<th width="30%">Harm</th>
											<th width="10%">Rating</th>
											<th width="15%">Residual Risk</th>
											<th width="10%">Is Risk Present?</th>
										</tr>
									</thead>
									<tbody>
										<?php if (!empty($job_details->dynamic_risks)) {
										    foreach ($job_details->dynamic_risks as $k => $dyn_risk) { ?>
											
											<?php if (!empty($ra_responses)) {
											    foreach ($ra_responses as $k => $resp) {
											        if ($resp->risk_id == $dyn_risk->risk_id) {
											            $response = $resp;
											            break;
											        }
											    }
											} ?>
											
											<tr class="<?php echo (in_array($dyn_risk->risk_id, $completed_risks)) ? 'light-green-bg' : ''; ?>">
												<td><?php echo $dyn_risk->risk_text; ?></td>
												<td><?php echo $dyn_risk->risk_harm; ?></td>
												<td><?php echo $dyn_risk->risk_rating; ?></td>
												<td><?php echo $dyn_risk->residual_risk; ?></td>
												<td><?php echo (!empty($response->risk_response)) ? $response->risk_response : ''; ?> <i class="<?php echo (in_array($dyn_risk->risk_id, $completed_risks)) ? 'far fa-check-circle text-green' : 'fas fa-exclamation-circle text-orange pointer'; ?>" title="<?php echo (!in_array($dyn_risk->risk_id, $completed_risks)) ? 'This risk has not been completed yet!' : ''; ?>" ></i></td>
											</tr>
										<?php }
										    } else { ?>
											<tr>
												<td colspan="5" >There's currently no Dynamic risks linked to this Job.</td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-12 col-sm-12 col-xs-12 hide">
		<div class="x_panel tile has-shadow">
			<legend>Completed Risk Assessment</legend>
			<?php if (!empty($__ra_records)) { ?>
				<table style="width:100%">
					<tr>
						<th width="5%">ID</th>
						<th width="15%">Timestamp</th>
						<th width="15%">Operative</th>
						<th width="13%" class="text-center">Total Expected</th>
						<th width="13%" class="text-center">Total Completed</th>
						<th width="13%">Status</th>
						<th width="15%"><span class="pull-right">Action</span></th>
					</tr>
					<?php if (!empty($ra_records)) {
					    foreach ($ra_records as $ra_record) { ?>
						<tr>
							<td><?php echo $ra_record->assessment_id; ?></td>
							<td><?php echo date('d-m-Y H:i:s', strtotime($ra_record->date_created)); ?></td>
							<td><?php echo ($ra_record->created_by) ? $ra_record->created_by : ''; ?></td>
							<td class="text-center" ><?php echo $ra_record->ra_expected_risks; ?></td>
							<td class="text-center" ><?php echo (!empty($ra_record->ra_responses)) ? count($ra_record->ra_responses) : 0; ?></td>
							<td><?php echo ($ra_record->status) ? $ra_record->status : 'Status unknown'; ?> <i class="far <?php echo ($ra_record->risks_completed == 1) ? ' fa-check-circle text-green ' : ' fas fa-spinner text-orange pointer'; ?>"></i></td>
							<td class="risk-toggler pointer" data-assessment_id="<?php echo $ra_record->assessment_id; ?>" data-toggle="modal" data-target="#ra-record-modal-md" ><span class="pull-right" >View responses</span></td>
						</tr>
					<?php }
					    } ?>
					
				</table>
				
				<div class="modal fade ra-record-modal-md" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog modal-md">
						<div class="modal-content">
							<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
								<h4 class="modal-title" id="myModalLabel">Risk Assessment Log</h4>
							</div>
							<div class="modal-body"></div>
						</div>
					</div>
				</div>
				
			<?php } else { ?>
				<div><?php echo $this->config->item('no_records'); ?></div>
			<?php } ?>
		</div>		
	</div>
</div>

<script>
	$(document).ready(function(){
		
		$( '.section-container-bar' ).click( function(){
			$( this ).closest( 'div' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');			
		});
		
		$( '.risk-toggler' ).click( function( event ){
				var assessmentId = $(this).data('assessment_id');
				$.ajax({
					url:"<?php echo base_url('webapp/job/view_ra_record/'); ?>",
					method:"POST",
					data:{assessment_id:assessmentId},
					dataType: 'json',
					success:function(data){
						if( data.status == 1 ){
							$(".modal-body").html(data.ra_record);
							$(".ra-record-modal-md").modal("show");							
						}else{
							swal({
								type: 'error',
								title: data.status_msg
							})
						}		
					}
				});
				
		});
	});
</script>