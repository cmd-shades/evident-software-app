<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<form id="update-system-form" method="post">
		<input type="hidden" name="page" value="details" />
		<div class="x_panel tile group-container system">
			<input type="hidden" name="system_type_id" value="<?php echo ( !empty( $systems_details->system_type_id ) ) ? $systems_details->system_type_id : '' ; ?>" />
			<h4 class="legend"><i class="fas fa-caret-down"></i>System Details</h4>
			<div class="row group-content el-hidden">
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">System Name</label>
						<input class="input-field" name="systems_details[name]" type="text" placeholder="System name" value="<?php echo ( !empty( $systems_details->name ) ) ? $systems_details->name : '' ; ?>" />
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">Reference Code</label>
						<input class="input-field" name="systems_details[system_reference_code]" type="text" placeholder="Reference Code" value="<?php echo ( !empty( $systems_details->system_reference_code ) ) ? $systems_details->system_reference_code : '' ; ?>" />
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">Is Local Server?</label>
						<select name="systems_details[is_local_server]" class="input-field">
							<option value="yes" <?php echo ( !empty( $systems_details->is_local_server ) && ( ( int ) $systems_details->is_local_server > 0 ) ) ? 'selected="selected"' : "" ; ?>>Yes</option>
							<option value="no" <?php echo ( empty( $systems_details->is_local_server ) || ( ( int ) $systems_details->is_local_server < 0 ) ) ? 'selected="selected"' : "" ; ?>>No</option>
						</select>
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">DRM Type</label>
						<?php
						if( !empty( $drm_types ) ){ ?>
							<select name="systems_details[drm_type_id]" class="input-field">
								<option value="">Please select</option>
								<?php
								foreach( $drm_types as $row ){ ?>
									<option value="<?php echo $row->setting_id; ?>" <?php echo ( !empty( $systems_details->drm_type_id ) && ( $systems_details->drm_type_id == $row->setting_id ) ) ? 'selected="selected"' : "" ; ?>><?php echo ( !empty( $row->setting_value ) ) ? $row->setting_value : '' ; ?></option>
								<?php
								} ?>
							</select>
						<?php
						} else { ?>
							<input class="input-field" name="systems_details[drm_type_id]" type="text" placeholder="DRM Type ID" value="<?php echo ( !empty( $systems_details->drm_type_id ) ) ? $systems_details->drm_type_id : '' ; ?>" />
						<?php
						} ?>
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">Delivery Mechanism</label>
						<?php
						if( !empty( $delivery_mechanism_types ) ){ ?>
							<select name="systems_details[delivery_mechanism_id]" class="input-field">
								<option value="">Please select</option>
								<?php
								foreach( $delivery_mechanism_types as $dm_id => $dm_row ){ ?>
									<option value="<?php echo $dm_row->setting_id; ?>" <?php echo ( !empty( $systems_details->delivery_mechanism_id ) && ( $systems_details->delivery_mechanism_id == $dm_row->setting_id ) ) ? 'selected="selected"' : "" ; ?>><?php echo ( !empty( $dm_row->setting_value ) ) ? $dm_row->setting_value : '' ; ?></option>
								<?php
								} ?>
							</select>
						<?php
						} else { ?>
							<input class="input-field" name="systems_details[delivery_mechanism_id]" type="text" placeholder="Delivery Mechanism ID" value="<?php echo ( !empty( $systems_details->delivery_mechanism_id ) ) ? $systems_details->delivery_mechanism_id : '' ; ?>" />
						<?php
						} ?>
					</div>

				</div>

				<?php
				if( $this->user->is_admin || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<div class="row">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									<button class="update-system-btn btn btn-block btn-update btn-primary" type="button" data-system_section="system">Update</button>
								</div>
							</div>
						</div>
					</div>
				<?php
				} else { ?>
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>
						</div>
					</div>
				<?php
				} ?>
			</div>
		</div>
	</form>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="x_panel tile group-container providers-approval">
		<span class="add-provider"><a type="button" data-toggle="modal" data-target="#addProvider"><i class="fas fa-plus-circle"></i></a></span>
		<?php /* <span class="upload-file"><a href="<?php echo base_url( "/webapp/content/upload_provider" ); ?>"><i class="fas fa-upload"></i></a></span> */ ?>
		<input type="hidden" name="system_id" value="<?php echo $systems_details->system_type_id; ?>" />
		<h4 class="legend"><i class="fas fa-caret-down"></i>Providers</h4>
		<div class="row group-content el-hidden">
			<div class="row">
				<?php
				$todays_date = date( 'Y-m-d' );
				if( !empty( $systems_details->providers ) ){
					foreach( $systems_details->providers as $prov_row ){?>
						<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
							<div class="row providers-list-item container-full">
								<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
									<span class=""><?php echo ( !empty( $prov_row->provider_name ) ) ? ( html_escape( excerpt( $prov_row->provider_name, 30 ) ) ) : '' ; ?></span>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<span class=""><?php echo ( validate_date( $prov_row->approval_date ) ) ? format_date_client( $prov_row->approval_date ) : '' ; ?></span>
								</div>
								<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
									<span class="delete-provider" data-provider_id="<?php echo ( !empty( $prov_row->provider_id ) ) ? html_escape( $prov_row->provider_id ) : '' ;?>"><div class=""><a href="#"><i class="fas fa-trash-alt"></i></a></div></span>
								</div>
							</div>
						</div>
					<?php
					}
				} ?>
			</div>
		</div>
	</div>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="x_panel tile group-container systems-documents">
		<h4 class="legend"><i class="fas fa-caret-down"></i>System Documents</h4>
		<div class="row group-content el-hidden">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
				<legend class="default-legend">Upload Files</legend>
				<form action="<?php echo base_url( 'webapp/systems/upload_docs/'.$systems_details->system_type_id ); ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
					<input type="hidden" name="system_type_id" value="<?php echo $systems_details->system_type_id; ?>" />
					<input type="hidden" name="module" value="systems" />
					<input type="hidden" name="doc_type" value="systems" />

					<div class="input-group form-group">
						<label class="input-group-addon">System File</label>
						<span class="control-fileupload single pointer">
							<label for="file-upload" class="custom-file-upload">
								<i class="fas fa-cloud-upload"></i> Select file
							</label>
							<input id="file-upload" name="upload_files[doc]" type="file"/>
						</span>
					</div>

					<div class="row">
						<div class="col-md-6">
							<button id="doc-upload-btn" class="btn btn-sm btn-block btn-success" type="submit">Upload Document(s)</button>
						</div>
					</div>
					<br/>
				</form>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
				<legend class="default-legend">Existing Documents</legend>
				<div class="row">
				<div class="col-md-12 table-responsive">
					<?php if( !empty( $systems_documents ) ){ foreach( $systems_documents as $file_group=>$files){ ?>
						<h5 style="color:#000" class="file-toggle pointer" data-class_grp="<?php echo str_replace( ' ', '', $file_group ); ?>" ><?php echo ucwords( $file_group ); ?> <span class="pull-right">(<?php echo count( $files ); ?>)</span></h5>
						<?php foreach( $files as $k=>$file){ ?>
							<div class="row <?php echo str_replace( ' ', '', $file_group ); ?>" style="display:block;padding:5px 0">
								<div class="col-md-10" style="padding-left:30px;"><a target="_blank" href="<?php echo $file->document_link; ?>"><?php echo $file->document_name; ?></a></div>
								<div class="col-md-2"><span class="pull-right"><a target="_blank" href="<?php echo $file->document_link; ?>"><i class="fas fa-download"></i></a> &nbsp;&nbsp;&nbsp;<i class="fas fa-trash-alt text-red delete-file" data-document_id="<?php echo ( !empty( $file->document_id ) ) ? $file->document_id : '' ; ?>"></i></span></div>
							</div>
						<?php }  ?>
					<?php } }  ?>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal To Add Provider -->
