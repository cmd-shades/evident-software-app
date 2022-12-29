<div class="row">
	<div class="x_content">
		<div class="col-md-4 col-sm-4 col-xs-12">
			<div class="x_title no-border text-center">
				<h2><small>SCHEDULES THIS WEEK</small></h2>
				<div class="clearfix"></div>
			</div>
			<div class="evi_panel has-shadow" >
				<div class="x_content">
					<table class="table" style="margin-top:-6px" >
						<tr>
							<td width="34%" >&nbsp;</td>
							<td width="22%" class="text-center text-bold" >Completed</td>
							<td width="22%" class="text-center text-bold" >In Progress</td>
							<td width="22%" class="text-center text-bold" >Not Due</td>
						</tr>

						<tbody id="weekly_schedules_data" >
							
						</tbody>
					</table>
				</div>
			</div>
		</div>
		
		<div class="col-md-4 col-sm-4 col-xs-12">
			<div class="x_title no-border text-center">
				<h2><small>SCHEDULES THIS MONTH</small></h2>
				<div class="clearfix"></div>
			</div>
			<div class="evi_panel has-shadow" >
				<div class="x_content">
					<table class="table" style="margin-top:-6px" >
						<tr>
							<td width="34%" >&nbsp;</td>
							<td width="22%" class="text-center text-bold" >Completed</td>
							<td width="22%" class="text-center text-bold" >In Progress</td>
							<td width="22%" class="text-center text-bold" >Not Due</td>
						</tr>
						<tbody id="monthly_schedules_data" >
							
						</tbody>						
					</table>
				</div>
			</div>
		</div>
		
		<div class="col-md-4 col-sm-4 col-xs-12">
			<div class="x_title no-border text-center">
				<h2><small>SCHEDULES THIS YEAR</small></h2>
				<div class="clearfix"></div>
			</div>
			<div class="evi_panel has-shadow" >
				<div class="x_content">
					<table class="table" style="margin-top:-6px" >
						<tr>
							<td width="34%" >&nbsp;</td>
							<td width="22%" class="text-center text-bold" >Completed</td>
							<td width="22%" class="text-center text-bold" >In Progress</td>
							<td width="22%" class="text-center text-bold" >Not Due</td>
						</tr>
						<tbody id="annual_schedules_data" >
							
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">

	$( document ).ready( function () {

		$.ajax({
			url:"<?php echo base_url('webapp/home/schedule_stats'); ?>",
			method: "GET",
			data:{ account_id:accountID, stat_type:"weekly_schedules", where:{week_start: '2020-03-18', week_end: '2020-03-18' } },
			dataType: 'json',
			success:function( result ){
				if( result.status == 1 ){
					load_periodic_schedules_chart( result, "weekly_schedules" );
				} else {
					$( "barchart-12" ).closest('.stats-container').find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
				}
			}
        });
		
		$.ajax({
			url:"<?php echo base_url('webapp/home/schedule_stats'); ?>",
			method: "GET",
			data:{ account_id:accountID, stat_type:"monthly_schedules", where:{week_start: '2020-03-18', week_end: '2020-03-18' } },
			dataType: 'json',
			success:function( result ){
				if( result.status == 1 ){
					load_periodic_schedules_chart( result, "monthly_schedules" );
				} else {
					$( "barchart-12" ).closest('.stats-container').find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
				}
			}
        });
		
		$.ajax({
			url:"<?php echo base_url('webapp/home/schedule_stats'); ?>",
			method: "GET",
			data:{ account_id:accountID, stat_type:"annual_schedules", where:{week_start: '2020-03-18', week_end: '2020-03-18' } },
			dataType: 'json',
			success:function( result ){
				if( result.status == 1 ){
					load_periodic_schedules_chart( result, "annual_schedules" );
				} else {
					$( "barchart-12" ).closest('.stats-container').find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
				}
			}
        });

	});
	
	function load_periodic_schedules_chart( json_data, canvas_id ){
		
		if ( json_data.hasOwnProperty( "stats" ) ) {
			
			if ( json_data[ "stats" ] != null) {
				$.each( json_data[ "stats" ], function( index, categoryData ) {

					var clickFilterUrl	= "<?php echo base_url('/webapp/job/schedule_activities'); ?>"+'?category_id='+categoryData.category_id+'&period_range='+categoryData.period_range;

					var schedulesBar = '<tr>';
						schedulesBar += '<td class="text-bold pull-left" ><a href="'+clickFilterUrl+'" ><img title="Category Schedules" width="20px" src="<?php echo base_url("'+categoryData.category_icon+'") ?>" /> '+categoryData.category_name+'</td></td>';
						schedulesBar += '<td class="text-center" >'+( ( categoryData.total_completed > 0 ) ? '<a href="'+clickFilterUrl+'&status=completed">' : '' )+categoryData.total_completed+( ( categoryData.total_completed > 0 ) ? '</a>' : '' )+'</td>';
						schedulesBar += '<td class="text-center" >'+( ( categoryData.total_in_progress > 0 ) ? '<a href="'+clickFilterUrl+'&status=in progress">' : '' )+categoryData.total_in_progress+( ( categoryData.total_in_progress > 0 ) ? '</a>' : '' )+'</td>';
						schedulesBar += '<td class="text-center" >'+( ( categoryData.total_not_due > 0 ) ? '<a href="'+clickFilterUrl+'&status=not due">' : '' )+categoryData.total_not_due+( ( categoryData.total_not_due > 0 ) ? '</a>' : '' )+'</td>';
						//schedulesBar += '<td class="text-center" >'+( ( categoryData.total_failed > 0 ) ? '<a href="'+clickFilterUrl+'&status=failed">' : '' )+categoryData.total_failed+( ( categoryData.total_failed > 0 ) ? '</a>' : '' )+'</td>';
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