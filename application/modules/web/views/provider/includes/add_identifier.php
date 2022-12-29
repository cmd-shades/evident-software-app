<div class="modal-body">
    <form id="adding-packet-identifier-form">
        <input type="hidden" name="provider_id" value="<?php echo (!empty($provider_details->provider_id)) ? (int) $provider_details->provider_id : '' ; ?>" />
        <div class="packet_identifier_adding_panel1 col-md-12 col-sm-12 col-xs-12">
            <div class="slide-group">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <legend class="legend-header">Definition</legend>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <h6 class="error_message pull-right" id="packet_identifier_adding_panel1-errors"></h6>
                    </div>
                </div>

                <div class="input-group form-group container-full">
                    <label class="input-group-addon el-hidden">Definition</label>
                    <?php
                    if (!empty($definitions)) { ?>
                        <select name="definition_id" class="form-control required container-full" title="Video Definition">
                            <option value="">Please select</option>
                            <?php
                            foreach ($definitions as $def_row) { ?>
                                <option value="<?php echo (!empty($def_row->definition_id)) ? $def_row->definition_id : ''; ?>"><?php echo (!empty($def_row->definition_name)) ? ucwords(html_escape($def_row->definition_name)) : '' ; ?></option>
                                <?php
                            } ?>
                        </select>
                        <?php
                    } ?>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-lg-offset-6 col-md-6 col-md-offset-6 col-sm-6 col-sm-offset-6 col-xs-12">
                        <button class="btn-block btn-next packet-adding-steps" data-currentpanel="packet_identifier_adding_panel1" type="button">Next</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="packet_identifier_adding_panel2 col-md-12 col-sm-12 col-xs-12 el-hidden">
            <div class="slide-group">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <legend class="legend-header">Packet Type</legend>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <h6 class="error_message pull-right" id="packet_identifier_adding_panel2-errors"></h6>
                    </div>
                </div>

                <div class="input-group form-group container-full">
                    <label class="input-group-addon el-hidden">Packet Type</label>
                    <?php
                    if (!empty($codec_types)) { ?>
                        <select name="type_id" class="form-control required container-full" title="Packet Type">
                            <option value="">Please select</option>
                            <?php
                            foreach ($codec_types as $t_row) { ?>
                                <option value="<?php echo (!empty($t_row->type_id)) ? $t_row->type_id : ''; ?>"><?php echo (!empty($t_row->type_name)) ? ucwords(html_escape($t_row->type_name)) : '' ; ?></option>
                                <?php
                            } ?>
                        </select>
                        <?php
                    } ?>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <button class="btn-block btn-back" data-currentpanel="packet_identifier_adding_panel2" type="button">Back</button>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <button class="btn-block btn-next packet-adding-steps" data-currentpanel="packet_identifier_adding_panel2" type="button">Next</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="packet_identifier_adding_panel3 col-md-12 col-sm-12 col-xs-12 el-hidden">
            <div class="slide-group">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <legend class="legend-header">Packet Identifier (PID)</legend>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <h6 class="error_message pull-right" id="packet_identifier_adding_panel3-errors"></h6>
                    </div>
                </div>
                <div class="input-group form-group container-full">
                    <label class="input-group-addon el-hidden">Packet Identifier (PID)</label>
                    <select name="packet_identifier_id" class="form-control required container-full" title="Packet Identifier (PID)">
                        <option value="">Please select</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <button class="btn-block btn-back" data-currentpanel="packet_identifier_adding_panel3" type="button">Back</button>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <button class="btn-block btn-next packet-adding-steps" data-currentpanel="packet_identifier_adding_panel3" type="button">Next</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="packet_identifier_adding_panel4 col-md-12 col-sm-12 col-xs-12 el-hidden">
            <div class="slide-group">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <legend class="legend-header">Description for <span class="summary" style="font-style: italic;"></span></legend>
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <h6 class="error_message pull-right" id="packet_identifier_adding_panel4-errors"></h6>
                    </div>
                </div>

                <div class="input-group form-group container-full">
                    <label class="input-group-addon el-hidden">Description</label>
                    <textarea name="description" class="form-control container-full" type="text" value="" placeholder="Description"></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <button class="btn-block btn-back" data-currentpanel="packet_identifier_adding_panel4" type="button">Back</button>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <button class="btn-block btn-next" data-currentpanel="packet_identifier_adding_panel3" type="submit">Add PID</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>