<style>
    img {
        cursor: zoom-in;
    }
    
    .perms-block{
        cursor:pointer;
    }
    
    .perms-block :hover,
    .perms-block :active{
        color:orange;
    }
    
    label {
        display: inline-block;
        max-width: 100%;
        margin-bottom: 0px;
        font-weight: 400;
    }
</style>

<div class="x_content">
    <?php if (!empty($user_details)) { ?>
        <h4 class="hide">Manage <?php echo (!empty($user_details->first_name)) ? ucwords($user_details->first_name) : 'User';?>'s Profile</h4>
        <div class="row">
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="flex">
                    <legend>Personal Details</legend>
                    <form method="post" action="#">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                <input type="text" name="first_name" value="<?php echo !empty($user_details->first_name) ? $user_details->first_name : ''; ?>" class="form-control has-feedback-left" placeholder="First Name">
                                <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                <input type="text" name="last_name" value="<?php echo !empty($user_details->last_name) ? $user_details->last_name : ''; ?>" class="form-control has-feedback-left" placeholder="Last Name">
                                <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                            </div>
                        </div>      
                        
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                <input type="text" name="email" value="<?php echo !empty($user_details->email) ? $user_details->email : ''; ?>" class="form-control has-feedback-left" placeholder="Email">
                                <span class="fa fa-at form-control-feedback left" aria-hidden="true"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                <input type="text" name="username" value="<?php echo !empty($user_details->username) ? $user_details->username : ''; ?>" class="form-control has-feedback-left" placeholder="Username">
                                <span class="fa fa-sign-in form-control-feedback left" aria-hidden="true"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                <input type="text" name="phone" value="<?php echo !empty($user_details->phone) ? $user_details->phone : ''; ?>" class="form-control has-feedback-left" placeholder="Mobile">
                                <span class="fa fa-phone form-control-feedback left" aria-hidden="true"></span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-info btn-block">Update Details</button>
                    </form>
                </div>
                <br/>
            </div>
            
            <div class="modal fade" id="enlargeImageModal" tabindex="-1" role="dialog" aria-labelledby="enlargeImageModal" aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        </div>
                        <div class="modal-body">
                            <img src="" class="enlargeImageModalSource" style="width: 100%;">
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if ($user_details->is_admin || in_array('admin', $permitted_actions)) { ?>
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="flex">
                        <legend>Permissions</legend>
                        <form>
                            <?php if (!empty($account_modules)) { ?>
                                <?php foreach ($account_modules as $k => $module_details) { ?>
                                    <h6 class="text-bold perms-block" data-module_tag="mod<?php echo $module_details->module_id; ?>"><strong><?php echo ucwords($module_details->module_name); ?></strong></h6>
                                    <div id="mod<?php echo $module_details->module_id; ?>" style="display:<?php echo ($k == 0) ? 'block' : 'none'; ?>; margin-left:20px">
                                        <div class="icheckbox_flat-green">
                                            <input id="perm_chk1<?php echo $module_details->module_id; ?>" type="checkbox" class="flat"> <label for="perm_chk1<?php echo $module_details->module_id; ?>" >Can add</label>
                                        </div>
                                        <div class="icheckbox_flat-green">
                                            <input id="perm_chk2<?php echo $module_details->module_id; ?>" type="checkbox" class="flat"> <label for="perm_chk2<?php echo $module_details->module_id; ?>" >Can view</label>
                                        </div>
                                        <div class="icheckbox_flat-green">
                                            <input id="perm_chk3<?php echo $module_details->module_id; ?>" type="checkbox" class="flat"> <label for="perm_chk3<?php echo $module_details->module_id; ?>" >Can delete</label>
                                        </div>
                                        <div class="icheckbox_flat-green">
                                            <input id="perm_chk4<?php echo $module_details->module_id; ?>" type="checkbox" class="flat"> <label for="perm_chk4<?php echo $module_details->module_id; ?>" >Is admin</label>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } else { ?>
                                <span>There's currently no modules for this account, please contact support.</span>
                            <?php } ?>
                            <button class="btn btn-sm btn-info btn-block disabled" >Update Permissions</button>
                        </form>
                    </div>
                    <br/>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <span>User record not found. <a class="go-back" onclick="goBack();" >Go back</a></span>
            </div>
        </div>
    <?php } ?>
</div>

<script>
    $(function() {
        $('img').on('click', function() {
            $('.enlargeImageModalSource').attr('src', $(this).attr('src'));
            $('#enlargeImageModal').modal('show');
        });
        
        $('.perms-block').click(function(){
            var permsClassId = $(this).data('module_tag');
            $('#'+permsClassId).slideToggle();
        });
    });
</script>
