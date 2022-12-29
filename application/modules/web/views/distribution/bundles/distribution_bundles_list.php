<style type="text/css">
    .border-top-none,
    .no-border-top,
    .no-top-border{
        border-top:none !important;
    }

    .distro-label{
        margin-bottom:0 !important;
    }

    .modal-vertical-centered {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) !important;
    }


    #distribution_group-dashboard .refresh-distro-bundle,
    #distribution_group-dashboard .cancel-distro-bundle{
        margin-left: 6px;
        width: 20px;
        height: auto;
    }

    #distribution_group-dashboard .refresh-distro-bundle > img,
    #distribution_group-dashboard .cancel-distro-bundle > img{
        width: 100%;
    }


    .bundle_header{
        display: block-inline; 
        float: left;
        font-weight: 800;
    }
    
    .bundle_data_1{
        display: block-inline; float: left;
    }
</style>

<?php
$show_cds_queueWaiting  = (!empty($this->input->get('show_cds_queueWaiting')) && ($this->input->get('show_cds_queueWaiting') == "yes")) ? true : false ;
$show_cds_running       = (!empty($this->input->get('show_cds_running')) && ($this->input->get('show_cds_running') == "yes")) ? true : false ;
$show_cds_completed     = (!empty($this->input->get('show_cds_completed')) && ($this->input->get('show_cds_completed') == "yes")) ? true : false ;


if ($show_cds_queueWaiting) { ?>
    <div class="col-lg-12 col-md-12 col-sm-12" style="margin-bottom: 20px;">
        <div class="col-lg-12 col-md-12 col-sm-12" style="border: 1px solid #000;">
            <?php
            if (!empty($queue_waiting_bundles)) { ?>
                <h3>Queue Waiting</h3>
                <div class="full-width" style="display: block; float: left; width: 100%;">
                    <span class="bundle_header" style="width:20%;">Bundle ID:</span>
                    <!-- <span class="bundle_header" style="width:5%;">CaCTiID:</span> -->
                    <span class="bundle_header" style="width:20%;">Bundle Name</span>
                    <span class="bundle_header" style="width:10%;">Created</span>
                    <!-- <span class="bundle_header" style="width:5%;">Priority</span> -->
                    <span class="bundle_header" style="width:10%;">Scheduled</span>
                    <span class="bundle_header" style="width:10%;">Server ID/Name</span>
                    <!-- <span class="bundle_header" style="width:5%;">Source</span> -->
                    <span class="bundle_header" style="width:7%;">State</span>
                    <span class="bundle_header" style="width:8%;">QueueID</span>
                    <span class="bundle_header" style="width:15%;">Error</span>
                </div>
                <?php
                foreach ($queue_waiting_bundles as $bundle) { ?>
                    <div class="full-width" style="display: block; float: left; width: 100%;">
                        <span class="bundle_data_1" style="width:20%;"><?php echo $bundle->_id; ?></span>
                        <!-- <span class="bundle_data_1" style="width:5%;"><?php echo (!empty($bundle->cactiId)) ? $bundle->cactiId : '&nbsp;'; ?></span> -->
                        <span class="bundle_data_1" style="width:20%;"><?php echo $bundle->content->name; ?></span>
                        <span class="bundle_data_1" style="width:10%;"><?php echo ((isset($bundle->content->created)) && (!empty((int) $bundle->content->created))) ? date("Y-m-d H:i:s", (($bundle->content->created) / 1000)) : '&nbsp;' ; ?></span>
                        <!-- <span class="bundle_data_1" style="width:5%;"><?php echo $bundle->priority; ?></span> -->
                        <span class="bundle_data_1" style="width:10%;"><?php echo ((isset($bundle->timeline->scheduled)) && (!empty((int) $bundle->timeline->scheduled))) ? date("Y-m-d H:i:s", (($bundle->timeline->scheduled) / 1000)) : '&nbsp;' ; ?></span>
                        <span class="bundle_data_1" style="width:10%;"><?php echo $bundle->server->id . '/' . $bundle->server->name; ?></span>
                        <!-- <span class="bundle_data_1" style="width:5%;"><?php echo $bundle->source; ?></span> -->
                        <span class="bundle_data_1" style="width:7%;"><?php echo $bundle->state; ?></span>
                        <span class="bundle_data_1" style="width:8%;"><?php echo (!empty($bundle->queueId)) ? $bundle->queueId : '&nbsp;' ; ?></span>
                        
                        <?php
                        if (!empty($bundle->error)) { ?>
                            <span class="bundle_data_1" style="width:15%;" title="<?php echo $bundle->error->text; ?>"><?php echo $bundle->error->code; ?></span>
                            <?php
                        } else { ?>
                            <span class="bundle_data_1" style="width:15%;">&nbsp;</span>
                            <?php
                        } ?>
                    </div>
                    <?php
                }
            } else { ?>
                <h3>No queueWaiting bundles</h3>
                <?php
            } ?>
        </div>
    </div>
    <?php
}


