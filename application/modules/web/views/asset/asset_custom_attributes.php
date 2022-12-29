<style type="text/css">
.checkbox_input [type="checkbox"]:after {
	content: attr(data-desc);
	margin: -3px 15px;
	vertical-align: top;
	display: inline-block;
	white-space: nowrap;
	cursor: pointer;
}

.checkbox_input{
	width: 100%;
	display: block;
	float: left;
	height: auto;
	padding: 4px 12px;
}

.checkbox_item{
	display: block;
	float: left;
	min-width: 200px;
}

.checkbox_input label.label-inline{
	line-height: 10px;
}

.create_attr_header{
	padding: 15px;
    float: left;
    padding-bottom: 0;
    width: 100%;
}

.attribute_creation_panel{
	padding-right: 15px;
	padding-left: 15px;
}

.modal-content{
	float: left;
	padding-bottom: 20px;
	min-height: 250px;
	min-width: 600px;
	height: auto;
}

.field_wrapper > .input-group.form-group{
	width: 95%;
}

</style>

<div class="edit_delete_attributes">
	<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#create_attr">Create Attribute</button>
	<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#edit_attr">Edit Attribute</button>
	<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#delete_attr">Delete Attribute</button>
</div>


<?php
if (!empty($manage_attributes->{ 2 })) {
    $j = 1;
    foreach ($manage_attributes->{ 2 } as $section_key => $section) { ?>  <!-- info_section_id -->
	<div class="row"> <!-- UPDATE AREA -->
		<div class="col-md-12 col-sm-12 col-xs-12" style="border: 0px solid red;">
			<div class="" style="padding-bottom: 50px;">
			<!-- <legend>Section Key: ( <?php echo $section_key; ?> )</legend> -->
			<?php foreach ($section as $group_key => $group) { ?>  <!-- group -->
				<div class="x_panel tile has-shadow accordion" >
				
					<legend class="legend" data-toggle="collapse" data-target="#collapseOne_<?php echo $j; ?>">Black with orange triangle - Group Key: ( <?php echo $group_key; ?> )</legend>
					
					
					<div style="border: 0px solid blue;" id="collapseOne_<?php echo $j; ?>" class="collapse row">
						<form id="group_<?php echo $group_key; ?>">
							<input type="hidden" name="profile_id" value="<?php echo (!empty($asset_details->asset_id)) ? ( int ) $asset_details->asset_id : '' ; ?>" />
							<input type="hidden" name="module_id" value="<?php echo (!empty($module_id)) ? ( int ) $module_id : '' ; ?>" />
							<input type="hidden" name="module_item_id" value="<?php echo (!empty($module_item_id)) ? ( int ) $module_item_id : '' ; ?>" />
							<input type="hidden" name="zone_id" value="2" />
						<?php
                            foreach ($group as $attr_key => $attr) { ?>  <!-- attributes -->

							<div class="col-lg-6 col-md-6 col-sm-6">
							<?php
                                switch($attr->response_type) {
                                    case  "select": ## select?>
									<div class="input-group form-group">
										<label class="input-group-addon attr-id-<?php echo $attr->attribute_id; ?>"><?php echo (!empty($attr->attribute_description)) ? ucwords(trim($attr->attribute_description)) : '' ?></label>
										<?php
                                                if (!empty($attr->options)) { ?>
											<select name="resp[<?php echo (!empty($attr->attribute_id)) ? (trim($attr->attribute_id)) : '' ?>]" class="form-control">
												<option value="">Please select</option>
											<?php foreach ($attr->options as $key => $row) { ?>
													<option value="<?php echo $row->option_id; ?>" <?php echo (!empty($manage_responses->{ $attr->attribute_id }->response_value) && $manage_responses->{ $attr->attribute_id }->response_value == $row->option_id) ? 'selected="selected"' : '' ; ?>><?php echo(!empty($row->option_label) ? ($row->option_label) : '--'); ?></option>
											<?php } ?>
											</select>
										<?php } else { ?>
											<input name="resp[<?php echo (!empty($attr->attribute_id)) ? (trim($attr->attribute_id)) : '' ; ?>]" class="form-control" type="" placeholder="<?php echo (!empty($attr->attribute_description)) ? ucwords(trim($attr->attribute_description)) : '' ; ?>" value="<?php  echo (!empty($manage_responses->{ $attr->attribute_id }->response_value)) ? $manage_responses->{ $attr->attribute_id }->response_value : ''; ?>">
										<?php } ?>
									</div>

								<?php
                                        break;

                                    case "radio": ## radio?>

									<div class="input-group form-group">
										<label class="input-group-addon attr-id-<?php echo $attr->attribute_id; ?>"><?php echo (!empty($attr->attribute_description)) ? ucwords(trim($attr->attribute_description)) : '' ?></label>
										<?php if (!empty($attr->options)) { ?>
											<div class="form-control checkbox_input">
											<?php foreach ($attr->options as $key => $row) { ?>
												<label class="label-inline">
													<input type="radio" name="resp[<?php echo (!empty($attr->attribute_id)) ? (trim($attr->attribute_id)) : '' ?>]" value="<?php echo (!empty($row->option_id)) ? ( int )($row->option_id) : '' ?>" <?php echo (!empty($manage_responses->{ $attr->attribute_id }->response_value) && $manage_responses->{ $attr->attribute_id }->response_value == $row->option_id) ? 'checked="checked"' : '' ; ?> /><?php echo (!empty($row->option_label)) ? ucwords(trim($row->option_label)) : '' ?>
												</label>
											<?php } ?>
											</div>
										<?php } else { ?>
											<input name="resp[<?php echo (!empty($attr->attribute_id)) ? (trim($attr->attribute_id)) : '' ; ?>]" class="form-control" type="" placeholder="<?php echo (!empty($attr->attribute_description)) ? ucwords(trim($attr->attribute_description)) : '' ; ?>" value="<?php  echo (!empty($manage_responses->{ $attr->attribute_id }->response_value)) ? $manage_responses->{ $attr->attribute_id }->response_value : ''; ?>" />
										<?php } ?>
									</div>

							<?php
                                        break;

                                    case "checkbox": ## checkbox?>

							<?php if (!empty($attr->options)) { ?>
							<?php 	$resp_array = [];
							    $resp_array	= !empty($manage_responses->{ $attr->attribute_id }->response_value) ? json_decode($manage_responses->{ $attr->attribute_id }->response_value) : false ; ?>

									<div class="input-group form-group">
										<label class="input-group-addon attr-id-<?php echo $attr->attribute_id; ?>"><?php echo (!empty($attr->attribute_description)) ? ucwords(trim($attr->attribute_description)) : '-- ' ; ?></label>
										<div class="form-control checkbox_input">
										<?php foreach ($attr->options as $key => $row) { ?>
											<div class="checkbox_item" style="width: <?php echo round(100 / count($attr->options)); ?>%;">
												<input type="hidden" name="resp[<?php echo (!empty($attr->attribute_id)) ? (trim($attr->attribute_id)) : '' ; ?>][<?php echo $row->option_id; ?>]" value="no" />
												<input type="checkbox" name="resp[<?php echo (!empty($attr->attribute_id)) ? (trim($attr->attribute_id)) : '' ; ?>][<?php echo $row->option_id; ?>]" value="yes" <?php echo (!empty($resp_array->{ $row->option_id }) && $resp_array->{ $row->option_id } == true) ? 'checked="checked"' : '' ; ?> data-desc="<?php echo (!empty($row->option_label)) ? ucwords(trim($row->option_label)) : '' ; ?>" />
											</div>
									<?php } ?>
										</div>
									</div>

							<?php } else { ?>
									<div class="input-group form-group">
										<label class="input-group-addon attr-id-<?php echo $attr->attribute_id; ?>"><?php echo (!empty($attr->attribute_description)) ? ucwords(trim($attr->attribute_description)) : '' ?></label>
										<input name="resp[<?php echo (!empty($attr->attribute_id)) ? (trim($attr->attribute_id)) : '' ; ?>]" class="form-control" type="" placeholder="<?php echo (!empty($attr->attribute_description)) ? ucwords(trim($attr->attribute_description)) : '' ; ?>" value="<?php  echo (!empty($manage_responses->{ $attr->attribute_id }->response_value)) ? $manage_responses->$section_key->$group_key->{ $attr->attribute_id }->response_value : ''; ?>" />
									</div>
							<?php }
							break;

                                    case "datetimepicker": ##datetimepicker?>
								<div class="input-group form-group">
									<label class="input-group-addon attr-id-<?php echo $attr->attribute_id; ?>"><?php echo (!empty($attr->attribute_description)) ? ucwords(trim($attr->attribute_description)) : '-- ' ; ?></label>
									<input name="resp[<?php echo (!empty($attr->attribute_id)) ? (trim($attr->attribute_id)) : '' ; ?>]" class="form-control datetimepicker" type="text" placeholder="<?php echo (!empty($manage_responses->{ $attr->attribute_id }->response_value)) ? format_date_client($manage_responses->{ $attr->attribute_id }->response_value) : date('d/m/Y H:m') ; ?>" value="<?php  echo (!empty($manage_responses->{ $attr->attribute_id }->response_value)) ? format_date_client($manage_responses->{ $attr->attribute_id }->response_value) : '' ; ?>" />
								</div>
							<?php
                                        break;

                                    case "datepicker": ## datepicker?>
								<div class="input-group form-group">
									<label class="input-group-addon attr-id-<?php echo $attr->attribute_id; ?>"><?php echo (!empty($attr->attribute_description)) ? ucwords(trim($attr->attribute_description)) : '-- ' ; ?></label>
									<input name="resp[<?php echo (!empty($attr->attribute_id)) ? (trim($attr->attribute_id)) : '' ; ?>]" class="form-control datepicker" type="text" placeholder="<?php echo (!empty($manage_responses->{ $attr->attribute_id }->response_value)) ? format_date_client($manage_responses->{ $attr->attribute_id }->response_value) : date('d/m/Y') ; ?>" value="<?php  echo (!empty($manage_responses->{ $attr->attribute_id }->response_value)) ? format_date_client($manage_responses->{ $attr->attribute_id }->response_value) : '' ; ?>" />
								</div>
							<?php
                                        break;

                                    case "textarea": ##textarea?>
								<div class="input-group form-group">
									<label class="input-group-addon attr-id-<?php echo $attr->attribute_id; ?>"><?php echo (!empty($attr->attribute_description)) ? ucwords(trim($attr->attribute_description)) : '' ; ?></label>
									<textarea name="resp[<?php echo (!empty($attr->attribute_id)) ? (trim($attr->attribute_id)) : '' ; ?>]" class="form-control" rows="3" placeholder="<?php echo (!empty($attr->attribute_description)) ? ucwords(trim($attr->attribute_description)) : '' ; ?>"><?php  echo (!empty($manage_responses->{ $attr->attribute_id }->response_value)) ? trim($manage_responses->{ $attr->attribute_id }->response_value) : ''; ?></textarea>
								</div>
							<?php
                                        break;

                                    case "email": ##email?>

							<?php
                                        break;
                                    default:
                                    case  "input":  ## text?>

								<div class="input-group form-group">
									<label class="input-group-addon attr-id-<?php echo $attr->attribute_id; ?>"><?php echo (!empty($attr->attribute_description)) ? ucwords(trim($attr->attribute_description)) : '-- ' ; ?></label>
									<input name="resp[<?php echo (!empty($attr->attribute_id)) ? (trim($attr->attribute_id)) : '' ; ?>]" class="form-control" type="text" placeholder="<?php echo (!empty($attr->attribute_description)) ? ucwords(trim($attr->attribute_description)) : '' ; ?>" value="<?php  echo (!empty($manage_responses->{ $attr->attribute_id }->response_value)) ? $manage_responses->{ $attr->attribute_id }->response_value : '' ; ?>" />
								</div>
							<?php
                                        break;
                                } ?>
						</div>
						<?php

                        $j++;
                            } ## end of attributes foreach?>

						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="row">
								<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
									<button class="update-btn btn btn-block btn-success" type="button">Update</button>
								</div>
								<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
									<button class="delete-btn btn btn-block btn-danger" type="button">Cancel</button>
								</div>
							</div>
						</div>
						</form>
					</div>
				</div>

				<?php
			} ## end of group foreach?>
			</div>
		</div>
	</div>
<?php
    }
} else { ?>
<h5>No Atrributes</h5>
<?php } ?>



