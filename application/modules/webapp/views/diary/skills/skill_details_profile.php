<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row alert alert-ssid bg-blue" role="alert">
					<div class="profile-overview">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<legend>Skill Profile <span class="pull-right"><span class="edit-skill pointer hide" title="Click to edit this Job Type profile"><i class="fas fa-pencil-alt"></i></span> &nbsp; <span class="---delete-skill pointer" title="Click to delete this Job Type profile" ><i class="far fa-trash-alt"></i></span></span></legend>
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Status</label></td>
											<td width="85%"><?php echo ( $skill_details->is_active == 1 ) ? 'Active <i class="far fa-check-circle"></i>' : 'Disabled <i class="far fa-times-circle text-red"></i>'; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Date Created</label></td>
											<td width="85%"><?php echo ( valid_date( $skill_details->date_created ) ) ? date( 'd-m-Y H:i:s', strtotime( $skill_details->date_created ) ) : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Created By</label></td>
											<td width="85%"><?php echo ( !empty( $skill_details->record_created_by ) ) ? ucwords( $skill_details->record_created_by ) : 'Data not available'; ?></td>
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
								<form id="update-skill-profile-form" class="form-horizontal">
									<input type="hidden" name="page" value="details" />
									<input type="hidden" name="skill_id" value="<?php echo $skill_details->skill_id; ?>" />
									<legend>Skill Details</legend>
									<div class="input-group form-group">
										<label class="input-group-addon">Skill Reference</label>
										<input id="skill_ref" class="form-control" type="text" placeholder="Skill Reference" readonly value="<?php echo $skill_details->skill_ref; ?>" />
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Skill</label>
										<input id="skill_name" name="skill_name" class="form-control" type="text" placeholder="Skill" value="<?php echo $skill_details->skill_name; ?>" />
									</div>									
									
									<div class="input-group form-group">
										<label class="input-group-addon">Skill Level</label>
										<select name="skill_level" class="form-control">
											<option>Please select level</option>
											<option value="Junior" <?php echo ( strtolower( $skill_details->skill_level ) == 'junior' ) ? 'selected=selected' : ''; ?> >Junior</option>
											<option value="Intermediate" <?php echo ( strtolower( $skill_details->skill_level ) == 'intermediate' ) ? 'selected=selected' : ''; ?> >Intermediate</option>
											<option value="Senior" <?php echo ( strtolower( $skill_details->skill_level ) == 'senior' ) ? 'selected=selected' : ''; ?> >Senior</option>
										</select>	
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Skill Description</label>
										<textarea id="skill_description" name="skill_description" type="text" class="form-control" rows="3"><?php echo ( !empty( $skill_details->skill_description ) ) ? $skill_details->skill_description : '' ?></textarea>     
									</div>
									<div class="input-group form-group">
										<button type="button" class="update-skill-btn btn btn-sm btn-success">Save Changes</button>
									</div>
								</form>
							</div>
						</div>

						<div class="col-md-6 col-sm-6 col-xs-12">
							<div class="x_panel tile has-shadow">
								<legend>Associated Job Types (<?php echo !empty( $associated_job_types ) ? count( $associated_job_types, 1 ) : 0; ?>)</legend>
								<?php if( !empty( $associated_job_types ) ){ ?>
									<div class="row">
										<?php foreach( $associated_job_types as $job_type ){ ?>
											<div class="col-md-4 col-sm-4 col-xs-12">
												<ul class="to_do">
													<li>H1 <p><?php echo $skill_details->skill_text; ?> <span class="pull-right"><span class="remove-skill pointer" data-job_type_id="<?php echo $job_type->job_type_id; ?>" data-skill_id="<?php echo $job_type->skill_id; ?>" title="Click to remove this Skill from this Job type" ><i class="far fa-trash-alt text-red"></i></span></span></p></li>
												</ul>
											</div>
										<?php } ?>								
									</div>
								<?php } ?>								
							</div>
						</div>
						
						<div class="col-md-6 col-sm-6 col-xs-12">
							<div class="x_panel tile has-shadow">
								<legend>Operative Trained in this Skill (<?php echo !empty( $skilled_people ) ? count( $skilled_people, 1 ) : 0; ?>)</legend>
								<?php if( !empty( $skilled_people ) ){ ?>
									<div class="row">
										<?php foreach( $skilled_people as $operative ){ ?>
											<div class="col-md-6 col-sm-6 col-xs-12">
												<ul class="to_do">
													<div class="x_panel tile has-shadow">
														<div><strong><a href="<?php echo base_url('webapp/people/profile/'.$operative->person_id.'/workpattern' ); ?>"><?php echo ucwords( $operative->full_name ); ?></a> <span class="pull-right"><span class="remove-skilled-person pointer" data-person_id="<?php echo $operative->person_id; ?>" data-skill_id="<?php echo $skill_details->skill_id; ?>" title="Click to remove this Person from this Skill" ><i class="far fa-trash-alt text-red"></i></span></span></strong></div>														
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

		$( '.update-skill-btn' ).click( function( event ){

			var formID = $( this ).closest( 'form' ).attr( 'id' );
			event.preventDefault();
			var formData = $('#'+formID ).serialize();
			swal({
				title: 'Confirm Skill Profile update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/diary/update_skill/'.$skill_details->skill_id ); ?>",
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
		
		
		//Un-assign person from a skill
		$( '.remove-skilled-person' ).click( function(){
			
			var skillId  	= $( this ).data( 'skill_id' );
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
				title: 'Confirm remove Skill from Operative?',
				type: 'warning',
				text: 'This is an irreversible action',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
			
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/diary/remove_skilled_person/' ); ?>" + personId,
						method:"POST",
						data:{ page:"details", skill_id:skillId, person_id:personId },
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
	});
</script>