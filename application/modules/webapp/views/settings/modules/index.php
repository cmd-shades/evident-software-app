<style>
	.panel-heading.active {
		background-color: #ffd051 !important;
		border: 1px solid #ffd051 !important;
		color: #5c5c5c !important;
		font-weight:500;
	}
</style>

<div class="row">
	<div class="col-md-4 col-sm-4 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Your <?php !empty( $module_details->module_name ) ? $module_details->module_name : ''; ?> Dropdowns List</legend>
			<div class="accordion" id="accordion" role="tablist" aria-multiselectable="true">
				<?php if( !empty( $config_tables ) ){ $counter = 1; foreach( $config_tables as $k => $table_details ){ ?>
					<div class="panel panel-list has-shadow">
						<div class="list-container-bar panel-heading collapsed bg-blue pointer no-radius" role="tab" id="heading<?php echo $counter; ?>" data-module_id="<?php echo $table_details->module_id ?>"  data-table_name="<?php echo $table_details->table_name; ?>"  data-list_name="<?php echo $table_details->list_name ?>" data-order_column="<?php echo $table_details->order_column ?>"  data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $counter; ?>" aria-expanded="true" aria-controls="collapse<?php echo $counter; ?>">
							<h4 class="panel-title"><?php echo $table_details->list_name ?> <span class="pull-right"></span></h4>
						</div>
					</div>
				<?php $counter++; } } else { ?>
					<div>
						<p>There are currently no configurable options for this module.</p>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
	
	<?php if( !empty( $config_tables ) ){ ?>
		<div class="col-md-8 col-sm-8 col-xs-12">
			<div class="x_panel tile has-shadow">
				<legend>Manage Your <span id="list-header">List</span> <span class="pull-right"><span id="add-new-btn"></span></span></legend>
				<div class="panel">
					<div id="loading-indicator" style="display:none;" >Loading data, please wait... <div style="display:inline-block"><img src="<?php echo base_url( '/assets/images/ajax-loader.gif' ); ?>" /></div></div>
					<div id="show-existing-list" >To manage a dropdown/list, please select one from the Manage Your list section.</div>
				</div>
			</div>
		</div>
		
		<div id="edit-option-container" ></div>
		
	<?php } ?>
</div>

<script>
	$( document ).ready( function(){
		
		$( '#show-existing-list #edit-option-container' ).on( 'keypress change', '.required', function(){
			$( '#feedback-message' ).text( '' );
			$( this ).css("border","1px solid #ccc");
		} );
		
		$('.panel-list').on('click', function () {
			$('.panel-heading').removeClass('active');
			$( this ).find('.panel-heading').addClass('active');
		});
		
		$( '.list-container-bar' ).click( function(){
			$( this ).closest( 'div' ).find( '.caret-icon' ).toggleClass('fa-caret-down fa-caret-right');
			
			var moduleId  	= $( this ).data( 'module_id' );
			var listName  	= $( this ).data( 'list_name' );
			var tableName 	= $( this ).data( 'table_name' );
			var orderColumn = $( this ).data( 'order_column' );
			
			var loadView  = getListData( moduleId, tableName, listName, orderColumn );
			
			if( loadView ){
				
				$( '#list-header' ).text( listName );
				
			} else {
				swal({
					type: 'error',
					text: 'Oops! Sometihng went wrong, please reload this page.'
				});
			}			
		});
		
		function getListData( moduleId, tableName, listName, orderColumn ){
			$('#loading-indicator').show();
			$('#show-existing-list').hide();
			if( ( moduleId > 0 ) && ( tableName.length > 0 ) ){
				
				$.ajax({
					url:"<?php echo base_url('webapp/settings/get_list_data/' ); ?>" + moduleId,
					method:"post",
					data:{ module_id_id:moduleId, table_name:tableName, list_name:listName, order_column:orderColumn },
					dataType: 'json',
					success:function( data ){
						if( data.status == 1 ){
							$( '#show-existing-list' ).show().html( data.table_data );
							$( '#add-new-btn' ).html( data.add_new_link );						
						}else{
							// swal({
								// type: 'error',
								// title: data.status_msg
							// })
							$( '#show-existing-list' ).show().html( '<span>'+ data.status_msg +'</span><br/><br/>' );
							//$( '#add-new-btn' ).html( '<a href="#" class="add-new-option" data-table_name="'+ tableName + '" data-list_name="'+ listName +'" title="Add a new option to '+ listName +'"><i class="fas fa-plus text-grey"></i></a>' );
						}
						$( '#loading-indicator' ).hide();						
					}
				});
				
				return true;
			}
			return false
		}
		
		//Load the add modal
		$( '#add-new-btn' ).on( 'click', '.add-new-option', function(){
			var tableName = $( this ).data( 'table_name' );
			$( '.add-option-'+tableName ).modal( 'show' );
		});
		
		//Submit Add form for processing
		$( '#show-existing-list' ).on( 'click', '.add-option-btn', function(){

			var moduleId  	= $( this ).data( 'module_id' ),
				tableName 	= $( this ).data( 'table_name' ),
				listName  	= $( this ).data( 'list_name' ),
				orderColumn = $( this ).data( 'order_column' );
				
			var containerClass	= 'form-' + tableName;

			var inputs_state = validate_inputs( containerClass );
			//Check for required inputs
			if( inputs_state ){
				//If name attribute returned, auto focus to the field and display arror message
				$( '[name="'+inputs_state+'"]' ).focus().css("border","1px solid red");
				var labelText = $( '[name="'+inputs_state+'"]' ).parent().find('label').text();
					labelText = ( labelText !== "" && ( labelText.length > 0 ) ) ? labelText : $( '[name="'+inputs_state+'"]' ).data( 'label_text' )+' is a required';
					$( '#feedback-message' ).html( '<span class="text-red">'+ labelText +' is a required field!</span>');
				return false;
			}
			
			var formData			= $( '#add-option-form-' + tableName ).serialize();
			
			$.ajax({
				url:"<?php echo base_url('webapp/settings/add_new_option/' ); ?>",
				method:"post",
				data:formData,
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 ){
						
						getListData( moduleId, tableName, listName, orderColumn );
						$( '#loading-indicator .add-option-'+tableName ).modal( 'hide' );
						$( '.modal-backdrop' ).remove();

						swal({
							type : 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 3000
						});					
						
					}else{
						swal({
							type: 'error',
							title: data.status_msg
						})
					}		
				}
			});

		});
		
		//Submit Edit form for processing
		$( '#edit-option-container' ).on( 'click', '.edit-option-btn', function(){

			var moduleId  	= $( this ).data( 'module_id' ),
				tableName 	= $( this ).data( 'table_name' ),
				listName  	= $( this ).data( 'list_name' ),
				orderColumn = $( this ).data( 'order_column' );

			var containerClass	= 'form-' + tableName;
			
			var formData	  = $( '#edit-option-form-' + tableName ).serialize();
			
			$.ajax({
				url:"<?php echo base_url('webapp/settings/edit_option/' ); ?>",
				method:"post",
				data:formData,
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 ){
						
						getListData( moduleId, tableName, listName, orderColumn );
						$( '#edit-option-container .edit-option-'+tableName ).modal( 'hide' );
						$( '.modal-backdrop' ).remove();

						swal({
							type : 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 3000
						});					
						
					}else{
						swal({
							type: 'error',
							title: data.status_msg
						})
					}		
				}
			});

		});
		
		//Validate required input fields
		function validate_inputs( containerClass ){
			
			var result = false;
			var panel  = "." + containerClass;
			
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
		
		
		//Delete record
		$( '#show-existing-list' ).on( 'click', '.delete-record', function(){
			var recordId  	= $( this ).closest( 'td' ).data( 'record_id' ),
				moduleId  	= $( this ).closest( 'td' ).data( 'module_id' ),
				tableName 	= $( this ).closest( 'td' ).data( 'table_name' ),
				orderColumn = $( this ).closest( 'td' ).data( 'order_column' ),
				listName  	= $( this ).closest( 'td' ).data( 'list_name' );
				
				swal({
					title: 'Conirm Delete?',
					text: 'This is an irreversible action',
					showCancelButton: true,
					confirmButtonColor: '#5CB85C',
					cancelButtonColor: '#9D1919',
					confirmButtonText: 'Yes'
				}).then( function ( result ) {
					if ( result.value ) {
						$.ajax({
							url:"<?php echo base_url('webapp/settings/delete_option/' ); ?>",
							method:'post',
							data:{module_id:moduleId, table_name:tableName, record_id:recordId},
							dataType: 'json',
							success:function( data ){
								if( data.status == 1 ){
									swal({
										type: 'success',
										title: data.status_msg,
										showConfirmButton: false,
										timer: 3000
									})
									getListData( moduleId, tableName, listName, orderColumn );
								}else{
									swal({
										type: 'error',
										title: data.status_msg
									})
								}		
							}
						});
					}else{
						return false;
					}
				}).catch( swal.noop )			
			
		});

		//Edit record
		$( '#show-existing-list' ).on( 'click', '.edit-record', function(){
			var recordId  	= $( this ).closest( 'td' ).data( 'record_id' ),
				moduleId  	= $( this ).closest( 'td' ).data( 'module_id' ),
				tableName 	= $( this ).closest( 'td' ).data( 'table_name' ),
				orderColumn = $( this ).closest( 'td' ).data( 'order_column' ),
				listName  	= encodeURIComponent( $( this ).closest( 'td' ).data( 'list_name' ) );
				
				$.ajax({
					url:"<?php echo base_url('webapp/settings/get_option/' ); ?>",
					method:'post',
					data:{module_id:moduleId, table_name:tableName, list_name:listName , order_column: orderColumn, record_id:recordId},
					dataType: 'json',
					success:function( data ){
						if( data.status == 1 ){
							$( '#edit-option-container' ).html( data.result );
							$( '.edit-option-'+tableName ).modal( 'show' );
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
						}		
					}
				});
			
		});
	} );
</script>