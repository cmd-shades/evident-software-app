<div class="row">
	<?php if ($this->user->is_admin || !empty($permissions->can_add) || !empty($permissions->can_view) || !empty($permissions->is_admin)) { ?>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="accordion" id="accordionOne" role="tablist" aria-multiselectable="true">
				<div class="panel has-shadow">
					<div class="section-container-bar panel-heading collapsed bg-grey pointer no-radius" role="tab" id="headingOne" data-toggle="collapse" data-parent="#accordionOne" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
						<h4 class="panel-title"><i class="caret-icon fas fa-caret-down text-yellow"></i> LINKED PEOPLE (<?php echo !empty($linked_people) ? count($linked_people) : 0; ?>) <span class="pull-right pointer link-people"><i class="fas fa-plus" title="Link people to this Contract" ></i></span></h4>
					</div>
					<div id="collapseOne" class="panel-collapse no-bg collapsed no-background" role="tabpanel" aria-labelledby="headingOne" >
						<div class="panel-body no-background">
							<div class="table-responsive">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<table id="datatable" class="table table-responsive" style="margin-bottom:0px;width:100%" >
										<thead>
											<tr>
												<th width="40%">FULL NAME</th>
												<th width="20%">EMAIL</th>
												<th width="20%">CONTRACT LEADER </th>
												<th width="20%"><span class="pull-right" >ACTION</span></th>
											</tr>
										</thead>
										
										<tbody>
											<?php if (!empty($linked_people)) {
											    foreach ($linked_people as $linked_person) { ?>
												<tr>
													<td><a href="<?php echo base_url('webapp/user/profile/'.$linked_person->person_id); ?>" ><?php echo ucwords($linked_person->first_name); ?> <?php echo ucwords($linked_person->last_name); ?></a></td>
													<td><?php echo !empty($linked_person->email) ? $linked_person->email : ''; ?></span></td>
													<td><a href="<?php echo base_url('webapp/user/profile/'.$linked_person->contract_lead_id); ?>" ><?php echo ucwords($linked_person->contract_leader); ?></a></td>
													<td><span class="pull-right"><span class="unlink-people pointer" data-contract_id="<?php echo $linked_person->contract_id; ?>" data-person_id="<?php echo $linked_person->person_id; ?>" title="Click to unlink this person from this Contract" ><i class="far fa-trash-alt text-red"></i> Unlink</span></span></td>							
												</tr>
											<?php }
											    } else { ?>
												<tr>
													<td colspan="4"><?php echo $this->config->item('no_records'); ?></td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<br/>	
			</div>
		</div>
		
		<!-- Modal for linked people to this people? -->
		<div class="modal fade link-people-modal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span></button>
						<h4 class="modal-title" id="myPeopleModalLabel">Link people to Contract</h4>						
					</div>
					<div class="modal-body" id="people-modal-container" >
						<input type="hidden" name="page" value="details" />
						<input type="hidden" name="contract_id" value="<?php echo $contract_details->contract_id; ?>" />
						
						<div class="form-group">
							<?php if (!empty($this->user->is_admin)) { ?>
								<label class="strong">Contract Lead Person</label>
								<select id="contract_lead_id" name="contract_lead_id" class="form-control">
									<option value="" >Please select</option>
									<?php if (!empty($available_users)) {
									    foreach ($available_users as $k => $user) { ?>
										<option value="<?php echo $user->id; ?>" <?php echo ($user->id == $contract_details->contract_lead_id) ? 'selected=selected' : ''; ?> ><?php echo $user->first_name." ".$user->last_name; ?></option>
									<?php }
									    } ?>
								</select>	
							<?php } else { ?>
								<input type="hidden" name="contract_lead_id" value="<?php echo $contract_details->contract_lead_id; ?>" />
							<?php } ?>
						</div>
						
						<div class="form-group">
							<label class="strong">Search people</label>
							<select id="linked_people" name="linked_people[]" multiple="multiple" class="form-control" style="width:100%; display:none; margin-bottom:10px;" data-label_text="Linked people" >
								<option value="" disabled >Search people</option>
								<?php if (!empty($available_people)) {
								    foreach ($available_people as $k => $people) { ?>
									<?php if (!in_array($people->person_id, $linked_people_ids)) { ?>
										<option value="<?php echo $people->person_id; ?>" ><?php echo ucwords($people->first_name); ?> - <?php echo ucwords($people->last_name); ?></option>
									<?php } ?>
								<?php }
								    } ?>
							</select>
						</div>
					</div>
					
					<div class="modal-footer">
						<button id="link-people-btn" class="btn btn-success btn-sm">Link Selected people</button>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
</div>

<script>

	//link_people
	$( document ).ready(function(){
		
		
		$( '.section-container-bar' ).click( function(){
			$( this ).closest( 'div' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');			
		});
		
		$( '.link-people' ).click( function(){
			$( ".link-people-modal" ).modal( "show" );			
		} );
		
		$( '#linked_people' ).select2({
			allowClear: true,
			minimumResultsForSearch: -1,
		});
		
		
		$( '#link-people-btn' ).click( function(){
			
			var leadPerson 	= $( '#contract_lead_id option:selected' ).val();
			var leadPerson2 = $( '[name="contract_lead_id"]' ).val();
			
			if( leadPerson ){
				if( leadPerson.length == 0 || leadPerson === undefined ){
					swal({
						type: 'error',
						text: 'Please select the Contract Leader',
					});
					return false;
				}
			} else {
				if( leadPerson2.length == 0 || leadPerson2 === undefined ){
					swal({
						type: 'error',
						text: 'Please select the Contract Leader',
					});
					return false;
				}
			}
			
			var formData = $( "#people-modal-container :input").serialize();
			$.ajax({
				url:"<?php echo base_url('webapp/contract/link_people/'); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						
						$( '.link-people-modal' ).modal( 'hide' );
						$( '.modal-backdrop' ).remove();
						
						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 2000
						})
						
						window.setTimeout(function(){ 
							location.reload();
						} ,1000);
					} else {
						swal({
							type: 'error',
							title: data.status_msg
						})
					}		
				}
			});
			return false;
		});
		
		
		//Unlink 
		$( '.unlink-people' ).click( function(){
			
			var contractId  = $( this ).data( 'contract_id' );
			var peopleId  	= $( this ).data( 'person_id' );
			if( peopleId == 0 || peopleId == undefined ){
				swal({
					title: 'Oops! Something went wrong',
					type: 'error',
					text: 'Please reload the page and try again!',
				})
			}
			swal({
				title: 'Confirm unlink Person?',
				type: 'warning',				
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
			
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/contract/unlink_people/'); ?>" + peopleId,
						method:"POST",
						data:{ page:"details", contract_id:contractId, person_id:peopleId },
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2100
								})
								window.setTimeout( function(){
									var new_url = window.location.href.split('?')[0];
									window.location.href = new_url;
								} ,1000 );
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						}
					});
				}
			}).catch( swal.noop )
		} );
		
	});
</script>