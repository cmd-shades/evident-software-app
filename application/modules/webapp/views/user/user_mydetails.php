<div id="profile-details" class="mydetails-modal">
	<style type="text/css">
		#user-profile .form-group {
			width: 100%;
		}

		#user-profile .input-group-addon{

			min-width: 150px;
		}

		#user-profile .mydetails-module-link:hover {
			color: white !important;
		}

		#user-profile .mydetails-module-link {
			font-weight: 100;
		}
	</style>
  
    <div class="mydetails-modal-content" id="user-profile">
      <div class="mydetails-top">
          <div style="position:absolute;top:15px;right:15px"><i class="fas fa-times fa-2x pointer" id="exit-profile-details" style="color:white;z-index:1000"></i></div>
          <div class="mydetails-profile-header">
              <div class="profile-icon-container" style="display:inline-block;position:relative;border-radius:50%;">
                  <div class="profile-icon" style="display:inline-block;">
                      
                      <?php if($profile_image['status']){ ?>
                          <!-- If user has profile picture -->
                          <img src="<?php echo $profile_image['image_link']; ?>" class='profile-image' onerror="noProfileImage()">
                          <!--                             -->
                      <?php } else { ?>
                          <!-- If user has no profile picture -->
                          <!-- Javascript below to detect for a missing profile image file -->
                          <div class="profile-icon-image"><i class="fas fa-user" style="color:white;"></i></div>
                      <?php } ?>
                      
                      <div class="hover-items" style='display:none;'>
                          <div class="upload-hover pointer <?php echo (!$profile_image['status']) ? 'upload-has-profile-image' : ''; ?> ">
                              <i class="fas fa-upload upload-icon"></i>
                          </div>
                          <div class="delete-hover pointer <?php echo (!$profile_image['status']) ? 'delete-has-profile-image' : ''; ?>">
                              <i class="fas fa-trash-alt delete-icon"></i>
                          </div>
                      </div>
                      <form id="pictureUploadForm" enctype='multipart/form-data' method="post" name="image_file" style="width:0px;height:0px;">
                          <input id="pictureFileInput" type="file" name="image_file" required />
                      </form>
                  </div>
              </div>
              <div style="font-weight:100;margin-left:10px;font-size:20px;margin-top:calc(75px - 20px);display:inline-block; position: absolute; display: inline-block;vertical-align:middle;color:white;">
                  <?php echo $this->user->first_name . " " . $this->user->last_name ?><br>
                  <small><span style="color:#f2f2f2"><?php echo $this->user->email; ?></span></small>
                  <div><small><span style="color:#f2f2f2"><?php echo ( !empty( $this->user->account_name ) ) ? $this->user->account_name : ''; ?></span></small></div>
              </div>

				<script>
					$( ".profile-icon" ).hover(
						function(){
							$( ".hover-items" ).css("display", "block")
						}, function() {
							$( ".hover-items" ).css("display", "none")
						}
					);


					function noProfileImage(){
						$("#user-profile").find(".profile-image").replaceWith( '<div class="profile-icon-image"><i class="fas fa-user" style="color:white;"></i></div>' );

					}
				</script>
			</div>

		</div>
		<div class="mydetails-bottom" style="height:100%;padding-top:10px;">
          
			<div class="mydetails-modules">
				<div class="profile-modules">
					<div class="row">
						<div class="col-md-4 col-sm-4 col-xs-12">
							<button type="button" class="btn btn-block btn-outline-dark mydetails-module-link mydetails-module-btn-active" data-module='.module-mydetails'>My Details</button>
						</div>
						<div class="col-md-4 col-sm-4 col-xs-12">
							<button type="button" class="btn btn-block btn-outline-dark mydetails-module-link" data-module='.module-changepassword' title="Change Password">Change P<span class="hidden-md hidden-sm">ass</span>w<span class="hidden-md hidden-sm">or</span>d</button>
						</div>
						<?php if( $this->user->is_admin || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
							<div class="col-md-4 col-sm-4 col-xs-12">
								<button type="button" class="btn btn-block btn-outline-dark mydetails-module-link who-is-logged-in-tab" data-module='.module-whoisloggedin'>Who's Logged In</button>
							</div>
						<?php } ?>
					</div>
				</div>
				<button type="button" class="btn btn-outline-dark logout-button" style="position:absolute;bottom:20px;right:20px;">Logout</button>
		  </div>
		  <div style="height:100%;padding:0 20px 20px 20px;">
            <div class="module module-mydetails" >
                <div class="form-container">
                    <h4>My Details</h4>
                    <hr>
					<form id="updatedetails-mydetails">
						<div class="input-group form-group">
							<label class="input-group-addon">Username</label>
							<input class="form-control" type="text" value="<?php echo $this->user->username; ?>" disabled>
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">First Name</label>
							<input id = "md-first_name" class="form-control" type="text" value="<?php echo !empty($this->user->first_name) ? $this->user->first_name : ''; ?>" placeholder="First Name" required>
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Last Name</label>
							<input id = "md-last_name" class="form-control" type="text" value="<?php echo !empty($this->user->last_name) ? $this->user->last_name : ''; ?>" placeholder="Last Name" required>
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Email</label>
							<input id = "md-email" class="form-control" type="text" value="<?php echo !empty($this->user->email) ? $this->user->email : ''; ?>" placeholder="Email" required>
						</div><br>
						<button type="submit" class="btn btn-outline-primary md-update-btn">Update my Details</button>
					</form>
                </div>
			</div>
            <div class="module module-changepassword" style="display:none;">
                <div class="form-container">
                    <h4>Change Password</h4>
                    <hr>
                    <form id="change-password-mydetails">
                        <div class="input-group form-group">
                            <label class="input-group-addon">Current Password</label>
                            <input id="md-current-pw" class="form-control" type="password" value="" placeholder="Current Password" required>
                        </div>
                        <div class="input-group form-group">
                            <label class="input-group-addon">New Password</label>
                            <input id="md-new-pw" class="form-control" type="password" value="" placeholder="New Password" required>
                        </div>
                        <div class="input-group form-group">
                            <label class="input-group-addon">New Password</label>
                            <input id="md-confirm-new-pw" class="form-control" type="password" value="" placeholder="Confirm New Password" required>
                        </div><br>
                        <button type="submit" class="btn btn-outline-primary md-update-btn">Update my Details</button>

                    </form>
                </div>
			</div>
			
			<?php if( $this->user->is_admin || !empty( $permissions->is_admin ) ){ ?>
				<div class="module module-whoisloggedin" style="display:none;">
					<div class="form-container">
						<h4>Who's Logged In</h4>
						<hr>
						<table class="table">
							<thead>
								<tr>
									<td width="30%" class="text-bold">Full Name</td>
									<td width="40%" class="text-bold">Username</td>
									<td width="30%" class="text-bold">Last Login</td>
								</tr>
							</thead>
							<tbody id="users-table-results">
								
							</tbody>
						</table>
					</div>
				</div>
			<?php } ?>
			</div>
			<hr>
		</div>
    </div>
