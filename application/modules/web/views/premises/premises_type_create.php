<style>
	.input-group-addon {
		min-width: 190px;
	}
</style>

<div>
	<div class="row">
		<?php if (!empty($message)) { ?>
			<div class="col-md-12 col-sm-12 col-xs-12 text-red" style="margin-bottom:10px;" ><?php echo $message; ?></div>
		<?php } ?>
		<!--  Single Premises creation -->
		<div class="col-md-offset-3 col-md-6 col-sm-offset-3 col-sm-6 col-xs-12">
			<div class="row" style="margin-top:5px">
			<div class="col-md-12 col-sm-12 col-xs-12">
						<legend>Add New Premises Type</legend>
					</div>
				<form id="premises-type-creation-form" method="post" >
					<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
					<input type="hidden"  name="page" value="details"/>
					<div class="premises_creation_panel1 col-md-12 col-sm-12 col-xs-12">
					
					<div class="x_panel tile has-shadow">
						<div class="row section-header">
								<div class="row">
									<div class="col-md-12 col-sm-12 col-xs-12">
										<div class="form-group">
											<div class="col-md-12 col-sm-12 col-xs-12">
												<legend class="legend-header">What is the premises type?</legend>
												<div class="input-group form-group">
													<label class="input-group-addon">Premises type *</label>
													<input name="premises_type" class="form-control" type="text" value="" placeholder="Premises type" required=required />
												</div>
											</div>
											
											<div class="col-md-12 col-sm-12 col-xs-12">
												<div class="input-group form-group">
													<label class="input-group-addon">Premises Description</label>
													<textarea name="premises_type_desc" class="form-control" type="text" value="" placeholder="Premises Type Description"  ></textarea>
												</div>
											
												<div class="input-group form-group">
													<label class="input-group-addon">Discipline</label>
													<select id="discipline_id" name="discipline_id" class="form-control required" data-label_text="Discipline" >
														<option value="" >Please Select a Discipline</option>
														<?php if (!empty($disciplines)) {
														    foreach ($disciplines as $k => $discipline) { ?>
															<option value="<?php echo $discipline->discipline_id; ?>" ><?php echo $discipline->account_discipline_name; ?></option>
														<?php }
														    } ?>
													</select>
												</div>
												
												<div class="input-group form-group">
													<label class="input-group-addon">Sub-address Required?</label>
													<select id="is_subaddress_required" name="is_subaddress_required" class="form-control">
														<option value="0">Please select</option>
														<option value="1" >Yes</option>
														<option value="0" >No</option>
													</select>
												</div>												
											</div>
										</div>
										
										<div class="col-md-5 col-sm-5 col-xs-12">
											<div class="form-group">
												<button id="add-premises-type-btn" class="btn btn-block btn-success" type="button" >Create Premises Type</button>
											</div>
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


<script>
	$( document ).ready(function(){	

		$( '.required' ).on( 'input', function(){
			$( '.error_message' ).text( '' );
		});


		$( '#add-premises-type-btn' ).click(function(){

			var discpLine= $( '#discipline_id option:selected' ).val();

			if( discpLine.length == 0  ){
				swal({
					type: 'error',
					title: 'Discipline field is required!'
				})
				return false;
			}

			var formData = $( "#premises-type-creation-form :input").serialize();
			$.ajax({
				url:"<?php echo base_url('webapp/premises/add_premises_type/'); ?>",
				method:"POST",
				data: formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						Swal.fire({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 2000
							  
					    }).then((result) => {
							if(data.premises_type.premises_type_id){
								 window.location = '<?php echo base_url("webapp/premises/premises_types/"); ?>'+data.premises_type.premises_type_id
							} else {
								window.location = '<?php echo base_url("webapp/premises/premises_types"); ?>'
							}						  
					    })
					}else{
						swal({
							type: 'error',
							title: data.status_msg
						})
					}
				}
			});
			return false;

		});
	});
</script>