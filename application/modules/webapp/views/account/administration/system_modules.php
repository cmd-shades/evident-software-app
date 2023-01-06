<link rel="stylesheet" href="<?php echo base_url('assets/css/checkbox.min.css') ?>">

<style media="screen">

        .system-module{
            height: 50px;
            background-color: #d4d4d4;
            margin: 10px;
        }
        
        .module-info {
            float: left;
            background-color: #d4d4d4;
            width: calc(100% - 140px);
            height: 100%;
        }
        
        .module-edit {
            float: left;
            width: 70px;
            height: 100%;
            text-align: center;
            background-color: #bfbfbf;
            padding-top: 15px;
            cursor: pointer;
        }
        
        .module-edit:hover {
            background-color: #b8b8b8;
        }
        
        .module-enable {
            float: left;
            width: 70px;
            height: 100%;
        }
        
        .el-switch {
            margin-left: 18px;
            margin-top: 15px;
        }
        
        .mod-enabled {
            background-color: #3498DB;
            -webkit-transition: all 0.1s ease;
            -moz-transition: all 0.1s ease;
            -o-transition: all 0.1s ease;
            transition: all 0.1s ease;
            color: white !important;
        }
        
        .mod-disabled {
            background-color: lightgray;
            -webkit-transition: all 0.1s ease;
            -moz-transition: all 0.1s ease;
            -o-transition: all 0.1s ease;
            transition: all 0.1s ease;
            color: black !important;
        }
        
        .module-heading {
            margin-left: 25px;
            font-size: 17px;
            margin-top: 17px;
            color: inherit;
        }
        
        #system-module-response {
            float: right;
            font-size: 12px;
        }
    
        #system-modules {
            padding: 20px;
        }
        
        .modal-title {
            font-size: 20px !important;
            margin-top: 20px;
        }
        
        #edit-module-modal .input-group-addon {
            min-width: 220px;
        }
        
        .modal-close-icon {
            position: absolute;
            top: 10px;
            right: 10px;
        }

</style>


<div class="row" style='padding:20px;'>
	<div class="col-md-12 col-sm-12 col-xs-12">
		<input type="hidden" name="page" value="details" />
		<input type="hidden" name="account_id" value="<?php echo $account_details->account_id; ?>" />			
		<div id='system-modules' class="x_panel tile has-shadow">
            <span id='system-module-response'></span>
			<legend>System Modules</legend>
			<div class='container'>
                <div class='row'>
                    <?php if( !empty( $system_modules )) { foreach( $system_modules as $module ) {?>
                    <div class='col-xl-2 col-lg-3 col-md-4 col-sm-4 col-xs-12 system-module-container'>
                        <div class='system-module' data-module_id = '<?php echo $module->module_id; ?>'>
                            <div class='module-info <?php echo $module->is_active ? 'mod-enabled' : 'mod-disabled'?>'>
                                <h2 class='module-heading'><?php echo $module->module_name; ?></h2>
                            </div>
                            <div class='module-edit'>
                                <i class="fas fa-pen-square fa-2x"></i>
                            </div>
                            <div class='module-enable'>
                                <label class="el-switch el-switch-md">
                                	<input class='system-module-enable' type="checkbox" name="switch" <?php echo $module->is_active ? 'checked' : '' ?>>
                                	<span class="el-switch-style"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                <?php } } else{ echo '<div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-xs-12 system-module-container"><p>No data avaliable!</p></div>'; }?>
                </div>
            </div>
		</div>
	</div>
</div>

