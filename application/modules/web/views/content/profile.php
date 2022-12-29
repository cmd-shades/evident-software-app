<div id="content-profile" class="row">
    <div class="x_panel">
        <div class="x_content">
            <div class="profile-details-container">
                <?php
                if (!empty($content_details)) { ?>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <h2 class="profile-header">Content Profile <?php echo (!empty($content_details->title)) ? "(ID: " . $content_details->content_id . ")" : '' ; ?>
                                <div class="delete_container">
                                    <a href="#"><i class="fas fa-trash-alt"></i></a>
                                </div>
                            
                                <form id="generate_file" method="post" action="<?php echo base_url("webapp/content/generate_file"); ?>">
                                    <input type="hidden" name="content_id" value="<?php echo (!empty($content_details->content_id)) ? (int) $content_details->content_id : '' ; ?>" />
                                    <input id="file_type" type="hidden" name="file_type" value="" />
                                
                                    <div class="generate_file download_xml" title="Generate XML file" data-filetype="xml"><img src="<?php echo base_url("assets/images/download_xml.png"); ?>" /></div>
                                    <div class="generate_file download_json" title="Generate JSON file" data-filetype="json"><img src="<?php echo base_url("assets/images/download_json.png"); ?>" /></div> 
                                </form> 
                            </h2>
                            <!-- Using Icons - end -->
                        </div>
                    </div>
                    <div class="row records-bar panel-primary" role="alert">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="row">
                                <div class="row profile_view">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <table style="width:100%;">
                                            <tr>
                                                <td width="30%"><i class="hide fa fa-user"></i> <label>Content Title</label></td>
                                                <td width="60%" title="<?php echo $content_details->title; ?>"><?php echo (!empty($content_details->title)) ? ucwords($content_details->title) : '' ; ?></td>
                                            </tr>
                                            <tr>
                                                <td width="30%"><i class="hide fa fa-user"></i> <label>Asset Code</label></td>
                                                <td width="60%" title="<?php echo $content_details->asset_code; ?>"><?php echo (!empty($content_details->asset_code)) ? ($content_details->asset_code) : '' ; ?></td>
                                            </tr>
                                            <tr>
                                                <td width="30%"><i class="hide fa fa-briefcase"></i> <label>Provider</label></td>
                                                <td width="60%" title="<?php echo $content_details->provider_name; ?>"><?php echo (!empty($content_details->provider_name)) ? ucwords($content_details->provider_name) : '' ; ?></td>
                                            </tr>
                                            <tr>
                                                <td width="30%"><i class="hide fa fa-briefcase"></i> <label>Provider Reference Code for Asset</label></td>
                                                <td width="60%" title="<?php echo $content_details->content_provider_reference_code; ?>"><?php echo (!empty($content_details->content_provider_reference_code)) ? ucwords($content_details->content_provider_reference_code) : '' ; ?></td>
                                            </tr>
                                            <tr>
                                                <td width="30%"><i class="hide fa fa-briefcase"></i> <label>Is Content Active</label></td>
                                                <td width="60%" title="Is Content Active"><?php echo (!empty($content_details->is_content_active) && ($content_details->is_content_active == true)) ? 'Yes' : 'No' ; ?></td>
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
                                        <td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Order Date</label></td>
                                        <td width="60%"><?php echo (!empty($content_details->order_date)) ? $content_details->order_date : '' ; ?></td>
                                    </tr>
                                    <tr>
                                        <td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Delivered Date</label></td>
                                        <td width="60%"><?php echo (!empty($content_details->delivered_date)) ? $content_details->delivered_date : '' ; ?></td>
                                    </tr>
                                    <tr>
                                        <td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Last Ingestion Date</label></td>
                                        <td width="60%"><?php echo (!empty($content_details->last_ingestion_date)) ? $content_details->last_ingestion_date : '' ; ?></td>
                                    </tr>                                   
                                    <tr>
                                        <td width="30%"><i class="hide fa fa-at text-bold"></i> <label>UIP Nominated</label></td>
                                        <td width="60%"><?php echo (!empty($content_details->is_uip_nominated) && ($content_details->is_uip_nominated == true)) ? 'Yes' : 'No' ; ?></td>
                                    </tr>
                                    <tr>
                                        <td width="30%"><i class="hide fa fa-at text-bold"></i> <label title="Has this Film been published to an External Streaming Service e.g. Airtime API">Published on Airtime</label></td>
                                        <td width="60%"><?php echo (!empty($content_details->external_content_ref)) ? 'Yes <i class="far fa-check-circle text-green" title="This Film has been published on a Streaming Service API Successfully" ></i>' : 'No <i class="far fa-times-circle text-red" title="This Film has not yet been pushed to Airtime API" ></i>' ; ?></td>
                                    </tr>
                                    
                                    <?php
                                    if (!empty($content_details->external_content_ref) && (!empty($content_details->is_airtime_asset) && strtolower($content_details->is_airtime_asset) == "yes")) { ?>
                                    <tr>
                                        <td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Airtime State</label></td>
                                        <td width="60%"><?php echo (!empty($content_details->airtime_state)) ? ucfirst($content_details->airtime_state) : "Offline"; ?></td>
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