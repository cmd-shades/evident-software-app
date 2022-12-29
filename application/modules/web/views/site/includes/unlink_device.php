<div id="unlink-device" class="row">
    <div class="x_panel no-border">
        <div class="row top_row">
            <form id="unlink_devices_form">
                <input type="hidden" name="site_id" value="<?php echo (!empty($site_id)) ? $site_id : null ; ?>" />
                <div class="row top-header">
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <div class="disconnect-devices"><span>Disconnect Devices</span></div>
                    </div>
                    <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
                        <div class="action-to-devices"><span class="">You can only apply actions to devices on the same page, for more devices submit and reclick the icon to return</span></div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                        <?php
                        $this->load->view('webapp/_partials/search_bar'); ?>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="datatable" class="table">
                        <thead>
                            <tr>
                                <th width="20%">Device Unique ID</th>
                                <th width="20%">Product Name</th>
                                <th width="15%">Platform</th>
                                <th width="15%">Airtime ID</th>
                                <th width="15%">Segment ID</th>
                                <th width="10%"><img class="unlink_devices_icon" src="<?php echo base_url("assets/images/icons/xs-unlink-device.png"); ?>" /><input type="checkbox" id="check_all" /></th>
                            </tr>
                        </thead>
                        <tbody id="table-results">
                        </tbody>
                    </table>
                </div>
                <div class="action-row">
                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6">
                        <button id="return" class="btn btn-sm btn-block btn-danger" type="button">Return</button>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-4 hidden-xs">&nbsp;</div>
                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6">
                        <button class="btn btn-sm btn-block submit-btn btn-success" type="submit">Disconnect Selected Rows</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>




<script type="text/javascript">
$( document ).ready( function(){
    var search_str          = "";
    var start_index         = 0;
    var action              = "linked_to_unlink";

    load_data( search_str );

    // Click on Pagination links
    $( "#table-results" ).on( "click", ".pagination > li", function( event ){
        event.preventDefault();
        $( "#check_all" ).prop( "checked", false );
        var search_str  = encodeURIComponent( $( "#search_term" ).val() );
        var start_index = $( this ).find( 'a' ).data( 'ciPaginationPage' );
        load_data( search_str, start_index );
    });

    function load_data( search_str, start_index ){
        $.ajax({
            url: "<?php echo base_url('webapp/site/action_devices_lookup'); ?>",
            method:"POST",
            data:{
                site_id: <?php echo (!empty($site_id)) ? $site_id : null ; ?>,
                search_term:search_str,
                start_index:start_index,
                action: action
            },
            success:function( data ){
                if( data ){
                    data = JSON.parse( data );
                    $( '#table-results' ).html( data.table_data );
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


    $( "#check_all" ).on( "click", function(){
        if( $( this ).prop( "checked" ) != true ){
            $( "#datatable input[type='checkbox']" ).each(
                function(){ $( this ).prop( "checked", false ) }
            )
        } else {
            $( "#datatable input[type='checkbox']" ).each(
                function(){ $( this ).prop( "checked", true ) }
            )
        }
    });


    $( "#datatable" ).on( "click", "input[type='checkbox']:not( :first )", function(){
        if( ( $( "#check_all" ).prop( "checked" ) == true ) && ( $( this ).prop( "checked" ) != true ) ){
            $( "#check_all" ).prop( "checked", false );
        }
    });

    $( "#return" ).on( "click", function( e ){
        e.preventDefault();
        location.href ="<?php echo (!empty($site_id)) ? base_url('webapp/site/profile/' . $site_id) : null ; ?>";
    });

    $( "#unlink_devices_form" ).on( "submit", function( e ){
        e.preventDefault();

        var selected = ( $( "#unlink_devices_form #table-results input[type='checkbox']:checked" ) );

        if( parseInt( selected.length ) > 0 ){

            var devices_data = {};

            selected.each( function( key ){
                var $this = $( this );

                var segmentID = String( $this.data( "easel_segment_id" ) );
                devices_data[key] = {
                    easelSegmentId          : String( $this.data( "easel_segment_id" ) ),
                    productId               : parseInt( $this.data( "product_id" ) ),
                    external_reference_id   : String( $this.data( "external_reference_id" ) ),
                    deviceId                : parseInt( $this.val() )
                };
            });

            $.ajax({
                url: "<?php echo base_url('webapp/site/unlink_devices'); ?>",
                method: "POST",
                data: { 'devices_data': JSON.stringify( devices_data ) },
                success:function( data ){
                    var data = JSON.parse( data );
                    if( data.status == true ){
                        swal({
                            type: 'success',
                            title: data.status_msg,
                            showConfirmButton: true,
                        }).then( ( result ) => {
                            window.location.reload( true );
                        }).catch( swal.noop );
                    }
                }
            });
        } else {
            swal({
                type: 'error',
                title: 'You have to select at least one Device'
            }).then( ( result ) => {
            }).catch( swal.noop );
        }
    });
});
</script>