<style>
	button, .buttons, .btn, .modal-footer .btn+.btn {
		margin-bottom: 5px;
		margin-right: 0px;
	}
</style>


<div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12">
	<form id="region-creation-form" >
		<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
		<input type="hidden" name="page" value="details"/>
		<div class="row">
			<div class="region_creation_panel1 col-md-12">
				<div class="x_panel tile has-shadow">
					<legend>Create New Region <span class="pull-right"><a href="<?php echo base_url('webapp/diary/manage_regions/'); ?>"><i class="fas fa-list"></i> Regions List</a></span></legend>
					<div class="input-group form-group">
						<label class="input-group-addon">Region Name</label>
						<input id="region_name" name="region_name" class="form-control" type="text" placeholder="Region" value="" />
					</div>
					
					<div class="form-group">
						<div class="input-group">
							<label class="input-group-addon">Region Description</label>
							<textarea id="region_description" name="region_description" type="text" class="form-control" rows="3"></textarea>     
						</div>
					</div>

					<div class="form-group">
						<label class="strong">Search Postcode Coverage</label>
						<div class="input-group">
							<input type="text" id="postcodes_areas" class="form-control <?php echo $module_identier; ?>-search_input"  placeholder="Enter comma separated postcode areas e.g. CR, SM, BR etc..." >
							<span class="input-group-btn"><button id="search_postcodes_areas" class="btn btn-default <?php echo $module_identier; ?>-bg" type="button" >Search</button></span>
						</div>
					</div>
					<div id="loading-indicator" style="display:none;" class="text-blue" >Fetching data, please wait... <div style="display:inline-block"><img src="<?php echo base_url('/assets/images/ajax-loader.gif'); ?>" /></div></div>
					
					<?php /* <div class="form-group">
                        <label class="strong">Search Postcode Coverage</label>
                        <input id="search-postcode-areas" class="form-control" placeholder="Search postcode regions e.g. CR or Croydon" />
                    </div>*/ ?>
					
					<div class="postcode-coverage" style="display:none">
						<label class="strong">Please check all postcodes that apply <small><i class="fas fa-info-circle pointer" title="A Postcode-area can only be part of 1 region within your Business. If the selected Postcode-area already exists, it will be omitted from your request."></i></small></label>
						<div class="row" id="region_postcodes" ></div> 
					</div>
					<hr>
					<div class="row form-group">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button id="create-region-btn" class="btn btn-sm btn-flow btn-success btn-next" type="button" >Create Region</button>					
						</div>
					</div>
				</div>						
			</div>	

		</div>
	</form>
</div>


