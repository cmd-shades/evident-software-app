<div id="add-new-distribution_group">
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="left-container">
            <div class="rows">
                <h2>Add Distribution Group</h2>
            </div>
            <div class="rows">
                <div class="step-name-wrapper current" >
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="step-name">Group Details</div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="step-status"><span class="tick_box el-hidden"><i class="fas fa-check"></i></span><span class="x-cross el-hidden"><i class="fas fa-times"></i></span></div>
                        </div>
                    </div>
                </div>
                
                <div class="step-name-wrapper" data-group-name="System Integrator">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="step-name">System Integrator</div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="step-status"><span class="tick_box el-hidden"><i class="fas fa-check"></i></span><span class="x-cross el-hidden"><i class="fas fa-times"></i></span></div>
                        </div>
                    </div>
                </div>

                <div class="step-name-wrapper" data-group-name="Associated Territory">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="step-name">Associated Territory</div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="step-status"><span class="tick_box el-hidden"><i class="fas fa-check"></i></span><span class="x-cross el-hidden"><i class="fas fa-times"></i></span></div>
                        </div>
                    </div>
                </div>

                <div class="step-name-wrapper" data-group-name="Provider Details">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="step-name">Provider Details</div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="step-status"><span class="tick_box el-hidden"><i class="fas fa-check"></i></span><span class="x-cross el-hidden"><i class="fas fa-times"></i></span></div>
                        </div>
                    </div>
                </div>

                <div class="step-name-wrapper" data-group-name="Applicable Sites">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="step-name">Applicable Sites</div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="step-status"><span class="tick_box el-hidden"><i class="fas fa-check"></i></span><span class="x-cross el-hidden"><i class="fas fa-times"></i></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- // Left container - END -->

    <div class="col-lg-8 col-md-8 col-sm-6 col-xs-12"> <!-- // Right container -->
        <div class="right-container" style="margin-top:60px;">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-1 col-md-8 col-md-offset-8 col-sm-8 col-sm-offset-2 col-xs-12 col-xs-offset-0">
                    <form id="distribution_group-creation-form" >
                        <div class="row">
                            <div class="distribution_group_creation_panel1 col-md-8 col-sm-12 col-xs-12" data-panel-index = "0">
                                <div class="slide-group">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <legend class="legend-header">What's the Distribution Group name?</legend>
                                            <input name="distribution_group" class="form-control required" type="text" value="" placeholder="Distribution Group" title="Distribution Group" />
                                        </div>
                                    </div>
                                    <br />
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <legend class="legend-header">Distribution Group Description</legend>
                                            <textarea name="distribution_group_desc" class="form-control" type="text" value="" placeholder="Distribution Group Description" title="Distribution Group Description" ></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-lg-6 col-lg-offset-6 col-md-6 col-md-offset-6 col-sm-6 col-sm-offset-6 col-xs-12">
                                            <button class="btn-block btn-next distribution_group-creation-steps" data-currentpanel="distribution_group_creation_panel1" type="button">Next</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="distribution_group_creation_panel2 col-md-8 col-sm-12 col-xs-12 el-hidden" data-panel-index = "1">
                                <div class="slide-group">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <legend class="legend-header">Please select the System Integrator</legend>
                                            <select id="system_integrator_id" name="system_integrator_id" class="form-control required" style="width:100%; margin-bottom:10px; background-color:none" data-label_text="System Integrator" >
                                                <option value="" >Select Integrator</option>
                                                <?php if (!empty($system_integrators)) {
                                                    foreach ($system_integrators as $k => $integrator) { ?>
                                                    <option value="<?php echo $integrator->system_integrator_id; ?>" ><?php echo ucwords($integrator->integrator_name); ?></option>
                                                    <?php }
                                                    } ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <legend class="legend-header">Distribution Server</legend>
                                            <?php if (!empty($distribution_servers)) { ?>
                                                <select id="delivery_point_selection" name="delivery_point_id" class="form-control" style="width:100%" >
                                                    <option value="" >Select Distribution Server</option>
                                                    <?php foreach ($distribution_servers as $server) { ?>
                                                        <option value="<?php echo $server->server_id; ?>" <?php echo (!empty($distribution_group_details->delivery_point_id) && ($distribution_group_details->delivery_point_id == $server->server_id)) ? 'selected="selected"' : "" ; ?> ><?php echo $server->server_name; ?> <?php echo (!empty($server->coggins_running)) ? '(' . (ucwords($server->coggins_running)) . ')' : '' ; ?></option>
                                                    <?php } ?>
                                                </select>
                                            <?php } else { ?>
                                                <div>Please set your Distribution Server options in <a href="<?php echo base_url('webapp/settings/module/2'); ?>" target="_blank" >Site Settings!</a></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button class="btn-block btn-back" data-currentpanel="distribution_group_creation_panel2" type="button">Back</button>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button class="btn-block btn-next fetch-territories" data-currentpanel="distribution_group_creation_panel2" type="button">Next</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="distribution_group_creation_panel3 col-md-8 col-sm-12 col-xs-12 el-hidden" data-panel-index = "1">
                                <div class="slide-group">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <legend class="legend-header">What is the Integrator Territory for this Group?</legend>
                                            <div id="integrator_territories">
                                                <select id="associated_territory_id" name="associated_territory_id" class="form-control required" style="width:100%; margin-bottom:10px; background-color:none" data-label_text="Linked Territory" >
                                                    <option value="" >Search / Select Territory</option>
                                                    <?php if (!empty($territories)) {
                                                        foreach ($territories as $k => $territory) { ?>
                                                        <option value="<?php echo $territory->territory_id; ?>" ><?php echo ucwords($territory->country . ' - ' . $territory->code); ?></option>
                                                        <?php }
                                                        } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button class="btn-block btn-back" data-currentpanel="distribution_group_creation_panel3" type="button">Back</button>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button class="btn-block btn-next fetch-content-providers" data-currentpanel="distribution_group_creation_panel3" type="button">Next</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="distribution_group_creation_panel4 col-md-8 col-sm-12 col-xs-12 el-hidden" data-panel-index = "2">
                                <div class="slide-group">
                                
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <legend class="legend-header">Please select Content Provider</legend>
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
                                    
                                        <div class="provider-suppliment" style="display:none">
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <legend class="legend-header">Number of Titles</legend>
                                                <?php if (!empty($no_of_titles_packages)) { ?>
                                                    <select id="no_of_titles_selection" name="no_of_titles_id" class="input-field" style="width:100%" >
                                                        <option value="">Please select Number of Titles</option>
                                                        <?php foreach ($no_of_titles_packages as $title_row) { ?>
                                                            <option value="<?php echo $title_row->setting_id; ?>" data-no_of_titles="<?php echo $title_row->setting_value; ?>" ><?php echo $title_row->setting_value; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } else { ?>
                                                    <div>Please set your Number of Titles options in <a href="<?php echo base_url('webapp/settings/module/2'); ?>" target="_blank" >Site Settings!</a></div>
                                                <?php } ?>
                                            </div>
                                            
                                            <div class="col-md-12 col-sm-12 col-xs-12 input-container">
                                                <legend class="legend-header">Number of Films Per Month</legend>
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
                                                <button id="add_provider_btn" class="add-combination">Add This Combination</button>
                                            </div>
                                        
                                        </div>
                                        
                                    </div>
                                    <div id="provider-packages" class="col-md-12 col-sm-12 col-xs-12" style="margin-top:10px; background: #f7f7f7" >
                                        <input class="dynamic-fields" type="hidden" name="system_integrator_id" value="" />
                                        <hr>
                                    </div>
                                
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12" style="display:none">
                                            <h6 class="error_message pull-right" id="distribution_group_creation_panel4-errors"></h6>
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button class="btn-block btn-back" data-currentpanel="distribution_group_creation_panel4" type="button">Back</button>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button class="fetch-applicable-sites btn-block btn-next" data-currentpanel="distribution_group_creation_panel4" type="button">Next</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="distribution_group_creation_panel5 col-md-8 col-sm-12 col-xs-12 el-hidden"  data-panel-index = "3">
                                <div class="slide-group">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <legend class="legend-header">Please select applicable Sites</legend>
                                        </div>
                                        <div class="hide col-md-12 col-sm-12 col-xs-12">
                                            <h6 class="error_message pull-right" id="distribution_group_creation_panel5-errors"></h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div id="filtered_sites"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button class="btn-block btn-back" data-currentpanel="distribution_group_creation_panel5" type="button">Back</button>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button id="create-distribution_group-btn" class="btn-block btn-flow btn-next" type="button" data-currentpanel="distribution_group_creation_panel5"  >Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- // Right container - END -->
</div>


<script type="text/javascript">

    $( document ).ready( function(){
        
        $( '#add_provider_btn' ).click( function( e ){
            
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
        
        //FECTH APPLICABLE SITES
        $( '.fetch-applicable-sites' ).click( function(){
            
            var currentpanel            = $( this ).data( "currentpanel" );
            var associatedTerritoryId   = $( '#associated_territory_id option:selected' ).val();
            
            if( associatedTerritoryId.length == 0 ){
                $( '[name="associated_territory_id"]' ).focus().css("border","1px solid red");
                swal({
                    type: 'error',
                    text: 'Associated Territory is required'
                })
                return false;
            }
            
            if( $( '#provider-packages .dynamic-fields' ).length ){
                
                var territoryElement = '<input class="dynamic-fields" type="hidden" name="associated_territory_id" value="'+associatedTerritoryId+'" >';;
                
                $( '#provider-packages' ).append( territoryElement );
                
                var formData     = $( '#provider-packages .dynamic-fields' ).serialize();
                
                $.ajax({
                    url:"<?php echo base_url('webapp/distribution/fetch_sites/'); ?>",
                    method: "POST",
                    data: formData,
                    dataType: 'json',
                    success:function( data ){
                        if( data.status == 1 && ( data.site_records !== '' ) ){
                            $( "#filtered_sites" ).html( data.site_records );
                        } else {
                            $( "#filtered_sites" ).html( '<div class="col-md-12 col-sm-6 col-xs-12">There is currently no sites matching your criteria!</div><br/>' );
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
            
            panelchange("."+currentpanel);
            
        });
        
        $( '#provider-packages' ).on( 'click', '.remove-provider', function( e ){
            
            e.preventDefault();
            $( this ).parent( 'div' ).remove();
            
        });
        
        $( '#associated_territory_id' ).select2({});
        
        $( '#system_integrator_id' ).on( 'change', function(){
            $( '#provider-packages input[name="system_integrator_id"]' ).val( $( this ).val() );
        });
        
        
        $( '.fetch-territories' ).click( function(){
            
            var currentpanel    = $( this ).data( "currentpanel" );
            var integratorId    = $( '#system_integrator_id option:selected' ).val();
            
            if( integratorId.length == 0 ){
                $( '[name="system_integrator_id"]' ).focus().css("border","1px solid red");
                swal({
                    type: 'error',
                    text: 'System Integrator is required'
                })
                return false;
            }
            
            $.ajax({
                url:"<?php echo base_url('webapp/distribution/fetch_integrator_territories/'); ?>",
                method: "POST",
                data: {integrator_id: integratorId},
                dataType: 'json',
                success:function( data ){
                    console.log(data);
                    if( data.status == 1 && ( data.integrator_territories !== '' ) ){
                        $( "#integrator_territories" ).html( '' );
                        $( "#integrator_territories" ).html( data.integrator_territories );
                    } else {
                        $( "#integrator_territories" ).html( '<div class="col-md-12 col-sm-6 col-xs-12">There is currently no Territories matching your criteria!</div><br/>' );
                    }
                }
            });
            
            panelchange("."+currentpanel);
            
        });
        
        //FECTH CONTENT PROVIDERS
        $( '.fetch-content-providers' ).click( function(){
            
            var currentpanel            = $( this ).data( "currentpanel" );
            var associatedTerritoryId   = $( '#associated_territory_id option:selected' ).val();
            var integratorId            = $( '#system_integrator_id option:selected' ).val();

            if( integratorId.length == 0 ){
                $( '[name="system_integrator_id"]' ).focus().css("border","1px solid red");
                swal({
                    type: 'error',
                    text: 'System Integrator is required'
                })
                return false;
            }
            
            if( associatedTerritoryId.length == 0 ){
                $( '[name="associated_territory_id"]' ).focus().css("border","1px solid red");
                swal({
                    type: 'error',
                    text: 'Associated Territory is required'
                })
                return false;
            }
            
            $.ajax({
                url:"<?php echo base_url('webapp/distribution/fetch_content_providers/'); ?>",
                method: "POST",
                data: {integrator_id: integratorId, territory_id: associatedTerritoryId},
                dataType: 'json',
                success:function( data ){
                    if( data.status == 1 && ( data.content_providers !== '' ) ){
                        $( '.provider-suppliment' ).show();
                        $( "#content_providers" ).html( '' );
                        $( "#content_providers" ).html( data.content_providers );
                    } else {
                        $( '.provider-suppliment' ).hide();
                        $( "#content_providers" ).html( '<div class="col-md-12 col-sm-6 col-xs-12">There is currently no Providers matching your criteria!</div><br/>' );
                    }
                }
            });

            panelchange("."+currentpanel);
            
        });

    
        //FETCH SITE (OLD)
        /* $( '#no_of_titles_id' ).change( function(){
            
            var noOfTitlesId            = $( 'option:selected', this ).val();
            var associatedTerritoryId   = $( '#associated_territory_id option:selected' ).val();
            
            if( associatedTerritoryId.length == 0 || associatedTerritoryId == 'undefined' ){
                swal({
                    type: 'error',
                    text: 'Please select a valid Territory'
                });
                return false;
            }
            
            if( noOfTitlesId.length > 0 && noOfTitlesId !== undefined ){
                
                $.ajax({
                    url:"<?php echo base_url('webapp/distribution/fetch_sites/'); ?>",
                    method: "POST",
                    data: { no_of_titles_id: noOfTitlesId, associated_territory_id:associatedTerritoryId },
                    dataType: 'json',
                    success:function( data ){
                        if( data.status == 1 && ( data.site_records !== '' ) ){
                            $( "#filtered_sites" ).html( data.site_records );
                        } else {
                            $( "#filtered_sites" ).html( '<div class="col-md-12 col-sm-6 col-xs-12">There is currently no sites matching your criteria!</div><br/>' );
                        }
                    }
                });
                
            }
            
        }); 
    
        $( '.fetch-sites' ).click( function(){

            var currentpanel            = $( this ).data( "currentpanel" );
            var noOfTitlesId            = $( '#no_of_titles_id option:selected' ).val();
            var associatedTerritoryId   = $( '#associated_territory_id option:selected' ).val();
            
            if( associatedTerritoryId.length == 0 ){
                $( '[name="associated_territory_id"]' ).focus().css("border","1px solid red");
                swal({
                    type: 'error',
                    text: 'Associated Territory is required'
                })
                return false;
            }
            
            if( noOfTitlesId.length == 0 ){
                $( '[name="no_of_titles_id"]' ).focus().css("border","1px solid red");
                swal({
                    type: 'error',
                    text: 'Number Of Titles is required'
                })
                return false;
            }
            
            $.ajax({
                url:"<?php echo base_url('webapp/distribution/fetch_sites/'); ?>",
                method: "POST",
                data: { no_of_titles_id: noOfTitlesId, associated_territory_id:associatedTerritoryId },
                dataType: 'json',
                success:function( data ){
                    if( data.status == 1 && ( data.site_records !== '' ) ){
                        $( "#filtered_sites" ).html( data.site_records );
                        panelchange("."+currentpanel);  
                    }
                }
            });
            
        });*/
        
        //TICK/UN-TICK ALL
        $( "#filtered_sites" ).on( "click", "#tick_all_sites", function(){
            if( $( this ).prop( "checked" ) != true ){
                $( "#filtered_sites input[type='checkbox']" ).each( 
                    function(){ $( this ).prop( "checked", false ) }
                )
            } else {
                $( "#filtered_sites input[type='checkbox']" ).each( 
                    function(){ $( this ).prop( "checked", true ) }
                )
            }
        });
        
        //Submit distribution_group form
        $( '#create-distribution_group-btn' ).click( function( e ){
            
            e.preventDefault();
            
            var formData     = $( '#distribution_group-creation-form' ).serialize();
            var checKedSites = $( "#filtered_sites input[type='checkbox']:checked" ).length;

            /*if ( checKedSites < 1 ){
                swal({
                    type: 'error',
                    text: 'Please tick at-least 1 Site to proceed!'
                })
                return false;
            }*/

            swal({
                title: 'Confirm new Distribution Group creation?',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function ( result ) {
                if ( result.value ) {
                    $.ajax({
                        url:"<?php echo base_url('webapp/distribution/create_distribution_group/'); ?>",
                        method:"POST",
                        data:formData,
                        dataType: 'json',
                        success:function( data ){
                            if( data.status == 1 && ( data.distribution_group !== '' ) ){

                                var distribution_groupId = data.distribution_group.distribution_group_id;

                                swal({
                                    type: 'success',
                                    title: data.status_msg,
                                    showConfirmButton: false,
                                    timer: 3000
                                })
                                window.setTimeout( function(){
                                    location.href = "<?php echo base_url('webapp/distribution/profile/'); ?>"+distribution_groupId;
                                }, 3000 );
                            }else{
                                swal({
                                    type: 'error',
                                    title: data.status_msg
                                })
                            }
                        }
                    });
                } else {
                    var currentpanel    = $("."+$(this).data( "currentpanel" ));
                    var panelnumber     = parseInt( currentpanel.match(/\d+/) )+parseInt(1);                    
                    $( ".distribution_group_creation_panel"+panelnumber ).hide( "slide", { direction : 'left' }, 500 );
                    go_back( ".distribution_group_creation_panel2" );
                    return false;
                }
            }).catch( swal.noop )
        });

        $( ".distribution_group-creation-steps" ).click( function(){
            $( '.error_message' ).each( function(){
                $( this ).text( '' );
            });

            var currentpanel = $(this).data( "currentpanel" );
            var inputs_state = check_inputs( currentpanel );
            if( inputs_state ){
                //If name attribute returned, auto focus to the field and display arror message
                $( '[name="'+inputs_state+'"]' ).focus();
                var labelText = $( '[name="'+inputs_state+'"]' ).parent().find( 'label' ).text();
                $( '#'+currentpanel+'-errors' ).text( ucwords( labelText ) +' is a requirement' );
                return false;
            }

            panelchange( "."+currentpanel )

            return false;
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

    $(".btn-next").click(function() {

        var currentpanel = $("."+$(this).data( "currentpanel" ));
        prev_group_is_valid = true;

        currentpanel.find("input").each(function(i, input_element) {
            if ($(input_element).hasClass("required")) {
                if ($(input_element).val() == "") {
                    prev_group_is_valid = false;
                }
            }
        });

        current_panel_id = $("."+$(this).data( "currentpanel" )).attr("data-panel-index")

        if(prev_group_is_valid){

            $($(".tick_box")[current_panel_id]).removeClass( "el-hidden" )
            $($(".x-cross")[current_panel_id]).addClass( "el-hidden" )
        } else {
            $($(".x-cross")[current_panel_id]).removeClass( "el-hidden" )
            $($(".tick_box")[current_panel_id]).addClass( "el-hidden" )
        }

    });

    $( ".btn-back" ).click( function(){
        var currentpanel = $( this ).data( "currentpanel" );
        go_back( "."+currentpanel )
        return false;
    });

    function panelchange( changefrom ){
        var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
        var changeto = ".distribution_group_creation_panel"+panelnumber;
        $( changefrom ).hide( "slide", {direction : 'left'}, 500 );
        $( changeto ).delay( 600 ).show( "slide", {direction : 'right'},500 );
        return false;
    }

    function go_back( changefrom ){
        var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
        var changeto = ".distribution_group_creation_panel"+panelnumber;
        $( changefrom ).hide( "slide", {direction : 'right'}, 500 );
        $( changeto ).delay( 600 ).show( "slide", {direction : 'left'},500 );
        return false;
    }
</script>