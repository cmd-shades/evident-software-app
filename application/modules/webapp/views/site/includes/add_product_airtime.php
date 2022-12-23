<form id="product-creation-form">
	<div class="product_creation_panel1 col-md-12 col-sm-12 col-xs-12">
		<div class="slide-group">
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<legend class="legend-header">Airtime type Product creation</legend>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<h6 class="error_message pull-right" id="product_creation_panel1-errors"></h6>
				</div>
			</div>
			
			<input type="hidden" name="product_details[product_type_id]" value="<?php echo ( !empty( $product_type_id ) ) ? $product_type_id : '' ; ?>" />
			<input type="hidden" name="product_details[site_id]" value="<?php echo ( !empty( $site_details->site_id ) ) ? $site_details->site_id : '' ; ?>" />
			<input type="hidden" name="product_details[site_reference_code]" value="<?php echo ( !empty( $site_details->site_reference_code ) ) ? $site_details->site_reference_code : '' ; ?>" />
			
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
					<legend class="legend-header">Is the install Free to Guest?</legend>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<h6 class="error_message pull-right" id="product_creation_panel2-errors"></h6>
				</div>
			</div>
			
			<div class="input-group form-group container-full">
				<label class="input-group-addon el-hidden">Free to Guest</label>
				<select id="is_airtime_ftg" class="form-control required container-full" name="product_details[is_airtime_ftg]" title="Is Airtime FTG?">
					<option value="">Free to Guest</option>
					<option value="yes">Yes</option>
					<option value="no">No</option>
				</select>
			</div>

			<div id="packageCharge" class="input-group form-group container-full el-hidden">
				<label class="input-group-addon el-hidden">Package Charge (p/r/p/m)</label>
				<input name="product_details[package_charge]" class="form-control container-full" type="text" value="" placeholder="Package Charge (p/r/p/m)" />
			</div>
			
			<div class="input-group form-group container-full">
				<label class="input-group-addon el-hidden">Number of Rooms?</label>
				<input name="product_details[no_of_rooms]" class="form-control required container-full" type="text" value="" placeholder="Number of Rooms" />
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
					<legend class="legend-header">What is the Start and End Date?</legend>
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
					<legend class="legend-header">What is Airtime PIN?</legend>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<h6 class="error_message pull-right" id="product_creation_panel4-errors"></h6>
				</div>
			</div>
			
			<div class="input-group form-group container-full">
				<label class="input-group-addon el-hidden">Airtime PIN</label>
				<input name="product_details[airtime_pin]" class="form-control required container-full" type="text" value="" placeholder="Airtime PIN" />
			</div>
			
			<div class="input-group form-group container-full">
				<label class="input-group-addon el-hidden">Sale Currency</label>
				<?php
				if( !empty( $sale_currencies ) ){ ?>
					<select name="product_details[sale_currency_id]" class="form-control">
						<option value="">Sale Currency</option>
						<?php foreach( $sale_currencies as $row ){?>
							<option value="<?php echo ( !empty( $row->setting_id ) ) ? $row->setting_id : ''; ?>" title="<?php echo ( !empty( $row->value_desc ) ) ? $row->value_desc : '' ?>"><?php echo ( !empty( $row->setting_value ) ) ? $row->setting_value : '' ?></option>
						<?php } ?>
					</select>
				<?php
				} else { ?>
					<input name="product_details[sale_currency_id]" type="text" value="" placeholder="Sale Currency ID" />
				<?php
				} ?>
			</div>
			
			<div class="input-group form-group container-full el-hidden">
				<label class="input-group-addon el-hidden">Product Status</label>
				<?php 
				if( !empty( $product_statuses ) ){ ?>
					<select class="form-control required container-full" name="product_details[product_status_id]" title="Product Status">
						<option value="">Please select Product Status</option>
						<?php
						foreach( $product_statuses as $row ){ ?>
							<option value="<?php echo ( !empty( $row->setting_id ) ) ? $row->setting_id : ''; ?>" <?php echo ( !empty( $row->setting_value ) && strtolower( $row->setting_value ) == "active" ) ? 'selected="selected"' : '' ; ?> title="<?php echo ( !empty( $row->value_desc ) ) ? $row->value_desc : '' ?>"><?php echo ( !empty( $row->setting_value ) ) ? $row->setting_value : '' ?></option>
						<?php
						} ?>
					</select>
				<?php
				} ?>
			</div>
			
			<div class="input-group form-group container-full">
				<label class="input-group-addon el-hidden">Adult Active?</label>
				<select class="form-control container-full" name="product_details[is_adult_active]" title="Is Adult Active?">
					<option value="">Adult Active</option>
					<option value="yes">Yes</option>
					<option value="no">No</option>
				</select>
			</div>
			
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn-block btn-back" data-currentpanel="product_creation_panel4" type="button">Back</button>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn-block btn-next check-airtimepin-button" data-currentpanel="product_creation_panel4" type="button">Next</button>
				</div>
			</div>
		</div>
	</div>
	
	
	<div class="product_creation_panel5 col-md-12 col-sm-12 col-xs-12 el-hidden">
		<div class="slide-group">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<legend class="legend-header">What is the associated Plan and Provider?</legend>
				</div>
				<div class="col-md-12 col-sm-12 col-xs-12">
					<h6 class="error_message pull-right" id="product_creation_panel5-errors"></h6>
				</div>
			</div>

			<div class="outer-provider-plan-container" style="">
				<div class="provider-plan-container">
						<div class="input-group form-group container-full">
							<label class="input-group-addon el-hidden">Content Provider</label>
							<?php 
							if( !empty( $content_providers ) ){ ?>
								<select class="form-control container-full content_provider_trigger" name="product_details[price_plans][0][provider_id]" title="Content Provider">
									<option value="">Please select Content Provider</option>
									<?php
									foreach( $content_providers as $row ){ ?>
										<option value="<?php echo $row->provider_id; ?>"><?php echo ( !empty( $row->provider_name ) ) ? $row->provider_name : '[Not set]' ; ?></option>
									<?php
									} ?>
								</select>
							<?php
							} ?>
						</div>
						
						<div class="input-group form-group container-full airtime_plan el-hidden">
							<label class="input-group-addon el-hidden">Airtime Plan</label>
							<select name="product_details[price_plans][0][plan_id]" class="airtime_plan_trigger form-control container-full">
							</select>
						</div>
						
						<div class="input-group form-group container-full airtime_plan_price el-hidden">
							<label class="input-group-addon el-hidden">Airtime Plan Price</label>
							<input name="product_details[price_plans][0][plan_price]" class=" form-control required container-full" type="text" value="" placeholder="Airtime Plan Price" />
						</div>
				</div>
			
				<div id="outputArea"></div>
				<div class="add_price_plan"><a class=""><i class="fas fa-plus-circle"></i> Add Price Plan</a></div>
			</div>
			
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn-block btn-back" data-currentpanel="product_creation_panel5" type="button">Back</button>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<button class="btn-block btn-next" data-currentpanel="product_creation_panel5" type="submit">Add Product</button>
				</div>
			</div>
		</div>
	</div>
