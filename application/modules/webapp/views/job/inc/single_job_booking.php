<form id="job-creation-form" >
	<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
	<input type="hidden"  name="page" value="details"/>
	<div class="row">
		<div class="job_creation_panel1 col-md-12">
			<div class="input-group form-group">
				<label class="input-group-addon">Job Type</label>
				<select id="job_type_id" name="job_type_id" class="form-control required" data-label_text="Job Type" >
					<option value="" >Please Select a Job type</option>
					<?php if( !empty( $job_types ) ) { foreach( $job_types as $k => $job_type ) { ?>
						<option value="<?php echo $job_type->job_type_id; ?>" ><?php echo $job_type->job_type; ?></option>
					<?php } } ?>
				</select>
			</div>
			<div class="input-group form-group">
				<label class="input-group-addon">Job Duration (Slots)</label>
				<select name="job_duration" class="form-control" >
					<option>Please select</option>
					<?php if( !empty( $job_durations ) ) { foreach( $job_durations as $k => $duration ) { ?>
						<option value="<?php echo $k; ?>" <?php echo ( $k == '1.0' ) ? 'selected="selected"' : '' ?> ><?php echo $duration; ?></option>
					<?php } } ?>
				</select>
			</div>
			<?php /* <div class="input-group form-group">
				<label class="input-group-addon">Job Date</label>
				<input id="job_date" name="job_date" class="form-control datepicker" type="text" placeholder="Job date" value="" />
			</div> -->
			<!-- <div class="input-group form-group" style="margin-left:20%" >
				<label class="pointer" ><input id="no_specific_date" type="checkbox" value="" /> No specific date</label>
			</div> */ ?>
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn btn-block btn-flow btn-success btn-next job-creation-steps" data-currentpanel="job_creation_panel1" type="button" >Next</button>					
				</div>
			</div>
		</div>
		
		<div class="job_creation_panel2 col-md-12" style="display:none">
		
			<div class="hide form-group">
				<input class="form-control" type="text" placeholder="Enter the address postcode..." value="" />
			</div>
			
			<div class="form-group top_search hide">
				<div class="input-group">
					<input type="text" id="postcode_search" class="form-control address-lookup <?php echo $module_identier; ?>-search_input"  placeholder="Enter the address postcode..." >
					<span class="input-group-btn"><button id="find_address" class="btn btn-default <?php echo $module_identier; ?>-bg" type="button" >Find address</button></span>
				</div>
			</div>
			
			<div class="form-group address-selection" style="display:none" >
				<legend class="legend-header">Please select the Job address</legend>
				<select id="address_lookup_result" name="address_id" class="form-control" style="width:100%; margin-bottom:20px;" ></select>
				<br/>
			</div>

			<div class="input-group form-group">
				<label class="input-group-addon">Required Works</label>
				<textarea name="works_required" rows="3" class="form-control" type="text" value="" placeholder="Please confirm the works required for this Job" ></textarea>
			</div>
			<div class="input-group form-group">
				<label class="input-group-addon">Access Requirements</label>
				<textarea name="access_requirements" rows="3" class="form-control" type="text" value="" placeholder="Access requirements" ></textarea>
			</div>
			<div class="input-group form-group">
				<label class="input-group-addon">Parking Requirements</label>
				<textarea name="parking_requirements" rows="3" class="form-control" type="text" value="" placeholder="Parking requirements" ></textarea>
			</div>
			<br/>
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="job_creation_panel2" type="button" >Back</button>					
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button id="create-job-and-redirect" class="btn btn-block btn-flow btn-success btn-next" data-currentpanel="job_creation_panel2" type="button" >Next</button>					
				</div>
			</div>
		</div>
		
		<div class="job_creation_panel3 col-md-12 col-sm-12 col-xs-12" style="display:none" >
		
			<label>Would you like to assign this Job now?</label>
			<select id="assign_now" class="form-control" >
				<option value="" >Please select</option>
				<option value="Yes" >Yes</option>
				<option value="No" selected="selected" >No</option>
			</select>
			
			<div class="assign-operative" style="display:none" >
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="form-group">
							<label>The following Operatives have the skills required to complete this Job.</label>
							<div id="available-operatives" >
								<select id="assigned_to" name="assigned_to" class="form-control required" style="width:100%" >
									
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<br/>
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="job_creation_panel3" type="button" >Back</button>					
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn btn-block btn-flow btn-success btn-next job-creation-steps" data-currentpanel="job_creation_panel3" type="button" >Next</button>					
				</div>
			</div>
		</div>
		
		<div class="job_creation_panel4 col-xs-12" style="display:none" >
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="form-group">
						<div class="text-center">
							<br/>
							<p>You are about to submit a request to create a new Job.</p>
							<p>Click the "Create Job" to proceed or Back to review your Job details.</p>
							<br/>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="job_creation_panel4" type="button" >Back</button>					
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn btn-block btn-flow btn-success btn-next" type="button" >Create Job</button>
				</div>
			</div>
		</div>
	</div>
</form>

