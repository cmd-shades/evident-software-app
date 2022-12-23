<style type="text/css">
span.restriction-item{
    padding-right: 20px;
    display: inline-block;
    float: left;
}
</style>

<div class="modal-header">
    <div class="row">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
            <h4 class="modal-title"><span class="site_name_in_modal"><?php echo (!empty($site_details->site_name)) ? $site_details->site_name : '' ; ?></span> (ID: <span class="site_name_in_modal"><?php echo (!empty($site_details->site_id)) ? $site_details->site_id : '' ; ?> )</span></h4>
        </div>
    </div>
</div>

<div class="modal-body">
    <div class="row group-content">
        <form id="update-site-form">
            <input type="hidden" name="site_id" value="<?php echo (!empty($site_details->site_id)) ? $site_details->site_id : '' ; ?>" />
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label-40">Site Name</label>
                    <input class="input-field-60" name="site_details[site_name]" type="text" placeholder="Site Name" value="<?php echo (!empty($site_details->site_name)) ? $site_details->site_name : '' ; ?>" />
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label-40">Address</label>
                    <textarea class="input-field-60" name="address_details[fulladdress]" type="text" placeholder="Address"><?php echo (!empty($site_details->fulladdress)) ? $site_details->fulladdress : '' ; ?></textarea>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label-40">Country</label>
                    <?php
                    if (!empty($territories)) { ?>
                    <select name="address_details[site_territory_id]" class="input-field-60">
                        <option value="">Please select</option>
                        <?php
                        foreach ($territories as $row) { ?>
                            <option value="<?php echo $row->territory_id; ?>" <?php echo((!empty($site_details->site_territory_id) && ($site_details->site_territory_id == $row->territory_id)) ? 'selected="selected"' : ''); ?>><?php echo (!empty($row->country)) ? $row->country : '' ; ?></option>
                            <?php
                        } ?>
                    </select>
                        <?php
                    } ?>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label-40">Contact Name</label>
                    <input class="input-field-60" name="contact_details[contact_full_name]" type="text" placeholder="Contact Name" value="<?php echo (!empty($site_details->contact_full_name)) ? $site_details->contact_full_name : '' ; ?>" />
                </div>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label-40">Contact Number</label>
                    <input class="input-field-60" name="contact_details[telephone_number]" type="text" placeholder="Contact Number" value="<?php echo (!empty($site_details->telephone_number)) ? $site_details->telephone_number : '' ; ?>" />
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label-40">Contact Email</label>
                    <input class="input-field-60" name="contact_details[email]" type="text" placeholder="Contact Email" value="<?php echo (!empty($site_details->email)) ? $site_details->email : '' ; ?>" />
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label-40">Contact Skype</label>
                    <input class="input-field-60" name="contact_details[skype]" type="text" placeholder="Contact Skype" value="<?php echo (!empty($site_details->skype)) ? $site_details->skype : '' ; ?>" />
                </div>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label-40">Site Notes</label>
                    <textarea class="input-field-60" name="site_details[site_notes]" type="text" placeholder="Site Notes"><?php echo ($site_details->site_notes) ? ($site_details->site_notes) : '' ; ?></textarea>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label-40">Distribution Group</label>
                    <?php
                    if (!empty($distribution_groups)) { ?>
                        <select name="site_details[distribution_group_id]" class="input-field-60">
                            <option value="">Please select</option>
                            <?php foreach ($distribution_groups as $row) {?>
                                <option value="<?php echo (!empty($row->setting_id)) ? $row->setting_id : ''; ?>" title="<?php echo (!empty($row->value_desc)) ? $row->value_desc : '' ?>" <?php echo((!empty($site_details->distribution_group_id) && ($site_details->distribution_group_id == $row->setting_id)) ? 'selected="selected"' : ''); ?>><?php echo (!empty($row->setting_value)) ? $row->setting_value : '' ;
                                echo (!empty($row->country)) ? '(' . ucfirst($row->country) . ')' : '' ?> </option>
                            <?php } ?>
                        </select>
                        <?php
                    } else { ?>
                        <select name="site_details[distribution_group_id]" class="input-field-60">
                            <option value="">No Distribution Group for this Territory in settings</option>
                        </select>
                        <?php
                    } ?>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label-40">Content Territory</label>
                    <?php
                    if (!empty($territories)) { ?>
                        <select name="site_details[content_territory_id]" class="input-field-60">
                            <option value="">Please select</option>
                            <?php
                            foreach ($territories as $row) { ?>
                                <option value="<?php echo $row->territory_id; ?>" <?php echo((!empty($site_details->content_territory_id) && ($site_details->content_territory_id == $row->territory_id)) ? 'selected="selected"' : ''); ?>><?php echo (!empty($row->country)) ? $row->country : '' ; ?></option>
                                <?php
                            } ?>
                        </select>
                        <?php
                    } ?>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label-40">Time Zone</label>
                    <?php
                    if (!empty($time_zones)) { ?>
                        <select name="site_details[time_zone_id]" class="input-field-60">
                            <option value="">Please select</option>
                            <?php
                            foreach ($time_zones as $key => $row) { ?>
                                <option value="<?php echo $row->time_zone_id; ?>" <?php echo((!empty($site_details->time_zone_id) && ($site_details->time_zone_id == $row->time_zone_id)) ? 'selected="selected"' : ''); ?>><?php echo (!empty($row->tz_db_name)) ? $row->tz_db_name : '' ; ?></option>
                                <?php
                            } ?>
                        </select>
                        <?php
                    } ?>
                </div>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label-40">Contract Signed</label>
                    <select name="site_details[is_signed]" class="input-field-60">
                        <option value="">Please select</option>
                        <option value="yes" <?php echo((!empty($site_details->is_signed) && ($site_details->is_signed == true)) ? 'selected="selected"' : ''); ?>>Yes</option>
                        <option value="no" <?php echo((empty($site_details->is_signed) || ($site_details->is_signed != true)) ? 'selected="selected"' : ''); ?>>No</option>
                    </select>
                </div>
                

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label-40">Invoice Currency</label>
                    <select name="site_details[invoice_currency_id]" class="input-field-60">
                        <option value="">Invoice Currency</option>
                        <?php
                        if (!empty($invoice_currencies)) { ?>
                            <?php foreach ($invoice_currencies as $row) {?>
                                <option value="<?php echo (!empty($row->setting_id)) ? $row->setting_id : ''; ?>" title="<?php echo (!empty($row->value_desc)) ? $row->value_desc : '' ?>" <?php echo((!empty($site_details->invoice_currency_id) && ($site_details->invoice_currency_id == $row->setting_id)) ? 'selected="selected"' : ''); ?>><?php echo (!empty($row->setting_value)) ? $row->setting_value : '' ?></option>
                            <?php }
                            } ?>
                    </select>
                </div>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label-40">Invoice To</label>
                    <?php
                    if (!empty($invoice_to)) { ?>
                        <select name="site_details[invoice_to]" id="invoice_to" class="input-field-60">
                            <option value="">Please select</option>
                            <?php foreach ($invoice_to as $inv_to_row) { ?>
                                <option value="<?php echo $inv_to_row; ?>" title="<?php echo ucwords($inv_to_row); ?>" <?php echo((!empty($site_details->invoice_to) && (strtolower($site_details->invoice_to) == strtolower($inv_to_row))) ? 'selected="selected"' : ''); ?>><?php echo (!empty($inv_to_row)) ? ucwords($inv_to_row) : '' ?></option>
                            <?php } ?>
                        </select>
                        <?php
                    } ?>
                </div>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label-40">Charge Frequency</label>
                    <select name="site_details[charge_frequency_id]" id="charge_frequency" class="input-field-60">
                        <?php
                        if (!empty($charge_frequencies)) { ?>
                            <option value="">Please select</option>
                            <?php foreach ($charge_frequencies as $inv_freq_row) { ?>
                                <option value="<?php echo $inv_freq_row->setting_id; ?>" title="<?php echo ucwords($inv_freq_row->setting_value); ?>" <?php echo((!empty($site_details->charge_frequency_id) && (($site_details->charge_frequency_id) == ($inv_freq_row->setting_id))) ? 'selected="selected"' : ''); ?>><?php echo (!empty($inv_freq_row->setting_value)) ? ucwords($inv_freq_row->setting_value) : '' ?></option>
                            <?php } ?>
                            <?php
                        } ?>
                    </select>
                </div>
    
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label-40">System Integrator</label>
                    <?php
                    if (!empty($system_integrators)) { ?>
                        <select id="select_system_integrator_id"  class="input-field-60">
                            <option value="">Please select</option>
                            <?php foreach ($system_integrators as $si_row) {?>
                                <option value="<?php echo (!empty($si_row->system_integrator_id)) ? $si_row->system_integrator_id : ''; ?>" title="<?php echo (!empty($si_row->integrator_name)) ? $si_row->integrator_name : '' ?>" <?php echo((!empty($site_details->system_integrator_id) && ($site_details->system_integrator_id == $si_row->system_integrator_id)) ? 'selected="selected"' : ''); ?>><?php echo (!empty($si_row->integrator_name)) ? $si_row->integrator_name : '' ?></option>
                            <?php } ?>
                        </select>
                        <input type="hidden" id="original_system_integrator_id" value="<?php echo (!empty($site_details->system_integrator_id)) ? $site_details->system_integrator_id : '' ; ?>" />
                        <input type="hidden" name="site_details[system_integrator_id]" value="" />
                        <?php
                    } ?>
                </div>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label-40">Operating Company</label>
                    <?php
                    if (!empty($operating_companies)) { ?>
                        <select name="site_details[operating_company_id]" id="select_operating_company_id" class="input-field-60">
                            <option value="">Please select</option>
                            <?php foreach ($operating_companies as $oc_row) {?>
                                <option value="<?php echo (!empty($oc_row->system_integrator_id)) ? $oc_row->system_integrator_id : ''; ?>" title="<?php echo (!empty($oc_row->integrator_name)) ? $oc_row->integrator_name : '' ?>" <?php echo((!empty($site_details->operating_company_id) && ($site_details->operating_company_id == $oc_row->system_integrator_id)) ? 'selected="selected"' : ''); ?>><?php echo (!empty($oc_row->integrator_name)) ? $oc_row->integrator_name : '' ?></option>
                            <?php } ?>
                        </select>
                        <?php
                    } ?>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label-40">System Type</label>
                    <?php
                    if (!empty($systems)) { ?>
                        <select name="site_details[system_type_id]" class="input-field-60">
                            <option value="">Please select</option>
                            <?php foreach ($systems as $row) {?>
                                <option value="<?php echo (!empty($row->system_type_id)) ? $row->system_type_id : ''; ?>" title="<?php echo (!empty($row->name)) ? $row->name : '' ?>" <?php echo((!empty($site_details->system_type_id) && ($site_details->system_type_id == $row->system_type_id)) ? 'selected="selected"' : ''); ?>><?php echo (!empty($row->name)) ? $row->name : '' ?></option>
                            <?php } ?>
                        </select>
                        <?php
                    } ?>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label-40">Number of Rooms</label>
                    <input class="input-field-60" name="site_details[number_of_rooms]" type="text" placeholder="Number of Rooms" value="<?php echo (!empty($site_details->number_of_rooms)) ? $site_details->number_of_rooms : '' ; ?>">
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label-40">Restrictions/Age Rating</label>
                    <?php if (!empty($age_rating)) { ?>
                            <div class="restriction_row input-field-60" style="height: auto;">
                            <?php foreach ($age_rating as $key => $row) { ?>
                                <span class="restriction-item" style="padding-right: 20px;">
                                    <input name="site_details[restrictions][]" type="checkbox" id="restrictions_<?php echo (!empty($row->age_rating_id)) ? $row->age_rating_id : '' ; ?>" value="<?php echo (!empty($row->age_rating_id)) ? $row->age_rating_id : '' ; ?>" title="<?php echo (!empty($row->age_rating_desc)) ? $row->age_rating_desc : '' ; ?>" <?php echo (!empty($row->age_rating_id) && !empty($site_details->site_restrictions) && (in_array($row->age_rating_id, $site_details->site_restrictions))) ? 'checked="checked"' : "" ; ?> />
                                    <label for="restrictions_<?php echo (!empty($row->age_rating_id)) ? $row->age_rating_id : '' ; ?>"><?php echo (!empty($row->age_rating_name)) ? $row->age_rating_name : '' ; ?></label>
                                </span>
                            <?php  } ?>
                            </div>
                    <?php } ?>
                </div>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="input-label-40">Override Device Flag</label>
                    <select name="site_details[is_device_flag_overriden]" id="invoice_to" class="input-field-60">
                        <option value="">Please select</option>
                        <option value="1" <?php echo((!empty($site_details->is_device_flag_overriden) && (($site_details->is_device_flag_overriden) == true)) ? 'selected="selected"' : ''); ?>>Yes</option>
                        <option value="0" <?php echo((empty($site_details->is_device_flag_overriden) || (($site_details->is_device_flag_overriden) != true)) ? 'selected="selected"' : ''); ?>>No</option>
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