<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
        <form id="user-details-form" class="form-horizontal">
            <input type="hidden" name="id" value="<?php echo $user_details->id; ?>" />
            <input type="hidden" name="page" value="permissions" />
            <input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />

            <div class="x_panel tile has-shadow">
                <legend>User Details</legend>
                <div class="input-group form-group">
                    <label class="input-group-addon">First Name</label>
                    <input name="first_name" value="<?php echo !empty($user_details->first_name) ? $user_details->first_name : ''; ?>" class="form-control" type="text" placeholder="First Name" />
                </div>
                <div class="input-group form-group">
                    <label class="input-group-addon">Last Name</label>
                    <input name="last_name" value="<?php echo !empty($user_details->last_name) ? $user_details->last_name : ''; ?>" class="form-control" type="text" placeholder="Last Name" />
                </div>
                <div class="input-group form-group">
                    <label class="input-group-addon">Username</label>
                    <input name="username" value="<?php echo !empty($user_details->username) ? $user_details->username : ''; ?>" class="form-control" type="text" placeholder="Username" />
                </div>
                <div class="input-group form-group">
                    <label class="input-group-addon">Email</label>
                    <input name="email" value="<?php echo !empty($user_details->email) ? $user_details->email : ''; ?>" class="form-control" type="text" placeholder="Email" />
                </div>
                <div class="input-group form-group">
                    <label class="input-group-addon">Mobile</label>
                    <input name="mobile_number" value="<?php echo !empty($user_details->mobile_number) ? $user_details->mobile_number : ''; ?>" class="form-control" type="text" placeholder="Mobile" />
                </div>

                <?php if (!empty($this->user->is_admin)) { ?>
                <div class="input-group form-group">
                    <label class="input-group-addon">User type</label>
                    <select name="user_type_id" class="form-control">
                        <option>Please select</option>
                        <?php if (!empty($user_types)) {
                            foreach ($user_types as $k => $user_type) { ?>
                            <option value="<?php echo $user_type->user_type_id; ?>" <?php echo ($user_type->user_type_id == $user_details->user_type_id) ? 'selected=selected' : ''; ?> ><?php echo $user_type->user_type_name; ?> <?php echo ($user_type->user_type_id == 1) ? '(System)' : ''; ?></option>
                            <?php }
                            } ?>
                    </select>
                </div>
                <?php } ?>

                <div style="display:block">
                    <div class="input-group form-group">
                        <label class="input-group-addon">Password</label>
                        <input name="password" type="password" value="" class="form-control" type="text" placeholder="Password"  />
                    </div>
                    <div class="input-group form-group">
                        <label class="input-group-addon">Password Confirm</label>
                        <input name="password_confirm" type="password" value="" class="form-control" type="text" placeholder="Confirm Password"  />
                    </div>
                <div>

                <?php if ($user_details->id == 1 && ($this->user->id != $user_details->id)) { ?>
                    <!-- <div class="row col-md-12">
                        <button class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >You do can not update this record</button>
                    </div> -->
                    <em>This record is readonly</em>
                <?php } else { ?>
                    <?php if ($this->user->is_admin || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
                        <div class="row">
                            <div class="col-md-6">
                                <button id="update-user-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next update-user-btn" type="button">Update User</button>
                            </div>

                            <?php if ($this->user->is_admin || !empty($permissions->can_delete) || !empty($permissions->is_admin)) { ?>
                                <div class="col-md-6">
                                    <button id="delete-user-btn" style="background: #b7001f"  class="btn btn-sm btn-block" type="button" data-user_id="<?php echo $user_details->id; ?>">Delete User</button>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <div class="row col-md-6">
                            <button class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function(){

        //Submit form for processing
        $( '#update-user-btn' ).click( function(){

            var formData = $('#user-details-form').serialize();
            var postUrl  = '<?php echo base_url("webapp/user/update_user/" . $user_details->id); ?>';

            swal({
                title: "Confirm user update?",
                showCancelButton: true,
                confirmButtonColor: "#5CB85C",
                cancelButtonColor: "#9D1919",
                confirmButtonText: "Yes"
            }).then( function (result) {
                if ( result.value ) {
                    $.ajax({
                        url:postUrl,
                        method:"POST",
                        data:formData,
                        dataType: 'json',
                        success:function(data){
                            if( data.status == 1 ){
                                swal({
                                    type: 'success',
                                    title: data.status_msg,
                                    showConfirmButton: false,
                                    timer: 3000
                                })
                                window.setTimeout(function(){
                                    location.reload();
                                } ,3000);
                            }else{
                                swal({
                                    type: 'error',
                                    title: data.status_msg
                                })
                            }
                        }
                    });
                }
            }).catch(swal.noop)
        });

        //Delete a user record (set as archived)
        $('#delete-user-btn').click(function(){

            var userId = $(this).data( 'user_id' );

            swal({
                title: 'Confirm delete user?',
                type: 'warning',
                /* text: 'This will also delete the attached person record', */
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function (result) {
                if ( result.value ) {
                    $.ajax({
                        url:"<?php echo base_url('webapp/user/delete_user/' . $user_details->id); ?>",
                        method:"POST",
                        data:{user_id:userId},
                        dataType: 'json',
                        success:function(data){
                            if( data.status == 1 ){
                                swal({
                                    type: 'success',
                                    title: data.status_msg,
                                    showConfirmButton: false,
                                    timer: 3000
                                })
                                window.setTimeout(function(){
                                    window.location.href = "<?php echo base_url('webapp/user/users'); ?>";
                                } ,3000);
                            }else{
                                swal({
                                    type: 'error',
                                    title: data.status_msg
                                })
                            }
                        }
                    });
                }
            }).catch(swal.noop)
        });
    });
</script>