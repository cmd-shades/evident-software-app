<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Contract Schedules</legend>
			<div class="form-group" >
				There's currently no records to display.
			</div>
		</div>	
	</div>
</div>
<?php /* ?>
<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<form id="update-schedules-form" class="form-horizontal">
			<input type="hidden" name="contract_id" value="<?php echo $contract_details->contract_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<input type="hidden"  name="page" value="schedules"/>
			<div class="x_panel tile has-shadow">
				<legend>Current Schedules <a disabled href="<?php echo base_url( 'webapp/job/new_schedule?contract_id='.$contract_details->contract_id ); ?>" class="pull-right pointer"><i class="fas fa-plus text-green" title="Add Operatives' availability" ></i></a></legend>
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
<?pph */ ?>

<div id="new-location-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title" id="myModalLabel">Add Contract Schedules</h4>				
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
			'contract_id': '<?php echo $contract_details->contract_id;?>'
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
		
		$('.contract-postcodes').focus(function(){
			$( '.location-container' ).slideDown( 'slow' );
		});
		
		$("#schedules-results").on( "click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find( 'a' ).data( 'ciPaginationPage' );
			load_data( search_str, where, start_index );
		});
		
		function load_data( search_str, where, start_index ){
			$.ajax({
				url:"<?php echo base_url( 'webapp/contract/schedules_lookup/'.$contract_details->contract_id ); ?>",
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