<div class="row">
	<?php if( $this->user->is_admin || !empty( $permissions->can_add ) || !empty( $permissions->is_admin ) ){ ?>
	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Add New Address</legend>
		</div>
	</div>
	<?php } ?>
	
	<?php if( $this->user->is_admin || !empty( $permissions->can_add ) || !empty( $permissions->can_view ) || !empty( $permissions->is_admin ) ){ ?>
	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Existing Addresses</legend>
		</div>
	</div>
	<?php } ?>
</div>

<script>
	$(document).ready(function(){
		
		//Submit form for processing
		$( '#update-user-btn' ).click( function( event ){
					
			event.preventDefault();
			var formData = $('#user-details-form').serialize();
			swal({
				title: 'Confirm site update?',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then((result) => {
				$.ajax({
					url:"<?php echo base_url('webapp/user/update_user/'.$site_details->site_id ); ?>",
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
			}).catch(swal.noop)
		});
	});
</script>