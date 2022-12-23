<div class="row group-content el-hidden">
	<div class="row">
		<?php
		$todays_date = date( 'Y-m-d' );
		if( !empty( $provider_details->territories ) ){
			foreach( $provider_details->territories as $ter_row ){?>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<div class="row territories-list-item container-full">
						<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
							<span class=""><?php echo ( !empty( $ter_row->country ) ) ? $ter_row->country : '' ; ?></span>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pull-right">
							<span class="delete-territory" data-territory_id="<?php echo ( !empty( $ter_row->territory_id ) ) ? $ter_row->territory_id : '' ;?>"><div class=""><a href="#"><i class="fas fa-trash-alt"></i></a></div></span>
						</div>
					</div>
				</div>
			<?php
			}
		} ?>
	</div>
</div>