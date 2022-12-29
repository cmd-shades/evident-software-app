<style>
	.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
		padding: 8px;
		line-height: 1.42857143;
		vertical-align: top;
		border: none;
		border-bottom: 1px solid #ddd;
	}
	.x_panel {
		margin-top: 10px;
	}
	
	.table .table {
		background:transparent;
	}
	
</style>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel tile has-shadow">
				<legend>Review Asset Upload</legend>
				<?php if (!empty($pending)) { ?>
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive" style="overflow-y: hidden;" >
							<table class="table table-responsive" style="margin-bottom:0px;width:100%" >
							
								<tbody>
									<?php foreach ($pending as $group => $records) { ?>
										<form id="frm-<?php echo $group;?>" action="<?php echo base_url('webapp/asset/add_assets/'.$this->user->account_id); ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
											<tr>
												<td colspan="9" style="color:<?php echo ($group == 'new-records') ? 'green' : 'red'; ?>" title="These records <?php echo ($group == 'new-records') ? 'are ready for processing into new asset records' : 'already exist on the system'; ?>" >
													<span class="pointer grp" data-grp_id="<?php echo $group;?>"><?php echo ucwords(str_replace('-', ' ', $group)); ?> ( <?php echo (!empty($records)) ? count($records) : '0'; ?> )</span>
													<span class="pull-right">
														<button style="display:none; width:328px" class="submit-btn btn-sm btn btn-block btn-default btn-<?php echo ($group == 'new-records') ? 'success' : 'danger'; ?> grp_<?php echo $group;?>" data-action_type="<?php echo ($group == 'new-records') ? 'add' : 'remove'; ?>" data-form_id="<?php echo $group; ?>" >
															<?php echo ($group == 'new-records') ? 'Submit' : 'Remove'; ?> Selected Records
														</button>
													</span>
												</td>
											</tr>
											<tr class="grp_<?php echo $group;?>" style="display:none">
												<td colspan="9" >
													<table class="table table-responsive" style="width:100%" >
														<thead>
															<tr>
																<th width="10%">Asset Name</th>
																<th width="10%">Make</th>
																<th width="10%">Model</th>
																<th width="10%">Colour</th>
																<th width="10%">Code</th>
																<th width="10%">Unique ID / SN</th>
																<th width="12%">Type</th>
																<th width="20%">Specifications</th>
																<th width="8%">
																	<div class="checkbox pull-right" >
																		<label ><strong>Tick all</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="chk-all chk<?php echo $group;?>" data-chk_id="<?php echo $group;?>" type="checkbox" value=""></label>
																	</div>
																</th>															
															</tr>
														</thead>
														<tbody>
															<?php foreach ($records as $key => $record) { ?>
																<tr data-temp_asset_id="<?php echo $record['temp_asset_id']; ?>" >
																	<td width="10%" style="border-bottom: 1px solid #fff;"><input class="form-control changeable-field" type="text" name="assets[<?php echo $record['temp_asset_id']; ?>][asset_name]" value="<?php echo $record['asset_name'];?>" ></td>
																	<td width="10%" style="border-bottom: 1px solid #fff;"><input class="form-control changeable-field" type="text" name="assets[<?php echo $record['temp_asset_id']; ?>][asset_make]" value="<?php echo $record['asset_make'];?>" ></td>
																	<td width="10%" style="border-bottom: 1px solid #fff;"><input class="form-control changeable-field" type="text" name="assets[<?php echo $record['temp_asset_id']; ?>][asset_model]" value="<?php echo $record['asset_model'];?>" ></td>
																	<td width="10%" style="border-bottom: 1px solid #fff;"><input class="form-control changeable-field" type="text" name="assets[<?php echo $record['temp_asset_id']; ?>][asset_colour]" value="<?php echo $record['asset_colour'];?>" ></td>
																	<td width="10%" style="border-bottom: 1px solid #fff;"><input class="form-control changeable-field" type="text" name="assets[<?php echo $record['temp_asset_id']; ?>][asset_code]" value="<?php echo $record['asset_code'];?>" ></td>
																	<td width="10%" style="border-bottom: 1px solid #fff;"><input class="form-control" type="text" name="assets[<?php echo $record['temp_asset_id']; ?>][asset_unique_id]" value="<?php echo $record['asset_unique_id'];?>" readonly ></td>
																	<td width="12%" style="border-bottom: 1px solid #fff;">
																		<select id="asset-types" name="assets[<?php echo $record['temp_asset_id']; ?>][asset_model]" class="form-control required changeable-field">
																			<option value="">Please select</option>
																			<?php if (!empty($asset_types)) {
																			    foreach ($asset_types as $category_name => $category_data) { ?>
																				<optgroup label="<?php echo strtoupper($category_name); ?>">
																					<?php foreach ($category_data as $k => $asset_type) { ?>
																						<option value="<?php echo $asset_type->asset_type_id; ?>" <?php echo ($asset_type->asset_type_id == $record['asset_type_id']) ? 'selected=selected' : ''; ?> data-asset_group="<?php echo $asset_type->asset_group; ?>" ><?php echo $asset_type->asset_type; ?></option>
																					<?php } ?>
																				</optgroup>
																			<?php }
																			    } ?>
																		</select>
																	</td>
																	<td width="20%" style="border-bottom: 1px solid #fff;">
																		<input class="form-control changeable-field" type="text" name="assets[<?php echo $record['temp_asset_id']; ?>][asset_specifications]" value="<?php echo $record['asset_specifications'];?>" >
																	</td>														
																	<td width="8%" style="border-bottom: 1px solid #fff;">
																		<div class="checkbox pull-right" >
																			<input type="hidden" name="assets[<?php echo $record['temp_asset_id']; ?>][checked]" value="0" />
																			<label><input type="checkbox" name="assets[<?php echo $record['temp_asset_id']; ?>][checked]" value="1" class="chk<?php echo $group;?>" ></label>
																		</div>
																	</td>														
																</tr>
															<?php } ?>
														</tbody>
													</table>
												</td>
											</tr>
										</form>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<br/>
				<div class="row">
					<div class="col-md-3">
						<a href="<?php echo base_url('webapp/asset/create'); ?>" class="btn btn-sm btn-block btn-danger" type="submit" >Go back and re-upload file</a>					
					</div>
					<div class="col-md-3 col-md-offset-6 pull-right hide" >
						<button class="btn btn-sm btn-block btn-success submit-btn" type="submit" >Submit Selected Records</button>					
					</div>					
				</div>
				<?php } else { ?>
				<div class="row">
					
					<div class="col-md-12">
						<span><?php echo $this->config->item('no_records');  ?></span>
						<br/>
						<br/>
					</div>
					<div class="col-md-3">
						<a href="<?php echo base_url('webapp/asset/create'); ?>" class="btn btn-sm btn-block btn-info" type="submit" >Restart Asset Upload process</a>					
					</div>
				</div>
				<?php } ?>
			</div>
	</div>	
</div>

<script>
	$(document).ready(function(){
		
		$( '.grp' ).click( function(){
			var grpId = $( this ).data( 'grp_id' );
			$( '.grp_'+grpId ).slideToggle();
		});
		
		//Check all selected inputs
		$( '.chk-all' ).click( function(){
			var chkId = $( this ).data( 'chk_id' );
			if( this.checked ) {
				$( '.chk'+chkId ).each(function() {
					this.checked = true;
				});
			}else{
				$( '.chk'+chkId ).each(function() {
					this.checked = false;
				});
			}
		});

		//Instant update
		$( 'select[class="changeable-field"], .changeable-field' ).change( function(){
			var tempAssetId 	= $( this ).closest('tr').data( 'temp_asset_id' );
			var formData 	= $( this ).serialize();
			$.ajax({
				url:"<?php echo base_url('webapp/asset/update_temp_data/'); ?>"+tempAssetId,
				method: "POST",
				data: formData,
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 ){
						//Use this to catch any post submission events
					} else {
						//Use this to show any errors
					}
				}
			});
			
		});
		
		//Submit checked records
		$( '.submit-btn' ).click( function( e ){
			e.preventDefault();
			var formId	   = $( this ).data( 'form_id' );
			var actionType = $( this ).data( 'action_type' );
			
			if( actionType == 'add' ){
				var postUrl = "<?php echo base_url('webapp/asset/create_assets/'); ?>";
			} else if ( actionType == 'remove' ){
				var postUrl = "<?php echo base_url('webapp/asset/drop_temp_records/'); ?>";
			}

			var totalChkd = 0;
			$( '.chk'+formId ).each( function(){
				if( this.checked ) {
					totalChkd++;					
				}				
			} );
			
			//Tick atleat 1 checkbox
			if( totalChkd == 0 ){
				swal({
					type: 'error',
					title: '<small>Please select at-least 1 record to '+actionType+'</small>'
				})
				return false;
			}
			
			var formData = $('#frm-'+formId ).serialize();
			
			swal({
				title: 'Confirm '+actionType+' asset records?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:postUrl,
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
								window.setTimeout(function(){ 
									if( data.all_done == 1 ){
										//Redirect to assets dashboard
										location.href = "<?php echo base_url('webapp/asset/assets/'); ?>";
									}else{
										location.reload();
									}									
								} ,2000);							
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
			
		} );
		
	});
</script>