<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
        <form id="add-dwellings-form" class="form-horizontal">
            <input type="hidden" name="site_id" value="<?php echo $site_details->site_id; ?>" />
            <input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
            <input type="hidden"  name="page" value="dwellings"/>
            <div class="x_panel tile has-shadow">
                <legend>Add Dwellings <em style="font-size:70%;display:none">(focus on the postcode field)</em></legend>
                <div class="input-group form-group">
                    <label class="input-group-addon" >Site Postcodes</label>
                    <input value="<?php echo !empty($site_details->site_postcodes) ? $site_details->site_postcodes : ''; ?>" class="form-control site-postcodes" type="text" placeholder="Comma separated e.g. CR0 4GE, CR0 9XP"  />
                </div>              
                <div class="dwelling-container" style="display:none">               
                    <?php if (!empty($site_addresses)) { ?>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <button class="btn btn-sm btn-block btn-flow btn-success add-dwellings-btn" type="button" >Add Selected Dwellings</button>                  
                            </div>
                        </div>
                        <div class="address-records">
                            <?php foreach ($site_addresses as $address_id => $addresss_record) { ?>
                                <?php
                                $fp_style = ($addresss_record) ? 'color:green;font-weight:600;font-style:italic' : 'color:red;font-weight:600;font-style:italic';
                                if (!empty($existing_dwellings) && in_array($addresss_record['main_address_id'], $existing_dwellings)) {
                                    $is_checked = 'checked="checked"';
                                    $check_style = 'color:green;font-weight:400;font-style:italic';
                                } else {
                                    $is_checked = $check_style = '';
                                }
                                ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label style="<?php echo $check_style; ?>" ><input type="checkbox" name="site_dwellings[]" id="address<?php echo $addresss_record['main_address_id']; ?>" value="<?php echo $addresss_record['main_address_id']; ?>" <?php echo $is_checked; ?> > <?php echo $addresss_record['summaryline']; ?></label>
                                    </div>
                                </div>
                            </div>                      
                            <?php } ?>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <button class="btn btn-sm btn-block btn-flow btn-success add-dwellings-btn" type="button" >Add Selected Dwellings</button>                  
                            </div>
                        </div>
                    <?php } else { ?>
                        <div>
                            <span>No addresses on this Block or you have an invalid postcode.</span>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </form>
    </div>
    
    <div class="col-md-6 col-sm-6 col-xs-12">
        <form action="<?php echo base_url('/vmblocks/update_block/' . $site_details->site_id); ?>" method="post" class="form-horizontal">
            <input type="hidden" name="referrer" value="marketing" type="text" readonly />
            <div class="x_panel tile has-shadow">
                <legend>Current Dwellings</legend>
                <?php if (!empty($site_dwellings)) { ?>
                    <div class="dwelling-records table-responsive">
                        <table class="table">
                            <thead>
                                <th width="10%">ID</th>
                                <th width="80%">Address summary line</th>
                                <th width="10%"><span class="pull-right">Action</span></th>
                            </thead>
                            <tbody>
                                <?php foreach ($site_dwellings as $dwelling) { ?>
                                    <?php $shorter_address = $dwelling->addressline1 . ', ' . $dwelling->addressline2 . ', ' . $dwelling->posttown . ', ' . $dwelling->county . ' ' . strtoupper($dwelling->postcode); ?>
                                <tr>
                                    <td><?php echo $dwelling->dwelling_id; ?></td>
                                    <td><?php echo $shorter_address; ?></td>
                                    <td class="pull-right"><span class="edit-modal"><i class="far fa-edit text-blue pointer" disabled></i></span> &nbsp;|&nbsp; <a class="disabled drop-dwelling" href="<?php echo base_url('/site/delete_dwelling/' . $site_details->site_id . '/' . $dwelling->dwelling_id); ?>"><i class="fas fa-trash-alt text-red pointer" disabled></i></a></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
        </form>
    </div>
</div>


<script>
    $(document).ready(function(){
        
        $('.site-postcodes').focus(function(){
            $( '.dwelling-container' ).slideDown( 'slow' );
        });

        //Submit site form
        $( '.add-dwellings-btn' ).click(function( e ){
            e.preventDefault();
            var formData = $('#add-dwellings-form').serialize();
            swal({
                title: 'Add selected dwelings?',
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if ( result.value ) {
                    $.ajax({
                        url:"<?php echo base_url('webapp/site/update_site/'); ?>",
                        method:"POST",
                        data:formData,
                        dataType: 'json',
                        success:function(data){
                            if( data.status == 1 && ( data.site !== '' ) ){
                                swal({
                                    type: 'success',
                                    title: data.status_msg,
                                    showConfirmButton: false,
                                    timer: 3000
                                })
                                window.setTimeout(function(){ 
                                    location.reload();
                                } ,3000);                           
                            }else{
                                swal({
                                    type: 'error',
                                    title: data.status_msg
                                })
                            }       
                        }
                    });
                }               
            }).catch(swal.noop)
        });
        
    });
</script>