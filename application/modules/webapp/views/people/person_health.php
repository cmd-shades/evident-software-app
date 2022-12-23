<style type="text/css">
.checkbox label:after,
.radio label:after {
    content: '';
    display: table;
    clear: both;
}

.checkbox .cr,
.radio .cr {
    position: relative;
    display: inline-block;
    border: 1px solid #a9a9a9;
    border-radius: .25em;
    width: 1.3em;
	vertical-align: bottom;
    height: 20px;
    margin: 16px 12px;
}

.radio .cr {
    border-radius: 50%;
}

.checkbox .cr .cr-icon,
.radio .cr .cr-icon {
    position: absolute;
    font-size: .8em;
    line-height: 0;
    top: 50%;
    left: 20%;
}

.radio .cr .cr-icon {
    margin-left: 0.04em;
}

.checkbox label input[type="checkbox"],
.radio label input[type="radio"] {
    display: none;
}

.checkbox label input[type="checkbox"] + .cr > .cr-icon,
.radio label input[type="radio"] + .cr > .cr-icon {
    transform: scale(3) rotateZ(-20deg);
    opacity: 0;
    transition: all .3s ease-in;
}

.checkbox label input[type="checkbox"]:checked + .cr > .cr-icon,
.radio label input[type="radio"]:checked + .cr > .cr-icon {
    transform: scale(1) rotateZ(0deg);
    opacity: 1;
}

.checkbox label input[type="checkbox"]:disabled + .cr,
.radio label input[type="radio"]:disabled + .cr {
    opacity: .5;
}


.input-group.form-group{
	width: 100%;
}

.input-group-addon:first-child{
	width: 40%;
	white-space: normal;
}


.checkbox .radio label, .checkbox > label{
	padding-left: 0;
	width: 100%;
}

.checkbox_label{
	width: 40%;
	min-width: 180px;
    float: left;
    padding: 18px 12px;
    font-size: 14px;
    font-weight: normal;
    line-height: 1;
    color: #555;
    text-align: center;
    background-color: #eee;
    border: 1px solid #ccc;
    border-radius: 4px;
    text-align: left;
}

/* end of checkboxes styling */


.clickable{
    border-top: 1px solid #eee;
    margin-top: 10px;
    background: #eee;
}

.clickable td:not( .log_status_select):not( .viewLogDetailsLink ){
    cursor: pointer;
}

table > tbody > tr > td.log_status_select > form > select {
    height: 30px;
    border-radius: 6px;
    padding: 0 6px;
}

.people_health_log_notes{
	width: 100%;
    display: block;
    border: 1px solid #ddd;
    border-top: none;
    padding: 20px;
    padding-top: 5px;
    float: left;
    border-bottom-left-radius: 6px;
    border-bottom-right-radius: 6px;
}

.health_note_row{
	width: 100%;
}

.people_health_log_notes div{
	float: left;
	display: block;
}

.health_note_id{
	width: 8%;
}

.date_created{
	width: 15%;
}

.created_by{
	width: 17%;
}

.note{
	width: 35%;
}


.health_log_previous_status{
	width: 10%;
}

.health_log_current_status{
	width: 10%;
}

.note_create_new{
	width: 4%;
}

.note_create_new .btn{
	width: 30px;
	float: right;
}

.reveable > td{
	padding-top: 0;
}

.padd_left_5{
	padding-left: 5px;
}

.washer > td{
	height: 3px;
    line-height: 0.1px;
    padding: 0;
}

.log_status_input, .log_status_select {
	background: #fff;
    height: 30px;
    border-radius: 6px;
    padding: 0 6px;
    border: 1px solid #a9a9a9;
	margin-bottom: 10px;
}

.log_status_input{
	width: 90%;
}

.log_note_in_modal{
	width: 100%;
    border-radius: 6px;
    margin-bottom: 10px;
}

.log_note_in_modal_container > td{
	vertical-align: top;
}

.margin_top_10{
	margin-top: 10px;
}

.leave_a_note{
	margin-bottom: 10px;
}


.col-md-6.col-sm-6.col-xs-12 .x_panel.tile.has-shadow{
	min-height: 310px;
}

.fixed_row{
	position: absolute;
    bottom: 10px;
    width: 100%;
}

