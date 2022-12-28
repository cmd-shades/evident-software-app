<style type="text/css">
	body {
		background-color: #FFFFFF;
	}
	.table > thead > tr > th {
		cursor:pointer;
	}
</style>

<style>
	#sortable1, #sortable2, #sortable3 {
		list-style-type: none; 
		margin: 0; 
		float: left; margin-right: 10px; 
		background: #eee; 
		padding: 5px; width: 143px;
	}
	
	#sortable1 li, #sortable2 li, #sortable3 li {
		margin: 5px; 
		padding: 5px; 
		font-size: 1.2em; 
		width: 120px; 
	}
</style>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<br/>
		<div class="x_panel has-shadow">
			<div class="x_content">
				<legend>System Reports</legend>
				<div class="row">
					<div class="col-md-4 col-sm-4 col-xs-12">
						<h4>All Fileds</h4>
						<ul id="sortable1" class="droptrue">
							<li class="ui-state-default hide pointer">Can be dropped..</li>
							<li class="ui-state-default pointer">Item 3</li>
							<li class="ui-state-default pointer">Item 4</li>
							<li class="ui-state-default pointer">Item 5</li>
						</ul>
					</div>	
					
					<div class="col-md-4 col-sm-4 col-xs-12">
						<form class="form-horizontal" action="<?php echo base_url('/webapp/report/reports/' ); ?>" method="post">
							<input type="text" name="account_id" value="<?php echo $this->account_id; ?>" />
							<input type="text" name="report_type" value="" />
							<h4>Your report Fileds</h4>
							<ul id="sortable2" class="dropfalse">
								<!-- <li class="ui-state-highlight">Cant be dropped..</li> 
								<li class="ui-state-highlight">Item 3</li>
								<li class="ui-state-highlight">Item 4</li>
								<li class="ui-state-highlight">Item 5</li>-->
							</ul>
						</form>
					</div>

					<div class="col-md-4 col-sm-4 col-xs-12">
						<ul id="sortable3" class="droptrue"></ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>

	//Draggable + Sortable
	$( function() {
		$( "ul.droptrue" ).sortable({
			connectWith: "ul"
		});

		$( "ul.dropfalse" ).sortable({
			connectWith: "ul",
			dropOnEmpty: false
		});

		$( "#sortable1, #sortable2, #sortable3" ).disableSelection();
	} );

	$(document).ready( function(){
		$('.report-type').click(function() {
			$('.report-type').not(this).prop('checked', false);
			
			var reportType = $(this).val();
			
			$('[name="report_type"]').val( reportType );
			
			$('.report-container').not(this).hide();
			$('.report-'+reportType ).show();

			$( '.columns, .check-all' ).each(function(){
				$(this).prop( 'checked', false );
			});
		});
		
		$( '.check-all' ).change(function(){
			var grpId = $(this).attr( 'id' );
			if( $(this).is(':checked') ){
				$( '.'+grpId ).each(function(){
					$(this).prop( 'checked', true );
				});				
			}else{
				$( '.'+grpId ).each(function(){
					$(this).prop( 'checked', false );
				});
			}
		});
		
		$('.columns').change(function(){
			
			var groupClass  = $(this).data( 'group_class' );
			var chkCount 	= 0;
			var totalChekd 	= 0;
			var unChekd		= 0;
			
			$( '.'+groupClass ).each(function(){
				chkCount++;
				if( $(this).is(':checked') ){
					totalChekd++;					
				}else{
					unChekd++;
				}
			});

			if( chkCount > 0 && ( chkCount == totalChekd ) ){
				$( '#'+groupClass ).prop( 'checked', true );
			}else{
				$( '#'+groupClass ).prop( 'checked', false );
			};
		});
		
	});
</script>