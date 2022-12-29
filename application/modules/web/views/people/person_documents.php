<div class="row">
	<?php if ($this->user->is_admin || !empty($permissions->can_add) || !empty($permissions->is_admin)) { ?>
		<div class="col-md-6 col-sm-6 col-xs-12">
			<form id="upload-docs-form" action="<?php echo base_url("webapp/people/upload_docs/".$person_details->person_id) ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
				<input type="hidden" name="referrer" value="documents" type="text" readonly />
				<input type="hidden" name="person_id" value="<?php echo $person_details->person_id; ?>" type="text" readonly />
				<div class="x_panel tile has-shadow">
					<legend>Upload New Document</legend>
					<div class="input-group form-group">
						<label class="input-group-addon">Document Type</label>
						<select name="doc_type" class="form-control" required >
							<option value="">Please select type</option>
							<option value="Appraisal" >Appraisal</option>
							<option value="Return To Work" >Return To Work</option>
							<option value="Disciplinary" >Disciplinary</option>
							<option value="Other" >Other</option>
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
			<legend>Personal Documents</legend>
			<div class="row">
				<div class="col-md-12 table-responsive">								
					<?php if (!empty($personal_documents)) {
					    foreach ($personal_documents as $file_group=>$files) { ?>
						<h5 class="file-toggle pointer" data-class_grp="<?php echo str_replace(' ', '', $file_group); ?>" ><?php echo ucwords($file_group); ?> <span class="pull-right">(<?php echo count($files); ?>)</span></h5>
						<?php foreach ($files as $k=>$file) { ?>
							<div class="row <?php echo str_replace(' ', '', $file_group); ?>" style="display:none;padding:5px 0">
								<div class="col-md-10" style="padding-left:30px;"><a target="_blank" href="<?php echo $file->document_link; ?>"><?php echo $file->document_name; ?></a></div>
								<div class="col-md-2"><span class="pull-right"><a target="_blank" href="<?php echo $file->document_link; ?>"><i class="fas fa-download"></i></a> &nbsp;&nbsp;&nbsp;<?php
                                $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = "details");
						    if ($this->user->is_admin || !empty($item_access->can_delete) || !empty($item_access->is_admin)) { ?>
									<i class="fas fa-trash-alt text-red delete-file" data-document_id="<?php echo (!empty($file->document_id)) ? $file->document_id : '' ; ?>"></i>
						<?php	} else { ?>
									<i class="fas fa-trash-alt text-red" data-document_id="<?php echo (!empty($file->document_id)) ? $file->document_id : '' ; ?>"></i>
						<?php 	} ?></span></div>
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
		
		$( ".delete-file" ).click( function( e ){
			e.preventDefault();
			
			var documentID = $( this ).data( 'document_id' );
			
			swal({
				title: 'Confirm document delete?',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ){
				if ( result.value ) {
					if( parseInt( documentID ) < 0 ){
						swal({
							title: 'Document ID is required',
							type: 'error',
						})
						return false;
					}
					
					$.ajax({
						url: "<?php echo base_url('webapp/people/delete_document/'); ?>",
						method:"POST",
						data: { document_id: documentID },
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2000
								})
								window.setTimeout( function(){
									location.reload( true );
								}, 2000 );
							} else {
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						}
					});
				}
			}).catch( swal.noop )
		});
		
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
				var ext = filename.substr(filename.lastIndexOf('.') + 1).toLowercase();
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