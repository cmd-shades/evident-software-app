<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<form id="update-locations-form" class="form-horizontal">
			<input type="hidden" name="site_id" value="<?php echo $site_details->site_id; ?>" />
			<input type="hidden" name="site_unique_id" value="<?php echo $site_details->site_unique_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<input type="hidden"  name="page" value="locations"/>
			<div class="x_panel tile has-shadow">
				<legend>Current Locations <span id="add-site-location" data-target="#addNewLocationModal" data-toggle="modal"  class="pull-right pointer add-site-location"><i class="fas fa-plus text-green" title="Add New Location" ></i></span></legend>
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
								<th width="25%" >Zone Name</th>
								<th width="20%" >Location Name</th>
								<th width="25%" >Resident Name</th>
								<th width="20%" >Location Type</th>
								<!-- <th width="15%" >Sub Block</th> -->
								<th width="10%" ><span class="pull-right">Action</span></th>
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


<!-- Modal for adding a new asset type -->
<div id="addNewLocationModal" class="modal fade add-site-location-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<form id="add-site-location-form">
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="site_id" value="<?php echo $site_details->site_id; ?>" />
			<input type="hidden" name="address_id" value="<?php echo $site_details->site_address_id; ?>" />
			<div class="modal-content">
				<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title" id="myAssetTypeModalLabel">Add New Location</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							
							<div class="input-group form-group">
								<label class="input-group-addon">Zone Name</label>
								<select id="zone_id" name="zone_id" class="form-control required" style="width:90%">
									<option value="">Please select</option>
									<?php if (!empty($site_zones)) {
									    foreach ($site_zones as $k => $zone) { ?>
										<option value="<?php echo $zone->zone_id; ?>" ><?php echo $zone->zone_name; ?><?php echo !empty($zone->zone_description) ? ' - '.$zone->zone_description : ''; ?></option>
									<?php }
									    } ?>
								</select>
								<div style="margin-top: 4px; display:inline-block; width:3%" class="zone-quick-add-toggle pointer pull-right" title="Quick Add new zone option"><span class="pull-right"><i class="fas fa-plus-circle fa-2x text-green"></i></span></div>
							</div>

							<div class="location-fields" style="display:block" >

								<div class="input-group form-group">
									<label class="input-group-addon">Location Type</label>
									<select id="location_type_id" name="location_type_id" class="form-control required">
										<option value="">Please select</option>
										<?php if (!empty($location_types)) {
										    foreach ($location_types as $location_type_id => $location_type) { ?>
											<option value="<?php echo $location_type_id; ?>" <?php echo ($location_type_id == 1) ? "selected='selected'" : ""; ?> ><?php echo $location_type; ?></option>
										<?php }
										    } ?>
									</select>
								</div>

								<div class="input-group form-group">
									<label class="input-group-addon">Location Name</label>
									<input name="location_name" class="form-control" type="text" value="" placeholder="Location Name" required=required />
								</div>

								<div class="optional-fields" style="display:block" >
									<h4>Optional Fields</h4>
									<div class="input-group form-group">
										<label class="input-group-addon">Resident First Name</label>
										<input name="resident_first_name" class="form-control" type="text" value="" placeholder="Resident First Name" required=required />
									</div>
									
									<div class="input-group form-group">
										<label class="input-group-addon">Resident Last Name</label>
										<input name="resident_last_name" class="form-control" type="text" value="" placeholder="Resident Last Name" required=required />
									</div>
									
									<div class="input-group">
										<label class="input-group-addon">Location Notes</label>
										<textarea id="location_notes" name="location_notes" type="text" class="form-control" rows="2"></textarea>     
									</div>
									
								</div>							
								<hr>
								<div class="row form-group">
									<div class="col-md-12 col-sm-12 col-xs-12">
										<button id="add-site-location-btn" class="btn btn-block btn-success" type="button" >Add Location</button>
									</div>
								</div>							
							</div>							
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<!-- Modal for adding a new zone -->
<div class="modal fade add-zone-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<form id="add-zone-form-container" >
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="site_id" value="<?php echo $site_details->site_id; ?>" />
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title" id="myZoneModalLabel">Add New Zone</h4>
					<span id="zone-feedback-message"></span>
				</div>
				<div class="modal-body">
					<div class="row">
						<input type="hidden" name="page" value="details" />
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h4>What is the name of this Zone?</h4>
							<div class="form-group">
								<input name="zone_name" class="form-control" type="text" value="" placeholder="Zone name" required=required />
							</div>
							<h4>Please provide a description</h4>
							<div class="form-group">
								<textarea rows="3" name="zone_description" class="form-control" type="text" value="" placeholder="Zone Description" required=required /></textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<button id="zone-quick-add-btn" class="btn btn-success btn-block" type="button">Add Zone</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<?php /*

    <!-- OLD VERSION -->

<div id="new-location-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Site Locations</h4>
            </div>
            <div class="modal-body">
                <div class="">
                    <form id="add-locations-form" >
                        <input type="hidden" name="site_id" value="<?php echo $site_details->site_id; ?>" />
                        <input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
                        <input type="hidden"  name="page" value="locations"/>

                        <div class="input-group form-group">
                            <label class="input-group-addon" >Building Postcode</label>
                            <input type="text" id="site_postcodes" value="<?php echo !empty( $site_details->address_postcode ) ? $site_details->address_postcode : ''; ?>" class="form-control site-postcodes <?php echo $module_identier; ?>-search_input"  placeholder="Enter the address postcode..." >
                            <span class="input-group-btn"><button id="find_address" class="btn btn-default <?php echo $module_identier; ?>-bg" type="button" >Find address</button></span>
                        </div>
                        <div class="dwelling-container" style="display:block" >
                            <?php if( !empty( $site_postcode ) ){ ?>
                                <div class="hide row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <button class="btn btn-sm btn-block btn-flow btn-success add-locations-btn" type="button" >Add Selected Location</button>
                                    </div>
                                </div>
                                <div id="building-addresses">

                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <button id="add-locations-btn" class="btn btn-sm btn-block btn-flow btn-success add-locations-btn" type="button" >Add Selected Location</button>
                                    </div>
                                </div>
                            <?php }else{ ?>
                                <div>
                                    <span>No addresses on this Block or you have an invalid postcode.</span>
                                </div>
                            <?php } ?>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

*/ ?>

