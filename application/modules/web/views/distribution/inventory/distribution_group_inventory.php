<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 6px 8px;
    border-top: 1px solid #fff;
    font-size: 95%;
}

.fas.fa-edit {
    font-size: 14px;
}

.film-search_input{
    border:0.5px solid #ddd;
    text-align:left;
    margin-bottom:10px;
}

.auto-remove{
    background:#ff6666;
}

#distribution_bundle_content-overview .current_films,
#auto-remove-content-modal .current_films{
    color: #000;
    margin-bottom: 0;
}

#distribution_bundle_content-overview .x_panel.tile.group-container,
#auto-remove-content-modal .x_panel.tile.group-container{
    margin-bottom: 9px;
    margin-top: 1px;
}

#distribution_bundle_content-overview .x_panel.tile.group-container .group-content,
#auto-remove-content-modal .x_panel.tile.group-container .group-content{
    color: #000;
    padding: 0;
    background: #fff;
}

#distribution_bundle_content-overview .sum,
#auto-remove-content-modal .sum{
    float: right;
    padding-right: 15px;
}

#distribution_bundle_content-overview .x_panel.tile.group-container h4.legend.pointer,
#auto-remove-content-modal .x_panel.tile.group-container h4.legend.pointer{
    padding-top: 8px;
    padding-bottom: 8px;
    font-size: 13px;
}
</style>


