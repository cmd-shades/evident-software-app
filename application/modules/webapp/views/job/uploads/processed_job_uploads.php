<style>
	.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
		padding: 8px;
		line-height: 1.42857143;
		vertical-align: top;
		border: none;
		border-bottom: 1px solid #ddd;
	}
	.x_panel {
		margin-top: 10px;
	}
	
	.table .table {
		background:transparent;
	}
	
	label.normalized{
		line-height: inherit;
		color: inherit;
		font-size: inherit;
		font-weight: inherit;
		overflow: hidden;
		/* white-space: nowrap; */
		text-overflow: ellipsis;
		/* position: relative; */
		z-index: 1;
		margin-right: 0;
		margin-bottom: 0px;
		cursor: pointer;
	}
	
</style>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Review Processed Records Upload</legend>
			<?php if( !empty( $successful_records ) ) { ?>
				<div class="row">
					<div class="col-md-12">
						<h4 class="text-green"><a style="font-size:22px;" class="text-bold text-green" target="_blank" href="<?php echo base_url( 'webapp/job/jobs/' ) ?>" ><?php echo count( $successful_records ); ?> Records were processed Successfully</a></h4>
					</div>
				</div>
			<?php } ?>
			
			<?php if( !empty( $processed_data ) && count( object_to_array( $processed_data ) ) ) { ?>
				<div class="row">
					<div class="col-md-12">
						<h4 class="text-red">The Records below were processed with Errors. Please review and resubmit or delete them.</h4>
						<hr>
						<div style="overflow-y: hidden;" >
							<?php foreach( $processed_data as $group => $records ){ ?>
								<form id="frm-<?php echo $group; ?>" method="POST" >
									<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
									<input type="hidden" name="page" value="details"/>
									<table width="100%" >								
										<tr>
											<td width="80%" style="color:<?php echo ( $group == 'jobs_created_successfully' ) ? 'green' : 'red'; ?>" title="These records <?php echo ( $group == 'jobs_created_successfully' ) ? 'are ready for processing into Job records' : ( ( ( $group == 'existing-records' )) ? 'already exist on the system' : 'are missing the Emails' ); ?>" ><strong><span class="pointer grp" data-grp_id="<?php echo $group;?>"><?php echo strtoupper( str_replace( '_', ' ', $group ) ); ?> ( <?php echo ( !empty( $records ) ) ? count( $records ) : '0'; ?> )</span></strong></td>
											<td width="20%" >
												<span class="pull-right">
													<button style="display:none" class="submit-records btn btn-sm btn-block btn-success btn-<?php echo ( $group == 'jobs_created_successfully' ) ? 'success' : 'danger'; ?> grp_<?php echo $group;?>" data-action_type="add" data-form_id="<?php echo $group; ?>" >
														Re-Submit Selected Records
													</button>
												</span>
											</td>
										</tr>
										
										<tr class="grp_<?php echo $group;?>" style="display:none">
											<td colspan="2" width="100%" >
												<table class="table table-responsive" style="width:100%" >
													<tr>
														<td class="text-bold" width="10%">Client Ref</td>
														<td class="text-bold" width="20%">Contract</td>
														<td class="text-bold" width="20%">Surname</td>
														<td class="text-bold" width="20%">Address</td>
														<td class="text-bold" width="20%">Job Type</td>
														<td class="text-bold" width="10%">
															<span class="pull-right">
																<label class="normalized" >Tick All &nbsp;<input class="chk-all chk<?php echo $group;?>" id="check-all-<?php echo $group;?>" data-chk_id="<?php echo $group;?>" type="checkbox" value=""></label>
															</span>
														</td>
													</tr>
													
													<tbody>
														<?php foreach( object_to_array( $records ) as $key => $record ){ ?>
															<tr data-temp_job_id="<?php echo $record['temp_job_id']; ?>" >
																<input type="hidden" name="jobs_data[<?php echo $record['temp_job_id']; ?>][account_id]" value="<?php echo $record['account_id'];?>" />
																<input type="hidden" name="jobs_data[<?php echo $record['temp_job_id']; ?>][temp_job_id]" value="<?php echo $record['temp_job_id'];?>" />
																<!-- <input type="hidden" name="jobs_data[<?php echo $record['temp_job_id']; ?>][job_notes]" value="<?php echo $record['job_notes'];?>" /> -->
																<input type="hidden" name="jobs_data[<?php echo $record['temp_job_id']; ?>][region_id]" value="<?php echo !empty( $record['region_id'] ) ? $record['region_id'] : '';?>" />
																<!-- <input type="hidden" name="jobs_data[<?php echo $record['temp_job_id']; ?>][access_requirements]" value="<?php echo $record['access_requirements'];?>" /> -->
																<!-- <input type="hidden" name="jobs_data[<?php echo $record['temp_job_id']; ?>][salutation]" value="<?php echo $record['salutation'];?>" /> -->
																<!-- <input type="hidden" name="jobs_data[<?php echo $record['temp_job_id']; ?>][customer_email]" value="<?php echo $record['customer_email'];?>" /> -->
																<!-- <input type="hidden" name="jobs_data[<?php echo $record['temp_job_id']; ?>][customer_main_telephone]" value="<?php echo $record['customer_main_telephone'];?>" /> -->
																
																<td width="10%" style="border-bottom: 1px solid #fff;"><input class="form-control changeable-field" type="text" name="jobs_data[<?php echo $record['temp_job_id']; ?>][client_reference]" value="<?php echo $record['client_reference'];?>" ></td>
																<td width="20%" style="border-bottom: 1px solid #fff;"><input class="form-control changeable-field" type="text" name="jobs_data[<?php echo $record['temp_job_id']; ?>][contract_name]" value="<?php echo $record['contract_name'];?>" ></td>
																<td width="20%" style="border-bottom: 1px solid #fff;"><input class="form-control changeable-field" type="text" name="jobs_data[<?php echo $record['temp_job_id']; ?>][customer_last_name]" value="<?php echo $record['customer_last_name'];?>" ></td>
																<td width="20%" style="border-bottom: 1px solid #fff;">
																	<select class="form-control changeable-field invalid_addresses" name="jobs_data[<?php echo $record['temp_job_id']; ?>][address_id]" style="border: 1px solid <?php echo ( $group == 'invalid_addresses' ) ? 'red' : ''; ?>;"  >
																		<option value="" >Please Select Correct Address</option>
																		<?php if( !empty( $record['postcode_addresses'] ) ) { foreach( json_decode( $record['postcode_addresses'] ) as $key => $main_address ) { ?>
																			<option value="<?php echo $main_address->main_address_id; ?>" <?php echo ( !empty( $record['suggested_address']->main_address_id ) && ( $main_address->main_address_id == $record['suggested_address']->main_address_id ) ) ? 'selected=selected' : ''; ?> ><?php echo $main_address->addressline1; ?><?php echo !empty( $main_address->addressline2 ) ? ', '.$main_address->addressline2 : ''; ?><?php echo $main_address->postcode; ?></option>
																		<?php } } ?>
																	</select>
																</td>
																<td width="25%" style="border-bottom: 1px solid #fff;">
																	<select class="form-control changeable-field invalid_job_types" name="jobs_data[<?php echo $record['temp_job_id']; ?>][job_type_id]" style="border: 1px solid <?php echo ( $group == 'invalid_job_types' ) ? 'red' : ''; ?>;" >
																		<option value="" >Select Job Type</option>
																		<?php if( !empty( $record['job_types'] ) ) { foreach( json_decode( $record['job_types'] ) as $jt => $job_type ) { ?>
																			<option value="<?php echo $job_type->job_type_id; ?>" <?php echo ( strtolower( $job_type->job_type ) == strtolower( $record['job_type'] ) ) ? 'selected=selected' : ''; ?> ><?php echo $job_type->job_type; ?></option>
																		<?php } } ?>
																	</select>
																</td>													
																<td width="5%" style="border-bottom: 1px solid #fff;">
																	<span class="pull-right">
																		<input type="hidden" name="jobs_data[<?php echo $record['temp_job_id']; ?>][checked]" value="0" />
																		<label class="normalized" ><input type="checkbox" name="jobs_data[<?php echo $record['temp_job_id']; ?>][checked]" value="1" class="chk<?php echo $group;?>" ></label>
																	</span>
																</td>														
															</tr>
														<?php } ?>
													</tbody>
													
												</table>
											</td>
										</tr>

										<tr>
											<td colspan="2" width="100%" >
												<span class="pull-right">
													<button style="display:none" class="submit-records btn btn-sm btn-block btn-danger grp_<?php echo $group;?>" data-action_type="remove" data-form_id="<?php echo $group; ?>" >
														&nbsp;&nbsp;&nbsp;Delete Selected Records&nbsp;&nbsp;&nbsp;
													</button>
												</span>
											</td>
										</tr>
									</table>
								</form>
							<?php } ?>
						</div>
					</div>
					<div class="col-md-12">
						<hr>
						<div class="row">
							<div class="col-md-3">
								<a href="<?php echo base_url( 'webapp/job/upload_jobs'); ?>" class="btn btn-sm btn-danger" type="submit" >Go back and re-upload file</a>					
							</div>
							<div class="hide col-md-offset-6 col-md-3">
								<a href="<?php echo base_url( 'webapp/job/process_upload_jobs'); ?>" class="btn btn-sm  btn-success pull-right" type="submit" >Submit Selected Records</a>					
							</div>
						</div>
					</div>
				</div>
				
			<?php } else { ?>
				<div class="row">
					<div class="col-md-12">
						<span><?php #echo $this->config->item( 'no_records' );  ?></span>
						<br/>
					</div>
					<div class="col-md-3">
						<a href="<?php echo base_url( 'webapp/job/upload_jobs'); ?>" class="btn btn-sm btn-block btn-info" type="submit" >Start New File Upload</a>					
					</div>
				</div>
			<?php } ?>
		</div>
	</div>	
