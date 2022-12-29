<div class="row asset_details">
	<div class="col-md-4 col-sm-4 col-xs-12">
		<form id="update-asset-form-left" enctype="multipart/form-data" class="form-horizontal">
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="asset_id" value="<?php echo $asset_details->asset_id; ?>" />
			<input type="hidden" name="site_id" value="<?php echo $asset_details->site_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<input type="hidden" name="attribute_group" value="generic" />
			<div class="x_panel tile has-shadow">
				<legend>Update Asset Details</legend>
				<?php if (!empty($this->user->is_admin)) { ?>
					<div class="input-group form-group">
						<label class="input-group-addon">Asset type</label>
						<select id="asset_type_id" name="asset_type_id" class="form-control">
							<option value="">Please select</option>
							<?php if (!empty($asset_types)) {
							    foreach ($asset_types as $category_name => $category_data) { ?>
								<optgroup label="<?php echo strtoupper($category_name); ?>">
									<?php foreach ($category_data as $k => $asset_type) { ?>
										<option value="<?php echo $asset_type->asset_type_id; ?>" data-asset_group="<?php echo $asset_type->asset_group; ?>"  <?php echo ($asset_details->asset_type_id == $asset_type->asset_type_id) ? 'selected' : '' ?>><?php echo $asset_type->asset_type; ?></option>
									<?php } ?>
								</optgroup>
							<?php }
							    } ?>
						</select>
					</div>
				<?php } ?>
				<div class="input-group form-group">
					<label class="input-group-addon">Asset Unique ID</label>
					<input name="asset_unique_id" class="form-control" type="text" placeholder="Asset unique Id" value="<?php echo $asset_details->asset_unique_id; ?>" />
				</div>

				<div id="asset_type_attributes" >
				<?php if (!empty($asset_details->asset_attributes)) { ?>
					<?php $return_data = '';
				    foreach ($asset_details->asset_attributes as $k => $attribute) {
				        $return_data .= '<div class="row" ><div class="col-md-12 col-sm-12 col-xs-12">';

				        $append_classes 	= ($attribute->is_mandatory 	== 1) ? 'required' : '';
				        $apply_max_length 	= '';

				        switch ($attribute->response_type) {
				            default:
				            case 'datepicker':
				            case 'short_text':
				            case 'numbers_only':
				                $append_classes 	.= ($attribute->response_type 	== 'datepicker') ? ' datepicker' : '';
				                $append_classes 	.= ($attribute->response_type 	== 'numbers_only') ? ' numbers-only' : '';

				                $apply_max_length	= 'maxlength="125"';

				                $return_data .= '<div class="input-group form-group">';
				                $return_data .= '<label class="input-group-addon" >'.ucwords($attribute->attribute_name).'</label>';
				                $return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_id]" 	value="'.$attribute->attribute_id.'">';
				                $return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_name]" 	value="'.htmlspecialchars($attribute->attribute_name).'">';
				                $return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][response_type]" 	value="'.$attribute->response_type.'">';
				                $return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][ordering]" 		value="'.$attribute->ordering.'">';
				                $return_data .= '<input type="text" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_value]" 	value="'.$attribute->attribute_value.'" class="form-control '.$append_classes.'" '.$apply_max_length.' placeholder="Enter the '.ucwords($attribute->attribute_name).' here..." >';
				                $return_data .= '</div>';

				                break;

				            case 'long_text':

				                $return_data .= '<div class="input-group form-group">';
				                $return_data .= '<label class="input-group-addon" >'.ucwords($attribute->attribute_name).'</label>';
				                $return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_id]" 	value="'.$attribute->attribute_id.'">';
				                $return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_name]" 	value="'.htmlspecialchars($attribute->attribute_name).'">';
				                $return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][response_type]" 	value="'.$attribute->response_type.'">';
				                $return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][ordering]" 		value="'.$attribute->ordering.'">';
				                $return_data .= '<textarea type="text" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_value]" 	value="'.$attribute->attribute_value.'" rows="4" class="form-control '.$append_classes.'" class="form-control" placeholder="Enter the '.ucwords($attribute->attribute_name).' here..." >'.$attribute->attribute_value.'</textarea>';
				                $return_data .= '</div>';

				                break;

				            case 'single_choice':
				            case 'multiple_choice':

				                $return_data .= '<div class="input-group form-group">';
				                $return_data .= '<label class="control-label"><strong>'.ucwords($attribute->attribute_name).'</strong></label>';
				                $return_data .= '<div class="col-md-12" >';
				                $return_data .= '<div class="rows" >';

				                if ($attribute->response_type == 'single_choice') {
				                    if (!empty($attribute->response_options)) {
				                        foreach ($attribute->response_options as $k => $option) {
				                            $return_data .= '<div class="col-md-4 col-sm-4 col-xs-12" >';
				                            $return_data .= '<label class="pointer" >';
				                            $return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_id]" 	value="'.$attribute->attribute_id.'">';
				                            $return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_name]" 	value="'.htmlspecialchars($attribute->attribute_name).'">';
				                            $return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][response_type]" 	value="'.$attribute->response_type.'">';
				                            $return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][ordering]" 		value="'.$attribute->ordering.'">';
				                            $return_data .= '<input type="radio"    name="asset_attributes['.$attribute->attribute_id.'][attribute_value]"  value="'.$option.'" id="optionsRadio'.$k.'" '.(strtolower($option) == strtolower($attribute->attribute_value) ? 'checked=checked' : '').' > &nbsp;'.$option;
				                            $return_data .= '</label>';
				                            $return_data .= '</div>';
				                        }
				                    } else {
				                        $return_data .= '<div class="col-md-12 col-sm-12 col-xs-12" >No options set for this attribute.</div>';
				                    }
				                } elseif ($attribute->response_type == 'multiple_choice') {
				                    if (!empty($attribute->response_options)) {
				                        $return_data .= '<div class="col-md-6 col-sm-6 col-xs-12" >';
				                        $return_data .= '<label class="pointer" >';
				                        $return_data .= '<input class="check-all" type="checkbox" id="check-all'.$attribute->attribute_id.'" data-attribute_id="'.$attribute->attribute_id.'"  > Tick all';
				                        $return_data .= '</label>';
				                        $return_data .= '</div>';
				                        foreach ($attribute->response_options as $k => $option) {
				                            $return_data .= '<div class="col-md-6 col-sm-6 col-xs-12" >';
				                            $return_data .= '<label class="pointer" >';
				                            $return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_id]" 	value="'.$attribute->attribute_id.'">';
				                            $return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_name]" 	value="'.htmlspecialchars($attribute->attribute_name).'">';
				                            $return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][response_type]" 	value="'.$attribute->response_type.'">';
				                            $return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][ordering]" 		value="'.$attribute->ordering.'">';
				                            $return_data .= '<input type="checkbox" name="asset_attributes['.$attribute->attribute_id.'][attribute_value][]" class="check-options check-opts'.$attribute->attribute_id.'" data-attribute_id="'.$attribute->attribute_id.'" value="'.$option.'" id="optionsCheckbox'.$k.'" '.((is_array($attribute->attribute_value) && in_array(strtolower($option), array_map('strtolower', $attribute->attribute_value))) ? 'checked=checked' : '').' > '.$option;
				                            $return_data .= '</label>';
				                            $return_data .= '</div>';
				                        }
				                    } else {
				                        $return_data .= '<div class="col-md-12 col-sm-12 col-xs-12" >No options set for this attribute.</div>';
				                    }
				                }

				                $return_data .= '</div>';
				                $return_data .= '</div>';
				                $return_data .= '</div>';

				                break;
				            case 'file':
				            case 'photo':
				            case 'image':

				                $return_data .= '<div class="input-group form-group">';
				                $return_data .= '<label class="input-group-addon" >'.ucwords($attribute->attribute_name).'</label>';
				                $return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_id]" 	value="'.$attribute->attribute_id.'">';
				                $return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_name]" 	value="'.htmlspecialchars($attribute->attribute_name).'">';
				                $return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][response_type]" 	value="'.$attribute->response_type.'">';
				                $return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][ordering]" 		value="'.$attribute->ordering.'">';
				                $return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_value]" 	value="'.$attribute->response_type.'" >';
				                $return_data .= '<span class="control-fileupload pointer">';
				                $return_data .= '<label for="asset_image" class="pointer text-left">Please choose a file on your computer <i class="fas fa-upload"></i></label><input name="user_files[]" type="file" id="asset_image" >';
				                #$return_data .= '<input type="text" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_value]" 	value="" class="form-control '.$append_classes.'" '.$apply_max_length.' placeholder="Please select image file from your computer..." >';
				                $return_data .= '</span>';
				                $return_data .= '</div>';

				                break;
				        }

				        $return_data .= '</div>';
				        $return_data .= '</div>';
				    }
				    echo $return_data;
				    ?>
				<?php } else { ?>
					<div class="input-group form-group">
						<span>There's currently no attributes set-up for this Asset Type</span>
					</div>
				<?php } ?>
				</div>

				<?php if ($this->user->is_admin || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
					<div class="row">
						<div class="col-md-6">
							<button id="update-asset-btn-1" class="btn btn-sm btn-block btn-flow btn-success btn-next update-asset-btn" type="button" >Update Asset Details</button>
						</div>

						<?php if ($this->user->is_admin || !empty($permissions->can_delete) || !empty($permissions->is_admin)) { ?>
							<div class="col-md-6">
								<button id="delete-asset-btn" class="btn btn-sm btn-block btn-flow btn-danger has-shadow" type="button" data-asset_id="<?php echo $asset_details->asset_id; ?>">Delete Asset</button>
							</div>
						<?php } ?>
					</div>
				<?php } else { ?>
					<div class="row col-md-6">
						<span id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</span>
					</div>
				<?php } ?>
			</div>
		</form>
	</div>

	<div class="col-md-4 col-sm-6 col-xs-12">
		<form id="update-asset-form-right" class="form-horizontal">
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="asset_id" value="<?php echo $asset_details->asset_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<div class="x_panel tile has-shadow">
				<legend>Information</legend>
				<p id="change-detected" class="col-md-12 text-red" style="display:block; margin:-10px 0 10px 0;"></p>
				<div class="input-group form-group">
					<label class="input-group-addon">Asset assignee</label>
					<?php if (!empty($asset_details->site_id)) { ?>
						<select id="new-assignee" name="assignee" class="form-control" required onclick='Swal("Asset already assigned to the building","Please unassign it first!","warning")'>
						</select>
					<?php } else { ?>
						<select id="new-assignee" name="assignee" class="form-control" required >
							<option value="">Please select</option>
							<option value="0">Unassign</option>
							<?php if (!empty($users)) {
							    foreach ($users as $k => $operative) { ?>
								<option value="<?php echo $operative->id; ?>" <?php echo ($operative->id == $asset_details->assignee) ? 'selected=selected' : ''; ?>><?php echo $operative->first_name." ".$operative->last_name; ?></option>
							<?php }
							    } ?>
						</select>
					<?php } ?>
				</div>

				<div class="input-group form-group">
					<label class="input-group-addon">Evidoc Result Status</label>
					<select id="audit_result_status_id" name="audit_result_status_id" class="form-control">
						<option value="">Please select</option>
						<?php if (!empty($audit_result_statuses)) {
						    foreach ($audit_result_statuses as $k => $result_status) { ?>
							<option value="<?php echo $result_status->audit_result_status_id; ?>" <?php echo ($result_status->audit_result_status_id == $asset_details->audit_result_status_id) ? 'selected=selected' : ''; ?> ><?php echo $result_status->result_status; ?></option>
						<?php }
						    } ?>
					</select>
				</div>

				<div class="input-group form-group">
					<label class="input-group-addon">Last modified by</label>
					<input class="form-control" type="text" placeholder="Last modified by" value="<?php echo (!empty($asset_details->last_modified_by)) ? ucwords($asset_details->last_modified_by) : 'No updates yet'; ?> <?php echo (valid_date($asset_details->last_modified)) ? '@ '.date('d/m/Y H:i:s', strtotime($asset_details->last_modified)) : ''; ?>" readonly />
				</div>

				<?php /*
                <div class="form-group">
                    <textarea name="asset_notes" class="form-control" rows="6" type="text" value="" style="width:100%;" placeholder="<?php echo ( !empty($asset_details->asset_notes) ) ? 'Last note: '.$asset_details->asset_notes : 'Action notes...' ?>"></textarea>
                </div> */ ?>

				<?php if ($this->user->is_admin || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
					<div class="row col-md-6">
						<button id="update-asset-btn-2" class="btn btn-sm btn-block btn-flow btn-success btn-next update-asset-btn" type="button" >Update Details</button>
					</div>
				<?php } else { ?>
					<div class="row col-md-6">
						<span id="no-permissions-2" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</span>
					</div>
				<?php } ?>
			</div>
		</form>
	</div>
	
	<div class="col-md-4 col-sm-6 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Profile Image(s)</legend>
				<div class="row" >
					<?php if (!empty($asset_details->profile_images)) {
					    foreach ($asset_details->profile_images as $img_id => $image) {
					        $img_id++; ?>
						<div class="text-center col-md-12 col-sm-12 col-xs-12">
							<?php if (!empty($asset_details->attribute_value) && (strtolower($asset_details->attribute_value) !== 'no image required')) { ?>
							
								<?php if (!empty($asset_details->attribute_value) && (strtolower($asset_details->attribute_value) == 'n/a')) { ?>
									<p>Image not supplied</p>
								<?php } else { ?>
									<img height="100%"  width="100%" src="<?php echo $image->document_link; ?>" />
								<?php } ?>
								
							<?php } else { ?>
								<p>No Image Required</p>
							<?php } ?>
							<h6 class="pull-left"><a target="_blank" href="<?php echo $image->document_link; ?>"><b>Image <?php echo $img_id; ?></b> - <?php echo $image->document_name; ?></a></h6>
						</div>
					<?php }
					    } else { ?>
						<div class="col-md-12 col-sm-12 col-xs-12">
							<span>There's currently no profile image set for this Asset</span>
						</div>
					<?php } ?>
				</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){

		$('#new-assignee').change(function(){
			$('#assignee').val( $(this).val() );
		});

		/*
		$('#asset_type').change(function(){
			var assetType 	= $(this).val();
			var assetGroup 	= $( 'option:selected', this).data( 'asset_group' );
			//Check group
			if( $.inArray( assetGroup, ['panel'] ) != -1 ){
				$( '#alarm-panel-info' ).slideDown();
				$( '#alarm_panel_code, #event_tracking_status' ).prop( 'required', true );
			}else{
				$( '#alarm-panel-info' ).slideUp();
				$( '#alarm_panel_code, #event_tracking_status' ).val( '');
				$( '#alarm_panel_code, #event_tracking_status' ).prop( 'required', false );
			}
		});*/


		$('#asset_type_id').change(function(){
			assetTypeID = $(this).val();
			originalAssetTypeID = <?php echo $asset_type->asset_type_id; ?>;

			if(assetTypeID != originalAssetTypeID){
				Swal.fire({
			      /* title: 'Are you sure?', */
			      text: "Changing asset type will wipe asset attributes!",
			      type: 'warning',
				  showCancelButton: true,
				  confirmButtonText: 'Continue'
			    }).then((result) => {
			      	if(!result.value){
						$(this).val(originalAssetTypeID)
					}
			    })
			}
		});

		//Re-assign the location based on selected status
		$('#asset_status').change(function(){
			var statusGroup 	= $( 'option:selected', this).data( 'status_group' );
			if( $.inArray( statusGroup, ['unassigned'] ) != -1 ){
				$('#new-assignee option[value=""]').prop('selected','selected');
			}
		});

		//Submit form for processing
		$( '.update-asset-btn' ).click( function( event ){
			event.preventDefault();

			var fData		= new FormData( document.getElementById( "update-asset-form-left" ) );
			var assetType 	= $('#asset_type').val();
			var panelStatus = $('#event_tracking_status option:selected').val();
			var assetGroup 	= $( '#asset_type option:selected').data( 'asset_group' );

			//Asset status
			var assetAssignee 	= $( '#new-assignee' ).val();
			var assetSite 		= $( '#site_id' ).val();

			//var formData = $('#update-asset-form').serialize();
			//var formData = $(this).closest('form').serialize();
			
			swal({
				title: 'Confirm asset update?',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/asset/update_asset/'.$asset_details->asset_id); ?>",
						method:"POST",
						data:fData,
						//data:formData,
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
									location.reload();
								} ,1000);
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						},
						cache: false,
						contentType: false,
						processData: false
					});
				}
			}).catch(swal.noop)
		});

		//Delete asset
		$('#delete-asset-btn').click(function(){

			var assetId = $(this).data( 'asset_id' );

			swal({
				title: 'Confirm asset delete?',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/asset/delete_asset/'.$asset_details->asset_id); ?>",
						method:"POST",
						data:{asset_id:assetId},
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2100
								})
								window.setTimeout(function(){
									window.location.href = "<?php echo base_url('webapp/asset/assets'); ?>";
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


		$( '#asset_type_attributes' ).on( 'change', '.check-all', function(){
			var attributeId = $( this ).data( 'attribute_id' );
			if( $(this).is( ':checked' ) ){
				$( '.check-opts'+attributeId ).each( function(){
					$( this ).prop( 'checked', true );
				});
			} else {
				$( '.check-opts'+attributeId ).each( function(){
					$( this ).prop( 'checked', false );
				});
			}
		} );

		$( '#asset_type_attributes' ).on( 'change', '.check-options', function(){
			var attributeId = $( this ).data( 'attribute_id' ),
				chkCount  	= 0,
				totalChekd	= 0,
				unChekd   	= 0;

			$( '.check-opts'+attributeId ).each( function(){
				chkCount++;
				if( $( this ).is( ':checked' ) ){
					totalChekd++;
				} else {
					unChekd++;
				}
			});

			if( chkCount > 0 && ( chkCount == totalChekd ) ){
				$( '#check-all'+attributeId ).prop( 'checked', true );
			}else{
				$( '#check-all'+attributeId ).prop( 'checked', false );
			}
		} );
		
		
		$( '#upload-docs-form' ).submit(function( e ){
			
			var selection = document.getElementById( 'asset_image' );

			if( selection.files.length == 0 ){
				swal({
					type: 'error',
					text: 'No file selected!'
				});
				return false;
			}

			for (var i=0; i < selection.files.length; i++) {
				var filename = selection.files[i].name;
				
				var ext = filename.substr(filename.lastIndexOf('.') + 1).toLowerCase();

				if( ext!== "csv" && ext!== "xls" && ext!== "xlsx" && ext!== "pdf" && ext!== "jpg" && ext!== "jpeg" && ext!== "png" && ext!== "doc" && ext!== "docx" ) {
					swal({
						type: 'error',
						text: 'You have selected an INVALID file type: .' +ext
					})
					return false;
				}
			}
		});
		
	});
</script>

