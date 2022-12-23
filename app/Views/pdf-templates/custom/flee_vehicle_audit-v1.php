<table class="generals_table" cellpadding="4" cellspacing="0" border="0" style="margin-top:20px">
    <tr>
        <td width="30%" align="left"><b>Audit ID</b></td>
        <td width="70%" align="left"><?php echo ( !empty($document_setup['document_content']->audit_id) ) ? ( $document_setup['document_content']->audit_id ) : '' ; ?></td>
    </tr>
    <tr>
        <td width="30%" align="left"><b>Audit Type</b></td>
        <td width="70%" align="left"><?php echo ( !empty($document_setup['document_content']->audit_type) ) ? ( $document_setup['document_content']->audit_type ) : '' ; ?></td>
    </tr>
    <tr>
        <td width="30%" align="left"><b>Audit Date</b></td>
        <td width="70%" align="left"><?php echo ( !empty($document_setup['document_content']->date_created) ) ? ( $document_setup['document_content']->date_created ) : '' ; ?></td>
    </tr>
    <tr>
        <td width="30%" align="left"><b>Audited By</b></td>
        <td width="70%" align="left"><?php echo ( !empty($document_setup['document_content']->created_by) ) ? ( $document_setup['document_content']->created_by ) : '' ; ?></td>
    </tr>
    <tr>
        <td width="30%" align="left"><b>Questions Completed</b></td>
        <td width="70%" align="left"><?php echo ( !empty($document_setup['document_content']->questions_completed) && ( $document_setup['document_content']->questions_completed == true )  ) ? 'Yes' : '' ; ?></td>
    </tr>
    <tr>
        <td width="30%" align="left"><b>Documents Uploaded</b></td>
        <td width="70%" align="left"><?php echo ( !empty($document_setup['document_content']->documents_uploaded) && ( $document_setup['document_content']->documents_uploaded == true )  ) ? 'Yes' : '' ; ?></td>
    </tr>
    <tr>
        <td width="30%" align="left"><b>Signature Uploaded</b></td>
        <td width="70%" align="left"><?php echo ( !empty($document_setup['document_content']->signature_uploaded) && ( $document_setup['document_content']->signature_uploaded == true )  ) ? 'Yes' : '' ; ?></td>
    </tr>
</table>
<br>
<h3 class="title">AUDIT RESPONSES</h3>
<hr>
<h3 class="title">&nbsp;</h3>
<table cellpadding="4" cellspacing="6" style="border:0.5px solid #ccc" >
    <tr>
        <td width="5%" style="border:0.5px solid #ccc"><strong>ID</strong></td>
        <td width="80%" style="border:0.5px solid #ccc"><b>Question</b></td>
        <td width="15%" style="border:0.5px solid #ccc"><b><span style="text-align:center">Response</span></b></td>
    </tr>
    <?php 
    foreach( $document_setup['document_content']->audit_responses as $key => $row ){ $key++; ?>
        <tr>
            <td width="5%" style="border:0.5px solid #ccc"><?php echo 'Q'.$key.'.'; ?></td>
            <td width="80%" style="border:0.5px solid #ccc"><?php echo $row->question; ?></td>
            <td width="15%" style="border:0.5px solid #ccc"><span style="text-align:center"><?php echo $row->response; ?></span></td>
        </tr>
        <?php 
    } ?>
</table>
<br>
<br>
<br>

<h3 class="title">&nbsp;</h3>
<h3 class="title">&nbsp;</h3>
<h3 class="title">&nbsp;</h3>
<h3 class="title">&nbsp;</h3>
<h3 class="title">AUDIT PHOTOS </h3>
<h3 class="title">&nbsp;</h3>
<table>
    <tr rowspan="4">
        <td>
            <table>
            <?php if($document_setup['document_content']->uploaded_docs ) { ?>
                <?php foreach( $document_setup['document_content']->uploaded_docs  as $segment => $segment_files ){?>
                        <tr>
                            <td width="100%" style="padding:10px;" colspan="3">
                    <?php foreach( $segment_files  as $q => $file ){ ?>
                                        <img src="<?php echo $file->document_link ?>" width="100%" height="300px" />
                                        <em><strong>Doc name:</strong> <?php echo $file->document_name; ?></em>
                    <?php } ?>
                                
                            </td>
                        </tr>
                <?php } ?>
            <?php } ?>
            </table>
        </td>
    </tr>
</table>