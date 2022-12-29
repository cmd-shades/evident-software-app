<style type="text/css">
.edit_delete_icons{
	position: absolute;
	right: 5px;
	top: 10px;
	z-index: 9;
}

.edit_delete_icons span{
	display: block;
    float: left;
	margin: 0 5px;
}

.edit_delete_icons span i{
	font-size: 25px;
    color: #fff;
}
</style>


<?php ## No link to the user profile?>
<div class="row alert alert-ssid" role="alert">
	<div class="edit_delete_icons">
		<span>
			<a href="#edit"><i class="fas fa-pencil-alt"></i></a>
		</span>
		<span>
			<a href="#delete"><i class="far fa-trash-alt"></i></a>
		</span>
	</div>

<?php 	if (isset($overview_attributes->{ 1 }) && !empty($overview_attributes->{ 1 })) {
    $number_of_columns = round(12 / count(( array ) $overview_attributes->{ 1 })); ## Attributes for the ZONE 1 - Overview
    foreach ($overview_attributes->{ 1 } as $section_key => $section) { 	?>
				<div class="col-md-<?php echo $number_of_columns; ?> col-sm-<?php echo $number_of_columns; ?> col-xs-12">
					<div class="row">
						<legend>Section #<?php echo $section_key; ?></legend>		<!-- SECTION TITLE from the DB !!! -->
						<div class="rows">
							<div class="row profile_view">
							<?php foreach ($section as $group_key => $group) {  ?>
								<div class="row col-sm-12">
									<div class="right col-xs-12">
										<table style="width:100%;">
									<?php 	foreach ($group as $key => $attribute) { ?>		<!-- OVERVIEW SECTION ATTRIBUTE -->
												<tr>
													<td width="30%" class="<?php echo "id_".$attribute->attribute_id; ?>">
														<label><?php echo $attribute->attribute_description ?></label>
													</td>
													
											<?php 	$attribute_response = ( object )[];
									    $attribute_response = (!empty($overview_responses->{ $attribute->attribute_id })) ? $overview_responses->{ $attribute->attribute_id } : false ;

									    if (!empty($attribute_response)) {
									        $attribute_response_value = (!empty($attribute_response->actual_response_value)) ? $attribute_response->actual_response_value : (!empty($attribute_response->response_value) ? $attribute_response->response_value : false);
									        ?>
															<td width="60%" style="color: #f5f5f5;"><?php echo (!empty($attribute_response_value)) ? $attribute_response_value : '' ; ?></td>
											<?php	} else { ?>
														<td width="60%" style="color: #f5f5f5;"></td>
											<?php 	}	?>
												</tr>
									<?php 	} ?>
										</table>
									</div>
								</div>
							<?php } ?>
							</div>
						</div>
					</div>
				</div>
	<?php 	} ?>
<?php 	} ?>
</div>