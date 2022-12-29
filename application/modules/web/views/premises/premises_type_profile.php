<style type="text/css">
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

@media( max-width: 480px ){
	.btn-info{
		margin-bottom:10px;
	}
}

.delete-premises-type{
	margin-left: 10px;
}

.input-group-addon {
    min-width: 190px;
}

</style>

<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<?php if (!empty($premises_type_details)) { ?>

			<div class="profile-details-container">
				<div class="row alert alert-ssid bg-blue" role="alert">
					<div class="profile-overview">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<legend>Premises Type Profile (<?php echo $premises_type_details->premises_type_id; ?>) <span class="pull-right"><span class="edit-premises-type pointer" title="Click to edit this premises Type profile"><i class="fas fa-pencil-alt"></i></span><span class="delete-premises-type pointer" data-premises_type="<?php echo $premises_type_details->premises_type_id; ?>" title="Click to delete this Premises Type profile" ><i class="far fa-trash-alt"></i></span></span></legend>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<table style="width:100%;">
								<tr>
									<td width="25%"><i class="hide fa fa-at text-bold"></i> <label>Premises Type</label></td>
									<td width="75%"><?php echo (!empty($premises_type_details->premises_type)) ? ucwords($premises_type_details->premises_type) : '' ; ?></td>
								</tr>
								<tr>
									<td width="25%"><i class="hide fa fa-at text-bold"></i> <label>Premises Description</label></td>
									<td width="75%"><?php echo (!empty($premises_type_details->premises_type_desc)) ? ucfirst($premises_type_details->premises_type_desc) : '' ; ?></td>
								</tr>
								<tr>
									<td width="25%"><label>Requires Sub Address</label></td>
									<td width="75%"><?php echo ($premises_type_details->is_subaddress_required == 1) ? 'Yes' : 'No'; ?></td>
								</tr>
							</table>
						</div>

						<div class="col-md-6 col-sm-6 col-xs-12">
							<table style="width:100%;">
								<tr>
									<td width="25%"><label>Status</label></td>
									<td width="75%"><?php echo ($premises_type_details->is_active == 1) ? 'Active <i class="far fa-check-circle"></i>' : 'Disabled <i class="far fa-times-circle text-red"></i>'; ?></td>
								</tr>
								<tr>
									<td width="25%"><label>Date Created</label></td>
									<td width="75%"><?php echo (!empty($premises_type_details->record_created_by)) ? ucwords($premises_type_details->record_created_by) : 'Data not available'; ?> <?php echo (valid_date($premises_type_details->date_created)) ? '@ '.date('d-m-Y H:i:s', strtotime($premises_type_details->date_created)) : ''; ?></td>
								</tr>
								<tr>
									<td width="25%"><label>Date Last Modified</label></td>
									<td width="75%"><?php echo (!empty($premises_type_details->record_modified_by)) ? ucwords($premises_type_details->record_modified_by) : 'No updates yet'; ?> <?php echo (valid_date($premises_type_details->last_modified)) ? '@ '.date('d-m-Y H:i:s', strtotime($premises_type_details->last_modified)) : ''; ?></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>

			<div class="row">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<?php if (!empty($premises_type_attributes)) {
						    $counter = 1; ?>
							<div class="panel has-shadow">
								<div class="section-container-bar panel-heading bg-grey pointer no-radius" id="heading<?php echo number_to_words($counter); ?>">
									<h4 class="panel-title"><i class="caret-icon fas fa-caret-down text-yellow hide"></i> Premises Type Attributes (<?php echo (is_object($premises_type_attributes)) ? count(object_to_array($premises_type_attributes)) : count($premises_type_attributes) ; ?>) <span class="pull-right pointer add-new-attribute"><i class="fas fa-plus" title="Add new Attribute to this premises type" ></i></span></h4>
								</div>
								<div id="collapse<?php echo number_to_words($counter); ?>" class="panel-collapse no-bg no-background" role="tabpanel" aria-labelledby="heading<?php echo number_to_words($counter); ?>" >
									<div class="panel-body">
										<div class="table-responsive">
											<div class="col-md-12 col-sm-12 col-xs-12">
												<table class="table">
													<thead>
														<tr>
															<th width="25%">Attribute / Label Name</th>
															<th width="15%">Response Type</th>
															<th width="15%">Response Options</th>
															<th width="10%" class="text-center" >Status</th>
															<th width="15%" class="text-center" >Order</th>
															<!-- <th width="10%"><span class="pull-right">Primary Attr.</span></th> -->
															<th width="20%"><span class="pull-right">Action</span></th>
														</tr>
													</thead>
													<?php foreach ($premises_type_attributes as $k => $attribute) {
													    $k++; ?>
														<tr>
															<td width="25%"><?php echo ucfirst($attribute->attribute_name); ?></td>
															<td width="15%"><span title="<?php echo $attribute->response_type_alt; ?>" ><?php echo (!empty($attribute->response_type_alt)) ? ucwords(str_replace('_', ' ', $attribute->response_type_alt)) : '' ; ?></span></td>
															<td width="15%"><span title="" ><?php echo (is_array($attribute->response_options)) ? implode(" | ", $attribute->response_options) : (is_object($attribute->response_options) ? json_encode($attribute->response_options) : $attribute->response_options) ; ?></span></td>
															<td width="10%" class="text-center" ><?php echo ($attribute->is_active == 1) ? '<i class="far fa-check-circle text-green" title="This Attribute is active" ></i>' : '<i class="far fa-times-circle text-red" title="This Attribute is currently disabled" ></i>'; ?></td></td>
															<td width="15%" class="text-center" ><span title="<?php echo $k; ?>" ><?php echo $k; ?></span></td>
															<td width="20%">
																<div class="pull-right">
																	<span class="edit-premises-attribute pointer" data-attribute_id="<?php echo $attribute->attribute_id; ?>" title="Click to Edit this record">
																		<label style="margin-right: 10px;" class="pointer text-grey" for="chk<?php echo $attribute->attribute_id; ?>" ><input id="chk<?php echo $attribute->attribute_id; ?>" type="checkbox" value="" class="make-primary-attribute pointer" data-attribute_id="<?php echo $attribute->attribute_id; ?>" <?php echo ($attribute->attribute_id == $premises_type_details->primary_attribute_id) ? 'checked' : '' ?>> Primary</label>
																	</span>
																	<span class="hide edit-premises-attribute pointer" data-attribute_id="<?php echo $attribute->attribute_id; ?>" title="Click to Edit this record">
																		<i class="far fa-edit"></i> Edit
																	</span> &nbsp; &nbsp;
																	<span class="delete-premises-attribute pointer text-red" data-attribute_id="<?php echo $attribute->attribute_id; ?>" data-premises_type_id="<?php echo $premises_type_details->premises_type_id; ?>" title="Click to Delete this record" >
																		<label style="margin-right: 0px;" class="pointer  text-red"><i class="far fa-trash-alt"></i> Delete</label>
																	</span>
																</div>
															</td>
														</tr>
													<?php $counter++;
													} ?>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
							<br/>
						<?php } else { ?>
							<div class="no-results">
								<div><?php echo $this->config->item('no_records'); ?></span> &nbsp;Click the button to add a new attribute <span><button title="Add new attribute for this premises type" style="width:8%" class="add-new-attribute btn btn-sm btn-success success-shadow"><i class="fas fa-plus-circle" style="font-size: 18px;"></i></button></span></div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>

			<!-- Modal for VIewing and Editing an existing premises Name -->
			<div id="edit-premises-type-modal" class="modal fade edit-premises-type-modal" tabindex="-1" role="dialog" aria-hidden="true">
				<form id="edit-premises-type-form">
					<input type="hidden" name="premises_type_id" value="<?php echo (!empty($premises_type_details->premises_type_id)) ? $premises_type_details->premises_type_id : '' ; ?>" />
					<div class="modal-dialog modal-md">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
								<h4 class="modal-title" id="myModalLabel">Update Premises Type Profile (<?php echo $premises_type_details->premises_type_id; ?>)</h4>
								<small id="feedback-message"></small>
							</div>

							<div class="modal-body">
								<div class="input-group form-group">
									<label class="input-group-addon">Premises Type Name</label>
									<input id="premises_type" name="premises_type" class="form-control" type="text" placeholder="Premises Type Name" value="<?php echo (!empty($premises_type_details->premises_type)) ? html_escape($premises_type_details->premises_type) : '' ; ?>" />
								</div>

								<div class="input-group form-group">
									<label class="input-group-addon">Premises Type Description</label>
									<textarea name="premises_type_desc" class="form-control" type="text" value="" style="width:100%; height:78px" ><?php echo (!empty($premises_type_details->premises_type_desc)) ? html_escape($premises_type_details->premises_type_desc) : '' ; ?></textarea>
								</div>

								<div class="input-group form-group">
									<label class="input-group-addon">Is Sub Address required?</label>
									<select name="is_subaddress_required" class="form-control">
										<option value="">Please select</option>
										<option value="1" <?php echo ($premises_type_details->is_subaddress_required == 1) ? 'selected=selected' : '' ?> >Yes</option>
										<option value="0" <?php echo ($premises_type_details->is_subaddress_required != 1) ? 'selected=selected' : '' ?> >No</option>
									</select>
								</div>

								<div class="input-group form-group">
									<label class="input-group-addon">Status</label>
									<select name="is_active" class="form-control">
										<option value="">Please select</option>
										<option value="1" <?php echo ($premises_type_details->is_active == 1) ? 'selected=selected' : '' ?> >Active</option>
										<option value="0" <?php echo ($premises_type_details->is_active != 1) ? 'selected=selected' : '' ?> >In-active</option>
									</select>
								</div>
							</div>

							<div class="modal-footer">
								<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">&nbsp;&nbsp;&nbsp;&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;</button>
								<button id="update-premises-type-btn" type="button" class="btn btn-sm btn-success">Save Changes</button>
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
							<h4 class="modal-title" id="myAddQuestModalLabel">Add Premises Type attribute</h4>
						</div>
						<?php include('premises_type_attribute_add_new.php'); ?>
					</div>
				</div>
			</div>

			<!-- Modal for Editing a Attribute -->
			<div class="modal fade edit-premises-attribute-modal" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<form id="edit-premises-attribute-form" >
							<input type="hidden" name="attribute_id" value="" />
							<input type="hidden" name="page" value="details" />
							<input type="hidden" name="premises_type_id" value="<?php echo $premises_type_details->premises_type_id; ?>" />

							<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
								<h4 class="modal-title" id="myEditQuestModalLabel">Edit Premises Attribute ID: <span id="attribute_id">Data loading....</span></h4>
								<small id="attribute-feedback-message"></small>
							</div>

							<div class="modal-body premises-attribute-body">

							</div>

							<div class="modal-footer">
								<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">&nbsp;&nbsp;&nbsp;&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;</button>
								<button id="edit-premises-attribute-btn" type="button" class="btn btn-sm btn-success">Save Changes</button>
							</div>
						</form>
					</div>
				</div>
			</div>

			<?php } else { ?>
				<div class="row">
					<div><?php echo $this->config->item('no_records'); ?> </div>
					<div><span class="pull-right pointer"><i class="fas fa-plus" title="Add new Attribute to this premises type" ></i></span></div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){

		$( ".delete-premises-type" ).on( "click", function( e ){
			e.preventDefault();
			var premisesTypeID = "<?php echo (!empty($premises_type_details->premises_type_id)) ? $premises_type_details->premises_type_id : '' ; ?>";
			swal({
				title: 'Confirm deleting premises Type?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ){
				if( result.value ){
					$.ajax({
						url:"<?php echo base_url('webapp/premises/delete_premises_type/'); ?>",
						method: "POST",
						data: {
							premises_type_id: premisesTypeID,
							xsrf_token: "<?php echo (!empty($xsrf_token)) ? $xsrf_token : false ?>",
						},
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 ){
								swal( 'Success', data.status_msg, 'success' );
								setTimeout( function(){
									window.location.href = "<?php echo base_url('webapp/premises/premises_types/'); ?>";
								}, 2000 );
							}
						}
					});
				}
			}).catch( swal.noop )
		});


		$( '.premises-attribute-body' ).on( '#response_type, change', function(){

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
		$( '.premises-attribute-body' ).on( 'click', '#premises-section-quick-add', function(){
			$(".profile-add-section-modal").modal( "show" );
		});

		$('#edit-premises-type-modal').on('hidden.bs.modal', function () {
			location.reload();
		});

		$( '.section-container-bar' ).click( function(){
			$( this ).closest( 'div' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');
		});

		$( '.edit-premises-type' ).click( function(){
			$(".edit-premises-type-modal").modal("show");
		} );

		$( '.add-new-attribute' ).click( function(){
			$(".add-attribute-modal").modal("show");
		} );

		//VIEW AND EDIT AN premises ATTRIBUTE
		$( '.edit-premises-attribute' ).click( function(){

			var attributeId  = $( this ).data( 'attribute_id' );
			var premisesTypeId = "<?php echo $premises_type_details->premises_type_id; ?>";

			if( attributeId.length != 0 ){

				$( '[name="attribute_id"]' ).val( attributeId );
				$( '#attribute_id' ).text( attributeId );

				$.ajax({
					url:"<?php echo base_url('webapp/premises/view_attribute_data/'); ?>" + attributeId,
					method:"post",
					data:{ attribute_id:attributeId, premises_type_id:premisesTypeId },
					dataType: 'json',
					success:function( data ){
						if( data.status == 1 ){
							$( '.premises-attribute-body' ).html( data.attribute_data );
							$( '.edit-premises-attribute-modal' ).modal( 'show' );
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

		// UPDATE ATTRIBUTE FORM
		$( '#edit-premises-attribute-btn' ).click( function() {
			$( '#attribute-feedback-message' ).show();
			$( '#attribute-feedback-message' ).text( '' );

			event.preventDefault();

			var attributeId 	 = $( '.premises-attribute-body [name="attribute_id"] ' ).val();
			var attributeName = $( '.premises-attribute-body [name="attribute"] ' ).val();
			var sectionName  = $( '.premises-attribute-body #section_name option:selected' ).val();

			if( attributeName.length == 0 ){
				$( '[name="attribute"]' ).focus().css( "border","1px solid red" );
				$( '#attribute-feedback-message' ).html( '<span class="text-red">Attribute label is required</span>' );
				return false;
			}

			var formData = $( this ).closest( 'form' ).serialize();

			swal({
				title: 'Confirm update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ){
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/premises/update_premises_type_attribute/'.$premises_type_details->premises_type_id); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function( data ){
							$( '#attribute-feedback-message' ).html( data.status_msg );
							setTimeout(function(){
								$( '#attribute-feedback-message' ).fadeOut( 'slow' );
								$('.edit-premises-attribute-modal').modal( 'hide' );
								var new_url = window.location.href.split( '?' )[0];
								window.location.href = new_url + "?toggled=" + sectionName;
							}, 3000 );
						}
					});
				}
			}).catch( swal.noop )

		});

		// DELETE OR ARCHIVE premises ATTRIBUTE
		$( '.delete-premises-attribute' ).click( function(){

			var attributeId  		= $( this ).data( 'attribute_id' );
			var premisesTypeId  	= $( this ).data( 'premises_type_id' );
			var sectionName  		= $( this ).data( 'section_name' );

			if( attributeId == 0 || attributeId == undefined ){
				swal({
					title: 'Oops! Something went wrong',
					type: 'error',
					text: 'Please reload the page and try again!',
				})
			}

			swal({
				title: 'Confirm delete Attribute?',
				type: 'warning',
				text: 'This is an irreversible action',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/premises/delete_premises_type_attribute/'); ?>" + attributeId,
						method:"POST",
						data:{ attribute_id:attributeId, premises_type_id:premisesTypeId },
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2500
								})
								window.setTimeout( function(){
									var new_url = window.location.href.split('?')[0];
									window.location.href = new_url + "?toggled=" + sectionName;
								}, 1000 );
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
		} );

		// REMOVE RED BORDER WHEN USER STARTS TO TYPE AGAIN
		$( '[name="premises_type"]' ).keyup( function(){
			$( this ).css("border","1px solid #ccc");
			$( '#feedback-message' ).show().text( '' );
		} );


		// UPDATE PREMISES TYPE FORM
		$( '#update-premises-type-btn' ).click( function( event ){

			$( '#feedback-message' ).show();
			$( '#feedback-message' ).text( '' );

			event.preventDefault();

			var premisesName 	= $( '#premises_type' ).val();

			if( !( parseInt( $( '[name="premises_type_id"]' ).val() ) > 0 ) ){
				$( '#feedback-message' ).html( '<span class="text-red">Missing Required data. Please refresh the page</span>' );
				return false;
			}

			if( premisesName.length < 3 ){
				$( '[name="premises_type"]' ).focus().css( "border","1px solid red" );
				$( '#feedback-message' ).html( '<span class="text-red">Premises Type Name is too short</span>' );
				return false;
			}

			var formData = $( this ).closest( 'form' ).serialize();

			swal({
				title: 'Confirm update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url: "<?php echo base_url('webapp/premises/update_premises_type/'.$premises_type_details->premises_type_id); ?>",
						method: "POST",
						data: formData,
						dataType: 'json',
						success: function( data ){
							$( '#feedback-message' ).html( data.status_msg );
							setTimeout( function(){
								$( '#feedback-message' ).fadeOut( 'slow' );
							}, 3000 );
						}
					});
				}
			}).catch( swal.noop )

		});

		$( '.premises-attribute-body' ).on( 'change', '.extra_info_trigger', function(){
			var respType 	= $( this ).data( 'response_type' );
			var selectdOpt  = $( 'option:selected', this ).val();
			if( selectdOpt.length > 0 ){
				var newText = 'If "' + selectdOpt + '", please provide further info.';
				$( '#extra-info-selected-'+respType ).val( newText );
			}
		} );

		$( '.make-primary-attribute' ).on( 'click', function( event ){
			if( $( this ).prop( 'checked' ) == true ){
				$( '.make-primary-attribute' ).not( this ).prop( 'checked', false )
				var thisAttrID = $( this ).attr( 'data-attribute_id' )
				updatePrimaryAttributeID( thisAttrID )
			} else {
				updatePrimaryAttributeID( 0 )
			}
		})

		function updatePrimaryAttributeID( primary_attribute_id ){

			var categoryId 	 = "<?php echo !empty($category->category_id) ? $category->category_id : '' ; ?>";
			var disciplineId = "<?php echo !empty($premises_type_details->discipline_id) ? $premises_type_details->discipline_id : '' ; ?>";

			$.ajax({
				url:"<?php echo base_url('webapp/premises/update_premises_type/'); ?>",
				method:"POST",
				data:{ discipline_id: disciplineId, premises_type: '<?php echo html_escape(ucwords($premises_type_details->premises_type)); ?>', premises_type_id : <?php echo $premises_type_details->premises_type_id; ?>, premises_group: '<?php echo ucwords($premises_type_details->premises_group); ?>', primary_attribute_id: primary_attribute_id, xsrf_token: "<?php echo (!empty($xsrf_token)) ? $xsrf_token : false ?>" },
				dataType: "json",
				success:function( result ){
					if( result.status ){
						swal({
							type: 	( result.status ) 		? 'success' 		: 'error',
							title: 	( result.status ) 		? 'Success' 		: 'Error',
							html: 	( result.status_msg ) 	? result.status_msg : 'Something went wrong!',
							showConfirmButton: false,
							timer: 2500
						})
						window.setTimeout(function(){
							location.reload();
						}, 1000 );
					}
				}
			});
		}
	});
</script>

