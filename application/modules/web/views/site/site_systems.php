<div class="row">
	<?php if ($this->user->is_admin || !empty($permissions->can_add) || !empty($permissions->can_view) || !empty($permissions->is_admin)) { ?>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel tile has-shadow">
				<legend>Expected Systems</legend>
				<table id="datatable" class="table table-responsive" style="margin-bottom:0px;width:100%" >
					<thead>
						<tr>
							<th width="30%">SYSTEM TYPE</th>
							<th width="25%">SYSTEM GROUP</th>
							<th width="15%">SYSTEM STATUS</th>
							<th width="15%"><span class="pull-right" >STATUS</span></th>
							<th width="15%"><span class="pull-right" >IS INSTALLED</span></th>
						</tr>
					</thead>
					
					<tbody>
						<?php if (!empty($expected_systems)) {
						    foreach ($expected_systems as $system) { ?>
							<tr>
								<td><?php echo $system->system_name; ?></td>
								<td><?php echo ucwords($system->system_group); ?></td>
								<td><?php echo ucwords($system->system_status); ?></td>
								<td><span class="pull-right" ><?php echo ($system->is_active == 1) ? 'Compliant' : 'Not Compliant'; ?></span></td>
								<td><span class="pull-right" ><i class="far <?php echo (!empty($installed_systems) && in_array($system->system_group, $installed_systems)) ? ' fa-check-circle text-green ' : ' fa-times-circle text-red'; ?>"></i></span></td>
							</tr>
						<?php }
						    } else { ?>
							<tr>
								<td colspan="5"><?php echo $this->config->item('no_records'); ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
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