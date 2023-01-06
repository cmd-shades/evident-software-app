<div>
	<div class="row">
		<?php if( !empty( $message ) ) { ?>
			<div class="col-md-12 col-sm-12 col-xs-12 text-red" style="margin-bottom:10px;" ><?php echo $message; ?></div>
		<?php } ?>
		<!--  Single Asset creation -->
		<div class="col-md-offset-3 col-md-6 col-sm-offset-3 col-sm-6 col-xs-12">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<legend>Add New Asset</legend>
				</div>
				<form id="asset-creation-form" method="post" >
					<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
					<input type="hidden"  name="page" value="details"/>
					<div class="asset_creation_panel1 col-md-12 col-sm-12 col-xs-12">
						<div class="x_panel tile has-shadow">
							<div class="row section-header">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<legend class="legend-header">What is the asset type?</legend>
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="asset_creation_panel1-errors"></h6>
								</div>
							</div>

							<div class="input-group form-group">
								<label class="input-group-addon">Asset type *</label>
								<div class="row">
									<div class="col-md-11 col-sm-11 col-xs-11">
										<select id="asset_type_id" name="asset_type_id" class="form-control required">
											<option value="">Please select</option>
											<?php if( !empty( $asset_types ) ) { foreach( $asset_types as $category_name => $category_data ) { ?>
												<optgroup label="<?php echo strtoupper( $category_name ); ?>">
													<?php foreach( $category_data as $k => $asset_type ) { ?>
														<option value="<?php echo $asset_type->asset_type_id; ?>" data-asset_group="<?php echo $asset_type->asset_group; ?>" ><?php echo $asset_type->asset_type; ?></option>
													<?php } ?>
												</optgroup>
											<?php } } ?>
										</select>
									</div>

									<div class="col-md-1 col-sm-1 col-xs-1">
										<div id="asset-type-quick-add" style="margin-top:4px" class="pointer" title="Quick Add new Asset type option"><span class="pull-right"><i class="fas fa-plus-circle fa-2x text-green"></i></span></div>
									</div>
								</div>
							</div>

							<div class="input-group form-group add_new_attribute" style="display: none;margin-top:10px;margin-bottom:10px;">
								<a href="#add-attribute-modal" role="button" data-toggle="modal">
									<i class="fas fa-plus-circle" style="font-size: 16px;"></i>
									<span style='font-size:15px;font-weight:100;color:black;'>&nbsp;Please add new attribute for this Asset type</span>
								</a>
							</div>

							<div id="asset-unique-id" style="display: none;" class="row">
								<div class="col-md-12 col-md-12 col-sm-12 col-xs-12">
									<div class="input-group form-group">
										<label class="input-group-addon">Unique ID *</label>
										<input name="asset_unique_id" class="form-control required" type="text" value="" placeholder="Asset unique id"  />
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6 col-md-12 col-sm-12 col-xs-12">
									<button id="fetch-attributes-btn" class="btn btn-block btn-flow btn-success btn-next" data-currentpanel="asset_creation_panel1" type="button">Next</button>
								</div>
							</div>
						</div>
					</div>

					<div class="asset_creation_panel2 col-md-12 col-sm-12" style="display: none;">
						<div class="x_panel tile has-shadow">
							<div class="row section-header">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<legend class="legend-header" >Please complete the Asset details below</legend>
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="asset_creation_panel2-errors"></h6>
								</div>
							</div>

							<div class="row" >
								<div id="asset_type_attributes" class="has-numbers-only-fields" >

								</div>
							</div>

							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="asset_creation_panel2" type="button" >Back</button>
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<button class="btn btn-block btn-flow btn-success btn-next asset-creation-steps" data-currentpanel="asset_creation_panel2" type="button">Next</button>
								</div>
							</div>
						</div>
					</div>

					<div class="asset_creation_panel3 col-md-12 col-sm-12" style="display:none">
						<div class="x_panel tile has-shadow">
							<div class="row section-header">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<legend>Asset connectivity and location details</legend>
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="asset_creation_panel3-errors"></h6>
								</div>
							</div>

							<?php include( 'asset_connectivity.php' ); ?>

							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="asset_creation_panel3" type="button" >Back</button>
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<button class="btn btn-block btn-flow btn-success btn-next asset-creation-steps" data-currentpanel="asset_creation_panel3" type="button" >Next</button>
								</div>
							</div>
						</div>
					</div>

					<div class="asset_creation_panel4 col-md-12 col-sm-12 col-xs-12" style="display:none" >

						<div class="x_panel tile has-shadow">
							<legend>&nbsp;</legend>
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="form-group">
										<div class="text-center">
											<p>You are about to submit a request to create a new Asset.</p>
											<p>Click the "Create Asset" to proceed or Back to review your entered details.</p>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="asset_creation_panel4" type="button" >Back</button>
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<button id="create-asset-btn" class="btn btn-block btn-flow btn-success btn-next" type="button" >Create Asset</button>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="modal fade add-location-modal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
						<h4 class="modal-title" id="myModalLabel">Location <small><em>Quick Add</em></small></h4>
					</div>
					<div id="add-location-form-container" class="modal-body">
						<div class="row">
							<input type="hidden" name="page" value="details" />
							<div class="col-md-12 col-sm-12 col-xs-12">
								<div class="form-group">
									<label id="asset-code-label" >Location Group</label>
									<?php if( !empty( $location_groups ) ){ ?>
										<div class="row">
											<?php foreach( $location_groups as $k=>$location_group ){ ?>
												<div class="col-md-3 col-sm-4 col-xs-12">
													<div class="checkbox">
														<label><input type="checkbox" class="location-groups" name="location_group" value="<?php echo strtolower( $location_group ); ?>" <?php echo ( strtolower( $location_group ) == strtolower( $this->router->fetch_class() ) ) ? "checked=checked" : ( strtolower( $this->router->fetch_class() ) == 'fleet' ? "checked=checked" : "" )  ?> > <?php echo ucwords( $location_group ); ?></label>
													</div>
												</div>
											<?php } ?>
										</div>
									<?php } ?>
								</div>
							</div>

							<div class="col-md-12 col-sm-12 col-xs-12">
								<div class="form-group">
									<label id="site_name_selector" >Site Name</label>
									<select id="site_id" name="site_id" class="form-control" required >
										<option value="">Please select</option>
										<?php if( !empty( $sites ) ) { foreach( $sites as $k => $site ) { ?>
											<option value="<?php echo $site->site_id; ?>" data-site_name="<?php echo $site->site_name; ?>" >#<?php echo $site->site_id; ?>. <?php echo $site->site_name; ?> - <?php echo $site->site_postcodes; ?></option>
										<?php } } ?>
									</select>
								</div>
							</div>

							<div class="col-md-12 col-sm-12 col-xs-12">
								<div class="form-group">
									<label >Building Name</label>
									<input id="building_name" name="building_name" class="form-control" type="text" value="" placeholder="Building name" required=required />
								</div>
							</div>
							<div class="col-md-12 col-sm-12 col-xs-12">
								<div class="form-group">
									<label id="asset-code-label" >Location Name</label>
									<input name="location_name" class="form-control" type="text" value="" placeholder="Location name" required=required />
								</div>
							</div>
							<div class="col-md-12 col-sm-12 col-xs-12">
								<div class="form-group">
									<label id="asset-code-label" >Location Description</label>
									<textarea name="location_description" rows="4" class="form-control" type="text" value="" placeholder="Location Description" ></textarea>
								</div>
							</div>
							<div class="col-md-12 col-sm-12 col-xs-12">
								<button id="quick-add-btn" class="btn btn-block btn-success" type="button">Add Location</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>

