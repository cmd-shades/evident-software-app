<style>
	select[multiple], select[size] {
		min-height: 200px;
	}
	
	.move-btn{
		padding: 5px 10px;
		font-size: 12px;
		line-height: 1.5;
		border-radius: 3px;
		width: 80px;
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

<legend>Site Selection</legend>
<table class="site-selection" width="100%" align="center">  
	<tr>  
		<td width="45%" align="center">
			<h5 class="text-bold" align="left" >Active Sites</h5>
			<select id="activeSites" multiple="multiple" class="form-control available-assets" >
				<option value="1">Alpha</option>
				<option value="2">Beta</option>
				<option value="3">Gamma</option>
				<option value="4">Delta</option>
				<option value="5">Epsilon</option>
				<option value="6">Zeta</option>
				<option value="7">Eta</option>
			</select> 
		</td>  
		<td width="10%" align="center" >
			<h5 align="left" >&nbsp;</h5>
			<div style="width:50%" >
				<div><button class="btn move-btn move-btn-add" title="Move selected item to the right" type="button" id="move-sites-right" value=">" > <strong ><i class="fas fa-chevron-right"></i></strong> </button></div><br/>
				<div><button class="btn move-btn move-btn-add" title="Move selected items to the right" type="button" id="move-sites-rightall" value=">>" > <strong><i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right"></i></strong> </button></div><br/>
				<div><button class="btn move-btn move-btn-remove" title="Move selected item to the left" type="button" id="move-sites-left" value="<" > <strong><i class="fas fa-chevron-left"></i></strong> </button></div><br/>
				<div><button class="btn move-btn move-btn-remove" title="Move selected items to the left" type="button" id="move-sites-leftall" value="<<" > <strong><i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left"></i></strong> </button></div><br/>
			<div>
		</td>  
		<td width="45%" align="center">
			<h5 class="text-bold" align="left" >Selected Sites</h5>
			<select id="selectedSites" multiple="multiple" class="form-control already-sent-assets" >
				
			</select> 
		</td>  
	</tr>  
	<tr>  
		<td colspan="3" height="10px"></td>  
	</tr>  
</table>
	
	
<legend>Film Selection</legend>
<table class="site-selection" width="100%" align="center">  
	<tr>  
		<td width="45%" align="center">
			<h5 class="text-bold" align="left" >Active Films</h5>
			<select id="activeFilms" multiple="multiple" class="form-control available-films" >
				<option value="1">Alpha</option>
				<option value="2">Beta</option>
				<option value="3">Gamma</option>
				<option value="4">Delta</option>
				<option value="5">Epsilon</option>
				<option value="6">Zeta</option>
				<option value="7">Eta</option>
			</select> 
		</td>  
		<td width="10%" align="center" >
			<h5 align="left" >&nbsp;</h5>
			<div style="width:50%" >
				<div><button class="btn move-btn move-btn-add" title="Move selected item to the right" type="button" id="move-films-right" value=">" > <strong ><i class="fas fa-chevron-right"></i></strong> </button></div><br/>
				<div><button class="btn move-btn move-btn-add" title="Move selected items to the right" type="button" id="move-films-rightall" value=">>" > <strong><i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right"></i></strong> </button></div><br/>
				<div><button class="btn move-btn move-btn-remove" title="Move selected item to the left" type="button" id="move-films-left" value="<" > <strong><i class="fas fa-chevron-left"></i></strong> </button></div><br/>
				<div><button class="btn move-btn move-btn-remove" title="Move selected items to the left" type="button" id="move-films-leftall" value="<<" > <strong><i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left"></i></strong> </button></div><br/>
			<div>
		</td>  
		<td width="45%" align="center">
			<h5 class="text-bold" align="left" >Selected Films</h5>
			<select id="selectedFilms" multiple="multiple" class="form-control already-sent-films" >
				
			</select> 
		</td>  
	</tr>  
	<tr>  
		<td colspan="3" height="10px"></td>  
	</tr>  
</table>

	<script type="text/javascript">
	
		$( function () { function moveItems(origin, dest) {
				$(origin).find(':selected').appendTo(dest);
			}
			 
			function moveAllItems(origin, dest) {
				$(origin).children().appendTo(dest);
			}
			
			//SITE SELECTION
			$('#move-sites-left').click(function () {
				moveItems('#selectedSites', '#activeSites');
			});
			 
			$('#move-sites-right').on('click', function () {
				moveItems('#activeSites', '#selectedSites');
			});
			 
			$('#move-sites-leftall').on('click', function () {
				moveAllItems('#selectedSites', '#activeSites');
			});
			 
			$('#move-sites-rightall').on('click', function () {
				moveAllItems('#activeSites', '#selectedSites');
			});
			
			//FILM SELECTION
			$('#move-films-left').click(function () {
				moveItems('#selectedFilms', '#activeFilms');
			});
			 
			$('#move-films-right').on('click', function () {
				moveItems('#activeFilms', '#selectedFilms');
			});
			 
			$('#move-films-leftall').on('click', function () {
				moveAllItems('#selectedFilms', '#activeFilms');
			});
			 
			$('#move-films-rightall').on('click', function () {
				moveAllItems('#activeFilms', '#selectedFilms');
			});
		});
		
    </script>
	