<script>
	$( document ).ready( function(){
		

		//Trigger Category modal
		$( '.zone-quick-add-toggle' ).click( function(){
			$( '.add-zone-modal' ).modal( 'show' );
		} );
		
		// New Zone Quick add
		$( '#zone-quick-add-btn' ).click(function(){

			var formData = $( "#add-zone-form-container" ).serialize();

			$.ajax({
				url:"<?php echo base_url('webapp/site/add_site_zone'); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){

						$(".add-zone-modal").modal("hide");

						var zoneId 	 = data.zone.zone_id;
						var zoneName = data.zone.zone_name;
						var zoneDesc = data.zone.zone_description;

						$('#zone-feedback-message').html( data.status_msg ).delay( 3000 ).fadeToggle( "slow" );

						var optionExists = ( $('#zone_id option[value="' + zoneId + '"]').length > 0 );

						if( !optionExists ){
							//Only add the new option if it doesn't already exist
							$('#zone_id').append( $('<option >').val( zoneId ).text( zoneName + ' - ' + zoneDesc ) );
						}

						//Set selected
						$('#zone_id option[value="'+zoneId+'"]').prop( 'selected', true );

					}else{
						swal({
							type: 'error',
							title: data.status_msg
						})
					}
				}
			});
			return false;

		});
		
		
		//################# 
		
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
			
			var siteId = "<?php echo $site_details->site_id ?>";
			var locationId = $( this ).data( 'location_id' );

			if( locationId.length == 0 ){
				swal({
					type: 'error',
					html: 'Something went wrong, please refresh the page and try again!',
					showCancelButton: true,
					confirmButtonColor: '#5CB85C',
					cancelButtonColor: '#9D1919',
					confirmButtonText: 'Yes'
				});
				return false;
			}

			event.preventDefault();

			swal({
				type: 'warning',
				title: 'Confirm delete location?',
				html: 'This is an irreversible action and will affect associated Assets, Jobs, Schedules etc.!',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/site/delete_site_location/'.$site_details->site_id.'/'); ?>"+locationId,
						method:"POST",
						data:{ page:'details' , xsrf_token: xsrfToken, site_id:siteId, location_id:locationId },
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2100
								})
								window.setTimeout(function(){
									location.reload();
								} ,1000);
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
		
		$('.site-postcodes').focus(function(){
			$( '.location-container' ).slideDown( 'slow' );
		});

		// LOAD ADDRESSES WHEN MODAL OPENS
		$( '.add-locations' ).click( function(){
			var postCode = $( '#site_postcodes' ).val();
			var siteID 	 = '<?php echo $site_details->site_id;?>';
			
			if( postCode.length > 0 ){
				$.post( "<?php echo base_url("webapp/site/get_addresses_by_postcode"); ?>",{postcodes:postCode, site_id:siteID},function(result){
					$( "#building-addresses" ).html( result["addresses_list"] );				
					$("#new-location-modal").modal( "show" );			
				},"json" );
			} else {
				$( "#new-location-modal" ).modal( "show" );
			}
		});
		
		//LOAD ADDRESSES WHEN POSTCODE IS CHANGED IN THE MODAL
		$( '.site-postcodes' ).change(function(){
			var postCode = $(this).val();
			var siteID 	 = '<?php echo $site_details->site_id;?>';
			if( postCode.length > 0 ){
				$.post("<?php echo base_url("webapp/site/get_addresses_by_postcode"); ?>",{postcodes:postCode, site_id:siteID},function(result){
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
		
		//Submit Location form
		$( '#add-site-location-btn' ).click(function( e ){
			
			var zoneID 		 = $( '#zone_id option:selected').val();
			var locationType = $( '#location_type_id option:selected').val();
			var locationName = $( '[name="location_name"]' ).val();

			if( zoneID.length == 0 ){
				swal({
					type: 'error',
					title: 'Please select the Zone!',
				})
				return false;
			}
			
			if( locationType.length == 0 ){
				swal({
					type: 'error',
					title: 'Please select the Location Type!',
				})
				return false;
			}

			if( locationName.length == 0 ){
				swal({
					type: 'error',
					title: 'Please provide the Location name!',
				})
				return false;
			}

			e.preventDefault();
			var formData = $('#add-site-location-form').serialize();
			swal({
				title: 'Confirm New Location?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( (result) => {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/site/add_site_location/'); ?>",
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
									$( "#addNewLocationModal" ).modal( "hide" );
									//$( "#new-location-modal" ).modal( "hide" );
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
		
		$("#locations-results").on( "click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find( 'a' ).data( 'ciPaginationPage' );
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