<style>
    table tr th,table tr td{
        padding: 5px 0; 
    }
    
    @media (max-width: 480px) {
        .btn-info{
            margin-bottom:10px;
        }
    }
    
</style>

<div class="row">
    <div class="x_content">
        <div class="row">
            <?php if (!empty($user_details)) { ?>
            <div class="alert alert-ssid records-bar" role="alert">
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="row">
                        <legend>Personal Info</legend>
                        <div class="rows">
                            <div class="row profile_view">
                                <div class="row col-sm-12">
                                    <div class="right col-xs-12">
                                        <table style="width:100%;">
                                            <tr>
                                                <td width="40%"><i class="hide fa fa-user"></i> <label>Full Name</label></td>
                                                <td><?php echo ucwords($user_details->first_name); ?> <?php echo ucwords($user_details->last_name); ?></td>
                                            </tr>
                                            <tr class="hide">
                                                <td width="40%"><i class="hide fa fa-briefcase"></i> <label>Employee ID</label></td>
                                                <td width="60%"><?php echo $user_details->account_user_id; ?></td>
                                            </tr>
                                            <tr>
                                                <td width="40%"><i class="hide fa fa-at text-bold"></i> <label>Email</label></td>
                                                <td width="60%"><?php echo strtolower($user_details->email); ?></td>
                                            </tr>
                                            <tr>
                                                <td width="40%"><i class="hide fa fa-phone"></i> <label>Mobile</label></td>
                                                <td width="60%"><?php echo (!empty($user_details->phone)) ? $user_details->phone : '- - -  - - - '; ?></td>
                                            </tr>
                                            <tr>
                                                <td width="40%"><i class="hide fa fa-lock"></i> <label>Username</label></td>
                                                <td width="60%"><?php echo $user_details->username; ?></td>
                                            </tr>                                               
                                        </table>
                                    </div>      
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="rows">
                        <legend>Address</legend>                        
                        <table style="width:100%;">
                            <tr>
                                <td>Details</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="hide col-md-4 col-sm-4 col-xs-12">
                    <div class="rows">
                        <legend>Address</legend>
                        <table style="width:100%;">
                            <tr>
                                <td>Details</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="alert alert-ssid" role="alert">
                <div class="row">
                    <div class="col-md-2 col-sm-3 col-xs-4">
                        <a class="btn btn-sm btn-info btn-block <?php echo ($this->uri->segment(4) == 'manage_lead' || !$this->uri->segment(4)) ? 'active' : '' ?>" href="<?php echo base_url("lms/lead_details/" . $this->uri->segment(3) . "/manage_lead"); ?>" role="button">Manage <span class="hidden-xs">Lead</span></a>
                    </div>
                    <div class="col-md-2 col-sm-3 col-xs-4">
                        <a class="btn btn-sm btn-info btn-block <?php echo ($this->uri->segment(4) == 'communication') ? 'active' : '' ?>" href="<?php echo base_url("lms/lead_details/" . $this->uri->segment(3) . "/communication"); ?>" role="button">Comm<span class="hidden-xs">unication</span></a>
                    </div>
                    <div class="col-md-2 col-sm-3 col-xs-4">
                        <a class="btn btn-sm btn-info btn-block <?php echo ($this->uri->segment(4) == 'payments') ? 'active' : '' ?>" href="<?php echo base_url("lms/lead_details/" . $this->uri->segment(3) . "/payments"); ?>" role="button">Payments</a>
                    </div>
                    <div class="col-md-2 col-sm-3 col-xs-4">
                        <a class="btn btn-sm btn-info btn-block <?php echo ($this->uri->segment(4) == 'packages') ? 'active' : '' ?>" href="<?php echo base_url("lms/lead_details/" . $this->uri->segment(3) . "/packages"); ?>" role="button"><span class="hidden-xs">Manage </span>Packages</a>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            
                <?php include('site_permissions.php'); ?>

            <?php } else { ?>
                <span><?php echo $this->config->item('no_records'); ?></span>
            <?php } ?>
        </div>
    </div>
</div>

