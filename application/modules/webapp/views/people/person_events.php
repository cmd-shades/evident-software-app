<!-- Run the admin check if a Tab requires that you're admin to view -->
<?php if( !empty( $admin_no_access ) ){
	$this->load->view('errors/access-denied', false, false, true );
}else{ ?>
	<div class="row">
		<?php if( $this->user->is_admin || !empty( $permissions->can_add ) || !empty( $permissions->is_admin ) ){ ?>
		<div class="col-md-4 col-sm-4 col-xs-12">
			<div class="x_panel tile has-shadow">
				<legend>Add New Events</legend>
				<p>Content coming soon...</p>
			</div>		
		</div>
		<?php } ?>
		
		<?php if( $this->user->is_admin || !empty( $permissions->can_view ) || !empty( $permissions->is_admin ) ){ ?>
		<div class="col-md-8 col-sm-8 col-xs-12">
			<div class="x_panel tile has-shadow">
				<legend>Existing Events</legend>
				<p>Content coming soon...</p>
			</div>		
		</div>
		<?php } ?>
	</div>
<?php } ?>
<script>
	$(document).ready(function(){

	});
</script>