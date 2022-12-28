<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Workforce Manager module">
    <meta name="author" content="SimplySID">

    <title>Workforce Manager Module - App from SimplySID collection</title>

    <!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="<?php echo base_url( "assets/css/bootstrap.min.css" ); ?>" />

    <!-- Custom styles for this template -->
	<link rel="stylesheet" href="<?php echo base_url( "assets/css/modern-business.css" ); ?>" />
  </head>

  <body>
    <!-- Navigation -->
    <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark fixed-top">
      <div class="container">
        <a class="navbar-brand" href="index.php"><img src="<?php echo base_url( "assets/" ); ?>images/logo-grey-white-1.png" /></a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
              <a class="nav-link" href="index.php" style="color: #d66204">Contact</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <div class="container">

      <!-- Page Heading/Breadcrumbs -->
      <h1 class="mt-4 mb-3" style="color:#d66204;">Workforce Manager
        <small>Who needs to be to do your job</small>
      </h1>

      <!-- Content Row -->
      <div class="row">
        <div class="col-lg-4 mb-4">
		  <img style="width: 100%; height: auto; margin: 0; padding: 0;" class="" src="<?php echo base_url( "assets/" ); ?>images/WolfAlert-Logo-1.jpg" />
        </div>
		
		<div class="col-lg-8 mb-4">
		<br />
		<br />
          <h3>Interested? Send us a Message</h3>
          <form name="sentMessage" id="contactForm" novalidate>
            <div class="control-group form-group">
              <div class="controls">
                <label>Full Name:</label>
                <input type="text" class="form-control" id="name" required data-validation-required-message="Please enter your name.">
                <p class="help-block"></p>
              </div>
            </div>
            <div class="control-group form-group">
              <div class="controls">
                <label>Phone Number:</label>
                <input type="tel" class="form-control" id="phone" required data-validation-required-message="Please enter your phone number.">
              </div>
            </div>
            <div class="control-group form-group">
              <div class="controls">
                <label>Email Address:</label>
                <input type="email" class="form-control" id="email" required data-validation-required-message="Please enter your email address.">
              </div>
            </div>
            <div class="control-group form-group">
              <div class="controls">
                <label>Message:</label>
                <textarea rows="10" cols="100" class="form-control" id="message" required data-validation-required-message="Please enter your message" maxlength="999" style="resize:none"></textarea>
              </div>
            </div>
            <div id="success"></div>
            <!-- For success/fail messages -->
            <button type="submit" class="btn btn-primary" id="sendMessageButton" style="background-color: #d66204;border-color: #d86b3c;">Send Message</button>
          </form>
        </div>
		
		
 
      </div>
      <!-- /.row -->

    </div>
    <!-- /.container -->

    <!-- Footer -->
    <footer class="py-5 bg-dark">
		<div class="container">
			<p class="m-0 text-center text-white">Copyright &copy; SimplySID 2018</p>
		</div>
      <!-- /.container -->
    </footer>

    <!-- Bootstrap core JavaScript -->
	<script type="text/javascript" src="<?php echo base_url( "assets/js/jquery-3.2.1.min.js" ); ?>"></script>
	<script type="text/javascript" src="<?php echo base_url( "assets/js/popper.min.js" ); ?>"></script>
	<script type="text/javascript" src="<?php echo base_url( "assets/js/bootstrap.min.js" ); ?>"></script>
	<script type="text/javascript" src="<?php echo base_url( "assets/js/bootstrap.bundle.min.js" ); ?>"></script>
	
    <!-- Contact form JavaScript -->
	<script type="text/javascript" src="<?php echo base_url( "assets/js/jqBootstrapValidation.js" ); ?>"></script>
	<script type="text/javascript" src="<?php echo base_url( "assets/js/contact_me.js" ); ?>"></script>
  </body>
</html>
