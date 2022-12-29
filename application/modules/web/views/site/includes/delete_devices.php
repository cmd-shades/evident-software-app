<style type="text/css">
#delete-devices-module .top_search #search_term{
    margin-top: 0;
}

p.description{
    margin-top: 20px;
    text-align: right;
    font-style: italic;
}

.checkbox input.chk-all{
    position: initial;
    margin-right: 5px;
    margin-left: 5px;
}


#delete-devices-module .x_content{
    overflow-y: hidden;
    margin-top: 10px;
    background: #fff;
    border: 1px solid #000;
    padding: 10px;
    padding-top: 20px;
}

.checkbox.pull-right{
    margin-top: 0;
    margin-bottom: 0;
}
</style>

<div id="delete-devices-module" class="row">
    <div class="x_panel no-border">
        <div class="rows">
            <div class="x_content">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                            <h2><span class="text-bold">Delete Devices</span></h2>
                        </div>
                        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                            <p class="description">You can only apply actions to devices on the same page, for more devices submit and re-click the icon to return</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                            <?php
                            $this->load->view('webapp/_partials/search_bar'); ?>
                        </div>
                    </div>
                    <form id="submit_devices">
                        <input type="hidden" name="site_id" value="<?php echo  (!empty($site_id)) ? $site_id : '' ; ?>" />
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    <table id="datatable" class="table">
                                        <thead>
                                            <tr>
                                                <th width="30%">Device Unique ID</th>
                                                <th width="30%">Product Name</th>
                                                <th width="15%">Platform</th>
                                                <th width="5%">
                                                    <div class="checkbox text-right">
                                                        <i class="fas fa-trash-alt"></i><input class="chk-all chkdelete_devices" data-chk_id="delete_devices" type="checkbox" value="">
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="table-results">
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6">
                                        <button id="cancel_delete_devices" class="btn btn-sm btn-block btn-danger" type="button">Return</button>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-4 hidden-xs">&nbsp;</div>
                                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6">
                                        <button id="" class="btn btn-sm btn-block submit-btn btn-success" type="submit">Delete Selected</button>
                                    </div>
                                </div>
                                <div class="row">&nbsp;</div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">
$( document ).ready( function(){
    $( "form#submit_devices" ).on( "submit", function( e ){
        e.preventDefault();
        
        var formData = $( "form#submit_devices" ).serialize();
        
        if( $('form#submit_devices input[type=checkbox]:checked' ).length ){

            swal({
                title: 'Confirm you want to Delete Devices records?',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function( result ){
                if( result.value ){
                    $.ajax({
                        url: "<?php echo base_url('webapp/site/devices_delete/'); ?>",
                        method: "POST",
                        data: formData,
                        dataType: 'json',
                        success: function( data ){
                            if( data.status == 1 ){
                                if( data.stats ){
                                    swal({
                                        type: 'success',
                                        title: data.stats.deleted + " of " + data.stats.to_delete + "<br /> Devices successfuly deleted",
                                        showConfirmButton: true,
                                    }).then( ( result ) => {
                                            <?php
                                            if (!empty($site_id)) { ?>
                                                location.href = "<?php echo base_url('webapp/site/delete_devices/' . $site_id); ?>";
                                                <?php
                                            } else { ?>
                                                location.href = "<?php echo base_url('webapp/site/sites'); ?>";
                                                <?php
                                            } ?>

                                    }).catch( swal.noop );
                                } else{
                                    swal({
                                        type: 'success',
                                        title: data.status_msg,
                                        showConfirmButton: true,
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
                                }
                            } else {
                                swal({
                                    type: 'error',
                                    title: data.status_msg
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
                            }
                        }
                    });
                }
            }).catch( swal.noop )
        } else {
            swal({
                title: 'At least one device needs to be picked to proceed',
                showCancelButton: false,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'OK'
            }).catch( swal.noop );
        }
        
    });
    
    $( "#cancel_delete_devices" ).on( "click", function(){
        swal({
            title: 'Confirm going back to the site profile?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#5CB85C',
            cancelButtonColor: '#9D1919',
            confirmButtonText: 'Yes'
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
    });

    var search_str          = "";
    var start_index         = 0;

    load_data( search_str );

    // Click on Pagination links
    $( "#table-results" ).on( "click", ".pagination > li", function( event ){
        event.preventDefault();
        var search_str  = encodeURIComponent( $( "#search_term" ).val() );
        var start_index = $( this ).find( 'a' ).data( 'ciPaginationPage' );
        load_data( search_str, start_index );
    });


    function load_data( search_str, start_index ){
        $.ajax({
            url: "<?php echo base_url('webapp/site/delete_devices_lookup'); ?>",
            method:"POST",
            data:{
                site_id: <?php echo (!empty($site_id)) ? $site_id : null ; ?>,
                search_term:search_str,
                start_index:start_index,
            },
            success:function( data ){
                if( data ){
                    $( '#table-results' ).html( data );
                }
            }
        });
    }

    $( '#search_term' ).keyup( function(){
        var start_index = $( this ).find( 'a' ).data( 'ciPaginationPage' );
        var search = encodeURIComponent( $( this ).val() );
        if( search.length > 0 ){
            load_data( search, start_index );
        } else {
            load_data( search_str, start_index );
        }
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

});
</script>