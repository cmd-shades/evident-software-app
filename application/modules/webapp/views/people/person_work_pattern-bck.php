<style>
	.wknd{
		color:#d32626 !important;		
	}
	.datepicker_input{
		margin: 0;
	}
	/*form input[type="text"]{
		font-size: 10px;
	}*/
	button.scheduleviewer{
		width: 19%;
	}
	
	.scheduletype, .viewby{
		border-radius: 6px;
		-webkit-border-radius: 6px;
		-moz-border-radius: 6px;
	}
	
	.scheduletype{
		width: 240px;
		margin:-6px 0 0 5px; 
	}
	
	.showbtn{margin-left:39.5%;
		font-weight:bold;
	}
	
	.working-day{
		margin-top:-5px;
		margin-left:39.5%;
	}
	
	.scheduleviewerbyweek, .scheduleviewer{
		margin-bottom: 4px;
	}
	
</style>

<?php $days = array('1'=>'Mon', '2'=>'Tue', '3'=>'Wed', '4'=>'Thu', '5'=>'Fri', '6'=>'Sat', '7'=>'Sun' );?>

<div class="row">
	<div class="col-md-6 col-sm-6 col-xs-12">
		<form id="update-person-form-left" class="form-horizontal">
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="user_id" value="<?php echo $person_details->user_id; ?>" />
			<input type="hidden" name="person_id" value="<?php echo $person_details->person_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<div class="x_panel tile has-shadow">
				<legend>Job Capability</legend>
				<div class="input-group form-group">
					<label class="input-group-addon">Can Accept Jobs</label>
					<select id="can_accept_jobs" name="can_accept_jobs" class="form-control">
						<option>Please select</option>
						<option value="1" <?php echo ( $person_details->can_accept_jobs == 1 ) ? 'selected=selected' : ''; ?> >Yes</option>
						<option value="0" <?php echo ( $person_details->can_accept_jobs != 1 ) ? 'selected=selected' : ''; ?> >No</option>
					</select>	
				</div>
				
				<div class="input-group form-group">
					<label class="input-group-addon">Can Do Jobs</label>
					<select id="can_do_jobs" name="can_do_jobs" class="form-control">
						<option>Please select</option>
						<option value="1" <?php echo ( $person_details->can_do_jobs == 1 ) ? 'selected=selected' : ''; ?> >Yes</option>
						<option value="0" <?php echo ( $person_details->can_do_jobs != 1 ) ? 'selected=selected' : ''; ?> >No</option>
					</select>	
				</div>

				<?php if( $this->user->is_admin || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
					<div class="row">
						<div class="col-md-6">
							<button id="update-person-btn-1" class="btn btn-sm btn-block btn-flow btn-success btn-next update-person-btn" type="button">Update Person Info</button>					
						</div>
					</div>
				<?php }else{ ?>
					<div class="row col-md-6">
						<span id="no-permissions-1" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</span>					
					</div>
				<?php } ?>
			
				<div class="row hide">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="row col-md-6">
							<span id="no-permissions-2" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="button" >Insufficient permissions</span>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	
	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Work Availability <span class="pull-right pointer add-availability"><i class="fas fa-plus text-green" title="Add Operatives' availability" ></i></span></legend>
			<div class="accordion" id="accordion-availability" role="tablist" aria-multiselectable="true">
				
			</div>
		</div>
	</div>
	
	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Skill-set <span class="pull-right pointer add-personal-skills"><i class="fas fa-plus text-green" title="Add new Skill to this Person" ></i></span></legend>
			<div class="accordion" id="accordion-skills" role="tablist" aria-multiselectable="true">
				<p>This person/operative is currently Skilled in the areas listed below.</p>
				<div class="row">
					<?php if( !empty( $personal_skills ) ){ foreach( $personal_skills as $skill ){ ?>	
						<div class="col-md-12 col-sm-12 col-xs-12">
							<ul class="to_do">
								<li>
									<div class="container">
										<div class="rows">
											<div class="col-md-12 col-sm-12 col-xs-12"><strong><?php echo $skill->skill_name; ?> <span><?php echo ( !empty( $skill->skill_level ) ) ? ' | Level '.$skill->skill_level : ''; ?></span> <span class="pull-right"><span class="remove-skill pointer" data-person_id="<?php echo $person_details->person_id; ?>" data-skill_id="<?php echo $skill->skill_id; ?>" title="Click to remove this Skill from this Person" ><i class="far fa-trash-alt text-red"></i></span></span></strong></div>
											<div class="col-md-12 col-sm-12 col-xs-12"><small><?php echo $skill->skill_description; ?></small></div>
										</div>
									</div>
								</li>
							</ul>
						</div>
					<?php } } ?>	
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Assigned Regions <span class="pull-right pointer assign-regions"><i class="fas fa-plus text-green" title="Add new Region in which this person can do Jobs" ></i></span></legend>
			<div class="accordion" id="accordion-regions" role="tablist" aria-multiselectable="true">
				<p>This person/operative is available to work in the following regions.</p>
				<div class="row">
					<?php if( !empty( $assigned_regions ) ){ foreach( $assigned_regions as $region ){ ?>	
						<div class="col-md-12 col-sm-12 col-xs-12">
							<ul class="to_do">
								<li>
									<div class="container">
										<div class="rows">
											<div class="col-md-12 col-sm-12 col-xs-12"><a href="<?php echo base_url('webapp/diary/manage_regions/'.$region->region_id ); ?>"><strong><?php echo $region->region_name; ?></a></strong> <span class="hide"><?php echo ( !empty( $region->region_description ) ) ? ' | '.$region->region_description : false; ?></span> <span class="pull-right"><span class="unassign-region pointer" data-person_id="<?php echo $person_details->person_id; ?>" data-region_id="<?php echo $region->region_id; ?>" title="Click to remove this Region from this Person\'s catchment" ><i class="far fa-trash-alt text-red"></i></span></span></div>
											<div class="col-md-12 col-sm-12 col-xs-12"><small><?php echo $region->region_postcodes; ?> </small></div>
										</div>
									</div>
								</li>
							</ul>
						</div>
					<?php } } ?>	
				</div>
			</div>
		</div>
	</div>
	
	<?php include_once( 'inc/personal_skills.php' ); ?>
	<?php include_once( 'inc/assigned_regions.php' ); ?>
	<?php include_once( 'inc/work_pattern_availability.php' ); ?>

