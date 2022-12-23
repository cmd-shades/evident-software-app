<div style="font-family: Arial, Helvetica, sans-serif; margin-top: 10px;">
    <p style="text-align: right;; color:     #696969;"><?php echo ( !empty($document_setup['generic_details']['document_date']) ) ? $document_setup['generic_details']['document_date'] : date('d jS Y'); ?></p><br>
    <?php if($document_setup['recipient_details']['show_recipient'] == true ) { ?>
        <div style="color:     #696969;">
            <text><?php echo ( !empty($document_setup['recipient_details']['address_line1']) ) ? ucwords(strtolower($document_setup['recipient_details']['address_line1'])).'<br>' : ''; ?></text>
            <text><?php echo ( !empty($document_setup['recipient_details']['address_line2']) ) ? ucwords(strtolower($document_setup['recipient_details']['address_line2'])).'<br>' : ''; ?></text>
            <text><?php echo ( !empty($document_setup['recipient_details']['address_line3']) ) ? ucwords(strtolower($document_setup['recipient_details']['address_line3'])).'<br>' : ''; ?></text>
            <text><?php echo ( !empty($document_setup['recipient_details']['address_town']) ) ? ucwords(strtolower($document_setup['recipient_details']['address_town'])).'<br>' : ''; ?></text>
            <text><?php echo ( !empty($document_setup['recipient_details']['address_postcode']) ) ? strtoupper($document_setup['recipient_details']['address_postcode']).'<br>' : ''; ?></text>
        </div>    
    <?php } ?>
    
    <?php if (!empty($document_setup['generic_details']['document_name']) ) { ?>
        <span style="text-align: center; font-size: 20px; color: #F89C1C;"><?php echo $document_setup['generic_details']['document_name']; ?></span><br/>
    <?php } ?>
    
    <div style="color:     #696969;">
        <?php echo ( !empty($document_setup['document_content']) ) ? $document_setup['document_content'] : 'PDF Error! No content was provided'; ?>
    </div>
</div>