<div id="distribution_bundle_content-overview">
    <div class="x_panel no-border">
        <div class="row">
            <div class="x_distribution_group" style="margin-top:-10px;" >
                <div class="rows">
                    <div class="col-md-6 col-xs-12">
                        <legend>Films In License</legend>
                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <div class="top_search">
                                    <input type="text" class="film-search_input btn-sm search-input" id="search_current_films" placeholder="Search Current films">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <?php
                                if (!empty($current_films)) {
                                    foreach ($current_films as $prov_key => $curent_provider_films) { ?>
                                        <div class="x_panel tile group-container content provider-container">
                                            <h4 class="legend pointer"><i class="fas fa-caret-down"></i><?php echo (!empty($prov_key)) ? $prov_key : 'Provider Name' ; ?> <span class="sum"><?php echo '(' . count((array) $curent_provider_films) . ')'; ?></span></h4>
                                            <div class="row group-content el-hidden">
                                                <table id="current_films" class="table" width="100%" style="border-top:none;">
                                                <?php
                                                foreach ($curent_provider_films as $key => $film) { ?>
                                                    <tr data-bundle_content_id="<?php echo $film->bundle_content_id ?>" class="edit-bundle-film pointer <?php echo !empty($film->film_attributes->content_group_class) ? $film->film_attributes->content_group_class : ''; ?>">
                                                        <td><?php echo date('d/m/Y', strtotime($film->clearance_date)); ?> - <?php echo $film->title; ?> ( <?php echo strtoupper($film->age_rating_name); ?> <?php echo !empty($film->film_attributes->codec_definition) ? $film->film_attributes->codec_definition : '';?>&nbsp;<?php echo !empty($film->film_attributes->content_languages) ? implode(' ', object_to_array($film->film_attributes->content_languages)) : ''; ?> )</td>
                                                        <td><span class="pull-right pointer edit-bundle-film" data-bundle_content_id="<?php echo $film->bundle_content_id ?>" title="Edit this Bundle Film" ><small><i class="fas fa-edit"></i></small></span></td>
                                                    </tr>
                                                    <?php
                                                } ?>
                                                </table>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } else { ?>
                                    <table id="current_films" class="table" width="100%" style="border-top:none;">
                                        <tr>
                                            <td colspan="2" >No License film records to display</td>
                                        </tr>
                                    </table>
                                    <?php
                                } ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <legend>Expired License</legend>
                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <div class="top_search">
                                    <input type="text" class="film-search_input btn-sm search-input" id="search_library_films" placeholder="Search Library films" >
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <?php
                                if (!empty($library_films)) {
                                    foreach ($library_films as $lib_prov_key => $library_provider_films) { ?>
                                        <div class="x_panel tile group-container content provider-container">
                                            <!-- <h4 class="legend pointer"><i class="fas fa-caret-down"></i><?php echo (!empty($lib_prov_key)) ? $lib_prov_key : 'Provider Name' ; ?> <span class="sum"><?php echo '(' . count((array) $library_provider_films) . ')'; ?></span></h4> -->
                                            <div class="row group-content el-shown">
                                                <table id="library_films" class="table" width="100%" style="border-top:none;">
                                                <?php
                                                foreach ($library_provider_films as $key => $film) { ?>
                                                    <tr data-bundle_content_id="<?php echo $film->bundle_content_id ?>" class="edit-bundle-film pointer <?php echo !empty($film->film_attributes->content_group_class) ? $film->film_attributes->content_group_class : ''; ?>">
                                                        <td><?php echo date('d/m/Y', strtotime($film->clearance_date)); ?> - <?php echo $film->title; ?> ( <?php echo strtoupper($film->age_rating_name); ?> <?php echo !empty($film->film_attributes->codec_definition) ? $film->film_attributes->codec_definition : '';?><?php echo !empty($film->film_attributes->content_languages) ? implode(' ', object_to_array($film->film_attributes->content_languages)) : ''; ?> )</td>
                                                        <td><span class="pull-right pointer edit-bundle-film" data-bundle_content_id="<?php echo $film->bundle_content_id ?>" title="Edit this Bundle Film" ><small><i class="fas fa-edit"></i></small></span></td>
                                                    </tr>
                                                    <?php
                                                } ?>
                                                </table>
                                            </div>
                                        </div>
                                        <?php
                                    } ?>
                                    <?php
                                } ?>

                                <?php
                                if (!empty($recylable_films)) {
                                    foreach ($recylable_films as $rec_prov_key => $recyclable_provider_films) { ?>
                                        <div class="x_panel tile group-container content provider-container">
                                            <!-- <h4 class="legend pointer"><i class="fas fa-caret-down"></i><?php echo (!empty($lib_prov_key)) ? $lib_prov_key : 'Provider Name' ; ?> <span class="sum"><?php echo '(' . count((array) $recyclable_provider_films) . ')'; ?></span></h4> -->
                                            <div class="row group-content el-shown">
                                                <table id="recyclable_films" class="table" width="100%" style="border-top:none;">
                                                <?php
                                                foreach ($recyclable_provider_films as $key => $film) { ?>
                                                    <tr data-bundle_content_id="<?php echo $film->bundle_content_id ?>" class="edit-bundle-film pointer <?php echo !empty($film->film_attributes->content_group_class) ? $film->film_attributes->content_group_class : ''; ?>">
                                                        <td><?php echo date('d/m/Y', strtotime($film->clearance_date)); ?> - <?php echo $film->title; ?> ( <?php echo strtoupper($film->age_rating_name); ?> <?php echo !empty($film->film_attributes->codec_definition) ? $film->film_attributes->codec_definition : '';?><?php echo !empty($film->film_attributes->content_languages) ? implode(' ', object_to_array($film->film_attributes->content_languages)) : ''; ?> )</td>
                                                        <td><span class="pull-right" title="Edit this Bundle Film" ><small><i class="fas fa-edit"></i></small></span></td>
                                                    </tr>
                                                    <?php
                                                } ?>
                                                </table>
                                            </div>
                                        </div>
                                        <?php
                                    } ?>
                                    <?php
                                } ?>

                                <?php
                                if (empty($library_films) && empty($recylable_films)) { ?>
                                    <table class="table" width="100%" style="border-top:none;">
                                        <tr>
                                            <td colspan="2" >No library film records to display</td>
                                        </tr>
                                    </table>
                                    <?php
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for adding a new asset type -->
<div id="update-bundle-content-modal" >
    <div class="modal fade update-bundle-content-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <form id="bundle_content-profile-form">
                <input type="hidden" name="page" value="details" />
                <input type="hidden" name="distribution_group_id" value="<?php echo $distribution_group_details->distribution_group_id; ?>" />
                <div class="modal-content">
                    <div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="myBundleModalLabel">Bundle Film Profile</h4>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button id="update_bundle_content_btn" class="btn btn-sm btn-success" type="button" >Update Bundle Film Information</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Review and Auto-Removing Films -->
<div id="auto-remove-content-modal" >
    <div class="modal fade auto-remove-content-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <form id="bundle_content-profile-form">
                <input type="hidden" name="page" value="details" />
                <input type="hidden" name="distribution_group_id" value="<?php echo $distribution_group_details->distribution_group_id; ?>" />
                <div class="modal-content">
                    <div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="myBundleModalLabel">Auto Remove Oldest Films</h4>
                    </div>

                    <div class="modal-body">
                        <div class="col-md-12 col-xs-12">
                            <?php
                            if (!empty($current_films)) {
                                foreach ($current_films as $prov_key => $curent_provider_films) { ?>
                                    <div class="x_panel tile group-container content provider-container">
                                        <h4 class="legend pointer"><i class="fas fa-caret-down"></i><?php echo (!empty($prov_key)) ? $prov_key : 'Provider Name' ; ?> <span class="sum"><?php echo '(' . count((array) $curent_provider_films) . ')'; ?></span></h4>
                                        <div class="row group-content el-shown">
                                            <table id="auto_remove_content" class="table" width="100%" style="border-top:none;">
                                                <?php
                                                foreach ($curent_provider_films as $key => $film) { ?>
                                                    <tr class="<?php echo in_array($film->bundle_content_id, $content_to_remove) ? 'auto-remove' : ''; ?>">
                                                        <td><?php echo date('d/m/Y', strtotime($film->clearance_date)); ?> - <?php echo $film->title; ?> ( <?php echo strtoupper($film->age_rating_name); ?> <?php echo !empty($film->film_attributes->codec_definition) ? $film->film_attributes->codec_definition : '';?><?php echo !empty($film->film_attributes->content_languages) ? implode(' ', object_to_array($film->film_attributes->content_languages)) : ''; ?> )</td>
                                                        <td><span class="pull-right" title="Check this item to remove it from this current Bundle" ><input type="checkbox" <?php echo in_array($film->bundle_content_id, $content_to_remove) ? 'checked=checked' : ''; ?> name="auto_remove_ids[]"  value="<?php echo $film->bundle_content_id ?>" ></span></td>
                                                    </tr>
                                                    <?php
                                                } ?>
                                            </table>
                                        </div>
                                    </div>
                                    <?php
                                } ?>
                                <?php
                            } else { ?>
                                <table id="auto_remove_content" class="table" width="100%" style="border-top:none;">
                                    <tr>
                                        <td colspan="2" >No film records to display</td>
                                    </tr>
                                </table>
                                <?php
                            } ?>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button id="remove_bundle_content_btn" class="btn btn-sm btn-success" type="button" >Remove Selected Films</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $( document ).ready(function(){


        $( ".legend" ).click( function( e ){
            var target = $( e.target );

            if( target.hasClass( "fa-trash-alt" ) ){
                return;
            } else {
                $( this ).children( ".fa-caret-down, .fa-caret-up" ).toggleClass( "fa-caret-down fa-caret-up" );
                $( this ).next( ".group-content" ).slideToggle( 400 );
            }
        });


        var autoRemove      = "<?php echo $auto_remove ?>";
        var totalToRemove   = "<?php echo $total_to_remove ?>";
        var baseLine        = "<?php echo(!empty($base_line) ? 1 : 0) ?>";

        <?php if ($total_to_remove && !empty($content_to_remove)) { ?>
            var autoRemove = 1;
        <?php } ?>

        if( autoRemove && ( baseLine == 0 ) ){
            $( '#auto-remove-content-modal .auto-remove-content-modal' ).modal( 'show' );
        }

        $( '.modal-body .datetimepicker' ).datetimepicker({
            formatDate: 'd/m/Y',
            timepicker: false,
            format:'d/m/Y',
            scrollMonth: false
        });

        $( '.modal-body' ).on( 'focus', '.datetimepicker', function(){
            $( this ).datetimepicker({
                formatDate: 'd/m/Y',
                timepicker: false,
                format:'d/m/Y',
                scrollMonth: false
            });
        });

        //SEARCH CURRENT FILMS
        var $currenFilmRows = $('#current_films tr');
        $( '#search_current_films' ).keyup(function() {
            var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
            $currenFilmRows.show().filter(function() {
                var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
                return !~text.indexOf(val);
            }).hide();
        });

        //SEARCH LIBRARY FILMS
        var $libraryFilmRows = $('#library_films tr');
        $( '#search_library_films' ).keyup(function() {
            var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
            $libraryFilmRows.show().filter(function() {
                var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
                return !~text.indexOf(val);
            }).hide();
        });


        //TOGGLE EDIT-MODAL
        $( '.edit-bundle-film' ).click( function(){

            var contendBundleId = $( this ).data( 'bundle_content_id' );

            $( '#update-bundle-content-modal .update-bundle-content-modal' ).modal( 'show' );

            if( contendBundleId ){
                $.ajax({
                    url:"<?php echo base_url('webapp/distribution/view_bundle_content_record/'); ?>",
                    method:"POST",
                    data:{bundle_content_id:contendBundleId},
                    dataType: 'json',
                    success:function( data ){
                        if( data.status == 1 ){
                            $( '#update-bundle-content-modal .modal-body' ).html( data.bundle_content );
                            $( '#update-bundle-content-modal .update-bundle-content-modal' ).modal( 'show' );
                        }else{
                            swal({
                                type: 'error',
                                title: data.status_msg
                            })
                        }
                    }
                });
            }

        });


        //UPDATE BUNDLE-CONTENT
        $( '#update_bundle_content_btn' ).click( function( e ){

            e.preventDefault();

            var formData    = $( this ).closest( 'form' ).serialize();

            swal({
                title: 'Confirm bundle content update?',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function( result ) {
                if ( result.value ) {
                    $.ajax({
                        url:"<?php echo base_url('webapp/distribution/update_bundle_content/'); ?>",
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
                                window.setTimeout( function(){
                                    window.location = window.location.href.split("?")[0];
                                }, 2000 );
                            }else{
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


        //AUTO-REMOVE BUNDLE-CONTENT
        $( '#remove_bundle_content_btn' ).click( function( e ){

            e.preventDefault();

            var formData    = $( this ).closest( 'form' ).serialize();

            var totalChecked= $( "#auto_remove_content input[type='checkbox']:checked" ).length;

            if( totalChecked == 0 ){
                swal({
                    title: 'Select at-least 1 Film',
                    html: 'You can remove up to <strong>'+totalToRemove+'</strong> Film(s)',
                    showCancelButton: true,
                    confirmButtonColor: '#5CB85C',
                    cancelButtonColor: '#9D1919',
                    confirmButtonText: 'Yes'
                });
                return false;
            }

            if( totalChecked > totalToRemove ){
                swal({
                    type: 'warning',
                    title: 'You have selectec more than required!',
                    html: 'You can remove up to <strong>'+totalToRemove+'</strong> Film(s)',
                    showCancelButton: true,
                    confirmButtonColor: '#5CB85C',
                    cancelButtonColor: '#9D1919',
                    confirmButtonText: 'Yes'
                }).then( function( result ) {
                    if ( result.value ) {
                        //Proceed
                    } else {
                        return false;
                    }
                });

            }

            swal({
                title: 'Confirm Auto-Remove content?',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function( result ) {
                if ( result.value ) {
                    $.ajax({
                        url:"<?php echo base_url('webapp/distribution/update_bundle_content/'); ?>",
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
                                window.setTimeout( function(){
                                    window.location = window.location.href.split("?")[0];
                                }, 2000 );
                            }else{
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

    });
</script>

