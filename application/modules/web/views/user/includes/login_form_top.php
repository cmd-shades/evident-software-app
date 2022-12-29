<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="author" content="Wolf Alert Ltd">
		<title><?php echo(!empty($page_title) ? $page_title : APP_NAME); ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" media="screen"/>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/font-awesome.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.settings.css') ?>" media="screen">
		<link rel="shortcut icon" href="<?php echo base_url(); ?>/favicon.png">
		<style>
		
			body{
				background: url(<?php echo base_url('assets/images/backgrounds/login-background.png'); ?>);
				-webkit-background-size: cover;
				-moz-background-size: cover;
				-o-background-size: cover;
				background-size: cover;
				overflow: hidden !important;
			}
			
			a {
				text-decoration: none;
				color:#0092CD;
			}
			
			body::after {
				
				content: "";
				/*opacity: 0.9;*/
				position: absolute;
				top: 0;
				bottom: 0;
				right: 0;
				left: 0;
				z-index: -1; 
			}

			.login-form{
				display: block; 
				padding:20px; 
				width:100%; 
				margin:0 auto;
			}
			
			.login-container{
				border:none;
				padding:25px 10px;
				border-radius: 10px;
				-webkit-border-radius: 10px;
				-moz-border-radius: 10px;
				background: rgba(244, 244, 244, 0.9);
				background: #F4F4F4;
				color:#5C5C5C;
				width:80%;
				margin:25px auto;
			}
			
			.login-container .form-control {
				line-height: 1.6;
				height:40px;
				border-radius: 0 !important;
				border: none !important;
				background: #fff;
				
			}

			.login-container .input-group-addon {
				background-color: #0092CD;
				border: none !important;
				border-radius: 0 !important;
				border-right: none !important;
			}
			
			img.img-responsive{
				margin:0 auto;
			}
			
			.login-tag-line{
				text-align:center;
				font-style:italic;
			}
			
			.login-tag-line h5{
				font-size:80%;
			}
			
			.feedback-msg-container{
				text-align:center;
				font-style:italic;
				margin-top: -10px;
				margin-bottom: 25px;
			}
			
			.login-screen-logo{
				margin-top:15%;
				margin-bottom: 25px;
			}
			
			input{
				color:#F89C1C;
			}
			
			.download-links-container{
				margin:25px auto;
				width:85%;
			}
			
			.app-store-container{
				width:100%;
				margin:0 auto;
				text-align:center;				
			}
			
		</style>
		<script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>
	</head>
	
	<body class="nav-sm" style="overflow:hidden">
		<div class="container body">
			<div class="main_container">			
				<div class="row" style="margin-top:50px;">
					
					<div class="col-md-6 col-md-offset-3">
						<div class="login-screen-logo">
							<img width="85%" height="" class="img-responsive" src="<?php echo base_url('assets/images/logos/web-login-logo-small.png'); ?>" alt="Wolf Alert Logo" title="Welcome to Evident Software">
						</div>
						<div class="hide login-tag-line">
							<h5><?php echo APP_TAG_LINE; ?></h5>
						</div>
					</div>
				
					<div class="row">
						<div class="col-md-6 col-md-offset-3">
							<div class="login-container">