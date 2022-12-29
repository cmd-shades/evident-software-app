<?php
    require('../dashboard/functions/functions.php');

    //Get Global Account ID
    $account_id = $_GET['account_id'];

    $building = getBuildingFeed($account_id, $api_end_point); //The building information


    ?> 
<!-- OUTCOMES start -->
<h4>Outcomes</h4>
          <?php foreach ($building->building_disciplines_info as $outcome) { //Start the loop
              $failed = $outcome->outcomes_info->failed_inspections_percent;
              $passed = 100;
              $recom  = $outcome->outcomes_info->recommendations_percent;
              ?>
          <div class="col-xs-6 col-md-2 content">
            <div class="chartoverview building">
              <!-- CHART start -->
              <svg class="circle-chart" viewbox="0 0 33.83098862 33.83098862" xmlns="http://www.w3.org/2000/svg">
               <circle class="circle-chart__circle" stroke="#66CC66" stroke-width="0.99" stroke-dasharray="100,100" fill="none" cx="16.91549431" cy="16.91549431" r="15.91549431" />
               <circle class="circle-chart__circle" stroke="#FF6666" stroke-width="0.98" stroke-dasharray="<?php echo $failed; ?>,90" fill="none" cx="16.91549431" cy="16.91549431" r="15.91549431" />
               <circle class="circle-chart__circle" stroke="#FF9933" stroke-width="1.0" stroke-dasharray="<?php echo $recom; ?>,97" fill="none" cx="16.91549431" cy="16.91549431" r="15.91549431" />
              </svg>
              <!-- CHART end -->
              <div class="building-text">
				<h3>
					<strong style="font-size:16px"><?php echo ($outcome->outcomes_info->passed_inspections_percent > 0) ? $outcome->outcomes_info->passed_inspections_percent.'%' : '0';?></strong> <span style="font-size:10px" title="Passed percentage" >PASSED</span>
				</h3>

				<h3>
					<strong style="font-size:16px"><?php echo ($outcome->outcomes_info->failed_inspections_percent > 0) ? $outcome->outcomes_info->failed_inspections_percent.'%' : '0';?></strong> <span style="font-size:10px" title="Failed percentage" >FAILED</span>
				</h3>

				<h3>
					<strong style="font-size:16px"><?php echo ($outcome->outcomes_info->recommendations_percent > 0) ? $outcome->outcomes_info->recommendations_percent.'%' : '0'; ?></strong> <span style="font-size:10px" title="Recommendations percentage" >RECMS</span>
				</h3>
                <div class="dis-icon">
                  <?php $discipline = strtolower($outcome->profile_info->discipline_name);?>
                  <img src="<?php if ($discipline == 'specialist equipment') {
                      $discipline = 'specialist';
                  } echo $GLOBALS['base_url'].'/application/modules/webapp/views/dashboard/images/'.$discipline.'.svg'; ?>" />
                </div>
              </div>
            </div>
          </div>
          <?php }?>
        </div>
        <!-- OUTCOMES end -->