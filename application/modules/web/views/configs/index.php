<style>
	body {
		background-color: #FFFFFF;
	}
	.table>thead>tr>th {
		cursor:pointer;
	}

	.nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover{
		color: #0092CD !important;
	}

	.nav-tabs>li>a{
		color: #555;
		width: max-content;
		margin: 0 auto;
		padding: 5px 35px;
	}

	.filters-toggle.open{
		top: 10px;
		position: absolute;
		background-color: rgb( 92, 92, 92 );
	}

	#filters-container{
	    display: block;
		overflow-y: hidden;
		position: relative;
		top: -11px;
		background: #f4f4f4;
	}

	.filters_to_center{
		margin: 0 auto;
		float: initial;
	}

	.top_search .input-group .fas.fa-search{
	    position: absolute;
		top: 8px;
		left: 20px;
		color: #fff;
		width: 28px;
		height: 28px;
		z-index: 99;
		font-size: 18px;
	}

	#search_term{
		text-indent: 32px;
	}

	#search_term::placeholder{
		color: #fff;
	}

	.filters_open{
	    min-height: 45px;
		background-color: #f4f4f4 !important;
		color: #5c5c5c !important;
		border-bottom: none !important;
		z-index: 99;
	    -webkit-box-shadow: none !important;
		-moz-box-shadow: none !important;
		box-shadow: none !important;
	}

	.zindex_99{
		z-index: 99;
	}

	#filters-container .nav>li>a:hover, #filters-container .nav>li>a:active, #filters-container .nav>li>a:focus{
		background: #f4f4f4;
		border: none;
		border-bottom: 1px solid #555;
		width: max-content;
		margin: 0 auto;
	}
	
	#filters-container .nav-tabs > li > a{
		display: inline-block;
	}
	
	#filters-container .nav-tabs > li > a > input{
		display: inline-block;
	}

	.nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover{
	    cursor: default;
		border: none;
		background-color: #f4f4f4;
		border-bottom: 1px solid #555;
		width: max-content;
		margin: 0 auto;
	}

	.nav.nav-tabs{
		border-bottom: none;
	}

	.nav-tabs > li{
		width: calc(100% / 9 * 2);
		text-align: center;
	}

	.nav-tabs > li:first-child, .nav-tabs > li:last-child{
		width: calc(100% / 9 * 1.5);
	}

	.padding_top_20{
		padding-top: 20px;
	}
	
	
	button.clear-filters{
		background-color: rgba( 92, 92, 92, 1);
		color: #fff;
	}
	
	button.clear-filters:hover, button.clear-filters:active {
		background-color: rgba( 92, 92, 92, 1);
		color: #0092CD;
	}
</style>

<div class="row">
	<div class="x_panel no-border">
		<div class="row">
			<div class="x_content">
			
				<!-- Filter toggle + search bar -->
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
						<div class="row">
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
								<h1 class="text-bold" >
									CONFIG 
									<?php if ($this->user->is_admin && (in_array($this->user->id, $this->super_admin_list))) { ?>
										<span class="hide pointer add-new-entry"><i class="fas fa-plus" title="Add New Entry" ></i></span>
									<?php } ?>
								</h1>
								<!-- <a href="<?php echo base_url('/webapp/diary/new_region'); ?>" class="btn btn-block btn-success success-shadow" title="Click to add a new Region"><i class="fas fa-plus-circle" title="" style="font-size: 18px;"></i></a><br/> -->
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center zindex_99">
						<div class="row hide">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<?php $this->load->view('webapp/_partials/filters'); ?>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
						<!-- <div style="width:100%"> -->
							<div class="form-group top_search" style="margin-bottom:-13px">
								<div class="input-group" style="width: 100%;">
									<i class="fas fa-search"></i><input type="text" class="form-control <?php echo $module_identier; ?>-search_input" id="search_term" value="" placeholder="Search Configurable options">
								</div>
							</div>
						<!-- </div> -->
					</div>
				</div>
				
				<div class="clearfix"></div>
				<br/>
				<div class="row" role="alert" >
					<div id="table-results" class="col-md-12 col-sm-12 col-xs-12">
						
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>

