<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Vehicle History Log<small> (last 10 entries)</small></legend>
			<?php if( !empty( $vehicle_change_logs ) ){ ?>
				<table style="width:100%">
					<thead>
						<tr>
							<th width="10%">Log Type</th>
							<th width="15%">Action</th>
							<th width="20%">Note</th>
							<th width="15%">Timestamp</th>
							<th width="10%">Actioned By</th>
							<th width="10%">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					<?php if( !empty( $vehicle_change_logs ) ){ foreach( $vehicle_change_logs as $row ) { ?>
						<tr>
							<td><?php echo ucwords( str_replace( '_', ' ', $row->log_type ) ); ?></td>
							<td><?php echo ucwords( $row->action ); ?></td>
							<td><?php echo $row->note; ?></td>
							<td><?php echo ( valid_date( $row->date_created ) ) ? date( 'd-m-Y H:i:s', strtotime( $row->date_created ) ) : '' ; ?></td>
							<td><?php echo $row->created_by_full_name; ?></td>
							<td><a href="<?php echo base_url( 'webapp/fleet/profile/'.$vehicle_details->vehicle_id ).'/'.$row->log_type; ?>">View</a></td>
						</tr>
					<?php } } ?>
					</tbody>
				</table>
			<?php }else{ ?>
				<span><?php echo $this->config->item('no_records'); ?></span>
			<?php } ?>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){
		$( '.datetimepicker' ).datetimepicker({
			formatDate: 'd/m/Y H:i:s',
			timepicker: true,
			format:'d/m/Y H:i:s',
		});


		$( ".been_audited" ).change( function( e ){
			e.preventDefault();
			if( $( this ).val() == 'yes' ){
				$( "#unassign-driver-btn, #assign-driver-btn" ).prop( "disabled", false );
			} else {
				alert( "You need to audit vehicle before submit the action." );
				$( "#unassign-driver-btn, #assign-driver-btn" ).attr( "disabled", true );
			}
		});

		$( '#unassign-driver-btn' ).click( function( event ){
			event.preventDefault();
			var formData = $( '#assignDriver' ).serialize();
			swal({
				title: 'Confirm removing driver',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url( 'webapp/fleet/remove_driver/'.$vehicle_details->vehicle_id ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 500
								})
								window.setTimeout( function(){
									location.reload();
								},500 );
							} else {
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						}
					});
				} else {
					console.log( 'fail' );
				}
			}).catch( swal.noop )
		});


		$( '#assign-driver-btn' ).click( function( event ){
			event.preventDefault();
			var formData = $( '#assignDriver' ).serialize();
			swal({
				title: 'Confirm assigning a new driver',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url( 'webapp/fleet/assign_driver/'.$vehicle_details->vehicle_id ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 500
								})
								window.setTimeout( function(){
									location.reload();
								},500 );
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						}
					});
				} else {
					console.log( 'fail' );
				}
			}).catch( swal.noop )
		});
	});
</script>