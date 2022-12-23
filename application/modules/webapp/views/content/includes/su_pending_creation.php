<style>
    .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
        padding: 8px;
        line-height: 1.42857143;
        vertical-align: top;
        border: none;
        border-bottom: 1px solid #ddd;
    }
    .x_panel {
        margin-top: 10px;
    }

    .table .table {
        background:transparent;
    }

    .cacti_id{
        width: 8%;
    }

    .reference{
        width: 21%;
    }

    .name{
        width: 21%;
    }

    .easel_id{
        width: 21%;
    }

    .state{
        width: 21%;
    }

    .border_bottom{
        border-bottom: 1px solid #fff;
    }

    .check-box{
        width: 8%;
    }

    .blue{
        color: #3498DB;
    }

    .red{
        /* color: #b7001f; */
        color: red;
    }

    .green{
        color: #256f2b;
    }

    .black{
        color: #000;
    }
</style>


<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel tile has-shadow">
            <legend>Review Content Upload</legend>
            <div class="row">
                <div class="col-lg-6">
                    <h5>This view is showing only items which were found in CaCTI DB based on the Asset Code</h5>
                    <br />
                    <h5>Colour code for the incoming data (CSV upload):</h5>
                    <ul>
                        <li><span class="green">green</span> - existing Easel ID (no incoming Easel ID)</li>
                        <li><span class="blue">blue</span> - no existing Easel ID (fresh incoming Easel ID)</li>
                        <li><span class="red">red</span> - conflict between Existing Easel ID AND incoming Easel ID</li>
                        <li><span class="black">black</span> - Existing Easel ID AND incoming Easel ID identical</li>
                    </ul>
                </div>
            </div>


            <?php
            if (!empty($pending)) { ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive" style="overflow-y: hidden;" >
                            <?php
                            foreach ($pending as $group => $records) { ?>
                                <form id="frm-<?php echo $group;?>" action="<?php

                                // echo base_url( 'webapp/content/add_clearance_territories/'.$this->user->account_id );

                                ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
                                    <table class="table table-responsive display" style="margin-top: 20px; margin-bottom: 20px;">
                                        <tbody>
                                            <tr>
                                                <td colspan="6" style="color:<?php echo ($group == 'new-records') ? 'red' : 'green'; ?>" title="These records <?php echo ($group == 'new-records') ? 'are ready for processing into Content records' : 'already exist on the system'; ?>" >
                                                    <span class="pointer grp" data-grp_id="<?php echo $group;?>"><?php echo ucwords(str_replace('-', ' ', $group)); ?> ( <?php echo (!empty($records)) ? count($records) : '0'; ?> )</span>
                                                </td>
                                            </tr>
                                            <tr class="grp_<?php echo $group;?>" style="display:none">
                                                <td colspan="6" >
                                                    <table class="table table-responsive" style="width:100%" >
                                                        <thead>
                                                            <tr>
                                                                <th class="cacti_id">Content ID</th>
                                                                <th class="reference">Reference</th>
                                                                <th class="name">Name (Title)</th>
                                                                <th class="easel_id">Easel ID</th>
                                                                <th class="state el-hidden">AT State</th>
                                                                <th class="check-box">
                                                                    <div class="checkbox pull-right" >
                                                                        <label ><strong>Tick all</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="chk-all chk<?php echo $group;?>" data-chk_id="<?php echo $group;?>" type="checkbox" value=""></label>
                                                                    </div>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
/*
['upload'] vs ['cacti']
{
    [upload_id] => 1
    [id] => d90fb0ad-5cdd-4ad1-8da3-5fb2487222da
    [reference] => theoryofeverything
    [type] => generic
    [name] => The Theory of Everything
    [state] => published
    [description] => A look at the relationship between the famous physicist Stephen Hawking and his wife.
    [duration] => P0DT2H3M0.000S
    [trailer] => d90fb0ad-5cdd-4ad1-8da3-5fb2487222da-t
    [feature] => d90fb0ad-5cdd-4ad1-8da3-5fb2487222da-f
} */

                                                            foreach ($records as $key => $record) {
                                                                $ready_for_processing   = true;
                                                                $disabled_tick          = false;
                                                                // colour code:
                                                                // green    - existing Easel ID, no incoming Easel ID
                                                                // blue - no existing Easel ID, fresh incoming Easel ID
                                                                // red  - conflict between Existing Easel ID AND incoming Easel ID
                                                                // black - Existing Easel ID AND incoming Easel ID identical?>

                                                                <tr data-reference="<?php echo $record['upload']['reference']; ?>" >
                                                                    <td class="cacti_id border_bottom">
                                                                        <a href="<?php echo base_url('webapp/content/profile/' . $record['cacti']['cacti_id']); ?>" target="_blank"><?php echo (!empty($record['cacti']['cacti_id'])) ? $record['cacti']['cacti_id'] : '' ;?></a>
                                                                    </td>
                                                                    <td class="reference border_bottom"><?php echo (!empty($record['upload']['reference'])) ? $record['upload']['reference'] : '' ;?></td>

                                                                    <td class="name border_bottom">
                                                                        <?php
                                                                        if (!empty($record['cacti']['cacti_name'])) {
                                                                            if (!empty($record['upload']['name'])) {
                                                                                ## is there a conflict?
                                                                                if (strtolower($record['cacti']['cacti_name']) == strtolower($record['upload']['name'])) {
                                                                                    echo '<span class="black">' . $record['cacti']['cacti_name'] . '</span>';
                                                                                } else {
                                                                                    $ready_for_processing   = false;
                                                                                    $disabled_tick          = true;
                                                                                    echo '<span class="red">CONFLICT:</span><span><br />Exist - ' . $record['cacti']['cacti_name'] . ' <br />Incom - ' . $record['upload']['name'] . '</span>';
                                                                                }
                                                                            } else {
                                                                                echo '<span class="green">' . $record['cacti']['cacti_name'] . '</span>';
                                                                            }
                                                                        } else {
                                                                            if (!empty($record['upload']['name'])) {
                                                                                echo '<span class="blue">' . $record['upload']['name'] . '</span>';
                                                                            } else {
                                                                                echo '<span class="">&nbsp;</span>';
                                                                            }
                                                                        } ?>
                                                                    </td>

                                                                    <td class="easel_id border_bottom">
                                                                        <?php
                                                                        if (!empty($record['cacti']['existing_easel_id'])) {
                                                                            if (!empty($record['upload']['id'])) {
                                                                                ## is there a conflict?
                                                                                if (strtolower($record['cacti']['existing_easel_id']) == strtolower($record['upload']['id'])) {
                                                                                    echo '<span class="black">' . $record['cacti']['existing_easel_id'] . '</span>';
                                                                                    $ready_for_processing   = false;
                                                                                } else {
                                                                                    $ready_for_processing = false;
                                                                                    echo '<span class="red">CONFLICT:</span><span><br />Exist - ' . $record['cacti']['existing_easel_id'] . '<br />Incom - ' . $record['upload']['id'] . '</span>';
                                                                                }
                                                                            } else {
                                                                                echo '<span class="green">' . $record['cacti']['existing_easel_id'] . '</span>';
                                                                            }
                                                                        } else {
                                                                            if (!empty($record['upload']['id'])) {
                                                                                echo '<span class="blue">' . $record['upload']['id'] . '</span>';
                                                                            } else {
                                                                                echo '<span class="">&nbsp;</span>';
                                                                            }
                                                                        } ?>
                                                                    </td>

                                                                    <td class="state border_bottom el-hidden">
                                                                        <?php
                                                                        if (!empty($record['cacti']['cacti_airtime_state'])) {
                                                                            if (!empty($record['upload']['state'])) {
                                                                                ## is there a conflict?
                                                                                if (strtolower($record['cacti']['cacti_airtime_state']) == strtolower($record['upload']['state'])) {
                                                                                    echo '<span class="black">' . $record['cacti']['cacti_airtime_state'] . '</span>';
                                                                                } else {
                                                                                    // $ready_for_processing = false;
                                                                                    echo '<span class="red">CONFLICT:</span><span><br />Exist - ' . $record['cacti']['cacti_airtime_state'] . '<br />Incom - ' . $record['upload']['state'] . '</span>';
                                                                                }
                                                                            } else {
                                                                                echo '<span class="green">' . $record['cacti']['cacti_airtime_state'] . '</span>';
                                                                            }
                                                                        } else {
                                                                            if (!empty($record['upload']['state'])) {
                                                                                echo '<span class="blue">' . $record['upload']['state'] . '</span>';
                                                                            } else {
                                                                                echo '<span class="">&nbsp;</span>';
                                                                            }
                                                                        } ?>
                                                                    </td>
                                                                    <td class="check-box border_bottom">
                                                                        <div class="checkbox pull-right" >
                                                                            <label>
                                                                                <input type="checkbox" name="upload_id[]" value="<?php echo $record['upload']['upload_id']; ?>" class="chk<?php echo (!empty($group)) ? $group : '' ;?>" <?php echo ($disabled_tick != false) ? 'disabled = "disabled"' : (($ready_for_processing != false) ? 'checked = "checked"' : '') ; ?> />
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                            } ?>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" style="color:<?php echo ($group == 'new-records') ? 'green' : 'red'; ?>" title="These records <?php echo ($group == 'new-records') ? 'are ready for processing into Content records' : 'already exist on the system'; ?>" >
                                                    <span class="pull-right">
                                                        <button style="display:none" class="submit-btn btn btn-default btn-<?php echo ($group == 'new-records') ? 'danger' : 'success' ; ?> grp_<?php echo $group;?>" data-action_type="<?php echo ($group == 'new-records') ? 'unknown' : 'process'; ?>" data-form_id="<?php echo $group; ?>" >
                                                            <?php echo ($group == 'new-records') ? 'Remove Records from the Upload' : 'Submit Selected Records' ; ?>
                                                        </button>
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </form>
                                <?php
                            } ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-offset-9 col-lg-3 col-md-offset-9 col-md-3 col-sm-6 col-xs-12 ">
                        <a href="<?php echo base_url('webapp/content/su_content_upload'); ?>" class="btn btn-sm btn-block btn-danger" type="button">Go back and re-upload file</a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 hide">
                        <button id="doc-upload-btn" data-form_id="<?php echo $group; ?>" data-action_type="<?php echo ($group == 'new-records') ? 'add' : 'remove'; ?>" class="btn btn-sm btn-block submit-btn btn-success" type="submit">Create Clearance Records</button>
                    </div>
                </div>
                <br/>
                <?php
            } else { ?>
                <div class="row">
                    <div class="col-md-12">
                        <span><?php echo $this->config->item('no_records');  ?></span>
                        <br/>
                        <br/>
                    </div>
                    <div class="col-md-3">
                        <a href="<?php echo base_url('webapp/content/su_content_upload'); ?>" class="btn btn-sm btn-block btn-info" type="submit">Start Upload</a>
                    </div>
                </div>
                <?php
            } ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){

        //Submit
        $( '#doc-upload-btn' ).click(  function( e ){
            e.preventDefault();

            swal({
                title: 'Confirm document upload?',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if ( result.value ) {
                    $( '#docs-upload-form' ).submit();
                }
            }).catch(swal.noop)

        });

        //Validate file type on upload
        $( '#upload-docs-form' ).submit(function( e ){
            var selection = document.getElementById('uploadfile');
            if( selection.files.length == 0 ){
                swal({
                    type: 'error',
                    text: 'No file selected!'
                });
                return false;
            }

            for (var i=0; i < selection.files.length; i++) {
                var filename = selection.files[i].name;
                var ext = filename.substr(filename.lastIndexOf('.') + 1);
                if( ext!== "csv" && ext!== "xls" && ext!== "xlsx" && ext!== "pdf" && ext!== "jpg" && ext!== "jpeg" && ext!== "png" && ext!== "doc" && ext!== "docx" ) {
                    swal({
                        type: 'error',
                        text: 'You have selected an INVALID file type: .' +ext
                    })
                    return false;
                }
            }
        });


        $( '.grp' ).click( function(){
            var grpId = $( this ).data( 'grp_id' );
            $( '.grp_' + grpId ).slideToggle();
        });

        //Check all selected inputs
        $( '.chk-all' ).click( function(){
            var chkId = $( this ).data( 'chk_id' );
            if( this.checked ){
                $( '.chk'+chkId ).each( function(){
                    if( $( this ).prop( "disabled" ) != true ){
                        this.checked = true;
                    }
                });
            } else {
                $( '.chk'+chkId ).each( function(){
                    if( $( this ).prop( "disabled" ) != true ){
                        this.checked = false;
                    }
                });
            }
        });


        //Submit checked records
        $( '.submit-btn' ).click( function( e ){
            e.preventDefault();
            var formId     = $( this ).data( 'form_id' );
            var actionType = $( this ).data( 'action_type' );

            if( actionType == 'process' ){
                var postUrl = "<?php echo base_url('webapp/content/su_update_movies/'); ?>";
            } else {
                var postUrl = false;
            }

            var totalChkd = 0;
            $( '.chk' + formId ).each( function(){
                if( this.checked ) {
                    totalChkd++;
                }
            } );

            //Tick at least 1 checkbox
            if( totalChkd == 0 ){
                swal({
                    type: 'error',
                    title: '<small>Please select at least 1 record to '+ actionType + '</small>'
                })
                return false;
            }

            var formData = $( '#frm-'+formId ).serialize();

            swal({
                title: 'Confirm processing selected records?',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function( result ){
                if( result.value ){
                    $.ajax({
                        url: postUrl,
                        method: "POST",
                        data: formData,
                        dataType: 'json',
                        success: function( data ){
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
                                // if( data.all_done == 1 ){
                                    // location.href = "<?php echo base_url('webapp/content/su_update_movies/'); ?>";
                                // } else {
                                // }
                            }, 3000 );
                        }
                    });
                }
            }).catch( swal.noop )
        });
    });
</script>