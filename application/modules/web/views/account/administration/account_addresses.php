<div class="row">
	<div class="col-md-6 col-sm-6 col-xs-12">
		<form id="update-account-form" method="post" >
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="account_id" value="<?php echo $account_details->account_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<div class="x_panel tile has-shadow">
				<legend>Manage (Un-verified) Addresses</legend>
				<?php if ($this->user->is_admin && (in_array($this->user->id, $super_admin_list))) { ?>
					<div class="row">
						<div class="col-md-6">
							<button id="update-account-btn" class="btn btn-block btn-flow btn-success btn-next" type="button" >Update Address Record</button>					
						</div>
					</div>
				<?php } else { ?>
					<div class="row col-md-6">
						<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-warning btn-next no-permissions" type="button" disabled >Insufficient permissions</button>					
					</div>
				<?php } ?>
			</div>
		</form>
	</div>
</div>

<script>
	$( document ).ready(function(){
		
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

