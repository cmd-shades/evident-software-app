<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<form id="update-schedules-form" class="form-horizontal">
			<input type="hidden" name="contract_id" value="<?php echo $contract_details->contract_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<input type="hidden"  name="page" value="schedules"/>
			<div class="x_panel tile has-shadow">
				<legend>Linked Buildings</legend>
				<div class="form-group" >
					<div class="drop-shaddow">
						<input type="text" id="search_term" class="grey-bg form-control <?php echo $module_identier; ?>-search_input" value="" placeholder="Search linked building..." />
					</div>
				</div>
				<br/>
				<div class="x_panel drop-shaddow">
					<table class="sortable datatable table table-responsive" style="margin-bottom:0; width:100%">
						<thead>
							<tr>
								<th>ID</th>
								<th>Building Name</th>
								<th>Building Reference</th>
								<th>Building Address</th>
								<th>Building Postcodes</th>
								<th>Status</th>
								<th><span class="pull-right">Action</span></th>
							</tr>
						</thead>
						<tbody id="contract-sites-results" style="overflow-y:auto;" >

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
			'contract_id': '<?php echo $contract_details->contract_id;?>'
		};

		load_data( search_str, where, start_index );

		$( '#contract-sites-results' ).on( 'click', '.unlink-item', function(){
			swal({
				type: 'info',
				text: 'Unlink Functionality coming soon'
			});
		});

		$( '#contract-sites-results' ).on( 'click', '.delete-item', function(){
			
			var siteId 		= $( this ).data( 'site_id' );
			var contractId 	= $( this ).data( 'contract_id' );
			
			swal({
				title: 'Confirm unlink Building?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ) {
				var uRl = "<?php echo base_url( '/webapp/contract/unlink_site/' ); ?>"+ siteId + '/' + contractId + '/linked_sites'; 
				window.location.href = uRl;
			});
		});

		$( "#contract-sites-results" ).on( "click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find( 'a' ).data( 'ciPaginationPage' );
			load_data( search_str, where, start_index );
		});

		function load_data( search_str, where, start_index ){
			$.ajax({
				url:"<?php echo base_url( 'webapp/contract/contract_buildings_lookup/'.$contract_details->contract_id ); ?>",
				method:"POST",
				data:{ search_term:search_str, where:where, start_index:start_index },
				success:function(data){
					$( '#contract-sites-results' ).html( data );
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
