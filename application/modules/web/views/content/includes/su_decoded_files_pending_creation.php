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

    .content_id{
        width: 10%;
    }

    .border_bottom{
        border-bottom: 1px solid #fff;
    }

    .check-box{
        width: 8%;
    }

    .clearance_date{
        width: 15%;
    }

</style>


<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel tile has-shadow">
            <legend>Review Content Upload</legend>
            <?php
            if (!empty($pending)) { ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive" style="overflow-y: hidden;" >
                            <?php
                            foreach ($pending as $group => $records) { ?>
                                <?php /* <form id="frm-<?php echo $group;?>" action="<?php echo base_url( 'webapp/content/su_process_decoded_files/'.$this->user->account_id ); ?>" method="post" class="form-horizontal" enctype="multipart/form-data" > */ ?>
                                <form id="frm-<?php echo $group; ?>" class="form-horizontal" enctype="multipart/form-data" >
                                    <input type="hidden" name="decoded_action" value="<?php echo ($group == 'new-records') ? 'add' : 'update'; ?>" />
                                    <table class="table table-responsive display" style="margin-top: 20px; margin-bottom: 20px;">
                                        <tbody>
                                            <tr>
                                                <td colspan="6" style="color:<?php echo ($group == 'new-records') ? 'green' : 'red'; ?>" title="These records <?php echo ($group == 'new-records') ? 'are ready for processing into Content records' : 'already exist on the system'; ?>" >
                                                    <span class="pointer grp" data-grp_id="<?php echo $group;?>"><?php echo ucwords(str_replace('-', ' ', $group)); ?> ( <?php echo (!empty($records)) ? count($records) : '0'; ?> )</span>
                                                </td>
                                            </tr>
                                            <tr class="grp_<?php echo $group;?>" style="display:none">
                                                <td colspan="6" >
                                                    <table class="table table-responsive" style="width:100%" >
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 3%;" width="3%">(File ID) Content ID</th>
                                                                <th style="width: 20%;" width="14%">File Name</th>
                                                                <th style="width: 5%;" width="5%">File type</th>
                                                                <th style="width: 20%;" width="20%">AT Reference</th>
                                                                <th style="width: 20%;" width="20%">AT Product Reference</th>
                                                                <th style="width: 24%;" width="24%">AT State</th>
                                                                <th width="4%">
                                                                    <div class="checkbox pull-right" >
                                                                        <label ><strong>Tick all</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="chk-all chk<?php echo $group;?>" data-chk_id="<?php echo $group;?>" type="checkbox" value=""></label>
                                                                    </div>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                                    
                                                        <tbody>
                                                            <?php
                                                            foreach ($records as $key => $record) { ?>
                                                                <tr data-tmp_upload_id="<?php echo $key; ?>" >
                                                                    <td class="content_id border_bottom">
                                                                        <?php
                                                                        $ready_for_action_content_id = true;
                                                                if (!empty($record['cacti']['existing_content_id'])) {
                                                                    if ($record['upload']['content_id'] != $record['cacti']['existing_content_id']) {
                                                                        echo "<span class=\"red\"> (" . $record['upload']['file_id'] . ") ISSUE: CSV-" . $record['upload']['content_id'] . '|CACTI-' . $record['cacti']['existing_content_id'] . "</span>";
                                                                        $ready_for_action_content_id = false;
                                                                    } else {
                                                                        echo "<span class=\"green\"> (" . $record['upload']['file_id'] . ") CSV-" . $record['upload']['content_id'] . '|CACTI-' . $record['cacti']['existing_content_id'] . "</span>";
                                                                    }
                                                                } else {
                                                                    echo "<span class=\"blue\"> INC: (" . $record['upload']['file_id'] . ") " . $record['upload']['content_id'] . "</span>";
                                                                } ?>
                                                                    </td>
                                                                    <td class="clearance_date border_bottom">
                                                                        <?php
                                                                $ready_for_action_file_name = true;
                                                                if (!empty($record['cacti']['existing_content_id'])) {
                                                                    if ($record['upload']['file_new_name'] != $record['cacti']['existing_file_new_name']) {
                                                                        echo "<span class=\"red\" title=\"\">ISSUE: CSV-" . $record['upload']['file_new_name'] . '|CACTI-' . $record['cacti']['existing_file_new_name'] . "</span>";
                                                                        $ready_for_action_file_name = false;
                                                                    } else {
                                                                        echo "<span class=\"green\" title=\"" . $record['upload']['file_new_name'] . "\">" . (substr($record['upload']['file_new_name'], 0, 20)) . " (...)</span>";
                                                                    }
                                                                } else {
                                                                    echo "<span class=\"blue\" title=\"" . $record['upload']['file_new_name'] . "\">INC: " . (substr($record['upload']['file_new_name'], 0, 20)) . "  (...)</span>";
                                                                } ?>
                                                                    </td>
                                                                    <td class="territory_name border_bottom">
                                                                        <?php
                                                                $ready_for_action_file_type = true;
                                                                if (!empty($record['cacti']['existing_content_id'])) {
                                                                    if (strtolower($record['upload']['file_type']) != strtolower($record['cacti']['existing_file_type'])) {
                                                                        echo "<span class=\"red\">ISSUE: CSV-" . $record['upload']['file_type'] . '|CACTI-' . $record['cacti']['existing_file_type'] . "</span>";
                                                                        $ready_for_action_file_type = false;
                                                                    } else {
                                                                        echo "<span class=\"green\">" . $record['upload']['file_type'] . "</span>";
                                                                    }
                                                                } else {
                                                                    echo "<span class=\"blue\">" . $record['upload']['file_type'] . "</span>";
                                                                } ?>
                                                                    </td>
                                                                    <td class="territory_name border_bottom">
                                                                        <?php
                                                                $ready_for_action_airtime_reference = true;
                                                                if (!empty($record['cacti']['existing_content_id']) && !empty($record['cacti']['existing_airtime_reference'])) {
                                                                    if (($record['upload']['airtime_reference']) != ($record['cacti']['existing_airtime_reference'])) {
                                                                        echo "<span class=\"red\">ISSUE: CSV-" . $record['upload']['airtime_reference'] . '|CACTI-' . $record['cacti']['existing_airtime_reference'] . "</span>";
                                                                        $ready_for_action_airtime_reference = false;
                                                                    } else {
                                                                        echo "<span class=\"green\">MATCH: " . $record['upload']['airtime_reference'] . "</span>";
                                                                    }
                                                                } else {
                                                                    echo "<span class=\"blue\">INC: " . $record['upload']['airtime_reference'] . "</span>";
                                                                } ?>
                                                                    </td>
                                                                    <td class="territory_name border_bottom">
                                                                        <?php
                                                                $ready_for_action_airtime_product_reference = true;
                                                                if (!empty($record['cacti']['existing_content_id']) && !empty($record['cacti']['existing_airtime_product_reference'])) {
                                                                    if (($record['upload']['easel_product_ref']) != ($record['cacti']['existing_airtime_product_reference'])) {
                                                                        echo "<span class=\"red\">ISSUE: CSV-" . $record['upload']['easel_product_ref'] . '|CACTI-' . $record['cacti']['existing_airtime_product_reference'] . "</span>";
                                                                        $ready_for_action_airtime_product_reference = false;
                                                                    } else {
                                                                        echo "<span class=\"green\">MATCH: " . $record['upload']['easel_product_ref'] . "</span>";
                                                                    }
                                                                } else {
                                                                    echo "<span class=\"blue\">INC: " . $record['upload']['easel_product_ref'] . "</span>";
                                                                } ?>
                                                                    </td>
                                                                
                                                                    <td class="territory_name border_bottom">
                                                                        <?php
                                                                $ready_for_action_airtime_state = true;
                                                                if (!empty($record['cacti']['existing_content_id']) && !empty($record['cacti']['existing_airtime_state'])) {
                                                                    if (($record['upload']['airtime_state']) != ($record['cacti']['existing_airtime_state'])) {
                                                                        echo "<span class=\"red\">ISSUE: CSV-" . $record['upload']['airtime_state'] . '|CACTI-' . $record['cacti']['existing_airtime_state'] . "</span>";
                                                                    } else {
                                                                        echo "<span class=\"green\">MATCH: " . $record['upload']['airtime_state'] . "</span>";
                                                                        $ready_for_action_airtime_state = false;
                                                                    }
                                                                } else {
                                                                    echo "<span class=\"blue\">INC: " . $record['upload']['airtime_state'] . "</span>";
                                                                } ?>
                                                                    </td>

                                                                    <td class="check-box border_bottom">
                                                                        <div class="checkbox pull-right" >
                                                                            <?php
                                                                    $ready_for_action = true;
                                                                if (!$ready_for_action_content_id || !$ready_for_action_file_name || !$ready_for_action_file_type || !$ready_for_action_airtime_reference || !$ready_for_action_airtime_product_reference || (empty($record['upload']['airtime_reference']) || empty($record['upload']['easel_product_ref']))) {
                                                                    $ready_for_action = false;
                                                                }

                                                                $blocked = (!$ready_for_action_content_id || !$ready_for_action_file_name || !$ready_for_action_file_type) ? true : false ; ?>

                                                                            <input type="hidden" name="decoded_files_upload[<?php echo $record['upload']['upload_id']; ?>][upload_id]" value="<?php echo $record['upload']['upload_id']; ?>" />
                                                                            <input type="hidden" name="decoded_files_upload[<?php echo $record['upload']['upload_id']; ?>][checked]" value="0" />
                                                                            <label><input type="checkbox" name="decoded_files_upload[<?php echo $record['upload']['upload_id']; ?>][checked]" value="1" class="chk<?php echo (!empty($group)) ? $group : '' ;?>" <?php echo ($ready_for_action) ? 'checked="checked"' : '' ; ?> <?php echo ($blocked) ? 'disabled="disabled"' : '' ; ?> /></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" style="color:<?php echo ($group == 'new-records') ? 'green' : 'red'; ?>" title="These records <?php echo ($group == 'new-records') ? 'are ready for processing into Content records' : 'already exist on the system'; ?>" >
                                                    <?php
                                                    ## The action for new-record is hidden for now (02/02/22)
                                                    if ($group != 'new-records') { ?>
                                                        <span class="pull-right">
                                                            <button style="display:none" class="submit-btn btn btn-default btn-<?php echo ($group == 'new-records') ? 'success' : 'success'; ?> grp_<?php echo $group;?>" data-action_type="<?php echo ($group == 'new-records') ? 'add' : 'update'; ?>" data-form_id="<?php echo $group; ?>" >
                                                                <?php echo ($group == 'new-records') ? 'Add New Records' : 'Update Existing records with data from Upload'; ?>
                                                            </button>
                                                        </span>
                                                        <?php
                                                    } ?>
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
                        <a href="<?php echo base_url('webapp/content/su_decoded_files_upload/' . $this->user->account_id); ?>" class="btn btn-sm btn-block btn-danger" type="button">Go back and re-upload file</a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 hide">
                        <button id="doc-upload-btn" data-form_id="<?php echo $group; ?>" data-action_type="<?php echo ($group == 'new-records') ? 'add' : 'update'; ?>" class="btn btn-sm btn-block submit-btn btn-success" type="submit">Create Records</button>
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
                        <a href="<?php echo base_url('webapp/content/su_decoded_files_upload/' . $this->user->account_id); ?>" class="btn btn-info" type="submit">Start Upload Clearance</a>
                    </div>
                </div>
                <?php
            } ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){

        $( '.grp' ).click( function(){
            var grpId = $( this ).data( 'grp_id' );
            $( '.grp_' + grpId ).slideToggle();
        });

        //Check all selected inputs
        $( '.chk-all' ).click( function(){
            var chkId = $( this ).data( 'chk_id' );
            if( this.checked ) {
                $( '.chk'+chkId ).each(function() {
                    this.checked = true;
                });
            }else{
                $( '.chk'+chkId ).each(function() {
                    this.checked = false;
                });
            }
        });

        //Submit checked records
        $( '.submit-btn' ).click( function( e ){
            e.preventDefault();
            var formId      = $( this ).data( 'form_id' );
            var actionType  = $( this ).data( 'action_type' );
            var postUrl     = "<?php echo base_url('webapp/content/su_process_decoded_files/'); ?>";

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
                    title: '<small>Please select at-least 1 record to '+ actionType + '</small>'
                })
                return false;
            }

            var formData = $( '#frm-'+formId ).serialize();

            swal({
                title: 'Confirm ' + actionType + ' records?',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function (result) {
                if ( result.value ) {
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
                                    timer: 2000
                                })
                                window.setTimeout(function(){
                                    if( data.processed == true ){
                                        // location.href = "<?php echo base_url('webapp/content/su_decoded_files_review/' . $this->user->account_id); ?>";
                                        location.reload();
                                    } else {
                                        location.reload();
                                    }
                                }, 2000);
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
        } );
    });
</script>