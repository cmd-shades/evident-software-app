<div style="font-family: Arial, Helvetica, sans-serif; margin-top: 0px;">
    <p style="text-align: right;; color:#5C5C5C; font-size:85%;">Generated on <?php echo ( !empty($document_setup['generic_details']['document_date']) ) ? $document_setup['generic_details']['document_date'] : date('d jS Y'); ?></p><br>
    <?php if($document_setup['recipient_details']['show_recipient'] == true ) { ?>
        <div style="color:     #5C5C5C;">
            <text><?php echo ( !empty($document_setup['recipient_details']['address_line1']) ) ? ucwords(strtolower($document_setup['recipient_details']['address_line1'])).'<br>' : ''; ?></text>
            <text><?php echo ( !empty($document_setup['recipient_details']['address_line2']) ) ? ucwords(strtolower($document_setup['recipient_details']['address_line2'])).'<br>' : ''; ?></text>
            <text><?php echo ( !empty($document_setup['recipient_details']['address_line3']) ) ? ucwords(strtolower($document_setup['recipient_details']['address_line3'])).'<br>' : ''; ?></text>
            <text><?php echo ( !empty($document_setup['recipient_details']['address_town']) ) ? ucwords(strtolower($document_setup['recipient_details']['address_town'])).'<br>' : ''; ?></text>
            <text><?php echo ( !empty($document_setup['recipient_details']['address_postcode']) ) ? strtoupper($document_setup['recipient_details']['address_postcode']).'<br>' : ''; ?></text>
        </div>    
    <?php } ?>
    
    <?php if (!empty($document_setup['generic_details']['document_name']) ) { ?>
        <span style="text-align: center; font-size: 20px; color: #0092CD;"><?php echo $document_setup['generic_details']['document_name']; ?> <span style="font-size:70%"><?php echo $document_setup['generic_details']['audit_frequency']; ?></span></span><br/>
    <?php } ?>
    
    <div style="color:     #5C5C5C;">
        <?php echo ( !empty($document_setup['document_content']) ) ? $document_setup['document_content'] : 'PDF Error! No content was provided'; ?>
    </div>
</div>