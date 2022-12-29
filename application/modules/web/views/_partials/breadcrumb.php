<?php defined('BASEPATH') or exit('No direct script access allowed');?>

    <style>

        #breadcrums a {
            text-transform: capitalize;
            font-weight: 100;
        }

        #breadcrums a:last-child {
          color: gray;
        }

        #breadcrums .seperator:last-of-type {
            color: gray !important;
        }

        #breadcrums {
            color: #0092CD;
        }
        
        #breadcrums .breadcrum-previous {
            margin-right: 5px;
        }
        
    </style>

    <div id="breadcrums"></div>
	
	<?php
        $requestSource 	= '';
$referer 		= (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : false;
;
if (!empty($referer)) {
    if (strpos(strtolower($referer), 'webapp/config') !== false) {
        $requestSource = 'config';
    }
}
?>

    <script>
        var uriSegments     =   <?php echo json_encode(array_values($this->uri->segment_array())); ?>;
        var baseUrl         =   '<?php echo base_url(); ?>';
        var breadcrum       =   "";
        var crumSeperator   =   " <span class='seperator' style='color:#0092CD;'>&nbsp;<small><i class='fas fa-angle-double-right'></i></small>&nbsp;</span> ";
        var requestSource	= 	'<?php echo $requestSource; ?>'
		
        function getPageType( uriSegments ){
            if( uriSegments.length < 3 ){
                return 'index';
            } if( uriSegments.length == 3 ){
                if( uriSegments[ 2 ].toLowerCase() == "index" ){ return 'index'; }
                controllerName = ( uriSegments[ 1 ][uriSegments[ 1 ].length - 1 ].toLowerCase() == 's') ? uriSegments[ 1 ].substring( 0, uriSegments[ 1 ].length - 1 ).toLowerCase() : uriSegments[ 1 ].toLowerCase()
                functionName = ( uriSegments[ 2 ][uriSegments[ 2 ].length - 1 ].toLowerCase() == 's') ? uriSegments[ 2 ].substring( 0, uriSegments[ 2 ].length - 1 ).toLowerCase() : uriSegments[ 2 ].toLowerCase()
                
                if( controllerName == functionName ){ return 'module_list'; }
                if( controllerName == 'fleet' && functionName == 'vehicle' ){ return 'module_list'; }
            }
            return 'module_profile';
        }
        
        
        function breadCrumReplace(originalTag){
            originalTag = originalTag.replace('audit', 'EviDoc')
            originalTag = originalTag.replace('site', 'Building')
            return originalTag
        }
        
        var pageType = getPageType( uriSegments )
                
        switch (pageType) {
            default:
            case 'index':
                breadcrum += "<a href='" + baseUrl + "'> Home</a>"
                break;
            case 'module_list':
                breadcrum += "<i style='color:#0092CD;margin-right:5px;' class='fas fa-arrow-circle-left breadcrum-previous pointer'></i><a href='" + (baseUrl + uriSegments[ 0 ]) + "'></a> <a href='" + baseUrl + "' style='color:#0092CD;'> Home</a> " + crumSeperator
                breadcrum += "<a href='" + (baseUrl + uriSegments[ 0 ] +"/" +  uriSegments[ 1 ]) + "'>" + breadCrumReplace(uriSegments[ 1 ].replace(/_/gi, " ")) + "</a>";
                break;
            case 'module_profile':
				
				var uriSegments1 = ( requestSource.length > 0 ) ? requestSource : uriSegments[ 1 ]; 
				
                breadcrum += "<i style='color:#0092CD;' class='fas fa-arrow-circle-left breadcrum-previous pointer'></i><a href='" + baseUrl + "' style='color:#0092CD;'> Home</a> " + crumSeperator
                breadcrum += "<a href='" + ( baseUrl + uriSegments[ 0 ] +"/" +  uriSegments1 ) + "'>" + breadCrumReplace( uriSegments1.replace( /_/gi, " " ) ) + "</a> " + crumSeperator;
                
                profileTab = ( uriSegments.length > 4 ) ? uriSegments[ 4 ] : uriSegments[ 2 ];
                
                breadcrum += "<a href='" + (baseUrl + uriSegments[ 0 ] +"/" +  uriSegments[ 1 ]) + "/" + uriSegments[ 2 ] + "'>" + /* or 4 */breadCrumReplace(profileTab.replace(/_/gi, " ")) + "</a>";
                break;
        }
        
        $( "#breadcrums" ).append( breadcrum )
        
        $("#breadcrums").find(".breadcrum-previous").on('click', function(event) {
            window.history.back()
        })
        
    </script>