<div class="floating-pallet">
	<div class="rows">
		<div class="col-md-1 col-sm-1 col-xs-2" id="more-modules" >
			<a class="btn btn-sm btn-default btn-block" >Overview</a>
		</div>
		<?php if( !empty( $evidoc_categories ) ){ foreach( $evidoc_categories as $k => $category ){ ?>
			<div class="col-md-1 col-sm-1 col-xs-2" id="more-modules" >
				<a class="btn btn-sm btn-default btn-block" disabled ><?php echo ucwords( $category->category_name ); ?></a>
			</div>
		<?php } } ?>
	</div>
	<div class="clear"></div>
</div>