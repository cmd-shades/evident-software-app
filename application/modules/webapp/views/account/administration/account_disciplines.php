<link rel="stylesheet" href="<?php echo base_url('assets/css/checkbox.min.css') ?>">

<style media="screen">

        .account-discipline{
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
        
        .discipline-edit {
            float: left;
            width: 70px;
            height: 100%;
            text-align: center;
            background-color: #bfbfbf;
            padding-top: 15px;
            cursor: pointer;
        }
        
        .discipline-edit:hover {
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
        
        #account-discipline-response {
            float: right;
            font-size: 12px;
        }
    
        #account-modules {
            padding: 20px;
        }
        
        .modal-title {
            font-size: 20px !important;
            margin-top: 20px;
        }
        
        #edit-discipline-modal .input-group-addon {
            min-width: 220px;
        }
        
        .modal-close-icon {
            position: absolute;
            top: 10px;
            right: 10px;
        }

</style>


<div class="row" style="padding:20px;">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <input type="hidden" name="page" value="details" />
        <input type="hidden" name="account_id" value="<?php echo $account_details->account_id; ?>" />           
        <div id="account-modules" class="x_panel tile has-shadow">
            <span id="account-discipline-response"></span>
            <legend>Account Disciplines</legend>
            <div class="container">
                <div class="row">
                    <?php if (!empty($disciplines)) {
                        foreach ($disciplines as $discipline) {?>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-xs-12 account-discipline-container">
                        <div class="account-discipline" data-discipline_id = '<?php echo $discipline->discipline_id; ?>'>
                            <div class="module-info <?php echo ( !empty($active_acc_disciplines) && !empty($active_acc_disciplines[$discipline->discipline_id]) ) ? 'mod-enabled' : 'mod-disabled'?>">
                                <h4 class="module-heading"><?php echo !empty($active_acc_disciplines[$discipline->discipline_id]) ? $active_acc_disciplines[$discipline->discipline_id]->account_discipline_name : $discipline->discipline_name; ?></h4>
                            </div>
                            
                            <div class="discipline-edit"  >
                                <i class="fas fa-pen-square fa-2x"></i>
                            </div>
                            <div class="module-enable">
                                <label class="el-switch el-switch-md">
                                    <input class="account-discipline-enable" type="checkbox" name="switch" <?php echo ( !empty($active_acc_disciplines) && !empty($active_acc_disciplines[$discipline->discipline_id]) ) ? 'checked' : ''?> >
                                    <span class="el-switch-style"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                        <?php }
                    } else {
                        echo '<div class="col-xl-2 col-lg-4 col-md-4 col-sm-4 col-xs-12 account-discipline-container"><p>No data avaliable!</p></div>';
                    }?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="edit-discipline-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Editing Discipline X</h4>
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

    $( document ).ready( function(){

        var accountID = "<?php echo $account_details->account_id; ?>";

        function labelify( input_text ){
            output_text = '';
            
            $.each( input_text.split('_'), function( i, word ){
                output_text += word.charAt(0).toUpperCase() + word.slice(1) + ' '
            });
            
            return output_text
        }

        $( '.account-discipline-enable' ).change( function(){
            discipline_enabled  = $( this ).prop( 'checked' );
            module_container    = $( this ).closest( '.account-discipline' );
            module_info_div     = $( module_container).find( '.module-info' );
            disciplineId        = $( module_container ).attr( 'data-discipline_id' );
            
            var discps_arr  = [];
            discps_arr.push( disciplineId );
            
            if( discipline_enabled ){
                
                var formData = {
                    'activation_data': {
                        'account_id': accountID,
                        'discipline_id': discps_arr
                    }
                };
                
                $.ajax({
                    url:"<?php echo base_url('webapp/account/activate_account_disciplines'); ?>",
                    method:"POST",
                    dataType: 'json',
                    data:formData,
                    success:function( data ){
                        
                        $( '#account-discipline-response' ).css( 'display', 'none' );
                        $( '#account-discipline-response' ).stop().fadeIn( 'fast' ).delay( 1500 ).fadeOut( 'slow' );
                        
                        if( data.status == 1 ){
                            
                            $( '#account-discipline-response' ).text( data.status_msg );
                            $( '#account-discipline-response' ).css('color', 'green');
                            
                            $( module_info_div ).removeClass( 'mod-disabled' );
                            $( module_info_div ).addClass( 'mod-enabled' );
                            
                        } else {
                            
                            $( module_info_div ).addClass( 'mod-disabled' );
                            $( module_info_div ).removeClass( 'mod-enabled' );
                            
                            $( '#account-discipline-response' ).text( data.status_msg )
                            $( '#account-discipline-response' ).css('color', 'red')
                        }
                    }
                });
                
            } else {
                
                var formData = {
                    'deactivation_data': {
                        'account_id': accountID,
                        'discipline_id': discps_arr
                    }
                };
                
                $.ajax({
                    url:"<?php echo base_url('webapp/account/deactivate_account_disciplines'); ?>",
                    method:"POST",
                    dataType: 'json',
                    data:formData,
                    success:function( data ){
                        
                        $( '#account-discipline-response' ).css( 'display', 'none' );
                        $( '#account-discipline-response' ).stop().fadeIn( 'fast' ).delay( 1500 ).fadeOut( 'slow' );
                        
                        if( data.status == 1 ){
                            
                            $( '#account-discipline-response' ).text( data.status_msg );
                            $( '#account-discipline-response' ).css('color', 'green');
                            
                            $( module_info_div ).addClass( 'mod-disabled' );
                            $( module_info_div ).removeClass( 'mod-enabled' );
                            
                        } else {
                            
                            $( module_info_div ).removeClass( 'mod-disabled' );
                            $( module_info_div ).addClass( 'mod-enabled' );
                            
                            $( '#account-discipline-response' ).text( data.status_msg )
                            $( '#account-discipline-response' ).css('color', 'red')
                        }
                    }
                });
                
                $( module_info_div ).removeClass( 'mod-enabled' );
                $( module_info_div ).addClass( 'mod-disabled' );

            }
        });
        
        $( '.discipline-edit' ).on('click', function(event) {
            
            var disciplineId    = $(this).closest( '.account-discipline' ).attr( 'data-discipline_id' )
            
            $.ajax({
                url:"<?php echo base_url('webapp/account/get_account_discipline') ?>",
                method:"POST",
                dataType: 'json',
                data:{ account_id:accountID , where:{discipline_id: disciplineId} },
                success:function( result ){
                    if(result.status == 1){
                        
                        discipline_data = (result.discipline_data ? result.discipline_data : false)
                        field_items = [
                            'account_discipline_id',
                            'account_discipline_name',
                            'account_discipline_desc',
                            'account_discipline_image_url',
                            'account_discipline_status',
                            'account_discipline_contact_1',
                        ];
                        
                        field_items = [
                            {field_name : 'account_discipline_id', field_type : 'text'},
                            {field_name : 'account_discipline_name', field_type : 'text'},
                            {field_name : 'account_discipline_desc', field_type : 'text'},
                            {field_name : 'account_discipline_image_url', field_type : 'text'},
                            {field_name : 'account_discipline_status', field_type : 'dropdown', options: [
                                {text: 'Active', value: 'Active'},
                                {text: 'Deactivated', value: 'Deactivated'},
                                {text: 'Un Available', value: 'Unavailable'}
                            ]},
                            {field_name : 'account_discipline_contact_1', field_type : 'dropdown', options: [
                                {text: 'Email', value: 'Email'},
                                {text: 'Phone', value: 'Phone'},
                                {text: 'Mobile', value: 'Mobile'},
                                {text: 'Website', value: 'Website'}
                            ]},
                        ]
                        
                        $( "#edit-discipline-modal" ).modal('show')
                        
                        modal_html = ''
                        
                        $.each( field_items, function( i, field ){
                            if(discipline_data.hasOwnProperty(field.field_name) || field.field_name === 'account_discipline_contact_1' || field.field_name === 'account_discipline_contact_2'){
                                
                                var showOrHide = ( field.field_name == 'account_discipline_id'  ) ? 'hidden' : '';
                                
                                modal_html  += '<div class="input-group form-group">'
                                modal_html += '<label class="input-group-addon '+ showOrHide +'">' + labelify( field.field_name ) + '</label>'
                                
                                switch (field.field_type) {
                                    case 'text':
                                        
                                        if(discipline_data[field.field_name] == null){
                                            modal_html += '<input name="' + field.field_name + '" class="form-control module-input '+ showOrHide +' " type="text" placeholder="' + labelify(field.field_name) + '">'
                                        } else {
                                            modal_html += '<input name="' + field.field_name + '" class="form-control module-input '+ showOrHide +'" type="text" placeholder="' + labelify(field.field_name) + '" value="' + discipline_data[field.field_name] + '">'
                                        }
                                        break;
                                    case 'dropdown':
                                        modal_html += '<select class="form-control module-input" name="' + field.field_name + '">'
                                        if(discipline_data[field.field_name] == null){
                                            $.each( field.options, function( i, field ){
                                                modal_html += '<option value="' + field.value + '">' + field.text + '</option>'
                                            });
                                        } else {
                                            field_value = discipline_data[field.field_name]
                                            $.each( field.options, function( i, field ){
                                                modal_html += '<option value="' + field.value + '" ' + (field.value == field_value ? 'selected' : '') + '>' + field.text + '</option>'
                                            });
                                        }

                                        if(field.field_name === 'account_discipline_contact_1' || field.field_name === 'account_discipline_contact_2'){
                                            console.log('else', 'dropdown', 'field', field)
                                        }
                                        
                                        modal_html += '</select>'
                                        break;
                                    default:
                                        
                                }
                                
                                
                                modal_html += '</div>'
                            }
                            // else {
                            //  console.log(field);
                            //
                            //  if(field.field_name === 'account_discipline_contact_1'){
                            //
                            //      modal_html += '<select class="form-control module-input" name="' + field.field_name + '">'
                            //      if(discipline_data[field.field_name] == null){
                            //          $.each( field.options, function( i, field ){
                            //              modal_html += '<option value="' + field.value + '">' + field.text + '</option>'
                            //          });
                            //      }
                            //
                            //  }
                            // }
                        });
                        
                        $('#edit-discipline-modal').find( '.modal-title').text( "Editing Discipline '" + discipline_data.account_discipline_name + "'" )
                        $('#edit-discipline-modal').attr( 'discipline_id', disciplineId )
                        $('#edit-discipline-modal').find( '.modal-body').html( modal_html )
                        
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
        
        $('#edit-discipline-modal').on('click', '.save-changes', function(event) {
            discipline_data = {}
            disciplineId = $('#edit-discipline-modal').attr('discipline_id')
            
            $('#edit-discipline-modal').find('.module-input').each(function(i, module_input) {
                input_name = $(module_input).attr('name')
                input_val = $(module_input).val()
                discipline_data[input_name] = input_val
            });

            update_base_discipline( disciplineId, discipline_data, true)
            
        })
        
        $('#edit-discipline-modal').on('click', '.modal-dismiss', function(event) {
            window.location.reload()
        })
        
        
        function update_base_discipline( disciplineId, discipline_data, showpopup = false ){
            $.ajax({
                url:"<?php echo base_url('webapp/account/update_account_discipline') ?>",
                method:"POST",
                dataType: "json",
                data:{ account_id: accountID, discipline_id: disciplineId, discipline_data: discipline_data},
                success:function( result ){
                    $( '#account-discipline-response' ).css( 'display', 'none' )
                    $( '#account-discipline-response' ).stop().fadeIn( 'fast' ).delay( 500 ).fadeOut( 'slow' )
                    
                    
                    if(!showpopup){
                        if(result.status == 1){
                            $( '#account-discipline-response' ).text( result.status_msg )
                            $( '#account-discipline-response' ).css('color', 'green')
                        } else {
                            $( '#account-discipline-response' ).text( result.status_msg )
                            $( '#account-discipline-response' ).css('color', 'red')
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
        
    });
</script>

