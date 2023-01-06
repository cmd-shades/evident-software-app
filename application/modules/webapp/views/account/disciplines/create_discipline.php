<style>
	.profile-details-container label {
		color: #555 !important;
	}
</style>

<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row">
					<div class="row">
						<div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12">
							<div class="x_panel tile has-shadow">
								<form id="create-discipline-form" class="form-horizontal">
									<input type="hidden" name="page" value="details" />
									<div class="col-md-12 col-xs-12">
										<legend>Discipline Details</legend>
										<div class="input-group form-group">
											<label class="input-group-addon">Discipline Name&nbsp;*</label>
											<input name="discipline_name" class="form-control required" type="text" placeholder="Discipline Name" value="" required />
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Discipline</label>
											<input name="discipline_desc" class="form-control" type="text" placeholder="Discipline Description" value="" />
										</div>

										<div class="input-group form-group">
											<label class="input-group-addon">Discipline Colour</label>
											<input name="discipline_colour" class="form-control" type="text" placeholder="Status Colour e.g. Grey" value="" />
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Colour Hex</label>
											<input name="discipline_colour_hex" class="form-control" type="text" placeholder="Status Colour Hex e.g. #CCC" value="" />
										</div>

										<div class="input-group form-group">
											<label class="input-group-addon">Status Icon</label>
											<input name="discipline_icon" class="form-control" type="text" placeholder="Status Icon e.g. " value="" />
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Image Url</label>
											<input name="discipline_image_url" class="form-control" type="text" placeholder="Image url" value="" />
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Discipline Category</label>
											<select name="category_id" class="form-control">
												<option>Please select</option>
												<option value="1">Fire</option>
												<option value="2">Fire &amp;  Securty</option>
												<option value="3">Water</option>
												<option value="4">Visual Securty</option>
											</select>
										</div>
									</div>
									<div class="col-md-12 col-xs-12">
										<div class="row">
											<div class="col-md-6">
												<?php if( $this->user->is_admin && ( in_array( $this->user->id, $this->super_admin_list ) ) ){ ?>
													<button class="btn btn-sm btn-block btn-flow btn-success btn-next" type="submit">Create Discipline</button>
												<?php } else { ?>
													<button class="btn btn-sm btn-block btn-flow btn-success btn-next" type="button" disabled>Create Discipline</button>
												<?php } ?>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

$( "form#create-discipline-form" ).submit( function( e ){

	e.preventDefault();

	var formData = $( this ).serialize();

	swal({
		title: 'Confirm Discipline creation?',
		showCancelButton: true,
		confirmButtonColor: '#5CB85C',
		cancelButtonColor: '#9D1919',
		confirmButtonText: 'Yes'
	}).then( function( result ){
		if( result.value ) {
			$.ajax({
				url:"<?php echo base_url( 'webapp/account/create_discipline/' ); ?>",
				method: "POST",
				data: formData,
				dataType: 'json',
				success:function( data ){
					if( ( data.status == 1 ) && ( data.discipline.discipline_id !== '' ) ){
						var newDisciplineID = data.discipline.discipline_id;
						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 2000
						})
						window.setTimeout( function(){
							location.href = "<?php echo base_url( 'webapp/account/discipline_profile/' ) ?>" + newDisciplineID;
						}, 1000);
					} else {
						swal({
							type: 'error',
							title: data.status_msg
						})
					}
				}
			});
		}
	}).catch( swal.noop )
});
</script>
