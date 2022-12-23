<div class="row report-page">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel">
			<div class="x_content">
				<legend>System Reports</legend>
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 system-reports">
						<form action="<?php echo base_url( "webapp/report/upload_docs" ); ?>" id="report" method="post" class="form-horizontal" enctype="multipart/form-data">

							<input type="hidden" id="category_group" name="category_group" value="" />

							<div class="input-group form-group container-full">
								<label class="input-group">Select report category:</label>
								<select id="report_categories" name="report_category_id" class="form-control">
									<option value="">Please select</option>
									<?php if( !empty( $report_categories ) ) { foreach( $report_categories as $k => $r_cat ) { ?>
										<option value="<?php echo $r_cat->category_id; ?>" data-category_group="<?php echo ( !empty( $r_cat->category_group ) ) ? $r_cat->category_group : '' ; ?>"><?php echo ( !empty( $r_cat->category_name ) ) ? $r_cat->category_name : '' ; ?></option>
									<?php } } ?>
								</select>
							</div>
							<div class="input-group form-group container-full">
								<label class="input-group">Select report type:</label>
								<select id="report_types" name="report_type_id" class="form-control">
									<option value="">Please select category first.</option>
								</select>
							</div>

							<!-- Container for Royalty Reports -->
							<div class="content_royalty_r_container el-hidden">
								<div class="input-group form-group container-full">
									<label class="input-group">Select provider:</label>
									<select id="content_providers" name="provider_id" class="form-control">
										<option value="">Please select</option>
										<?php if( !empty( $content_providers ) ) { foreach( $content_providers as $k => $r_provider ) { ?>
											<option value="<?php echo $r_provider->provider_id; ?>" <?php echo ( strtolower( $r_provider->provider_name ) != 'uip' ) ? 'disabled="disabled" style="background: lightgrey;"' : "" ; ?>><?php echo ( !empty( $r_provider->provider_name ) ) ? $r_provider->provider_name : '' ; ?> </option>
										<?php } } ?>
									</select>
								</div>

								<div class="input-group form-group container-full">
									<div class="row">
										<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
											<label class="input-group">Month:</label>
											<select id="month" name="month" class="form-control">
												<option value="">Please select</option>
												<?php
												$curr_month	= date( 'F' );

 												if( !isset( $months ) || empty( $months ) ){
													$months 	= ['January', 'February','March','April','May','June','July','August','September','October','November', 'December'];
												}
												foreach( $months as $key => $month ){ ?>
													<option value="<?php echo $key + 1; ?>" data-month_name="<?php echo strtolower( $month ); ?>"><?php echo $month; ?></option>
												<?php
												} ?>
											</select>
										</div>

										<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
											<label class="input-group" for="year">Year:</label>
											<select id="year" name="year"  class="form-control">
												<option value="">Please select</option>
											<?php
											$year = date( 'Y' );
											for( $i = -5; $i<5; $i++ ){ ?>
												<option value="<?php echo $year + $i; ?>" <?php echo ( $i == 0 ) ? 'selected="selected"' : '' ; ?>><?php echo $year + $i; ?></option>
											<?php } ?>
											</select>
										</div>
									</div>
								</div>

								<div class="input-group form-group container-full">
									<label class="input-group">Expected files:</label>
									<div class="" id="expected_files">
										<div class="files-selected"><ul><li>Please select Month</li></ul></div>
									</div>
								</div>

								<div class="input-group form-group">
									<label class="input-group">File upload</label>
									<span class="report-fileupload pointer">
										<label for="files" class="custom-file-upload light">File upload</label>
										<input type="file"
										   multiple="multiple"
											   name="upload_files[]"
												 id="files"
											  class="form-control"
										   onchange="javascript:updateList()"
											  />
									</span>
								</div>

								<div class="input-group form-group container-full">
									<label class="input-group">Selected files:</label>
									<div class="files-selected"><ul id="fileList"><li>No files selected...</li></ul></div>
								</div>

								<div class="input-group form-group container-full">
									<div class="row">
										<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
											<button id="doc-upload-btn" class="btn btn-sm btn-block btn-success" type="submit">Generate</button>
										</div>
									</div>
								</div>
							</div>
							<!-- Container for Royalty Reports - END -->
							
							
							<!-- Container for Basic Reports -->
							<div class="content_basic_r_container el-hidden">
							
								<div class="input-group form-group container-full el-hidden">
									<label class="input-group">Filters</label>
									<select id="filter" class="form-control">
										<option value="">Please select</option>
										<?php if( !empty( $filters ) ) { } ?>
									</select>
								</div>
							
								<div class="input-group form-group container-full">
									<div class="row">
										<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
											<button id="doc-upload-btn" class="btn btn-sm btn-block btn-success" type="submit">Generate</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>

					<div class="col-lg-offset-2 col-lg-6 col-md-offset-2 col-md-6 col-sm-12 col-xs-12 generated-reports">
						<div class="input-group form-group container-full">
							<label class="input-group">Generated Reports</label>
						</div>

						<?php
						foreach( $report_categories as $category ){
							if( $category->category_id == 1 ){ ?>
							<div class="x_panel tile group-container no-background">
								<h4 class="legend category-caller data-container" data-category_id="<?php echo $category->category_id; ?>">
									<i class="fas fa-caret-down"></i><?php echo ( !empty( $category->category_name ) ) ? ucwords( $category->category_name ) : '' ; ?>
								</h4>
								<div class="row group-content category-container" style="display: none;">
								</div>
							</div>
						<?php
							}
						} ?>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>




