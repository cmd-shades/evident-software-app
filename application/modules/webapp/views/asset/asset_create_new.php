<div>
	<div class="row">
		<?php if( !empty( $message ) ) { ?>
			<div class="col-md-12 col-sm-12 col-xs-12 text-red" style="margin-bottom:10px;" ><?php echo $message; ?></div>
		<?php } ?>
		<!--  Single Asset creation -->
		<div class="col-md-6 col-sm-6 col-xs-12">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<legend>Add New Asset <small>(Single)</small></legend>
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
										<select id="asset-types" name="asset_type_id" class="form-control required">
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
							
							<!-- Common fields -->
							<div class="common-fields" style="display:none" >
								<legend class="legend-header">Please provide the following details</legend>
								<!-- <h6 class="error_message" style="display: block; color:red; font-weight:600" id="asset_creation_panel1-errors"></h6> -->
							</div>
							
							<div class="common-fields" style="display:none" >						
								
								<div class="input-group form-group">
									<label class="input-group-addon">Asset name *</label>
									<input name="asset_name" class="form-control required" type="text" value="" placeholder="Asset name"  />
								</div>
								<div class="input-group form-group">
									<label class="input-group-addon">Asset Colour</label>
									<input name="asset_colour" class="form-control" type="text" value="" placeholder="Colour if applicable"  />							
								</div>
								<div class="input-group form-group">
									<label class="input-group-addon" id="unique-id-label">Asset Unique ID *</label>
									<input name="asset_unique_id" id="unique-id" class="form-control required" type="text" value="" placeholder="Unique identifier"  />							
								</div>						
							</div>
							
							<div class="mobile-devices" style="display:none">
								<div class="input-group form-group">
									<label class="input-group-addon">Asset IMEI number *</label>
									<input name="asset_imei_number" id="asset_imei_number" class="form-control required" type="text" value="" placeholder="IMEI number"  />
								</div>
							</div>
							
							<div class="alarm-panels-attributes" style="display:none">
								<div class="input-group form-group">
										<label class="input-group-addon">Alarm Panel Code *</label>
										<input name="alarm_panel_code" id="alarm_panel_code" class="form-control required" type="text" value="" placeholder="Alarm Panel Code"  />
									</div>
							</div>
							
							<?php /* ?><div class="ppe-assets" style="display:none">
								<div class="input-group form-group">
									<label class="input-group-addon" id="asset-code-label" >Asset Code</label>
									<input name="asset_code" class="form-control" type="text" value="" placeholder="Asset code"  />
								</div>
							</div>
							<?php */ ?>
							
							<div class="row">
								<div class="col-md-6 col-md-12 col-sm-12 col-xs-12">
									<button class="btn btn-block btn-flow btn-success btn-next asset-creation-steps" data-currentpanel="asset_creation_panel1" type="button">Next</button>					
								</div>
							</div>
						</div>
					</div>
					
					<div class="asset_creation_panel2 col-md-12 col-sm-12" style="display:none">
						<div class="x_panel tile has-shadow">
							<div class="row section-header">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<legend class="legend-header" >What is the asset make and model?</legend>
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="asset_creation_panel2-errors"></h6>
								</div>
								<div class="col-md-12 col-sm-12 col-xs-12" id="feedback_message"></div>
							</div>

							<h6 class="error_message" style="display: block; color:red; font-weight:600" id="asset_creation_panel2-errors"></h6>
							<div class="input-group form-group">
								<label class="input-group-addon">Asset make *</label>
								<input name="asset_make" class="form-control required" type="text" value="" placeholder="E.g. Samsung, Apple, Bosch etc."  />
							</div>
							<div class="input-group form-group">
								<label class="input-group-addon">Asset model *</label>
								<input name="asset_model" class="form-control required" type="text" value="" placeholder="Asset model"  />
							</div>
								
							<div class="input-group form-group">
								<label class="input-group-addon">Asset Specifications</label>
								<textarea name="asset_specifications" class="form-control" type="text" value="" style="width:100%;" placeholder="Item pecicifications e.g. RAM, Battery life etc." ></textarea>
							</div>
							
							<div class="input-group form-group">
								<label class="input-group-addon">Location name</label>
								<input name="location_name" class="form-control" type="text" value="" placeholder="Location name e.g. First floor riser by the fire door"  />
							</div>
							
							<div class="input-group form-group">
								<label class="input-group-addon">Location Zone</label>
								<input name="location_zone" class="form-control" type="text" value="" placeholder="Location Zone e.g. First Floor"  />
							</div>
							
							<?php /* ?><div class="input-group form-group">
								<label class="input-group-addon">Asset location</label>
								<select id="location_id" name="location_id" class="form-control" style="width:94%">
									<option>Please select</option>
									<?php if( !empty( $asset_locations ) ) { foreach( $asset_locations as $k => $location ) { ?>
										<option value="<?php echo $location->location_id; ?>" data-site_id="<?php echo !empty( $location->site_id ) ? $location->site_id : null; ?>" ><?php echo $location->location_name; ?></option>
									<?php } } ?>
								</select>
								<div id="location-quick-add" style="float:right; width:4%; margin:3px 0px 0 0;" class="pointer" title="Quick Add new location"><span class="pull-right"><i class="fas fa-plus-square fa-2x"></i></span></div>
							</div>
							<?php */ ?>
							
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="asset_creation_panel2" type="button" >Back</button>					
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<button class="btn btn-block btn-flow btn-success btn-next asset-creation-steps" data-currentpanel="asset_creation_panel2" type="button" >Next</button>					
								</div>
							</div>
						</div>
					</div>
					
					<div class="asset_creation_panel3 col-md-12 col-sm-12 col-xs-12" style="display:none" >
						<div class="x_panel tile has-shadow">
							<div class="row section-header">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<legend class="legend-header" >Additional information <small><em>(for mobile devices only, skip if not applicable)</em></small></legend>
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="asset_creation_panel2-errors"></h6>
								</div>
							</div>
							
							<!-- Mobile device attributes -->
							<div class="mobile-device-attributes" style="display:none">
								<div class="input-group form-group">
									<label class="input-group-addon">Phone number</label>
									<input name="attributes[phone_number]" class="form-control" type="text" value="" placeholder="Phone number"  />
								</div>
								<div class="input-group form-group">
									<label class="input-group-addon">Call allowance (minutes)</label>
									<input name="attributes[call_allowance]" class="form-control" type="text" value="" placeholder="if applicable"  />
								</div>
								<div class="input-group form-group">
									<label class="input-group-addon">Data allowance (GB)</label>
									<input name="attributes[data_allowance]" class="form-control" type="text" value="" placeholder="if applicable"  />
								</div>
							</div>

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
					
					<div class="asset_creation_panel4  col-md-12 col-sm-12 col-xs-12" style="display:none" >
						<div class="x_panel tile has-shadow">
							<div class="row section-header">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<legend class="legend-header" >Additional information <small><em>(Enter all that apply)</em></small></legend>
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="asset_creation_panel2-errors"></h6>
								</div>
							</div>

							<!-- PPE Attributes -->
							<div class="mobile-device-attributes" style="display:block">
								<div class="input-group form-group">
									<label class="input-group-addon">Purchase price</label>
									<input name="purchase_price" class="form-control" type="text" value="" placeholder="If applicable"  />
								</div>
								<div class="input-group form-group">
									<label class="input-group-addon">Purchase date</label>
									<input name="purchase_date" class="form-control datepicker" type="text" value="" placeholder="dd-mm-yyy if applicable"  />
								</div>
								<div class="input-group form-group">
									<label class="input-group-addon">End of life date</label>
									<input name="end_of_life_date" class="form-control datepicker" type="text" value="" placeholder="dd-mm-yyy if applicable"  />
								</div>
								<?php /* ?>		
								<div class="input-group form-group">
									<label class="input-group-addon">Lease price</label>
									<input name="lease_price" class="form-control" type="text" value="" placeholder="if applicable"  />
								</div>
								<div class="input-group form-group">
									<label class="input-group-addon">Charge frequency</label>
									<select id="charge_frequency" name="charge_frequency" class="form-control">
										<option>Please select</option>
										<option value="One off" >One off</option>					
										<option value="Weekly" >Weekly</option>							
										<option value="Monthly" >Monthly</option>							
										<option value="Annually" >Annually</option>							
									</select>
								</div> 
								<?php */ ?>
							</div>

							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="asset_creation_panel4" type="button" >Back</button>					
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<button class="btn btn-block btn-flow btn-success btn-next asset-creation-steps" data-currentpanel="asset_creation_panel4" type="button" >Next</button>					
								</div>
							</div>	
							
						</div>						
					</div>

					<div class="asset_creation_panel5  col-md-12 col-sm-12 col-xs-12" style="display:none" >
						<div class="x_panel tile has-shadow">
							<div class="row section-header">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<legend class="legend-header" >Please confirm which Building this asset belongs to (skip if not applicable)</legend>
								</div>
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="form-group  has-shadow" >
										<select id="site_id" name="site_id" class="form-control" style="width:100%" data-label_text="Parent Asset" >
											<option value="" >Select linked Site</option>
											<?php if( !empty( $sites ) ) { foreach( $sites as $k => $site ) { ?>
												<option value="<?php echo $site->site_id; ?>" ><?php echo $site->site_name; ?> <?php echo !empty( $site->postocde ) ? $asset->postocde : ''; ?></option>
											<?php } } ?>
										</select>
									</div>
								</div>
							</div>
						
							<div class="row section-header">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<legend class="legend-header" >Is this Asset linked to another asset? If so, please select it below</legend>
								</div>
								<div class="col-md-12 col-sm-12 col-xs-12">
									<h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="asset_creation_panel5-errors"></h6>
								</div>
								
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="form-group  has-shadow" >
										<select id="parent_asset_id" name="parent_asset_id" class="form-control" style="width:100%" data-label_text="Parent Asset" >
											<option value="" >Select linked asset</option>
											<?php if( !empty( $existing_assets ) ) { foreach( $existing_assets as $k => $asset ) { ?>
												<option value="<?php echo $asset->asset_id; ?>" ><?php echo $asset->asset_name; ?> <?php echo !empty( $asset->asset_make ) ? $asset->asset_make : ''; ?> <?php echo !empty( $asset->asset_model ) ? $asset->asset_model : ''; ?></option>
											<?php } } ?>
										</select>
									</div>
								</div>								
							</div>
							
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="asset_creation_panel5" type="button" >Back</button>					
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
								<button id="quick-add-btn" class="btn btn-block btn-success" type="button" >Add Location</button>					
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Upload Assets -->
		<div class="col-md-6 col-sm-6 col-xs-12">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<legend>Upload Multiple Assets (csv) <span class="pointer pull-right">&nbsp;<small><a download="<?php echo base_url( 'assets/public/csv-templates/AssetsImportTemplate.csv' ); ?>" href="<?php echo base_url( 'assets/public/csv-templates/AssetsImportTemplate.csv' ); ?>" target="_blank" ><i class="fas fa-download" title="Click to Download upload template"></i></a></small></span></legend>
				</div>
				<div class="col-md-12 col-sm-12 col-xs-12 pull-left">
					<form id="docs-upload-form" action="<?php echo base_url( 'webapp/asset/upload_assets/'.$this->user->account_id ); ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
						<input type="hidden" name="page" value="details" />
						<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
						<div class="x_panel tile has-shadow">
							<legend class="legend-header">Please upload your updated file</legend>
							<div class="input-group form-group">
								<label class="input-group-addon">Choose file</label>
								<span class="control-fileupload pointer">
									<label for="file1" class="pointer text-left">Please choose a file on your computer.</label><input id="uploadfile" name="upload_file[]" type="file" id="uploadfile" >
								</span>
							</div>
							<div class="row">
								<div class="col-md-6">
									<button id="doc-upload-btn" class="btn btn-block btn-success" type="submit" >Upload Document</button>					
								</div>
							</div>				
						</div>
					</form>
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
										<option value="">Please select category</option>
										<option value="comm device" >Communication device (Phones, PCs, Laptops)</option>
										<option value="plant" >Plant (Harnesses, Helmets etc.)</option>
										<option value="device" selected="selected" >Installable assets item</option>
										<option value="asset" >Any asset item</option>
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
							<button id="category-quick-add-btn" class="btn btn-success btn-block" type="button" >Add New Category</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
	$(document).ready(function(){
		
		//$( '#parent_asset_id' ).select2();
		
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
						
						var assetTypeId     	= data.asset_type.asset_type_id;
						var assetTypeName   	= data.asset_type.asset_type;
						var assetCategoryId 	= data.asset_type.category_id;
						var assetCategoryName 	= data.asset_type.category_name;

						$('#feedback_message').html( data.status_msg ).delay(3000).fadeToggle("slow");
						
						var optionExists = ( $('#asset-types option[value=' + assetTypeId + ']').length > 0 );

						if( !optionExists ){
							//Only add the new option if it doesn't already exist
							$('#asset-types').append( $('<option >').val( assetTypeId ).text( ucwords( assetTypeName ) ).attr( { 'data-asset_group': assGroup } ) );
						}
						
						//Set selected
						$('#asset-types option[value="'+assetTypeId+'"]').prop( 'selected', true );
						
						//Set the Category if it already exists
						var categoryExists = ( $('#evidoc_category_id option[value=' + assetCategoryId + ']').length > 0 );

						if( !categoryExists ){
							$('#evidoc_category_id').append( $('<option >').val( assetCategoryId ).text( assetCategoryName ) );
						}
						
						//Set selected
						$('#evidoc_category_id option[value="'+assetCategoryId+'"]').prop( 'selected', true );
						
						toggleCommonFields( assGroup );
						
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

		//Toggle some fields depending on the selected asset type
		$( '#asset-types' ).change(function(){
			
			$('#unique-id-label').text('Asset Unique ID *');
			var assetType 	= $(this).val();
			var assetGroup  = $( 'option:selected', this).data( 'asset_group' );

			$('.common-fields').slideDown();

			//Unique ID
			if( $.inArray( "'"+assetGroup+"'", ['comm device'] ) ){
				//$('#unique-id').text('Asset Serial Number');
				$('#unique-id-label').text('Asset Serial Number *');
			}else if( $.inArray( "'"+assetGroup+"'", ['plant'] ) ){
				//$('#unique-id').text('Fuel Card Number');
				$('#unique-id-label').text('Fuel Card Number *');				
			}else{
				$('#unique-id-label').text('Asset Unique ID *');
			}

			$('.ppe-assets, .alarm-panels-attributes').hide();

			switch( assetGroup ){
				case 'comm device':
					$('.mobile-devices, .mobile-device-attributes').slideDown();
					//$('[name="asset_code"]').val('');
					$('[name="alarm_panel_code"]').val('');
					$('[name="asset_imei_number"]').addClass( 'required' );
					$('[name="alarm_panel_code"]').removeClass( 'required' );
					break;
				case 'device':
				case 'asset':
					$('[name="asset_imei_number"]').val('');
					$('[name="alarm_panel_code"]').val('');
					$('[name="asset_imei_number"]').removeClass( 'required' );
					$('[name="alarm_panel_code"]').removeClass( 'required' );
					break;
				case 'plant': 
					$('.ppe-assets').slideDown();
					$('[name="asset_imei_number"]').val('');
					$('[name="alarm_panel_code"]').val('');
					$('[name="asset_imei_number"]').removeClass( 'required' );
					$('[name="alarm_panel_code"]').removeClass( 'required' );
					$('.mobile-device-attributes').slideDown();
					break;
				case 'panel':
					$('.alarm-panels-attributes').slideDown();
					//$('[name="asset_code"]').val('');
					$('[name="asset_imei_number"]').val('');
					$('[name="asset_imei_number"]').removeClass( 'required' );
					$('[name="alarm_panel_code"]').addClass( 'required' );
					break;
			}
		});
	
		$(".asset-creation-steps").click(function(){
			
			//Clear errors first
			$( '.error_message' ).each(function(){
				$( this ).text( '' );
			});
			
			var currentpanel = $(this).data("currentpanel");			
			var inputs_state = check_inputs( currentpanel );			
			if( inputs_state ){
				//If name attribute returned, auto focus to the field and display arror message
				$( '[name="'+inputs_state+'"]' ).focus();
				var labelText = $( '[name="'+inputs_state+'"]' ).parent().find('label').text();
				$( '#'+currentpanel+'-errors' ).text( ucwords( labelText ) +' is a required' );
				return false;
			}
			panelchange("."+currentpanel)	
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
								
								var newAssetId = data.asset.asset.asset_id;
								
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){ 
									location.href = "<?php echo base_url('webapp/asset/profile/'); ?>"+newAssetId;
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
	});
	
	function toggleCommonFields( assetGroup ){
		
		$('.common-fields').slideDown();

		//Unique ID
		if( $.inArray( "'"+assetGroup+"'", ['comm device'] ) ){
			//$('#unique-id').text('Asset Serial Number');
			$('#unique-id-label').text('Asset Serial Number *');
		}else if( $.inArray( "'"+assetGroup+"'", ['plant'] ) ){
			//$('#unique-id').text('Fuel Card Number');
			$('#unique-id-label').text('Fuel Card Number *');				
		}else{
			$('#unique-id-label').text('Asset Unique ID *');
		}

		$('.ppe-assets, .alarm-panels-attributes').hide();

		switch( assetGroup ){
			case 'comm device':
				$('.mobile-devices, .mobile-device-attributes').slideDown();
				//$('[name="asset_code"]').val('');
				$('[name="alarm_panel_code"]').val('');
				$('[name="asset_imei_number"]').addClass( 'required' );
				$('[name="alarm_panel_code"]').removeClass( 'required' );
				break;
			case 'device':
			case 'asset':
				$('[name="asset_imei_number"]').val('');
				$('[name="alarm_panel_code"]').val('');
				$('[name="asset_imei_number"]').removeClass( 'required' );
				$('[name="alarm_panel_code"]').removeClass( 'required' );
				break;
			case 'plant': 
				$('.ppe-assets').slideDown();
				$('[name="asset_imei_number"]').val('');
				$('[name="alarm_panel_code"]').val('');
				$('[name="asset_imei_number"]').removeClass( 'required' );
				$('[name="alarm_panel_code"]').removeClass( 'required' );
				$('.mobile-device-attributes').slideDown();
				break;
			case 'panel':
				$('.alarm-panels-attributes').slideDown();
				//$('[name="asset_code"]').val('');
				$('[name="asset_imei_number"]').val('');
				$('[name="asset_imei_number"]').removeClass( 'required' );
				$('[name="alarm_panel_code"]').addClass( 'required' );
				break;
		}
		
	}
</script>