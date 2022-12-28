<style>
	.input-group-addon {
		min-width: 190px;
	}
</style>

<div>
	<div class="row">
		<?php if( !empty( $message ) ) { ?>
			<div class="col-md-12 col-sm-12 col-xs-12 text-red" style="margin-bottom:10px;" ><?php echo $message; ?></div>
		<?php } ?>
		<!--  Single Asset creation -->
		<div class="col-md-offset-3 col-md-6 col-sm-offset-3 col-sm-6 col-xs-12">
			<div class="row" style="margin-top:5px">
			<div class="col-md-12 col-sm-12 col-xs-12">
						<legend>Add New Asset Type</legend>
					</div>
				<form id="asset-type-creation-form" method="post" >
					<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
					<input type="hidden"  name="page" value="details"/>
					<div class="asset_creation_panel1 col-md-12 col-sm-12 col-xs-12">
					
					<div class="x_panel tile has-shadow">
						<div class="row section-header">
								<div class="row">
									<div class="col-md-12 col-sm-12 col-xs-12">
										<div class="form-group">
											<div class="col-md-12 col-sm-12 col-xs-12">
												<legend class="legend-header">What is the asset type?</legend>
												<div class="input-group form-group">
													<label class="input-group-addon">Asset type *</label>
													<input name="asset_type" class="form-control" type="text" value="" placeholder="Asset type" required=required />
												</div>
											</div>

											<div class="col-md-12 col-sm-12 col-xs-12">
												<div class="input-group form-group">
													<label class="input-group-addon">Auto Generate Unique IDs?</label>
													<select name="auto_generate_unique_ids" class="form-control">
														<option>Please select</option>
														<option value="1" >Yes - Do not use Barcodes</option>
														<option value="0" selected >No - Use Evident Barcodes (recommended)</option>
													</select>
												</div>
											</div>
											
											<div class="col-md-12 col-sm-12 col-xs-12">
												<div class="input-group form-group">
													<label class="input-group-addon">Asset Description</label>
													<textarea name="description" class="form-control" type="text" value="" placeholder="Asset Type Description"  ></textarea>
												</div>
											</div>
											
											<div class="col-md-12 col-sm-12 col-xs-12">
												<div class="input-group form-group">
													<label class="input-group-addon">Discipline</label>
													<select id="discipline_id" name="discipline_id" class="form-control required" data-label_text="Discipline" >
														<option value="" >Please Select a Discipline</option>
														<?php if( !empty( $disciplines ) ) { foreach( $disciplines as $k => $discipline ) { ?>
															<option value="<?php echo $discipline->discipline_id; ?>" ><?php echo $discipline->account_discipline_name; ?></option>
														<?php } } ?>
													</select>
												</div>
											</div>
											
											<div class="col-md-12 col-sm-12 col-xs-12">
												<div class="input-group form-group">
													<label class="input-group-addon">Category</label>
													<div class="form-group has-shadow" >
														<select id="category_id" name="category_id" class="form-control required" style="width:94%">
															<option value="">Please select category</option>
															<?php if( !empty( $evidoc_categories ) ) { foreach( $evidoc_categories as $k => $category ) { ?>
																<option value="<?php echo $category->category_id; ?>" ><?php echo $category->category_name_alt; ?></option>
															<?php } } ?>
														</select>
													</div>
													<div class="pull-right" style="width:5%">
														<div style="margin-top:-10px" class="evidoc-category-quick-add pointer" title="Quick Add new category option"><span class="pull-right"><i class="fas fa-plus-circle fa-2x text-green"></i></span></div>
													</div>
												</div>
											</div>											
										</div>
										
										<div class="col-md-12 col-sm-12 col-xs-12">
											<div class="form-group input-group">
												<label class="input-group-addon">Asset Group *</label>
												<select id="asset_group" name="asset_group" class="form-control required">
													<option value="">Please select Asset Group (sub-category)</option>
													<?php if( !empty( $sub_categories ) ) { foreach( $sub_categories as $k => $sub_category ) { ?>
														<option value="<?php echo $sub_category->sub_category ?>"><?php echo $sub_category->sub_category_desc ?></option>
													<?php } } ?>
												</select>
											</div>
										</div>
										
										<div class="col-md-5 col-sm-5 col-xs-12">
											<div class="form-group">
												<button id="add-asset-type-btn" class="btn btn-block btn-success" type="button" >Add Asset Type</button>
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


