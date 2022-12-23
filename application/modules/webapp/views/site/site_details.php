<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel tile group-container site-products-container">
        <span class="pull-right create-product"><a type="button" class="" data-toggle="modal" data-target="#createProduct"><i class="fas fa-plus-circle"></i></a></span>
        <input type="hidden" name="site_id" value="<?php echo (!empty($site_details->site_id)) ? $site_details->site_id : '' ; ?>" />
        <h4 class="legend pointer"><i class="fas fa-caret-down"></i>Site Products</h4>

        <div class="row group-content">
            <?php
            if (!empty($site_products)) {
                foreach ($site_products as $key => $row) { ?>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="x_panel tile group-container product-container <?php echo $row->product_reference_code; ?>">
                            <?php
                            if (!empty($row->product_type_name) && (strtolower($row->product_type_name) == "airtime")) { ?>
                                <div class="add-price-plan-to-product" data-product_id="<?php echo (!empty($row->product_id)) ? $row->product_id : '' ; ?>"><a type="button" class="" data-toggle="modal" data-target="#addPricePlanToProduct" style="color: #fff;" title="Add Price Band to Product"><i class="fas fa-plus-circle"></i></a></div>
                                <div class="airtime_activate_market_container"><a href="#" data-product_id="<?php echo (!empty($row->product_id)) ? $row->product_id : '' ; ?>" data-is_market_active_on_airtime="<?php echo (!empty($row->is_market_active_on_airtime)) ? $row->is_market_active_on_airtime : '' ; ?>" ><img src="<?php echo ($row->is_market_active_on_airtime) ? base_url("assets/images/icons/active-market.png") : base_url("assets/images/icons/inactive-market.png") ; ?>" /></a>
                                </div>
                                <?php
                            } else { ?>
                                <div class="delete_product_container"><a href="#" data-product_id="<?php echo (!empty($row->product_id)) ? $row->product_id : '' ; ?>"><i class="fas fa-trash-alt"></i></a></div>
                                <?php
                            } ?>

                            <h4 class="legend"><i class="fas fa-caret-down"></i><?php echo $row->product_name; ?> (ID: <?php echo $row->product_id; ?>)</h4>
                            <div class="row group-content">
                                <?php
                                ## AIRTIME product type
                                if (!empty($row->product_type_name) && (strtolower($row->product_type_name) == "airtime")) { ?>
                                    <form class="productForm">
                                        <input type="hidden" name="product_id" value="<?php echo (!empty($row->product_id)) ? $row->product_id : '' ; ?>" />
                                        <div class="standard-div">
                                            <div class="container-full standard-div">
                                                <label class="input-label-40">Product Name</label>
                                                <input class="input-field-60" name="product_name" type="text" placeholder="Product Name" value="<?php echo (!empty($row->product_name)) ? $row->product_name : '' ; ?>" />
                                            </div>

                                            <div class="container-full standard-div">
                                                <label class="input-label-40">Product Description</label>
                                                <textarea class="input-field-60" name="product_description" type="text" placeholder="Product Description"><?php echo (!empty($row->product_description)) ? $row->product_description : '' ; ?></textarea>
                                            </div>

                                            <div class="container-full standard-div">
                                                <label class="input-label-40">Product Type</label>
                                                <?php if (!empty($product_types)) { ?>
                                                    <select name="product_type_id" class="input-field-60">
                                                        <option value="">Please select Product Type</option>
                                                        <?php foreach ($product_types as $p_row) { ?>
                                                            <option value="<?php echo $p_row->setting_id; ?>" <?php echo (!empty($row->product_type_id) && ($row->product_type_id == $p_row->setting_id)) ? 'selected="selected"' : '' ; ?>><?php echo $p_row->setting_value; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } else { ?>
                                                    <input class="input-field-60" name="product_type_id" type="text" placeholder="Product Type ID" value="<?php echo (!empty($row->product_type_id)) ? ($row->product_type_id) : '' ; ?>" />
                                                <?php } ?>
                                            </div>

                                            <div class="container-full standard-div">
                                                <label class="input-label-40">Free to Guest</label>
                                                <select class="input-field-60 is_airtime_ftg" name="is_airtime_ftg" title="Is Airtime FTG?">
                                                    <option value="">Please select Free to Guest</option>
                                                    <option value="yes" <?php echo (!empty($row->is_airtime_ftg) && ($row->is_airtime_ftg == true)) ? 'selected="selected"' : '' ; ?>>Yes</option>
                                                    <option value="no" <?php echo (empty($row->is_airtime_ftg) || ($row->is_airtime_ftg != true)) ? 'selected="selected"' : '' ; ?>>No</option>
                                                </select>
                                            </div>

                                            <div class="container-full standard-div">
                                                <label class="input-label-40">Package Charge (p/r/p/m)</label>
                                                <input name="package_charge" class="input-field-60" type="text" value="<?php echo (!empty($row->package_charge)) ? ($row->package_charge) : '' ; ?>" placeholder="Package Charge (p/r/p/m)" />
                                            </div>

                                            <div class="container-full standard-div">
                                                <label class="input-label-40">Number of Rooms</label>
                                                <input class="input-field-60" name="no_of_rooms" type="text" placeholder="Number of Rooms" value="<?php echo (!empty($row->no_of_rooms)) ? $row->no_of_rooms : '' ; ?>" />
                                            </div>

                                            <div class="container-full standard-div">
                                                <label class="input-label-40">Product Status</label>
                                                <?php
                                                if (!empty($product_statuses)) { ?>
                                                    <select name="product_status_id" class="input-field-60">
                                                        <option value="">Please select Product Status</option>
                                                        <?php foreach ($product_statuses as $pr_row) {?>
                                                            <option value="<?php echo (!empty($pr_row->setting_id)) ? $pr_row->setting_id : ''; ?>" title="<?php echo (!empty($pr_row->value_desc)) ? $pr_row->value_desc : '' ?>" <?php echo (!empty($row->product_status_id) && ($row->product_status_id == $pr_row->setting_id)) ? 'selected="selected"' : '' ; ?>><?php echo (!empty($pr_row->setting_value)) ? $pr_row->setting_value : '' ?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } ?>
                                            </div>

                                            <div class="container-full standard-div">
                                                <label class="input-label-40">Start Date</label>
                                                <input class="input-field-60 datetimepicker" name="start_date" type="text" placeholder="Start Date" value="<?php echo (validate_date($row->start_date)) ? format_date_client($row->start_date) : '' ; ?>" />
                                            </div>

                                            <div class="container-full standard-div end_date">
                                                <label class="input-label-40">End Date</label>
                                                <input class="input-field-60 datetimepicker" name="end_date" type="text" placeholder="End Date" value="<?php echo (validate_date($row->end_date)) ? format_date_client($row->end_date) : '' ; ?>" />
                                            </div>

                                            <div class="container-full standard-div">
                                                <label class="input-label-40">Airtime PIN</label>
                                                <input class="input-field-60" name="airtime_pin" type="text" placeholder="Airtime PIN" value="<?php echo (!empty($row->airtime_pin)) ? ($row->airtime_pin) : '' ; ?>" />
                                            </div>

                                            <div class="container-full standard-div">
                                                <label class="input-label-40">Sale Currency</label>
                                                <?php
                                                if (!empty($sale_currencies)) { ?>
                                                    <select name="sale_currency_id" class="input-field-60">
                                                        <option value="">Please select Sale Currency</option>
                                                        <?php foreach ($sale_currencies as $sc_row) {?>
                                                            <option value="<?php echo (!empty($sc_row->setting_id)) ? $sc_row->setting_id : ''; ?>" title="<?php echo (!empty($sc_row->value_desc)) ? $sc_row->value_desc : '' ?>" <?php echo (!empty($row->sale_currency_id) && ($row->sale_currency_id == $sc_row->setting_id)) ? 'selected="selected"' : '' ; ?>><?php echo (!empty($sc_row->setting_value)) ? $sc_row->setting_value : '' ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <?php
                                                } else { ?>
                                                    <input name="sale_currency_id" class="input-field-60" type="text" value="" placeholder="Sale Currency ID" />
                                                    <?php
                                                } ?>
                                            </div>

                                            <div class="container-full standard-div">
                                                <label class="input-label-40">Is Adult Active?</label>
                                                <select class="input-field-60 container-full" name="is_adult_active" title="Is Adult Active?">
                                                    <option value="">Please select Adult Active</option>
                                                    <option value="yes" <?php echo (!empty($row->is_adult_active) && ($row->is_adult_active == true)) ? 'selected="selected"' : '' ; ?>>Yes</option>
                                                    <option value="no" <?php echo (empty($row->is_adult_active) || ($row->is_adult_active != true)) ? 'selected="selected"' : '' ; ?>>No</option>
                                                </select>
                                            </div>

                                            <?php
                                            ## Price plans
                                            if (!empty($row->price_plan)) {
                                                foreach ($row->price_plan as $plan) { ?>
                                                    <div class="x_panel tile group-container product-price_plan <?php echo "plan_id_" . $plan->product_price_plan_id; ?>">
                                                    <div class="delete_price_plan_container"><a href="#/" data-product_price_plan_id="<?php echo (!empty($plan->product_price_plan_id)) ? $plan->product_price_plan_id : '' ; ?>"><i class="fas fa-trash-alt"></i></a></div>
                                                        <h4 class="legend"><i class="fas fa-caret-up"></i>Price Plan: <?php echo (!empty($plan->price_plan_name)) ? $plan->price_plan_name : '' ;?>
                                                        <?php if (!empty($plan->easel_price_band_ref)) { ?>
                                                            <span class="easel-plan-reference" data-easel_price_band_ref="<?php echo $plan->easel_price_band_ref; ?>"><img src="<?php echo base_url("assets/images/AT_white.png") ?>" /></span>
                                                        <?php } ?>

                                                        </h4>
                                                        <div class="row group-content" style="display: block;">
                                                            <input type="hidden" name="price_plans[<?php echo (!empty($plan->product_price_plan_id)) ? $plan->product_price_plan_id : '' ; ?>][product_price_plan_id]" value="<?php echo (!empty($plan->product_price_plan_id)) ? $plan->product_price_plan_id : '' ; ?>">

                                                            <input type="hidden" name="price_plans[<?php echo (!empty($plan->product_price_plan_id)) ? $plan->product_price_plan_id : '' ; ?>][content_provider_id]" value="<?php echo (!empty($plan->provider_id)) ? $plan->provider_id : '' ; ?>">

                                                            <input type="hidden" name="price_plans[<?php echo (!empty($plan->product_price_plan_id)) ? $plan->product_price_plan_id : '' ; ?>][price_plan_id]" value="<?php echo (!empty($plan->price_plan_id)) ? $plan->price_plan_id : '' ; ?>">

                                                            <div class="standard-div container-full">
                                                                <div class="container-full standard-div">
                                                                    <label class="input-label-40">Provider</label>
                                                                    <input class="input-field-60" type="text" placeholder="Price Plan" value="<?php echo ($plan->provider_name) ? html_escape($plan->provider_name) : '' ; ?>" readonly="readonly" />
                                                                </div>

                                                                <div class="container-full standard-div airtime_plan">
                                                                    <label class="input-label-40">Price Plan</label>
                                                                    <input class="input-field-60" type="text" placeholder="Price Plan" value="<?php echo ($plan->price_plan_name) ? html_escape($plan->price_plan_name) : '' ; ?>" readonly="readonly" />
                                                                </div>

                                                                <div class="container-full standard-div airtime_plan_price">
                                                                    <label class="input-label-40">Plan Price</label>
                                                                    <input class="input-field-60" name="price_plans[<?php echo (!empty($plan->product_price_plan_id)) ? $plan->product_price_plan_id : '' ; ?>][plan_price]" type="text" placeholder="Plan Price" value="<?php echo (!empty($plan->plan_price)) ? $plan->plan_price : ''  ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                } ?>
                                                <?php
                                            } ?>


                                            <div class="rows update_product_div">
                                                <div style="margin-right: -0.5px;">
                                                    <?php
                                                    $item_access = $this->webapp_service->check_access($this->user, $this->module_id, "details");
                                    if (!$this->user->is_admin || !empty($item_access->can_edit) || !empty($item_access->is_admin)) { ?>
                                                        <button class="btn-success btn-block" type="submit">Update Product</button>
                                                        <?php
                                    } else { ?>
                                                        <button class="btn-success btn-block no-permissions" disabled style="width: 100%;margin-top: 10px;">No Permissions</button>
                                                        <?php
                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <?php
                                } else { ?>
                                    <form class="productForm">
                                        <input type="hidden" name="product_id" value="<?php echo (!empty($row->product_id)) ? $row->product_id : '' ; ?>" />
                                        <div class="standard-div">
                                            <div class="container-full standard-div">
                                                <label class="input-label-40">Product Name</label>
                                                <input class="input-field-60" name="product_name" type="text" placeholder="Product Name" value="<?php echo (!empty($row->product_name)) ? $row->product_name : '' ; ?>" />
                                            </div>

                                            <div class="container-full standard-div">
                                                <label class="input-label-40">Product Description</label>
                                                <textarea class="input-field-60" name="product_description" type="text" placeholder="Product Description"><?php echo (!empty($row->product_description)) ? $row->product_description : '' ; ?></textarea>
                                            </div>

                                            <div class="container-full standard-div">
                                                <label class="input-label-40">Product Type</label>
                                                <?php if (!empty($product_types)) { ?>
                                                    <select name="product_type_id" class="input-field-60">
                                                        <option value="">Please select</option>
                                                        <?php foreach ($product_types as $p_row) { ?>
                                                            <option value="<?php echo $p_row->setting_id; ?>" <?php echo (!empty($row->product_type_id) && ($row->product_type_id == $p_row->setting_id)) ? 'selected="selected"' : '' ; ?>><?php echo $p_row->setting_value; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } ?>
                                            </div>

                                            <div class="container-full standard-div <?php echo (!empty($row->product_type_name) && (strtolower($row->product_type_name) != "airtime")) ? 'el-shown' : 'el-hidden' ; ?>">
                                                <label class="input-label-40">Delivery Mechanism</label>
                                                <?php
                                                if (!empty($delivery_mechanism)) { ?>
                                                    <select name="delivery_mechanism_id" class="input-field-60">
                                                        <option value="">Please select</option>
                                                        <?php foreach ($delivery_mechanism as $del_row) { ?>
                                                            <option value="<?php echo $del_row->setting_id; ?>" <?php echo (!empty($row->delivery_mechanism_id) && ($row->delivery_mechanism_id == $del_row->setting_id)) ? 'selected="selected"' : '' ; ?>><?php echo $del_row->setting_value; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } ?>
                                            </div>

                                            <div class="container-full standard-div">
                                                <label class="input-label-40">Content Provider</label>
                                                <?php
                                                $providers = (strtolower($row->product_type_name) == 'linear') ? $content_providers_linear : $content_providers;

                                    if (!empty($providers)) { ?>
                                                    <select name="content_provider_id" class="input-field-60">
                                                        <option value="">Please select</option>
                                                        <?php foreach ($providers as $cp_row) { ?>
                                                            <option value="<?php echo $cp_row->provider_id; ?>" <?php echo (!empty($row->content_provider_id) && ($row->content_provider_id == $cp_row->provider_id)) ? 'selected="selected"' : '' ; ?>><?php echo $cp_row->provider_name; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } ?>
                                            </div>

                                            <div class="container-full standard-div">
                                                <label class="input-label-40">Number of Rooms</label>
                                                <input class="input-field-60" name="no_of_rooms" type="text" placeholder="Number of Rooms" value="<?php echo (!empty($row->no_of_rooms)) ? $row->no_of_rooms : '' ; ?>" />
                                            </div>

                                            <div class="container-full standard-div">
                                                <label class="input-label-40">Product Status</label>
                                                <?php
                                    if (!empty($product_statuses)) { ?>
                                                    <select name="product_status_id" class="input-field-60">
                                                        <option value="">Please select</option>
                                                        <?php foreach ($product_statuses as $pr_row) {?>
                                                            <option value="<?php echo (!empty($pr_row->setting_id)) ? $pr_row->setting_id : ''; ?>" title="<?php echo (!empty($pr_row->value_desc)) ? $pr_row->value_desc : '' ?>" <?php echo (!empty($row->product_status_id) && ($row->product_status_id == $pr_row->setting_id)) ? 'selected="selected"' : '' ; ?>><?php echo (!empty($pr_row->setting_value)) ? $pr_row->setting_value : '' ?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } ?>
                                            </div>

                                            <div class="container-full standard-div">
                                                <label class="input-label-40">Start Date</label>
                                                <input class="input-field-60 datetimepicker" name="start_date" type="text" placeholder="Start Date" value="<?php echo (validate_date($row->start_date)) ? format_date_client($row->start_date) : '' ; ?>" />
                                            </div>

                                            <div class="container-full standard-div end_date" style="display: <?php echo (!empty($row->product_status_name) && strtolower($row->product_status_name) == "active") ? "none;" : "block;"; ?>">
                                                <label class="input-label-40">End Date</label>
                                                <input class="input-field-60 datetimepicker" name="end_date" type="text" placeholder="End Date" value="<?php echo (validate_date($row->end_date)) ? format_date_client($row->end_date) : '' ; ?>" />
                                            </div>

                                            <div class="container-full standard-div <?php echo (!empty($row->product_type_name) && (strtolower($row->product_type_name) == "vod")) ? 'el-shown' : 'el-hidden' ; ?>">
                                                <label class="input-label-40">Number of Titles</label>
                                                <?php
                                    if (!empty($no_of_titles_packages)) { ?>
                                                    <select name="no_of_titles_id" class="input-field-60">
                                                        <option value="">Please select</option>
                                                        <?php foreach ($no_of_titles_packages as $titl_row) { ?>
                                                            <option value="<?php echo $titl_row->setting_id; ?>" <?php echo (!empty($row->no_of_titles_id) && ($row->no_of_titles_id == $titl_row->setting_id)) ? 'selected="selected"' : '' ; ?>><?php echo $titl_row->setting_value; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } ?>
                                            </div>

                                            <div class="container-full standard-div <?php echo (!empty($row->product_type_name) && (strtolower($row->product_type_name) == "vod")) ? 'el-shown' : 'el-hidden' ; ?>">
                                                <label class="input-label-40">Films per Month</label>
                                                <?php
                                    if (!empty($films_per_month)) { ?>
                                                    <select name="films_per_month_id" class="input-field-60">
                                                        <option value="">Please select</option>
                                                        <?php foreach ($films_per_month as $fpm_row) { ?>
                                                            <option value="<?php echo $fpm_row->setting_id; ?>" <?php echo (!empty($row->films_per_month_id) && ($row->films_per_month_id == $fpm_row->setting_id)) ? 'selected="selected"' : '' ; ?>><?php echo $fpm_row->setting_value; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } ?>
                                            </div>

                                            <div class="container-full standard-div">
                                                <label class="input-label-40">Product Note</label>
                                                <input class="input-field-60" name="product_note" type="text" placeholder="Product Note" value="<?php echo (!empty($row->product_note)) ? $row->product_note : '' ; ?>" />
                                            </div>

                                            <div class="container-full standard-div">
                                                <label class="input-label-40">Package Charge (<?php echo !empty($site_details->invoice_currency_name) ? strtoupper($site_details->invoice_currency_name) : 'GBP' ?>)</label>
                                                <input class="input-field-60" name="package_charge" type="text" placeholder="Package Charge" value="<?php echo (!empty($row->package_charge)) ? $row->package_charge : '' ; ?>" />
                                            </div>

                                            <div class="container-full standard-div el-hidden">
                                                <label class="input-label-40">Is Package Active?</label>
                                                <select class="input-field-60" name="is_package_active" title="Active">
                                                    <option value="yes" <?php echo (!empty($row->is_package_active) && ($row->is_package_active == true)) ? 'selected="selected"' : '' ; ?>>Active</option>
                                                    <option value="no" <?php echo (empty($row->is_package_active) || ($row->is_package_active != true)) ? 'selected="selected"' : '' ; ?>>Inactive</option>
                                                </select>
                                            </div>

                                            <div class="container-full standard-div <?php echo((!empty($row->product_type_name) && (strtolower($row->product_type_name) == "vod")) ? 'el-shown' : 'el-hidden'); ?>">
                                                <label class="input-label-40">Is Product FTG?</label>
                                                <select class="input-field-60" name="is_content_ftg" title="Is Product FTG?">
                                                    <option value="yes" <?php echo (!empty($row->is_content_ftg) && ($row->is_content_ftg == true)) ? 'selected="selected"' : '' ; ?>>Yes</option>
                                                    <option value="no" <?php echo (empty($row->is_content_ftg) || ($row->is_content_ftg != true)) ? 'selected="selected"' : '' ; ?>>No</option>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="rows update_product_div">
                                            <div style="margin-right: -0.5px;">
                                                <?php
                                    $item_access = $this->webapp_service->check_access($this->user, $this->module_id, "details");

                                    if (!$this->user->is_admin || !empty($item_access->can_edit) || !empty($item_access->is_admin)) { ?>
                                                    <button class="btn-success btn-block" type="submit">Update Product</button>
                                                    <?php
                                    } else { ?>
                                                    <button class="btn-success btn-block no-permissions" disabled style="width: 100%;margin-top: 10px;">No Permissions</button>
                                                    <?php
                                    } ?>
                                            </div>
                                        </div>
                                    </form>
                                    <?php
                                }   ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }   ?>
                <?php
            } else { ?>
                <p class="no-item">No product has been created for this site.</p>
                <?php
            } ?>
        </div>
    </div> <!-- End of Site Products -->
    <div class="x_panel tile group-container site-documents">
        <h4 class="legend pointer"><i class="fas fa-caret-down"></i>Site Documents</h4>
        <div class="row group-content el-hidden">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
                <legend class="default-legend">Upload Files</legend>
                <form action="<?php echo base_url('webapp/site/upload_docs/' . $site_details->site_id); ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
                    <input type="hidden" name="site_id"     value="<?php echo $site_details->site_id; ?>" />
                    <input type="hidden" name="module"      value="site" />
                    <input type="hidden" name="doc_type"    value="site" />


                    <div class="input-group form-group">
                        <label class="input-group-addon">Site file</label>
                        <span class="control-fileupload single pointer">
                            <label for="file-upload" class="custom-file-upload">
                                <i class="fas fa-cloud-upload"></i> Select file
                            </label>
                            <input id="file-upload" name="upload_files[doc]" type="file"/>
                        </span>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <button id="doc-upload-btn" class="btn btn-sm btn-block btn-success" type="submit">Upload Document(s)</button>
                        </div>
                    </div>
                    <br/>
                </form>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
                <legend class="default-legend">Existing Documents</legend>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <?php if (!empty($site_documents)) {
                            foreach ($site_documents as $file_group => $files) { ?>
                            <h5 style="color:#000" class="file-toggle pointer" data-class_grp="<?php echo str_replace(' ', '', $file_group); ?>" ><?php echo ucwords($file_group); ?> <span class="pull-right">(<?php echo count($files); ?>)</span></h5>
                                                            <?php foreach ($files as $k => $file) { ?>
                                <div class="row <?php echo str_replace(' ', '', $file_group); ?>" style="display:block;padding:5px 0">
                                    <div class="col-md-10" style="padding-left:30px;"><a target="_blank" href="<?php echo $file->document_link; ?>"><?php echo $file->document_name; ?></a></div>
                                    <div class="col-md-2"><span class="pull-right"><a target="_blank" href="<?php echo $file->document_link; ?>"><i class="fas fa-download"></i></a> &nbsp;&nbsp;&nbsp;<i class="fas fa-trash-alt text-red delete-file" data-document_id="<?php echo (!empty($file->document_id)) ? $file->document_id : '' ; ?>"></i></span></div>
                                </div>
                                                            <?php }  ?>
                            <?php }
                            }  ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SITE INVENTORY -->
    <div class="x_panel tile group-container site-inventory">
        <h4 class="legend pointer"><i class="fas fa-caret-down"></i>Site Inventory</h4>
        <div class="row group-content el-hidden">
            <?php include('site_inventory.php'); ?>
        </div>
    </div>

    <!-- SITE DEVICES -->
    <div class="x_panel tile group-container site-devices">
        <span class="upload-file"><a href="<?php echo base_url("/webapp/site/upload_devices/" . ((!empty($site_details->site_id)) ? $site_details->site_id : '')); ?>"><i class="fas fa-upload"></i></a></span>
        <h4 class="legend pointer"><i class="fas fa-caret-down"></i>Site Devices</h4>
        <div class="row group-content el-hidden">
            <?php include('site_devices.php'); ?>
        </div>
    </div>

    <div class="x_panel tile group-container site-reports">
        <h4 class="legend pointer"><i class="fas fa-caret-down"></i>Site Reports</h4>
        <div class="row group-content el-hidden">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
                <div class="x_panel tile group-container site-reports-window">
                    <h4 class="legend pointer"><i class="fas fa-caret-down"></i>Site Reports Window</h4>
                    <div class="row group-content el-hidden">
                        <form class="update-window-form">
                            <input type="hidden" name="site_id" value="<?php echo (!empty($site_details->site_id)) ? $site_details->site_id : '' ; ?>" />
                            <div class="row">
                                <legend class="input-label">Window</legend>
                                <div id="months_container">

                                    <?php
                                        if (!isset($months) || empty($months)) {
                                            $months     = ['January', 'February','March','April','May','June','July','August','September','October','November', 'December'];
                                        }

                                        $site_active_months = [];
        if (!empty($site_details->active_months)) {
            $site_active_months = array_column($site_details->active_months, 'month_id');
        } ?>

                                    <li class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label for="all_months">
                                            <input type="checkbox" id="all_months" value="" <?php echo (count($site_active_months) > 11) ? 'checked="checked"' : "" ; ?> >
                                            <span class="month_name">All Months</span>
                                        </label>
                                    </li>

                                    <?php
        foreach ($months as $key => $month) { ?>
                                        <li class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <label for="<?php echo strtolower($month); ?>">
                                                <input type="checkbox" data-month_name="<?php echo strtolower($month); ?>" name="month[]" id="<?php echo strtolower($month); ?>" value="<?php echo $key + 1; ?>" <?php echo(in_array(($key + 1), $site_active_months) ? 'checked="checked"' : ""); ?> >
                                                <span class="month_name"><?php echo ucfirst($month); ?></span>
                                            </label>
                                        </li>
                                        <?php
        } ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <?php
                $item_access = $this->webapp_service->check_access($this->user, $this->module_id, "details");

        if (!$this->user->is_admin || !empty($item_access->can_edit) || !empty($item_access->is_admin)) { ?>
                                                <button class="update-window-btn btn btn-block btn-update btn-primary" type="button">Update</button>
                                                <?php
        } else { ?>
                                                <button class="btn-success btn-block btn-update no-permissions" disabled>No Permissions</button>
                                                <?php
        } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
                <div class="x_panel tile group-container site-reports">
                    <?php include_once("includes\inc_report_settings.php"); ?>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="addPricePlanToProduct" tabindex="-1" role="dialog" aria-labelledby="disable-site" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <?php $this->view('site/includes/add_price_plan_to_product.php'); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="disableSite" tabindex="-1" role="dialog" aria-labelledby="disable-site" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="slide-group">
                            <form id="disable-site-form">
                                <input type="hidden" name="site_id" value="<?php echo (!empty($site_details->site_id)) ? $site_details->site_id : '""' ; ?>" />
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <legend class="legend-header">What is the disable Site Date?</legend>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <h6 class="error_message pull-right" id="disable-site-details1-errors" style="display: none;"></h6>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <label class="input-label-40">Disable Site date</label>
                                        <input class="input-field-60 datetimepicker" type="text" name="disable_site_date" placeholder="Select date"  />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <?php
                                        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, "details");

        if (!$this->user->is_admin || !empty($item_access->can_edit) || !empty($item_access->is_admin)) { ?>
                                            <button class="btn-success btn-block btn-update" type="submit">Disable Site</button>
                                            <?php
        } else { ?>
                                            <button class="btn-success btn-block btn-update no-permissions" disabled>No Permissions</button>
                                            <?php
        } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createProduct" tabindex="-1" role="dialog" aria-labelledby="create-product" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="slide-group">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <legend class="legend-header">Product type?</legend>
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <h6 class="error_message pull-right" id="product_creation_panel1-errors"></h6>
                                </div>
                            </div>

                            <div class="input-group form-group container-full">
                                <label class="input-group-addon el-hidden">Product Type</label>
                                <?php
                                if (!empty($product_types)) { ?>
                                    <select class="form-control" onchange="location = this.value;">
                                        <option value="">Product Type</option>
                                        <?php foreach ($product_types as $row) {?>
                                            <option value="<?php echo base_url("webapp/site/profile/" . $site_details->site_id . "/product/" . ((!empty($row->setting_id)) ? $row->setting_id : '')); ?>" title="<?php echo (!empty($row->value_desc)) ? $row->value_desc : '' ?>"><?php echo (!empty($row->setting_value)) ? $row->setting_value : '' ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editSite" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <?php $this->view('site/includes/edit_site_details.php'); ?>
        </div>
    </div>
</div>


<div class="modal fade" id="reallocateDevice" tabindex="-1" role="dialog" aria-labelledby="reallocate-device" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <?php $this->view('site/includes/reallocate_device.php'); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
$( document ).ready( function(){

    $( "#reallocate-device-form" ).on( "submit", function( e ){
        e.preventDefault();

        var siteId  = $( "#summary_site_id" ).val(),
            deviceId    = $( "#summary_device" ).val(),
            productId   = $( "#summary_product_id" ).val();
        
        if( !( parseInt( deviceId ) > 0 ) ){
            swal({
                title: "Incorrect Device",
                type: 'error',
            })
            return false;
        }
        
        if( !( parseInt( productId ) > 0 ) ){
            swal({
                title: "Incorrect Product",
                type: 'error',
            })
            return false;
        }

        $.ajax({
            url:"<?php echo base_url('webapp/site/reallocate_device/'); ?>",
            method: "POST",
            data: { 
                "site_id": siteId,
                "device_id": deviceId,
                "product_id": productId
            },
            dataType: "JSON",
            success: function( data ){
                if( data.status == 1 || data.status == true ){
                    swal({
                        type: 'success',
                        title: data.status_msg,
                        showConfirmButton: false,
                        timer: 6000
                    })
                } else {
                    swal({
                        type: 'error',
                        title: data.status_msg
                    })
                }
                window.setTimeout( function(){
                    location.reload();
                }, 6000 );
            },
            error: function(){
                swal({
                    title: "Unspecified Error",
                    type: 'error',
                })
            }
        });
    });


    $( "#reallocate_device_panel2" ).on( "click", function(){
        var summary_device              = $( "#reallocate_device_id" ).val(),
            summary_device_unique_id    = $( "#reallocate_device_unique_id" ).val(), 
            summary_site_id             = $( "#reallocation_site_id" ).val(),
            summary_site_name           = $( "#reallocation_site_id option:selected" ).text(),
            summary_product_id          = $( "select[name='reallocation_product_id']" ).val();
            summary_product_name        = $( "select[name='reallocation_product_id'] option:selected" ).text();

        $( "#reallocate-device-form #summary_device" ).val( summary_device );
        $( "#reallocate-device-form #summary_device_unique_id" ).val( summary_device_unique_id );
        $( "#reallocate-device-form #summary_site_id" ).val( summary_site_id );
        $( "#reallocate-device-form #summary_site_name" ).val( summary_site_name );
        $( "#reallocate-device-form #summary_product_id" ).val( summary_product_id );
        $( "#reallocate-device-form #summary_product_name" ).val( summary_product_name );
    });

    $( "#reallocate_device_panel1" ).on( "click", function(){
        var siteId      = parseInt( $( "#reallocation_site_id" ).val() );
        if( siteId > 0 ){
            $.ajax({
                url:"<?php echo base_url('webapp/site/get_product_by_site_id/'); ?>",
                method: "POST",
                data: { "site_id": siteId },
                dataType: "JSON",
                success: function( data ){
                    if( data.status == true ){
                        $( "#reallocation_products" ).html( data.dataset );
                        panelchange( ".reallocate_device_panel1", ".reallocate_device_panel", ".reallocate_device_panel2" );
                    } else {
                        swal({
                            title: "No Product found for this SIte ID",
                            type: 'error',
                        })
                        return false;
                    }
                },
                error: function(){
                    swal({
                        title: "Unspecified Error ",
                        type: 'error',
                    })
                }
            });
        } else {
            swal({
                title: "Wrong Site ID",
                type: 'error',
            })
            return false;
        }
    });

    $( "#reallocate-device-form .reallocate-device-steps" ).click( function(){
        $( '.error_message' ).each( function(){
            $( this ).text( '' );
        });

        var currentpanel = $( this ).data( "currentpanel" );
        var inputs_state = check_inputs( currentpanel );
        if( inputs_state ){
            $( '[name="' + inputs_state + '"]' ).focus();
            var labelText = $( '[name="' + inputs_state + '"]' ).parent().find( 'label' ).text();
            $( '#' + currentpanel + '-errors' ).text( ucwords( labelText ) + ' is a required');
            return false;
        }
        panelchange( "." + currentpanel, ".reallocate_device_panel" );

        return false;
    });


    $( "#devices-module #table-results" ).on( "click", ".reallocate_device_link", function(){
        var deviceId            = $( this ).data( "device_id" );
        var device_unique_id    = $( this ).data( "device_unique_id" );
        $( "#reallocate-device-form #reallocate_device_id" ).val( deviceId );
        $( "#reallocate-device-form #reallocate_device_unique_id" ).val( device_unique_id );

        $.ajax({
            url:"<?php echo base_url('webapp/site/site_by_product_type/'); ?>",
            method: "POST",
            success:function( data ){
                $( "#reallocation_site_id" ).html( data );
            }
        });

        $( "#reallocation_site_id" ).select2({
            dropdownParent: $( "#reallocateDevice" ),
            placeholder: 'Select the site',
            selectOnClose: true,
            delay: 150,
        });

        $( "#reallocateDevice" ).modal( 'show' );
    });


    $( ".airtime_activate_market_container a" ).on( "click", function(){
        var productID       = $( this ).data( "product_id" );
        var activeMarket    = $( this ).data( "is_market_active_on_airtime" );

        var title = ( parseInt( activeMarket ) > 0 ) ? "Confirm market deactivate?" : "Confirm market activate?" ;

        swal({
            title: title,
            showCancelButton: true,
            confirmButtonColor: '#5CB85C',
            cancelButtonColor: '#9D1919',
            confirmButtonText: 'Yes'
        }).then( function (result) {
            if ( result.value ) {

                if( parseInt( productID ) < 0 ){
                    swal({
                        title: 'Product ID is required',
                        type: 'error',
                    })
                    return false;
                }

                var url = ( parseInt( activeMarket ) > 0 ) ? "<?php echo base_url('webapp/site/deactivate_market'); ?>" : "<?php echo base_url('webapp/site/activate_market'); ?>" ;

                $.ajax({
                    url: url,
                    method: "POST",
                    data: { "product_id": productID },
                    dataType: 'json',
                    success:function( data ){
                        if( data.status == 1 || data.status == true ){
                            swal({
                                type: 'success',
                                title: data.status_msg,
                                showConfirmButton: false,
                                timer: 3000
                            })
                        } else {
                            swal({
                                type: 'error',
                                title: data.status_msg
                            })
                        }
                        window.setTimeout( function(){
                            location.reload();
                        }, 3000 );
                    }
                });
            }
        }).catch( swal.noop )
    });


    $( "#update-site-form" ).on( "change", "#select_system_integrator_id", function(){
        var integratorId = $( this ).val();

        if( parseInt( integratorId ) > 0 ){

            $( "select[name='site_details[system_type_id]'] option:gt(0)" ).remove();

            $.ajax({
                url:"<?php echo base_url('webapp/site/get_systems_by_integrator/'); ?>",
                method:"POST",
                data: {
                    integrator_id:integratorId,
                },
                dataType: 'json',
                success: function( data ){
                    if( data.systems ){
                        $( "select[name='site_details[system_type_id]']" ).append( data.systems );
                    } else {
                        swal({
                            type: 'error',
                            title: data.status_msg
                        })
                    }
                }
            });
        } else {
        }
    });


    $( ".service_trigger" ).on( "change", function(){
        var thisElement = $( this );

        if( parseInt( thisElement ) > 0 && parseInt( thisElement ) > 0 ){
            $.ajax({
                url:"<?php echo base_url('webapp/report/settings_value/'); ?>",
                method:"POST",
                data: {
                    royalty_type_id:rType,
                    royalty_service_id:rService,
                    provider_id:providerID,
                },
                dataType: 'json',
                success: function( data ){
                    if( data.settings_values ){
                        if( data.settings_values.setting_royalty_id ){
                            // $( thisElement ).parent().next().find( ".setting-value" ).val( "" ).val( data.settings_values.setting_value );
                        } else {
                            // $( thisElement ).parent().next().find( ".setting-value" ).val( "" )
                        }

                    } else {
                        $( thisElement ).parent().next().find( ".setting-value" ).val( "" )
                        $( thisElement ).parent().next().find( ".report-setting" ).val( "" )
                        swal({
                            type: 'error',
                            title: data.status_msg
                        })
                    }
                }
            });
        } else {
            // $( thisElement ).parent().next().find( ".setting-value" ).val( "" );
        }
    });


    $( ".service_trigger" ).on( "change", function(){
        var thisElement = $( this );
        var rType       = $( this ).data( "royalty_type_id" );
        var rService    = $( this ).val();
        var providerID  = $( ".provider-id" ).val();

        if( parseInt( rType ) > 0 && parseInt( rService ) > 0 ){
            $.ajax({
                url:"<?php echo base_url('webapp/report/settings_value/'); ?>",
                method:"POST",
                data: {
                    royalty_type_id:rType,
                    royalty_service_id:rService,
                    provider_id:providerID,
                },
                dataType: 'json',
                success: function( data ){
                    if( data.settings_values ){
                        if( data.settings_values.setting_royalty_id ){
                            $( thisElement ).parent().next().find( ".setting-value" ).val( "" ).val( data.settings_values.setting_value );
                            $( thisElement ).parent().next().find( ".report-setting" ).val( "" ).val( data.settings_values.setting_royalty_id );
                        } else {
                            $( thisElement ).parent().next().find( ".setting-value" ).val( "" )
                            $( thisElement ).parent().next().find( ".report-setting" ).val( "" )
                        }

                    } else {
                        $( thisElement ).parent().next().find( ".setting-value" ).val( "" )
                        $( thisElement ).parent().next().find( ".report-setting" ).val( "" )
                        swal({
                            type: 'error',
                            title: data.status_msg
                        })
                    }
                }
            });
        } else {
            $( thisElement ).parent().next().find( ".setting-value" ).val( "" );
            $( thisElement ).parent().next().find( ".report-setting" ).val( "" );
        }
    });

    $( ".update-uip-report-settings-btn" ).on( "click", function(){

        // var rType        = $( '.site-reports *[name="royalty_type_id"]' );
        // var rService     = $( '.site-reports *[name="royalty_service_id"]' );

        // rType.css( "border", "1px solid #bcbcbc" );
        // rService.css( "border", "1px solid #bcbcbc" );

        // if( parseInt( rType.val() ) > 0 && parseInt( rService.val() ) > 0 ){

            var formData = $( "#uip_report_settings" ).serialize();

            $.ajax({
                url:"<?php echo base_url('webapp/report/update_site_royalty_setting/'); ?>",
                method:"POST",
                data: formData,
                dataType: 'json',
                success: function( data ){
                    if( data.status == 1 ){
                        swal({
                            type: 'success',
                            title: data.status_msg,
                            showConfirmButton: false,
                            timer: 3000
                        })
                        window.setTimeout( function(){
                            window.location.reload( true );
                        }, 3000 );
                    } else {
                        swal({
                            type: 'error',
                            title: data.status_msg
                        })
                    }
                }
            });
        // } else {
            // swal({
                // type: 'error',
                // title: "Missing Required Data"
            // });

            // if( parseInt( rType.val() ) > 0 ){
                // rService.css( "border", "2px solid red" );
            // } else {
                // if( parseInt( rService.val() ) > 0 ){
                    // rType.css( "border", "2px solid red" );
                // } else {
                    // rType.css( "border", "2px solid red" );
                    // rService.css( "border", "2px solid red" );
                // }
            // }

        // }
    });


    $( ".update-window-btn" ).click( function(){
        swal({
            title: 'Confirm Window update?',
            showCancelButton: true,
            confirmButtonColor: '#5CB85C',
            cancelButtonColor: '#9D1919',
            confirmButtonText: 'Yes'
        }).then( function( result ){
            if ( result.value ) {
                var formData = $( ".update-window-form" ).serialize();
                $.ajax({
                    url:"<?php echo base_url('webapp/site/update_window/'); ?>",
                    method:"POST",
                    data: formData,
                    dataType: 'json',
                    success:function( data ){
                        if( data.status == 1 ){
                            swal({
                                type: 'success',
                                title: data.status_msg,
                                showConfirmButton: false,
                                timer: 3000
                            })
                            window.setTimeout( function(){
                                window.location.reload( true );
                            }, 3000 );
                        } else {
                            swal({
                                type: 'error',
                                title: data.status_msg
                            })
                        }
                    }
                });
            }
        }).catch( swal.noop )
    });



    $( "#months_container" ).on( "click", "#all_months", function(){

        if( $( this ).prop( "checked" ) != true ){
            $( "#months_container input[type='checkbox']" ).each(
                function(){ $( this ).prop( "checked", false ) }
            )
        } else {
            $( "#months_container input[type='checkbox']" ).each(
                function(){ $( this ).prop( "checked", true ) }
            )
        }
    });

    $( "#months_container" ).on( "click", "input[type='checkbox']:not( :first )", function(){
        if( ( $( "#all_months" ).prop( "checked" ) == true ) && ( $( this ).prop( "checked" ) != true ) ){
            $( "#all_months" ).prop( "checked", false );
        }
    });



    <?php
    if (!empty($this->session->flashdata('stats_message'))) { ?>
        swal({
            type: 'info',
            title: "<?php echo $this->session->flashdata('stats_message') ?>",
            timer: 3000
        })
    <?php } ?>

    $( ".generate_stats" ).on( "click", function( e ){
        e.preventDefault();

        $( "form#generate_stats" ).submit();
    });


    function pullAirtimePlan( thisElement, providerID ){
        if( parseInt( providerID ) > 0 ){

            $.ajax({
                url: "<?php echo base_url('webapp/provider/provider_price_plan/'); ?>",
                method: "POST",
                data: {
                    "provider_id": providerID,
                },
                dataType: 'JSON',
                success: function( data ) {
                    if( data.status == 1 ){
                        $( thisElement ).parent().parent().find( ".airtime_plan" ).removeClass( "el-hidden" ).addClass( "el-shown" );
                        $( thisElement ).parent().parent().find( ".airtime_plan_price" ).removeClass( "el-hidden" ).addClass( "el-shown" );
                        var element = $( thisElement ).parent().parent().find( '.airtime_plan_trigger' );
                        $( element ).empty().append( data.provider_price_plan );
                    } else {
                        swal({
                            type: 'error',
                            title: data.status_msg,
                            timer: 3000
                        })
                        $( thisElement ).parent().parent().find( ".airtime_plan" ).removeClass( "el-shown" ).addClass( "el-hidden" );
                        $( thisElement ).parent().parent().find( ".airtime_plan_price" ).removeClass( "" ).addClass( "el-hidden" );
                    }
                }
            });
        } else {
            $( this ).parent().next( ".airtime_plan, .airtime_plan_price" ).removeClass( "el-shown" ).addClass( "el-hidden" );
        }
    }


    $( '.content_provider_trigger' ).on( "change", function(){
        var thisElement     = $( this );
        var providerID      = thisElement.val();
        pullAirtimePlan( thisElement, providerID );
    });

    // End of the content provider trigger


    $( ".delete_price_plan_container" ).click( function(){

        var productPricePlanId = $( this ).find( "a" ).data( 'product_price_plan_id' );

        swal({
            title: 'Confirm Price Plan delete?',
            // type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#5CB85C',
            cancelButtonColor: '#9D1919',
            confirmButtonText: 'Yes'
        }).then( function (result) {
            if ( result.value ) {
                if( typeof productPricePlanId === 'undefined' || parseInt( productPricePlanId ) < 0 ){
                    swal({
                        title: 'Price Plan ID is required',
                        type: 'error',
                    })
                    return false;
                }

                $.ajax({
                    url:"<?php echo base_url('webapp/product/delete_product_price_plan/'); ?>",
                    method: "POST",
                    data: { product_price_plan_id: productPricePlanId },
                    dataType: 'json',
                    success:function( data ){
                        if( data.status == 1 ){
                            swal({
                                type: 'success',
                                title: data.status_msg,
                                showConfirmButton: false,
                                timer: 5000
                            })
                            window.setTimeout( function(){
                                window.location.reload( true );
                            }, 5000 );
                        } else {
                            swal({
                                type: 'error',
                                title: data.status_msg
                            })
                        }
                    }
                });
            }
        }).catch( swal.noop )
    });




    $( "[name='product_status_id']" ).change( function(){
        var prod_status_name    = "";
        prod_status_name        = $( "option:selected", this ).html();
        if( prod_status_name == "Active" ){
            $( this ).parent().parent().find( ".end_date" ).val( "" );
            $( this ).parent().parent().find( ".end_date" ).css( "display", "none" );
        } else {
            var today = new Date().toLocaleDateString();
            $( this ).parent().parent().find( ".end_date" ).css( "display", "block" );
        }
    })


    $( ".delete-file" ).click( function( e ){
        e.preventDefault();

        var documentID = $( this ).data( 'document_id' );

        swal({
            title: 'Confirm document delete?',
            // type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#5CB85C',
            cancelButtonColor: '#9D1919',
            confirmButtonText: 'Yes'
        }).then( function( result ){
            if ( result.value ) {
                if( parseInt( documentID ) < 0 ){
                    swal({
                        title: 'Document ID is required',
                        type: 'error',
                    })
                    return false;
                }

                $.ajax({
                    url: "<?php echo base_url('webapp/site/delete_document/'); ?>",
                    method:"POST",
                    data: { document_id: documentID },
                    dataType: 'json',
                    success:function( data ){
                        if( data.status == 1 ){
                            swal({
                                type: 'success',
                                title: data.status_msg,
                                showConfirmButton: false,
                                timer: 2000
                            })
                            window.setTimeout( function(){
                                location.reload( true );
                            }, 2000 );
                        } else {
                            swal({
                                type: 'error',
                                title: data.status_msg
                            })
                        }
                    }
                });
            }
        }).catch( swal.noop )
    });


    $( function(){
        $( 'input[type=file]' ).change( function(){
            var t = $( this ).val();
            var labelText = 'File : ' + t.substr( 12, t.length );
            $( this ).prev( 'label' ).text( labelText );
        })
    });

    $( '.file-toggle' ).click( function(){
        var classGrp = $( this ).data( 'class_grp' );
        $( '.'+classGrp ).slideToggle();
    });

    $( ".generate_file" ).on( "click", function( e ){
        e.preventDefault();

        var fileType = $( this ).data( "filetype" );
        $( "#file_type" ).val( fileType );

        $( "form#generate_file" ).submit();
    });

    $( "#disable-site-form" ).on( "submit", function( e ){
        e.preventDefault();

        var formData = $( this ).serialize();
        swal({
            title: 'Confirm Site disabling?',
            // type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#5CB85C',
            cancelButtonColor: '#9D1919',
            confirmButtonText: 'Yes'
        }).then( function( result ){
            if ( result.value ) {
                $.ajax({
                    url:"<?php echo base_url('webapp/site/disable/'); ?>",
                    method: "POST",
                    data:formData,
                    dataType: 'json',
                    success:function(data){
                        if( data.status == 1 ){
                            swal({
                                type: 'success',
                                title: data.status_msg,
                                showConfirmButton: false,
                                timer: 2000
                            })
                            window.setTimeout( function(){
                                location.reload();
                            }, 2000 );
                        } else {
                            swal({
                                type: 'error',
                                title: data.status_msg
                            })
                        }
                    }
                });
            }
        }).catch( swal.noop )
    });


    // Product Update
    $( '.productForm' ).on( "submit", function( e ){
        e.preventDefault();

        var formData = $( this ).serialize();

        swal({
            title: 'Confirm Product update?',
            // type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#5CB85C',
            cancelButtonColor: '#9D1919',
            confirmButtonText: 'Yes'
        }).then( function( result ){
            if ( result.value ) {
                $.ajax({
                    url:"<?php echo base_url('webapp/product/product_update/'); ?>",
                    method: "POST",
                    data:formData,
                    dataType: 'json',
                    success:function(data){
                        if( data.status == 1 ){
                            swal({
                                type: 'success',
                                title: data.status_msg,
                                showConfirmButton: false,
                                timer: 8000
                            })
                            window.setTimeout( function(){
                                location.reload();
                            }, 8000 );
                        } else {
                            swal({
                                type: 'error',
                                title: data.status_msg
                            })
                        }
                    }
                });
            }
        }).catch( swal.noop )
    });

    $( document ).on( "keydown", ":input:not(textarea):not(:submit)", function( event ){
        if( event.key == "Enter" ){
            event.preventDefault();
        }
    });


    $( ".delete_product_container a" ).on( "click", function(){
        var productID = $( this ).data( "product_id" );

        swal({
            title: "Confirm product delete?",
            // text: "This will permanently delete this product<br>and any price plans associated with it!",
            html: "<div>This will permanently delete this product<br />and any price plans associated with it!</div>",

            // type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#5CB85C',
            cancelButtonColor: '#9D1919',
            confirmButtonText: 'Yes'
        }).then( function (result) {
            if ( result.value ) {

                if( parseInt( productID ) < 0 ){
                    swal({
                        title: 'Product ID is required',
                        type: 'error',
                    })
                    return false;
                }

                $.ajax({
                    url: "<?php echo base_url('webapp/product/delete_product/'); ?>",
                    method: "POST",
                    data: { "product_id": productID },
                    dataType: 'json',
                    success:function( data ){
                        if( data.status == 1 || data.status == true ){
                            swal({
                                type: 'success',
                                title: data.status_msg,
                                showConfirmButton: false,
                                timer: 2000
                            })
                            window.setTimeout( function(){
                                location.reload();
                            }, 2000 );
                        } else {
                            swal({
                                type: 'error',
                                title: data.status_msg
                            })
                        }
                    }
                });
            }
        }).catch( swal.noop )
    });


    //** Validate any inputs that have the required class, if empty return the name attribute **/
    function check_inputs( currentpanel ){
        var result = false;
        var panel = "." + currentpanel;

        $( $( panel + " .required" ).get().reverse() ).each( function(){
            var fieldName = '';
            var inputValue = $( this ).val();
            if( ( inputValue == false ) || ( inputValue == '' ) || ( inputValue.length == 0 ) ){
                fieldName = $(this).attr( 'name' );
                result = fieldName;
                return result;
            }
        });
        return result;
    }

    $( ".btn-back" ).click( function(){
        var currentpanel = $( this ).data( "currentpanel" );
        go_back( "." + currentpanel )
        return false;
    });

    function panelchange( changefrom, elementClass, changeto ){
        var panelnumber = parseInt( changefrom.match(/\d+/) ) + parseInt( 1 );
        var changeto = elementClass + panelnumber;
        $( changefrom ).hide( "slide", {direction : 'left'}, 500 );
        $( changeto ).delay( 600 ).show( "slide", {direction : 'right'},500 );
        return false;
    }


    function go_back( changefrom, delay = 500 ){
        var panelnumber = parseInt( changefrom.match(/\d+/) ) - parseInt( 1 );
        var elementClass = changefrom.substr( 0, parseInt( changefrom.length ) - parseInt( panelnumber.toString().length ) );
        var changeto = elementClass + panelnumber;
        $( changefrom ).hide( "slide", {direction : 'right'}, 500 );
        $( changeto ).delay( delay ).show( "slide", {direction : 'left'},500 );
        return false;
    }


    $( ".delete_container" ).click( function(){
        swal({
            title: 'Confirm site delete?',
            // type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#5CB85C',
            cancelButtonColor: '#9D1919',
            confirmButtonText: 'Yes'
        }).then( function (result) {
            if ( result.value ) {
                var siteID = <?php echo (!empty($site_details->site_id)) ? $site_details->site_id : '""' ; ?>;
                if( parseInt( siteID ) < 0 ){
                    swal({
                        title: 'Site ID is required',
                        type: 'error',
                    })
                    return false;
                }

                $.ajax({
                    url:"<?php echo base_url('webapp/site/delete_site/'); ?>",
                    method:"POST",
                    data: { site_id: siteID },
                    dataType: 'json',
                    success:function( data ){
                        if( data.status == 1 ){
                            swal({
                                type: 'success',
                                title: data.status_msg,
                                showConfirmButton: false,
                                timer: 2000
                            })
                            window.setTimeout( function(){
                                location.href ="<?php echo base_url("webapp/site"); ?>";
                            }, 2000 );
                        } else {
                            swal({
                                type: 'error',
                                title: data.status_msg
                            })
                        }
                    }
                });
            }
        }).catch( swal.noop )
    });


    $( ".legend" ).click( function(){
        $( this ).children( ".fas" ).toggleClass( "fa-caret-down fa-caret-up" );
        $( this ).next( ".group-content" ).slideToggle( 400 );
    });



    function validEmail( email ){
        var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
        return pattern.test( email );
    }


    function validPhone( phone ){
        var pattern = /([0-9]{10})|(\([0-9]{3}\)\s+[0-9]{3}\-[0-9]{4})/;
        // if spaces allowed
        var phone = phone.replace(/\s/g, "" );
        return pattern.test( phone );
    }

    //Submit form for processing
    $( '#update-site-form' ).on( "submit", function( e ){
        e.preventDefault();

        var originalSystemIntegratorID      = $( "#original_system_integrator_id" ).val();
        var selectSystemintegratorID        = $( "#select_system_integrator_id" ).val();
        if( ( selectSystemintegratorID == '' ) && ( $.trim( selectSystemintegratorID ).length == 0 ) ){
            $( 'input[name="site_details[system_integrator_id]"]' ).val( originalSystemIntegratorID );
        } else {
            $( 'input[name="site_details[system_integrator_id]"]' ).val( selectSystemintegratorID );
        }


        // phone validation
        var phone = $( "[name='contact_details[telephone_number]']" ).val();
        if( !validPhone( phone ) ) {
            alert( 'Please enter valid phone number (only digits or spaces, min 10)' );
            return false;
        }

        // email validation
        var email = $( "[name='contact_details[email]']" ).val();
        if( !validEmail( email ) ) {
            alert( 'Please enter valid email' );
            return false;
        }

        var formData = $( this ).serialize();

        swal({
            title: 'Confirm site update?',
            // type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#5CB85C',
            cancelButtonColor: '#9D1919',
            confirmButtonText: 'Yes'
        }).then( function( result ){
            if ( result.value ) {
                $.ajax({
                    url:"<?php echo base_url('webapp/site/update_site/'); ?>",
                    method:"POST",
                    data:formData,
                    dataType: 'json',
                    success:function(data){
                        if( data.status == 1 ){
                            swal({
                                type: 'success',
                                title: data.status_msg,
                                showConfirmButton: false,
                                timer: 6000
                            })
                            window.setTimeout( function(){
                                location.reload();
                            }, 6000 );
                        } else {
                            swal({
                                type: 'error',
                                title: data.status_msg
                            })
                        }
                    }
                });
            }
        }).catch( swal.noop )
    });


    //SITE INVENTORY / SEARCH
    var $currenFilmRows = $('#current_films tr');
    $( '#search_current_films' ).keyup(function() {
        var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
        $currenFilmRows.show().filter(function() {
            var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
            return !~text.indexOf(val);
        }).hide();
    });

    //SEARCH LIBRARY FILMS
    var $libraryFilmRows = $('#library_films tr');
    $( '#search_library_films' ).keyup(function() {
        var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
        $libraryFilmRows.show().filter(function() {
            var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
            return !~text.indexOf(val);
        }).hide();
    });
});
</script>