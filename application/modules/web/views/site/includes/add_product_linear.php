<form id="product-creation-form">
    <div class="product_creation_panel1 col-md-12 col-sm-12 col-xs-12">
        <div class="slide-group">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <?php
                    $product_type = "linear";
                    if (!empty($product_type_id)) {
                        $product_type_data = $this->db->get_where("setting", ["setting_id" => $product_type_id ])->row();
                        if (!empty($product_type_data)) {
                            $product_type = $product_type_data->setting_value;
                        }
                    } ?>
                    <legend class="legend-header"><?php echo ucwords($product_type); ?>  Product type creation</legend>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <h6 class="error_message pull-right" id="product_creation_panel1-errors"></h6>
                </div>
            </div>
            
            <input type="hidden" name="product_details[product_type_id]" value="<?php echo (!empty($product_type_id)) ? $product_type_id : '' ; ?>" />
            <input type="hidden" name="product_details[site_id]" value="<?php echo (!empty($site_details->site_id)) ? $site_details->site_id : '' ; ?>" />
            
            <div class="input-group form-group container-full">
                <label class="input-group-addon el-hidden">Product Name</label>
                <input name="product_details[product_name]" class="form-control required container-full" type="text" value="" placeholder="Product Name" />
        
                <label class="input-group-addon el-hidden"> Product Reference Code</label>
                <input name="product_details[product_reference_code]" class="form-control required" type="text" value="" placeholder="Product Reference Code"  />
            </div>
            
            <div class="input-group form-group container-full">
                <label class="input-group-addon el-hidden">Product Description</label>
                <textarea name="product_details[product_description]" class="form-control container-full" type="text" value="" placeholder="Product description"></textarea>
            </div>

            <div class="row">
                <div class="col-lg-6 col-lg-offset-6 col-md-6 col-md-offset-6 col-sm-6 col-sm-offset-6 col-xs-12">
                    <button class="btn-block btn-next product-creation-steps" data-currentpanel="product_creation_panel1" type="button">Next</button>
                </div>
            </div>
        </div>
    </div>

    <div class="product_creation_panel2 col-md-12 col-sm-12 col-xs-12 el-hidden">
        <div class="slide-group">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <legend class="legend-header">What is the Content Provider?</legend>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <h6 class="error_message pull-right" id="product_creation_panel2-errors"></h6>
                </div>
            </div>
            
            <div class="input-group form-group container-full">
                <label class="input-group-addon el-hidden">Content Provider</label>
                <?php
                if (!empty($content_providers)) { ?>
                    <select class="form-control container-full" name="product_details[content_provider_id]" title="Content Provider">
                        <option value="">Please select</option>
                        <?php
                        foreach ($content_providers_linear as $row) { ?>
                            <option value="<?php echo $row->provider_id; ?>"><?php echo (!empty($row->provider_name)) ? $row->provider_name : '[Not set]' ; ?></option>
                            <?php
                        } ?>
                    </select>
                    <?php
                } ?>
            </div>
            
            <div class="input-group form-group container-full">
                <label class="input-group-addon el-hidden">Number of Rooms?</label>
                <input name="product_details[no_of_rooms]" class="form-control required container-full" type="text" value="" placeholder="Number of Rooms?" />
            </div>
            
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <button class="btn-block btn-back" data-currentpanel="product_creation_panel2" type="button">Back</button>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <button class="btn-block btn-next product-creation-steps" data-currentpanel="product_creation_panel2" type="button">Next</button>
                </div>
            </div>
        </div>
    </div>

    <div class="product_creation_panel3 col-md-12 col-sm-12 col-xs-12 el-hidden">
        <div class="slide-group">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <legend class="legend-header">What is the Start and End Date?</legend>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <h6 class="error_message pull-right" id="product_creation_panel3-errors"></h6>
                </div>
            </div>
            
            <div class="input-group form-group container-full">
                <label class="input-group-addon el-hidden">Start Date</label>
                <input class="form-control container-full datetimepicker" name="product_details[start_date]" type="text" placeholder="Start Date" value="" />
            </div>
            
            <div class="input-group form-group container-full">
                <label class="input-group-addon el-hidden">End Date</label>
                <input class="form-control container-full datetimepicker" name="product_details[end_date]" type="text" placeholder="End Date" value="" />
            </div>


            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <button class="btn-block btn-back" data-currentpanel="product_creation_panel3" type="button">Back</button>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <button class="btn-block btn-next product-creation-steps" data-currentpanel="product_creation_panel3" type="button">Next</button>
                </div>
            </div>
        </div>
    </div>
    
    
    <div class="product_creation_panel4 col-md-12 col-sm-12 col-xs-12 el-hidden">
        <div class="slide-group">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <legend class="legend-header">What is the product note?</legend>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <h6 class="error_message pull-right" id="product_creation_panel4-errors"></h6>
                </div>
            </div>
            
            <div class="input-group form-group container-full">
                <label class="input-group-addon el-hidden">Product Note</label>
                <textarea name="product_details[product_note]" class="form-control container-full" type="text" value="" placeholder="Product Note"></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <button class="btn-block btn-back" data-currentpanel="product_creation_panel4" type="button">Back</button>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <button class="btn-block btn-next product-creation-steps" data-currentpanel="product_creation_panel4" type="button">Next</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="product_creation_panel5 col-md-12 col-sm-12 col-xs-12 el-hidden">
        <div class="slide-group">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <legend class="legend-header">What is the Product Delivery Mechanism?</legend>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <h6 class="error_message pull-right" id="product_creation_panel5-errors"></h6>
                </div>
            </div>
            
            <div class="input-group form-group container-full">
                <label class="input-group-addon el-hidden">Product Delivery Mechanism</label>
                <?php
                if (!empty($delivery_mechanism)) { ?>
                    <select class="form-control required container-full" name="product_details[delivery_mechanism_id]" title="Product Delivery Mechanism">
                        <option value="">Please select</option>
                        <?php
                        foreach ($delivery_mechanism as $row) { ?>
                            <option value="<?php echo (!empty($row->setting_id)) ? $row->setting_id : ''; ?>" title="<?php echo (!empty($row->value_desc)) ? $row->value_desc : '' ?>"><?php echo (!empty($row->setting_value)) ? $row->setting_value : '' ?></option>
                            <?php
                        } ?>
                    </select>
                    <?php
                } ?>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <button class="btn-block btn-back" data-currentpanel="product_creation_panel5" type="button">Back</button>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <button class="btn-block btn-next product-creation-steps" data-currentpanel="product_creation_panel5" type="button">Next</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="product_creation_panel6 col-md-12 col-sm-12 col-xs-12 el-hidden">
        <div class="slide-group">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <legend class="legend-header">What is the Package Charge and the Product Status?</legend>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <h6 class="error_message pull-right" id="product_creation_panel6-errors"></h6>
                </div>
            </div>
            
            <div class="input-group form-group container-full">
                <label class="input-group-addon el-hidden">Package Charge (p/r/p/m)</label>
                <input name="product_details[package_charge]" class="form-control required container-full" type="text" value="" placeholder="Package Charge (p/r/p/m)?" />
            </div>
            
            <div class="input-group form-group container-full">
                <label class="input-group-addon el-hidden">Product Status</label>
                <?php
                if (!empty($product_statuses)) { ?>
                    <select class="form-control required container-full" name="product_details[product_status_id]" title="Product Status">
                        <option value="">Please select Product Status</option>
                        <?php
                        foreach ($product_statuses as $row) { ?>
                            <option value="<?php echo (!empty($row->setting_id)) ? $row->setting_id : ''; ?>" <?php echo (!empty($row->setting_value) && strtolower($row->setting_value) == "active") ? 'selected="selected"' : '' ; ?> title="<?php echo (!empty($row->value_desc)) ? $row->value_desc : '' ?>"><?php echo (!empty($row->setting_value)) ? $row->setting_value : '' ?></option>
                            <?php
                        } ?>
                    </select>
                    <?php
                } ?>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <button class="btn-block btn-back" data-currentpanel="product_creation_panel6" type="button">Back</button>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <button class="btn-block btn-next" data-currentpanel="product_creation_panel5" type="submit">Add Product</button>
                </div>
            </div>
        </div>
    </div>
</form>