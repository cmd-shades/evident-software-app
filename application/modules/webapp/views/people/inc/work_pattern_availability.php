<!-- Modal for Adding Availability / Resources -->
<div class="modal fade add-availability-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title" id="myAvailModalLabel">Submit Availability for <?php echo ucwords( $person_details->first_name.' '.$person_details->last_name ); ?></h4>						
			</div>
			<div class="modal-body" id="add-availability-modal-container" >
				<input type="hidden" name="page" value="details" />
				<input type="hidden" name="person_id" value="<?php echo $person_details->person_id; ?>" />
				<table class="table" >
					<thead>
						<tr>
							<td colspan="7">
								<div class="row hide" >
									<div class="form-group">
										<div class="radio">
											<label class="radio-inline"><input type="radio" id="amount_25" name="amount" value="25" checked="">$25.00</label>
										</div>
									</div>

									<div class="form-group">
										<div class="radio">
											<label class="radio-inline"><input type="radio" id="amount_50" name="amount" value="50">$50.00</label>
										</div>
									</div>

									<div class="form-group">
										<div class="radio">
											<label class="radio-inline control-label"><input type="radio" id="amount_100" name="amount" value="100">$100.00</label>
										</div>
									</div>

									<div class="form-group">
										<div class="radio">
											<label class="radio-inline control-label"><input type="radio" id="amount_other" name="amount" value="Other: $">Other: $</label>
										</div>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="7" >
								<label class="strong" >Please Select the week you would like to start from </label>
								<select class="form-control" id="select_week_beginning" name="resource[<?php echo $person_details->person_id; ?>][week_beginning]">
									<option value="" selected="selected">Select Week beginning</option>						
									<?php
										$i_counter			= 1;
										$start_date 		= date( 'd-m-Y', strtotime('monday this week' ) );
										$end_date   		= strtotime( date('d-m-Y', strtotime( $start_date.'+ 4 months')));
										$max_active_date 	= date( 'd-m-Y', strtotime( $start_date.'+3 months' ) );
										for( $week_start 	= strtotime( 'Monday', strtotime( $start_date ) ); $week_start <= $end_date; $week_start = strtotime( '+1 week', $week_start ) ){ ?>
											<option value="<?php echo date( 'd-m-Y', $week_start ); ?>" <?php echo ( strtotime( date( 'd-m-Y', $week_start ) ) > strtotime( $max_active_date ) ) ? 'disabled' : '';?> <?php echo ( $i_counter == 1 ) ? 'selected="selected"' : ''; ?> ><?php echo date( 'M jS, Y', $week_start ); ?></option>							
									<?php $i_counter++; } ?>							
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="7" >
								<label>Please Select the work pattern to apply </label>
								<select class="form-control" id="work_pattern" name="resource[<?php echo $person_details->person_id; ?>][work_pattern]">
									<?php if( !empty( $preset_shifts_patterns ) ) { foreach( $preset_shifts_patterns as $k => $pattern ) { ?>
										<option value="<?php echo $pattern->start_time.'-'.$pattern->finish_time; ?>" data-start_time="<?php echo $pattern->start_time; ?>" data-finish_time="<?php echo $pattern->finish_time; ?>" <?php echo ( ( $pattern->start_time.'-'.$pattern->finish_time ) == '07:00-16:00' ) ? 'selected=selected' : ''; ?> ><?php echo $pattern->start_time.' - '.$pattern->finish_time; ?> Hrs</option>
									<?php } } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="7" >
								<label>How far ahead would you like to create availability for? </label>
								<div class="row">
									<div class="col-md-2" ><label class="pointer"><input class="resource-radio" type="radio" name="resource[<?php echo $person_details->person_id; ?>][weeks_ahead]" style="margin-top:-1px;" value="1" checked=checked/> 1 Week </label></div>
									<div class="col-md-2" ><label class="pointer"><input class="resource-radio" type="radio" name="resource[<?php echo $person_details->person_id; ?>][weeks_ahead]" style="margin-top:-1px;" value="2"/> 2 Weeks </label></div>
									<div class="col-md-2" ><label class="pointer"><input class="resource-radio" type="radio" name="resource[<?php echo $person_details->person_id; ?>][weeks_ahead]" style="margin-top:-1px;" value="4"/> 4 Weeks </label></div>
									<div class="col-md-2" ><label class="pointer"><input class="resource-radio" type="radio" name="resource[<?php echo $person_details->person_id; ?>][weeks_ahead]" style="margin-top:-1px;" value="8"/> 8 Weeks </label></div>
									<div class="col-md-2" ><label class="pointer"><input class="resource-radio" type="radio" name="resource[<?php echo $person_details->person_id; ?>][weeks_ahead]" style="margin-top:-1px;" value="12"/> 12 Weeks </label></div>
									<div class="col-md-2" ><span class="pointer" style="display:inline" ><input class="resource-radio" id="weeks_other" type="radio" name="resource[<?php echo $person_details->person_id; ?>][weeks_ahead]" style="margin-top:0px; display:inline-block" value="other"/> <strong for="weeks_other">Other</strong><input id="custom_weeks" type="text" min="1" style="margin-top:-10px; width:42%; display:inline-block; float:right" class="form-control numbers-only" value="" placeholder="" /></span></div>
								</div>
								<br/>
							</td>
						</tr>
						<tr class="bg-blue" >
							<?php foreach( $week_days as $key => $day ) { ?>
								<th <?php echo ( $key == 6 || $key == 7 ) ? "class='text-red'" : ""; ?> ><?php echo $day->day_short; ?></th>
							<?php } ?>											
						</tr>
					</thead>
					<tbody>
						<tr>
							<?php foreach( $week_days as $key => $day ) { ?>
								<td>
									<div><input class="form-control timepicker <?php echo ( $key == 6 || $key == 7 ) ? 'weekend-start'  : 'weekday-start';  ?>" type="text" name="resource[<?php echo $person_details->person_id; ?>][shift_times][<?php echo $day->day_key; ?>][start_time]" value=""  placeholder="Start time"/ style="margin-bottom:5px;"></div>
									<div><input class="form-control timepicker <?php echo ( $key == 6 || $key == 7 ) ? 'weekend-finish' : 'weekday-finish'; ?>" type="text" name="resource[<?php echo $person_details->person_id; ?>][shift_times][<?php echo $day->day_key; ?>][finish_time]" value="" placeholder="Finish time"/></div>
								</td>
							<?php } ?>	
						</tr>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button id="submit-availability-btn" class="btn btn-success btn-sm">Add Availability</button>
			</div>
		</div>
	</div>
