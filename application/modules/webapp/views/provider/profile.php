<div id="provider-profile" class="row">
    <div class="x_panel">
        <div class="x_provider">
            <div class="profile-details-container">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <h2 class="profile-header">Provider Profile <?php echo (!empty($provider_details->provider_id)) ? " (ID: " . $provider_details->provider_id . ")" : '' ; ?></h2><?php if (!empty($provider_details)) {
                            ?><div class="delete_container"><a href="#"><i class="fas fa-trash-alt"></i></a></div><?php
                        } ?>
                    </div>
                </div>
                <?php
                if (!empty($provider_details)) { ?>
                    <div class="row records-bar panel-primary" role="alert">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="row">
                                <div class="row profile_view">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <table style="width:100%;">
                                            <tr>
                                                <td width="30%"><i class="hide fa fa-user"></i> <label>Provider Name</label></td>
                                                <td width="60%" title="<?php echo $provider_details->provider_name; ?>"><?php echo (!empty($provider_details->provider_name)) ? ucwords($provider_details->provider_name) : '' ; ?></td>
                                            </tr>
                                            <tr>
                                                <td width="30%"><i class="hide fa fa-user"></i> <label>Provider Description</label></td>
                                                <td width="60%" title="<?php echo $provider_details->provider_description; ?>"><?php echo (!empty($provider_details->provider_description)) ? ($provider_details->provider_description) : '' ; ?></td>
                                            </tr>
                                            <tr class="el-hidden">
                                                <td width="30%"><label>Provider Group</label></td>
                                                <td width="60%" title="<?php echo $provider_details->provider_group; ?>"><?php echo (!empty($provider_details->provider_group)) ? ucwords($provider_details->provider_group) : '' ; ?></td>
                                            </tr>
                                            <tr>
                                                <td width="30%"><label>Provider Category</label></td>
                                                <td width="60%" title="<?php echo $provider_details->provider_category_name; ?>"><?php echo (!empty($provider_details->provider_category_name)) ? ucwords($provider_details->provider_category_name) : '' ; ?></td>
                                            </tr>
                                            <tr>
                                                <td width="30%"><label>Provider Reference Code</label></td>
                                                <td width="60%" title="<?php echo $provider_details->provider_reference_code; ?>"><?php echo (!empty($provider_details->provider_reference_code)) ? ucwords($provider_details->provider_reference_code) : '' ; ?></td>
                                            </tr>

                                            <?php if (!empty($provider_details->provider_group_name) && ($provider_details->provider_group_name == "channel")) { ?>
                                                <tr>
                                                    <td width="30%"><label>Provider URL</label></td>
                                                    <td width="60%" title="<?php echo $provider_details->provider_url; ?>"><?php echo (!empty($provider_details->provider_url)) ? html_escape($provider_details->provider_url) : '' ; ?></td>
                                                </tr>
                                            <?php } ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="row">
                                <table class="full-width">
                                    <tr>
                                        <td width="30%"><i class="hide fa fa-at text-bold"></i> <label>&nbsp;</label></td>
                                        <td width="60%">&nbsp;</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php include $include_page; ?>
                    </div>
                    <?php
                } else { ?>
                    <div class="row">
                        <span><?php echo $this->config->item('no_records'); ?></span>
                    </div>
                    <?php
                } ?>
            </div>
        </div>
    </div>
</div>