<!-- Modal for adding Address Regions -->
<div class="modal fade add-address-regions-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<form id="add-region-postcodes-form" onsubmit="return false" >
			<div class="modal-content">
				<div class="modal-header"><a type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></a>
					<h4 class="modal-title" id="myPostCodeDistrictModalLabel">Add Missing Address Postcode Regions</h4>						
				</div>
				<div class="modal-body" id="risk-items-modal-container" >
					<input type="hidden" name="page" value="details" />
					<input type="hidden" name="region_id" value="" />
					<input type="hidden" name="region_name" value="" />
					<input type="hidden" name="account_id" value="" />
					
					<div class="form-group">
						<div class="row">
							<div class="col-md-3 col-sm-3 col-xs-12"><input type="text" class="form-control" name="address_regions[postcode_area][]"  value="" placeholder="E.g. CR" /></div>
							<div class="col-md-3 col-sm-3 col-xs-12"><input type="text" class="form-control" name="address_regions[postcode_qi][]"  value="" placeholder="E.g. 4" /></div>
							<div class="col-md-3 col-sm-3 col-xs-12"><input type="text" class="form-control" name="address_regions[posttown][]"  value="" placeholder="(Optional) E.g. Mitcham"  /></div>
							<div class="col-md-3 col-sm-3 col-xs-12"><input type="text" class="form-control" name="address_regions[county][]"  value="" placeholder="(Optional) E.g. Surrey"  /></div>
						</div>
					</div>
				</div>
				
				<div class="postcode-coverage" style="display:none">
					<div class="modal-footer">
						<button class="left btn btn-success btn-sm" type="button" >Add Selected Postcode Areas</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
	$( document ).ready( function(){
		
		$( '#region_postcodes' ).on( 'click', '.add-new-address-region', function(){
			$( ".add-address-regions-modal" ).modal( "show" );
		});
		
		$( '#region_postcodes' ).on( 'change', '#check-all-postcodes', function(){
			if( $( this ).is(':checked') ){
				$( '.postcode-checks' ).each(function(){
					$( this ).prop( 'checked', true );
				});
			} else {
				$( '.postcode-checks' ).each(function(){
					$( this ).prop( 'checked', false );
				});
			}
		});

		$( '#region_postcodes' ).on( 'change', '.postcode-checks', function(){
			
			var chkCount  = 0;
			var totalChekd= 0;
			var unChekd   = 0;
			$( '.postcode-checks' ).each(function(){
				chkCount++;
				if( $( this ).is(':checked') ){
					totalChekd++;					
				}else{
					unChekd++;
				}
			});
			
			if( chkCount > 0 && ( chkCount == totalChekd ) ){
				$( '#check-all-postcodes' ).prop( 'checked', true );
			}else{
				$( '#check-all-postcodes' ).prop( 'checked', false );
			}

		});

		// Search for postcode areas when user presses the Enter key
		$( "#postcodes_areas" ).on( 'keyup', function (e) {
			if ( e.keyCode === 13 ) {
				$(".add-postcode-districts-modal").modal( "show" );
				var searchTerm 	= encodeURIComponent( $( '#postcodes_areas' ).val() );
				if( searchTerm.length > 1 ){
					$( '#loading-indicator' ).show();
					$.post( "<?php echo base_url('webapp/diary/search_postcode_areas'); ?>",{ search_term:searchTerm},function(result){
						$( '#loading-indicator' ).hide();
						$( '#region_postcodes' ).html( result['postcode_areas'] );
						$( '.postcode-coverage' ).slideDown( 'slow' );
					},
					"json" );
				} else {
					swal({
						type: 'error',
						text: 'Please provide at least 1 valid postcode area!'
					});
				}
			}
		});
		
		// Search for postcode areas on button click
		$( '#search_postcodes_areas' ).click( function(){
			var searchTerm 	= encodeURIComponent( $( '#postcodes_areas' ).val() );
			if( searchTerm.length > 1 ){
				$( '#loading-indicator' ).show();
				$.post( "<?php echo base_url("webapp/diary/search_postcode_areas"); ?>",{ search_term:searchTerm},function(result){
					$( '#loading-indicator' ).hide();
					$( '#region_postcodes' ).html( result['postcode_areas'] );
					$( '.postcode-coverage' ).slideDown( 'slow' );
				},
				"json" );
			} else {
				swal({
					type: 'error',
					text: 'Please provide at least 1 valid postcode area!'
				});
			}
		});
		
		//** Validate any inputs that have the required class, if empty return the name attribute **/
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
		
		//Submit region form
		$( '#create-region-btn' ).click(function( e ){
			e.preventDefault();
			var formData = $('#region-creation-form').serialize();
			
			swal({
				title: 'Confirm new Region creation?',
				showCancelButton: 	true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: 	'#9D1919',
				confirmButtonText: 	'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/diary/add_region/'); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 && ( data.region !== '' ) ){
								
								var newRegionId = data.region.region_id;
								
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){ 
									location.reload();
									//location.href = "<?php echo base_url('webapp/diary/manage_regions/'); ?>"+newRegionId;
								} ,3000);							
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}		
						}
					});
				}else{
					return false;
				}
			}).catch( swal.noop )
		});
		
	});
</script>