if ($show_cds_running) { ?>
    <div class="col-lg-12 col-md-12 col-sm-12" style="margin-bottom: 20px;">
        <?php
        if (!empty($running_bundles)) { ?>
            <div class="col-lg-12 col-md-12 col-sm-12" style="border: 1px solid #000;">
                <h3>Queue Running</h3>
                <div class="full-width" style="display: block; float: left; width: 100%;">
                    <span class="bundle_header" style="width:20%;">Bundle ID:</span>
                    <!-- <span class="bundle_header" style="width:5%;">CaCTiID:</span> -->
                    <span class="bundle_header" style="width:20%;">Bundle Name</span>
                    <span class="bundle_header" style="width:10%;">Created</span>
                    <!-- <span class="bundle_header" style="width:5%;">Priority</span> -->
                    <span class="bundle_header" style="width:10%;">Scheduled</span>
                    <span class="bundle_header" style="width:10%;">Server ID/Name</span>
                    <!-- <span class="bundle_header" style="width:5%;">Source</span> -->
                    <span class="bundle_header" style="width:8%;">State</span>
                    <span class="bundle_header" style="width:8%;">QueueID</span>
                    <span class="bundle_header" style="width:7%;">Error</span>
                    <span class="bundle_header" style="width:7%;">Progress</span>
                </div>
                <?php
                foreach ($running_bundles as $bundle) { ?>
                    <div class="full-width" style="display: block; float: left; width: 100%;">
                        <span class="bundle_data_1" style="width:20%;"><?php echo $bundle->_id; ?></span>
                        <!-- <span class="bundle_data_1" style="width:10%;"><?php echo (!empty($bundle->cactiId)) ? $bundle->cactiId : '&nbsp;'; ?></span> -->
                        <span class="bundle_data_1" style="width:20%;"><?php echo $bundle->content->name; ?></span>
                        <span class="bundle_data_1" style="width:10%;"><?php echo ((isset($bundle->content->created)) && (!empty((int) $bundle->content->created))) ? date("Y-m-d H:i:s", (($bundle->content->created) / 1000)) : '&nbsp;' ; ?></span>
                        <!-- <span class="bundle_data_1" style="width:5%;"><?php echo $bundle->priority; ?></span> -->
                        <span class="bundle_data_1" style="width:10%;"><?php echo ((isset($bundle->timeline->scheduled)) && (!empty((int) $bundle->timeline->scheduled))) ? date("Y-m-d H:i:s", (($bundle->timeline->scheduled) / 1000)) : '&nbsp;' ; ?></span>
                        <span class="bundle_data_1" style="width:10%;"><?php echo $bundle->server->id . '/' . $bundle->server->name; ?></span>
                        <!-- <span class="bundle_data_1" style="width:5%;"><?php echo $bundle->source; ?></span>-->
                        <span class="bundle_data_1" style="width:8%;"><?php echo $bundle->state; ?></span>
                        <span class="bundle_data_1" style="width:8%;"><?php echo (!empty($bundle->queueId)) ? $bundle->queueId : '&nbsp;' ; ?></span>
                        <span class="bundle_data_1" style="width:7%;"><?php echo (!empty($bundle->distribution->status->errors)) ? $bundle->distribution->status->errors : '0' ; ?></span>
                        <span class="bundle_data_1" style="width:7%;"><?php echo (!empty($bundle->distribution->status->progress)) ? $bundle->distribution->status->progress : '0' ; ?></span>
                    </div>
                    <?php
                } ?>
            </div>
            <?php
        } else { ?>
            <div class="col-lg-12 col-md-12 col-sm-12" style="border: 1px solid #000;">
                <h3>No Running bundles</h3>
            </div>
            <?php
        } ?>
    </div>
    <?php
} ?>

