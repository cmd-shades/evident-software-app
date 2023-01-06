<style>
	#pleaseWaitDialog{
		margin-top:12%;
	}
</style>

<div class="site-schedule" >
	
	<div class="building-details" >
		<div class="alert bg-blue no-border" role="alert">
			<table class="table-responsive" style="width:100%" >
				<tr>
					<th width="25%" ><legend>BUILDING NAME</legend></th>
					<th width="20%" ><legend>ESTATE NAME</legend></th>
					<th width="30%" ><legend>BUILDING ADDRESS</legend></th>
					<th width="25%" ><legend>COMPLIANCE STATUS</legend></th>
				</tr>
				<tr>
					<td ><span class="pull-left" ><a href="<?php echo base_url('/webapp/site/profile/'.$site_details->site_id.'/schedules' ); ?>"><?php echo ( !empty( $site_details->site_name ) ) ? $site_details->site_name : ''; ?></a></span></td>
					<td ><span class="pull-left" ><?php echo ucwords( $site_details->estate_name ); ?></span></td>
					<td ><span class="pull-left" ><?php echo ucwords( $site_details->summaryline ); ?></span></td>
					<td ><span class="pull-left" ><?php echo !empty( $site_details->result_status_alt ) ? $site_details->result_status_alt : ''; ?></span></td>
				</tr>
			</table>
		</div>
	</div>		
	
	<div class="hide progress">
		<div id="progress-bar" class="progress-bar progress-bar-success" data-transitiongoal="0" aria-valuenow="0" style="width: 0"></div>
	</div>
	
	<div class="x_panel tile has-shadow">
		<div class="schedule_creation_panel1 col-md-12 col-sm-12 col-xs-12">
			<h4>Please select your Schedule Frequency </h4>
			<div class="form-group" >
				<select id="frequency_id" name="frequency_id" class="form-control required" data-label_text="Schedule Frequency" >
					<option value="" >Please Select frequency</option>
					<?php if( !empty( $schedule_frequencies ) ) { foreach( $schedule_frequencies as $k => $frequency ) { ?>
						<option value="<?php echo $frequency->frequency_id; ?>" <?php echo ( $frequency->frequency_id == 78 ) ? 'selected="selected"' : ''; ?> data-frequency_name="<?php echo $frequency->frequency_name; ?>" data-frequency_desc="<?php echo $frequency->frequency_desc; ?>" data-activity_interval="<?php echo $frequency->activity_interval; ?>" data-frequency_group="<?php echo $frequency->frequency_group; ?>" data-activities_required="<?php echo $frequency->activities_required; ?>" ><?php echo $frequency->frequency_name; ?></option>
					<?php } } ?>
				</select>
			</div>
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn btn-block btn-flow btn-success btn-next schedule-creation-steps" data-currentpanel="schedule_creation_panel1" data-progress_level="20" type="button">Next</button>				
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">&nbsp;</div>
			</div>
		</div>

		<div class="schedule_creation_panel2 col-md-12 col-sm-12 col-xs-12" style="display:none" >
			<h4>Please select Category you wish to create Schedules for</h4>
			<div class="row">
				<!-- Get  list of Asset Types by Category ID -->
				<div>
					<div class="row">
						<?php foreach( $building_categories as $category => $category_data ){ ?>
							<div class="">
								<?php $category_ref = lean_string( $category ); ?>
								<div class="col-md-4 col-sm-4 col-xs-12">
									<div class="alert bg-blue panel-chk pointer" data-category_ref="<?php echo $category_ref; ?>" >
										<div class="row">
											<div class="col-md-12 col-sm-12 col-xs-12">
												<span><label class="text-white pointer"  ><input name="category_id[]" id="category-chk-<?php echo $category_ref; ?>" class="category-check-all" data-category_check_id="category-chk-<?php echo $category_ref; ?>" type="checkbox" value="<?php echo $category_data->category->category_id; ?>" > &nbsp;<?php echo ucwords( $category ); ?> <span class="hide">(<?php echo count( $category_data->asset_types ); ?>)</span></label></span>
												<span class="hide pull-right pointer asset-types-toggle" data-asset_types_check_id="<?php echo $category_ref; ?>" >View list</span>
											</div>
										</div>
									</div>
									<div class="hide _<?php echo $category_ref; ?> x_panel" style="display:block;" >
										<div class="col-md-12 col-sm-12 col-xs-12">
											<ul>
												<?php foreach( $category_data->asset_types as $key => $asset_type ){ ?>
													<div><label class="pointer" ><input name="asset_type_id[]" class="category-check category-chk-<?php echo $category_ref; ?>" data-category_class="category-chk-<?php echo $category_ref; ?>" type="checkbox" value="<?php echo $asset_type->asset_type_id; ?>" > &nbsp;<?php echo ucwords( $asset_type->asset_type ); ?></label></div>
												<?php } ?>
											</ul>
										</div>
									</div>
								</div>
							</div>						
						<?php } ?>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-back btn-warning" data-currentpanel="schedule_creation_panel2" data-progress_level="40"type="button" >Back</button>					
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-success btn-next fetch-assets-by-category" data-currentpanel="schedule_creation_panel2" data-progress_level="60" type="button" >Next</button>					
					</div>
				</div>
				
			</div>
		</div>

		<div class="schedule_creation_panel3 col-md-12 col-sm-12 col-xs-12" style="display:none" >
			<h4>Please select the Asset Types to create Schedules for</h4>
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div id="loading-indicator" style="display:none;" >Fetching data, please wait... <div style="display:inline-block"><img src="<?php echo base_url( '/assets/images/ajax-loader.gif' ); ?>" /></div></div>
				</div>
				
				<div id="assets_by_asset_type_category" >
					
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn btn-block btn-flow btn-back btn-warning" data-currentpanel="schedule_creation_panel3" data-progress_level="40"type="button" >Back</button>					
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn btn-block btn-flow btn-success btn-next fetch-evidocs-btn" data-currentpanel="schedule_creation_panel3" data-progress_level="60" type="button" >Next</button>					
				</div>
			</div>
		</div>
			
		
		<div class="schedule_creation_panel4 col-md-12 col-sm-12 col-xs-12" style="display:none" >
			<h4>Which EviDoc are these Schedules for?</h4>
			<div class="form-group" >
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-4 col-sm-4">
							<h4 class="bold" >Schedule Frequency</h4>
						</div>
						<div class="col-md-4 col-sm-4">
							<h4 class="bold" >Asset Type</h4>
						</div>
						<div class="col-md-4 col-sm-4">
							<h4 class="bold" >EviDoc Name</h4>
						</div>
					</div>
				</div>
				<div id="evidoc_type_id" >
					
				</div>
			
			</div>
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn btn-block btn-flow btn-back btn-warning" data-currentpanel="schedule_creation_panel4" data-progress_level="40"type="button" >Back</button>					
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn btn-block btn-flow btn-success btn-next fetch-job-types-btn" data-currentpanel="schedule_creation_panel4" data-progress_level="60" type="button" >Next</button>					
				</div>
			</div>
		</div>
		
		<div class="schedule_creation_panel5 col-md-12 col-sm-12 col-xs-12" style="display:none" >
			<h4>Which Job Type is this Schedule for?</h4>
			<div class="form-group" >
			
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-3 col-sm-3">
							<h4 class="bold" >Schedule Frequency</h4>
						</div>
						<div class="col-md-3 col-sm-3">
							<h4 class="bold" >Asset Type</h4>
						</div>
						<div class="col-md-3 col-sm-3">
							<h4 class="bold" >EviDoc Name</h4>
						</div>
						<div class="col-md-3 col-sm-3">
							<h4 class="bold" >Job Type</h4>
						</div>
					</div>
				</div>
			
				<div id="job_type_id" name="job_type_id" style="width:100%" >

				</div>

			</div>
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn btn-block btn-flow btn-back btn-warning" data-currentpanel="schedule_creation_panel5" data-progress_level="40"type="button" >Back</button>					
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn btn-block btn-flow btn-success btn-next schedule-creation-steps refine-selected-options" data-currentpanel="schedule_creation_panel5" data-progress_level="60" type="button" >Next</button>					
				</div>
			</div>
		</div>
		
		<form id="schedule-creation-form" >
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="contract_id" value="<?php echo ( !empty( $site_details->contract_id ) ) ? $site_details->contract_id : false; ?>" />
			<input type="hidden" name="location_id" value="" />
			<input type="hidden" name="site_id" value="<?php echo ( !empty( $site_details->site_id ) ) ? $site_details->site_id : false; ?>" />
		
			<div class="schedule_creation_panel6 col-md-12 col-sm-12 col-xs-12" style="display:none" >
				<h4>When is the first Activity due by?</h4>
				<div class="form-group" >
					<input name="first_activity_due_date" class="form-control nowonwards-date required" type="text" data-label_text="Schedule Due By" placeholder="dd-mm-yyyy" value="<?php echo date( 'd-m-Y' ); ?>" />
				</div>
				<div class="hide">
					<h4>How many time should each Asset be checked?</h4>
					<div class="form-group" >
						<input name="number_of_checks" class="form-control required" type="text" data-label_text="Number of Assets" placeholder="Number of Assets" value="1" />
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-back btn-warning" data-currentpanel="schedule_creation_panel6" data-progress_level="60" type="button" >Back</button>					
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-success btn-next prepare-schedule-placeholders" data-currentpanel="schedule_creation_panel6" data-progress_level="80" type="button" >Next</button>					
					</div>
				</div>
			</div>
			
			<div class="schedule_creation_panel7 col-md-12 col-sm-12 col-xs-12" style="display:none" >
				
				<div class="row" style="margin:15px 0">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-back btn-warning" data-currentpanel="schedule_creation_panel7" data-progress_level="80" type="button" >Back</button>					
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-success btn-next schedule-creation-steps" data-currentpanel="schedule_creation_panel7" data-progress_level="100" type="button" >Next</button>					
					</div>
				</div>
				
				<h4>Please Review your Schedule Activities Setup</h4>
				<div id="schedule_placeholders" class="form-group" >
					
				</div>

				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-back btn-warning" data-currentpanel="schedule_creation_panel7" data-progress_level="80" type="button" >Back</button>					
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-success btn-next schedule-creation-steps" data-currentpanel="schedule_creation_panel7" data-progress_level="100" type="button" >Next</button>					
					</div>
				</div>
			</div>
			
			<div class="schedule_creation_panel8 col-md-12 col-sm-12 col-xs-12" style="display:none" >
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="form-group">
							<div class="text-center">
								<br/>
								<p>You are about to submit a request to create Activity Schedules.</p>
								<p>Click the "Create Schedules" to proceed or Back to review your setup.</p>
								<br/>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-back" data-currentpanel="schedule_creation_panel8" type="button" >Back</button>					
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button id="create-schedule-btn" class="btn btn-block btn-flow btn-success btn-next" type="button" >Create Schedules</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<script>

	$( document ).ready( function() {
		
		var siteId = "<?php echo $site_details->site_id; ?>";
		
		//FETCH ASSET-TYPES-BY-SELECTED CATEGORY
		$( '.fetch-assets-by-category' ).click( function(){
			$('#loading-indicator').show();
			var categoryIds		= [];
			var assetTypeIds	= [];
			var currentpanel 	= $( this ).data( "currentpanel" );
			var progress_level 	= $( this ).data( "progress_level" );
			
			$( "[name='category_id[]']" ).each( function(){
				if( $( this ).is( ':checked' ) ){
					categoryIds.push( $( this ).val() );
				}
			});
			
			$( "[name='asset_type_id[]']" ).each( function(){
				if( $( this ).is( ':checked' ) ){
					assetTypeIds.push( $( this ).val() );
				}
			});
			
				
			if( categoryIds.length == 0 || categoryIds == '' ){
				swal({
					type: 'error',
					text: 'Please select at least 1 category to proceed!'
				});
				return false;
			}
			
			var wheRe = {
				site_id:siteId,
				category_id:categoryIds,
				asset_type_id:assetTypeIds,
			};

			$.ajax({
				url:"<?php echo base_url( 'webapp/job/fetch_assets_by_category/' ); ?>",
				method:"POST",
				data:{ page:'details', where:wheRe},
				dataType: 'json',
				success:function( data ){
					
					$('#loading-indicator').hide();
					
					if( data.status == 1 && ( data.assets_data !== '' ) ){
			
						$( '#assets_by_asset_type_category' ).html( data.assets_data );
						panelchange( "."+currentpanel );
						
						return false;

					}else{
						swal({
							type: 'warning',
							text: data.status_msg,
							showCancelButton: true,
							confirmButtonColor: '#5CB85C',
							cancelButtonColor: '#9D1919',
							confirmButtonText: 'Ok'
						}).catch( swal.noop )
						return false;
					}		
				}
			});
			
			return false;
			
		});		

		
		$( '.panel-chk' ).click( function(){
			var categoryRef 	= $( this ).data( 'category_ref' );
			var panelCheckId 	= $( '#category-chk-'+categoryRef );
			var categoryCheckId = $( '.category-chk-'+categoryRef );
			if( $( panelCheckId ).is( ':checked' ) ){
				$( panelCheckId ).prop( 'checked', false );
				$( categoryCheckId ).prop( 'checked', false );
			} else {
				$( panelCheckId ).prop( 'checked', true );
				$( categoryCheckId ).prop( 'checked', true );			
			}
		});
		
		$( '.category-check-all' ).click( function(){
			var categoryCheckId = $( this ).data( 'category_check_id' );
			if( $( this ).is( ':checked' ) ){
				$( '.'+categoryCheckId ).prop( 'checked', true );
			} else {
				$( '.'+categoryCheckId ).prop( 'checked', false );
			}			
		});
		
		
		$( '.category-check' ).click( function(){
			
			var chkCount  = 0;
			var totalChekd= 0;
			var unChekd   = 0;
			var ctaegoryClass = $( this ).data( 'category_class' );
			
			$( '.'+ctaegoryClass ).each(function(){
				chkCount++;
				if( $(this).is(':checked') ){
					totalChekd++;					
				} else {
					unChekd++;
				}
			});

			if( chkCount > 0 && ( chkCount == unChekd ) ){
				$( '#'+ctaegoryClass ).prop( 'checked', false );
			} else if( chkCount > 0 && ( chkCount == totalChekd ) ){
				$( '#'+ctaegoryClass ).prop( 'checked', true );				
			}else{
				$( '#'+ctaegoryClass ).prop( 'checked', true );
			}			
		});
		
		$( '#assets_by_asset_type_category' ).on( 'click', '.group-check-all', function(){
			var groupCheckId = $( this ).data( 'group_check_id' );
			if( $( this ).is( ':checked' ) ){
				$( '.'+groupCheckId ).prop( 'checked', true );
			} else {
				$( '.'+groupCheckId ).prop( 'checked', false );
			}
		} );
		
		$( '#evidoc_type_id' ).on( 'click', '.group-check-all', function(){
				var groupCheckId = $( this ).data( 'group_check_id' );
				if( $( this ).is( ':checked' ) ){
					$( '.'+groupCheckId ).prop( 'checked', true );
				} else {
					$( '.'+groupCheckId ).prop( 'checked', false );
				}			
		} );
		
		$( '.group-check' ).click( function(){
			
			var chkCount  = 0;
			var totalChekd= 0;
			var unChekd   = 0;
			var groupClass = $( this ).data( 'group_class' );
			
			$( '.'+groupClass ).each(function(){
				chkCount++;
				if( $(this).is(':checked') ){
					totalChekd++;					
				} else {
					unChekd++;
				}
			});

			if( chkCount > 0 && ( chkCount == unChekd ) ){
				$( '#'+groupClass ).prop( 'checked', false );
			} else if( chkCount > 0 && ( chkCount == totalChekd ) ){
				$( '#'+groupClass ).prop( 'checked', true );				
			}else{
				$( '#'+groupClass ).prop( 'checked', true );
			}			
		});
		
		
		$( '#assets_by_asset_type_category' ).on( 'click', '.asset-types-toggle', function(){
			var assetTypeCheckId = $( this ).data( 'asset_types_check_id' );
			$( '.'+assetTypeCheckId ).slideToggle( 'fast' );
		});
		
		$( '.asset-types-toggle' ).click( function(){
			var assetTypeCheckId = $( this ).data( 'asset_types_check_id' );
			$( '.'+assetTypeCheckId ).slideToggle( 'slow' );
		});		
		

		$( '.asset-list-toggle' ).click( function(){
			var checkId = $( this ).data( 'asset_check_id' );
			$( '.'+checkId ).slideToggle( 'slow' );
		});		
		

		$( '.nowonwards-date' ).datetimepicker({
			minDate:0,
			timepicker:false,
			format:'d-m-Y'
		});
		
		$( '.nowonwards-date' ).datetimepicker({
			minDate:0,
			timepicker:false,
			format:'d-m-Y'
		});
	
		var auditGroup 		= "asset";
		var siteId  		= "<?php echo ( $site_details->site_id ) ? $site_details->site_id : false; ?>";
		var schedActivities 	= 4; 
		$( '#schedule_frequencies' ).select2({
			allowClear: true,
			minimumResultsForSearch: -1,
		});
		
		//LOAD-EVIDOCS
		$( '.fetch-evidocs-btn' ).click( function(){
			
			var assetTypeIds	= [];
			var assetIds		= [];
			var currentpanel 	= $( this ).data( "currentpanel" );
			var progress_level 	= $( this ).data( "progress_level" );

			var freqId 	 		= $( '#frequency_id option:selected' ).val();
			var freqName 	 	= $( '#frequency_id option:selected' ).data( 'frequency_desc' );
			
			//$( '[name="number_of_checks"]' ).val( $( '#frequency_id option:selected' ).data( 'activities_required' ) );
			
			freqId			 	= ( freqId ) ? freqId: '';
			
			if( freqId.length == 0 || freqId == '' ){
				swal({
					type: 'error',
					text: 'Please choose a Schedule frequency'
				});
				return false;
			}

			$( "#assets_by_asset_type_category [name='asset_type_id[]']" ).each( function(){
				if( $( this ).is( ':checked' ) ){
					assetTypeIds.push( $( this ).val() );
				}
			});
			
			$( "#assets_by_asset_type_category [name='asset_id[]']" ).each( function(){
				if( $( this ).is( ':checked' ) ){
					var assetGroupName 	= $( this ).data( 'asset_type' ),
						assetGroupId 	= $( this ).data( 'asset_type_id' );
						assetUniqId 	= $( this ).data( 'asset_unique_id' );
					assetIds.push({
						asset_type_id: assetGroupId, 
						asset_type: assetGroupName, 
						asset_id:  $( this ).val(),
						asset_unique_id:  assetUniqId,
						frequency_id: freqId,
						frequency_name: freqName
					});
				}
			});
	
			if( assetTypeIds.length == 0 || assetTypeIds == '' ){
				swal({
					type: 'error',
					text: 'Please select at least 1 Asset Type to proceed!'
				});
				return false;
			}
				

			var wheRe = {
				frequency_id:freqId,
				audit_group:auditGroup,
				assets_data:assetIds,
			};
			
			if( freqName ){
				wheRe.frequency_name = encodeURIComponent( freqName );
			}

			$.ajax({
				url:"<?php echo base_url( 'webapp/job/fetch_evidocs_multi_display/' ); ?>",
				method:"POST",
				data:{ page:'details', where:wheRe},
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 && ( data.evidocs_data !== '' ) ){
					
						$( '#evidoc_type_id' ).html( data.evidocs_data );
						panelchange( "."+currentpanel );
			
						return false;

					}else{
						swal({
							type: 'warning',
							html: data.status_msg,
							showCancelButton: true,
							confirmButtonColor: '#5CB85C',
							cancelButtonColor: '#9D1919',
							confirmButtonText: 'Ok'
						}).catch( swal.noop )
						return false;
					}		
				}
			});
			
			return false;
			
		});
		
		$( '.fetch-job-types-btn' ).click( function(){
			
			var checkedAssets	= [],
				currentpanel 	= $( this ).data( "currentpanel" ),
				progress_level 	= $( this ).data( "progress_level" ),
				evidocTypeId	= [],
				freqId 	 		= $( '#frequency_id option:selected' ).val(),
				freqName 	 	= $( '#frequency_id option:selected' ).data( 'frequency_desc' );
			
			
			$( "#evidoc_type_id [name='asset_id[]']" ).each( function(){
				if( $( this ).is( ':checked' ) ){
					var assetGroupName 	= $( this ).data( 'asset_type' ),
						assetAssetTypeId= $( this ).data( 'asset_type_id' ),
						assetUniqId 	= $( this ).data( 'asset_unique_id' ),
						eviTypeId	= $( '#evidoc'+assetAssetTypeId+' option:selected' ).val(),
						eviTypeNm	= $( '#evidoc'+assetAssetTypeId+' option:selected' ).data( 'evidoc_type' );
					checkedAssets.push({
						asset_type_id: assetAssetTypeId, 
						asset_type: assetGroupName, 
						asset_id:  $( this ).val(),
						asset_unique_id:  assetUniqId,
						frequency_id: freqId,
						frequency_name: freqName,
						evidoc_type: encodeURIComponent( eviTypeNm ),
						evidoc_type_id: eviTypeId
					});
					evidocTypeId.push( eviTypeId );
				}
			});

			var evidocTypeId		= ( evidocTypeId ) ? evidocTypeId : '';
			
			if( evidocTypeId.length == 0 || evidocTypeId == '' ){
				swal({
					type: 'error',
					text: 'Please choose an Evidoc for this Schedule!'
				});
				return false;
			}

			var wheRe = {
				evidoc_type_id: $.unique( evidocTypeId ),
				assets_data:checkedAssets
			};

			$.ajax({
				url:"<?php echo base_url( 'webapp/job/fetch_job_types_multiple_display/' ); ?>",
				method:"POST",
				data:{ page:'details', where:wheRe},
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 && ( data.job_types_data !== '' ) ){

						$( '#job_type_id' ).html( data.job_types_data );
						panelchange( "."+currentpanel );
						
						return false;

					}else{
						swal({
							type: 'warning',
							text: data.status_msg,
							showCancelButton: true,
							confirmButtonColor: '#5CB85C',
							cancelButtonColor: '#9D1919',
							confirmButtonText: 'Ok'
						}).catch( swal.noop )
						return false;
					}		
				}
			});
			
			return false;
			
		});
		
		var selectedOptions = [];
		$( '.refine-selected-options' ).click( function(){
			$( '.selected-options' ).each( function(){
				var assType 	= $( this ).val(),
					assTypeID 	= $( this ).data( 'asset_type_id' ),
					eviDoc 		= $( this ).data( 'evidoc_type' ),
					eviDocId 	= $( this ).data( 'evidoc_type_id' ),
					jobTypeId	= $( '#jobtype'+assTypeID+' option:selected' ).val(),
					jobType		= $( '#jobtype'+assTypeID+' option:selected' ).data( 'job_type' )
					totalAsstes = $( this ).data( 'total_assets' );
					
				selectedOptions.push({
					asset_type_id: assTypeID,
					asset_type: assType,
					evidoc_type_id: eviDocId,
					evidoc_type: eviDoc,
					job_type_id: jobTypeId,
					job_type: jobType,
					total_assets: totalAsstes,
				});
			});
		} );
		
		//CLEAR RED-BORDERED ELEMENTS
		$( '.required' ).change( function(){
			$( this ).css("border","1px solid #ccc");
		});
		
		$(".schedule-creation-steps").click(function(){
			//CLEAR ERRORS FIRST
			$( '.error_message' ).each(function(){
				$( this ).text( '' );
			});
			
			var currentpanel 	= $(this).data( "currentpanel" );
			var progress_level 	= $(this).data( "progress_level" );
			
			var inputs_state = check_inputs( currentpanel );			
			if( inputs_state ){
				$( '[name="'+inputs_state+'"]' ).focus().css("border","1px solid red");
				var labelText = $( '[name="'+inputs_state+'"]' ).parent().find('label').text();
					labelText = ( labelText !== "" && ( labelText.length > 0 ) ) ? labelText : $( '[name="'+inputs_state+'"]' ).data( 'label_text' )+' is a required';
				swal({
					type: 'error',
					title: labelText
				})
				return false;
			}
			panelchange("."+currentpanel);

			return false;
		});

		// VALIDATE ANY INPUTS THAT HAVE THE REQUIRED CLASS, IF EMPTY RETURN THE NAME ATTRIBUTE
		function check_inputs( currentpanel ){
			
			var result = false;
			var panel  = "." + currentpanel;
			
			$( $( panel + " .required" ).get().reverse() ).each( function(){
				var fieldName  = '';
				var inputValue = $( this ).val();
				if( ( inputValue == false ) || ( inputValue == '' ) || ( inputValue.length == 0 ) ){
					fieldName = $(this).attr( 'name' );
					result    = fieldName;
					return result;
				}
			});
			return result;
		}
		
		//GO BACK-BTN
		$(".btn-back").click(function(){
			var currentpanel = $(this).data("currentpanel");
			go_back("."+currentpanel)	
			return false;
		});
		
		function panelchange(changefrom){
			var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
			var changeto = ".schedule_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", {direction : 'left'}, 500);
			$( changeto ).delay(600).show( "slide", {direction : 'right'},500);	
			return false;	
		}
		
		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
			var changeto = ".schedule_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", {direction : 'right'}, 500);
			$( changeto ).delay(600).show( "slide", {direction : 'left'},500);	
			
			var progress_level 	= $( changeto ).data( "progress_level" );
			
			return false;	
		}
		
		//SUBMIT SCHEDULE FORM
		$( '#create-schedule-btn' ).click(function( e ){
		
			e.preventDefault();
			
			submitScheduleForm();
			
		});
		
		function submitScheduleForm( liMit = "<?php echo SCHEDULE_CLONE_DEFAULT_LIMIT; ?>", offSet = 0 ){
			
			var freqId 				= $( '#frequency_id option:selected' ).val();
			var totalAssets 		= $( '#total_assets' ).val();
			var totalactivitiesDue 	= $( '#total_activities_due' ).val();
			
			$( '#schedule-creation-form' ).append( '<input type="hidden" name="frequency_id" value="'+freqId+'" />' );
			$( '#schedule-creation-form' ).prepend( '<input type="hidden" name="total_assets" value="'+totalAssets+'" /><input type="hidden" name="total_activities_due" value="'+totalactivitiesDue+'" />' );
			
			var formData = $( '#schedule-creation-form' ).serialize();

			swal({
				title: 'Confirm Schedule creation?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function ( result ) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/create_schedules/' ); ?>",
						method:'POST',
						data:formData,
						dataType: 'json',
						beforeSend: function(){
							showPleaseWait();
						},
						success:function( data ){
							hidePleaseWait();
							if( data.status == 1 && ( data.schedules.schedule_id !== '' ) ){
								var activitiesData 	= data.schedules.activities_data,
									newScheduleId 	= data.schedules.schedule_id,
									contractID 		= data.schedules.contract_id,
									scheduleName 	= data.schedules.schedule_name,
									frequencyID 	= data.schedules.frequency_id,
									siteName 		= "<?php echo ( !empty( $site_details->site_name ) ) ? $site_details->site_name : ''; ?>" + " " + "<?php echo ( !empty( $site_details->site_postcodes ) ) ? strtoupper( $site_details->site_postcodes ) : ''; ?>",
									totalActivities	= totalactivitiesDue,
									//totalActivities	= data.schedules.activities,
									dataCounters 	= activitiesData.counters,
									totalAssets 	= dataCounters.expected_assets,
									liMit			= activitiesData.counters.limit,
									offSet			= ( Math.floor( ( dataCounters.processed_activities / dataCounters.expected_activities )*dataCounters.activity_pages ) ) * activitiesData.counters.limit;
								swal({
									type: 'success',
									showCancelButton: true,
									confirmButtonColor: '#5CB85C',
									cancelButtonColor: '#9D1919',
									confirmButtonText: 'Proceed',
									title: 'Scheduling Process started!',
									html:
										'<p>Please check and confirm that the details below are correct, then click proceed to complete the Scheduling process.</p>' +
										'<table class="table table-responsive pull-left">' +
											'<tr>' +
												'<th>Schedule Name:</th><td>' + scheduleName+ '</td>' +
											'</tr>' +
											'<tr>' +
												'<th>Building Name:</th><td>' + siteName+ '</td>' +
											'</tr>' +
											'<tr>' +
												'<th>Assets Processed:</th><td>' + dataCounters.processed_assets + ' of ' + dataCounters.expected_assets + '</td>' +
											'</tr>' +
											'<tr>' +
												'<th>Jobs Processed:</th><td>' + dataCounters.processed_activities + ' of ' + totalactivitiesDue + '</td>' +
											'</tr>' +
										'</table>'
								}).then( function (result) {
									if ( result.value ) {
										
										if( dataCounters.processed_activities === dataCounters.expected_activities ){
											completeSchedulingProcess({
												page: 'details',
												schedule_id: newScheduleId,
												contract_id: contractID,
												frequency_id: frequencyID,
											});
											
										} else {
											submitScheduleForm( liMit, offSet );
										}
							
									} else {
										//Do this if user cancels to change the name
									}
								});
								
							} else {
								swal({
									type: 'error',
									title: data.status_msg
								})
							}		
						}
					});
				}else{
					$( ".site_creation_panel8" ).hide( "slide", { direction : 'left' }, 500 );
					go_back( ".site_creation_panel2" );
					return false;
				}
			}).catch( swal.noop )
		}

		//** Prepare Activities Placeholder **/
		$( '.prepare-schedule-placeholders' ).click( function(){

			var currentpanel 	= $( this ).data( "currentpanel" );
			var frequencyObj	= $( '#frequency_id option:selected' );
			var jobTypeObj		= $( '#job_type_id option:selected' );
			
			var scheduleObj		= [],
				currentpanel 	= $( this ).data( "currentpanel" ),
				progress_level 	= $( this ).data( "progress_level" ),
				freqId 	 		= $( '#frequency_id option:selected' ).val(),
				freqName 	 	= $( '#frequency_id option:selected' ).data( 'frequency_desc' ),
				activityInterval= $( '#frequency_id option:selected' ).data( 'activity_interval' ),
				dueDate			= $( '[name="first_activity_due_date"]' ).val(),
				numberOfChecks	= $( '[name="number_of_checks"]' ).val();
			
			$( "#job_type_id [name='asset_id[]']" ).each( function(){
				if( $( this ).is( ':checked' ) ){
					var assetGroupName 	= $( this ).data( 'asset_type' ),
						assetAssetTypeId= $( this ).data( 'asset_type_id' ),
						assetUniqId 	= $( this ).data( 'asset_unique_id' ),
						jobTypeId		= $( '#jobtype'+assetAssetTypeId+' option:selected' ).val(),
						jobType			= $( '#jobtype'+assetAssetTypeId+' option:selected' ).data( 'job_type' ),
						evidDocTypeId	= $( '#jobtype'+assetAssetTypeId+' option:selected' ).data( 'evidoc_type_id' ),
						evidDocType		= $( '#jobtype'+assetAssetTypeId+' option:selected' ).data( 'evidoc_type' );
					scheduleObj.push({
						asset_type_id: assetAssetTypeId, 
						asset_type: assetGroupName, 
						asset_id:  $( this ).val(),
						asset_unique_id:  assetUniqId,
						frequency_id: freqId,
						frequency_name: freqName,
						activity_interval: activityInterval,
						evidoc_type: encodeURIComponent( evidDocType ),
						evidoc_type_id: evidDocTypeId,
						job_type: encodeURIComponent( jobType ),
						job_type_id: jobTypeId						
					});
				}
			});
			
			var where = { 
				total_activities	: ( frequencyObj.data( 'activities_required' ) ) ? frequencyObj.data( 'activities_required' ) : 0,
				frequency_id 		: ( frequencyObj.val() ) ? frequencyObj.val() : 0,
				frequency_name 		: ( frequencyObj.data( 'frequency_name' ) ) ? frequencyObj.data( 'frequency_name' ) : false,
				frequency_group 	: ( frequencyObj.data( 'frequency_group' ) ) ? frequencyObj.data( 'frequency_group' ) : false,
				activity_interval 	: ( frequencyObj.data( 'activity_interval' ) ) ? frequencyObj.data( 'activity_interval' ) : false,
				due_date			: dueDate,
				number_of_checks	: numberOfChecks,
				assets_data			: scheduleObj
			};
			
			if( where.assets_data.length == 0 || where.assets_data == '' ){
				swal({
					type: 'error',
					text: 'Please choose an Evidoc for this Schedule!'
				});
				return false;
			}

			$.ajax({
				url:"<?php echo base_url( 'webapp/job/prepare_schedule_placeholders/' ); ?>",
				method:"POST",
				data:{ page:'details', where },
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 && ( data.schedules_data !== '' ) ){

						$( '#schedule_placeholders' ).html( data.schedules_data );
						panelchange( "."+currentpanel );
						
						return false;

					}else{
						swal({
							type: 'warning',
							text: data.status_msg,
							showCancelButton: true,
							confirmButtonColor: '#5CB85C',
							cancelButtonColor: '#9D1919',
							confirmButtonText: 'Ok'
						}).catch( swal.noop )
						return false;
					}		
				}
			});
			
			return false;
			
		});
		
		
		/*
		* Complete Scheduling Process
		**/
		function completeSchedulingProcess( formData ){
		
			var formData = formData;

			if( formData.schedule_id.length > 0 ){
				$.ajax({
					url:"<?php echo base_url('webapp/job/complete_scheduling_process' ); ?>",
					method:'POST',
					data:formData,
					dataType: 'json',
					beforeSend: function(){
						showPleaseWait();
					},
					success:function( data ){
						hidePleaseWait();
						if( data.status == 1 && ( data.schedule.schedule_id !== '' ) ){
							var siteId 			= "<?php echo $site_details->site_id; ?>";
							var freqId 			= data.schedule.frequency_id;
							var newScheduleId 	= data.schedule.schedule_id;
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 3000
							})
							
							window.setTimeout(function(){
								
								swal({
									title: 'Do you want to create more schedules?',
									showCancelButton: true,
									confirmButtonColor: '#3085d6',
									cancelButtonColor: '#d33',
									confirmButtonText: 'Yes please',
									cancelButtonText: 'No thanks, I\'m done',
								}).then( function ( result ) {
									if ( result.value ) {
										
										if( siteId ){
											location.href = "<?php echo base_url('webapp/job/new_schedule?site_id='); ?>"+siteId+"&frequency_id="+freqId;
										} else {
											location.href = "<?php echo base_url('webapp/job/schedule_frequencies/'); ?>";
										}
										
									} else {
										if( siteId ){
											location.href = "<?php echo base_url('webapp/site/profile/'); ?>"+siteId+"/schedules";
										} else {
											location.href = "<?php echo base_url('webapp/job/schedule_frequencies/'); ?>";
										}
									}
								})
							
							} ,5000);
							
							location.href = "<?php echo base_url('webapp/job/schedule_frequencies/'); ?>";
							
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
						}
					}
				});
			
			} else {
				swal({
					type: 'error',
					title: 'Invalid Data'
				});
				return false;
			}
		}
		
		
	});
</script>