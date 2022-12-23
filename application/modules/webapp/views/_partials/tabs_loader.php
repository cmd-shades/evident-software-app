<div class="floating-pallet">
	<div class="rows">
		<?php if( !empty( $module_tabs ) ){ foreach( $module_tabs as $k=>$module ){ ?>
			<div class="col-md-2 col-sm-3 col-xs-4">
				<a class="btn-<?php echo $module_identier; ?> btn-distro <?php echo ( $active_tab != $module->module_item_tab ) ? 'shadow-'.$module_identier : ''; ?>" href="<?php echo base_url("webapp/".$this->router->fetch_class()."/profile/".$this->uri->segment(4)."/".$module->module_item_tab ); ?>" role="button"><?php echo ucwords( $module->module_item_name ); ?></a>
			</div>
		<?php } }else{ ?>
			<div class="col-md-12 col-sm-12 col-xs-12 text-red"><em>The module items for this module have not been setup yet. Please contact your system admin</em></div>
		<?php } ?>
	</div>
	<div class="clear"></div>
</div>
