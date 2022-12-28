<style>
	.swal2-container {
	  z-index: 10000;
	}

	table tr th,table tr td{
		padding: 5px 0; 
	}
	
	.accordion .panel:hover {
		background: transparent;
	}
	
	.show_toggled{
		display: none;
	}
	
	@media (max-width: 480px) {
		.btn-info{
			margin-bottom:10px;
		}
	}
</style>

<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<?php if( !empty( $asset_type_details ) ) { ?>
			<div class="profile-details-container">
				<div class="row alert alert-ssid bg-blue" role="alert">
					<div class="profile-overview">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<legend>Asset Type Profile (<?php echo $asset_type_details->asset_type_id; ?>) <span class="pull-right"><span class="edit-asset-type pointer" title="Click to edit this Asset Name profile"><i class="fas fa-pencil-alt"></i></span><span class="hide delete-asset-type pointer" data-asset_type="<?php echo $asset_type_details->asset_type_id; ?>" title="Click to delete this Asset Name profile" ><i class="far fa-trash-alt"></i></span></span></legend>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<table style="width:100%;">
								<tr>
									<td width="25%"><i class="hide fa fa-at text-bold"></i> <label>Asset Type</label></td>
									<td width="75%"><?php echo ucwords( $asset_type_details->asset_type ); ?></td>
								</tr>
								<tr>
									<td width="25%"><i class="hide fa fa-at text-bold"></i> <label>Asset Group</label></td>
									<td width="75%"><?php echo ucwords( $asset_type_details->asset_group ); ?> <?php echo ( !empty( $asset_sub_category ) ) ? '<small>('.$asset_sub_category.')</small>' : ''; ?></td>
								</tr>
								<tr>
									<td width="25%"><i class="hide fa fa-at text-bold"></i> <label>Asset Category</label></td>
									<td width="75%"><?php echo ucwords( $asset_type_details->category_name ); ?></td>
								</tr>
								<tr>
									<td width="25%"><i class="hide fa fa-at text-bold"></i> <label>Asset Description</label></td>
									<td width="75%"><?php echo ucwords( $asset_type_details->asset_type_desc ); ?></td>
								</tr>
							</table>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<table style="width:100%;">
								<tr>
									<td width="25%"><label>Status</label></td>
									<td width="75%"><?php echo ( $asset_type_details->is_active == 1 ) ? 'Active <i class="far fa-check-circle"></i>' : 'Disabled <i class="far fa-times-circle text-red"></i>'; ?></td>
								</tr>												
								<tr>
									<td width="25%"><label>Date Created</label></td>
									<td width="75%"><?php echo ( !empty( $asset_type_details->record_created_by ) ) ? ucwords( $asset_type_details->record_created_by ) : 'Data not available'; ?> <?php echo ( valid_date( $asset_type_details->date_created ) ) ? '@ '.date( 'd-m-Y H:i:s', strtotime( $asset_type_details->date_created ) ) : ''; ?></td>
								</tr>
								<tr>
									<td width="25%"><label>Date Last Modified</label></td>
									<td width="75%"><?php echo ( !empty( $asset_type_details->record_modified_by ) ) ? ucwords( $asset_type_details->record_modified_by ) : 'No updates yet'; ?> <?php echo ( valid_date( $asset_type_details->last_modified ) ) ? '@ '.date( 'd-m-Y H:i:s', strtotime( $asset_type_details->last_modified ) ) : ''; ?></td>
								</tr>
							</table>							
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			
			<div class="clearfix"></div>
			
			<div class="row">
				<div class="row">
					<?php if( !empty( $asset_type_attributes ) ){ ?>
						<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="x_panel tile has-shadow">
								<legend>Asset Type Attributes <span class="pull-right pointer add-new-attribute"><i class="fas fa-plus" title="Add new Attribute to this Asset type" ></i></span></legend>
								<div class="accordion" id="accordion-risks" role="tablist" aria-multiselectable="true">
									<div class="row">
										<?php if( !empty( $asset_type_attributes )){ $counter = 1; ?>
											<div class="col-md-12 col-sm-12 col-xs-12">
												
											</div>
										<?php } } ?>	
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
			
			<div class="row">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						
						<div class="x_panel tile has-shadow">
							<legend>Asset Type Attributes <span class="pull-right pointer add-new-attribute"><i class="fas fa-plus" title="Add new Attribute to this Asset type" ></i></span></legend>
							<div class="accordion" id="accordion" role="tablist" aria-multiselectable="true">
								<?php if( !empty( $asset_type_attributes )){ $counter = 1; ?>
									<div id="collapse1" class="panel-collapse no-bg no-background" role="tabpanel" aria-labelledby="heading1" >
										<div class="panel-body">
											<div class="table-responsive">
												<div class="col-md-12 col-sm-12 col-xs-12">
													<table class="table">
														<thead>
															<tr>
																<th width="35%">Attribute / Label Name</th>
																<th width="15%">Response Type</th>
																<th width="15%">Response Options</th>
																<th width="10%" class="text-center" >Status</th>
																<!-- <th width="15%" class="text-center" >Order</th> -->
																<th width="10%"><span class="pull-right">Action</span></th>
															</tr>
														</thead>
														<?php foreach( $asset_type_attributes as $section => $attribute ){ $counter++; ?>
															<tr>
																<td width="35%"><?php echo $attribute->attribute_name; ?></td>
																<td width="15%"><span title="<?php echo $attribute->response_type; ?>" ><?php echo $attribute->response_type; ?></span></td>
																<td width="15%"><span title="" ><?php echo ( is_array( $attribute->response_options ) ) ? implode( " | ", $attribute->response_options ) : ( is_object( $attribute->response_options ) ? json_encode( $attribute->response_options ) : $attribute->response_options ) ; ?></span></td>
																<td width="10%" class="text-center" ><?php echo ( $attribute->is_active == 1 ) ? '<i class="far fa-check-circle text-green" title="This Attribute is active" ></i>' : '<i class="far fa-times-circle text-red" title="This Attribute is currently disabled" ></i>'; ?></td></td>
																<!-- <td width="15%" class="text-center" ><span title="<?php echo $attribute->ordering; ?>" ><?php echo $attribute->ordering; ?></span></td> -->
																<td width="10%"><span class="pull-right"><span class="edit-asset-attribute pointer" data-attribute_id="<?php echo $attribute->attribute_id; ?>" data-section_name="<?php echo $section; ?>" title="Click to Edit this record"><i class="far fa-edit"></i> Edit</span> &nbsp; &nbsp; <span class="delete-asset-attribute pointer text-red" data-attribute_id="<?php echo $attribute->attribute_id; ?>" data-section_name="<?php echo $section; ?>" title="Click to Delete this record" ><i class="far fa-trash-alt"></i> Delete</span></span></td>
															</tr>
														<?php } ?>	
													</table>
												</div>
											</div>
										</div>
									</div>
									<br/>
								<?php } else{ ?>
									<div class="no-results">
										<div><?php echo $this->config->item('no_records'); ?></span> &nbsp;Click the button to add a new attribute <span><button title="Add new attribute for this Asset type" style="width:8%" class="add-new-attribute btn btn-sm btn-success success-shadow"><i class="fas fa-plus-circle" style="font-size: 18px;"></i></button></span></div>							
									</div>
								<?php } ?>	
							</div>
							
						</div>
					</div>
				</div>
			</div>
			
			<!-- Modal for VIewing and Editing an existing Asset Name -->
			<div id="edit-asset-type-modal" class="modal fade edit-asset-type-modal" tabindex="-1" role="dialog" aria-hidden="true">
				<form id="edit-asset-type-form" >
					<div class="modal-dialog modal-md">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
								<h4 class="modal-title" id="myModalLabel">Update Asset Type Profile (<?php echo $asset_type_details->asset_type_id; ?>)</h4>
								<small id="feedback-message"></small>
							</div>

							<div class="modal-body">
								<div class="input-group form-group">
									<label class="input-group-addon">Asset Type</label>
									<input id="asset_type" name="asset_type" class="form-control" type="text" placeholder="Asset type" value="<?php echo $asset_type_details->asset_type; ?>" />
								</div>
								<div class="input-group form-group">
									<label class="input-group-addon">Asset Group</label>
									<select id="asset_group" name="asset_group" class="form-control">
										<option value="">Please select group</option>
										<?php if( !empty( $asset_groups ) ) { foreach( $asset_groups as $group_name => $group_desc ) { ?>
											<option value="<?php echo $group_name; ?>" <?php echo ( strtolower( $group_name ) == strtolower( $asset_type_details->asset_group ) ) ? 'selected=selected' : ''; ?> ><?php echo $group_desc; ?></option>
										<?php } } ?>
									</select>
								</div>
								<div class="input-group form-group">
									<label class="input-group-addon">Asset Category</label>
									<select id="category_id" name="category_id" class="form-control">
										<option value="">Please select group</option>
										<?php if( !empty( $asset_categories ) ) { foreach( $asset_categories as $k => $category ) { ?>
											<option value="<?php echo $category->category_id; ?>" <?php echo ( strtolower( $category->category_id ) == strtolower( $asset_type_details->category_id ) ) ? 'selected=selected' : ''; ?> ><?php echo $category->category_name; ?></option>
										<?php } } ?>
									</select>
								</div>

								<div class="form-group">
									<label class="input-group push-10-left">Asset Description</label>
									<textarea name="asset_type_desc" class="form-control" type="text" value="" style="width:100%; height:78px" ><?php echo $asset_type_details->asset_type_desc; ?></textarea>
								</div>
								
								<div class="input-group form-group">
									<label class="input-group-addon">Status</label>
									<select name="is_active" class="form-control">
										<option>Please select</option>
										<option value="1" <?php echo ( $asset_type_details->is_active == 1 ) ? 'selected=selected' : '' ?> >Active</option>
										<option value="0" <?php echo ( $asset_type_details->is_active != 1 ) ? 'selected=selected' : '' ?> >In-active</option>
									</select>
								</div>
							</div>
							
							<div class="modal-footer">
								<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">&nbsp;&nbsp;&nbsp;&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;</button>
								<button id="update-asset-type-btn" type="button" class="btn btn-sm btn-success">Save Changes</button>
							</div>
						</div>
					</div>
				</form>
			</div>
			
			<!-- Modal for adding a new Attribute. Should this be a modal? -->
			<div class="modal fade add-attribute-modal" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title" id="myAddQuestModalLabel">Add Asset Type attribute</h4>						
						</div>
						<?php include( 'asset_type_attribute_add_new.php' ); ?>
					</div>
				</div>
			</div>
			
			<!-- Modal for Editing a Attribute -->
			<div class="modal fade edit-asset-attribute-modal" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<form id="edit-asset-attribute-form" >
							<input type="hidden" name="attribute_id" value="" />
							<input type="hidden" name="page" value="details" />
							<input type="hidden" name="asset_type_id" value="<?php echo $asset_type_details->asset_type_id; ?>" />
					
							<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
								<h4 class="modal-title" id="myEditQuestModalLabel">Edit Asset Attribute ID: <span id="attribute_id">Data loading....</span></h4>
								<small id="attribute-feedback-message"></small>								
							</div>

							<div class="modal-body asset-attribute-body">
								
							</div>

							<div class="modal-footer">
								<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">&nbsp;&nbsp;&nbsp;&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;</button>
								<button id="edit-asset-attribute-btn" type="button" class="btn btn-sm btn-success">Save Changes</button>
							</div>
						</form>
					</div>
				</div>
			</div>

			<?php }else{ ?>
				<div class="row">
					<div><?php echo $this->config->item('no_records'); ?> </div>
					<div><span class="pull-right pointer"><i class="fas fa-plus" title="Add new Attribute to this Asset type" ></i></span></div>
				</div>
			<?php } ?>	
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){
	
		var assetGrp = $( '#asset_group option:selected' ).val().toLowerCase();
		if( assetGrp == 'asset' ){
			$( '.asset-types' ).show();
		}
		
		$( '.asset-attribute-body' ).on( '#response_type, change', function(){
			
			var respType 	= $( 'option:selected', this ).data( 'resp_type' );
			var respTypeAlt = $( 'option:selected', this ).data( 'resp_type_alt' );
			var respDesc 	= $( 'option:selected', this ).data( 'resp_desc' );

			$( '.resp-extra-options' ).hide();
			$( '#selected-option' ).text( '' );

			$( '.resp-type-options' ).hide();
			$( '.resp-type-options' ).hide();
			$( '.resp_' + respType ).show();
		});

		
		// SECTION QUICK ADD
		$( '.asset-attribute-body' ).on( 'click', '#asset-section-quick-add', function(){
			$(".profile-add-section-modal").modal( "show" );
		});
		
		$('#edit-asset-type-modal').on('hidden.bs.modal', function () { 
			location.reload();
		});
		
		$( '.section-container-bar' ).click( function(){
			$( this ).closest( 'div' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');			
		});
		
		$( '.edit-asset-type' ).click( function(){
			$(".edit-asset-type-modal").modal("show");
		} );
		
		$( '.add-new-attribute' ).click( function(){
			$(".add-attribute-modal").modal("show");
		} );
		
		//VIEW AND EDIT AN ASSET ATTRIBUTE
		$( '.edit-asset-attribute' ).click( function(){
			
			var attributeId  = $( this ).data( 'attribute_id' );
			var assetTypeId = "<?php echo $asset_type_details->asset_type_id; ?>";

			if( attributeId.length != 0 ){
				
				$( '[name="attribute_id"]' ).val( attributeId );
				$( '#attribute_id' ).text( attributeId );
				
				$.ajax({
					url:"<?php echo base_url('webapp/asset/view_attribute_data/' ); ?>" + attributeId,
					method:"post",
					data:{ attribute_id:attributeId, asset_type_id:assetTypeId },
					dataType: 'json',
					success:function( data ){
						if( data.status == 1 ){
							$( '.asset-attribute-body' ).html( data.attribute_data );
							$( '.edit-asset-attribute-modal' ).modal( 'show' );						
						}else{
							swal({
								type: 'error',
								title: data.status_msg
							})
						}		
					}
				});
			}else{
				swal({
					type: 'error',
					title: 'Oops! Something went wrong, please refresh this page.'
				})
			}

		} );
		
		// SUBMIT EDIT ATTRIBUTE FORM
		$( '#edit-asset-attribute-btn' ).click( function() {
			$( '#attribute-feedback-message' ).show();
			$( '#attribute-feedback-message' ).text( '' );

			event.preventDefault();
			
			var attributeId 	 = $( '.asset-attribute-body [name="attribute_id"] ' ).val();
			var attributeName = $( '.asset-attribute-body [name="attribute"] ' ).val();
			var sectionName  = $( '.asset-attribute-body #section_name option:selected' ).val();
			
			if( attributeName.length == 0 ){
				$( '[name="attribute"]' ).focus().css("border","1px solid red");
				$( '#attribute-feedback-message' ).html( '<span class="text-red">Attribute label is required</span>' );
				return false;
			}
			
			var formData = $(this).closest('form').serialize();
			
			swal({
				title: 'Confirm update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/asset/update_attribute/'.$asset_type_details->asset_type_id ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							$( '#attribute-feedback-message' ).html( data.status_msg );								
							setTimeout(function(){
								$( '#attribute-feedback-message' ).fadeOut( 'slow' );
								$('.edit-asset-attribute-modal').modal( 'hide' );
								var new_url = window.location.href.split('?')[0];
								window.location.href = new_url + "?toggled=" + sectionName;
							},3000 );
						}
					});
				}
			}).catch( swal.noop )
			
		});
		
		// DELETE OR ARCHIVE ASSET ATTRIBUTE
		$( '.delete-asset-attribute' ).click( function(){
			
			var attributeId  	= $( this ).data( 'attribute_id' );
			var sectionName  	= $( this ).data( 'section_name' );
			
			if( attributeId == 0 || attributeId == undefined ){
				swal({
					title: 'Oops! Something went wrong',
					type: 'error',
					text: 'Please reload the page and try again!',
				})
			}

			swal({
				title: 'Confirm delete Attribute?',
				type: 'attribute',
				text: 'This is an irreversible action',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/asset/delete_attribute/' ); ?>" + attributeId,
						method:"POST",
						data:{ page:"details", attribute_id:attributeId },
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2500
								})
								window.setTimeout(function(){
									var new_url = window.location.href.split('?')[0];
									window.location.href = new_url + "?toggled=" + sectionName;
								} ,1000);
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						}
					});
				}
			}).catch( swal.noop )
		} );
	
		// REMOVE RED BORDER WHEN USER STARTS TO TYPE AGAIN
		$( '[name="asset_type"]' ).keyup( function(){
			$( this ).css("border","1px solid #ccc");
			$( '#feedback-message' ).show().text( '' );			
		} );
	
		// SUBMIT FORM FOR PROCESSING
		$( '#update-asset-type-btn' ).click( function( event ){
			
			$( '#feedback-message' ).show();
			$( '#feedback-message' ).text( '' );

			event.preventDefault();
			
			var assetName 	= $( '#asset_type' ).val();
			var assetGroup 	= $( '#asset_group option:selected').val();
			var assetCat   	= $( '#category_id option:selected' ).val();
			
			if( assetName.length == 0 ){
				$( '[name="asset_type"]' ).focus().css("border","1px solid red");
				$( '#feedback-message' ).html( '<span class="text-red">Asset type is required</span>' );
				return false;
			}
			
			if( assetGroup.length == 0 ){
				$( '[name="asset_group"]' ).focus().css("border","1px solid red");
				$( '#feedback-message' ).html( '<span class="text-red">Asset Group is required</span>' );
				return false;
			}
			
			if( assetCat.length == 0 ){
				$( '[name="category_id"]' ).focus().css("border","1px solid red");
				$( '#feedback-message' ).html( '<span class="text-red">Asset category is required</span>' );
				return false;
			}
			
			var formData = $(this).closest('form').serialize();
			
			swal({
				title: 'Confirm update?',
				// type: 'attribute',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/asset/update_asset_type/'.$asset_type_details->asset_type_id ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							$( '#feedback-message' ).html( data.status_msg );								
							setTimeout(function(){
								$( '#feedback-message' ).fadeOut( 'slow' );
							},3000 );
						}
					});
				}
			}).catch( swal.noop )

		});

		$( '.asset-attribute-body' ).on( 'change', '.extra_info_trigger', function(){
			var respType 	= $( this ).data( 'response_type' );
			var selectdOpt  = $( 'option:selected', this ).val();
			if( selectdOpt.length > 0 ){
				var newText = 'If "' + selectdOpt + '", please provide further info.';
				$( '#extra-info-selected-'+respType ).val( newText );
			}
		} );
		
	});
</script>

