							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- /.content-wrapper -->
			<?php if (in_array($_SERVER['REMOTE_ADDR'], array("127.0.0.1", "::1"))) { ?>
				<footer class="main-footer">
					<div class="container-fluid">
							<div class="pull-right hidden-xs">
								<small><b>Evident Version</b> <?php echo APP_VERSION; ?></small>
							</div>
					</div>
				</footer>
			<?php } ?>
		</div>

		<!-- JS files -->
		<script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/jquery.slimscroll.min.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/fastclick.min.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/Chart.min.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/jquery.datetimepicker.full.min.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/moment.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/collapse.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/transition.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/sorttable.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/nprogress.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/jquery.smartWizard.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/sweetalert2.min.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/jquery-ui.min.js'); ?>" type="text/javascript"></script>
		<!-- <script src="<?php echo base_url('assets/js/icheck.min.js'); ?>" type="text/javascript"></script> -->
		<script src="<?php echo base_url('assets/js/select2.min.js'); ?>" type="text/javascript"></script>
		
	</body>
</html>

<script>
	$( document ).ready(function(){
		
		$.ajaxSetup({
			beforeSend: function(xhr) {
				var forms = $( "form" );
				forms.each(function(i) {
				   $( this ).prepend( '<input type="hidden" name="'+csrfName+'" value="'+xsrfToken+'">' );
				});
			}
		});
		
		//console.log(xsrfToken);
		var forms = $( "form" );
		forms.each(function(i) {
		   $( this ).prepend( '<input type="hidden" name="'+csrfName+'" value="'+xsrfToken+'">' );
		});
		
		//Select2 snippet to allow hiding of selected elements
		$( "select" ).on( "select2:select", function( evt ) {
			var element  = evt.params.data.element;
			var $element = $( element );
			$element.detach();
			$( this ).append( $element );
			$( this ).trigger( "change" );
		});
		
		//Force number and decimals only
		$('.numbers-only').keyup(function(){
			var val = $(this).val();
			if(isNaN(val)){
				 val = val.replace(/[^0-9\.]/g,'');
				 if(val.split('.').length>2) 
					 val =val.replace(/\.+$/,"");
			}
			$(this).val(val); 
		});
		
		$('.filters-toggle').click(function(){
			$( '#filters-container' ).slideToggle();
			$( ".top_audit .filters-toggle, .top_site .filters-toggle, .top_asset .filters-toggle, .top_people .filters-toggle" ).toggleClass( "filters_open" );
		});
		
		$( '.clear-filters' ).click(function(){
			$('.filters-container').find('input[type=checkbox]:checked').removeAttr('checked');
			//location.reload();
		});
		
		$('.datepicker').datetimepicker({
			timepicker:false,
			format:'d-m-Y',
			scrollInput : false
		});
		
		$('.datetimepicker').datetimepicker({
			timepicker:true,
			format:'d-m-Y H:i',
			scrollInput : false
		});

		//Redirect user to previous page when they click Go-back
		window.goBack = function (e){
			var homeLocation = "<?php echo base_url('webapp/home'); ?>";
			var oldHash = window.location.hash;

			history.back(); // Try to go back

			var newHash = window.location.hash;

			if( newHash === oldHash && ( typeof(document.referrer ) !== "string" || document.referrer  === "" )	){
				window.setTimeout(function(){
					// redirect to default location
					window.location.href = homeLocation;
				},1000); // set timeout in ms
			}
			
			if(e){
				if( e.preventDefault )
					e.preventDefault();
				if( e.preventPropagation )
					e.preventPropagation();
			}
			return false; // stop event propagation and browser default event
		}
		
		$(".sensitive-data").focus(function(){
			this.type = "text";
		}).blur(function(){
			this.type = "password";
		});
		
		//LIVE TIME TICKER
		clockUpdate();
		setInterval(clockUpdate, 1000);
		
	});
	
	function ucwords ( str ) {
		return ( str.replace(/[^a-z0-9\s]/gi, ' ').replace(/[_\s]/g, ' ') + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
			return $1.toUpperCase();
		});
	}

	//Used for document upload
	$(function() {
		$('input[type=file]').change(function(){
			var t = $(this).val();
			var labelText = 'File : ' + t.substr(12, t.length);
			$(this).prev('label').text(labelText);
		})
	});
	
	//Prevent illegal form submissions
	$('.no-permissions').click( function( e ){
		e.preventDefault();
		swal({
			type: 'error',
			title: '<small>You do not have sufficient permissions to complete this action!</small>'
		})
		return false;
	});
	
	function numberWithCommas(number) {
		var parts = number.toString().split(".");
		parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		return parts.join(".");
	}
	
	//Escape Special Chars
	var entityMap = {
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#39;',
		'/': '&#x2F;',
		'`': '&#x60;',
		'=': '&#x3D;'
	};

	function escapeHtml( string ) {
		return String(string).replace(/[&<>"'`=\/]/g, function (s) {
			return entityMap[s];
		});
	}
	

	function clockUpdate() {
		var date = new Date();
		$('.digital-clock').css({'color': '#fff', 'text-shadow': '0 0 6px #ff0'});
		function addZero(x) {
			if (x < 10) {
				return x = '0' + x;
			} else {
				return x;
			}
		}

		function twelveHour(x) {
			if (x > 12) {
				return x = x - 12;
			} else if (x == 0) {
				return x = 12;
			} else {
				return x;
			}
		}

		var h = addZero(twelveHour(date.getHours()));
		var m = addZero(date.getMinutes());
		var s = addZero(date.getSeconds());

		$('.time_now').text(h + ':' + m + ':' + s)
	}
	
	/**
	 * Displays overlay with "Please wait" text. Based on bootstrap modal. Contains animated progress bar.
	 */
	function showPleaseWait() {
		
		if ( document.querySelector( "#pleaseWaitDialog") == null ) {
			var modalLoading = '<div class="modal" id="pleaseWaitDialog" data-backdrop="static" data-keyboard="false" role="dialog">\
				<div class="modal-dialog modal-vertical-centered">\
					<div class="modal-content">\
						<div class="modal-body" style="min-height: 40px;">\
							<h4 class="modal-title">Please wait...</h4>\
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
