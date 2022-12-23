<style type="text/css">
.select2-container--default .selection .select2-selection--single{
    border: none;
}

.container-full .select2-container{
    width: 100% !important;
}

.btn-server-back {
    background-color: #2ebdd9;
    color: black;
}


.add_another_email a {
    cursor: pointer;
}
</style>

<div class="row">
    <div class="server_creation_panel1 col-lg-12 col-md-12 col-sm-12 col-xs-12" data-panel-index="0">
        <div class="slide-group">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <legend class="legend-header">Add / Link Distribution Server</legend>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <h6 class="error_message pull-right" id="server_creation_panel1-errors"></h6>
                </div>
            </div>
            
            <div class="input-group form-group container-full">
                <label class="input-group-addon el-hidden">Available Servers</label>
                <div class="input-field-full container-full" style="padding: 2px 0px 0px 15px;">
                    <select class="js-example-basic-single input-field-full container-full" id="coggins_server_id" name="coggins_server_id"></select>
                </div>
            </div>

                            
            <div class="input-group form-group container-full">
                <label class="input-group-addon el-hidden">Server Description for CaCTi</label>
                <input name="server_description" class="input-field-full container-full" type="text" value="" placeholder="Server Description" title="Server Description" />
            </div>
            
            <div class="row">
                <div class="col-lg-6 col-lg-offset-6 col-md-6 col-md-offset-6 col-sm-6 col-sm-offset-6 col-xs-12">
                    <button class="btn-block btn-next server-creation-steps" data-currentpanel="server_creation_panel1" type="button">Next</button>
                </div>
            </div>
        </div>
    </div>

    <div class="server_creation_panel2 col-lg-12 col-md-12 col-sm-12 col-xs-12 el-hidden" data-panel-index="1">
        <div class="slide-group">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <legend class="legend-header">Email notification Points?</legend>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <h6 class="error_message pull-right" id="server_creation_panel2-errors"></h6>
                </div>
            </div>

            <div class="notification_points">
                <?php /* Not in use yet
                <div class="input-group form-group container-full">
                    <label class="input-group-addon el-hidden">Full name of the person to notify</label>
                    <input name="notifications[0][fullname]" class="input-field-full container-full" type="text" value="" placeholder="Full name of the person to notify" title="Full name of the person to notify" />
                </div>
                */ ?>

                <div class="input-group form-group container-full">
                    <label class="input-group-addon el-hidden">Email address of the person to notify</label>
                    <input name="notifications[0][email]" class="input-field-full container-full required notifications" type="text" value="" placeholder="Email of the person to notify" title="Email of the person to notify" />
                </div>
            </div>
            
            <div id="email_outputArea">
            </div>
            
            <div class="add_another_email"><a class=""><i class="fas fa-plus-circle"></i> Add Email</a></div>

            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <button class="btn-block btn-server-back" data-currentpanel="server_creation_panel2" type="button">Back</button>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <button id="server-setting-btn" class="btn-block btn-flow btn-next" type="submit">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>