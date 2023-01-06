<!DOCTYPE html>
<html class="no-js">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title><?php echo ( !empty( $page_title ) ? $page_title : APP_NAME ); ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.fonts.css'); ?>" media="screen"/>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" media="screen"/>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/font-awesome.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.main.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.settings.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/nprogress.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/jquery.datetimepicker.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/sweetalert2.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/jquery-ui.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/ionicons.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/adminlte-wrapper.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/core-skin.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/select2.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.main.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.settings.css') ?>" media="screen">
		<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url(); ?>/favicon.png">
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

		<?php if( !empty( $module_style ) ){ ?>
			<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom/'.$module_style) ?>" media="screen">
		<?php } ?>

		<script src="<?php echo base_url('assets/js/jquery.min.js'); ?>" type="text/javascript"></script>
		<script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>

		<!-- my details -->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom/my_details.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/jsfilter.css') ?>" media="screen">
		<script src="<?php echo base_url('assets/js/jsfilter.js') ?>" charset="utf-8"></script>
	</head>
	<script>
		var csrfName	= 'xsrf_token',
			xsrfToken	= "<?php echo ( !empty( $xsrf_token ) ) ? $xsrf_token : false ?>";
	
			$.ajaxSetup({
				beforeSend: function( jqXHR, settings ) {
					settings.data += '&'+csrfName+'='+xsrfToken
				}
			});
	
		$( document ).ajaxComplete( function( event, request, settings ){

			var forms = $( "form" );
			forms.each( function( i ) {
				$( this ).prepend( '<input type="hidden" name="'+csrfName+'" value="'+xsrfToken+'">' );
			});
			
			
			
			$.ajaxSetup({
				beforeSend: function( jqXHR, settings ) {
					settings.data += '&'+csrfName+'='+xsrfToken
				}
			});
			
		});
	</script>

	<body class="hold-transition core-skin fixed layout-top-nav">
		<div class="wrapper">
			<header class="main-header">
				<nav class="navbar navbar-static-top">
					<div class="container-fluid">
						<div class="navbar-header">
							<a href="<?php echo base_url('/webapp/home/index'); ?>" class="navbar-brand"><img style="width:45px; margin-top:-14px;" class="hidden-medium" src="<?php echo base_url('assets/images/logos/main-logo-small-clear-bg.png'); ?>" /></a>
							<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse"><i class="fa fa-bars"></i></button>
						</div>
						<!-- Collect the nav links, forms, and other content for toggling -->
						<div class="collapse navbar-collapse pull-left" id="navbar-collapse">
							
							<style>
								.navbar-nav li a {
								}
							
							</style>
							<ul class="nav navbar-nav">
								<li class="<?php echo ( $active_class == 'dashboard' || $this->uri->segment(2) == 'home' ) ? 'active' : ''; ?>" ><a href="<?php echo base_url('/webapp/home/index'); ?>">Home <span class="sr-only">(current)</span></a></li>
								<?php if( !empty( $permitted_modules ) ){ ?>
									<li class="dropdown <?php echo ( ( $active_class != 'dashboard' ) && ( $this->uri->segment(2) != 'home'  ) ) ? 'active' : ''; ?>">
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
														
														case "premises":
															$module_home = "premises";
															break;

														default:
															$module_home = $module->module_controller.'s';
													}
												} else {
													$module_home = $module->module_controller.'s';
												} ?>
												<li><a href="<?php echo base_url('/webapp/'.$module->module_controller.'/'.$module_home ); ?>" <?php ?> ><?php echo $module->module_name; ?></a></li>
											<?php } ?>
											<?php if( !empty( $this->user->is_admin ) && in_array( $this->user->id, SUPER_ADMIN_ACCESS ) ){ ?>
												<li><a href="<?php echo base_url('/webapp/account/administration' ); ?>" <?php ?> ><strong>Account Admin</strong>&nbsp; <i class="fa fa-cogs" aria-hidden="true"></i></a></li>											
											<?php } ?>
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
									<?php /*echo '<li><a style="font-weight:normal;" href="' . base_url( "webapp/report" ) . '">Reports</a></li>';*/ ?>
									<li ><a style="font-weight:normal;" class='customize-quickbar pointer'><small><i style="font-size:15px" class="fas fa-cog"></i></small></a></li>
								<?php } ?>
							</ul>
						</div>
						<!-- Navbar Right Menu -->
						<div class="navbar-custom-menu">
							<ul class="nav navbar-nav">
								<!-- User Account Menu -->
								<li class="dropdown user user-menu">
									<a id="dropdown-toggle-mydetails" class='pointer'>
										<img src="<?php echo base_url( 'assets/images/avatars/user.png' ); ?>" class="user-image" alt="User Image">
										<span class="hidden-xs"><?php echo ( !empty( $this->user->first_name  ) ) ? ucwords( $this->user->first_name ) : ''; ?> <small><span class="hide label label-warning"><?php echo ( !empty( $new_messages_count ) ) ? $new_messages_count : '' ?></span></small></span>
									</a>
									</ul>
								</li>
							</ul>
						</div>
					</div>
				</nav>
			</header>

			<!-- Full Width Column -->
			<div class="content-wrapper" >
				<div class="">
					<div class="container-fluid body">
					
                        <section class="content-header" id='evi-breadcrumbs'>
                            
							<span style="font-size:15px;"><?php $this->load->view('_partials/breadcrumb', false); ?></span>

							<?php if( ( $this->uri->segment(2) == "audit" ) && ( in_array( $this->uri->segment(3), ['evidoc_names'] ) ) ){ ?>
								<span class="pull-right" style="position: absolute; right: 20px; top: 20px;">
									<!-- <a href="<?php echo base_url( "webapp/audit/audits" ); ?>">EviDocs List</a>&nbsp;|&nbsp; -->
									<a href="<?php echo base_url( "webapp/audit/exceptions" ); ?>">View Exceptions</a>&nbsp;
								</span>
							<?php } else if( ( $this->uri->segment(2) == "audit" ) && ( in_array( $this->uri->segment(3), ['exceptions','new_type'] ) ) ) { ?>
								<span class="pull-right" style="position: absolute; right: 20px; top: 20px;">
									<a href="<?php echo base_url( "webapp/audit/audits" ); ?>">EviDocs List</a>&nbsp;|&nbsp;
									<!-- <a href="<?php echo base_url( "webapp/audit/evidoc_names" ); ?>">EviDoc Manager</a>&nbsp; -->
								</span>
							<?php } else if( ( $this->uri->segment(2) == "audit" ) && ( in_array( $this->uri->segment(3), ['audits'] ) ) ) { ?>
								<span class="pull-right" style="position: absolute; right: 20px; top: 20px;">
									<!-- <a href="<?php echo base_url( "webapp/audit/evidoc_names" ); ?>">EviDoc Manager</a>&nbsp;|&nbsp; -->
									<a href="<?php echo base_url( "webapp/audit/exceptions" ); ?>">View Exceptions</a>&nbsp;
								</span>
							<?php } ?>
							

							<?php if( $this->uri->segment(2) == "job" ){ ?>
								<?php if( !in_array( $this->user->user_type_id, EXTERNAL_USER_TYPES ) ){ ?>
									<span class="pull-right" style="position: absolute; right: 20px; top: 20px;">
										<a href="<?php echo base_url( "webapp/job/overview" ); ?>">Overview |</a>
										<a href="<?php echo base_url( "webapp/job/bulk_assign" ); ?>">Assign Jobs |</a>									
										<a href="<?php echo base_url( "webapp/job/reassign" ); ?>">Re-Assign Jobs |</a>
										<a href="<?php echo base_url( "webapp/job/bulk_rebook" ); ?>">Re-Book Jobs |</a>
										<?php if( $this->uri->segment(3) != "jobs" ){ ?>
											<a style="display:<?php echo ( in_array( $this->user->account_id, TESSERACT_LINKED_ACCOUNTS ) ) ? 'inline-block' : 'none'; ?>" href="<?php echo base_url( "webapp/job/checklists" ); ?>">Evidoc Checklists |</a>
											<a href="<?php echo base_url( "webapp/job/jobs" ); ?>">Jobs List</a>
											<?php echo ( in_array( $this->uri->segment(3), ['job_types','new_type'] ) ) ? '' : ''; ?>
										<?php } ?>
										<?php if( $this->uri->segment(3) != "job_types" ){ ?>
											<!-- <a href="<?php echo base_url( "webapp/job/job_types" ); ?>"><i class="fa fa-tools"></i>Job Types</a> -->
											<?php echo ( $this->uri->segment(3) != "jobs" ) ? '' : ''; ?>
										<?php } ?>
									</span>
								<?php } ?>
							<?php } ?>

							<?php if( $this->uri->segment(2) == "diary" ){ ?>
								<span class="pull-right" style="position: absolute; right: 20px; top: 20px;">
									<!-- <a href="<?php echo base_url( "webapp/diary/manage_regions" ); ?>">Regions </a> -->

									<!-- <a href="<?php echo base_url( "webapp/diary/manage_skills" ); ?>">| Skill Sets</a> -->

									<?php if( $this->uri->segment(3) != "progress" ){ ?>
										<a href="<?php echo base_url( "webapp/diary/scheduler" ); ?>" target="_blank">Scheduler</a>
									<?php } ?>
								</span>
							<?php } ?>

							<?php if( ( $this->uri->segment(2) == "asset" ) && ( in_array( $this->uri->segment(3), ["assets","new_attribute"] ) ) ){ ?>
								<span class="pull-right hide" style="position: absolute; right: 20px; top: 20px;">
									<a href="<?php echo base_url( "webapp/asset/asset_types" ); ?>">Asset Types</a>&nbsp;|&nbsp;
									<a href="<?php echo base_url( "webapp/asset/attributes" ); ?>">Attributes</a>&nbsp;
								</span>
							<?php } else if( ( $this->uri->segment(2) == "asset" ) && ( in_array( $this->uri->segment(3), ["asset_types"] ) ) ) { ?>
								<span class="pull-right hide" style="position: absolute; right: 20px; top: 20px;">
									<a href="<?php echo base_url( "webapp/asset/assets" ); ?>">Assets</a>&nbsp;|&nbsp;
									<a href="<?php echo base_url( "webapp/asset/attributes" ); ?>">Attributes</a>&nbsp;
								</span>
							<?php } else if( ( $this->uri->segment(2) == "asset" ) && ( in_array( $this->uri->segment(3), ["attributes","create_asset_type","create"] ) ) ) { ?>
								<span class="pull-right hide" style="position: absolute; right: 20px; top: 20px;">
									<a href="<?php echo base_url( "webapp/asset/assets" ); ?>">Assets</a>&nbsp;|&nbsp;
									<!-- <a href="<?php echo base_url( "webapp/asset/asset_types" ); ?>">Asset Types</a>&nbsp; -->
								</span>
							<?php } ?>
							
							
							<?php if( $this->uri->segment(2) == "home" ){ ?>
								<span class="pull-right" id="current_time" style="position: absolute; right: 20px; top: 20px;">
									<?php echo date( 'd/m/Y' ) ;?> <span class="time_now" ></span><?php echo date( 'A' ) ;?> 
								</span>
							<?php } ?>
							
							<?php if( $this->uri->segment(2) == "account" ){ ?>
								<span class="pull-right" style="position: absolute; right: 20px; top: 20px;">
									<a href="<?php echo base_url( "webapp/account/administration" ); ?>">Account Manager </a>

									<a href="<?php echo base_url( "webapp/account/disciplines" ); ?>">| Disciplines</a>
									
									<a href="<?php echo base_url( "webapp/account/evidoc_templates" ); ?>">| Evidoc Templates</a>
									
									<a class="hide" href="<?php echo base_url( "webapp/account/job_type_templates" ); ?>">| Job Type Templates</a>
								</span>
							<?php } ?>
							
                        </section>
						
						<div class="content-wrapper" >
						<script>

								mydetails_active = false

								$( "#dropdown-toggle-mydetails" ).click(function() {
									if(mydetails_active == false){
										mydetails_active = true
										$.ajax({
											url:"<?php echo base_url('webapp/user/my_details');?>",
											method:"POST",
											dataType: "text",
											success:function( result ){
												$( "body" ).append( result );
											}
										});
									}
								});
								
								$(".customize-quickbar").on('click', function(event) {
									$.ajax({
										url:"<?php echo base_url('webapp/user/reorder_taskbar'); ?>",
										method:"POST",
										success:function( result ){
											$("#quickbar-details").remove()
											$("body").append(result)
										}
									});
								})

						</script>
						<div class="main-content" style="margin-top:5px;">
