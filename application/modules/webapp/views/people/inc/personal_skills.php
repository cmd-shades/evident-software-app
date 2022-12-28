<!-- Modal for Personal Skills -->
<div class="modal fade add-personal-skills-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title" id="mySkillModalLabel">Add Personal Skills</h4>						
			</div>
			<div class="modal-body" id="personal-skills-modal-container" >
				<input type="hidden" name="page" value="details" />
				<input type="hidden" name="person_id" value="<?php echo $person_details->person_id; ?>" />
				<label class="strong">Available Skills</label>
				<div class="form-group">
					<select id="personal_skills" name="personal_skills[]" multiple="multiple" class="form-control" style="width:100%; display:none; margin-bottom:10px;" data-label_text="Associated Risks" >
						<option value="" disabled >Search / Select Skills list</option>
						<?php if( !empty( $available_skills ) ) { foreach( $available_skills as $k => $skill ) { ?>
							<?php if( !in_array( $skill->skill_id, $linked_skills ) ){ ?>
								<option value="<?php echo $skill->skill_id; ?>" ><?php echo ucwords( $skill->skill_name ); ?></option>
							<?php } ?>
						<?php } } ?>
					</select>
				</div>
			</div>
			
			<div class="modal-footer">
				<button id="add-personal-skills-btn" class="btn btn-success btn-sm">Add Selected Skills</button>
			</div>
		</div>
	</div>
</div>


<script>
	$( document ).ready(function(){
		
		$( '#personal_skills' ).select2({
			allowClear: true,
			minimumResultsForSearch: -1,
		});
		
		$( '.add-personal-skills' ).click( function(){
			$(".add-personal-skills-modal").modal( "show" );
		} );

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

	});
</script>

