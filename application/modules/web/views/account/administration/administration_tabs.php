<div id="ev-menu" class="infini-content"></div>

<script>

	administration_tabs = []
	base_url = '<?php echo $tab_base; ?>'

	$.each( <?php echo json_encode($account_admin_tabs); ?>, function(i, admin_tab) {
		var full_Url_Link = base_url+admin_tab.link_tab;
		administration_tabs.push({text : admin_tab.text, link : full_Url_Link, selected_tab: ( '<?php echo $page; ?>'.toLowerCase() == admin_tab.link_tab.toLowerCase()) })
	});

	$( document ).ready(function() {
		infi_scroll = new infiScroll( administration_tabs , 6, document.getElementById( "ev-menu" ) )
	});

</script>