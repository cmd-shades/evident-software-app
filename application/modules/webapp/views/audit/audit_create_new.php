<style>
	.panel-body{
		background-color:#F7F7F7; 
		height:140px; 
		min-height:140px;
	}
</style>


<div>
	<div class="col-md-4 col-md-offset-4 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Create New Audit</legend>
			<?php if( !empty( $audit_types ) ){ ?>
				<div class="rows">
					<div class="accordion" id="panel-accordion" role="tablist" aria-multiselectable="true">
						<?php $k = 1; foreach( $audit_types as $category => $category_audits ){ $k++; ?>
							<div class="panel" >
								<a class="panel-heading collapsed text-auto" role="tab" id="heading<?php echo number_to_words( $k ); ?><?php echo $k; ?>" data-toggle="collapse" data-parent="#panel-accordion" href="#collapse<?php echo number_to_words( $k ); ?><?php echo $k; ?>" aria-expanded="false" aria-controls="collapse<?php echo number_to_words( $k ); ?>">
									<h4 class="panel-title"><?php echo  $category; ?><span class="pull-right"><?php echo ( !empty( $category_audits ) ) ? '<em>('.count( object_to_array( $category_audits ) ).' type'.( ( count( object_to_array( $category_audits ) ) > 1 ) ? 's' : '' ).')</em>' : ''; ?></span></h4>
								</a>
								<div id="collapse<?php echo number_to_words( $k ); ?><?php echo $k; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<?php echo number_to_words( $k ); ?>">
									<form id="audit-form<?php echo $k; ?>">
										<div class="panel-body" >
											<div id="radio-panel<?php echo $k; ?>" style="margin:0 30px; height:140px; min-height:140px;" >
												<legend class="hide legend-header">&nbsp;</legend>
												<?php foreach( $category_audits as $key => $inner_audit_type ){ ?>
													<div class="radio" >
														<label><input class="radio-opt-selector" type="radio" value="<?php echo $inner_audit_type->audit_type_id; ?>" data-grp_id="<?php echo $k; ?>" name="audit_type_id" > <?php echo $inner_audit_type->alt_audit_type; ?></label>
													</div>
												<?php } ?>
											</div>

											<div id="search-panel<?php echo $k; ?>" style="margin:0 30px; display:none; height:140px; min-height:140px;" >
												<div class="">
													<legend class="hide legend-header">Find the <?php echo  $category; ?> to audit </legend>
													<div class="form-group">
														<input class="form-control" type="text" placeholder="Search <?php echo  $category; ?> records..." value="" />
													</div>
													<div class="form-group">
														<select id="asset_type_id" name="asset_type_id" class="form-control" >
															<option>Please select</option>
															<option value="1">Please select <?php echo $k; ?></option>
														</select>
													</div>
													
													<div class="row">
														<div class="col-md-6 col-sm-6 col-xs-12">
															<button class="btn btn-sm btn-block btn-info btn-back" type="button" data-container_id="<?php echo $k; ?>" >Back</button>					
														</div>
														<div class="col-md-6 col-sm-6 col-xs-11">
															<button id="submit-survey-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="button" >Audit <?php echo  $category; ?></button>					
														</div>
													</div>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		
		//Trigger search field
		$( '.radio-opt-selector' ).change( function(){ 

			var grpId  = $( this ).data( 'grp_id' );
			
			$( '#radio-panel'+grpId ).hide( "slide", {direction : 'left'}, 500);
			$( '#search-panel'+grpId ).delay(600).show( "slide", {direction : 'right'},500);
			
		} );
		
		//Go back-btn
		$( '.btn-back' ).click( function(){ 

			var containerId  = $( this ).data( 'container_id' );
			
			$( '#search-panel'+containerId ).hide( "slide", {direction : 'right'}, 500);
			$( '#radio-panel'+containerId ).delay(600).show( "slide", {direction : 'left'},500);	

		} );
		
		
		
		
		//Detect selected option value
		$( '.radio-options' ).change( function(){ 
			
			var selectedOption = $( this ).val().toLowerCase();
			var triggerOption  = $( this ).data( 'extra_info_trigger' ).toLowerCase();
			var infoKey  	   = $( this ).data( 'info_key' );
			
			if( selectedOption == triggerOption ){
				$( '.extra-info-response'+infoKey ).slideDown();
			}else{
				$( '.extra-info-response'+infoKey ).slideUp();
				$( '.extra-info-response'+infoKey+' :input' ).val( '' );
			}
			
		} );
	
		$(".survey-creation-steps").click(function(){
				
			var sectionTag 	= $( this ).data( 'section_tag' );
				sectionTag  = '.sectGrp'+sectionTag;
		
			var formData 	= $( sectionTag+' :input' ).serialize();
			
			submitForm( formData );
			
			//Submit section data
			//if( submitForm( formData ) ){
				var currentpanel = $( this ).data("currentpanel");
				panelchange("."+currentpanel);
				return false;
			// }else{
				// swal({
					// type: 'error',
					// title: 'Something went wrong while submitting your data. Please try again.'
				// });
				// return false;
			// }
		});	
		
		$(".btn-back").click(function(){
			var currentpanel = $(this).data("currentpanel");
			go_back("."+currentpanel)	
			return false;
		});	
		
		function panelchange(changefrom){
			var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
			var changeto = ".survey_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'left'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'right'},500);	
			return false;	
		}
		
		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
			var changeto = ".survey_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'right'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'left'},500);	
			return false;	
		}

		//Update permissions
		function submitForm( formData ){
			//submit only the changed items
			$.ajax({
				url:"<?php echo base_url('webapp/survey/create_survey/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 ){
						//Set survey id
						var newSurveyId = data.survey.survey.survey_id;
						$( '.survey_id' ).val( newSurveyId );
						return true;
					}else{
						swal({
							type: 'error',
							title: data.status_msg
						})
						return false;
					}			
				}
			});
			
		}
		
		//Trigger Site search on btn click
		$('#find-site').click(function(){
			var searchTerm = encodeURIComponent( $( '#site-search' ).val() );
			if( searchTerm.length > 0 ){
				$.post("<?php echo base_url("webapp/survey/search_site"); ?>",{ search_term:searchTerm },function(result){
					$("#site-lookup-result").html( result["sites_list"] );				
				},"json");
			}
		});

		//Trigger address search on pressing-enter key
		$( '#site-search' ).keypress( function( e ){
			if( e.which == 13 ){
				$('#find-site').click();
			}
		});
		
		//If user clears the postcode filed, clear the previously selected address
		$( '#site-search' ).change( function(){

			var searchTerm = $( this ).val();

			if( searchTerm == null || searchTerm.length == 0 ||  searchTerm == '' ){
				$( '.confirm-site' ).slideUp( 'fast' );
				$( '[name="site_name"]' ).val( '' );
				$( '[name="site_address"]' ).val( '' );
				$("#site-lookup-result").html( '' );	
			}
		});
		
		//Show and populate the address fields when an address is selected
		$( '#site-lookup-result' ).change( function(){
			
			var siteName 	= $('option:selected', this).data( 'site_name' ),
				siteAddress = $('option:selected', this).data( 'site_address' );

			$( '[name="site_name"]' ).val( siteName );
			$( '[name="site_address"]' ).val( siteAddress );
			
			$( '.confirm-site' ).slideDown( 'fast' );

		});
		
		//Submit Survey on completion
		$( '#submit-survey-btn' ).click(function( e ){
			e.preventDefault();
			
			var sectionTag 	= $( this ).data( 'section_tag' );
				sectionTag  = '.sectGrp'+sectionTag;
		
			var formData 	= $( sectionTag+' :input' ).serialize();
		
			swal({
				title: 'Confirm Survey submission?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/survey/create_survey/' ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 && ( data.survey !== '' ) ){
								
								var reqSource	= $( '[name="request_source"]' ).val();
								var newSurveyId = data.survey.survey.survey_id;
								
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){ 
									if( reqSource == 'risks' ){
										//If the last call is from submitting risks the redirect to survey items
										location.href = "<?php echo base_url('webapp/survey/create/'); ?>"+newSurveyId+"/survey_items/";
									}else if( reqSource == 'survey_items' ){
										//If the last call is from submitting survey items, then redirect to the profile
										location.href = "<?php echo base_url('webapp/survey/profile/'); ?>"+newSurveyId;
									}else{
										//If the last call is from submitting questions, redirect to risks
										location.href = "<?php echo base_url('webapp/survey/create/'); ?>"+newSurveyId+"/risks/";
									}									
								} ,3000);							
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}		
						}
					});
				}
			}).catch(swal.noop)
		});
		
	});
</script>