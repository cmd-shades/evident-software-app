<div id="integrator-profile" class="row">
    <div class="x_panel">
        <div class="x_content">
            <?php
            if (!empty($integrator_details)) { ?>
                <div class="profile-details-container">
                    <div class="row">
                        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                            <h2 class="profile-header <?php echo (!empty($integrator_details->integrator_status) && (strtolower($integrator_details->integrator_status) != "active")) ? 'inactive' : "" ; ?>"><span class="text-bold"><?php echo (!empty($integrator_details->integrator_name)) ? ucwords($integrator_details->integrator_name) : '' ; ?></span><?php echo (!empty($integrator_details->system_integrator_id)) ? ' (ID: <span class="text-bold">' . $integrator_details->system_integrator_id . '</span>)' : '' ; ?></h2>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                            <div class="pull-right">
                                <span class="delete_container" title="Click to archive Integrator">
                                    <a href="#">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </span>
                                <span class="edit_container" >
                                    <a class="edit-integrator" href="#" data-toggle="modal" data-target="#editIntegrator" title="Click to edit Integrator">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </span>
                                <span class="disable_container">
                                    <a class="disable-integrator" href="#" data-toggle="modal" data-target="#disableIntegrator" title="Click to disable Integrator">
                                        <i class="fas fa-ban"></i>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row records-bar panel-primary" role="alert">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="row">
                                <div class="row profile_view">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <table style="width:100%;">
                                            <tr>
                                                <td width="30%"><i class="hide fa fa-user"></i> <label>Integrator Name</label></td>
                                                <td width="60%" title="<?php echo $integrator_details->integrator_name; ?>"><?php echo (!empty($integrator_details->integrator_name)) ? ucwords($integrator_details->integrator_name) : '' ; ?></td>
                                            </tr>
                                            <tr>
                                                <td width="30%"><i class="hide fa fa-user"></i> <label>Start Date</label></td>
                                                <td width="60%"><?php echo (validate_date($integrator_details->start_date)) ? format_date_client($integrator_details->start_date) : '' ; ?></td>
                                            </tr>
                                            <tr>
                                                <td width="30%"> <label>Invoice Currency</label></td>
                                                <td width="60%"><?php echo (!empty($integrator_details->invoice_currency_name)) ? strtoupper($integrator_details->invoice_currency_name) : '' ; ?></td>
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
                                        <td width="30%"><label>Contact Name</label></td>
                                        <td width="60%"><?php echo (!empty($integrator_details->contact_name)) ? ucfirst($integrator_details->contact_name) . " " : "" ; ?></td>
                                    </tr>

                                    <tr>
                                        <td width="30%"><label>Email</label></td>
                                        <td width="60%"><?php echo (!empty($integrator_details->integrator_email)) ? ($integrator_details->integrator_email) : "" ; ?></td>
                                    </tr>

                                    <tr>
                                        <td width="30%"><label>Phone</label></td>
                                        <td width="60%"><?php echo (!empty($integrator_details->integrator_phone)) ? ($integrator_details->integrator_phone) : "" ; ?></td>
                                    </tr>
                                    
                                    <tr>
                                        <td width="30%"><label>Integrator Status</label></td>
                                        <td width="60%"><?php echo (!empty($integrator_details->integrator_status)) ? ucwords($integrator_details->integrator_status) : "" ; ?></td>
                                    </tr>
                                    
                                    <tr>
                                        <td width="30%"><label>Contract Signed</label></td>
                                        <td width="60%"><?php echo (!empty($integrator_details->is_signed) && ($integrator_details->is_signed == true)) ? 'Yes' : 'No' ; ?></td>
                                    </tr>

                                    <?php
                                    if (!empty($integrator_details->disable_date)) { ?>
                                        <tr>
                                            <td width="30%"><label>Disable Date</label></td>
                                            <td width="60%"><?php echo (!empty($integrator_details->disable_date)) ? ucwords($integrator_details->disable_date) : "" ; ?></td>
                                        </tr>
                                        <?php
                                    } ?>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php include $include_page; ?>
                    </div>
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