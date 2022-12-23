<div class="row group-content el-hidden">
    <div class="row">
        <?php
        $todays_date = date('Y-m-d');
        if (!empty($price_plans)) {
            foreach ($price_plans as $plan_row) {?>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="row price-plan-list-item container-full">
                        <div class="col-lg-10 col-md-11 col-sm-11 col-xs-10">
                            <span class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><?php echo (!empty($plan_row->price_plan_name)) ? $plan_row->price_plan_name : '' ; ?></span>
                            <span class="col-lg-6 col-md-6 col-sm-6 col-xs-6">Period (<?php echo (isset($plan_row->start_period)) ? $plan_row->start_period : 'N/A' ; ?> -> <?php echo (isset($plan_row->end_period)) ? $plan_row->end_period : 'N/A' ; ?>) </span>
                        </div>
                        <div class="col-lg-2 col-md-1 col-sm-1 col-xs-2 pull-right">
                            <span class="delete-price-plan" data-provider_plan_id="<?php echo (!empty($plan_row->provider_plan_id)) ? $plan_row->provider_plan_id : '' ;?>"><div class=""><a href="#"><i class="fas fa-trash-alt"></i></a></div></span>
                        </div>
                    </div>
                </div>
                <?php
            }
        } ?>
    </div>
</div>