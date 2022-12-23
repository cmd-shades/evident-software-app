<style>
	.close{
		color: #5c5c5c;
	}
	
	/* Modal Width Override - We kinda need it a bit wider */
	@media (min-width: 992px){
		.modal-lg {
			width: 990px;
		}
	}
</style>

<!-- Modal for adding a new bundle -->
<div id="add-distribution-bundle-modal" >
	<div class="modal fade add-distribution-bundle-modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<form id="distribution_bundle-creation-form">
				<input type="hidden" name="page" value="details" />
				<input type="hidden" name="distribution_group_id" value="<?php echo $distribution_group_details->distribution_group_id; ?>" />
				<div class="modal-content">
					<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
						<h4 class="modal-title" id="myAssetTypeModalLabel">Add a New Distribution Bundle</h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="distribution_bundle_creation_panel1 col-md-12 col-sm-12 col-xs-12">
								<div class="section-container">
									<div class="input-group form-group">
										<label class="input-group-addon">Bundle name *</label>
										<input name="distribution_bundle" class="form-control required has-validation" type="text" value="" placeholder="Bundle name"  />
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">License Start Date</label>
										<input name="license_start_date" class="form-control datetimepicker" type="text" value="" placeholder="License Start Date"  />							
									</div>
									
									<div class="row">
										<div class="col-md-6 col-md-12 col-sm-12 col-xs-12">
											<button class="btn btn-block btn-flow btn-success btn-next distribution-bundle-creation-steps" data-currentpanel="distribution_bundle_creation_panel1" role="button" >Next</button>					
										</div>
									</div>
								</div>
							</div>

							<div class="distribution_bundle_creation_panel2 col-md-12 col-sm-12" style="display:none">
								<div class="section-container">
									
									<?php include( 'inc/bundle_sites_selection.php' ) ?>
									
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="distribution_bundle_creation_panel2" type="button" >Back</button>					
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn btn-block btn-flow btn-success btn-next distribution-bundle-creation-steps" data-currentpanel="distribution_bundle_creation_panel2" type="button" >Next</button>					
										</div>
									</div>
								</div>
							</div>
							
							<div class="distribution_bundle_creation_panel3 col-md-12 col-sm-12" style="display:none">
								<div class="section-container">
									
									<?php include( 'inc/bundle_content_selection.php' ) ?>
									
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="distribution_bundle_creation_panel3" type="button" >Back</button>					
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn btn-block btn-flow btn-success btn-next distribution-bundle-creation-steps" data-currentpanel="distribution_bundle_creation_panel3" type="button" >Next</button>					
										</div>
									</div>
								</div>
							</div>
							
							<div class="distribution_bundle_creation_panel4 col-md-12 col-sm-12" style="display:none">
								<div class="section-container">
									<legend>Summary</legend>
									<div class="row">
										<div class="col-md-12 col-sm-12 col-xs-12">
											<div class="input-group form-group">
												<label class="input-group-addon">Is this a Base Line Bundle?</label>
												<div class="form-control">
													<input type="radio" name="base_line" value="1" id="base_line_yes" > &nbsp;&nbsp;<label for="base_line_yes" class="pointer" >Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;
													<input type="radio" name="base_line" value="0" id="base_line_no" checked="checked" > <label for="base_line_no" class="pointer" >No</label>&nbsp;
												</div>
											</div>
										</div>
									</div>
									
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-back" data-currentpanel="distribution_bundle_creation_panel4" type="button">Back</button>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button id="create-distribution_bundle-btn" class="btn-block btn-flow btn-next" type="button" data-currentpanel="distribution_bundle_creation_panel4"  >Create Bundle</button>
										</div>
									</div>
								</div>
							</div>
						</div>										
					</div>
				</div>
			</form>
		</div>
	</div>
</div>


<script>
	$( document ).ready(function(){
		
		$( '.has-validation' ).on( 'keypress', function(){
			$( this ).css("border","1px solid #ccc");;
		});
		
		//Add New Bundle Trigger
		$( '#add-new-bundle-trigger' ).click(function(){
			$( '#add-distribution-bundle-modal .add-distribution-bundle-modal' ).modal( 'show' );
		});
		
		$( ".distribution-bundle-creation-steps" ).click(function(){
			
			//Clear errors first
			$( '.error_message' ).each( function(){
				$( this ).text( '' );
			});
			
			var currentpanel = $(this).data( "currentpanel" );			
			var inputs_state = check_inputs( currentpanel );			
			if( inputs_state ){
				//If name attribute returned, auto focus to the field and display arror message
				$( '[name="'+inputs_state+'"]' ).focus().css("border","1px solid red");;
				//var labelText = $( '[name="'+inputs_state+'"]' ).parent().find('label').text();
				//$( '#'+currentpanel+'-errors' ).text( ucwords( labelText ) +' is a required' );
				return false;
			}
			panelchange( "."+currentpanel )	
			return false;
		});
		
		
		//Submit distribution_bundle form
		$( '#create-distribution_bundle-btn' ).click( function( e ){
			
			e.preventDefault();
			
			var formData 	 	= $( '#distribution_bundle-creation-form' ).serialize();

			var totalSelected 	= $( '#selectedContent option:selected' ).length;
			var isBaseLine 		= $( 'input[name="base_line"]:checked' ).val();
				isBaseLine		= ( ( !isBaseLine ) || ( isBaseLine == undefined ) ) ? 0 : isBaseLine;
				
			var films_to_add	= [];

			swal({
				title: 'Confirm new Distribution Bundle creation?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function ( result ) {
				if ( result.value ) {
					
					var films_added	= []
					$( "[name='bundle_content[]'] option" ).each( function(){ 
						films_added.push( $( this ).val() ); 
					});
					
					var addedStr = encodeURIComponent( JSON.stringify( films_added ) ); 
					
					$.ajax({
						url:"<?php echo base_url( 'webapp/distribution/create_distribution_bundle/' ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 && ( data.distribution_bundle !== '' ) ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout( function(){
									
									if( isBaseLine == 1 ){
										location.href = "<?php echo base_url( 'webapp/distribution/profile/'.$distribution_group_details->distribution_group_id.'/inventory?base_line=' ); ?>"+isBaseLine;
									} else {
										//Re-direct to the inventory for Auto Removal Confirmation
										location.href = "<?php echo base_url( 'webapp/distribution/profile/'.$distribution_group_details->distribution_group_id.'/inventory?auto_remove=1&total_to_remove=' ); ?>"+totalSelected+'&base_line='+isBaseLine+'&films_added=' + addedStr;
									}
								}, 3000 );
							} else {
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						}
					});
				} else {
					var currentpanel 	= $("."+$(this).data( "currentpanel" ));
					var panelnumber 	= parseInt( currentpanel.match(/\d+/) )+parseInt(1);					
					$( ".distribution_bundle_creation_panel"+panelnumber ).hide( "slide", { direction : 'left' }, 500 );
					go_back( ".distribution_bundle_creation_panel2" );
					return false;
				}
			}).catch( swal.noop )
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
		
		$( ".btn-back" ).click(function(){
			var currentpanel = $( this ).data("currentpanel");
			go_back("."+currentpanel)	
			return false;
		});	
		
		function panelchange(changefrom){
			var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
			var changeto 	= ".distribution_bundle_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", { direction : 'left' }, 500);
			$(changeto).delay( 600 ).show( "slide", { direction : 'right' },500 );	
			return false;	
		}
		
		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
			var changeto = ".distribution_bundle_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'right'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'left'},500);	
			return false;	
		}
	});
</script>