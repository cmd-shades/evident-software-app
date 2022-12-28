<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="row">
			<form id="updateForm" style="display: block; float: left;">
				<input type="hidden" name="postdata[contract_id]" value="<?php echo $profile_data[0]->contract_id; ?>" />
				<input type="hidden" name="postdata[account_id]" value="<?php echo $profile_data[0]->account_id; ?>" />
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="x_panel tile fixed_height_340">
						<legend>Update Contract Details</legend>
						<div class="input-group form-group">
							<label class="input-group-addon">Contract Name</label>
							<input name="postdata[contract_name]" type="text" class="form-control" id="contract_name" value="<?php echo ( !empty( $profile_data[0]->contract_name ) ) ? ( $profile_data[0]->contract_name ) : '' ; ?>" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Contract Type</label>
							<select name="postdata[contract_type_id]" class="form-control" id="contract_type_id" />
								<option value="">Please select the type of the contract</option>
								<?php if( !empty( $contract_types ) ){
									foreach( $contract_types as $row ){ ?>
										<option value="<?php echo $row->type_id; ?>" <?php echo ( !empty( $profile_data[0]->contract_type_id ) && ( $profile_data[0]->contract_type_id == $row->type_id ) ) ? ( 'selected="selected"' ) : '' ; ?>><?php echo ucwords( $row->type_name ); ?></option>
									<?php
									}
								} else { ?>
									<option value="8">Assets Management</option>
									<option value="9">Emergency Light</option>
								<?php } ?>
							</select>
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Contract Status</label>
							<select name="postdata[contract_status_id]" class="form-control" id="contract_status_id" />
								<option value="">Please, select the status of the contract</option>
								<?php if( !empty( $contract_statuses ) ){
									foreach( $contract_statuses as $row ){ ?>
										<option value="<?php echo $row->status_id; ?>" <?php echo ( !empty( $profile_data[0]->contract_status_id ) && ( $profile_data[0]->contract_status_id == $row->status_id ) ) ? ( 'selected="selected"' ) : '' ; ?>><?php echo ucwords( $row->status_name ); ?></option>
									<?php
									}
								} else { ?>
									<option value="16">Awaiting Action Default</option>
								<?php } ?>
							</select>
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Contract Leader</label>
							<select name="postdata[contract_lead_id]" type="text" class="form-control" id="contract_lead_id">
								<option value="">Please, select the person, who is leading the contract.</option>
								<?php if( !empty( $contract_leaders ) ){
									asort( $contract_leaders );
									foreach( $contract_leaders as $row ){ ?>
										<option value="<?php echo $row->id ?>" <?php echo ( !empty( $profile_data[0]->contract_lead_id ) && ( $profile_data[0]->contract_lead_id == $row->id ) ) ? ( 'selected="selected"' ) : '' ; ?>><?php echo ucwords( $row->first_name.' '.$row->last_name ); ?></option>
									<?php
									}
								} else { ?>
									<option value="">Please, add users.</option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>

				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="x_panel tile fixed_height_340">
						<div class="input-group form-group">
							<label class="input-group-addon">Contract Start Date</label>
							<input type="text" name="postdata[start_date]" value="<?php echo ( !empty( $profile_data[0]->start_date ) && !in_array( $profile_data[0]->start_date, array( "0000-00-00", "1970-01-01" ) ) ) ? ( date( 'd/m/Y', strtotime( $profile_data[0]->start_date ) ) ) : '' ; ?>" class="form-control datetimepicker" data-date-format="DD/MM/Y"  />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Contract End Date</label>
							<input type="text" name="postdata[end_date]" value="<?php echo ( !empty( $profile_data[0]->end_date ) && !in_array( $profile_data[0]->end_date, array( "0000-00-00", "1970-01-01" ) ) ) ? ( date( 'd/m/Y', strtotime( $profile_data[0]->end_date ) ) ) : '' ; ?>" class="form-control datetimepicker" data-date-format="DD/MM/Y" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Contract Description</label>
							<textarea name="postdata[description]" type="text" class="form-control" rows="4" cols="50" id="description"><?php echo ( !empty( $profile_data[0]->description ) ) ? ( $profile_data[0]->description ) : '' ; ?></textarea>
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Contract Note</label>
							<textarea name="postdata[last_note]" type="text" class="form-control" rows="1" cols="50" id="last_note"><?php echo ( !empty( $profile_data[0]->last_note ) ) ? ( $profile_data[0]->last_note ) : '' ; ?></textarea>
						</div>
						
						<?php if( $this->user->is_admin || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
							<div class="row col-md-6 col-sm-6 col-xs-12" style="margin-top: 50px;">
								<button id="updateContractBtn" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="submit">Update Contract</button>
							</div>
						<?php } else { ?>
							<div class="row col-md-6 col-sm-6 col-xs-12" style="margin-top: 50px;">
								<button class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>
							</div>
						<?php } ?>
			</form>
						<?php if( $this->user->is_admin || !empty( $permissions->can_delete ) || !empty( $permissions->is_admin ) ){ ?>
							<form id="deleteContractForm" class="col-md-6 col-sm-6 col-xs-12 pull-right">
								<div class="">
									<input type="hidden" name="postdata[contract_id]" value="<?php echo $profile_data[0]->contract_id; ?>" />
									<input type="hidden" name="postdata[account_id]" value="<?php echo $profile_data[0]->account_id; ?>" />
									<button type="submit" class="btn btn-sm btn-primary red_shadow" id="deleteButton" <?php echo ( !empty( $profile_data[0]->archived ) && ( $profile_data[0]->archived == 1 ) ) ? 'disabled = "disabled"' : '' ; ?> >Delete Contract</button>
								</div>
							</form>
						<?php } ?>
					</div>
				</div>
		</div>
	</div>
</div>

<script>
 $( document ).ready( function(){

	$( '#updateContractBtn' ).click( function( event ){
		event.preventDefault();
		var formData = $( '#updateForm' ).serialize();
		swal({
			title: 'Confirm contract update?',
			// type: 'question',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function( result ) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/contract/update/' ); ?>",
					method:"POST",
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
						} else {
							swal({
								type: 'warning',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 3000
							})
						}
						window.setTimeout( function(){
							location.reload();
						}, 3000 );
					}
				});
			} else {
				console.log( 'fail' );
			}
		}).catch( swal.noop )
	});
	
	
	$( '#deleteButton' ).click( function( e ){
		e.preventDefault();
		var formData = $( '#deleteContractForm' ).serialize();
		swal({
			title: 'Confirm contract delete?',
			// type: 'question',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function( result ) {
			if ( result.value ) {
				$.ajax({
					url:'<?php echo base_url( "webapp/contract/delete_contract/" ); ?>',
					method: "POST",
					data:formData,
					dataType: 'json',
					success:function( data ){
						if( data.status == 1 || data.status == true){
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 3000
							})
							window.setTimeout( function(){
								window.location.href =  '<?php echo base_url( "webapp/contract/contracts" ); ?>';
							}, 3000 );
						} else {
							swal({
								type: 'warning',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 3000
							})
						}
					}
				});
			} else {
				console.log( 'fail' );
			}
		}).catch( swal.noop )
	});
});
</script>