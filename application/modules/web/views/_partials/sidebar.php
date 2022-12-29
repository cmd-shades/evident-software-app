<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
	<div class="menu_section">
		<ul class="nav side-menu">
			<li class="<?php echo ($active_class == 'dashboard' || $this->uri->segment(2) == 'home') ? 'active' : ''; ?>"><a href="<?php echo base_url('/webapp/home/index'); ?>"><i class="fas fa-home"></i><span class="hidden-sm hidden-xs"> Home</span> </a></li>
			<li class="<?php echo ($active_class == 'sites') ? 'active' : ''; ?>"><a disabled href="<?php echo base_url('/webapp/site/sites'); ?>"><i class="fas fa-th"></i> <span class="hidden-sm hidden-xs">Sites</span></a></li>
			<li class="<?php echo ($active_class == 'users') ? 'active' : ''; ?>"><a disabled href="<?php echo base_url('/webapp/user/users'); ?>"><i class="fas fa-user"></i> <span class="hidden-sm hidden-xs">Users</span></a></li>
			<!-- <li class="<?php echo ($active_class == 'panels') ? 'active' : ''; ?>"><a disabled href="<?php echo base_url('/webapp/panel/panels'); ?>"><i class="fas fa-cubes"></i> <span class="hidden-sm hidden-xs">Panels</span></a></li>
			<li class="<?php echo ($active_class == 'devices') ? 'active' : ''; ?>"><a disabled href="<?php echo base_url('/webapp/device/devices'); ?>"><i class="fas fa-bars"></i> <span class="hidden-sm hidden-xs">Devices</span></a></li>
			<li class="<?php echo ($active_class == 'audits') ? 'active' : ''; ?>"><a disabled href="<?php echo base_url('/webapp/audit/audits'); ?>"><i class="fas fa-folder-open"></i> <span class="hidden-sm hidden-xs">Audits</span></a></li>
			<li class="<?php echo ($active_class == 'reports') ? 'active' : ''; ?>" ><a disabled href="<?php echo base_url('/webapp/report/reports'); ?>"><i class="fas fa-bar-chart"></i> <span class="hidden-sm hidden-xs">Reports</span></a></li> -->
		</ul>
	</div>
</div>
<div class="sidebar-footer hidden-small">&nbsp;</div>