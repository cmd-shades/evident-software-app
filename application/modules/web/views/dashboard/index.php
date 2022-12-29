<?php
$container_column = 'col-md-12 col-sm-12 col-xs-12';
$inner_columns = 'col-md-2 col-sm-4 col-xs-6 col-centered';
?>

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