<div id="edit-module-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Editing Module X</h4>
        <button type="button" class="close modal-close-icon modal-dismiss" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary modal-dismiss">Close</button>
        <button type="button" class="btn btn-primary save-changes">Save changes</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">

    function labelify( input_text ){
        output_text = '';
        
        $.each( input_text.split('_'), function( i, word ){
            output_text += word.charAt(0).toUpperCase() + word.slice(1) + ' '
        });
        
        return output_text
    }

    $( '.system-module-enable' ).change( function(){
        module_enabled = $( this ).prop( 'checked' )
        module_container = $( this ).closest( '.system-module' )
        module_info_div = $( module_container).find( '.module-info' )
        module_id = $( module_container ).attr( 'data-module_id' )
        if( module_enabled ){
            $( module_info_div ).removeClass( 'mod-disabled' );
            $( module_info_div ).addClass( 'mod-enabled' )
            update_base_module( module_id, { is_active: 1 } )
            
        } else {
            $( module_info_div ).removeClass( 'mod-enabled' );
            $( module_info_div ).addClass( 'mod-disabled' )
            update_base_module( module_id, { is_active: 0 } )
        }
    });
    
    $( '.module-edit' ).on('click', function(event) {
        module_id = $(this).closest('.system-module').attr('data-module_id')
        
        $.ajax({
            url:"<?php echo base_url('webapp/account/get_system_module') ?>",
            method:"POST",
            dataType: 'json',
            data:{ module_id: module_id },
            success:function( result ){
                if(result.status == 1){
                    
                    module_data = (result.module_data ? result.module_data : false)
                    field_items = ['module_name', 'module_ranking', 'module_price', 'module_price_management', 'module_price_intelligence', 'description', 'mobile_visible'];
                    
                    field_items = [
                        {field_name : 'module_name', field_type : 'text'},
                        {field_name : 'module_ranking', field_type : 'text'},
                        {field_name : 'module_price', field_type : 'text'},
                        {field_name : 'module_price_management', field_type : 'text'},
                        {field_name : 'module_price_intelligence', field_type : 'text'},
                        {field_name : 'mobile_visible', field_type : 'dropdown', options: [ {text: 'Enabled', value: 1}, {text: 'Disabled', value: 0} ]}
                    ]
                    
                    $( "#edit-module-modal" ).modal('show')
                    
                    modal_html = ''
                    
                    $.each( field_items, function( i, field ){
                        if(module_data.hasOwnProperty(field.field_name)){
                            modal_html  += '<div class="input-group form-group">'
                            modal_html += '<label class="input-group-addon">' + labelify( field.field_name ) + '</label>'
                            
                            switch (field.field_type) {
                                case 'text':
                                    if(module_data[field.field_name] == null){
                                        modal_html += '<input name="' + field.field_name + '" class="form-control module-input" type="text" placeholder="' + labelify(field.field_name) + '">'
                                    } else {
                                        modal_html += '<input name="' + field.field_name + '" class="form-control module-input" type="text" placeholder="' + labelify(field.field_name) + '" value="' + module_data[field.field_name] + '">'
                                    }
                                    break;
                                case 'dropdown':
                                    modal_html += '<select class="form-control module-input" name="' + field.field_name + '">'
                                    if(module_data[field.field_name] == null){
                                        $.each( field.options, function( i, field ){
                                            modal_html += '<option value="' + field.value + '">' + field.text + '</option>'
                                        });
                                    } else {
                                        field_value = module_data[field.field_name]
                                        $.each( field.options, function( i, field ){
                                            modal_html += '<option value="' + field.value + '" ' + (field.value == field_value ? 'selected' : '') + '>' + field.text + '</option>'
                                        });
                                    }
                                    
                                    modal_html += '</select>'
                                    break;
                                default:
                                    
                            }
                            
                            
                            modal_html += '</div>'
                        }
                    });
                    
                    $('#edit-module-modal').find( '.modal-title').text( "Editing Module '" + module_data.module_name + "'" )
                    $('#edit-module-modal').attr( 'module_id', module_id )
                    $('#edit-module-modal').find( '.modal-body').html( modal_html )
                    
                } else {
                    Swal.fire({
                      title: 'Error!',
                      text: "There was an error while attempting to load system data for this module!",
                      type: 'error',
                    })
                    
                }
            }
        })
    })
    
    $('#edit-module-modal').on('click', '.save-changes', function(event) {
        module_data = {}
        module_id = $('#edit-module-modal').attr('module_id')
        
        $('#edit-module-modal').find('.module-input').each(function(i, module_input) {
            input_name = $(module_input).attr('name')
            input_val = $(module_input).val()
            module_data[input_name] = input_val
        });

        update_base_module( module_id, module_data, true)
        
    })
    
    $('#edit-module-modal').on('click', '.modal-dismiss', function(event) {
        window.location.reload()
    })
    
    
    function update_base_module( module_id, module_data, showpopup = false ){
        $.ajax({
            url:"<?php echo base_url('webapp/account/update_system_module') ?>",
            method:"POST",
            dataType: "json",
            data:{ module_id: module_id, module_data: module_data},
            success:function( result ){
                $( '#system-module-response' ).css( 'display', 'none' )
                $( '#system-module-response' ).stop().fadeIn( 'fast' ).delay( 500 ).fadeOut( 'slow' )
                
                
                if(!showpopup){
                    if(result.status == 1){
                        $( '#system-module-response' ).text( result.status_msg )
                        $( '#system-module-response' ).css('color', 'green')
                    } else {
                        $( '#system-module-response' ).text( result.status_msg )
                        $( '#system-module-response' ).css('color', 'red')
                    }
                } else {
                    if(result.status == 1){
                        Swal.fire({
                              title: 'Success',
                              text: "Updated module successfully!",
                              type: 'success',
                              showConfirmButton: false,
                              timer: 1000
                        })
                    } else {
                        Swal.fire({
                              title: 'Error!',
                              text: "Failed to update module details!",
                              type: 'error',
                              showConfirmButton: false,
                              timer: 1000
                        })
                    }
                }
                
                
            }
        });
    }
    
</script>

