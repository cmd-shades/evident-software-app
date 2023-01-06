<style>

    #profile-details .input-group-addon{

        min-width: 150px;
    }

    .mydetails-module-link:hover {
        color: white !important;
    }


    .sort-module {
        padding-top: 5px;
        padding-bottom: 5px;
        color: rgb(50, 50, 50);
        height:100%;
        text-align: center;
        margin: 10px;
        padding-left: 10px;
        padding-right:10px;
        background-color: #e8e8e8;
        background-image: linear-gradient(to bottom right, #e8e8e8, #d9d9d9);
    }

    #sort-modules {
        padding-inline-start: 0px;
        list-style-type: none;
        width: 100%;
        height:100%;
    }

    .sort-module-active {
        background-color: #52b4de;
        background-image: linear-gradient(to bottom right, #52b4de, #41abd9);
        color: white;
    }
    
    .sort-module-icon {
        color:white;
        font-size:55px;
        vertical-align:top;
        margin-top:20px;
    }
    
    .sort-module-info {
        margin-left:10px;
        font-size:20px;
        display:inline-block; 
        color:white;
    }
    
</style>
<div id="quickbar-details" class="mydetails-modal">
<div class="mydetails-modal-content" id="user-profile">
    <div class="mydetails-top">
        <div style="position:absolute;top:15px;right:15px"><i class="fas fa-times fa-2x pointer" id="exit-modal" style="color:white;z-index:1000"></i></div>
        <div class="mydetails-profile-header">
            <div class='wizard-header' style="padding:20px;">
                <i class="fas fa-random sort-module-icon"></i>
                <div class='sort-module-info'>
                    Module Quickbar<br>
                    <small>Add 4 modules to your quickbar to access them faster<br>Drag items to rearrange their order.</small>
                </div>
            </div>
        </div>
    </div>
      <div class="mydetails-bottom" style="height:100%;">
          <ul id="sort-modules">

              <?php 
                  $quickbar_modules = $this->webapp_service->get_quickbar_modules($this->user->account_id);
                  
                  if($quickbar_modules){
                      $enabled_modules = array_column($quickbar_modules, 'module_id');
                      foreach( $permitted_modules  as $k => $module ){
                          echo "<li class='sort-module " . (in_array($module['module_id'], $enabled_modules) ? 'sort-module-active' : '') . "' style='text-align:left' data-module_id=" . $module['module_id'] . "><small>" .  $module['module_name'] . "</small><span style='float:right'><input class='active-module-checkbox' type='checkbox' " . (in_array($module['module_id'], $enabled_modules) ? 'checked' : '') . "></span></li>";
                      } 
                  } else {
                      foreach( $permitted_modules  as $k => $module ){
                          echo "<li class='sort-module' style='text-align:left' data-module_id=" . $module['module_id'] . "><small>" .  $module['module_name'] . "</small><span style='float:right'><input class='active-module-checkbox' type='checkbox'></span></li>";
                      } 
                  }
                  
               ?>
        </ul>
        <script>
        
        </script>
        
        <button id="update-quickbar" type="submit" class="btn btn-outline-primary md-update-btn" style="margin: 10px;">Update Quickbar</button>
    </div>
</div>
  

 
  
 
<script>


$(document).ready(function() {
     
     
     $("#quickbar-details").find("#update-quickbar").on('click', function(event) {
         active_modules = []
         
         $("#quickbar-details").find("#sort-modules").find(".sort-module-active").each(function(index, elem) {
             active_modules.push($(elem).attr("data-module_id"))
         });
         
         $.ajax({
             url:"<?php echo base_url('webapp/user/save_taskbar'); ?>",
             method:"POST",
             dataType:"json",
             data:{ user_quickbar_modules : active_modules },
             success:function( result ){
                 if(result.status == true){
                     Swal.fire({
                           title: 'Success',
                           text: "Successfully updated your account quickbar!",
                           type: 'success',
                           timer: 1000
                       }).then((result) => {
                         location.reload()
                       })
                 } else {
                     Swal.fire({
                       title: 'Error!',
                       text: result.status_msg,
                       type: 'error',
                       timer: 1000
                     })
                 }
             }
         });
     })
     
     
     $("#quickbar-details").find(".active-module-checkbox").on('click', function(event) {
         
         const MAX_QUICKBAR_MODULES = <?php echo json_encode($max_quickbar_modules); ?>
         
         if($("#sort-modules").find(".sort-module-active").length < MAX_QUICKBAR_MODULES){
             $(this).is(":checked") ? ($(this).closest('.sort-module').addClass('sort-module-active')) : ($(this).closest('.sort-module').removeClass('sort-module-active'))
         } else {

             if($(this).is(":checked")){
                 Swal.fire({
                   title: 'Info',
                   text: 'There is a maximum of ' + MAX_QUICKBAR_MODULES + ' quickbar items!',
                   type: 'info',
                 })
             }
             
             $(this).prop('checked', false);
             $(this).closest('.sort-module').removeClass('sort-module-active')
             
         }
     })
     
     $("#quickbar-details").find("#exit-modal").click(function(){
        $("#quickbar-details").remove();
    });
    
    $(document).on('click', function (e) {
        if($(e.target).attr("id") == "quickbar-details"){
            $("#quickbar-details").remove();
        }
    });
    
    $("#sort-modules").sortable()
    
    
    
});

</script>
</div>

