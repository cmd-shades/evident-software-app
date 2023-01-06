<?php
	
	$root 		= ( isset( $_SERVER['HTTPS'] ) ? "https://" : "http://" ) . $_SERVER['HTTP_HOST'];
	$root 		.= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
	$root_parts = explode( 'application', $root );	
	$base_url	= !empty( $root_parts[0] ) ? $root_parts[0] : 'http://localhost/evident-core';
	
	#$api_end_point	= 'http://77.68.92.77/evident-core/serviceapp/api/';
	$api_end_point	= $base_url.'/serviceapp/api/';

	require('../dashboard/functions/functions.php');

	//Get Global Account ID
	$account_id = $_GET['account_id'];
  
?> 
<div class="row">

	<?php

		//If start and end dates are available

		if( isset($_GET['start']) ) {

			//Get the start date from URL

			$start = $_GET['start'];

			//Get the end date from URL

			$end = $_GET['end'];

			echo '<h3 class="text-white">Date ranging from: '.$start.' until '.$end.'</h3>';

			$overview_feed = getOverviewDateRangeFeed( $start, $end, $account_id, $api_end_point );
		} else {
			$overview_feed = getOverviewFeed( $account_id, $api_end_point );
		}
			$overviews  = !empty( $overview_feed->data ) 		? $overview_feed->data 		 : false;
			$extra_info = !empty( $overview_feed->extra_info ) 	? $overview_feed->extra_info : false;

		if( !empty( $overviews ) ){
			usort( $overviews, "orderDisciplineIDs" );
			foreach ($overviews as $overview){
				$failed = $overview->outcomes_info->failed_inspections_percent;
				$passed = 100;
				$recom  = $overview->outcomes_info->recommendations_percent; 
	?>
      <div class="col-xs-6 col-sm-6  col-md-4 content">
         <div class="chartoverview">
            <div class="content-overlay"></div>
            <svg class="circle-chart" viewbox="0 0 33.83098862 33.83098862" xmlns="http://www.w3.org/2000/svg">
               <circle class="circle-chart__circle" stroke="#66CC66" stroke-width="0.99" stroke-dasharray="100,100" fill="none" cx="16.91549431" cy="16.91549431" r="15.91549431" />
               <circle class="circle-chart__circle" stroke="#FF6666" stroke-width="0.98" stroke-dasharray="<?php echo $failed; ?>,90" fill="none" cx="16.91549431" cy="16.91549431" r="15.91549431" />
               <circle class="circle-chart__circle" stroke="#FF9933" stroke-width="1.0" stroke-dasharray="<?php echo $recom; ?>,97" fill="none" cx="16.91549431" cy="16.91549431" r="15.91549431" />
               
            </svg>
            <div class="static-text">
               <h3><?php echo $overview
                  ->attendance_info->completed_visits; ?><span>/<strong> <?php echo $overview
                  ->attendance_info->scheduled_visits; ?></strong></span></h3>
               <h4>ATTENDANCES COMPLETED</h4>
               <p><img height="40" src="<?php $icon = strtolower($overview
                  ->profile_info
                  ->discipline_name);
                  if ($icon == 'specialist equipment')
                  {
                  $icon = 'specialist';
                  }
                  echo $base_url.'/application/modules/webapp/views/dashboard/images/' . $icon . '.svg'; ?>"/></p>
            </div>
            <div class="buildings-button" >
               <a href="<?php echo strtolower( $overview->profile_info->discipline_name ) ; ?>"><strong>VIEW BUILDINGS</strong></a>
            </div>
            <div class="content-details fadeIn-bottom">
               <h3 class="content-title">OUTCOMES</h3>
               <div class="row">
                  <div class="circle pass">
                     <p class="count" ><?php echo $overview->outcomes_info->passed_inspections; ?></p>
                  </div>
                  <h4>Passed</h4>
               </div>
               <div class="row">
                  <div class="circle recommendations">
                     <p class="count" ><?php echo $overview->outcomes_info->recommendations; ?></p>
                  </div>
                  <h4>Recommendations</h4>
               </div>
               <div class="row">
                  <div class="circle failed">
                     <p class="count" ><?php echo $overview->outcomes_info->failed_inspections; ?></p>
                  </div>
                  <h4>Failed</h4>
               </div>
            </div>
         </div>
      </div>
		<?php } } else { ?>
			<div class="col-xs-12 col-sm-12  col-md-12 content">
				<div>
					<div class="panels panel-defaults">
						<br>
						<div class="panel-headings text-white" role="tab" id="heading-1">
							&nbsp;&nbsp;&nbsp;No Disciplines overview information to display
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
   </div>
   
	<script>
		$( document ).ready( function(){
			
			var lastUpdatedAt = "<?php echo !empty( $extra_info->last_updated ) ? $extra_info->last_updated : date( 'h:i:s A' ); ?>";
			$( '.header-last-modified' ).text( lastUpdatedAt );
			
		});
	</script>