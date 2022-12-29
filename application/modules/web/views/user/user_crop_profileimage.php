<link rel="stylesheet" href="<?php echo base_url('assets/cropme/cropme.min.css'); ?>">
<script src="<?php echo base_url('assets/cropme/cropme.min.js'); ?>"></script>

<style>

#profile-details .input-group-addon{

    min-width: 150px;
}

.mydetails-module-link:hover {
    color: white !important;
}

.mydetails-profile-header {
    margin: 0px;
}

</style>

  <div id="profile-details" class="mydetails-modal">
    <div class="mydetails-modal-content" id="user-profile">

        <div class="mydetails-top">
            <div style="position:absolute;top:15px;right:15px"><i class="fas fa-times fa-2x pointer" id="exit-profile-details" style="color:white;z-index:1000"></i></div>
            <div class="mydetails-profile-header">
                
                <div class='wizard-header' style="padding:20px;">
                    <i class="fas fa-tasks" style="color:white;font-size:55px;"></i>
                    <div style="margin-left:10px;font-size:20px;display:inline-block; display: inline-block;color:white;">
                        Upload a new profile image<br><small>Crop your new image</small>
                    </div>
                </div>
                
        </div>
  
        </div>
      
      <div class="mydetails-bottom" style="height:100%">
          
          <div class='image-tools' style='position:absolute;bottom:20px;left:20px;z-index:100'>
              <div class='pointer rotate-cropped-image' style='height:65px;width:65px;border-radius:50%;background-color: white;'><i class="fas fa-undo" style='font-size:30px;color:gray;position:absolute;left:calc(50% - 15px);top:calc(50% - 15px);'></i></div>
          </div>
          
          <div class='pointer upload-cropped-image' style='position:absolute;bottom:20px;right:20px;z-index:100;height:65px;width:65px;border-radius:50%;background-color: #03cd00; background-image: linear-gradient(to bottom right, #00cd0e, #67f06e);'><i class="fas fa-check" style='font-size:30px;color:white;position:absolute;left:calc(50% - 15px);top:calc(50% - 15px);'></i></div>
		  
          
          <div style="height:100%;">
            <div class="module module-mydetails" >
                <div class="form-container">
                    <div class='container' style="position:relative;width:100%;height:100%;display:block;">
                        <div id="cropImageView" />
                    </div>
                    <script>
                    
                      this.options = {
                          "container": {
                            "width": "100%",
                          },
                          "viewport": {
                            "width": 200,
                            "height": 200,
                            "type": "circle",
                            "border": {
                              "width": 2,
                              "enable": true,
                              "color": "#fff"
                            }
                          },
                          "zoom": {
                            "enable": true,
                            "mouseWheel": true,
                            "slider": false
                          },
                          "transformOrigin": "viewport"
                      }
                      
                      
                                            
                      this.cropme = new Cropme(document.getElementById('cropImageView'), this.options)
                      this.cropme.bind({
                        url: '<?php echo $profile_image; ?>',
                      })
                       
                      cropMeThis = this.cropme;
                      
                      cropMeThis.rotation = 0;
                      
                      $("#profile-details").find(".upload-cropped-image").on('click', function(event) {
                          cropMeThis.crop().then(res => {
                            $.ajax({
                                url:"<?php echo base_url('webapp/user/update_profile_picture'); ?>",
                                method:"POST",
                                dataType: "json",
                                data:{ base64Image : res },
                                success:function( result ){
                                    $("#profile-details").remove()
                                    mydetails_active = false
                                    cropMeThis.destroy()
                                    if(result.status == 1){
                                        
                                        swal({
                                            title: "Profile Image",
                                            text: "Profile picture has been updated!",
                                            type: "success",
                                            showCancelButton: false,
                                         }).then(
                                               function () { 
                                                   $.ajax({
                                                       url:"<?php echo base_url('webapp/user/my_details');?>",
                                                       method:"POST",
                                                       dataType: "text",
                                                       success:function( result ){
                                                           $( "body" ).append( result );
                                                       }
                                                   });
                                           
                                           });
                                        
                                        
                                    } else {
                                        Swal.fire(
                                          'Profile Image',
                                          'An error occured while attempting to update your profile picture.',
                                          'error'
                                        )
                                    }
                                }
                            });
                            
                          })
                      })
                      
                      $("#profile-details").find(".rotate-cropped-image").on('click', function(event) {
                          cropMeThis.rotation -= 90
                          cropMeThis.rotate(cropMeThis.rotation)
                      });
                      
                      
                      $(document).on('click', function (e) {
                          if($(e.target).attr("id") == "profile-details"){
                              $("#profile-details").remove();
                              mydetails_active = false
                              cropMeThis.destroy()
                          }
                      });
                  
                      $("#exit-profile-details").click(function(){
                          $("#profile-details").remove();
                          mydetails_active = false
                          cropMeThis.destroy()
                      });

                      
                    </script>
                </div>
			</div>

		  </div>
	  </div>
    </div>
  </div>
