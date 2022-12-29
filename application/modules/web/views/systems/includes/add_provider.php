<div class="modal-body">
    <form id="adding-provider-to-system-form" >
        <input type="hidden" name="system_type_id" value="<?php echo (!empty($systems_details->system_type_id)) ? ($systems_details->system_type_id) : '' ; ?>" />
        <div class="row">
            <div class="adding-provider-to-system1 col-md-12 col-sm-12 col-xs-12">
                <div class="slide-group">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <legend class="legend-header">What is the Provider Approval Date?</legend>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <h6 class="error_message pull-right" id="adding-provider-to-system1-errors"></h6>
                        </div>
                    </div>

                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">Approval Date</label>
                        <input class="form-control container-full datetimepicker" name="approval_date" type="text" placeholder="Approval Date" value="" />
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button class="btn-block btn-back" data-currentpanel="adding-provider-to-system1" type="button">Back</button>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button class="btn-block btn-next adding-provider-to-system-steps" data-currentpanel="adding-provider-to-system1" type="button">Next</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="adding-provider-to-system2 col-md-12 col-sm-12 col-xs-12 el-hidden">
                <div class="slide-group">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <legend class="legend-header">What is the Provider?</legend>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <h6 class="error_message pull-right" id="adding-provider-to-system2-errors"></h6>
                        </div>
                    </div>

                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">What is the Provider?</label>
                        <?php if (!empty($remaining_providers)) { ?>
                            <ul class="providers_list" title="Provider Name">
                                <?php
                                $prov_numb  = count((array) $remaining_providers);
                            $col11_num  = ($prov_numb % 2 == 1) ? floor($prov_numb / 2) + 1 : ($prov_numb / 2) ;
                            $i = 1;
                            $coll1 = $coll2 = false;

                            foreach ($remaining_providers as $prov_row) {
                                if ($i <= $col11_num) {
                                    if (!$coll1) { ?>
                                        <div class="col_1">
                                            <li>
                                                <label for="all_providers">
                                                    <input type="checkbox" id="all_providers" value="" /> 
                                                    <span class="provider_name">All Providers</span>
                                                </label>
                                            </li>
                                            <?php
                                        $coll1 = true;
                                    } ?>
                                        <li>
                                            <label for="<?php echo(strtolower($prov_row->provider_reference_code)); ?>">
                                                <input type="checkbox" name="providers[]" id="<?php echo(strtolower($prov_row->provider_reference_code)); ?>" value="<?php echo (!empty($prov_row->provider_id)) ? $prov_row->provider_id : '' ; ?>" /> <span class="provider_name weak"><?php echo ucwords(strtolower($prov_row->provider_name)); ?></span>
                                            </label>
                                        </li>
                                        <?php
                                    if ($i == $col11_num) {
                                        echo '</div>';
                                    }
                                } else {
                                    if (!$coll2) {
                                        echo '<div class="col_2">';
                                        $coll2 = true;
                                    } ?>
                                        <li>
                                            <label for="<?php echo(strtolower($prov_row->provider_reference_code)); ?>">
                                                <input type="checkbox" name="providers[]" id="<?php echo(strtolower($prov_row->provider_reference_code)); ?>" value="<?php echo (!empty($prov_row->provider_id)) ? $prov_row->provider_id : '' ; ?>" /> <span class="provider_name weak"><?php echo ucwords(strtolower($prov_row->provider_name)); ?></span>
                                            </label>
                                        </li>
                                        <?php
                                } ?>
                                    <?php
                                $i++;
                            } ?>
                                </div>
                            </ul>
                            <?php
                        } else { ?>
                            <p>No providers have been set or all providers are already added.</p>
                            <?php
                        } ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button class="btn-block btn-back" data-currentpanel="adding-provider-to-system2" type="button">Back</button>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button class="btn-block btn-next" data-currentpanel="adding-provider-to-system2" type="submit">Add Providers</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>