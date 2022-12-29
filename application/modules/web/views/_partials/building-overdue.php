<?php
    require('../dashboard/functions/functions.php');

    //Get Global Account ID
    $account_id = $_GET['account_id'];

    $overdues = getOverdueJobsFeed($account_id, $api_end_point); //Count of overdue jobs


    ?> 
<!-- OVERDUES start -->
<div class="overdues">
			<h4>Overdue jobs</h4>
			<ul class="row">
				<?php if (!empty($overdues->overdue_jobs->job_disciplines)) {
				    $overdues = $overdues->overdue_jobs->job_disciplines;
                    //usort( $overdues, "orderDisciplineIDs" ); //Order the disciplines
                    //foreach( $overdues as $overdue ) { //Start the outer loop
				    foreach ($overdues as $disciplines) { //Start the loop?>
							<li class="over">
								<?php $discipline = strtolower($disciplines->discipline_name);?>
								<div class="totals overdue <?php echo $discipline;?>">
									<div class="total-icon">
										<img src="<?php if ($discipline == 'specialist equipment') {
										    $discipline = 'specialist';
										} echo $base_url.'application/modules/webapp/views/dashboard/images/'.$discipline.'.svg'; ?>" />
										<h4><?php echo $discipline;?></h4>
										<a href="<?php echo $base_url.'webapp/job/overview?discipline_id='.$disciplines->discipline_id.'&overdue_jobs=1&date_range='.$_COOKIE['daterange'];?>">
											<div class="circle <?php if ($disciplines->jobs_total > 0) {
											    echo'failed';
											} else {
											    echo'pass';
											};?>">
												<p><?php echo $disciplines->jobs_total;?></p>
											</div>
										</a>
									</div>
								</div>
							</li>
						<?php }?>
					<?php //}?>
				<?php } else { ?>
					<div class="alert alert-info" role="alert">
						No data found
					</div>
				<?php } ?>
			</ul>
			</div>
		<!-- OVERDUES end -->