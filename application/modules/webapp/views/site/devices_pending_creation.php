<style type="text/css">
    /* .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
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

    .tmp_device_id{
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
    
    .table-responsive.display{
        margin-top: 20px; 
        margin-bottom: 20px;
    } */
    
</style>


<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel tile has-shadow">
            <legend>Review Devices waiting for the Upload</legend>
            <?php
            if (!empty($pending)) { ?>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="table-responsive" style="overflow-x: initial;">
                            <?php
                            foreach ($pending as $group => $records) { ?>
                                <form id="frm-<?php echo $group;?>" action="<?php echo base_url('webapp/site/add_devices/' . $this->user->account_id); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                                    <table class="table table-responsive display">
                                        <tbody>
                                            <tr>
                                                <td colspan="6" style="color:<?php echo ($group == 'new-records') ? 'green' : 'red'; ?>" title="These records <?php echo ($group == 'new-records') ? 'are ready for processing into Devices records' : 'already exist on the system'; ?>" >
                                                    <span class="pointer grp" data-grp_id="<?php echo $group;?>"><?php echo ucwords(str_replace(['-', '_'], ' ', $group)); ?> ( <?php echo (!empty($records)) ? count($records) : '0'; ?> )</span>
                                                </td>
                                            </tr>
                                            <tr class="grp_<?php echo $group;?>" style="display:none">
                                                <td colspan="6" >
                                                    <table class="table table-responsive full-width">
                                                        <thead>
                                                            <tr>
                                                                <th width="8%">Site ID</th>
                                                                <th width="8%">Product ID</th>
                                                                <th width="34%">Device Unique ID</th>
                                                                <th width="20%">Platform</th>
                                                                <th width="25%">Created Date</th>
                                                                <th width="5%">
                                                                    <div class="checkbox pull-right">
                                                                        <label><strong>Tick all</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="chk-all chk<?php echo $group;?>" data-chk_id="<?php echo $group;?>" type="checkbox" value=""></label>
                                                                    </div>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($records as $key => $record) { ?>
                                                                <tr data-tmp_device_id="<?php echo $record['tmp_device_id']; ?>" >
                                                                    <td class="site_id border_bottom"><input class="form-control changeable-field" type="text" name="batch_devices[<?php echo $record['tmp_device_id']; ?>][site_id]" value="<?php echo (!empty($record['site_id'])) ? $record['site_id'] : '' ;?>" ></td>
    
                                                                    <td class="product_id border_bottom"><input class="form-control changeable-field" type="text" name="batch_devices[<?php echo $record['tmp_device_id']; ?>][product_id]" value="<?php echo (!empty($record['product_id'])) ? $record['product_id'] : '' ;?>" ></td>

                                                                    <td class="device_unique_id border_bottom hide">
                                                                        <input class="form-control changeable-field" type="hidden" name="batch_devices[<?php echo $record['tmp_device_id']; ?>][device_unique_id]" value="<?php echo (!empty($record['device_unique_id'])) ? $record['device_unique_id'] : '' ;?>" >
                                                                    </td>

                                                                    <td class="device_unique_id border_bottom"><input class="form-control changeable-field" type="text" name="batch_devices[<?php echo $record['tmp_device_id']; ?>][device_unique_id]" value="<?php echo (!empty($record['device_unique_id'])) ? $record['device_unique_id'] : '' ;?>" ></td>
                                                                
                                                                    <td class="platform border_bottom"><input class="form-control changeable-field" type="text" name="batch_devices[<?php echo $record['tmp_device_id']; ?>][platform]" value="<?php echo (!empty($record['platform'])) ? $record['platform'] : '' ;?>" ></td>
                                                                    
                                                                    <td class="created_date border_bottom"><input class="form-control changeable-field" type="text" name="batch_devices[<?php echo $record['tmp_device_id']; ?>][created_date]" value="<?php echo (!empty($record['created_date'])) ? $record['created_date'] : '' ;?>" ></td>
                                                                    

                                                                    <td class="check-box border_bottom">
                                                                        <div class="checkbox pull-right">
                                                                            <input type="hidden" name="batch_devices[<?php echo $record['tmp_device_id']; ?>][checked]" value="0" />
                                                                            <label><input type="checkbox" name="batch_devices[<?php echo $record['tmp_device_id']; ?>][checked]" value="1" class="chk<?php echo (!empty($group)) ? $group : '' ;?>" ></label>
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
                                                    <span class="pull-right">
                                                        <button style="display:none" class="submit-btn btn btn-default btn-<?php echo ($group == 'new-records') ? 'success' : 'danger'; ?> grp_<?php echo $group;?>" data-action_type="<?php echo ($group == 'new-records') ? 'add' : 'remove'; ?>" data-form_id="<?php echo $group; ?>" >
                                                            <?php echo ($group == 'new-records') ? 'Submit Selected Records' : 'Remove Records from the Upload'; ?>
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
                    <?php if (!empty($site_id)) { ?>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <button class="btn btn-sm btn-block btn-success btn-return" type="button">Return</button>
                        </div>
                        <div class="col-lg-offset-6 col-lg-3 col-md-offset-6 col-md-3 col-sm-6 col-xs-12">
                        <?php
                    } else { ?>
                        <div class="col-lg-offset-9 col-lg-3 col-md-offset-9 col-md-3 col-sm-6 col-xs-12">
                        <?php
                    } ?>
                        <a href="<?php echo base_url('webapp/site/upload_devices'); ?>" class="btn btn-sm btn-block btn-danger" type="button">Go back and re-upload file</a>
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
                        <a href="<?php echo base_url('webapp/site/upload_devices'); ?>" class="btn btn-sm btn-block btn-info" type="submit">Start Upload Devices</a>
                    </div>
                </div>
                <?php
            } ?>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $( ".btn-return" ).on( "click", function(){
        swal({
            title: 'Confirm going back to the site profile?',
            icon: 'warning',
            confirmButtonText: 'Yes',
            confirmButtonColor: '#5CB85C',
            cancelButtonColor: '#9D1919',
            showCancelButton: true,
        }).then( ( result ) => {
            if ( result.value ){
                <?php
                if (!empty($site_id)) { ?>
                    location.href = "<?php echo base_url('webapp/site/profile/' . $site_id); ?>";
                    <?php
                } else { ?>
                    location.href = "<?php echo base_url('webapp/site/sites'); ?>";
                    <?php
                } ?>
            }
        }).catch( swal.noop );
    })
    
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
        var selection = document.getElementById( 'uploadfile' );
        if( selection.files.length == 0 ){
            swal({
                type: 'error',
                text: 'No file selected!'
            });
            return false;
        }

        for( var i=0; i < selection.files.length; i++ ){
            var filename    = selection.files[i].name;
            var ext         = filename.substr(filename.lastIndexOf('.') + 1);
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
        if( this.checked ) {
            $( '.chk'+chkId ).each( function(){
                this.checked = true;
            });
        }else{
            $( '.chk'+chkId ).each( function(){
                this.checked = false;
            });
        }
    });


    //Submit checked records
    $( '.submit-btn' ).click( function( e ){
        e.preventDefault();
        var formId     = $( this ).data( 'form_id' );
        var actionType = $( this ).data( 'action_type' );

        if( actionType == 'add' ){
            var postUrl = "<?php echo base_url('webapp/site/create_devices/'); ?>";
        } else if ( actionType == 'remove' ){
            var postUrl = "<?php echo base_url('webapp/site/drop_temp_devices/'); ?>";
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
            title: 'Confirm ' + actionType + ' Devices records?',
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
                    success: function(data){
                        if( data.status == 1 ){
                            swal({
                                type: 'success',
                                title: data.status_msg,
                                showConfirmButton: false,
                                timer: 2000
                            })
                            
                            window.setTimeout( function(){
                                if( data.all_done == 1 ){
                                    <?php
                                    if (!empty($site_id)) { ?>
                                        location.href = "<?php echo base_url('webapp/site/profile/' . $site_id); ?>";
                                        <?php
                                    } else { ?>
                                        location.href = "<?php echo base_url('webapp/site/review_devices'); ?>";
                                        <?php
                                    } ?>
                                } else {
                                    location.reload();
                                }
                            }, 2000);

                        } else {
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