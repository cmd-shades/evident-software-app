<div class="rows">
	<div class="col-md-6 col-sm-6 col-xs-12">
		<form id="update-account-form" method="post" >
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="account_id" value="<?php echo $account_details->account_id; ?>" />			
			<div class="x_panel tile has-shadow">
				<legend>Update Account Details</legend>
				<div class="input-group form-group">
					<label class="input-group-addon">Account Name</label>
					<input name="account_name" class="form-control" type="text" placeholder="Account Name" value="<?php echo $account_details->account_name; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Account Reference</label>
					<input class="form-control" type="text" placeholder="Account Reference" value="<?php echo $account_details->account_reference; ?>" readonly />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Account First Name</label>
					<input name="account_first_name" class="form-control" type="text" placeholder="Account Holder First Name" value="<?php echo $account_details->account_first_name; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Account Last Name</label>
					<input name="account_last_name" class="form-control" type="text" placeholder="Account Holder Last Name" value="<?php echo $account_details->account_last_name; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Account Email</label>
					<input name="account_email" class="form-control" type="text" placeholder="Account Email" value="<?php echo $account_details->account_email; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Account Status</label>
					<select name="account_status" class="form-control">
						<option>Please select</option>
						<option value="Active" <?php echo (strtolower($account_details->account_status) == 'active') ? 'selected=selected' : ''; ?> >Active</option>
						<option value="Closed" <?php echo (strtolower($account_details->account_status) == 'closed') ? 'selected=selected' : ''; ?> >Closed</option>
						<option value="Suspended" <?php echo (strtolower($account_details->account_status) == 'suspended') ? 'selected=selected' : ''; ?> >Suspended</option>
						<option value="Trial" <?php echo (strtolower($account_details->account_status) == 'trial') ? 'selected=selected' : ''; ?> >Trial</option>
					</select>	
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Is Active</label>
					<select name="is_active" class="form-control">
						<option>Please select</option>
						<option value="1" <?php echo (strtolower($account_details->is_active) == 1) ? 'selected=selected' : ''; ?> >Yes</option>
						<option value="0" <?php echo (strtolower($account_details->is_active) != 1) ? 'selected=selected' : ''; ?> >No</option>						
					</select>	
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Membership Number</label>
					<input class="form-control" type="text" placeholder="Account Membership Number" value="<?php echo $account_details->account_membership_number; ?>" readonly/>
				</div>
				<br/>
				<?php if ($this->user->is_admin && (in_array($this->user->id, $super_admin_list))) { ?>
					<div class="row">
						<div class="col-md-6">
							<button id="update-account-btn" class="btn btn-block btn-flow btn-success btn-next" type="button" >Update Account</button>					
						</div>
					</div>
				<?php } else { ?>
					<div class="row col-md-6">
						<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>					
					</div>
				<?php } ?>
			</div>
		</form>
	</div>
	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Account Headquarters</legend>
			<table style="width:100%">
				<tr>
					<th width="50%">Account Postcode</th><td width="50%"><?php echo (!empty($account_details->account_postcodes)) ? $account_details->account_postcodes : "" ; ?></td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="full-width">
							<iframe width="100%" height="280" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%&height=280&hl=en&q=<?php echo (!empty($account_details->account_postcodes)) ? str_replace(" ", "+", $account_details->account_postcodes) : "" ; ?>&ie=UTF8&t=&z=16&iwloc=B&output=embed"></iframe>
						</div>				
					</td>
				</tr>
			</table>
		</div>
	</div>
	
</div>

<script>
	$(document).ready(function(){
		
		//Submit form for processing
		$( '#update-account-btn' ).click( function( event ){
			event.preventDefault();
			var formData = $('#update-account-form').serialize();
			swal({
				title: 'Confirm account update?',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/account/update_account/'.$account_details->account_id); ?>",
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

