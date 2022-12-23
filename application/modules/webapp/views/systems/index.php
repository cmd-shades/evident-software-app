<div id="systems-dashboard" class="row">
    <div class="x_panel no-border">
        <div class="row">
            <div class="x_content">
                <div class="row">
                    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                        <h2>Systems</h2>
                    </div>
                    <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <a href="<?php echo base_url('/webapp/systems/create'); ?>" class="btn btn-block btn-new">New System</a>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                <?php
                                $this->load->view('webapp/_partials/search_bar'); ?>
                            </div>
                            
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
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
                                    <h5 class="text-bold text-auto">System Statuses</h5>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-6">
                                            <span class="checkbox" style="margin:0">
                                                <label><input type="checkbox" id="check-all" value="all" > All</label>
                                            </span>
                                        </div>
                                        <?php if (!empty($system_statuses)) {
                                            foreach ($system_statuses as $k => $status) { ?>
                                            <div class="col-md-6 col-sm-6 col-xs-6">
                                                <span class="checkbox" style="margin:0">
                                                    <label><input type="checkbox" class="user-types" name="system_statuses[]" value="<?php echo $status->status_id; ?>" > <?php echo ucwords($status->status_name); ?></label>
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
                <div class="table-responsive alert alert-ssid" role="alert" style="overflow-y: hidden;" >
                    <table id="datatable" class="table table-responsive" style="margin-bottom:0px;" >
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="20%">Name</th>
                                <th width="10%">Local Server</th>
                                <th width="20%">DRM type</th>
                                <th width="10%">Approved by Provider</th>
                                <th width="10%">Approval Date</th>
                                <th width="20%">Provider Name</th>
                                <th width="10%">Delivery Mechanism</th>
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
    $(document).ready(function(){

        var search_str          = null;
        var systems_statuses_arr    = [];
        var start_index         = 0;

        //Load default brag-statuses
        $('.user-types').each(function(){
            if( $(this).is(':checked') ){
                systems_statuses_arr.push( $(this).val() );
            }
        });

        load_data( search_str, systems_statuses_arr );

        //Do Search when filters are changed
        $('.user-types').change(function(){
            systems_statuses_arr =  get_statuses( '.user-types' );
            load_data( search_str, systems_statuses_arr );
        });

        //Do search when All is selected
        $('#check-all').change(function(){
            var search_str  = $('#search_term').val();

            if( $(this).is(':checked') ){
                $('.user-types').each(function(){
                    $(this).prop( 'checked', true );
                });
            }else{
                $('.user-types').each(function(){
                    $(this).prop( 'checked', false );
                });
            }
            systems_statuses_arr =  get_statuses( '.user-types' );
            load_data( search_str, systems_statuses_arr );
        });

        //Pagination links
        $("#table-results").on("click", "li.page", function( event ){
            event.preventDefault();
            //var off_set = $(this).data('ciPaginationPage');
            var start_index = $(this).find('a').data('ciPaginationPage');
            load_data( search_str, systems_statuses_arr, start_index );
        });

        function load_data( search_str, systems_statuses_arr, start_index ){
            $.ajax({
                url:"<?php echo base_url('webapp/systems/lookup'); ?>",
                method:"POST",
                data:{search_term:search_str,systems_statuses:systems_statuses_arr,start_index:start_index},
                success:function(data){
                    $('#table-results').html(data);
                }
            });
        }

        $('#search_term').keyup(function(){
            var search = encodeURIComponent( $(this).val() );
            if( search.length > 0 ){
                load_data( search , systems_statuses_arr );
            }else{
                load_data( search_str, systems_statuses_arr );
            }
        });

        function get_statuses( identifier ){
            systems_statuses_arr = [];
            var chkCount  = 0;
            var totalChekd= 0;
            var unChekd   = 0;
            $( identifier ).each(function(){
                chkCount++;
                if( $(this).is(':checked') ){
                    totalChekd++;
                    systems_statuses_arr.push( $(this).val() );
                }else{
                    unChekd++;
                }
            });

            if( chkCount > 0 && ( chkCount == totalChekd ) ){
                $( '#check-all' ).prop( 'checked', true );
            }else{
                $( '#check-all' ).prop( 'checked', false );
            }
            return systems_statuses_arr;
        }
    });
</script>