<!-- Modal for adding a new asset type -->
<div class="modal fade add-asset-type-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<form id="add-asset-type-form">
			<input type="hidden" name="page" value="details" />
			<div class="modal-content">
				<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title" id="myAssetTypeModalLabel">Add a New Asset Type</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="form-group">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<h4>What type of Asset is it?</h4>
									<div class="form-group">
										<input name="asset_type" class="form-control" type="text" value="" placeholder="Asset type" required=required />
									</div>
								</div>
								<div class="col-md-12 col-sm-12 col-xs-12">
									<h4 title="You can only add a type that does not already exist" >What Discipline does this belong to?</h4>
								</div>
								
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="form-group" >
										<select id="discipline_id" name="discipline_id" class="form-control required" data-label_text="Discipline" >
											<option value="" >Please Select a Discipline</option>
											<?php if( !empty( $disciplines ) ) { foreach( $disciplines as $k => $discipline ) { ?>
												<option value="<?php echo $discipline->discipline_id; ?>" ><?php echo $discipline->account_discipline_name; ?></option>
											<?php } } ?>
										</select>
									</div>
								</div>
								
								<div class="col-md-12 col-sm-12 col-xs-12">
									<h4 title="You can only add a type that does not already exist" >What category does this belong to?</h4>
								</div>
								
								<div class="col-md-11 col-sm-11 col-xs-12">
									<div class="form-group has-shadow" >
										<select id="category_id" name="category_id" class="form-control required">
											<option value="">Please select category</option>
											<?php if( !empty( $evidoc_categories ) ) { foreach( $evidoc_categories as $k => $category ) { ?>
												<option value="<?php echo $category->category_id; ?>" ><?php echo $category->category_name_alt; ?></option>
											<?php } } ?>
										</select>
									</div>
								</div>
								<div class="col-md-1 col-sm-1 col-xs-1">
									<div style="margin-top:4px" class="evidoc-category-quick-add pointer" title="Quick Add new category option"><span class="pull-right"><i class="fas fa-plus-circle fa-2x text-green"></i></span></div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<h4>Please set the sub-group</h4>
									<select id="asset_group" name="asset_group" class="form-control required">
										<option value="">Please select sub-category</option>
										<?php if( !empty( $sub_categories ) ) { foreach( $sub_categories as $k => $sub_category ) { ?>
											<option value="<?php echo $sub_category->sub_category ?>"><?php echo $sub_category->sub_category_desc ?></option>
										<?php } } ?>
									</select>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-sm-12 col-xs-12">
							<br/>
							<div class="form-group">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<button id="add-asset-type-btn" class="btn btn-block btn-success" type="button" >Add Asset Type</button>
								</div>
							</div>
							<br/>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<!-- Modal for adding a new category -->
