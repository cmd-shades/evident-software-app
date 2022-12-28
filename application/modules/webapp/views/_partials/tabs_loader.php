
<?php
	$module_item_id					= false;
	$postdata["account_id"] 		= $this->user->account_id;
	$postdata["module_id"] 			= $this->module_id;
	$postdata["module_item_name"] 	= $active_tab;
	$API_call	 	  				= $this->webapp_service->api_dispatcher( $this->api_end_point.'access/module_items', $postdata, ['auth_token'=>$this->auth_token], true );
	$module_id						= $postdata["module_id"];
	$module_item					= ( !empty( $API_call->module_items ) ) ? $API_call->module_items : null;

?>

    <script src="<?php echo base_url( "assets/js/custom/infiscroll.js" ); ?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url("assets/css/custom/infiscroll.css"); ?>">

    <div id="ev-menu" class="infini-content"></div>
    <br>

<?php

$scrollArray  = array();

$profile_link = !empty( $profile_link ) ? $profile_link : 'profile';

if(!empty($unordered_tabs)){
    foreach( $unordered_tabs as $k => $module ){

        $is_selected_tab = array_key_exists( "module_item_tab", object_to_array( $module ) ) ? ( $active_tab == $module->module_item_tab ) : false;

        array_push( $scrollArray, array( "text" => ucwords( $module->module_item_name ), "link" => base_url("webapp/".$this->router->fetch_class()."/".$profile_link."/".$this->uri->segment(4)."/".$module->module_item_tab ), "selected_tab" => $is_selected_tab));

	}

} else if( !empty( $module_tabs ) ) {
    foreach( $module_tabs as $k => $module ){

		$is_selected_tab = array_key_exists( "module_item_tab", object_to_array( $module ) ) ? ( $active_tab == $module->module_item_tab ) : false;

        if(array_key_exists( "module_item_tab", object_to_array( $module ) )){
            array_push( $scrollArray, array("text" => ucwords( $module->module_item_name ), "link" => base_url("webapp/".$this->router->fetch_class()."/".$profile_link."/".$this->uri->segment(4)."/".$module->module_item_tab ), "selected_tab" => $is_selected_tab));
        }


    }

} else  if( !empty( $module ) && is_array( $module ) ){

		$is_selected_tab = array_key_exists( "module_item_tab", object_to_array( $module ) ) ? ( $active_tab == $module->module_item_tab ) : false;
        array_push($scrollArray, array( "text" => ucwords( $more_module->module_item_name ), "link" => bbase_url("webapp/".$this->router->fetch_class()."/".$profile_link."/".$this->uri->segment(4)."/".$more_module->module_item_tab ), "selected_tab" => $is_selected_tab));
}

?>

<script>

    $( document ).ready(function() {
        infi_scroll = new infiScroll( <?php echo json_encode($scrollArray); ?>, 6, document.getElementById( "ev-menu" ) )

    });

</script>
