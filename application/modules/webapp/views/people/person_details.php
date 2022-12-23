<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
        <form id="update-person-form-left" class="form-horizontal">
            <input type="hidden" name="page" value="details" />
            <input type="hidden" name="user_id" value="<?php echo $person_details->user_id; ?>" />
            <input type="hidden" name="person_id" value="<?php echo $person_details->person_id; ?>" />
            <input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
            <div class="x_panel tile has-shadow">
                <legend>Update Personal Info (ID: <?php echo $person_details->person_id; ?>)</legend>
                <div class="input-group form-group">
                    <label class="input-group-addon">Status</label>
                    <select id="user_status" name="status_id" class="form-control">
                        <option>Please select</option>
                        <?php if (!empty($user_statuses)) {
                            foreach ($user_statuses as $k => $status) { ?>
                            <option value="<?php echo $status->status_id; ?>" <?php echo ($person_details->status_id == $status->status_id) ? 'selected=selected' : ''; ?> ><?php echo $status->status; ?></option>
                            <?php }
                            } ?>
                    </select>
                </div>

                <div class="leave-date" style="display:<?php echo (in_array($person_details->status_id, [2])) ? 'block' : 'none'; ?>">
                    <div class="input-group form-group">
                        <label class="input-group-addon">Leave date</label>
                        <input name="leave_date" class="form-control datepicker" type="text" placeholder="Leave date" value="<?php echo (validate_date($person_details->leave_date)) ? date('d-m-Y', strtotime($person_details->leave_date)) : ''; ?>" />
                    </div>
                </div>
                
                <div class="input-group form-group">
                    <label class="input-group-addon">Title</label>
                    <select id="title" name="title" class="form-control">
                        <option>Please select</option>
                        <?php if (!empty($user_titles)) {
                            foreach ($user_titles as $k => $title) { ?>
                            <option value="<?php echo $title; ?>" <?php echo ((!empty($person_details->title)) && (strtolower($person_details->title) == strtolower($title))) ? 'selected="selected"' : ''; ?> ><?php echo ucfirst($title); ?></option>
                            <?php }
                            } ?>
                    </select>
                </div>
                <div class="input-group form-group">
                    <label class="input-group-addon">First name</label>
                    <input name="first_name" class="form-control readonly-data" type="text" placeholder="First name" value="<?php echo (!empty($person_details->first_name)) ? $person_details->first_name : '' ; ?>" readonly />
                </div>
                <div class="input-group form-group">
                    <label class="input-group-addon">Last name</label>
                    <input name="last_name" class="form-control readonly-data" type="text" placeholder="Last name" value="<?php echo (!empty($person_details->last_name)) ? $person_details->last_name : '' ; ?>" readonly />
                </div>
                <div class="input-group form-group">
                    <label class="input-group-addon">Preferred name</label>
                    <input name="preferred_name" class="form-control" type="text" placeholder="Preffered name" value="<?php echo (!empty($person_details->preferred_name)) ? $person_details->preferred_name : '' ; ?>" />
                </div>
                <div class="input-group form-group">
                    <label class="input-group-addon">Work email</label>
                    <input name="work_email" class="form-control" type="text" placeholder="Work email" value="<?php echo (!empty($person_details->work_email)) ? $person_details->work_email : '' ; ?>" />
                </div>
                <div class="input-group form-group">
                    <label class="input-group-addon">Work mobile</label>
                    <input name="work_mobile" class="form-control" type="text" placeholder="Work mobile" value="<?php echo (!empty($person_details->work_mobile)) ? $person_details->work_mobile : '' ; ?>" />
                </div>
                <div class="input-group form-group">
                    <label class="input-group-addon">Employment Status</label>
                    <select name="employment_status" class="form-control">
                        <option value="">Please select</option>
                        <option value="employee" <?php echo (!empty($person_details->employment_status) && (strtolower($person_details->employment_status) == "employee")) ? 'selected="selected"' : '' ; ?>>Employee</option>
                        <option value="subcontractor" <?php echo (!empty($person_details->employment_status) && (strtolower($person_details->employment_status) == "subcontractor")) ? 'selected="selected"' : '' ; ?>>Subcontractor</option>
                    </select>
                </div>

    <?php       if ($this->user->is_admin || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <button id="update-person-btn-1" class="btn btn-sm btn-block btn-flow btn-success btn-next update-person-btn" type="button">Update Person Info</button>
                        </div>

                        <?php /*    if( $this->user->is_admin || !empty( $permissions->can_delete ) || !empty( $permissions->is_admin ) ){ ?>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <button id="delete-person-btn" class="btn btn-sm btn-block btn-flow btn-danger has-shadow" type="button" data-person_id="<?php echo ( !empty( $person_details->person_id ) ) ? $person_details->person_id : '' ; ?>">Archive Person</button>
                                    </div>
                        <?php   } */ ?>
                    </div>
    <?php	  } else { ?>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled>No permissions</button>
                        </div>
                    </div>
    <?php	  } ?>

            </div>
        </form>
    </div>

    <div class="col-md-6 col-sm-6 col-xs-12">
        <form id="update-person-form-right" class="form-horizontal">
            <input type="hidden" name="page" value="details" />
            <input type="hidden" name="user_id" value="<?php echo $person_details->user_id; ?>" />
            <input type="hidden" name="person_id" value="<?php echo $person_details->person_id; ?>" />
            <input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
            <div class="x_panel tile has-shadow">
                <legend>Confidential Data</legend>
                <div class="input-group form-group">
                    <label class="input-group-addon">Date of Birth</label>
                    <input name="date_of_birth" class="form-control datepicker sensitive-data" type="password" placeholder="Date of Birth" value="<?php echo (validate_date($person_details->date_of_birth)) ? date('d-m-Y', strtotime($person_details->date_of_birth)) : ''; ?>" />
                </div>
                <div class="input-group form-group">
                    <label class="input-group-addon">Nationality</label>
                    <select name="nationality_id" class="form-control">
                        <option>Please select</option>
                        <?php if (!empty($countries)) {
                            foreach ($countries as $k => $country) { ?>
                            <option value="<?php echo $country->country_id; ?>" <?php echo ($person_details->nationality_id == $country->country_id) ? 'selected=selected' : ''; ?> ><?php echo $country->country_name; ?></option>
                            <?php }
                            } ?>
                    </select>
                </div>
                <div class="input-group form-group">
                    <label class="input-group-addon">Right to work</label>
                    <select name="right_to_work" class="form-control">
                        <option>Please select</option>
                        <option value="Yes" <?php echo (strtolower($person_details->right_to_work) == 'yes') ? 'selected=selected' : ''; ?> >Yes</option>
                        <option value="No" <?php echo (strtolower($person_details->right_to_work) != 'yes') ? 'selected=selected' : ''; ?> >No</option>
                    </select>
                </div>
                <div class="input-group form-group">
                    <label class="input-group-addon">Right to work type</label>
                    <input name="right_to_work_type" class="form-control" type="text" placeholder="Right to work type" value="<?php echo (!empty($person_details->right_to_work_type)) ? $person_details->right_to_work_type : '' ; ?>" />
                </div>
                <div class="input-group form-group">
                    <label class="input-group-addon">National Insurance</label>
                    <input name="national_insurance_number" class="form-control sensitive-data" type="password" placeholder="National Insurance number" value="<?php echo (!empty($person_details->national_insurance_number)) ? $person_details->national_insurance_number : '' ; ?>" />
                </div>
                <div class="input-group form-group">
                    <label class="input-group-addon">Personal email</label>
                    <input name="personal_email" class="form-control" type="text" placeholder="Personal email" value="<?php echo (!empty($person_details->personal_email)) ? $person_details->personal_email : '' ; ?>" />
                </div>
                <div class="input-group form-group">
                    <label class="input-group-addon">Personal mobile</label>
                    <input name="personal_mobile" class="form-control" type="text" placeholder="Personal mobile" value="<?php echo (!empty($person_details->personal_mobile)) ? $person_details->personal_mobile : '' ; ?>" />
                </div>
                <div class="input-group form-group">
                    <label class="input-group-addon">Personal landline</label>
                    <input name="personal_landline" class="form-control" type="text" placeholder="Personal landline" value="<?php echo (!empty($person_details->personal_landline)) ? $person_details->personal_landline : '' ; ?>" />
                </div>
                
                
                
                <div class="row">
    <?php       if ($this->user->is_admin || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <button id="update-person-btn-2" class="btn btn-sm btn-block btn-flow btn-success btn-next update-person-btn" type="button" >Update Confidential Data</button>
                    </div>
    <?php       } else { ?>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="button" disabled >No permissions</button>
                    </div>
    <?php       } ?>
                </div>
            </div>
        </form>
    </div>

