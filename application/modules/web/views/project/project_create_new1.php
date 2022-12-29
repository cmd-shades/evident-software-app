<style type="text/css">
#ajax{
	display: block;
    width: 100%;
    float: left;
    padding: 6px;
    background: #fff;
    border: 1px solid #ccc;
	height: 200px;
    overflow: auto;
}

.input-group.form-group{
	width: 100%;
}

.error_message{
	color: #ff0000;	
}


h5{
	float: left;
}
</style>


<div>
	<legend>Create New Project</legend>
	<form id="project-creation-form" method="post">
		<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
		<input type="hidden" name="page" value="details" />
		<div class="row">
			<div class="project_creation_panel1 col-md-6 col-sm-12 col-xs-12">
				<div class="x_panel tile has-shadow">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5 class="left">What is the Project Name?</h5>
							<h5 class="pull-right error_message" style="display: none;">Please, enter the Project Name</h5>
							<div class="input-group form-group" style="width: 100%;">
								<label class="input-group-addon">Project Name&nbsp;*</label>
								<input name="project_name" type="text" class="form-control" id="project_name" required>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12 pull-right">
							<button class="btn btn-block btn-flow btn-success btn-next project-creation-steps" data-currentpanel="project_creation_panel1" type="button">Next</button>
						</div>
					</div>
				</div>
			</div>

			<div class="project_creation_panel2 col-md-6 col-sm-12 col-xs-12" style="display: none;">
				<div class="x_panel tile has-shadow">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5>What is the Type of the Project?</h5>
							<h5 class="pull-right error_message" style="display: none;">Please, select the Project Type</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">Project Type&nbsp;</label>
								<select name="project_type_id" class="form-control" id="project_type_id" required>
									<option value="">Please select the type of the project</option>
									<?php
                                    if (!empty($project_types)) {
                                        foreach ($project_types as $row) { ?>
											<option value="<?php echo $row->type_id; ?>"><?php echo ucwords($row->type_name); ?></option>
										<?php
                                        }
                                    } else { ?>
										<option value="8">Asset Management</option>
									<?php
                                    } ?>
								</select>
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
			</div>


			<div class="project_creation_panel3 col-md-6 col-sm-12 col-xs-12" style="display: none;">
				<div class="x_panel tile has-shadow">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5>What is the Status of the Project?</h5>
							<h5 class="pull-right error_message" style="display: none;">Please, select the Project Status</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">Project Status&nbsp;*</label>
								<select name="project_status_id" type="text" class="form-control" id="project_status_id" required>
									<option value="">Please, select the status of the project</option>
									<?php
                                    if (!empty($project_statuses)) {
                                        foreach ($project_statuses as $row) { ?>
											<option value="<?php echo $row->status_id; ?>"><?php echo ucwords($row->status_name); ?></option>
										<?php
                                        }
                                    } else { ?>
										<option value="16">Awaiting Action Default</option>
									<?php
                                    } ?>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-info btn-back" data-currentpanel="project_creation_panel3" type="button" >Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-success btn-next project-creation-steps" data-currentpanel="project_creation_panel3" type="button" >Next</button>
						</div>
					</div>
				</div>
			</div>

			<div class="project_creation_panel4 col-md-6 col-sm-12 col-xs-12" style="display: none;">
				<div class="x_panel tile has-shadow">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h4>Who is the Project Leader?</h4>
							<h5 class="pull-right error_message" style="display: none;">Please, select the Project Leader</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">Project Leader&nbsp;*</label>
								<select name="project_lead_id" type="text" class="form-control" id="project_lead_id" required>
									<option value="">Please, select the person, who is leading the project</option>
										<?php if (!empty($project_leaders)) {
										    asort($project_leaders);
										    foreach ($project_leaders as $row) { ?>
												<option value="<?php echo $row->id ?>"><?php echo ucwords($row->first_name.' '.$row->last_name); ?></option>
										<?php }
										    } else { ?>
											<option value="">Please add users</option>
										<?php
										    } ?>
								</select>
							</div>
							
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-info btn-back" data-currentpanel="project_creation_panel4" type="button" >Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-success btn-next project-creation-steps" data-currentpanel="project_creation_panel4" type="button" >Next</button>
						</div>
					</div>
				</div>
			</div>

			<div class="project_creation_panel5 col-md-6 col-sm-12 col-xs-12" style="display: none;">
				<div class="x_panel tile has-shadow">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5>Project Start Date</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">Start Date:</label>
								<input type="text" name="project_start_date" value="" class="form-control datepicker" placeholder="dd-mm-yyyy, leave blank if unknown" />
							</div>
							<h5>Project Finish Date (expected)</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">End Date:</label>
								<input type="text" name="project_finish_date" value="" class="form-control datepicker" placeholder="dd-mm-yyyy, leave blank if unknown" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-info btn-back" data-currentpanel="project_creation_panel5" type="button">Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-success btn-next project-creation-steps" data-currentpanel="project_creation_panel5" type="button">Next</button>
						</div>
					</div>
				</div>
			</div>


			<div class="project_creation_panel6 col-md-6 col-sm-12 col-xs-12" style="display: none;">
				<div class="x_panel tile has-shadow">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5>What is the Description of the Project?</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">Description</label>
								<textarea name="description" type="text" rows="4" class="form-control" id="description"></textarea>
							</div>
							<div class="input-group form-group">
								<label class="input-group-addon">Note</label>
								<textarea name="last_note" type="text" rows="4" class="form-control" id="last_note"></textarea>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-info btn-back" data-currentpanel="project_creation_panel6" type="button" >Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button type="submit" class="btn btn-block btn-flow btn-success" data-currentpanel="project_creation_panel6" id="createProjectButton">Create Project</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<script>
	$(document).ready( function(){

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

		$(".btn-back").click(function(){
			var currentpanel = $(this).data("currentpanel");
			go_back("."+currentpanel)
			return false;
		});

		function panelchange( changefrom ){
			var panelnumber = parseInt( changefrom.slice( 20 ) )+parseInt( 1 );
			var changeto = ".project_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", { direction : 'left' }, 500 );
			$( changeto ).delay( 600 ).show( "slide", {direction : 'right'},500 );
			return false;
		}

		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.slice( 20 ) )-parseInt( 1 );
			var changeto = ".project_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'right'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'left'},500);
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
		$( '#createProjectButton' ).click( function( e ){
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
						url:"<?php echo base_url('webapp/project/add_project/'); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 && ( data.project_id !== '' ) ){
								var newProjectId = data.project_id;
								swal({
									type: 'success',
									title: data.message,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout( function(){
									location.href = "<?php echo base_url('webapp/project/profile/'); ?>" + newProjectId;
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
					$( ".project_creation_panel6" ).hide( "slide", { direction : 'left' }, 500 );
					go_back( ".project_creation_panel2" );
					return false;
				}
			}).catch(swal.noop)
			
		});
	});
</script>