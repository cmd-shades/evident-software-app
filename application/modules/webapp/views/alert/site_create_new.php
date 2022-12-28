<div>
	<legend>Create New Site</legend>
	<form id="site-creation-form" >
		<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
		<input type="hidden"  name="page" value="details"/>
		<div class="row">
		
			<div class="site_creation_panel1 col-md-6 col-sm-12 col-xs-12">
				<div class="x_panel tile has-shadow">
					<div class="row section-header">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<legend class="legend-header">What's the name of your Site?</legend>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="site_creation_panel1-errors"></h6>
						</div>
					</div>
				
					<div class="input-group form-group">
						<label class="input-group-addon">Site name</label>
						<input name="site_name" class="form-control required" type="text" value="" placeholder="Site name..."  />
					</div>
					
					<div class="hide input-group form-group">
						<label class="input-group-addon">Site reference </label>
						<input name="site_reference" class="form-control" type="text" value="" placeholder="Stite reference (optional)"  />
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
							<legend class="legend-header">What is your Site postcode?</legend>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="site_creation_panel2-errors"></h6>
						</div>
					</div>
					
					<div class="input-group form-group">
						<label class="input-group-addon">Site Postcode</label>
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
					<legend>Please select dwellings on this site</legend>
					
					<div id="address-lookup-result"></div>
					
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
				<div class="x_panel tile has-shadow">
					<legend>Please enter the Site <em>Event Tracking ID <small>(skip if unknown)</small></em></legend>
					<div class="input-group form-group">
						<label class="input-group-addon">Event Tracking ID</label>
						<input name="event_tracking_status_id" class="form-control required" type="text" value="" placeholder="Event Tracking ID"  />
					</div>
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
					<legend>Do you have any sticky notes for this site?</legend>
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12 ">
							<textarea class="form-control" name="site_notes"></textarea>
						</div>
					</div>
					<br/>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="site_creation_panel5" type="button" >Back</button>					
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button id="create-site-btn" class="btn btn-block btn-flow btn-success btn-next" type="button" >Create Site</button>					
						</div>
					</div>						
				</div>						
			</div>	

		</div>
	</form>
</div>

<script>
	$(document).ready(function(){
		
		//Address lookup
		$('.postcode-lookup').change(function(){
			var postCode = $(this).val();
			if( postCode.length > 0 ){
				$.post("<?php echo base_url("webapp/site/get_addresses_by_postcode"); ?>",{postcodes:postCode},function(result){
					$("#address-lookup-result").html(result["addresses_list"]);				
				},"json");
			}
		});
		
		//Package manipulations
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