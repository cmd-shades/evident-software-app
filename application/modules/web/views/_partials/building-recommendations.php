<?php
    require('../dashboard/functions/functions.php');

    //Get Global Account ID
    $account_id = $_GET['account_id'];

    //Get the discipline ID from URL
    $recommendations = getBuildingRecommendationsFeed($account_id, $api_end_point); //Recommendations for the specific building


    ?> 
<!-- RECOMMENDATIONS start -->
<div class="col-xs-12 col-md-5">
            <div class="row">
              <div class="col-md-8">
                <h4>Recommendations
                </h4>
              </div>
              <div class="col-md-4">
                <h4 class="hidden-xs text-right">Priority
                </h4>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12 col-md-12">
                <?php if (!empty($overdues->overdue_jobs->job_disciplines)) {
                    foreach ($recommendations as $recommendation) {//Start the outer loop
                        if (is_array($recommendation) || is_object($recommendation)) { //Is it an array or object?
                            foreach ($recommendation->building_recommendations as $buildingrec) { //Start the loop?>
                <div class="recommend">
                  <p>
                    <?php echo $buildingrec->recommendation;?>
                  </p>
                  <div class="priorities">
                    <span>
                      <?php echo $buildingrec->priority_rating;?>
                    </span>
                  </div>
                </div>
                <?php }
                            }
                    }
                } else {
                    echo '<div class="alert alert-info" role="alert">
                  No data found
                </div>';
                }?>
              </div>
            </div>
          </div>
          <!-- RECOMMENDATIONS end -->