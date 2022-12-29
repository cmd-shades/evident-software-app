<style type="text/css">
.checkbox label:after,
.radio label:after {
    content: '';
    display: table;
    clear: both;
}

.checkbox .cr,
.radio .cr {
    position: relative;
    display: inline-block;
    border: 1px solid #a9a9a9;
    border-radius: .25em;
    width: 1.3em;
    vertical-align: bottom;
    height: 20px;
    margin: 16px 12px;
}

.radio .cr {
    border-radius: 50%;
}

.checkbox .cr .cr-icon,
.radio .cr .cr-icon {
    position: absolute;
    font-size: .8em;
    line-height: 0;
    top: 50%;
    left: 20%;
}

.radio .cr .cr-icon {
    margin-left: 0.04em;
}

.checkbox label input[type="checkbox"],
.radio label input[type="radio"] {
    display: none;
}

.checkbox label input[type="checkbox"] + .cr > .cr-icon,
.radio label input[type="radio"] + .cr > .cr-icon {
    transform: scale(3) rotateZ(-20deg);
    opacity: 0;
    transition: all .3s ease-in;
}

.checkbox label input[type="checkbox"]:checked + .cr > .cr-icon,
.radio label input[type="radio"]:checked + .cr > .cr-icon {
    transform: scale(1) rotateZ(0deg);
    opacity: 1;
}

.checkbox label input[type="checkbox"]:disabled + .cr,
.radio label input[type="radio"]:disabled + .cr {
    opacity: .5;
}


.input-group.form-group{
    width: 100%;
}

.input-group-addon:first-child{
    width: 30%;
    white-space: normal;
}


.checkbox .radio label, .checkbox > label{
    width: 100%;
}

.checkbox_label{
    width: 40%;
    min-width: 180px;
    float: left;
    padding: 18px 12px;
    font-size: 14px;
    font-weight: normal;
    line-height: 1;
    color: #555;
    text-align: center;
    background-color: #eee;
    border: 1px solid #ccc;
    border-radius: 4px;
    text-align: left;
}

/* end of checkboxes styling */


.id{
    width: 5%;
    margin: 0 !important;
    padding: 0 !important;
    min-width: 20px;
    text-align: center;
}

.valid_from{
    width: 5%;
    margin: 0 !important;
    padding: 0 !important;
    min-width: 20px;
    text-align: left;
}

.expiry_date{
    width: 5%;
    min-width: 20px;
}

.reminder_date{
    width: 5%;
    min-width: 20px;
}

.sent_date{
    width: 5%;
    min-width: 20px;
}

.sent_by{
    width: 10%;
    min-width: 40px;
}

.ref_number{
    width: 10%;
    min-width: 40px;
}

.scr_type{
    width: 10%;
    min-width: 40px;
}

.scr_result{
    width: 10%;
    min-width: 40px;
}

.action{
    width: 10%;
    min-width: 40px;
}

.padd_left_5{
    padding-left: 5px;
}

.washer > td{
    height: 3px;
    line-height: 0.1px;
    padding: 0;
}

.log_status_input, .log_status_select {
    background: #fff;
    height: 30px;
    border-radius: 6px;
    padding: 0 6px;
    border: 1px solid #a9a9a9;
    margin-bottom: 10px;
}

.log_status_input{
    width: 90%;
}

.log_note_in_modal{
    width: 100%;
    border-radius: 6px;
    margin-bottom: 10px;
}

.log_note_in_modal_container > td{
    vertical-align: top;
}

.margin_top_10{
    margin-top: 10px;
}

.upd_button {
    margin-top: 20px;
}

.checkbox_in_input{
    border: 1px solid #cccccc;
    border-top-right-radius: 4px;
    border-bottom-right-radius: 4px;
}

.executive_acceptance_of_risk > .input-group-addon{
    padding-left: 10px;
}


.executive_acceptance_of_risk > .checkbox_in_input{
    padding-left: 5px;
}

.delete_div{
    position: absolute;
    bottom: 0px;
    left: 0;
}
</style>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel tile has-shadow">
        
            <div class="rows" style="margin-top: 0px; margin-bottom: 6px;">
                <a class="btn btn-sm btn-flow btn-success btn-next pull-right no_right_margin" id="addNewlog">Create Log &nbsp;<i class="fas fa-chevron-down"></i></a>
                <legend>Existing Security Screening Log(s)</legend>
            </div>
