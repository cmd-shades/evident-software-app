<!-- Run the admin check if a Tab requires that you're admin to view -->
<?php if( !empty( $admin_no_access ) ){
	$this->load->view('errors/access-denied', false, false, true );
}else{ ?>
<!-- Move this to the css file after manipulation -->
<style>
	.accordion .panel-heading {
		background: #ccc;
		padding: 13px;
		width: 100%;
		display: block;
	}

	.alert-info-bars {
		color: #fff;
		background: #0092CD;
		background-color: rgba(0, 146, 205, 0.88);
		border-color: rgba(0, 146, 205, 0.88);
	}
	
	.module-actions{
		margin-left: 20px;		
	}
</style>

<div class="row">
	<div class="col-md-6 col-sm-6 col-xs-12">
		<!-- <form id="user-permissions-form" action="<?php echo base_url( '/webapp/user/update_permissions/'.$user_details->id ); ?>" method="post" class="form-horizontal"> -->
		<form id="user-permissions-form" class="form-horizontal">
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<input type="hidden" name="id" value="<?php echo $user_details->id; ?>" />
			<div class="x_panel tile has-shadow">
				<legend>Module Access Permissions</legend>
				<div class="accordion" id="accordion" aria-multiselectable="true">
					<div class="row">
						<?php if( !empty( $account_modules ) ){ $k = 0; foreach( $account_modules as $category=>$cat_details ){ ?>
							<?php foreach( $cat_details->modules as $key => $module_data ){ $k++; ?>
								<div class="col-md-6 col-sm-6 col-xs-12 module-class pointer" title="Click to change permissions to the <?php echo $module_data->module_name; ?> module" data-module_id="<?php echo $module_data->module_id; ?>" data-toggle="modal" data-target="#permissions-modal-md">
									<div class="alert alert-ssid alert-dismissible cat-group pointer " role="alert">
										<p><?php echo $module_data->module_name; ?> <span class="pull-right"><i class="<?php echo $cat_details->category->category_icon_class; ?>"></i></span></p>
									</div>
								</div>
							<?php } ?>
						<?php } }else{ ?>
							<div class="col-md-12 col-sm-6 col-xs-12">
								<span>There's currently no modules available for your account.</span>
							</div>
						<?php } ?>
					</div>

					<div class="modal fade permissions-modal-md" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog modal-md">
							<div class="modal-content">
								<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
									<h4 class="modal-title" id="myModalLabel">Update Module Permissions for <strong><?php echo ucwords($user_details->first_name); ?> <?php echo ucwords($user_details->last_name); ?></strong></h4>								</div>
								<div class="modal-body"></div>
							</div>
						</div>
					</div>
					
				</div>
				<div class="clearfix"></div>
			</div>
		</form>
	</div>
	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="username-criteria info-containers" style="display:block">
			<div class="alert alert-info-bars alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4>Setting user permissions <sup class="text-yellow text-italic">Beta</sup></h4>
				<p>&#8226; &nbsp; All user permissions are set on a per module-basis for the selected user profile</p>
				<p>&#8226; &nbsp; To set permissions, click on the module name from the listed modules</p>
				<p>&#8226; &nbsp; A popup displays with all the available tabs under the clicked module name</p>
				<p>&#8226; &nbsp; A list of permissions are listed: </p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp; &#187; &nbsp;<strong>Grant/Revoke Access</strong><em> - Grants or revokes the minimal permissions (view only) for the entire module</em></p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp; &#187; &nbsp;<strong>Is Admin</strong><em> - Grants Admin rights for the entire module</em></p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp; &#187; &nbsp;<strong>View</strong><em> - view only access to the selected tab</em></p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp; &#187; &nbsp;<strong>Edit</strong><em> - view &amp; edit access to the selected tab</em></p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp; &#187; &nbsp;<strong>Delete</strong><em> - view &amp; delete access to the selected tab</em></p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp; &#187; &nbsp;<strong>Full access</strong><em> - Grants full access to selected tab</em></p>
				<br/>
            </div>
		</div>
	</div>
</div>

<?php } ?>

