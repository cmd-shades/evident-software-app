<div id="settings-dashboard" class="row">
	<div class="x_panel no-border">
		<div class="row">
			<div class="x_content">
				<div class="row">
					<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
						<h2>Settings</h2>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="rows">
							<?php if( !empty( $module_access ) ){
								foreach( $module_access as $module ){ 
									if( in_array( $module->module_id, $allowed_modules ) ){	?>
										<div class="rows">
											<a href="<?php echo base_url( 'webapp/settings/module/'.$module->module_id ); ?>"><?php echo $module->module_name; ?></a>
										</div>
									<?php 
									}
								}
							} ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>