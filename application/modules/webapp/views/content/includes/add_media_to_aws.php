<div class="modal-body">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<form id="adding-media-to-aws-form" data-content_id="<?php echo ( !empty( $content_details->content_id ) ) ? ( $content_details->content_id ) : '' ; ?>">
		<div class="row">
			<div class="adding-media-to-aws1 col-md-12 col-sm-12 col-xs-12">
				<div class="slide-group">
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<legend class="legend-header">Content Compile Review</legend>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<h6 class="error_message pull-right" id="adding-media-to-aws2-errors"></h6>
						</div>
					</div>

					<div class="input-group form-group container-full files-table">
						<?php
						if( !empty( $decoded_file_streams ) ){ ?>
							<div id="content_compile_review_aws_files" class="table">
								<div class="section-container">
									<div class="section-line header">
										<div class="col-file-file-name text-bold">FILE NAME</div>
										<div class="col-file-type text-bold text-center">TYPE</div>
										<div class="col-file-onaws text-bold text-center">ON AWS</div>
										<div class="col-file-onat text-bold text-center">AIRTIME ID</div>
										<div class="col-file-check text-bold text-center">SELECT&nbsp;<input class="checked-content-all" type="checkbox" /></div>
									</div>
								</div>
								<div class="section-container section-movie-files">	
									<?php
									if( !empty( $decoded_file_streams ) ){
										foreach( $decoded_file_streams as $key => $file ){ ?>
											<div class="section-line" data-doc_id="<?php echo ( !empty( $file->document_id ) ) ? $file->document_id : '' ; ?>" data-file_id="<?php echo ( !empty( $file->file_id ) ) ? $file->file_id : '' ; ?>">
												<div class="col-file-file-name text-left"><?php echo ( !empty( $file->file_new_name ) ) ? $file->file_new_name : '' ; ?><?php echo ( !empty( $file->main_record ) ) ? '<span title="Main Record"> *</span>' : '' ; ?></div>
												<div class="col-file-type text-center"><?php echo ( !empty( $file->type_alt_name ) ) ? ucfirst( $file->type_alt_name ) : '' ; ?></div>
												
												<?php /* This (below) is related to the second Techlive bucket: */ ?>
												<div class="col-file-onaws text-center"><?php echo ( !empty( $file->is_on_aws ) ) ? '<i class="fas fa-check text-green"></i>' : '<i class="fas fa-times text-red"></i>' ;?></div>
												
												<div class="col-file-onat text-center"><?php echo ( !empty( $file->airtime_reference ) ) ? '<i class="fas fa-check text-green" title="'.$file->airtime_reference.'"></i>' : '<i class="fas fa-times text-red"></i>' ;?></div>
												<div class="col-file-check text-center">
													<input class="aws-checked-document" type="checkbox" name="checked_document[]" value="<?php echo ( !empty( $file->file_id ) ) ? $file->file_id : '' ; ?>" <?php echo ( !empty( $file->airtime_reference ) ) ? 'checked = "checked"' : '' ; ?> data-file_id="<?php echo ( !empty( $file->file_id ) ) ? $file->file_id : '' ; ?>" data-file_type="<?php echo ( !empty( $file->type_alt_name ) ) ? $file->type_alt_name : '' ; ?>" />
												</div>
											</div>
										<?php
										} 
									}?>
								</div>
							</div>
						<?php
						} else { ?>
							<div id="content_compile_review_aws_files" class="table">
								<div class="section-container">
									<div class="section-line"><div class="no-top-border text-bold">Files not found</div></div>
								</div>
							</div>
						<?php
						} ?>
					</div>

					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">&nbsp;</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn-block btn-next disabled" data-currentpanel="adding-media-to-aws2" type="submit" disabled="disabled">Send Selected Files</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>