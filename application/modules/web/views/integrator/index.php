<div id="integrator-dashboard" class="row">
    <div class="x_panel no-border">
        <div class="row">
            <div class="x_content">
                <div class="row">
                    <div class="col-lg-5 col-md-6 col-sm-12 col-xs-12">
                        <h2>System Integrator</h2>
                    </div>
                    <div class="col-lg-7 col-md-6 col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <a href="<?php echo base_url('/webapp/integrator/create'); ?>" class="btn btn-block btn-new">New System Integrator</a>
                            </div>
                            <?php
                            if (!empty($integrator_categories)) { ?>
                                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                    <select class="btn-block btn-primary" name="filter[category_id]">
                                        <option value="">Please select</option>
                                        <?php
                                        foreach ($integrator_categories as $key => $row) { ?>
                                            <option value="<?php echo $row->category_id; ?>" title="<?php echo (!empty($row->integrator_category_description)) ? $row->integrator_category_description : '' ; ?>"><?php echo (!empty($row->integrator_category_name)) ? $row->integrator_category_name : '' ; ?></option>
                                            <?php
                                        } ?>
                                    </select>
                                </div>
                                <?php
                            } ?>

                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                <?php
                                $this->load->view('webapp/_partials/search_bar'); ?>
                            </div>

                            <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                <a href="<?php echo base_url('/webapp/settings/module/' . $module_id); ?>" class="btn btn-block btn-secondary">Settings</a>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="clearfix"></div>
                <div class="table-responsive alert alert-ssid" role="alert" style="overflow-y: hidden;" >
                    <table id="datatable" class="table table-responsive" style="margin-bottom:0px;" >
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="35%">System Integrator Name</th>
                                <th width="20%">Email Address</th>
                                <th width="20%">Start Date</th>
                                <th width="20%">Currency</th>
                            </tr>
                        </thead>
                        <tbody id="table-results">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $( document ).ready(function(){

        var search_str          = null;
        var category_id         = null;
        var start_index         = 0;

        load_data( search_str, category_id );

        // Pagination links
        $( "#table-results" ).on( "click", "li.page", function( event ){
            event.preventDefault();
            start_index = $( this ).find( 'a' ).data( 'ciPaginationPage' );
            load_data( search_str, start_index );
        });

        function load_data( search_str, start_index ){
            $.ajax({
                url:"<?php echo base_url('webapp/integrator/lookup'); ?>",
                method: "POST",
                data:{
                    search_term:search_str,
                    start_index:start_index
                },
                success:function( data ){
                    $( '#table-results' ).html( data );
                }
            });
        }

        $( '#search_term' ).keyup( function(){
            var search = encodeURIComponent( $( this ).val() );
            if( search.length > 0 ){
                load_data( search );
            } else {
                load_data( search_str );
            }
        });
    });
</script>