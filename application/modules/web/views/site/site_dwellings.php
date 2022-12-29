<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<form id="add-locations-form" class="form-horizontal">
			<input type="hidden" name="site_id" value="<?php echo $site_details->site_id; ?>" />
			<input type="hidden" name="site_unique_id" value="<?php echo $site_details->site_unique_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<input type="hidden"  name="page" value="locations"/>
			<div class="x_panel tile has-shadow">
				<legend>Current Locations <span class="pull-right pointer add-availability"><i class="fas fa-plus text-green" title="Add Operatives' availability" ></i></span></legend>
				<div class="form-group" >
					<div class="drop-shaddow">
						<input type="text" id="search_term" class="black-bg form-control <?php echo $module_identier; ?>-search_input" value="" placeholder="Search locations..." />
					</div>
				</div>
				<br/>
				<div class="x_panel drop-shaddow">
					<table class="sortable datatable table table-responsive" style="margin-bottom:0; width:100%">
						<thead>
							<tr>
								<th>Address Line 1</th>
								<th>Location Type</th>
								<th>Resident Name</th>
								<th>Area</th>
								<th><span class="pull-right">Action</span></th>
							</tr>
						</thead>
						
						<tbody id="locations-results" style="overflow-y:auto;" >
							
						</tbody>
					</table>
				</div>
			</div>
		</form>
	</div>
</div>


<script>
	$( document ).ready( function(){
		
		var search_str   = null;
		var start_index	 = 0;
		var where = { 	
			'site_id': '<?php echo $site_details->site_id;?>'
		};
		
		load_data( search_str, where, start_index );
		
		$( '#locations-results' ).on( 'click', '.unlink-item', function(){
			swal({
				type: 'info',
				text: 'Unlink Functionality coming soon'
			});
		});
		
		$( '#locations-results' ).on( 'click', '.delete-item', function(){
			swal({
				type: 'warning',
				text: 'Delete Functionality coming soon'
			});
		});
		
		$('.site-postcodes').focus(function(){
			$( '.location-container' ).slideDown( 'slow' );
		});

		//Submit site form
		$( '.add-locations-btn' ).click(function( e ){
			e.preventDefault();
			var formData = $('#add-locations-form').serialize();
			swal({
				title: 'Add selected locations?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then((result) => {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/site/update_site/'); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 && ( data.site !== '' ) ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){ 
									location.reload();
								} ,3000);							
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
		
		$("#locations-results").on("click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find( 'a' ).data('ciPaginationPage');
			load_data( search_str, where, start_index );
		});
		
		function load_data( search_str, where, start_index ){
			$.ajax({
				url:"<?php echo base_url('webapp/site/locations_lookup/'.$site_details->site_id); ?>",
				method:"POST",
				data:{ search_term:search_str, where:where, start_index:start_index },
				success:function(data){
					$( '#locations-results' ).html( data );
				}
			});
		}
		
		$( '#search_term' ).keyup( function(){
			var search = encodeURIComponent( $(this).val() );
			if( search.length > 0 ){
				load_data( search , where );
			} else {
				load_data( search_str, where );
			}
		});
		
	});
</script>