<?php $container_column = 'col-md-12 col-sm-12 col-xs-12'; ?>
<?php if ($module_count > 0) {
    $inner_columns 	  = 'col-md-2 col-sm-4 col-xs-6 col-centered';
    switch($module_count) {
        case 0:
        case 1:
        case 2:
        case 3:
            #$container_column = 'col-md-10 col-md-offset-1 col-sm-6 col-md-offset-3 col-sm-12 col-xs-12';
            $inner_columns 	  = 'col-md-2 col-sm-4 col-xs-6 col-centered';
            break;
        case 4:
            $container_column = 'col-md-4 col-md-offset-4';
            $inner_columns 	  = 'col-md-6 col-sm-4 col-xs-6';
            break;
        case 5:
        case 6:
        case 9:
            $container_column = 'col-md-6 col-md-offset-3';
            $inner_columns 	  = 'col-md-4 col-centered';
            break;
        case 7:
        case 8:
            #case 10:
        case 11:
        case 12:
            $container_column = 'col-md-8 col-md-offset-2';
            $inner_columns 	  = 'col-md-3 col-centered';
            break;
        case 10:
        case 13:
        case 14:
        case 15:
            $container_column = 'col-md-10 col-md-offset-1';
            $inner_columns 	  = 'col-md-5th-1 col-centered';
            break;
        default:
            $inner_columns = 'col-md-2 col-sm-4 col-xs-6 col-centered';
            break;
    }
} else {
    $container_column = 'col-md-12 col-sm-12 col-xs-12';
}?>

<div class="row">
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
		
		<div class="row row-centered">
			<div class="module-container <?php echo $container_column; ?>">
					<?php if (!empty($permitted_modules)) { ?>
						<?php foreach ($permitted_modules as $k => $module_details) { ?>
							<div class="<?php echo $inner_columns; ?>">
								<a href="<?php echo base_url('/webapp/'.$module_details->module_controller); ?>">
									<div class="x_panel no-border module-item bg-transparent">
										<img style="width:100%" src="<?php echo base_url($module_details->app_img_url); ?>" />
										<h6><?php //echo $module_details->module_name;?></h6>
									</div>
								</a>
							</div>
						<?php } ?>
					<?php } else { ?>
						<div class="col-md-6 col-sm-offet-3 col-sm-8 col-sm-offet-2 col-xs-12">
							<p>You do not currently have any module access permissions.</p>
						</div>
					<?php } ?>
				
			</div>
		</div>
	</div>
</div>