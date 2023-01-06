<style>
	.btn.btn-app {
		position: relative;
		padding: 12px 5px;
		margin: 15px;
		min-width: 25%;
		height: 110px;
		box-shadow: none;
		border-radius: 0;
		text-align: center;
		color: #fff;
		border: 1px solid #0092cd;
		background-color: #0092cd;
		font-size: 12px;
	}
	
	.btn.btn-app:hover {
		background: #EBC700;
		color: #444;
		border-color: #EBC700;
	}
	
	.btn.btn-app>.fa, .btn.btn-app>.glyphicon, .btn.btn-app>.ion {
		font-size: 25px;
		display: block;
		margin-top: 15px;
	}
</style>
<div class="col-md-4 col-md-offset-4 col-sm-12 col-xs-12">
	<legend class="evidocs-legend">Select your Module Settings</legend>		
	<div class="x_panel no-border has-shadow">
		<div class="x_content">
			<div class="box-body">
				<div style="text-align:center">
					<?php if( !empty( $permitted_modules ) ){ foreach( $permitted_modules as $key => $module ){ ?>
					<a href="<?php echo base_url('webapp/settings/module/'.$module->module_id ); ?>" class="btn btn-app">
						<i class="fa fa-cogs"></i> 
						<div style="margin-top:10px;"><?php echo $module->module_name; ?></div>
					</a>
					<?php } } else { ?>
						<div>
							<p>You do not currently have any modules setup.</p>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>		
</div>


