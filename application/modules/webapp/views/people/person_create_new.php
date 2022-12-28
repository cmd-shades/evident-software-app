<div class="row">
	<div class="col-md-6 col-sm-6 col-xs-12 pull-left">
		<legend>Upload Multiple Records (csv) <span class="pointer">&nbsp;<small><a id="download_template" href="<?php echo base_url( 'assets/public/csv-templates/PeopleImportTemplate.csv' ); ?>" target="_blank" download="<?php echo base_url( 'assets/public/csv-templates/PeopleImportTemplate.csv' ); ?>"><i class="fas fa-download" title="Click to Download upload template"></i></a></small></span></legend>
		<form id="docs-upload-form" action="<?php echo base_url( 'webapp/people/upload_people/'.$this->user->account_id ); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
			<input type="hidden" name="page" value="documents" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<div class="x_panel tile has-shadow">
				<legend class="legend-header">Please upload your updated file</legend>
				<div class="input-group form-group">
					<label class="input-group-addon">Choose file</label>
					<span class="control-fileupload pointer">
						<label for="file1" class="pointer text-left">Please choose a file on your computer.</label><input id="uploadfile" name="upload_file[]" type="file" id="uploadfile">
					</span>
				</div>
				<br/>
				<br/>
				<br/>
				<div class="row">
					<div class="col-md-6">
						<button id="doc-upload-btn" class="btn btn-sm btn-block btn-success" type="submit">Upload Document</button>
					</div>
				</div>
			</div>
		</form>
	</div>

	<div class="col-md-6 col-sm-6 col-xs-12 pull-right">
		<legend>Add Single Person</legend>
		<form id="person-creation-form" method="post">
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<input type="hidden"  name="page" value="details"/>
			<div class="row">
				<div class="person_creation_panel1 col-md-12 col-sm-12 col-xs-12">
					<div class="x_panel tile has-shadow">
						<div class="row section-header">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<legend class="legend-header">Does this person exist as a user?</legend>
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="person_creation_panel1-errors"></h6>
							</div>
						</div>

						<div class="form-group top_search">
							<div class="input-group">
								<input type="text" id="user-search" class="form-control user-lookup <?php echo $module_identier; ?>-search_input"  placeholder="Search by name or email user...">
								<span class="input-group-btn"><button id="find-user" class="btn btn-default <?php echo $module_identier; ?>-bg" type="button">Find user</button></span>
							</div>
						</div>
						<select id="user-lookup-result" name="user_id" class="form-control">
							<option value="">No, create as new (also create a system user)</option>
						</select>
						<br/>
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12 pull-right">
								<button id="person_creation_panel1" class="btn btn-block btn-flow btn-success btn-next person-creation-steps" data-currentpanel="person_creation_panel1" type="button">Next</button>
							</div>
						</div>
					</div>
				</div>

				<div class="person_creation_panel2 col-md-12 col-sm-12 col-xs-12" style="display:none">
					<div class="x_panel tile has-shadow">
						<div class="row section-header">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<legend class="legend-header">Please, provide person details:</legend>
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="person_creation_panel2-errors"></h6>
							</div>
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">User type</label>
							<select id="user_type_id" name="user_type_id" class="form-control">
								<option>Please select</option>
								<?php if( !empty( $user_types ) ) { foreach( $user_types as $k => $user_type ){ ?>
									<?php if( ( strtolower( $user_type->user_group ) == "admin" ) && ( !$this->user->is_admin ) ){ ?>
										
									<?php } else { ?>
										<option value="<?php echo $user_type->user_type_id; ?>"><?php echo $user_type->user_type_name; ?></option>
									<?php } ?>
								<?php } } ?>
							</select>
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Person's Category</label>
							<select name="category_id" class="form-control">
								<option>Please select</option>
								<?php if( !empty( $people_categories ) ) { foreach( $people_categories as $k => $row ) { ?>
									<option value="<?php echo $row->category_id; ?>"><?php echo $row->category_name_alt; ?></option>
								<?php } } ?>
							</select>
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">First name</label>
							<input name="first_name" class="form-control" type="text" placeholder="First name" value="" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Last name</label>
							<input name="last_name" class="form-control" type="text" placeholder="Last name" value="" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Preferred name</label>
							<input name="preferred_name" class="form-control" type="text" placeholder="Preferred name" value="" />
						</div>
						
						<div class="input-group form-group">
							<label class="input-group-addon">Email</label>
							<input name="personal_email" class="form-control" type="email" placeholder="Email" value="" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Mobile Number</label>
							<input name="mobile_number" class="form-control" type="text" placeholder="Mobile number" value="" />
						</div>
					
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="person_creation_panel2" type="button">Back</button>
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button id="create-person-btn" class="btn btn-block btn-flow btn-success btn-next" type="button">Create Person</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>

</div>

<script>
	$(document).ready(function(){

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

			$('#doc-upload-btn').attr( 'disabled', 'disabled' );
		});

		//Trigger user search on btn click
		$( '#find-user' ).click( function(){
			var personDetail = encodeURIComponent( $( '#user-search' ).val() );
			var accountId 	 = "<?php echo $this->user->account_id; ?>";
			if( personDetail.length > 0 ){
				$.post("<?php echo base_url( 'webapp/people/search_for_user' ); ?>",{account_id:accountId, userdata:personDetail},function(result){
					$("#user-lookup-result").html(result["users_list"]);
				},"json");
			}
		});

		//Trigger user search on pressing-enter key
		$( '#user-search' ).keypress( function( e ){
			if( e.which == 13 ){
				$('#find-user').click();
			}
		});

		$(".person-creation-steps").click(function(){

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
				
				var personal_email = $( "#user-lookup-result" ).find( 'option:selected' ).data( "personal_email" );
				$( '*[name="personal_email"]' ).val( personal_email );		
				
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
			var changeto = ".person_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'left'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'right'},500);
			return false;
		}

		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
			var changeto = ".person_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'right'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'left'},500);
			return false;
		}

		//Submit person form
		$( '#create-person-btn' ).click(function( e ){
			e.preventDefault();

			var formData = $( '#person-creation-form' ).serialize();

			swal({
				title: 'Confirm new person creation?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/people/create_person/' ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 && ( data.person !== '' ) ){

								var newPersonID = data.person.person_id;

								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout( function(){
									location.href = "<?php echo base_url('webapp/people/profile/'); ?>" + newPersonID;
								} ,3000);
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						}
					});
				} else {
					$( ".person_creation_panel2" ).hide( "slide", { direction : 'left' }, 500 );
					go_back( ".person_creation_panel2" );
					return false;
				}
			}).catch( swal.noop )
		});
	});
</script>