<div id="add-new-content" class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="left-container"> <!-- // Left container -->
            <div class="row">
                <h1>Add Content</h1>
            </div>
            <div class="row">
                <div class="step-name-wrapper current" data-group-name="Content Provider">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="step-name">Content Provider</div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="step-status"><span class="tick_box el-hidden"><i class="fas fa-check"></i></span><span class="x-cross el-hidden"><i class="fas fa-times"></i></span></div>
                        </div>
                    </div>
                </div>

                <div class="step-name-wrapper" data-group-name="Content Details - IMDb">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="step-name">Content Details - 1</div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="step-status"><span class="tick_box el-hidden"><i class="fas fa-check"></i></span><span class="x-cross el-hidden"><i class="fas fa-times"></i></span></div>
                        </div>
                    </div>
                </div>

                <div class="step-name-wrapper" data-group-name="Content Details - 2">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="step-name">Content Details - 2</div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="step-status"><span class="tick_box el-hidden"><i class="fas fa-check"></i></span><span class="x-cross el-hidden"><i class="fas fa-times"></i></span></div>
                        </div>
                    </div>
                </div>

                <div class="step-name-wrapper" data-group-name="Content Details - 3">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="step-name">Content Details - 3</div>
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

    <div class="col-lg-9 col-md-9 col-sm-6 col-xs-12"> <!-- // Right container -->
        <div class="right-container">
            <div class="row">
                <div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12 col-xs-offset-0">
                    <form id="content-creation-form" >
                        <div class="row">
                            <div class="content_creation_panel1 col-md-6 col-sm-12 col-xs-12" data-panel-index = "0">
                                <div class="slide-group">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <legend class="legend-header">What's the content provider?</legend>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <h6 class="error_message pull-right" id="content_creation_panel1-errors"></h6>
                                        </div>
                                    </div>

                                    <div class="input-group form-group container-full">
                                        <label class="input-group-addon el-hidden">Content provider</label>
                                        <?php
                                        if (!empty($content_providers)) { ?>
                                            <select name="content_provider_id" class="form-control required" title="Content provider">
                                                <option value="">Please select</option>
                                                <?php
                                                foreach ($content_providers as $key => $row) { ?>
                                                    <option value="<?php echo(!empty($row->provider_id) ? $row->provider_id : ''); ?>" title="<?php echo(!empty($row->provider_description) ? $row->provider_description : ''); ?>" data-provider_reference="<?php echo(!empty($row->provider_reference_code) ? $row->provider_reference_code : ''); ?>"><?php echo(!empty($row->provider_name) ? $row->provider_name : ''); ?></option>
                                                    <?php
                                                } ?>
                                            </select>
                                            <?php
                                        } else { ?>
                                            <p>Please add Content Providers using the Provider module</p>
                                            <?php
                                        }   ?>
                                    </div>
                                    <div class="input-group form-group container-full el-hidden">
                                        <label class="input-group-addon el-hidden">Provider reference</label>
                                        <input id="provider_reference" class="form-control" type="text" value="" placeholder="Provider Reference" title="Provider Reference" readonly="readonly" />
                                    </div>
                                    
                                    <div class="input-group form-group container-full">
                                        <label class="input-group-addon el-hidden">Provider Reference Code for Asset</label>
                                        <input name="content_provider_reference_code" class="form-control" type="text" value="" placeholder="Provider Reference Code for Asset" title="Provider Reference Code for Asset" />
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6 col-lg-offset-6 col-md-6 col-md-offset-6 col-sm-6 col-sm-offset-6 col-xs-12">
                                            <button class="btn-block btn-next content-creation-steps" data-currentpanel="content_creation_panel1" type="button">Next</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="content_creation_panel2 col-md-6 col-sm-12 col-xs-12 el-hidden" data-panel-index = "1">
                                <div class="slide-group">
                                    <div class="row hide">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                        <!-- <legend class="legend-header">The Content details (IMDb)?</legend> -->
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <h6 class="error_message pull-right" id="content_creation_panel2-errors"></h6>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <legend class="legend-header">How would you like to add the content?</legend>
                                            <select class="add-content-method form-control" title="How would you like to add the content?">
                                                <option value="">Please select</option>
                                                <option value="fetch-imdb-data">Search IMDB database</option>
                                                <option value="add-manually">Add manually</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="search-for-movie" style="display: none;">
                                        <div class="input-group form-group container-full">
                                            <label for="search" class="input-group-addon el-hidden">IMDb Search</label>
                                            <input name="search" class="form-control input-imdbdata" type="text" value="" placeholder="Movie Title - IMDb Search" />
                                            <button id="fetch-imdbdata" class="btn-block btn-primary form-control fetch-imdbdata submit">Search</button>
                                        </div>

                                        <div class="input-group form-group container-full imdb_fetch_result">
                                            <div id="pages"></div>
                                            <div id="details"></div>
                                        </div>

                                        <div class="input-group form-group container-full">
                                            <button id="fetch_movie_data" class="btn-block btn-primary form-control submit" style="display: none;">Pull Movie Data</button>
                                        </div>
                                    </div>

                                    <?php /* -------- IMDb fetch ---------- */ ?>
                                    <div id="imdb_fetch_container" class="el-hidden">

                                        <div id="content-type" class="input-group form-group container-full el-hidden">
                                            <label class="input-group-addon el-hidden">Content Type</label>
                                            <select name="content_film[type]" class="form-control" title="Content Type">
                                                <option value="">Please select</option>
                                                <option value="series">Series</option>
                                                <option value="episode">Episode</option>
                                                <option value="movie">Movie</option>
                                                <option value="adult">Adult</option>
                                            </select>
                                        </div>                                      

                                        <div class="input-group form-group container-full">
                                            <label class="input-group-addon el-hidden">IMDb ID</label>
                                            <input name="content_film[imdb_id]" class="form-control" type="text" value="" placeholder="IMDb ID" title="IMDb ID" />
                                        </div>


                                        <div class="input-group form-group container-full">
                                            <label class="input-group-addon el-hidden">Film Title</label>
                                            <input name="content_film[title]" class="form-control" type="text" value="" placeholder="Film Title" title="Film Title" />
                                        </div>
                                        
                                        <div class="input-group form-group container-full">
                                            <label class="input-group-addon el-hidden">Tagline</label>
                                            <input name="content_film[tagline]" class="form-control" type="text" value="" placeholder="Tagline" title="Tagline" />
                                        </div>

                                        <div class="input-group form-group container-full">
                                            <label class="input-group-addon el-hidden">Synopsis</label>
                                            <input name="content_film[plot]" class="form-control" type="text" value="" placeholder="Synopsis" title="Synopsis" />
                                        </div>

                                        <div class="input-group form-group container-full">
                                            <label class="input-group-addon el-hidden">Release Date</label>
                                            <input name="content_film[release_date]" class="form-control datetimepicker" data-date-format="DD/MM/YY" type="text" value="" placeholder="Release Date" title="Release Date" />
                                        </div>

                                        <div class="input-group form-group container-full">
                                            <label class="input-group-addon el-hidden">Release Year</label>
                                            <input name="content_film[release_year]" class="form-control" type="text" value="" placeholder="Release Year" title="Release Year" />
                                        </div>

                                        <div class="input-group form-group container-full">
                                            <label class="input-group-addon el-hidden">Asset Code</label>
                                            <input name="content_film[asset_code]" class="form-control required" type="text" value="" placeholder="Asset Code" title="Asset Code" style="font-weight:bold"/>
                                        </div>

                                        <div class="input-group form-group container-full">
                                            <label class="input-group-addon el-hidden">Certificates/Age Ratings</label>
                                            <?php
                                            if (!empty($age_rating)) { ?>
                                                <select name="content_film[age_rating_id]" class="form-control required" title="Certificates">
                                                    <option value="">Please select</option>

                                                    <?php
                                                    foreach ($age_rating as $key => $r_row) { ?>
                                                        <option value="<?php echo(!empty($r_row->age_rating_id) ? $r_row->age_rating_id : ''); ?>" title="<?php echo(!empty($r_row->age_rating_desc) ? $r_row->age_rating_desc : ''); ?>"><?php echo(!empty($r_row->age_rating_desc) ? $r_row->age_rating_desc : ''); ?></option>
                                                        <?php
                                                    } ?>
                                                </select>
                                                <?php
                                            }   ?>
                                        </div>

                                        <div class="input-group form-group container-full">
                                            <label class="input-group-addon el-hidden">Running Time</label>
                                            <input name="content_film[running_time]" class="form-control" type="text" value="" placeholder="Running Time (mins)" title="Running Time (mins)" />
                                        </div>


                                        <div class="input-group form-group container-full">
                                            <label class="input-group-addon el-hidden">Genre</label>
                                            <div id="genres">
                                            </div>
                                        </div>

                                        <div class="input-group form-group container-full">
                                            <label class="input-group-addon el-hidden">Actors</label>
                                            <input name="content_film[actors]" class="form-control" type="text" value="" placeholder="Actors" title="Actors" />
                                        </div>

                                        <div class="input-group form-group container-full">
                                            <label class="input-group-addon el-hidden">Director</label>
                                            <input name="content_film[director]" class="form-control" type="text" value="" placeholder="Director" title="Director" />
                                        </div>

                                        <div class="input-group form-group container-full">
                                            <label class="input-group-addon el-hidden">Poster Image link</label>
                                            <input name="content_film[poster_link]" class="form-control" type="text" value="" placeholder="Poster Image link" title="Poster Image link" />
                                        </div>

                                        <div class="input-group form-group container-full">
                                            <label class="input-group-addon el-hidden">IMDb Link</label>
                                            <input name="content_film[imdb_link]" class="form-control" type="text" value="" placeholder="IMDb Link" title="IMDb Link" />
                                        </div>
                                    </div>

                                    <?php /* -------- IMDb fetch end ---------- */ ?>

                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button class="btn-block btn-back" data-currentpanel="content_creation_panel2" type="button">Back</button>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button class="btn-block btn-next content-creation-steps validate_reference" data-currentpanel="content_creation_panel2" type="button">Next</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="content_creation_panel3 col-md-6 col-sm-12 col-xs-12 el-hidden" data-panel-index = "2">
                                <div class="slide-group">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <legend class="legend-header">The Content details (Dates)?</legend>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <h6 class="error_message pull-right" id="content_creation_panel3-errors"></h6>
                                        </div>
                                    </div>

                                    <div class="input-group form-group container-full">
                                        <label class="input-group-addon el-hidden">Order Date</label>
                                        <input name="order_date" class="form-control datetimepicker" type="text" data-date-format="DD/MM/YY" value="" placeholder="Order Date" title="Order Date" />
                                    </div>

                                    <div class="input-group form-group container-full">
                                        <label class="input-group-addon el-hidden">Delivered Date</label>
                                        <input name="delivered_date" class="form-control datetimepicker" type="text" data-date-format="DD/MM/YY" value="" placeholder="Delivered Date" title="Delivered Date" />
                                    </div>

                                    <div class="input-group form-group container-full">
                                        <label class="input-group-addon el-hidden">Last Ingestion Date</label>
                                        <input name="last_ingestion_date" class="form-control datetimepicker" type="text" data-date-format="DD/MM/YY" value="" placeholder="Last Ingestion Date" title="Last Ingestion Date" />
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button class="btn-block btn-back" data-currentpanel="content_creation_panel3" type="button">Back</button>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button class="btn-block btn-next content-creation-steps" data-currentpanel="content_creation_panel3" type="button">Next</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="content_creation_panel4 col-md-6 col-sm-12 col-xs-12 el-hidden"  data-panel-index = "3">
                                <div class="slide-group">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <legend class="legend-header">The Content details?</legend>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <h6 class="error_message pull-right" id="content_creation_panel4-errors"></h6>
                                        </div>
                                    </div>

                                    <div class="input-group form-group container-full is_uip_nominated">
                                        <label class="input-group-addon el-hidden">UIP Nominated</label>
                                        <select name="is_uip_nominated" class="form-control" title="UIP Nominated">
                                            <option value="">UIP Nominated?</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>

                                    <div class="input-group form-group container-full">
                                        <label class="input-group-addon el-hidden">Is Active?</label>
                                        <select name="is_content_active" class="form-control" title="Is Active?">
                                            <option value="">Is the Content Active?</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button class="btn-block btn-back" data-currentpanel="content_creation_panel4" type="button">Back</button>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button id="create-content-btn" class="btn-block btn-flow btn-next" type="button">Submit</button>
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
    $( "#genres" ).on( "click", "#all_genres", function(){
        if( $( this ).prop( "checked" ) != true ){
            $( "#genres input[type='checkbox']" ).each( 
                function(){ $( this ).prop( "checked", false ) }
            )
        } else {
            $( "#genres input[type='checkbox']" ).each( 
                function(){ $( this ).prop( "checked", true ) }
            )
        }
    });
    
    $( "#genres" ).on( "click", "input[type='checkbox']:not( :first )", function(){
        if( ( $( "#all_genres" ).prop( "checked" ) == true ) && ( $( this ).prop( "checked" ) != true ) ){
            $( "#all_genres" ).prop( "checked", false );
        }
    })

    
    var newDataType = "movie";
    $.ajax({
        url:"<?php echo base_url('webapp/content/fetch_genres/'); ?>",
        method: "POST",
        data: { contentType: newDataType },
        dataType: 'json',
        success:function( data ){
            if( data.status == 1 && ( data.genres !== '' ) ){
                $( "#genres" ).html( data.genres );
            }
        }
    });
    
    
    $( 'select[name="content_film[type]"]' ).on( "change", function( e ){
        e.preventDefault();
        var newDataType = $( this ).val();
        
        if( newDataType != "movie" ){
            $( ".is_uip_nominated" ).addClass( "el-hidden" );
            $( "select[name='is_uip_nominated']" ).attr( "disabled", true );
        } else {
            $( ".is_uip_nominated" ).removeClass( "el-hidden" );
            $( ".is_uip_nominated" ).addClass( "el-shown" );
            $( "select[name='is_uip_nominated']" ).attr( "disabled", false );
        }
        
        
        // for Adult movie allow only specific categories: 
        if( newDataType == "adult" ){
            $( "select[name='content_film[age_rating_id]'] option" ).each( function(){
                if( jQuery.inArray( parseInt( $( this ).val() ), [12, 13, 14] ) !== -1 ){  // Allow Only '18', 'C18' and 'R18'
                    $( this ).prop( 'disabled', false );
                } else {
                    $( this ).prop( 'disabled', true ); 
                }
            });
        } else {
            $( "select[name='content_film[age_rating_id]'] option" ).each( function(){
                $( this ).prop( 'disabled', false );    
            });
        }

        $.ajax({
            url:"<?php echo base_url('webapp/content/fetch_genres/'); ?>",
            method: "POST",
            data: { contentType: newDataType },
            dataType: 'json',
            success:function( data ){
                if( data.status == 1 && ( data.genres !== '' ) ){
                    $( "#genres" ).html( data.genres );
                }
            }
        });
    });



    $( '*[name="content_provider_id"]' ).on( "change", function(){
        var prov_ref = $( this ).find( ':selected' ).data( 'provider_reference' );
        $( "#provider_reference" ).val( prov_ref );
    });

    $( ".add-content-method" ).on( "change", function(){
        if( $( this ).val() == "fetch-imdb-data" ){
            $( ".search-for-movie" ).css( "display", "block" );
            $( "#content-type" ).removeClass( "el-shown" );
            $( "#content-type" ).addClass( "el-hidden" );
        } else if( $( this ).val() == "add-manually" ){
            $( ".search-for-movie" ).css( "display", "none" );
            
            $( "#content-type" ).removeClass( "el-hidden" );
            $( "#content-type" ).addClass( "el-shown" );
            $( "#imdb_fetch_container" ).find( ":input" ).val( function(){
                switch ( this.type ){
                    case 'text':
                        return this.defaultValue;
                    case 'checkbox':
                    case 'radio':
                        this.checked = this.defaultChecked;
                }
            });

            if( "#imdb_fetch_container:hidden" ){
                $( "#imdb_fetch_container" ).css( "display", "block" );
            }

        } else {
            $( ".search-for-movie" ).css( "display", "none" );
        }
    });


    function ucwords ( str ) { //EK TM
        return ( str.replace(/[^a-z0-9\s]/gi, ' ').replace(/[_\s]/g, ' ') + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
            return $1.toUpperCase();
        });
    }


    function updateTechliveID(){
        title_text = $( "#imdb_fetch_container *[name = 'content_film[title]' ]" ).val();
        if(title_text.length > 255) title_text = title_text.substring(0,254);
        title_text = title_text.replace(/[^a-z0-9]+/gi, "").toLowerCase();
        asset_input = $( "#imdb_fetch_container *[name = 'content_film[asset_code]' ]" ).val(title_text);
    }


    $( "#imdb_fetch_container *[name = 'content_film[title]' ]" ).on("input", function() {
        updateTechliveID()
    });


    $( "#fetch_movie_data" ).click( function( e ){
        e.preventDefault();

        var imdbID = $( '*[name="movie_titles"]' ).val();

        if( ( !imdbID ) || ( /^\s*$/.test( imdbID ) ) ){
            alert( "Please select the movie" );
            return false;
        }

        $.ajax({
            url: "http://www.omdbapi.com/?apikey=<?php echo OMDb_API_KEY; ?>&",
            method:"GET",
            data:
            {
                i: imdbID,
            },
            dataType: 'JSON',
            success:function( newData ){
                if( newData.Response.toLowerCase() == "true" ){
                    $( "#imdb_fetch_container" ).removeClass( "el-hidden" );
                    $( "#imdb_fetch_container" ).addClass( "el-shown" );

                    $( "#imdb_fetch_container *[name = 'content_film[imdb_id]' ]" ).val( newData.imdbID );
                    $( "#imdb_fetch_container *[name = 'content_film[title]' ]" ).val( newData.Title );
                    $( "#imdb_fetch_container *[name = 'content_film[release_date]' ]" ).val( newData.Released );
                    $( "#imdb_fetch_container *[name = 'content_film[release_year]' ]" ).val( newData.Year );
                    $( "#imdb_fetch_container *[name = 'content_film[asset_code]' ]" ).val( newData.Title );

                    updateTechliveID()

                    $( '#imdb_fetch_container *[name = "content_film[age_rating_id]" ] option' ).each( function(){
                        if( $( this ).text() == newData.Rated ){ $( this ).attr( 'selected', 'selected' );
                        } else { $( this ).removeAttr( 'selected' ); }
                    });

                    $( "#imdb_fetch_container *[name = 'content_film[plot]' ]" ).val( newData.Plot );
                    $( "#imdb_fetch_container *[name = 'content_film[type]' ]" ).val( newData.Type );
                    // hide the UIP Nominated
                    if( newData.Type != "movie" ){
                        $( ".is_uip_nominated" ).addClass( "el-hidden" );
                        $( "select[name='is_uip_nominated']" ).attr( "disabled", true );
                    } else {
                        $( ".is_uip_nominated" ).removeClass( "el-hidden" );
                        $( ".is_uip_nominated" ).addClass( "el-shown" );
                        $( "select[name='is_uip_nominated']" ).attr( "disabled", false );
                    }

                    $.ajax({
                        url:"<?php echo base_url('webapp/content/fetch_genres/'); ?>",
                        method: "POST",
                        data: { contentType: newData.Type},
                        dataType: 'json',
                        success:function( data ){
                            if( data.status == 1 && ( data.genres !== '' ) ){
                                
                                console.log( 'im here' );
                                $( "#genres" ).html( data.genres );
                            }
                        }
                    });

                    $( "#imdb_fetch_container *[name = 'content_film[running_time]' ]" ).val( newData.Runtime );
                    $( "#imdb_fetch_container *[name = 'content_film[genre]' ]" ).val( newData.Genre );
                    $( "#imdb_fetch_container *[name = 'content_film[actors]' ]" ).val( newData.Actors );
                    $( "#imdb_fetch_container *[name = 'content_film[director]' ]" ).val( newData.Director );
                    $( "#imdb_fetch_container *[name = 'content_film[poster_link]' ]" ).val( newData.Poster );
                    $( "#imdb_fetch_container *[name = 'content_film[imdb_link]' ]" ).val( ( "www.imdb.com/title/" + newData.imdbID ) );

                } else {
                    swal({
                        type: 'error',
                        title: newData.imdbID
                    })
                }
            }
        });
    });


    $("#gen-unique-id").click( function( e ){
        e.preventDefault();
        film_title = $( "#imdb_fetch_container *[name = 'content_film[title]' ]" ).val();
        $( "#imdb_fetch_container *[name = 'content_film[reference_techlive_id]' ]" ).val(getShortCode(film_title));

    });

    var searchInput     = $( '*[name="search"]' );
    var searchTerm      = searchInput.val();
    var submitButton    = $( '#submit' );
    var currentPage     = $( '*[name="page"]' ).val();
    var totalResults    = 0;
    var pages           = 0;

    function displayMovies( data ){

        var currentPage = $( '*[name="page"]' ).val();
        var responseStatus = data.Response;

        if( $.isEmptyObject( data ) || responseStatus.toLowerCase() == "false" ){

            $( "#pages" ).css( "display", "none" );
            var searchTerm2         = searchInput.val();

            $( "#details" ).html('<p>No IMDb results for the  <b>"' + searchTerm2 + '"</b> search term! </p>' );
            $( "#fetch_movie_data" ).css( "display", "none" );

            submitButton.prop( 'disabled', false ).val( "Search" );
            searchInput.prop( 'disabled', false );
        } else {
            $( "#fetch_movie_data" ).css( "display", "block" );
            $( "#pages" ).css( "display", "block" );

            var listHTML = '<select name="movie_titles" class="form-control"><option value="">Please select a movie</option>';
            $.each( data.Search, function( i,movie ){
                listHTML += '<option value="' + movie.imdbID + '">' + movie.Title + ' - ' + movie.Year + ' - ' + ucwords( movie.Type ) + '</option>'
            }); // end each

            listHTML += '</select>';
            var totalResults = data.totalResults;
            var pages = Math.ceil( totalResults / 10 );
            var pagination = '<select name="page" class="form-control">';
            for( var i = 1; i <= pages; i++ ){
                if( currentPage == i ){
                    pagination += '<option value="' + i + '" selected="selected">' + i + '</option>';
                } else {
                    pagination += '<option value="' + i + '">' + i + '</option>';
                }
            }
            pagination += '</select>';

            $( "#pages" ).html( pagination );
            $( "#details" ).html( listHTML );
            submitButton.prop( "disabled", false ).val( "Search" );
            searchInput.prop( "disabled", false );
        }
    }


    $( '#fetch-imdbdata' ).click( function( e ){
        e.preventDefault();

        var searchInput = $( '*[name="search"]' );
        var searchTerm = searchInput.val();
        var submitButton = $( '#submit' );
        var currentPage = $( '*[name="page"]' ).val();
        var contentTypes = ['movie','series','episode'];


        submitButton.prop( "disabled", true ).val( "Loading" );
        searchInput.prop( "disabled", true );

        // the AJAX part
        var movieAPI = "http://www.omdbapi.com/?apikey=<?php echo OMDb_API_KEY; ?>&";
        var movieOptions = {
            s: searchTerm,
            page: currentPage,
            type: 'movie',
        };

        $.getJSON( movieAPI, movieOptions, displayMovies );
    }); // end click

    $( "#pages" ).change( 'select[name="page"]', function( e ){
        e.preventDefault();
        var searchInput = $( '*[name="search"]' );
        var searchTerm = searchInput.val();
        var submitButton = $( '#submit' );
        var currentPage = $( '*[name="page"]' ).val();

        submitButton.prop( 'disabled', true ).val( "Loading" );
        searchInput.prop( 'disabled', true );

        // the AJAX part
        var movieAPI = "http://www.omdbapi.com/?apikey=<?php echo OMDb_API_KEY; ?>&";
        var movieOptions = {
            s: searchTerm,
            page: currentPage
        };
        $.getJSON( movieAPI, movieOptions, displayMovies );
    });


    //Submit content form
    $( '#create-content-btn' ).click( function( e ){
        e.preventDefault();
        var formData = $( '#content-creation-form' ).serialize();

        swal({
            title: 'Confirm new content creation?',
            showCancelButton: true,
            confirmButtonColor: '#5CB85C',
            cancelButtonColor: '#9D1919',
            confirmButtonText: 'Yes'
        }).then( function ( result ) {
            if ( result.value ) {
                $.ajax({
                    url:"<?php echo base_url('webapp/content/create_content/'); ?>",
                    method:"POST",
                    data:formData,
                    dataType: 'json',
                    success:function( data ){
                        if( data.status == 1 && ( data.content !== '' ) ){

                            var contentId = data.content.content_id;

                            swal({
                                type: 'success',
                                title: data.status_msg,
                                showConfirmButton: false,
                                timer: 3000
                            })
                            window.setTimeout( function(){
                                location.href = "<?php echo base_url('webapp/content/profile/'); ?>"+contentId;
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
                $( ".content_creation_panel4" ).hide( "slide", { direction : 'left' }, 500 );
                go_back( ".content_creation_panel2" );
                return false;
            }
        }).catch( swal.noop )
    });

    $( ".content-creation-steps" ).click( function(){
        $( '.error_message' ).each( function(){
            $( this ).text( '' );
        });

        var currentpanel = $(this).data( "currentpanel" );
        var inputs_state = check_inputs( currentpanel );
        if( inputs_state ){
            //If name attribute returned, auto focus to the field and display error message
            $( '[name="'+inputs_state+'"]' ).css( "border", "2px solid red" );
            $( '[name="'+inputs_state+'"]' ).focus();
            var labelText = $( '[name="'+inputs_state+'"]' ).parent().find( 'label' ).text();
            $( '#'+currentpanel+'-errors' ).text( ucwords( labelText ) +' is a requirement' );
            return false;
        }

        if($(this).hasClass("validate_reference")){

            ref = $( "#imdb_fetch_container *[name = 'content_film[asset_code]' ]" ).val()

            $.ajax({
                url:"<?php echo base_url('webapp/content/check_reference/'); ?>",
                method:"POST",
                data:{
                    "reference": ref,
                    "module": "content"
                },
                dataType: 'json',
                success:function( data ){
                    if( ( data.status == 1 ) ){
                        swal({
                            type: 'error',
                            title: data.status_msg,
                            timer: 3000
                        })

                    } else {
/*                      swal({
                            type: 'success',
                            title: data.status_msg,
                            timer: 3000
                        }) */
                        panelchange( "."+currentpanel )

                    }
                }
            });

        } else {
            panelchange( "."+currentpanel )
        }

        return false;
    });

    //** Validate any inputs that have the required class, if empty return the name attribute **/
    function check_inputs( currentpanel ){

        var result = false;
        var panel = "." + currentpanel;

        $( $( panel + " .required" ).get().reverse() ).each( function(){
            $( this ).css( "border", "none" );
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
        var changeto = ".content_creation_panel"+panelnumber;
        $( changefrom ).hide( "slide", {direction : 'left'}, 500 );
        $( changeto ).delay( 600 ).show( "slide", {direction : 'right'},500 );
        return false;
    }

    function go_back( changefrom ){
        var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
        var changeto = ".content_creation_panel"+panelnumber;
        $( changefrom ).hide( "slide", {direction : 'right'}, 500 );
        $( changeto ).delay( 600 ).show( "slide", {direction : 'left'},500 );
        return false;
    }
</script>