</div>

<script>
	$(document).ready(function(){
		
		$( '.grp' ).click( function(){
			var grpId = $( this ).data( 'grp_id' );
			$( '.grp_'+grpId ).slideToggle();
		});
		
		//Check all selected inputs
		$( '.invalid_addresses' ).change( function(){
			var addresId = $( this ).val();
			if( addresId.length > 0 ){
				$( this ).parent.css( "border","1px solid #ccc" );
			}
		});
		
		$( '.invalid_job_types' ).change( function(){
			var jobTypeId = $( this ).val();
			if( jobTypeId.length > 0 ){
				$( this ).parent.css( "border","1px solid #ccc" );
			}
		});
		
		//Check all selected inputs
		$( '.chk-all' ).click( function(){
			var chkId = $( this ).data( 'chk_id' );
			if( this.checked ) {
				$( '.chk'+chkId ).each(function() {
					this.checked = true;
				});
			}else{
				$( '.chk'+chkId ).each(function() {
					this.checked = false;
				});
			}
		});

		//Instant update
		$( 'select[class="changeable-field"], .changeable-field' ).change( function(){
			var tempUserId 	= $( this ).closest('tr').data( 'temp_job_id' );
			var formData 	= $( this ).serialize();
			$.ajax({
				url:"<?php echo base_url( 'webapp/job/update_temp_data/' ); ?>"+tempUserId,
				method: "POST",
				data: formData,
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 ){
						//Use this to catch any post submission events
					} else {
						//Use this to show any errors
					}
				}
			});
			
		});
		
		//Submit checked records
		$( '.submit-records' ).click( function( e ){
			e.preventDefault();
			var formId	   = $( this ).data( 'form_id' );
			
			var actionType = $( this ).data( 'action_type' );
			
			if( actionType == 'add' ){
				var postUrl = "<?php echo base_url('webapp/job/process_job_uploads/' ); ?>";
			} else if ( actionType == 'remove' ){
				var postUrl = "<?php echo base_url('webapp/job/drop_temp_records/' ); ?>";
			}

			var totalChkd = 0;
			$( '.chk'+formId ).each( function(){
				if( this.checked ) {
					totalChkd++;					
				}				
			} );
			
			//Tick atleat 1 checkbox
			if( totalChkd == 0 ){
				swal({
					type: 'error',
					title: '<small>Please select at-least 1 record to '+actionType+'</small>'
				})
				return false;
			}
			
			var formData = $( '#frm-'+formId ).serialize();
			
			swal({
				title: 'Confirm '+actionType+' job upload records?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					
					$( '#frm-'+formId ).attr( 'action', postUrl );;
					$( '#frm-'+formId ).submit();
					
					/*$.ajax({
						url:postUrl,
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 5000
								})
								window.setTimeout(function(){ 
									if( data.all_done == 1 ){
										//Redirect to jobs dashboard
										location.href = "<?php echo base_url('webapp/job/jobs/'); ?>";
									}else{
										location.reload();
									}									
								} ,3000);							
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}		
						}
					});*/
					
				}
			}).catch(swal.noop)
			
		} );
		
	});
</script>