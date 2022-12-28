<style type="text/css">
table{
	width: 100%;
}

.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td{
	border: none;
}

.clickable{
	border-top: 1px solid #eee;
	margin-top: 10px;
	background: #eee;
}

.reveable{
	border-bottom: 1px solid #eee;
	margin-bottom: 10px;
}

.event_tracking_logs{
	padding-top: 10px;
}

.event_tracking_logs div{
	float: left;
	display: block;
}

.etl_creation_date{
	width: 15%;
}

.etl_log_note{
	width: 60%;
}

.etl_created_by{
	width: 20%;
}

.etl_create_new{
	width: 5%;
}

.log_note{
	width: 100%; 
	border-radius: 6px;
}

.modal-header .row{
	margin-top: 10px;
	margin-bottom: 10px;
}

.modal_log_table{
	padding-top: 10px;
}

.modal_log_table td{
	padding: 0 5px;
	vertical-align: top;
}

.modal_log_table td.center, td.center{
	text-align: center;
}

.modal_log_table td.left{
	text-align: left;
}

.table > tbody > tr.reveable > td{
    border: 1px solid #eee;
    border-top: none;
    border-radius: 10px;
    padding: 0 10px;
}


.table > tbody > tr.washer > td{
	line-height: 8px;
    padding: 0;
}

.event_log_row{
	display: block;
    float: left;
    width: 100%;
    padding-bottom: 6px;
}

table > tbody > tr > td.event_status_id_select > form > select{
	height: 30px;
    border-radius: 6px;
    padding: 0 6px;
}
</style>