<div class="modal fade" id="addProvider" tabindex="-1" role="dialog" aria-labelledby="addProvider" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<?php $this->view( 'systems/includes/add_provider.php' ); ?>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){

		$( ".delete-file" ).click( function( e ){
			e.preventDefault();

			var documentID = $( this ).data( 'document_id' );

			swal({
				title: 'Confirm document delete?',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ){
				if ( result.value ) {
					if( parseInt( documentID ) < 0 ){
						swal({
							title: 'Document ID is required',
							type: 'error',
						})
						return false;
					}

					$.ajax({
						url: "<?php echo base_url( 'webapp/systems/delete_document/' ); ?>",
						method:"POST",
						data: { document_id: documentID },
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2000
								})
								window.setTimeout( function(){
									location.reload( true );
								}, 2000 );
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


		$( '#adding-provider-to-system-form' ).on( "submit", function( e ){
			e.preventDefault();
			var formData = $( '#adding-provider-to-system-form' ).serialize();

			swal({
				title: 'Confirm adding Provider\'s Approval Date(s) to the System?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url( 'webapp/systems/add_provider/' ); ?>",
						method: "POST",
						data: formData,
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 && ( data.new_provider !== '' ) ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2000
								})
								window.setTimeout( function(){
									 location.reload();
								}, 2000 );
							} else {
								swal({
									type: 'error',
									title: data.status_msg
								})
								return false;
							}
						}
					});
				} else {
					return false;
				}
			}).catch( swal.noop )
		});


		var trigger = $( "#all_providers" );
		$( trigger ).on( "change", function(){
			if( $( this ).prop( "checked" ) != true ){
				$( ".providers_list input[type='checkbox']" ).each(
					function(){ $( this ).prop( "checked", false ) }
				)
			} else {
				$( ".providers_list input[type='checkbox']" ).each(
					function(){ $( this ).prop( "checked", true ) }
				)
			}
		});

		$( ".providers_list input[type='checkbox']" ).not( ":first" ).on( "click", function(){
			if( ( $( trigger ).prop( "checked" ) == true ) && ( $( this ).prop( "checked" ) != true ) ){
				$( trigger ).prop( "checked", false );
			}
		})

		$( ".adding-provider-to-system-steps" ).click( function(){
			$( '.error_message' ).each( function(){
				$( this ).text( '' );
			});

			var currentpanel = $( this ).data( "currentpanel" );

			// If true - there are errors
			var inputs_state = check_inputs( currentpanel );
			if( inputs_state ){
				//If name attribute returned, auto focus to the field and display arror message
				$( '#adding-provider-to-system-form [name="'+inputs_state+'"]' ).focus();

				var labelText = $( '#adding-provider-to-system-form [name="' + inputs_state + '"]' ).parent().find( 'label' ).text();
				$( '#' + currentpanel + '-errors' ).text( ucwords( labelText ) + ' is a required' );
				return false;
			}

			var elementClass = ".adding-provider-to-system";
			panelchange( "." + currentpanel, elementClass )
			return false;
		});



		//** Validate any inputs that have the required class, if empty return the name attribute **/
		function check_inputs( currentpanel ){
			var result = false;
			var panel = "." + currentpanel;

			$( $( panel + " .required" ).get().reverse() ).each( function(){
				var fieldName = '';
				var inputValue = $( this ).val();
				if( ( inputValue == false ) || ( inputValue == '' ) || ( inputValue.length == 0 ) ){
					fieldName = $(this).attr( 'name' );
					result = fieldName;
					return result;
				}
			});
			return result;
		}

		$( ".btn-back" ).click( function(){
			var currentpanel = $( this ).data( "currentpanel" );
			go_back( "." + currentpanel )
			return false;
		});

		function panelchange( changefrom, elementClass ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
			var changeto = elementClass + panelnumber;
			$( changefrom ).hide( "slide", {direction : 'left'}, 500 );
			$( changeto ).delay( 600 ).show( "slide", {direction : 'right'},500 );
			return false;
		}

		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) ) - parseInt( 1 );
			var elementClass = changefrom.substr( 0, parseInt( changefrom.length ) - parseInt( panelnumber.toString().length ) );
			var changeto = elementClass + panelnumber;
			$( changefrom ).hide( "slide", {direction : 'right'}, 500 );
			$( changeto ).delay( 600 ).show( "slide", {direction : 'left'},500 );
			return false;
		}

		$( ".delete-provider" ).on( "click", function( e ){
			e.preventDefault();
			var ProviderID = $( this ).data( "provider_id" );
			var SystemID = <?php echo '"'.( ( !empty( $systems_details->system_type_id ) ) ? $systems_details->system_type_id : '' ).'"' ; ?>;

			swal({
				title: 'Confirm deleting provider?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ){
				if( result.value ){
					$.ajax({
						url:"<?php echo base_url( 'webapp/systems/delete_provider/' ); ?>",
						method: "POST",
						data: {
							provider_id: ProviderID,
							system_id: SystemID
						},
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2000
								})
								window.setTimeout( function(){
									 location.reload();
								}, 2000 );
							} else {
								swal({
									type: 'error',
									title: data.status_msg
								})
								return false;
							}
						}
					});
				} else {
					return false;
				}
			}).catch( swal.noop )
		})


		$( ".delete_container" ).click( function(){
			swal({
				title: 'Confirm system delete?',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					var systemID = <?php echo $systems_details->system_type_id; ?>;
					if( parseInt( systemID ) < 0 ){
						swal({
							title: 'Site ID is required',
							type: 'error',
						})
						return false;
					}

					$.ajax({
						url:"<?php echo base_url( 'webapp/systems/delete_system/'.$systems_details->system_type_id ); ?>",
						method:"POST",
						data: { system_type_id: systemID },
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout( function(){
									location.href ="<?php echo base_url( "webapp/systems" ); ?>";
								}, 3000 );
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


		$( ".legend" ).click( function(){
			$( this ).children( ".fas" ).toggleClass( "fa-caret-down fa-caret-up" );
			$( this ).next( ".group-content" ).slideToggle( 400 );
		});

		//Submit form for processing
		$( '.update-system-btn' ).click( function( e ){

			e.preventDefault();
			var section 	= $( this ).data( "system_section" );
			var formData 	= $( "." + section + " input," + "." + section + " select," + "." + section + " textarea" ).serialize();

			swal({
				title: 'Confirm system update?',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ){
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url( 'webapp/systems/update_system/'.$systems_details->system_type_id ); ?>",
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
								window.setTimeout( function(){
									location.reload();
								}, 3000 );
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
	});
</script>