<?php
if ($show_cds_completed) { ?>
    <div class="col-lg-12 col-md-12 col-sm-12" style="margin-bottom: 20px;">
        <div class="col-lg-12 col-md-12 col-sm-12" style="border: 1px solid #000;">
            <?php
            if (!empty($completed_bundles)) { ?>
                <h3>Queue Completed</h3>
                <div class="full-width" style="display: block; float: left; width: 100%;">
                    <span class="bundle_header" style="width:20%;">Bundle ID:</span>
                    <!-- <span class="bundle_header" style="width:5%;">CaCTiID:</span> -->
                    <span class="bundle_header" style="width:20%;">Bundle Name</span>
                    <span class="bundle_header" style="width:10%;">Created</span>
                    <!-- <span class="bundle_header" style="width:5%;">Priority</span> -->
                    <span class="bundle_header" style="width:10%;">Scheduled</span>
                    <span class="bundle_header" style="width:10%;">Server ID/Name</span>
                    <!-- <span class="bundle_header" style="width:5%;">Source</span> -->
                    <span class="bundle_header" style="width:8%;">State</span>
                    <span class="bundle_header" style="width:8%;">QueueID</span>
                    <span class="bundle_header" style="width:7%;">Errors</span>
                    <span class="bundle_header" style="width:7%;">Progress</span>
                </div>
                <?php
                foreach ($completed_bundles as $bundle) { ?>
                    <div class="full-width" style="display: block; float: left; width: 100%;">
                        <span class="bundle_data_1" style="width:20%;"><?php echo $bundle->_id; ?></span>
                        <!-- <span class="bundle_data_1" style="width:10%;"><?php echo (!empty($bundle->cactiId)) ? $bundle->cactiId : '&nbsp;'; ?></span> -->
                        <span class="bundle_data_1" style="width:20%;"><?php echo $bundle->content->name; ?></span>
                        <span class="bundle_data_1" style="width:10%;"><?php echo ((isset($bundle->content->created)) && (!empty((int) $bundle->content->created))) ? date("Y-m-d H:i:s", (($bundle->content->created) / 1000)) : '&nbsp;' ; ?></span>
                        <!-- <span class="bundle_data_1" style="width:5%;"><?php echo $bundle->priority; ?></span> -->
                        <span class="bundle_data_1" style="width:10%;"><?php echo ((isset($bundle->timeline->scheduled)) && (!empty((int) $bundle->timeline->scheduled))) ? date("Y-m-d H:i:s", (($bundle->timeline->scheduled) / 1000)) : '&nbsp;' ; ?></span>
                        <span class="bundle_data_1" style="width:10%;"><?php echo $bundle->server->id . '/' . $bundle->server->name; ?></span>
                        <!-- <span class="bundle_data_1" style="width:5%;"><?php echo $bundle->source; ?></span>-->
                        <span class="bundle_data_1" style="width:8%;"><?php echo $bundle->state; ?></span>
                        <span class="bundle_data_1" style="width:8%;"><?php echo (!empty($bundle->queueId)) ? $bundle->queueId : '&nbsp;' ; ?></span>
                        <span class="bundle_data_1" style="width:7%;"><?php echo (!empty($bundle->distribution->status->errors)) ? $bundle->distribution->status->errors : '0' ; ?></span>
                        <span class="bundle_data_1" style="width:7%;"><?php echo (!empty($bundle->distribution->status->progress)) ? $bundle->distribution->status->progress : '0' ; ?></span>
                    </div>
                    <?php
                }
            } else { ?>
                <h3>No Completed bundles</h3>
                <?php
            } ?>
        </div>
    </div>
    <?php
} ?>


<div id="distribution_group-dashboard" class="row">
    <div class="x_panel no-border">
        <div class="row">
            <div class="x_distribution_group" style="margin-top:-20px;" >
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><hr></div>
                <div class="row">
                    <div class="pull-right col-lg-7 col-md-6 col-sm-12 col-xs-12">
                        <div class="rows" >
                            <div class="col-md-offset-4 col-md-4 col-sm-6 col-xs-12">
                                <button id="add-new-bundle-trigger" class="create-new-bundles btn btn-block btn-new">New Bundle</button>
                            </div>
                            <div class="pull-right col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                <div class="top_search">
                                    <!-- Search bar -->
                                    <input type="text" class="btn-block <?php echo $module_identier; ?>-search_input btn-primary search-input" id="search_term" value="" placeholder="Search" style="border:none" >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="rows">
                    <div class="table-responsive alert alert-ssid" role="alert" style="overflow-y: hidden;" >
                        <table id="datatable" class="table table-responsive" style="margin-bottom:0px;" >
                            <thead>
                                <tr>
                                    <th width="22%">Distribution Bundles</th>
                                    <th width="15%">Licence Start Date</th>
                                    <th width="12%">Send Status</th>
                                    <th width="12%">Progress (%)</th>
                                    <th width="10%">Errors</th>
                                    <th width="19%">Send Status Timestamp</th>
                                    <th width="10%"><span class="pull-right">Action<span></th>
                                </tr>
                            </thead>
                            <tbody id="bundles-table-results">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'distribution_bundle_create.php'; ?>

