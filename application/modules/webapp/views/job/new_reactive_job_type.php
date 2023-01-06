<style>
	.panel-body{
		background-color:#F7F7F7; 
		height:140px; 
		min-height:140px;
	}
</style>


<div>
	<br/>
	<div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
		<!-- <span><small id="feedback-message" class="text-red">Error!</small></span> -->
		<div class="x_panel tile has-shadow">
			<legend class="evidocs-legend text-bold">NEW REACTIVE JOB TYPE</legend>
			<form id="reactive-job-type-creation-form" >
				<input type="hidden" name="override_existing" value="" />
				<input type="hidden" name="page" value="details" />
				<input type="hidden" name="job_type_id" value="" />
				<input type="hidden" name="is_reactive" value="1" />
				<input type="hidden" name="notify_engineer" value="1" />
				<input type="hidden" name="job_type_subtype" value="Service Call" />

				<div class="input-group form-group">
					<label class="input-group-addon">Discipline</label>
					<select id="reactive_discipline_id" name="discipline_id" class="form-control required" data-label_text="Discipline" >
						<option value="" >Please Select</option>
						<?php if( !empty( $disciplines ) ) { foreach( $disciplines as $disp_key => $discipline ) { ?>
							<option value="<?php echo $discipline->discipline_id; ?>" data-discipline_name="<?php echo $discipline->account_discipline_name; ?>" ><?php echo $discipline->account_discipline_name; ?></option>
						<?php } } ?>
					</select>
				</div>

				<div class="input-group form-group">
					<label class="input-group-addon">Job Type</label>
					<input id="reactive_job_type" name="job_type" class="form-control" readonly type="text" placeholder="Job Type" value="" />
				</div>
				
				<div class="input-group form-group">
					<label class="input-group-addon">Description</label>
					<textarea id="reactive_job_type_desc" name="job_type_desc" class="form-control" type="text" data-label_text="Job Type Description" placeholder="Give a detailed description of what this Job Type does..." ></textarea>
				</div>	
				<hr>
				<?php if( $this->user->is_admin || !empty( $permissions->can_add ) || !empty( $permissions->is_admin ) ){ ?>
					<div class="row">
						<div class="col-md-4">
							<button id="create-reactive-job-type-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="button" >Create Reactive Job Type</button>
						</div>
					</div>
				<?php }else{ ?>
					<div class="row col-md-4">
						<button id="reactive-no-permissions" class="btn btn-sm btn-block btn-flow btn-warning btn-next no-permissions" type="button" disabled >Insufficient permissions</button>
					</div>
				<?php } ?>
				
			</form>
		</div>
	</div>
</div>

<script>

	$( document ).ready( function() {
		
		//Discipline Selection
		$( '#reactive_discipline_id' ).change( function(){
			
			var discpId 	= $( 'option:selected', this ).val();
			var discpName 	= $( 'option:selected', this ).data( 'discipline_name' );

			if( discpId.length > 0 ){
				//$( '#reactive_job_type' ).val( 'Reactive Maintenance - '+discpName );
				//$( '#reactive_job_type_desc' ).val( 'Use for Reactive Maintenance '+discpName+' related jobs' );
				$( '#reactive-job-type-creation-form [name="job_type"]' ).val( 'Reactive Maintenance - '+discpName );
				$( '#reactive-job-type-creation-form [name="job_type_desc"]' ).val( 'Use for Reactive Maintenance '+discpName+' related jobs' );
			} else {
				$( '[name="job_type"]' ).val( '' );
				$( '[name="job_type_desc"]' ).val( '' );
			}

		});
		
		//Submit Evidoc form
		$( '#create-reactive-job-type-btn' ).click(function( e ){
		
			e.preventDefault();
			
			submitJobTypeForm();
			
		});
		
		function submitJobTypeForm(){
			
			var formData = $('#reactive-job-type-creation-form').serialize();
			
			swal({
				title: 'Confirm New Reactive Job Type?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function ( result ) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/create_job_type/' ); ?>",
						method:'POST',
						data:formData,
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 && ( data.job_type !== '' ) ){
								console.log(data);
								var alreadyExists= data.already_exists;
								var newJobTypeId = data.job_type.job_type_id;
								console.log(alreadyExists);
								if( alreadyExists ){
									var existUrl = "<?php echo base_url('webapp/job/job_types/' ); ?>"+data.job_type.job_type_id;
									swal({
										type: 'warning',
										showCancelButton: true,
										confirmButtonColor: '#5CB85C',
										cancelButtonColor: '#9D1919',
										confirmButtonText: 'Override',
										title: 'This Job Type already exists!',
										html:
											'<b>Job Type: </b>' + ucwords( data.job_type.job_type ) + '<br/>' +
											//'<b>Category: </b>' + ucwords( data.job_type.category_name ) + '<br/>' +
											'<b>Description: </b><br/>' +
											'<em>' + data.job_type.job_type_desc + '</em>' + '<br/><br/>' +
											'Click <a href="'+existUrl+'" target="_blank">here</a> to view it or Cancel to go back and change name'
									}).then( function (result) {
										if ( result.value ) {
											//Do this if user accepts to Override
											$( '[name="job_type_id"]' ).val( data.job_type.job_type_id );
											$( '[name="override_existing"]' ).val( 1 );
											$( '[name="job_type_desc"]' ).val( data.job_type.job_type_desc );	
											
											//Do something here
											submitJobTypeForm();											
										}else{
											//Do this if user cancels to change the name
										}
									})
									
								} else {
									swal({
										type: 'success',
										title: data.status_msg,
										showConfirmButton: false,
										timer: 2000
									})
									window.setTimeout(function(){ 
										location.href = "<?php echo base_url('webapp/job/job_types/'); ?>"+newJobTypeId;
									} ,1000);
								}	
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}		
						}
					});
				}else{
					$( ".asset_creation_panel8" ).hide( "slide", { direction : 'left' }, 500 );
					go_back( ".asset_creation_panel2" );
					return false;
				}
			}).catch( swal.noop )
		}

	});
</script>