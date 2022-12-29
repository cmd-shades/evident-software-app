<div class="floating-pallet">
	<div class="row">
		<?php if (!empty($account_admin_tabs)) {
		    foreach ($account_admin_tabs as $k => $module) { ?>
				<div class="col-md-2 col-sm-2 col-xs-4" id="more-modules" >
					<a href="<?php echo base_url('webapp/'.$this->router->fetch_class().'/profile/'.$account_profile_id.'/'.$module); ?>" class="btn btn-sm btn-<?php echo $module_identier; ?> btn-block" ><?php echo ucwords($module); ?></a>
				</div>
		<?php }
		    } else { ?>
			<div class="col-md-12 col-sm-12 col-xs-12 text-red"><em>The module items for this module have not been setup yet. Please contact your system admin</em></div>
		<?php } ?>
	</div>
	<div class="clear"></div>
</div>