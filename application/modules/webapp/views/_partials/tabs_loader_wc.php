<?php 
	$module_item_id					= false;
	$postdata["account_id"] 		= $this->user->account_id;
	$postdata["module_id"] 			= $this->module_id;
	$postdata["module_item_name"] 	= $active_tab;
	$API_call	 	  				= $this->webapp_service->api_dispatcher( $this->api_end_point.'access/module_items', $postdata, ['auth_token'=>$this->auth_token], true );
	$module_item					= ( !empty( $API_call->module_items ) ) ? $API_call->module_items : null;
	$module_item_id					= $module_item[0]->module_item_id;
	
?>


<style type="text/css">
/* override position and transform in 3.3.x */

.carousel-inner{
	width: calc( 100% - 100px );
	margin: auto;
}

.carousel-inner .item.left.active {
	transform: translateX(-100%);
}
.carousel-inner .item.right.active {
	transform: translateX(100%);
}

.carousel-inner .item.next {
	transform: translateX(16.67%)
}
.carousel-inner .item.prev {
	transform: translateX(-16.67%)
}

.carousel-inner .item.right,
.carousel-inner .item.left {
	transform: translateX(100%);
}
/*  .carousel-control.left,.carousel-control.right {background:yellow;} */


.carousel-control{
	width: 30px;
    display: block;
    background: #0092CD;
    min-height: 1px;
    height: 30px;
	font-size: 22px;
}
.carousel-control > i{
	color: #f7cf00;
}
</style>



<script>
$( '.carousel .item' ).each( function(){
	var next = $( this ).next();
		if( !next.length ){
		next = $( this ).siblings( ':first' );
	}
	next.children( ':first-child' ).clone().appendTo( $( this ) );

	if( next.next().length>0 ){
		next.next().children( ':first-child' ).clone().appendTo( $( this ) );
	} else {
		$( this ).siblings( ':first' ).children( ':first-child' ).clone().appendTo( $( this ) );
	}
});
</script>


