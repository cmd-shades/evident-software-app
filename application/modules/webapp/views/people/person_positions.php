<!-- Run the admin check if a Tab requires that you're admin to view -->
<?php if( !empty( $admin_no_access ) ){
	$this->load->view('errors/access-denied', false, false, true );
}else{ ?>

<div class="row">
	<?php if( $this->user->is_admin || !empty( $permissions->can_add ) || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
	<div class="col-md-4 col-sm-4 col-xs-12">
		<form  id="update-person-position" class="form-horizontal">
			<input type="hidden" name="page" value="position" />
			<input type="hidden" name="user_id" value="<?php echo $person_details->user_id; ?>" />
			<input type="hidden" name="person_id" value="<?php echo $person_details->person_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<div class="x_panel tile has-shadow position-info">
				<legend>Position details <span class="error_message pull-right" style="display: block; color:red; font-size:14px; verticle-align:bottom" id="position-data-errors"></span></legend>
				<div class="input-group form-group">
					<label class="input-group-addon">Department</label>
					<select id="department_id" name="department_id" class="form-control" data-field_name="Department" >
						<option disabled>Please select</option>
						<?php if( !empty( $departments ) ) { foreach( $departments as $k => $department ) { ?>
							<option value="<?php echo $department->department_id; ?>" <?php echo ( $person_details->department_id == $department->department_id ) ? 'selected=selected' : ''; ?> ><?php echo $department->department_name; ?></option>
						<?php } } ?>
					</select>	
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon" >Job Title</label>
					<select id="job_title_id" name="job_title_id" class="form-control" data-field_name="Job Title" >
						<option disabled>Please select</option>
						<?php if( !empty( $job_titles ) ) { foreach( $job_titles as $k => $job_title ) { ?>
							<option value="<?php echo $job_title->job_title_id; ?>" <?php echo ( $person_details->job_title == $job_title->job_title ) ? 'selected=selected' : ''; ?> ><?php echo $job_title->job_title; ?></option>
						<?php } } ?>
					</select>	
				</div>
				
				<div class="input-group form-group">
					<label class="input-group-addon">Position Type</label>
					<select id="position_type" name="position[position_type]" class="form-control">
						<option value="Permanent" selected=selected >Permanent</option>
						<option value="Temporary" >Temporary</option>
						<option value="Covering">Just Covering</option>
					</select>	
				</div>
				
				<div class="input-group form-group">
					<label class="input-group-addon">Position Start date</label>
					<input name="position[job_start_date]" class="form-control datepicker" type="text" placeholder="Start date" value="" />
				</div>
				
				<div class="position-end-date" style="display:none">
					<div class="input-group form-group">
						<label class="input-group-addon">Position End date</label>
						<input id="position-end-date" name="position[job_end_date]" class="form-control datepicker" type="text" data-field_name="Position End Date" placeholder="End date" value="" />
					</div>
				</div>

				<div class="form-group">
					<textarea name="position[position_notes]" class="form-control" type="text" value="" style="width:100%;" placeholder="Position notes"></textarea>
				</div>
				
				<?php if( $this->user->is_admin || !empty( $permissions->can_add ) || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
					<div class="row col-md-6">
						<button id="update-position-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="button" >Update Position Info</button>					
					</div>
				<?php }else{ ?>
					<div class="row col-md-6">
						<button class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>					
					</div>
				<?php } ?>
			</div>
		</form>
	</div>
	<?php } ?>
	
	<div class="col-md-8 col-sm-8 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Position History</legend>
			<?php if( $this->user->is_admin || !empty( $permissions->can_view ) || !empty( $permissions->is_admin ) ){ ?>
				<table style="width:100%">
					<tr>
						<th width="30%">Job Title</th>
						<th width="15%">Position Type</span></th>
						<th width="15%">Department</th>
						<th width="13%">Start Date</th>
						<th width="12%">End Date</th>
						<!-- <th width="15%">Added By</th> -->
						<th width="15%"><span class="pull-right">Created at</span></th>
					</tr>
					<?php if( !empty( $job_positions ) ){ foreach( $job_positions as $position ) { ?>
						<tr>
							<td><?php echo $position->job_title; ?></td>
							<td><?php echo $position->position_type; ?></span></td>
							<td><?php echo $position->department_name; ?></td>
							<td><?php echo ( valid_date( $position->job_start_date ) ) ? date( 'd-m-Y', strtotime( $position->job_start_date ) ) : ''; ?></td>
							<td><?php echo valid_date( $position->job_end_date ) ? date( 'd-m-Y', strtotime( $position->job_end_date ) ) : 'To present...'; ?></td>
							<!-- <td><?php echo $position->created_by; ?></td> -->
							<td><span class="pull-right"><?php echo date( 'd-m-Y H:i:s', strtotime( $position->date_created ) ); ?></span></td>
						</tr>
					<?php } }else{ ?>
						<tr>
							<td colspan="5"><?php echo $this->config->item('no_records'); ?></td>
						</tr>
					<?php } ?>
				</table>
			<?php } ?>
		</div>		
	</div>
</div>
<?php } ?>

<script>
	$(document).ready(function(){
		
		$( '#position_type' ).change( function(){
			var posType = $('option:selected', this).val();
			if( ( posType.length > 0 ) && ( posType != 'Permanent' ) ){
				$( '.position-end-date' ).slideDown();
				$( '#position-end-date' ).addClass( 'required' );
			}else{
				$( '.position-end-date' ).slideUp();
				$( '#position-end-date' ).removeClass( 'required' );
				$( '#position-end-date' ).val( '' );
				$( '#position-data-errors' ).text( '' );
			}
		});
		
		//** Validate any inputs that have the required class, if empty return the name attribute **/
		function check_inputs( containerClass ){
			
			var result = false;
			var panel  = "." + containerClass;
			
			$( $( panel + " .required" ).get().reverse() ).each( function(){
				var fieldName  = '';
				var inputValue = $( this ).val();
				if( ( inputValue == false ) || ( inputValue == '' ) || ( inputValue.length == 0 ) ){
					fieldName = $(this).attr( 'name' );
					fieldName2 = $(this).data( 'field_name' );
					result    = fieldName;					
					return result;
				}
			});
			return result;
		}
		
		//Submit form for processing
		$( '#update-position-btn' ).click( function( event ){
		
			var inputs_state = check_inputs( 'position-info' );
			
			if( inputs_state ){
				//If name attribute returned, auto focus to the field and display arror message
				$( '[name="'+inputs_state+'"]' ).focus();
				var labelText = $( '[name="'+inputs_state+'"]' ).parent().find('label').text();
				$( '#position-data-errors' ).text( ucwords( labelText ) +' is a required' );
				return false;
			}

			event.preventDefault();
			var formData = $(this).closest('form').serialize();
			swal({
				title: 'Update position info?',
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
	});
</script>