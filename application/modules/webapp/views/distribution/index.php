<div id="distribution_group-dashboard" class="row">
    <div class="x_panel no-border">
        <div class="row">
            <div class="x_distribution_group">
                <div class="row">
                    <div class="col-lg-5 col-md-6 col-sm-12 col-xs-12">
                        <h2>Distribution Groups</h2>
                    </div>
                    <div class="col-lg-7 col-md-6 col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                <a href="<?php echo base_url('/webapp/distribution/create_group'); ?>" class="btn btn-block btn-new">New Distribution Group</a>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                <?php $this->load->view('webapp/_partials/search_bar'); ?>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                <a href="<?php echo base_url('/webapp/settings/module/' . $module_id); ?>" class="btn btn-block btn-secondary">Settings</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search by Filters -->
                <div id="filters-container" class="table-responsive filters-container <?php echo $module_identier; ?>-color <?php echo 'border-' . $module_identier; ?>" role="alert">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="filters">
                            <div class="col-md-3 col-sm-3 col-xs-12" style="margin:0">
                                <div class="row">
                                    <h5 class="text-bold text-auto">Distribution Group statuses</h5>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-6">
                                            <span class="checkbox" style="margin:0">
                                                <label><input type="checkbox" id="check-all" value="all" > All</label>
                                            </span>
                                        </div>
                                        <?php if (!empty($site_statuses)) {
                                            foreach ($site_statuses as $k => $status) { ?>
                                            <div class="col-md-6 col-sm-6 col-xs-6">
                                                <span class="checkbox" style="margin:0">
                                                    <label><input type="checkbox" class="user-types" name="site_statuses[]" value="<?php echo $status->status_id; ?>" > <?php echo ucwords($status->status_name); ?></label>
                                                </span>
                                            </div>
                                            <?php }
                                            } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Clear Filter -->
                        <?php $this->load->view('webapp/_partials/clear_filters.php') ?>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="clearfix"></div>
                <div class="row">
                    <div class="table-responsive alert alert-ssid" role="alert" style="overflow-y: hidden;" >
                        <table id="datatable" class="table table-responsive" style="margin-bottom:0px;" >
                            <thead>
                                <tr>
                                    <th width="25%">Group ID</th>
                                    <th width="35%">Distribution Group</th>
                                    <th width="25%">Territory</th>
                                    <th width="15%"><span class="pull-right">Group Status</span></th>
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
</div>

<script type="text/javascript">
    $( document ).ready(function(){

        var search_str          = null;
        var start_index         = 0;
        var where               = false;
        
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

        
        //Pagination links
        $( "#table-results" ).on( "click", "li.page", function( event ){
            event.preventDefault();
            var start_index = $( this ).find( 'a' ).data( 'ci-pagination-page' );
            var search_str  = encodeURIComponent( $( '#search_term' ).val() );
            load_data( search_str, where, start_index );
        });
        
        
        // Pull the data
        function load_data( search_str, where, start_index ){
            $.ajax({
                url:"<?php echo base_url('webapp/distribution/distribution_group_lookup'); ?>",
                method:"POST",
                data:{ search_term:search_str, where:where, start_index:start_index },
                success:function( data ){
                    $( '#table-results' ).html( data );
                }
            });
        }

    });
</script>

