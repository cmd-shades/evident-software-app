<link rel="stylesheet" href="<?php echo base_url('assets/css/select2.min.css') ?>">
<link rel="stylesheet" href="<?php echo base_url('assets/css/checkbox.min.css') ?>">

<style media="screen">

        #edit-data-table {
              table-layout: auto;
              text-align: center;
        }
        
        #edit-data-table td, #edit-data-table th {
            text-align: center;
            text-transform: capitalize;
        }
        
        #edit-data-table th {
            padding-bottom: 5px;
        }

        .system-table{
            height: 40px;
            margin-bottom: 12px;
            background-color: #5C5C5C;
        }
        
        .table-info {
            float: left;
            color: white;
            width: calc(100% - 100px);
            height: 100%;
            font-size: 14px;
        }
        
        .table-destroy {
            float: left;
            width: 50px;
            height: 100%;
            text-align: center;
            background-color: #3498DB;
            padding-top: 12px;
            font-size: 14px;
            cursor: pointer;
            color: white;
        }
        
        .table-edit {
            float: left;
            width: 50px;
            height: 100%;
            text-align: center;
            background-color: #db9b34;
            padding-top: 12px;
            font-size: 14px;
            cursor: pointer;
            color: white;
        }
        
        .row-edit {
            width: 39px;
            text-align: center;
            background-color: #db9b34;
            padding: 8px;
            font-size: 11px;
            float: right;
            cursor: pointer;
            color: white;
        }
        
        .module-heading {
            margin-left: 25px;
            font-size: 17px;
            color: inherit;
            font-weight: 100;
        }
        
        #system-table-response {
            float: right;
            font-size: 12px;
        }
    
        #system-tables {
            padding: 20px;
            background-color: #F7F7F7;
        }
        
        .modal-title {
            font-size: 20px !important;
            margin-top: 20px;
        }
        
        #edit-module-modal .input-group-addon {
            min-width: 220px;
        }
        
    
        
        .module-container {
            margin-top: 20px;
        }

        .add-table {
            max-width: 200px;
            position: absolute;
            top: 25px;
            right: 25px;
        }
        
        .module-heading small {
            color: white;
        }
        
        .tables-list {
            margin: 10px;
            height: 50vh;
            background-color: lightgray;
            border-radius: 5px;
            overflow-y: auto;
        }
        
        .configure-step {
            margin-bottom: 20px;
        }
        
        .modal-backdrop.in {
          opacity: 0.1;
        }

</style>


