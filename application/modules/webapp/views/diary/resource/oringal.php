<script src="<?php echo base_url('assets/js/custom/jobsort-events.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/custom/jobsort-ui.js'); ?>" type="text/javascript"></script>

<script src="<?php echo base_url('assets/js/custom/jobsort-util.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/custom/jobsort-map.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/custom/jobsort-modal.js'); ?>" type="text/javascript"></script>

<style>
	
	.job-header h1, 
	.job-header h2, 
	.job-header h3, 
	.job-header h4, 
	.job-header h4, 
	.job-header h5, 
	.job-header h5{
		margin-top: 0px;
	}

</style>

<div class="work-scheduler-container">
	<legend>Work Scheduler</legend>
	<div class="x_panel no-border shaded">
		<div class="x_content">
			<div class="row headers">
				<div class="col-md-2" >
					<legend>Work</legend>
				</div>
				
				<div class="col-md-10" >
					<legend>Details</legend>
				</div>
			</div>

			<div class="row routing-details">
				<div class="col-md-2 col-sm-2 col-sm-12" >
					<?php if( !empty( $un_booked_jobs ) ){ ?>
						<div id="jobs-sortable" class="sortable-list connectedSortable">
							<?php foreach( $un_booked_jobs as $k => $job ) { ?>
								<div class="job-element pointer text-white" id="job-<?php echo $job->job_id; ?>" job_id = "<?php echo $job->job_id; ?>" job_slots="<?php echo $job->job_duration; ?>" job_name="<?php echo lean_string( $job->job_type ); ?>" job_description="<?php echo $job->job_type; ?>" job_location="<?php echo $job->summaryline; ?>">
									<div class="job-header strong">
										<h4>
											<span class="job-el-type"><?php echo ( !empty( $job->job_type ) ? $job->job_type : false ); ?></span>
											<span class="job-el-duration pull-right"><?php echo ( !empty( $job->job_duration ) ? $job->job_duration : false ); echo " ".( ( $job->job_duration > 1 ) ? "hours" : "hour" ); ?></span>
										</h4>
									</div>
									<div class="job-details small" >
										<span class="pull-left"><?php echo ( !empty( $job->summaryline ) ? $job->summaryline : '<span class="text-orange">NO POSTCODE AVAILABLE</span>&nbsp;' ); ?></span>
									</div>
								</div>
							<?php } ?>
						</div>
					<?php } else { ?>
						<div><?php echo $this->config->item( 'no_records' ); ?></div>
					<?php } ?>
					
					<?php if( !empty( $page_counters->pages ) && ( $page_counters->pages > 1 ) ){ ?>
						<div class="row">
							<div class="col-md-12">Load More...</div>
						</div>
					<?php } ?>
				</div>
				
				<div class="col-md-10 col-sm-10 col-sm-12" >
					<div class="" >
						<table class="table-responsive" style="margin-bottom:0px;width:100%" >
							<thead>
								<tr>
									<th width="14%" class="text-center" ><span class="week-mon" ><?php echo !empty( $resource->monday ) 		? strtoupper( $resource->monday ) 	: strtoupper( date( 'D d', strtotime( 'monday this week' ) ) ); ?></span></th>															
									<th width="14%" class="text-center" ><span class="week-tue" ><?php echo !empty( $resource->tuesday ) 	? strtoupper( $resource->tuesday )	: strtoupper( date( 'D d', strtotime( 'tuesday this week' ) ) ); ?></span></th>															
									<th width="14%" class="text-center" ><span class="week-wed" ><?php echo !empty( $resource->wednesday ) 	? strtoupper( $resource->wednesday ) 	: strtoupper( date( 'D d', strtotime( 'wednesday this week' ) ) ); ?></span></th>															
									<th width="14%" class="text-center" ><span class="week-thu" ><?php echo !empty( $resource->thursday ) 	? strtoupper( $resource->thursday )	: strtoupper( date( 'D d', strtotime( 'thursday this week' ) ) ); ?></span></th>															
									<th width="14%" class="text-center" ><span class="week-fri" ><?php echo !empty( $resource->friday ) 		? strtoupper( $resource->friday )	: strtoupper( date( 'D d', strtotime( 'friday this week' ) ) ); ?></span></th>															
									<th width="14%" class="text-center" ><span class="week-sat" ><?php echo !empty( $resource->saturday ) 	? strtoupper( $resource->saturday ) 	: strtoupper( date( 'D d', strtotime( 'saturday this week' ) ) ); ?></span></th>															
									<th width="14%" class="text-center" ><span class="week-sun" ><?php echo !empty( $resource->sunday ) 		? strtoupper( $resource->sunday )	: strtoupper( date( 'D d', strtotime( 'sunday this week' ) ) ); ?></span></th>															
								</tr>
							</thead>
							<tbody id="diary-results">
							
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$( document ).ready(function(){

	});
</script>

