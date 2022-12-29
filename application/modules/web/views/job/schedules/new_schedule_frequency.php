<style>
	.panel-body{
		background-color:#F7F7F7; 
		height:140px; 
		min-height:140px;
	}
</style>

<div>
	<div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
		<legend class="evidocs-legend">Add New Schedule Frequency</legend>
		<div class="x_panel tile has-shadow">
			<form id="schedule-frequency-creation-form" >
				<input type="hidden" name="override_existing" value="" />
				<input type="hidden" name="page" value="details" />
				<input type="hidden" name="frequency_id" value="" />
				<div class="rows">
					<div class="schedule_frequency_creation_panel1 col-md-12 col-sm-12 col-xs-12">

						<h4>What is the name of this Schedule Frequency?</h4>
						<div class="">
							<div class="form-group">
								<input name="frequency_name" class="form-control required" type="text" data-label_text="Schedule Frequency" placeholder="E.g. Daily, Weekly, Monthly etc." value="" />
							</div>							
						</div>

						<div>
							<h4>Please provide a detailed description</h4>
							<div class="">
								<div class="form-group">
									<textarea name="frequency_desc" class="form-control required" type="text" data-label_text="Schedule Frequency Description" placeholder="Give a detailed description of what this Schedule Frequency does..." value=""></textarea>
								</div>							
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-success btn-next schedule-frequency-creation-steps" data-currentpanel="schedule_frequency_creation_panel1" type="button">Next</button>				
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">&nbsp;</div>
						</div>
					</div>
					
					<div class="schedule_frequency_creation_panel2 col-md-12 col-sm-12 col-xs-12" style="display:none" >

						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<h4>Which Frequency Group does it belong to?</h4>
								<div class="form-group" >
									<select id="frequency_group" name="frequency_group" class="form-control required" data-label_text="Frequency Group" >
										<option value="" >Please Select a group</option>
										<?php if (!empty($frequency_groups)) {
										    foreach ($frequency_groups as $group => $frequency_group) { ?>
											<option value="<?php echo $frequency_group->frequency; ?>" ><?php echo $frequency_group->frequency_alt; ?></option>
										<?php }
										    } ?>
									</select>
								</div>
								<h4>How many activities are required for this Schedule?</h4>
								<div class="form-group row" >
									<div class="col-md-12 col-sm-12 col-xs-12">
										<input name="activities_required" class="numbers-only form-control" type="text" data-label_text="Activities" placeholder="" value="" />
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-back" data-currentpanel="schedule_frequency_creation_panel2" type="button" >Back</button>					
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-success btn-next schedule-frequency-creation-steps" data-currentpanel="schedule_frequency_creation_panel2" type="button" >Next</button>					
							</div>
						</div>
					</div>
					
					<div class="schedule_frequency_creation_panel3 col-md-12 col-sm-12 col-xs-12" style="display:none" >
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<div class="form-group">
									<div class="text-center">
										<br/>
										<p>You are about to submit a request to create a new Schedule Frequency.</p>
										<p>Click the "Create Schedule Frequency" to proceed or Back to review your Schedule Frequency setup.</p>
										<br/>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-back" data-currentpanel="schedule_frequency_creation_panel3" type="button" >Back</button>					
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button id="create-schedule-frequency-btn" class="btn btn-block btn-flow btn-success btn-next" type="button" >Create Schedule Frequency</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script>

	$( document ).ready( function() {

		$( '#evidoc_type_id, #associated_risks_id, #required_skills' ).select2({
			allowClear: true,
			minimumResultsForSearch: -1,
		});
		
		//Clear red-bordered elements
		$( '.required' ).change( function(){
			$( this ).css("border","1px solid #ccc");
		});
		
		//Trigger search field
		$('[name="frequency_name"]').keyup(function(){
			$( this ).focus().css("border","1px solid #ccc");
			$('[name="frequency_name"]').val();
		});
		
		$(".schedule-frequency-creation-steps").click(function(){
			//Clear errors first
			$( '.error_message' ).each(function(){
				$( this ).text( '' );
			});
			
			var currentpanel = $(this).data( "currentpanel" );
			
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
			panelchange("."+currentpanel)	
			return false;
		});

		//** Validate any inputs that have the required class, if empty return the name attribute **/
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
		
		//Go back-btn
		$(".btn-back").click(function(){
			var currentpanel = $(this).data("currentpanel");
			go_back("."+currentpanel)	
			return false;
		});
		
		function panelchange(changefrom){
			var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
			var changeto = ".schedule_frequency_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", {direction : 'left'}, 500);
			$( changeto ).delay(600).show( "slide", {direction : 'right'},500);	
			return false;	
		}
		
		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
			var changeto = ".schedule_frequency_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", {direction : 'right'}, 500);
			$( changeto ).delay(600).show( "slide", {direction : 'left'},500);	
			return false;	
		}
		
		//Submit Evidoc form
		$( '#create-schedule-frequency-btn' ).click(function( e ){
		
			e.preventDefault();
			
			submitJobTypeForm();
			
		});
		
		function submitJobTypeForm(){
			
			var formData = $( '#schedule-frequency-creation-form' ).serialize();
			
			swal({
				title: 'Confirm Frequency creation?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function ( result ) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/create_schedule_frequency/'); ?>",
						method:'POST',
						data:formData,
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 && ( data.schedule_frequency !== '' ) ){
								console.log(data);
								var alreadyExists= data.already_exists;
								var newFrequencyId = data.schedule_frequency.frequency_id;
								console.log(alreadyExists);
								if( alreadyExists ){
									var existUrl = "<?php echo base_url('webapp/job/schedule_frequencies/'); ?>"+data.schedule_frequency.frequency_id;
									swal({
										type: 'warning',
										showCancelButton: true,
										confirmButtonColor: '#5CB85C',
										cancelButtonColor: '#9D1919',
										confirmButtonText: 'Override',
										title: 'This Schedule Frequency already exists!',
										html:
											'<b>Schedule Frequency: </b>' + ucwords( data.schedule_frequency.frequency_name ) + '<br/>' +
											'<b>Description: </b><br/>' +
											'<em>' + data.schedule_frequency.frequency_desc + '</em>' + '<br/><br/>' +
											'Click <a href="'+existUrl+'" target="_blank">here</a> to view it or Cancel to go back and change name'
									}).then( function (result) {
										if ( result.value ) {
											//Do this if user accepts to Override
											$( '[name="frequency_id"]' ).val( data.schedule_frequency.frequency_id );
											$( '[name="override_existing"]' ).val( 1 );
											$( '[name="frequency_desc"]' ).val( data.schedule_frequency.frequency_desc );	
											
											//Do something here
											submitJobTypeForm();											
										}else{
											//Do this if user cancels to change the name
										}
									})
									
								} else {
									swal({
										type: 'success',
										title: data.status_msg,
										showConfirmButton: false,
										timer: 2000
									})
									window.setTimeout(function(){ 
										location.href = "<?php echo base_url('webapp/job/schedule_frequencies/'); ?>"+newFrequencyId;
									} ,1000);
								}	
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

	});
</script>