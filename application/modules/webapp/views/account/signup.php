<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="author" content="Simply SID Ltd">
		<title><?php echo ( !empty( $page_title ) ? $page_title : APP_NAME ); ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.fonts.css'); ?>" media="screen"/>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" media="screen"/>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/font-awesome.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.main.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.settings.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/jquery.datetimepicker.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/sweetalert2.min.css') ?>" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/jquery-ui.min.css') ?>" media="screen">
		<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url(); ?>/favicon.png">

		<!-- Load JQuery here -->
		<script src="<?php echo base_url('assets/js/jquery.min.js'); ?>" type="text/javascript"></script>
	</head>

	<style>
		/*#container {display:none;}
		.no-js #container { display: block;}*/
	</style>

	<style type="text/css">

		html, body {
			height: auto;
			background: none;
		}
		
		body{
			background: url(<?php echo base_url('assets/images/backgrounds/evident-background.png'); ?>) repeat-x;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;
			overflow: hidden !important;
		}

		.account_signup_container{
			height: auto;
			/* min-height: 465px; */
			display: block;
			background: #fff;
			width: 74.33%;
			margin-left: 0%;
			margin-right: 8%;
			border-radius: 15px;
			background:#f2efef;
		} 
		
		.has_light_shadow{
			webkit-box-shadow: 0px 0px 20px 5px rgba(92, 92, 92, 0.3);
			-moz-box-shadow: 0px 0px 20px 5px rgba(92, 92, 92, 0.3);
			box-shadow: 0px 0px 20px 5px rgba(92, 92, 92, 0.3);
		}

		.text-center{
			text-align:center !important;
		}


		.error_message{
			display: block;
			color:#ff0000;
			font-weight:300;
			margin-top:-0px;
		}
		
		.form-header{			
			color: #0092CD;
			border-radius: 15px 15px 0 0;
			background: #ccc;
			font-weight:600;
			margin-top: -10px;
			margin-bottom: 5px;
		}
		
		.main-container{
			margin-top: 100px;
		}
		
		.form-field-container{
			/* min-height: 260px; */
		}
	</style>

	<body>
	
		<div class="main-container">
			<div class="row">
				<div class="col-md-7 col-sm-7 col-xs-12 text-center">
					<div class="login-screen-logo text-center">
						<img width="76%" src="<?php echo base_url('assets/images/logos/web-login-logo-small.png'); ?>" alt="Evident Logo" title="Welcome to Evident Software">
					</div>
				</div>
				
				<div class="col-md-5 col-sm-5 col-xs-12">
					<div class="x_panel tile has-shadow account_signup_container">
						<div class="row">
							<div class="row form-header text-center"><h3 class="welcome_header text-dark-blue text-bold">Signup to Evident Software Limited</h3></div>
							<form id="account-creation-form" method="post" >
								<div class="form-body">
									
									<!-- ACCOUNT DETAILS -->
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="account_creation_panel1 col-md-12 col-sm-12 col-xs-12 account-signup-form" style="display:block">
											<div class="form-field-container">
												<div class="form-group">						
													<h5 class="error_message pull-left" id="account_creation_panel1-errors"></h5>
												</div>
												<div class="form-group">
													<input name="account_name" class="form-control required" type="text" placeholder="Company name" title="Please provide your Business / Company name" />
												</div>
												<div class="form-group">
													<input name="organisation_area" class="form-control" type="text" placeholder="Business Sector" title="Please provide your Business Sector" />
												</div>
												
												<div class="row">
													<div class="col-md-6 col-sm-6 col-xs-12">
														<div class="form-group">
															<input name="account_first_name" class="form-control required" type="text" placeholder="First name" required title="What is the main contact's First Name" />
														</div>
													</div>
													<div class="col-md-6 col-sm-6 col-xs-12">
														<div class="form-group">
															<input name="account_last_name" class="form-control required" type="text" placeholder="Last name" required title="What is the main contact's Last Name" />
														</div>
													</div>
												</div>
												
												<div class="form-group">
													<input name="account_email" class="form-control required" type="text" placeholder="Contact email" required title="What is the main contact's email" />
												</div>
												
												<div class="row">
													<div class="col-md-6 col-sm-6 col-xs-12">
														<div class="form-group">
															<input name="account_mobile" class="form-control required" type="text" placeholder="Contact mobile" required title="What is the main contact's mobile number" />
														</div>
													</div>
													<div class="col-md-6 col-sm-6 col-xs-12">
														<div class="form-group">
															<input name="account_telephone" class="form-control required" type="text" placeholder="Contact telephone" required title="What is the main contact's telephone number" />
														</div>
													</div>
												</div>
												
												<div class="hide form-group">
													<input name="admin_username" class="form-control required" type="text" placeholder="Account admin username" required title="Please choose your account username" />
												</div>
												
												<div class="form-group">
													<input name="account_holder_job_title" class="form-control" type="text" placeholder="Job Title" title="Please provide your Job Title" />
												</div>
												<div class="form-group">
													<div class="row">
														<div class="col-md-12 col-sm-12 col-xs-12">
															<button class="btn btn-block btn-flow btn-success btn-next account-creation-steps" data-currentpanel="account_creation_panel1" type="button">Next</button>
														</div>
													</div>
												</div>
												
											</div>
										</div>
										
										<div class="account_creation_panel2 col-md-12 col-sm-12 col-xs-12 account-signup-form" style="display:none">
											<div class="form-field-container">
												<div class="form-group">
													<h5 class="error_message pull-left" id="account_creation_panel2-errors"></h5>
												</div>
												
												<!-- MODULE & PACKAGES INFORMATIO -->
												<div class="col-md-12 col-sm-12 col-xs-12">
													<!--  Default module selection -->
													<?php if( !empty( $modules ) ){ ?>
															<div class="row">
																<div class="col-md-12 col-sm-12 col-xs-12"><h5>Please select the modules you would like. <br><small>(Users, EviDocs and Reports come as standard)</small></h5></div>
																<?php foreach( $modules as $key => $module ){ 
																		if( !in_array( strtolower( $module->module_controller ), [ "user", "audit", "settings", "warehouse", "eregister", "alert", "invoicing", "workforcemanager", "requisition", "report" ] )  ){	?>
																		<div class="col-md-6 col-sm-6 col-xs-12">
																			<div class="checkbox" >
																				<label><input name="account_modules[]" class="radio-opt-selector module_selector" type="checkbox" checked=checked value="<?php echo $module->module_id; ?>" data-module_price="<?php echo ( !empty( $module->module_price ) && ( ( float ) $module->module_price > 0 ) ) ? $module->module_price : MODULE_PRICE ; ?>" data-module_price_management="<?php echo ( ( !empty( $module->module_price_management ) && ( ( float ) $module->module_price_management > 0 ) ) ) ? $module->module_price_management : MODULE_PRICE_MANAGEMENT ; ?>" data-module_price_intelligence="<?php echo ( !empty( $module->module_price_intelligence ) && ( ( float )$module->module_price_intelligence > 0 ) ) ? $module->module_price_intelligence : MODULE_PRICE_INTELLIGENCE ; ?>" /> <?php echo $module->module_name; ?></label>
																			</div>
																		</div>		
																	<?php }else{ ?>
																		<!-- Add Evidocs Module -->
																		<input name="account_modules[]"  type="hidden" value="<?php echo $module->module_id; ?>" />
																	<?php } ?>
																<?php } ?>
															</div>
													<?php } ?>

													<!--  Package Selection selection -->
													<?php if( !empty( $packages ) ){ ?>
														<div class="hide row">
															<div class="col-md-12 col-sm-12 col-xs-12" ><h5>Please select the Package Tier you would like</h5></div>
															<?php foreach( $packages as $k => $package ){ ?>
																<div class="col-md-6 col-sm-6 col-xs-12" >
																	<div class="checkbox" >
																		<label><input name="package_id" class="package-option" type="checkbox" <?php echo ( $package->package_id == 2 ) ? 'checked=checked' : ''; ?> value="<?php echo $package->package_id; ?>" /><?php echo $package->package_code; ?></label>
																	</div>
																</div>
															<?php } ?>
															<div class="col-md-12 col-sm-12 col-xs-12"><h5>Monthly Price (After your Free Trial): <strong><span class="package_price">£80.00</span></strong></h5></div>
														</div>
													<?php } else { ?>
														<div class="hide row">
															<div class="col-md-4 col-sm-6 col-xs-12" >
																<h5>No packages available at the moment</h5>
															</div>
														</div>
													<?php } ?>
												</div>
												
												<div class="form-group">
													<div class="row">
														<div class="col-md-6 col-sm-6 col-xs-12">
															<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="account_creation_panel2" type="button">Back</button>
														</div>
														<div class="col-md-6 col-sm-6 col-xs-12">
															<button class="btn btn-block btn-flow btn-success btn-next account-creation-steps" data-currentpanel="account_creation_panel2" type="button">Next</button>
														</div>
													</div>
												</div>
											</div>
										</div>
										
										<div class="account_creation_panel3 col-md-12 col-sm-12 col-xs-12 account-signup-form" style="display:none">
											<div class="form-field-container">
												<div class="form-group">
													<div class="row">
														<div class="col-md-12 col-sm-12 col-xs-12">
															<h6>We would like to contact you in future regards offers and other promotional events.</h6>
															<h6>Please untick the methods if you would not like us to contact you via these method.</h6>
														</div>
														<div class="col-md-6 col-sm-6 col-xs-12">
															<div class="checkbox">
																<input type="hidden" name="consent_email" value="0" />
																<label><input name="consent_email" class="radio-opt-selector" type="checkbox" checked="checked" value="1">Email</label>
															</div>
														</div>
														<div class="col-md-6 col-sm-6 col-xs-12">
															<div class="checkbox">
																<input type="hidden" name="consent_phone" value="0" />
																<label><input name="consent_phone" class="radio-opt-selector" type="checkbox" checked="checked" value="1">Phone</label>
															</div>
														</div>
														<div class="col-md-6 col-sm-6 col-xs-12">
															<div class="checkbox">
																<input type="hidden" name="consent_post" value="0" />
																<label><input name="consent_post" class="radio-opt-selector" type="checkbox" checked="checked" value="1">Post</label>
															</div>
														</div>
														<div class="col-md-6 col-sm-6 col-xs-12">
															<div class="checkbox">
																<input type="hidden" name="consent_sms" value="0" />
																<label><input name="consent_sms" class="radio-opt-selector" type="checkbox" checked="checked" value="1">SMS</label>
															</div>
														</div>
													</div>
												</div>
												<div class="form-group">
													<div class="row">
														<div class="col-md-6 col-sm-6 col-xs-12">
															<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="account_creation_panel3" type="button" >Back</button>
														</div>
														<div class="col-md-6 col-sm-6 col-xs-12">
															<button id="account-creation-btn" class="btn btn-block btn-success btn-next" type="button">Sign-up</button>
														</div>
													</div>
												</div>
											</div>
										</div>
										
									</div>
									
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- JS files -->
		<script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/custom.min.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/fastclick.min.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/sweetalert2.min.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/jquery.datetimepicker.full.min.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/jquery-ui.min.js'); ?>" type="text/javascript"></script>

		<script>
			$( document ).ready( function(){
				
				$( '[name="account_email"]' ).keyup(function() {
					$( '[name="admin_username"]' ).val( $(this).val() );
				});
				
				//Clear red-bordered elements
				$( '.required' ).on( 'change', function(){
					$( this ).css( "border","1px solid #ccc" );
					$( '.error_message' ).each(function(){
						$( this ).text( '' );
					});	
				});
				
				function packagePrice(){
					var package_price = 0;
					var tier_id = $( ".package-option:checked" ).val();

					if( tier_id == 1 ){
						var data_section = "module_price";
					} else if( tier_id == 2 ){
						var data_section = "module_price_management";
					} else if( tier_id == 3 ){
						var data_section = "module_price_intelligence";
					} else {
						var data_section = "module_price";
					}
					
					$( ".module_selector" ).each( function(){
						var value = parseInt( $( this ).data( data_section ), 10 );

						if( $( this ).prop( "checked" ) == true ){
							package_price = package_price + value;
						}
					});
					
					return package_price;
				}
				
				var pPrice = packagePrice();
				$( "span.package_price" ).text( "£" + pPrice + ".00" );
				
				
				// CALC
				$( ".package-option, .module_selector" ).change( function( e ){
					e.preventDefault();
					
					var pPrice = packagePrice();
					
					$( "span.package_price" ).text( "£" + pPrice + ".00" );
					
				});
		

				$('.package-option').click(function() {
					$('.package-option').not(this).prop('checked', false);
				});

				$(".account-creation-steps").click(function(){

					//Clear errors first
					$( '.error_message' ).each(function(){
						$( this ).text( '' );
					});

					var currentpanel = $(this).data("currentpanel");
					var inputs_state = check_inputs( currentpanel );

					if( inputs_state ){

						//If name attribute returned, auto focus to the field and display arror message
						$( '[name="'+inputs_state+'"]' ).focus().css("border","1px solid red");;
						//var labelText = $( '[name="'+inputs_state+'"]' ).parent().find('label').text();
						var palceholderText = $( '[name="'+inputs_state+'"]' ).prop('placeholder');
						if( palceholderText == undefined ){
							var palceholderText = $( '[name="'+inputs_state+'"]' ).data( 'placeholder' );
						}
						
						var labelText = ( palceholderText.length > 0 ) ?  palceholderText : inputs_state;

						$( '#'+currentpanel+'-errors' ).text( ucwords( labelText ) +' is a required' );
						return false;
					}
					panelchange("."+currentpanel)
					return false;
				});

				//** Validate any inputs that have the required class, if empty return the name attribute **/
				function check_inputs( currentpanel ){

					var result = false;
					var panel  = "." + currentpanel;

					$( $( panel + " .required" ).get().reverse() ).each( function(){
						var fieldName  = '';
						var inputValue = $( this ).val();
						if( ( inputValue == false ) || ( inputValue == '' ) || ( inputValue.length == 0 ) ){
							fieldName = $(this).attr( 'name' );
							console.log( 'Src'+fieldName );
							result    = fieldName;
							return result;
						}
					});
					return result;
				}

				$(".btn-back").click(function(){
					var currentpanel = $(this).data("currentpanel");
					go_back("."+currentpanel)
					return false;
				});

				function panelchange(changefrom){
					var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
					var changeto = ".account_creation_panel"+panelnumber;
					$(changefrom).hide( "slide", {direction : 'left'}, 500);
					$(changeto).delay(600).show( "slide", {direction : 'right'},500);
					return false;
				}

				function go_back( changefrom ){
					var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
					var changeto = ".account_creation_panel"+panelnumber;
					$(changefrom).hide( "slide", {direction : 'right'}, 500);
					$(changeto).delay(600).show( "slide", {direction : 'left'},500);
					return false;
				}

				//Submit account creation form
				$( '#account-creation-btn' ).click(function( e ){
					e.preventDefault();

					var formData = $( '#account-creation-form' ).serialize();

					swal({
						title: 'Confirm Business Account Sign-up?',
						showCancelButton: true,
						confirmButtonColor: '#5CB85C',
						cancelButtonColor: '#9D1919',
						confirmButtonText: 'Yes'
					}).then( function (result) {
						if ( result.value ) {
							$.ajax({
								url:"<?php echo base_url('webapp/account/create_account/' ); ?>",
								method:"POST",
								data:formData,
								dataType: 'json',
								success:function(data){
									if( data.status == 1 && ( data.account !== '' ) ){
										swal({
											type: 'success',
											title: data.status_msg,
											showConfirmButton: false,
											timer: 3000
										})
										window.setTimeout(function(){
											location.href = "<?php echo base_url('webapp/user/login/'); ?>";
										} ,3000);
									}else{
										swal({
											type: 'error',
											text: data.status_msg
										})
									}
								}
							});
						} else {
							$( ".account_creation_panel6" ).hide( "slide", { direction : 'left' }, 500 );
							go_back( ".account_creation_panel2" );
							return false;
						}
					}).catch(swal.noop)
				});

			});

			//UCwords function
			function ucwords ( str ) {
				return ( str.replace(/[^a-z0-9\s]/gi, ' ').replace(/[_\s]/g, ' ') + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
					return $1.toUpperCase();
				});
			}

		</script>
	</body>
</html>