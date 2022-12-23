<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
        <form id="upload-docs-form" action="<?php echo base_url("webapp/people/upload_docs/" . $person_details->person_id) ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
            <input type="hidden" name="person_id" value="<?php echo (!empty($person_details->person_id)) ? $person_details->person_id : '' ; ?>" type="text" readonly />
            <div class="x_panel tile has-shadow">
                <legend>Upload New Document</legend>
                <div class="input-group form-group">
                    <label class="input-group-addon">Document Type</label>
                    <select name="doc_type" class="form-control" required >
                        <option value="">Please select type</option>
                        <option value="Fleet">Fleet</option>
                        <option value="Security Screening">Security Screening</option>
                        <option value="Policies and Procedures">Policies and Procedures</option>
                        <option value="ID">ID</option>
                        <option value="HR">HR</option>
                        <option value="Certificates and Training">Certificates and Training</option>
                        <option value="Others">Other</option>
                    </select>
                </div>
                <div class="input-group form-group">
                    <label class="input-group-addon">Choose file</label>
                    <span class="control-fileupload single pointer">
                        <label for="file1" class="pointer text-left">Please choose a file on your computer.</label><input name="user_files[]" type="file" id="uploadfile" >
                    </span>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <button class="btn btn-sm btn-block btn-success" type="submit" >Upload Document</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="x_panel tile has-shadow">
            <legend>Personal Documents</legend>
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <?php if (!empty($audit_documents)) {
                        foreach ($audit_documents as $file_group => $files) { ?>
                        <h5 class="file-toggle pointer" data-class_grp="<?php echo str_replace(' ', '', $file_group); ?>" ><?php echo ucwords($file_group); ?> <span class="pull-right">(<?php echo count($files); ?>)</span></h5>
                                                    <?php foreach ($files as $k => $file) { ?>
                            <div class="row <?php echo str_replace(' ', '', $file_group); ?>" style="display:none;padding:5px 0">
                                <div class="col-md-10" style="padding-left:30px;"><a target="_blank" href="<?php echo $file->document_link; ?>"><?php echo $file->document_name; ?></a></div>
                                <div class="col-md-2"><span class="pull-right"><a target="_blank" href="<?php echo $file->document_link; ?>"><i class="fas fa-download"></i></a> &nbsp;&nbsp;&nbsp;<i class="fas fa-trash-alt text-red"></i></span></div>
                            </div>
                                                    <?php }  ?>
                        <?php }
                        }  ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $( document ).ready( function(){

        $(function() {
            $('input[type=file]').change(function(){
                var t = $(this).val();
                var labelText = 'File : ' + t.substr(12, t.length);
                $(this).prev('label').text(labelText);
            })
        });

        $('.file-toggle').click(function(){
            var classGrp = $(this).data( 'class_grp' );
            $( '.'+classGrp ).slideToggle();
        });

        $('#upload-docs-form').submit(function( e ){
            var selection = document.getElementById('uploadfile');
            if( selection.files.length == 0 ){
                swal({
                    type: 'error',
                    text: 'No file selected!'
                });
                return false;
            }

            for (var i=0; i < selection.files.length; i++) {
                var filename = selection.files[i].name;
                var ext = filename.substr(filename.lastIndexOf('.') + 1);
                if( ext!== "csv" && ext!== "xls" && ext!== "xlsx" && ext!== "pdf" && ext!== "jpg" && ext!== "JPG" && ext!== "jpeg" && ext!== "JPEG" && ext!== "png" && ext!== "doc" && ext!== "docx" ) {
                    swal({
                        type: 'error',
                        text: 'You have selected an INVALID file type: .' +ext
                    })
                    return false;
                }
            }
        });
    });
</script>