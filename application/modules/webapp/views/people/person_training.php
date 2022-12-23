<style type="text/css">
.checkbox label:after,
.radio label:after {
    content: '';
    display: table;
    clear: both;
}

.checkbox .cr,
.radio .cr {
    position: relative;
    display: inline-block;
    border: 1px solid #a9a9a9;
    border-radius: .25em;
    width: 1.3em;
    vertical-align: bottom;
    height: 20px;
    margin: 10px 12px;
}

.radio .cr {
    border-radius: 50%;
}

.checkbox .cr .cr-icon,
.radio .cr .cr-icon {
    position: absolute;
    font-size: 0.7em;
    line-height: 0;
    top: 50%;
    left: 20%;
}

.radio .cr .cr-icon {
    margin-left: 0.04em;
}

.checkbox label input[type="checkbox"],
.radio label input[type="radio"] {
    display: none;
}

.checkbox label input[type="checkbox"] + .cr > .cr-icon,
.radio label input[type="radio"] + .cr > .cr-icon {
    transform: scale(3) rotateZ(-20deg);
    opacity: 0;
    transition: all .3s ease-in;
}

.checkbox label input[type="checkbox"]:checked + .cr > .cr-icon,
.radio label input[type="radio"]:checked + .cr > .cr-icon {
    transform: scale(1) rotateZ(0deg);
    opacity: 1;
}

.checkbox label input[type="checkbox"]:disabled + .cr,
.radio label input[type="radio"]:disabled + .cr {
    opacity: .5;
}


.input-group.form-group{
    width: 100%;
}

.input-group-addon:first-child{
    width: 60%;
    white-space: normal;
}


.checkbox .radio label, .checkbox > label{
    padding-left: 0;
    width: 100%;
}

.checkbox_label{
    width: 50%;
    min-width: 180px;
    float: left;
    padding: 12px 12px;
    font-size: 14px;
    font-weight: normal;
    line-height: 1;
    color: #555;
    text-align: center;
    background-color: #eee;
    border: 1px solid #ccc;
    border-radius: 4px;
    text-align: left;
}

/* end of checkboxes styling */


.group_title{
    margin-bottom: 0;
    margin-top: 20px;
}

.x_panel{
    padding-bottom: 20px;
}


.upd_button{
    margin-top: 20px;
}

.row.checklist legend{
    cursor: pointer;
}

.form-control.radio{
    width: 100%;
    margin-top: 0;
    display: block;
    height: auto;
}

.form-control.radio .label_inline{
    width: 33%;
    float: left;
    display: block;
    padding-top: 5px;
    padding-bottom: 5px;
}

.form-control.radio .label_inline .cr{
    margin: 0px 5px;
}

.checkbox .input-group-addon{
    padding-left: 12px;
}
</style>

<div class="row checklist">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="row">
        
        <?php
        if (!empty($checklist_questions->training)) {
            foreach ($checklist_questions->training as $type_key => $item_type) {
                if (!empty($checklist_answers->{ 'training' }->$type_key)) {
                    $answers = json_decode($checklist_answers->{ 'training' }->$type_key[0]->{ 'answers' });
                } ?>

                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="x_panel tile has-shadow">
                        <form class="form-horizontal">
                            <input type="hidden" name="page" value="details" />
                            <input type="hidden" name="person_id" value="<?php echo $person_details->person_id; ?>" />
                            <input type="hidden" name="category" value="training" />
                            <input type="hidden" name="item_type" value="<?php echo $type_key; ?>" />

                            <legend><?php echo $type_key; ?></legend>
                            <div class="section" style="display: none;">
                        <?php   foreach ($item_type as $group_key => $group) {
                            if ($group_key != 'default') { ?>
                                        <div class="input-group form-group group_title">
                                            <h4><strong><?php echo ucwords($group_key); ?></strong></h4>
                                        </div>
                            <?php	} ?>

                            <?php   foreach ($group as $question) { ?>
                                        <div class="input-group form-group checkbox">
                                            <label class="input-group-addon"><?php echo $question->item_name; ?></label>
                                            <input name="answers[<?php echo $question->question_id; ?>]" type="hidden" value="" />
                                            <div class="form-control radio">
                                                <label class="label_inline">
                                                    <input type="radio" name="answers[<?php echo $question->question_id; ?>]" value="yes" <?php echo ((!empty($answers->{ $question->question_id })) && $answers->{ $question->question_id } == 'yes') ? 'checked="checked"' : '' ; ?>>
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Yes
                                                </label>

                                                <label class="label_inline">
                                                    <input type="radio" name="answers[<?php echo $question->question_id; ?>]" value="no" <?php echo ((empty($answers->{ $question->question_id })) || $answers->{ $question->question_id } == 'no') ? 'checked="checked"' : '' ; ?>>
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>No
                                                </label>

                                                <label class="label_inline">
                                                    <input type="radio" name="answers[<?php echo $question->question_id; ?>]" value="n_a" <?php echo ((!empty($answers->{ $question->question_id })) && $answers->{ $question->question_id } == 'n_a') ? 'checked="checked"' : '' ; ?>>
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>N/A
                                                </label>
                                            </div>
                                        </div>
                            <?php	} ?>
                        <?php   } ?>
                            
                                <div class="row upd_button">
                        <?php       if ($this->user->is_admin || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <button class="btn btn-sm btn-block btn-flow btn-success btn-next checklist_update_btn" type="button">Update <?php echo ucfirst($type_key); ?></button>
                                        </div>
                        <?php	  } else { ?>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled>No permissions</button>
                                        </div>
                        <?php	  } ?>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php	}
            } else { ?>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="x_panel tile has-shadow">
                <div><h4><?php echo $this->config->item('no_records'); ?></h4></div>

        <?php } ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        
        $( ".row.checklist legend" ).click( function( e ){
            $( this ).next( ".section" ).toggle( 800 );
        })
        
        $( "form .checklist_update_btn" ).on( "click", function( e ){
            e.preventDefault();

            var dataset = $( this.form ).serialize();

            swal({
                title: 'Confirm checklist update?',
                /* type: 'question', */
                showCancelButton: true,
                confirmButtonColor: '#5CB85C',
                cancelButtonColor: '#9D1919',
                confirmButtonText: 'Yes'
            }).then( function (result) {
                if ( result.value ) {
                    $.ajax({
                        url:"<?php echo base_url('webapp/people/update_checklist/'); ?>",
                        method:"POST",
                        data: dataset,
                        dataType: 'json',
                        success:function(data){
                            if( data.status == 1 ){
                                swal({
                                    type: 'success',
                                    title: data.status_msg,
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            } else {
                                swal({
                                    type: 'error',
                                    title: data.status_msg
                                });
                            };
                            window.setTimeout(function(){
                                location.reload();
                            }, 3000);
                        }
                    });
                }
            }).catch(swal.noop)
        });
    });
</script>

