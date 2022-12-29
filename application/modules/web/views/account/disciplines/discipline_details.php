<style>
	div.panel-min-height{
		height:452px;
		min-height:452px;
	}
	
	label {
    line-height: 24px;
    /* color: #999999;
	}
</style>

<div class="row">
	<div class="col-md-4 col-sm-4 col-xs-12">
		<div class="x_panel tile has-shadow">
			<form id="update-job-form" class="form-horizontal">
				<input type="hidden" name="page" value="details" />
				<input type="hidden" name="discipline_id" value="<?php echo $discipline_details->discipline_id; ?>" />
				<legend>Discipline Details</legend>
				<div class="input-group form-group">
					<label class="input-group-addon">Discipline Ref</label>
					<input id="discipline_ref" name="discipline_ref" class="form-control" type="text" placeholder="Discipline Ref" readonly value="<?php echo $discipline_details->discipline_ref; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Discipline Name</label>
					<input id="discipline_name" name="discipline_name" class="form-control" type="text" placeholder="Discipline Name" value="<?php echo $discipline_details->discipline_name; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Discipline Desc</label>
					<input id="discipline_name" name="discipline_name" class="form-control" type="text" placeholder="Discipline Desc" value="<?php echo $discipline_details->discipline_desc; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Discipline Colour</label>
					<input id="discipline_category" name="discipline_colour" class="form-control" type="text" placeholder="Discipline Colour" value="<?php echo $discipline_details->discipline_colour; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Colour (Hex)</label>
					<input id="discipline_category" name="discipline_colour_hex" class="form-control" type="text" placeholder="Discipline Colour (Hex)" value="<?php echo $discipline_details->discipline_colour_hex; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Status Icon</label>
					<input id="discipline_category" name="discipline_icon" class="form-control" type="text" placeholder="Discipline Icon" value="<?php echo $discipline_details->discipline_icon; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Image Url</label>
					<input id="discipline_image_url" name="discipline_image_url" class="form-control" type="text" placeholder="Image Url" value="<?php echo $discipline_details->discipline_image_url; ?>" />
				</div>
				<div class="row" >
					<?php if ($this->user->is_admin || !empty($permissions->can_delete) || !empty($permissions->is_admin)) { ?>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button type="button" class="btn-block update-stock-discipline-btn btn btn-sm btn-success" >Update Discipline</button>
						</div>
					<?php } ?>

					<?php if ($this->user->is_admin || !empty($permissions->can_delete) || !empty($permissions->is_admin)) { ?>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-sm btn-danger has-shadow delete-stock-discipline-btn" type="button" data-discipline_id="<?php echo $discipline_details->discipline_id; ?>">Delete Discipline</button>
						</div>
					<?php } ?>
					
				</div>
			</form>
		</div>
	</div>

<script>
	$( document ).ready( function(){
		
		$('input.adjustment-action').on('change', function() {
			$('input.adjustment-action').not(this).prop('checked', false);  
		});
		
		$( '#adjust-stock-trigger' ).click( function(){
			$( "#adjust-stock-quantities-modal" ).modal( "show" );
		} );
		
		$( '#transfer-stock-trigger' ).click( function(){
			$( "#transfer-stock-modal-md" ).modal( "show" );
		} );

		$( '.update-stock-discipline-btn' ).click( function( event ){

			var formID = $( this ).closest( 'form' ).attr( 'id' );
			event.preventDefault();
			var formData = $('#'+formID ).serialize();
			swal({
				title: 'Confirm Discipline update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/account/update_discipline/'.$discipline_details->discipline_id); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2000
								})
								window.setTimeout(function(){
									location.reload();
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
			}).catch(swal.noop)
		});


		//Delete Discipline from
		$('.delete-stock-discipline-btn').click(function(){

			var disciplineId = $(this).data( 'discipline_id' );
			swal({
				title: 'Confirm delete Discipline?',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/stock/delete_discipline/'.$discipline_details->discipline_id); ?>",
						method:"POST",
						data:{'page':'details', discipline_id:disciplineId},
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 1500
								})
								window.setTimeout(function(){
									window.location.href = "<?php echo base_url('webapp/job/stock'); ?>";
								} ,1500);
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
		});
		
		
		$( '#adjust-stock-quantities-btn' ).click( function( event ){
			
			var stockLocationId = $( '#stock_location_id option:selected' ).val(),
				availableQty 	= $( '#stock_location_id option:selected' ).data( 'available_qty' ),
				adjustmentQty	= $( '#adjustment_qty' ).val();
				adjustmentNotes	= $( '#adjustment_notes' ).val();

			if( stockLocationId.length == 0 || stockLocationId === undefined ){
				swal({
					type: 'error',
					title: 'Please select a Stock Location'
				});
				return false;
			}
			
			if( adjustmentQty.length == 0 || adjustmentQty === undefined ){
				swal({
					type: 'error',
					title: 'Please enter the Quantity'
				});
				return false;
			}
			
			if ( !$( "input[name='adjustment_action']" ).is( ':checked' ) ) {
				swal({
					type: 'error',
					title: 'Please specify the adjustment Action'
				});
				return false;
			}

			if( adjustmentNotes.length == 0 || adjustmentNotes === undefined ){
				swal({
					type: 'error',
					title: 'Please leave a note for this action'
				});
				return false;
			}

			var adjustmentAction= $( "input[name='adjustment_action']:checked" ).val();
			
			if( adjustmentAction == 'deduct' && ( adjustmentQty > availableQty ) ){
				swal({
					type: 'warning',
					title: 'There is not enough disciplines available to deduct your entered amount!'
				});
				return false;
			}

			var formID = $( this ).closest( 'form' ).attr( 'id' );
			event.preventDefault();
			var formData = $('#'+formID ).serialize();
			
			swal({
				title: 'Confirm Stock Level Adjustment?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/stock/adjust_discipline_levels/'.$discipline_details->discipline_id); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2000
								})
								window.setTimeout(function(){
									location.reload();
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
			}).catch(swal.noop)
		});
		
		$( '#transfer-stock-btwn-locations-btn' ).click( function( event ){
			
			var sourceLocationId = $( '#source_location_id option:selected' ).val(),
				availableQty 	 = $( '#source_location_id option:selected' ).data( 'available_qty' ),
				destLocationId 	 = $( '#destination_location_id option:selected' ).val(),
				transferQty		 = $( '#transfer_qty' ).val();
				transferNotes	 = $( '#transfer_notes' ).val();

			if( sourceLocationId.length == 0 || sourceLocationId === undefined ){
				swal({
					type: 'warning',
					title: 'Please select the source Location'
				});
				return false;
			}
			
			if( destLocationId.length == 0 || destLocationId === undefined ){
				swal({
					type: 'warning',
					title: 'Please select the destination Location'
				});
				return false;
			}
			
			if( sourceLocationId ==  destLocationId ){
				swal({
					type: 'warning',
					title: 'The Source and Destination locations must be different'
				});
				return false;
			}
			
			if( transferQty.length == 0 || transferQty === undefined ){
				swal({
					type: 'warning',
					title: 'Please enter the Quantity to transfer'
				});
				return false;
			}

			if( transferNotes.length == 0 || transferNotes === undefined ){
				swal({
					type: 'error',
					title: 'Please leave a note for this action'
				});
				return false;
			}
			
			if( transferQty > availableQty ){
				swal({
					type: 'warning',
					title: 'There is not enough disciplines available to move from the Source location!'
				});
				return false;
			}

			var formID = $( this ).closest( 'form' ).attr( 'id' );
			event.preventDefault();
			var formData = $('#'+formID ).serialize();
			
			swal({
				title: 'Confirm Stock Transfer request?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/stock/request_stock_transfer/'.$discipline_details->discipline_id); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2000
								})
								window.setTimeout(function(){
									location.reload();
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
			}).catch(swal.noop)
		});
	});
</script>