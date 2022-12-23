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
    <div class="x_panel no-border">
        <div class="x_content">
            <div class="profile-details-container">
                <?php if (!empty($person_details)) { ?>
                    <div class="row alert alert-ssid" role="alert">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="row">
                                <legend>User Details</legend>
                                <div class="rows">
                                    <div class="row profile_view">
                                        <div class="row col-sm-12">
                                            <div class="right col-xs-12">
                                                <table style="width:100%;">
                                                    <tr>
                                                        <td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Full name</label></td>
                                                        <td width="60%"><?php echo ucwords($person_details->first_name . ' ' . $person_details->last_name); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Status</label></td>
                                                        <td width="60%"><?php echo ucwords($person_details->status); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="30%"><i class="hide fa fa-briefcase"></i> <label>Position</label></td>
                                                        <td width="60%"><?php echo ucwords($person_details->job_title); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Department</label></td>
                                                        <td width="60%"><?php echo ucwords($person_details->department_name); ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="row">
                                <legend>Contact</legend>
                                <table style="width:100%;">
                                    <tr>
                                        <td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Email address(es)</label></td>
                                        <td width="60%"><em>Work:  </em><a href="mailto:<?php echo (!empty($person_details->work_email)) ? strtolower($person_details->work_email) : 'Not registered'; ?>"><?php echo (!empty($person_details->work_email)) ? strtolower($person_details->work_email) : 'Not registered' ; ?></a> | <em>Personal:  </em><a href="mailto:<?php echo (!empty($person_details->personal_email)) ? strtolower($person_details->personal_email) : 'Not registered' ; ?>"><?php echo (!empty($person_details->personal_email)) ? ucwords($person_details->personal_email) : 'Not registered' ; ?></a></td>
                                    </tr>
                                    <tr>
                                        <td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Telephone</label></td>
                                        <td width="60%"><em>Work: </em><a href="tel: <?php echo (!empty($person_details->work_mobile)) ? $person_details->work_mobile : '' ; ?>"><?php echo (!empty($person_details->work_mobile)) ? $person_details->work_mobile : ' Not registered' ; ?></a> | <em>Personal:  </em><a href="tel: <?php echo (!empty($person_details->personal_mobile)) ? $person_details->personal_mobile : '' ; ?>"><?php echo (!empty($person_details->personal_mobile)) ? $person_details->personal_mobile : ' Not registered' ; ?></a></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                    
                <?php   } else { ?>
                    <div class="row alert alert-ssid" role="alert">
                        <span><?php echo $this->config->item('no_data'); ?></span>
                    </div>
                    <div class="clearfix"></div>
                <?php   } ?>
                </div>
                    
                <div class="row">
                    <?php $this->load->view('webapp/_partials/tabs_loader'); ?>
                    <?php if (!empty($include_page)) {
                        include $include_page;
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>

