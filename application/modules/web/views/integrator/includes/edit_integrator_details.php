<div class="modal-header">
    <div class="row">
        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
            <h4 class="modal-title"><span class="integrator_name_in_modal"><?php echo (!empty($integrator_details->integrator_name)) ? $integrator_details->integrator_name : '' ; ?></span> (ID: <span class="integrator_name_in_modal"><?php echo (!empty($integrator_details->system_integrator_id)) ? $integrator_details->system_integrator_id : '' ; ?> )</span></h4>
        </div>
    </div>
</div>

<div class="modal-body">
    <div class="rows group-content">
        <form id="update-integrator-form">
            <input type="hidden" name="system_integrator_id" value="<?php echo (!empty($integrator_details->system_integrator_id)) ? $integrator_details->system_integrator_id : '' ; ?>" />
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label">Integrator Name</label>
                    <input class="input-field" name="integrator_details[integrator_name]" type="text" placeholder="Integrator Name" value="<?php echo (!empty($integrator_details->integrator_name)) ? $integrator_details->integrator_name : '' ; ?>" />
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label">Integrator Status</label>
                    <select class="input-field" name="integrator_details[integrator_status]">
                        <option value="active" <?php echo (!empty($integrator_details->integrator_status) && (strtolower($integrator_details->integrator_status) == "active")) ? 'selected="selected"' : '' ; ?>>Active</option>
                        <option value="disabled" <?php echo (empty($integrator_details->integrator_status) || (strtolower($integrator_details->integrator_status) == "disabled")) ? 'selected="selected"' : '' ; ?>>Disabled</option>
                    </select>
                </div>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label">Contact Name</label>
                    <input class="input-field" name="integrator_details[contact_name]" type="text" placeholder="Contact Name" value="<?php echo (!empty($integrator_details->contact_name)) ? $integrator_details->contact_name : '' ; ?>" />
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label">Phone Number</label>
                    <input class="input-field" name="integrator_details[integrator_phone]" type="text" placeholder="Phone Number" value="<?php echo (!empty($integrator_details->integrator_phone)) ? $integrator_details->integrator_phone : '' ; ?>" />
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label">Email</label>
                    <input class="input-field" name="integrator_details[integrator_email]" type="text" placeholder="Email" value="<?php echo (!empty($integrator_details->integrator_email)) ? $integrator_details->integrator_email : '' ; ?>" />
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label">Start Date</label>
                    <input class="input-field datetimepicker" name="integrator_details[start_date]" type="text" placeholder="Start Date" value="<?php echo (validate_date($integrator_details->start_date)) ? format_date_client($integrator_details->start_date) : '' ; ?>" />
                </div>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label">Invoice Currency</label>
                    <select name="integrator_details[invoice_currency_id]" class="input-field">
                        <option value="">Please select</option>
                        <?php
                        if (!empty($si_currencies)) { ?>
                            <?php foreach ($si_currencies as $row) {?>
                                <option value="<?php echo (!empty($row->setting_id)) ? $row->setting_id : ''; ?>" title="<?php echo (!empty($row->value_desc)) ? $row->value_desc : '' ?>" <?php echo((!empty($integrator_details->invoice_currency_id) && ($integrator_details->invoice_currency_id == $row->setting_id)) ? 'selected="selected"' : ''); ?>><?php echo (!empty($row->setting_value)) ? $row->setting_value : '' ?></option>
                            <?php } ?>
                            <?php
                        } ?>
                    </select>
                </div>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label">Contract Signed</label>
                    <select name="integrator_details[is_signed]" class="input-field">
                        <option value="">Please select</option>
                        <option value="yes" <?php echo((!empty($integrator_details->is_signed) && ($integrator_details->is_signed == true)) ? 'selected="selected"' : ''); ?>>Yes</option>
                        <option value="no" <?php echo((empty($integrator_details->is_signed) || ($integrator_details->is_signed != true)) ? 'selected="selected"' : ''); ?>>No</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <button class="btn btn-block btn-update btn-primary" type="submit" data-content_section="content">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>