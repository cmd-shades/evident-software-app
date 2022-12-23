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
				<div class="row">
					<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
						<h2>User Manager</h2>
					</div>
					<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
						<div class="row">
							<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
								<a href="<?php echo base_url('/webapp/user/create' ); ?>" class="btn btn-block btn-new">Add User</a>
							</div>
							<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
								<?php
								$this->load->view( 'webapp/_partials/search_bar' ); ?>
							</div>
							
							<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
								<a href="<?php echo base_url( '/webapp/settings' ); ?>" class="btn btn-block btn-secondary">Settings</a>
							</div>
						</div>
					</div>
				</div>
				
				
				<!-- Search by Filters -->
				<div id="filters-container" class="table-responsive filters-container <?php echo $module_identier; ?>-color <?php echo 'border-'.$module_identier; ?>" role="alert" style="overflow-y: hidden; display:none" >
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="filters">
							
							<div class="col-md-4 col-sm-4 col-xs-12" style="margin:0">
								<div class="row">
									<h5 class="text-bold text-auto">Statuses</h5>
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-6">
											<span class="checkbox" style="margin:0">
												<label><input type="checkbox" id="check-all-statuses" value="all" > All</label>
											</span>
										</div>
										<?php if( !empty($user_statuses) ) { foreach( $user_statuses as $k =>$status ){ ?>
											<div class="col-md-6 col-sm-6 col-xs-6">
												<span class="checkbox" style="margin:0">
													<label><input type="checkbox" class="user-statuses" name="user_statuses[]" value="<?php echo $status->status_id; ?>" > <?php echo ucwords( $status->status ); ?></label>
												</span>
											</div>
										<?php } } ?>							
									</div>							
								</div>
							</div>
							
							<div class="col-md-4 col-sm-4 col-xs-12" style="margin:0">
								<div class="row">
									<h5 class="text-bold text-auto">Types</h5>
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-6">
											<span class="checkbox" style="margin:0">
												<label><input type="checkbox" id="check-all-types" value="all" > All</label>
											</span>
										</div>
										<?php if( !empty($user_types) ) { foreach( $user_types as $k =>$user_type ){ ?>
											<div class="col-md-6 col-sm-6 col-xs-6">
												<span class="checkbox" style="margin:0">
													<label><input type="checkbox" class="user-types" name="user_types[]" value="<?php echo $user_type->user_type_id ?>" > <?php echo ucwords( $user_type->user_type_name ); ?>s</label>
												</span>
											</div>
										<?php } } ?>
										<div class="col-md-6 col-sm-6 col-xs-6">
											<span class="checkbox" style="margin:0">
												<label><input type="checkbox" class="user-types" name="user_types[]" value="<?php echo $current_user->user_type_id; ?>" > Same as myself</label>
											</span>
										</div>							
									</div>							
								</div>
							</div>

							<div class="pull-right col-md-3 col-sm-3 col-xs-12" style="margin:0; display:none">
								<div class="row">
									<h5 class="text-bold">Quick Actions</h5>
									<form>
										<div>
											<select name="active" id="select-action" class="form-control" required>
												<option value="">Select action</option>
												<option value="1">Actvate user</option>
												<option value="0">De-actvate user</option>														
											</select>
										</div>
										<div class="assignees-list" style="display:none; margin-top:10px;">
											<select id="assign_to" name="assign_to" class="form-control">
												<option value="" >Please select assignee</option>
											</select>
										</div>
										<br/>
										<a id="submit-action" class="btn btn-sm btn-info btn-block" >Submit Action</a>
									</form>
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
								<!-- <th width="8%">ID</th> -->
								<th width="5%">ID</th>
								<th width="25%">Full name</th>
								<th width="15%">Email</th>
								<th width="20%">Username</th>
								<th width="20%">User type</th>
								<th width="10%">Status</th>
							</tr>
						</thead>
						<tbody id="table-results">
							
						</tbody>
					</table>
				</div>
				
				<div class="clearfix"></div>

			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		
		var search_str   		= null;
		var user_types_arr		= [];
		var user_statuses_arr	= [];
		var start_index	 		= 0;
		
		//Load default brag-statuses
		$('.user-types').each(function(){
			if( $(this).is(':checked') ){
				user_types_arr.push( $(this).val() );
			}
		});
		
		//Load default brag-statuses
		$('.user-statuses').each(function(){
			if( $(this).is(':checked') ){
				user_statuses_arr.push( $(this).val() );
			}
		});
		
		load_data( search_str, user_types_arr, user_statuses_arr );
		
		//Do Search when filters are changed
		$('.user-types, .user-statuses').change(function(){
			user_types_arr 		=  get_statuses( '.user-types' );
			user_statuses_arr 	=  get_statuses( '.user-statuses' );
			load_data( search_str, user_types_arr, user_statuses_arr );
		});
	
		//Do search when All is selected
		$('#check-all-types, #check-all-statuses').change(function(){
			var search_str  = $('#search_term').val();
				
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
				
			}else if( identifier == 'check-all-types' ){
				if( $(this).is(':checked') ){
					$('.user-types').each(function(){
						$(this).prop( 'checked', true );
					});
				}else{
					$('.user-types').each(function(){
						$(this).prop( 'checked', false );
					});
				}
					
				user_types_arr 	=  get_statuses( '.user-types' );
			}
			load_data( search_str, user_types_arr, user_statuses_arr );
		});

		//Pagination links
		$("#table-results").on("click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find('a').data('ciPaginationPage');
			load_data( search_str, user_types_arr, user_statuses_arr, start_index );
		});
		
		function load_data( search_str, user_types_arr, user_statuses_arr, start_index ){
			$.ajax({
				url:"<?php echo base_url('webapp/user/lookup'); ?>",
				method:"POST",
				data:{search_term:search_str, user_types:user_types_arr, user_statuses:user_statuses_arr, start_index:start_index},
				success:function(data){
					$('#table-results').html(data);
				}
			});
		}
		
		$('#search_term').keyup(function(){
			var search = encodeURIComponent( $(this).val() );
			if( search.length > 0 ){
				load_data( search , user_types_arr, user_statuses_arr );
			}else{
				load_data( search_str, user_types_arr, user_statuses_arr );
			}
		});
		
		function get_statuses( identifier ){

			var chkCount  = 0;
			var totalChekd= 0;
			var unChekd   = 0;
			
			var idClass	  = '';
			
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
				
			}else if( identifier == '.user-types' ){
				
				user_types_arr 	= [];
				
				$( identifier ).each(function(){
					chkCount++;
					if( $(this).is(':checked') ){
						totalChekd++;
						user_types_arr.push( $(this).val() );
					}else{
						unChekd++;
					}
				});
				
				if( chkCount > 0 && ( chkCount == totalChekd ) ){
					$( '#check-all-types' ).prop( 'checked', true );
				}else{
					$( '#check-all-types' ).prop( 'checked', false );
				}
				
				return user_types_arr;
			}

		}
	});
</script>

