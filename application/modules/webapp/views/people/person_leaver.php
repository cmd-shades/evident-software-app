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
    width: 20px;
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
/* 	padding-left: 0;
	width: 100%; */
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

.x_panel.tile.has-shadow.position-info{
	min-height: 250px;
}

.add_leaver_log_btn{
    margin-top: 20px;
    width: 100%;
}

.center{
	text-align: center;
}

.log_id{
	width: 5%;
}

.leaving_reason{
	width: 10%;
}

.reemployment_possible{
	width: 10%;
}

.reemployment_reason{
	width: 20%;
}

.exit_interview{
	width: 6%;
}

.exit_interview_note{
	width: 29%;
}

.created_by{
	width: 10%;
}

.created_date{
	width: 10%;
}

.full_block{
	width: 100%;
	display: block;
	float: left;
}

.form-control.radio{
    width: 100%;
    margin-top: 0;
    display: block;
	height: auto;
}

.form-control.radio .label_inline{
	width: 50%;
	float: left;
    display: block;
    padding-top: 5px;
    padding-bottom: 5px;
}

.form-control.radio .label_inline .cr{
	margin: 0px 5px;
}

.checkbox .input-group-addon{
	padding-left: 12px;
}
</style>


<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<div class="rows" style="margin-top: 0px; margin-bottom: 6px;">
				<a class="btn btn-sm btn-flow btn-success btn-next pull-right no_right_margin" id="addNewlog">Create Leaver Log &nbsp;<i class="fas fa-chevron-down"></i></a>
				<legend>Leaver's Details Logs</legend>
			</div>
	<?php 		if( $this->user->is_admin || !empty( $permissions->can_view ) || !empty( $permissions->is_admin ) ){ ?>
			<div class="control-group form-group full_block">
				<table style="width:100%; table-layout: fixed;">
					<tr>
						<th class="log_id">Log ID</th>
						<th class="leaving_reason">Leaving<br />Reason</th>
						<th class="reemployment_possible center">Reemployment<br />Possible?</th>
						<th class="reemployment_reason">Reemployment<br />Reason</th>
						<th class="exit_interview center">Exit<br />Interview</th>
						<th class="exit_interview_note">Exit<br />interview Note</th>
						<th class="created_by">Created By</th>
						<th class="created_date">Created Date</th>
					</tr>
				</table>
			</div>
			<div class="control-group form-group table_body full_block">
				<table style="width:100%; table-layout: fixed;">
			<?php 	if( !empty( $leaver_logs ) ){ foreach( $leaver_logs as $row ){ ?>
						<tr>
							<td class="log_id"><?php echo $row->leaver_log_id; ?></td>
							<td class="leaving_reason"><?php echo $row->reason_name; ?></td>
							<td class="reemployment_possible center"><?php echo format_boolean_client( $row->reemployment ); ?></td>
							<td class="reemployment_reason"><?php echo $row->reemployment_reason; ?></td>
							<td class="exit_interview center"><?php echo format_boolean_client( $row->exit_interview ); ?></td>
							<td class="exit_interview_note"><?php echo $row->exit_interview_note; ?></td>
							<td class="created_by"><?php echo ( !empty( $row->created_by_full_name ) ) ? $row->created_by_full_name : '' ; ?></td>
							<td class="created_date"><?php echo ( validate_date( $row->created_date ) ) ? format_datetime_client( $row->created_date ) : ''; ?></td>
						</tr>
			<?php 		} }else{ ?>
						<tr>
							<td colspan="8"><?php echo $this->config->item( 'no_records' ); ?></td>
						</tr>
			<?php 		} ?>
				</table>
			</div>
	<?php 		} ?>
		</div>
	</div>
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="row create-leaver-log" style="display: none;">
			<form id="create_leaver_log">
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<input type="hidden" name="page" value="position" />
					<input type="hidden" name="person_id" value="<?php echo $person_details->person_id; ?>" />
					<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
					<div class="x_panel tile has-shadow position-info">
						<legend>Add Leaver's Details Log</legend>
						<div class="input-group form-group">
							<label class="input-group-addon">Leaving Reason</label>
							<select id="leaving_reason_id" name="leaving_reason_id" class="form-control" data-field_name="Department" >
								<option value="" >Please select</option>
								<?php if( !empty( $leaver_reasons ) ) { foreach( $leaver_reasons as $reason ) { ?>
									<option value="<?php echo $reason->reason_id; ?>"><?php echo $reason->reason_name; ?></option>
								<?php } } ?>
							</select>
						</div>

						<div class="input-group form-group checkbox">
							<label class="input-group-addon">Would be Re-employed?</label>
							<div class="form-control radio">
								<label class="label_inline">
									<input type="radio" name="reemployment" value="yes">
									<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Yes
								</label>

								<label class="label_inline">
									<input type="radio" name="reemployment" value="no">
									<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>No
								</label>
							</div>
						</div>

						<div class="form-group reemployment_div" style="display: none;">
							<textarea name="reemployment_reason" class="form-control" type="text" value="" style="width:100%;" placeholder="Re-employment Reason"></textarea>
						</div>

						<div class="input-group form-group checkbox">
							<label class="input-group-addon">Exit Interview?</label>
							<div class="form-control radio">
								<label class="label_inline">
									<input type="radio" name="exit_interview" value="yes">
									<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Yes
								</label>

								<label class="label_inline">
									<input type="radio" name="exit_interview" value="no">
									<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>No
								</label>
							</div>
						</div>

						<div class="form-group exit_interview_div" style="display: none;">
							<textarea name="exit_interview_note" class="form-control" type="text" value="" style="width:100%;" placeholder="Exit interview Note"></textarea>
						</div>

						<div class="row add_leaver_log_btn">
			<?php 		if( $this->user->is_admin || !empty( $permissions->can_add ) || !empty( $permissions->is_admin ) ){ ?>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<button id="add_leaver_log_btn" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="button">Add Leaver Log</button>
							</div>
			<?php 		} else { ?>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled>No permissions</button>
							</div>
			<?php 		} ?>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){

		$( "input[name='reemployment'], input[name='exit_interview']" ).on( "change", function(){
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
			$( ".create-leaver-log" ).slideToggle( 1000 );
			$( this ).children( '.fas' ).toggleClass( 'fa-chevron-down fa-chevron-up' );
		});

		//Submit form for processing
		$( '#add_leaver_log_btn' ).click( function( e ){
			e.preventDefault();

			var missing_values = false;

			if( $( "*[name='leaving_reason_id']" ).val() == "" ){
				$( "*[name='leaving_reason_id']" ).css( "border", "2px solid red" );
				missing_values = true;
			}

			if( $( "*[name='reemployment']:checked" ).val() == undefined ){
				$( "*[name='reemployment']" ).closest( ".form-control.radio" ).css( "border", "2px solid red" );
				missing_values = true;
			} else {
				if( ( $( "*[name='reemployment']:checked" ).val() == "yes" ) && ( ( $( "*[name='reemployment_reason']" ).val() ).trim().length < 1 ) ){
					$( ".reemployment_div" ).css( "border", "2px solid red" );
					missing_values = true;
				}
			}

			if( $( "*[name='exit_interview']:checked" ).val() == undefined ){
				$( "*[name='exit_interview']" ).closest( ".form-control.radio" ).css( "border", "2px solid red" );
				missing_values = true;
			} else {
				if( ( $( "*[name='exit_interview']:checked" ).val() == "yes" ) && ( ( $( "*[name='exit_interview_note']" ).val() ).trim().length < 1 ) ){
					$( ".exit_interview_div" ).css( "border", "2px solid red" );
					missing_values = true;
				}
			}

			if( missing_values == true ){
				swal({
					type: 'error',
					title: 'Please, give an answer to all required field(s).',
				})
				return false;
			}

			var formData = $( this ).closest( 'form' ).serialize();
			swal({
				title: 'Add Leaver\'s details?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ){
				if( result.value ){
					$.ajax({
						url:"<?php echo base_url( 'webapp/people/create_leavers_details_log/' ); ?>",
						method: "POST",
						data:formData,
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){
									location.reload();
								}, 3000);
							} else {
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						}
					});
				}
			}).catch( swal.noop );
		});
	});
</script>