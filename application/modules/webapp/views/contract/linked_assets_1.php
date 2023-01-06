<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<?php if( !empty( $linked_sites ) ){ ?>
			<div class="x_panel tile shadow">
				<div class="rows">
					<legend>Sites linked to Contract No <strong><?php echo $profile_data[0]->contract_id; ?></strong></legend>
					<div class="control-group form-group">					
						<table>
							<tr>
								<th class="width_60">Site ID</th>
								<th class="width_160">Site Name</th>
								<th class="width_240">Site Address</th>
								<th class="width_80">Site Reference</th>
								<th class="width_80">Site Postcodes</th>
								<th class="width_120">Site Created</th>
								<th class="width_120">Site Linked By</th>
								<th class="width_80">Action</th>
							</tr>
						</table>
					</div>
					<div class="control-group form-group table_body">
						<table class="table linked_sites">
							<tbody>
								<?php foreach( $linked_sites as $row ){ ?>
									<tr>
										<td data-label="Site ID" class="width_60"><a href="<?php echo base_url( 'webapp/site/profile/'.$row->site_id ); ?>"><?php echo str_pad( $row->site_id, 4, '0', STR_PAD_LEFT ); ?></a></td>
										<td data-label="Site Name" class="width_160"><?php echo $row->site_name; ?></td>
										<td data-label="Summary Line" class="width_240"><?php echo $row->summaryline; ?></td>
										<td data-label="Site Reference" class="width_80"><?php echo $row->site_reference; ?></td>
										<td data-label="Site Postcodes" class="width_80"><?php echo $row->site_postcodes; ?></td>
										<td data-label="Date Created" class="width_120"><?php echo $row->date_created; ?></td>
										<td data-label="Date By" class="width_120"><?php echo $row->created_by_fullname; ?></td>
										<td data-label="Unlink Site" class="width_80">
											<?php if( $this->user->is_admin || !empty( $permissions->can_delete ) || !empty( $permissions->is_admin ) ){ ?>
												<a class="unlink_site_btn" onclick="return confirm( 'Are you sure you want to Unlink the site from the Contract?' );" href="<?php echo base_url( "webapp/contract/unlink_site/".$row->site_id."/".$profile_data[0]->contract_id."/linked_sites" ); ?>">Unlink Site</a>
											<?php } ?>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="rows" style="margin-top: 10px;">
					<a class="btn btn-sm btn-flow btn-success btn-next pull-right no_right_margin" id="linkSitesWFButton">Link Site to this Contract &nbsp;<i class="fas fa-chevron-down"></i></a>
				</div>
			</div>
		<?php } else { ?>
			<div class="x_panel tile shadow">
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12 pull-left">
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12 pull-left">
								<legend>No Sites linked to this Contract </legend>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12 pull-left">
								<a class="btn btn-sm btn-flow btn-success btn-next pull-left" id="linkSitesWFButton">Link Site to this Contract &nbsp;<i class="fas fa-chevron-down"></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="tile shadow link_sites" style="display: none;">
			<div class="x_panel" style="border-bottom: none;">
				<div class="rows">
					<form action="<?php echo base_url( "webapp/contract/link_sites" ) ?>" method="post" name="linkSitesWFForm" id="linkSitesWFForm">
						<input type="hidden" name="postdata[contract_id]" value="<?php echo $profile_data[0]->contract_id; ?>" />
						<input type="hidden" name="postdata[account_id]" value="<?php echo $profile_data[0]->account_id; ?>" />
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<legend>Link Sites to this Contract</legend>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<div class="control-group form-group">
									<div class="controls">
										<label>Find the site by typing the Site Name:</label>
										<input type="text" id="search_term" class="form-control" value="" />
									</div>
								</div>
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<div class="control-group form-group">
									<div class="controls">
										<label>Provide the Site ID. Multiple ID's must be comma separated:</label>
										<input type="text" name="postdata[sites]" class="form-control" value="" />
									</div>
								</div>
							</div>
						</div>
						
						<?php if( $this->user->is_admin || !empty( $permissions->can_add ) || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
							<div class="row" style="margin-bottom: 6px;">
								<div class="col-md-6 col-sm-12 col-xs-12 pull-right">
									<button type="submit" class="btn btn-sm btn-flow btn-primary btn-success pull-right no_right_margin" id="linkSites">Link Sites</button>
								</div>
							</div>
						<?php } else { ?>
							<div class="row" style="margin-bottom: 6px;">
								<div class="col-md-6 col-sm-12 col-xs-12 pull-right">
									<button class="btn btn-sm btn-flow btn-success btn-next right no-permissions" type="button" disabled >Insufficient permissions</button>
								</div>
							</div>
						<?php } ?>

						<div class="rows">
							<div class="col-md-12 sites_2link_wrapper">
								<table class="table linked_sites" id="contract_sites">
								</table>
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

		var search_str   			= null;
		var start_index	 			= 0;
		load_data( search_str );

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
					$( '#contract_sites' ).html( data.sites );
				}
			});
		}
	});
</script>