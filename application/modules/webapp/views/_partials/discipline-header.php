<?php 

	require('../dashboard/functions/functions.php');

	//Get Global Account ID
	$account_id = $_GET['account_id'];

	//Get the discipline ID from URL
	$disciplineid = $_GET['id'];
	//If start and end dates are available
	if(isset($_GET['start'])) { 
		//Get the start date from URL
		$start = $_GET['start'];

		//Get the end date from URL
		$end = $_GET['end'];

		echo '<h3 class="text-white">Date ranging from: '.$start.' until '.$end.'</h3>';

		$disciplines = getDisciplineDateRangeFeed( $disciplineid, $account_id, $api_end_point );
	} else {
		$disciplines = getDisciplineFeed( $disciplineid, $account_id, $api_end_point );
	}
  
	$builddisciplines = getBuildingDisciplinesFeed( $disciplineid, $account_id, $api_end_point ); //Get the buildings in a discipline
	$extra_info 	  = !empty( $disciplines->extra_info ) ? $disciplines->extra_info : false;

	if( !empty( $disciplines ) ){

		foreach( $disciplines->data as $discipline){ //Start the loop
      
			$failed = $discipline->outcomes_info->passed_inspections_percent; //Get the fail rate
			$passed = 100;// Set the pass rate
			$recom = 100 - $discipline->outcomes_info->recommendations_percent; //Get the recommendation rate
	?>

<!-- Banner START -->
<section class="banner <?php $disciplinename = strtolower($discipline->profile_info->discipline_name); if($disciplinename =='specialist equipment'){echo'specialist';}else{echo $disciplinename;}?>-bg">
  <div class="container">
    <!-- Mobile date filter START -->
    <div class="row filters">
      <div class="col-xs-12 col-md-8 filter hidden-medium">
        <!-- 7 day filter START -->
        <a id="7" href="#" class="btn active datefilter" onClick="recp('7')">7 days</a>
        <!-- 7 day filter END -->
        <!-- 30 day filter START -->
        <a id="30" href="#" class="btn datefilter" onClick="recp('30')">30 days</a>
        <!-- 30 day filter END -->
        <!-- 365 day filter START -->
        <a id="365" href="#" class="btn datefilter" onClick="recp('365')">Year</a>
        <!-- 365 day filter END -->
        <!-- Custom date filter -->
        <!-- <a id="custom" href="#" class="btn">Custom</a> -->
        <!-- Custom date filter END -->

        <!-- Calendar START -->
        <section class="calendar" style="display: none;">
          <div class="calendar__inputs">
            <input class="calendar__input" readonly="readonly" type="text" id="calendar-start" placeholder="Start Date">
            <input class="calendar__input" readonly="readonly" type="text" id="calendar-end" placeholder="End Date">
          </div>
          <div class="calendar__pikaday" id="calendar-container"></div>
          <button class="calendar__reset" id="calendar-clear">Submit</button>
        </section>
        <!-- Calendar END -->
    </div>
  </div>
  <!-- Mobile date filter END -->

  <div class="row">
    <div class="col-xs-12 col-md-5 content">
      <div class="chartoverview discipline">
        <!-- CHART start -->
        <svg class="circle-chart" viewbox="0 0 33.83098862 33.83098862" xmlns="http://www.w3.org/2000/svg">
        <circle class="circle-chart__circle" stroke="#66CC66" stroke-width="0.99" stroke-dasharray="100,100" fill="none" cx="16.91549431" cy="16.91549431" r="15.91549431" />
               <circle class="circle-chart__circle" stroke="#FF6666" stroke-width="0.98" stroke-dasharray="<?php echo $failed; ?>,90" fill="none" cx="16.91549431" cy="16.91549431" r="15.91549431" />
               <circle class="circle-chart__circle" stroke="#FF9933" stroke-width="1.0" stroke-dasharray="<?php echo $recom; ?>,97" fill="none" cx="16.91549431" cy="16.91549431" r="15.91549431" />
                </svg>
        <!-- CHART end -->
      <div class="discipline-text">
        <h3>
          <?php echo $discipline->attendance_info->completed_visits;?>
          <span>
            /
            <strong> 
              <?php echo $discipline->attendance_info->scheduled_visits;?>
            </strong>
          </span>
        </h3>
      </div>
    </div>
  </div>

  <div class="col-md-1"></div>

  <div class="col-xs-12 col-md-6">
  <!-- Discipline name START -->
    <h1>
      <?php echo $discipline->profile_info->discipline_name;?>
      <!-- Date range START -->
      - <?php echo $disciplines->extra_info->date_range;?> Days
      <!-- Date range END -->
    </h1>
  <!-- Discipline name END -->
  <div class="row">
    <div class="col-xs-12 col-md-9">
      <div class="row">
        <!-- Passed count START -->
        <div class="circle-chart__info col-xs-4 col-md-12">
          <div class="circle pass">
            <p class="count" >
              <?php echo $discipline->outcomes_info->passed_inspections;?>
            </p>
          </div>
          <h4>Passed</h4>
        </div>
        <!-- Passed count END -->

        <!-- Recommendations count START -->
        <div class="circle-chart__info col-xs-4 col-md-12">
          <div class="circle recommendations">
            <p class="count" >
              <?php echo $discipline->outcomes_info->recommendations;?>
            </p>
          </div>
          <h4>Recommendations</h4>
        </div>
        <!-- Recommendations count END -->

        <!-- Failed count START -->
        <div class="circle-chart__info col-xs-4 col-md-12">
          <div class="circle failed">
            <p class="count" >
              <?php echo $discipline->outcomes_info->failed_inspections;?>
            </p>
          </div>
          <h4>Failed</h4>
        </div>
        <!-- Failed count END -->

      </div>
    </div>
  </div>
</section>
<!-- Banner END -->

	<?php } } else { ?>
	
	<section class="banner">
		<div class="container">
			<div class="col-xs-12 col-sm-12  col-md-12 content">
				<div>
					<div class="panels panel-defaults">
						<div class="panel-headings" role="tab" id="heading-1">
							&nbsp;&nbsp;&nbsp;No Discipline information to display
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php } ?>

<script>
	$( document ).ready( function(){
		var lastUpdatedAt = "<?php echo !empty( $extra_info->last_updated ) ? $extra_info->last_updated : ''; ?>";
		if( lastUpdatedAt ){
			$( '.header-last-modified' ).text( lastUpdatedAt );
		}		
	});
</script>