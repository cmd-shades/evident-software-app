<style type="text/css">
.input-group.form-group{
	width: 100%;
}

.error_message{
	color: #ff0000;	
}
</style>


<div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
	<legend class="evidocs-legend">Add New Project</legend>
	<div class="x_panel tile has-shadow">
		<form id="project-creation-form" method="post">
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<input type="hidden" name="page" value="details" />
			<div class="x_content project_creation_panel1 col-md-12 col-sm-12 col-xs-12">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<h4 class="left">Please provide some Project details.</h4>
						<h4 class="pull-right error_message" style="display: none;">Project Name</h4>
						<div class="input-group form-group" style="width: 100%;">
							<label class="input-group-addon">Project Name&nbsp;*</label>
							<input name="project_name" type="text" class="form-control" id="project_name" required>
						</div>
						
						<div class="input-group form-group" style="width: 100%;">
							<label class="input-group-addon">Project Description&nbsp;*</label>
							<textarea name="project_description" class="form-control" type="text" data-label_text="Project description..." value="" required ></textarea>
						</div>						
					</div>
				</div>

				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-success btn-next project-creation-steps" data-currentpanel="project_creation_panel1" type="button">Next</button>
					</div>
				</div>
			</div>
			
			<div class="x_content project_creation_panel2 col-md-12 col-sm-12 col-xs-12" style="display:none" >
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<h5>Project Dates</h5>
						<div class="input-group form-group">
							<label class="input-group-addon">Start Date</label>
							<input type="text" name="project_start_date" value="" class="form-control datepicker" placeholder="dd-mm-yyyy, leave blank if unknown" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">End Date</label>
							<input type="text" name="project_finish_date" value="" class="form-control datepicker" placeholder="dd-mm-yyyy, leave blank if unknown" />
						</div>
					</div>
				</div>
		
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-info btn-back" data-currentpanel="project_creation_panel2" type="button" >Back</button>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-success btn-next project-creation-steps" data-currentpanel="project_creation_panel2" type="button" >Next</button>
					</div>
				</div>
			</div>
			
			<div class="x_content project_creation_panel3 col-md-12 col-sm-12 col-xs-12" style="display:none" >
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="form-group">
							<div class="text-center">
								<br/>
								<p>You are about to submit a request to create a new Project.</p>
								<p>Click the "Create Project" to proceed or Back to review your Project setup.</p>
								<br/>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-back" data-currentpanel="project_creation_panel3" type="button" >Back</button>					
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button id="create-project-btn" class="btn btn-block btn-flow btn-success btn-next" type="button" >Create Project</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
	$( document ).ready( function(){

		$( ".project-creation-steps" ).click( function(){
			var currentpanel = $( this ).data( "currentpanel" );
			var inputs_state = check_inputs( currentpanel );

			if( inputs_state == true ){
				panelchange( "." + currentpanel )
				return false;
			} else {
				show_warning( currentpanel );
				return false;
			}
		});

		//Go back-btn
		$(".btn-back").click(function(){
			var currentpanel = $(this).data("currentpanel");
			go_back("."+currentpanel)	
			return false;
		});
		
		function panelchange(changefrom){
			var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
			var changeto = ".project_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", {direction : 'left'}, 500);
			$( changeto ).delay(600).show( "slide", {direction : 'right'},500);	
			return false;	
		}
		
		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
			var changeto = ".project_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", {direction : 'right'}, 500);
			$( changeto ).delay(600).show( "slide", {direction : 'left'},500);	
			return false;	
		}

		function check_inputs( currentpanel ){
			var result = true;
			var panel = "." + currentpanel;

			$( panel + " input" ).each( function(){
				var value = $( this ).val();
				if( ( value == false ) || ( value == '' ) ){
					result = false;
				}
			});

			$( panel + " select" ).each( function(){
				var value = $( this ).val();
				if( ( value == false ) || ( value == '' ) ){
					result = false;
				}
			});

			return result;
		}

		function show_warning( currentpanel ){
			var panel = "." + currentpanel;
			$( panel ).find( ".error_message" ).show();
		}

		//Submit project form
		$( '#create-project-btn' ).click( function( e ){
			e.preventDefault();
			var formData = $( '#project-creation-form' ).serialize();
			swal({
				title: 'Confirm new project creation?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if (result.value) {
					$.ajax({
						url:"<?php echo base_url( 'webapp/project/create_project/' ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function( data ){
							
							if( ( data.status == 1 ) && ( data.project.project_id !== '' ) ){
								var newProjectId = data.project.project_id;
								swal({
									type: 'success',
									title: data.message,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout( function(){
									location.href = "<?php echo base_url( 'webapp/project/profile/' ); ?>" + newProjectId;
								}, 3000 );
							} else {
								swal({
									type: 'error',
									title: data.message
								})
							}
						}
					});
				} else {
					$( ".project_creation_panel3" ).hide( "slide", { direction : 'left' }, 500 );
					go_back( ".project_creation_panel2" );
					return false;
				}
			}).catch(swal.noop)
			
		});
	});
</script>