<!-- Modal for adding a new asset type -->
<div id="update-distribution-bundle-modal" class="modal fade update-distribution-bundle-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="distribution_bundle-profile-form">
            <input type="hidden" name="page" value="details" />
            <input type="hidden" name="distribution_group_id" value="<?php echo $distribution_group_details->distribution_group_id; ?>" />
            <div class="modal-content">
                <div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myBundleModalLabel">Distribution Bundle Profile</h4>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Review and Auto-Removing Films -->
<div id="content-compile-review-modal" >
    <div class="modal fade content-compile-review-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form id="content-compile-form">
                <input type="hidden" name="page" value="details" />
                <input type="hidden" name="distribution_group_id" value="<?php echo $distribution_group_details->distribution_group_id; ?>" />
                <input type="hidden" name="distribution_bundle_id" value="<?php echo !empty($distribution_bundle_id) ? $distribution_bundle_id : ''; ?>" />
                <div class="modal-content">
                    <div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="myContentCompileModalLabel">Content Compile Review &nbsp;&nbsp;<span id="refresh-page" class="pointer" title="Click to refresh the page Content"><i class="fas fa-sync-alt text-green"></i></span></h4>
                    </div>
                    <div class="modal-body">
                        <table id="content_compile_review" class="table" width="100%" style="border-top:none; font-size:90%">
                            <?php if (!empty($compile_review_data)) { ?>
                                <tr>
                                    <td class="no-top-border text-bold" width="23%">NAME<input class="checked-content-all" id="crc-all" type="checkbox" style="margin-left: 6px;" /></td>
                                    <td class="no-top-border text-bold text-center" width="11%">MOVIE</td>
                                    <td class="no-top-border text-bold text-center" width="11%">TRAILER</td>
                                    <td class="no-top-border text-bold text-center" width="11%">HERO</td>
                                    <td class="no-top-border text-bold text-center" width="11%">STANDARD</td>
                                    <td class="no-top-border text-bold text-center" width="11%">VTT</td>
                                    <td class="no-top-border text-bold text-center" width="11%">XML</td>
                                    <td class="no-top-border text-bold text-center" width="11%">JSON</td>
                                </tr>
                                <?php foreach ($compile_review_data as $key => $content) { ?>
                                    <tr>
                                        <td class="" width="23%">
                                            <input class="checked-content" id="crc-<?php echo $key; ?>" type="checkbox" name="checked_content[]" value="<?php echo $content->content_id; ?>"/>
                                            <label class="distro-label" for="crc-<?php echo $key; ?>" ><?php echo $content->content_name; ?></label>
                                        </td>
                                        <td class="text-center" width="11%" >
                                            <?php echo !empty($content->alt_movie_assets->movie) ? '<i class="fas fa-check text-green" title="Movie asset is ready to send" ></i>' : '<i class="fas fa-times text-red" title="Movie asset was not found!" ></i>'; ?>
                                        </td>
                                        <td class="text-center" width="11%" >
                                            <?php echo !empty($content->alt_movie_assets->trailer) ? '<i class="fas fa-check text-green" title="Trailer asset is ready to send" ></i>' : '<i class="fas fa-times text-red" title="Trailer asset was not found!" ></i>'; ?>
                                        </td>
                                        <td class="text-center" width="11%">
                                            <?php echo !empty($content->movie_images->hero) ? '<i class="fas fa-check text-green" title="Hero image is ready to send" ></i>' : '<i class="fas fa-times text-red" title="Hero image was not found!" ></i>'; ?>
                                        </td>
                                        <td class="text-center" width="11%">
                                            <?php echo !empty($content->movie_images->standard) ? '<i class="fas fa-check text-green" title="Standard image is ready to send" ></i>' : '<i class="fas fa-times text-red" title="Standard image was not found!" ></i>'; ?>
                                        </td>
                                        <td class="text-center" width="11%">
                                            <?php echo !empty($content->movie_subtitles) ? '<i class="fas fa-check text-green" title="VTT file(s) ready to send" ></i>' : '<i class="fas fa-times text-red" title="VTT file(s) was not found!" ></i>'; ?>
                                        </td>
                                        <td class="text-center" width="11%">
                                            <?php echo !empty($content->movie_xml_file) ? '<i class="fas fa-check text-green" title="XML file is ready to send" ></i>' : '<i class="fas fa-times text-red" title="XML file was not found!" ></i>'; ?>
                                        </td>
                                        <td class="text-center" width="11%">
                                            <?php echo !empty($content->movie_json_file) ? '<i class="fas fa-check text-green" title="JSON file ready to send" ></i>' : '<i class="fas fa-times text-red" title="JSON file was not found!" ></i>'; ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button id="compile_content_btn" class="btn btn-sm btn-success" type="button" data-distribution_bundle_id="<?php echo !empty($distribution_bundle_id) ? $distribution_bundle_id : ''; ?>" >Send Selected Films</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $( document ).ready(function(){

        $( '#bundles-table-results' ).on( "click", ".cancel-distro-bundle", function(){
            
            var bundleID = $( this ).data( "distribution_bundle_id" );
            
            swal({
                title: 'Confirm Cancel Bundle?',
                text: 'This will be cancel the sending bundle on Coggins',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function (result) {
                if ( result.value ) {
                    $.ajax({
                        url:"<?php echo base_url('webapp/distribution/cancel_distribution_bundle/'); ?>",
                        method:"POST",
                        data: { distribution_bundle_id: bundleID },
                        dataType: 'json',
                        success:function( data ){
                            if( data.status == 1 ){
                                swal({
                                    type: 'success',
                                    title: data.status_msg,
                                    showConfirmButton: false,
                                    timer: 2000
                                })
                                window.setTimeout( function(){
                                    location.reload();
                                }, 2000 );
                            } else {
                                swal({
                                    type: 'error',
                                    title: data.status_msg
                                })
                            }
                        }
                    });
                }
            }).catch( swal.noop )
        });

        // CHECK all trigger - start
        // "#crc-all" - 'check all' trigger
        // "#content_compile_review" - container
        // ".checked-content" - class of the element

        $( "#crc-all" ).on( "click", function(){
            if( $( this ).prop( 'checked' ) == true ){
                $( "#content_compile_review .checked-content" ).each( function(){
                    $( this ).prop( 'checked', true );
                });
            } else {
                $( "#content_compile_review .checked-content" ).each( function(){
                    $( this ).prop( 'checked', false );
                });
            }
        });

        $( "#content_compile_review .checked-content" ).on( "click", function(){

            var checked = $( "#content_compile_review .checked-content:checked" ).length;
            var all     = $( "#content_compile_review .checked-content" ).length;

            if( ( $( this ).prop( 'checked' ) != true ) && ( $( "#crc-all" ).prop( 'checked' ) == true ) ){
                $( "#crc-all" ).prop( 'checked', false );
            }

            if( ( $( this ).prop( 'checked' ) == true ) && ( parseInt( checked ) == parseInt( all ) ) ){
                $( "#crc-all" ).prop( 'checked', true );
            }
        });
        // CHECK all trigger - end

        $( "#update-distribution-bundle-modal" ).on( "click", "input#schedule-bundle", function(){
            $( ".schedule-bundle-container" ).toggleClass( "el-hidden el-shown-table" );
        });

        $( "#loading" ).dialog({
            hide: 'slide',
            show: 'slide',
            autoOpen: false
        });

        var contenCompile       = "<?php echo(!empty($compile_review) ? 1 : 0) ?>";
        var distroGroupId       = "<?php echo $distribution_group_details->distribution_group_id; ?>";
        var search_str          = null;
        var start_index         = 0;
        var where               = {
            'distribution_group_id':distroGroupId
        };


        if( contenCompile == '1' ){
            $( '#content-compile-review-modal .content-compile-review-modal' ).modal( 'show' );
        }

        // Initial data pull
        load_data( search_str, where, start_index );

        // Pull data on search
        $( '#search_term' ).keyup( function(){
            var search = encodeURIComponent( $( this ).val() );
            if( search.length > 0 ){
                load_data( search, where, start_index );
            } else {
                load_data( search_str, where, start_index );
            }
        });
        
        $( '#bundles-table-results' ).on( "click", ".refresh-distro-bundle", function(){
            // var search = encodeURIComponent( $( this ).val() );
            var search  = encodeURIComponent( $( '#search_term' ).val() );
            if( search.length > 0 ){
                load_data( search, where, start_index );
            } else {
                load_data( search_str, where, start_index );
            }
        });


        //Pagination links
        $( "#bundles-table-results" ).on( "click", "li.page", function( event ){
            event.preventDefault();
            var start_index = $( this ).find( 'a' ).data( 'ci-pagination-page' );
            var search_str  = encodeURIComponent( $( '#search_term' ).val() );
            load_data( search_str, where, start_index );
        });


        // Pull the data
        function load_data( search_str, where, start_index ){
            $.ajax({
                url:"<?php echo base_url('webapp/distribution/distribution_bundles_lookup/'); ?>"+distroGroupId,
                method:"POST",
                data:{ search_term:search_str, where:where, start_index:start_index },
                success:function( data ){
                    $( '#bundles-table-results' ).html( data );
                }
            });
        }

        $( '#bundles-table-results' ).on( 'click', '.click-view-bundle', function(){

            var distroBundleId = $( this ).parent().data( 'distribution_bundle_id' );

            if( distroBundleId ){
                $.ajax({
                    url:"<?php echo base_url('webapp/distribution/view_distribution_bundle_record/'); ?>",
                    method:"POST",
                    data:{distribution_bundle_id:distroBundleId},
                    dataType: 'json',
                    success:function( data ){
                        if( data.status == 1 ){
                            $( "#update-distribution-bundle-modal .modal-body" ).html( data.distribution_bundle );
                            $( '.update-distribution-bundle-modal' ).modal( 'show' );

                            var minimumTime = new Date();
                            // minimumTime.setHours( minimumTime.getHours() + 1 );
                            minimumTime.setMinutes( minimumTime.getMinutes() + 11 );

                            $( '#distribution_bundle-profile-form .datetimepicker' ).datetimepicker({
                                formatDate: 'd/m/Y',
                                defaultDate: new Date(),
                                timepicker: true,
                                format:'d/m/Y H:i',
                                scrollMonth: false,
                                minDate: 0,
                                minTime: minimumTime,
                                step: 5,
                            });

                        }else{
                            swal({
                                type: 'error',
                                title: data.status_msg
                            })
                        }
                    }
                });
            }
        } );


        $( '#bundles-table-results' ).on( 'click', '.delete-distro-bundle', function(){
            var distributionBundleId = $( this ).data( 'distribution_bundle_id' );
            swal({
                title: 'Confirm Delete Bundle?',
                text: 'This will be removed from any linked Sites & Films',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function (result) {
                if ( result.value ) {
                    $.ajax({
                        url:"<?php echo base_url('webapp/distribution/delete_distribution_bundle/'); ?>"+distributionBundleId,
                        method:"POST",
                        data: { distribution_bundle_id: distributionBundleId },
                        dataType: 'json',
                        success:function( data ){
                            if( data.status == 1 ){
                                swal({
                                    type: 'success',
                                    title: data.status_msg,
                                    showConfirmButton: false,
                                    timer: 2000
                                })
                                window.setTimeout( function(){
                                    location.reload();
                                }, 2000 );
                            } else {
                                swal({
                                    type: 'error',
                                    title: data.status_msg
                                })
                            }
                        }
                    });
                }
            }).catch( swal.noop )

        });

        $( '#distribution_bundle-profile-form').on( 'click', '#update-distribution_bundle-btn', function(){

            var formData = $( this ).closest( 'form' ).serialize();
            var bundleId = $( "#update-distribution-bundle-modal .modal-body #distribution_bundle_id" ).val();
            var actIon   = $( "#update-distribution-bundle-modal .modal-body #send_status option:selected" ).val();

            var dateBase = $( ".schedule-bundle-container [name='schedule_date_time']" ).val();
            var splitDate = dateBase.split( /[\s:/]+/ );
            var scheduleDate = new Date( splitDate[2], splitDate[1] - 1, splitDate[0], splitDate[3], splitDate[4] - 10 );
            var nowDate = new Date();

            if( nowDate > scheduleDate ){

                swal({
                    type: 'error',
                    title: 'Schedule Date must be in the future',
                    showCancelButton: false,
                    // confirmButtonColor: '#5CB85C',
                    // cancelButtonColor: '#9D1919',
                    // confirmButtonText: 'Yes'
                })

                return false;
            }

            swal({
                title: 'Update Schedule Date & Time?',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function( result ){
                if( result.value ){
                    $.ajax({
                        url:"<?php echo base_url('webapp/distribution/update_distribution_bundle/'); ?>",
                        method:"POST",
                        data:formData,
                        dataType: 'json',
                        success:function( data ){
                            if( data.status == 1 ){
                                swal({
                                    type: 'success',
                                    title: data.status_msg,
                                    showConfirmButton: false,
                                    timer: 2000
                                })

                                location.href = "<?php echo base_url('webapp/distribution/profile/' . $distribution_group_details->distribution_group_id . '/bundles?distribution_bundle_id='); ?>"+bundleId+'&compile_review=1';

                            } else {
                                swal({
                                    type: 'error',
                                    title: data.status_msg
                                })
                            }
                        }
                    });

                    window.setTimeout( function(){
                        // window.location = window.location.href;
                    }, 2000 );
                }
            }).catch( swal.noop )
        });


        $( '#refresh-page' ).click(function() {
            location.reload();
        });


        $( '#content-compile-form' ).on( 'click', '#compile_content_btn', function(){

            var formData        = $( this ).closest( 'form' ).serialize();
            var bundleId        = $( this ).data( 'distribution_bundle_id' );
            var checkedContent  = $( "#content_compile_review input[type='checkbox']:checked" ).length;

            if( !bundleId ){
                swal({
                    type: 'error',
                    title: 'Invalid Request?',
                    text: 'Distribution Bundle ID is required',
                });
                return false;
            }

            if ( checkedContent < 1 ){
                swal({
                    type: 'error',
                    text: 'Please tick at-least 1 Film to proceed!'
                })
                return false;
            }

            swal({
                title: 'Confirm Send Selected Films?',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function( result ) {
                if ( result.value ) {

                    //Show please wait....

                    $.ajax({
                        url:"<?php echo base_url('webapp/distribution/send_distribution_bundle/'); ?>",
                        method:"POST",
                        data:formData,
                        dataType: 'json',
                        beforeSend: function(){
                            $( "#content-compile-review-modal" ).hide();
                            //$( "#loading" ).dialog( 'open' ).html( "<p>Please Wait...</p>" );
                            // showPleaseWait();
                        },
                        success:function( data ){
                            if( data.status == 1 ){
                                hidePleaseWait();
                                swal({
                                    type: 'success',
                                    title: data.status_msg,
                                    showConfirmButton: false,
                                    timer: 2000
                                })
                                window.setTimeout( function(){
                                    window.location = window.location.href.split("?")[0];
                                }, 2000 );
                            }else{
                                hidePleaseWait();
                                swal({
                                    type: 'error',
                                    title: data.status_msg,
                                    timer: 3000
                                });
                                location.reload();
                            }
                        }
                    });
                }
            }).catch( swal.noop )

        });

    });


    /**
     * Displays overlay with "Please wait" text. Based on bootstrap modal. Contains animated progress bar.
     */
    function showPleaseWait() {

        if ( document.querySelector( "#pleaseWaitDialog") == null ) {
            var modalLoading = '<div class="modal" id="pleaseWaitDialog" data-backdrop="static" data-keyboard="false" role="dialog">\
                <div class="modal-dialog modal-vertical-centered">\
                    <div class="modal-content">\
                        <div class="modal-body" style="min-height: 40px;">\
                            <h4 class="modal-title">Please wait...</h4>\
                            <div class="progress">\
                              <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%; height: 40px"></div>\
                            </div>\
                            <p class="modal-title"><small>This may take several minutes...</small></p>\
                        </div>\
                    </div>\
                </div>\
            </div>';
            $(document.body).append(modalLoading);
        }

        $( "#pleaseWaitDialog" ).modal( "show" );
    }

    /**
     * Hides "Please wait" overlay. See function showPleaseWait().
     */
    function hidePleaseWait() {
        $( "#pleaseWaitDialog" ).modal( "hide" );
        $( '.modal-backdrop' ).remove();
    }


</script>

