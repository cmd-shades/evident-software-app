<div class="row">
    <?php if ($this->user->is_admin || !empty($permissions->can_add) || !empty($permissions->is_admin)) { ?>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <form id="upload-docs-form" action="<?php echo base_url("marketing/upload_docs/" . $site_details->site_id) ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
                <input type="hidden" name="referrer" value="campaign_docs" type="text" readonly />
                <div class="x_panel tile has-shadow">
                    <legend>Upload New Document</legend>
                    <div class="input-group form-group">
                        <label class="input-group-addon">Document Type</label>
                        <select name="doc_type" class="form-control" required >
                            <option value="">Please select type</option>
                            <option value="Contracts" >Signed Contract</option>
                            <option value="Surveys" >Survey</option>
                            <option value="Others" >Other</option>
                        </select>                   
                    </div>
                    <div class="input-group form-group">
                        <label class="input-group-addon">Choose file</label>
                        <span class="control-fileupload single pointer">
                            <label for="file1" class="pointer text-left">Please choose a file on your computer.</label><input name="site_files[]" type="file" id="uploadfile" >
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
    <?php } ?>
    
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="x_panel tile has-shadow">
            <legend>Site Documents</legend>
            <div class="row">
                <div class="col-md-12 table-responsive">                                
                    <?php if (!empty($site_details_docs)) {
                        foreach ($site_details_docs as $file_group => $files) { ?>
                        <h5 class="file-toggle pointer" data-class_grp="<?php echo str_replace(' ', '', $file_group); ?>" ><?php echo ucwords($file_group); ?> <span class="pull-right">(<?php echo count($files); ?>)</span></h5>
                                                    <?php foreach ($files as $k => $file) { ?>
                            <div class="row <?php echo str_replace(' ', '', $file_group); ?>" style="display:none">
                                <div class="col-md-10" style="padding-left:30px;"><?php echo $file->filename; ?></div>
                                <div class="col-md-2"><span class="pull-right"><a target="_blank" href="<?php echo $file->fileurl; ?>"><i class="fas fa-download"></i></a> &nbsp;&nbsp;&nbsp;<i class="fas fa-trash-alt text-red"></i></span></div>
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
    $(document).ready(function(){
        
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
                if( ext!== "csv" && ext!== "xls" && ext!== "xlsx" && ext!== "pdf" && ext!== "jpg" && ext!== "jpeg" && ext!== "png" && ext!== "doc" && ext!== "docx" ) {
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