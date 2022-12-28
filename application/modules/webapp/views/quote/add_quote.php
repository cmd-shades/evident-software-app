<div>
	<legend>Create New Quote</legend>
	<form id="site-creation-form" method="post" action="<?php echo base_url( "quote/add_quote/" ) ?>" >
		<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
		<input type="hidden"  name="page" value="details"/>
		<div class="row">

			<div class="site_creation_panel1 col-md-6 col-sm-12 col-xs-12">
				<div class="x_panel tile has-shadow">
					<legend>The quote needs to be linked:</legend>

					<div class="row">
						<div class="form-group col-md-12 col-sm-12 col-xs-12">
							<!-- <label for="inputState">State</label> -->
							<select id="q_linked_to" class="form-control">
								<option value="">Please select</option>
								<option value="customer">with Customer</option>
								<option value="site">with Site</option>
							</select>
						</div>
					</div>
					
					<div class="customer_cont row" style="display: none;">
						<div class="form-group col-md-12 col-sm-12 col-xs-12">
							<!-- <label for="inputState">State</label> -->
							<select name="customer_id" id="inputState" class="form-control">
								<?php
								if( !empty( $customer_list ) ){ ?>
									<option value="">Please select Customer</option>
									<?php
									foreach( $customer_list as $key => $row ){ ?>
										<option value="<?php echo $row->customer_id; ?>"><?php echo ( !empty( $row->business_name ) ) ? ( ucfirst( $row->business_name ) ) : ( ( !empty( $row->customer_first_name ) && !empty( $row->customer_last_name ) ) ? ( ucfirst( $row->customer_first_name ).' '.ucfirst( $row->customer_last_name ) ) : '' ); ?></option>
									<?php
									}
								} else { ?>
									<option value="">Please add Customers to your Account</option>
								<?php
								} ?>
							</select>
						</div>
					</div>					
					<div class="site_cont row" style="display: none;">
						<div class="form-group col-md-12 col-sm-12 col-xs-12">
							<div class="input-group form-group">
								<label class="input-group-addon">Provide Site ID</label>
								<input name="site_id" class="form-control" type="text" value="" placeholder="Site ID" />
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<button class="btn btn-block btn-flow btn-success btn-next site-creation-steps" data-currentpanel="site_creation_panel1" type="button" >Next</button>
						</div>
					</div>
				</div>
			</div>

			<div class="site_creation_panel2 col-md-6 col-sm-12 col-xs-12" style="display:none">
				<div class="x_panel tile has-shadow">
					<legend>What is the Status for the Quote?</legend>
					<div class="input-group form-group">
						<label class="input-group-addon">Status</label>
						<select name="quote_status" class="form-control">
														<?php
								if( !empty( $customer_list ) ){ ?>
									<option value="">Please select</option>
									<?php
									foreach( $quote_statuses as $row ){ ?>
										<option value="<?php echo $row->status_id; ?>"><?php echo $row->status_name; ?></option>
									<?php
									}
								} else { ?>
									<option value="">Please add Customers to your Account</option>
								<?php
								} ?>
						</select>
					</div>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-info btn-back" data-currentpanel="site_creation_panel2" type="button" >Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-success btn-next site-creation-steps" data-currentpanel="site_creation_panel2" type="button" >Next</button>
						</div>
					</div>
				</div>
			</div>
			
			
			<?php /* ------------------------------------------- */ ?>
			<div class="site_creation_panel3 col-md-12 col-sm-12 col-xs-12" style="display:block;" >
				<div class="ajaxContainer">
					<?php /* <!-- <form id="ajaxForm" method="post" action="<?php echo base_url( '/quote/add_quote/' ); ?>"> --> */ ?>
						<input type="text" id="ajax_input" name="q" placeholder="Type Here"/>
						<input type="submit" id="ajax_submit" value="SUB" />
					<?php /* <!-- </form> --> */ ?>
				</div>
				
				<div id="ajax" style="display: block;width:  50%;float: left;">&nbsp;</div>
			</div>
			
			
			<script>
			$( '#ajax_input' ).on( 'keyup', function(){
				var submitdata = $( '#ajax_input' ).val();
				$.ajax({
					/* type: "POST", */
					url: '<?php echo base_url( 'webapp/quote/billable_items/' ); ?>',
					dataType: 'json',
					/* data: { submitdata: submitdata }, */
					success: function( data ){
						/* response = jQuery.parseJSON(data); */
						console.log( data );
                     
						/* $( '#ajax' ).html( data['content'] ); */
						$( '#ajax' ).html( data.billable_items );
					}
				});  
				return false;
			});
			</script>
			
			<?php /* ------------------------------------------- */ ?>
			
			<form action="<?php echo base_url( '/quote/add_quote/' ); ?>" method="post" class="form-horizontal">
			
			<div class="site_creation_panel3 col-md-12 col-sm-12 col-xs-12" style="display:block;" >
				<!-- <div class="x_panel tile has-shadow">
					<legend>Please add items to the quote</legend> -->
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<form action="<?php echo base_url( '/quote/add_quote/' ); ?>" method="post" class="form-horizontal">
								<div class="x_panel tile fixed_height_380">
									<fieldset>
										<legend>Select Quote Items</legend>
										<div class="input-group form-group">
											<label class="input-group-addon">Item Name</label>
											<input name="item_name" id="item_name" value="" class="form-control" type="text" placeholder="Please select type"  />
										</div>
										
										
										<!--  AJAX  -->
										
										<div class="input-group form-group">
											<label class="input-group-addon">Quantity</label>
											<input name="item_quantity" value="" class="form-control" type="text" placeholder="1"  />
										</div>
										<div class="input-group form-group">
											<label class="input-group-addon">Price</label>
											<input name="item_price" value="" class="form-control" type="text" placeholder="£00.00"  />
										</div>
									</fieldset>
									
									<fieldset>
										<div class="input-group form-group">
											<label class="input-group-addon" >Custom Item</label>
											<input name="site_name" value="" class="form-control" type="text" placeholder="Please type name"  />
										</div>
										<div class="input-group form-group">
											<label class="input-group-addon">Quantity</label>
											<input name="site_name" value="" class="form-control" type="text" placeholder="1"  />
										</div>
										<div class="input-group form-group">
											<label class="input-group-addon">Price</label>
											<input name="site_name" value="" class="form-control" type="text" placeholder="£00.00"  />
										</div>
									</fieldset>
									<?php //if( $this->user->is_admin || in_array('admin', $permitted_actions  || in_array('add', $permitted_actions ) ) ){ ?>
									<?php if( $this->user->is_admin || ( count( array_intersect( ['add','admin'], $permitted_actions ) ) > 0 ) ){ ?>
										<div class="row col-md-6">
											<button id="create-lead-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="submit" >Add Item</button>
										</div>
									<?php }else{ ?>
										<div class="row col-md-6">
											<button id="create-lead-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>
										</div>
									<?php } ?>
								</div>
							</form>
						</div>

						<div class="col-md-6 col-sm-6 col-xs-12">
							<div class="x_panel tile">
								<legend>Quote Items</legend>
								<?php if( $this->user->is_admin || ( count( array_intersect( ['view','admin'], $permitted_actions ) ) > 0 ) ){ ?>
									<table style="width:100%">
										<tr style="font-size:86%" >
											<th width="50%">Item Name</th>
											<th width="12.5%">Price</th>
											<th width="12.5%">Quantity</th>
											<th width="12.5%">Item Value</th>
											<th width="12.5%"><span class="pull-right">Remove</span></th>
										</tr>
										<?php
										if( !empty( $quote_data->quote_items ) ){
											$total = 0;
											foreach( $quote_data->quote_items as $item ){
											$total += $item->item_qty * $item->quoted_price;
											?>
											<tr>
												<!-- <td><a href="<?php echo base_url( 'webapp/quote/profile/'.$item->item_id ); ?>"><?php echo $item->item_id; ?></a></td> -->
												<td width="50%"><?php echo $item->item_name; ?></td>
												<td width="12.5%">£<?php echo $item->quoted_price; ?></td>
												<td width="12.5%"><?php echo $item->item_qty; ?></td>
												<td width="12.5%">£<?php echo $item->item_qty * $item->quoted_price; ?></td>
												<td width="12.5%">
													<span class="pull-right">
														<span>
															<?php if( $this->user->is_admin || ( count( array_intersect( ['delete','admin'], $permitted_actions ) ) > 0 ) ){ ?>
																<span class="text-bold"><a href="#" style="color: red;">x</a></span>
															<?php } ?>
														</span>
													</span>
												</td>
											</tr>
											<?php
											} ?>
											<tr>
												<td colspan="3">&nbsp;</td>
												<td><strong>TOTAL</strong></td>
												<td><strong>£<?php echo $total; ?></strong></td>
											</tr>
											<?php
										} else { ?>
											<tr>
												<td class="5"><?php echo $this->config->item('no_records'); ?></td>
											</tr>
										<?php } ?>
									</table>
								<?php } ?>
							</div>
						</div>
					</div>

					
					
			<?php /* ------------------------------------------- */ ?>
						<div id="address-lookup-result"></div>

						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-info btn-back" data-currentpanel="site_creation_panel3" type="button" >Back</button>
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-success btn-next site-creation-steps" data-currentpanel="site_creation_panel3" type="button" >Next</button>
							</div>
						</div>
			</div>
			
			<?php /* ------------------------------------------- */ ?>

			<div class="site_creation_panel4 col-md-6 col-sm-12 col-xs-12" style="display:none" >
				<div class="x_panel tile has-shadow">
					<legend>Do you have any notes to this quote?</legend>
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<textarea class="form-control" name="quote_notes"></textarea>
						</div>
					</div>
					<br/>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-info btn-back" data-currentpanel="site_creation_panel4" type="button">Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button id="create-site-btn" class="btn btn-block btn-flow btn-success btn-next" type="button">Create Site</button>
						</div>
					</div>
				</div>
			</div>

		</div>
	</form>
