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
	
</style>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<!-- <form id="docs-upload-form" action="<?php echo base_url( 'webapp/people/uplaod_people/'.$this->user->account_id ); ?>" method="post" class="form-horizontal" enctype="multipart/form-data" > -->
			<div class="x_panel tile has-shadow">
				<legend>Review People Upload</legend>
				<?php if( !empty( $pending ) ) { ?>
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive" style="overflow-y: hidden;" >
							<table class="table table-responsive" style="margin-bottom:0px;width:100%" >
							
								<tbody>
									<?php foreach( $pending as $group => $records ){ ?>
										<form id="frm-<?php echo $group;?>" action="<?php echo base_url( 'webapp/people/add_people/'.$this->user->account_id ); ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
											<tr>
												<td colspan="6" style="color:<?php echo ( $group == 'new-records' ) ? 'green' : 'red'; ?>" title="These records <?php echo ( $group == 'new-records' ) ? 'are ready for processing into People records' : 'already exist on the system'; ?>" >
													<span class="pointer grp" data-grp_id="<?php echo $group;?>"><?php echo ucwords( str_replace( '-', ' ', $group ) ); ?> ( <?php echo ( !empty( $records ) ) ? count( $records ) : '0'; ?> )</span>
													<span class="pull-right">
														<button style="display:none" class="submit-btn btn btn-default btn-<?php echo ( $group == 'new-records' ) ? 'success' : 'danger'; ?> grp_<?php echo $group;?>" data-action_type="<?php echo ( $group == 'new-records' ) ? 'add' : 'remove'; ?>" data-form_id="<?php echo $group; ?>" >
															<?php echo ( $group == 'new-records' ) ? 'Submit' : 'Remove'; ?> Selected Records
														</button>
													</span>
												</td>
											</tr>
											<tr class="grp_<?php echo $group;?>" style="display:none">
												<td colspan="6" >
													<table class="table table-responsive" style="width:100%" >
														<thead>
															<tr>
																<th width="10%">First name</th>
																<th width="15%">Last name</th>
																<th width="10%">Preferred name</th>
																<th width="15%">Email</th>
																<th width="15%">User type</th>
																<th width="13%">Department</th>
																<th width="14%">Job title</th>															
																<th width="8%">
																	<div class="checkbox pull-right" >
																		<label ><strong>Tick all</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="chk-all chk<?php echo $group;?>" data-chk_id="<?php echo $group;?>" type="checkbox" value=""></label>
																	</div>
																</th>															
															</tr>
														</thead>
														<tbody>
															<?php foreach( $records as $key => $record ){ ?>
																<tr data-temp_user_id="<?php echo $record['temp_user_id']; ?>" >
																	<td width="10%" style="border-bottom: 1px solid #fff;"><input class="form-control changeable-field" type="text" name="people[<?php echo $record['temp_user_id']; ?>][first_name]" value="<?php echo $record['first_name'];?>" ></td>
																	<td width="15%" style="border-bottom: 1px solid #fff;"><input class="form-control changeable-field" type="text" name="people[<?php echo $record['temp_user_id']; ?>][last_name]" value="<?php echo $record['last_name'];?>" ></td>
																	<td width="10%" style="border-bottom: 1px solid #fff;"><input class="form-control changeable-field" type="text" name="people[<?php echo $record['temp_user_id']; ?>][preferred_name]" value="<?php echo $record['preferred_name'];?>" ></td>
																	<td width="15%" style="border-bottom: 1px solid #fff;"><input class="form-control changeable-field" type="text" name="people[<?php echo $record['temp_user_id']; ?>][personal_email]" value="<?php echo $record['personal_email'];?>" ></td>
																	<td width="15%" style="border-bottom: 1px solid #fff;">
																		<select class="form-control changeable-field" name="people[<?php echo $record['temp_user_id']; ?>][user_type_id]" >
																			<option>Please select</option>
																			<?php if( !empty( $user_types ) ) { foreach( $user_types as $k => $user_type ) { ?>
																				<option value="<?php echo $user_type->user_type_id; ?>" <?php echo ( $user_type->user_type_id == $record['user_type_id'] ) ? 'selected=selected' : ''; ?> ><?php echo $user_type->user_type_name; ?> <?php echo ( $user_type->user_type_id == 1 ) ? '(System)' : ''; ?></option>
																			<?php } } ?>
																		</select>
																	</td>
																	<td width="13%" style="border-bottom: 1px solid #fff;">
																		<select class="form-control changeable-field" name="people[<?php echo $record['temp_user_id']; ?>][department_id]" >
																			<option>Please select</option>
																			<?php if( !empty( $departments ) ) { foreach( $departments as $a => $dept ) { ?>
																				<option value="<?php echo $dept->department_id; ?>" <?php echo ( $dept->department_id == $record['department_id'] ) ? 'selected=selected' : ''; ?> ><?php echo $dept->department_name; ?></option>
																			<?php } } ?>
																		</select>
																	</td>
																	<td width="14%" style="border-bottom: 1px solid #fff;">
																		<select class="form-control changeable-field" name="people[<?php echo $record['temp_user_id']; ?>][job_title_id]" >
																			<option>Please select</option>
																			<?php if( !empty( $job_titles ) ) { foreach( $job_titles as $j => $job_title ) { ?>
																				<option value="<?php echo $job_title->job_title_id; ?>" <?php echo ( $job_title->job_title_id == $record['job_title_id'] ) ? 'selected=selected' : ''; ?> ><?php echo $job_title->job_title; ?></option>
																			<?php } } ?>
																		</select>
																	</td>														
																	<td width="8%" style="border-bottom: 1px solid #fff;">
																		<div class="checkbox pull-right" >
																			<input type="hidden" name="people[<?php echo $record['temp_user_id']; ?>][checked]" value="0" />
																			<label><input type="checkbox" name="people[<?php echo $record['temp_user_id']; ?>][checked]" value="1" class="chk<?php echo $group;?>" ></label>
																		</div>
																	</td>														
																</tr>
															<?php } ?>
														</tbody>
													</table>
												</td>
											</tr>
										</form>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<br/>
				<div class="row">
					<div class="col-md-3">
						<a href="<?php echo base_url( 'webapp/people/create'); ?>" class="btn btn-sm btn-block btn-danger" type="submit" >Go back and re-upload file</a>					
					</div>
					<div class="col-md-3 col-md-offset-6 pull-right" >
						<button id="doc-upload-btn" class="btn btn-sm btn-block btn-success" type="submit" >Create People Records</button>					
					</div>					
				</div>
				<?php } else { ?>
				<div class="row">
					
					<div class="col-md-12">
						<span><?php echo $this->config->item( 'no_records' );  ?></span>
						<br/>
						<br/>
					</div>
					<div class="col-md-3">
					
						<a href="<?php echo base_url( 'webapp/people/create'); ?>" class="btn btn-sm btn-block btn-info" type="submit" >Start Upload People</a>					
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
			var tempUserId 	= $( this ).closest('tr').data( 'temp_user_id' );
			var formData 	= $( this ).serialize();
			$.ajax({
				url:"<?php echo base_url( 'webapp/people/update_temp_data/' ); ?>"+tempUserId,
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
		
		//Submit asset form
		$( '#create-asset-btn' ).click(function( e ){
			e.preventDefault();
			
			var formData = $('#asset-creation-form').serialize();
			
			swal({
				title: 'Confirm new asset creation?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/people/create_person/' ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 && ( data.asset !== '' ) ){
								
								var newAssetId = data.asset.asset.asset_id;
								
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){ 
									location.href = "<?php echo base_url('webapp/people/profile/'); ?>"+newAssetId;
								} ,3000);							
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}		
						}
					});
				}else{
					$( ".asset_creation_panel4" ).hide( "slide", { direction : 'left' }, 500 );
					go_back( ".asset_creation_panel2" );
					return false;
				}
			}).catch(swal.noop)
		});
		
		//Submit checked records
		$( '.submit-btn' ).click( function( e ){
			e.preventDefault();
			var formId	   = $( this ).data( 'form_id' );
			var actionType = $( this ).data( 'action_type' );
			
			if( actionType == 'add' ){
				var postUrl = "<?php echo base_url('webapp/people/create_people/' ); ?>";
			} else if ( actionType == 'remove' ){
				var postUrl = "<?php echo base_url('webapp/people/drop_temp_records/' ); ?>";
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
			
			var formData = $('#frm-'+formId ).serialize();
			
			swal({
				title: 'Confirm '+actionType+' people records?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
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
										//Redirect to people dashboard
										location.href = "<?php echo base_url('webapp/people/people/'); ?>";
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
					});
				}
			}).catch(swal.noop)
			
		} );
		
	});
</script>