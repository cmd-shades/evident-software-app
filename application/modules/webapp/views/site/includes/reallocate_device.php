<form id="reallocate-device-form">
    <div class="reallocate_device_panel1 col-lg-12 col-md-12 col-sm-12 col-xs-12" data-panel-index="0">

        <input type="hidden" id="reallocate_device_id" name="reallocate_device_id" value="" />
        <input type="hidden" id="reallocate_device_unique_id" name="reallocate_device_unique_id" value="" />

        <div class="slide-group">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <legend class="legend-header">Reallocate Device</legend>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <h6 class="error_message pull-right" id="product_creation_panel1-errors"></h6>
                </div>
            </div>

            <div class="input-group form-group container-full" style="margin-bottom: 20px;">
                <label class="input-group">Search Sites</label>
                <div class="container-full" style="padding: 2px 0px 0px 0px;">
                    <select class="js-example-basic-single input-field-full container-full" style="width:100% !important;" id="reallocation_site_id" name="reallocation_site_id"></select>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">&nbsp;</div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <button id="reallocate_device_panel1" class="btn-block btn-next" data-currentpanel="reallocate_device_panel1" type="button">Next</button>
                </div>
            </div>
        </div>
    </div>

    <div class="reallocate_device_panel2 col-lg-12 col-md-12 col-sm-12 col-xs-12 el-hidden" data-panel-index="1">
        <div class="slide-group">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <legend class="legend-header">Reallocate Device</legend>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <h6 class="error_message pull-right" id="server_creation_panel2-errors"></h6>
                </div>
            </div>


            <div class="input-group form-group container-full" style="margin-bottom: 20px;">
                <label class="input-group">Search Products</label>
                <div class="container-full" style="padding: 2px 0px 0px 0px;">
                    <select class="input-field-full container-full" style="width:100% !important;" id="reallocation_products" name="reallocation_product_id"></select>
                </div>
            </div>


            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <button class="btn-block btn-back" data-currentpanel="reallocate_device_panel2" type="button">Back</button>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <button id="reallocate_device_panel2" class="btn-block btn-next reallocate-device-steps" data-currentpanel="reallocate_device_panel2" type="button">Next</button>
                </div>
            </div>
        </div>
    </div>


    <div class="reallocate_device_panel3 col-lg-12 col-md-12 col-sm-12 col-xs-12 el-hidden" data-panel-index="2">
        <div class="slide-group">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <legend class="legend-header">Reallocate Device</legend>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <h6 class="error_message pull-right" id="server_creation_panel2-errors"></h6>
                </div>
            </div>

            <div class="input-group form-group container-full" style="margin-bottom: 20px;">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 input-container" style="padding:0;margin:0;">
                    <label class="input-label-40">Device</label>
                    <input class="summary_device_unique_id input-field-60" id="summary_device_unique_id" type="text" placeholder="Device Unique ID" value="" />
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 input-container el-hidden" style="padding:0;margin:0;">
                    <label class="input-label-40">Device ID</label>
                    <input class="summary_device input-field-60" id="summary_device" name="device_id" type="text" placeholder="Device ID" value="" />
                </div>
                <?php /*
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 input-container el-hidden" style="padding:0;margin:0;">
                    <label class="input-label-40">Site ID</label>
                    <input class="summary_site_id input-field-60" id="summary_site_id" name="site_id" type="text" placeholder="Site ID" value="" />
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 input-container el-hidden" style="padding:0;margin:0;">
                    <label class="input-label-40">Site Name</label>
                    <input class="summary_site_name input-field-60" id="summary_site_name" type="text" placeholder="Site Name" value="" />
                </div>
                */ ?>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 input-container el-hidden" style="padding:0;margin:0;">
                    <label class="input-label-40">Product ID</label>
                    <input class="summary_product_id input-field-60" id="summary_product_id" name="product_id" type="text" placeholder="Product ID" value="" />
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 input-container" style="padding:0;margin:0;">
                    <label class="input-label-40">Site Product</label>
                    <input class="summary_product_name input-field-60" id="summary_product_name" type="text" placeholder="Product Name" value="" />
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <button class="btn-block btn-back" data-currentpanel="reallocate_device_panel3" type="button">Back</button>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <button id="server-setting-btn" class="btn-block btn-flow btn-next" type="submit">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>