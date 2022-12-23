<div id="distribution-profile" class="row">
    <div class="x_panel">
        <div class="x_distribution">
            <div class="profile-details-container">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="row">
                            <h2>Distribution Group Profile <?php echo (!empty($distribution_group_details->distribution_group_id)) ? "(ID: " . $distribution_group_details->distribution_group_id . ")" : '' ; ?></h2>
                        </div>
                    </div>
                </div>
                <?php if (!empty($distribution_group_details)) { ?>
                    <div class="row records-bar panel-primary" role="alert">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="row">
                                <div class="row profile_view">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <table style="width:100%;">
                                            <tr>
                                                <td width="30%"><label>Distribution Group</label></td>
                                                <td width="60%" title="<?php echo $distribution_group_details->distribution_group; ?>"><?php echo (!empty($distribution_group_details->distribution_group)) ? ucwords($distribution_group_details->distribution_group) : '' ; ?></td>
                                            </tr>
                                            <tr>
                                                <td width="30%"><label>Distribution Group Desc</label></td>
                                                <td width="60%" title="<?php echo $distribution_group_details->distribution_group_desc; ?>"><?php echo (!empty($distribution_group_details->distribution_group_desc)) ? ($distribution_group_details->distribution_group_desc) : '' ; ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="row">
                                <table style="width:100%;">
                                    <tr>
                                        <td width="30%"><label>Date Created</label></td>
                                        <td width="60%"><?php echo (!empty($distribution_group_details->date_created)) ? $distribution_group_details->date_created : '' ; ?></td>
                                    </tr>
                                    <tr>
                                        <td width="30%"><label>Date Last Modified</label></td>
                                        <td width="60%"><?php echo (!empty($distribution_group_details->last_modified)) ? $distribution_group_details->last_modified : '' ; ?></td>
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