<div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12">
	<form id="skill-creation-form" >
		<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
		<input type="hidden" name="page" value="details"/>
		<div class="row">
			<div class="skill_creation_panel1 col-md-12">
				<div class="x_panel tile has-shadow">
					<legend>Create New Skill <span class="pull-right"><a href="<?php echo base_url('webapp/diary/manage_skills/'); ?>"><i class="fas fa-list"></i> Skills List</a></span></legend>
					<div class="input-group form-group">
						<label class="input-group-addon">Skill</label>
						<input id="skill_name" name="skill_name" class="form-control" type="text" placeholder="Skill" value="" />
					</div>

					<div class="input-group form-group">
						<label class="input-group-addon">Skill Level</label>
						<select name="skill_level" class="form-control">
							<option>Please select level</option>
							<option value="Junior" >Junior</option>
							<option value="Intermediate" >Intermediate</option>
							<option value="Senior" >Senior</option>
						</select>	
					</div>
					
					<div class="input-group form-group">
						<label class="input-group-addon">Skill Description</label>
						<textarea id="skill_description" name="skill_description" type="text" class="form-control" rows="3"></textarea>     
					</div>
					
					<div class="row form-group form-group">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button id="create-skill-btn" class="btn btn-sm btn-flow btn-success btn-next" type="button" >Create Skill Record</button>					
						</div>
					</div>
				</div>						
			</div>	

		</div>
	</form>
</div>

<script>
	$(document).ready(function(){

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
		
		//Submit skill form
		$( '#create-skill-btn' ).click(function( e ){
			e.preventDefault();
			var formData = $('#skill-creation-form').serialize();
			
			swal({
				title: 'Confirm new Skill creation?',
				showCancelButton: 	true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: 	'#9D1919',
				confirmButtonText: 	'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/diary/add_skill/' ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 && ( data.skill !== '' ) ){
								
								var newSkillId = data.skill.skill_id;
								
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){ 
									location.reload();
									//location.href = "<?php echo base_url('webapp/diary/manage_skills/'); ?>"+newSkillId;
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
					return false;
				}
			}).catch( swal.noop )
		});
		
	});
</script>