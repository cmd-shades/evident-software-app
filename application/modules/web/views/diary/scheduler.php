<div class="row top_<?php echo $module_identier; ?>">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<legend>DIARY PLANNING</legend>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<?php	$this->load->view('webapp/_partials/diary_search_bar'); ?>
					</div>
				</div>
				<div class="row margin-top-15">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="filters gray-box shaded">

							<div class="single-filter-container full-width el-hidden">
								<h4 href="#skills_set" class="legend filter-collapse-toggle"><i class="fas fa-caret-down"></i>Skill Set</h4>
								<div id="skills_set" class="collapse filter-container">
									<label>
										<input type="checkbox" class="form-check-input skills" name="skills[]" value="1" autocomplete="off" /><span class="label-text">All</span>
									</label>

									<label>
										<input type="checkbox" class="form-check-input skills" name="skills[]" value="2" autocomplete="off" /><span class="label-text">Aerial</span>
									</label>

									<label>
										<input type="checkbox" class="form-check-input skills" name="skills[]" value="3" autocomplete="off" /><span class="label-text">Face Plates</span>
									</label>

									<label>
										<input type="checkbox" class="form-check-input skills" name="skills[]" value="4" autocomplete="off" /><span class="label-text">Fork Lift Driving</span>
									</label>

									<label>
										<input type="checkbox" class="form-check-input skills" name="skills[]" value="5" autocomplete="off" /><span class="label-text">IRS</span>
									</label>

									<label>
										<input type="checkbox" class="form-check-input skills" name="skills[]" value="6" autocomplete="off" /><span class="label-text">Surveying</span>
									</label>
								</div>
							</div>


							<div class="single-filter-container full-width el-hidden">
								<h4 href="#region" class="legend filter-collapse-toggle"><i class="fas fa-caret-down"></i>Region</h4>
								<div id="region" class="collapse filter-container">
									<label>
										<input type="checkbox" class="form-check-input regions" name="regions[]" value="1" autocomplete="off" /><span class="label-text">North</span>
									</label>
									<label>
										<input type="checkbox" class="form-check-input regions" name="regions[]" value="2" autocomplete="off" /><span class="label-text">East</span>
									</label>
									<label>
										<input type="checkbox" class="form-check-input regions" name="regions[]" value="3" autocomplete="off" /><span class="label-text">South</span>
									</label>
									<label>
										<input type="checkbox" class="form-check-input regions" name="regions[]" value="4" autocomplete="off" /><span class="label-text">West</span>
									</label>
								</div>
							</div>

							<div class="single-filter-container full-width">
								<h4 href="#day" class="legend filter-collapse-toggle"><i class="fas fa-caret-down"></i>Day</h4>
								<div id="day" class="collapse filter-container">
									<label>
										<input type="checkbox" class="form-check-input days" name="days[]" value="monday" autocomplete="off" /><span class="label-text">Monday</span>
									</label>
									<label>
										<input type="checkbox" class="form-check-input days" name="days[]" value="tuesday" autocomplete="off" /><span class="label-text">Tuesday</span>
									</label>
									<label>
										<input type="checkbox" class="form-check-input days" name="days[]" value="wednesday" autocomplete="off" /><span class="label-text">Wednesday</span>
									</label>
									<label>
										<input type="checkbox" class="form-check-input days" name="days[]" value="thursday" autocomplete="off" /><span class="label-text">Thursday</span>
									</label>
									<label>
										<input type="checkbox" class="form-check-input days" name="days[]" value="friday" autocomplete="off" /><span class="label-text">Friday</span>
									</label>
									<label>
										<input type="checkbox" class="form-check-input days" name="days[]" value="saturday" autocomplete="off" /><span class="label-text">Saturday</span>
									</label>
									<label>
										<input type="checkbox" class="form-check-input days" name="days[]" value="sunday" autocomplete="off" /><span class="label-text">Sunday</span>
									</label>
								</div>
							</div>

							<div class="single-filter-container full-width">
								<h4 href="#date" class="legend filter-collapse-toggle"><i class="fas fa-caret-down"></i>Date</h4>
								<div id="date" class="collapse filter-container">
									<div class="input-group form-group">
										<input name="date" class="form-control dates" type="text" placeholder="Pick a date" />
									</div>
								</div>
							</div>

							<div class="single-filter-container full-width">
								<h4 href="#start_time" class="legend filter-collapse-toggle"><i class="fas fa-caret-down"></i>Start Time</h4>
								<div id="start_time" class="collapse filter-container">
									<label>
										<input type="checkbox" class="form-check-input start_times" name="start_times[]" value="07:30:00" autocomplete="off" /><span class="label-text">07:30</span>
									</label>
									<label>
										<input type="checkbox" class="form-check-input start_times" name="start_times[]" value="08:00:00" autocomplete="off" /><span class="label-text">08:00</span>
									</label>
									<label>
										<input type="checkbox" class="form-check-input start_times" name="start_times[]" value="08:30:00" autocomplete="off" /><span class="label-text">08:30</span>
									</label>
									<label>
										<input type="checkbox" class="form-check-input start_times" name="start_times[]" value="09:00:00" autocomplete="off" /><span class="label-text">09:00</span>
									</label>
									<label>
										<input type="checkbox" class="form-check-input start_times" name="start_times[]" value="09:30:00" autocomplete="off" /><span class="label-text">09:30</span>
									</label>
									<label>
										<input type="checkbox" class="form-check-input start_times" name="start_times[]" value="10:00:00" autocomplete="off" /><span class="label-text">10:00</span>
									</label>
									<label>
										<input type="checkbox" class="form-check-input start_times" name="start_times[]" value="11:00:00" autocomplete="off" /><span class="label-text">11:00</span>
									</label>
									<label>
										<input type="checkbox" class="form-check-input start_times" name="start_times[]" value="12:00:00" autocomplete="off" /><span class="label-text">12:00</span>
									</label>
								</div>
							</div>

							<div class="single-filter-container full-width">
								<h4 href="#finish_time" class="legend filter-collapse-toggle"><i class="fas fa-caret-down"></i>Finish Time</h4>
								<div id="finish_time" class="collapse filter-container">
									<label>
										<input type="checkbox" class="form-check-input finish_times" name="finish_times[]" value="14:00:00" autocomplete="off" /><span class="label-text">14:00</span>
									</label>
									<label>
										<input type="checkbox" class="form-check-input finish_times" name="finish_times[]" value="17:00:00" autocomplete="off" /><span class="label-text">17:00</span>
									</label>
									<label>
										<input type="checkbox" class="form-check-input finish_times" name="finish_times[]" value="18:00:00" autocomplete="off" /><span class="label-text">18:00</span>
									</label>
									<label>
										<input type="checkbox" class="form-check-input finish_times" name="finish_times[]" value="20:00:00" autocomplete="off" /><span class="label-text">20:00</span>
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
				<div class="table-responsive shaded" role="alert">
					<table id="datatable" class="table table-responsive" style="margin-bottom:0px;width:100%" >
						<thead>
							<tr>
								<th width="20%">Field Operative</th>
								<th width="10%">Day</th>
								<th width="10%">Date</th>
								<th width="15%">Start Time</th>
								<th width="15%">Finish Time</th>
								<?php /* <th width="10%">Hours</th> */ ?>
								<th width="10%" class="text-center" >Slots</th>
								<th width="10%" class="text-center" >Used</th>
								<th width="10%" class="text-center" >Free</th>
							</tr>
						</thead>
						<tbody id="table-results">
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
				<div id="counters">
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade single-resource-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title" id="updateResource">Update Resource Details <span class="hide resource-id-span"></span></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-12" id="ajax_single_resource">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$( document ).ready( function(){
	$( "#ajax_single_resource" ).on( "click", "#deleteResource", function( e ){
		e.preventDefault();
		var resourceID = $( this ).data( "resource_id" );
		swal({
			title: 'Confirm resource delete?',
			// type: 'question',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url('webapp/diary/delete_resource'); ?>",
					method: "POST",
					dataType: "JSON",
					data: { resource_id: resourceID },
					success:function( output ){
						if( output.status == 1 ){
							swal({
								type: 'success',
								title: output.status_msg,
								showConfirmButton: false,
								timer: 3000
							})
							window.setTimeout( function(){
								location.reload();
							}, 3000 );
						} else {
							swal({
								type: 'error',
								title: output.status_msg
							})
						}
					}
				});
			}
		}).catch( swal.noop )
	});


	$( "#ajax_single_resource" ).on( "submit", "form#resource_update_in_modal", function( e ){
		e.preventDefault();
		var resourceID = $( '[name="resource_id"]', this ).val();
		var formData = $( "form#resource_update_in_modal" ).serialize();
		swal({
			title: 'Confirm resource update?',
			// type: 'question',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url('webapp/diary/update_resource'); ?>",
					method: "POST",
					dataType: "JSON",
					data: formData,
					success:function( output ){
						if( output.status == 1 ){
							swal({
								type: 'success',
								title: output.status_msg,
								showConfirmButton: false,
								timer: 3000
							})
							window.setTimeout( function(){
								location.reload();
							},3000 );
						} else {
							swal({
								type: 'error',
								title: output.status_msg
							})
						}
					}
				});
			}
		}).catch( swal.noop )
	});

	var weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];

	$( "#table-results" ).on( "click", ".single_resource", function(){
		var resourceID = $( this ).data( "resource_id" );
 		$.ajax({
			url:"<?php echo base_url('webapp/diary/get_resource'); ?>",
			method: "POST",
			dataType: "JSON",
			data:{ resource_id: resourceID },
			success:function( output ){
				if( output.status == 1 || output.status == true ){
					$( '#ajax_single_resource' ).html( output.resource );
					$( "#ajax_single_resource .datetimepicker" ).datetimepicker({
						timepicker:false,
						format:'d/m/Y',
						scrollInput : false
					});

					$( ".resource-id-span" ).html( resourceID );

					$( "#ajax_single_resource").on( "change", '.reference_date', function( e ){
						e.preventDefault();

						var dateStr = $( this ).val();
						var dateStrDate = new Date( dateStr.split( '/' )[2], dateStr.split( '/' )[1] - 1, dateStr.split( '/' )[0] );
 						var day = weekday[dateStrDate.getDay()];
						$( ".day-field" ).val( day );
					});

					$( "#ajax_single_resource .timepicker" ).datetimepicker({
						datepicker:false,
						format:'H:i',
						allowTimes: [
							'07:00', '07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00','14:30',
							'15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30', '22:00'
						],
						step:30
					});
				}
				jQuery( ".single-resource-modal" ).modal( "show" );

			}
		});
	});

	function convertDate( inputFormat ){
		function pad( s ){ return (s < 10) ? '0' + s : s; }
		var d = new Date( inputFormat );
		return [pad(d.getDate()), pad(d.getMonth()+1), d.getFullYear()].join('/');
	}

	$( '.dates' ).datetimepicker({
		timepicker:false,
		format:'d-m-Y',
	});

	$( ".filter-collapse-toggle" ).click( function(){
		$( this ).children( ".fas" ).toggleClass( "fa-caret-down fa-caret-up" );
		$($(this).attr('href')).slideToggle( 400 );
	});

	// Initial settings
	var search_str   	= null;
	var skills_arr		= [];
	var regions_arr		= [];
	/* var date			= "<?php echo date('Y-m-d'); ?>"; */
	var dates_arr		= [];
	var day_arr			= [];
	var start_times		= [];
	var finish_times	= [];
	var start_index	 	= 0;

	var where = {
		'regions'		:regions_arr,
		'skills' 		:skills_arr,
		'dates' 		:dates_arr,
		'days' 			:day_arr,
		'start_times'	:start_times,
		'finish_times'	:finish_times,
	};

	$( '.user-skills' ).each( function(){
		if( $( this ).is( ':checked' ) ){
			where.skill_id = skills_arr.push( $( this ).val() );
		}
	});

	$( '.user-regions' ).each( function(){
		if( $( this ).is( ':checked' ) ){
			where.region_id = regions_arr.push( $( this ).val() );
		}
	});

	load_data( search_str, where, start_index );
	// End of initial settings and load the data

	//Do Search when filters are changed
	$( '.days, .dates, .start_times, .finish_times' ).change( function(){
		where.days			= get_statuses( '.days' );
		where.dates			= get_statuses( '.dates' );
		where.start_times	= get_statuses( '.start_times' );
		where.finish_times	= get_statuses( '.finish_times' );
		var search_str  	= encodeURIComponent( $('#search_term').val() );
		load_data( search_str, where, start_index );
	});


	// Pagination links
	$( "#table-results" ).on( "click", "li.page", function( event ){
		event.preventDefault();
		var start_index 	= $( this ).find( 'a' ).data( 'ciPaginationPage' );
		var search_str  	= encodeURIComponent( $('#search_term').val() );
		load_data( search_str, where, start_index );
	});

	function load_data( search_str, where, start_index ){
		$.ajax({
			url:"<?php echo base_url('webapp/diary/resources_lookup'); ?>",
			method:"POST",
			data:{ search_term:search_str, where:where, start_index:start_index },
			success:function( data ){
				var newData = JSON.parse( data );
				$( '#table-results' ).html( newData.return_data );


				$( '#counters' ).html( newData.counters_data );

				// TODO: Patch the pagination library!!!

				// JQuery Patches

				// patch to show the correct page number
				if(start_index !== 0){
					$( '#table-results' ).find('.pagination').find( 'a' ).removeClass('pgn-link')
					if(start_index == 1){
						$( '#table-results' ).find('.pagination').find( "a:first" ).addClass('pgn-link')
					} else {
						$( '#table-results' ).find('.pagination').find( "a[data-ci-pagination-page='" + start_index + "']" ).addClass('pgn-link')
					}
				}

				// patch to fix the next button
				$( '#table-results' ).find('.pagination').find( "a[rel='next']" ).attr('data-ci-pagination-page', (start_index == 0 ? 2 : start_index + 1))

			}
		});
	}

	$( '#search_term' ).keyup( function(){
		var search = encodeURIComponent( $( this ).val() );
		if( search.length > 0 ){
			load_data( search , where );
		}else{
			load_data( search_str, where );
		}
	});

	function get_statuses( identifier ){

		var chkCount  = 0;
		var totalChekd= 0;
		var unChekd   = 0;
		var idClass	  = '';

		if( identifier == '.days' ){

			day_arr  = [];

			$( identifier ).each(function(){
				chkCount++;
				if( $(this).is(':checked') ){
					totalChekd++;
					day_arr.push( $( this ).val() );
				} else {
					unChekd++;
				}
			});
			return day_arr;

		} else if( identifier == '.dates' ){
			dates_arr 	= [];

			$( identifier ).each(function(){
				chkCount++;
				if( $( this ).val() != '' ){
					totalChekd++;
					var d = $( this ).datetimepicker( 'getValue' );
					var c = convertDate( d );
					dates_arr.push( c );
				} else {
					unChekd++;
				}
			});

			return dates_arr;
		} else if( identifier == '.start_times' ){
			start_times_arr  = [];

			$( identifier ).each( function(){
				chkCount++;
				if( $( this ).is( ':checked' ) ){
					totalChekd++;
					start_times_arr.push( $( this ).val() );
				} else {
					unChekd++;
				}
			});
			return start_times_arr;
		} else if( identifier == '.finish_times' ){
			finish_times_arr  = [];

			$( identifier ).each( function(){
				chkCount++;
				if( $( this ).is( ':checked' ) ){
					totalChekd++;
					finish_times_arr.push( $( this ).val() );
				} else {
					unChekd++;
				}
			});
			return finish_times_arr;
		}
	}
});
</script>
