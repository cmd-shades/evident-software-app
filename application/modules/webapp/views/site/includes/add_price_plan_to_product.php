<div class="modal-body">
    <div class="row">
        <form id="add-price-plan-to-product">
            <input type="hidden" name="product_id" value="" />
            <input type="hidden" name="site_id" value="<?php echo (!empty($site_details->site_id)) ? $site_details->site_id : '' ; ?>" />
            
            <div class="product_creation_panel5 col-md-12 col-sm-12 col-xs-12">
                <div class="slide-group">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <legend class="legend-header"><strong>Adding the Price Plan into Product</strong><br />What is the Provider, Plan and Price?</legend>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <h6 class="error_message pull-right" id="product_creation_panel5-errors"></h6>
                        </div>
                    </div>

                    <div class="product-details">
                    </div>
                    <div class="outer-provider-plan-container">
                        <div class="provider-plan-container">
                                <div class="input-group form-group container-full">
                                    <label class="input-group-addon el-hidden">Content Provider</label>
                                    <?php
                                    if (!empty($content_providers)) { ?>
                                        <select class="form-control container-full content_provider_trigger" name="price_plans[0][provider_id]" title="Content Provider">
                                            <option value="">Please select Content Provider</option>
                                            <?php
                                            foreach ($content_providers as $row) { ?>
                                                <option value="<?php echo $row->provider_id; ?>"><?php echo (!empty($row->provider_name)) ? $row->provider_name : '[Not set]' ; ?></option>
                                                <?php
                                            } ?>
                                        </select>
                                        <?php
                                    } ?>
                                </div>
                                
                                <div class="input-group form-group container-full airtime_plan el-hidden">
                                    <label class="input-group-addon el-hidden">Airtime Plan</label>
                                    <select name="price_plans[0][plan_id]" class="airtime_plan_trigger form-control container-full">
                                    </select>
                                </div>
                                
                                <div class="input-group form-group container-full airtime_plan_price el-hidden">
                                    <label class="input-group-addon el-hidden">Airtime Plan Price</label>
                                    <input name="price_plans[0][plan_price]" class=" form-control required container-full" type="text" value="" placeholder="Airtime Plan Price" />
                                </div>
                        </div>
                    
                        <div id="outputArea"></div>
                        <div class="add_price_plan"><a class=""><i class="fas fa-plus-circle"></i> Add Price Plan</a></div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <!-- <button class="btn-block btn-back" data-currentpanel="product_creation_panel5" type="button">Back</button> -->
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <?php
                            $item_access = $this->webapp_service->check_access($this->user, $this->module_id, "details");

            if (!$this->user->is_admin || !empty($item_access->can_edit) || !empty($item_access->is_admin)) { ?>
                                <button class="btn-success btn-block" type="submit">Add Price Plan</button>
                                <?php
            } else { ?>
                                <button class="btn-success btn-block no-permissions" disabled>No Permissions</button>
                                <?php
            } ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>



<script type="text/javascript">
$( document ).ready( function(){
    $( "#add-price-plan-to-product" ).on( "submit", function(){

        var formData    = $( "#add-price-plan-to-product" ).serialize();
        var productId   = $( '#add-price-plan-to-product [name="product_id"]' ).val();
        
        if( parseInt( productId ) > 0 ){
            
            $.ajax({
                url: "<?php echo base_url('webapp/product/add_price_plan_to_product/'); ?>",
                method: "POST",
                data: formData,
                dataType: 'JSON',
                success: function( data ) {
                    if( data.status == 1 ){
                        swal({
                            type: 'success',
                            title: data.status_msg,
                            showConfirmButton: false,
                            timer: 3000
                        })
                        window.setTimeout( function(){
                            location.reload();
                        }, 3000 );
                    } else {
                        swal({
                            type: 'error',
                            title: data.status_msg,
                            timer: 3000
                        })
                    }
                }
            });
        } else {

        }
        
        return false;
    });
    
    $( ".add-price-plan-to-product" ).on( "click", function(){
        var productID = $( this ).data( "product_id" );
        $( '[name="product_id"]' ).val( '' ).val( productID );
    });
    
    // Adding dynamic field after click
    var i = 1;
    $( ".add_price_plan > a" ).on( "click", function(){
        var template2 = '<div class="provider-plan-container">';
        template2 = '<div class="input-group form-group container-full">';
        template2 += '<label class="input-group-addon el-hidden">Content Provider</label>';
            <?php
            if (!empty($content_providers)) { ?>
            template2 += '<div class="provider-plan-container">';
                template2 += '<select class="form-control container-full content_provider_trigger" name="price_plans[' + i + '][provider_id]" title="Content Provider">';
                    template2 += '<option value="">Please select Content Provider</option>';
                    <?php
                    foreach ($content_providers as $row) { ?>
                        template2 += '<option value="<?php echo $row->provider_id; ?>"><?php echo (!empty($row->provider_name)) ? $row->provider_name : "[Not set]" ; ?></option>';
                        <?php
                    } ?>
                template2 += '</select>';
                <?php
            } ?>
        
        template2 += '<div class="input-group form-group container-full airtime_plan el-hidden">';
            template2 += '<label class="input-group-addon el-hidden">Airtime Plan</label>';
            template2 += '<select name="price_plans[' + i + '][plan_id]" class="airtime_plan_trigger form-control container-full">';
            template2 += '</select>';
        template2 += '</div>';
        
        template2 += '<div class="input-group form-group container-full airtime_plan_price el-hidden">';
            template2 += '<label class="input-group-addon el-hidden">Airtime Plan Price</label>';
            template2 += '<input name="price_plans[' + i + '][plan_price]" class="form-control required container-full" type="text" value="" placeholder="Airtime Plan Price" />';
        template2 += '</div>';
        template2 += '</div>';
        template2 += '</div>';

        i++;
        $( "#outputArea" ).append( template2 );
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

    $( '#outputArea' ).on( "change", ".content_provider_trigger", function(){
        var thisElement     = $( this );
        var providerID      = thisElement.val();
        pullAirtimePlan( thisElement, providerID );
    });

    
    $( '.content_provider_trigger' ).on( "change", function(){
        var thisElement     = $( this );
        var providerID      = thisElement.val();
        pullAirtimePlan( thisElement, providerID );
    });
        
        
    $( "#is_airtime_ftg" ).on( "change", function(){
        if( $( this ).val() == 'yes' ){
            $( "#packageCharge" ).removeClass( "el-hidden" );
            $( "*[name='product_details[package_charge]']" ).addClass( "required" );
        } else {
            $( "#packageCharge" ).removeClass( "el-hidden" ).addClass( "el-hidden" );
            $( "*[name='product_details[package_charge]']" ).removeClass( "required" );
        }
    });
});
</script>