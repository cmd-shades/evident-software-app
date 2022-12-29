<div>
	<form class="doc-categories-from">
		<input type="text" name="category_id" value="" />
		<input type="hidden" name="doc_type_id"  value="" />
		<div class="col-md-4 col-md-offset-4 col-sm-12 col-xs-12">
			<div class="record_creation_panel1" class="col-md-12 col-sm-12 col-xs-12" style="display:block">
				<legend>What you like to submit?</legend>
				<div class="row">
					<?php if (!empty($categories)) {
					    foreach ($categories as $k => $category) {
					        $k++; ?>
						<div class="col-md-6 col-sm-6 col-xs-12 pointer btn-next record-creation-steps category-selector" data-currentpanel="record_creation_panel1" data-category_id="<?php echo $category->category_id; ?>" >
							<div class="x_panel tile has-shadow">
								<h3 style="margin-bottom:2px;" id="submit-survey-btn<?php echo $k; ?>" ><?php echo $category->category_name_alt;?></h3>
								<small><?php echo $category->description;?></small>
							</div>
						</div>
					<?php }
					    } ?>
				</div>			
			</div>
		
			<div class="record_creation_panel2" class="col-md-12 col-sm-12 col-xs-12" style="display:none">
				<legend>Please select the Doc type you require</legend>
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div id="docTypesResults"></div>
					</div>
					<div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-info btn-back" data-currentpanel="record_creation_panel2" type="button" >Categories</button>					
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-success btn-next record-creation-steps" type="button" data-section_tag="doc_types" >Request Data</button>					
						</div>
					</div>				
				</div>
			</div>
		</div>
	</form>
</div>

<script>
	$( document ).ready(function(){
		
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
		
		$( '.category-selector' ).click( function() {
			var categoryId = $( this ).data( 'category_id' );
			$( '[name="category_id"]' ).val( categoryId );
			$.ajax({
				url:"<?php echo base_url('webapp/audit/start/'); ?>",
				method:"POST",
				data:{ "category_id" : categoryId },
				success:function( data ){
					if( data.status == 1 ){
						$("#docTypesResults").html( data.doc_types );
					}else{
						swal({
							type: "error",
							title: data.status_msg
						})
						return false;
					}			
				}
			});
		});
	
		$(".record-creation-steps").click(function(){
				
			var sectionTag 	= $( this ).data( 'section_tag' );
				sectionTag  = '.sectGrp'+sectionTag;
		
			var formData 	= $( sectionTag+' :input' ).serialize();
			
			submitForm( formData );

			var currentpanel = $( this ).data("currentpanel");
			panelchange("."+currentpanel);
			return false;
		});	
		
		$(".btn-back").click(function(){
			var currentpanel = $(this).data("currentpanel");
			go_back( "."+currentpanel )	
			return false;
		});	
		
		function panelchange(changefrom){
			var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
			var changeto = ".record_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", {direction : 'left'}, 500);
			$( changeto ).delay(600).show( "slide", {direction : 'right'},500);	
			return false;	
		}
		
		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
			var changeto = ".record_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", {direction : 'right'}, 500);
			$( changeto ).delay(600).show( "slide", { direction : 'left' },500);	
			return false;	
		}

		//Update permissions
		function submitForm( formData ){
			return true;
			//submit only the changed items
			/*$.ajax({
				url:"<?php echo base_url('webapp/survey/create_survey/'); ?>",
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
			});*/
			
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
						url:"<?php echo base_url('webapp/survey/create_survey/'); ?>",
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