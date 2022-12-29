<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<form id="update-schedules-form" class="form-horizontal">
			<input type="hidden" name="location_id" value="<?php echo $location_details->location_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<input type="hidden"  name="page" value="schedules"/>
			<div class="x_panel tile has-shadow">
				<legend>Current Location Schedules <a href="<?php echo base_url('webapp/job/new_schedule?location_id='.$location_details->location_id); ?>" class="pull-right pointer"><i class="fas fa-plus text-green" title="Add Operatives' availability" ></i></a></legend>
				<div class="form-group" >
					<div class="drop-shaddow">
						<input type="text" id="search_term" class="black-bg form-control <?php echo $module_identier; ?>-search_input" value="" placeholder="Search schedules..." />
					</div>
				</div>
				<br/>
				<div class="x_panel drop-shaddow">
					<table class="sortable datatable table table-responsive" style="margin-bottom:0; width:100%">
						<thead>
							<tr>
								<th>Schedule Name</th>
								<th>Frequency</th>
								<th>Total Activities</th>
								<th>Status</th>
								<th><span class="pull-right">Action</span></th>
							</tr>
						</thead>
						<tbody id="schedules-results" style="overflow-y:auto;" >
							
						</tbody>
					</table>
				</div>
			</div>
		</form>
	</div>
</div>

<div id="new-location-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title" id="myModalLabel">Add location Schedules</h4>				
			</div>
			<div class="modal-body">
				<div class="">
					
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){
		
		var search_str   = null;
		var start_index	 = 0;
		var where = { 	
			'location_id': '<?php echo $location_details->location_id;?>'
		};
		
		load_data( search_str, where, start_index );
		
		$( '#schedules-results' ).on( 'click', '.unlink-item', function(){
			swal({
				type: 'info',
				text: 'Unlink Functionality coming soon'
			});
		});
		
		$( '#schedules-results' ).on( 'click', '.delete-item', function(){
			swal({
				type: 'warning',
				text: 'Delete Functionality coming soon'
			});
		});
		
		$('.location-postcodes').focus(function(){
			$( '.location-container' ).slideDown( 'slow' );
		});

		// LOAD ADDRESSES WHEN MODAL OPENS
		$( '.add-schedules' ).click( function(){
			var postCode = $( '#location_postcodes' ).val();
			var locationID 	 = '<?php echo $location_details->location_id;?>';
			if( postCode.length > 0 ){
				$.post( "<?php echo base_url("webapp/location/get_addresses_by_postcode"); ?>",{postcodes:postCode, location_id:locationID},function(result){
					$( "#building-addresses" ).html( result["addresses_list"] );				
					$("#new-location-modal").modal( "show" );			
				},"json" );
			} else {
				$( "#new-location-modal" ).modal( "show" );
			}
		});
		
		//LOAD ADDRESSES WHEN POSTCODE IS CHANGED IN THE MODAL
		$( '.location-postcodes' ).change(function(){
			var postCode = $(this).val();
			var locationID 	 = '<?php echo $location_details->location_id;?>';
			if( postCode.length > 0 ){
				$.post("<?php echo base_url("webapp/location/get_addresses_by_postcode"); ?>",{postcodes:postCode, location_id:locationID},function(result){
					$( "#building-addresses" ).html( result["addresses_list"] );			
				},"json");
			}
		});
		
		// SELECT ALL ADDRESSES
		$( '#building-addresses' ).on( 'change', '#check_all', function(){
			if( $( this ).is( ':checked' ) ){
				$( '.address-chks' ).each( function(){
					$( this ).prop( 'checked', true );
				});
			} else {
				$( '.address-chks' ).each( function(){
					$( this ).prop( 'checked', false );
				});
			}
		} );
		
		//Submit location form
		$( '.add-schedules-btn' ).click(function( e ){
			e.preventDefault();
			var formData = $('#add-schedules-form').serialize();
			swal({
				title: 'Add selected schedules?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( (result) => {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/location/add_location_location/'); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2000
								})
								window.setTimeout(function(){
									$( "#new-location-modal" ).modal( "hide" );
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
		
		$("#schedules-results").on( "click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find( 'a' ).data( 'ciPaginationPage' );
			load_data( search_str, where, start_index );
		});
		
		function load_data( search_str, where, start_index ){
			$.ajax({
				url:"<?php echo base_url('webapp/location/schedules_lookup/'.$location_details->location_id); ?>",
				method:"POST",
				data:{ search_term:search_str, where:where, start_index:start_index },
				success:function(data){
					$( '#schedules-results' ).html( data );
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