<?php if( !empty( $unordered_tabs ) ){  ##  tabs taken from the server - not ordered and not packed into array ?>
	<div class="" style="width: 100%;float: left;display: block;padding-bottom: 15px;margin: 0 10px;">
		<?php if( strtolower( $active_tab ) == "attributes" ){?>
			<div id="manage_attributes_link" style="width: 50px;display: block; float: left;">
				<div><a href="#" data-toggle="modal" data-target="#manage_attr_modal"><img style="width: auto; height: 30px;" src="<?php echo  base_url( "assets/images/backgrounds/edit-attribute.jpg" ); ?>" /></div>
			</div>
			<div id="myCarousel" class="carousel slide" data-interval="false" style="display: block; float: left; width: calc( 100% - 70px )">
		<?php } else { ?>
			<div id="myCarousel" class="carousel slide" data-interval="false" style="display: block; float: left; width: calc( 100% - 20px );">
		<?php } ?>
																 
				<?php $i = 0; ?>
					<div class="carousel-inner <?php  echo  $i; ?>">
				<?php foreach( $unordered_tabs as $k => $module ){ ?>
					<?php if( $i % 6 == 0 ){
					?>
						<div class="item <?php echo ( $i == 0 ) ? "active" : "" ;?>">
							<div class="row">
					<?php } ?>
					<!-- Carousel items -->
						<div class="i_<?php echo $i; ?> col-sm-2">
							<a class="btn btn-sm btn-<?php echo $module_identier; ?> btn-block <?php echo ( $active_tab != $module->module_item_tab ) ? 'shadow-'.$module_identier : ''; ?>" href="<?php echo base_url("webapp/".$this->router->fetch_class()."/profile/".$this->uri->segment(4)."/".$module->module_item_tab ); ?>" role="button"><?php echo ucwords( $module->module_item_name ); ?></a>
						</div>
					<?php if( ( ( $i > 0 ) && ( ( $i + 1 ) % 6 == 0 ) ) || ( $module->module_item_id == $unordered_tabs[ count( $unordered_tabs )-1]->module_item_id ) ){ ?>
							</div>
							<!--/row-->
						</div>
					<?php } ?>

					<?php
					$i++; ?>
					<?php } ?>
					</div> <!-- Carousel-inner -->

			<a class="left carousel-control" href="#myCarousel" data-slide="prev"><i class="fas fa-caret-left"></i></a>
			<a class="right carousel-control" href="#myCarousel" data-slide="next"><i class="fas fa-caret-right"></i></a>
		</div>
		<!--/myCarousel-->
	</div>
<?php } else if( !empty( $module_tabs ) ) { ?>
	<div class="floating-pallet">
		<div class="row">
			<?php if( !empty( $module_tabs ) ){ foreach( $module_tabs as $k => $module ){ ?>
				<?php if( $k !== 'more' ){ ?>
					<div class="col-md-2 col-sm-2 col-xs-4">
						<a class="btn btn-sm btn-<?php echo $module_identier; ?> btn-block <?php echo ( $active_tab != $module->module_item_tab ) ? 'shadow-'.$module_identier : ''; ?>" href="<?php echo base_url("webapp/".$this->router->fetch_class()."/profile/".$this->uri->segment(4)."/".$module->module_item_tab ); ?>" role="button"><?php echo ucwords( $module->module_item_name ); ?></a>
					</div>
				<?php } else { ?>

					<div class="col-md-2 col-sm-2 col-xs-4" id="more-modules" >
						<a class="btn btn-sm btn-<?php echo $module_identier; ?> btn-block" ><?php echo ucwords( $k ); ?> &nbsp;<i class="caret-icon fas fa-caret-down"></i></a>
					</div>
					<?php if( !empty( $module ) && is_array( $module ) ){ ?>
						<div class="more-modules" style="display:<?php echo ( !empty( $more_list_active ) ) ? 'block' : 'none' ?>; border: 1px solid transparent;" >
							<?php foreach( $module as $key => $more_module ){ ?>
								<div class="col-md-2 col-sm-2 col-xs-4" style="padding-top:15px">
									<a class="btn btn-sm btn-<?php echo $module_identier; ?> btn-block <?php echo ( $active_tab != $more_module->module_item_tab ) ? 'shadow-'.$module_identier : ''; ?>" href="<?php echo base_url("webapp/".$this->router->fetch_class()."/profile/".$this->uri->segment(4)."/".$more_module->module_item_tab ); ?>" role="button"><?php echo ucwords( $more_module->module_item_name ); ?></a>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
				<?php } ?>

			<?php } }else{ ?>
				<div class="col-md-12 col-sm-12 col-xs-12 text-red"><em>The module items for this module have not been setup yet. Please contact your system admin</em></div>
			<?php } ?>
		</div>
		<div class="clear"></div>
	</div>

<?php } else { ?>
	<div class="col-md-12 col-sm-12 col-xs-12 text-red"><em>The module items for this module have not been setup yet. Please contact your system admin</em></div>
<?php } ?>


<!-- Modal -->
<div id="manage_attr_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Manage Attr Modal</h4>
		</div>
		<div class="modal-body">
			<div class="edit_delete_attributes">
				<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#create_attr" data-dismiss="modal">Create Attribute</button>
				<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#edit_attr" data-dismiss="modal">Edit Attribute</button>
				<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#delete_attr" data-dismiss="modal">Delete Attribute</button>
			</div>
		</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>



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

					<?php if( $this->user->is_admin && $show_information == 1 ){ ?>
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

					<?php if( !empty( $attribute_sections ) ){ ?>
						<div class="input-group form-group">
							<label class="input-group-addon">Section</label>
							<select name="attr_data[section_id]" class="form-control">
								<option value="">Please select</option>
								<?php
								$i = 0;
								foreach( $attribute_sections as $row ){ ?>
									<option value="<?php echo $row->section_id; ?>" <?php echo ( $i < 1 ) ? 'selected="selected"' : "" ; ?> ><?php echo $row->section_name; ?></option>
								<?php $i++; } ?>
							</select>
						</div>
					<?php } else { ?>
						<input name="attr_data[section_id]" class="form-control" type="hidden" value="1" />
					<?php } ?>

					<?php if( !empty( $attribute_groups->{ $module_id }->{ $module_item_id } ) ){ 
						$i=0;
						$groups_within_section = $attribute_groups->{ $module_id }->{ $module_item_id };
								foreach( $groups_within_section as $section_id => $groups ){ ?>
									<div class="input-group form-group section_groups" id="section_id_<?php echo $section_id; ?>" style="<?php echo ( $i < 1 ) ? "display: table;" : "display: none;" ?>">
										<label class="input-group-addon">Group</label>
										<select class="form-control group_id_selects">
											<option value="">Please select</option>
											<?php foreach( $groups as $row ){ ?>
												<option value="<?php echo $row->group_id; ?>"><?php echo $row->group_name; ?></option>
											<?php } ?>
										</select>
									</div>
						<?php	$i++; } ?>
						<input type="hidden" name="attr_data[group_id]" value="" />
					<?php } else { ?>
						<input name="attr_data[group_id]" class="form-control" type="hidden" value="1" />
					<?php } ?>
					<!-- <h4>Or create a new one</h4> -->

					<div class="input-group form-group" style="display: none;">
						<label class="input-group-addon">Attribute Group order?</label> <?php ##  - user needs to dynamic sort ?>
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
						<?php if( !empty( $response_types ) ){ ?>
						<select name="attr_data[attribute_input_type_id]" class="form-control">
							<option value="">Please select</option>
							<?php foreach( $response_types as $row ){
									if( !in_array( $row->response_type, $excluded_response_types ) ){	?>
										<option value="<?php echo $row->response_type_id; ?>" data-response_type="<?php echo $row->response_type; ?>" <?php echo ( !empty( $row->response_type ) && ( $row->response_type == "input" ) ) ? 'selected="selected"' : "" ; ?>><?php echo $row->response_type_alt; ?></option>
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
		$( '#more-modules' ).click( function(){
			$( '.more-modules' ).slideToggle( 'slow' );
			$( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');
		});
	});
</script>