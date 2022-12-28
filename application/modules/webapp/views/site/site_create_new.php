<div>
	<legend>Create New Building</legend>
	<form id="site-creation-form" >
		<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
		<input type="hidden"  name="page" value="details"/>
		<div class="row">
		
			<div class="site_creation_panel1 col-md-6 col-sm-12 col-xs-12">
				<div class="x_panel tile has-shadow">
					<div class="row section-header">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<legend class="legend-header">What's the name of your Building?</legend>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="site_creation_panel1-errors"></h6>
						</div>
					</div>
				
					<div class="input-group form-group">
						<label class="input-group-addon">Building Name</label>
						<input name="site_name" class="form-control required" type="text" value="" placeholder="Building name..."  />
					</div>
					
					<div class="input-group form-group">
						<label class="input-group-addon">Estate Name </label>
						<input name="estate_name" class="form-control" type="text" value="" placeholder="Estate name (optional)"  />
					</div>
					
					<div class="input-group form-group">
						<label class="input-group-addon">3rd Party Site Ref</label>
						<input name="external_site_ref" class="form-control" type="text" placeholder="3rd Party/External Site Ref (optional)" />
					</div>

					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-success btn-next site-creation-steps" data-currentpanel="site_creation_panel1" type="button" >Next</button>					
						</div>
					</div>
				</div>
			</div>

			<div class="site_creation_panel2 col-md-6 col-sm-12 col-xs-12" style="display:none">
				<div class="x_panel tile has-shadow">
					<div class="row section-header">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<legend class="legend-header">What is your Building postcode?</legend>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="site_creation_panel2-errors"></h6>
						</div>
					</div>
					
					<div class="input-group form-group">
						<label class="input-group-addon">Building Postcode</label>
						<input name="site_postcodes" class="postcode-lookup form-control required" type="text" value="" placeholder="Comma separated e.g. CR0 9Xp, CR0 4GE"  />
					</div>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="site_creation_panel2" type="button" >Back</button>					
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-success btn-next site-creation-steps" data-currentpanel="site_creation_panel2" type="button" >Next</button>					
						</div>
					</div>
				</div>
			</div>

			<div class="site_creation_panel3 col-md-6 col-sm-12 col-xs-12" style="display:none" >
				<div class="x_panel tile has-shadow">
					<legend>Please select your building address</legend>
					<select id="site_address_id" name="site_address_id" class="form-control">
					</select>
					<br/>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="site_creation_panel3" type="button" >Back</button>					
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-success btn-next site-creation-steps" data-currentpanel="site_creation_panel3" type="button" >Next</button>					
						</div>
					</div>
				</div>
			</div>
			
			<div class="site_creation_panel4 col-md-6 col-sm-12 col-xs-12" style="display:none" >
				
				<legend>Please select addresses on this Building</legend>
					
				<div id="address-lookup-result"></div>
				
				<?php /* ?><div class="x_panel tile has-shadow">
					<legend>Please enter the Site <em>Event Tracking ID <small>(skip if unknown)</small></em></legend>
					<div class="input-group form-group">
						<label class="input-group-addon">Event Tracking ID</label>
						<input name="event_tracking_status_id" class="form-control" type="text" value="" placeholder="Event Tracking ID"  />
					</div>
				<?php */ ?>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="site_creation_panel4" type="button" >Back</button>					
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-success btn-next site-creation-steps" data-currentpanel="site_creation_panel4" type="button" >Next</button>					
						</div>
					</div>
				</div>
				
			</div>
			
			<div class="site_creation_panel5 col-md-6 col-sm-12 col-xs-12" style="display:none" >
				<div class="x_panel tile has-shadow">
					<legend>Additonal Information</legend>
					<div class="input-group form-group">
						<label class="input-group-addon">Confirm Region</label>
						<select id="region_id" name="region_id" class="form-control" >
						<!-- <select id="region_id" name="region_id" class="form-control" style="width:100%; display:none; margin-bottom:10px;" data-label_text="Building Region" > -->
							<option value="">Please select</option>
							<?php if( !empty( $postcode_regions ) ) { foreach( $postcode_regions as $k => $region ) { ?>
								<option value="<?php echo $region->region_id; ?>" ><?php echo $region->region_name; ?></option>
							<?php } } ?>
						</select>
					</div>
					
					<div class="form-group">
						<h5>Do you have any notes for this Building (sticky notes)?</h5>
						<textarea class="form-control" name="site_notes"></textarea>
					</div>
					<hr>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="site_creation_panel5" type="button" >Back</button>					
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button id="create-site-btn" class="btn btn-block btn-flow btn-success btn-next" type="button" >Create Building Record</button>					
						</div>
					</div>						
				</div>						
			</div>	

		</div>
	</form>
</div>

<script>
	$(document).ready(function(){
		
		/* $( '#__region_id' ).select2({
			placeholder: "Please select",
			allowClear: true,
			minimumResultsForSearch: -1,
		}); */
		
		//ADDRESS LOOKUP
		$('.postcode-lookup').change(function(){
			var postCode = $(this).val();
			if( postCode.length > 0 ){
				$.post("<?php echo base_url("webapp/site/get_addresses_by_postcode"); ?>",{postcodes:postCode},function(result){
					$("#address-lookup-result").html(result["addresses_list"]);				
					$("#site_address_id").html(result["site_address"]);				
				},"json");
			}
		});
		
		// SELECT ALL ADDRESSES
		$( '#address-lookup-result' ).on( 'change', '#check_all', function(){
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
		
		//PACKAGE MANIPULATIONS
		$('.addons').change(function(){
			$('.sports-addon').click(function() {
				$('.sports-addon').not(this).prop('checked', false);
			});
			
			$('.movies-addon').click(function() {
				$('.movies-addon').not(this).prop('checked', false);
			});
		});

		$(".site-creation-steps").click(function(){
			
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
			var changeto = ".site_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'left'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'right'},500);	
			return false;	
		}
		
		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
			var changeto = ".site_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'right'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'left'},500);	
			return false;	
		}
		
		//SUBMIT SITE FORM
		$( '#create-site-btn' ).click(function( e ){
			e.preventDefault();
			var formData = $('#site-creation-form').serialize();
			
			swal({
				title: 'Confirm new Building creation?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/site/create_site/' ); ?>",
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
				}else{
					$( ".site_creation_panel4" ).hide( "slide", { direction : 'left' }, 500 );
					go_back( ".site_creation_panel2" );
					return false;
				}
			}).catch(swal.noop)
		});
		
	});
</script>