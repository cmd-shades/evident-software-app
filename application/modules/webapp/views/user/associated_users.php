<div class="row">
	<?php if( $this->user->is_admin || !empty( $permissions->can_add ) || !empty( $permissions->can_view ) || !empty( $permissions->is_admin ) ){ ?>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="accordion" id="accordionOne" role="tablist" aria-multiselectable="true">
				<div class="panel has-shadow">
					<div class="section-container-bar panel-heading collapsed bg-grey pointer no-radius" role="tab" id="headingOne" data-toggle="collapse" data-parent="#accordionOne" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
						<h4 class="panel-title"><i class="caret-icon fas fa-caret-down text-yellow"></i> ASSOCIATED USERS (<?php echo !empty( $associated_users ) ? count( $associated_users ) : 0; ?>) <span class="pull-right pointer associate-users"><i class="fas fa-plus" title="Associate Users to this record" ></i></span></h4>
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
												<th width="20%">PRIMARY USER</th>
												<th width="20%"><span class="pull-right" >ACTION</span></th>
											</tr>
										</thead>
										
										<tbody>
											<?php if( !empty( $associated_users ) ){ foreach( $associated_users as $associated_user ) { ?>
												<tr>
													<td><a href="<?php echo base_url( 'webapp/user/profile/'.$associated_user->user_id ); ?>" ><?php echo ucwords( $associated_user->first_name ); ?> <?php echo ucwords( $associated_user->last_name ); ?></a></td>
													<td><?php echo !empty( $associated_user->email ) ? $associated_user->email : ''; ?></span></td>
													<td><a href="<?php echo base_url( 'webapp/user/profile/'.$associated_user->primary_user_id ); ?>" ><?php echo !empty( $associated_user->primary_user ) ? ucwords( $associated_user->primary_user ) : ''; ?></a></td>
													<td><span class="pull-right"><span class="disassociate-users pointer" data-primary_user_id="<?php echo $associated_user->primary_user_id; ?>" data-user_id="<?php echo $associated_user->user_id; ?>" title="Click to un-associate this user from this person" ><i class="far fa-trash-alt text-red"></i> Unlink</span></span></td>							
												</tr>
											<?php } }else{ ?>
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
		
		<!-- Modal Associating Users? -->
		<div class="modal fade associate-users-modal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span></button>
						<h4 class="modal-title" id="myPeopleModalLabel">Associate Users</h4>						
					</div>
					<div class="modal-body" id="users-modal-container" >
						<input type="hidden" name="page" value="details" />
						<input type="hidden" name="primary_user_id" value="<?php echo $user_details->id; ?>" />
						
						<div class="form-group hide">
							<?php if( !empty( $this->user->is_admin ) ){ ?>
								<label class="strong">Primary User</label>
								<select id="primary_user_id" name="primary_user_id" class="form-control">
									<option value="" >Please select</option>
									<?php if( !empty( $users ) ) { foreach( $users as $k => $user ) { ?>
										<option value="<?php echo $user->id; ?>" <?php echo ( $user->id == $user_details->id ) ? 'selected=selected' : ''; ?> ><?php echo $user->first_name." ".$user->last_name; ?></option>
									<?php } } ?>
								</select>	
							<?php } else { ?>
								<input type="hidden" name="primary_user_id" value="<?php echo $user_details->id; ?>" />
							<?php } ?>
						</div>
						
						<div class="form-group">
							<label class="strong">Search users</label>
							<select id="associated_users" name="associated_users[]" multiple="multiple" class="form-control" style="width:100%; display:none; margin-bottom:10px;" data-label_text="Associated Users" >
								<option value="" disabled >Search users</option>
								<?php if( !empty( $available_users ) ) { foreach( $available_users as $k => $available_user ) { ?>
									<?php if( !in_array( $available_user->id, $associated_users_ids ) ){ ?>
										<option value="<?php echo $available_user->id; ?>" ><?php echo ucwords( $available_user->first_name ); ?> - <?php echo ucwords( $available_user->last_name ); ?></option>
									<?php } ?>
								<?php } } ?>
							</select>
						</div>
					</div>
					
					<div class="modal-footer">
						<button id="associate-users-btn" class="btn btn-success btn-sm">Associate Selected users</button>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
</div>

<script>

	//Associate Users
	$( document ).ready(function(){
		
		
		$( '.section-container-bar' ).click( function(){
			$( this ).closest( 'div' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');			
		});
		
		$( '.associate-users' ).click( function(){
			$( ".associate-users-modal" ).modal( "show" );			
		} );
		
		$( '#associated_users' ).select2({
			allowClear: true,
			minimumResultsForSearch: -1,
		});
		
		
		$( '#associate-users-btn' ).click( function(){
			
			/* var leadPerson 	= $( '#primary_user_id option:selected' ).val();
			var leadPerson2 = $( '[name="primary_user_id"]' ).val();
			
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
			} */
			
			var formData = $( "#users-modal-container :input").serialize();
			$.ajax({
				url:"<?php echo base_url('webapp/user/associate_users/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						
						$( '.associate-users-modal' ).modal( 'hide' );
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
		$( '.disassociate-users' ).click( function(){
			
			var primaryUserId  = $( this ).data( 'primary_user_id' );
			var userId  	= $( this ).data( 'user_id' );
			if( userId == 0 || userId == undefined ){
				swal({
					title: 'Oops! Something went wrong',
					type: 'error',
					text: 'Please reload the page and try again!',
				})
			}
			swal({
				title: 'Confirm Disassociate Users?',
				type: 'warning',				
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
			
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/user/disassociate_users/' ); ?>" + userId,
						method:"POST",
						data:{ page:"details", primary_user_id:primaryUserId, user_id:userId },
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