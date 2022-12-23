<style>
	select[multiple], select[size] {
		min-height: 200px;
	}
	
	.move-btn{
		padding: 5px 10px;
		font-size: 12px;
		line-height: 1.5;
		border-radius: 3px;
		width: 90%;
	}
	
	.move-btn-add{
		color: #fff;
		background-color: #5bc0de;
		border-color: #46b8da;
	}
	
	.move-btn-remove{
		color: #fff;
		background-color: #5bc0de;
		border-color: #46b8da;
	}
	
	select:-internal-list-box option:checked {
		
	}
	
	option {
		font-weight: normal;
		min-height: 1.4em;
		padding: 3px 2px;
	}
</style>

<table class="site-selection" width="100%" align="center" >  
	<tr>  
		<td width="45%" align="center">
			<h5 class="text-bold" align="left" >Active Sites</h5>
			<select id="activeSites"  multiple="multiple" class="form-control available-assets" style="font-size:90%" >
				<?php 
				if( !empty( $active_sites ) ){ 
					foreach( $active_sites as $site ){ 
						if( in_array( $site->site_id, array_values( $linked_sites ) ) ){ ?>
							<option value="<?php echo $site->site_id; ?>"><?php echo $site->site_name; ?></option>
				<?php 	}
					} 
				} ?>
			</select> 
		</td>  
		<td width="10%" align="center" >
			<h5 align="left" >&nbsp;</h5>
			<div style="width:50%" >
				<div><button class="btn move-btn-sm move-btn btn-sm move-btn-add" title="Move selected item to the right" type="button" id="move-sites-right" value=">" > <strong ><i class="fas fa-chevron-right"></i></strong> </button></div><br/>
				<div><button class="btn move-btn-sm move-btn move-btn-add" title="Move selected items to the right" type="button" id="move-sites-rightall" value=">>" > <strong><i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right"></i></strong> </button></div><br/>
				<div><button class="btn move-btn-sm move-btn move-btn-remove" title="Move selected item to the left" type="button" id="move-sites-left" value="<" > <strong><i class="fas fa-chevron-left"></i></strong> </button></div><br/>
				<div><button class="btn move-btn-sm move-btn move-btn-remove" title="Move selected items to the left" type="button" id="move-sites-leftall" value="<<" > <strong><i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left"></i></strong> </button></div><br/>
			<div>
		</td>  
		<td width="45%" align="center">
			<h5 class="text-bold" align="left" >Selected Sites</h5>
			<select id="selectedSites" name="bundle_sites[]" multiple="multiple" class="form-control already-sent-assets" style="font-size:90%" >
				
			</select> 
		</td>  
	</tr>  
	<tr>  
		<td colspan="3" height="10px"></td>  
	</tr>  
</table>

<hr>

<script type="text/javascript">

	$( function () { function moveItems(origin, dest) {
			$(origin).find(':selected').appendTo(dest);
		}
		 
		function moveAllItems(origin, dest) {
			
			$(origin).children().appendTo(dest);
			
			$( '#activeSites option' ).prop( 'selected', false );
			$( '#selectedSites option' ).prop( 'selected', true );
			
		}
		
		$( '#activeSites option' ).prop( 'selected', false );
		$( '#selectedSites option' ).prop( 'selected', true );
		
		//SITE SELECTION
		$('#move-sites-left').click(function () {
			moveItems('#selectedSites', '#activeSites');
		});
		 
		$('#move-sites-right').on('click', function () {
			moveItems( '#activeSites', '#selectedSites' );
			
		});
		 
		$('#move-sites-leftall').on('click', function () {
			moveAllItems('#selectedSites', '#activeSites');
		});
		 
		$('#move-sites-rightall').on('click', function () {
			moveAllItems('#activeSites', '#selectedSites');
		});
		
	});
	
</script>
	