<div class="modal fade add-category-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<form id="add-category-form-container" >
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title" id="myCategoryModalLabel">Add an New Category</h4>
					<span id="category-feedback-message"></span>
				</div>
				<div class="modal-body">
					<div class="row">
						<input type="hidden" name="page" value="details" />
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h4>What is the name of this Category?</h4>
							<div class="form-group">
								<input name="category_name" class="form-control" type="text" value="" placeholder="Category name" required=required />
							</div>
							<h4>Please provide a description</h4>
							<div class="form-group">
								<textarea rows="4" name="description" class="form-control" type="text" value="" placeholder="Please provide a description of your Category" required=required /></textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<button id="category-quick-add-btn" class="btn btn-success btn-block" type="button">Add New Category</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>


<!-- Modal for adding a new Attribute. Should this be a modal? -->
<div id="add-attribute-modal" class="modal fade add-attribute-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title" id="myAddQuestModalLabel">Add Asset Type attribute</h4>
			</div>
			<?php include( 'asset_type_attribute_add_new.php' ); ?>
		</div>
	</div>
</div>

<script>
	$( document ).ready(function(){

		$( '.required' ).on( 'input', function(){
			$( '.error_message' ).text( '' );
		});

		$( "#asset_type_id" ).on( "change", function( e ){
			e.preventDefault();
			
			
			var assetType 	= $(this).val();
			var assetGroup  = $( 'option:selected', this).data( 'asset_group' );
			if( assetType.length == 0 ){
				$( '#asset-unique-id' ).slideUp( 'fast' );
				$( '[name="asset_name"]' ).val( '' );
			}
			$( '#asset_creation_panel1-errors' ).text( '' );

			$( '#asset_type_attributes' ).html( '' );

			assetTypeId		= $( '#asset_type_id option:selected' ).val();

			// FETCH ATTRIBUTES
			$.ajax({
				url:"<?php echo base_url( 'webapp/asset/fetch_attributes/' ); ?>",
				method:"POST",
				data:{ page:'details', asset_type_id:assetTypeId },
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 ){
						$( '#asset_type_attributes' ).html( data.attributes_data );
						$( ".add_new_attribute" ).css( "display", "none" );
						$( '#asset-unique-id' ).slideDown();
						return false;

					} else {
						$( '#asset-unique-id' ).slideUp();
						swal({
							type: 'error',
							title: data.status_msg
						})

						$( ".add_new_attribute" ).css( "display", "block" );
						$( "#asset-attribute-creation-form input[name='asset_type_id']" ).val( assetTypeId );
					}
				}
			});
		});

		
		
		

		$( '#fetch-attributes-btn' ).click( function(){
			var currentpanel 	= $(this).data( "currentpanel" ),
				assetTypeId		= $( '#asset_type_id option:selected' ).val(),
				assetUniqueId	= $( '[name="asset_unique_id"]' ).val();

			// FETCH ATTRIBUTES
			$.ajax({
				url:"<?php echo base_url( 'webapp/asset/fetch_attributes/' ); ?>",
				method:"POST",
				data:{ page:'details', asset_type_id:assetTypeId },
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 ){

						$( '#asset_type_attributes' ).html( data.attributes_data );
						$( '.datepicker2' ).datetimepicker({
							formatDate: 'd/m/Y',
							timepicker: false,
							format: 'd/m/Y',
						});
						
						if( assetTypeId.length == 0 ){
							$( '#asset_creation_panel1-errors' ).text( 'Please select an Asset type' );
							return false;
						}

						if( assetUniqueId.length == 0 ){
							$( '#asset_creation_panel1-errors' ).text( 'Asset Unique ID is required!' );
							return false;
						}

						panelchange( "."+currentpanel )	
						return false;

					} else {
						swal({
							type: 'error',
							title: data.status_msg
						})
						
						$( ".add_new_attribute" ).css( "display", "block" );
						$( "#asset-attribute-creation-form input[name='asset_type_id']" ).val( assetTypeId );
					}		
				}
			});
			return false;

		});

		//Quick add a New Asset Type
		$( '#asset-type-quick-add' ).click(function(){
			$( '.add-asset-type-modal' ).modal( 'show' );
		});

		$( '#add-asset-type-btn' ).click(function(){

			var catId    = $( '#category_id option:selected' ).val();
			var assGroup = $( '#asset_group option:selected' ).val();

			if( assGroup.length == 0  ){
				swal({
					type: 'error',
					title: 'Asset group is required!'
				})
				return false;
			}

			var formData = $( "#add-asset-type-form :input").serialize();

			$.ajax({
				url:"<?php echo base_url('webapp/audit/add_asset_type/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){

						$(".add-asset-type-modal").modal("hide");
						$("#asset-unique-id").css("display", "block");

						var assetTypeId     	= data.asset_type.asset_type_id;
						var assetTypeName   	= data.asset_type.asset_type;
						var assetCategoryId 	= data.asset_type.category_id;
						var assetCategoryName 	= data.asset_type.category_name;

						$('#feedback_message').html( data.status_msg ).delay(3000).fadeToggle("slow");

						var optionExists = ( $('#asset_type_id option[value=' + assetTypeId + ']').length > 0 );

						if( !optionExists ){
							//Only add the new option if it doesn't already exist
							$('#asset_type_id').append( $('<option selected>').val( assetTypeId ).text( ucwords( assetTypeName ) ).attr( { 'data-asset_group': assGroup } ) );
						}

						//Set selected
						$('#asset_type_id option[value="'+assetTypeId+'"]').prop( 'selected', true );

						//Set the Category if it already exists
						var categoryExists = ( $('#evidoc_category_id option[value=' + assetCategoryId + ']').length > 0 );

						if( !categoryExists ){
							$('#evidoc_category_id').append( $('<option >').val( assetCategoryId ).text( assetCategoryName ) );
						}

						//Set selected
						$('#evidoc_category_id option[value="'+assetCategoryId+'"]').prop( 'selected', true );
						
						$( '#asset-unique-id' ).slideUp();
						$('.add_new_attribute').slideDown()
						$('#asset-attribute-creation-form').find('input[name=asset_type_id]').val(assetTypeId)

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

		//Site location selection
		$( '#site_id' ).change( function(){
			var buildingName = $( 'option:selected', this).data( 'site_name' );
			$( '[name="building_name"]' ).val( buildingName );
		});

		$( ".asset-creation-steps" ).click(function(){

			//Clear errors first
			$( '.error_message' ).each(function(){
				$( this ).text( '' );
			});

			var currentpanel = $(this).data( "currentpanel" );
			var inputs_state = check_inputs( currentpanel );
			if( inputs_state ){
				//If name attribute returned, auto focus to the field and display error message
				$( '[name="'+inputs_state+'"]' ).focus();
				var labelText = $( '[name="'+inputs_state+'"]' ).parent().find('label').text();
				$( '#'+currentpanel+'-errors' ).text( ucwords( labelText ) +' is a required' );
				return false;
			}
			panelchange( "."+currentpanel )
			return false;
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

		$(".btn-back").click(function(){
			var currentpanel = $(this).data("currentpanel");
			go_back("."+currentpanel)
			return false;
		});

		function panelchange(changefrom){
			var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
			var changeto = ".asset_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'left'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'right'},500);
			return false;
		}

		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
			var changeto = ".asset_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'right'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'left'},500);
			return false;
		}

		//Submit asset form
		$( '#create-asset-btn' ).click(function( e ){

			//var siteID = $( '#location_id option:selected').data( 'site_id' );

			//if( siteID.length > 0 ){
				//$('#asset-creation-form').append('<input type="hidden" name="site_id" value="'+siteID+'" />');
			//}

			e.preventDefault();

			var formData = $('#asset-creation-form').serialize();

			swal({
				title: 'Confirm new asset creation?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/asset/create_asset/' ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 && ( data.asset !== '' ) ){

								var newAssetId = data.asset.asset_id;

								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){
									location.href = "<?php echo base_url('webapp/asset/profile/'); ?>"+newAssetId;
								} ,2000);
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						}
					});
				}else{
					$( ".asset_creation_panel4" ).hide( "slide", { direction : 'left' }, 500 );
					go_back( ".asset_creation_panel2" );
					return false;
				}
			}).catch(swal.noop)
		});

		//Location Quick Add
		$( '#location-quick-add' ).click(function(){

			$(".add-location-modal").modal("show");

		});

		//Individual package selections
		$('.location-groups').click(function() {
			$('.location-groups').not(this).prop('checked', false);
		});


		$( '#quick-add-btn' ).click(function(){

			var formData = $( "#add-location-form-container :input").serialize();

			$.ajax({
				url:"<?php echo base_url('webapp/asset/add_new_location/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){

						$(".add-location-modal").modal("hide");

						var locationId 	 = data.location.location.location_id;
						var locationName = data.location.location.location_name;
						var siteID 		 = data.location.location.site_id;

						$('#feedback_message').html( data.status_msg ).delay(3000).fadeToggle("slow");

						var optionExists = ( $('#location_id option[value=' + locationId + ']').length > 0 );

						if( !optionExists ){
							//Only add the new option if it doesn't already exist
							$('#location_id').append( $('<option >').val( locationId ).text( locationName ).attr( { 'data-site_id': siteID } ) );
						}

						//Set selected
						$('#location_id option[value="'+locationId+'"]').prop( 'selected', true );

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


		//Muiltiple Assets upload
		$('#docs-upload-form').submit(function(){

			var files = $('#uploadfile').val();

			if( files.length == 0 ){
				swal({
					type: 'error',
					title: '<small>No files selected for upload!</small>'
				});
				return false;
			}

			var selection = document.getElementById('uploadfile');
			for (var i=0; i < selection.files.length; i++) {
				var filename = selection.files[i].name;
				var ext = filename.substr(filename.lastIndexOf('.') + 1);
				if( ext!== "csv" ) {
					swal({
						type: 'error',
						title: '<small>You have selected an INVALID file type: .' +ext+'</small>'
					})
					return false;
				}
			}

			$('#doc-upload-btn').attr('disabled', 'disabled');

		});

		//Trigger Category modal
		$( '.evidoc-category-quick-add' ).click( function(){
			$( '.add-category-modal' ).modal( 'show' );
		} );

		// New Category Quick add
		$( '#category-quick-add-btn' ).click(function(){

			var formData = $( "#add-category-form-container :input").serialize();

			$.ajax({
				url:"<?php echo base_url('webapp/audit/add_category/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){

						$(".add-category-modal").modal("hide");

						var categoryId 	 = data.category.category_id;
						var categoryName = data.category.category_name;
						var categoryDesc = data.category.description;

						$('#category-feedback-message').html( data.status_msg ).delay( 3000 ).fadeToggle( "slow" );

						var optionExists  = ( $('#evidoc_category_id option[value="' + categoryId + '"]').length > 0 );
						var optionExists2 = ( $('#category_id option[value="' + categoryId + '"]').length > 0 );

						if( !optionExists ){
							//Only add the new option if it doesn't already exist
							$('#evidoc_category_id').append( $('<option >').val( categoryId ).text( categoryName + ' - ' + categoryDesc ) );
						}

						if( !optionExists2 ){
							//Only add the new option if it doesn't already exist
							$('#category_id').append( $('<option >').val( categoryId ).text( categoryName + ' - ' + categoryDesc ) );
						}

						//Set selected
						$('#evidoc_category_id option[value="'+categoryId+'"]').prop( 'selected', true );
						$('#category_id option[value="'+categoryId+'"]').prop( 'selected', true );

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


		$( '.has-numbers-only-fields' ).on( 'keyup', '.numbers-only', function(){
			var val = $(this).val();
			if( isNaN( val ) ){
				 val = val.replace(/[^0-9\.]/g,'');
				 if(val.split('.').length>2)
					 val =val.replace(/\.+$/,"");
			}
			$(this).val(val);
		} );
		
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