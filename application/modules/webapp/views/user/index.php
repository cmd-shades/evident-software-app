<style>
	body {
		background-color: #FFFFFF;
	}
	.table>thead>tr>th {
		cursor:pointer;
	}
</style>

<div class="row">
	<div class="x_panel no-border">
		<div class="row">
			<div class="x_content">
				<div class="module-topbar-container">
				  <div style="width:250px">
				      <!-- create button -->
					  <a href="<?php echo base_url('/webapp/user/create' ); ?>" class="btn btn-block btn-success success-shadow"><i class="fas fa-plus-circle" style="font-size: 18px;"></i></a>
				  </div>
				  	<div style="width:15%;"></div>
			         <!-- filters -->
					 <div id="user-status" class='filter-container'>
						 <div class='filter-clear pointer' title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
						 <div class='filter-heading'><i class="fas fa-filter filter-icon"></i><span style="font-size:14px;margin-left:10px;">Status <span class='filter-count'></span></span></div>
						 <div class='filter-dropdown' style="display:none;">
							 <div class='filter-item'>
							   <input type="checkbox" class='filter-checkbox filter-select-all' id="filter-all-t">
							   <label class='filter-label' for="filter-all-t">All</label>
							 </div>
							 <section class='active-filters'>
								 <?php if( !empty($user_statuses) ) { foreach( $user_statuses as $k =>$status ){ ?>
									 <div class='filter-item'>
										 <input id="fil-ut-<?php echo $k; ?>" type="checkbox" class='filter-checkbox audit_statuses' value="<?php echo $status->status_id; ?>">
										 <label for = "fil-ut-<?php echo $k; ?>" class='filter-label'><?php echo ucwords( $status->status ); ?></label>
									 </div>
								 <?php } } ?>
							 </section>
						 </div>
					 </div>
					 <div style="width:15%;"></div>
					 <div id="user-type" class='filter-container'>
						 <div class='filter-clear pointer' title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
						 <div class='filter-heading'><i class="fas fa-filter filter-icon"></i><span style="font-size:14px;margin-left:10px;">Type <span class='filter-count'></span></span></div>
						 <div class='filter-dropdown' style="display:none;">
							 <div class='filter-item'>
							   <input type="checkbox" class='filter-checkbox filter-select-all' id="filter-all-s">
							   <label class='filter-label' for="filter-all-s">All</label>
							 </div>
							 <section class='active-filters'>
								 <?php if( !empty($user_types) ) { foreach( $user_types as $k =>$user_type ){ ?>
									 <div class='filter-item'>
										 <input id="fil-us-<?php echo $k; ?>" type="checkbox" class='filter-checkbox audit_statuses' value="<?php echo $user_type->user_type_id ?>">
										 <label for = "fil-us-<?php echo $k; ?>" class='filter-label'> <?php echo ucwords( $user_type->user_type_name ); ?></label>
									 </div>
								 <?php } } ?>
								 <div class='filter-item'>
									 <input id="fil-usms-<?php echo $k; ?>" type="checkbox" class='filter-checkbox audit_statuses' value="<?php echo $current_user->user_type_id; ?>">
									 <label for = "fil-usms-<?php echo $k; ?>" class='filter-label'> Same as myself</label>
								 </div>
							 </section>
						 </div>
					 </div>
			      <div style="width:15%;"></div>
				  <div style="width:400px">
				      <div class="form-group top_search" style="margin-bottom:-13px">
				          <!-- search bar -->
				          <div class="input-group" style="width: 100%;">
				              <input type="text" class="form-control <?php echo $module_identier; ?>-search_input" id="search_term" value="" placeholder="Search <?php echo ( $module_identier != "people" ) ? ( !empty( $rename_search_word ) ? $rename_search_word : ucwords( $module_identier )."s" ) : ucwords( $module_identier ) ; ?>">
				          </div>
				      </div>
				  </div>
				</div>

				
				<!-- Search by Filters -->
				<div id="filters-container" class="table-responsive filters-container <?php echo $module_identier; ?>-color <?php echo 'border-'.$module_identier; ?>" role="alert" style="overflow-y: hidden; display:none" >
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="filters">
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
				<div class="table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;" >
					<table id="datatable" class="table table-responsive" style="margin-bottom:0px;font-size:98%" >
						<thead>
							<tr>
								<!-- <th width="8%">ID</th> -->
								<th width="20%">Full Name</th>
								<th width="20%">Email</th>
								<th width="18%">Username</th>
								<th width="15%">User Type</th>
								<th width="15%">Status</th>
								<?php if( !empty( $this->user->is_admin ) ){ ?>
									<th width="12%">Last Login</th>
								<?php } ?>
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
		
		load_data( search_str, user_types_arr, user_statuses_arr );
		
		userTypeFilter = new setupResultFilter($("#user-type"))

		userTypeFilter.update = function(){
			user_types_arr = userTypeFilter.getFilters()
			load_data( search_str, user_types_arr, user_statuses_arr );
		}
		
		userStatusFilter = new setupResultFilter($("#user-status"))

		userStatusFilter.update = function(){
			user_statuses_arr = userStatusFilter.getFilters()
			load_data( search_str, user_types_arr, user_statuses_arr );
		}
		
		//Pagination links
		$("#table-results").on("click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find('a').data('ciPaginationPage');
			var search_str 	= encodeURIComponent( $( '#search_term' ).val() );
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
		
		
		$( "#table-results" ).on("click", ".force-logout", function( event ){
			
			var userID 	= $( this ).data( 'user_id' );
			var postUrl	= '<?php echo base_url("webapp/user/force_user_logout/"); ?>'+userID;
			var adminUsr= '<?php echo $this->user->id; ?>';
			var userFn 	= $( this ).data( 'first_name' );
			
			swal({
				title: "Confirm Force Logout for "+userFn+"?",
				showCancelButton: true,
				confirmButtonColor: "#5CB85C",
				cancelButtonColor: "#9D1919",
				confirmButtonText: "Yes"
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url: postUrl,
						method:"POST",
						data:{page:'details', user_id: userID },
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									text: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){ 
								
									if( userID == adminUsr ){
										var new_url = '<?php echo base_url("webapp/user/login" ); ?>';
										window.location.href = new_url;
									} else {
										location.reload();
									}
									
								} ,3000);							
							}else{
								swal({
									type: 'error',
									text: data.status_msg
								})
							}		
						}
					});
				}
			}).catch(swal.noop)
			
		});
		
	});
</script>

