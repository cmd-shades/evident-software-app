<div class="row">
	<?php if ($this->user->is_admin || !empty($permissions->can_add) || !empty($permissions->can_view) || !empty($permissions->is_admin)) { ?>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="accordion" id="accordionOne" role="tablist" aria-multiselectable="true">
				<div class="panel has-shadow">
					<div class="section-container-bar panel-heading collapsed bg-grey pointer no-radius" role="tab" id="headingOne" data-toggle="collapse" data-parent="#accordionOne" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
						<h4 class="panel-title"><i class="caret-icon fas fa-caret-down text-yellow"></i> Parent</h4>
					</div>
					<div id="collapseOne" class="panel-collapse no-bg collapsed no-background" role="tabpanel" aria-labelledby="headingOne" >
						<div class="panel-body no-background">
							<div class="table-responsive">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<table id="datatable" class="table table-responsive" style="margin-bottom:0px;width:100%" >
										<thead>
											<tr>
												<th width="20%">ASSET TYPE</th>
												<th width="10%">SUB TYPE</th>
												<th width="15%">UNIQUE ID</th>
												<th width="25%">PRIMARY ATTRIBUTE</th>
												<th width="15%">STATUS</th>
												<th width="15%"><span class="pull-right" >ACTION</span></th>
											</tr>
										</thead>
										
										<tbody>
											<?php if (!empty($parent_assets)) {
											    foreach ($parent_assets as $parent_asset) { ?>
												<tr>
													<td><?php echo $parent_asset->asset_type; ?></td>
													<td><?php echo ucwords($parent_asset->asset_group); ?></td>
													<td><a href="<?php echo base_url('webapp/asset/profile/'.$parent_asset->asset_id); ?>" ><?php echo ucwords($parent_asset->asset_unique_id); ?></a></td>
													<td><?php echo !empty($parent_asset->attribute_value) ? ' - '.$parent_asset->attribute_value : ''; ?></span></td>
													<td><?php echo (!empty($parent_asset->is_active) && ($parent_asset->is_active == 1)) ? 'Active' : 'In-active'; ?></td>
													<td><span class="pull-right"><span class="unlink-asset pointer" data-job_type_id="<?php echo $parent_asset->asset_id; ?>" data-parent_asset_id="<?php echo $asset_details->asset_id; ?>" data-asset_id="<?php echo $parent_asset->asset_id; ?>" title="Click to unlink this Asset from this Asset" ><i class="far fa-trash-alt text-red"></i></span></span></td>							
												</tr>
											<?php }
											    } else { ?>
												<tr>
													<td colspan="6"><?php echo $this->config->item('no_records'); ?></td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<br/>	
			</div>
		</div>
		
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="accordion" id="accordionTwo" role="tablist" aria-multiselectable="true">
				<div class="panel has-shadow">
					<div class="section-container-bar panel-heading bg-grey collapsed pointer no-radius" role="tab" id="headingTwo" data-toggle="collapse" data-parent="#accordionTwo" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
						<h4 class="panel-title"><span><i class="caret-icon fas fa-caret-down text-yellow"></i> Children <span class="pull-right pointer link-assets"><i class="fas fa-plus" title="Link an asset to this Asset" ></i></span></h4>
					</div>
					<div id="collapseTwo" class="panel-collapse no-bg collapsed no-background" role="tabpanel" aria-labelledby="headingTwo" >
						<div class="panel-body no-background">
							<div class="table-responsive">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<table id="datatable" class="table table-responsive" style="margin-bottom:0px;width:100%" >
										<thead>
											<tr>
												<th width="20%">ASSET TYPE</th>
												<th width="10%">SUB TYPE</th>
												<th width="15%">UNIQUE ID</th>
												<th width="25%">PRIMARY ATTRIBUTE</th>
												<th width="15%">STATUS</th>
												<th width="15%"><span class="pull-right" >ACTION</span></th>
											</tr>
										</thead>
										
										<tbody>
											<?php if (!empty($child_assets)) {
											    foreach ($child_assets as $child_asset) { ?>
												<tr>
													<td><?php echo $child_asset->asset_type; ?></td>
													<td><?php echo ucwords($child_asset->asset_group); ?></td>
													<td><a href="<?php echo base_url('webapp/asset/profile/'.$child_asset->asset_id); ?>" ><?php echo ucwords($child_asset->asset_unique_id); ?></a></td>
													<td><?php echo !empty($child_asset->attribute_value) ? ' - '.$child_asset->attribute_value : ''; ?></span></td>
													<td><?php echo (!empty($child_asset->is_active) && ($child_asset->is_active == 1)) ? 'Active' : 'In-active'; ?></td>
													<td><span class="pull-right"><span class="unlink-asset pointer" data-job_type_id="<?php echo $child_asset->asset_id; ?>" data-parent_asset_id="<?php echo $asset_details->asset_id; ?>" data-asset_id="<?php echo $child_asset->asset_id; ?>" title="Click to unlink this Asset from this Asset" ><i class="far fa-trash-alt text-red"></i></span></span></td>							
												</tr>
											<?php }
											    } else { ?>
												<tr>
													<td colspan="6"><?php echo $this->config->item('no_records'); ?></td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<br/>	
			</div>
		</div>
		
		<div class="hide col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel tile has-shadow">
				<legend>Children <span class="pull-right pointer link-assets"><i class="fas fa-plus" title="Link an asset to this Asset" ></i></span></legend>
				<table id="datatable" class="table table-responsive" style="margin-bottom:0px;width:100%" >
					<thead>
						<tr>
							<th width="20%">ASSET TYPE</th>
							<th width="10%">SUB TYPE</th>
							<th width="15%">UNIQUE ID</th>
							<th width="25%">PRIMARY ATTRIBUTE</th>
							<th width="15%">STATUS</th>
							<th width="15%"><span class="pull-right" >ACTION</span></th>
						</tr>
					</thead>
					
					<tbody>
						<?php if (!empty($child_assets)) {
						    foreach ($child_assets as $child_asset) { ?>
							<tr>
								<td><?php echo $child_asset->asset_type; ?></td>
								<td><?php echo ucwords($child_asset->asset_group); ?></td>
								<td><a href="<?php echo base_url('webapp/asset/profile/'.$child_asset->asset_id); ?>" ><?php echo ucwords($child_asset->asset_unique_id); ?></a></td>
								<td><?php echo !empty($child_asset->attribute_value) ? ' - '.$child_asset->attribute_value : ''; ?></span></td>
								<td><?php echo ($child_asset->is_active == 1) ? 'Active' : 'In-active'; ?></td>
								<td><span class="pull-right"><span class="unlink-asset pointer" data-job_type_id="<?php echo $child_asset->asset_id; ?>" data-parent_asset_id="<?php echo $asset_details->asset_id; ?>" data-asset_id="<?php echo $child_asset->asset_id; ?>" title="Click to unlink this Asset from this Asset" ><i class="far fa-trash-alt text-red"></i></span></span></td>							
							</tr>
						<?php }
						    } else { ?>
							<tr>
								<td colspan="6"><?php echo $this->config->item('no_records'); ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>		
		</div>
		
		<!-- Modal for linked assets to this Asset? -->
		<div class="modal fade link-assets-modal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span></button>
						<h4 class="modal-title" id="myRiskItemModalLabel">Link Assets</h4>						
					</div>
					<div class="modal-body" id="asset-items-modal-container" >
						<input type="hidden" name="page" value="details" />
						<input type="hidden" name="parent_asset_id" value="<?php echo $asset_details->asset_id; ?>" />
						<label class="strong">Search assets</label>
						<div class="form-group">
							<select id="linked_assets" name="linked_assets[]" multiple="multiple" class="form-control" style="width:100%; display:none; margin-bottom:10px;" data-label_text="Linked Assets" >
								<option value="" disabled >Search assets</option>
								<?php if (!empty($available_assets)) {
								    foreach ($available_assets as $k => $asset) { ?>
									<?php if (!in_array($asset->asset_id, $linked_assets)) { ?>
										<option value="<?php echo $asset->asset_id; ?>" ><?php echo ucwords($asset->asset_unique_id); ?> - <?php echo ucwords($asset->asset_type); ?></option>
									<?php } ?>
								<?php }
								    } ?>
							</select>
						</div>
					</div>
					
					<div class="modal-footer">
						<button id="link-asset-btn" class="btn btn-success btn-sm">Link Selected Assets</button>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
</div>

<script>

	//link_assets

	$( document ).ready(function(){
		
		
		$( '.section-container-bar' ).click( function(){
			$( this ).closest( 'div' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');			
		});
		
		$( '.link-assets' ).click( function(){
			$( ".link-assets-modal" ).modal( "show" );			
		} );
		
		$( '#linked_assets' ).select2({
			allowClear: true,
			minimumResultsForSearch: -1,
		});
		
		
		$( '#link-asset-btn' ).click( function(){
			var formData = $( "#asset-items-modal-container :input").serialize();
			$.ajax({
				url:"<?php echo base_url('webapp/asset/link_assets/'); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						
						$( '.link-assets-modal' ).modal( 'hide' );
						$( '.modal-backdrop' ).remove();
						
						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 2000
						})
						
						window.setTimeout(function(){ 
							location.reload();
						} ,1000);
					} else {
						swal({
							type: 'error',
							title: data.status_msg
						})
					}		
				}
			});
			return false;
		});
		
		
		//Unlink 
		$( '.unlink-asset' ).click( function(){
			
			var parentAssetId  	= $( this ).data( 'parent_asset_id' );
			var assetId  		= $( this ).data( 'asset_id' );
			if( assetId == 0 || assetId == undefined ){
				swal({
					title: 'Oops! Something went wrong',
					type: 'error',
					text: 'Please reload the page and try again!',
				})
			}
			swal({
				title: 'Confirm unlink Asset?',
				type: 'warning',
				text: 'This is an irreversible action',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
			
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/asset/unlink_assets/'); ?>" + assetId,
						method:"POST",
						data:{ page:"details", parent_asset_id:parentAssetId ,asset_id:assetId },
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2100
								})
								window.setTimeout( function(){
									var new_url = window.location.href.split('?')[0];
									window.location.href = new_url;
								} ,1000 );
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
		
	});
</script>