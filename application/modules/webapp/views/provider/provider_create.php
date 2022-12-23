<div id="add-new-provider" class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="left-container">
            <!-- // Left container -->
            <div class="row">
                <h1>Add Provider</h1>
            </div>
            <div class="row">
                <div class="step-name-wrapper current" data-group-name="Provider Details">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="step-name">Provider Details</div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="step-status"><span class="tick_box el-hidden"><i class="fas fa-check"></i></span><span class="x-cross el-hidden"><i class="fas fa-times"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="step-name-wrapper" data-group-name="Provider Category">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="step-name">Provider Category</div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="row">
                            <div class="step-status"><span class="tick_box el-hidden"><i class="fas fa-check"></i></span><span class="x-cross el-hidden"><i class="fas fa-times"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- // Left container - END -->




    <div class="col-lg-9 col-md-9 col-sm-6 col-xs-12">
        <!-- // Right container -->
        <div class="right-container">
            <div class="row">
                <div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12 col-xs-offset-0">
                    <form id="provider-creation-form">
                        <div class="row">
                            <div class="provider_creation_panel1 col-md-6 col-sm-12 col-xs-12" data-panel-index="0">
                                <div class="slide-group">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <legend class="legend-header">What's the Provider Details?</legend>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <h6 class="error_message pull-right" id="provider_creation_panel1-errors"></h6>
                                        </div>
                                    </div>
                                    <div class="input-group form-group container-full">
                                        <label class="input-group-addon el-hidden">Provider Name</label>
                                        <input name="provider_name" class="form-control" type="text" value="" placeholder="Provider Name" title="Provider Name" />
                                    </div>

                                    <div class="input-group form-group container-full">
                                        <label class="input-group-addon el-hidden">Provider Reference Code</label>
                                        <input name="provider_reference_code" class="form-control required" type="text" value="" placeholder="Provider Reference Code" title="Provider Reference Code" />
                                    </div>

                                    <div class="input-group form-group container-full">
                                        <label class="input-group-addon el-hidden">Provider Description</label>
                                        <input name="provider_description" class="form-control" type="text" value="" placeholder="Provider Description" title="Provider Description" />
                                    </div>

                                    <div class="input-group form-group container-full el-hidden">
                                        <label class="input-group-addon el-hidden">Provider Group</label>
                                        <input name="provider_group" class="form-control" type="text" value="" placeholder="Provider Group" title="Provider Description" />
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6 col-lg-offset-6 col-md-6 col-md-offset-6 col-sm-6 col-sm-offset-6 col-xs-12">
                                            <button class="btn-block btn-next provider-creation-steps check-reference-button" data-currentpanel="provider_creation_panel1" type="button">Next</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="provider_creation_panel2 col-md-6 col-sm-12 col-xs-12 el-hidden" data-panel-index="2">
                                <div class="slide-group">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <legend class="legend-header">What's the Provider Category?</legend>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <h6 class="error_message pull-right" id="provider_creation_panel2-errors"></h6>
                                        </div>
                                    </div>

                                    <div class="input-group form-group container-full">
                                        <label class="input-group-addon el-hidden">Provider Category</label>
                                        <?php if (!empty($provider_categories)) { ?>
                                        <select name="content_provider_category_id" class="form-control" title="Provider Category">
                                            <option value="">Provider Category</option>
                                            <?php foreach ($provider_categories as $key => $row) { ?>
                                            <option value="<?php echo (!empty($row->setting_id)) ? $row->setting_id : '' ; ?>">
                                                <?php echo (!empty($row->provider_category_name)) ? $row->provider_category_name : '' ; ?></option>
                                            <?php } ?>
                                        </select>
                                        <?php } ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button class="btn-block btn-back" data-currentpanel="provider_creation_panel2" type="button">Back</button>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <button id="create-provider-btn" class="btn-block btn-flow btn-next" type="button">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- // Right container - END -->
</div>

