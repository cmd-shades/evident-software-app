<style>
	.modal-vertical-centered {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%) !important;
	}
</style>

<div class="contract-schedule" >
	
	<div class="building-details" >
		<div class="alert bg-blue no-border" role="alert">
			<table class="table-responsive" style="width:100%" >
				<tr>
					<th width="25%" ><legend>CONTRACT NAME</legend></th>
					<th width="25%" ><legend>CONTRACT TYPE</legend></th>
					<th width="35%" ><legend>CONTRACT LEAD</legend></th>
					<th width="15%" ><legend>CONTRACT STATUS</legend></th>
				</tr>
				<tr>
					<td ><span class="pull-left" ><a href="<?php echo base_url('/webapp/contract/profile/'.$contract_details->contract_id.'/schedules' ); ?>"><?php echo ( !empty( $contract_details->contract_name ) ) ? $contract_details->contract_name : ''; ?></a></span></td>
					<td ><span class="pull-left" ><?php echo ucwords( $contract_details->type_name ); ?></span></td>
					<td ><span class="pull-left" ><?php echo ucwords( $contract_details->contract_lead_name ); ?></span></td>
					<td ><span class="pull-left" ><?php echo !empty( $contract_details->status_name ) ? $contract_details->status_name : ''; ?></span></td>
				</tr>
			</table>
		</div>
	</div>		
	
	<div class="hide progress">
		<div id="progress-bar" class="progress-bar progress-bar-success" data-transitiongoal="0" aria-valuenow="0" style="width: 0"></div>
	</div>
	
	<div class="x_panel tile has-shadow">
		<div class="schedule_creation_panel1 col-md-12 col-sm-12 col-xs-12">
			<h4>Please select your Schedule Frequency </h4>
			<div class="form-group" >
				<select id="frequency_id" name="frequency_id" class="form-control required" data-label_text="Schedule Frequency" >
					<option value="" >Please Select frequency</option>
					<?php if( !empty( $schedule_frequencies ) ) { foreach( $schedule_frequencies as $k => $frequency ) { ?>
						<option value="<?php echo $frequency->frequency_id; ?>" <?php echo ( !empty( $frequency_id ) && ( $frequency->frequency_id == $frequency_id ) ) ? 'selected="selected"' : ''; ?> data-frequency_name="<?php echo $frequency->frequency_name; ?>" data-frequency_desc="<?php echo $frequency->frequency_desc; ?>" data-activity_interval="<?php echo $frequency->activity_interval; ?>" data-frequency_group="<?php echo $frequency->frequency_group; ?>" data-activities_required="<?php echo $frequency->activities_required; ?>" ><?php echo $frequency->frequency_name; ?></option>
					<?php } } ?>
				</select>
			</div>
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn btn-block btn-flow btn-success btn-next schedule-creation-steps" data-currentpanel="schedule_creation_panel1" data-progress_level="20" type="button">Next</button>				
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">&nbsp;</div>
			</div>
		</div>

		<div class="schedule_creation_panel2 col-md-12 col-sm-12 col-xs-12" style="display:none" >
			<h4>Please select Category you wish to create Schedules for</h4>
			<div class="row">
				<!-- Get  list of Asset Types by Category ID -->
				
				<div class="row">
					<div id="audit_categories">
					<?php if( !empty( $evidoc_categories ) ){ ?>
							<div class="col-md-4 col-sm-4 col-xs-12">
								<div class="alert bg-blue panel-chk pointer" >
									<div class="row">
										<div class="col-md-12 col-sm-12 col-xs-12">
											<span><label class="text-white pointer text-bold"  ><input id="category-chk-all" data-category_check="category-check" type="checkbox" checked > &nbsp;Check / Uncheck all </label></span>
										</div>
									</div>
								</div>
							</div>
						<?php foreach( $evidoc_categories as $ck => $category_data ){ ?>
							<?php $category_ref = strip_all_whitespace( $category_data->category_ref ); ?>
							<?php if( !empty( $category_data->category_name ) ){ ?>
								<div class="col-md-4 col-sm-4 col-xs-12">
									<div class="alert bg-blue panel-chk pointer" data-category_ref="<?php echo $category_ref; ?>" >
										<div class="row">
											<div class="col-md-12 col-sm-12 col-xs-12">
												<span><label class="text-white pointer"  ><input name="category_id[]" id="category-chk-<?php echo $category_ref; ?>" class="category-check" data-category_check_id="category-chk-<?php echo $category_ref; ?>" type="checkbox" checked value="<?php echo $category_data->category_id; ?>" > &nbsp;<?php echo ucwords( $category_data->category_name ); ?> </label></span>
											</div>
										</div>
									</div>
								</div>
							<?php } ?>
						<?php } ?>
					<?php } ?>
					</div>						
				</div>

				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-back btn-warning" data-currentpanel="schedule_creation_panel2" data-progress_level="40"type="button" >Back</button>					
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<?php if( !empty( $contract_buildings ) ){ ?>
							<button class="btn btn-block btn-flow btn-success btn-next schedule-creation-steps" data-currentpanel="schedule_creation_panel2" data-progress_level="60" type="button" >Next</button>											
						<?php } else { ?>
							<button class="btn btn-block btn-flow btn-success btn-next fetch-buildings-btn" data-currentpanel="schedule_creation_panel2" data-progress_level="60" type="button" >Next</button>											
						<?php } ?>
						
					</div>
				</div>
				
			</div>
		</div>

		<div class="schedule_creation_panel3 col-md-12 col-sm-12 col-xs-12" style="display:none" >
			<br>
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn btn-block btn-flow btn-back btn-warning" data-currentpanel="schedule_creation_panel3" data-progress_level="40"type="button" >Back</button>					
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button id="fetch-evidocs-btn" class="btn btn-block btn-flow btn-success btn-next fetch-evidocs-btn" data-currentpanel="schedule_creation_panel3" data-progress_level="60" type="button" >Next</button>					
				</div>
			</div>
			
			<h4>Please select the Buildings to create Schedules for <i title="For optimal performance, this view loads a maximum of <?php echo SCHEDULE_BUILDINGS_LIMIT; ?> buildings at a time" class="fas fa-info-circle"></i></h4>
			
			<div class="row">
				<div id="contract_buildings">
					<?php if( !empty( $contract_buildings ) ){ ?>
					
						<?php foreach( $contract_buildings as $ref => $site_data ){ ?>
							<?php $site_ref = strip_all_whitespace( $site_data->site_name.$site_data->site_postcodes ); ?>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="small alert bg-blue panel-chk pointer" data-site_ref="<?php echo $site_ref; ?>" >
									<div class="row">
										<div class="col-md-12 col-sm-12 col-xs-12">
											<span><label class="text-white pointer"  ><input name="site_id[]" id="site-chk-<?php echo $site_ref; ?>" class="buildings-chk site-check-all" data-site_name="<?php echo $site_data->site_name; ?>" data-site_postcode="<?php echo $site_data->site_postcodes ?>" data-address_id="<?php echo $site_data->site_address_id ?>" data-site_check_id="site-chk-<?php echo $site_ref; ?>" type="checkbox" checked value="<?php echo $site_data->site_id; ?>" > &nbsp;<small><?php echo ucwords( $site_data->site_name ).', '.strtoupper( $site_data->site_postcodes ); ?></small></label></span>
											<span class="pull-right pointer contract-buildings-toggle" data-contract_site_check_ref="<?php echo $site_ref ?>" ><?php echo  ( ( !empty( $site_data->site_reference ) ) ? $site_data->site_reference : "0" ) ?></span>
											<!-- $return_data .= '<span class="pull-right pointer contract-buildings-toggle" data-contract_site_check_ref="'.$site_ref.'" ><small>'. ( ( !empty( $site_data->schedules_summary ) ) ? count( $site_data->schedules_summary ) : "0" ).' existing Schedules</small></span> -->
										</div>
									</div>
								</div>
							</div>
					<?php } } else { ?>
					
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<p>There are currently no Buildings linked to this Contract. Please link them <a target="_blank" href="<?php echo base_url('/webapp/contract/profile/'.$contract_details->contract_id.'/linked_sites' ); ?>" >here</a>. If you've already done that, <a href="" onClick="window.location.reload();" >click here</a> to restart this process.</p>
						</div>
						
					<?php } ?>
				</div>						
			</div>
			
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn btn-block btn-flow btn-back btn-warning" data-currentpanel="schedule_creation_panel3" data-progress_level="40"type="button" >Back</button>					
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button id="fetch-evidocs-btn" class="btn btn-block btn-flow btn-success btn-next fetch-evidocs-btn" data-currentpanel="schedule_creation_panel3" data-progress_level="60" type="button" >Next</button>					
				</div>
			</div>
		</div>
			
		<div class="schedule_creation_panel4 col-md-12 col-sm-12 col-xs-12" style="display:none" >
			
			<div class="form-group" >
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-back btn-warning" data-currentpanel="schedule_creation_panel4" data-progress_level="40"type="button" >Back</button>					
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-success btn-next fetch-job-types-btn" data-currentpanel="schedule_creation_panel4" data-progress_level="60" type="button" >Next</button>					
					</div>
				</div>			
			</div>
			<br>
			<h4>Which EviDoc are these Schedules for?</h4>
			<div class="row">
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-4 col-sm-4">
							<h4 class="bold" >Schedule Frequency</h4>
						</div>
						<div class="col-md-4 col-sm-4">
							<h4 class="bold" >Building Name</h4>
						</div>
						<div class="col-md-4 col-sm-4">
							<h4 class="bold" >EviDoc Name</h4>
						</div>
					</div>
				</div>
				<div id="evidoc_type_id" >
					
				</div>
			
			</div>
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn btn-block btn-flow btn-back btn-warning" data-currentpanel="schedule_creation_panel4" data-progress_level="40"type="button" >Back</button>					
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn btn-block btn-flow btn-success btn-next fetch-job-types-btn" data-currentpanel="schedule_creation_panel4" data-progress_level="60" type="button" >Next</button>					
				</div>
			</div>
		</div>
		
		<div class="schedule_creation_panel5 col-md-12 col-sm-12 col-xs-12" style="display:none" >
			<div class="form-group" >
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-back btn-warning" data-currentpanel="schedule_creation_panel5" data-progress_level="40"type="button" >Back</button>					
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-success btn-next schedule-creation-steps refine-selected-options" data-currentpanel="schedule_creation_panel5" data-progress_level="60" type="button" >Next</button>					
					</div>
				</div>
				<br>
				<h4>Which Job Type is this Schedule for?</h4>
				<div class="row">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-3 col-sm-3">
								<h4 class="bold" >Schedule Frequency</h4>
							</div>
							<div class="col-md-3 col-sm-3">
								<h4 class="bold" >Building Name</h4>
							</div>
							<div class="col-md-3 col-sm-3">
								<h4 class="bold" >EviDoc Name</h4>
							</div>
							<div class="col-md-3 col-sm-3">
								<h4 class="bold" >Job Type</h4>
							</div>
						</div>
					</div>
				
					<div id="job_type_id" name="job_type_id" style="width:100%" >

					</div>
				</div>

			</div>
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn btn-block btn-flow btn-back btn-warning" data-currentpanel="schedule_creation_panel5" data-progress_level="40"type="button" >Back</button>					
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn btn-block btn-flow btn-success btn-next schedule-creation-steps refine-selected-options" data-currentpanel="schedule_creation_panel5" data-progress_level="60" type="button" >Next</button>					
				</div>
			</div>
		</div>
		
		<form id="schedule-creation-form" >
			<input type="hidden" name="page" value="details" />
			<input id="contract_id" type="hidden" name="contract_id" value="<?php echo ( !empty( $contract_details->contract_id ) ) ? $contract_details->contract_id : false; ?>" />
			<input type="hidden" name="location_id" value="" />
			<input type="hidden" name="schedule_name" value="Contract Buildings - " />
		
			<div class="schedule_creation_panel6 col-md-12 col-sm-12 col-xs-12" style="display:none" >
				<h4>When is the first Activity due by?</h4>
				<div class="form-group" >
					<input name="first_activity_due_date" class="form-control datepicker nowonwards-date required" type="text" data-label_text="Schedule Due By" placeholder="dd-mm-yyyy" value="<?php echo date( 'd-m-Y' ); ?>" />
				</div>
				<h4>How many time should each Building be checked?</h4>
				<div class="form-group" >
					<input name="number_of_checks" class="form-control required" type="text" data-label_text="Number of Checks" placeholder="Number of Checks" value="1" />
				</div>
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-back btn-warning" data-currentpanel="schedule_creation_panel6" data-progress_level="60" type="button" >Back</button>					
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-success btn-next prepare-schedule-placeholders" data-currentpanel="schedule_creation_panel6" data-progress_level="80" type="button" >Next</button>					
					</div>
				</div>
			</div>
			
			<div class="schedule_creation_panel7 col-md-12 col-sm-12 col-xs-12" style="display:none" >
				
				<div class="row">
					<div class="row" style="margin:15px 0">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-back btn-warning" data-currentpanel="schedule_creation_panel7" data-progress_level="80" type="button" >Back</button>					
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-success btn-next schedule-creation-steps" data-currentpanel="schedule_creation_panel7" data-progress_level="100" type="button" >Next</button>					
						</div>
					</div>
				</div>
			
				<h4>Please Review your Schedule Activities Setup</h4>
				<div id="schedule_placeholders" class="form-group" >
					
				</div>

				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-back btn-warning" data-currentpanel="schedule_creation_panel7" data-progress_level="80" type="button" >Back</button>					
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-success btn-next schedule-creation-steps" data-currentpanel="schedule_creation_panel7" data-progress_level="100" type="button" >Next</button>					
					</div>
				</div>
			</div>
			
			<div class="schedule_creation_panel8 col-md-12 col-sm-12 col-xs-12" style="display:none" >
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="form-group">
							<div class="text-center">
								<br/>
								<p>You are about to submit a request to create Activity Schedules.</p>
								<p>Click the "Create Schedules" to proceed or Back to review your setup.</p>
								<br/>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-back" data-currentpanel="schedule_creation_panel8" type="button" >Back</button>					
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button id="create-schedule-btn" class="btn btn-block btn-flow btn-success btn-next" type="button" >Create Schedules</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<script>

	$( document ).ready( function() {
		
		$( '#schedule_placeholders' ).on( 'focus', '.datepicker', function(){
			$( this ).datetimepicker({
				timepicker:false,
				format:'d-m-Y',
				scrollInput : false
			});
		});

		var preSelectedFreqId = "<?php echo ( $frequency_id ) ? $frequency_id : ''; ?>";
		
		if( preSelectedFreqId ){
			var currentpanel = 'schedule_creation_panel1';
			panelchange( "."+currentpanel );
		}
		
		$("#category-chk-all").click(function(){
		
			var categoryClass = $( this ).data( 'category_check' );
			
			if( $( this ).is(':checked') ){
				$( '.'+categoryClass ).each( function(){
					$( this ).prop( 'checked', true );
				} );
			} else {
				$( '.'+categoryClass ).each( function(){
					$( this ).prop( 'checked', false );
				} );
			}
		});
		

		$( '#contract_buildings' ).on( 'click', '#buildings-chk-all', function(){
		
			var buildingsClass = $( this ).data( 'buildings_check' );
			
			if( $( this ).is(':checked') ){
				$( '.'+buildingsClass ).each( function(){
					$( this ).prop( 'checked', true );
				} );
			} else {
				$( '.'+buildingsClass ).each( function(){
					$( this ).prop( 'checked', false );
				} );
			}
		});
		
		var contractId 		= "<?php echo $contract_details->contract_id; ?>";
		var selectedSiteId 	= "<?php echo !empty( $selected_site_id ) ? $selected_site_id : ''; ?>";
		
		$(".schedule-creation-steps").click(function(){
			//CLEAR ERRORS FIRST
			$( '.error_message' ).each(function(){
				$( this ).text( '' );
			});
			
			var currentpanel 	= $(this).data( "currentpanel" );
			var progress_level 	= $(this).data( "progress_level" );
			
			var inputs_state = check_inputs( currentpanel );			
			if( inputs_state ){
				$( '[name="'+inputs_state+'"]' ).focus().css("border","1px solid red");
				var labelText = $( '[name="'+inputs_state+'"]' ).parent().find('label').text();
					labelText = ( labelText !== "" && ( labelText.length > 0 ) ) ? labelText : $( '[name="'+inputs_state+'"]' ).data( 'label_text' )+' is a required';
				swal({
					type: 'error',
					title: labelText
				})
				return false;
			}
			panelchange("."+currentpanel);

			return false;
		});

		// VALIDATE ANY INPUTS THAT HAVE THE REQUIRED CLASS, IF EMPTY RETURN THE NAME ATTRIBUTE
		function check_inputs( currentpanel ){
			
			var result = false;
			var panel  = "." + currentpanel;
			
			$( $( panel + " .required" ).get().reverse() ).each( function(){
				var fieldName  = '';
				var inputValue = $( this ).val();
				if( ( inputValue == false ) || ( inputValue == '' ) || ( inputValue.length == 0 ) ){
					fieldName = $(this).attr( 'name' );
					result    = fieldName;
					return result;
				}
			});
			return result;
		}
		
		//GO BACK-BTN
		$(".btn-back").click(function(){
			var currentpanel = $(this).data("currentpanel");
			go_back("."+currentpanel)	
			return false;
		});
		
		function panelchange(changefrom){
			var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
			var changeto = ".schedule_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", {direction : 'left'}, 500);
			$( changeto ).delay(600).show( "slide", {direction : 'right'},500);	
			return false;	
		}
		
		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
			var changeto = ".schedule_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", {direction : 'right'}, 500);
			$( changeto ).delay(600).show( "slide", {direction : 'left'},500);	
			
			var progress_level 	= $( changeto ).data( "progress_level" );
			
			return false;	
		}
		
		//SUBMIT SCHEDULE FORM
		$( '#create-schedule-btn' ).click(function( e ){
		
			e.preventDefault();
			
			submitScheduleForm();
			
		});
		
		function submitScheduleForm( liMit = "<?php echo SCHEDULE_CLONE_DEFAULT_LIMIT; ?>", offSet = 0 ){
			
			var freqId 				= $( '#frequency_id option:selected' ).val();
			var totalSites 			= $( '#total_sites' ).val();
			var totalactivitiesDue 	= $( '#total_activities_due' ).val();
			
			$( '#schedule-creation-form' ).append( '<input type="hidden" name="frequency_id" value="'+freqId+'" />' );
			$( '#schedule-creation-form' ).prepend( '<input type="hidden" name="total_sites" value="'+totalSites+'" /><input type="hidden" name="total_activities_due" value="'+totalactivitiesDue+'" />' );
			
			var formData = $( '#schedule-creation-form' ).serialize();

			swal({
				title: 'Confirm Schedule creation?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function ( result ) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/create_schedules/' ); ?>",
						method:'POST',
						data:formData,
						dataType: 'json',
						beforeSend: function(){
							showPleaseWait();
						},
						success:function( data ){
							hidePleaseWait();
							if( data.status == 1 && ( data.schedules.schedule_id !== '' ) ){
								var activitiesData 	= data.schedules.activities_data,
									newScheduleId 	= data.schedules.schedule_id,
									contractID 		= data.schedules.contract_id,
									scheduleName 	= data.schedules.schedule_name,
									frequencyID 	= data.schedules.frequency_id,
									contractName 	= "<?php echo ( !empty( $contract_details->contract_name ) ) ? $contract_details->contract_name : ''; ?>",
									totalActivities	= totalactivitiesDue,
									dataCounters 	= activitiesData.counters,
									totalAssets 	= dataCounters.expected_assets,
									liMit			= activitiesData.counters.limit,
									offSet			= ( Math.floor( ( dataCounters.processed_activities / dataCounters.expected_activities )*dataCounters.activity_pages ) ) * activitiesData.counters.limit;
								swal({
									type: 'success',
									showCancelButton: true,
									confirmButtonColor: '#5CB85C',
									cancelButtonColor: '#9D1919',
									confirmButtonText: 'Proceed',
									title: 'Scheduling Process started!',
									html:
										'<p>Please check and confirm that the details below are correct, then click proceed to complete the Scheduling process.</p>' +
										'<table class="table table-responsive pull-left">' +
											'<tr>' +
												'<th>Schedule Name:</th><td>' + scheduleName+ '</td>' +
											'</tr>' +
											'<tr>' +
												'<th>Contract Name:</th><td>' + contractName+ '</td>' +
											'</tr>' +
											'<tr>' +
												'<th>Buildings Processed:</th><td>' + dataCounters.processed_sites + ' of ' + dataCounters.expected_sites + '</td>' +
											'</tr>' +
											'<tr>' +
												'<th>Jobs Processed:</th><td>' + dataCounters.processed_activities + ' of ' + totalactivitiesDue + '</td>' +
											'</tr>' +
										'</table>'
								}).then( function (result) {
									if ( result.value ) {
										
										if( dataCounters.processed_activities === dataCounters.expected_activities ){
											completeSchedulingProcess({
												page: 'details',
												schedule_id: newScheduleId,
												contract_id: contractID,
												frequency_id: frequencyID,
											});
											
										} else {
											submitScheduleForm( liMit, offSet );
										}
							
									} else {
										//Do this if user cancels to change the name
									}
								});
								
							} else {
								swal({
									type: 'error',
									title: data.status_msg
								})
							}		
						}
					});
				}else{
					$( ".site_creation_panel8" ).hide( "slide", { direction : 'left' }, 500 );
					go_back( ".site_creation_panel2" );
					return false;
				}
			}).catch( swal.noop )
		}
		

		//FETCH BUILDINGS-BY-CONTRACT
		$( '.fetch-contract-buildings-details-btn' ).click( function(){
		
			var contractId = "<?php echo $contract_details->contract_id; ?>";
		
			$( '#loading-indicator' ).show();
				
			if( contractId.length == 0 || contractId == '' ){
				swal({
					type: 'error',
					text: 'Something went wrong! Pleaase reload the page and try again.'
				});
				return false;
			}
			
			var wheRe = {
				contract_id:contractId,
				schedules_info:1
			};

			$.ajax({
				url:"<?php echo base_url( 'webapp/job/buildings_by_contract/' ); ?>",
				method:"POST",
				data:{ page:'details', where:wheRe},
				dataType: 'json',
				success:function( data ){
					
					$('#loading-indicator').hide();
					
					if( data.status == 1 && ( data.buildings_data !== '' ) ){
			
						$( '#contract_buildings_data' ).html( data.buildings_data );
						panelchange( "."+currentpanel );
						return false;

					}else{
						swal({
							type: 'warning',
							text: data.status_msg,
							showCancelButton: true,
							confirmButtonColor: '#5CB85C',
							cancelButtonColor: '#9D1919',
							confirmButtonText: 'Ok'
						}).catch( swal.noop )
						return false;
					}		
				}
			});
			
			return false;
			
		});	
		
		$( '.contract-buildings-toggle' ).click( function(){
			var siteReference = $( this ).data( 'contract_site_check_ref' );
			$( '.'+siteReference ).slideToggle( 'fast' );
		});
		
		
		var auditGroup 		= "site";
		
		//LOAD-EVIDOCS
		$( '.fetch-evidocs-btn' ).click( function(){
			
			var auditCategoryIds= [];
			var sitesData			= [];
			var currentpanel 	= $( this ).data( "currentpanel" );
			var progress_level 	= $( this ).data( "progress_level" );

			var freqId 	 		= $( '#frequency_id option:selected' ).val();
			var freqName 	 	= $( '#frequency_id option:selected' ).data( 'frequency_desc' );
			
			freqId			 	= ( freqId ) ? freqId: '';
			
			if( freqId.length == 0 || freqId == '' ){
				swal({
					type: 'error',
					text: 'Please choose a Schedule frequency'
				});
				return false;
			}

			$( "#audit_categories [name='category_id[]']" ).each( function(){
				if( $( this ).is( ':checked' ) ){
					auditCategoryIds.push( $( this ).val() );
				}
			});
			
			$( "#contract_buildings [name='site_id[]']" ).each( function(){
				if( $( this ).is( ':checked' ) ){
					sitesData.push({
						site_id:  $( this ).val(),
						site_name:  $( this ).data( 'site_name' ),
						site_postcode:  $( this ).data( 'site_postcode' ),
						address_id:  $( this ).data( 'address_id' ),
						frequency_id:  freqId,
						frequency_name:  freqName,
					});
				}
			});
	
			if( auditCategoryIds.length == 0 || auditCategoryIds == '' ){
				swal({
					type: 'error',
					text: 'Please select at least 1 Evidoc Category to proceed!'
				});
				return false;
			}
				
			var wheRe = {
				frequency_id:freqId,
				category_id:  auditCategoryIds,
				audit_group:auditGroup,
				sites_data:sitesData,
			};
			
			if( freqName ){
				wheRe.frequency_name = encodeURIComponent( freqName );
			}
			
			$.ajax({
				url:"<?php echo base_url( 'webapp/job/fetch_building_evidocs_display/' ); ?>",
				method:"POST",
				data:{ page:'details', where:wheRe},
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 && ( data.evidocs_data !== '' ) ){
					
						$( '#evidoc_type_id' ).html( data.evidocs_data );
						panelchange( "."+currentpanel );
			
						return false;

					}else{
						swal({
							type: 'warning',
							html: data.status_msg,
							showCancelButton: true,
							confirmButtonColor: '#5CB85C',
							cancelButtonColor: '#9D1919',
							confirmButtonText: 'Ok'
						}).catch( swal.noop )
						return false;
					}		
				}
			});
			
			return false;
			
		});
		
		//Load Job Types
		$( '.fetch-job-types-btn' ).click( function(){
			
			var checkedSites	= [],
				currentpanel 	= $( this ).data( "currentpanel" ),
				progress_level 	= $( this ).data( "progress_level" ),
				evidocTypeId	= [],
				freqId 	 		= $( '#frequency_id option:selected' ).val(),
				freqName 	 	= $( '#frequency_id option:selected' ).data( 'frequency_desc' );
			
			
			$( "#evidoc_type_id [name='site_id[]']" ).each( function(){
				if( $( this ).is( ':checked' ) ){
					var siteName 	= $( this ).data( 'site_name' ),
						siteId		= $( this ).val(),
						sitePostcode= $( this ).data( 'site_postcode' ),
						addressId	= $( this ).data( 'address_id' ),
						eviTypeId	= $( '#evidoc'+siteId+' option:selected' ).val(),
						eviTypeNm	= $( '#evidoc'+siteId+' option:selected' ).data( 'evidoc_type' );
					checkedSites.push({
						site_id: siteId, 
						site_name: siteName, 
						site_postcode:  sitePostcode, 
						address_id:  addressId, 
						frequency_id: freqId,
						frequency_name: freqName,
						evidoc_type: encodeURIComponent( eviTypeNm ),
						evidoc_type_id: eviTypeId
					});
					evidocTypeId.push( eviTypeId );
				}
			});

			var evidocTypeId		= ( evidocTypeId ) ? evidocTypeId : '';
			
			if( evidocTypeId.length == 0 || evidocTypeId == '' ){
				swal({
					type: 'error',
					text: 'Please choose an Evidoc for this Schedule!'
				});
				return false;
			}

			var wheRe = {
				evidoc_type_id: $.unique( evidocTypeId ),
				sites_data:checkedSites
			};

			$.ajax({
				url:"<?php echo base_url( 'webapp/job/fetch_job_types_multi_building_display/' ); ?>",
				method:"POST",
				data:{ page:'details', where:wheRe},
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 && ( data.job_types_data !== '' ) ){

						$( '#job_type_id' ).html( data.job_types_data );
						panelchange( "."+currentpanel );
						
						return false;

					}else{
						swal({
							type: 'warning',
							text: data.status_msg,
							showCancelButton: true,
							confirmButtonColor: '#5CB85C',
							cancelButtonColor: '#9D1919',
							confirmButtonText: 'Ok'
						}).catch( swal.noop )
						return false;
					}		
				}
			});
			
			return false;
			
		});
		
		//** Prepare Activities Placeholder **/
		$( '.prepare-schedule-placeholders' ).click( function(){

			var auditGroup		= 'site';
			var contractId		= $( '#contract_id' ).val();
			var currentpanel 	= $( this ).data( "currentpanel" );
			var frequencyObj	= $( '#frequency_id option:selected' );
			var jobTypeObj		= $( '#job_type_id option:selected' );
			
			var scheduleObj		= [],
				currentpanel 	= $( this ).data( "currentpanel" ),
				progress_level 	= $( this ).data( "progress_level" ),
				freqId 	 		= $( '#frequency_id option:selected' ).val(),
				freqName 	 	= $( '#frequency_id option:selected' ).data( 'frequency_desc' ),
				activityInterval= $( '#frequency_id option:selected' ).data( 'activity_interval' ),
				dueDate			= $( '[name="first_activity_due_date"]' ).val(),
				numberOfChecks	= $( '[name="number_of_checks"]' ).val();
			
			$( "#job_type_id [name='site_id[]']" ).each( function(){
				if( $( this ).is( ':checked' ) ){
					var siteId			= $( this ).val(),
						siteName 		= $( this ).data( 'site_name' ),
						sitePostcode 	= $( this ).data( 'site_postcode' ),
						addressId 		= $( this ).data( 'address_id' ),
						jobTypeId		= $( '#jobtype'+siteId+' option:selected' ).val(),
						jobType			= $( '#jobtype'+siteId+' option:selected' ).data( 'job_type' ),
						evidDocTypeId	= $( '#jobtype'+siteId+' option:selected' ).data( 'evidoc_type_id' ),
						evidDocType		= $( '#jobtype'+siteId+' option:selected' ).data( 'evidoc_type' );
					scheduleObj.push({
						site_id:  $( this ).val(),
						contract_id:  contractId,
						site_name: siteName,
						site_postcode: sitePostcode,
						address_id: addressId,
						frequency_id: freqId,
						frequency_name: freqName,
						activity_interval: activityInterval,
						evidoc_type: encodeURIComponent( evidDocType ),
						evidoc_type_id: evidDocTypeId,
						job_type: encodeURIComponent( jobType ),
						job_type_id: jobTypeId						
					});
				}
			});

			var where = { 
				total_activities	: ( frequencyObj.data( 'activities_required' ) ) ? frequencyObj.data( 'activities_required' ) : 0,
				frequency_id 		: ( frequencyObj.val() ) 						? frequencyObj.val() : 0,
				frequency_name 		: ( frequencyObj.data( 'frequency_name' ) ) 	? frequencyObj.data( 'frequency_name' ) : false,
				frequency_group 	: ( frequencyObj.data( 'frequency_group' ) ) 	? frequencyObj.data( 'frequency_group' ) : false,
				activity_interval 	: ( frequencyObj.data( 'activity_interval' ) ) 	? frequencyObj.data( 'activity_interval' ) : false,
				due_date			: dueDate,
				number_of_checks	: numberOfChecks,
				contract_id			:  contractId,
				sites_data			: scheduleObj
			};

			if( where.sites_data.length == 0 || where.sites_data == '' ){
				swal({
					type: 'error',
					text: 'Please choose an Evidoc for this Schedule!'
				});
				return false;
			}

			$.ajax({
				url:"<?php echo base_url( 'webapp/job/prepare_building_schedule_placeholders/' ); ?>",
				method:"POST",
				data:{ page:'details', where, audit_group:auditGroup },
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 && ( data.schedules_data !== '' ) ){

						$( '#schedule_placeholders' ).html( data.schedules_data );
						panelchange( "."+currentpanel );
						
						return false;

					}else{
						swal({
							type: 'warning',
							text: data.status_msg,
							showCancelButton: true,
							confirmButtonColor: '#5CB85C',
							cancelButtonColor: '#9D1919',
							confirmButtonText: 'Ok'
						}).catch( swal.noop )
						return false;
					}		
				}
			});
			
			return false;
			
		});
		
		
		//Load Buildings
		$( '.fetch-buildings-btn' ).click( function(){
			
			$( '#contract_buildings' ).html( '<div>Fetching data... please wait...</div>' );
			
			var currentpanel 	= $( this ).data( "currentpanel" );
			
			var contractId 		= "<?php echo $contract_details->contract_id; ?>",
				freqId 	 		= $( '#frequency_id option:selected' ).val(),
				freqName 	 	= $( '#frequency_id option:selected' ).data( 'frequency_desc' );

			var wheRe = {
				contract_id: contractId,
				frequency_id: freqId
			};

			$.ajax({
				url:"<?php echo base_url( 'webapp/job/fetch_contract_buildings/' ); ?>"+contractId,
				method:"POST",
				data:{ page:'schedules', where:wheRe },
				dataType: 'json',
				beforeSend: function(){
					showPleaseWait();
				},
				success:function( data ){
					hidePleaseWait();
					if( data.status == 1 && ( data.contract_buildings !== '' ) ){

						$( '#contract_buildings' ).html( data.contract_buildings );
						$( '#fetch-evidocs-btn' ).prop( 'disabled', false );
						panelchange( "."+currentpanel );
						return false;

					}else{
						$( '#fetch-evidocs-btn' ).prop( 'disabled', true );
						swal({
							type: 'warning',
							html: data.status_msg,
							showCancelButton: true,
							confirmButtonColor: '#5CB85C',
							cancelButtonColor: '#9D1919',
							confirmButtonText: 'Ok'
						}).catch( swal.noop )
						return false;
					}		
				}
			});
			
			return false;
			
		});
		
		
		/*
		* Complete Scheduling Process
		**/
		function completeSchedulingProcess( formData ){
		
			var contractId = "<?php echo $contract_details->contract_id; ?>";
			var formData = formData;

			if( formData.schedule_id.length > 0 ){
				$.ajax({
					url:"<?php echo base_url('webapp/job/complete_scheduling_process' ); ?>",
					method:'POST',
					data:formData,
					dataType: 'json',
					beforeSend: function(){
						showPleaseWait();
					},
					success:function( data ){
						hidePleaseWait();
						if( data.status == 1 && ( data.schedule.schedule_id !== '' ) ){
							var contractId 		= ( contractId ) ? contractId : data.schedule.contract_id;
							var freqId 			= data.schedule.frequency_id;
							var newScheduleId 	= data.schedule.schedule_id;
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 2500
							})
							
							window.setTimeout(function(){
								
								swal({
									title: 'Do you want to create more schedules?',
									showCancelButton: true,
									confirmButtonColor: '#3085d6',
									cancelButtonColor: '#d33',
									confirmButtonText: 'Yes please',
									cancelButtonText: 'No thanks, I\'m done',
								}).then( function ( result ) {
									if ( result.value ) {
										
										if( contractId ){
											location.href = "<?php echo base_url('webapp/job/new_schedule?contract_id='); ?>"+contractId+"&frequency_id="+freqId;
										} else {
											location.href = "<?php echo base_url('webapp/job/schedules/'); ?>";
										}
										
									} else {
										if( contractId ){
											location.href = "<?php echo base_url('webapp/contract/profile/'); ?>"+contractId+"/schedules";
										} else {
											location.href = "<?php echo base_url('webapp/job/schedules/'); ?>";
										}
									}
								})
							} ,2500);
							
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
						}
					}
				});
			
			} else {
				swal({
					type: 'error',
					title: 'Invalid Data'
				});
				return false;
			}
		}

	});
	
</script>