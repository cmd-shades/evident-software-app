<style>
	#pleaseWaitDialog{
		margin-top:12%;
	}
</style>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<form id="update-schedules-form" class="form-horizontal">
			<input type="hidden" name="contract_id" value="<?php echo $contract_details->contract_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<input type="hidden"  name="page" value="schedules"/>
			<div class="x_panel tile has-shadow">
				<legend>Current Schedules <a disabled href="<?php echo base_url('webapp/job/new_schedule?contract_id='.$contract_details->contract_id); ?>" class="pull-right pointer"><i class="fas fa-plus text-green" title="Create building schedules for this Contract" ></i></a></legend>
				<div class="form-group" >
					<div class="drop-shaddow">
						<input type="text" id="search_term" class="black-bg form-control <?php echo $module_identier; ?>-search_input" value="" placeholder="Search schedules..." />
					</div>
				</div>
				<br/>
				<div class="x_panel drop-shaddow">
					<table class="sortable datatable table table-responsive" style="margin-bottom:0; width:100%">
						<thead>
							<tr>
								<th width="5%">ID</th>
								<th width="20%">Schedule Name</th>
								<th width="10%">Date Created</th>
								<th width="10%">Total Sites</th>
								<th width="10%">Frequency</th>
								<th width="10%">Jobs per Site</th>
								<th width="10%">Expiry Date</th>
								<th width="10%">Status</th>
								<th width="5%" class="text-center" ><span>Cloned?</span></th>
								<th width="10%"><span class="pull-right">Action</span></th>
							</tr>
						</thead>
						<tbody id="schedules-results" style="overflow-y:auto;" >
							
						</tbody>
					</table>
				</div>
			</div>
		</form>
	</div>
</div>

