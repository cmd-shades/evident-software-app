<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12 pull-left">
        <legend>Upload Multple Records (csv) <span class="pointer">&nbsp;<small><a href="<?php echo base_url('assets/public/csv-templates/PeopleImportTemplate.csv'); ?>" target="_blank" ><i class="fas fa-download" title="Click to Download upload template"></i></a></small></span></legend>
        <form id="docs-upload-form" action="<?php echo base_url('webapp/people/upload_people/' . $this->user->account_id); ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
            <input type="hidden" name="page" value="documents" />
            <input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
            <div class="x_panel tile has-shadow">
                <legend class="legend-header">Please upload your updated file</legend>
                <div class="input-group form-group">
                    <label class="input-group-addon">Choose file</label>
                    <span class="control-fileupload pointer">
                        <label for="file1" class="pointer text-left">Please choose a file on your computer.</label><input id="uploadfile" name="upload_file[]" type="file" id="uploadfile" >
                    </span>
                </div>
                <br/>
                <br/>
                <br/>
                <div class="row">
                    <div class="col-md-6">
                        <button id="doc-upload-btn" class="btn btn-sm btn-block btn-success" type="submit" >Upload Document</button>                    
                    </div>
                </div>              
            </div>
        </form>
    </div>

    <div class="person_creation_panel1 col-md-6 col-sm-6 col-xs-12 pull-right hide">
        <legend>Add Single Person</legend>
        <form id="person-creation-form" method="post" >
            <input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
            <input type="hidden"  name="page" value="details"/>
            <div class="row">
            
                <div class="person_creation_panel1 col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel tile has-shadow">
                        <div class="row section-header">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <legend class="legend-header">Does this person exist as a system user?</legend>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="person_creation_panel1-errors"></h6>
                            </div>
                        </div>                  

                        <div class="form-group top_search">
                            <div class="input-group">
                                <input type="text" id="user-search" class="form-control user-lookup <?php echo $module_identier; ?>-search_input"  placeholder="Search by name or email user..." >
                                <span class="input-group-btn"><button id="find-user" class="btn btn-default <?php echo $module_identier; ?>-bg" type="button" >Find user</button></span>
                            </div>
                        </div>
                        <select id="user-lookup-result" name="user_id" class="form-control" >
                            <option value="" >No, create as new (also create a system user)</option>
                        </select>
                        <br/>
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-12 pull-right">
                                <button class="btn btn-block btn-flow btn-success btn-next person-creation-steps" data-currentpanel="person_creation_panel1" type="button">Next</button>                    
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="person_creation_panel2 col-md-12 col-sm-12 col-xs-12" style="display:none">
                    <div class="x_panel tile has-shadow">
                        <div class="row section-header">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <legend class="legend-header" >What is the person make and model?</legend>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="person_creation_panel2-errors"></h6>
                            </div>
                        </div>

                        <div class="input-group form-group">
                            <label class="input-group-addon" >User type</label>
                            <select id="user_type_id" name="user_type_id" class="form-control" >
                                <option>Please select</option>
                                <?php if (!empty($user_types)) {
                                    foreach ($user_types as $k => $user_type) { ?>
                                    <option value="<?php echo $user_type->user_type_id; ?>" ><?php echo $user_type->user_type; ?></option>
                                    <?php }
                                    } ?>
                            </select>
                        </div>
                        <div class="input-group form-group">
                            <label class="input-group-addon">Addressee First name</label>
                            <input name="contact_first_name" class="form-control" type="text" placeholder="Addressee First name" value="" />
                        </div>
                        <div class="input-group form-group">
                            <label class="input-group-addon">Addressee Last name</label>
                            <input name="contact_last_name" class="form-control" type="text" placeholder="Addressee Last name" value="" />
                        </div>
                        <div class="input-group form-group">
                            <label class="input-group-addon">Mobile</label>
                            <input name="contact_mobile" class="form-control" type="text" placeholder="Mobile" value="" />
                        </div>
                        <div class="hide input-group form-group">
                            <label class="input-group-addon">Telephone</label>
                            <input name="contact_number" class="form-control" type="text" placeholder="Telephone" value="" />
                        </div>
                        <div class="input-group form-group">
                            <label class="input-group-addon">Email</label>
                            <input name="contact_email" class="form-control" type="email" placeholder="Email user" value="" />
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="person_creation_panel2" type="button" >Back</button>                 
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <button class="btn btn-block btn-flow btn-success btn-next person-creation-steps" data-currentpanel="person_creation_panel2" type="button" >Next</button>                   
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="person_creation_panel3 col-md-12 col-sm-12 col-xs-12" style="display:none" >
                    <div class="x_panel tile has-shadow">
                        <div class="row section-header">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <legend class="legend-header" >Additional information <small><em>(for mobile devices only, skip if not applicable)</em></small></legend>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="person_creation_panel2-errors"></h6>
                            </div>
                        </div>
                        
                        <!-- Mobile device attributes -->
                        <div class="mobile-device-attributes" style="display:none">
                            <div class="input-group form-group">
                                <label class="input-group-addon">Phone number</label>
                                <input name="attributes[phone_number]" class="form-control" type="text" value="" placeholder="Phone number"  />
                            </div>
                            <div class="input-group form-group">
                                <label class="input-group-addon">Call allowance (minutes)</label>
                                <input name="attributes[call_allowance]" class="form-control" type="text" value="" placeholder="if applicable"  />
                            </div>
                            <div class="input-group form-group">
                                <label class="input-group-addon">Data allowance (GB)</label>
                                <input name="attributes[data_allowance]" class="form-control" type="text" value="" placeholder="if applicable"  />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="person_creation_panel3" type="button" >Back</button>                 
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <button class="btn btn-block btn-flow btn-success btn-next person-creation-steps" data-currentpanel="person_creation_panel3" type="button" >Next</button>                   
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="person_creation_panel4 col-md-12 col-sm-12 col-xs-12" style="display:none" >
                    <div class="x_panel tile has-shadow">
                        <div class="row section-header">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <legend class="legend-header" >Additional information <small><em>(for leasable persons, skip if not applicable)</em></small></legend>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="person_creation_panel2-errors"></h6>
                            </div>
                        </div>

                        <!-- PPE Attributes -->
                        <div class="mobile-device-attributes" style="display:none">
                            <div class="input-group form-group">
                                <label class="input-group-addon">Purchase price</label>
                                <input name="attributes[purchase_price]" class="form-control" type="text" value="" placeholder="if applicable"  />
                            </div>
                            <div class="input-group form-group">
                                <label class="input-group-addon">Purchase date</label>
                                <input name="attributes[purchase_date]" class="form-control datepicker" type="text" value="" placeholder="dd-mm-yyy if applicable"  />
                            </div>                      
                            <div class="input-group form-group">
                                <label class="input-group-addon">Lease price</label>
                                <input name="attributes[lease_price]" class="form-control" type="text" value="" placeholder="if applicable"  />
                            </div>
                            <div class="input-group form-group">
                                <label class="input-group-addon">Charge frequency</label>
                                <select id="charge_frequency" name="attributes[charge_frequency]" class="form-control">
                                    <option>Please select</option>
                                    <option value="One off" >One off</option>                           
                                    <option value="Weekly" >Weekly</option>                         
                                    <option value="Monthly" >Monthly</option>                           
                                    <option value="Annually" >Annually</option>                         
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="person_creation_panel4" type="button" >Back</button>                 
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <button id="create-person-btn" class="btn btn-block btn-flow btn-success btn-next" type="button" >Create Asset</button>                 
                            </div>
                        </div>                      
                    </div>                      
                </div>  

            </div>
        </form>
    </div>

