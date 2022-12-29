<style>
	#pleaseWaitDialog{
		margin-top:12%;
	}
</style>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<form id="update-premises-form" class="form-horizontal">
			<input type="hidden" name="site_id" value="<?php echo $site_details->site_id; ?>" />
			<input type="hidden" name="site_unique_id" value="<?php echo $site_details->site_unique_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<input type="hidden"  name="page" value="premises"/>
			<div class="x_panel tile has-shadow">
				<legend>Current Site Premises 
					<span id="add-new-premises" data-toggle="modal" data-target="#new-premises-modal" class="pull-right pointer" title="Add new Site Premises">
						<i class="fas fa-plus text-green" title="Add Site Premises" ></i>
					</span>
				</legend>
				<div class="form-group" >
					<div class="drop-shaddow">
						<input type="text" id="search_term" class="grey-bg form-control <?php echo $module_identier; ?>-search_input" value="" placeholder="Search premises..." />
					</div>
				</div>
				<br/>
				<div class="x_panel drop-shaddow">
					<table class="sortable datatable table table-responsive" style="margin-bottom:0; width:100%">
						<thead>
							<tr>
								<th width="20%">Premises ID</th>
								<th width="20%">Premises Type</th>
								<th width="20%">Premises Desc</th>
								<th width="20%">Primary Attribute</th>
								<th width="10%"><span class="pull-right">Status</span></th>
								<th width="10%"><span class="pull-right">Action</span></th>
							</tr>
						</thead>
						<tbody id="premises-results" style="overflow-y:auto;" >

						</tbody>
					</table>
				</div>
			</div>
		</form>
	</div>
</div>

<div id="new-premises-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
		
			<form id="premises-creation-form" method="post" >
				<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
				<input type="hidden" name="page" value="details"/>
				<input type="hidden" name="site_id" value="<?php echo $site_details->site_id; ?>" />
		
				<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title" id="myModalLabel">Add New Site Premises</h4>
				</div>
				<div class="modal-body">
				
					<div class="input-group form-group">
						<label class="input-group-addon">Premises Type</label>
						<select id="premises_type_id" name="premises_type_id" class="form-control required">
							<option value="">Please select</option>
							<?php if (!empty($premises_types)) {
							    foreach ($premises_types as $premises_type) { ?>
								<option value="<?php echo $premises_type->premises_type_id; ?>" data-premises_type_ref="<?php echo $premises_type->premises_type_ref; ?>" ><?php echo $premises_type->premises_type; ?></option>
							<?php }
							    } ?>
						</select>
					</div>
					
					<div id="premises_desc" style="display:none">
						<div class="input-group form-group">
							<label class="input-group-addon">Premises Description</label>
							<input name="premises_desc" class="form-control" type="text" placeholder="Premises Description" value="" />
						</div>
					</div>
					
					<div class="row" id="premises_type_attributes" style="display:none">

					</div>
				</div>
				
				<div class="modal-footer">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<button id="create-premises-btn" class="btn btn-block btn-flow btn-success btn-next" type="button" >Add Site Premises</button>
						</div>
					</div>
				</div>
			</form>
		</div>
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

		$( '#premises-results' ).on( 'click', '.unlink-item', function(){
			swal({
				type: 'info',
				text: 'Unlink Functionality coming soon'
			});
		});

		$( '#premises-results' ).on( 'click', '.delete-item', function(){
			swal({
				type: 'warning',
				text: 'Delete Functionality coming soon'
			});
		});

		//Submit Site Premises form
		$( '#create-premises-btn' ).click(function( e ){
			e.preventDefault();
			var formData = $( '#premises-creation-form' ).serialize();
			swal({
				title: 'Confirm new Site Premises?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( (result) => {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/premises/create_premises/'); ?>",
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
									$( "#new-premises-modal" ).modal( "hide" );
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

		$("#premises-results").on( "click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find( 'a' ).data( 'ciPaginationPage' );
			load_data( search_str, where, start_index );
		});

		function load_data( search_str, where, start_index ){
			$.ajax({
				url:"<?php echo base_url('webapp/premises/premises_lookup/'.$site_details->site_id); ?>",
				method:"POST",
				data:{ search_term:search_str, where:where, start_index:start_index },
				success:function(data){
					$( '#premises-results' ).html( data );
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
			
		//Clone Premises
		$( "#premises-results" ).on( "click", ".clone-premises-btn", function( e ){
			
			e.preventDefault();
			
			var premisesId = $( this ).data( 'premises_id' );
			
			submitClonePremisesForm(premisesId);
			
		});
		
		$( "#premises_type_id" ).on( "change", function( e ){
	
			e.preventDefault();
			
			var premisesTypeId	= $( 'option:selected', this ).val();

			if( premisesTypeId.length > 0 ){
				
				$( '#premises_desc' ).slideDown( 'fast' );
				$( '#premises_type_attributes' ).show();
				
				// FETCH ATTRIBUTES
				$.ajax({
					url:"<?php echo base_url('webapp/premises/fetch_preset_attributes/'); ?>",
					method:"POST",
					data:{ page:'details', premises_type_id:premisesTypeId },
					dataType: 'json',
					beforeSend: function(){
						$( '#premises_type_attributes' ).html( '<div class="col-md-12">Fetching attributes, please wait...</div>' );
					},
					success:function( data ){
						console.log( data );
						if( data.status == 1 ){
							$( '#premises_type_attributes' ).html( data.attributes_data );
							$( ".add_new_attribute" ).css( "display", "none" );
							$( '#premises-unique-id' ).slideDown();
							return false;

						} else {
							$( '#premises_type_attributes' ).html( '<div class="col-md-12"><span class="text-red">'+ data.status_msg +'</span></div>' );
						}
					}
				});
				
			} else {
				$( '#premises_desc' ).slideUp( 'fast' );
				$( '[name="premises_desc"]' ).val( '' );
				$( '#premises_type_attributes' ).html( '' );
			}
			
		});
		
	});
	
</script>