</div>
<script type="text/javascript">
$( document ).ready( function(){
	$( "#users-table-results" ).on( "click", ".pagination li", function( e ){
		e.preventDefault();
		var paginationPage = $( this ).find( "a" ).data( "ci-pagination-page" );
		
		if( ( jQuery.isNumeric( paginationPage ) ) ) {
			$.ajax({
				url:"<?php echo base_url('webapp/user/whois_logged_in'); ?>",
				method:"POST",
				data:{
					page:'details',
					start_index: paginationPage
					},
				success:function(data){
					$( '#users-table-results' ).html(data);
				}
			});
		}

		return false;
	});



    $("#user-profile").on('click',".mydetails-module-link",function(){
        $("#profile-details").find(".module").hide()
        $("#profile-details").find(".mydetails-module-link").removeClass("mydetails-module-btn-active")
        $(this).addClass("mydetails-module-btn-active")
        $("#profile-details").find($(this).attr("data-module")).show();
		
		
		var moduleName = $( this ).data( 'module' );
		if( moduleName == '.module-whoisloggedin' ){
			$.ajax({
				url:"<?php echo base_url('webapp/user/whois_logged_in'); ?>",
				method:"POST",
				data:{
					page:'details',
					start_index: 1
					},
				success:function(data){
					$( '#users-table-results' ).html(data);
				}
			});
		}
    });

    $("#user-profile").on('click',".upload-hover",function(){
        $("#profile-details").find('#pictureFileInput').trigger('click');
    });
    
    $("#user-profile").on('click',".delete-hover",function(){
        Swal.fire({
              title: 'Are you sure?',
              text: "Are you sure you want to delete your profile image?",
              type: 'warning',
              showCancelButton: true,
        }).then((result) => {
          if(result.value){
              $.ajax({
                  url:"<?php echo base_url("webapp/user/delete_profile_picture") ?>",
                  method:"POST",
                  success:function( result ){
                  Swal.fire({
                        title: 'Success',
                        text: "Successfully deleted profile picture!",
                        type: 'success',
                    })
                    $("#profile-details").remove();
                    mydetails_active = false
                  }
              });
          }
        })
    });
    
    
    
    $("#profile-details").find('#pictureFileInput').change(function (){
        
        
        if($(this).val() != ""){
            var formData = new FormData($("#profile-details").find("#pictureUploadForm").get(0));

            
            $.ajax({
                url: '<?php echo base_url("webapp/user/prepare_profile_picture"); ?>',  
                type: 'POST',
                data: formData,
                dataType :'json',
                beforeSend: function() {
                    // requires an empty message to override the other beforeSend that adds the key in.
                },
                success:function(data){
                    if(data.status == 1){
                        $.ajax({
                            url:"<?php echo base_url("webapp/user/crop_profileimage") ?>",
                            method:"POST",
                            data:{ 'profile_image' : data.image_filename },
                            success:function( result ){
                                console.log(data)
                                $("#profile-details").html(result)
                            }
                        });
                    } else {
                        Swal.fire(
                          'Error!',
                          (data.message.error) ? (data.message.error) : 'An error occured while upload your profile image!',
                          'error'
                        )
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        } else {
            console.log("User did not choose an image to upload!")
        }
        /*
        var aProof = new FormData($("#profile-details").find("#pictureUploadForm").get(0));
        
        $.ajax({
            type:"POST",
            url:"<?php echo base_url('webapp/user/prepare_profile_picture'); ?>",
            data:aProof,
            contentType: false,
            processData: false,
            beforeSend: function() {
                
            },
            success:function(response){
                console.log(response);
            },
        });  */
        
        
    });

    $("#user-profile").find(".profile-image-upload").change(function(){
        
        supportedFileTypes = ['png', 'jpg']
        
        fileName = $("#user-profile").find(".profile-image-upload").val()
        fileType = fileName.split(".").pop()
        
        image_is_supported = supportedFileTypes.includes(fileType.toLowerCase())
        
        if(image_is_supported){
            
            $("#user-profile").find("#prof-upload-form").submit()
            
        } else {
            Swal.fire(
              'Profile Picture Upload',
              'Unsupported file type, please upload a .png, .jpeg or .jpg',
              'error'
            )
        }
        
    });


    $( "#user-profile" ).on( 'click',".logout-button",function(){
        window.location.href = "<?php echo base_url( '/webapp/user/logout' ); ?>"
    });

    $("#user-profile").on('submit','#change-password-mydetails',function(event){
       event.preventDefault();
       current_password = $("#change-password-mydetails").find("#md-current-pw").val()
       new_password = $("#change-password-mydetails").find("#md-new-pw").val()
       confirm_new_password = $("#change-password-mydetails").find("#md-confirm-new-pw").val()
       $.ajax({
           url:"<?php echo base_url('webapp/user/update_mypassword'); ?>",
           method:"POST",
           data:{ id : <?php echo $this->user->id; ?>, username: "<?php echo $this->user->username; ?>", old : current_password, new: new_password, new_confirm : confirm_new_password},
           dataType: "json",
           success:function( result ){
               Swal.fire(
                 'Success!',
                 (result.status == 1 ? 'Please login again.' : result.message),
                 (result.status == 1 ? 'success' : 'error')
               )

           }
       });
    });

    $("#user-profile").on('submit','#updatedetails-mydetails',function(event){
        event.preventDefault();

        first_name = $("#md-first_name").val()
        last_name = $("#md-last_name").val()
        email = $("#md-email").val()

        $.ajax({
            url:"<?php echo base_url('webapp/user/update_user'); ?>",
            method:"POST",
            data:{ id : <?php echo $this->user->id; ?>, 'first_name': first_name, 'last_name' : last_name, 'email' : email},
            dataType: "json",
            success:function( result ){
				Swal.fire(
                  (result.status == 1 ? 'Success' : 'Error'),
                  (result.status_msg) ? result.status_msg : 'An unknown error has occured!',
                  (result.status == 1 ? 'success' : 'error')
                )

                if(result.status == 1){
                    setTimeout(function() {
                        window.location.href = "<?php echo base_url("/webapp/user/logout"); ?>"
                    }, 3000);
                }
            }
        });

    });

    $(document).on('click', function (e) {
        if($(e.target).attr("id") == "profile-details"){
            $("#profile-details").remove();
            mydetails_active = false
        }
    });

    $("#exit-profile-details").click(function(){
        $("#profile-details").remove();
        mydetails_active = false
    });
	
	$( "#users-table-results" ).on("click", ".force-logout", function( event ){
			
		var userID 	= $( this ).data( 'user_id' );
		var postUrl	= '<?php echo base_url("webapp/user/force_user_logout/"); ?>'+userID;
		var adminUsr= '<?php echo $this->user->id; ?>';
		var userFn 	= $( this ).data( 'first_name' );
		
		swal({
			title: "Confirm Force Logout for "+userFn+"?",
			showCancelButton: true,
			confirmButtonColor: "#5CB85C",
			cancelButtonColor: "#9D1919",
			confirmButtonText: "Yes"
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url: postUrl,
					method:"POST",
					data:{page:'details', user_id: userID },
					dataType: 'json',
					success:function(data){
						if( data.status == 1 ){
							swal({
								type: 'success',
								text: data.status_msg,
								showConfirmButton: false,
								timer: 3000
							})
							window.setTimeout(function(){ 
							
								if( userID == adminUsr ){
									var new_url = '<?php echo base_url("webapp/user/login" ); ?>';
									window.location.href = new_url;
								} else {
									location.reload();
								}
								
							} ,3000);							
						}else{
							swal({
								type: 'error',
								text: data.status_msg
							})
						}		
					}
				});
			}
		}).catch(swal.noop)
		
	});
	
});
</script>