</div>

<script>
    $(document).ready(function(){

        $('#docs-upload-form').submit(function(){
            
            var files = $('#uploadfile').val(); 

            if( files.length == 0 ){                
                swal({
                    type: 'error',
                    title: '<small>No files selected for upload!</small>'                   
                });
                return false;
            }
            
            var selection = document.getElementById('uploadfile');      
            for (var i=0; i < selection.files.length; i++) {            
                var filename = selection.files[i].name;
                var ext = filename.substr(filename.lastIndexOf('.') + 1);
                if( ext!== "csv" ) {
                    swal({
                        type: 'error',
                        title: '<small>You have selected an INVALID file type: .' +ext+'</small>'                   
                    })
                    return false;
                }
            } 

            $('#doc-upload-btn').attr('disabled', 'disabled');      
            
        });
        
        //Trigger user search on btn click
        $('#find-user').click(function(){
            var personDetail = encodeURIComponent( $( '#user-search' ).val() );
            var accountId    = "<?php echo $this->user->account_id; ?>";
            if( personDetail.length > 0 ){
                $.post("<?php echo base_url("webapp/people/search_for_user"); ?>",{account_id:accountId, userdata:personDetail},function(result){
                    $("#user-lookup-result").html(result["users_list"]);                
                },"json");
            }
        });

        //Trigger user search on pressing-enter key
        $( '#user-search' ).keypress( function( e ){
            if( e.which == 13 ){
                $('#find-user').click();
            }
        });
        
        $(".person-creation-steps").click(function(){
            
            //Clear errors first
            $( '.error_message' ).each(function(){
                $( this ).text( '' );
            });
            
            var currentpanel = $(this).data("currentpanel");            
            var inputs_state = check_inputs( currentpanel );            
            if( inputs_state ){
                //If name attribute returned, auto focus to the field and display arror message
                $( '[name="'+inputs_state+'"]' ).focus();
                var labelText = $( '[name="'+inputs_state+'"]' ).parent().find('label').text();
                $( '#'+currentpanel+'-errors' ).text( ucwords( labelText ) +' is a required' );
                return false;
            }
            panelchange("."+currentpanel)   
            return false;
        });
        
        //** Validate any inputs that have the required class, if empty return the name attribute **/
        function check_inputs( currentpanel ){
            
            var result = false;
            var panel  = "." + currentpanel;
            
            $( $( panel + " .required" ).get().reverse() ).each( function(){
                var fieldName  = '';
                var inputValue = $( this ).val();
                if( ( inputValue == false ) || ( inputValue == '' ) || ( inputValue.length == 0 ) ){
                    fieldName = $(this).attr( 'name' );
                    result    = fieldName;
                    return result;
                }
            });
            return result;
        }
        
        $(".btn-back").click(function(){
            var currentpanel = $(this).data("currentpanel");
            go_back("."+currentpanel)   
            return false;
        }); 
        
        function panelchange(changefrom){
            var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
            var changeto = ".person_creation_panel"+panelnumber;
            $(changefrom).hide( "slide", {direction : 'left'}, 500);
            $(changeto).delay(600).show( "slide", {direction : 'right'},500);   
            return false;   
        }
        
        function go_back( changefrom ){
            var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
            var changeto = ".person_creation_panel"+panelnumber;
            $(changefrom).hide( "slide", {direction : 'right'}, 500);
            $(changeto).delay(600).show( "slide", {direction : 'left'},500);    
            return false;   
        }
        
        //Submit person form
        $( '#create-person-btn' ).click(function( e ){
            e.preventDefault();
            
            var formData = $('#person-creation-form').serialize();
            
            swal({
                title: 'Confirm new person creation?',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function (result) {
                if ( result.value ) {
                    $.ajax({
                        url:"<?php echo base_url('webapp/people/create_person/'); ?>",
                        method:"POST",
                        data:formData,
                        dataType: 'json',
                        success:function(data){
                            if( data.status == 1 && ( data.person !== '' ) ){
                                
                                var newAssetId = data.person.person.person_id;
                                
                                swal({
                                    type: 'success',
                                    title: data.status_msg,
                                    showConfirmButton: false,
                                    timer: 3000
                                })
                                window.setTimeout(function(){ 
                                    location.href = "<?php echo base_url('webapp/people/profile/'); ?>"+newAssetId;
                                } ,3000);                           
                            }else{
                                swal({
                                    type: 'error',
                                    title: data.status_msg
                                })
                            }       
                        }
                    });
                }else{
                    $( ".person_creation_panel4" ).hide( "slide", { direction : 'left' }, 500 );
                    go_back( ".person_creation_panel2" );
                    return false;
                }
            }).catch(swal.noop)
        });
        
    });
</script>