.x_panel.tile.has-shadow.right_panel{
	padding-top: 20px;
	padding-bottom: 50px;
}

</style>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<div class="rows" style="margin-top: 0px; margin-bottom: 6px;">
				<a class="btn btn-sm btn-flow btn-success btn-next pull-right no_right_margin" id="addNewlog">Create New Record &nbsp;<i class="fas fa-chevron-down"></i></a>
				<legend>Existing Health Records</legend>
			</div>
						

<?php 		if( $this->user->is_admin || !empty( $permissions->can_view ) || !empty( $permissions->is_admin ) ){ ?>
			<div class="control-group form-group full_block">
				<table style="width:100%; table-layout: fixed;">
					<tr class="headers">
						<td width="12%">Questionnaire Date</td>
						<td width="10%">Questionnaire Type</td>
						<td width="12%">Questionnaire Result</td>
						<td class="text-center" width="10%">H&amp;S Assessment Required</td>
						<td class="text-center" width="10%">Medical Conditions</td>
						<td class="text-center" width="10%">Disability</td>
						<td class="text-center" width="10%">Review Required</td>
						<td width="8%">Date</td>
						<td width="6%">&nbsp;</td>
						<td width="8%">&nbsp;</td>
					</tr>
				</table>
			</div>
			<div class="control-group form-group table_body full_block">
				<table style="width:100%; table-layout: fixed;">
					<?php
					if( !empty( $health_log ) ){
					$ctr = 1;
					foreach( $health_log as $row ){ ?>
						<tr class="clickable <?php echo $row->health_log_id; ?>" data-health_log_id="row_<?php echo $ctr; ?>">
							<td width="12%"><?php echo ( validate_date( $row->medical_qnaire_date ) ) ? trim( date( 'd/m/Y', strtotime( $row->medical_qnaire_date ) ) ) : ''; ?></td>
							<td width="10%"><?php echo $row->q_type_name; ?></td>
							<td width="12%"><?php echo $row->q_result_name; ?></td>
							<td class="text-center" width="10%"><i class="far <?php echo ( !empty( $row->medical_hs_assessment_req ) && ( $row->medical_hs_assessment_req == 1 ) ) ? "fa-check-circle text-green" :  "fa-times-circle text-red" ; ?>"></i></td>
							<td class="text-center" width="10%"><i class="far <?php echo ( !empty( $row->medical_conditions ) && ( $row->medical_conditions == 1 ) ) ? "fa-check-circle text-green" :  "fa-times-circle text-red" ; ?>"></i></td>
							<td class="text-center" width="10%"><i class="far <?php echo ( !empty( $row->medical_disability ) && ( $row->medical_disability == 1 ) ) ? "fa-check-circle text-green" :  "fa-times-circle text-red" ; ?>"></i></td>
							<td class="text-center" width="10%"><i class="far <?php echo ( !empty( $row->is_review_required ) && ( $row->is_review_required == 1 ) ) ? "fa-check-circle text-green" :  "fa-times-circle text-red" ; ?>"></i></td>
							<td width="8%"><?php echo ( validate_date( $row->review_date ) ) ? format_date_client( $row->review_date ) : '---'; ?></td>
							<td width="6%"><i class="fas fa-chevron-down"></i></td>
							<td width="8%" class="viewLogDetailsLink" data-target="#viewLogDetails" data-toggle="modal" data-current_status="<?php echo $row->log_status; ?>" data-health_log_id="<?php echo $row->health_log_id; ?>" ><a href="#viewLogDetails">More details</a></td>
						</tr>
						<tr class="reveable">
							<td colspan="10">
								<div class="people_health_log_notes reveal_row_<?php echo $ctr; ?>" style="display: none;float: left; width: 100%;">
							<?php 	if( !empty( $health_log_notes->{ $row->health_log_id } ) ){
										$header_trigger = $nonotes_trigger = false;
										foreach( $health_log_notes->{ $row->health_log_id } as $note_row ){
											if( !$header_trigger ){ ?>
											<div class="health_note_row">
												<div class="health_note_id"><strong>Note ID</strong></div>
												<div class="date_created"><strong>Created Date</strong></div>
												<div class="created_by"><strong>Created By</strong></div>
												<div class="note"><strong>Note</strong></div>
												<div class="health_log_previous_status hide"><strong>Previous<br />Log Status</strong></div>
												<?php /* <div class="health_log_current_status hide"><strong>Current<br />Log Status</strong></div> */ ?>
												<div class="note_create_new pull-right"><button type="button" class="btn btn-sm btn-block btn-flow btn-success btn-next createNewNoteBtn" data-toggle="modal" data-health_log_id = "<?php echo $row->health_log_id; ?>" data-current_status="<?php echo $row->log_status; ?>" data-status_change="no" data-target="#createNewNote" title="Add Comment">+</button></div>
											</div>
											<?php
											$header_trigger = true;
											} ?>
											<div class="health_note_row">
												<div class="health_note_id"><?php echo ( !empty( $note_row->health_note_id ) ) ? $note_row->health_note_id : '' ; ?></div>
												<div class="date_created"><?php echo ( !empty( $note_row->date_created ) ) ? $note_row->date_created : '' ; ?></div>
												<div class="created_by"><?php echo ( !empty( $note_row->created_by_full_name ) ) ? $note_row->created_by_full_name : '' ; ?></div>
												<div class="note"><?php echo ( !empty( $note_row->note ) ) ? $note_row->note : '' ; ?></div>
												<div class="health_log_previous_status hide"><?php echo ( !empty( $note_row->health_log_previous_status ) ) ? $note_row->health_log_previous_status : '' ; ?></div>
												<div class="health_log_current_status hide"><?php echo ( !empty( $note_row->health_log_current_status ) ) ? $note_row->health_log_current_status : '' ; ?></div>
												<div class="etl_create_new">&nbsp;</div>
											</div>
								<?php 	}

									} else { ?>
										<div class="health_note_row" style="width: 100%; display: block; float: left;margin-bottom: 10px;">
											<div><p>No comments available</p></div>
											<div class="note_create_new pull-right"><button type="button" class="btn btn-sm btn-block btn-flow btn-success btn-next createNewNoteBtn" data-toggle="modal" data-health_log_id = "<?php echo $row->health_log_id; ?>" data-current_status="<?php echo $row->log_status; ?>" data-status_change="no" data-target="#createNewNote" title="Add quick note">+</button></div>
										</div>
							<?php 	}	?>
								</div>
							</td>
						</tr>
						<tr class="washer">
							<td colspan="10">&nbsp;</td>
						</tr>
					<?php
						$ctr++;
						}
					} else { ?>
						<tr>
							<td colspan="5"><?php echo $this->config->item( 'no_records' ); ?></td>
						</tr>
					<?php
					} ?>
				</table>
			</div>
			<?php } ?>
		</div>
	</div>
	<div class="col-md-12 col-sm-12 col-xs-12 create-health-record" style="display: none;">
		<form id="create-health-log">
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="x_panel tile has-shadow">
						<legend>Create New Record</legend>
						<input type="hidden" name="page" value="health" />
						<input type="hidden" name="person_id" value="<?php echo $person_details->person_id; ?>" />
						<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />

						<div class="input-group form-group">
							<label class="input-group-addon">Questionnaire Date&nbsp;*</label>
							<input name="medical_qnaire_date" value="" class="form-control datetimepicker" placeholder="<?php echo date( 'd/m/Y' ); ?>" data-date-format="DD/MM/Y" />
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Questionnaire Type&nbsp;*</label>
							<select name="medical_qnaire_type_id" class="form-control" required>
								<option value="">Please select</option>
								<?php
								if( !empty( $q_types ) ){
									foreach( $q_types as $key => $row ){ ?>
										<option value="<?php echo $row->q_type_id; ?>"><?php echo $row->q_type_name; ?></option>
									<?php
									}?>
								<?php
								} else { ?>
									<option value="1">Return to work OR</option>
									<option value="2">Maternity Return</option>
									<option value="3">Short Absence</option>
									<option value="4">Long Absence</option>
								<?php
								} ?>
							</select>
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">H &amp; S Assessment Required&nbsp;*</label>
								<select name="medical_hs_assessment_req" class="form-control" required>
									<option value="">Please select</option>
									<option value="no">No</option>
									<option value="yes">Yes</option>
								</select>
							</label>
						</div>

						<div class="input-group form-group medical_hs_assessment_req_div" style="display: none;">
							<label class="input-group-addon">Health &amp; Safety<br />Adjustment Note</label>
							<textarea name="medical_hs_adjustment_note" class="form-control"  rows="3"></textarea>
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Medical Conditions&nbsp;*</label>
								<select name="medical_conditions" class="form-control" required>
									<option value="">Please select</option>
									<option value="no">No</option>
									<option value="yes">Yes</option>
								</select>
							</label>
						</div>

						<div class="input-group form-group medical_conditions_div" style="display: none;">
							<label class="input-group-addon">Medical Conditions Note</label>
							<textarea name="medical_conditions_note" class="form-control" rows="3"></textarea>
						</div>
					</div>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="x_panel tile has-shadow right_panel">
						<div class="input-group form-group">
							<label class="input-group-addon">Disability&nbsp;*</label>
							<select name="medical_disability" class="form-control" required>
								<option value="">Please select</option>
								<option value="no">No</option>
								<option value="yes">Yes</option>
							</select>
						</div>
						<div class="input-group form-group medical_disability_div" style="display: none;">
							<label class="input-group-addon">Medical Disability Note</label>
							<textarea name="medical_disability_note" class="form-control" rows="3"></textarea>
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">General Note</label>
							<textarea name="general_note" class="form-control"  rows="3"></textarea>
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Is a Review required&nbsp;*</label>
							<select name="is_review_required" class="form-control" required>
								<option value="">Please select</option>
								<option value="no">No</option>
								<option value="yes">Yes</option>
							</select>
						</div>

						<div class="input-group form-group is_review_required_div" style="display: none;">
							<label class="input-group-addon">Review Date</label>
							<input name="review_date" value="" class="form-control datetimepicker" placeholder="<?php echo date( 'd/m/Y' ); ?>" data-date-format="DD/MM/Y" />
						</div>


						<div class="input-group form-group">
							<label class="input-group-addon">Questionnaire Result&nbsp;*</label>
							<select name="medical_qnaire_result_id" class="form-control" required>
								<option value="">Please select</option>
								<?php
								if( !empty( $q_results ) ){
									foreach( $q_results as $key => $row ){ ?>
										<option value="<?php echo $row->q_result_id; ?>"><?php echo $row->q_result_name; ?></option>
									<?php
									}?>
								<?php
								} else { ?>
									<option value="1">Fit for the role with no restrictions</option>
									<option value="2">Fit for the role with restrictions</option>
									<option value="3">Full Medical Required</option>
									<option value="4">Unfit for Role</option>
									<option value="5">Supplementary information required</option>
								<?php
								} ?>
							</select>
						</div>

			<?php 		if( $this->user->is_admin || !empty( $permissions->can_add ) || !empty( $permissions->is_admin ) ){ ?>
							<div class="row fixed_row">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									<button id="create-person-health-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next create-person-health-btn" type="button">Create New Record</button>
								</div>
							</div>
			<?php		} else { ?>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled>No permissions</button>
							</div>
			<?php		} ?>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>


