<div class="row">
	<?php if( $this->user->is_admin || !empty( $permissions->can_add ) || !empty( $permissions->can_view ) || !empty( $permissions->is_admin ) ){ ?>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel tile has-shadow">
				<legend>Assets assigned to this Building <span class="pull-right pointer" >Grand Total: <?php echo !empty( $total_assets ) ? $total_assets : 0 ;?></span></legend>
				
				<div class="accordion" id="accordion" role="tablist" aria-multiselectable="true">
					<?php if( !empty( $assigned_assets )){ $counter = 1; ?>
						<?php foreach( $assigned_assets as $floor_id => $floor_assets_data ){ $counter++; ?>
							<div class="panel">
								<div class="section-container-bar panel-heading collapsed bg-grey pointer no-radius" role="tab" id="heading<?php echo number_to_words( $counter ); ?>" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo number_to_words( $counter ); ?>" aria-expanded="true" aria-controls="collapse<?php echo number_to_words( $counter ); ?>">
						<h4 class="panel-title"><i class="caret-icon fas fa-caret-down text-yellow"></i> <?php echo ( !empty( $floor_assets_data->sub_block_name ) ) ? ucwords( $floor_assets_data->sub_block_name ).' - ' : ''; ?> <?php echo !empty( $floor_assets_data->floor_name ) ? ucwords( $floor_assets_data->floor_name ) : '<span class="text-yellow">[ZONE NAME NOT SET]</span>'; ?> <span class="pull-right">(<?php echo $floor_assets_data->total_assets; ?>)<span></h4>
								</div>
								
								<div id="collapse<?php echo number_to_words( $counter ); ?>" class="panel-collapse collapse no-bg no-background" role="tabpanel" aria-labelledby="heading<?php echo number_to_words( $counter ); ?>" >
									<div class="panel-body">
										<div>
											<table class="table table-responsive" style="width:99.5%">
												<?php $kv = 1; foreach( $floor_assets_data->floor_assets as $asset_category => $asset_category_data ){ $kv++; ?>
													
													<?php 
														$category_total = 0;
														foreach( $grouped_assets as $k => $category_data ){
															if( strtolower( $asset_category ) == strtolower( $category_data->category_name ) ){
																$category_total = $category_data->total_assets;
																break;
															}
														}

														$type_counter = 0;
														foreach( array_values ( object_to_array( $asset_category_data ) ) as $ax => $ax_assets ) {
															foreach( $ax_assets as $axa ){
																$type_counter++;
															}
														}
													?>
													
													<tr class="asset-categories-toggle pointer bg-grey acategory-<?php echo lean_string( $asset_category ); ?>" data-asset_category_ref="<?php echo lean_string( $asset_category ); ?>" >
														<th width="70%"><i class="caret-icon fas fa-caret-down"></i> <?php echo $asset_category; ?></th>
														<th><span class="pull-right" ><?php echo $type_counter; ?><span></th>
													</tr>
													
													<tr class="<?php echo lean_string( $asset_category ); ?> asset-types pointer" style="display:none" >
														<td colspan="2" >
															<table class="table-responsive" width="100%" >
																<?php foreach( $asset_category_data as $asset_type => $assets ){ ?>
																	
																	
																	
																	<tr class="asset-types-toggle pointer atype-<?php echo lean_string( $asset_type ); ?>" data-asset_type_ref="<?php echo lean_string( $asset_type ); ?>" >
																		<th width="70%"><i class="caret-icon fas fa-caret-down"></i> <?php echo $asset_type; ?></th>
																		<th><span class="pull-right" ><?php echo count( $assets ); ?><span></th>
																	</tr>
																	
																	<tr class="<?php echo lean_string( $asset_type ); ?> asset-types pointer" style="display:none" >
																		<td colspan="2" >
																			<table class="table-responsive" style="margin-left:20px;" width="100%" >
																				<?php foreach( $assets as $key => $asset ){ ?>
																					<tr>
																						<td width="20%" ><a href="<?php echo base_url( '/webapp/asset/profile/'.$asset->asset_id ) ?>" ><?php echo $asset->asset_unique_id; ?></a></td>
																						<td><?php echo ucwords( $asset->attribute_value ); ?></td>																
																					</tr>
																				<?php } ?>
																			</table>
																		</td>													
																	</tr>
																<?php } ?>
															</table>
														</td>													
													</tr>
													
												<?php } ?>
											</table>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
					<?php } ?>
				</div>
			</div>		
		</div>
	<?php } ?>
</div>

<script>
	$(document).ready(function(){
		
		$( '.asset-types-toggle' ).click( function(){
			var typeRef = $( this ).data( 'asset_type_ref' );
			$( '.atype'+typeRef ).closest( 'tr' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');
			$( '.'+typeRef ).slideToggle();
		});
		
		$( '.asset-categories-toggle' ).click( function(){
			var typeRef = $( this ).data( 'asset_category_ref' );
			$( '.acategory'+typeRef ).closest( 'tr' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');
			$( '.'+typeRef ).slideToggle();
		});
		
		$( '.section-container-bar' ).click( function(){
			$( this ).closest( 'div' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');			
		});
	
	});
</script>