</div>

<script>
	$(document).ready( function(){
		$( '#q_linked_to' ).change( function(){
			if( $('#q_linked_to').val() == "customer" ){
				$('.customer_cont').show();
				$('.site_cont').hide();
			} else if ( $('#q_linked_to').val() == "site" ) {
				$('.customer_cont').hide();
				$('.site_cont').show();
			} else {
				$('.customer_cont').hide();
				$('.site_cont').hide();
			}
		});




		//Address lookup
		$( '.postcode-lookup' ).change( function(){
			var postCode = $(this).val();
			if( postCode.length > 0 ){
				$.post("<?php echo base_url("webapp/site/get_addresses_by_postcode"); ?>",{postcodes:postCode},function(result){
					$("#address-lookup-result").html(result["addresses_list"]);
				},"json");
			}
		});

		//Package manipulations
		$( '.addons' ).change( function(){
			$('.sports-addon').click(function() {
				$('.sports-addon').not(this).prop('checked', false);
			});

			$('.movies-addon').click(function() {
				$('.movies-addon').not(this).prop('checked', false);
			});
		});

		$(".site-creation-steps").click( function(){
			var currentpanel = $(this).data("currentpanel");
			panelchange("."+currentpanel)
			return false;
		});

		$(".btn-back").click(function(){
			var currentpanel = $(this).data("currentpanel");
			go_back("."+currentpanel)
			return false;
		});

		function panelchange(changefrom){
			var panelnumber = parseInt(changefrom.slice(20))+parseInt(1);
			var changeto = ".site_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'left'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'right'},500);
			return false;
		}

		function go_back( changefrom ){
			var panelnumber = parseInt(changefrom.slice(20))-parseInt(1);
			var changeto = ".site_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'right'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'left'},500);
			return false;
		}
		
		
		
		$('#item_name').change( function( e ){
			e.preventDefault();
			
			$.ajax({
				url:"<?php echo base_url( 'webapp/quote/billable_items/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success: function( data ){
					if( data.status == 1 && ( data.site !== '' ) ){

						var newSiteId = data.site.site.site_id;

						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 3000
						})
						window.setTimeout(function(){
							location.href = "<?php echo base_url('webapp/site/profile/'); ?>"+newSiteId;
						} ,3000);
					}else{
						swal({
							type: 'error',
							title: data.status_msg
						})
					}
				}
			});
		});
		
		
		/*
		$data['billable_items']	= false;
		$request					= $this->ssid_common->api_call( 'billable_item/items', $postdata, $method = 'GET' );
		if( !empty( $request->status ) && ( $request->status == 1 ) ){
			$data['billable_items']	= ( !empty( $request->items ) ) ? $request->items :  false ;
		}
		*/
		

		//Submit site form
		$( '#create-site-btn' ).click(function( e ){
			e.preventDefault();
			var formData = $('#site-creation-form').serialize();

			swal({
				title: 'Confirm new site creation?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then((result) => {
				$.ajax({
					url:"<?php echo base_url('webapp/site/create/' ); ?>",
					method:"POST",
					data:formData,
					dataType: 'json',
					success:function(data){
						if( data.status == 1 && ( data.site !== '' ) ){

							var newSiteId = data.site.site.site_id;

							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 3000
							})
							window.setTimeout(function(){
								location.href = "<?php echo base_url('webapp/site/profile/'); ?>"+newSiteId;
							} ,3000);
						}else{
							swal({
								type: 'error',
								title: data.status_msg
							})
						}
					}
				});
			}).catch(swal.noop)

		});

	});
</script>

