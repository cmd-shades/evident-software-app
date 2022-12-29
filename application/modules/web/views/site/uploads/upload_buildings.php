<div class="row">
	<div class="col-md-6 col-sm-6 col-xs-12 pull-left">
		<legend>Upload Building Upload Records (csv) <span class="pointer">&nbsp;<small><a id="download_template" href="<?php echo base_url('assets/public/csv-templates/BuildingUploadsTemplate.csv'); ?>" target="_blank" download="<?php echo base_url('assets/public/csv-templates/BuildingUploadsTemplate.csv'); ?>"><i class="fas fa-download" title="Click to Download Building uploads template"></i></a></small></span></legend>
		<form id="docs-upload-form" action="<?php echo base_url('webapp/site/submit_upload_buildings_file/'.$this->user->account_id); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
			<input type="hidden" name="page" value="documents" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<div class="x_panel tile has-shadow">
				<legend class="legend-header">Please upload your updated file</legend>
				<div class="form-group" >
					<div class="input-group form-group">
						<label class="input-group-addon">Choose file</label>
						<span class="control-fileupload pointer">
							<label for="file1" class="pointer text-left">Please choose a file on your computer.</label><input id="uploadfile" name="upload_file[]" type="file" id="uploadfile">
						</span>
					</div>
				</div>
				<div class="form-group" >
					<div class="row">
						<div class="col-md-6">
							<button id="doc-upload-btn" class="btn btn-sm btn-block btn-success" type="submit">Upload Buildings</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
	$( document ).ready(function(){

		$( '#docs-upload-form' ).submit( function(){

			var files = $( '#uploadfile' ).val();

			if( files.length == 0 ){
				swal({
					type: 'error',
					title: '<small>No files selected for upload!</small>'
				});
				return false;
			}

			var selection = document.getElementById( 'uploadfile' );
			for ( var i=0; i < selection.files.length; i++) {
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

			$('#doc-upload-btn').attr( 'disabled', 'disabled' );
		});
		

		$(".buildings-creation-steps").click(function(){

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
			
			if( $( "#user-lookup-result" ).val() != "" ){
				var first_name = $( "#user-lookup-result" ).find( 'option:selected' ).data( "first_name" );
				$( '*[name="first_name"]' ).val( first_name );
				
				var last_name = $( "#user-lookup-result" ).find( 'option:selected' ).data( "last_name" );
				$( '*[name="last_name"]' ).val( last_name );
				
				var preferred_name = $( "#user-lookup-result" ).find( 'option:selected' ).data( "preferred_name" );
				$( '*[name="preferred_name"]' ).val( first_name );
				
				var buildingsal_email = $( "#user-lookup-result" ).find( 'option:selected' ).data( "buildingsal_email" );
				$( '*[name="buildingsal_email"]' ).val( buildingsal_email );		
				
				var user_type_id = $( "#user-lookup-result" ).find( 'option:selected' ).data( "user_type_id" );
				$( '*[name="user_type_id"]' ).val( user_type_id );
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
			var changeto = ".buildings_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'left'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'right'},500);
			return false;
		}

		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
			var changeto = ".buildings_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'right'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'left'},500);
			return false;
		}

	});
</script>