<div class="row section-header">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<legend class="legend-header" >Please confirm which Building this asset belongs to</legend>
	</div>
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="form-group  has-shadow" >
			<select id="site_id" name="site_id" class="form-control" style="width:100%; margin-bottom:10px; background-color:none"  data-label_text="Parent Building" >
				<option value="" >Select Building</option>
				<?php if (!empty($sites)) {
				    foreach ($sites as $k => $site) { ?>
					<option value="<?php echo $site->site_id; ?>" ><?php echo $site->site_name; ?> <?php echo !empty($site->postocde) ? $asset->postocde : ''; ?></option>
				<?php }
				    } ?>
			</select>
			
		</div>
		
		<div id="zones_container" class="col-md-12 col-sm-12 col-xs-12" style="display:none" >
			
			<div class="form-group" >
				<h4>Please select the Sub-block <small>(where applicable)</small></h4>
				<select id="sub_block_id" class="form-control" style="width:100%" data-label_text="Building Sub-block" >
					<option value="1" >1 - 50</option>
					<option value="2" >51 - 99</option>
					<option value="3" >100 - 150</option>				
				</select>
			</div>
			
			<h4>Please select the Zone</h4>
			<div class="form-group" >
				<select id="zone_id" name="zone_id" class="form-control" style="width:100%; margin-bottom:10px; background-color:none"  data-label_text="Site Zone" >

				</select>
			</div>
			
			<div id="locations_container" class="col-md-12 col-sm-12 col-xs-12" style="display:none" >
				<div class="form-group  has-shadow" >
					<h4>Please select the Location</h4>
					<select id="location_id" name="location_id" class="form-control" style="width:100%" data-label_text="Zone locations" >

					</select>
				</div>
			</div>
		</div>
		
	</div>
</div>

<div class="row section-header">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<legend class="legend-header" >Is this Asset linked to a another Asset?</legend>
		<div class="form-group  has-shadow" >
			<select id="is_asset_linked" class="form-control" style="width:100%" data-label_text="Parent Asset" >
				<option value="Yes" >Yes</option>
				<option value="No" selected >No</option>				
			</select>
		</div>
	</div>
	
	<div id="linked_asset_container" class="col-md-12 col-sm-12 col-xs-12" style="display:none" >
		<legend class="legend-header" >Select Linked Asset</legend>
		<div class="form-group  has-shadow" >
			<select id="select-linked-asset" style="width:100%" data-label_text="Parent Asset" name="parent_asset_id" >
				<option value="" disabled selected>Select linked asset</option>
				<?php if (!empty($existing_assets)) {
				    foreach ($existing_assets as $k => $asset) { ?>
					<option value="<?php echo $asset->asset_id; ?>" ><?php echo !empty($asset->asset_unique_id) ? $asset->asset_unique_id : ''; ?> <?php echo !empty($asset->attribute_value) ? ' - '.$asset->attribute_value : ''; ?></option>
				<?php }
				    } ?>
			</select>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){
		
		$( '#site_id' ).select2({});
		
		$( '#zone_id' ).select2({});
		
		
		$( '#is_asset_linked' ).change( function(){
			var selectedOpt = $( 'option:selected', this ).val();
			if( selectedOpt == 'Yes' ){
				$( '#linked_asset_container' ).slideDown();
			} else {
				$( '#linked_asset_container' ).slideUp();
			}
		});
		
		$( '#site_id' ).change( function(){
			var siteId = $( 'option:selected', this ).val();
			
			$( '#zone_id' ).prop( "selectedIndex", 0 );
			$( '#location_id' ).prop( "selectedIndex", 0 );
			$( '.opts' ).hide();
			
			if( siteId.length > 0 ){
				$.post( "<?php echo base_url('webapp/asset/get_site_zones/'); ?>"+siteId,{ site_id:siteId },function( result ){
					$( "#zone_id" ).html( result['site_zones'] );				
					$( "#location_id" ).html( result['zone_locations'] );				
				}, "json" );
				
				$( '#zones_container' ).slideDown();
			} else {
				$( '#zones_container' ).slideUp();
			}
		});
		
		$( '#zone_id' ).change( function(){
			var zoneId = $( 'option:selected', this ).val();
			$( '#location_id' ).prop( "selectedIndex", 0 );
			if( zoneId.length > 0 ){
				$( '.opts' ).hide();
				$( '.opt' + zoneId ).show();
				$( '#locations_container' ).slideDown();
			} else {
				$( '.opts' ).hide();
				$( '#locations_container' ).slideUp();
			}
		});

		$('#select-linked-asset').select2();
	});
</script>