<div id="new-location-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title" id="myModalLabel">Add Contract Schedules</h4>				
			</div>
			<div class="modal-body">
				<div class="">
					
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){
		
		var search_str   = null;
		var start_index	 = 0;
		var where = { 	
			'contract_id': '<?php echo $contract_details->contract_id;?>'
		};
		
		load_data( search_str, where, start_index );
		
		$( '#schedules-results' ).on( 'click', '.unlink-item', function(){
			swal({
				type: 'info',
				text: 'Unlink Functionality coming soon'
			});
		});
		
		$( '#schedules-results' ).on( 'click', '.delete-item', function(){
			swal({
				type: 'warning',
				text: 'Delete Functionality coming soon'
			});
		});
		
		$('.contract-postcodes').focus(function(){
			$( '.location-container' ).slideDown( 'slow' );
		});
		
		$("#schedules-results").on( "click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find( 'a' ).data( 'ciPaginationPage' );
			load_data( search_str, where, start_index );
		});
		
		function load_data( search_str, where, start_index ){
			$.ajax({
				url:"<?php echo base_url('webapp/contract/schedules_lookup/'.$contract_details->contract_id); ?>",
				method:"POST",
				data:{ search_term:search_str, where:where, start_index:start_index },
				success:function(data){
					$( '#schedules-results' ).html( data );
				}
			});
		}
		
		$( '#search_term' ).keyup( function(){
			var search = encodeURIComponent( $(this).val() );
			if( search.length > 0 ){
				load_data( search , where );
			} else {
				load_data( search_str, where );
			}
		});
		
		
		//Clone Schedule
		$( "#schedules-results" ).on( "click", ".clone-schedule-btn", function( e ){
			
			e.preventDefault();
			
			var scheduleId = $( this ).data( 'schedule_id' );
			
			submitCloneScheduleForm(scheduleId);
			
		});
		
		
		function submitCloneScheduleForm( scheduleId, liMit = "<?php echo SCHEDULE_CLONE_DEFAULT_LIMIT; ?>", offSet = 0 ){

			if( !scheduleId ){
				swal({
					type: 'error',
					title: 'Invalid Schedule ID',
				})
			}

			if( offSet > 0 ){
				var confirmationMessage = 'Continue Schedule Cloning process?',
					warningMessage 		= '';
			} else {
				var confirmationMessage = 'Confirm Clone Schedule?',
				warningMessage 			= 'This will also generate new Activitites & Jobs';
			}

			swal({
				title: confirmationMessage,
				html: warningMessage,
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/clone_schedule'); ?>",
						method:"POST",
						data:{ page:'details', schedule_id:scheduleId, limit:liMit, offset: offSet },
						dataType: 'json',
						beforeSend: function(){
							showPleaseWait();
						},
						success:function(data){
							hidePleaseWait();
							if( data.status == 1 && ( data.schedule.schedule_id !== '' ) ){
								var newScheduleId 	= data.schedule.schedule_id,
									scheduleRef 	= data.schedule.schedule_ref,
									scheduleName 	= data.schedule.schedule_name,
									clonedSchID 	= data.schedule.cloned_schedule_id,
									contractID 		= data.schedule.contract_id,
									contractName 	= data.schedule.contract_name,
									frequencyID 	= data.schedule.frequency_id,
									totalActivities	= data.schedule.activities,
									totalSites 		= data.schedule.sites,
									totalAssets 	= data.schedule.assets,
									dataCounters 	= data.schedule.counters,
									liMit			= data.schedule.counters.limit,
									offSet			= ( Math.floor( ( dataCounters.processed_activities / dataCounters.expected_activities )*dataCounters.activity_pages ) ) * data.schedule.counters.limit;
								swal({
									type: 'success',
									showCancelButton: true,
									confirmButtonColor: '#5CB85C',
									cancelButtonColor: '#9D1919',
									confirmButtonText: 'Proceed',
									title: 'Cloning Process started!',
									html:
										'<p>Please check and confirm that the details below are correct, then click proceed to complete the cloning process.</p>' +
										'<table class="table table-responsive pull-left">' +
											'<tr>' +
												'<th>Schedule Name:</th><td>' + scheduleName+ '</td>' +
											'</tr>' +
											'<tr>' +
												'<th>Contract:</th><td>' + contractName+ '</td>' +
											'</tr>' +
											'<tr>' +
												'<th>Sites Processed:</th><td>' + dataCounters.processed_sites + ' of ' + dataCounters.expected_sites + '</td>' +
											'</tr>' +
											'<tr>' +
												'<th>Activities Processed:</th><td>' + dataCounters.processed_activities + ' of ' + dataCounters.expected_activities + '</td>' +
											'</tr>' +
											/*'<tr>' +
												'<td>Total Assets:</td><td>' + totalCustomers+ '</td>' +
											'</tr>' +*/
										'</table>'
								}).then( function (result) {
									if ( result.value ) {
										
										if( dataCounters.processed_activities === dataCounters.expected_activities ){
											//Do something here
											submitCloneJobsForm({
												page: 'details',
												schedule_id: newScheduleId,
												cloned_schedule_id: clonedSchID,
												contract_id: contractID,
												frequency_id: frequencyID,
											});	
										} else {
											submitCloneScheduleForm( scheduleId, liMit, offSet );
										}
							
									} else {
										//Do this if user cancels to change the name
									}
								});
								
							} else {
								swal({
									type: 'error',
									title: data.status_msg
								})
							}		
						}
					});
				}
			}).catch(swal.noop)
			
		}

		
		/*
		* Clone Schedule Jobs
		**/
		function submitCloneJobsForm( formData ){
		
			var formData = formData;

			if( ( formData.schedule_id.length > 0 ) && ( formData.cloned_schedule_id.length > 0 ) ){
				$.ajax({
					url:"<?php echo base_url('webapp/job/clone_jobs'); ?>",
					method:'POST',
					data:formData,
					dataType: 'json',
					beforeSend: function(){
						showPleaseWait();
					},
					success:function( data ){
						hidePleaseWait();
						if( data.status == 1 && ( data.jobs.schedule_id !== '' ) ){
							var newScheduleId = data.jobs.schedule_id;
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 3000
							})
							window.setTimeout(function(){
								location.reload();
							} ,2000);
							
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
						}
					}
				});
			
			} else {
				swal({
					type: 'error',
					title: 'Invalid Data'
				});
				return false;
			}
		}
		
	});
	
	
	/**
	 * Displays overlay with "Please wait" text. Based on bootstrap modal. Contains animated progress bar.
	 */
	function showPleaseWait() {
		
		if ( document.querySelector( "#pleaseWaitDialog") == null ) {
			var modalLoading = '<div class="modal" id="pleaseWaitDialog" data-backdrop="static" data-keyboard="false" role="dialog">\
				<div class="modal-dialog modal-vertical-centered">\
					<div class="modal-content">\
						<div class="modal-body" style="min-height: 40px;">\
							<h4 class="modal-title">Processing your request. Please wait...</h4>\
							<div class="progress">\
							  <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%; height: 40px"></div>\
							</div>\
							<p class="modal-title"><small>This may take several minutes...</small></p>\
						</div>\
					</div>\
				</div>\
			</div>';
			$(document.body).append(modalLoading);
		}
	  
		$( "#pleaseWaitDialog" ).modal( "show" );
	}

	/**
	 * Hides "Please wait" overlay. See function showPleaseWait().
	 */
	function hidePleaseWait() {
		$( "#pleaseWaitDialog" ).modal( "hide" );
		$( '.modal-backdrop' ).remove();
	}
	
</script>