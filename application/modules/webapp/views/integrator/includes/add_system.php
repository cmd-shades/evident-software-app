<div class="modal-body">
	<form id="adding-system-to-integrator-form" >
		<input type="hidden" name="integrator_id" value="<?php echo ( !empty( $integrator_details->system_integrator_id ) ) ? ( $integrator_details->system_integrator_id ) : '' ; ?>" />
		<div class="row">
			<div class="adding-system-to-integrator1 col-md-12 col-sm-12 col-xs-12">
				<div class="slide-group">
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<legend class="legend-header">What is the system type?</legend>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<h6 class="error_message pull-right" id="adding-territory-to-integrator1-errors"></h6>
						</div>
					</div>

					
					<div class="input-group form-group container-full">
						<label class="input-group-addon el-hidden">What is the system type?</label>
						<?php if( !empty( $remaining_systems ) ){ ?>
							<ul class="systems_list" title="System Name">
								<?php 
								$terr_numb 	= count( ( array ) $remaining_systems );
								$col11_num 	= ( $terr_numb % 2 == 1 ) ? floor( $terr_numb / 2 ) + 1 : ( $terr_numb / 2 ) ;
								
								$i = 1;
								$coll1 = $coll2 = false;
								
								foreach( $remaining_systems as $ter_row ){
									
									if( $i <= $col11_num ){
										if( !$coll1 ){ ?>
										<div class="col_1">
											<li>
												<label for="all_systems">
													<input type="checkbox" id="all_systems" value="" /> 
													<span class="system_name">All Systems</span>
												</label>
											</li>
										<?php 
										$coll1 = true; } ?>
										<li>
											<label for="<?php echo ( strtolower( $ter_row->system_type_id ) ); ?>">
												<input type="checkbox" name="integrator_systems[]" id="<?php echo ( strtolower( $ter_row->system_type_id ) ); ?>" value="<?php echo ( !empty( $ter_row->system_type_id ) ) ? $ter_row->system_type_id : '' ; ?>" /> <span class="territory_name weak"><?php echo ucwords( strtolower( $ter_row->name ) ); ?></span>
											</label>
										</li>

										<?php 
										if( $i ==  $col11_num ){ echo '</div>';	}
									} else {
										if( !$coll2 ){ echo '<div class="col_2">'; $coll2 = true; } ?>
										<li>
											<label for="<?php echo ( strtolower( $ter_row->system_type_id ) ); ?>">
												<input type="checkbox" name="integrator_systems[]" id="<?php echo ( strtolower( $ter_row->system_type_id ) ); ?>" value="<?php echo ( !empty( $ter_row->system_type_id ) ) ? $ter_row->system_type_id : '' ; ?>" /> <span class="territory_name weak"><?php echo ucwords( strtolower( $ter_row->name ) ); ?></span>
											</label>
										</li>
									<?php
									} ?>
								<?php 
								$i++; } ?>
								</div>
							</ul>
						<?php
						} else { ?>
							<p>No System have been set or all System types are already added.</p>
						<?php
						} ?>
					</div>

					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12 pull-right">
							<button class="btn-block btn-next" data-currentpanel="adding-territory-to-integrator3" type="submit">Add System</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>