<style type="text/css">
.site-devices #devices-module{
	margin-top: -20px;
}

.site-devices #devices-module .top_search{
	max-width: 300px;
	margin-bottom: 10px;
}


.site-devices #datatable > thead > tr > th{
    vertical-align: bottom;
    border-bottom: 2px solid #c0c0c0;
	padding: 8px;
	font-weight: 600;
}

.site-devices #datatable > tbody > tr > td{
    padding: 8px;
    vertical-align: top;
    border-top: 1px solid #ddd;
	color: #000;
}

.site-devices .table-responsive{
	overflow-y: hidden;
    margin-top: 10px;
    background: #fff;
    border: 1px solid #000;
    padding: 10px;
}

.site-devices .img_airtime{
	width: auto;
	min-width: 70px;
	/* max-width: 85px; */
	max-height: 38px;
}

.site-devices .col-sm-2 > .img_container{
	text-align: center;
}

.site-devices .col-md-2 > .img_container{
	text-align: right;
}

.site-devices .img_container > a > img{
	margin-top: 10px;
}

.site-devices .upload-file{
	position: absolute;
    top: 10px;
    right: 10px;
}

.site-devices .fas.fa-upload{
    color: #fff;
    font-size: 14px;
}

.site-devices .sync-error-icon{
	height: 18px;
}

.clickable > img{
    cursor: pointer;
}

.non-clickable > img{
    cursor: auto;
}
</style>

