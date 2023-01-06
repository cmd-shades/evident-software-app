<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row alert alert-ssid bg-blue" role="alert">
					<div class="profile-overview">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<legend>Region Profile <span class="pull-right"><span class="edit-region pointer hide" title="Click to edit thie Job Type profile"><i class="fas fa-pencil-alt"></i></span> &nbsp; <span class="---delete-region pointer" title="Click to delete this Job Type profile" ><i class="far fa-trash-alt"></i></span></span></legend>
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Status</label></td>
											<td width="85%"><?php echo ( $region_details->is_active == 1 ) ? 'Active <i class="far fa-check-circle"></i>' : 'Disabled <i class="far fa-times-circle text-red"></i>'; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Date Created</label></td>
											<td width="85%"><?php echo ( valid_date( $region_details->date_created ) ) ? date( 'd-m-Y H:i:s', strtotime( $region_details->date_created ) ) : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Created By</label></td>
											<td width="85%"><?php echo ( !empty( $region_details->record_created_by ) ) ? ucwords( $region_details->record_created_by ) : 'Data not available'; ?></td>
										</tr>
									</table>							
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">

									</table>							
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<div class="x_panel tile has-shadow">
								<form id="update-region-profile-form" class="form-horizontal">
									<input type="hidden" name="page" value="details" />
									<input type="hidden" name="region_id" value="<?php echo $region_details->region_id; ?>" />
									<legend>Region Details</legend>
									<div class="input-group form-group">
										<label class="input-group-addon">Region Reference</label>
										<input id="region_ref" class="form-control" type="text" placeholder="Region Reference" readonly value="<?php echo $region_details->region_ref; ?>" />
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Region</label>
										<input id="region_name" name="region_name" class="form-control" type="text" placeholder="Region" value="<?php echo $region_details->region_name; ?>" />
									</div>									

									<div class="input-group form-group">
										<label class="input-group-addon">Region Description</label>
										<textarea id="region_description" name="region_description" type="text" class="form-control" rows="3"><?php echo ( !empty( $region_details->region_description ) ) ? $region_details->region_description : '' ?></textarea>     
									</div>
									
									<div class="input-group form-group">
										<label class="input-group-addon">Email Notification Req?</label>
										<select id="notification_required" name="notification_required" class="form-control" data-label_text="Email Notification Required" >
											<option value="">Please select</option>
											<option value="1" <?php echo ( $region_details->notification_required == 1 ) ? 'selected=selected' : ''; ?> >Yes</option>
											<option value="0" <?php echo ( $region_details->notification_required != 1 ) ? 'selected=selected' : ''; ?> >No</option>
										</select>
									</div>
									
									<div class="notification_required_container" style="display:none">
										<div class="input-group form-group">
											<label class="input-group-addon">Emails List</label>
											<textarea name="notification_emails" class="form-control" type="text" style="height:88px;" data-label_text="Notification Emails" placeholder="List of emails addresses e.g. support@yourcompany.com,  customercare@yourcompany.com" value=""><?php echo !empty( $region_details->notification_emails ) ? $region_details->notification_emails : ''; ?></textarea>
										</div>
									</div>
									
									<br/>
									<div class="input-group form-group">
										<button type="button" class="update-region-btn btn btn-sm btn-success">Save Changes</button>
									</div>
								</form>
							</div>
						</div>

						<div class="col-md-6 col-sm-6 col-xs-12">
							<div class="x_panel tile has-shadow">
								<legend>Associated Region Postcodes (<?php echo !empty( $region_details->region_areas ) ? count( $region_details->region_areas, 1 ) : 0; ?>) <span class="pull-right pointer add-postcode-districts"><i class="fas fa-plus text-green" title="Add Postcode district to this Region" ></i></span></legend>
								<?php if( !empty( $region_details->region_areas ) ){ ?>
									<div class="row">
										<?php foreach( $region_details->region_areas as $pc_region ){ ?>
											<div class="col-md-3 col-sm-3 col-xs-12">
												<ul class="to_do strong">
													<li><p><?php echo $pc_region->postcode_district; ?> <?php //echo $pc_region->posttown; ?> <span class="pull-right"><span class="remove-region-postcodes pointer" data-region_id="<?php echo $pc_region->region_id; ?>" data-postcode_district="<?php echo $pc_region->postcode_district; ?>" title="Click to remove this Postcode from this Region" ><i class="far fa-trash-alt text-red"></i></span></span></p></li>
												</ul>
											</div>
										<?php } ?>								
									</div>
								<?php } ?>								
							</div>
						</div>
						
						<!-- Modal for adding Postcode Districts -->
						<div class="modal fade add-postcode-districts-modal" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog modal-md">
								<form id="add-region-postcodes-form" onsubmit="return false" >
									<div class="modal-content">
										<div class="modal-header"><a type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></a>
											<h4 class="modal-title" id="myPostCodeDistrictModalLabel">Add Postcode Areas</h4>						
										</div>
										<div class="modal-body" id="risk-items-modal-container" >
											<input type="hidden" name="page" value="details" />
											<input type="hidden" name="region_id" value="<?php echo $region_details->region_id; ?>" />
											<input type="hidden" name="region_name" value="<?php echo $region_details->region_name; ?>" />
											<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
											
											<label class="strong">Search Postcode Coverage &nbsp;&nbsp;&nbsp;<small><i title="To search multiples, enter comma separated postcode areas e.g. CR, SM, BR etc..."></i></small></label>
											<div class="form-group">
												<div class="input-group">
													<input type="text" id="postcodes_areas" class="form-control <?php echo $module_identier; ?>-search_input"  placeholder="Enter comma separated postcode areas e.g. CR, SM, BR etc..." >
													<span class="input-group-btn"><button id="search_postcodes_areas" class="btn btn-default <?php echo $module_identier; ?>-bg" type="button" >Search</button></span>
												</div>
											</div>
											<div id="loading-indicator" style="display:none;" class="text-blue" >Fetching data, please wait... <div style="display:inline-block"><img src="<?php echo base_url( '/assets/images/ajax-loader.gif' ); ?>" /></div></div>
											<div class="form-group">
												<?php /* <div class="form-group">
													<label class="strong">Search Postcode Coverage</label>
													<input id="search-postcode-areas" class="form-control" placeholder="Search postcode regions e.g. CR or Croydon" />
												</div> */ ?>
												
												<div class="postcode-coverage" style="display:none">
													<label class="strong">Please check all postcodes that apply <small><i class="fas fa-info-circle pointer" title="A Postcode-area can only be part of 1 region within your Business. If the selected Postcode-area already exists, it will be omitted from your request."></i></small></label>
													<div class="row" id="region_postcodes" ></div> 
												</div>
											</div>
										</div>
										
										<div class="postcode-coverage" style="display:none">
											<div class="modal-footer">
												<button id="add-region-postcodes-btn" class="left btn btn-success btn-sm" type="button" >Add Selected Postcode Areas</button>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
						
						<div class="col-md-6 col-sm-6 col-xs-12 hide">
							<div class="x_panel tile has-shadow">
								<legend>Associated Job Types (<?php echo !empty( $associated_job_types ) ? count( $associated_job_types, 1 ) : 0; ?>)</legend>
								<?php if( !empty( $associated_job_types ) ){ ?>
									<div class="row">
										<?php foreach( $associated_job_types as $job_type ){ ?>
											<div class="col-md-4 col-sm-4 col-xs-12">
												<ul class="to_do">
													<li>H1 <p><?php echo $region_details->region_text; ?> <span class="pull-right"><span class="remove-region pointer" data-job_type_id="<?php echo $job_type->job_type_id; ?>" data-region_id="<?php echo $region_details->region_id; ?>" title="Click to remove this Region from this Job type" ><i class="far fa-trash-alt text-red"></i></span></span></p></li>
												</ul>
											</div>
										<?php } ?>								
									</div>
								<?php } ?>								
							</div>
						</div>

						<div class="col-md-6 col-sm-6 col-xs-12">
							<div class="x_panel tile has-shadow">
								<legend>Operatives who work in this Region (<?php echo !empty( $assigned_operatives ) ? count( $assigned_operatives, 1 ) : 0; ?>)</legend>
								<?php if( !empty( $assigned_operatives ) ){ ?>
									<div class="row">
										<?php foreach( $assigned_operatives as $operative ){ ?>
											<div class="col-md-6 col-sm-6 col-xs-12">
												<ul class="to_do">
													<div class="x_panel tile has-shadow">
														<div><strong><a href="<?php echo base_url('webapp/people/profile/'.$operative->person_id.'/workpattern' ); ?>"><?php echo $operative->full_name; ?></a> <span class="pull-right"><span class="unassign-person pointer" data-person_id="<?php echo $operative->person_id; ?>" data-region_id="<?php echo $region_details->region_id; ?>" title="Click to un-assign this Person from this Region" ><i class="far fa-trash-alt text-red"></i></span></span></strong></div>
														<div><small><?php echo ( !empty( $operative->personal_skills ) ) ? $operative->personal_skills : '<span class="text-red">Skills data not set...</span>'; ?></small></div>
													</div>
												</ul>
											</div>
										<?php } ?>								
									</div>
								<?php } ?>								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){

		$( '.remove-region-postcodes' ).click( function(){
			var regionID 		 = $( this ).data( 'region_id' ),
				postcodeDistrict = $( this ).data( 'postcode_district' );

			if( postcodeDistrict == 0 || postcodeDistrict == undefined ){
				swal({
					title: 'Oops! Something went wrong',
					type: 'error',
					text: 'Please reload the page and try again!',
				});
				
				return false;
			}

			swal({
				title: 'Confirm remove postcode?',
				type: 'warning',
				text: 'This is an irreversible action',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {

				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/diary/remove_region_postcodes/' ); ?>" + postcodeDistrict,
						method:"POST",
						data:{ page:"details", region_id:regionID, postcode_district:postcodeDistrict },
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2000
								})
								window.setTimeout(function(){
									location.reload();
								} ,1000);
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
	
		$( '.update-region-btn' ).click( function( event ){

			var formID = $( this ).closest( 'form' ).attr( 'id' );
			event.preventDefault();
			var formData = $('#'+formID ).serialize();
			swal({
				title: 'Confirm Region Profile update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/diary/update_region/'.$region_details->region_id ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2000
								})
								window.setTimeout(function(){ 
									location.reload();
								} ,1000);							
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}		
						}
					});
				}
			}).catch(swal.noop)
		});
		
		
		//Un-assign person from a region
		$( '.unassign-person' ).click( function(){
			
			var regionId  	= $( this ).data( 'region_id' );
			var personId  	= $( this ).data( 'person_id' );
			var	sectionName	= 'not-set';
			if( personId == 0 || personId == undefined ){
				swal({
					title: 'Oops! Something went wrong',
					type: 'error',
					text: 'Please reload the page and try again!',
				})
			}
			swal({
				title: 'Confirm un-assign Operative?',
				type: 'warning',
				text: 'This is an irreversible action',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
			
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/diary/unassign_person/' ); ?>" + personId,
						method:"POST",
						data:{ page:"details", region_id:regionId, person_id:personId },
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2100
								})
								window.setTimeout(function(){
									var new_url = window.location.href.split('?')[0];
									window.location.href = new_url + "?toggled=" + sectionName;
								} ,1000);
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
		} );
		
		
		$( '.add-postcode-districts' ).click( function(){
			$(".add-postcode-districts-modal").modal( "show" );
		} );
		
		
		$( '#region_postcodes' ).on( 'change', '#check-all-postcodes', function(){
			if( $( this ).is(':checked') ){
				$( '.postcode-checks' ).each(function(){
					$( this ).prop( 'checked', true );
				});
			} else {
				$( '.postcode-checks' ).each(function(){
					$( this ).prop( 'checked', false );
				});
			}
		});

		$( '#region_postcodes' ).on( 'change', '.postcode-checks', function(){
			
			var chkCount  = 0;
			var totalChekd= 0;
			var unChekd   = 0;
			$( '.postcode-checks' ).each(function(){
				chkCount++;
				if( $( this ).is(':checked') ){
					totalChekd++;					
				}else{
					unChekd++;
				}
			});
			
			if( chkCount > 0 && ( chkCount == totalChekd ) ){
				$( '#check-all-postcodes' ).prop( 'checked', true );
			}else{
				$( '#check-all-postcodes' ).prop( 'checked', false );
			}

		});
		
		$( "#postcodes_areas" ).on( 'keyup', function (e) {
			if ( e.keyCode === 13 ) {
				$(".add-postcode-districts-modal").modal( "show" );
				var searchTerm 	= encodeURIComponent( $( '#postcodes_areas' ).val() );
				if( searchTerm.length > 1 ){
					$( '#loading-indicator' ).show();
					$.post( "<?php echo base_url( 'webapp/diary/search_postcode_areas' ); ?>",{ search_term:searchTerm},function(result){
						$( '#loading-indicator' ).hide();
						$( '#region_postcodes' ).html( result['postcode_areas'] );
						$( '.postcode-coverage' ).slideDown( 'slow' );
					},
					"json" );
				} else {
					swal({
						type: 'error',
						text: 'Please provide at least 1 valid postcode area!'
					});
				}
			}
		});
		
		// Search for postcode areas on button click
		$( '#search_postcodes_areas' ).click( function(){
			var searchTerm 	= encodeURIComponent( $( '#postcodes_areas' ).val() );
			if( searchTerm.length > 1 ){
				$( '#loading-indicator' ).show();
				$.post( "<?php echo base_url( "webapp/diary/search_postcode_areas" ); ?>",{ search_term:searchTerm},function(result){
					$( '#loading-indicator' ).hide();
					$( '#region_postcodes' ).html( result['postcode_areas'] );
					$( '.postcode-coverage' ).slideDown( 'slow' );
				},
				"json" );
			} else {
				swal({
					type: 'error',
					text: 'Please provide at least 1 valid postcode area!'
				});
			}
		});
		
		
		//Submit region form
		$( '#add-region-postcodes-btn' ).click(function( e ){
			
			if( $( 'div#region_postcodes input:checked').length == 0 ){
				swal({
					type: 'error',
					text: 'Please tick at least 1 postcode area!'
				});
				return false;
			}
			
			e.preventDefault();
			var formData = $('#add-region-postcodes-form').serialize();
			
			$.ajax({
				url:"<?php echo base_url('webapp/diary/add_region_postcodes/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 && ( data.regions.length > 0 ) ){
						$('.add-postcode-districts-modal').modal( 'hide' );
						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 2000
						})
						window.setTimeout(function(){ 
							location.reload();
						} ,1200);							
					}else{
						swal({
							type: 'error',
							title: data.status_msg
						})
					}		
				}
			});
		});
		
		
		var emailReq = $( "#notification_required option:selected" ).val();
		if( emailReq == 1 ){
			$( '.notification_required_container' ).show();
		} else {
			$( '.notification_required_container' ).hide();
		}
		
		//Email Notification Required
		$( '#notification_required' ).on('change', function() {
			var emailReq = $( "#notification_required option:selected" ).val();;
			if( emailReq == 1 ){
				$( '.notification_required_container' ).slideDown();
			} else {
				$( '.notification_required_container' ).slideUp( 'slow' );
			}
		});
		
	});
</script>