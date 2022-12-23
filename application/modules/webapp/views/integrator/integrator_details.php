<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="x_panel tile group-container systems">
		<?php /* <span class="add-address"><a type="button" data-toggle="modal" data-target="#addAddress"><i class="fas fa-plus-circle"></i></a></span> */ ?>
		<input type="hidden" name="system_integrator_id" value="<?php echo ( !empty( $integrator_details->system_integrator_id ) ) ? $integrator_details->system_integrator_id : '' ; ?>" />
		<h4 class="legend"><i class="fas fa-caret-down"></i>Addresses</h4>

		<div class="row group-content el-hidden">
			<div class="row">
				<?php
				if( !empty( $integrator_details->addresses ) ){
					foreach( $integrator_details->addresses as $adr_type => $adr_row1 ){
						foreach( $adr_row1 as $adr_row ){ ?>
							<form class="update-address-form">
								<input type="hidden" name="address_id" value="<?php echo ( !empty( $adr_row->address_id ) ) ? $adr_row->address_id : '' ; ?>" />

								<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 address_item">
									<?php if( !empty( $adr_row->address_type ) ){ ?>
										<div class="x_panel tile group-container">
											<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
												<h4><?php echo $adr_row->address_type; ?> Address</h4>
											</div>
										</div>
									<?php } ?>

									<?php /* Hidden for now. We warned that the separation is needed. 1/05/2020
									<div class="container-full">
										<label class="input-label">Address</label>
										<input name="addressline" class="input-field" type="text" value="<?php echo ( !empty( $adr_row->addressline ) ) ? ( $adr_row->addressline ) : '' ; ?>" placeholder="Address" />
									</div>

									<div class="container-full">
										<label class="input-label">Postcode</label>
										<input name="postcode" class="input-field" type="text" value="<?php echo ( !empty( $adr_row->postcode ) ) ? ( $adr_row->postcode ) : '' ; ?>" placeholder="Postcode" />
									</div>

									<div class="container-full">
										<label class="input-label">Town</label>
										<input name="posttown" class="input-field" type="text" value="<?php echo ( !empty( $adr_row->posttown ) ) ? ( $adr_row->posttown ) : '' ; ?>" placeholder="Town" />
									</div>
									*/ ?>

									<div class="container-full">
										<label class="input-label">Address</label>
										<input name="fulladdress" class="input-field" type="text" value="<?php echo ( !empty( $adr_row->fulladdress ) ) ? ( $adr_row->fulladdress ) : '' ; ?>" placeholder="Address" />
									</div>

									<div class="container-full">
										<label class="input-label">Country</label>
										<?php
										if( !empty( $territories ) ){ ?>
											<select name="integrator_territory_id" class="input-field">
												<option value="">Please select</option>
												<?php
												foreach( $territories as $row ){ ?>
													<option value="<?php echo $row->territory_id; ?>" <?php echo ( ( !empty( $adr_row->integrator_territory_id ) && ( $adr_row->integrator_territory_id == $row->territory_id ) ) ? 'selected="selected"' : '' ); ?>><?php echo ( !empty( $row->country ) ) ? $row->country : '' ; ?></option>
												<?php
												} ?>
											</select>
										<?php
										} ?>
									</div>

									<div class="row">
										<div class="col-lg-6 offset-lg-6 col-md-6 offset-md-6 col-sm-6 offset-sm-6 col-xs-12 pull-right">
											<?php
											$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, "details" );
											if( !$this->user->is_admin || !empty( $item_access->can_edit ) || !empty( $item_access->is_admin ) ){ ?>
												<button class="btn-success btn-block" type="submit">Update Address</button>
											<?php
											} else { ?>
												<button class="btn-success btn-block no-permissions" disabled style="width: 100%;margin-top: 10px;">No Permissions</button>
											<?php
											} ?>
										</div>
									</div>
								</div>
							</form>
						<?php
						}
					}
				} ?>
			</div>
		</div>
	</div>

	<div class="x_panel tile group-container systems">
		<span class="add-system"><a type="button" data-toggle="modal" data-target="#addSystem"><i class="fas fa-plus-circle"></i></a></span>
		<input type="hidden" name="system_integrator_id" value="<?php echo ( !empty( $integrator_details->system_integrator_id ) ) ? $integrator_details->system_integrator_id : '' ; ?>" />
		<h4 class="legend"><i class="fas fa-caret-down"></i>Systems</h4>
		<div class="row group-content el-hidden">
			<div class="row">
				<?php
				$todays_date = date( 'Y-m-d' );
				if( !empty( $integrator_details->systems ) ){
					foreach( $integrator_details->systems as $sys_row ){?>
						<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
							<div class="row systems-list-item container-full">
								<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
									<span class=""><?php echo ( !empty( $sys_row->product_system_type_name ) ) ? $sys_row->product_system_type_name : '' ; ?></span>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pull-right">
									<span class="delete-system" data-system_id="<?php echo ( !empty( $sys_row->system_id ) ) ? $sys_row->system_id : '' ;?>"><div class=""><a href="#"><i class="fas fa-trash-alt"></i></a></div></span>
								</div>
							</div>
						</div>
					<?php
					}
				} ?>
			</div>
		</div>
	</div>

	<div class="x_panel tile group-container territories">
		<span class="add-territory"><a type="button" data-toggle="modal" data-target="#addTerritory"><i class="fas fa-plus-circle"></i></a></span>
		<input type="hidden" name="system_integrator_id" value="<?php echo ( !empty( $integrator_details->system_integrator_id ) ) ? $integrator_details->system_integrator_id : '' ; ?>" />
		<h4 class="legend"><i class="fas fa-caret-down"></i>Territories</h4>
		<div class="row group-content el-hidden">
			<div class="row">
				<?php
				$todays_date = date( 'Y-m-d' );
				if( !empty( $integrator_details->territories ) ){
					foreach( $integrator_details->territories as $ter_row ){?>
						<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
							<div class="row territories-list-item container-full">
								<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
									<span class=""><?php echo ( !empty( $ter_row->country ) ) ? $ter_row->country : '' ; ?></span>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pull-right">
									<span class="delete-territory" data-territory_id="<?php echo ( !empty( $ter_row->territory_id ) ) ? $ter_row->territory_id : '' ;?>"><div class=""><a href="#"><i class="fas fa-trash-alt"></i></a></div></span>
								</div>
							</div>
						</div>
					<?php
					}
				} ?>
			</div>
		</div>
	</div>


	<div class="x_panel tile group-container integrator-documents">
		<h4 class="legend"><i class="fas fa-caret-down"></i>Integrator Documents</h4>
		<div class="row group-content el-hidden">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
				<legend class="default-legend">Upload Files</legend>
				<form action="<?php echo base_url( 'webapp/integrator/upload_docs/'.$integrator_details->system_integrator_id ); ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
					<input type="hidden" name="system_integrator_id" value="<?php echo ( !empty( $integrator_details->system_integrator_id ) ) ? $integrator_details->system_integrator_id : '' ; ?>" />
					<input type="hidden" name="module" value="integrator" />
					<input type="hidden" name="doc_type" value="integrator" />

					<div class="input-group form-group">
						<label class="input-group-addon">Integrator file</label>
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
						<?php if( !empty( $integrator_documents ) ){ foreach( $integrator_documents as $file_group => $files ){ ?>
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

<!-- Modal to disable the Integrator -->
<div class="modal fade" id="disableIntegrator" tabindex="-1" role="dialog" aria-labelledby="disable-integrator" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="slide-group">
							<form id="disable-integrator-form">
								<input type="hidden" name="integrator_id" value="<?php echo ( !empty( $integrator_details->system_integrator_id ) ) ? $integrator_details->system_integrator_id : '' ; ?>" />
								<div class="row">
									<div class="col-md-12 col-sm-12 col-xs-12">
										<legend class="legend-header">What is the Disable Date?</legend>
									</div>
									<div class="col-md-12 col-sm-12 col-xs-12">
										<h6 class="error_message pull-right" id="product_creation_panel1-errors" style="display: none;"></h6>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<label class="input-label">Disable Date</label>
										<input class="input-field datetimepicker" type="text" name="disable_date" placeholder="Select date"  />
									</div>
								</div>

								<div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<?php
										$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, "details" );

										if( !$this->user->is_admin || !empty( $item_access->can_edit ) || !empty( $item_access->is_admin ) ){ ?>
											<button class="btn-success btn-block btn-update" type="submit">Disable</button>
										<?php
										} else { ?>
											<button class="btn-success btn-block btn-update no-permissions" disabled>No Permissions</button>
										<?php
										} ?>
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

<!-- Modal To Add Clearance Manually -->
<div class="modal fade" id="addSystem" tabindex="-1" role="dialog" aria-labelledby="addSystem" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<?php $this->view( 'integrator/includes/add_system.php' ); ?>
		</div>
	</div>
</div>

<!-- Modal To Add Clearance Manually -->
<div class="modal fade" id="addTerritory" tabindex="-1" role="dialog" aria-labelledby="addTerritory" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<?php $this->view( 'integrator/includes/add_territory.php' ); ?>
		</div>
	</div>
</div>


<div class="modal fade" id="editIntegrator" tabindex="-1" role="dialog" aria-labelledby="editIntegrator" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<?php $this->view( 'integrator/includes/edit_integrator_details.php' ); ?>
		</div>
	</div>
</div>

<script type="text/javascript">
$( document ).ready( function(){
	$( "#disable-integrator-form" ).on( "submit", function( e ){
		e.preventDefault();

		var formData = $( this ).serialize();
		swal({
			title: 'Confirm disabling Integrator?',
			html: 'All sites and products linked with this Integrator <br />will be disabled!',
			// type: 'question',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function( result ){
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/integrator/disable/' ); ?>",
					method: "POST",
					data: formData,
					dataType: 'json',
					success:function(data){
						if( data.status == 1 ){
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 2000
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
						url: "<?php echo base_url( 'webapp/integrator/delete_document/' ); ?>",
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


	var systemTrigger = $( "#all_systems" );
	$( systemTrigger ).on( "change", function(){
		if( $( this ).prop( "checked" ) != true ){
			$( ".systems_list input[type='checkbox']" ).each(
				function(){ $( this ).prop( "checked", false ) }
			)
		} else {
			$( ".systems_list input[type='checkbox']" ).each(
				function(){ $( this ).prop( "checked", true ) }
			)
		}
	});

	$( ".systems_list input[type='checkbox']" ).not( ":first" ).on( "click", function(){
		if( ( $( systemTrigger ).prop( "checked" ) == true ) && ( $( this ).prop( "checked" ) != true ) ){
			$( systemTrigger ).prop( "checked", false );
		}
	})

	$( ".delete-system" ).on( "click", function( e ){
		e.preventDefault();
		var systemID = $( this ).data( "system_id" );

		swal({
			title: 'Confirm deleting System?',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/integrator/delete_system/' ); ?>",
					method: "POST",
					data: {
						system_id: systemID
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


	$( '#adding-system-to-integrator-form' ).on( "submit", function( e ){
		e.preventDefault();
		var formData = $( '#adding-system-to-integrator-form' ).serialize();

		swal({
			title: 'Confirm adding System?',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function( result ){
			if( result.value ){
				$.ajax({
					url:"<?php echo base_url( 'webapp/integrator/add_system/' ); ?>",
					method: "POST",
					data: formData,
					dataType: 'json',
					success:function( data ){
						if( data.status == 1 && ( data.new_system !== '' ) ){
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


	var trigger = $( "#all_territories" );
	$( trigger ).on( "change", function(){
		if( $( this ).prop( "checked" ) != true ){
			$( ".territory_list input[type='checkbox']" ).each(
				function(){ $( this ).prop( "checked", false ) }
			)
		} else {
			$( ".territory_list input[type='checkbox']" ).each(
				function(){ $( this ).prop( "checked", true ) }
			)
		}
	});

	$( ".territory_list input[type='checkbox']" ).not( ":first" ).on( "click", function(){
		if( ( $( trigger ).prop( "checked" ) == true ) && ( $( this ).prop( "checked" ) != true ) ){
			$( trigger ).prop( "checked", false );
		}
	})

	$( ".delete-territory" ).on( "click", function( e ){
		e.preventDefault();
		var territoryID = $( this ).data( "territory_id" );

		swal({
			title: 'Confirm deleting Territory?',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/integrator/delete_territory/' ); ?>",
					method: "POST",
					data: {
						territory_id: territoryID
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

	$( '#adding-territory-to-integrator-form' ).on( "submit", function( e ){
		e.preventDefault();
		var formData = $( '#adding-territory-to-integrator-form' ).serialize();

		swal({
			title: 'Confirm adding Territory?',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/integrator/add_territory/' ); ?>",
					method: "POST",
					data: formData,
					dataType: 'json',
					success:function( data ){
						if( data.status == 1 && ( data.new_territory !== '' ) ){
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


	$( '.update-address-form' ).on( "submit", function( e ){
		e.preventDefault();

		var formData = $( this ).serialize();
		swal({
			title: 'Confirm Integrator update?',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/integrator/update_address/' ); ?>",
					method: "POST",
					data: formData,
					dataType: 'json',
					success: function( data ){
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
						}
					}
				});
			}
		}).catch( swal.noop )
	});


	$( ".delete_container" ).click( function(){
		swal({
			title: 'Confirm System Integrator delete?',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function( result ) {
			if ( result.value ) {

				var si_id = <?php echo ( !empty( $integrator_details->system_integrator_id ) ) ? $integrator_details->system_integrator_id : -1 ; ?>;
				if( parseInt( si_id ) < 0 ){
					swal({
						title: 'Integrator ID is required',
						type: 'error',
					})
					return false;
				}

				$.ajax({
					url: "<?php echo base_url( 'webapp/integrator/delete_integrator/' ); ?>",
					method: "POST",
					data: {system_integrator_id: si_id},
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
								location.href ="<?php echo base_url( "webapp/integrator" ); ?>";
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


	function validEmail( email ){
		var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
		return pattern.test( email );
	}


	function validPhone( phone ){
		var pattern = /([0-9]{10})|(\([0-9]{3}\)\s+[0-9]{3}\-[0-9]{4})/;
		// if spaces allowed
		var phone = phone.replace( /\s/g, "" );
		return pattern.test( phone );
	}


	//Submit form for processing
	$( '#update-integrator-form' ).on( "submit", function( event ){
		event.preventDefault();


		// phone validation
		var phone = $( "[name='integrator_details[integrator_phone]']" ).val();
		if( !validPhone( phone ) ) {
			alert( 'Please enter valid phone number (only digits, min 10)' );
			return false;
		}

		// email validation
		var email = $( "[name='integrator_details[integrator_email]']" ).val();
		if( !validEmail( email ) ) {
			alert( 'Please enter valid email' );
			return false;
		}

		var formData = $( '#update-integrator-form' ).serialize();
		swal({
			title: 'Confirm Integrator update?',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/integrator/update/' ); ?>",
					method: "POST",
					data: formData,
					dataType: 'json',
					success: function( data ){
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
});
</script>

