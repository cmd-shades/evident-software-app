<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

		<title><?php echo(!empty($page_title) ? $page_title : APP_NAME); ?></title>

		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.fonts.css'); ?>" media="screen"/>
		
		<!-- <link rel="stylesheet" href="<?php echo base_url(); ?>/assets2/css/bootstrap.min.css">--> <!-- Bootstrap 3.3.7 -->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap.css'); ?>" media="screen"/> -->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/font-awesome.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.main.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.settings.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/nprogress.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/jquery.datetimepicker.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/sweetalert2.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/jquery-ui.min.css') ?>" media="screen">
		<!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/assets2/css/ionicons.min.css" media="screen" > -->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/assets2/css/AdminLTE.css">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/assets2/css/_all-skins.min.css">
		<!-- Google Font -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
		<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url(); ?>/favicon.png">
		
		<!-- Tell the browser to be responsive to screen width -->
		<!-- Font Awesome 
		<link rel="stylesheet" href="<?php echo base_url(); ?>/assets2/css/font-awesome.min.css">-->
		<!-- Ionicons -->
		<!-- Theme style -->
		<!-- AdminLTE Skins. Choose a skin from the css/skins
		folder instead of downloading all of them to reduce the load. -->

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

		<!-- jQuery 3 -->
		<script src="<?php echo base_url(); ?>/assets2/js/jquery.min.js"></script>
		<!-- AdminLTE for demo purposes -->
		<script src="<?php echo base_url(); ?>/assets2/js/Chart.min.js"></script>
		
	</head>
	
	<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
	<body class="hold-transition skin-blue layout-top-nav">
		<div class="wrapper">
			<header class="main-header">
				<nav class="navbar navbar-static-top">
					<div class="container-fluid">
						<div class="navbar-header">
							<a href="<?php echo base_url('/home/index'); ?>" >
								<img class="hidden-medium" src="<?php echo base_url('assets/images/logos/main-logo-small-clear-bg.png'); ?>" />
							</a>
							<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse"><i class="fa fa-bars"></i></button>
						</div>

						<!-- Navbar Right Menu -->
						<div class="navbar-custom-menu">
							<ul class="nav navbar-nav">

								<!-- Notifications Menu -->
								<li class="dropdown notifications-menu hide">
									<!-- Menu toggle button -->
									<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bell-o"></i><span class="label label-warning">10</span></a>
									<ul class="dropdown-menu">
										<li class="header">You have 10 notifications</li>
										<li>
											<!-- Inner Menu: contains the notifications -->
											<ul class="menu">
												<li><!-- start notification -->
													<a href="#"><i class="fa fa-users text-aqua"></i> 5 new members joined today</a>
												</li>
											</ul>
										</li>
										<li class="footer"><a href="#">View all</a></li>
									</ul>
								</li>
								
								<!-- User Account Menu -->
								<li class="dropdown user user-menu">
									<!-- Menu Toggle Button -->
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" >
										<!-- The user image in the navbar-->
										<img src="<?php echo base_url('assets/images/avatars/user.png'); ?>" class="user-image" alt="User Image">
										<span class="hidden-xs"><?php echo ucwords($this->user->first_name); ?></span>
									</a>
									
									<ul class="dropdown-menu">
										<!-- The user image in the menu -->
										<li class="user-header">
											<img src="<?php echo base_url(); ?>/assets2/images/avatars/user.png" class="img-circle" alt="User Image">
											<p><?php echo ucwords($this->user->first_name." ".$this->user->last_name); ?><small>Technical Delivery</small></p>
										</li>
										<!-- Menu Body -->
										<li class="user-body">
											<div class="row">
												<div class="col-xs-4 text-center"><a href="#">1 Job</a></div>
												<div class="col-xs-4 text-center"><a href="#">4 Holidays</a></div>
												<div class="col-xs-4 text-center"><a href="#">19 Msgs</a></div>
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
			<div class="content-wrapper">
				<div class="container-fluid">
				<br/>
				<br/>