<div class="row">
	<div class="col-md-4 col-sm-4 col-xs-12 create_event_form_wrapper">
		<div class="row">
			<form action="<?php echo base_url( "webapp/fleet/create_event" ) ?>" method="post" name="create_event" id="createEventForm" novalidate>
				<input type="hidden" name="postdata[vehicle_id]" value="<?php echo $vehicle_details->vehicle_id; ?>" />
				<input type="hidden" name="postdata[account_id]" value="<?php echo $vehicle_details->account_id; ?>" />
				<input type="hidden" name="referring_page" value="<?php echo ( !empty( $include_page ) ) ? $include_page : NULL ; ?>" />

				<div class="x_panel tile fixed_height_300">
					<legend>Create Event</legend>

					<div class="input-group form-group">
						<label class="input-group-addon">Event Category *:</label>
						<select name="postdata[event_category_id]" type="text" class="form-control event_category_id" id="" required />
							<option value="">Please select</option>
							<?php
							if( !empty( $event_categories ) ){
								foreach( $event_categories as $category_id=>$row ){ ?>
									<option value="<?php echo $row->event_category_id; ?>"><?php echo ucwords( $row->category_name ); ?></option>
								<?php
								}
							} else { ?>
								<option value="">Please, add Types of the event</option>
							<?php
							} ?>
						</select>
					</div>

					<div class="input-group form-group category_fields">
						<label class="input-group-addon">Event Type: *</label>
						<select name="postdata[event_type_id]" type="text" class="form-control" id="event_type_ids" required />
							<option value="">Please select</option>
						</select>
					</div>
					
					<div class="input-group form-group">
						<label class="input-group-addon">Event Date: *</label>
						<input type="text" name="postdata[event_date]" value="<?php echo date( 'd/m/Y H:i:s' ); ?>" class="form-control datetimepicker" placeholder="<?php echo date( 'd/m/Y H:i:s' ); ?>" data-date-format="DD/MM/Y H:i:s" required />
					</div>
					<div class="input-group form-group">
						<label class="input-group-addon">Event Note:</label>
						<textarea name="postdata[event_note]" type="text" cols="50" rows="5" class="form-control" id="event_note"></textarea>
					</div>
					
					<?php if( $this->user->is_admin || !empty( $permissions->can_add ) || !empty( $permissions->is_admin ) ){ ?>
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12 pull-left">
								<button id="addEventItem" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="submit">Create Event</button>
							</div>
						</div>
					<?php } else { ?>
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12 pull-left">
								<button class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled>Insufficient permissions</button>
							</div>
						</div>
					<?php } ?>
				</div>
			</form>
		</div>
	</div>
	<div class="col-xl-8 col-md-8 col-sm-8 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Existing Vehicle Events</legend>
			<?php
			if( !empty( $vehicle_events ) ){ ?>
			<table class="table table-responsive">
				<tr>
					<th width="6%">Event ID</th>
					<th width="15%">Category</th>
					<th width="15%">Event Type</th>
					<th width="15%" >Date</th>
					<th width="35%">Event Note</th>
					<th width="10%">Status</th>
					<th  width="4%">&nbsp;</th>
					
				</tr>

					<?php
					$ctr = 1;
					foreach( $vehicle_events as $row ){ ?>
					<tr class="clickable" data-row_id="row_<?php echo $ctr; ?>">
						<td width="6%"><?php echo $row->event_id; ?></td>
						<td width="15%"><?php echo $row->category_name; ?></td>
						<td width="15%"><?php echo $row->event_type_name; ?></td>
						<td width="15%"><?php echo $row->event_date; ?></td>
						<td width="35%"><?php echo $row->event_note; ?></td>
						<td width="10%" class="event_status_id_select">
							<form class="event_status_update">
								<input type="hidden" name="event_id" value="<?php echo $row->event_id; ?>" />
								<input type="hidden" name="page" value="<?php echo $active_tab; ?>" />
								<select name="event_status_id" data-event_id="<?php echo $row->event_id; ?>">
								<?php 
								foreach( $event_statuses as $status ){ ?>
									<option value="<?php echo ( !empty( $status->event_status_id ) ) ? $status->event_status_id : '' ; ?>" <?php echo ( !empty( $status->event_status_id ) && ( $row->event_status_id == $status->event_status_id ) ) ? "selected = selected" : "" ; ?>><?php echo ( !empty( $status->event_status_name ) ) ? ucfirst( $status->event_status_name ) : "" ; ?></option>
								<?php
								} ?>
								</select>
							</form
						</td>
						
						<td width="4%"><i class="fas fa-chevron-down"></i></td>
					</tr>
					<tr class="reveable">
						<td colspan="7">
							<div class="event_tracking_logs reveal_row_<?php echo $ctr; ?>" style="display: none;">
								<div class="event_log_row">
									<div class="etl_creation_date"><strong>Creation Date</strong></div>
									<div class="etl_log_note"><strong>Log Note</strong></div>
									<div class="etl_created_by"><strong>Created By</strong></div>
									<div class="etl_create_new"><button type="button" class="btn btn-sm btn-block btn-flow btn-success btn-next createNewTrackerLogBtn" data-toggle="modal" data-event_id = "<?php echo $row->event_id; ?>" data-event_type = "<?php echo $row->event_type_name; ?>" data-event_category = "<?php echo $row->category_name; ?>" data-target="#createNewTrackerLog">+</button></div>
								</div>
								<?php
								if( !empty( $event_tracking_logs ) ){
									foreach( $event_tracking_logs as $log_row ){
										if( $log_row->event_id == $row->event_id ){
										?>
										<div class="event_log_row">
											<div class="etl_creation_date"><?php echo ( !empty( $log_row->date_created ) ) ? $log_row->date_created : '' ; ?></div>
											<div class="etl_log_note"><?php echo ( !empty( $log_row->log_note ) ) ? $log_row->log_note : '' ; ?></div>
											<div class="etl_created_by"><?php echo ( !empty( $log_row->created_by_full_name ) ) ? $log_row->created_by_full_name : '' ; ?></div>
											<div class="etl_create_new">&nbsp;</div>
										</div>
										<?php
										} else { ?>
										<?php
										}
									}
								} else { ?>
									<div style="width: 100%; display: block; float: left;margin-bottom: 10px;">
										<div>No data available</div>
									</div>

								<?php
								}	?>
							</div>
						</td>
					</tr>
					<tr class="washer"><td colspan="7">&nbsp;</td></tr>
					<?php
					$ctr ++;
					} ?>
			</table>
			<?php
			} else { ?>
				<span><?php echo $this->config->item( 'no_records' ); ?></span>
			<?php
			} ?>
		</div>
	</div>
</div>


