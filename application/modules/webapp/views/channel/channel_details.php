<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="x_panel tile group-container territories">
		<span class="add-territory"><a type="button" data-toggle="modal" data-target="#addTerritory"><i class="fas fa-plus-circle"></i></a></span>
		<input type="hidden" name="channel_id" value="<?php echo ( !empty( $channel_details->channel_id ) ) ? $channel_details->channel_id : '' ; ?>" />
		<h4 class="legend"><i class="fas fa-caret-down"></i>Territories</h4>
		<div class="row group-content el-hidden">
			<div class="row">
				<?php
				$todays_date = date( 'Y-m-d' );
				if( !empty( $channel_details->territories ) ){
					foreach( $channel_details->territories as $ter_row ){?>
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
	
	<div class="x_panel tile group-container channel-documents">
		<h4 class="legend"><i class="fas fa-caret-down"></i>Channel Documents</h4>
		<div class="row group-content el-hidden">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
				<legend class="default-legend">Upload Files</legend>
				<form action="<?php echo base_url( 'webapp/channel/upload_docs/'.$channel_details->channel_id ); ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
					<input type="hidden" name="channel_id" value="<?php echo ( !empty( $channel_details->channel_id ) ) ? $channel_details->channel_id : '' ; ?>" />
					<input type="hidden" name="module" value="channel" />
					<input type="hidden" name="doc_type" value="channel" />
					
					<div class="input-group form-group">
						<label class="input-group-addon">Channel file</label>
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
						<?php if( !empty( $channel_documents ) ){ foreach( $channel_documents as $file_group => $files ){ ?>
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

<!-- Modal to disable the Channel -->
<div class="modal fade" id="disableChannel" tabindex="-1" role="dialog" aria-labelledby="disable-channel" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="slide-group">
							<form id="disable-channel-form">
								<input type="hidden" name="channel_id" value="<?php echo ( !empty( $channel_details->channel_id ) ) ? $channel_details->channel_id : '' ; ?>" />
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

<div class="modal fade" id="editChannel" tabindex="-1" role="dialog" aria-labelledby="editChannel" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<?php $this->view( 'channel/includes/edit_channel_details.php' ); ?>
		</div>
	</div>
</div>

<!-- Modal To Add Clearance Manually -->
<div class="modal fade" id="addTerritory" tabindex="-1" role="dialog" aria-labelledby="addTerritory" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<?php $this->view( 'channel/includes/add_territory.php' ); ?>
		</div>
	</div>
</div>


<script type="text/javascript">
$( document ).ready( function(){
	
	$( "[name='channel_details[is_channel_ott]']" ).on( "change", function(){
		$( ".source_url, .technical_encoded_url, .satelite_sources, .regions" ).toggleClass( function(){
			return $( this ).is( '.el-shown, .el-hidden') ? 'el-shown el-hidden' : 'el-shown';
		});
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
					url: "<?php echo base_url( 'webapp/channel/delete_document/' ); ?>",
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
					url:"<?php echo base_url( 'webapp/channel/delete_territory/' ); ?>",
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
	
	$( '#adding-territory-to-channel-form' ).on( "submit", function( e ){
		e.preventDefault();
		var formData = $( '#adding-territory-to-channel-form' ).serialize();

		swal({
			title: 'Confirm adding Territory?',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/channel/add_territory/' ); ?>",
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
	
	
	
	$( ".delete_container" ).on( "click", function( e ){
		e.preventDefault();
		swal({
			title: 'Confirm Channel delete?',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function( result ) {
			if ( result.value ) {

				var channelID = $( ".delete_container" ).data( "channel_id" );
				console.log( channelID );
				if( parseInt( channelID ) < 0 ){
					swal({
						title: 'Channel ID is required',
						type: 'error',
					})
					return false;
				}
				console.log( channelID );
				$.ajax({
					url: "<?php echo base_url( 'webapp/channel/delete_channel/' ); ?>",
					method: "POST",
					data: { channel_id: channelID },
					dataType: 'JSON',
					success: function( data ){
						if( data.status == 1 ){
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 2000
							})
							window.setTimeout( function(){
								location.href = "<?php echo base_url( 'webapp/channel' ); ?>";
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


	//Submit form for processing
	$( '#update-channel-form' ).on( "submit", function( event ){
		event.preventDefault();
		
		var formData = $( '#update-channel-form' ).serialize();
		
		swal({
			title: 'Confirm Channel update?',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/channel/update/' ); ?>",
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

