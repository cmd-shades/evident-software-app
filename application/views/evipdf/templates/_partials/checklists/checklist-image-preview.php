<?php 
/*
function sanitiseString($_inputString){
    $inputString = html_escape($_inputString);
    $outputString = "";
    for ($i=0; $i < strlen($inputString); $i++) {
        $outputString .= (( 1 + $i ) % 18 == 0) ? $inputString[$i] . '<br>' : $inputString[$i];
    }
    return $outputString;
}
*/

 ?>
<style>

    .upload-table {
        border-collapse: collapse;
        width: 100%;
        border: 1px solid lightgray;
    }
    
    .upload-table th, .upload-table td {
        text-align: left;
    }
    
    .image-preview {
        max-width: 300px;
        max-height: 220px;
    }
    
    .data-field {
        word-break: break-all;
        width: 100px;
    }
    
    .upload-table tr td {
        text-align:center;
    }
</style>


<?php 

    /* limits the images per line to the amount set in MAX_IMAGES_PER_LINE */

    $MAX_IMAGES_PER_LINE = 3;
    $image_count = 0;
    
    $uploadtable_html = "<table class='upload-table' border='0' style='width:100%;height:100%;' cellpadding='15px'><tr><td colspan='10'><!-- <span style='font-size:19px;border:none'>Question. " . $question_number . " - Attached Images</span><br>-->(Larger images can be found at the back of this document)</td></tr><tr>";

    echo $uploadtable_html;
	
    foreach ($response_uploads as $upload_data) { 
    
        if(($image_count + 1) % ($MAX_IMAGES_PER_LINE + 1) == 0){
            echo '</tr></table>';
            echo $uploadtable_html;
        }
		
        echo "<td style='border:none'><img class='image-preview' src='" . $upload_data->document_link . "'></td>";        
        $image_count ++;

    }
    echo "</tr></table>";

?>
    
    
