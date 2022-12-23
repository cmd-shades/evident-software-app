<style type="text/css">
.nav-sm {
    background-color: #e9e9ea;
}

.module-item > .img-responsive{
	padding: 5% 20%;
}

.module-unit{
	margin-bottom: 0;
	padding: 40px;
}

a.module_link{
    display: block;
    margin-bottom: 15px;
}
</style>

<?php 
$container_column = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';

if( $module_count > 0 ){
	$inner_columns 	  = 'col-md-2 col-sm-4 col-xs-6';
	switch( --$module_count ){  ## decreased by 1 because of the product hidden module
		case 0:
		case 1:
		case 2:
		case 3:
		case 4:
			#$container_column = 'col-md-10 col-md-offset-1 col-sm-6 col-md-offset-3 col-sm-12 col-xs-12';
			$inner_columns 	  = 'col-lg-3 col-md-3 col-sm-4 col-xs-6';
			break;
		case 5:
		case 7:
		case 8:
			$container_column = 'col-lg-12 col-md-12 col-sm-12';
			$inner_columns 	  = 'col-lg-3 col-md-4 col-sm-6 col-xs-6';
			break;
		#case 10:
		/* case 11:
		case 12:
			$container_column = 'col-md-8 col-md-offset-2';
			$inner_columns 	  = 'col-md-3';
			break; */
			
		case 6:
			$container_column = 'col-md-8 col-md-offset-2';
			$inner_columns 	  = 'col-md-4';
			break;
		case 9:
		case 10:
		case 11:
		case 12:
		case 13:
		case 14:
		case 15:		
			/* $container_column = 'col-md-10 col-md-offset-1'; */
			$container_column = 'col-md-12';
			/* $inner_columns 	  = 'col-md-5th-1'; */
			$inner_columns 	  = 'col-md-3 col-sm-4 col-xs-6';
			break;
		default:
			$inner_columns = 'col-md-2 col-sm-4 col-xs-6';
			break;
	}
} else {
	$container_column = 'col-md-12 col-sm-12 col-xs-12';	
}?>

<div class="row home">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="hide row">
			<div class="home-search-form">
				<div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 col-xs-12 top_search">	
					<div class="input-group">
						<input type="text" class="form-control" id="search_term" placeholder="Search for...">
						<span class="input-group-btn">
							<button class="btn btn-default" type="button">Go!</button>
						</span>
					</div>				
					<br/>
				</div>
			</div>
			<div id="home-search-results"></div>						
		</div>
		
		<div class="row row-centered row">
			<div class="module-container <?php echo $container_column; ?>">
				<?php if( !empty( $permitted_modules ) ){ ?>
					<?php foreach( $permitted_modules as $k => $module_details ){ 
						if( $module_details->module_id != 8 ){ ?>
						<div class="module-unit <?php echo $inner_columns; ?>">
							<div class="row">
								<a class="module_link" href="<?php echo base_url( '/webapp/'.$module_details->module_controller ); ?>">
									<div class="no-border module-item bg-transparent col-centered">
										<img class="img-responsive module-image" src="<?php echo base_url( $module_details->app_img_url ); ?>" />
									</div>
								</a>
							</div>
							<div class="row">
								<?php if( $module_details->create_new_item_link ){ ?>
									<a class="new_item_link btn btn-home" href="<?php echo (!empty( $module_details->create_new_item_link ) ) ? base_url( $module_details->create_new_item_link ) : "" ; ?>">Add <?php echo (!empty( $module_details->module_controller ) ) ? ucfirst( $module_details->module_controller ) : "" ;  ?></a>
								<?php } else { ?>
									<span class="" style="height: 34px;">&nbsp;</span>
								<?php } ?>
							</div>
						</div>
					<?php } 
					} ?>
				<?php } else { ?>
					<div class="col-md-6 col-sm-offet-3 col-sm-8 col-sm-offet-2 col-xs-12">
						<p>You do not currently have any module access permissions.</p>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>