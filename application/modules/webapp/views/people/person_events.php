<style type="text/css">
#ajax_events .event_title_in_overview{
    font-style: italic;
}

#ajax_events .input_row{
    width: 100%;
    padding: 2px 5px;
    border-radius: 5px;
    border-width: 1px;
}

#event_update_in_modal .input_title{
    width: 128px;
}

.margin_top_8{
    margin-top: 8px;
}

.margin_top_10{
    margin-top: 10px;
}

#event_update_in_modal td{
    vertical-align: top;
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
                <a class="btn btn-sm btn-flow btn-success btn-next pull-right no_right_margin" id="addNewlog">Create New Events &nbsp;<i class="fas fa-chevron-down"></i></a>
                <legend>Existing Events</legend>
            </div>
<?php       if ($this->user->is_admin || !empty($permissions->can_view) || !empty($permissions->is_admin)) { ?>
            <div class="control-group form-group full_block">
                <table style="width:100%; table-layout: fixed;">
                    <tr>
                        <th width="10%">Event Date</th>
                        <th width="10%">Event Category</th>
                        <th width="15%">Event Type</th>
                        <th width="15%">Event Title</th>
                        <th width="10%">Event Status</th>
                        <th width="15%">Event Supervisor</th>
                        <th width="10%">Event Review Date</th>
                        <th width="10%">&nbsp;</th>
                    </tr>
                </table>
            </div>
            <div class="control-group form-group table_body full_block">
                <table style="width:100%; table-layout: fixed;">
                    <?php if (!empty($events)) {
                        foreach ($events as $row) { ?>
                        <tr>
                            <td width="10%"><?php echo (validate_date($row->event_date)) ? format_date_client($row->event_date) : ''; ?></td>
                            <td width="10%"><?php echo $row->category_name; ?></td>
                            <td width="15%"><?php echo $row->event_type_name; ?></td>
                            <td width="15%"><?php echo $row->event_title; ?></td>
                            <td width="10%"><?php echo $row->event_status; ?></td>
                            <td width="15%"><?php echo $row->event_supervisor_fullname; ?></td>
                            <td width="10%"><?php echo (validate_date($row->event_review_date)) ? format_date_client($row->event_review_date) : ''; ?></td>
                            <td width="10%" class="viewLogDetailsLink" data-target="#viewLogDetails" data-toggle="modal" data-event_id="<?php echo $row->event_id; ?>" ><a href="#">More details</a></td>
                        </tr>
                        <?php }
                        } else { ?>
                        <tr>
                            <td colspan="5"><?php echo $this->config->item('no_records'); ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
<?php } ?>
        </div>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-12 create_new_events" style="display: none;">
        <div class="x_panel tile has-shadow">
            <legend>Create New Events</legend>
            <form id="create-event-form">
                <input type="hidden" name="page" value="events" />
                <input type="hidden" name="person_id" value="<?php echo $person_details->person_id; ?>" />
                <input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />


                <div class="input-group form-group">
                    <label class="input-group-addon">Event Category</label>
                    <select name="event_category_id" class="form-control" required>
                        <option value="">Please select</option>
                        <?php
                            if (!empty($event_categories)) {
                                foreach ($event_categories as $row) { ?>
                                <option value="<?php echo $row->category_id; ?>"><?php echo $row->category_name; ?></option>
                                <?php
                                }
                            } else { ?>
                            <option value="">Please, add categories</option>
                            <?php
                            } ?>
                    </select>
                </div>

                <div class="event_type_container input-group form-group" style="display: inline-table;display: none;">
                    <label class="input-group-addon">Event Type</label>
                    <select name="event_type_id" class="event_type_ids form-control">
                        <option>Please select</option>
                        <?php
                            if (!empty($event_types)) {
                                foreach ($event_types as $row) { ?>
                                <option value="<?php echo $row->event_type_id; ?>"><?php echo $row->event_type; ?></option>
                                <?php
                                }
                            } else { ?>
                            <option>Please, add types</option>
                            <?php
                            } ?>
                    </select>
                </div>

                <div class="event_title_container input-group form-group">
                    <label class="input-group-addon">Event Title</label>
                    <input name="event_title" class="form-control" type="text" placeholder="Event Title" value="" />
                </div>

                <div class="input-group form-group">
                    <label class="input-group-addon">Event Supervisor</label>
                    <select name="event_supervisor_id" class="form-control" required>
                        <option>Please select</option>
                        <?php
                            if (!empty($event_supervisor)) {
                                foreach ($event_supervisor as $row) { ?>
                                <option value="<?php echo $row->person_id; ?>" <?php echo ((!empty($person_event->event_supervisor_id)) && ($person_event->event_supervisor_id == $row->person_id)) ? "selected='selected'" : '' ; ?> ><?php echo $row->first_name . ' ' . $row->last_name; ?></option>
                                <?php
                                }
                            } else { ?>
                            <option>Please, add supervisors</option>
                            <?php
                            } ?>
                    </select>
                </div>

                <div class="input-group form-group">
                    <label class="input-group-addon">Event Date</label>
                    <input name="event_date" value="" class="form-control datetimepicker" placeholder="<?php echo date('d/m/Y'); ?>" data-date-format="DD/MM/Y" />
                </div>

                <div class="input-group form-group">
                    <label class="input-group-addon">Event Review Date</label>
                    <input name="event_review_date" value="" class="form-control datetimepicker" placeholder="<?php echo date('d/m/Y'); ?>" data-date-format="DD/MM/Y" />
                </div>

                <div class="input-group form-group">
                    <label class="input-group-addon">Event Notes</label>
                    <textarea name="event_note" class="form-control" rows="3"></textarea>
                </div>

    <?php       if ($this->user->is_admin || !empty($permissions->can_add) || !empty($permissions->is_admin)) { ?>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <button id="create-event-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="button">Create Event</button>
                        </div>
                    </div>
    <?php	  } else { ?>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled>No permissions</button>
                    </div>
    <?php	  } ?>
            </form>
        </div>
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
                        <h4 class="modal-title"><span style="font-weight: 400;">Details of Event (ID <span class="event_id_in_overview"></span>): <span class="event_title_in_overview"></span> <br />
                        For </span><?php echo $person_details->first_name . ' ' . $person_details->last_name; ?> / <?php echo $person_details->job_title; ?></h4>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12" id="ajax_events">

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
        
        $( '#ajax_events' ).on( "click", '#deleteEventBtn', function( e ){
            e.preventDefault();

            var event_ID = $( "#deleteEventBtn" ).data( "event_id" );

            swal({
                title: 'Confirm Event delete?',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function( result ) {
                if ( result.value ) {
                    $.ajax({
                        url:"<?php echo base_url('webapp/people/delete_event/'); ?>",
                        method: "POST",
                        data: { event_id : event_ID }, 
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
        
        $( "#addNewlog" ).on( "click", function(){
            $( ".table_body" ).slideToggle( 1000 );
            $( ".create_new_events" ).slideToggle( 1000 );
            $( this ).children( '.fas' ).toggleClass( 'fa-chevron-down fa-chevron-up' );
        });

        $( "*[name='event_category_id']" ).change( function(){
            var cat_id = $( this ).val();
            get_event_type_by_cat_id( cat_id );
        });
        
        $( "#ajax_events" ).on( "change", "*[name='event_category_id']", function(){
            var cat_id = $( this ).val();
            get_event_type_by_cat_id( cat_id )
        });
        
        
        function get_event_type_by_cat_id( cat_id ){
            if( ( cat_id == 8 ) || ( cat_id == 7 ) ){
                $( ".event_type_container" ).css( "display", "inline-table" );
                $( ".event_title_container" ).css( "display", "none" );

                var event_category_id = cat_id;

                $.ajax({
                    url:"<?php echo base_url('webapp/people/get_event_type_by_cat_id/'); ?>" + event_category_id,
                    method: "POST",
                    dataType: 'JSON',
                    success: function( data ){
                        if( data.status == 1 ){
                            $( ".event_type_ids" ).find( 'option' ).not( ':first' ).remove();
                            $( "#ajax_events .event_type_ids" ).find( 'option' ).not( ':first' ).remove();
                            $( ".event_type_ids" ).append( '<option value="">Please select</option>' );
                            $.each( data.event_types, function( key, value ){
                                $( ".event_type_ids" ).append( '<option value=' + value.event_type_id + '>' + value.event_type_name + '</option>' );
                            });
                        } else {
                            swal({
                                type: 'error',
                                title: data.status_msg
                            })
                        }
                    }
                });
            
            } else {
                $( ".event_type_container" ).css( "display", "none" );
                $( ".event_title_container" ).css( "display", "inline-table" );
            }
        }


        $( '#ajax_events' ).on( "click", '#updateEventBtn', function( e ){
            e.preventDefault();
            
            if( ( $( '*[name="event_category_id"]' ).val() == "" ) && $( '#event_update_in_modal *[name="event_category_id"]' ).val() == "" ){
                alert( "The Event Category field cannot be empty" );
                $( '*[name="event_category_id"], #event_update_in_modal *[name="event_category_id"]' ).css( "border", "2px solid red" );
                return false;
            }
            
            var formData = $( '#event_update_in_modal' ).serialize();
            swal({
                title: 'Confirm Event update?',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function( result ) {
                if ( result.value ) {
                    $.ajax({
                        url:"<?php echo base_url('webapp/people/update_event/'); ?>",
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

        function load_event_details( event_id ){
            $.ajax({
                url:"<?php echo base_url('webapp/people/get_event_details/'); ?>",
                method: "POST",
                data: {
                    'account_id': <?php echo $this->user->account_id; ?>,
                    'event_id': event_id,
                },
                dataType: 'json',
                success:function( data ){
                    if( ( data.status == '1' ) || ( data.status == true ) ){
                        $( "#ajax_events" ).html( data.event_details );
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
            var event_id = $( this ).data( "event_id" );
            $( ".event_id_in_overview" ).html( event_id );
            load_event_details( event_id );
        });


        //Submit form for processing
        $( '#create-event-btn' ).click( function( e ){
            e.preventDefault();
            var formData = $( '#create-event-form' ).serialize();
            swal({
                title: 'Confirm New Event creation?',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function( result ) {
                if ( result.value ) {
                    $.ajax({
                        url:"<?php echo base_url('webapp/people/create_event/'); ?>",
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

                                window.setTimeout( function(){
                                    location.reload();
                                },500 );
                            }else{
                                swal({
                                    type: 'error',
                                    title: data.status_msg
                                })
                            }
                        }
                    });
                } else {
                    console.log( 'fail' );
                }
            }).catch( swal.noop )
        });

        $( '.datetimepicker' ).datetimepicker({
            formatDate: 'd/m/Y',
            timepicker: false,
            format: 'd/m/Y',
        });
    });
</script>