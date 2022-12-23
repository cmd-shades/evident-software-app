<div class="row group-content el-hidden">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
            <label class="input-label">Provider Name</label>
            <input class="input-field" name="provider_name" type="text" placeholder="Provider Name" title="Provider Name" value="<?php echo (!empty($provider_details->provider_name)) ? ($provider_details->provider_name) : '' ; ?>" />
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
            <label class="input-label">Provider Reference Code</label>
            <input class="input-field" name="provider_reference_code" type="text" placeholder="Provider Reference Code" title="Provider Reference Code" value="<?php echo (!empty($provider_details->provider_reference_code)) ? ($provider_details->provider_reference_code) : '' ; ?>" />
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
            <label class="input-label">Provider Description</label>
            <input class="input-field" name="provider_description" type="text" placeholder="Provider Description" title="Provider Description" value="<?php echo (!empty($provider_details->provider_description)) ? ($provider_details->provider_description) : '' ; ?>" />
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container el-hidden">
            <label class="input-label">Provider Group</label>
            <input class="input-field" name="provider_group" type="text" placeholder="Provider Group" title="Provider Group" value="<?php echo (!empty($provider_details->provider_group)) ? ($provider_details->provider_group) : '' ; ?>" />
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
            <label class="input-label">Provider Category</label>
            <?php
            if (!empty($provider_categories)) { ?>
                <select name="content_provider_category_id" class="input-field">
                    <option value="">Please select</option>
                    <?php
                    foreach ($provider_categories as $key => $row) { ?>
                        <option value="<?php echo $row->setting_id; ?>" data-setting_group_name="<?php echo (!empty($row->setting_group_name)) ? $row->setting_group_name : '' ; ?>" <?php echo (!empty($provider_details->content_provider_category_id) && ($provider_details->content_provider_category_id == $row->setting_id)) ? 'selected="selected"' : "" ; ?>><?php echo $row->setting_value; ?></option>
                        <?php
                    } ?>
                </select>
                <?php
            } else { ?>
                <input class="input-field" name="content_provider_category_id" type="text" placeholder="Provider Category ID" value="<?php echo !empty($provider_details->content_provider_category_id) ? $provider_details->content_provider_category_id : '' ; ?>" />
                <?php
            } ?>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container provider_url_container" style="display: <?php echo (!empty($provider_details->provider_group_name) && (strtolower($provider_details->provider_group_name) == "channel")) ? 'block' : 'none' ; ?>">
            <label class="input-label">Provider URL</label>
            <input class="input-field" name="provider_url" type="text" placeholder="Provider URL" title="Provider URL" value="<?php echo (!empty($provider_details->provider_url)) ? ($provider_details->provider_url) : '' ; ?>" />
        </div>

    </div>

    <?php
    if ($this->user->is_admin || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <button id="update-provider-btn" class="btn btn-block btn-update btn-primary" type="button">Update</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else { ?>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } ?>
</div>