<?php       if ($this->user->is_admin || !empty($permissions->can_view) || !empty($permissions->is_admin)) { ?>
            <div class="control-group form-group" style="width: 100%; display: block; float: left;">
                <table style="width:100%; table-layout: fixed;">
                    <tr>
                        <td class="id">ID</td>
                        <td class="valid_from">Valid From</td>
                        <td class="expiry_date">Expiry Date</td>
                        <td class="reminder_date">Reminder Date</td>
                        <td class="sent_date">Sent Date</td>
                        <td class="sent_by">Sent By</td>
                        <td class="ref_number">Reference Number</td>
                        <td class="scr_type">Screening Type</td>
                        <td class="scr_result">Screening Result</td>
                        <td class="action">Action</td>
                    </tr>
                </table>
            </div>
            <div class="control-group form-group table_body" style="width: 100%; display: block; float: left;">
                <table style="width:100%; table-layout: fixed;">
                    <?php
                    if (!empty($security_screening_logs)) {
                        $ctr = 1;
                        foreach ($security_screening_logs as $row) { ?>
                            <tr class="<?php echo $row->log_id; ?>" data-log_id="row_<?php echo $ctr; ?>">
                                <td class="id"><?php echo $row->log_id; ?></td>
                                <td class="valid_from"><?php echo (validate_date($row->valid_from)) ? format_date_client($row->valid_from) : ''; ?></td>
                                <td class="expiry_date"><?php echo (validate_date($row->expiry_date)) ? format_date_client($row->expiry_date) : ''; ?></td>
                                <td class="reminder_date"><?php echo (validate_date($row->reminder_date)) ? format_date_client($row->reminder_date) : ''; ?></td>
                                <td class="sent_date"><?php echo (validate_date($row->sent_date)) ? format_date_client($row->sent_date) : ''; ?></td>
                                <td class="sent_by"><?php echo $row->sent_by_full_name; ?></td>
                                <td class="ref_number"><?php echo $row->ref_number; ?></td>
                                <td class="scr_type"><?php echo $row->screening_type; ?></td>
                                <td class="scr_result"><?php echo $row->screening_result; ?></td>
                                <td class="action viewLogDetailsLink" data-target="#viewLogDetails" data-toggle="modal" data-log_id="<?php echo $row->log_id; ?>" ><a href="#viewLogDetails">More details</a></td>
                            </tr>
        
                            <tr class="washer">
                                <td colspan="10">&nbsp;</td>
                            </tr>
                            <?php
                            $ctr++;
                        }
                    } else { ?>
                        <tr>
                            <td colspan="10"><?php echo $this->config->item('no_records'); ?></td>
                        </tr>
                        <?php
                    } ?>
                </table>
<?php } ?>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="row create-security-log" style="display: none;">
            <form id="create-security-log">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="x_panel tile has-shadow fixed_height_340">
                        <legend>Add New Security Screening Log</legend>
                        <input type="hidden" name="page" value="health" />
                        <input type="hidden" name="person_id" value="<?php echo $person_details->person_id; ?>" />
                        <input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />

                        <div class="input-group form-group">
                            <label class="input-group-addon">Valid From</label>
                            <input name="valid_from" value="" class="form-control datetimepicker" placeholder="<?php echo date('d/m/Y'); ?>" data-date-format="DD/MM/Y" />
                        </div>

                        <div class="input-group form-group">
                            <label class="input-group-addon">Expiry Date</label>
                            <input name="expiry_date" value="" class="form-control datetimepicker" placeholder="<?php echo date('d/m/Y'); ?>" data-date-format="DD/MM/Y" />
                        </div>

                        <div class="input-group form-group">
                            <label class="input-group-addon">Reminder Date</label>
                            <input name="reminder_date" value="" class="form-control datetimepicker" placeholder="<?php echo date('d/m/Y'); ?>" data-date-format="DD/MM/Y" />
                        </div>
                        
                        <div class="input-group form-group">
                            <label class="input-group-addon">Sent Date</label>
                            <input name="sent_date" value="" class="form-control datetimepicker" placeholder="<?php echo date('d/m/Y'); ?>" data-date-format="DD/MM/Y" />
                        </div>

                        <div class="input-group form-group">
                            <label class="input-group-addon">Sent By</label>
                            <select name="sent_by" class="form-control" required>
                                <option value="">Please select</option>
                                <?php
                                if (!empty($screening_supervisor)) {
                                    foreach ($screening_supervisor as $key => $row) { ?>
                                        <option value="<?php echo $row->person_id; ?>"><?php echo $row->first_name . ' ' . $row->last_name; ?></option>
                                        <?php
                                    }?>
                                    <?php
                                } else { ?>
                                    <option value="Basic DBS">Basic DBS</option>
                                    <option value="Enhanced DBS">Enhanced DBS</option>
                                    <option value="BS7858">BS7858</option>
                                    <option value="NPCC Letter">NPCC Letter</option>
                                    <option value="Intruder">Intruder</option>
                                    <option value="Provisional Screening Certificate">Provisional Screening Certificate</option>
                                    <?php
                                } ?>
                            </select>
                        </div>
                    
                        <div class="input-group form-group">
                            <label class="input-group-addon">Reference Number</label>
                            <input type="text" name="ref_number" class="form-control" placeholder="Reference Number" value="" />
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="x_panel tile has-shadow fixed_height_340">
                        <div class="input-group form-group">
                            <label class="input-group-addon">Screening Provider</label>
                            <input type="text" name="screening_provider" class="form-control" placeholder="Screening Provider" value="" />
                        </div>

                        <div class="input-group form-group">
                            <label class="input-group-addon">Screening Type</label>
                            <select name="screening_type" class="form-control" required>
                                <option value="">Please select</option>
                                <?php
                                if (!empty($screening_type)) {
                                    foreach ($screening_type as $key => $row) { ?>
                                        <option value="<?php echo $row; ?>"><?php echo $row; ?></option>
                                        <?php
                                    }?>
                                    <?php
                                } else { ?>
                                    <option value="Basic DBS">Basic DBS</option>
                                    <option value="Enhanced DBS">Enhanced DBS</option>
                                    <option value="BS7858">BS7858</option>
                                    <option value="NPCC Letter">NPCC Letter</option>
                                    <option value="Intruder">Intruder</option>
                                    <option value="Provisional Screening Certificate">Provisional Screening Certificate</option>
                                    <?php
                                } ?>
                            </select>
                        </div>

                        <div class="input-group form-group">
                            <label class="input-group-addon">Screening Result</label>
                            <select name="screening_result" class="form-control" required>
                                <option value="">Please select</option>
                                <?php
                                if (!empty($screening_result)) {
                                    foreach ($screening_result as $key => $row) { ?>
                                        <option value="<?php echo $row; ?>"><?php echo $row; ?></option>
                                        <?php
                                    }?>
                                    <?php
                                } else { ?>
                                    <option value="Pending">Pending</option>
                                    <option value="Passed">Passed</option>
                                    <option value="Failed">Failed</option>
                                    <?php
                                } ?>
                            </select>
                        </div>

                        <div class="input-group form-group">
                            <!-- <label class="input-group-addon">Screening Note</label> -->
                            <textarea name="screening_note" class="form-control" rows="3" placeholder="Notes"></textarea>
                        </div>
                        
                        <div class="input-group form-group checkbox executive_acceptance_of_risk" style="display: none;">
                            <label class="input-group-addon">Executive Acceptance of Risk</label>
                            <label class="input-group checkbox_in_input">
                                <input name="executive_acceptance_of_risk" type="hidden" value="no">
                                <input name="executive_acceptance_of_risk" class="form-control" type="checkbox" value="yes">
                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                            </label>
                        </div>
                        
                        
                        
            <?php       if ($this->user->is_admin || !empty($permissions->can_add) || !empty($permissions->is_admin)) { ?>
                            <div class="row upd_button">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                                    <button id="create-screening-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next create-security-screening-btn" type="button">Create Log</button>
                                </div>
                            </div>
                        <?php
            } else { ?>
                            <div class="row col-lg-4 col-md-4 col-sm-4 col-xs-6 upd_button">
                                <button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled>No permissions</button>
                            </div>
                        <?php
            } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div id="viewLogDetails" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                        <h4 class="modal-title"><span style="font-weight: 400">Security Screening Log (<span class="screening_log_in_overview"></span>) for: <br /> </span><strong><?php echo $person_details->first_name . ' ' . $person_details->last_name; ?></strong> / <strong><?php echo $person_details->job_title; ?></strong></h4>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12" id="ajax_screening_log">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $( document ).ready( function(){
        $( '#ajax_screening_log' ).on( "click", '#deleteSecurityBtn', function( e ){
            e.preventDefault();

            var security_log_ID = $( "#deleteSecurityBtn" ).data( "security_log_id" );
            swal({
                title: 'Confirm Security Log delete?',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function( result ) {
                if ( result.value ) {
                    $.ajax({
                        url:"<?php echo base_url('webapp/people/delete_security_log/'); ?>",
                        method: "POST",
                        data: { security_log_ID : security_log_ID }, 
                        dataType: 'json',
                        success:function( data ){
                            if( data.status == 1 ){
                                swal({
                                    type: 'success',
                                    title: data.status_msg,
                                    showConfirmButton: false,
                                    timer: 3000
                                })
                            } else {
                                swal({
                                    type: 'error',
                                    title: data.status_msg
                                })
                            }
                            window.setTimeout( function(){
                                location.reload();
                            }, 600 );
                        }
                    });
                } else {
                    console.log( 'fail' );
                }
            }).catch( swal.noop )
        });
        
        $( "*[name='screening_result']" ).change( function( e ){
            e.preventDefault();
            if( $( this ).val() == "Failed" ){
                $( ".executive_acceptance_of_risk" ).show();
            } else {
                $( ".executive_acceptance_of_risk" ).hide();
            }               
        });
        
        $( "#ajax_screening_log" ).on( "change", "*[name='screening_result']", function( e ) {
            e.preventDefault();
            if( $( this ).val() == "Failed" ){
                $( "#ajax_screening_log .executive_acceptance_of_risk" ).show();
            } else {
                $( "#ajax_screening_log .executive_acceptance_of_risk" ).hide();
            }   
        })

        $( "#ajax_screening_log" ).on( "click", "#updateSecurityLog", function( e ){
            e.preventDefault();
            
            var formData = $( '#update_security_log' ).serialize();

            $.ajax({
                url:"<?php echo base_url('webapp/people/update_security_log/'); ?>",
                method: "POST",
                data: formData,
                dataType: 'json',
                success:function( data ){
                    if( data.status == 1 ){
                        swal({
                            type: 'success',
                            title: data.status_msg,
                            showConfirmButton: false,
                            timer: 3000
                        })
                    } else {
                        swal({
                            type: 'warning',
                            title: data.status_msg,
                            showConfirmButton: false,
                            timer: 3000
                        })
                    }
                    window.setTimeout( function(){
                        location.reload();
                    }, 3000 );
                }
            });
        });

        
        
        function load_log_details( log_id ){
            $.ajax({
                url:"<?php echo base_url('webapp/people/security_logs/'); ?>",
                method: "POST",
                data: {
                    'log_id': log_id,
                    'person_id' : <?php echo $person_details->person_id; ?>
                },
                dataType: 'json',
                success:function( result ){
                    if( result.status == '1' ){
                        $( "#ajax_screening_log" ).html( result.security_logs );
                        $( '.datetimepicker' ).datetimepicker({
                            formatDate: 'd/m/Y',
                            timepicker: false,
                            format: 'd/m/Y',
                        });
                        
                    } else {
                        swal({
                            type: 'warning',
                            title: data.status_msg,
                            showConfirmButton: false,
                            timer: 3000
                        })
                    }
                }
            });
        }
        
    
        $( '.viewLogDetailsLink' ).on( 'click', function(){
            var log_id = $( this ).data( "log_id" );
            $( ".screening_log_in_overview" ).html( log_id );
            load_log_details( log_id );
        });
        
        $( '.datetimepicker' ).datetimepicker({
            formatDate: 'd/m/Y',
            timepicker: false,
            format:'d/m/Y',
        });

    
        $( "#addNewlog" ).on( "click", function(){
            $( ".table_body" ).slideToggle( 1000 );
            $( ".create-security-log, .link_sites" ).slideToggle( 1000 );
            $( this ).children( '.fas' ).toggleClass( 'fa-chevron-down fa-chevron-up' );
        });
    
    
        $( "#create-screening-btn" ).on( "click", function( e ){
            e.preventDefault();

            var check_empty = false;
            var fields_to_check = ["valid_from", "expiry_date", "reminder_date", "sent_date", "sent_by", "ref_number", "screening_provider", "screening_type", "screening_result", "screening_note"];
            
            $( fields_to_check ).each( function(){
                var field = $( "*[name='" + this +"']" );
                if( $( field ).val() == "" ){
                    check_empty = true;
                    field.css( "border", "2px solid red" );
                }
            });

            if( check_empty == true ){
                alert( "Required fields cannot be empty" );
                return false;
            } else {

                var formData = $( '#create-security-log' ).serialize();
                swal({
                    title: 'Confirm New Security Log creation?',
                    showCancelButton: true,
                    confirmButtonColor: '#5CB85C',
                    cancelButtonColor: '#9D1919',
                    confirmButtonText: 'Yes'
                }).then( function( result ) {
                    if ( result.value ) {
                        $.ajax({
                            url:"<?php echo base_url('webapp/people/create_security_log/'); ?>",
                            method: "POST",
                            data: formData,
                            dataType: 'json',
                            success:function( data ){
                                if( data.status == 1 || data.status == true ){
                                    swal({
                                        type: 'success',
                                        title: data.status_msg,
                                        showConfirmButton: false,
                                        timer: 3000
                                    })
                                } else {
                                    swal({
                                        type: 'error',
                                        title: data.status_msg
                                    })
                                }

                                window.setTimeout( function(){
                                    location.reload();
                                }, 3000 );
                            }
                        });
                    } else {
                    }
                }).catch( swal.noop )
            }
        });
    });
</script>