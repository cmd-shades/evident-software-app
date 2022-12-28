


<div class="asset-schedule" >
	
	<?php 
		$site_id 		= $asset_details->site_id;
		$asset_id 		= $asset_details->asset_id;
		$contract_id 	= !empty( $asset_details->contract_id ) ? $asset_details->contract_id : false;
	?>
	<div class="asset-details" >
		<div class="alert bg-blue no-border" role="alert">
			<table class="table-responsive" style="width:100%" >
				<tr>
					<th width="25%" ><legend>ASSET TYPE</legend></th>
					<th width="30%" ><legend><?php echo ( !empty( $asset_details->primary_attribute ) ) ? strtoupper( $asset_details->primary_attribute ) : 'PRIMARY ATTRIBUTE'; ?></legend></th>
					<th width="25%" ><legend>ASSET GROUP</legend></th>
					<th width="20%" ><legend>CATEGORY</legend></th>
				</tr>
				
				<tr>
					<td ><span class="pull-left" ><?php echo ( !empty( $asset_details->asset_type ) ) ? $asset_details->asset_type : ''; ?></span></td>
					<td ><span class="pull-left" ><?php echo ucwords( $asset_details->attribute_value ); ?></span></td>
					<td ><span class="pull-left" ><?php echo ucwords( $asset_details->asset_group ); ?></span></td>
					<td ><span class="pull-left" ><?php echo ucwords( $asset_details->category_name ); ?></span></td>
				</tr>
			</table>
		</div>
	</div>		
	
	<div class="hide progress">
		<div id="progress-bar" class="progress-bar progress-bar-success" data-transitiongoal="0" aria-valuenow="0" style="width: 0"></div>
	</div>
	
	<div class="x_panel tile has-shadow">
		<form id="schedule-creation-form" >
			<input type="hidden" name="override_existing" value="" />
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="contract_id" value="<?php echo ( !empty( $contract_id ) ) ? $contract_id : false; ?>" />
			<input type="hidden" name="site_id" value="<?php echo ( !empty( $site_id ) ) ? $site_id : false; ?>" />
			<input type="hidden" name="location_id" value="" />
			<input type="hidden" name="asset_id" value="<?php echo ( !empty( $asset_id ) ) ? $asset_id : false; ?>" />
			<div class="schedule_creation_panel1 col-md-12 col-sm-12 col-xs-12">
				<h4>Please select your Schedule Frequency</h4>
				<div class="form-group" >
					<select id="frequency_id" name="frequency_id" class="form-control required" data-label_text="Schedule Frequency" >
						<option value="" >Please Select frequency</option>
						<?php if( !empty( $schedule_frequencies ) ) { foreach( $schedule_frequencies as $k => $frequency ) { ?>
							<option value="<?php echo $frequency->frequency_id; ?>" data-frequency_name="<?php echo $frequency->frequency_name; ?>" data-frequency_desc="<?php echo $frequency->frequency_desc; ?>" data-activity_interval="<?php echo $frequency->activity_interval; ?>" data-frequency_group="<?php echo $frequency->frequency_group; ?>" data-activities_required="<?php echo $frequency->activities_required; ?>" ><?php echo $frequency->frequency_name; ?></option>
						<?php } } ?>
					</select>
				</div>
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-success btn-next fetch-evidocs-btn" data-currentpanel="schedule_creation_panel1" data-progress_level="20" type="button">Next</button>				
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">&nbsp;</div>
				</div>
			</div>

			<div class="schedule_creation_panel2 col-md-12 col-sm-12 col-xs-12" style="display:none" >
				<h4>Which EviDoc is this Schedule for?</h4>
				<div class="form-group" >
					<select id="evidoc_type_id" name="evidoc_type_id" class="form-control required save-completed" style="width:100%" >
					
					</select>
				</div>
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-back" data-currentpanel="schedule_creation_panel2" data-progress_level="20" type="button" >Back</button>					
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-success btn-next fetch-job-types-btn" data-currentpanel="schedule_creation_panel2" data-progress_level="40" type="button" >Next</button>					
					</div>
				</div>
			</div>
			
			<div class="schedule_creation_panel3 col-md-12 col-sm-12 col-xs-12" style="display:none" >
				<h4>Which Job Type is this Schedule for?</h4>
				<div class="form-group" >
					<select id="job_type_id" name="job_type_id" class="form-control required save-completed" style="width:100%" >
					
					</select>
				</div>
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-back" data-currentpanel="schedule_creation_panel3" data-progress_level="40"type="button" >Back</button>					
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-success btn-next schedule-creation-steps" data-currentpanel="schedule_creation_panel3" data-progress_level="60" type="button" >Next</button>					
					</div>
				</div>
			</div>
			
			<div class="schedule_creation_panel4 col-md-12 col-sm-12 col-xs-12" style="display:none" >
				<h4>When is the first Activity due by?</h4>
				<div class="form-group" >
					<input name="first_activity_due_date" class="form-control nowonwards-date required" type="text" data-label_text="Schedule Due By" placeholder="dd-mm-yyyy" value="" />
				</div>
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-back" data-currentpanel="schedule_creation_panel4" data-progress_level="60" type="button" >Back</button>					
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-success btn-next generate-activities-btn" data-currentpanel="schedule_creation_panel4" data-progress_level="80" type="button" >Next</button>					
					</div>
				</div>
			</div>
			
			<div class="schedule_creation_panel5 col-md-12 col-sm-12 col-xs-12" style="display:none" >
				<h4>Please set the Activity Proportions</h4>
				<div id="activity_proportions" class="form-group" >
					
				</div>
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-back" data-currentpanel="schedule_creation_panel5" data-progress_level="80" type="button" >Back</button>					
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-success btn-next schedule-creation-steps" data-currentpanel="schedule_creation_panel5" data-progress_level="100" type="button" >Next</button>					
					</div>
				</div>
			</div>
			
			<div class="schedule_creation_panel6 col-md-12 col-sm-12 col-xs-12" style="display:none" >
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
						<button class="btn btn-block btn-flow btn-back" data-currentpanel="schedule_creation_panel6" type="button" >Back</button>					
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

	var completedData = []

	$( document ).ready( function() {
		
		$( '.save-completed' ).change(function(){
			completedData[$(this).attr('id')] =  $(this).val()
		})
		
		$( '.nowonwards-date' ).datetimepicker({
			//minDate:0,
			timepicker:false,
			format:'d-m-Y'
		});
		
		$( '.nowonwards-date' ).datetimepicker({
			//minDate:0,
			timepicker:false,
			format:'d-m-Y'
		});
	
		var auditGroup 		= "<?php echo $audit_group; ?>";
		var assetId  		= "<?php echo ( $asset_details->asset_id ) ? $asset_details->asset_id : false; ?>";
		var assetType  		= "<?php echo ( $asset_details->asset_type_id ) ? $asset_details->asset_type_id : false; ?>";
		var schedActivities 	= 4; 
		$( '#schedule_frequencies' ).select2({
			allowClear: true,
			minimumResultsForSearch: -1,
		});
		
		//LOAD-EVIDOCS
		$( '.fetch-evidocs-btn' ).click( function(){
			
			
			var currentpanel 	= $( this ).data( "currentpanel" );
			var progress_level 	= $( this ).data( "progress_level" );
			// var schedActivities = $( '#frequency_id option:selected' ).data( 'activities_required' );
			// var activityIntevl 	= $( '#frequency_id option:selected' ).data( 'activity_interval' );
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

			var wheRe = {
				frequency_id:freqId,
				audit_group:auditGroup
			};
			
			if( assetType ){
				wheRe.asset_type_id = assetType;
			}
			
			if( freqName ){
				wheRe.frequency_name = encodeURIComponent( freqName );
			}

			$.ajax({
				url:"<?php echo base_url( 'webapp/job/fetch_evidocs/' ); ?>",
				method:"POST",
				data:{ page:'details', where:wheRe},
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 && ( data.evidocs_data !== '' ) ){

						// if( schedActivities > 0 ){
							// generate_activity_placeholders( schedActivities, freqId, activityIntevl );
						// }
					
						$( '#evidoc_type_id' ).html( data.evidocs_data );
						panelchange( "."+currentpanel );

						update_progress_bar( progress_level );
						
						updateValuesFromCompleted();
						
						$.each(Object.keys(completedData), function(index, id) {
							value = (completedData[id])
							$("." + currentpanel).find("#" + id).val(value)
						});
						
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
		
		function updateValuesFromCompleted(){
			$.each(Object.keys(completedData), function(index, id) {
				value = (completedData[id])
				$(".asset-schedule").find("#" + id).val(value)
			});
		}
		
		$( '.fetch-job-types-btn' ).click( function(){
			var currentpanel 	= $( this ).data( "currentpanel" );
			var progress_level 	= $( this ).data( "progress_level" );
			var evidocTypeId 	= $( '#evidoc_type_id option:selected' ).val();
			evidocTypeId		= ( evidocTypeId ) ? evidocTypeId: '';
			if( evidocTypeId.length == 0 || evidocTypeId == '' ){
				swal({
					type: 'error',
					text: 'Please choose an Evidoc for this Schedule!'
				});
				return false;
			}
			
			var wheRe = {
				evidoc_type_id:evidocTypeId,
			};

			$.ajax({
				url:"<?php echo base_url( 'webapp/job/fetch_job_types/' ); ?>",
				method:"POST",
				data:{ page:'details', where:wheRe},
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 && ( data.evidocs_data !== '' ) ){

						$( '#job_type_id' ).html( data.job_types_data );
						panelchange( "."+currentpanel );
						
						update_progress_bar( progress_level );
						
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
		
		//CLEAR RED-BORDERED ELEMENTS
		$( '.required' ).change( function(){
			$( this ).css("border","1px solid #ccc");
		});
		
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
			update_progress_bar( progress_level );
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
			
			// update previously entered fields
			
			$.each(Object.keys(completedData), function(index, id) {
				value = (completedData[id])
				$("." + currentpanel).find("#" + id).val(value)
			});
			
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
			update_progress_bar( progress_level );
			
			return false;	
		}
		
		//SUBMIT SCHEDULE FORM
		$( '#create-schedule-btn' ).click(function( e ){
		
			e.preventDefault();
			
			submitScheduleForm();
			
		});
		
		function submitScheduleForm(){
			
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
						success:function( data ){
							if( data.status == 1 && ( data.schedules !== '' ) ){

								var asset_id = data.schedules.asset_id;
							
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2000
								})
								
								window.setTimeout(function(){ 
									if( asset_id ){
										location.href = "<?php echo base_url('webapp/asset/profile/'); ?>"+asset_id+"/schedules";
									} else {
										location.href = "<?php echo base_url('webapp/job/schedule_frequencies/'); ?>";
									}

								} ,1000);

							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}		
						}
					});
				}else{
					$( ".asset_creation_panel8" ).hide( "slide", { direction : 'left' }, 500 );
					go_back( ".asset_creation_panel2" );
					return false;
				}
			}).catch( swal.noop )
		}

		//LOAD-EVIDOCS
		$( '.generate-activities-btn' ).click( function(){
			var currentpanel 	= $( this ).data( "currentpanel" );
			var frequencyObj	= $( '#frequency_id option:selected' );
			var jobTypeObj		= $( '#job_type_id option:selected' );

			var frequencyData = { 
				total_activities	: ( frequencyObj.data( 'activities_required' ) ) ? frequencyObj.data( 'activities_required' ) : 0,
				frequency_id 		: ( frequencyObj.val() ) ? frequencyObj.val() : 0,
				frequency_name 		: ( frequencyObj.data( 'frequency_name' ) ) ? frequencyObj.data( 'frequency_name' ) : false,
				frequency_group 	: ( frequencyObj.data( 'frequency_group' ) ) ? frequencyObj.data( 'frequency_group' ) : false,
				activity_interval 	: ( frequencyObj.data( 'activity_interval' ) ) ? frequencyObj.data( 'activity_interval' ) : false,
			};
			
			var jobTypeData = {
				job_type_id	: ( jobTypeObj.val() ) ? jobTypeObj.val() : 0,
				job_type	: ( jobTypeObj.data( 'job_type' ) ) ? jobTypeObj.data( 'job_type' ) : false,
				due_date	: $( '[name="first_activity_due_date"]' ).val(),
			}
			
			if( ( frequencyData.frequency_id > 0 ) && ( jobTypeData.job_type_id > 0 ) ){
				var laceholdersGenerated = generate_activity_placeholders( frequencyData, jobTypeData );
				if( laceholdersGenerated ){
					panelchange( "."+currentpanel );
				}
			} else {
				swal({
					type: 'warning',
					text: 'Invalid Evidoc type or Job Type info',
				});
				return false;
			}
			return false;
			
		});
		
		function generate_activity_placeholders( frequencyData = false, jobTypeData = false ){
			var html_snippet	= '<table class="small table table-responsive" >';
				//html_snippet	+= '<tr><td colspan="4" ><legend>Asset Schedule</legend></td></tr>';
				html_snippet	+= '<tr>';
					html_snippet	+= '<th width="35%" >ACTIVITY</th>';					
					html_snippet	+= '<th width="35%" >JOB TYPE</th>';				
					html_snippet	+= '<th width="15%" >DUE DATE</th>';		
					html_snippet	+= '<th width="15%" >PROPORTION</th>';								
				html_snippet	+= '</tr>';
			if( ( frequencyData.frequency_id > 0 ) && ( jobTypeData.job_type_id > 0 ) ){
				var i,
					activityCount 		= frequencyData.total_activities,
					activityName		= frequencyData.frequency_name,
					activityInterval	= parseInt( frequencyData.activity_interval );
					
				var jobTypeId 			= jobTypeData.job_type_id,
					jobType				= jobTypeData.job_type,
					dueDate				= jobTypeData.due_date;

				for( i = 1; i <= activityCount; i++ ){
					var perCentage	 	= 100;
						perCentage	 	= parseInt( perCentage );
		
					html_snippet	+= '<tr>';
						html_snippet	+= '<td>';
							html_snippet	+= '<input type="hidden" name="schedule_name" value="'+activityName+'" />';
							html_snippet	+= '<input type="hidden" name="schedule_activities['+i+'][asset_id]" value="'+assetId+'" />';
							html_snippet	+= '<input type="hidden" name="schedule_activities['+i+'][activities_total]" value="'+( frequencyData.total_activities )+'" />';
							html_snippet	+= '<input type="hidden" name="schedule_activities['+i+'][job_type_id]" value="'+jobTypeId+'" />';
							html_snippet	+= '<input type="hidden" name="schedule_activities['+i+'][activity_interval]" value="'+activityInterval+'" />';
							html_snippet	+= '<input name="schedule_activities['+i+'][activity_name]" value="'+activityName+' Activity '+i+'" class="form-control" />';					
						html_snippet	+= '</td>';					
						html_snippet	+= '<td>'+jobType+'</td>';				
						html_snippet	+= '<td>';
							html_snippet	+= '<input name="schedule_activities['+i+'][due_date]" class="nowonwards-date form-control" type="text" placeholder="Job Due date" value="'+dueDate+'" />';
						html_snippet	+= '</td>';
						html_snippet	+= '<td><input name="schedule_activities['+i+'][proportion]" class="form-control" type="hidden" placeholder="Proportion percentage" value="'+( perCentage )+'" />'+( perCentage )+'%</td>';
					html_snippet	+= '</tr>';

					dueDate = moment( dueDate, 'DD-MM-YYYY' ).add( activityInterval, 'days' );
					dueDate = moment( dueDate, "DD-MM-YYYY" ).format( 'DD-MM-YYYY' );
				}
			} else {
				html_snippet	+= '<tr><td colspan="4">There was an error generating your requested Jobs / Activities</td></tr>';				
			}
			html_snippet	+= '</table>';
			
			$( '#activity_proportions' ).append( html_snippet );

			return true;
		}
		
		function update_progress_bar( completion = 0 ){
			if( completion > 0 ){
				completion = parseInt( completion )
				$( '#progress-bar' ).data( 'transitiongoal', completion );
				$( '#progress-bar' ).data( 'valuenow', completion );
				$( '#progress-bar' ).css( 'width', completion+'%' );
			}
			return true;
		}

	});
</script>