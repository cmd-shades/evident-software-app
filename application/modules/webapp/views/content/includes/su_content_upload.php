<style type="text/css">
input#uploadfile{
	display: block !important;
	right: 1px;
	top: 1px;
	height: 34px;
	opacity: 0;
	width: 100%;
	background: none;
	position: absolute;
	overflow: hidden;
	z-index: 2;
}
</style>


<div class="col-md-6 col-sm-6 col-xs-12 pull-left">
	<legend>Upload Content and Assets (csv)</legend>
	<form id="docs-upload-form" action="<?php echo base_url( 'webapp/content/su_content_upload/' ); ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
		<input type="hidden" name="uploaded" value="yes" />
		<div class="x_panel tile has-shadow">
			<legend class="legend-header">Please upload your file</legend>
			<div class="input-group form-group">
				<label class="input-group-addon">Choose file</label>
				<span class="control-fileupload single pointer">
					<label for="file1" class="pointer text-left">Please choose a CSV file.</label>
					<input id="uploadfile" name="upload_file[]" type="file" id="uploadfile" />
				</span>
			</div>
			<br/>
			<br/>
			<br/>
			<div class="row">
				<div class="col-md-6">
					<button id="doc-upload-btn" class="btn btn-sm btn-block btn-success" type="submit">Upload Content</button>					
				</div>
			</div>				
		</div>
	</form>
</div>