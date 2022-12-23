<style>
    body {
        background-color: #F7F7F7;
    }
    .table>thead>tr>th {
        cursor:pointer;
    }
</style>

<div class="row">
    <div class="x_panel no-border">
        <div class="row">
            <div class="x_content">
                
                <!-- Module statistics and info -->
                <div class="module-statistics table-responsive alert alert-ssid" role="alert" style="overflow-y: hidden;" >
                    <legend>People Manager</legend>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <?php
                        if (!empty($simple_stats[0])) {
                            $stats_no = count(get_object_vars($simple_stats[0]));

                            foreach ($simple_stats[0] as $key => $value) { ?>
                                <div class="col-md-<?php echo ceil(12 / $stats_no); ?> col-sm-<?php echo ceil(12 / $stats_no); ?> col-xs-12" style="margin:0">
                                    <div class="row">
                                        <h5 class="text-bold text-center"><?php echo ucwords(str_replace("_", " ", $key)); ?></h5>
                                        <h3 class="text-center"><?php echo $value; ?></h3>
                                    </div>
                                </div>
                                <?php
                            } ?>
                            <?php
                        } else { ?>
                            <div class="col-md-12 col-sm-12 col-xs-12" style="margin:0">
                                <div class="row">
                                    <h5 class="text-bold text-center">No Stats available</h5>
                                    <h3 class="text-center">&nbsp;</h3>
                                </div>
                            </div>
                            <?php
                        } ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
                
                <!-- Filter toggle + search bar -->
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <?php
                        $this->load->view('webapp/_partials/filters'); ?>
                        <?php
                        $this->load->view('webapp/_partials/center_options'); ?>
                    </div>
                    <div class="col-lg-4 col-lg-offset-4 col-md-4 col-md-offset-4 col-sm-6 col-xs-12">
                        <?php
                        $this->load->view('webapp/_partials/search_bar'); ?>
                    </div>
                </div>
                
                <!-- Search by Filters -->
                <div id="filters-container" class="table-responsive filters-container <?php echo $module_identier; ?>-color <?php echo 'border-' . $module_identier; ?>" role="alert" style="overflow-y: hidden; display:none" >
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="filters">
                            <div class="col-md-6 col-sm-4 col-xs-12" style="margin:0">
                                <div class="row">
                                    <h5 class="text-bold text-auto">Statuses</h5>
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6 col-xs-6">
                                            <span class="checkbox" style="margin:0">
                                                <label><input type="checkbox" id="check-all-statuses" value="all" > All</label>
                                            </span>
                                        </div>
                                        <?php if (!empty($user_statuses)) {
                                            foreach ($user_statuses as $k => $status) { ?>
                                            <div class="col-md-4 col-sm-6 col-xs-6">
                                                <span class="checkbox" style="margin:0">
                                                    <label><input type="checkbox" class="user-statuses" name="user_statuses[]" value="<?php echo $status->status_id; ?>" > <?php echo ucwords($status->status); ?></label>
                                                </span>
                                            </div>
                                            <?php }
                                            } ?>                            
                                    </div>                          
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-sm-4 col-xs-12" style="margin:0">
                                <div class="row">
                                    <h5 class="text-bold text-auto">Departments</h5>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-6">
                                            <span class="checkbox" style="margin:0">
                                                <label><input type="checkbox" id="check-all-departments" > All</label>
                                            </span>
                                        </div>
                                        <?php if (!empty($departments)) {
                                            foreach ($departments as $k => $department) { ?>
                                            <div class="col-md-6 col-sm-6 col-xs-6">
                                                <span class="checkbox" style="margin:0">
                                                    <label><input type="checkbox" class="people-departments" name="departments[]" value="<?php echo $department->department_id; ?>" > <?php echo ucwords($department->department_name); ?></label>
                                                </span>
                                            </div>
                                            <?php }
                                            } ?>                            
                                    </div>                          
                                </div>
                            </div>
                            
                            <!-- Clear Filter -->
                            <?php $this->load->view('webapp/_partials/clear_filters.php') ?>                
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                
                <div class="clearfix"></div>
                <div class="table-responsive alert alert-ssid" role="alert" style="overflow-y: hidden;" >
                    <table id="datatable" class="table table-responsive" style="margin-bottom:0px;width:100%" >
                        <thead>
                            <tr>
                                <!-- <th width="5%">ID</th> -->
                                <th width="5%">ID</th>
                                <th width="25%">Full Name</th>
                                <th width="10%">Preferred Name</th>
                                <th width="15%">Work Email</th>
                                <th width="15%">Department</th>
                                <th width="20%">Job Title</th>
                                <th width="10%">Status</th>
                            </tr>
                        </thead>
                        <tbody id="table-results">
                            
                        </tbody>
                    </table>
                </div>
                <div class="clearfix"></div>
    <?php       if ($this->user->is_admin || !empty($permissions->can_add) || !empty($permissions->is_admin)) { ?>
                    <div class="row">
                        <div class="col-md-2 col-sm-3 col-xs-12">
                            <a href="<?php echo base_url('/webapp/people/create'); ?>" class="btn btn-block btn-success success-shadow">Create new</a>
                        </div>
                    </div>
    <?php	  } else { ?>
                    <div class="row">
                        <div class="col-md-2 col-sm-3 col-xs-12">
                            <button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled>No permissions</button>
                        </div>
                    </div>
    <?php	  } ?>
                
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){

        var search_str              = null;
        var people_departments_arr  = [];
        var user_statuses_arr       = [];
        var start_index             = 0;
        
        //Load default brag-statuses
        $('.user-statuses').each(function(){
            if( $(this).is(':checked') ){
                user_statuses_arr.push( $(this).val() );
            }
        });
        
        $('.people-departments').each(function(){
            if( $(this).is(':checked') ){
                people_departments_arr.push( $(this).val() );
            }
        });
        
        load_data( search_str, user_statuses_arr, people_departments_arr );
        
        //Do Search when filters are changed
        $('.user-statuses, .people-departments').change(function(){
            people_departments_arr  =  get_statuses( '.people-departments' );
            user_statuses_arr       =  get_statuses( '.user-statuses' );
            load_data( search_str, user_statuses_arr, people_departments_arr );
        });
    
        //Do search when All is selected
        $('#check-all-statuses, #check-all-departments').change(function(){
            var search_str  = encodeURIComponent( $('#search_term').val() );
            
            var identifier = $(this).attr('id');
            
            if( identifier == 'check-all-statuses' ){
                if( $(this).is(':checked') ){
                    $('.user-statuses').each(function(){
                        $(this).prop( 'checked', true );
                    });
                }else{
                    $('.user-statuses').each(function(){
                        $(this).prop( 'checked', false );
                    });
                }
                
                user_statuses_arr  =  get_statuses( '.user-statuses' );
                
            }else if( identifier == 'check-all-departments' ){
                if( $(this).is(':checked') ){
                    $('.people-departments').each(function(){
                        $(this).prop( 'checked', true );
                    });
                }else{
                    $('.people-departments').each(function(){
                        $(this).prop( 'checked', false );
                    });
                }
                    
                people_departments_arr  =  get_statuses( '.people-departments' );
            }
            load_data( search_str, user_statuses_arr, people_departments_arr );
        });

        //Pagination links
        $("#table-results").on("click", "li.page", function( event ){
            event.preventDefault();
            var start_index = $(this).find('a').data('ciPaginationPage');
            load_data( search_str, user_statuses_arr, people_departments_arr, start_index );
        });
        
        function load_data( search_str, user_statuses_arr, people_departments_arr, start_index ){
            $.ajax({
                url:"<?php echo base_url('webapp/people/lookup'); ?>",
                method:"POST",
                data:{search_term:search_str,user_statuses:user_statuses_arr,departments:people_departments_arr,start_index:start_index},
                success:function(data){
                    $('#table-results').html(data);
                }
            });
        }
        
        $('#search_term').keyup(function(){
            var search = encodeURIComponent( $(this).val() );
            if( search.length > 0 ){
                load_data( search , user_statuses_arr, people_departments_arr,  );
            }else{
                load_data( search_str, user_statuses_arr, people_departments_arr );
            }
        });
        
        function get_statuses( identifier ){

            var chkCount  = 0;
            var totalChekd= 0;
            var unChekd   = 0;
            
            var idClass   = '';
            
            if( identifier == '.user-statuses' ){
                
                user_statuses_arr  = [];
                
                $( identifier ).each(function(){
                    chkCount++;
                    if( $(this).is(':checked') ){
                        totalChekd++;
                        user_statuses_arr.push( $(this).val() );
                    }else{
                        unChekd++;
                    }
                });
                
                if( chkCount > 0 && ( chkCount == totalChekd ) ){
                    $( '#check-all-statuses' ).prop( 'checked', true );
                }else{
                    $( '#check-all-statuses' ).prop( 'checked', false );
                }
                
                return user_statuses_arr;
                
            }else if( identifier == '.people-departments' ){
                
                people_departments_arr  = [];
                
                $( identifier ).each(function(){
                    chkCount++;
                    if( $(this).is(':checked') ){
                        totalChekd++;
                        people_departments_arr.push( $(this).val() );
                    }else{
                        unChekd++;
                    }
                });
                
                if( chkCount > 0 && ( chkCount == totalChekd ) ){
                    $( '#check-all-departments' ).prop( 'checked', true );
                }else{
                    $( '#check-all-departments' ).prop( 'checked', false );
                }
                
                return people_departments_arr;
            }

        }
    });
</script>

