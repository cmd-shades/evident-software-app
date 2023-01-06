<style>
 .checkbox label{
	color:#f4f4f4;
 }
</style>

<div class="contract-schedule" >
	<div class="contract-details" >
		<h4>Please select the Asset types you want to created the Schedules for</h4>
		<div class="row">
			<div class="contract_schedule_panel1 col-md-12 col-sm-12 col-xs-12">
				<form class="filter-content">
					<fieldset class="filter">
						<label class="all-label" for="all">
							<input class="all" type="checkbox" id="all">All Bands
						</label>
						<label for="0">
							<input type="checkbox" id="0">Aesthetic Perfection
						</label>
						<label for="1">
							<input type="checkbox" id="1">Blutengel
						</label>
						<label for="2">
							<input type="checkbox" id="2">Cephalgy
						</label>
						<label for="3">
							<input type="checkbox" id="3">Diary of Dreams
						</label>
						<label for="4">
							<input type="checkbox" id="4">Eisbrecher
						</label>
						<label for="5">
							<input type="checkbox" id="5">In Strict Confidence
						</label>
						<label for="6">
							<input type="checkbox" id="6">Javelynn
						</label>
						<label for="7">
							<input type="checkbox" id="7">L'Âme Immortelle
						</label>
						<label for="8">
							<input type="checkbox" id="8">Metallspürhunde
						</label>
						<label for="9">
							<input type="checkbox" id="9">Unheilig
						</label>
						<label for="10">
							<input type="checkbox" id="10">Wolfsheim
						</label>
					</fieldset>
				</form>
				
				<!-- 
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="checkbox">
						<label><input checked type="checkbox" name="" value="" style="margin-top:6px;" > Asset 1</label>
					</div>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="checkbox">
						<label><input checked type="checkbox" name="" value="" style="margin-top:6px;" > Asset 2</label>
					</div>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="checkbox">
						<label><input checked type="checkbox" name="" value="" style="margin-top:6px;" > Asset 3</label>
					</div>
				</div>					
				<div class="row">
					<div class="col-md-12" >
						<button class="btn btn-success" >Next</button>
					</div>
				</div> -->
			</div>
		</div>
	</div>
</div>

<script>
	var checkbox = {
    init: function(){
		var that = this;

		$( '.filter-content .filter' ).each( function(){
		
		$parent = $( this );

		that.activeChildsByParent( $parent );

		$parent.find( 'input[type="checkbox"]:not(".all")' ).on( 'change', function(){
			var $thisParent = $( this ).closest( '.filter' );
			that.activeChildsByParent( $thisParent );
		});

		$parent.find( 'input[type="checkbox"].all' ).on( 'change', function(){
			var $thisParent = $( this ).closest( '.filter' );
			that.toggleAllChildsByParent( $thisParent );
		})

		});
	},
	toggleAllChildsByParent:function( $parent ){
			var $childs = $parent.find( 'input[type="checkbox"]:not(.all)' ),
			  stateAll = $parent.find( 'input[type="checkbox"].all' ).prop( 'checked' );

			$childs.prop( 'checked', stateAll );
			this.activeChildsByParent( $parent );
		},
		activeChildsByParent:function( $parent ){
			var $allChilds = $parent.find( 'input[type="checkbox"].all' ),
			total = $parent.find( 'input[type="checkbox"]:not(.all)' ).length,
			actives = $parent.find( 'input[type="checkbox"]:not(.all):checked').length;

			if( actives < total){
				$allChilds.prop( 'checked', false );
			}else{
				$allChilds.prop( 'checked', true );
			}
		}
	}

	$( document ).ready( function(){
	  checkbox.init();
	})
</script>