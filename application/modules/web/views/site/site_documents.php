<div class="row">
	<?php if ($this->user->is_admin || !empty($permissions->can_add) || !empty($permissions->is_admin)) { ?>
		<div class="col-md-6 col-sm-6 col-xs-12">
			<form id="upload-docs-form" action="<?php echo base_url("webapp/site/upload_docs/".$site_details->site_id) ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
				<input type="hidden" name="referrer" value="documents" type="text" readonly />
				<input type="hidden" name="site_id" value="<?php echo $site_details->site_id; ?>" type="text" readonly />
				<div class="x_panel tile has-shadow">
					<legend>Upload New Document</legend>
					<div class="input-group form-group">
						<label class="input-group-addon">Document Type</label>
						<select name="doc_type" class="form-control" required >
							<option value="">Please select type</option>
							<?php if (!empty($doc_type_groups)) {
							    asort($doc_type_groups);
							    foreach ($doc_type_groups as $group) { ?>
									<option value="<?php echo html_escape(str_replace(["'", "+", "&"], "", $group)); ?>"><?php echo html_escape($group); ?></option>
							<?php }
							    } ?>
						</select>
					</div>
					<div class="input-group form-group">
						<label class="input-group-addon">Choose file</label>
						<span class="control-fileupload pointer">
							<label for="file1" class="pointer text-left">Please choose a file on your computer.</label><input name="user_files[]" type="file" id="uploadfile" >
						</span>
					</div>
					<div class="row">
						<div class="col-md-6">
							<button class="btn btn-sm btn-block btn-success" type="submit">Upload Document</button>
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
					<?php if (!empty($site_documents)) {
					    foreach ($site_documents as $file_group=>$files) { ?>
						<h5 class="file-toggle pointer" data-class_grp="<?php echo str_replace(' ', '', $file_group); ?>" ><?php echo strtoupper($file_group); ?> <span class="pull-right">(<?php echo count($files); ?>)</span></h5>
						<?php foreach ($files as $k=>$file) { ?>
							<div class="row <?php echo str_replace(' ', '', $file_group); ?>" style="display:<?php echo (!empty($selected_folder) && (strtolower($selected_folder) == strtolower($file_group))) ? 'block' : 'none' ?>; padding:5px 0">
								<div class="col-md-10" style="padding-left:30px;"><a target="_blank" href="<?php echo $file->document_link; ?>"><?php echo $file->document_name; ?></a></div>
								<div class="col-md-2"><span class="pull-right"><a target="_blank" href="<?php echo $file->document_link; ?>"><i class="fas fa-download"></i></a> &nbsp;&nbsp;&nbsp;<span class="pointer delete-file-btn" data-document_id="<?php echo $file->document_id; ?>"><i class="fas fa-trash-alt text-red"></i></span></span></div>
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
				
				var ext = filename.substr(filename.lastIndexOf('.') + 1).toLowerCase();

				if( ext!== "csv" && ext!== "xls" && ext!== "xlsx" && ext!== "pdf" && ext!== "jpg" && ext!== "jpeg" && ext!== "png" && ext!== "doc" && ext!== "docx" ) {
					swal({
						type: 'error',
						text: 'You have selected an INVALID file type: .' +ext
					})
					return false;
				}
			}
		});
		
		
		//DELETE FILE
		$( '.delete-file-btn' ).click( function( event ){
			
			var documentId = $( this ).data( 'document_id' );

			event.preventDefault();

			swal({
				type: 'warning',
				title: 'Confirm Delete file?',
				html: 'This is an irreversible action',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/audit/delete_document/'); ?>"+documentId,
						method:"POST",
						data:{ page:'details', document_group:'site', xsrf_token: xsrfToken, document_id:documentId },
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2100
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