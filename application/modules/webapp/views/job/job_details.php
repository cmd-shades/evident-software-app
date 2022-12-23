<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
        <form id="update-job-form" class="form-horizontal">
            <input type="hidden" name="page" value="details" />
            <input type="hidden" name="job_id" value="<?php echo $job_details->job_id; ?>" />
            <input type="hidden" name="site_id" value="<?php echo (!empty($job_details->site_id)) ? $job_details->site_id : ''; ?>" />
            <input type="hidden" name="customer_id" value="<?php echo (!empty($job_details->customer_id)) ? $job_details->customer_id : ''; ?>" />
            <input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
            <div class="x_panel tile has-shadow">
                <legend>Update Job Details</legend>
                <div class="input-group form-group">
                    <label class="input-group-addon">Job type</label>
                    <select name="job_type_id" class="form-control">
                        <option>Please select</option>
                        <?php if (!empty($job_types)) {
                            foreach ($job_types as $k => $job_type) { ?>
                            <option value="<?php echo $job_type->job_type_id; ?>" <?php echo ($job_details->job_type_id == $job_type->job_type_id) ? 'selected=selected' : ''; ?> ><?php echo $job_type->job_type; ?></option>
                            <?php }
                            } ?>
                    </select>   
                </div>
                <div class="input-group form-group">
                    <label class="input-group-addon">Job date</label>
                    <input name="job_date" class="form-control datepicker" type="text" placeholder="Job date" value="<?php echo (validate_date($job_details->job_date)) ? date('d-m-Y', strtotime($job_details->job_date)) : ''; ?>" />
                </div>
                <div class="input-group form-group">
                    <label class="input-group-addon">Job status</label>
                    <select name="status_id" class="form-control">
                        <option>Please select</option>
                        <?php if (!empty($job_statuses)) {
                            foreach ($job_statuses as $k => $job_status) { ?>
                            <option value="<?php echo $job_status->status_id; ?>" <?php echo ($job_details->status_id == $job_status->status_id) ? 'selected=selected' : ''; ?> ><?php echo $job_status->job_status; ?></option>
                            <?php }
                            } ?>
                    </select>   
                </div>
                
                <div class="input-group form-group">
                    <label class="input-group-addon">Job assignee</label>
                    <select name="assigned_to" class="form-control">
                        <option>Please select</option>
                        <?php if (!empty($operatives)) {
                            foreach ($operatives as $k => $operative) { ?>
                            <option value="<?php echo $operative->id; ?>" <?php echo ($operative->id == $job_details->assigned_to) ? 'selected=selected' : ''; ?> ><?php echo $operative->first_name . " " . $operative->last_name; ?></option>
                            <?php }
                            } ?>
                    </select>   
                </div>
                
                <div class="form-group">
                    <label class="">Job notes</label>
                    <textarea name="job_notes" class="form-control" type="text" value="" style="width:100%; height:85px;" placeholder="<?php echo (!empty($job_details->job_notes)) ? 'Last note: ' . $job_details->job_notes : 'Job notes...' ?>"></textarea>
                </div>

                <?php if ($this->user->is_admin || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
                    <div class="row">
                        <div class="col-md-6">
                            <button id="update-job-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="button" >Update Job Details</button>                   
                        </div>
                        
                        <?php if ($this->user->is_admin || !empty($permissions->can_delete) || !empty($permissions->is_admin)) { ?>
                            <div class="col-md-6">
                                <button id="delete-job-btn" class="btn btn-sm btn-block btn-flow btn-danger has-shadow" type="button" data-job_id="<?php echo $job_details->job_id; ?>">Delete Job</button>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <div class="row col-md-6">
                        <button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>                 
                    </div>
                <?php } ?>
            </div>
        </form>
    </div>
    
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="x_panel tile has-shadow">
            <legend>Job GPS Location</legend>
            <table style="width:100%">
                <tr>
                    <th colspan="2">GPS Location (start) &nbsp;: <?php echo (!empty($job_details->gps_latitude)) ? 'Lat: ' . $job_details->gps_latitude . ',' : ' - - -'; ?> <?php echo (!empty($job_details->gps_longitude)) ? ' Long: ' . $job_details->gps_longitude : ' - - - '; ?></th>                  
                </tr>
                <tr>
                    <th colspan="2">GPS Location (finish): <?php echo (!empty($job_details->finish_gps_latitude)) ? 'Lat: ' . $job_details->finish_gps_latitude . ',' : ((!empty($job_details->gps_latitude)) ? $job_details->gps_latitude : ' - - -'); ?> <?php echo (!empty($job_details->finish_gps_longitude)) ? ' Long: ' . $job_details->finish_gps_longitude : ((!empty($job_details->gps_longitude)) ? $job_details->gps_longitude : ' - - - '); ?></th>
                </tr>
                <tr>
                    <th width="50%"></th><td width="50%"></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="full-width">
                            <iframe width="100%" height="245" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=<?php echo $job_details->gps_latitude; ?>,<?php echo $job_details->gps_longitude; ?>&hl=es;zoom=1&amp;output=embed" ></iframe>
                            <!-- <iframe width="210" height="100" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed/v1/view?key=AIzaSyBo10O1NYI8Ppkcluanpr51rec5MpX8MDM&center=51.324034,-0.172468&zoom=18&maptype=roadmap" ></iframe> -->
                        </div>              
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        
        //Submit form for processing
        $( '#update-job-btn' ).click( function( event ){
                    
            event.preventDefault();
            var formData = $('#update-job-form').serialize();
            swal({
                title: 'Confirm job update?',
                // type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function (result) {
                if ( result.value ) {
                    $.ajax({
                        url:"<?php echo base_url('webapp/job/update_job/' . $job_details->job_id); ?>",
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
        
        //Delete Job from
        $('#delete-job-btn').click(function(){
            
            var jobId = $(this).data( 'job_id' );
            
            swal({
                title: 'Confirm delete Job?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function (result) {
                if ( result.value ) {
                    $.ajax({
                        url:"<?php echo base_url('webapp/job/delete_job/' . $job_details->job_id); ?>",
                        method:"POST",
                        data:{job_id:jobId},
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
                                    window.location.href = "<?php echo base_url('webapp/job/jobs'); ?>";
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

