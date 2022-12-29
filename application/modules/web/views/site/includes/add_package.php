<div class="modal-body">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <form id="adding-package-to-product-form" >
        <input type="hidden" name="page" value="details" />
        <input type="hidden" name="product_id" value="" />
        <div class="row">
            <div class="adding-package-to-product1 col-md-12 col-sm-12 col-xs-12">
                <div class="slide-group">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <legend class="legend-header">Package Details?</legend>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <h6 class="error_message pull-right" id="adding-package-to-product1-errors"></h6>
                        </div>
                    </div>

                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">Package Type</label>
                        <?php if (!empty($package_types)) { ?>
                            <select name="package_type_id" class="form-control" title="Package Type">
                                <option value="">Please select</option>
                                <?php foreach ($package_types as $ptype_row) { ?>
                                    <option value="<?php echo $ptype_row->setting_id; ?>"><?php echo $ptype_row->setting_value; ?></option>
                                <?php } ?>
                            </select>   
                            <?php
                        } else { ?>
                            <input class="form-control" name="package_type_id" type="text" placeholder="Package Type ID" value="" />
                            <?php
                        } ?>
                    </div>
                    
                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">Package Name</label>
                        <input class="form-control" name="package_name" type="text" placeholder="Package Name" value="" />
                    </div>
                    
                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">Package Description</label>
                        <input class="form-control" name="package_description" type="text" placeholder="Package Description" value="" />
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-6 col-lg-offset-6 col-md-6 col-md-offset-6 col-sm-6 col-sm-offset-6 col-xs-12">
                            <button class="btn-block btn-next adding-package-to-product-steps" data-currentpanel="adding-package-to-product1" id="check-reference-button" type="button">Next</button>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="adding-package-to-product2 col-md-12 col-sm-12 col-xs-12 el-hidden">
                <div class="slide-group">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <legend class="legend-header">What is the Content Provider?</legend>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <h6 class="error_message pull-right" id="adding-package-to-product2-errors"></h6>
                        </div>
                    </div>
                    
                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">Content Provider</label>
                        <select class="form-control container-full" name="content_provider_id" title="Content Provider">
                            <option value="">Please select</option>
                            <?php if (!empty($content_providers)) {
                                foreach ($content_providers as $row) { ?>
                                    <option value="<?php echo $row->provider_id; ?>"><?php echo (!empty($row->provider_name)) ? $row->provider_name : '[Not set]' ; ?></option>
                                    <?php
                                }
                            } else { ?>
                                <input class="form-control" name="content_provider_id" type="text" placeholder="Content Provider ID" value="" />
                                <?php
                            } ?>
                        </select>
                    </div>
                    
                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">Number of Titles</label>
                        <input class="form-control" name="no_of_titles" type="text" placeholder="Number of Titles" value="" />
                    </div>
                    
                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">Number of Rooms?</label>
                        <input name="no_of_rooms" class="form-control required container-full" type="text" value="" placeholder="Number of Rooms?" />
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button class="btn-block btn-back" data-currentpanel="adding-package-to-product2" type="button">Back</button>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button class="btn-block btn-next adding-package-to-product-steps" data-currentpanel="adding-package-to-product2" type="button">Next</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="adding-package-to-product3 col-md-12 col-sm-12 col-xs-12 el-hidden">
                <div class="slide-group">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <legend class="legend-header">What is the Start and End Date?</legend>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <h6 class="error_message pull-right" id="adding-package-to-product3-errors"></h6>
                        </div>
                    </div>
                    
                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">Start Date</label>
                        <input class="form-control container-full datetimepicker" name="start_date" type="text" placeholder="Start Date" value="" />
                    </div>
                    
                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">End Date</label>
                        <input class="form-control container-full datetimepicker" name="end_date" type="text" placeholder="End Date" value="" />
                    </div>

                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">Is Package Active?</label>
                        <select class="form-control container-full" name="is_package_active" title="Is Package Active">
                            <option value="yes" selected="selected">Active</option>
                            <option value="no">Inactive</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button class="btn-block btn-back" data-currentpanel="adding-package-to-product3" type="button">Back</button>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button class="btn-block btn-next adding-package-to-product-steps" data-currentpanel="adding-package-to-product3" type="button">Next</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="adding-package-to-product4 col-md-12 col-sm-12 col-xs-12 el-hidden">
                <div class="slide-group">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <legend class="legend-header">What is the package charge?</legend>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <h6 class="error_message pull-right" id="adding-package-to-product4-errors"></h6>
                        </div>
                    </div>
                    
                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">Package Charge</label>
                        <input name="package_charge" class="form-control required container-full" type="text" value="" placeholder="Package Charge?" />
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button class="btn-block btn-back" data-currentpanel="adding-package-to-product4" type="button">Back</button>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button class="btn-block btn-next" data-currentpanel="adding-package-to-product4" type="submit">Add Package</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </form>
</div>