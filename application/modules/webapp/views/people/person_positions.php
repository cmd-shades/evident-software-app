<style type="text/css">
.full_block{
    width: 100%;
    display: block;
    float: left;
}

.x_panel.tile.has-shadow.position-info{
    min-height: 305px;
}

.x_panel.tile.has-shadow.position-info.right_panel{
    padding-top: 20px;
}

.delete_div{
    position: absolute;
    bottom: 0px;
    left: 0;
}
</style>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel tile has-shadow">
            <div class="rows" style="margin-top: 0px; margin-bottom: 6px;">
                <a class="btn btn-sm btn-flow btn-success btn-next pull-right no_right_margin" id="addNewlog">Create Position Log &nbsp;<i class="fas fa-chevron-down"></i></a>
                <legend>Position History</legend>
            </div>
    <?php   if ($this->user->is_admin || !empty($permissions->can_view) || !empty($permissions->is_admin)) { ?>
            <div class="control-group form-group full_block">
                <table style="width:100%; table-layout: fixed;">
                    <tr>
                        <th class="department_name">Department</th>
                        <th class="job_title">Job Title</th>
                        <th class="position_type">Position Type</th>
                        <th class="business">Business</th>
                        <!-- <th class="location">Location</th> -->
                        <th class="line_manager_full_name">Line Manager</th>
                        <!-- <th class="position_notes">Note</th> -->
                        <th class="job_start_date">Start Date</th>
                        <th class="job_end_date">End Date</th>
                        <!-- <th class="15%">Added By</th> -->
                        <!-- <th class="date_created"><span class="pull-right">Created at</span></th> -->
                        <th>&nbsp;</th>
                    </tr>
                </table>
            </div>
            <div class="control-group form-group table_body full_block">
                <table style="width:100%; table-layout: fixed;">
            <?php   if (!empty($job_positions)) {
                foreach ($job_positions as $position) { ?>
                        <tr>
                            <td class="department_name pos_<?php echo $position->position_id; ?>"><?php echo $position->department_name; ?></td>
                            <td class="job_title"><?php echo $position->job_title; ?></td>
                            <td class="position_type"><?php echo $position->position_type; ?></td>
                            <td class="business"><?php echo $position->business; ?></td>
                                            <?php /* <td class="location"><?php echo $position->location; ?></td> */ ?>
                            <td class="line_manager_full_name"><?php echo (!empty($position->line_manager_full_name)) ? $position->line_manager_full_name : '' ; ?></td>
                                            <?php /* <td class="position_notes"><?php echo $position->position_notes; ?></td> */ ?>
                            <td class="job_start_date"><?php echo (validate_date($position->job_start_date)) ? format_date_client($position->job_start_date) : ''; ?></td>
                            <td class="job_end_date"><?php echo (validate_date($position->job_end_date)) ? format_date_client($position->job_end_date) : 'To present...'; ?></td>
                                            <?php /* <td class=""><?php echo $position->created_by; ?></td> */ ?>
                                            <?php /* <td class="date_created"><span class="pull-right"><?php echo date( 'd/m/Y H:i:s', strtotime( $position->date_created ) ); ?></span></td> */ ?>
                            <td width="10%" class="viewLogDetailsLink" data-target="#viewLogDetails" data-toggle="modal" data-position_id="<?php echo $position->position_id; ?>" ><a href="#">More details</a></td>
                        </tr>
                <?php       }
            } else { ?>
                        <tr>
                            <td colspan="5"><?php echo $this->config->item('no_records'); ?></td>
                        </tr>
            <?php       } ?>
                </table>
            </div>
    <?php       } ?>
        </div>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="row create-leaver-log" style="display: none;">
            <form id="update-person-position" class="form-horizontal">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <input type="hidden" name="page" value="position" />
                    <input type="hidden" name="user_id" value="<?php echo $person_details->user_id; ?>" />
                    <input type="hidden" name="person_id" value="<?php echo $person_details->person_id; ?>" />
                    <input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
                    <div class="x_panel tile has-shadow position-info">
                        <legend>Position details <span class="error_message pull-right" style="display: block; color:red; font-size:14px; verticle-align:bottom" id="position-data-errors"></span></legend>
                        <div class="input-group form-group">
                            <label class="input-group-addon">Department</label>
                            <select id="department_id" name="department_id" class="form-control required" data-field_name="Department" >
                                <option value="" >Please select</option>
                                <?php if (!empty($departments)) {
                                    foreach ($departments as $k => $department) { ?>
                                    <option value="<?php echo $department->department_id; ?>"><?php echo $department->department_name; ?></option>
                                    <?php }
                                    } ?>
                            </select>
                        </div>
                        <div class="input-group form-group">
                            <label class="input-group-addon" >Job Title</label>
                            <select id="job_title_id" name="job_title_id" class="form-control required" data-field_name="Job Title" >
                                <option value="">Please select</option>
                                <?php if (!empty($job_titles)) {
                                    foreach ($job_titles as $k => $job_title) { ?>
                                    <option value="<?php echo $job_title->job_title_id; ?>"><?php echo $job_title->job_title; ?></option>
                                    <?php }
                                    } ?>
                            </select>
                        </div>

                        <div class="input-group form-group">
                            <label class="input-group-addon">Position Type</label>
                            <select id="position_type" name="position[position_type]" class="form-control">
                                <option value="">Please select</option>
                                <option value="Fixed Term Contract Conditional">Fixed Term Contract Conditional</option>
                                <option value="Fixed Term Contract Confirmed">Fixed Term Contract Confirmed</option>
                                <option value="Permanent Conditional">Permanent Conditional</option>
                                <option value="Permanent Confirmed">Permanent Confirmed</option>
                                <option value="Temporary Conditional">Temporary Conditional</option>
                                <option value="Temporary Confirmed">Temporary Confirmed</option>
                            </select>
                        </div>
                        <div class="input-group form-group">
                            <label class="input-group-addon">Business</label>
                            <select name="position[business]" class="form-control">
                                <option value="">Please select</option>
                                <option value="TechLive">TechLive</option>
                                <option value="Subcontractor">Subcontractor</option>
                            </select>
                        </div>
                        <div class="input-group form-group">
                            <label class="input-group-addon">Location</label>
                                <select name="position[location]" class="form-control">
                                    <option value="">Please select</option>
                            <?php if (!empty($locations)) {
                                asort($locations);
                                foreach ($locations as $row) { ?>
                                        <option value="<?php echo $row; ?>"><?php echo $row; ?></option>
                                <?php   }
                            } else { ?>
                                    <option value="London">London</option>
                                    <option value="Manchester">Manchester</option>
                                    <option value="Newcastle">Newcastle</option>
                                    <option value="St. Ives">St. Ives</option>
                            <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="x_panel tile has-shadow position-info right_panel">
                        <div class="input-group form-group">
                            <label class="input-group-addon">Line Manager</label>
                            <select id="position_type" name="position[line_manager_id]" class="form-control">
                                <option value="">Please select</option>
                                <?php if (!empty($line_managers)) {
                                    foreach ($line_managers as $row) { ?>
                                        <option value="<?php echo $row->person_id; ?>"><?php echo $row->first_name . ' ' . $row->last_name; ?></option>
                                    <?php }
                                    } ?>
                            </select>
                        </div>

                        <div class="input-group form-group">
                            <label class="input-group-addon">Position Start date</label>
                            <input name="position[job_start_date]" class="form-control datepicker" type="text" placeholder="<?php echo date('d/m/Y'); ?>" value="" />
                        </div>

                        <div class="position-end-date" style="display:none">
                            <div class="input-group form-group">
                                <label class="input-group-addon">Position End date</label>
                                <input id="position-end-date" name="position[job_end_date]" class="form-control datepicker" type="text" data-field_name="Position End Date" placeholder="End date" value="" />
                            </div>
                        </div>

                        <div class="form-group">
                            <textarea name="position[position_notes]" class="form-control" type="text" value="" style="width:100%;" placeholder="Position notes"></textarea>
                        </div>

                        <div class="row fixed_row">
            <?php       if ($this->user->is_admin || !empty($permissions->can_add) || !empty($permissions->is_admin)) { ?>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <button id="create-position-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="button">Create Position Log</button>
                            </div>
            <?php       } else { ?>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <button class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >No permissions</button>
                            </div>
            <?php       } ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Modal -->
<div id="viewLogDetails" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <div class="col-md-10">
                        <h4 class="modal-title"><span style="font-weight: 400;">Details of Position (ID <span class="position_id_in_overview"></span>): <span class="position_title_in_overview"></span> <br />
                        For </span><?php echo $person_details->first_name . ' ' . $person_details->last_name; ?> / <?php echo $person_details->job_title; ?></h4>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12" id="ajax_positions">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        
        $( '#ajax_positions' ).on( "click", '#deletePositionBtn', function( e ){
            e.preventDefault();

            var positionID = $( "#deletePositionBtn" ).data( "position_id" );
            swal({
                title: 'Confirm Position delete?',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function( result ) {
                if ( result.value ) {
                    $.ajax({
                        url:"<?php echo base_url('webapp/people/delete_position/'); ?>",
                        method: "POST",
                        data: { position_id : positionID }, 
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
        
        $( '#ajax_positions' ).on( "click", '#updatePositionBtn', function( e ){
            e.preventDefault();
            var formData = $( '#position_update_in_modal' ).serialize();
            swal({
                title: 'Confirm Position update?',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function( result ) {
                if ( result.value ) {
                    $.ajax({
                        url:"<?php echo base_url('webapp/people/update_position/'); ?>",
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
        
        
        function load_position_details( position_id ){
            $.ajax({
                url:"<?php echo base_url('webapp/people/get_position_details/'); ?>",
                method: "POST",
                data: {
                    'account_id': <?php echo $this->user->account_id; ?>,
                    'position_id': position_id,
                },
                dataType: 'json',
                success:function( data ){
                    if( ( data.status == '1' ) || ( data.status == true ) ){
                        $( "#ajax_positions" ).html( data.position_details );
                        $( '.datetimepicker' ).datetimepicker({
                            formatDate: 'd/m/Y',
                            timepicker: false,
                            format: 'd/m/Y',
                        });
                    } else {

                    }
                }
            });
        }
        
        
        $( '.viewLogDetailsLink' ).on( 'click', function( e ){
            var position_id = $( this ).data( "position_id" );
            $( ".position_id_in_overview" ).html( position_id );
            load_position_details( position_id );
        });

    
        $( "#addNewlog" ).on( "click", function(){
            $( ".table_body" ).slideToggle( 1000 );
            $( ".create-leaver-log" ).slideToggle( 1000 );
            $( this ).children( '.fas' ).toggleClass( 'fa-chevron-down fa-chevron-up' );
        });
    
        $( '#position_type' ).change( function(){
            var posType = $('option:selected', this).val();
            var end_date_required_fields = ['Fixed Term Contract Conditional', 'Temporary Conditional', 'Temporary Confirmed', 'Fixed Term Contract Confirmed'];

            if( ( posType.length > 0 ) && ( jQuery.inArray( posType, end_date_required_fields ) > -1 ) ){
                $( '.position-end-date' ).slideDown();
                $( '#position-end-date' ).addClass( 'required' );
            } else {
                $( '.position-end-date' ).slideUp();
                $( '#position-end-date' ).removeClass( 'required' );
                $( '#position-end-date' ).val( '' );
                $( '#position-data-errors' ).text( '' );
            }
        });

        //** Validate any inputs that have the required class, if empty return the name attribute **/
        function check_inputs( containerClass ){

            var result = false;
            var panel  = "." + containerClass;

            $( $( panel + " .required" ).get().reverse() ).each( function(){
                var fieldName   = '';
                var inputValue  = $( this ).val();
                if( ( inputValue == false ) || ( inputValue == '' ) || ( inputValue.length == 0 ) ){
                    fieldName   = $(this).attr( 'name' );
                    fieldName2  = $(this).data( 'field_name' );
                    result      = fieldName;
                    return result;
                }
            });
            return result;
        }

        //Submit form for processing
        $( '#create-position-btn' ).click( function( event ){

            var inputs_state = check_inputs( 'position-info' );

            if( inputs_state ){
                //If name attribute returned, auto focus to the field and display arror message
                $( '[name="'+inputs_state+'"]' ).focus();
                var labelText = $( '[name="'+inputs_state+'"]' ).parent().find('label').text();
                $( '#position-data-errors' ).text( ucwords( labelText ) +' is a required' );
                return false;
            }

            event.preventDefault();
            var formData = $(this).closest('form').serialize();
            swal({
                title: 'Add new position info?',
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
    });
</script>