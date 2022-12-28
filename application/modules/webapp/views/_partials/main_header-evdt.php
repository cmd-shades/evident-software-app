<!DOCTYPE html>
<html>
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
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.main.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.settings.css') ?>" media="screen">
		<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url(); ?>/favicon.png">

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		
		<script src="<?php echo base_url('assets/js/jquery.min.js'); ?>" type="text/javascript"></script>
		
		<style>
			.core-skin .wrapper .content-wrapper .body {background: #fff;}
			.content-header{
				background: #f4f4f4;
				color:#0092CD;
				margin-left:-15px;
				margin-right:-15px;
				padding: 15px 20px;
			}
			
			.content-header h1{
				font-size:16px;
			}
		</style>
		
		<?php if( !empty( $module_style ) ){ ?>
			<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom/'.$module_style) ?>" media="screen">
		<?php } ?>
		
	</head>

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
							<ul class="nav navbar-nav">
								<li class="<?php echo ( $active_class == 'dashboard' || $this->uri->segment(2) == 'home' ) ? 'active' : ''; ?>" ><a href="<?php echo base_url('/webapp/home/index'); ?>">Dashboard <span class="sr-only">(current)</span></a></li>
								<?php if( !empty( $permitted_modules ) ){ ?>
									<li class="dropdown <?php echo ( ( $active_class != 'dashboard' ) && ( $this->uri->segment(2) != 'home'  ) ) ? 'active' : ''; ?>">
										<a href="#" class="dropdown-toggle" data-toggle="dropdown">Modules <span class="caret"></span></a>
										<ul class="dropdown-menu" role="menu">
											<?php foreach( $permitted_modules  as $k => $module ){ ?>
												<?php $module_home	= ( !empty( $module->is_pluralised ) ) ? $module->module_controller :$module->module_controller.'s'; ?>
												<li><a href="<?php echo base_url('/webapp/'.$module->module_controller.'/'.$module_home ); ?>"><?php echo $module->module_name; ?></a></li>
											<?php } ?>
										</ul>
									</li>
								<?php } ?>
							</ul>
						</div>

						<!-- Navbar Right Menu -->
						<div class="navbar-custom-menu">
							<ul class="nav navbar-nav">
								<!-- User Account Menu -->
								<li class="dropdown user user-menu">
									<!-- Menu Toggle Button -->
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" >
										<!-- The user image in the navbar-->
										<img src="<?php echo base_url( 'assets/images/avatars/user.png' ); ?>" class="user-image" alt="User Image">
										<span class="hidden-xs"><?php echo ucwords( $this->user->first_name ); ?> <small><span class="hide label label-warning"><?php echo ( !empty( $new_messages_count ) ) ? $new_messages_count : '' ?></span></small></span>
									</a>
									
									<ul class="dropdown-menu">
										<!-- The user image in the menu -->
										<li class="user-header">
											<img src="<?php echo base_url(); ?>/assets/images/avatars/user.png" class="img-circle" alt="User Image">
											<p><?php echo ucwords( $this->user->first_name." ".$this->user->last_name ); ?><small>Technical Delivery</small></p>
										</li>
										<!-- Menu Body. PLaceholder to be introduce as a feature -->
										<li class="user-body hide">
											<div class="row">
												<div class="col-xs-4 text-center pull-left"><a href="#"><a href="#"><i class="fas fa-sitemap"></i> <br/>2 Assets</a></div>
												<div class="col-xs-4 text-center pull-right"><a href="#"><a href="#"><i class="fa fa-users text-aqua"></i> <br/>19 Msgs</a></div>
											</div>
										</li>
										
										<!-- Menu Footer-->
										<li class="user-footer">
											<div class="pull-left"><a href="#" class="btn btn-default btn-flat">My Details</a></div>
											<div class="pull-right"><a href="#" class="btn btn-default btn-flat">Sign out</a></div>
										</li>
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
						<!-- Content Header (Page header) -->
						<section class="content-header">
							<h1><?php echo ( !empty( $breadcrumb) ) ? $breadcrumb : ''; ?></h1>
							<h1 class="hide"><?php echo ucwords( $this->router->fetch_class() ); ?></h1>
							<ol class="breadcrumb hide"><?php echo ( !empty( $breadcrumb) ) ? $breadcrumb : ''; ?></ol>
							<ol class="breadcrumb hide">
								<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
								<li><a href="#">Class</a></li>
								<li class="active">Method</li>
							</ol>
						</section>