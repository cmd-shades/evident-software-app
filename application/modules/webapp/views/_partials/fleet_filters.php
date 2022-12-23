<?php 
foreach( $filters as $filter ){ ?>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 min_height_70">
			<div class="single_filter closed shaded fleet-bg" style="position: absolute;"> 
				<label><?php echo $filter['filter_name']; ?> &nbsp;<i class="fas fa-caret-down"></i></label>
				<ul class="chk10" style="display: none;">
					<li class="chk1">
						<span class="checkbox">
							<label>
								<input type="checkbox" id="check-all" checked="checked"> All
								<span class="pull-right unchecked"></span>
							</label>
						</span>
					</li>
					<?php 
					if( !empty( $filter['filter_data'] ) ){ 
						foreach( $filter['filter_data'] as $k => $row ){
						$keys = array_keys( get_object_vars( $row ) ); ?>
						<li>
							<span class="checkbox">
								<label>
									<input type="checkbox" class="user-types" name="vehicle_statuses[]" checked=checked value="<?php echo $row->{ $keys[0] }; ?>" > <?php echo ucwords( $row->{ $keys[2] } ); ?>
									<span class="pull-right unchecked"></span>
								</label>
							</span>
						</li>
						<?php 
						} 
					} ?>
				</ul>
			</div>
		</div>
	</div>
<?php 
} ?>