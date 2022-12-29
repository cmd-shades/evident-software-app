<style>
	.table>tbody>tr>th, 
	.table>tfoot>tr>th, 
	.table>thead>tr>td, 
	.table>tbody>tr>td, 
	.table>tfoot>tr>td {
		border-top: none;
	}
</style>

<div class="row">
	<div class="x_content">

		<div class="col-md-6 col-sm-6 col-xs-12">
			<div class="evi_panel has-shadow light-grey-bg" >
				<div class="evi_content">
					<table class="table table-responsive no-top-border" >
						<tr>
							<th width="50%" ><span class="pull-left" >THIS MONTH</span></th>
							<th width="50%" ><span class="pull-right">MAINTENANCE CALLS (<?php echo(!empty($monthly_jobs_data) ? ((string) array_sum(array_column(object_to_array($monthly_jobs_data), 'category_totals'))) : 0) ?>)</span></th>
						</tr>
						<tbody id="monthly_jobs_data" >
							<?php if (!empty($monthly_jobs_data)) {
							    foreach ($monthly_jobs_data as $category => $monthly_category_data) { ?>
								<tr>
									<th width="50%" ><?php echo ($monthly_category_data->category_totals > 0) ? '<a href="'.base_url('webapp/job/jobs?category_id='.$monthly_category_data->category_id).'">' : ''; ?> <span class="pull-left" ><?php echo $monthly_category_data->category_name; ?></span><?php echo ($monthly_category_data->category_totals > 0) ? '</a>' : ''; ?></th>
									<th width="50%" ><?php echo ($monthly_category_data->category_totals > 0) ? '<a href="'.base_url('webapp/job/jobs?category_id='.$monthly_category_data->category_id).'">' : ''; ?> <span class="pull-right"><?php echo $monthly_category_data->category_totals; ?></span><?php echo ($monthly_category_data->category_totals > 0) ? '</a>' : ''; ?></th>
								</tr>
							<?php }
							    } else { ?>
								<tr>
									<th width="100%" colspan="2" ><span class="pull-left" >There's currently no monthly Jobs data</span></th>
								</tr>
							<?php } ?>
						</tbody>						
					</table>
				</div>
			</div>
		</div>
		
		<div class="col-md-6 col-sm-6 col-xs-12">
			<div class="evi_panel has-shadow light-grey-bg" >
				<div class="evi_content">
					<table class="table table-responsive no-top-border" >
						<tr>
							<th width="50%" ><span class="pull-left" >THIS YEAR</span></th>
							<th width="50%" ><span class="pull-right">MAINTENANCE CALLS (<?php echo(!empty($annual_jobs_data) ? ((string) array_sum(array_column(object_to_array($annual_jobs_data), 'category_totals'))) : 0) ?>)</span></th>
						</tr>
						<tbody id="annual_jobs_data" >
							<?php if (!empty($annual_jobs_data)) {
							    foreach ($annual_jobs_data as $category => $annual_category_data) { ?>
								<tr>
									<th width="50%" ><a href="<?php echo ($annual_category_data->category_totals > 0) ? base_url('webapp/job/jobs?category_id='.$annual_category_data->category_id) : ''; ?>" <?php echo ($annual_category_data->category_totals > 0) ? '' : 'disabled'; ?> ><span class="pull-left" ><?php echo $annual_category_data->category_name; ?></span></a></th>
									<th width="50%" ><a href="<?php echo ($annual_category_data->category_totals > 0) ? base_url('webapp/job/jobs?category_id='.$annual_category_data->category_id) : ''; ?>" <?php echo ($annual_category_data->category_totals > 0) ? '' : 'disabled'; ?> ><span class="pull-right"><?php echo $annual_category_data->category_totals; ?></span></a></th>
								</tr>
							<?php }
							    } else { ?>
								<tr>
									<th width="100%" colspan="2" ><span class="pull-left" >There's currently no annual Jobs data</span></th>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">

	$( document ).ready( function () {

		// $.ajax({
			// url:"<?php echo base_url('webapp/home/schedule_stats'); ?>",
			// method: "GET",
			// data:{ account_id:accountID, stat_type:"weekly_schedules", where:{week_start: '2020-03-18', week_end: '2020-03-18' } },
			// dataType: 'json',
			// success:function( result ){
				// if( result.status == 1 ){
					// load_periodic_schedules_chart( result, "weekly_schedules" );
				// } else {
					// $( "barchart-12" ).closest('.stats-container').find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
				// }
			// }
        // });
		
		// $.ajax({
			// url:"<?php echo base_url('webapp/home/schedule_stats'); ?>",
			// method: "GET",
			// data:{ account_id:accountID, stat_type:"annual_schedules", where:{week_start: '2020-03-18', week_end: '2020-03-18' } },
			// dataType: 'json',
			// success:function( result ){
				// if( result.status == 1 ){
					// load_periodic_schedules_chart( result, "annual_schedules" );
				// } else {
					// $( "barchart-12" ).closest('.stats-container').find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
				// }
			// }
        // });

	});
	
	function load_periodic_schedules_chart( json_data, canvas_id ){
		
		if ( json_data.hasOwnProperty( "stats" ) ) {
			
			if ( json_data[ "stats" ] != null) {
				$.each( json_data[ "stats" ], function( index, categoryData ) {

					var clickFilterUrl	= "<?php echo base_url('/webapp/job/schedules'); ?>";

					var schedulesBar = '<tr href="'+clickFilterUrl+'">';
						schedulesBar += '<td class="text-bold pull-left" ><img title="Fire Schedules" width="20px" src="<?php echo base_url("'+categoryData.category_icon+'") ?>" /> '+categoryData.category_name+'</td></td>';
						schedulesBar += '<td class="text-center" >'+categoryData.total_completed+'</td>';
						schedulesBar += '<td class="text-center" >'+categoryData.total_in_progress+'</td>';
						schedulesBar += '<td class="text-center" >'+categoryData.total_not_due+'</td>';
						schedulesBar = schedulesBar+'</tr>';
					
						switch( canvas_id ){
							case 'weekly_schedules':
								$( '#weekly_schedules_data' ).append( schedulesBar );
								break;
								
							case 'monthly_schedules':
								$( '#monthly_schedules_data' ).append( schedulesBar );
								break;
								
							case 'annual_schedules':
								$( '#annual_schedules_data' ).append( schedulesBar );
								break;
						}
				});
			}
			
		}
		
	}
	
</script>