<div id="viewLogDetails" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<div class="row">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<div class="col-md-8">
						<h4 class="modal-title"><span style="font-weight: 400">Health Log (<span class="health_log_in_overview"></span>) for </span><?php echo $person_details->first_name.' '.$person_details->last_name; ?> / <?php echo $person_details->job_title; ?></h4>
					</div>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-12" id="ajax_health_log">
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="modal-body" style="border-top: 1px solid #e5e5e5;">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<h5>Added comments</h5>
							<div class="col-md-12" id="ajax_health_log_notes">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- Modal -->
<div id="createNewNote" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<div class="row">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<div class="col-md-12">
						<h5 class="modal-title">New Comment for Health Log ID: <span class="log_id_in_modal"></span></h5>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<form id="new_note_form">
							<input type="hidden" name="health_log_id" value="" />
							<input type="hidden" name="status_change" value="no" />
							<input type="hidden" name="health_log_previous_status" value="" />
							<input type="hidden" name="health_log_current_status" value="" />
							<input type="hidden" name="person_id" value="<?php echo $person_details->person_id; ?>" />
							<!-- show the note section -->
							<div class="row">
								<div class="leave_a_note col-md-12">
									<p style="margin-bottom: 0;">Please leave a comment below</p>
								</div>
								<div class="leave_a_note col-md-12">
									<textarea class="note" name="note" style="width: 100%; border-radius: 6px;" required></textarea>
								</div>

					<?php 		if( $this->user->is_admin || !empty( $permissions->can_add ) || !empty( $permissions->is_admin ) ){ ?>
									<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
										<button class="addNewNoteBtn btn-success btn btn-sm btn-block btn-flow margin_top_10">Add</button>
									</div>
					<?php		} else { ?>
									<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
										<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="button" disabled>No permissions</button>
									</div>
					<?php		} ?>

							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<script>
	$( document ).ready( function(){


		$( "*[name='medical_hs_assessment_req'], *[name='medical_conditions'], *[name='medical_disability'], *[name='is_review_required']" ).on( "change", function(){
			var div_name = "." + $( this ).prop( "name" ) + "_div";
			
			if( $( this ).val() == undefined ){
			} else if ( $( this ).val() == "yes" ){
				$( div_name ).slideDown();
			} else if( $( this ).val() == "no" ){
				$( div_name ).slideUp();
			}
		});

		$( "#addNewlog" ).on( "click", function(){
			$( ".table_body" ).slideToggle( 1000 );
			$( ".create-health-record" ).slideToggle( 1000 );
			$( this ).children( '.fas' ).toggleClass( 'fa-chevron-down fa-chevron-up' );
		});



		/* To create a new note against the health log */
 		$( '.createNewNoteBtn' ).on( 'click', function(){
 			var log_id_in_modal 	= $( this ).data( "health_log_id" );
 			var status_change 		= $( this ).data( "status_change" );
 			var current_status 		= $( this ).data( "current_status" );
			$( ".log_id_in_modal" ).html( log_id_in_modal );
			$( "#new_note_form input[name='health_log_id']" ).val( log_id_in_modal );
			$( "#new_note_form input[name='health_log_previous_status']" ).val( current_status );
			$( "#new_note_form input[name='health_log_current_status']" ).val( current_status );
 		});


		$( "#new_note_form" ).submit( function( e ){
			e.preventDefault();

			var formData 	= $( "#new_note_form" ).serialize();
			var noteValue 	= $( "#new_note_form textarea[name='note']" ).val();

			if( ( noteValue != '' ) && ( noteValue.length > 0 ) ){
				if( save_health_note( formData ) != false ){
					window.setTimeout( function(){
						location.reload();
						}, 3000 );
				} else {
					alert( 'false' );
				}
			} else {
				alert( 'The Note is missing or is too short!' );
			}

			$( '#createNewNote' ).modal( 'toggle' );
		});

		function save_health_note( formData ){
			$.ajax({
				url:"<?php echo base_url( 'webapp/people/create_health_note/' ); ?>",
				method: "POST",
				data: formData,
				dataType: 'json',
				success:function( data ){

					if( data.status == 1 || data.status == true ){
						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 3000
						})
						return true;
					} else {
						swal({
							type: 'warning',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 3000
						})

						return false;
					}

				}
			});
		}

		$( "#ajax_health_log" ).on( "click", ".addNewNoteBtn", function( e ){
			e.preventDefault();

 			var status_change 		= false;
			/*
			var original_status 	= $( "#log_status_update input[name='health_log_previous_status']" ).val();
			var current_status		= $( "#log_status_update select[name='health_log_current_status']" ).val();
			if( original_status != current_status ){
				var status_change = true;
				$( "#log_status_update select[name='status_change']" ).val( 'yes' );
			} */

			var note_in_modal = $( ".log_note_in_modal" ).val();

			if( note_in_modal == '' ){
				alert( 'You need to leave a longer comment.' );
				return false;
			}

			if( status_change == true ){
				$.ajax({
					url:"<?php echo base_url( 'webapp/people/update_health_log_status/' ); ?>",
					method: "POST",
					data: {
						'log_status' : $( "#log_status_update select[name='health_log_current_status']" ).val(),
						'health_log_id' : $( "#log_status_update input[name='health_log_id']" ).val(),
					},
					dataType: 'json',
					success:function( data ){
						if( data.status == 1 ){
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 3000
							});
							window.setTimeout( function(){

							}, 3000 );

						} else {
							swal({
								type: 'warning',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 3000
							});
							window.setTimeout( function(){

							}, 3000 );
						}
					}
				});
			}

			var formData = $( "#log_status_update" ).serialize();
			if( save_health_note( formData ) != false ){
				window.setTimeout( function(){
					location.reload();
					}, 3000 );
			} else {
				alert( 'false' );
			}
		});


		/* To open the DIV containing the health log notes */
		$( ".clickable td" ).not( "td.log_status_select" ).not( "td.viewLogDetailsLink" ).click( function(){
			var health_log_id = "reveal_" + $( this ).parent().data( "health_log_id" );
			$( "." + health_log_id ).slideToggle( 1000 );
			$( this ).parent( ".clickable" ).find( '.fas' ).toggleClass( 'fa-chevron-down fa-chevron-up' );
		});

		function load_log_details( health_log_id ){
			$.ajax({
				url:"<?php echo base_url( 'webapp/people/get_health_log/' ); ?>",
				method: "POST",
				data: {
					'health_log_id': health_log_id,
					'person_id'	: <?php echo $person_details->person_id; ?>
				},
				dataType: 'json',
				success:function( data ){
					if( data.status == '1' ){
						$( "#ajax_health_log" ).html( data.health_log );
					} else {
						/* Just no logs available */
					}
				}
			});
		}

		function load_health_log_notes( health_log_id ){
			$.ajax({
				url:"<?php echo base_url( 'webapp/people/get_health_log_notes/' ); ?>",
				method: "POST",
				data: {
					'health_log_id': health_log_id,
				},
				dataType: 'json',
				success:function( data ){
					$( "#ajax_health_log_notes" ).html( data.health_log_notes );
				}
			});
		}

		$( '.viewLogDetailsLink' ).on( 'click', function(){
			var health_log_id = $( this ).data( "health_log_id" );
			$( ".health_log_in_overview" ).html( health_log_id );

			var current_status = $( this ).data( "current_status" );
			$( "input[name='previous_status']" ).val( current_status );
			$( "input[name='current_status']" ).val( current_status );

			load_log_details( health_log_id );
			load_health_log_notes( health_log_id );
		});


		$( '.datetimepicker' ).datetimepicker({
			formatDate: 'd/m/Y',
			timepicker: false,
			format:'d/m/Y',
		});


		//Submit form for processing
		$( '#create-person-health-btn' ).click( function( e ){
			e.preventDefault();

			var check_empty = false;
			var fields_to_check = ["medical_qnaire_date", "medical_hs_assessment_req", "medical_conditions", "medical_disability", "medical_qnaire_result_id", "medical_qnaire_type_id"];

			$( fields_to_check ).each( function(){
				var field = $( "*[name='" + this +"']" );
				if( $( field ).val() == "" ){
					check_empty = true;
					field.css( "border", "2px solid red" );
				}
			});

			if( check_empty == true ){
				alert( "Required fields cannot be empty" );
				return false;
			} else {

				var formData = $( '#create-health-log' ).serialize();
				swal({
					title: 'Confirm New Health Log creation?',
					showCancelButton: true,
					confirmButtonColor: '#5CB85C',
					cancelButtonColor: '#9D1919',
					confirmButtonText: 'Yes'
				}).then( function( result ) {
					if ( result.value ) {
						$.ajax({
							url:"<?php echo base_url( 'webapp/people/create_health_log/' ); ?>",
							method: "POST",
							data: formData,
							dataType: 'json',
							success:function( data ){
								if( data.status == 1 ){
									swal({
										type: 'success',
										title: data.status_msg,
										showConfirmButton: false,
										timer: 3000
									})

									window.setTimeout( function(){
										location.reload();
									},500 );
								}else{
									swal({
										type: 'error',
										title: data.status_msg
									})
								}
							}
						});
					} else {
					}
				}).catch( swal.noop )
			}
		});
	});
</script>