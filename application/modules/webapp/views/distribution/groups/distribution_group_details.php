<style>
    .linked-sites{
        color: #000000;
        background-color: red;
    }

    .close {
        color: #5c5c5c;
    }
</style>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 content-profile-details">
    <form class="distribution_details_form" id="update-content-form" method="post" >
        <input type="hidden" name="page" value="details" />
        <div class="x_panel tile group-container content">
            <input type="hidden" name="distribution_group_id" value="<?php echo (!empty($distribution_group_details->distribution_group_id)) ? $distribution_group_details->distribution_group_id : '' ; ?>" />
            <h4 class="legend pointer"><i class="fas fa-caret-down"></i>Distribution Group Details</h4>
            <div class="row group-content">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 input-container">
                                <label class="input-label-40">Distribution Group</label>
                                <input name="distribution_group" class="input-field-60" type="text" placeholder="Distribution Group" value="<?php echo !empty($distribution_group_details->distribution_group) ? $distribution_group_details->distribution_group : '' ; ?>" />
                            </div>

                            <div class="col-md-12 col-sm-12 col-xs-12 input-container">
                                <label class="input-label-40">System Integrator</label>
                                <select id="system_integrator_id" name="system_integrator_id" class="input-field-60" data-label_text="System Integrator" disabled >
                                    <option value="" >Select Integrator</option>
                                    <?php if (!empty($system_integrators)) {
                                        foreach ($system_integrators as $k => $integrator) { ?>
                                        <option value="<?php echo $integrator->system_integrator_id; ?>" <?php echo (!empty($distribution_group_details->system_integrator_id) && ($distribution_group_details->system_integrator_id == $integrator->system_integrator_id)) ? 'selected="selected"' : "" ; ?> ><?php echo ucwords($integrator->integrator_name); ?></option>
                                        <?php }
                                        } ?>
                                </select>
                            </div>
                            
                            <div class="col-md-12 col-sm-12 col-xs-12 input-container">
                                <label class="input-label-40">Distribution Server</label>
                                <select id="delivery_point_id" name="delivery_point_id" class="input-field-60" data-label_text="Distribution Server">
                                    <option value="">Select Distribution Server</option>
                                    <?php if (!empty($distribution_servers)) {
                                        foreach ($distribution_servers as $server) { ?>
                                        <option value="<?php echo $server->server_id; ?>" <?php echo (!empty($distribution_group_details->delivery_point_id) && ($distribution_group_details->delivery_point_id == $server->server_id)) ? 'selected="selected"' : "" ; ?> ><?php echo $server->server_name; ?> <?php echo (!empty($server->coggins_running)) ? '(' . (ucwords($server->coggins_running)) . ')' : '' ; ?></option>
                                        <?php }
                                        } ?>
                                </select>
                            </div>

                            <div class="col-md-12 col-sm-12 col-xs-12 input-container">
                                <label class="input-label-40">Associated Territory</label>
                                <?php if (!empty($territories)) { ?>
                                    <select id="associated_territory_id" name="associated_territory_id" class="input-field-60" data-label_text="Linked Territory" readonly>
                                        <option value="" >Search / Select Territory</option>
                                        <?php if (!empty($territories)) {
                                            foreach ($territories as $k => $territory) { ?>
                                            <option value="<?php echo $territory->territory_id; ?>" <?php echo (!empty($distribution_group_details->associated_territory_id) && ($distribution_group_details->associated_territory_id == $territory->territory_id)) ? 'selected="selected"' : "" ; ?> ><?php echo $territory->territory_name; ?></option>
                                            <?php }
                                            } ?>
                                    </select>
                                <?php } else { ?>
                                    <input class="input-field" name="associated_territory_id" type="text" placeholder="Content Provider ID" value="<?php echo !empty($distribution_group_details->associated_territory_id) ? $distribution_group_details->associated_territory_id : '' ; ?>" />
                                <?php } ?>
                            </div>
                            
                            <div class="col-md-12 col-sm-12 col-xs-12 input-container">
                                <label class="input-label-40">Distribution Group Description</label>
                                <textarea id="" name="distribution_group_desc" class="input-field-60" data-label_text="Distribution Group Description"><?php echo !empty($distribution_group_details->distribution_group_desc) ? $distribution_group_details->distribution_group_desc : '' ; ?></textarea>
                            </div>

                            <?php if ($this->user->is_admin || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
                                <div class="rows">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <button class="update-distribution_group-btn btn btn-block btn-update btn-primary" type="button" data-distribution_group_section="distribution_details">Update</button>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <button class="delete-distribution_group-btn btn btn-block btn-danger" style="width: 30%; float: right; min-width: 150px;" type="button" data-distribution_group_section="distribution_details">Delete</button>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="rows">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="col-md-12 col-sm-12 col-xs-12 input-container">
                            <div class="tile" style="background: #f7f7f7; color:#000">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <legend>Linked Sites</legend>
                                        <div class="row" >
                                            <?php if (!empty($available_sites)) {
                                                foreach ($available_sites as $site) { ?>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label for="site-<?php echo(strtolower(trim($site->site_id))) ?>">
                                                    <input type="checkbox" <?php echo (!empty($linked_sites) && in_array($site->site_id, $linked_sites)) ? 'checked' : ''; ?> name="linked_sites[]" id="site-<?php echo(strtolower(trim($site->site_id))) ?>" value="<?php echo trim($site->site_id); ?>" class="site-records" /> <span class="site_name"><?php echo ucwords(trim($site->site_name)); ?></span></label>
                                                </div>
                                                <?php }
                                                } ?>
                                        </div>
                                        <br/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 providers-profile-details">
    <div class="x_panel tile group-container territories-clearance">
        <!-- <span class="add-distribution-provider pointer"><a type="button" data-toggle="modal" data-target="#add-distribution-provider"><i class="fas fa-plus-circle"></i></a></span> -->
        <span id="add-distribution-provider" class="add-clearance pointer"><i class="fas fa-plus-circle"></i></span>
        <input type="hidden" name="content_id" value="<?php echo $distribution_group_details->distribution_group_id ; ?>" />
        <h4 class="legend pointer"><i class="fas fa-caret-down"></i>Distribution Group Providers</h4>
        <div class="row group-content">
            <div class="rows">
                <div class="tile" style="background: #f7f7f7; color:#000">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <table class="table table-responsive" style="color:#000" >
                            <tr>
                                <td><strong>Provider Name</strong></td>
                                <td><strong>Number of Titles</strong></td>
                                <td><strong>Films Per Month</strong></td>
                                <td><strong>Date Created</strong></td>
                                <td><strong><span class="pull-right">Action</span></strong></td>
                            </tr>
                            <?php if (!empty($distribution_group_details->distribution_group_providers)) {
                                foreach ($distribution_group_details->distribution_group_providers as $provider) { ?>
                                                                    <?php $providerClass = strip_all_whitespace($provider->provider_name) . $provider->provider_id . $provider->no_of_titles;?>
                                <tr id="provider-<?php echo $providerClass;?>" >
                                    <td><?php echo $provider->provider_name;?></td>
                                    <td><?php echo $provider->no_of_titles;?></td>
                                    <td><?php echo $provider->films_per_month;?></td>
                                    <td><?php echo date('d/m/Y H:i:s', strtotime($provider->date_created)); ?></td>
                                    <td><span class="pull-right remove-distro-provider pointer" data-combination_id="<?php echo $provider->combination_id;?>"><i class="fas fa-trash-alt"></i></a></span></td>
                                </tr>
                                <?php }
                                } else { ?>
                                <tr>
                                    <td colspan="5">There's currently no Providers associated to this Distribution Group</td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade add-distribution-provider-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <input type="hidden" name="page" value="details" />
        <input type="hidden" name="distribution_group_id" value="<?php echo $distribution_group_details->distribution_group_id; ?>" />
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myAddDitroProviderModalLabel">Add Distribution Group Provider</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 input-container">
                        <label>Please select Provider</label>
                        <div id="content_providers">
                            <?php if (!empty($available_providers)) { ?>
                                <select id="provider_selection" class="input-field" style="width:100%" >
                                    <option value="">Please select Provider</option>
                                    <?php foreach ($available_providers as $provider) { ?>
                                        <option value="<?php echo $provider->provider_id; ?>" data-provider_id="<?php echo $provider->provider_id; ?>" data-provider_name="<?php echo $provider->provider_name; ?>" ><?php echo $provider->provider_name; ?></option>
                                    <?php } ?>
                                </select>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12 input-container">
                        <label>Number of Titles</label>
                        <?php if (!empty($no_of_titles_packages)) { ?>
                            <select id="no_of_titles_selection" class="input-field" style="width:100%" >
                                <option value="">Please select Number of Titles</option>
                                <?php foreach ($no_of_titles_packages as $title_row) { ?>
                                    <option value="<?php echo $title_row->setting_id; ?>" data-no_of_titles="<?php echo $title_row->setting_value; ?>" <?php echo (!empty($row->no_of_titles_id) && ($row->no_of_titles_id == $title_row->setting_id)) ? 'selected="selected"' : '' ; ?>><?php echo $title_row->setting_value; ?></option>
                                <?php } ?>
                            </select>
                        <?php } else { ?>
                            <div>Please set your Number of Titles options in <a href="<?php echo base_url('webapp/settings/module/2'); ?>" target="_blank" >Site Settings!</a></div>
                        <?php } ?>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12 input-container">
                        <label>Number of Films Per Month</label>
                        <?php if (!empty($films_per_month)) { ?>
                            <select id="films_per_month_selection" class="input-field" style="width:100%" >
                                <?php foreach ($films_per_month as $fpm_number) { ?>
                                    <option value="<?php echo $fpm_number->setting_id; ?>" data-films_per_month="<?php echo $fpm_number->setting_value; ?>" ><?php echo $fpm_number->setting_value; ?></option>
                                <?php } ?>
                            </select>
                        <?php } else { ?>
                            <div>Please set your Number of Films Per month options in <a href="<?php echo base_url('webapp/settings/module/2'); ?>" target="_blank" >Site Settings!</a></div>
                        <?php } ?>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top:5px;" >
                        <button id="add_combination_btn" class="add-combination">Add This Combination</button>
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12" >
                        <div id="provider-packages" class="col-md-12 col-sm-12 col-xs-12" style="margin-top:10px; background: #f7f7f7" >
                            <hr>
                        </div>
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top:5px;" >
                        <button id="add_providers_btn" class="btn-sm btn-block btn-flow btn-success ">Add</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$( document ).ready( function(){

    var distributionGroupId = "<?php echo $distribution_group_details->distribution_group_id; ?>";

    //Add New Bundle Trigger
    $( '#add-distribution-provider' ).click(function(){
        $( '.add-distribution-provider-modal' ).modal( 'show' );
    });


    //Add Combinations
    $( '#add_combination_btn' ).click( function( e ){

        e.preventDefault();

        var noTitlesId   = $( '#no_of_titles_selection option:selected' ).val();
            noOfTitles   = $( '#no_of_titles_selection option:selected' ).data( 'no_of_titles' );

        var providerId   = $( '#provider_selection option:selected' ).val(),
            providerName = $( '#provider_selection option:selected' ).data( 'provider_name' );
            if( providerName ){
                providerName = providerName.replace( /[^a-z0-9\s]/gi, "" );
                providerClass= providerName+providerId+noTitlesId;
            }

        if( providerId.length == 0 || providerId === undefined ){
            swal({
                type: 'error',
                text: 'Please select a valid Provider!',
            });
            return false;
        }

        if( noTitlesId.length == 0 || noTitlesId === undefined ){
            swal({
                type: 'error',
                text: 'Please select a the no of titles!',
            });
            return false;
        }

        var filmsPerMonthId  = $( '#films_per_month_selection option:selected' ).val(),
            filmsPerMonth    = $( '#films_per_month_selection option:selected' ).data( 'films_per_month' );

        var providersContainer = $( '#provider-packages' );

        if ( $('#provider-'+providerClass ).length === 0 ) {
            var uniqueRef       = providerId+noTitlesId;
            var elementCloner   = '<div id="provider-'+providerClass+'" class="provider-'+providerClass+'" >';

                    elementCloner += '<input class="dynamic-fields" type="hidden" name="provider_details['+uniqueRef+'][provider_id]" value="'+providerId+'" >';
                    elementCloner += '<input class="dynamic-fields" type="hidden" name="provider_details['+uniqueRef+'][provider_name]" value="'+providerName+'" >';
                    elementCloner += '<input class="dynamic-fields" type="hidden" name="provider_details['+uniqueRef+'][no_of_titles_id]" value="'+noTitlesId+'" >';
                    elementCloner += '<input class="dynamic-fields" type="hidden" name="provider_details['+uniqueRef+'][no_of_titles]" value="'+noOfTitles+'" >';
                    elementCloner += '<input class="dynamic-fields" type="hidden" name="provider_details['+uniqueRef+'][films_per_month_id]" value="'+filmsPerMonthId+'" >';
                    elementCloner += '<input class="dynamic-fields" type="hidden" name="provider_details['+uniqueRef+'][films_per_month]" value="'+filmsPerMonth+'" >';

                    elementCloner += '<div class="col-md-4 col-sm-4 col-xs-12">';
                        elementCloner += '<div>'+providerName+'</div>';
                    elementCloner += '</div>';

                    elementCloner += '<div class="col-md-3 col-sm-3 col-xs-12">';
                        elementCloner += '<div>'+noOfTitles+'</div>';
                    elementCloner += '</div>';

                    elementCloner += '<div class="col-md-3 col-sm-3 col-xs-12">';
                        elementCloner += '<div>'+filmsPerMonth+'</div>';
                    elementCloner += '</div>';


                    elementCloner += '<div class="col-md-2 col-sm-2 col-xs-12 pointer remove-provider" data-provider_id="'+uniqueRef+'" >';
                        elementCloner += '<span class="text-red text-bold pull-right" title="Remove this item" >X</span>';
                    elementCloner += '</div>';

                    elementCloner += '<div class="col-md-12 col-sm-12 col-xs-12">&nbsp;</div>';
                elementCloner += '</div>';

                $( providersContainer ).append( elementCloner );

        } else {
            swal({
                type: 'error',
                text: 'This Combination is already selected, please create a new combination!',
            });
            return false;
        }

    });

    $( '#provider-packages' ).on( 'click', '.remove-provider', function( e ){
        e.preventDefault();
        $( this ).parent( 'div' ).remove();
    });

    //ADD DISTRIBUTION GROUP Providers
    $( '#add_providers_btn' ).click( function(){

        if( $( '#provider-packages .dynamic-fields' ).length ){

            var distroElement = '<input class="dynamic-fields" type="hidden" name="page" value="details" >';;
                distroElement += '<input class="dynamic-fields" type="hidden" name="distribution_group_id" value="'+distributionGroupId+'" >';;

            $( '#provider-packages' ).prepend( distroElement );

            var formData = $( '#provider-packages .dynamic-fields' ).serialize();

            $.ajax({
                url:"<?php echo base_url('webapp/distribution/add_distribution_group_provider/'); ?>",
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
                        }, 3000 );
                    } else {
                        swal({
                            type: 'error',
                            title: data.status_msg
                        })
                    }
                }
            });

        } else {
            swal({
                type: 'error',
                text: 'Please add at-least one Provider and number of Titles!',
            });
            return false;
        }
    });

    $( '.remove-distro-provider' ).click( function(){
        var comboId = $( this ).data( 'combination_id' );
        swal({
            title: 'Confirm Delete Distribution Provider?',
            showCancelButton: true,
            confirmButtonColor: '#5CB85C',
            cancelButtonColor: '#9D1919',
            confirmButtonText: 'Yes'
        }).then( function (result) {
            if ( result.value ) {
                $.ajax({
                    url:"<?php echo base_url('webapp/distribution/delete_distribution_group_provider/'); ?>"+comboId,
                    method:"POST",
                    data: { combination_id: comboId },
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

    $( '.no-edit' ).click( function(){
        swal({
            text: 'Edit not permitted'
        });
    });

    //** Validate any inputs that have the required class, if empty return the name attribute **/
    function check_inputs( currentpanel ){
        var result = false;
        var panel = "." + currentpanel;

        $( $( panel + " .required" ).get().reverse() ).each( function(){
            var fieldName = '';
            var inputValue = $( this ).val();
            if( ( inputValue == false ) || ( inputValue == '' ) || ( inputValue.length == 0 ) ){
                fieldName = $(this).attr( 'name' );
                result = fieldName;
                return result;
            }
        });
        return result;
    }

    $( ".btn-back" ).click( function(){
        var currentpanel = $( this ).data( "currentpanel" );
        go_back( "." + currentpanel )
        return false;
    });

    function panelchange( changefrom, elementClass ){
        var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
        var changeto = elementClass + panelnumber;
        $( changefrom ).hide( "slide", {direction : 'left'}, 500 );
        $( changeto ).delay( 600 ).show( "slide", {direction : 'right'},500 );
        return false;
    }

    function go_back( changefrom ){
        var panelnumber = parseInt( changefrom.match(/\d+/) ) - parseInt( 1 );
        var elementClass = changefrom.substr( 0, parseInt( changefrom.length ) - parseInt( panelnumber.toString().length ) );
        var changeto = elementClass + panelnumber;
        $( changefrom ).hide( "slide", {direction : 'right'}, 500 );
        $( changeto ).delay( 600 ).show( "slide", {direction : 'left'},500 );
        return false;
    }

    $( ".delete-distribution_group-btn" ).click( function(){
        swal({
            title: 'Confirm Delete Distribution Group?',
            text: 'This will unlink any linked Sites',
            showCancelButton: true,
            confirmButtonColor: '#5CB85C',
            cancelButtonColor: '#9D1919',
            confirmButtonText: 'Yes'
        }).then( function (result) {
            if ( result.value ) {
                var cont_id = <?php echo (!empty($distribution_group_details->distribution_group_id)) ? $distribution_group_details->distribution_group_id : '' ; ?>;
                if( parseInt( cont_id ) < 0 ){
                    swal({
                        title: 'Conent ID is required',
                        type: 'error',
                    })
                    return false;
                }

                $.ajax({
                    url:"<?php echo base_url('webapp/distribution/delete_distribution_group/') . ((!empty($distribution_group_details->distribution_group_id)) ? $distribution_group_details->distribution_group_id : ''); ?>",
                    method:"POST",
                    data: { distribution_group_id: cont_id },
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
                                location.href ="<?php echo base_url('webapp/distribution/distributions'); ?>";
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

    $( ".legend" ).click( function( e ){
        var target = $( e.target );

        if( target.hasClass( "fa-trash-alt" ) ){
            console.log( "Delete movie file" );
            return;
        } else {
            $( this ).children( ".fa-caret-down, .fa-caret-up" ).toggleClass( "fa-caret-down fa-caret-up" );
            $( this ).next( ".group-content" ).slideToggle( 400 );
        }
    });

    $( '.update-distribution_group-btn' ).click( function( e ){

        e.preventDefault();

        var section     = $( this ).data( "distribution_details" );
        //var formData  = $( "." + section + "_form input," + "." + section + " select," + "." + section + " textarea" ).serialize();
        var formData    = $( this ).closest( 'form' ).serialize();

        swal({
            title: 'Confirm distribution group details update?',
            showCancelButton: true,
            confirmButtonColor: '#5CB85C',
            cancelButtonColor: '#9D1919',
            confirmButtonText: 'Yes'
        }).then( function( result ) {
            if ( result.value ) {
                $.ajax({
                    url:"<?php echo base_url('webapp/distribution/update_distribution_group/') . ((!empty($distribution_group_details->distribution_group_id)) ? $distribution_group_details->distribution_group_id : ''); ?>",
                    method: "POST",
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
                                location.reload();
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

    $(function() {
        $('input[type=file]').change(function(){
            var t = $(this).val();
            var labelText = 'File : ' + t.substr(12, t.length);
            $(this).prev('label').text(labelText);
        })
    });

    $('.file-toggle').click(function(){
        var classGrp = $(this).data( 'class_grp' );
        $( '.'+classGrp ).slideToggle();
    });
});
</script>