</div>

<script>
	$( document ).ready(function(){
		
		$( '#custom_weeks' ).keypress( function(){
			$( '#custom_weeks' ).focus().css("border","1px solid #ccc");
		});
		
		$( '.resource-radio' ).change( function(){
			$( '#custom_weeks' ).focus().css("border","1px solid #ccc");
			var weeksAhead = $( this ).val();
			if( weeksAhead == 'other' ){
				$( '#custom_weeks' ).prop( 'required', true );
			} else {
				$( '#custom_weeks' ).prop( 'required', false );
				
			}
		});
		
		//Default start / finish times
		var startTime 	= $( '#work_pattern :selected' ).data( 'start_time' ),
			finishTime 	= $( '#work_pattern :selected' ).data( 'finish_time' );
			$( '.weekday-start' ).val( startTime );	
			$( '.weekday-finish' ).val( finishTime );
		
		$( '.add-availability' ).click( function(){
			$( ".add-availability-modal" ).modal( "show" );
		} );
		
		$( '#submit-availability-btn' ).click( function(){
			
			var resourceRdi = $( "input:radio.resource-radio:checked" ).val();
			var weeksOther 	= $( '#custom_weeks' ).val();
			
			if( resourceRdi == 'other' ){
				if( weeksOther.length == 0 ){
					swal({
						type: 'error',
						title: 'Please provide a custom value!'
					});
					$( '#custom_weeks' ).focus().css("border","1px solid red");
					return false;
				} else {
					if( weeksOther > 52 ){
						swal({
							type: 'error',
							title: 'Maximum number of weeks ahead is 52 (1 year)!'
						});
						return false;
					}
					$( '#weeks_other' ).val( weeksOther );
				}
			} 

			var formData 	= $( '#add-availability-modal-container :input' ).serialize();

			$.ajax({
				url:"<?php echo base_url( 'webapp/people/create_diary_resource/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						
						$( '.add-availability-modal' ).modal( 'hide' );
						$( '.modal-backdrop' ).remove();
						
						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 2000
						})
						
						window.setTimeout(function(){ 
							location.reload();
						} ,1000);
					} else {
						swal({
							type: 'error',
							title: data.status_msg
						})
					}		
				}
			});
			return false;
			
		});
		
		//TIMEPICKER ONLY
		//var allowedShiftTimes = ['06:00', '06:30', '07:00', '07:30', '08:00', '15:00', '15:30', '16:00', '17:00', '18:00', '19:00', '20:00' ];

		$('.timepicker').datetimepicker({
				datepicker:false,
				format:'H:i',
				allowTimes: [
					'07:00', '07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00','14:30', 
					'15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30', '22:00'  
				],
				step:30
			} 
		);
		
		//Availability
		$('#work_pattern').change( function(){
			var startTime 	= $( '#work_pattern :selected' ).data( 'start_time' ),
				finishTime 	= $( '#work_pattern :selected' ).data( 'finish_time' );
			$( '.weekday-start' ).val( startTime );	
			$( '.weekday-finish' ).val( finishTime );				
		});
		
	});
</script>