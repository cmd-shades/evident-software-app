<form id="product-creation-form">
	<div class="product_creation_panel1 col-md-12 col-sm-12 col-xs-12">
		<div class="slide-group">
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<legend class="legend-header">VOD type Product creation</legend>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<h6 class="error_message pull-right" id="product_creation_panel1-errors"></h6>
				</div>
			</div>
			
			<input type="hidden" name="product_details[product_type_id]" value="<?php echo ( !empty( $product_type_id ) ) ? $product_type_id : '' ; ?>" />
			<input type="hidden" name="product_details[site_id]" value="<?php echo ( !empty( $site_details->site_id ) ) ? $site_details->site_id : '' ; ?>" />
			
			<div class="input-group form-group container-full">
				<label class="input-group-addon el-hidden">Product Name</label>
				<input name="product_details[product_name]" class="form-control required container-full" type="text" value="" placeholder="Product Name" />
		
				<label class="input-group-addon el-hidden"> Product Reference Code</label>
				<input name="product_details[product_reference_code]" class="form-control required" type="text" value="" placeholder="Product Reference Code"  />
			</div>
			
			<div class="input-group form-group container-full">
				<label class="input-group-addon el-hidden">Product Description</label>
				<textarea name="product_details[product_description]" class="form-control container-full" type="text" value="" placeholder="Product description"></textarea>
			</div>

			<div class="row">
				<div class="col-lg-6 col-lg-offset-6 col-md-6 col-md-offset-6 col-sm-6 col-sm-offset-6 col-xs-12">
					<button class="btn-block btn-next product-creation-steps" data-currentpanel="product_creation_panel1" type="button">Next</button>
				</div>
			</div>
		</div>
	</div>

	<div class="product_creation_panel2 col-md-12 col-sm-12 col-xs-12 el-hidden">
		<div class="slide-group">
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<legend class="legend-header">What is the Content Provider?</legend>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<h6 class="error_message pull-right" id="product_creation_panel2-errors"></h6>
				</div>
			</div>
			
			<div class="input-group form-group container-full">
				<label class="input-group-addon el-hidden">Content Provider</label>
				<?php 
				if( !empty( $content_providers ) ){ ?>
					<select class="form-control container-full" name="product_details[content_provider_id]" title="Content Provider">
						<option value="">Please select</option>
						<?php
						foreach( $content_providers as $row ){ ?>
							<option value="<?php echo $row->provider_id; ?>"><?php echo ( !empty( $row->provider_name ) ) ? $row->provider_name : '[Not set]' ; ?></option>
						<?php
						} ?>
					</select>
				<?php 
				} ?>
			</div>
			
			<div class="input-group form-group container-full">
				<label class="input-group-addon el-hidden">Number of Rooms?</label>
				<input name="product_details[no_of_rooms]" class="form-control required container-full" type="text" value="" placeholder="Number of Rooms?" />
			</div>
			
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn-block btn-back" data-currentpanel="product_creation_panel2" type="button">Back</button>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn-block btn-next product-creation-steps" data-currentpanel="product_creation_panel2" type="button">Next</button>
				</div>
			</div>
		</div>
	</div>

	<div class="product_creation_panel3 col-md-12 col-sm-12 col-xs-12 el-hidden">
		<div class="slide-group">
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<legend class="legend-header">What is the start and end date and the package size?</legend>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<h6 class="error_message pull-right" id="product_creation_panel3-errors"></h6>
				</div>
			</div>
			
			<div class="input-group form-group container-full">
				<label class="input-group-addon el-hidden">Start Date</label>
				<input class="form-control required container-full datetimepicker" name="product_details[start_date]" type="text" placeholder="Start Date" value="" />
			</div>
			
			<div class="input-group form-group container-full">
				<label class="input-group-addon el-hidden">End Date</label>
				<input class="form-control container-full datetimepicker" name="product_details[end_date]" type="text" placeholder="End Date" value="" />
			</div>
			
			<div class="input-group form-group container-full">
				<label class="input-group-addon el-hidden">No of Titles</label>
				<?php 
				if( !empty( $no_of_titles_packages ) ){ ?>
					<select class="form-control container-full" name="product_details[no_of_titles_id]" title="No of Titles">
						<option value="">No of Titles</option>
						<?php
						foreach( $no_of_titles_packages as $row ){ ?>
							<option value="<?php echo ( !empty( $row->setting_id ) ) ? ( int ) $row->setting_id : '' ; ?>"><?php echo ( !empty( $row->setting_value ) ) ? $row->setting_value : '[Not set]' ; ?></option>
						<?php
						} ?>
					</select>
				<?php 
				} ?>
			</div>
			
			<div class="input-group form-group container-full">
				<label class="input-group-addon el-hidden">Films per Month</label>
				<?php 
				if( !empty( $films_per_month ) ){ ?>
					<select class="form-control container-full" name="product_details[films_per_month_id]" title="No of Titles">
						<option value="">Films per Month</option>
						<?php
						foreach( $films_per_month as $fpm_row ){ ?>
							<option value="<?php echo ( !empty( $fpm_row->setting_id ) ) ? ( int ) $fpm_row->setting_id : '' ; ?>"><?php echo ( !empty( $fpm_row->setting_value ) ) ? $fpm_row->setting_value : '[Not set]' ; ?></option>
						<?php
						} ?>
					</select>
				<?php 
				} ?>
			</div>

			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn-block btn-back" data-currentpanel="product_creation_panel3" type="button">Back</button>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn-block btn-next product-creation-steps" data-currentpanel="product_creation_panel3" type="button">Next</button>
				</div>
			</div>
		</div>
	</div>
	
	
	<div class="product_creation_panel4 col-md-12 col-sm-12 col-xs-12 el-hidden">
		<div class="slide-group">
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<legend class="legend-header">What is the product note?</legend>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<h6 class="error_message pull-right" id="product_creation_panel4-errors"></h6>
				</div>
			</div>
			
			<div class="input-group form-group container-full">
				<label class="input-group-addon el-hidden">Product Note</label>
				<textarea name="product_details[product_note]" class="form-control container-full" type="text" value="" placeholder="Product Note"></textarea>
			</div>

			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn-block btn-back" data-currentpanel="product_creation_panel4" type="button">Back</button>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn-block btn-next product-creation-steps" data-currentpanel="product_creation_panel4" type="button">Next</button>
				</div>
			</div>
		</div>
	</div>
	
	<div class="product_creation_panel5 col-md-12 col-sm-12 col-xs-12 el-hidden">
		<div class="slide-group">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<legend class="legend-header">What is the Product Delivery Mechanism?</legend>
				</div>
				<div class="col-md-12 col-sm-12 col-xs-12">
					<h6 class="error_message pull-right" id="product_creation_panel5-errors"></h6>
				</div>
			</div>
			
			<div class="input-group form-group container-full">
				<label class="input-group-addon el-hidden">Product Delivery Mechanism</label>
				<?php 
				if( !empty( $delivery_mechanism ) ){ ?>
					<select class="form-control required container-full" name="product_details[delivery_mechanism_id]" title="Product Delivery Mechanism">
						<option value="">Please select</option>
						<?php
						foreach( $delivery_mechanism as $row ){ ?>
							<option value="<?php echo ( !empty( $row->setting_id ) ) ? $row->setting_id : ''; ?>" title="<?php echo ( !empty( $row->value_desc ) ) ? $row->value_desc : '' ?>"><?php echo ( !empty( $row->setting_value ) ) ? $row->setting_value : '' ?></option>
						<?php
						} ?>
					</select>
				<?php
				} else { ?>
					<input class="form-control" name="product_details[delivery_mechanism_id]" type="text" placeholder="Product Delivery Mechanism" value="" />
				<?php
				} ?>
			</div>

			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn-block btn-back" data-currentpanel="product_creation_panel5" type="button">Back</button>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn-block btn-next product-creation-steps" data-currentpanel="product_creation_panel5" type="button">Next</button>
				</div>
			</div>
		</div>
	</div>
	
	<div class="product_creation_panel6 col-md-12 col-sm-12 col-xs-12 el-hidden">
		<div class="slide-group">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<legend class="legend-header">What is the package charge?</legend>
				</div>
				<div class="col-md-12 col-sm-12 col-xs-12">
					<h6 class="error_message pull-right" id="product_creation_panel6-errors"></h6>
				</div>
			</div>

			<div class="input-group form-group container-full">
				<label class="input-group-addon el-hidden">Package Charge (p/r/p/m)</label>
				<input name="product_details[package_charge]" class="form-control required container-full" type="text" value="" placeholder="Package Charge (p/r/p/m)?" />
			</div>
			
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn-block btn-back" data-currentpanel="product_creation_panel6" type="button">Back</button>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn-block btn-next product-creation-steps" data-currentpanel="product_creation_panel6" type="button">Next</button>
				</div>
			</div>
		</div>
	</div>

	
	<div class="product_creation_panel7 col-md-12 col-sm-12 col-xs-12 el-hidden">
		<div class="slide-group">
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<legend class="legend-header">Is the product FTG and what is the Status?</legend>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<h6 class="error_message pull-right" id="product_creation_panel7-errors"></h6>
				</div>
			</div>
			
			<div class="input-group form-group container-full">
				<label class="input-group-addon el-hidden">Free to Guest</label>
				<select class="form-control container-full" name="product_details[is_content_ftg]" title="Is FTG?">
					<option value="">Please select</option>
					<option value="yes">Yes</option>
					<option value="no">No</option>
				</select>
			</div>
			
			<div class="input-group form-group container-full">
				<label class="input-group-addon el-hidden">Product Status</label>
				<?php 
				if( !empty( $product_statuses ) ){ ?>
					<select class="form-control required container-full" name="product_details[product_status_id]" title="Product Status">
						<option value="">Please select</option>
						<?php
						foreach( $product_statuses as $row ){ ?>
							<option value="<?php echo ( !empty( $row->setting_id ) ) ? $row->setting_id : ''; ?>" <?php echo ( !empty( $row->setting_value ) && strtolower( $row->setting_value ) == "active" ) ? 'selected="selected"' : '' ; ?> title="<?php echo ( !empty( $row->value_desc ) ) ? $row->value_desc : '' ?>"><?php echo ( !empty( $row->setting_value ) ) ? $row->setting_value : '' ?></option>
						<?php
						} ?>
					</select>
				<?php
				} ?>
			</div>

			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn-block btn-back" data-currentpanel="product_creation_panel7" type="button">Back</button>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn-block btn-next" data-currentpanel="product_creation_panel7" type="submit">Add Product</button>
				</div>
			</div>
		</div>
	</div>
</form>