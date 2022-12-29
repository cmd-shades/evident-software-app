<style>
    .linked-sites{
        color: #000000;
    }
</style>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 content-profile-details">
    <form class="distribution_details_form" id="update-content-form" method="post" >
        <input type="hidden" name="page" value="details" />
        <div class="x_panel tile group-container content">
            <input type="hidden" name="distribution_group_id" value="<?php echo $distribution_bundle_details->distribution_group_id; ?>" />
            <input type="hidden" name="distribution_bundle_id" value="<?php echo (!empty($distribution_bundle_details->distribution_bundle_id)) ? $distribution_bundle_details->distribution_bundle_id : '' ; ?>" />
            <h4 class="legend pointer"><i class="fas fa-caret-down"></i>Distribution Bundle Details</h4>
            <div class="row group-content">
                <div class="row">
                    
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 input-container">
                                <label class="input-label">Distribution Bundle</label>
                                <input name="distribution_bundle"  class="input-field" type="text" placeholder="Distribution Bundle" value="<?php echo !empty($distribution_bundle_details->distribution_bundle) ? $distribution_bundle_details->distribution_bundle : '' ; ?>" />
                            </div>
                        
                            <div class="col-md-12 col-sm-12 col-xs-12 input-container">
                                <label class="input-label">System Integrator</label>
                                <select id="system_integrator_id"    name="system_integrator_id" class="input-field" data-label_text="System Integrator" disabled >
                                    <option value="" >Select Integrator</option>
                                    <?php if (!empty($system_integrators)) {
                                        foreach ($system_integrators as $k => $integrator) { ?>
                                        <option value="<?php echo $integrator->system_integrator_id; ?>" <?php echo (!empty($distribution_bundle_details->system_integrator_id) && ($distribution_bundle_details->system_integrator_id == $integrator->system_integrator_id)) ? 'selected="selected"' : "" ; ?> ><?php echo ucwords($integrator->integrator_name); ?></option>
                                        <?php }
                                        } ?>
                                </select>
                            </div>
                            
                            <div class="col-md-12 col-sm-12 col-xs-12 input-container">
                                <label class="input-label">Associated Territory</label>
                                <?php if (!empty($territories)) { ?>
                                    <select id="associated_territory_id" name="associated_territory_id" class="input-field" data-label_text="Linked Territory" disabled >
                                        <option value="" >Search / Select Territory</option>
                                        <?php if (!empty($territories)) {
                                            foreach ($territories as $k => $territory) { ?>
                                            <option value="<?php echo $territory->territory_id; ?>" <?php echo (!empty($distribution_bundle_details->associated_territory_id) && ($distribution_bundle_details->associated_territory_id == $territory->territory_id)) ? 'selected="selected"' : "" ; ?> ><?php echo ucwords($territory->country . ' - ' . $territory->code); ?></option>
                                            <?php }
                                            } ?>
                                    </select>
                                <?php } else { ?>
                                    <input class="input-field" name="associated_territory_id" type="text" placeholder="Content Provider ID" value="<?php echo !empty($distribution_bundle_details->associated_territory_id) ? $distribution_bundle_details->associated_territory_id : '' ; ?>" />
                                <?php } ?>
                            </div>
                            
                            <div class="col-md-12 col-sm-12 col-xs-12 input-container">
                                <label class="input-label">No of Titles</label>
                                <select id="no_of_titles_id" name="no_of_titles_id" class="input-field" data-label_text="No Of Titles" disabled >
                                    <option value="" >Search / Select Territory</option>
                                    <?php if (!empty($no_of_titles_packages)) {
                                        foreach ($no_of_titles_packages as $k => $title_row) { ?>
                                        <option value="<?php echo $title_row->setting_id; ?>" <?php echo (!empty($distribution_bundle_details->no_of_titles_id) && ($distribution_bundle_details->no_of_titles_id == $title_row->setting_id)) ? 'selected="selected"' : "" ; ?> ><?php echo ucwords($title_row->setting_value); ?></option>
                                        <?php }
                                        } ?>
                                </select>
                            </div>
                            
                            <?php if ($this->user->is_admin || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
                                <div class="rows">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <button class="update-distribution_bundle-btn btn btn-block btn-update btn-primary" type="button" data-distribution_bundle_section="distribution_details">Update</button>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <button class="delete-distribution_bundle-btn btn btn-block btn-update btn-danger" type="button" data-distribution_bundle_section="distribution_details">Delete</button>
                                            </div>
                                        </div>
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
                        <div class="linked-sites" >
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <h4>Linked Sites</h4>
                            </div>
                            <?php if (!empty($available_sites)) {
                                foreach ($available_sites as $site) { ?>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <label for="site-<?php echo(strtolower(trim($site->site_id))) ?>">
                                    <input type="checkbox" <?php echo (in_array($site->site_id, $linked_sites)) ? 'checked' : ''; ?> name="linked_sites[]" id="site-<?php echo(strtolower(trim($site->site_id))) ?>" value="<?php echo trim($site->site_id); ?>" class="site-records" /> <span class="site_name"><?php echo ucwords(trim($site->site_name)); ?></span></label>
                                </div>
                                <?php }
                                } ?>
                        </div>
                    </div>
                    
                </div>
                
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
$( document ).ready( function(){
    
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

    $( ".delete-distribution_bundle-btn" ).click( function(){
        swal({
            title: 'Confirm Delete Distribution Bundle?',
            text: 'This will unline any linked Sites',
            showCancelButton: true,
            confirmButtonColor: '#5CB85C',
            cancelButtonColor: '#9D1919',
            confirmButtonText: 'Yes'
        }).then( function (result) {
            if ( result.value ) {
                var cont_id = <?php echo (!empty($distribution_bundle_details->distribution_bundle_id)) ? $distribution_bundle_details->distribution_bundle_id : '' ; ?>;
                if( parseInt( cont_id ) < 0 ){
                    swal({
                        title: 'Conent ID is required',
                        type: 'error',
                    })
                    return false;
                }

                $.ajax({
                    url:"<?php echo base_url('webapp/distribution/delete_distribution_bundle/') . ((!empty($distribution_bundle_details->distribution_bundle_id)) ? $distribution_bundle_details->distribution_bundle_id : ''); ?>",
                    method:"POST",
                    data: { distribution_bundle_id: cont_id },
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

    $( '.update-distribution_bundle-btn' ).click( function( e ){
        
        e.preventDefault();

        var section     = $( this ).data( "distribution_details" );
        //var formData  = $( "." + section + "_form input," + "." + section + " select," + "." + section + " textarea" ).serialize();
        var formData    = $( this ).closest( 'form' ).serialize();

        swal({
            title: 'Confirm distribution_bundle update?',
            showCancelButton: true,
            confirmButtonColor: '#5CB85C',
            cancelButtonColor: '#9D1919',
            confirmButtonText: 'Yes'
        }).then( function( result ) {
            if ( result.value ) {
                $.ajax({
                    url:"<?php echo base_url('webapp/distribution/update_distribution_bundle/') . ((!empty($distribution_bundle_details->distribution_bundle_id)) ? $distribution_bundle_details->distribution_bundle_id : ''); ?>",
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