<script>
	$(document).ready(function(){
		
		$( '.cat-group' ).click(function(){
			var modGrpClass = $(this).data( 'cat_group' );
			$('.'+modGrpClass).slideToggle();				
		});
		
		$( '.module-class' ).click(function(){
			
			var moduleId = $(this).data( 'module_id' );
			var userId 	 = "<?php echo $user_details->id; ?>";
			
			var csrfName	= 'xsrf_token',
				xsrfToken	= "<?php echo ( !empty( $xsrf_token ) ) ? $xsrf_token : false ?>";
		
				$.ajaxSetup({
					beforeSend: function( jqXHR, settings ) {
						settings.data += '&'+csrfName+'='+xsrfToken
					}
				});
		
			$.ajax({
				url:"<?php echo base_url('webapp/user/module_items_list/'.$user_details->id.'/' ); ?>" + moduleId,
				method:"POST",
				data:{module_id:moduleId, user_id:userId},
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						$( ".modal-body ").html( data.module_items );
						$( ".permissions-modal-md" ).modal( "show" );		
					}else{
						swal({
							type: 'error',
							title: data.status_msg
						})
					}		
				}
			});
			
		});
		
		//Update Module access permissions
		//Submit updated permissions with every click
		$( '.modal-body' ).on( 'click', '.module-access', function(){
			
			var moduleId 	 		= $( this ).data( 'module_id' );
			var grpContainerClass 	= '.module-access-grp';
			
			if( $( this ).hasClass( 'grant-access' ) ){
				
				if( $( this ).is(':checked') ){
					$( '.view' ).each( function(){					
						$( this ).prop( 'checked', true );
					});
				}else{
					$( '.mod-item-chk, .grant-admin-access' ).each( function(){
						$( this ).prop( 'checked', false );
					});
				}
				
			}else if( $( this ).hasClass( 'grant-admin-access' )){
				if( $( this ).is( ':checked' ) ){
					
					$( '.mod-item-chk, .grant-access' ).each( function(){					
						$(this).prop( 'checked', true );
					});
				}else{
					
					$( '.mod-item-chk' ).each( function(){
						if( !$( this ).hasClass( 'view' ) ){
							$( this ).prop( 'checked', false );
						}
					});
					
					$( '.modal-body' ).find( '.grant-mobile-access' ).prop( 'checked', false )
					
					if($( '.modal-body' ).find( '.grant-mobile-access' ).prop( 'checked' )){
						Swal.fire({
							title: 'Info',
		  			      	text: "Mobile access requires all permissions.",
		  			      	type: 'info',
						})
					}	
				}
			}else if( $( this ).hasClass( 'grant-mobile-access' )){
				if( $( this ).is( ':checked' ) ){
					
					$( '.mod-item-chk, .grant-access' ).each( function(){					
						$( this ).prop( 'checked', true );
					});
				}
			}
			
			var formData = $( '.modal-body' ).find( 'input' ).serialize()
			submitForm( moduleId, formData );
			
		});
		
		//Submit updated permissions with every click
		$('.modal-body').on('click', '.mod-item-chk', function(){ 
			
			var moduleId 	 	= $(this).data( 'module_id' );
			var moduleItemId 	= $(this).data( 'module_item_id' );
			var containerClass 	= '.mod-item-grp' + moduleItemId;
			var permsClass   	= $(this).data( 'mod_item_class' );
			
			
			if( $( this ).hasClass( 'check-all' ) ){
				if( $( this ).is( ':checked' ) ){
					$( '.' + permsClass ).each( function(){					
						$(this).prop( 'checked', true );
					});
					updateForm(moduleId, moduleItemId, containerClass, permsClass)
				}else if( $( '.modal-body' ).find( '.grant-mobile-access' ).prop( 'checked' ) ){
					$( this ).prop( 'checked', true )
					Swal.fire({
						title: 'info',
						text: "Mobile access requires all permissions! Continuing will disable mobile access.",
						type: 'warning',
						showCancelButton: true,
						confirmButtonText: 'Continue'
					}).then(( result ) => {
					  if ( result.value ) {
						$( this ).prop( 'checked', false );
						$( '.' + permsClass ).each( function(){					
							$( this ).prop( 'checked', false );
						});
						$( '.modal-body' ).find( '.grant-mobile-access' ).prop( 'checked', false)
						updateForm( moduleId, moduleItemId, containerClass, permsClass );
					  }
					})
				} else {
					$( '.' + permsClass ).each( function(){					
						$( this ).prop( 'checked', false );
					});
					updateForm(moduleId, moduleItemId, containerClass, permsClass)
				}
			} else if( !$( this ).prop( 'checked' ) && $( '.modal-body' ).find( '.grant-mobile-access' ).prop( 'checked' ) ){
				$( this ).prop( 'checked', true )
				Swal.fire({
					title: 'info',
					text: "Mobile access requires all permissions! Continuing will disable mobile access.",
					type: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Continue'
				}).then( ( result ) => {
					if ( result.value ) {
						$( this ).prop( 'checked', false )
						$( '.modal-body' ).find( '.grant-mobile-access' ).prop( 'checked', false )
						updateForm( moduleId, moduleItemId, containerClass, permsClass )
					}
				})
			} else {
				updateForm( moduleId, moduleItemId, containerClass, permsClass )
			}
			
		});
		
		function updateForm( moduleId, moduleItemId, containerClass, permsClass ){
			//Listen to click from the Full-access input field
			if( $( this ).hasClass( 'check-all' ) ){
				
				if( $(this).is(':checked') ){
					$( '.' + permsClass ).each( function(){					
						$( this ).prop( 'checked', true );
					});
				}else{
					$( '.'+permsClass ).each( function(){					
						$(this).prop( 'checked', false );
					});
				}
				
			}else{
				
				//Check all inputs that are not admin
				
				var chkCount  = 0;
				var totalChekd= 0;
				var unChekd   = 0;
			
				$( '.'+permsClass ).each( function(){
					
					//Only count inputs that are not admin
					if( !$( this ).hasClass( 'check-all' ) ){
						chkCount++;
						if( $( this ).is(':checked') ){
							totalChekd++;						
						}else{
							unChekd++;
						}
					}
				});
				
				if( chkCount > 0 && ( chkCount == totalChekd ) ){
					$( '#'+permsClass ).prop( 'checked', true );
				}else{
					$( '#'+permsClass ).prop( 'checked', false );
				}

			}
			
			var formData = $( '.modal-body' ).find( 'input' ).serialize()
			
			submitForm( moduleId, formData );
			
		}
		
		//Update permissions
		function submitForm( moduleId, formData ){
			
			var csrfName	= 'xsrf_token',
				xsrfToken	= "<?php echo ( !empty( $xsrf_token ) ) ? $xsrf_token : false ?>";
		
				$.ajaxSetup({
					beforeSend: function( jqXHR, settings ) {
						settings.data += '&'+csrfName+'='+xsrfToken
					}
				});
			
			//submit only the changed items
			$.ajax({
				url:"<?php echo base_url('webapp/user/update_module_permissions/'.$user_details->id ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 ){
						$( '#feedback_message').text( data.status_msg ).delay( 2000 ).fadeToggle( "slow" );							
					}else{
						swal({
							type: 'error',
							title: data.status_msg
						})
					}			
				}
			});
			
		}
		
		//Set as item admin
		$( '.modal-body' ).on( 'click', '.tick-all', function(){ 
			var permsClass = $( this ).find( 'input' ).data( 'mod_item_class' );			
		});
	
	});
</script>