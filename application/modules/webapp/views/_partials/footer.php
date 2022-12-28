						</div>
					</div>
				</div>
			</div>
			<!-- /.content-wrapper -->
			<?php if( !in_array( $_SERVER['REMOTE_ADDR'], array("127.0.0.1", "::1") ) ){ ?>
				<footer class="main-footer">
					<div class="container-fluid">
							<div class="pull-right hidden-xs">
								<small><b>Evident Version</b> 1.0.0</small>
							</div>
					</div>
				</footer>
			<?php } ?>
		</div>

		<!-- JS files -->
		<script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/jquery.slimscroll.min.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/fastclick.min.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('assets/js/adminlte.min.js'); ?>" type="text/javascript"></script>
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
		<script src="<?php echo base_url('assets/js/icheck.min.js'); ?>" type="text/javascript"></script>
		
	</body>
</html>

<script>
	$(document).ready(function(){
		
		$('.filters-toggle').click(function(){
			$( '#filters-container' ).slideToggle();
		});
		
		$( '.clear-filters' ).click(function(){
			$('.filters-container').find('input[type=checkbox]:checked').removeAttr('checked');
			//location.reload();
		});
		
		$('.datepicker').datetimepicker({
			timepicker:false,
			format:'d-m-Y'
		});
		
		$('.datetimepicker').datetimepicker({
			timepicker:true,
			format:'d-m-Y'
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
</script>
