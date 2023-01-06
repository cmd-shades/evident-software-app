<style type="text/css">
#change-detected{
	display:block; 
	margin:-10px 0 10px 0;
}
</style>

<div class="row asset_details">

	<div class="col-md-6 col-sm-6 col-xs-12">
		<form id="update-asset-form-right" class="form-horizontal">
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="asset_id" value="<?php echo $asset_details->asset_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<div class="x_panel tile has-shadow">
				<legend>Asset Building &amp; Location</legend>
				<p id="change-detected" class="col-md-12 text-red"></p>
				<div class="input-group form-group">
					<label class="input-group-addon">Assigned Building</label>
					<?php 	if( !empty( $sites ) && ( $asset_details->site_id ) ) { ?>
								<input type="text" id="site_name" class="form-control" value="<?php echo ( !empty( $asset_details->site_name ) ) ? ( $asset_details->site_name ) : "" ; ?>" readonly="readonly" placeholder="" />
					<?php 	} else { ?>
								<input type="text" id="site_name" readonly="readonly" class="form-control" placeholder="" readonly="readonly" />
					<?php 	} ?>
					<input type="hidden" id="hidden_site_id" />
					<input type="hidden" id="hidden_site_name" />
					<input type="hidden" id="site_id" type="text" name="site_id" value="<?php echo ( !empty( $asset_details->site_id ) ? ( int ) $asset_details->site_id : "" ) ?>"/>
					<?php if( !empty( $asset_details->assignee ) ){ ?>
						<div class="search_site_btn" data-toggle="modal"><i class="fas fa-search" onclick="Swal('Asset already assigned to person','Please unassign it first!','warning')"></i></div>
					<?php } else { ?>
						<div class="search_site_btn" data-toggle="modal" data-target="#site_search_cont"><i class="fas fa-search"></i></div>
					<?php } ?>
					
				</div>
				
				<div class="input-group form-group">
					<label class="input-group-addon">Zone</label>
					<?php if( !empty( $site_zones ) ){ ?>
						<select name="zone_id" class="form-control">
							<option value="">Please select</option>
							<?php foreach( $site_zones as $zone ){ ?>
								<option value="<?php echo $zone->zone_id; ?>" <?php echo ( $zone->zone_id == $asset_details->zone_id ) ? 'selected="selected"' : '' ?>><?php echo ( !empty( $zone->zone_name ) ) ? $zone->zone_name : '' ; ?></option>
							<?php } ?>
						</select>
					<?php } else { ?>
							<select name="zone_id" class="form-control">
								<option value="">Please Assign to Site</option>
							</select>
					<?php } ?>
				</div>

				<?php if( !empty( $site_zones ) && !empty( $asset_details->site_id ) && !empty( $asset_details->zone_id ) && !empty( $asset_details->location_id ) ){ ?>
					<div class="input-group form-group" id="location">
						<label class="input-group-addon">Location</label>
						<?php if( !empty( $site_locations ) ){ ?>
							<select name="location_id" class="form-control">
								<option value="">Please select</option>
								<?php foreach( $site_locations as $loc ){ ?>
									<option value="<?php echo $loc->location_id; ?>" <?php echo ( $loc->location_id == $asset_details->location_id ) ? 'selected="selected"' : '' ?>><?php echo ( !empty( $loc->location_name ) ) ? $loc->location_name : '' ; ?></option>
								<?php } ?>								
							</select>
						<?php } ?>
					</div>
				<?php } else { ?>
					<div class="input-group form-group" id="location">
						<label class="input-group-addon">Location</label>
						<select name="location_id" class="form-control">
							<option value="">Please select Zone</option>
						</select>
					</div>
				<?php } ?>

				<?php if( $this->user->is_admin || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
					<div class="row col-md-6">
						<button id="update-asset-btn-2" class="btn btn-sm btn-block btn-flow btn-success btn-next update-asset-btn" type="button" >Update Details</button>
					</div>
				<?php }else{ ?>
					<div class="row col-md-6">
						<span id="no-permissions-2" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</span>
					</div>
				<?php } ?>
			</div>
		</form>
	</div>
	
	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Asset Information</legend>
			<table style="width:100%">
				<tr>
					<td colspan="2">
						<div class="full-width">
							<iframe width="100%" height="280" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%&height=280&hl=en&q=<?php echo ( !empty( $asset_details->site_postcodes ) ) ? str_replace( " ", "+", $asset_details->site_postcodes ) : "" ; ?>&ie=UTF8&t=&z=16&iwloc=B&output=embed"></iframe>
						</div>				
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>


