<div class="modal-body">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <form id="adding-system-to-product-form" >
        <input type="hidden" name="page" value="details" />
        <input type="hidden" name="system_details[site_id]" value="<?php echo $site_details->site_id; ?>" />
        <input type="hidden" name="product_id" value="" />
        <div class="row">
            <div class="adding-system-to-product1 col-md-12 col-sm-12 col-xs-12">
                <div class="slide-group">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <legend class="legend-header">What is the system type?</legend>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <h6 class="error_message pull-right" id="adding-system-to-product1-errors"></h6>
                        </div>
                    </div>

                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">Choose System Type</label>
                        <?php
                        if (!empty($systems)) { ?>
                            <select name="system_details[system_type_id]" class="form-control required">
                                <option value="">Please select</option>
                                <?php
                                foreach ($systems as $system_type_id => $row) { ?>
                                    <option value="<?php echo $system_type_id; ?>" <?php echo (!empty($site_details->system_type_id) && ($site_details->system_type_id == $system_type_id)) ? 'selected="selected"' : "" ; ?>><?php echo $row->name; ?></option>
                                    <?php
                                } ?>
                            </select>
                            <?php
                        } else { ?>
                            <input class="form-control" name="system_details[system_type_id]" type="text" placeholder="System Type" value="<?php echo $site_details->system_type_id; ?>" />
                            <?php
                        } ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-6 col-lg-offset-6 col-md-6 col-md-offset-6 col-sm-6 col-sm-offset-6 col-xs-12">
                            <button class="btn-block btn-next adding-system-to-product-steps" data-currentpanel="adding-system-to-product1" id="check-reference-button" type="button">Next</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="adding-system-to-product2 col-md-12 col-sm-12 col-xs-12 el-hidden">
                <div class="slide-group">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <legend class="legend-header">Is Airtime Active?</legend>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <h6 class="error_message pull-right" id="adding-system-to-product2-errors"></h6>
                        </div>
                    </div>
                    
                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">Is Airtime Active?</label>
                        <select class="form-control required container-full" name="system_details[is_airtime_active]">
                            <option value="">Is Airtime Active?</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                    
                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">Is the System a Hybrid?</label>
                        <select class="form-control required container-full" name="system_details[is_hybrid]">
                            <option value="">Is the System a Hybrid?</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button class="btn-block btn-back" data-currentpanel="adding-system-to-product2" type="button">Back</button>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button class="btn-block btn-next adding-system-to-product-steps" data-currentpanel="adding-system-to-product2" type="button">Next</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="adding-system-to-product3 col-md-12 col-sm-12 col-xs-12 el-hidden">
                <div class="slide-group">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <legend class="legend-header">What is the Field Site PIN?</legend>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <h6 class="error_message pull-right" id="adding-system-to-product3-errors"></h6>
                        </div>
                    </div>
                    
                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">What is the Field Site PIN?</label>
                        <input name="system_details[field_site_pin]" class="form-control required container-full" type="text" value="" placeholder="Field Site PIN?" />
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button class="btn-block btn-back" data-currentpanel="adding-system-to-product3" type="button">Back</button>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button class="btn-block btn-next adding-system-to-product-steps" data-currentpanel="adding-system-to-product3" type="button">Next</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="adding-system-to-product4 col-md-12 col-sm-12 col-xs-12 el-hidden">
                <div class="slide-group">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <legend class="legend-header">Would you like to Add Packages to the system?</legend>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <h6 class="error_message pull-right" id="adding-system-to-product4-errors"></h6>
                        </div>
                    </div>
                    
                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">Would you like to add packages to the system?</label>
                        <select class="form-control required container-full" name="system_details[add_packages]">
                            <option value="">Add packages to the system?</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button class="btn-block btn-back" data-currentpanel="adding-system-to-product4" type="button">Back</button>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12 el-hidden" id="add_packages_yes">
                            <button class="btn-block btn-next adding-system-to-product-steps" data-currentpanel="adding-system-to-product4" type="button">Next</button>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12 el-hidden" id="add_packages_no">
                            <button class="btn-block btn-next" data-currentpanel="adding-system-to-product4" type="submit">Add System</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="adding-system-to-product5 col-md-12 col-sm-12 col-xs-12 el-hidden">
                <div class="slide-group">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <legend class="legend-header">Adding Packages to the system</legend>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <h6 class="error_message pull-right" id="adding-system-to-product5-errors"></h6>
                        </div>
                    </div>
        
                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">Adding Packages to the system</label>
                        <select id="package_to_system" class="form-control required">
                            <option value="">Please select package</option>
                            <?php
                            foreach ($package as $key => $row) { ?>
                                <option value="<?php echo $row->package_id; ?>" data-packagename="<?php echo $row->package_name; ?>"><?php echo $row->package_name; ?> (£<?php echo $row->package_charge; ?>)</option>
                                <?php
                            } ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <legend class="legend-header">Added packages:</legend>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div id="outputArea"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button class="btn-block btn-back" data-currentpanel="adding-system-to-product5" type="button">Back</button>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button class="btn-block btn-next" data-currentpanel="adding-system-to-product5" type="submit">Add System</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </form>
</div>