<!-- Modal -->
<div id="createNewTrackerLog" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
			
				<div class="row">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<div class="col-md-8">
						<h4 class="modal-title">Create a new Event Tracker Log</h4>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<strong>Event ID: </strong><span class="event_id_in_modal"></span>
					</div>
					<div class="col-md-4">
						<strong>Event Type: </strong><span class="event_type_in_modal"></span>
					</div>
					<div class="col-md-4">
						<strong>Category: </strong><span class="event_category_in_modal"></span>
					</div>
				</div>
				<div class="row">
					<form action="" id="new_log_form" class="">
						<div class="col-md-10">
							<input type="hidden" name="event_id" class="event_id_in_modal" value="" />
							<textarea class="log_note" name="log_note" style="width: 100%; border-radius: 6px;" ></textarea>
						</div>
						<div class="col-md-2">
							<button class="addNewBtn btn-success btn btn-sm btn-block btn-flow btn-success btn-next">Add</button>
						</div>
					</form>
				</div>
				<div class="row" id="tracker_data">
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){
		
		
		$( "select[name='event_status_id']" ).on( "change", function(){
			var formData = $( this ).parent().serialize();

			$.ajax({
				url:"<?php echo base_url( 'webapp/fleet/update_vehicle_event_status/' ); ?>",
				method: "POST",
				data: formData,
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 ){
						console.log( data );
						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 2000
						})
						$( "#tracker_data" ).html( data.event_tracking_logs );
					} else {
						/* Error from request */
						swal({
							type: 'error',
							title: data.status_msg
						})
					}
				}
			});
			
		});
		
		$( ".event_category_id" ).on( 'change', function(){
			var event_category_id = $( this ).val();
			$.ajax({
				url:"<?php echo base_url( 'webapp/fleet/get_event_type_by_cat_id/' ); ?>" + event_category_id,
				method: "POST",
				dataType: 'json',
				success: function( data ){
					if( data.status == 1 ){
						$( "#event_type_ids" ).find( 'option' ).not( ':first' ).remove();
						$.each( data.event_types, function( key, value ){
							$( "#event_type_ids" ).append( '<option value=' + value.event_type_id + '>' + value.event_type_name + '</option>' );
						});
					} else {
						swal({
							type: 'error',
							title: data.status_msg
						})
					}
				}
			});
		});
	

		function load_tracker_logs( event_id, event_type, event_category ){
			$( "input.event_id_in_modal" ).val( event_id );
			$( "span.event_id_in_modal" ).html( event_id );
			$( "span.event_type_in_modal" ).html( event_type );
			$( "span.event_category_in_modal" ).html( event_category );

			$.ajax({
				url:"<?php echo base_url( 'webapp/fleet/get_event_tracking_logs/' ); ?>" + event_id,
				method: "GET",
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 ){
						$( "#tracker_data" ).html( data.event_tracking_logs );
					} else {
						/* Just no logs available */
					}
				}
			});
		}

		$( '.createNewTrackerLogBtn' ).on( 'click', function( e ){
			var event_id = $( this ).data( "event_id" );
			var event_type = $( this ).data( "event_type" );
			var event_category = $( this ).data( "event_category" );
			load_tracker_logs( event_id, event_type, event_category );
		});

		$( '.addNewBtn' ).on( 'click', function( e ){
			e.preventDefault();
			var formData = $( '#new_log_form' ).serialize();
			var trimmed = $.trim( $( "textarea[name='log_note']" ).val() );

			if( !trimmed || trimmed.length < 10 ){
				/* do nothing or show the message */
				alert( "The log cannot be too short" );
			} else {
				var event_id = $( 'input[name="event_id"]' ).val();

				$.ajax({
					url:"<?php echo base_url( 'webapp/fleet/add_event_tracker_log/' ); ?>",
					method: "POST",
					data: formData,
					dataType: 'json',
					success: function( data ){
						if( data.status == 1 ){
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 2000
							})

							window.setTimeout( function(){
								load_tracker_logs( event_id );
							}, 500 );

							$( "textarea[name='log_note']" ).val( '' );

						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
						}
					}
				});
			}
		});

		$( function (){
			$( 'body' ).on( 'click', '.close', function( e ) {
				location.reload();
			});
		});

		$( ".clickable td" ).not( '.event_status_id_select' ).click( function(){
			var row_id = "reveal_" + $( this ).parent().data( "row_id" );
			$( "." + row_id ).slideToggle( 400 );
			$( this ).parent().find( '.fas' ).toggleClass( 'fa-chevron-down fa-chevron-up' );
		});

		$( '.datetimepicker' ).datetimepicker({
			formatDate: 'd/m/Y H:i:s',
			timepicker:true,
			format:'d/m/Y H:i:s',
		});

		var activeClass = "type_" + $( "#event_type_id" ).val();
		$( "#event_type_id" ).on( "change", function( e ){
			e.preventDefault;
			var activeClass = "type_" + $( "#event_type_id" ).val();
			$( ".category_fields" ).each( function(){
				if( !( $( this ).hasClass( activeClass ) ) ){
					$( this ).css( "display", "none" );
				} else {
					$( this ).css( "display", "block" );
				}
			});
		});

		function check_inputs(){
			var result = true;
			$( "#createEventForm input, #createEventForm select").each( function(){
				if( $( this ).hasClass( 'no_check' ) ){

				} else {
					var value = $( this ).val();
					if( ( value == false ) || ( value == '' ) ){
						result = false;
					}
				}
			});
			return result;
		}

		$( "#createEventForm" ).submit( function(){
			var check = check_inputs();
			if( check == false ){
				alert( 'All fields needs to be provided to create an event.' )
				return false;
			} else {
				$( this ).find( 'button#addEventItem' ).prop( "disabled", true );
				return true;
			}
		});
	});
</script>