<script type="text/javascript">
    $("input[name='provider_name']").on("input", function(){
        title_text = $( "*[name = 'provider_name' ]" ).val().replace( /[^a-z0-9]+/gi, "" ).toLowerCase();
        $("*[name = 'provider_reference_code' ]").val( title_text );
        
    });

    //Submit provider form
    $('#create-provider-btn').click(function(e) {
        e.preventDefault();
        var formData = $('#provider-creation-form').serialize();

        swal({
            title: 'Confirm new provider creation?',
            showCancelButton: true,
            confirmButtonColor: '#5CB85C',
            cancelButtonColor: '#9D1919',
            confirmButtonText: 'Yes'
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: "<?php echo base_url('webapp/provider/create_provider/'); ?>",
                    method: "POST",
                    data: formData,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == 1 && (data.provider !== '')) {

                            var providerId = data.provider.provider_id;

                            swal({
                                type: 'success',
                                title: data.status_msg,
                                showConfirmButton: false,
                                timer: 3000
                            })
                            window.setTimeout(function() {
                                location.href = "<?php echo base_url('webapp/provider/profile/'); ?>" + providerId;
                            }, 3000);
                        } else {
                            swal({
                                type: 'error',
                                title: data.status_msg
                            })
                        }
                    }
                });
            } else {
                $( ".provider_creation_panel1" ).hide( "slide", {
                    direction: 'left'
                }, 500);
                go_back( ".provider_creation_panel2" );
                return false;
            }
        }).catch(swal.noop)
    });


    $(".provider-creation-steps").click(function() {
        //Clear errors first
        $('.error_message').each(function() {
            $(this).text('');
        });

        var currentpanel = $(this).data("currentpanel");
        var inputs_state = check_inputs(currentpanel);
        if (inputs_state) {
            //If name attribute returned, auto focus to the field and display arror message
            $('[name="' + inputs_state + '"]').focus();
            var labelText = $('[name="' + inputs_state + '"]').parent().find('label').text();
            $('#' + currentpanel + '-errors').text(ucwords(labelText) + ' is a required');
            return false;
        }

        if ($(this).hasClass("check-reference-button")) {
            ref = $("*[name = 'provider_reference_code' ]").val()

            if (!(ref == "")) {
                $.ajax({
                    url: "<?php echo base_url('webapp/provider/check_reference/'); ?>",
                    method: "POST",
                    data: {
                        "reference": ref,
                        "module": "provider"
                    },
                    dataType: 'json',
                    success: function(data) {
                        if ((data.status == 1)) {
                            swal({
                                type: 'error',
                                title: data.status_msg,
                                timer: 3000
                            })
                        } else {
                            panelchange("." + currentpanel)
                        }
                    }
                });
            } else {
                swal({
                    type: 'error',
                    title: "Reference can not be empty!",
                    timer: 3000
                })
            }
        } else {
            panelchange("." + currentpanel)
        }
        return false;
    });

    //** Validate any inputs that have the required class, if empty return the name attribute **/
    function check_inputs(currentpanel) {

        var result = false;
        var panel = "." + currentpanel;

        $($(panel + " .required").get().reverse()).each(function() {
            var fieldName = '';
            var inputValue = $(this).val();
            if ((inputValue == false) || (inputValue == '') || (inputValue.length == 0)) {
                fieldName = $(this).attr('name');
                result = fieldName;
                return result;
            }
        });
        return result;
    }


    $(".btn-next").click(function() {
        current_panel_id = $("." + $(this).data("currentpanel")).attr("data-panel-index")
        $($(".tick_box")[current_panel_id]).removeClass("el-hidden")

    });

    $(".btn-back").click(function() {
        var currentpanel = $(this).data("currentpanel");
        go_back("." + currentpanel)
        return false;
    });

    function panelchange(changefrom) {
        var panelnumber = parseInt(changefrom.match(/\d+/)) + parseInt(1);
        var changeto = ".provider_creation_panel" + panelnumber;
        $(changefrom).hide("slide", {
            direction: 'left'
        }, 500);
        $(changeto).delay(600).show("slide", {
            direction: 'right'
        }, 500);
        return false;
    }

    function go_back(changefrom) {
        var panelnumber = parseInt(changefrom.match(/\d+/)) - parseInt(1);
        var changeto = ".provider_creation_panel" + panelnumber;
        $(changefrom).hide("slide", {
            direction: 'right'
        }, 500);
        $(changeto).delay(600).show("slide", {
            direction: 'left'
        }, 500);
        return false;
    }
</script>