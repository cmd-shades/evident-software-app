<div class="row group-content el-hidden">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
        <legend class="default-legend">Upload Files</legend>
        <form action="<?php echo base_url('webapp/provider/upload_docs/' . $provider_details->provider_id); ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
            <input type="hidden" name="provider_id" value="<?php echo $provider_details->provider_id; ?>" />
            <input type="hidden" name="module" value="provider" />
            <input type="hidden" name="doc_type" value="provider" />

            <div class="input-group form-group">
                <label class="input-group-addon">Provider file</label>
                <span class="control-fileupload single pointer">
                    <label for="file-upload" class="custom-file-upload">
                        <i class="fas fa-cloud-upload"></i> Select file
                    </label>
                    <input id="file-upload" name="upload_files[doc]" type="file"/>
                </span>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <button id="doc-upload-btn" class="btn btn-sm btn-block btn-success" type="submit">Upload Document(s)</button>
                </div>
            </div>
            <br/>
        </form>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
        <legend class="default-legend">Existing Documents</legend>
        <div class="row">
        <div class="col-md-12 table-responsive">
            <?php if (!empty($provider_documents)) {
                foreach ($provider_documents as $file_group => $files) { ?>
                <h5 style="color:#000" class="file-toggle pointer" data-class_grp="<?php echo str_replace(' ', '', $file_group); ?>" ><?php echo ucwords($file_group); ?> <span class="pull-right">(<?php echo count($files); ?>)</span></h5>
                                    <?php foreach ($files as $k => $file) { ?>
                    <div class="row <?php echo str_replace(' ', '', $file_group); ?>" style="display:block;padding:5px 0">
                        <div class="col-md-10" style="padding-left:30px;"><a target="_blank" href="<?php echo $file->document_link; ?>"><?php echo $file->document_name; ?></a></div>
                        <div class="col-md-2"><span class="pull-right"><a target="_blank" href="<?php echo $file->document_link; ?>"><i class="fas fa-download"></i></a> &nbsp;&nbsp;&nbsp;<i class="fas fa-trash-alt text-red delete-file" data-document_id="<?php echo (!empty($file->document_id)) ? $file->document_id : '' ; ?>"></i></span></div>
                    </div>
                                    <?php }  ?>
                <?php }
                }  ?>
        </div>
    </div>
    </div>
</div>