<!-- Modal for adding a new category -->
<div class="modal fade add-category-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<form id="add-category-form-container" >
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title" id="myCategoryModalLabel">Add an New Category</h4>
					<span id="category-feedback-message"></span>
				</div>
				<div class="modal-body">
					<div class="row">
						<input type="hidden" name="page" value="details" />
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h4>What is the name of this Category?</h4>
							<select name="category_name" class="form-control required">
								<option value="">Please select Category</option>
								<?php if( !empty( $disciplines ) ) { foreach( $disciplines as $k => $discipline ) { ?>
									<option value="<?php echo $discipline->account_discipline_name; ?>" ><?php echo $discipline->account_discipline_name; ?></option>
								<?php } } ?>
							</select>
							<div class="form-group">
								<input name="category_name" class="form-control" type="text" value="" placeholder="Category name" required=required />
							</div>
							<h4>Please provide a description</h4>
							<div class="form-group">
								<textarea rows="4" name="description" class="form-control" type="text" value="" placeholder="Please provide a description of your Category" required=required /></textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<button id="category-quick-add-btn" class="btn btn-success btn-block" type="button">Add New Category</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
	$( document ).ready(function(){
		
		// New Category Quick add
		$( '#category-quick-add-btn' ).click(function(){

			var formData = $( "#add-category-form-container :input").serialize();

			$.ajax({
				url:"<?php echo base_url('webapp/audit/add_category/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){

						$(".add-category-modal").modal("hide");

						var categoryId 	 = data.category.category_id;
						var categoryName = data.category.category_name;
						var categoryDesc = data.category.description;

						$('#category-feedback-message').html( data.status_msg ).delay( 3000 ).fadeToggle( "slow" );

						var optionExists  = ( $('#evidoc_category_id option[value="' + categoryId + '"]').length > 0 );
						var optionExists2 = ( $('#category_id option[value="' + categoryId + '"]').length > 0 );

						if( !optionExists ){
							//Only add the new option if it doesn't already exist
							$('#evidoc_category_id').append( $('<option >').val( categoryId ).text( categoryName + ' - ' + categoryDesc ) );
						}

						if( !optionExists2 ){
							//Only add the new option if it doesn't already exist
							$('#category_id').append( $('<option >').val( categoryId ).text( categoryName + ' - ' + categoryDesc ) );
						}

						//Set selected
						$('#evidoc_category_id option[value="'+categoryId+'"]').prop( 'selected', true );
						$('#category_id option[value="'+categoryId+'"]').prop( 'selected', true );

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
		
		
		//Trigger Category modal
		$( '.evidoc-category-quick-add' ).click( function(){
			$( '.add-category-modal' ).modal( 'show' );
		} );
		

		$( '.required' ).on( 'input', function(){
			$( '.error_message' ).text( '' );
		});


		$( '#add-asset-type-btn' ).click(function(){

			var catId    = $( '#category_id option:selected' ).val();
			var assGroup = $( '#asset_group option:selected' ).val();
			var discpLine= $( '#discipline_id option:selected' ).val();

			if( assGroup.length == 0  ){
				swal({
					type: 'error',
					title: 'Asset group is required!'
				})
				return false;
			}

			if( discpLine.length == 0  ){
				swal({
					type: 'error',
					title: 'Discipline field is required!'
				})
				return false;
			}

			var formData = $( "#asset-type-creation-form :input").serialize();
			//var formData = {category_id : 76, account_id: 3, asset_type : 'test assetd1ddd1dd1d1d'}
			$.ajax({
				url:"<?php echo base_url('webapp/audit/add_asset_type/' ); ?>",
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
							if(data.asset_type.asset_type_id){
								 window.location = '<?php echo base_url("webapp/asset/asset_types/"); ?>'+data.asset_type.asset_type_id
							} else {
								window.location = '<?php echo base_url("webapp/asset/asset_types"); ?>'
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