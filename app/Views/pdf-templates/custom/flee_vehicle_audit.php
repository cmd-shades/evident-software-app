<div style="font-size:92%">
    <table class="generals_table" cellpadding="4" cellspacing="0" border="0" style="margin-top:20px; font-size:92%">
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
    <h3 style="font-size:90%" class="title">AUDIT RESPONSES</h3>
    <hr>
    <h3 style="font-size:90%" class="title">&nbsp;</h3>
    <table cellpadding="5" style="border:1px solid #696969; font-size:92%" >
        <tr>
            <td width="10%" style="border:1px solid #696969"><strong>ID</strong></td>
            <td width="50%" style="border:1px solid #696969"><b>Question</b></td>
            <td width="20%" style="border:1px solid #696969; text-align:center"><b><span style="text-align:center">Response</span></b></td>
            <td width="20%" style="border:1px solid #696969; text-align:center"><b><span style="text-align:center">Extra Info</span></b></td>
        </tr>
        <?php 
        foreach( $document_setup['document_content']->audit_responses as $key => $row ){ $key++; ?>
            <tr>
                <td width="5%" style="border:1px solid #696969"><?php echo 'Q'.$key.'.'; ?></td>
                <td width="55%" style="border:1px solid #696969"><?php echo $row->question; ?></td>
                <td width="20%" style="border:1px solid #696969"><span style="text-align:center"><?php echo $row->response; ?></span></td>
                <td width="20%" style="border:1px solid #696969"><span style="text-align:center"><?php echo $row->response_extra; ?></span></td>
            </tr>
            <?php 
        } ?>
    </table>
</div>