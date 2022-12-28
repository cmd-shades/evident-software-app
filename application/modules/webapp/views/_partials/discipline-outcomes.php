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

		$disciplines = getDisciplineDateRangeFeed($disciplineid, $account_id, $api_end_point);
	} else {
		$disciplines = getDisciplineFeed($disciplineid, $account_id, $api_end_point);
	}

	$builddisciplines = getBuildingDisciplinesFeed($disciplineid, $account_id, $api_end_point); //Get the buildings in a discipline
  
  
?> 
<?php 
	if( !empty( $disciplines->data ) ){
		foreach($disciplines->data as $discipline){ //Start the loop
			$passed = $discipline->outcomes_info->passed_inspections_percent; //Get the pass rate
			$failed = 100;// Set the fail rate
			$recom  = 100 - $discipline->outcomes_info->recommendations_percent; //Get the recommendation rate
?> 

        </div>
	</div>
			
		<div class="disciplines-box">
			<div class="row">
				<div class="col-xs-12 col-md-3">
					<div class="row">
						<div class="col-xs-6 col-md-12">
							<!-- TOTALS start -->
							<div class="totals fire">
								<div class="total-icon">
									<img src="<?php echo $base_url;?>/application/modules/webapp/views/dashboard/images/buildings.svg"/>
								</div>
								<p>Buildings 
									<span>
										<?php echo $discipline->buildings_info->total_buildings;?>
									</span>
								</p>
							</div>
						</div>
						
						<div class="col-xs-6 col-md-12">
							<div class="totals fire">
								<div class="total-icon">
									<img src="<?php echo $base_url;?>/application/modules/webapp/views/dashboard/images/outcomes.svg"/>
								</div>
								<p>Outcomes 
									<span>
										<?php echo $discipline->outcomes_total;?>
									</span>
								</p>
							</div>
						</div>
						<!-- TOTALS end -->
					</div>			
				</div>
				
				<div class="col-md-5 col-md-offset-1">
					<!-- PRIMARY CONTACT start -->
					<div class="contacts">
						<?php if( !empty( $discipline->contact_info->primary_contact ) ){ ?> 
							<!-- <img src="<?php //echo $api_end_point;?>/application/modules/webapp/views/dashboard/images/person.svg"/> -->
							<img src="<?php echo $base_url;?>/assets/images/avatars/user.png"/>
							<p>Name: 
							<?php echo $discipline->contact_info->primary_contact->first_name;?> 
							<?php echo $discipline->contact_info->primary_contact->last_name;?> 
							<br> Phone: 
							<?php echo $discipline->contact_info->primary_contact->telephone;?> 
							<br> Email: 
							<?php echo $discipline->contact_info->primary_contact->email;?> 
							</p>
						<?php } else { ?>
							<img width="15px" src="<?php echo $api_end_point;?>/assets/images/avatars/user.png"/>
							<p>Primary Contact not set</p>
						<?php } ?>
					</div>
					<!-- PRIMARY CONTACT end -->
					
					<!-- SECONDARY CONTACT start -->
					<div class="contacts">
						<?php if( !empty( $discipline->contact_info->secondary_contact ) ){ ?> 
						<!-- <img src="<?php //echo $api_end_point;?>/application/modules/webapp/views/dashboard/images/person.svg"/> -->
						<img src="<?php echo $base_url;?>/assets/images/avatars/user.png"/>
						<p>Name: 
							<?php echo $discipline->contact_info->secondary_contact->first_name;?> 
							<?php echo $discipline->contact_info->secondary_contact->last_name;?> 
							<br> Phone: 
							<?php echo $discipline->contact_info->secondary_contact->telephone;?> 
							<br> Email: 
							<?php echo $discipline->contact_info->secondary_contact->email;?> 
						</p>
						<?php } else { ?>
							<img width="15px" src="<?php echo $api_end_point;?>/assets/images/avatars/user.png"/>
							<p>Secondary Contact not set</p>
						<?php } ?>
					</div>
					<!-- SECONDARY CONTACT end -->
				</div>
			</div>

			<!-- ACCORDION start -->
			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
				<?php if( !empty( $builddisciplines ) ){ $count = 1; foreach($builddisciplines as $builddiscipline) {
				$site = $builddiscipline->site_id;?>
				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="heading-1">
						<a role="button" data-toggle="collapse" data-parent="#accordion-1" href="#collapse-<?php echo $count;?>" aria-expanded="false" aria-controls="collapse-<?php echo $count;?>"></a>
						<h4 class="panel-title">
							<span class="building">
								<a href="<?php echo $base_url;?>/webapp/dashboard/building?siteid=<?php echo $site;?>">
									<?php echo $builddiscipline->site_name;?>
								</a>
							</span>
							
							<span class="postcode">
								<?php echo strtoupper( $builddiscipline->site_postcodes );?>
							</span>
							
							<span class="priorities <?php echo $builddiscipline->outcome_status;?>">
								<?php echo $builddiscipline->total_outcomes;?>
							</span>
						</h4>
					</div>
					
					<!-- NESTED ACCORDION LOOP start -->
					<div id="collapse-<?php echo $count;?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-1">
						<?php $outcomes = getOutcomesFeed( $disciplineid, $site, $account_id, $api_end_point); //Get the buildings outcomes
						foreach( $outcomes as $i ) {
							if (is_array($i) || is_object($i) && $i->site_id == $site) { ?>
								<div class="panel-body">
									<div class="panel-group" id="accordion-1-<?php echo $count;?>" role="tablist" aria-multiselectable="true">
										<?php if( !empty( $i->outcomes_info ) ){ ?>
										<?php $counter = 1; foreach( $i->outcomes_info as $di ){ ?>
										<div class="panel panel-default">
											<div class="panel-heading" role="tab" id="heading-<?php echo $count;?>-<?php echo $counter;?>">
												<a role="button" data-toggle="collapse" data-parent="#accordion-1-<?php echo $count;?>" href="#collapse-<?php echo $count;?>-<?php echo $counter;?>" aria-expanded="false" aria-controls="collapse-<?php echo $count;?>-<?php echo $counter;?>">
												</a>
												<h4 class="panel-title">
												<span class="building"><?php echo $di->audit_type;?></span>
												<span class="priorities"><?php echo $di->total_outcomes;?></span>
												</h4>
											</div>
											
											<div id="collapse-<?php echo $count;?>-<?php echo $counter;?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-<?php echo $count;?>-<?php echo $counter;?>">
												<div class="panel-body">
													<?php if (is_array($di) || is_object($di)) { foreach($di->audit_outcomes as $did){?>
														<h4 class="outcomers">
															<span class="building">
																<a target="_blank" href="<?php echo $base_url;?>webapp/audit/profile/<?php echo $did->audit_id;?>">
																<?php echo $did->outcome_note;?>
																</a>
															</span>
															<span class="postcode">
																<?php echo $did->date_created;?>
															</span>
															
															<span class="priorities <?php echo $did->audit_outcome;?>">
																<?php echo ucwords( $did->audit_outcome );?>
															</span>
														</h4>
													<?php } };?>
												</div>
											</div>
										</div>
										<?php $counter++; } } else { ?>
											<div class="panel-heading" role="tab" >
											<div class="alert alert-info" role="alert">
  No data found
</div>
											</div>
										<?php } ?>
									</div>
								</div>
						<?php } } ?>
					</div>
				</div>

				<?php $count++;} } else { ?>
					<div>
						<div class="panel panel-default">
							<div class="panel-heading" role="tab" id="heading-1s">
							<div class="alert alert-info" role="alert">
  No data found
</div>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
    
			</div>
        <!-- NESTED ACCORDION end -->
		</section>	
    </div> 
	<!-- ACCORDION end -->
	
	<?php } } else {?>
		<div>
			<div class="panels panel-defaults">
				<div class="panel-headings" role="tab" id="heading-1">
				<div class="alert alert-info" role="alert">
  No data found
</div>
				</div>
			</div>
		</div>
	<?php } ?>