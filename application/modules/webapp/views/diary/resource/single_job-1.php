<div class="">
	<div class="row">
		<div class="col-md-4 col-sm-6 col-xs-12" >
			<div class="row">
				<?php if( !empty( $job_details ) ) { ?>
					<div class="col-md-6" ><span>BookingBooking Job ID (100)</span></div>
					<div class="col-md-6" >
						<div id="jobs-sortable" class="sortable-list connectedSortable">
							<div class="job-element pointer text-white" id="job-<?php echo $job_details->job_id; ?>" job_id = "<?php echo $job_details->job_id; ?>" job_slots="<?php echo $job_details->job_duration; ?>" job_name="<?php echo lean_string( $job_details->job_type ); ?>" job_description="<?php echo $job_details->job_type; ?>" job_location="<?php echo $job_details->summaryline; ?>">
								<div class="job-header strong">
									<h4>
										<span class="job-el-type"><?php echo ( !empty( $job_details->job_type ) ? $job_details->job_type : false ); ?></span>
										<span class="job-el-duration pull-right"><?php echo ( !empty( $job_details->job_duration ) ? $job_details->job_duration : false ); echo " ".( ( $job_details->job_duration > 1 ) ? "hours" : "hour" ); ?></span>
									</h4>
								</div>
								<div class="job-details small" >
									<span class="pull-left"><?php echo ( !empty( $job_details->summaryline ) ? $job_details->summaryline : '<span class="text-text">NO POSTCODE AVAILABLE</span>&nbsp;' ); ?></span>
								</div>
							</div>
						</div>
					</div>	
				<?php } else { ?>
					<div class = "col-md-12" >
						<span>There's currently no date matching your criteria</span>
					</div>
				<?php } ?>
			</div>
		</div>
		<div class="col-md-4 col-sm-6 col-xs-12" >
			<div class="row">
				<div class="col-md-6 text-center" ><input type="text" class="form-control datepicker" style="width:80%" value="<?php echo date( 'd-m-Y', strtotime( 'monday this week' ) ) ?>" placeholder="Week of" /></div>
				<div class="col-md-6 text-center" ><input type="text" class="form-control datepicker" style="width:80%" value="<?php echo date( 'd-m-Y', strtotime( 'sunday this week' ) ) ?>" placeholder="Week ending" /></span><span></span></div>
			</div>
		</div>
		<div class="col-md-4 col-sm-12 col-xs-12" >
			<div class="area row" >
				<div class="col-md-12 col-sm-12 col-xs-12 top_search">
					<div class="black-box shaded">
						<div>
							<input type="text" class="form-control <?php echo $module_identier; ?>-search_input" id="search_term" value="" placeholder="Search Operatives" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<legend>&nbsp;</legend>
	</div>

	<div class="row routing-details">
		<div class="col-md-12 col-sm-12 col-sm-12" >
			<table class="table-responsive" style="width:100%" >
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
					<tr>
						<th width="14%" class="text-center" ><span class="week-mon" ><?php echo !empty( $resource->monday ) 		? strtoupper( $resource->monday ) 	: strtoupper( date( 'D d', strtotime( 'monday this week' ) ) ); ?></span></th>															
						<th width="14%" class="text-center" ><span class="week-tue" ><?php echo !empty( $resource->tuesday ) 	? strtoupper( $resource->tuesday )	: strtoupper( date( 'D d', strtotime( 'tuesday this week' ) ) ); ?></span></th>															
						<th width="14%" class="text-center" ><span class="week-wed" ><?php echo !empty( $resource->wednesday ) 	? strtoupper( $resource->wednesday ) 	: strtoupper( date( 'D d', strtotime( 'wednesday this week' ) ) ); ?></span></th>															
						<th width="14%" class="text-center" ><span class="week-thu" ><?php echo !empty( $resource->thursday ) 	? strtoupper( $resource->thursday )	: strtoupper( date( 'D d', strtotime( 'thursday this week' ) ) ); ?></span></th>															
						<th width="14%" class="text-center" ><span class="week-fri" ><?php echo !empty( $resource->friday ) 		? strtoupper( $resource->friday )	: strtoupper( date( 'D d', strtotime( 'friday this week' ) ) ); ?></span></th>															
						<th width="14%" class="text-center" ><span class="week-sat" ><?php echo !empty( $resource->saturday ) 	? strtoupper( $resource->saturday ) 	: strtoupper( date( 'D d', strtotime( 'saturday this week' ) ) ); ?></span></th>															
						<th width="14%" class="text-center" ><span class="week-sun" ><?php echo !empty( $resource->sunday ) 		? strtoupper( $resource->sunday )	: strtoupper( date( 'D d', strtotime( 'sunday this week' ) ) ); ?></span></th>															
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>


<script type="text/javascript">
	$( document ).ready(function(){

	});
</script>