<div class="modal fade bd-example-modal-xl" id="add-table-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add a table to Configure</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                    <input type="hidden"  name="page" value="details"/>
                    <div class="row">
                        <div class="col-md-12">
                            <div class='configure-step step-select-module'>
                                <legend class='legend-header'>Please select the module that relates to this table</legend>
                                <select id='module-select' name='module_id' style="width:100%" class='config-input'>
                                    <option value="" disabled selected>Select a table</option>
                                    <?php foreach($system_modules as $module){
                                            echo '<option value="' . $module->module_id . '">' . $module->module_name . '</option>'; 
                                    } ?>
                                </select>
                            </div>
                            <div class='configure-step step-select-table reset-hide'  style='display:none;'>
                                <legend class='legend-header'>Please select a table to configure</legend>
                    			<select id='table-select' name="table_name" style="width:100%" class='config-input' >
                    				<option value="" disabled selected>Select a table</option>
                                    <?php foreach($available_tables as $table){
                                            echo '<option value="' . $table . '">' . $table . '</option>'; 
                                    } ?>
                    			</select>
                            </div>
                            <div class='configure-step step-primary-key reset-hide'  style='display:none;'>
                                <legend class='legend-header'>Choose a column to order by</legend>
                    			<select id='primary-select' name="order_column" style="width:100%" class='config-input'>
                    				<option value="" disabled selected>Select a table</option>
                    			</select>
                                <div style='margin-top:20px'>
                                    <legend class='legend-header'>What name would you like to give this list?</legend>
                                    <input type="text" name='list_name_alt' class='form-control config-input' placeholder='My New List'>
                                </div>
                            </div>
                            <button id='add-table-btn' class="btn btn-block btn-flow btn-success btn-next reset-hide" type="button" style='display:none;'>Add Table</button>	
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bd-example-modal-xl" id="edit-table-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style='min-width:90%'>
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Editing Table X</h4>
              <button type="button" class="close modal-close-icon modal-dismiss" aria-label="Close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <table id='edit-data-table' class="table table-responsive" style="margin-bottom:0px;">
					<thead class='edit-table-columns'></thead>
                    <tbody class='edit-table-data'></tbody>
				</table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary modal-dismiss"  data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade bd-example-modal-xl" id="edit-row-modal" tabindex="2" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Editing Table X</h4>
              <button type="button" class="close modal-close-icon modal-dismiss" aria-label="Close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary modal-dismiss"  data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary save-changes">Save changes</button>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <input type="hidden" name="page" value="details" />			
        <div id='system-tables' class="x_panel tile has-shadow" style='padding:40px;'>
            <a id='add-table' class="btn btn-block btn-success success-shadow add-table"><i class="fas fa-plus-circle" style="font-size: 18px;"></i></a>
            <legend>Configrable Tables / Modules</legend>
            <div class='container'  style='padding:20px;'>
                <div class='row'>
                    <?php if( !empty( $config_tables )) { foreach( $config_tables as $module_name => $module_tables ) {?>
                        <div class='module-container' >
                            <div class='row'>
                            <legend><?php echo $module_name; ?></legend>
                            <?php foreach($module_tables as $config_table){?>
                                <div class='col-xl-4 col-lg-4 col-md-6 col-sm-6 col-xs-12 system-table-container '>
                                    <div class='system-table' data-attr_config_id = '<?php echo $config_table->id ?>' data-attr_config_table = '<?php echo $config_table->table_name; ?>'>
                                        <div class='table-info mod-enabled'>
                                            <h2 class='module-heading'> <span style='text-transform:capitalize'><?php echo $config_table->list_name_alt . ' <small>' . str_replace('_', ' ', $config_table->table_name); ?></small></span></small></h2>
                                        </div>
                                        <div class='table-edit'>
                                            <i class="fas fa-edit"></i>
                                        </div>
                                        <div class='table-destroy'>
                                            <i class="fas fa-times"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            </div>
                        </div>
                    <?php } } ?>
                </div>
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
    
    $('#module-select').on('change', function(event) {
        if($(this).val() != null){
            $('#add-table-modal').find('.step-select-table').slideDown()
        }
    })
    
    $('#table-select').on('change', function(event) {
        if($(this).val() != null){
            
            $('#add-table-modal').find('.step-primary-key').slideDown()
            $('#primary-select').empty()

            $.ajax({
                url:"<?php echo base_url('webapp/account/get_table_columns'); ?>",
                method:"GET",
                data:{ 'table_name' : $('#table-select').val()},
                dataType: "json",
                success:function( result ){
                    if(result.status == 1){
                        table_columns = result.table_columns
                        $.each( table_columns, function( i, table_column ){
                            var newOption = new Option(table_column, table_column, false, false);
                            $('#primary-select').append(newOption).trigger('change');
                        });
                        
                    } else {
                        Swal.fire({
                              title: 'Error!',
                              text: 'We failed to fetch columns for this table!',
                              type: 'error',
                        })
                    }
                }
            });
        }
    })
    
    $('#primary-select').on('change', function(event) {
        if($(this).val() != null){
            $('#add-table-btn').slideDown();
        }
    });
    
    $('#add-table').on('click', function(event) {
        $('#add-table-modal').modal('show');
    })
    
    $('#add-table-modal').on('hidden.bs.modal', function (e) {
        $('#add-table-modal').find('.reset-hide').css('display', 'none');
    })
    
    $(document).ready(function() {
        
         $('#table-select').select2({
             dropdownParent: $("#add-table-modal")
         });
         
         $('#module-select').select2({
             dropdownParent: $("#add-table-modal")
         });
         
         $('#primary-select').select2({
             dropdownParent: $("#add-table-modal")
         });
         
         $('.table-destroy').on('click', function(event) {
             
             settings_id = $(this).closest('.system-table').attr('data-attr_config_id')
             
             Swal.fire({
               title: 'Delete configuration?',
               text: "Are you sure you want to clear this table's configuration?",
               type: 'warning',
               showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Clear configuration'
             }).then((result) => {
               if(result.value){
                   
                   $.ajax({
                       url:"<?php echo base_url('webapp/account/delete_config_entry'); ?>",
                       method:"POST",
                       data:{ settings_id : settings_id },
                       dataType: "JSON",
                       success:function( result ){
                           if(result.status){
                               Swal.fire({
                                 title: 'Success',
                                 text: 'The table you have selected has been cleared',
                                 type: 'success',
                                 timer: 3000
                             }).then((result) => {
                               window.location.reload()
                             })
                           }
                       }
                   });
                   
               }
             })
             
         })
         
         $('.table-edit').on('click', function(event) {
             var table_name = $(this).closest('.system-table').attr('data-attr_config_table')
             
             $.ajax({
                 url:"<?php echo base_url('webapp/account/get_related_config_table'); ?>",
                 method:"POST",
                 dataType: "JSON",
                 data:{ table_name : table_name },
                 success:function( result ){
                     console.log(result)
                     if(result.status == 1){
                         
                         var table_header_html = ''
                         
                         $.each( result.table_columns, function( i, table_column ){
                             table_header_html += '<th>' + labelify(table_column) + '</th>'
                         });
                         
                         // add a row for the edit buttons
                         table_header_html += '<th></th>'
                         
                         $('#edit-table-modal').find('.edit-table-columns').html(table_header_html)
                         
                         var table_body_html = ''
                         
                         dataAvailable = false
                         
                         $.each( result.table_data, function( i, table_row ){
                             table_body_html += '<tr class="data-table_row">'
                             
                             $.each( result.table_columns, function( i, table_column ){
                                 table_body_html += '<td class="data-table_value" data-cell_value = "' + ( (table_row[table_column] == null) ? '' :  table_row[table_column] ) + '" data-table_column="' + table_column + '">' + ( (table_row[table_column] == null) ? '-' :  labelify(table_row[table_column]) )+ '</td>'
                                 dataAvailable = true;
                             });
                             
                             table_body_html += '<td class="edit-row"><div class="row-edit"> <i class="fas fa-edit"></i> </div></td>'
                             
                             table_body_html += '</tr>'
                         });
                         
                         if(dataAvailable){
                             $('#edit-table-modal').find('.edit-table-data').html(table_body_html)
                         } else {
                             $('#edit-table-modal').find('.edit-table-data').html('<tr><td colspan="100%">No Data is available!</td></tr>')
                         }
                         
                         $('#edit-table-modal').modal('show');
                     } else {
                         Swal.fire({
                           title: 'Error',
                           text: "We failed to read the data for this table!",
                           type: 'error',
                         })
                     }
                 }
             });
         })
         
         
         $('#add-table-btn').on('click', function(event) {
            event.preventDefault()
            
            config_data = {}
            
            $('#add-table-modal').find('.config-input').each(function(i, input_element) {
                config_data[$(input_element).attr('name')] = $(input_element).val()
            });
            
            $.ajax({
                url:"<?php echo base_url('webapp/account/update_config_table'); ?>",
                method:"POST",
                dataType: 'JSON',
                data: {config_data : config_data},
                success:function( result ){
                    if(result.status == 1){
                      swal({
                          title: 'Success',
                          text: "Successfully updated configurable tables",
                          type: 'success',
                          timer: 3000
                      }).then((result) => {
                          window.location.reload()
                      })
                    } else {
                        Swal.fire({
                          title: 'Error',
                          text: (result.message) ? result.message : 'An unknown error has occured!',
                          type: 'error',
                        })
                    }
                }
            });
            
         })
         
         $('#edit-table-modal').on('click', '.row-edit', function(event) {
             modal_html = ''
             $(this).closest('.data-table_row').find('.data-table_value').each(function(i, data_cell) {
                 field_name = $(data_cell).attr('data-table_column')
                 field_value = $(data_cell).attr('data-cell_value')
                 
                 modal_html  += '<div class="input-group form-group">'
                    modal_html += '<label class="input-group-addon">' + labelify( field_name ) + '</label>'
                    modal_html += '<input name="' + field_name + '" class="form-control module-input" type="text" placeholder="' + labelify(field_name) + '" value="' + field_value + '">'
                 modal_html += '</div>'
             });
             $('#edit-row-modal').find('.modal-body').html(modal_html)
             $('#edit-row-modal').modal('show')
         })

    });
    
    
</script>