</div>

<script>
    $(document).ready(function(){

        $( '#user_status' ).change( function(){
            var statusId = $('option:selected', this).val();
            if( statusId == '2' ){
                $( '.leave-date' ).slideDown();
            }else{
                $( '.leave-date' ).slideUp();
                $( '[name="leave_date"]' ).val( '' );
            }
        });

        $( '.readonly-data' ).click( function(){
            swal({
                title: 'Ops! Readonly field',
                text: 'You can change this field in User Manager',
            })
        });

        //Submit form for processing
        $( '.update-person-btn' ).click( function( event ){

            event.preventDefault();
            var formData = $(this).closest('form').serialize();
            swal({
                title: 'Confirm person update?',
                // type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function (result) {
                if ( result.value ) {
                    $.ajax({
                        url:"<?php echo base_url('webapp/people/update_person/' . $person_details->person_id); ?>",
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
                                }, 3000);
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

        $('#delete-person-btn').click(function(){

            var personId = $(this).data( 'person_id' );

            swal({
                title: 'Confirm person delete?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function (result) {
                if ( result.value ) {
                    $.ajax({
                        url:"<?php echo base_url('webapp/people/delete_person/' . $person_details->person_id); ?>",
                        method:"POST",
                        data:{person_id:personId},
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
                                    window.location.href = "<?php echo base_url('webapp/people'); ?>";
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

