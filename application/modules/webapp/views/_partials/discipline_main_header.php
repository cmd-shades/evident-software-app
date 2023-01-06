<?php 
	
	if( !isset( $_COOKIE['daterange'] ) ){ 
		$_COOKIE['daterange']='7';
	}
	$sessiondate = $_COOKIE['daterange'];

	require('./application/modules/webapp/views/dashboard/functions/functions.php');
	//Get the current discipline name
	$disciplinename = getDisciplineName( $root_folder );
?>
<!DOCTYPE html>
<html class="no-js">
	<!-- Head START -->
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title><?php echo ( !empty( $page_title ) ? $page_title : APP_NAME ); ?></title>
		<!-- Bootstrap styles START -->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.fonts.css'); ?>" media="screen"/>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" media="screen"/>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/font-awesome.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.settings.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/jquery.datetimepicker.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/jquery-ui.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/adminlte-wrapper.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/core-skin.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.main.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.settings.css') ?>" media="screen">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" />
		<!-- Bootstrap styles END -->
		<!-- Favicon START -->
		<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url(); ?>/favicon.png">
		<!-- Favicon END -->
		
		<!-- Dashboard Compiled SASS START -->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom/style.css') ?>" media="screen">
		<!-- Dashboard Compiled SASS END -->

				
		<script src="<?php echo base_url('assets/js/jquery.min.js'); ?>" type="text/javascript"></script>
		<script>function loadtime() {
   var today = new Date();
    var hr = today.getHours();
    var min = today.getMinutes();
    var sec = today.getSeconds();
    ap = (hr < 12) ? "<span>AM</span>" : "<span>PM</span>";
    hr = (hr == 0) ? 12 : hr;
    hr = (hr > 12) ? hr - 12 : hr;
    //Add a zero in front of numbers<10
    hr = checkTime(hr);
    min = checkTime(min);
    sec = checkTime(sec);
    document.getElementById("clock").innerHTML = hr + ":" + min + ":" + sec + " " + ap;
	document.getElementById("clock2").innerHTML = hr + ":" + min + ":" + sec + " " + ap;

    
    var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    var curWeekDay = days[today.getDay()];
    var curDay = today.getDate();
    var curMonth = months[today.getMonth()];
    var curYear = today.getFullYear();
    var date = curWeekDay+", "+curDay+" "+curMonth+" "+curYear;
    document.getElementById("date").innerHTML = date;
	document.getElementById("date2").innerHTML = date;

    
    var time = setTimeout(function(){ startTime() }, 500);
		}
			jQuery(document).ready(function() {
    if (window.innerWidth < 480) { //change this value for your convenience
        $(".chartoverview").click(function() {
            $(this).toggleClass("reveal-show");
        })
    }

    $(".chartoverview").hover(function() {
        $(this).toggleClass("reveal-show");
    })
})</script>
<script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>




		<script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script></head>
	<!-- Head END -->

	<!-- Body START -->
	<body onload="loadtime()" class="hold-transition core-skin fixed layout-top-nav darkskin">
		<div class="wrapper">

			<!-- Header/Nav START -->
			<header class="main-header">
				<!-- Determine the header colour START -->
				<!-- Nav START -->
				<nav class="navbar navbar-static-top <?php echo $disciplinename;?>">
				<!-- Determine the header colour END -->
					<div class="container pt-15">
						<div class="navbar-header">
							<!-- Evident logo START -->
							<a href="<?php echo base_url('/webapp/dashboard/index'); ?>" class="navbar-brand">
								<img src="<?php echo base_url('application/modules/webapp/views/dashboard/images/logo.png'); ?>" />
							</a>
							<!-- Evident logo END -->
							
							<!-- Mobile menu toggle START -->
							<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
								<i class="fa fa-bars"></i>
							</button>
							<!-- Mobile menu toggle END -->

							<!-- Mobile - user icon and last updated time START -->
							<div class="dash-user">
								<img height="19" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/user.svg'); ?>" class="hidden-medium"/>
								<small class="hidden-medium">LAST UPDATED: 2:03PM</small>
							</div>
							<!-- Mobile - user icon and last updated time END -->
						</div>

						<!-- Primary menu START-->
						<div class="collapse navbar-collapse pull-left" id="navbar-collapse">
							<ul class="nav navbar-nav">
								<!-- User welcome and time START -->
								<li class="hidden-medium mobile-welcome" >
									<a href="<?php echo base_url('/webapp/user/login'); ?>">
										<span>Welcome 
											<strong><?php echo ( !empty( $this->user->first_name  ) ) ? ucwords( $this->user->first_name ) : ''; ?></strong>
										</span> 
										<img height="19" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/cog.svg'); ?>"/>
										<br>
										<!-- Current time START -->
										<small>
											<div id="date"></div>
											<div id="clock"></div>
										</small>
										<!-- Current time END -->
									</a>
								</li>
								<!-- User welcome and time END -->
								<li class="<?php echo ( $active_class == 'dashboard' || $this->uri->segment(2) == 'home' ) ? 'active' : ''; ?>" >
									<a href="<?php echo base_url('/webapp/dashboard/index'); ?>">
										Home
										<span class="sr-only">(current)</span>
									</a>
								</li>
								
								<?php /* if( !empty( $permitted_modules ) ){ ?>
									<li class="<?php echo ( ( $active_class != 'dashboard' ) && ( $this->uri->segment(2) != 'home'  ) ) ? 'active' : ''; ?>">
										<a class="dropdown-toggle pointer" data-toggle="dropdown">Modules <span class="caret"></span></a>
										<ul class="dropdown-menu" role="menu">
											<?php foreach( $permitted_modules  as $k => $module ){ ?>
												<?php
												if ( !empty( $module->is_pluralised ) ){
													switch( $module->module_controller ){
														case "fleet":
															$module_home = "vehicles";
															break;

														case "people":
															$module_home = "people";
															break;

														case "diary":
															$module_home = "diary";
															break;

														default:
															$module_home = $module->module_controller.'s';
													}
												} else {
													$module_home = $module->module_controller.'s';
												} ?>
												<li><a href="<?php echo base_url('/webapp/'.$module->module_controller.'/'.$module_home ); ?>" <?php ?> ><?php echo $module->module_name; ?></a></li>
											<?php } ?>
											<li>
										</ul>
									</li>
									<?php 
									$quickbar_modules = $this->webapp_service->get_quickbar_modules($this->user->account_id);
									if( $quickbar_modules ){
										foreach($quickbar_modules as $module){
											foreach($permitted_modules as $perm_module){
												if($perm_module->module_id == $module->module_id){
													echo '<li><a style="font-weight:normal;" href="' . base_url("webapp/" . $perm_module->module_url_link) . '">' . $perm_module->module_name . '</a></li>';
												}
											}
										}
									} else {
										$default_bar_modules = array(1, 3, 4, 8);
										foreach($default_bar_modules as $module_id){
											foreach($permitted_modules as $perm_module){
												if($perm_module->module_id == $module_id){
													echo '<li><a style="font-weight:normal;" href="' . base_url("webapp/" . $perm_module->module_url_link) . '">' . $perm_module->module_name . '</a></li>';
												}
											}
										}
									} ?>
									<li ><a style="font-weight:normal;" class='customize-quickbar pointer'><small><i style="font-size:15px" class="fas fa-cog"></i></small></a></li>
								<?php }*/ ?>
								
								<li>
									<a href="#">
										Modules
										<span class="sr-only">(current)</span>
									</a>
								</li>
								<li>
									<a href="<?php echo base_url('/webapp/site/sites'); ?>">
										Buildings
										<span class="sr-only">(current)</span>
									</a>
								</li>
								<li>
									<a href="<?php echo base_url('/webapp/audit/audits'); ?>">
										EviDocs
										<span class="sr-only">(current)</span>
									</a>
								</li>
								<li>
									<a href="<?php echo base_url('/webapp/job/jobs'); ?>">
										Jobs
										<span class="sr-only">(current)</span>
									</a>
								</li>
								<li>
									<a href="<?php echo base_url('/webapp/asset/assets'); ?>">
										Assets
										<span class="sr-only">(current)</span>
									</a>
								</li>
								<li>
									<a href="<?php echo base_url('/webapp/report/reports'); ?>">
										Reports
										<span class="sr-only">(current)</span>
									</a>
								</li>
								<!-- Cog link desktop only START -->
								<li class="hidden-xs" >
									<a target="_blank" href="<?php echo base_url('/webapp/config/'); ?>">
										<img height="20" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/cog.svg'); ?>"/>
										<span class="sr-only">(current)</span>
									</a>
								</li>
								<!-- Cog link desktop only END -->
							</ul>
						</div>
						<!-- Primary menu END -->
						
						<!-- Desktop - user welcome and time START -->
						<div class="dash-user">
							<span>Welcome 
								<strong><?php echo ( !empty( $this->user->first_name  ) ) ? ucwords( $this->user->first_name ) : ''; ?></strong>
							</span> 
							<img height="19" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/user.svg'); ?>" class="fright"/>
							<br>
							<!-- Current date and time START -->
							<small>
								<div id="date2"></div>
								<div id="clock2"></div>
							</small>
							<!-- Current date and time END -->
						</div>
						<!-- Desktop - user welcome and time END -->
						
						<!-- Clear the primary menu START -->
						<div class="clearfix"></div>
						<!-- Clear the primary menu END -->

						<!-- Breadcrumbs START -->
						<div class="breadcrumbs">
							<a href="<?php echo base_url('/webapp/dashboard/index'); ?>">Home</a>
							<a href="<?php echo base_url('/webapp/dashboard/'.$disciplinename); ?>" class="active"><?php echo $disciplinename;?></a>
						</div>
						<!-- Breadcrumbs END -->

						<!-- Secondary menu - for buildings only START -->
						<?php 
							if ($disciplinename == 'building') { //Is the current page a building?
						?>

						<!-- Mobile building menu START -->
						<div class="current disciplines small">
							<!-- Fixed dashboard link START -->
							<ul>
								<li class="<?php echo ( $active_class == 'dashboard' || $this->uri->segment(2) == 'home' ) ? 'active' : ''; ?>" >
									<a href="<?php echo base_url('/webapp/dashboard/building'); ?>">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/overview.svg'); ?>"/>
										<span>Dashboard
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
							</ul>
							<!-- Fixed dashboard link END -->
						</div>
						
						<!-- Scrollable links START -->
						<div class="disciplines small">
							<ul>
								<li>
									<a href="#">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/details.svg'); ?>"/>
										<span>Details
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/subblocks.svg'); ?>"/>
										<span>Sub Blocks
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/systems.svg'); ?>"/>
										<span>Systems
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/assets.svg'); ?>"/>
										<span>Assets
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/evidocs.svg'); ?>"/>
										<span>EviDocs
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/map.svg'); ?>"/>
										<span>Map
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/people.svg'); ?>"/>
										<span>People
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
							</ul>
						</div>
						<!-- Scrollable links END -->
						<!-- Mobile building menu END -->

						<!-- Desktop building menu START -->
						<div class="disciplines large">
							<ul>
								<li class="current <?php echo ( $active_class == 'dashboard' || $this->uri->segment(2) == 'home' ) ? 'active' : ''; ?>" >
									<a href="<?php echo base_url('/webapp/dashboard/building'); ?>">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/overview.svg'); ?>"/>
										<span>Dashboard
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/details.svg'); ?>"/>
										<span>Details
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/subblocks.svg'); ?>"/>
										<span>Sub Blocks
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/systems.svg'); ?>"/>
										<span>Systems
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/assets.svg'); ?>"/>
										<span>Assets
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/evidocs.svg'); ?>"/>
										<span>EviDocs
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/map.svg'); ?>"/>
										<span>Map
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
								<li>
									<a href="#">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/people.svg'); ?>"/>
										<span>People
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
							</ul>
						</div>
						<!-- Desktop building menu END -->
						
						<!-- Secondary menu - for buildings only END -->

						<?php }
							else {
						?>

						<!-- Secondary mobile menu START -->
						<div class="current disciplines small">
							<!-- Fixed active link START -->
							<ul>
								<li class="<?php echo ( $active_class == 'dashboard' || $this->uri->segment(2) == 'home' ) ? 'active' : ''; ?>" ><a href="<?php echo base_url('/webapp/dashboard/'.$disciplinename); ?>"><img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/'.$disciplinename.'.svg'); ?>"/><span><?php echo $disciplinename;?><span class="sr-only">(current)</span></span></a></li>
							</ul>
							<!-- Fixed active link END -->
						</div>

						<!-- Scrollable links START -->
						<div class="disciplines small">
								<ul>
								<?php if($disciplinename !== 'overview'){?>
									<li class="<?php echo ( $active_class == 'dashboard' || $this->uri->segment(2) == 'home' ) ? 'active' : ''; ?>" >
										<a href="<?php echo base_url('/webapp/dashboard/index'); ?>">
											<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/overview.svg'); ?>"/>
												<span>Overview<span class="sr-only">(current)</span>
											</span>
										</a>
									</li>
								<?php };?>
								<?php if($disciplinename !== 'fire'){?>
									<li class="<?php echo ( $active_class == 'dashboard' || $this->uri->segment(2) == 'home' ) ? 'active' : ''; ?>" >
										<a href="<?php echo base_url('/webapp/dashboard/fire'); ?>">
											<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/fire.svg'); ?>"/>
												<span>Fire<span class="sr-only">(current)</span>
											</span>
										</a>
									</li>
								<?php };?>
								<?php if($disciplinename !== 'electricity'){?>
									<li>
										<a href="<?php echo base_url('/webapp/dashboard/electricity'); ?>">
											<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/electricity.svg'); ?>"/>
												<span>Electricity<span class="sr-only">(current)</span>
											</span>
										</a>
									</li>
								<?php };?>
								<?php if($disciplinename !== 'security'){?>
									<li>
										<a href="<?php echo base_url('/webapp/dashboard/security'); ?>">
											<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/security.svg'); ?>"/>
												<span>Security<span class="sr-only">(current)</span>
											</span>
										</a>
									</li>
								<?php };?>
								<?php if($disciplinename !== 'water'){?>
									<li>
										<a href="<?php echo base_url('/webapp/dashboard/water'); ?>">
											<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/water.svg'); ?>"/>
												<span>Water<span class="sr-only">(current)</span>
											</span>
										</a>
									</li>
								<?php };?>
								<?php if($disciplinename !== 'gas'){?>
									<li>
										<a href="<?php echo base_url('/webapp/dashboard/gas'); ?>">
											<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/gas.svg'); ?>"/>
												<span>Gas<span class="sr-only">(current)</span>
											</span>
										</a>
									</li>
								<?php };?>
								<?php if($disciplinename !== 'specialist'){?>
									<li>
										<a href="<?php echo base_url('/webapp/dashboard/specialist'); ?>">
											<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/specialist.svg'); ?>"/>
												<span>specialist<span class="sr-only">(current)</span>
											</span>
										</a>
									</li>
								<?php };?>
							</ul>
						</div>
						<!-- Scrollable links END -->
						<!-- Secondary mobile menu END -->
						
						<!-- Secondary desktop menu START -->
						<div class="disciplines large">
							<ul>
								<li <?php if($disciplinename == 'overview'){?>class="current"<?php };?>>
									<a href="<?php echo base_url('/webapp/dashboard/index'); ?>">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/overview.svg'); ?>"/>
										<span>Overview
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
								<li <?php if($disciplinename == 'fire'){?>class="current"<?php };?>>
									<a href="<?php echo base_url('/webapp/dashboard/fire'); ?>">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/fire.svg'); ?>"/>
										<span>Fire
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
								<li <?php if($disciplinename == 'electricity'){?>class="current"<?php };?>>
									<a href="<?php echo base_url('/webapp/dashboard/electricity'); ?>">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/electricity.svg'); ?>"/>
										<span>Electricity
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
								<li <?php if($disciplinename == 'security'){?>class="current"<?php };?>>
									<a href="<?php echo base_url('/webapp/dashboard/security'); ?>">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/security.svg'); ?>"/>
										<span>Security
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
								<li <?php if($disciplinename == 'water'){?>class="current"<?php };?>>
									<a href="<?php echo base_url('/webapp/dashboard/water'); ?>">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/water.svg'); ?>"/>
										<span>Water
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
								<li <?php if($disciplinename == 'gas'){?>class="current"<?php };?>>
									<a href="<?php echo base_url('/webapp/dashboard/gas'); ?>">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/gas.svg'); ?>"/>
										<span>Gas
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
								<li <?php if($disciplinename == 'specialist'){?>class="current"<?php };?>>
									<a href="<?php echo base_url('/webapp/dashboard/specialist'); ?>">
										<img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/specialist.svg'); ?>"/>
										<span>specialist
											<span class="sr-only">(current)</span>
										</span>
									</a>
								</li>
							</ul>
						</div>
						<!-- Secondary desktop menu END -->

						<?php 
							} //End if statement
						?>

					</nav>
					<!-- Nav END -->

					<!-- Overview date filter and refresh START -->
					<?php 
						if($disciplinename == 'overview') { //Is this the overview page?
					?>
						<div class="container" style=" position: relative; z-index:9999999">
							<div class="row filters" style=" position: absolute; top: 60px; right: 0; width: 65%;" >
								<!-- Date filter START -->
								<div class="col-xs-12 col-sm-9 col-md-9 filter">
									<!-- 7 day filter START -->
									<a id="7" href="#" class="btn <?php if($_COOKIE['daterange'] == 7 || $sessiondate == 7){ echo 'active' ;}?>  datefilter" onClick="deskrecp('7')">7 days</a>
								   <!-- 7 day filter END -->
								   <!-- 30 day filter START -->
								   <a id="30" href="#" class="btn <?php if($_COOKIE['daterange'] == 30){ echo 'active' ;}?> datefilter" onClick="deskrecp('30')">30 days</a>
								   <!-- 30 day filter END -->
								   <!-- 90 day filter START -->
								   <a id="90" href="#" class="btn <?php if($_COOKIE['daterange'] == 90){ echo 'active' ;}?> datefilter" onClick="deskrecp('90')">90 days</a>
								   <!-- 90 day filter END -->
								   <!-- 180 day filter START -->
								   <a id="180" href="#" class="btn <?php if($_COOKIE['daterange'] == 180){ echo 'active' ;}?> datefilter" onClick="deskrecp('180')">180 days</a>
								   <!-- 180 day filter END -->
								   <!-- 365 day filter START -->
								   <a id="365" href="#" class="btn <?php if($_COOKIE['daterange'] == 365){ echo 'active' ;}?> datefilter" onClick="deskrecp('365')">Year</a>
								   <!-- 365 day filter END -->
									<!-- Custom date filter -->
									<!-- <a id="custom" href="#" class="btn">Custom</a> -->
									<!-- Custom date filter END -->

									<!-- Calendar START -->
									<section class="calendar" style="display: none;">
										<div class="calendar__inputs">
											<input class="calendar__input" readonly="readonly" type="text" id="calendar-start" placeholder="Start Date">
											<input class="calendar__input" readonly="readonly" type="text" id="calendar-end" placeholder="End Date">
										</div>
										<div class="calendar__pikaday" id="calendar-container"></div>
										<button class="calendar__reset" id="calendar-clear">Submit</button>
									</section>
									<!-- Calendar END -->
								</div>
								<!-- Date filter END -->

								<!-- Referesh START -->
								<div class="col-md-3 refresh hidden-xs">
									<p>
										<a href="#" onClick="window.location.reload();"class="btn">REFRESH</a>
										<br>
										<small>LAST UPDATED: <span id="header-last-modified" class="header-last-modified" ><?php echo date( 'h:i:s A' ); ?></span></small>
									</p>
								</div>
								<!-- Refresh END -->
							</div>
						</div>
						
					<?php 
						}; //End of if statement
					?>
					<!-- Overview date filter and refresh END -->
   				</div>
			</header>
			<!-- Header END -->
			