<script>

	$( document ).ready( function(){
		
		$( '#assigned_to, #address_lookup_result' ).select2();
		
		$( '#no_specific_date' ).change( function(){
			if( $( this ).is( ':Checked' ) ){
				$( '[name="job_date"]').removeClass( 'required' );
				$( '#job_date' ).val( '' );
			} else {
				$( '[name="job_date"]').addClass( 'required' );
			}
		});
		
		// ADDRESS LOOKUP
		$( '.postcode-lookup' ).change( function(){
			var postCode = $(this).val();
			if( postCode.length > 0 ){
				$.post( "<?php echo base_url( "webapp/job/get_addresses_by_postcode" ); ?>",{postcodes:postCode},function(result){
					$( "#address-lookup-result" ).html( result["addresses_list"] );				
				},"json" );
			}
		});
		
		$( '.check-required-skills' ).click( function(){
			return true;
			var currentpanel = $( this ).data( "currentpanel" );
			
			var jobType = $( '[name="job_type_id"] option:selected' ).val(),
				jobDate = $( '#job_date' ).val();

			if( jobType.length == 0 ){
				swal({
					type: 'error',
					text: 'Please select a Job Type'
				});
				return false;
			}
			
			/*if( ( jobDate.length == 0 ) && ( !$( '#no_specific_date' ).is( ':checked' ) ) ){
				swal({
					type: 'error',
					text: 'Please enter a valid Job Date'
				});
				return false;
			}
			
			if( $( '#no_specific_date' ).is( ':checked' ) ){
				$( '#job_date' ).val( '' );
			}*/
			
			$.ajax({
				url:"<?php echo base_url( 'webapp/job/check_required_skills/' ); ?>",
				method:"POST",
				data:{ page:'details', job_type_id:jobType, where:{job_date:jobDate, people_skills:1} },
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 && ( data.skills_data !== '' ) ){

						$( '#assigned_to' ).html( data.skills_data );
						panelchange( "."+currentpanel )	
						return false;

					}else{
						
						swal({
							type: 'warning',
							title: 'Please Note',
							text: data.status_msg,
							showCancelButton: true,
							confirmButtonColor: '#5CB85C',
							cancelButtonColor: '#9D1919',
							confirmButtonText: 'That\'s Okay'
						}).then( function (result) {
							if ( result.value ) {
								panelchange( "."+currentpanel )	
								return false;
							}
						}).catch( swal.noop )
						
						// swal({
							// type: 'warning',
							// text: data.status_msg
						// }).then( function (result){
							// panelchange( "."+currentpanel )	
							// return false;
						// });
					}		
				}
			});
			
			return false;
		});
		
		$(".job-creation-steps").click(function(){
			
			var jobType = $( '[name="job_type_id"] option:selected' ).val(),
				jobDate = $( '#job_date' ).val();

			if( jobType.length == 0 ){
				swal({
					type: 'error',
					text: 'Please select a Job Type'
				});
				return false;
			}
			
			var currentpanel = $(this).data( "currentpanel" );
			panelchange( "."+currentpanel )	
			return false;
		});	
		
		$(".btn-back").click(function(){
			var currentpanel = $(this).data("currentpanel");
			go_back("."+currentpanel)	
			return false;
		});	
		
		function panelchange(changefrom){
			var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
			var changeto = ".job_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'left'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'right'},500);	
			return false;	
		}
		
		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
			var changeto = ".job_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'right'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'left'},500);	
			return false;	
		}
		
		// ASSIGN ENGINEER / OPERATIVE
		$( '#assign_now' ).change( function(){
			var showOps = $( 'option:selected', this ).val();
			if( showOps.toLowerCase() == 'yes' ){
				$( '.assign-operative' ).slideDown( 'fast' );
			} else {
				$( '.assign-operative' ).slideUp( 'fast' );
			}
		} );
		
		// SUBMIT JOB FORM
		$( '#create-job-btn' ).click(function( e ){
			e.preventDefault();
			var formData = $('#job-creation-form').serialize();
			console.log(formData);
			return false;
			swal({
				title: 'Confirm new job creation?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url( 'webapp/job/create_job/' ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 && ( data.job !== '' ) ){
								
								var newSiteId = data.job.job.job_id;
								
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){ 
									location.href = "<?php echo base_url('webapp/job/profile/'); ?>"+newSiteId;
								} ,3000);							
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}		
						}
					});
				}
			}).catch( swal.noop )
		});
		
		// CREATE TEMP JOB FORM
		$( '#create-job-and-redirect' ).click(function( e ){
			e.preventDefault();
			var formData = $('#job-creation-form').serialize();
			$.ajax({
				url:"<?php echo base_url( 'webapp/job/create_job/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 && ( data.job !== '' ) ){
						var newJobId = data.job.job.job_id;
						location.href = "<?php echo base_url( 'webapp/diary/routing?job=' ); ?>"+newJobId;				
					}else{
						swal({
							type: 'error',
							title: data.status_msg
						})
					}		
				}
			});

		});
		
		//Trigger address search on btn click
		$( '#find_address' ).click(function(){
			var postCode = encodeURIComponent( $( '#postcode_search' ).val() );
			if( postCode.length > 0 ){
				$.post("<?php echo base_url("webapp/people/get_addresses_by_postcode"); ?>",{postcodes:postCode},function(result){
					$("#address_lookup_result").html( result["addresses_list"] );
					$( '.address-selection' ).slideDown( 'slow' );					
				},"json");
			}
		});

		//Trigger address search on pressing-enter key
		$( '#postcode_search' ).keypress( function( e ){
			if( e.which == 13 ){
				$( '#find_address' ).click();
			}
		});
	});
</script>