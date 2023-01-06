<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<?php if( !empty( $linked_assets ) ){ ?>
			<div class="x_panel tile shadow">
				<div style="margin-top: 10px;">
					<legend>Linked Assets <span class="pull-right pointer attach-risk"><i class="fas fa-plus text-green" title="Attach assets to this Contract" ></i></span></legend>
					<div class="row">
						<div class="col-md-12">
							<table class="table table-responsive">
								<thead>
									<tr>
										<th>ID</th>
										<th>Unique ID</th>
										<th>Type</th>
										<th>Category</th>
										<th><span class="pull-right">Action</span></th>									
									</tr>
								</thead>
								<tbody>
									<?php foreach( $linked_assets as $k => $asset ){ ?>
										<tr>
											<td><?php echo $asset->asset_id; ?></td>
											<td><?php echo $asset->asset_unique_id; ?></td>
											<td><?php echo $asset->asset_type; ?></td>
											<td><?php echo $asset->category_name; ?></td>
											<td><span class="pull-right"><a href="<?php echo base_url( 'webapp/asset/profile/'.$asset->asset_id ); ?>" title="Click to view the Asset profile" ><i class="fas fa-external-link-alt"></i> View</a> | <span class="unlink-asset pointer text-red" data-asset_id="<?php echo $asset->asset_id; ?>" title="Click to unlink this Asset from this contract"><i class="far fa-trash-alt"></i> Unlink</span></span></td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		<?php } else { ?>
			<div class="x_panel tile shadow">
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12 pull-left">
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12 pull-left">
								<legend>No Assets linked to this Contract </legend>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>

<script>
	$( document ).ready( function(){

		$( '.unlink-asset' ).click( function(){
			var assetId = $( this ).data( 'asset_id' );
			swal({
				type: 'error',
				text: 'Are you sure you want to un-link this Asset?',
			})
		});
	
		var search_str   			= null;
		var start_index	 			= 0;
		//load_data( search_str );

		//Do search when search field change
		$( '#search_term' ).on( "keyup", function( event ){
			event.preventDefault();
			var start_index = $( this ).find( 'a' ).data( 'ciPaginationPage' );
			var search_str  = $( '#search_term' ).val();
			load_data( search_str, start_index );
		});

		function load_data( search_str, start_index ){
			$.ajax({
				url:"<?php echo base_url( 'webapp/contract/asset_lookup' ); ?>",
				method:"POST",
				dataType: 'json',
				data:{ search_term:search_str, start_index:start_index },
				success:function( data ){
					$( '#contract_assets' ).html( data.assets );
				}
			});
		}
	});
</script>