<script type="text/javascript">
$( document ).ready( function(){

	$( ".group-container" ).on( "click", ".delete-report", function(){
		
		var this_el = $( this );
		
		swal({
			title: "Confirm you want to delete the report",
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function( result ){
			if ( result.value ){
				var reportID = $( this_el ).data( "report_id" );

				$.ajax({
					url: "<?php echo base_url( 'webapp/report/delete_report/' ); ?>",
					method: "POST",
					dataType: "JSON",
					data: {
						report_id: reportID
					},
					success: function( data ){
						swal({
							type: ( data.status ) ? "success" : "error",
							title: ( data.status_msg ) ? data.status_msg : 'Unknown feedback',
						}).then( function(){
							window.location.reload( true );
						});
					}
				});
			}
		});
	});

	$( ".group-container" ).on( "click", ".inner-legend", function(){
		$( this ).children( ".fas" ).toggleClass( "fa-caret-down fa-caret-up" );
		$( this ).next( ".group-content" ).slideToggle( 400 );
	});

	$( ".category-caller" ).on( "click", function(){

		var caller		= $( this );
		var categoryID 	= caller.data( "category_id" );
		var open 		= $( "> i", caller ).hasClass( "fa-caret-down" );

		if( open ){
			// This is working too fast for now. For the future if needed this can be uncommented
			// swal({
				// title: "Fetching reports",
				// text: "Please wait...",
				// showConfirmButton: false,
				// timer: 3000,
			// })

			$.ajax({
				url: "<?php echo base_url( 'webapp/report/get_reports/' ); ?>",
				method: "POST",
				data: {
					category_id: categoryID
				},
				success: function( data ){
					data = JSON.parse( data );
					if( ( data.status == 1 ) || ( data.status == true ) ){
						$( caller ).parent().find( ".category-container" ).empty().append( data.reports );
					} else {
					// swal({
						// type: 'error',
						// title: data.status_msg
					// })
					}
				}
			});
		}
	});

	<?php
	if( !empty( $feedback ) ){ ?>
		swal({
			type: <?php echo ( !empty( $feedback['status'] ) && ( $feedback['status'] == true ) ) ? "'success'" : "'error'" ; ?>,
			title: <?php echo ( !empty( $feedback['message'] ) ) ? "'".ucfirst( $feedback['message']."'" ) : 'Report generation completed with unknown status' ; ?>,
		})
	<?php
	} ?>

	$( ".legend" ).click( function(){
		$( this ).children( ".fas" ).toggleClass( "fa-caret-down fa-caret-up" );
		$( this ).next( ".group-content" ).slideToggle( 400 );
	});

	$( "#doc-upload-btn" ).on( "click", function( e ){
		e.preventDefault();

		var raportCategory 		= $( '*[name="report_category_id"]' ).val();
		if( raportCategory.length === 0 ){
			$( '*[name="report_category_id"]' ).css( "border", "2px solid #f00" );
			swal({
				type: 'error',
				title: "Report Category is required",
			})
			return false;
		} else {
			$( '*[name="report_category_id"]' ).css( "border", "1px solid #ccc" );
		}

		var raportType 			= $( '*[name="report_type_id"]' ).val();
		if( raportType.length === 0 ){
			$( '*[name="report_type_id"]' ).css( "border", "2px solid #f00" );
			swal({
				type: 'error',
				title: "Report Type is required",
			})
			return false;
		} else {
			$( '*[name="report_type_id"]' ).css( "border", "1px solid #ccc" );
		}

		var provider 			= $( '*[name="provider_id"]' ).val();
		if( provider.length === 0 ){
			$( '*[name="provider_id"]' ).css( "border", "2px solid #f00" );
			swal({
				type: 'error',
				title: "Provider is required",
			})
			return false;
		} else {
			$( '*[name="provider_id"]' ).css( "border", "1px solid #ccc" );
		}

		var month				= $( '*[name="month"]' ).val();
		if( month.length === 0 ){
			$( '*[name="month"]' ).css( "border", "2px solid #f00" );
			swal({
				type: 'error',
				title: "Month is required",
			})
			return false;
		} else {
			$( '*[name="month"]' ).css( "border", "1px solid #ccc" );
		}

		var year 				= $( '*[name="year"]' ).val();
		if( year.length === 0 ){
			$( '*[name="year"]' ).css( "border", "2px solid #f00" );
			swal({
				type: 'error',
				title: "Year is required",
			})
			return false;
		} else {
			$( '*[name="year"]' ).css( "border", "1px solid #ccc" );
		}

		var upload_files 		= $( '*[name="upload_files[]"]' );
		if( !( upload_files.get(0).files.length ) || ( upload_files.get(0).files.length == 0 ) || ( upload_files.get(0).files.length === undefined ) ){
			$( '*[name="upload_files"]' ).css( "border", "2px solid #f00" );
			swal({
				type: 'error',
				title: "Viewing Stats file(s) is a requirement",
			})
			return false;
		} else {
			$( '*[name="upload_files"]' ).css( "border", "1px solid #ccc" );
		}

		$( "form#report" ).submit();
	});

	var file_list = [];
	updateList = function() {
		var input = document.getElementById( 'files' );
		var output = document.getElementById( 'fileList' );
		var children = '';
		if( input.files.length > 0 ){
			for( var i = 0; i < input.files.length; ++i ){
				if( jQuery.inArray( input.files.item( i ).name, file_list ) > -1 ){
					children += '<li style="color: #5cb85c;">' + input.files.item( i ).name + '</li>';
				} else {
					children += '<li style="color:#b7001f;">' + input.files.item( i ).name + '</li>';
				}


			}
		} else {
			children += '<li>No files selected...</li>';
		}
		output.innerHTML = children+'</ul>';
	}


	$( "#report_categories" ).on( "change", function(){
		
		var categoryID = $( this ).val();
		var categoryGroup = $( "option:selected", this ).data( "category_group" );
		// var categoryGroup = $( this ).children( "selected:selected" ).data( "category_group" );
		console.log( categoryGroup );
		$( "#category_group" ).val( categoryGroup );

		if ( parseInt( categoryID ) ){
			$.ajax({
				url:"<?php echo base_url( 'webapp/report/get_report_types/' ); ?>",
				method: "POST",
				data: {
					category_id: categoryID
				},
				dataType: 'JSON',
				success: function( data ){
					if( data.status == 1 || data.status == true ){
						$( "#report_types" ).empty().append( data.report_types );
					} else {
						swal({
							type: 'error',
							title: data.status_msg
						})
					}
				}
			});
		} else {
			$( "#report_types" ).empty().append( '<option value="">Please add Report Types</option>' );
		}
	});


	$( "[name='provider_id']" ).on( "change", function(){
		$( "*[name='month'], *[name='year']" ).val( "" );
	});

	$( "*[name='month'], *[name='year']" ).on( "change", function(){
		var providerID 	= $( "#report [name='provider_id']" ).val();
		var monthID 	= $( "#report [name='month']" ).val();

		if ( parseInt( providerID ) ){
			$.ajax({
				url:"<?php echo base_url( 'webapp/report/expected_files/' ); ?>",
				method: "POST",
				data: {
					provider_id: providerID,
					month_id: monthID,
				},
				dataType: 'JSON',
				success: function( data ){
					if( data.status == 1 || data.status == true ){
						var html_files = '<div class="files-selected"><ul>';
						$( data.files ).each( function( i, element ){
							html_files += '<li>' + (i + 1) + ': ' + element + '</li>'
						});
						html_files += '</ul>';

						$( "#expected_files" ).empty().append( html_files );
						file_list = data.files;
					} else {
						$( "#expected_files" ).empty();
						swal({
							type: 'error',
							title: data.status_msg
						})
					}
				}
			});
		} else {
			$( "#expected_files" ).empty().append( '<li>Unknown Provider</li>' );
		}
	});


	$( "#report_types" ).on( "change", function(){
		var categoryID = $( "#report_categories" ).val();
		if( parseInt( categoryID ) == 1 ){ // 1 - Royalty Reports
			$( ".content_royalty_r_container" ).removeClass( "el-hidden" ).addClass( "el-visible" );
			$( ".content_basic_r_container" ).removeClass( "el-visible" ).addClass( "el-hidden" );
		} else {
			$( ".content_royalty_r_container" ).removeClass( "el-visible" ).addClass( "el-hidden" );
			$( ".content_basic_r_container" ).removeClass( "el-hidden" ).addClass( "el-visible" );
			
			// load filters by the report type ID
			// filters aren't required yet
		}
	})
	
	$( "#report_categories" ).on( "change", function(){
		$( ".content_royalty_r_container" ).removeClass( "el-visible" ).addClass( "el-hidden" );
		$( ".content_basic_r_container" ).removeClass( "el-visible" ).addClass( "el-hidden" );
	});
});
</script>