<div id="devices-module" class="row">
	<div class="x_panel no-border">
		<div class="row">
			<div class="x_content">
				<div class="row">
					<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12" >
						<?php
						$this->load->view( 'webapp/_partials/search_bar' ); ?>
					</div>
					<div class="col-lg-2 col-md-offset-0 col-md-2 col-sm-offset-1 col-sm-2 col-xs-offset-1 col-xs-2">
						<div class="img_container">
							<a class="inactive_multiple_link non-clickable" href="#" onclick="return false;">
								<img class="img_airtime" src="<?php echo base_url( 'assets/images/icons/xs-inactive-multiple-connect.png' ); ?>" alt="Select Devices to Link to Airtime" title="Select Devices to Link to Airtime" />
							</a>
							<a class="active_multiple_link clickable" href="<?php echo base_url( "webapp/site/link_device/".$site_details->site_id ) ?>">
								<img class="img_airtime" src="<?php echo base_url( 'assets/images/icons/xs-multiple-connect.png' ); ?>" alt="Select Devices to Link to Airtime" title="Select Devices to Link to Airtime" />
							</a>
						</div>
					</div>
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
						<div class="img_container">
							<a class="inactive_multiple_unlink non-clickable" href="#" onclick="return false;">
								<img class="img_airtime" src="<?php echo base_url( 'assets/images/icons/xs-inactive-multiple-disconnect.png' ); ?>" alt="Select Devices to Unlink from Airtime" title="Select Devices to Unlink from Airtime" />
							</a>
							<a class="active_multiple_unlink clickable" href="<?php echo base_url( "webapp/site/unlink_device/".$site_details->site_id ) ?>">
								<img class="img_airtime" src="<?php echo base_url( 'assets/images/icons/xs-multiple-disconnect.png' ); ?>" alt="Select Devices to Unlink from Airtime" title="Select Devices to Unlink from Airtime" />
							</a>
						</div>
					</div>
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
						<div class="img_container">
							<a class="inactive_multiple_delete non-clickable" href="#" onclick="return false;">
								<img class="img_airtime " src="<?php echo base_url( 'assets/images/icons/xs-inactive-multiple-delete.png' ); ?>" title="Select Devices to Delete on CaCTi" alt="Select Devices to Delete on CaCTi" />
							</a>
							<a class="active_multiple_delete clickable" href="<?php echo base_url( "webapp/site/delete_devices/".$site_details->site_id ) ?>">
								<img class="img_airtime" src="<?php echo base_url( 'assets/images/icons/xs-multiple-delete.png' ); ?>" title="Select Devices to Delete on CaCTi" alt="Select Devices to Delete on CaCTi" />
							</a>
						</div>
					</div>
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
						<div class="img_container">
							<a class="inactive_create_n_link_at non-clickable" href="#" onclick="return false;">
								<img class="img_airtime" src="<?php echo base_url( 'assets/images/icons/xs-inactive-create-and-link-airtime.png' ); ?>" alt="Add and connect Devices on Airtime" title="Add and connect Devices on Airtime" />
							</a>
							<!-- <a class="active_create_n_link_at" href="<?php echo base_url( "webapp/site/create_n_link_device/".$site_details->site_id ) ?>"> -->
							<a class="active_create_n_link_at clickable">
								<img class="img_airtime" src="<?php echo base_url( 'assets/images/icons/xs-create-and-link-airtime.png' ); ?>" alt="Add and connect Devices on Airtime" title="Add and connect Devices on Airtime" />
							</a>
						</div>
					</div>
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
						<div class="img_container">
							<a class="inactive_unlink_at non-clickable" href="#" onclick="return false;">
								<img class="img_airtime" src="<?php echo base_url( 'assets/images/icons/xs-inactive-unlink-AT.png' ); ?>" alt="Unlink Airtime" />
							</a>
							<a class="active_unlink_at clickable">
								<img class="img_airtime" src="<?php echo base_url( 'assets/images/icons/xs-unlink-AT.png' ); ?>" alt="Unlink Airtime" />
							</a>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table id="datatable" class="table">
						<thead>
							<tr>
								<th width="20%">Device Unique ID</th>
								<th width="18%">Product Name</th>
								<th width="7%">Platform</th>
								<th width="20%">Airtime</th>
								<th width="10%">Status</th>
								<th width="15%">Sync Errors</th>
								<th width="10%">Move</th>
							</tr>
						</thead>
						<tbody id="table-results">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$( document ).ready( function(){
	
	$( ".active_unlink_at" ).on( "click", function( e ){
		e.preventDefault();
		$.ajax({
			url: "<?php echo base_url( 'webapp/site/airtime_disconnect' ); ?>",
			method: "POST",
			data:{ 
				site_id: <?php echo ( !empty( $site_details->site_id ) ) ? $site_details->site_id : NULL ; ?>,
			},
			success: function( data ){

				data = JSON.parse( data );
				
				if( data.status == 1 ){
					if( data.stats ){
						swal({
							type: 'success',
							title: '<b>' + data.stats.devices_disconnected + "</b> of <b>" + data.stats.devices_to_disconnect + "</b><br /> Devices successfully disconnected from Airtime.<br /><b>",
							showConfirmButton: true,
						}).then( ( result ) => {
							window.location.reload( true );
						}).catch( swal.noop );
					} else{
						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: true,
						}).then( ( result ) => {
							if ( result.value ){
								window.location.reload( true );
							}
						}).catch( swal.noop );
					}
				} else {
					swal({
						type: 'error',
						title: data.status_msg
					}).then( ( result ) => {
						if ( result.value ){
							window.location.reload( true );
						}
					}).catch( swal.noop );
				}
			}
		});
	});
	
	$( ".active_create_n_link_at" ).on( "click", function( e ){
		e.preventDefault();
		$.ajax({
			url: "<?php echo base_url( 'webapp/site/airtime_connect' ); ?>",
			method: "POST",
			data:{ 
				site_id: <?php echo ( !empty( $site_details->site_id ) ) ? $site_details->site_id : NULL ; ?>,
			},
			success: function( data ){

				data = JSON.parse( data );
				
				if( data.status == 1 ){
					if( data.stats ){
						swal({
							type: 'success',
							title: '<b>' + data.stats.devices_reconnected + "</b> of <b>" + data.stats.devices_to_reconnect + "</b><br /> Devices successfuly reconnected on Airtime.<br /><b>" + data.stats.devices_linked + "</b> of <b>" + data.stats.devices_to_link + "</b><br /> Devices successfuly added and connected on Airtime",
							showConfirmButton: true,
						}).then( ( result ) => {
							window.location.reload( true );
						}).catch( swal.noop );
					} else{
						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: true,
						}).then( ( result ) => {
							if ( result.value ){
								window.location.reload( true );
							}
						}).catch( swal.noop );
					}
				} else {
					swal({
						type: 'error',
						title: data.status_msg
					}).then( ( result ) => {
						if ( result.value ){
							window.location.reload( true );
						}
					}).catch( swal.noop );
				}
			}
		});
	});
	

	var search_str   		= "";
	var start_index	 		= 0;

	load_data( search_str );

	// Click on Pagination links
	$( "#table-results" ).on( "click", ".pagination > li", function( event ){
		event.preventDefault();
		var search_str 	= encodeURIComponent( $( "#search_term" ).val() );
		var start_index = $( this ).find( 'a' ).data( 'ciPaginationPage' );
		load_data( search_str, start_index );
	});
	
	var elements = ['inactive_multiple_link', 'active_multiple_link', 'inactive_multiple_unlink', 'active_multiple_unlink', 'inactive_multiple_delete', 'active_multiple_delete', 'inactive_create_n_link_at', 'active_create_n_link_at', 'inactive_unlink_at', 'active_unlink_at'];
	
	var active = ['inactive_multiple_link', 'active_unlink_at','active_multiple_delete','inactive_multiple_unlink','inactive_create_n_link_at' ];
	
	function update_links( active ){
		jQuery.each( elements, function( index, value ){
			if( jQuery.inArray( value, active ) > -1 ){
				$( "." + value ).removeClass( "el-hidden" ).removeClass( "el-visible" ).addClass( "el-visible" );
			} else {
				$( "." + value ).removeClass( "el-hidden" ).removeClass( "el-visible" ).addClass( "el-hidden" );
			}
		})
	}
	

	function load_data( search_str, start_index ){
		$.ajax({
			url: "<?php echo base_url( 'webapp/site/devices_lookup' ); ?>",
			method:"POST",
			data:{ 
				site_id: <?php echo ( !empty( $site_details->site_id ) ) ? $site_details->site_id : NULL ; ?>,
				search_term:search_str,
				start_index:start_index,
			},
			success:function( data ){
				if( data ){
					data = JSON.parse( data );
					$( '#table-results' ).html( data.table_data );
					update_links( data.active_links );
				}
			}
		});
	}

	$( '#search_term' ).keyup( function(){
		var start_index = $( this ).find( 'a' ).data( 'ciPaginationPage' );
		var search = encodeURIComponent( $( this ).val() );
		if( search.length > 0 ){
			load_data( search, start_index );
		} else {
			load_data( search_str, start_index );
		}
	});
});
</script>