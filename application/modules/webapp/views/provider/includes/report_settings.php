<div class="row">
    <?php
    foreach ($royalty_settings as $key => $rs_row) { ?>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
            <label class="input-label-80"><?php echo $rs_row->setting_name; ?><?php echo (!empty($rs_row->currency)) ? '<span style="float: right;">(' . $rs_row->currency . ')</span>' : '' ; ?></label>
            <input type="hidden" name="settings[<?php echo $key; ?>][setting_id]" value="<?php echo (!empty($rs_row->setting_royalty_id)) ? ($rs_row->setting_royalty_id) : '' ; ?>" />
            <input type="text" name="settings[<?php echo $key; ?>][setting_value]" value="<?php echo (!empty($rs_row->setting_value)) ? ($rs_row->setting_value) : '' ; ?>" class="input-field-20" /> 
        </div>
        <?php
    } ?>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <?php
                if ($this->user->is_admin || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
                    <button id="update-report-settings-btn" class="btn btn-block btn-update btn-primary" type="button">Update</button>
                    <?php
                } else { ?>
                    <button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>
                    <?php
                } ?>
            </div>
        </div>
    </div>
</div>