</div>

<script>
	$( document ).ready(function(){
		
		$( '#personal_skills, #assigned_regions' ).select2({
			allowClear: true,
			minimumResultsForSearch: -1,
		});
		
		$( '.add-personal-skills' ).click( function(){
			$(".add-personal-skills-modal").modal( "show" );
		} );
		
		$( '.assign-regions' ).click( function(){
			$(".assign-regions-modal").modal( "show" );
		} );
		
		$( '.add-availability' ).click( function(){
			$(".add-availability-modal").modal( "show" );
		} );
		
		$( '.readonly-data' ).click( function(){
			swal({
				title: 'Ops! Readonly field',
				text: 'You can change this field in User Manager',
			})
		});

		//Submit form for processing
		$( '.update-person-btn' ).click( function( event ){

			event.preventDefault();
			var formData = $(this).closest('form').serialize();
			swal({
				title: 'Confirm person update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/people/update_person/'.$person_details->person_id ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){ 
									location.reload();
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
		});

		
		$( '#add-personal-skills-btn' ).click( function(){
			var formData = $( '#personal-skills-modal-container :input' ).serialize();
			$.ajax({
				url:"<?php echo base_url('webapp/people/add_personal_skills/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						
						$( '.add-personal-skills-modal' ).modal( 'hide' );
						$( '.modal-backdrop' ).remove();
						
						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 3000
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
		
		
		//Remove Personal skill
		$( '.remove-skill' ).click( function(){
			
			var personId  	= $( this ).data( 'person_id' );
			var skillId  	= $( this ).data( 'skill_id' );
			var	sectionName	= 'not-set';
			if( skillId == 0 || skillId == undefined ){
				swal({
					title: 'Oops! Something went wrong',
					type: 'error',
					text: 'Please reload the page and try again!',
				})
			}
			swal({
				title: 'Confirm remove Skill?',
				type: 'warning',
				text: 'This is an irreversible action',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
			
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/people/remove_personal_skill/' ); ?>" + skillId,
						method:"POST",
						data:{ page:"details", person_id:personId, skill_id:skillId },
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
		
		
		/*$( '#assign-regions-btn' ).click( function(){
			var formData = $( '#assign-regions-modal-container :input' ).serialize();
			$.ajax({
				url:"<?php echo base_url('webapp/people/assign_regions/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						
						$( '.assign-regions-modal' ).modal( 'hide' );
						$( '.modal-backdrop' ).remove();
						
						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 3000
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
		});*/
		
		
		//Un-assign region from a person
		/*$( '.unassign-region' ).click( function(){
			
			var personId  	= $( this ).data( 'person_id' );
			var regionId  	= $( this ).data( 'region_id' );
			var	sectionName	= 'not-set';
			if( regionId == 0 || regionId == undefined ){
				swal({
					title: 'Oops! Something went wrong',
					type: 'error',
					text: 'Please reload the page and try again!',
				})
			}
			swal({
				title: 'Confirm unassign Region?',
				type: 'warning',
				text: 'This is an irreversible action',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
			
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/people/unassign_region/' ); ?>" + regionId,
						method:"POST",
						data:{ page:"details", person_id:personId, region_id:regionId },
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
		} );*/

	});
</script>

