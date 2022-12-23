<!DOCTYPE html>
<html lang="en" class="no-js">

	<!-- <script>
		// Sets 'js' on html element and removes 'no-js' if present (here to prevent flashingor flickering )
		(function(){
			document.documentElement.className = document.documentElement.className.replace(/(^|\s)no-js(\s|$)/, '$1$2') + (' js '); 
		})();
	</script> -->

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="author" content="Evident Software">
		<title><?php echo ( !empty( $page_title ) ? $page_title : APP_NAME ); ?></title>
		
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:bi|Open+Sans:bi">
		
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.fonts.css'); ?>" media="screen"/>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" media="screen"/>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/font-awesome.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.main.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.settings.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/nprogress.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/jquery.datetimepicker.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/sweetalert2.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/jquery-ui.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/select2.min.css') ?>" media="screen">
		<link rel="shortcut icon" type="image/png" href="<?php echo base_url(); ?>favicon.png">

		<!-- Load JQuery here -->
		<script src="<?php echo base_url('assets/js/jquery.min.js'); ?>" type="text/javascript"></script>
	</head>

	<style>
	
		@import url('https://fonts.googleapis.com/css?family=Open+Sans');
		
		/*#container {display:none;}
		.no-js #container { display: block;}*/
	</style>
	
	<style type="text/css">
		 font-family: 'Open Sans',serif;
		 
		.table > thead > tr > th {
			cursor:pointer;
		}
	</style>
	
	<?php if( !empty( $module_style ) ){ ?>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom/'.$module_style) ?>" media="screen">
	<?php } ?>
	
	<body class="nav-sm">
		<div id="container" class="container body">
			<div class="main_container">
				<div class="left_col">
					<div class="left_col scroll-view">
						<div class="navbar nav_title">
							<a href="<?php echo base_url( '/webapp/home/index' ); ?>" class="site_title">
								<img class="hidden-medium sidebar-logo" src="<?php echo base_url( 'assets/images/logos/techlive_logo_small.png' ); ?>" />
							</a>							
						</div>
						
						<div class="clearfix"></div>
						
						<!-- sidebar menu -->
						<?php if( !$hide_sidebar ){ $this->load->view('webapp/_partials/sidebar'); } ?>						
					</div>
				</div>

				<!-- top navigation -->
				<div class="top_nav">
					<div class="nav_menu">
						<nav>
							<div class="nav toggle">
								<a id="menu_toggle"><i class="fa fa-bars"></i></a>								
							</div>
							<ul class="hide nav navbar-nav" style="margin-top: 20px;" >
								<span role="presentation"></span>
							</ul>
							
							<ul class="nav navbar-nav navbar-left">
								<li>
									<div style="margin: 20px 0 0 10px;" >
										<span role="presentation"><?php echo ( !empty( $breadcrumb) ) ? $breadcrumb : ''; ?></span>
									</div>
								</li>
							</ul>
							<?php if( !empty( $this->user->first_name ) ){ ?>
								<ul class="nav navbar-nav navbar-right">
									<li role="presentation">
										<a href="<?php echo base_url('webapp/user/logout');?>" aria-expanded="false" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
									</li>
								</ul>
								
								<ul class="nav navbar-nav navbar-right">
									<li role="presentation">
										<a href="" aria-expanded="false" title="You logged in as <?php echo $this->user->first_name." ".$this->user->last_name; ?> <?php echo timeago( ( date ( 'H:i:s', $this->user->login_time ) ) ); ?>" ><small class="user-motd"><em>Hi <?php echo $this->user->first_name; ?></em></small></a>
									</li>
								</ul>
							<?php } ?>
						</nav>
					</div>
				</div>				

				<!-- main page content -->
				<div class="right_col" role="main">