<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="author" content="Wolf Alert Ltd">
		<title><?php echo ( !empty($page_title) ? $page_title : APP_NAME ); ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" media="screen"/>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/font-awesome.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.settings.css') ?>" media="screen">
		<link rel="shortcut icon" href="<?php echo base_url(); ?>/public/favicon.png">
		<style>
			body{
				background:#fff url(<?php echo base_url( 'assets/images/backgrounds/login-background.jpg' ); ?>) no-repeat;
				-webkit-background-size: cover;
				-moz-background-size: cover;
				-o-background-size: cover;
				background-size: cover;
				background-position: top center;
				height: 100vh;
			}

			a{
				text-decoration: none;
				color:#F93711;
			}

			body::after {
				content: "";
				position: absolute;
				top: 0;
				bottom: 0;
				right: 0;
				left: 0;
				z-index: -1;
			}

			.login-container{
				border: none;
				padding: 0px 10px;
				width: 40%;
				margin: 25px auto;
				min-width: 400px;
				margin-top: 40vh;
			}

			.container-full{
				margin: 0 auto;
				width: 100%;
			}

			.logo-container{
				width: 400px;
				margin-top: 100px;
				background: rgba( 255,255,255,0.7 );
				padding: 10px 20px;
			}

 			.login-form{
				display: block;
				padding:20px;
				margin:0 auto;
			}

			.login-form .username-container input,
			.login-form .password-container input,
			.signin-container button,
			.feedback-msg-container{
				
				border-radius: 15px;
				min-height: 45px;
				color: #fff;
			}

			.login-form .username-container input::placeholder,
			.login-form .password-container input::placeholder{
				color: white;
			}

			.login-form .username-container input,
			.login-form .password-container input{
				background: rgba( 0,0,0,0.6 );
				margin: 22.5px 0px;
			}

			.signin-container button{
				width: 50%;
				background: rgba(82, 173, 109, 0.7);
			}

			.feedback-msg-container{
				background: rgba( 255, 255, 255, 0.7 );
				font-style:italic;
				color: #f20f1a;
				padding: 12px 12px;
				border: 1px solid #f20f1a;
			}

			.techlive-logo{
				width: 300px;
				height: auto;
			}

			.footer{
				position:fixed;
				bottom:0;
				right:0;
				left: 0;
				color:#fff;
				padding:4px;
				margin-bottom:0;
			}

			.footer-techlive-logo{
				position:fixed;
				bottom:10px;
				left: 10px;
			}
			
			.techlive-logo-small{
				height: 80px;
				width: auto;
			}
			
			.powered-by-Evident{
				position:fixed;
				bottom:10px;
				right: 10px;
				color: #0092cd;
			}
			
			.powered-by-Evident span{
				font-size: 10px;
			}
		</style>
	</head>

	<body class="nav-sm">
		<div class="container body">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="row">
						<div class="col-centered container-full">
							<div class="logo-container col-centered hide">
								<img src="<?php echo base_url( "assets/images/backgrounds/techlive_logo.png" ) ?>" class="img-responsive techlive-logo" title="TechLive Logo" alt="TechLive logo" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-centered container-full">
							<div class="login-container col-centered">
								<form class="login-form container-full" method="post" action="<?php echo base_url('webapp/user/login'); ?>" role="form">
									<?php
									$message = ( !empty( $this->session->flashdata( "message" ) ) ) ? $this->session->flashdata( "message" ) : false ;
									if( !empty( $message ) ){ ?>
										<div class="feedback-msg-container text-center">
											<em><?php echo $message; ?></em>
										</div>
									<?php } ?>

									<div class="username-container container-full">
										<input type="text" name="username" id="username" tabindex="1" style="border-style:none;" class="form-control text-center" aria-describedby="addon-username" placeholder="Username" value="<?php echo !empty( $username ) ? $username : ''; ?>">
									</div>
									<div class="password-container container-full">
										<input type="password" name="password" id="password" tabindex="1" style="border-style:none;" class="form-control text-center" aria-describedby="addon-password" placeholder="Password" value="">
									</div>
									<div class="signin-container">
										<button type="submit" class="btn btn-block col-centered">Sign In</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="footer">
			<div class="footer-techlive-logo">
				<img src="<?php echo base_url( "assets/images/logos/cacti-logo-cacutus.png" ) ?>" class="img-responsive techlive-logo-small" title="TechLive Logo" alt="TechLive logo" />
			</div>
			<div class="powered-by-Evident">
				<span>Powered by Evident</span>
			</div>

			<?php /* <small><?php echo APP_NAME; ?> <?php echo APP_VERSION; ?> <!-- <small>powered by Evident LTD</small>--></small> */ ?>

		</div>
	</body>
</html>
