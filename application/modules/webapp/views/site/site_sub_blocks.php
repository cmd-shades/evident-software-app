<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<form id="update-sub_blocks-form" class="form-horizontal">
			<input type="hidden" name="site_id" value="<?php echo $site_details->site_id; ?>" />
			<input type="hidden" name="site_unique_id" value="<?php echo $site_details->site_unique_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<input type="hidden"  name="page" value="sub_blocks"/>
			<div class="x_panel tile has-shadow">
				<legend>Sub Blocks <span class="pull-right pointer add-sub_blocks"><i class="fas fa-plus text-green" title="Add New Sub Blocks" ></i> <small>New</small></span></legend>
				<div class="hide form-group" >
					<div class="drop-shaddow">
						<input type="text" id="search_term" class="black-bg form-control <?php echo $module_identier; ?>-search_input" value="" placeholder="Search sub_blocks..." />
					</div>
				</div>
				<div class="">
					<table class="sortable table table-responsive" style="margin-bottom:0; width:100%">
						<thead>
							<tr>
								<th width="25%">Name</th>
								<th width="25%">Address</th>
								<th width="20%">Number of Locations</th>
								<th width="20%">Number of Assets</th>
								<th width="10%"><span class="pull-right">Action</span></th>
							</tr>
						</thead>
						
						<tbody style="overflow-y:auto;" >
							<?php if( $sub_blocks ){ foreach( $sub_blocks as $k => $sub_block ){ ?>
							<tr>
								<td><?php echo ucwords( $sub_block->sub_block_name ); ?></td>
								<td><?php echo ucwords( $sub_block->sub_block_address ); ?> <?php echo ( !empty( $sub_block->sub_block_postcode ) ) ? strtoupper( $sub_block->sub_block_postcode ) : ''; ?></td>
								<td><?php echo ( !empty( $sub_block->sub_block_locations ) )? count( $sub_block->sub_block_locations )	: 0; ?></td>
								<td><?php echo ( !empty( $sub_block->sub_block_asssets ) ) 	? count( $sub_block->sub_block_asssets ) 	: 0; ?></td>
								<td>
									<span class="pull-right" >
										<span>
											<span class="text-green text-bold"><a href="<?php echo base_url( 'webapp/site/sub_block_profile/'.$sub_block->sub_block_id ); ?>"><i title="Click here to view this Sub Block record" class="fas fa-pencil-alt text-blue pointer"></i></a></span>
											&nbsp;&nbsp;
											<?php if( $this->user->is_admin || !empty( $permissions->can_delete ) || !empty( $permissions->is_admin ) ){ ?>
												<span class="text-green text-bold delete-sub_block-btn" data-sub_block_id = "<?php echo $sub_block->sub_block_id; ?>"><i title="Click here to delete this Sub Block record" class="fas fa-trash-alt text-red pointer"></i></span>
											<?php } ?>
										</span>
									</span>
								</td>
							</tr>
							<?php } } else { ?>
								<tr>
									<td colspan="5"><?php echo $this->config->item('no_records'); ?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</form>
	</div>
</div>

<div id="new-sub_block-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title" id="myModalLabel">Add Site Sub Blocks</h4>				
			</div>
			<div class="modal-body">
				<div class="">
					<form id="add-sub_blocks-form" >
						<input type="hidden" name="site_id" value="<?php echo $site_details->site_id; ?>" />
						<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
						<input type="hidden"  name="page" value="sub_blocks"/>
						
						<!-- <div class="input-group form-group">
							<label class="input-group-addon" >Building Postcode</label>
							<input type="text" id="site_postcodes" value="<?php echo !empty( $site_details->address_postcode ) ? $site_details->address_postcode : ''; ?>" class="form-control site-postcodes <?php echo $module_identier; ?>-search_input"  placeholder="Enter the address postcode..." >
							<span class="input-group-btn"><button id="find_address" class="btn btn-default <?php echo $module_identier; ?>-bg" type="button" >Find address</button></span>								
						</div>-->			
						<div class="dwelling-container" style="display:block" >				
							<div>
								<div class="input-group form-group">
									<label class="input-group-addon">Sub Block Name</label>
									<input name="sub_block_name" class="form-control" type="text" placeholder="Sub Block Name" value="" />
								</div>
								<div class="input-group form-group">
									<label class="input-group-addon">Sub Block Description</label>
									<textarea name="sub_block_desc" type="text" class="form-control" rows="2"></textarea>     
								</div>
								<div class="input-group form-group">
									<label class="input-group-addon">Sub Block Postcode</label>
									<input name="sub_block_postcode" class="form-control" type="text" placeholder="Sub Block Postcode" value="" />
								</div>
								<div class="input-group form-group">
									<label class="input-group-addon">Sub Block Address</label>
									<textarea name="sub_block_address" type="text" class="form-control" rows="2"></textarea>     
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<button id="add-sub_blocks-btn" class="btn btn-sm btn-block btn-flow btn-success add-sub_blocks-btn" type="button" >Add Sub-Block</button>					
								</div>
							</div>
						</div>
						
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){
		
		var search_str   = null;
		var start_index	 = 0;
		var where = { 	
			'site_id': '<?php echo $site_details->site_id;?>'
		};
		
		load_data( search_str, where, start_index );
		
		$( '#sub_blocks-results' ).on( 'click', '.unlink-item', function(){
			swal({
				type: 'info',
				text: 'Unlink Functionality coming soon'
			});
		});
		
		$( '.delete-sub_block-btn' ).click( function(){
			
			var siteId = "<?php echo $site_details->site_id ?>";
			var sub_blockId = $( this ).data( 'sub_block_id' );

			if( sub_blockId.length == 0 ){
				swal({
					type: 'error',
					html: 'Something went wrong, please refresh the page and try again!',
					showCancelButton: true,
					confirmButtonColor: '#5CB85C',
					cancelButtonColor: '#9D1919',
					confirmButtonText: 'Yes'
				});
				return false;
			}

			event.preventDefault();

			swal({
				type: 'warning',
				title: 'Confirm delete Sub Block?',
				html: 'This is an irreversible action and will affect associated Zones and Locations.!',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/site/delete_sub_block/'.$site_details->site_id.'/' ); ?>"+sub_blockId,
						method:"POST",
						data:{ page:'details' , xsrf_token: xsrfToken, site_id:siteId, sub_block_id:sub_blockId },
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
									location.reload();
								} ,1000);
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
		
		$('.site-postcodes').focus(function(){
			$( '.sub_block-container' ).slideDown( 'slow' );
		});

		// LOAD ADDRESSES WHEN MODAL OPENS
		$( '.add-sub_blocks' ).click( function(){
			$( "#new-sub_block-modal" ).modal( "show" );
			return false;
			var postCode = $( '#site_postcodes' ).val();
			var siteID 	 = '<?php echo $site_details->site_id;?>';
			if( postCode.length > 0 ){
				$.post( "<?php echo base_url("webapp/site/get_addresses_by_postcode"); ?>",{postcodes:postCode, site_id:siteID},function(result){
					$( "#building-addresses" ).html( result["addresses_list"] );				
					$("#new-sub_block-modal").modal( "show" );			
				},"json" );
			} else {
				$( "#new-sub_block-modal" ).modal( "show" );
			}
		});
		
		//LOAD ADDRESSES WHEN POSTCODE IS CHANGED IN THE MODAL
		$( '.site-postcodes' ).change(function(){
			var postCode = $(this).val();
			var siteID 	 = '<?php echo $site_details->site_id;?>';
			if( postCode.length > 0 ){
				$.post("<?php echo base_url("webapp/site/get_addresses_by_postcode"); ?>",{postcodes:postCode, site_id:siteID},function(result){
					$( "#building-addresses" ).html( result["addresses_list"] );			
				},"json");
			}
		});
		
		// SELECT ALL ADDRESSES
		$( '#building-addresses' ).on( 'change', '#check_all', function(){
			if( $( this ).is( ':checked' ) ){
				$( '.address-chks' ).each( function(){
					$( this ).prop( 'checked', true );
				});
			} else {
				$( '.address-chks' ).each( function(){
					$( this ).prop( 'checked', false );
				});
			}
		} );
		
		//Submit site form
		$( '.add-sub_blocks-btn' ).click(function( e ){
			e.preventDefault();
			var formData = $('#add-sub_blocks-form').serialize();
			swal({
				title: 'Add New Sub Block?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( (result) => {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/site/add_site_sub_block/' ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2000
								})
								window.setTimeout(function(){
									$( "#new-sub_block-modal" ).modal( "hide" );
									location.reload();
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
		});
		
		$("#sub_blocks-results").on( "click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find( 'a' ).data( 'ciPaginationPage' );
			load_data( search_str, where, start_index );
		});
		
		function load_data( search_str, where, start_index ){
			$.ajax({
				url:"<?php echo base_url( 'webapp/site/sub_blocks_lookup/'.$site_details->site_id ); ?>",
				method:"POST",
				data:{ search_term:search_str, where:where, start_index:start_index },
				success:function(data){
					$( '#sub_blocks-results' ).html( data );
				}
			});
		}
		
		$( '#search_term' ).keyup( function(){
			var search = encodeURIComponent( $(this).val() );
			if( search.length > 0 ){
				load_data( search , where );
			} else {
				load_data( search_str, where );
			}
		});
		
	});
</script>