<!-- The Modal -->
<div class="modal" id="site_search_cont">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Assign the Asset to the Site</h4>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<h5 class="modal-title">Type the name of the site and you will be provided with possible match</h5>
				<input id="site_search" class="form-control" type="text" placeholder="" value="<?php echo ( ( !empty( $sites ) ) && ( !empty( $asset_details->site_id ) ) && ( !empty( $sites->{$asset_details->site_id }->site_name ) ) ) ? $sites->{$asset_details->site_id }->site_name : "" ; ?>" />
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" id="remove_from_site" data-dismiss="modal">Remove from Site</button>
				<button type="button" class="btn btn-success" data-dismiss="modal" id="apply_site_search" >Apply</button>
			</div> 

		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$( "select[name='zone_id']" ).change( function(){
			
			var zoneID = $( this ).val();
			var siteID = $( 'input[name="site_id"]' ).val();
			$.ajax({
				url:"<?php echo base_url( 'webapp/asset/get_site_locations/' ); ?>",
				method: "POST",
				data:{ 
					zone_id:zoneID,
					site_id:siteID,
				},
				dataType: 'json',
				success:function( data ){
					$( "select[name='location_id']" ).empty();
					$( "select[name='location_id']" ).append( data.site_locations );
				}
			});
		});
		
		$( "#apply_site_search" ).click( function(){
			var siteID = $( "#hidden_site_id" ).val();
			$( "#site_id" ).val( siteID );
			
			var site_name = $( "#hidden_site_name" ).val();
			$( "#site_name" ).val( site_name );
			
			$.ajax({
				url:"<?php echo base_url( 'webapp/asset/get_site_zones/' ); ?>",
				method:"POST",
				data:{ site_id:siteID },
				dataType: 'json',
				success:function( data ){
					$( "select[name='zone_id']" ).empty();
					$( "select[name='zone_id']" ).append( data.site_zones );
					$( "select[name='location_id']" ).empty();
				}
			});
		});
		
 		$( "#remove_from_site" ).click( function(){
			$( "#site_search" ).val( '' );
			$( "#site_id" ).val( '' );
			$( "#site_name" ).val( '' );
			$( "select[name='zone_id']" ).empty();
			$( "select[name='location_id']" ).empty();
			
		});
	
		var sites_data = [
			<?php
			foreach( $sites as $row ){
				echo '{';
				echo 'value:"'.( !empty( $row->site_id ) ? $row->site_id : '' ).'",';
				echo 'label:"'.( !empty( $row->site_name ) ? html_escape( $row->site_name ) : '' ).'",';
				echo 'site_reference:"'.( !empty( $row->site_reference ) ? html_escape( $row->site_reference ) : '' ).'",';
				echo 'summaryline:"'.( !empty( $row->summaryline ) ? html_escape( $row->summaryline ) : '' ).'",';
				echo 'site_postcodes:"'.( !empty( $row->site_postcodes ) ? html_escape( $row->site_postcodes ) : '' ).'",';
				echo '},';
			} ?>
		];

		$.ui.autocomplete.prototype._renderItem = function(	ul, item ){
			return $( '<li></li>' )
				.data( "item.autocomplete", item )
				.append( "<div class='site_item'>" + item.label + ", " + item.site_postcodes + ", " + item.site_reference + "</div>" )
				.appendTo( ul );
		};

 		$( ".modal-body #site_search" ).autocomplete({
			source: function( request, response ){
				var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
				response( $.grep( sites_data, function( value ) {
					return matcher.test( value.value ) || matcher.test( value.label ) || matcher.test( value.site_reference ) || matcher.test( value.summaryline ) || matcher.test(value.site_postcodes );
				}));
			},
			focus: function( event, ui ) {
				$( ".modal-body #site_search" ).val( ui.item.label );
				return false;
			},
			select: function( event, ui ) {
				$( ".modal-body #site_search" ).val( ui.item.label );
				$( ".asset_details #hidden_site_id" ).val( ui.item.value );
				$( ".asset_details #hidden_site_name" ).val( ui.item.label );
				return false;
			}
		});
	
		$('#new-assignee').change(function(){
			$('#assignee').val( $(this).val() );
		});

		$('#asset_type').change(function(){
			var assetType 	= $(this).val();
			var assetGroup 	= $( 'option:selected', this).data( 'asset_group' );
			//Check group
			if( $.inArray( assetGroup, ['panel'] ) != -1 ){
				$( '#alarm-panel-info' ).slideDown();
				$( '#alarm_panel_code, #event_tracking_status' ).prop( 'required', true );
			}else{
				$( '#alarm-panel-info' ).slideUp();
				$( '#alarm_panel_code, #event_tracking_status' ).val( '');
				$( '#alarm_panel_code, #event_tracking_status' ).prop( 'required', false );
			}
		});
		
		$('#asset_type_id').change(function(){
			assetTypeID = $(this).val();
			originalAssetTypeID = <?php echo (!empty( $asset_type->asset_type_id ) ) ? $asset_type->asset_type_id : '""' ; ?>;
			
			if(assetTypeID != originalAssetTypeID){
				Swal.fire({
			      /* title: 'Are you sure?', */
			      text: "Changing asset type will wipe asset attributes!",
			      type: 'warning',
				  showCancelButton: true,
				  confirmButtonText: 'Continue'
			    }).then((result) => {
			      	if(!result.value){
						$(this).val(originalAssetTypeID)
					}
			    })
			}
		});

		//Re-assign the location based on selected status
		$('#asset_status').change(function(){
			var statusGroup 	= $( 'option:selected', this).data( 'status_group' );
			if( $.inArray( statusGroup, ['unassigned'] ) != -1 ){
				$('#new-assignee option[value=""]').prop('selected','selected');
			}
		});

		//Submit form for processing
		$( '.update-asset-btn' ).click( function( event ){
			event.preventDefault();

			var assetType 	= $('#asset_type').val();
			var panelStatus = $('#event_tracking_status option:selected').val();
			var assetGroup 	= $( '#asset_type option:selected').data( 'asset_group' );

			//Asset status
			var assetAssignee 	= $( '#new-assignee' ).val();
			var assetSite 		= $( '#site_id' ).val();

			//var formData = $('#update-asset-form').serialize();
			var formData = $(this).closest('form').serialize();
			swal({
				title: 'Confirm asset update?',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/asset/update_asset/'.$asset_details->asset_id ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2500
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

		//Delete asset
		$('#delete-asset-btn').click(function(){

			var assetId = $(this).data( 'asset_id' );

			swal({
				title: 'Confirm asset delete?',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/asset/delete_asset/'.$asset_details->asset_id ); ?>",
						method:"POST",
						data:{asset_id:assetId},
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
									window.location.href = "<?php echo base_url('webapp/asset/assets'); ?>";
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
		
		
		$( '#asset_type_attributes' ).on( 'change', '.check-all', function(){
			var attributeId = $( this ).data( 'attribute_id' );
			if( $(this).is( ':checked' ) ){
				$( '.check-opts'+attributeId ).each( function(){
					$( this ).prop( 'checked', true );
				});
			} else {
				$( '.check-opts'+attributeId ).each( function(){
					$( this ).prop( 'checked', false );
				});
			}			
		} );
		
		$( '#asset_type_attributes' ).on( 'change', '.check-options', function(){
			var attributeId = $( this ).data( 'attribute_id' ),
				chkCount  	= 0,
				totalChekd	= 0,
				unChekd   	= 0;
				
			$( '.check-opts'+attributeId ).each( function(){
				chkCount++;
				if( $( this ).is( ':checked' ) ){
					totalChekd++;					
				} else {
					unChekd++;
				}
			});

			if( chkCount > 0 && ( chkCount == totalChekd ) ){
				$( '#check-all'+attributeId ).prop( 'checked', true );
			}else{
				$( '#check-all'+attributeId ).prop( 'checked', false );
			}
		} );
	});
</script>