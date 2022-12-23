<form id="setting-creation-form">
    <div class="row">
        <div class="setting_creation_panel1 col-lg-12 col-md-12 col-sm-12 col-xs-12" data-panel-index="0">
            <div class="slide-group">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <legend class="legend-header">Setting Details</legend>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <h6 class="error_message pull-right" id="setting_creation_panel1-errors"></h6>
                    </div>
                </div>
                
                <div class="input-group form-group container-full">
                    <label class="input-group-addon el-hidden">Setting Module</label>
                    <?php if (!empty($module_access)) { ?>
                        <select name="module_id" class="input-field-full container-full" title="Module">
                            <option value="">Please select</option>
                            <?php foreach ($module_access as $module) {
                                if (in_array($module->module_id, $allowed_modules)) { ?>
                                    <option value="<?php echo (!empty($module->module_id)) ? $module->module_id : '' ; ?>" <?php echo (!empty($module->module_id) && (!empty($module_id)) && ($module->module_id == $module_id)) ? ('selected = "selected"') : '' ; ?>><?php echo $module->module_name; ?></option>
                                    <?php
                                }
                            } ?>
                        </select>
                    <?php } ?>
                </div>

                <div class="input-group form-group container-full">
                    <?php
                    if (!empty($setting_names)) { ?>
                        <select name="setting_name_id" class="input-field-full container-full resetable setting-trigger" title="Setting Name ID">
                            <option value="">Please select</option>
                            <?php
                            foreach ($setting_names as $row) { ?>
                                <option value="<?php echo $row->setting_name_id; ?>"><?php echo $row->setting_name; ?></option>
                                <?php
                            } ?>
                            <option value="" class="new_setting" data-trigger="yes"> + Add new Setting Name</option>
                        </select>
                        <?php
                    } else { ?>
                        <select class="input-field-full container-full resetable  setting-trigger" title="Setting Name">
                            <option value="">Please select</option>
                            <option value="" class="new_setting" data-trigger="yes"> + Add new Setting Name</option>
                        </select>
                        <?php
                    } ?>
                </div>
                    
                <div class="new-setting-name el-hidden">
                    <div class="input-group container-full">
                        <label class="input-group-addon el-hidden">Setting Name</label>
                        <input name="setting_name" class="input-field-full container-full" type="text" value="" placeholder="Setting Name" title="Setting Name" />
                    </div>
                    
                    <div class="input-group container-full">
                        <label class="input-group-addon el-hidden">Setting Name Description</label>
                        <input name="setting_name_desc" class="input-field-full container-full" type="text" value="" placeholder="Setting Name Description" title="Setting Name Description" />
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-6 col-lg-offset-6 col-md-6 col-md-offset-6 col-sm-6 col-sm-offset-6 col-xs-12">
                        <button class="btn-block btn-next setting-creation-steps check-reference-button" data-currentpanel="setting_creation_panel1" type="button">Next</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="setting_creation_panel2 col-lg-12 col-md-12 col-sm-12 col-xs-12 el-hidden" data-panel-index="1">
            <div class="slide-group">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <legend class="legend-header">What are the Values?</legend>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <h6 class="error_message pull-right" id="setting_creation_panel2-errors"></h6>
                    </div>
                </div>

                <div class="value_attributes">
                    <div class="input-group" style="float: left; width: 60%;">
                        <label class="input-group-addon el-hidden">Setting Value</label>
                        <input name="value[0][item_value]" class="input-field-full container-full" type="text" value="" placeholder="Setting Value" title="Setting Value" />
                    </div>

                    <div class="input-group" style="float: right; width: 25%;">
                        <label class="input-group-addon el-hidden">Value Order</label>
                        <select name="value[0][setting_order]" class="input-field-full container-full resetable" title="Value Order">
                            <option value="">Order</option>
                            <?php
                            for ($i = 1; $i < 49; $i++) { ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php
                            } ?>
                        </select>
                    </div>

                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">Value Description</label>
                        <input name="value[0][value_desc]" class="input-field-full container-full required required" type="text" value="" placeholder="Value Description" title="Value Description" />
                    </div>
                </div>
                
                <div id="outputArea">
                </div>
                
                <div class="add_another_attribute"><a class=""><i class="fas fa-plus-circle"></i> Add Value</a></div>

                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <button class="btn-block btn-back" data-currentpanel="setting_creation_panel2" type="button">Back</button>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <button id="create-setting-btn" class="btn-block btn-flow btn-next" type="submit">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>