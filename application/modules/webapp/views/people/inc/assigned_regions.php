<!-- Modal for Assigned Regions -->
<div class="modal fade assign-regions-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title" id="myRegionModalLabel">Assign Regions</h4>						
			</div>
			<div class="modal-body" id="assign-regions-modal-container" >
				<input type="hidden" name="page" value="details" />
				<input type="hidden" name="person_id" value="<?php echo $person_details->person_id; ?>" />
				<label class="strong">Available Regions</label>
				<div class="form-group">
					<select id="assigned_regions" name="assigned_regions[]" multiple="multiple" class="form-control" style="width:100%; display:none; margin-bottom:10px;" data-label_text="Associated Risks" >
						<option value="" disabled >Search / Select Skills list</option>
						<?php if( !empty( $available_regions ) ) { foreach( $available_regions as $k => $region ) { ?>
							<?php if( !in_array( $region->region_id, $linked_regions ) ){ ?>
								<option value="<?php echo $region->region_id; ?>" ><?php echo ucwords( $region->region_name ); ?> | <?php echo $region->region_postcodes; ?></option>
							<?php } ?>
						<?php } } ?>
					</select>
				</div>
			</div>
			<div class="modal-footer">
				<button id="assign-regions-btn" class="btn btn-success btn-sm">Assign Selected Regions</button>
			</div>
		</div>
	</div>
</div>


<script>
	$( document ).ready(function(){
		
		$( '#assigned_regions' ).select2({
			allowClear: true,
			minimumResultsForSearch: -1,
		});
		
		$( '.assign-regions' ).click( function(){
			$(".assign-regions-modal").modal( "show" );
		} );

		$( '#assign-regions-btn' ).click( function(){
			var formData = $( '#assign-regions-modal-container :input' ).serialize();
			$.ajax({
				url:"<?php echo base_url('webapp/people/assign_regions/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						
						$( '.assign-regions-modal' ).modal( 'hide' );
						$( '.modal-backdrop' ).remove();
						
						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 3000
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
		
		
		//Un-assign region from a person
		$( '.unassign-region' ).click( function(){
			
			var personId  	= $( this ).data( 'person_id' );
			var regionId  	= $( this ).data( 'region_id' );
			var	sectionName	= 'not-set';
			if( regionId == 0 || regionId == undefined ){
				swal({
					title: 'Oops! Something went wrong',
					type: 'error',
					text: 'Please reload the page and try again!',
				})
			}
			swal({
				title: 'Confirm unassign Region?',
				type: 'warning',
				text: 'This is an irreversible action',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
			
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/people/unassign_region/' ); ?>" + regionId,
						method:"POST",
						data:{ page:"details", person_id:personId, region_id:regionId },
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2100
								})
								window.setTimeout(function(){
									var new_url = window.location.href.split('?')[0];
									window.location.href = new_url + "?toggled=" + sectionName;
								} ,1000);
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

