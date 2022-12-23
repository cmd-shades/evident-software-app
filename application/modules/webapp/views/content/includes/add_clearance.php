<div class="modal-body">
    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
    <form id="adding-clearance-to-content-form" >
        <input type="hidden" name="content_id" value="<?php echo (!empty($content_details->content_id)) ? ($content_details->content_id) : '' ; ?>" />
        <div class="row">
    
            <?php
            $st_slide_visible = (isset($module_id) && ($module_id == 3) && ($this->router->fetch_class() == "content")) ? false : true ;   ?>
        
            <div class="adding-clearance-to-content1 col-md-12 col-sm-12 col-xs-12 <?php echo ($st_slide_visible) ? "el-shown" : "el-hidden" ; ?>">
                <div class="slide-group">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <legend class="legend-header">Territory clearance</legend>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <h6 class="error_message pull-right" id="adding-clearance-to-content1-errors"></h6>
                        </div>
                    </div>

                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">What territory clearance would you like to add:</label>
                        <select class="form-control container-full" title="Adding a Clearance">
                            <option value="">Please select</option>
                            <option value="single-to-single">Single content</option>
                            <option value="single-to-multiple">Multiple content</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 col-lg-offset-6 col-md-6 col-md-offset-6 col-sm-6 col-sm-offset-6 col-xs-12">
                            <button class="btn-block btn-next adding-clearance-to-content-steps" data-currentpanel="adding-clearance-to-content1" type="button">Next</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="adding-clearance-to-content2 col-md-12 col-sm-12 col-xs-12 <?php echo ($st_slide_visible) ? "el-hidden" : "el-shown" ; ?>"">
                <div class="slide-group">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <legend class="legend-header">What is the Clearance Date?</legend>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <h6 class="error_message pull-right" id="adding-clearance-to-content2-errors"></h6>
                        </div>
                    </div>

                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">Clearance Date</label>
                        <input class="form-control container-full datetimepicker" name="clearance_start_date" type="text" placeholder="Clearance Start Date" value="" />
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button class="btn-block btn-back el-hidden" data-currentpanel="adding-clearance-to-content2" type="button">Back</button>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button class="btn-block btn-next adding-clearance-to-content-steps" data-currentpanel="adding-clearance-to-content2" type="button">Next</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="adding-clearance-to-content3 col-md-12 col-sm-12 col-xs-12 el-hidden">
                <div class="slide-group">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <legend class="legend-header">What is the territory?</legend>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <h6 class="error_message pull-right" id="adding-clearance-to-content3-errors"></h6>
                        </div>
                    </div>

                    <div class="input-group form-group container-full">
                        <label class="input-group-addon el-hidden">What is the territory?</label>
                        <?php if (!empty($remains_territories)) { ?>
                            <ul class="territory_list" title="Territory Name">
                                <?php
                                $terr_numb  = count((array) $remains_territories);
                            $col11_num  = ($terr_numb % 2 == 1) ? floor($terr_numb / 2) + 1 : ($terr_numb / 2) ;

                            $i = 1;
                            $coll1 = $coll2 = false;

                            foreach ($remains_territories as $ter_row) {
                                if ($i <= $col11_num) {
                                    if (!$coll1) { ?>
                                        <div class="col_1">
                                            <li>
                                                <label for="all_territories">
                                                    <input type="checkbox" id="all_territories" value="" /> 
                                                    <span class="territory_name">All Territories</span>
                                                </label>
                                            </li>
                                            <?php
                                        $coll1 = true;
                                    } ?>
                                        <li>
                                            <label for="<?php echo(strtolower($ter_row->country)); ?>">
                                                <input type="checkbox" name="territories[]" id="<?php echo(strtolower($ter_row->country)); ?>" value="<?php echo (!empty($ter_row->territory_id)) ? $ter_row->territory_id : '' ; ?>" /> <span class="territory_name weak"><?php echo ucwords(strtolower($ter_row->country)); ?></span>
                                            </label>
                                        </li>
                                        <?php
                                    if ($i ==  $col11_num) {
                                        echo '</div>';
                                    }
                                } else {
                                    if (!$coll2) {
                                        echo '<div class="col_2">';
                                        $coll2 = true;
                                    } ?>
                                        <li>
                                            <label for="<?php echo(strtolower($ter_row->country)); ?>">
                                                <input type="checkbox" name="territories[]" id="<?php echo(strtolower($ter_row->country)); ?>" value="<?php echo (!empty($ter_row->territory_id)) ? $ter_row->territory_id : '' ; ?>" /> <span class="territory_name weak"><?php echo ucwords(strtolower($ter_row->country)); ?></span>
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
                            <p>No territories have been set or all territories are already added.</p>
                            <?php
                        } ?>
                        
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <button class="btn-block btn-back" data-currentpanel="adding-clearance-to-content3" type="button">Back</button>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <button class="btn-block btn-next" data-currentpanel="adding-clearance-to-content3" type="submit">Add Clearance</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>