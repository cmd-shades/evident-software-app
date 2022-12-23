<div class="modal fade in" id="createProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<?php
					$product_type_id = $this->uri->segment( 6, 1 );

					switch( $product_type_id ){
						case 70: ## VOD
							include_once( "includes/add_product_vod.php" );
							break;

						case 71: ## Airtime
							include_once( "includes/add_product_airtime.php" );
							break;

						case 69: ## linear
						default:
							include_once( "includes/add_product_linear.php" );
					} ?>
				</div>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">
$( document ).ready( function(){
	$( ".check-airtimepin-button" ).click( function( e ){
		e.preventDefault();

		var pin = $( "[name = 'product_details[airtime_pin]' ]" ).val();
		if( !( pin == "" ) ){
 			$.ajax({
				url:"<?php echo base_url( 'webapp/site/check_reference/' ); ?>",
				method: "POST",
				data: {
					"reference": pin,
					"module": "airtime_product"
				},
				dataType: 'json',
				success: function( data ){
					if( ( data.status == 1 ) ){
						swal({
							type: 'error',
							title: "Provided Airtime PIN already exists",
							timer: 3000
						})
						return false;
					} else {
						var currentpanel = $( this ).data( "currentpanel" );
						var inputs_state = check_inputs( currentpanel );
						if( inputs_state ){
							//If name attribute returned, auto focus to the field and display error message
							$( '[name="'+inputs_state+'"]' ).focus();
							var labelText = $( '[name="' + inputs_state + '"]' ).parent().find( 'label' ).text();
							$( '#' + currentpanel + '-errors' ).text( ucwords( labelText ) + ' is a required' );
							return false;
						}

						var elementClass = ".product_creation_panel";
						panelchange( ".product_creation_panel4", elementClass );
					}
				}
			});

		} else {
			swal({
				type: 'error',
				title: "Reference can not be empty!",
				timer: 3000
			})
		}
	});

	$( '#product-creation-form' ).on( "submit", function( e ){
		e.preventDefault();

		var pin = $( "[name = 'product_details[airtime_pin]' ]" ).val();
		if( !( pin == "" ) ){
  			$.ajax({
				url: "<?php echo base_url( 'webapp/site/check_reference/' ); ?>",
				method: "POST",
				data: {
					"reference": pin,
					"module": "airtime_product"
				},
				dataType: 'json',
				success: function( data ){
					if( ( data.status == 1 ) ){
						swal({
							type: 'error',
							title: "Provided Airtime PIN already exists",
							timer: 3000
						})
						return false;
					} else {
						var formData = $( '#product-creation-form' ).serialize();
						swal({
							title: 'Confirm new product creation?',
							showCancelButton: true,
							confirmButtonColor: '#5CB85C',
							cancelButtonColor: '#9D1919',
							confirmButtonText: 'Yes'
						}).then( function (result) {
							if ( result.value ) {
								$.ajax({
									url:"<?php echo base_url( 'webapp/product/create_product/' ); ?>",
									method: "POST",
									data:formData,
									dataType: 'json',
									success:function( data ){
										if( data.status == 1 && ( data.new_product !== '' ) ){

											var site_id = data.new_product.site_id;

											swal({
												type: 'success',
												title: data.status_msg,
												showConfirmButton: false,
												timer: 6000
											})
											window.setTimeout( function(){
												 location.href = "<?php echo ( !empty( $site_details->site_id ) ) ? base_url( 'webapp/site/profile/'.$site_details->site_id ) : base_url( 'webapp/site/' ) ; ?>";
											}, 6000 );
										} else {
											swal({
												type: 'error',
												title: data.status_msg
											})
											return false;
										}
									}
								});
							} else {
								return false;
							}
 						}).catch( swal.noop )
  					}
				}
 			});

		} else {
			swal({
				type: 'error',
				title: "Reference can not be empty!",
				timer: 3000
			})

			return false;
		}


	});

	$( "[name = 'product_details[product_name]' ]" ).on( "input", function() {
		title_text = $( "[ name = 'product_details[product_name]']" ).val();
		if( title_text.length > 255 ) title_text = title_text.substring( 0,254 );
		title_text = title_text.replace( /[^a-z0-9]+/gi, "" ).toLowerCase();
		$( "[name = 'product_details[product_reference_code]' ]" ).val(title_text);
	});

	$( "#check-reference-button" ).click( function( e ){
		e.preventDefault();
		var ref = $( "[name = 'product_details[product_reference_code]' ]" ).val()

		if( !( ref == "" ) ){
			$.ajax({
				url:"<?php echo base_url( 'webapp/site/check_reference/' ); ?>",
				method:"POST",
				data:{
					"reference": ref,
					"module": "product"
				},
				dataType: 'json',
				success:function( data ){
					if( ( data.status == 1 ) ){
						swal({
							type: 'error',
							title: data.status_msg,
							timer: 3000
						})
						panelchange( ".product_creation_panel1", ".product_creation_panel" );
					} else {
						var currentpanel = $( this ).data( "currentpanel" );
						var inputs_state = check_inputs( currentpanel );
						if( inputs_state ){
							//If name attribute returned, auto focus to the field and display error message
							$( '[name="'+inputs_state+'"]' ).focus();
							var labelText = $( '[name="' + inputs_state + '"]' ).parent().find( 'label' ).text();
							$( '#' + currentpanel + '-errors' ).text( ucwords( labelText ) + ' is a required' );
							return false;
						}

						var elementClass = ".product_creation_panel";

						panelchange( ".product_creation_panel1", elementClass );
					}
				}
			});

		} else {
			swal({
				type: 'error',
				title: "Reference can not be empty!",
				timer: 3000
			})
		}
	});


	// product creation section
	$( ".product-creation-steps" ).click( function(){
		$( '.error_message' ).each( function(){
			$( this ).text( '' );
		});

		var currentpanel = $( this ).data( "currentpanel" );
		// If true - there are errors
		var inputs_state = check_inputs( currentpanel );
		if( inputs_state ){
			//If name attribute returned, auto focus to the field and display arror message
			$( '[name="'+inputs_state+'"]' ).focus();
			var labelText = $( '[name="' + inputs_state + '"]' ).parent().find( 'label' ).text();
			$( '#' + currentpanel + '-errors' ).text( ucwords( labelText ) + ' is a required' );
			return false;
		}

		var elementClass = ".product_creation_panel";
		panelchange( "." + currentpanel, elementClass )
		return false;
	});

	$( '#createProduct' ).modal( 'show' );



	//** Validate any inputs that have the required class, if empty return the name attribute **/
	function check_inputs( currentpanel ){
		var result = false;
		var panel = "." + currentpanel;

		$( $( panel + " .required" ).get().reverse() ).each( function(){
			var fieldName = '';
			var inputValue = $( this ).val();
			if( ( inputValue == false ) || ( inputValue == '' ) || ( inputValue.length == 0 ) ){
				fieldName = $(this).attr( 'name' );
				result = fieldName;
				return result;
			}
		});
		return result;
	}

	$( ".btn-back" ).click( function(){
		var currentpanel = $( this ).data( "currentpanel" );
		go_back( "." + currentpanel )
		return false;
	});

	function panelchange( changefrom, elementClass, changeto ){
		var panelnumber = parseInt( changefrom.match(/\d+/) ) + parseInt( 1 );
		var changeto = elementClass + panelnumber;
		$( changefrom ).hide( "slide", {direction : 'left'}, 500 );
		$( changeto ).delay( 600 ).show( "slide", {direction : 'right'},500 );
		return false;
	}

	function go_back( changefrom ){
		var panelnumber = parseInt( changefrom.match(/\d+/) ) - parseInt( 1 );
		var elementClass = changefrom.substr( 0, parseInt( changefrom.length ) - parseInt( panelnumber.toString().length ) );
		var changeto = elementClass + panelnumber;
		$( changefrom ).hide( "slide", {direction : 'right'}, 500 );
		$( changeto ).delay( 500 ).show( "slide", {direction : 'left'},500 );
		return false;
	}
});
</script>