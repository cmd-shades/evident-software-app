<div class="row premises_details">
	<div class="col-md-4 col-sm-4 col-xs-12">
		<form id="update-premises-form-left" enctype="multipart/form-data" class="form-horizontal">
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="premises_id" value="<?php echo $premises_details->premises_id; ?>" />
			<input type="hidden" name="site_id" value="<?php echo $premises_details->site_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<input type="hidden" name="attribute_group" value="generic" />
			<div class="x_panel tile has-shadow">
				<legend>Update Premises Details</legend>
				<div class="input-group form-group">
					<label class="input-group-addon">Premises type</label>
					<select id="premises_type_id" name="premises_type_id" class="form-control">
						<option value="">Please select</option>
						<?php if( !empty( $premises_types ) ) { foreach( $premises_types as $premises_type ) { ?>
							<option value="<?php echo $premises_type->premises_type_id; ?>" data-premises_type_ref="<?php echo $premises_type->premises_type_ref; ?>"  <?php echo ($premises_details->premises_type_id == $premises_type->premises_type_id) ? 'selected="selected"' : '' ?> ><?php echo $premises_type->premises_type; ?></option>
						<?php } } ?>
					</select>
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Premises Ref</label>
					<input name="premises_ref" class="form-control" type="text" placeholder="Premises unique Id" value="<?php echo $premises_details->premises_ref; ?>" />
				</div>

				<div id="premises_type_attributes" >
				<?php if( !empty( $premises_details->premises_attributes ) ){ ?>
					<?php $return_data = '';
						foreach( $premises_details->premises_attributes as $k => $attribute ){
							$return_data .= '<div class="row" ><div class="col-md-12 col-sm-12 col-xs-12">';

								$append_classes 	= ( $attribute->is_mandatory 	== 1 ) ? 'required' : '';
								$apply_max_length 	= '';

								switch ( $attribute->response_type ){

									default:
									case 'datepicker':
									case 'short_text':
									case 'numbers_only':
										$append_classes 	.= ( $attribute->response_type 	== 'datepicker' ) 	? ' datepicker' 	: '';
										$append_classes 	.= ( $attribute->response_type 	== 'numbers_only' ) ? ' numbers-only' 	: '';

										$apply_max_length	= 'maxlength="125"';

										$return_data .= '<div class="input-group form-group">';
											$return_data .= '<label class="input-group-addon" >'.ucwords( $attribute->attribute_name ).'</label>';
											$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_id]" 	value="'.$attribute->attribute_id.'">';
											$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_name]" 	value="'.htmlspecialchars( $attribute->attribute_name ).'">';
											$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][response_type]" 	value="'.$attribute->response_type.'">';
											$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][ordering]" 		value="'.$attribute->ordering.'">';
											$return_data .= '<input type="text" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_value]" 	value="'.$attribute->attribute_value.'" class="form-control '.$append_classes.'" '.$apply_max_length.' placeholder="Enter the '.ucwords( $attribute->attribute_name ).' here..." >';
										$return_data .= '</div>';

										break;

									case 'long_text':

										$return_data .= '<div class="input-group form-group">';
											$return_data .= '<label class="input-group-addon" >'.ucwords( $attribute->attribute_name ).'</label>';
											$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_id]" 	value="'.$attribute->attribute_id.'">';
											$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_name]" 	value="'.htmlspecialchars( $attribute->attribute_name ).'">';
											$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][response_type]" 	value="'.$attribute->response_type.'">';
											$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][ordering]" 		value="'.$attribute->ordering.'">';
											$return_data .= '<textarea type="text" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_value]" 	value="'.$attribute->attribute_value.'" rows="4" class="form-control '.$append_classes.'" class="form-control" placeholder="Enter the '.ucwords( $attribute->attribute_name ).' here..." >'.$attribute->attribute_value.'</textarea>';
										$return_data .= '</div>';

										break;

									case 'single_choice':
									case 'multiple_choice':

										$return_data .= '<div class="input-group form-group">';
											$return_data .= '<label class="control-label"><strong>'.ucwords( $attribute->attribute_name ).'</strong></label>';
											$return_data .= '<div class="col-md-12" >';
												$return_data .= '<div class="rows" >';

													if( $attribute->response_type == 'single_choice' ){
														if( !empty( $attribute->response_options ) ){ foreach( $attribute->response_options as $k => $option ){
															$return_data .= '<div class="col-md-4 col-sm-4 col-xs-12" >';
																$return_data .= '<label class="pointer" >';
																	$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_id]" 	value="'.$attribute->attribute_id.'">';
																	$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_name]" 	value="'.htmlspecialchars( $attribute->attribute_name ).'">';
																	$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][response_type]" 	value="'.$attribute->response_type.'">';
																	$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][ordering]" 		value="'.$attribute->ordering.'">';
																	$return_data .= '<input type="radio"    name="premises_attributes['.$attribute->attribute_id.'][attribute_value]"  value="'.$option.'" id="optionsRadio'.$k.'" '.( strtolower( $option ) == strtolower( $attribute->attribute_value ) ? 'checked=checked' : '' ).' > &nbsp;'.$option;
																$return_data .= '</label>';
															$return_data .= '</div>';

														} } else {
															$return_data .= '<div class="col-md-12 col-sm-12 col-xs-12" >No options set for this attribute.</div>';
														}
													} else if ( $attribute->response_type == 'multiple_choice' ){
														if( !empty( $attribute->response_options ) ){
															$return_data .= '<div class="col-md-6 col-sm-6 col-xs-12" >';
																$return_data .= '<label class="pointer" >';
																	$return_data .= '<input class="check-all" type="checkbox" id="check-all'.$attribute->attribute_id.'" data-attribute_id="'.$attribute->attribute_id.'"  > Tick all';
																$return_data .= '</label>';
															$return_data .= '</div>';
															foreach( $attribute->response_options as $k => $option ){
																$return_data .= '<div class="col-md-6 col-sm-6 col-xs-12" >';
																	$return_data .= '<label class="pointer" >';
																		$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_id]" 	value="'.$attribute->attribute_id.'">';
																		$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_name]" 	value="'.htmlspecialchars( $attribute->attribute_name ).'">';
																		$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][response_type]" 	value="'.$attribute->response_type.'">';
																		$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][ordering]" 		value="'.$attribute->ordering.'">';
																		$return_data .= '<input type="checkbox" name="premises_attributes['.$attribute->attribute_id.'][attribute_value][]" class="check-options check-opts'.$attribute->attribute_id.'" data-attribute_id="'.$attribute->attribute_id.'" value="'.$option.'" id="optionsCheckbox'.$k.'" '.( ( is_array( $attribute->attribute_value ) && in_array( strtolower( $option ), array_map( 'strtolower', $attribute->attribute_value ) ) ) ? 'checked=checked' : '' ).' > '.$option;
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
											$return_data .= '<label class="input-group-addon" >'.ucwords( $attribute->attribute_name ).'</label>';
											$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_id]" 	value="'.$attribute->attribute_id.'">';
											$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_name]" 	value="'.htmlspecialchars( $attribute->attribute_name ).'">';
											$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][response_type]" 	value="'.$attribute->response_type.'">';
											$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][ordering]" 		value="'.$attribute->ordering.'">';
											$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_value]" 	value="'.$attribute->response_type.'" >';
											$return_data .= '<span class="control-fileupload pointer">';
												$return_data .= '<label for="premises_image" class="pointer text-left">Please choose a file on your computer <i class="fas fa-upload"></i></label><input name="user_files[]" type="file" id="premises_image" >';
												#$return_data .= '<input type="text" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_value]" 	value="" class="form-control '.$append_classes.'" '.$apply_max_length.' placeholder="Please select image file from your computer..." >';
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
						<span>There's currently no attributes set-up for this Premises Type</span>
					</div>
				<?php } ?>
				</div>

				<?php if( $this->user->is_admin || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
					<div class="row">
						<div class="col-md-6">
							<button id="update-premises-btn-1" class="update-premises-btn btn btn-sm btn-block btn-flow btn-success btn-next update-premises-btn" type="button" >Update Premises Details</button>
						</div>

						<?php if( $this->user->is_admin || !empty( $permissions->can_delete ) || !empty( $permissions->is_admin ) ){ ?>
							<div class="col-md-6">
								<button id="delete-premises-btn" class="btn btn-sm btn-block btn-flow btn-danger has-shadow" type="button" data-premises_id="<?php echo $premises_details->premises_id; ?>">Delete Premises</button>
							</div>
						<?php } ?>
					</div>
				<?php }else{ ?>
					<div class="row col-md-6">
						<span id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</span>
					</div>
				<?php } ?>
			</div>
		</form>
	</div>

	<div class="col-md-4 col-sm-6 col-xs-12">
		<form id="update-premises-form-right" class="form-horizontal">
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="premises_id" value="<?php echo $premises_details->premises_id; ?>" />
			<input type="hidden" name="site_id" value="<?php echo $premises_details->site_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<input type="hidden" name="attribute_group" value="generic" />
			<div class="x_panel tile has-shadow">
				<legend>Building Information</legend>
				<span id="change-detected" class="col-md-12 text-red"></span>
				<div class="input-group form-group">
					<label class="input-group-addon">Assigned Building</label>
					<?php if( !empty( $sites ) ){ ?>
						<select name="site_id" class="form-control">
							<option value="">Please select</option>
							<?php foreach( $sites as $site ){ ?>
								<option value="<?php echo $site->site_id; ?>" <?php echo ( $site->site_id == $premises_details->site_id ) ? 'selected="selected"' : '' ?>><?php echo ( !empty( $site->site_name ) ) ? $site->site_name.' - '.$site->site_postcodes  : '' ; ?></option>
							<?php } ?>								
						</select>
					<?php } ?>
				</div>
				
				<div class="input-group form-group">
					<label class="input-group-addon">Zone</label>
					<?php if( !empty( $site_zones ) ){ ?>
						<select name="zone_id" class="form-control">
							<option value="">Please select</option>
							<?php foreach( $site_zones as $zone ){ ?>
								<option value="<?php echo $zone->zone_id; ?>" <?php echo ( $zone->zone_id == $premises_details->zone_id ) ? 'selected="selected"' : '' ?>><?php echo ( !empty( $zone->zone_name ) ) ? $zone->zone_name : '' ; ?></option>
							<?php } ?>
						</select>
					<?php } else ?>
				</div>

				<?php if( !empty( $site_zones ) && !empty( $premises_details->site_id ) && !empty( $premises_details->zone_id ) && !empty( $premises_details->location_id ) ){ ?>
					<div class="input-group form-group" id="location">
						<label class="input-group-addon">Location</label>
						<?php if( !empty( $site_locations ) ){ ?>
							<select name="location_id" class="form-control">
								<option value="">Please select</option>
								<?php foreach( $site_locations as $loc ){ ?>
									<option value="<?php echo $loc->location_id; ?>" <?php echo ( $loc->location_id == $premises_details->location_id ) ? 'selected="selected"' : '' ?>><?php echo ( !empty( $loc->location_name ) ) ? $loc->location_name : '' ; ?></option>
								<?php } ?>								
							</select>
						<?php } ?>
					</div>
				<?php } else { ?>
					<div class="input-group form-group" id="location">
						<label class="input-group-addon">Location</label>
						<select name="location_id" class="form-control">
							<option value="">Please select Zone</option>
						</select>
					</div>
				<?php } ?>

				<?php if( $this->user->is_admin || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
					<div class="row col-md-6">
						<button id="update-premises-btn-2" class="btn btn-sm btn-block btn-flow btn-success btn-next update-premises-btn" type="button" >Update Details</button>
					</div>
				<?php }else{ ?>
					<div class="row col-md-6">
						<span id="no-permissions-2" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</span>
					</div>
				<?php } ?>
			</div>
		</form>
	</div>
	
	<div class="col-md-4 col-sm-6 col-xs-12 hide">
		<div class="x_panel tile has-shadow">
			<legend>Building Information</legend>
			<div class="row" >
				<?php if( !empty( $premises_details->profile_images ) ){ foreach( $premises_details->profile_images as $img_id => $image ) { $img_id++; ?>
					<div class="text-center col-md-12 col-sm-12 col-xs-12">
						<img height="100%"  width="100%" src="<?php echo $image->document_link; ?>" />
						<h6 class="pull-left"><a target="_blank" href="<?php echo $image->document_link; ?>"><b>Image <?php echo $img_id; ?></b> - <?php echo $image->document_name; ?></a></h6>
					</div>
				<?php } } else { ?>
					<div class="col-md-12 col-sm-12 col-xs-12">
						<span>There's currently no profile image set for this Premises</span>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="col-md-4 col-sm-6 col-xs-12 hide">
		<form id="update-premises-form-right" class="form-horizontal">
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="premises_id" value="<?php echo $premises_details->premises_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<div class="x_panel tile has-shadow">
				<legend>Information</legend>
				<p id="change-detected" class="col-md-12 text-red" style="display:block; margin:-10px 0 10px 0;"></p>
				<div class="input-group form-group">
					<label class="input-group-addon">Last modified by</label>
					<input class="form-control" type="text" placeholder="Last modified by" value="<?php echo ( !empty( $premises_details->last_modified_by ) ) ? ucwords( $premises_details->last_modified_by ) : 'No updates yet'; ?> <?php echo ( valid_date( $premises_details->last_modified ) ) ? '@ '.date( 'd/m/Y H:i:s', strtotime( $premises_details->last_modified ) ) : ''; ?>" readonly />
				</div>

				<?php if( $this->user->is_admin || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
					<div class="row col-md-6">
						<button id="update-premises-btn-2" class="btn btn-sm btn-block btn-flow btn-success btn-next update-premises-btn" type="button" >Update Details</button>
					</div>
				<?php }else{ ?>
					<div class="row col-md-6">
						<span id="no-permissions-2" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</span>
					</div>
				<?php } ?>
			</div>
		</form>
	</div>

</div>

<script>
	$(document).ready(function(){

		$('#premises_type_id').change(function(){
			premisesTypeID 			= $(this).val();
			originalPremisesTypeID 	= <?php echo $premises_details->premises_type_id; ?>;

			if(premisesTypeID != originalPremisesTypeID){
				Swal.fire({
			        text: "Changing premises type will wipe premises attributes!",
					type: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Continue'
			    }).then((result) => {
			      	if(!result.value){
						$(this).val(originalPremisesTypeID)
					}
			    })
			}
		});

		//Submit form for processing
		$( '.update-premises-btn' ).click( function( event ){
			event.preventDefault();

			//var btnId			= $( this ).attr( 'id' )
			var fData			= new FormData( document.getElementById( "update-premises-form-left" ) );
			var formData			= $( this ).closest( 'form' ).serialize();
			console.log(fData);
			var premisesType 	= $('#premises_type').val();
			var premisesSite 	= $( '#site_id' ).val();
			
			swal({
				title: 'Confirm premises update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/premises/update_premises/'.$premises_details->premises_id ); ?>",
						method:"POST",
						data:fData,
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

		//Delete premises
		$('#delete-premises-btn').click(function(){

			var premisesId = $(this).data( 'premises_id' );

			swal({
				title: 'Confirm premises delete?',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/premises/delete_premises/'.$premises_details->premises_id ); ?>",
						method:"POST",
						data:{premises_id:premisesId},
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
									window.location.href = "<?php echo base_url('webapp/premises/premises'); ?>";
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


		$( '#premises_type_attributes' ).on( 'change', '.check-all', function(){
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

		$( '#premises_type_attributes' ).on( 'change', '.check-options', function(){
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
		
	});
</script>