<!-- Modal -->
<div id="create_attr" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="create_attr_header">
				<legend class="legend">Create an attribute</legend>
			</div>

			<form id="create_attribute_form">
				<div class="attribute_creation_panel attribute_creation_panel1 col-md-12 col-sm-12 col-xs-12">
					<div class="row section-header">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<legend class="legend-header">Attribute Label and Description (tooltip)</legend>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="attribute_creation_panel1-errors"></h6>
						</div>
					</div>
					<div class="input-group form-group">
						<label class="input-group-addon">Attribute Label:</label>
						<input id="attribute_description" name="attr_data[attribute_description]" class="form-control required" type="text" placeholder="Attribute Label (i.e. Room Colour)" value="" />
						<input name="attr_data[attribute_name]" class="form-control" type="hidden" value="" />
					</div>

					<div class="input-group form-group">
						<label class="input-group-addon">Attribute Description:</label>
						<input name="attr_data[attribute_alt_text]" class="form-control" type="text" placeholder="Attribute Description (i. e. What is the colour of the room?)" value="" />
					</div>

					<br/>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12 pull-left">
							<button class="btn btn-block btn-danger" data-dismiss="modal" type="button">Cancel</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12 pull-right">
							<button id="name_creator" class="btn btn-block btn-flow btn-success btn-next attribute-creation-steps" data-currentpanel="attribute_creation_panel1" type="button">Next</button>
						</div>
					</div>
				</div>

				<div class="attribute_creation_panel attribute_creation_panel2 col-md-12 col-sm-12 col-xs-12" style="display:none;">
					<div class="row section-header">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<legend class="legend-header">Attribute location</legend>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="attribute_creation_panel2-errors"></h6>
						</div>
					</div>

					<input name="attr_data[module_id]" class="form-control" type="hidden" value="<?php echo $module_id; ?>" />
					<input name="attr_data[module_item_id]" class="form-control" type="hidden" value="<?php echo $module_item_id; ?>" />
					<input name="attr_data[module_item_name]" class="form-control" type="hidden" value="attributes" />

					<?php if ($this->user->is_admin && $show_information == 1) { ?>
						<div class="input-group form-group">
							<label class="input-group-addon">Attribute Area</label>
							<select name="attr_data[zone_id]" class="form-control">
								<option value="">Please select</option>
								<option value="1">Information</option>
								<option value="2">Management</option>
							</select>
						</div>
					<?php } else { ?>
						<input name="attr_data[zone_id]" class="form-control" type="hidden" value="2" />
					<?php } ?>

					<?php if (!empty($attribute_sections)) { ?>
						<div class="input-group form-group">
							<label class="input-group-addon">Section</label>
							<select name="attr_data[section_id]" class="form-control">
								<option value="">Please select</option>
								<?php
                                $i = 0;
					    foreach ($attribute_sections as $row) { ?>
									<option value="<?php echo $row->section_id; ?>" <?php echo ($i < 1) ? 'selected="selected"' : "" ; ?> ><?php echo $row->section_name; ?></option>
								<?php $i++;
					    } ?>
							</select>
						</div>
					<?php } else { ?>
						<input name="attr_data[section_id]" class="form-control" type="hidden" value="1" />
					<?php } ?>

					<?php if (!empty($attribute_groups->{ $module_id }->{ $module_item_id })) {
					    $i=0;
					    $groups_within_section = $attribute_groups->{ $module_id }->{ $module_item_id };
					    foreach ($groups_within_section as $section_id => $groups) { ?>
									<div class="input-group form-group section_groups" id="section_id_<?php echo $section_id; ?>" style="<?php echo ($i < 1) ? "display: table;" : "display: none;" ?>">
										<label class="input-group-addon">Group</label>
										<select class="form-control group_id_selects">
											<option value="">Please select</option>
											<?php foreach ($groups as $row) { ?>
												<option value="<?php echo $row->group_id; ?>"><?php echo $row->group_name; ?></option>
											<?php } ?>
										</select>
									</div>
						<?php	$i++;
					    } ?>
						<input type="hidden" name="attr_data[group_id]" value="" />
					<?php } else { ?>
						<input name="attr_data[group_id]" class="form-control" type="hidden" value="1" />
					<?php } ?>
					<!-- <h4>Or create a new one</h4> -->

					<div class="input-group form-group" style="display: none;">
						<label class="input-group-addon">Attribute Group order?</label> <?php ##  - user needs to dynamic sort?>
						<input name="attr_data[group_order]" class="form-control" type="text" placeholder="Attribute order" value="" />
					</div>

					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12 pull-left">
							<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="attribute_creation_panel2" type="button">Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12 pull-right">
							<button class="btn btn-block btn-flow btn-success btn-next attribute-creation-steps" data-currentpanel="attribute_creation_panel2" type="button">Next</button>
						</div>
					</div>
				</div>

				<div class="attribute_creation_panel attribute_creation_panel3 col-md-12 col-sm-12 col-xs-12" style="display:none;">
					<div class="row section-header">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<legend class="legend-header">Attribute type</legend>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="attribute_creation_panel3-errors"></h6>
						</div>
					</div>

					<div class="input-group form-group">
						<label class="input-group-addon">Attribute type:</label>
						<?php if (!empty($response_types)) { ?>
						<select name="attr_data[attribute_input_type_id]" class="form-control">
							<option value="">Please select</option>
							<?php foreach ($response_types as $row) {
							    if (!in_array($row->response_type, $excluded_response_types)) { ?>
										<option value="<?php echo $row->response_type_id; ?>" data-response_type="<?php echo $row->response_type; ?>" <?php echo (!empty($row->response_type) && ($row->response_type == "input")) ? 'selected="selected"' : "" ; ?>><?php echo $row->response_type_alt; ?></option>
									<?php } ?>
							<?php } ?>
						</select>
						<?php } ?>
						
						<input type="hidden" name="attr_data[response_type]" value="" />
					</div>

					<div class="attribute_options" style="display: none;">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="row">
								<legend class="legend-header">Add max 10 options by using the '+'. Remove a single option using the ' - '. </legend>
								<div style="display: block; position: absolute;right: 0;z-index: 999;top: 35px;"><a href="javascript:void(0);" class="add_button" title="Add field" style="font-size: 20px; font-weight: 800;"> + </a></div>


								<!-- One single input row -->
								<div class="field_wrapper" style="width: 100%;">
									<div class="input-group form-group" style="">
										<label class="input-group-addon">Option Value</label>
										<input type="text" name="attr_data[options][][option_label]" class="form-control" value="">
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="attribute_creation_panel3" type="button">Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button id="create-attribute-btn" class="btn btn-block btn-flow btn-success btn-next" type="button">Create Attribute</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>


