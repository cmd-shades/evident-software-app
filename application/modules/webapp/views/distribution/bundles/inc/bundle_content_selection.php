<style>
	select[multiple], select[size] {
		min-height: 300px;
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
	
	select[multiple], select[size] {
		padding: 6px 6px;
	}

	option {
		font-weight: normal;
		min-height: 1.4em;
		padding: 4px 4px;
	}
	
	#activeContent .content-films{
		
	}
	
	#activeContent .current-film{
		background: #99cc99;
		background-color: #99cc99;
	}
	
	#activeContent .latest-film{
		background: #a5c4f2;
		background-color: #a5c4f2;
	}
	
	#activeContent .library-film-red{
		background: #ff9999;
		background-color: #ff9999;
	}
	
	#activeContent .library-film-orange{
		background: #ff9966;
		background-color: ff9966;
	}
		
	#activeContent .missing-file-data{
		background: #a2a2a2;
		background-color: #a2a2a2;
	}
</style>

<table class="content-selection" width="100%" align="center">  
	<tr>  
		<td width="45%" align="center">
			<h5 class="text-bold" align="left">Available Films</h5>
			<select id="activeContent" multiple="multiple" class="form-control available-content" style="font-size:90%" >
				<?php if( !empty( $available_content ) ){ foreach( $available_content as $content ){ ?>
					<?php $missing_file_data = empty( $content->film_attributes->codec_definition ) ? true : false; ?>
				
					<option value="<?php echo $content->content_id; ?>" class="<?php echo !empty( $content->film_attributes->content_group_class ) ? ( !empty( $missing_file_data ) ? 'missing-file-data' : $content->film_attributes->content_group_class ) : ''; ?>" <?php echo !empty( $missing_file_data ) ? 'disabled' : ''; ?> ><?php echo date( 'd/m/Y', strtotime( $content->clearance_date ) ); ?> - <?php echo !empty( $content->provider_name ) ? ucwords( $content->provider_name ) : '';?> - <?php echo $content->title; ?> ( <?php echo strtoupper( $content->age_rating_name ); ?> <?php echo !empty( $content->film_attributes->codec_definition ) ? $content->film_attributes->codec_definition : '';?> <?php echo !empty( $content->film_attributes->content_languages ) ? implode( ' ', object_to_array( $content->film_attributes->content_languages ) ) : ''; ?> )</option>
				<?php } }else{ ?>
					<option disabled >No content available</option>
				<?php }  ?>
			</select> 
		</td>
		<td width="10%" align="center" >
			<h5 align="left" >&nbsp;</h5>
			<div style="width:50%" >
				<div><button class="btn move-btn-sm move-btn move-btn-add" title="Move selected item to the right" type="button" id="move-content-right" value=">" > <strong ><i class="fas fa-chevron-right"></i></strong> </button></div><br/>
				<div><button class="btn move-btn-sm move-btn move-btn-add" title="Move selected items to the right" type="button" id="move-content-rightall" value=">>" > <strong><i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right"></i></strong> </button></div><br/>
				<div><button class="btn move-btn-sm move-btn move-btn-remove" title="Move selected item to the left" type="button" id="move-content-left" value="<" > <strong><i class="fas fa-chevron-left"></i></strong> </button></div><br/>
				<div><button class="btn move-btn-sm move-btn move-btn-remove" title="Move selected items to the left" type="button" id="move-content-leftall" value="<<" > <strong><i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left"></i></strong> </button></div><br/>
			<div>
		</td>  
		<td width="45%" align="center">
			<h5 class="text-bold" align="left" >Selected Films</h5>
			<select id="selectedContent" name="bundle_content[]" multiple="multiple" class="form-control already-sent-content" style="font-size:90%" >
				
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
				$( origin ).find( ':selected' ).appendTo( dest );
			}
			 
			function moveAllItems( origin, dest ) {
				
				$( origin ).children( ':not(:disabled)' ).appendTo( dest );
				
				$( '#activeContent option' ).prop( 'selected', false );
				$( '#selectedContent option' ).prop( 'selected', true );
				
			}
			
			$( '#activeContent option' ).prop( 'selected', false );
			$( '#selectedContent option' ).prop( 'selected', true );
			
			//FILM SELECTION
			$('#move-content-left').click(function () {
				moveItems( '#selectedContent', '#activeContent' );
			});
			 
			$('#move-content-right').on('click', function () {
				moveItems( '#activeContent', '#selectedContent');
			});
			 
			$('#move-content-leftall').on('click', function () {
				moveAllItems( '#selectedContent', '#activeContent' );
			});
			 
			$('#move-content-rightall').on('click', function () {
				moveAllItems( '#activeContent', '#selectedContent' );
			});
		});
		
    </script>
	