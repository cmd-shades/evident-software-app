
<?php 
	$module_item_id					= false;
	$postdata["account_id"] 		= $this->user->account_id;
	$postdata["module_id"] 			= $this->module_id;
	$postdata["module_item_name"] 	= $active_tab;
	$API_call	 	  				= $this->webapp_service->api_dispatcher( $this->api_end_point.'access/module_items', $postdata, ['auth_token'=>$this->auth_token], true );
	$module_id						= $postdata["module_id"];
	$module_item					= ( !empty( $API_call->module_items ) ) ? $API_call->module_items : null;
	$module_item_id					= $module_item[0]->module_item_id;
	
?>

<script src="<?php echo base_url( "assets/js/custom/infiscroll.js" ); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url("assets/css/custom/infiscroll.css"); ?>">

<div class="infini-menu">

    <button onclick="infi_scroll.prev()" class="infi-button ev-shadow infini-btn-prev"><i class="fas fa-caret-left"></i></button>

    <div class="infini-content" id="ev-infin-menu"></div>

    <button onclick="infi_scroll.next()" class="infi-button ev-shadow infini-btn-next"><i class="fas fa-caret-right"></i></button>

</div>

<?php

$scrollArray = array();


if(!empty($unordered_tabs)){
    foreach( $unordered_tabs as $k => $module ){
        $shadow =  ( $active_tab == $module->module_item_tab );
        array_push($scrollArray, array("text" => ucwords( $module->module_item_name ), "link" => base_url("webapp/".$this->router->fetch_class()."/profile/".$this->uri->segment(4)."/".$module->module_item_tab ), "shadow" => !$shadow));
    }

} else if( !empty( $module_tabs ) ) {

    foreach( $module_tabs as $k => $module ){
        $shadow = true;
        
        if(array_key_exists("module_item_tab", $module )){
            array_push($scrollArray, array("text" => ucwords( $module->module_item_name ), "link" => base_url("webapp/".$this->router->fetch_class()."/profile/".$this->uri->segment(4)."/".$module->module_item_tab ), "shadow" => !$shadow));
        }
        
        //array_push($scrollArray, array("text" => ucwords( $module->module_item_name ), "link" => base_url("webapp/".$this->router->fetch_class()."/profile/".$this->uri->segment(4)."/".$module->module_item_tab ), "shadow" => !$shadow));
    }

} else  if( !empty( $module ) && is_array( $module ) ){
        $shadow =  ( $active_tab == $module->module_item_tab );
        array_push($scrollArray, array("text" => ucwords( $more_module->module_item_name ), "link" => bbase_url("webapp/".$this->router->fetch_class()."/profile/".$this->uri->segment(4)."/".$more_module->module_item_tab ), "shadow" => !$shadow));
}

?>

<script>

    $( document ).ready(function() {

        infi_scroll = new infiScroll( <?php echo json_encode($scrollArray); ?>, 6, document.getElementById("ev-infin-menu"))

        $("#ev-infin-menu").on('click','.scroll-el',function(){
            window.location.href = $(this).attr("data-link");
        });

    });

</script>