</form>

<script type="text/javascript">
$( document ).ready( function(){
	
	// Adding dynamic field after click
	var i = 1;
	$( ".add_price_plan > a" ).on( "click", function(){
		var template2 = '<div class="provider-plan-container">';
		template2 = '<div class="input-group form-group container-full">';
		template2 += '<label class="input-group-addon el-hidden">Content Provider</label>';
			<?php 
			if( !empty( $content_providers ) ){ ?>
			template2 += '<div class="provider-plan-container">';
				template2 += '<select class="form-control container-full content_provider_trigger" name="product_details[price_plans][' + i + '][provider_id]" title="Content Provider">';
					template2 += '<option value="">Please select Content Provider</option>';
					<?php
					foreach( $content_providers as $row ){ ?>
						template2 += '<option value="<?php echo $row->provider_id; ?>"><?php echo ( !empty( $row->provider_name ) ) ? $row->provider_name : "[Not set]" ; ?></option>';
					<?php
					} ?>
				template2 += '</select>';
			<?php
			} ?>
		
		template2 += '<div class="input-group form-group container-full airtime_plan el-hidden">';
			template2 += '<label class="input-group-addon el-hidden">Airtime Plan</label>';
			template2 += '<select name="product_details[price_plans][' + i + '][plan_id]" class="airtime_plan_trigger form-control container-full">';
			template2 += '</select>';
		template2 += '</div>';
		
		template2 += '<div class="input-group form-group container-full airtime_plan_price el-hidden">';
			template2 += '<label class="input-group-addon el-hidden">Airtime Plan Price</label>';
			template2 += '<input name="product_details[price_plans][' + i + '][plan_price]" class="form-control required container-full" type="text" value="" placeholder="Airtime Plan Price" />';
		template2 += '</div>';
		template2 += '</div>';
		template2 += '</div>';

		i++;
		$( "#outputArea" ).append( template2 );
	});
	
	
	function pullAirtimePlan( thisElement, providerID ){
		if( parseInt( providerID ) > 0 ){
			
			$.ajax({
				url: "<?php echo base_url( 'webapp/provider/provider_price_plan/' ); ?>",
				method: "POST",
				data: {
					"provider_id": providerID,
				},
				dataType: 'JSON',
				success: function( data ) {
					if( data.status == 1 ){
						$( thisElement ).parent().parent().find( ".airtime_plan" ).removeClass( "el-hidden" ).addClass( "el-shown" );
						$( thisElement ).parent().parent().find( ".airtime_plan_price" ).removeClass( "el-hidden" ).addClass( "el-shown" );
						var element = $( thisElement ).parent().parent().find( '.airtime_plan_trigger' );
						$( element ).empty().append( data.provider_price_plan );
					} else {
						swal({
							type: 'error',
							title: data.status_msg,
							timer: 3000
						})
						$( thisElement ).parent().parent().find( ".airtime_plan" ).removeClass( "el-shown" ).addClass( "el-hidden" );
						$( thisElement ).parent().parent().find( ".airtime_plan_price" ).removeClass( "" ).addClass( "el-hidden" );
					}
				}
			});
		} else {
			$( this ).parent().next( ".airtime_plan, .airtime_plan_price" ).removeClass( "el-shown" ).addClass( "el-hidden" );
		}
	}

	$( '#outputArea' ).on( "change", ".content_provider_trigger", function(){
		var thisElement 	= $( this );
		var providerID 		= thisElement.val();
		pullAirtimePlan( thisElement, providerID );
	});

	
	$( '.content_provider_trigger' ).on( "change", function(){
		var thisElement 	= $( this );
		var providerID 		= thisElement.val();
		pullAirtimePlan( thisElement, providerID );
	});
		
		
	$( "#is_airtime_ftg" ).on( "change", function(){
		if( $( this ).val() == 'yes' ){
			$( "#packageCharge" ).removeClass( "el-hidden" );
			$( "*[name='product_details[package_charge]']" ).addClass( "required" );
		} else {
			$( "#packageCharge" ).removeClass( "el-hidden" ).addClass( "el-hidden" );
			$( "*[name='product_details[package_charge]']" ).removeClass( "required" );
		}
	});
});
</script>