<!-- Modal for adding a new Config Entry. Should this be a modal? -->
<div class="modal fade add-config-entry-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form id="add-config-entry-form" >
				<input type="hidden" name="page" value="details" />
				<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title" id="myAddQuestModalLabel">Add a New Config Entry</h4>
				</div>
				
				<div class="modal-body config-entry-body">
					<div class="input-group form-group">
						<label class="input-group-addon">Entry Name</label>
						<input name="entry_name" class="form-control" type="text" placeholder="Entry Name" value="" />
					</div>
					<div class="input-group form-group">
						<label class="input-group-addon">Entry Group</label>
						<input name="entry_group" class="form-control" type="text" placeholder="Entry Group" value="" />
					</div>
					<div class="input-group form-group">
						<label class="input-group-addon">Entry Description</label>
						<input name="entry_description" class="form-control" type="text" placeholder="Entry Description" value="" />
					</div>
					<div class="input-group form-group">
						<label class="input-group-addon" title="Where this should should point to">Entry Url Link</label>
						<input name="entry_url_link" class="form-control" type="text" placeholder="E.g. /asset/asset_types" value="" />
					</div>
					<div class="input-group form-group">
						<label class="input-group-addon" title="Where this should should point to">Icon Image Url</label>
						<input name="entry_img_url" class="form-control" type="text" placeholder="E.g. /assets/images/config-icons/asset-types.png" value="/assets/images/config-icons/" />
					</div>
				</div>

				<div class="modal-footer">
					<button id="add-config-entry-btn" type="button" class="btn btn-sm btn-success">Add Entry</button>
					<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">&nbsp;&nbsp;&nbsp;&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal for Editing a Config Entry -->
<div class="modal fade edit-config-entry-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form id="edit-config-entry-form" >
				<input type="hidden" name="entry_id" value="" />
				<input type="hidden" name="page" value="details" />
				<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title" id="myEditQuestModalLabel">Edit A Config Entry</h4>
					<small id="config-entry-feedback-message"></small>
				</div>

				<div class="modal-body config-entry-body">
	
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">&nbsp;&nbsp;&nbsp;&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;</button>
					<button id="edit-config-entry-btn" type="button" class="btn btn-sm btn-success">Save Changes</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">

	$( document ).ready(function(){

		var search_str   	= null;
		var start_index	 	= 0;
		var where 			= {};

		load_data( search_str, where, start_index );
	
		//Pagination links
		$( "#table-results" ).on("click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find('a').data('ciPaginationPage');
			load_data( search_str, where, start_index);
		});
		
		function load_data( search_str, where, start_index ){
			$.ajax({
				url:"<?php echo base_url('webapp/config/config_entries_list'); ?>",
				method:"POST",
				data:{ search_term:search_str, where:where, start_index:start_index },
				success:function(data){
					$('#table-results').html(data);
				}
			});
		}
		
		$('#search_term').keyup(function(){
			var search = encodeURIComponent( $(this).val() );
			if( search.length > 0 ){
				load_data( search , where, start_index );
			}else{
				load_data( search_str, where, start_index );
			}
		});
		
		$( '.add-new-entry' ).click( function(){
			$( '.add-config-entry-modal' ).modal( 'show' );
		});
		
		//Submit form for processing
		$( '#add-config-entry-btn' ).click( function( event ){
			event.preventDefault();
			var formData = $('#add-config-entry-form').serialize();
			swal({
				title: 'Confirm add new Config Entry?',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/config/add_config_entry/'); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2000
								})
								window.setTimeout(function(){ 
									location.reload();
								} ,2000);							
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}		
						}
					});
				}
			}).catch(swal.noop)
		});

	});
</script>

