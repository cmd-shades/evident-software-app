<div class="row">
		<div class="col-md-5 col-sm-5 col-xs-12">
			<form id="user-details-form" class="form-horizontal">
			<input type="hidden" name="id" value="<?php echo $user_details->id; ?>" />
			<input type="hidden" name="page" value="permissions" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			
			<div class="x_panel tile has-shadow">
				<legend>Buildings Visibility Permission</legend>
				
				<?php if( !empty( $this->user->is_admin ) ){ ?>
				
					<div class="input-group form-group">
						<label class="input-group-addon">Assigned Region</label>
						<select id="region_id" name="region_id" class="form-control" >
							<option value="">Please select</option>
							<option value="0" <?php echo empty( $user_details->region_id ) ? 'selected=selected' : ''; ?> >- Ignore Region Restrictions</option>
							<?php if( !empty( $postcode_regions ) ) { foreach( $postcode_regions as $k => $region ) { ?>
								<option value="<?php echo $region->region_id; ?>" <?php echo ( $user_details->region_id == $region->region_id ) ? 'selected=selected' : ''; ?> ><?php echo $region->region_name; ?></option>
							<?php } } ?>
						</select>
					</div>
				
					<div class="input-group form-group info-fields" data-info_tag="buildings_visibility" >
						<label class="input-group-addon">Buildings Visibility &nbsp;<i title="This is used for managing Visibility of Buildings/Sites." class="fas fa-info-circle"></i></label>
						<select name="buildings_visibility" class="form-control">
							<option value="" >Please select</option>
							<option value="Full" <?php echo ( !empty( $user_details->buildings_visibility ) && ( strtolower( $user_details->buildings_visibility ) ) == 'full' ) ? 'selected=selected' : ''; ?> >Full - Can see all Buildings</option>
							<option value="Limited" <?php echo ( !empty( $user_details->buildings_visibility ) && ( strtolower($user_details->buildings_visibility ) ) != 'full' ) ? 'selected=selected' : ''; ?> >Limited - Restricted to Associated Buildings only</option>
						</select>	
					</div>
				<?php } ?>
				
				<?php if( $this->user->is_admin || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
					<div class="row">
						<div class="col-md-6">
							<button id="update-user-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next update-user-btn" type="button" >Update User Permission</button>					
						</div>
					</div>
				<?php }else{ ?>
					<div class="row col-md-6">
						<button class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>					
					</div>
				<?php } ?>
			</div>
		</form>
		</div>
		
		<div class="col-md-7 col-sm-7 col-xs-12">
			<?php if( $this->user->is_admin || !empty( $permissions->can_add ) || !empty( $permissions->can_view ) || !empty( $permissions->is_admin ) ){ ?>
			<div class="accordion" id="accordionOne" role="tablist" aria-multiselectable="true">
				<div class="panel has-shadow">
					<div class="section-container-bar panel-heading collapsed bg-grey pointer no-radius" role="tab" id="headingOne" data-toggle="collapse" data-parent="#accordionOne" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
						<h4 class="panel-title"><i class="caret-icon fas fa-caret-down text-yellow"></i> ASSOCIATED BUILDINGS (<?php echo !empty( $associated_buildings ) ? count( $associated_buildings ) : 0; ?>) <span class="pull-right pointer associate-buildings"><i class="fas fa-plus" title="Associate Buildings to this record" ></i></span></h4>
					</div>
					<div id="collapseOne" class="panel-collapse no-bg collapsed no-background" role="tabpanel" aria-labelledby="headingOne" >
						<div class="panel-body no-background">
							<div class="row table-responsive">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<?php if( !empty( $associated_buildings ) ){ ?>
										<table id="datatable" class="table table-responsive sortable" style="margin-bottom:0px;width:100%" >
											<thead>
												<tr>
													<th width="10%">SITE ID</th>
													<th width="50%">SITE NAME</th>
													<th width="20%">POSTCODE</th>
													<th width="20%"><span class="pull-right" >ACTION</span></th>
												</tr>
											</thead>
											
											<tbody>
												<?php foreach( $associated_buildings as $associated_building ) { ?>
													<tr>
														<td><a href="<?php echo base_url( 'webapp/site/profile/'.$associated_building->site_id ); ?>" ><?php echo ucwords( $associated_building->site_id ); ?></a></td>
														<td><?php echo !empty( $associated_building->site_name ) ? $associated_building->site_name : ''; ?></span></td>
														<td><?php echo !empty( $associated_building->site_postcodes ) ? strtoupper( $associated_building->site_postcodes ) : ''; ?></span></td>
														<td><span class="pull-right"><span class="disassociate-buildings pointer" data-user_id="<?php echo $associated_building->user_id; ?>" data-site_id="<?php echo $associated_building->site_id; ?>" title="Click to un-associate this Building from this user" ><i class="far fa-trash-alt text-red"></i> Unlink</span></span></td>							
													</tr>
												<?php } ?>
											</tbody>
										</table>
									<?php }else{ ?>
										<p><?php echo $this->config->item('no_records'); ?></p>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<br/>	
			</div>
			<?php } ?>
		</div>
		
		
		<!-- Modal Associating Buildings? -->
		<div class="modal fade associate-buildings-modal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span></button>
						<h4 class="modal-title" id="myPeopleModalLabel">Associate Buildings</h4>						
					</div>
					<div class="modal-body" id="buildings-modal-container" >
						<input type="hidden" name="page" value="details" />
						<input type="hidden" name="user_id" value="<?php echo $user_details->id; ?>" />
						<div class="form-group">
							<label class="strong">Search buildings</label>
							<select id="associated_buildings" name="associated_buildings[]" multiple="multiple" class="form-control" style="width:100%; display:none; margin-bottom:10px;" data-label_text="Associated Buildings" >
								<option value="" disabled >Search buildings</option>
								<?php if( !empty( $available_buildings ) ) { foreach( $available_buildings as $k => $available_building ) { ?>
									<?php if( !in_array( $available_building->site_id, $associated_buildings_ids ) ){ ?>
										<option value="<?php echo $available_building->site_id; ?>" ><?php echo ucwords( $available_building->site_name ); ?> - <?php echo strtoupper( $available_building->site_postcodes ); ?></option>
									<?php } ?>
								<?php } } ?>
							</select>
						</div>
					</div>
					
					<div class="modal-footer">
						<button id="associate-buildings-btn" class="btn btn-success btn-sm">Add Selected buildings</button>
					</div>
				</div>
			</div>
		</div>
	
</div>

<script>

	//Associate Buildings
	$( document ).ready(function(){
		
		//Submit form for processing
		$( '#update-user-btn' ).click( function(){

			var formData = $('#user-details-form').serialize();
			var postUrl  = '<?php echo base_url("webapp/user/update_user/".$user_details->id ); ?>';

			swal({
				title: "Confirm user update?",
				showCancelButton: true,
				confirmButtonColor: "#5CB85C",
				cancelButtonColor: "#9D1919",
				confirmButtonText: "Yes"
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
									text: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){ 
									location.reload();
								} ,3000);							
							}else{
								swal({
									type: 'error',
									text: data.status_msg
								})
							}		
						}
					});
				}
			}).catch(swal.noop)
		});
		
		
		$( '.section-container-bar' ).click( function(){
			$( this ).closest( 'div' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');			
		});
		
		$( '.associate-buildings' ).click( function(){
			$( ".associate-buildings-modal" ).modal( "show" );			
		} );
		
		$( '#associated_buildings' ).select2({
			allowClear: true,
			minimumResultsForSearch: -1,
		});
		
		
		$( '#associate-buildings-btn' ).click( function(){
			
			var formData = $( "#buildings-modal-container :input").serialize();
			$.ajax({
				url:"<?php echo base_url('webapp/user/associate_buildings/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						
						$( '.associate-buildings-modal' ).modal( 'hide' );
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
		$( '.disassociate-buildings' ).click( function(){
			
			var primaryUserId  = $( this ).data( 'user_id' );
			var siteId  	= $( this ).data( 'site_id' );
			if( siteId == 0 || siteId == undefined ){
				swal({
					title: 'Oops! Something went wrong',
					type: 'error',
					text: 'Please reload the page and try again!',
				})
			}
			swal({
				title: 'Confirm Disassociate Building?',
				type: 'warning',				
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
			
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/user/disassociate_buildings/' ); ?>" + siteId,
						method:"POST",
						data:{ page:"details", user_id:primaryUserId, site_id:siteId },
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