<!-- Modal -->
<div id="edit_attr" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Modal Header</h4>
      </div>
      <div class="modal-body">
        <p>Some text in the modal.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>


<!-- Modal -->
<div id="delete_attr" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Modal Header</h4>
      </div>
      <div class="modal-body">
        <p>Some text in the modal.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<script>
	$( document ).ready( function(){

		function isEmpty( str ) {
			return ( !str || 0 === str.length || !( jQuery.trim( str ) ) );
		}

		// dynamic options
		var x 			= 1; //Initial field counter is 1
		var maxField 	= 10; //Input fields increment limitation
		var addButton 	= $( '.add_button' ); //Add button selector
		var wrapper 	= $( '.field_wrapper' ); //Input field wrapper
		var fieldHTML 	= '<div class="field_wrapper" style="width: 100%;"><div class="input-group form-group" style=""><label class="input-group-addon">Option Value</label><input type="text" name="attr_data[options][][option_label]" class="form-control" value=""><a href="javascript:void(0);" class="remove_button" style="font-size: 20px; font-weight: 800;display: block; position: absolute;right: -27.5px;z-index: 999;top: 0px;"> - </a></div></div>';

		//Once add button is clicked
		$( addButton ).click( function(){
			//Check maximum number of input fields
			if(x < maxField){
				x++; //Increment field counter
				$(wrapper).append(fieldHTML); //Add field html
			}
		});

		//Once remove button is clicked
		$(wrapper).on('click', '.remove_button', function(e){
			e.preventDefault();
			$(this).parent('div').remove(); //Remove field html
			x--; //Decrement field counter
		});
		// dynamic options - end

		// adding option to the multiple choice input fields
		$( "*[name='attr_data[attribute_input_type_id]']" ).change( function( e ){
			e.preventDefault();

			var response_type_id = $( "*[name='attr_data[attribute_input_type_id]']" ).val();
			var response_type = $( this ).find( ':selected' ).data( 'response_type' );

			$( "*[name='attr_data[response_type]']" ).val( response_type );

			// VIR - Very Important Realisation
			var response_types_w_options = <?php echo json_encode($response_types_w_options); ?>;

			if( response_types_w_options.indexOf( response_type ) > -1 ){
				$( ".attribute_options" ).css( "display", "block" );
			} else {
				$( ".attribute_options" ).css( "display", "none" );
			}
		})
		// adding option to the multiple choice input fields - end


		// dynamic group depends on the section chosen
		var active_group_id = $( "*[ name='attr_data[section_id]']" ).val();
		var active_group = ".section_groups#section_id_" + active_group_id;
		$( active_group ).css( "display", "table" );


		$( "*[name='attr_data[section_id]']" ).change( function( e ){
			e.preventDefault;
			$( ".section_groups" ).css( "display", "none" );

			var active_group_id = $( "*[name='attr_data[section_id]']" ).val();
			if( active_group_id != "" ){
				var active_group = ".section_groups#section_id_" + active_group_id;
				$( active_group ).css( "display", "table" );
			} else {
				$( ".section_groups" ).css( "display", "table" )
			}
		});
		
		
		$( ".group_id_selects" ).change( function(){
			var group_id = $( this ).val();
			$( "*[name='attr_data[group_id]']" ).val( group_id );
		})
		// dynamic group depends on the section chosen - end


		// a label field validation
		$( "*[name='attr_data[attribute_description]']" ).change( function( e ){
			e.preventDefault();
			var label = $( "*[name='attr_data[attribute_description]'" ).val();
			check_label( label, true );
		});

		$( "#name_creator" ).click( function( e ){
			e.stopImmediatePropagation();
			var label = $( "*[name='attr_data[attribute_description]'" ).val();
			check_label( label, false );
		});

		function check_label( label, focusout ){
			
			var module_id = $( "*[name='attr_data[module_id]']" ).val();
			
			if( isEmpty( label ) ){
				swal({
					title: 'Please, provide the Attribute Label',
					type: 'error',
					confirmButtonColor: '#5CB85C',
					cancelButtonColor: '#9D1919',
				})
				return false;
			} else {
 				$.ajax({
					url:"<?php echo base_url('webapp/attribute/check_label/'); ?>",
					method: "POST",
					data: { 
						label: label,
						module_id: module_id
					},
					dataType: 'JSON',
					success: function( data ){

						if( data.status == 1 && ( data.status_msg != '' ) ){

							if( focusout == true ){
/* 								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								}); */
								$( "*[name='attr_data[attribute_name]']" ).val( data.trimmed_label );
								return true;
							} else {
								if( $( "*[name='attr_data[attribute_name]']" ).val() == data.trimmed_label ){
									panelchange( ".attribute_creation_panel1" );
									return true;
								} else {
									$( "*[name='attr_data[attribute_name]']" ).val( data.trimmed_label );
									alert( "the label is not the same" );
								}
							}
						} else {
							swal({
								type: 'error',
								title: data.status_msg,
							})
							$( "*[name='attr_data[attribute_description]'" ).focus();
							return false;
						}
					}
				});
			}
		}
		// a label field validation - end

		$( "#create-attribute-btn" ).click( function( e ){
			e.preventDefault();

			var formData = $( this ).closest( "form" ).serialize();

			swal({
				title: 'Confirm creation?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ){
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/attribute/create_attribute/'); ?>",
						method: "POST",
						data: formData,
						dataType: 'JSON',
						success:function( data ){
							if( data.status == 1 && ( data.status_msg != '' ) ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								});
								window.setTimeout( function(){
									location.href = "<?php echo base_url('webapp/asset/profile/'); ?>" + <?php echo $asset_details->asset_id; ?> + "/attributes";
								} ,500);
							} else {
								swal({
									type: 'error',
									title: data.status_msg,
								})
							}
						}
					});
				} else {
					swal({
						type: 'success',
						title: data.status_msg,
						showConfirmButton: false,
						timer: 3000
					})
					return false;
				}
			}).catch( swal.noop )
		});


		/* *********************************************** */

		$( ".attribute-creation-steps" ).click( function(){

			// Clear errors first
			$( '.error_message' ).each(function(){
				$( this ).text( '' );
			});

			var currentpanel = $( this ).data("currentpanel");
			var inputs_state = check_inputs( currentpanel );
			if( inputs_state ){
				//If name attribute returned, auto focus to the field and display arror message
				$( '[name="' + inputs_state + '"]' ).focus();
				var labelText = $( '[name="' + inputs_state + '"]' ).parent().find( 'label' ).text();
				$( '#' + currentpanel + '-errors' ).text( ucwords( labelText ) + ' is a required' );
				return false;
			}
			panelchange( "." + currentpanel )
			return false;
		});

		//** Validate any inputs that have the required class, if empty return the name attribute **/
		function check_inputs( currentpanel ){
			var result = false;
			var panel  = "." + currentpanel;

			$( $( panel + " .required" ).get().reverse() ).each( function(){
				var fieldName  = '';
				var inputValue = $( this ).val();
				if( ( inputValue == false ) || ( inputValue == '' ) || ( inputValue.length == 0 ) ){
					fieldName = $( this ).attr( 'name' );
					result    = fieldName;
					return result;
				}
			});
			return result;
		}

		$( ".btn-back" ).click( function(){
			var currentpanel = $( this ).data( "currentpanel" );
			go_back( "."+currentpanel );
			return false;
		});

		function panelchange( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
			var changeto = ".attribute_creation_panel" + panelnumber;
			$( changefrom ).hide( "slide", { direction : 'left'}, 500);
			$( changeto ).delay( 600 ).show( "slide", { direction : 'right' },500);
			return false;
		}

		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
			var changeto = ".attribute_creation_panel" + panelnumber;
			$( changefrom ).hide( "slide", {direction : 'right'}, 500 );
			$( changeto ).delay( 600 ).show( "slide", { direction : 'left' },500 );
			return false;
		}


		/* *********************************************** */

		$( ".update-btn" ).click( function( e ){
			e.preventDefault();

			var formData = $( this ).closest( "form" ).serialize();

			swal({
				title: 'Confirm update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ){
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/attribute/update_attribute_responses/'); ?>",
						method: "POST",
						data: formData,
						dataType: 'JSON',
						success:function( data ){
							if( data.status == 1 && ( data.status_msg != '' ) ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								});
								window.setTimeout( function(){
									location.href = "<?php echo base_url('webapp/asset/profile/'); ?>" + <?php echo $asset_details->asset_id; ?> + "/custom_attributes";
								} ,500);
							} else {
								swal({
									type: 'error',
									title: data.status_msg,
								})
							}
						}
					});
				} else {
					swal({
						type: 'success',
						title: data.status_msg,
						showConfirmButton: false,
						timer: 3000
					})
					return false;
				}
			}).catch( swal.noop )

		});
	});
</script>

