
<style>

    .upload-table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 10px;
        border: 1px solid lightgray;
    }
    
    .upload-table th, .upload-table td {
        text-align: left;
    }

    .data-field {
        word-break: break-all;
        width: 100px;
    }
    
    .upload-table tr td {
        text-align:center;
    }
    
</style>

<table class='upload-table' border='0' style='width:100%;height:100%;' cellpadding='15px'>
    <tr>
        <td style='height:400px;width:100%;'>
            <img src="<?php echo $question_response->document_link; ?>">
			<br>
			<?php if( $image_context ){ ?>
				<small>Question <?php echo $image_context['question_number']; ?>.  <span style='font-weight:normal'><?php echo html_escape($image_context['question_content']); ?></span> Uploaded by <?php echo html_escape($question_response->uploaded_by); ?></small>
			<?php } else {?>
				<small>Uploaded by <?php echo html_escape($question_response->uploaded_by); ?></small>
			<?php } ?>			
        </td>
    </tr>
</table>

    
    
