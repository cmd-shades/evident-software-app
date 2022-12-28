<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		
		<div class="x_panel tile has-shadow">
			<legend>Engineer Job Status View</legend>
			
			<div class="row">
				<div class="col-md-2 col-sm-2 col-xs-12"><input type="input" class="form-control datepicker" placeholder="Job Date"/></div>
				<div class="col-md-1 col-sm-1 col-xs-12"><button class="btn btn btn-block btn-success">Fetch Jobs</button></div>
			</div>
			<hr>
			<div class="accordion" id="accordion" role="tablist" aria-multiselectable="true">
				<?php if( !empty( $evidoc_questions )){ $counter = 1; ?>
					<?php foreach( $evidoc_questions as $section => $section_questions ){ $counter++; ?>
						<div class="panel has-shadow">
							<div class="section-container-bar panel-heading collapsed bg-grey pointer no-radius" role="tab" id="heading<?php echo number_to_words( $counter ); ?>" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo number_to_words( $counter ); ?>" aria-expanded="true" aria-controls="collapse<?php echo number_to_words( $counter ); ?>">
								<h4 class="panel-title"><i class="caret-icon fas fa-caret-down text-yellow"></i> <?php echo ucwords( $section ); ?> (<?php echo ( is_object( $section_questions ) ) ? count( object_to_array( $section_questions ) ) : count( $section_questions ) ; ?>)</h4>
							</div>
							<div id="collapse<?php echo number_to_words( $counter ); ?>" class="panel-collapse collapse no-bg no-background <?php echo ( !empty( $toggled_section ) && ( strtolower( lean_string( $toggled_section ) ) == strtolower( lean_string( $section ) ) ) ) ? 'show_toggled' : ''?>" role="tabpanel" aria-labelledby="heading<?php echo number_to_words( $counter ); ?>" >
								<div class="panel-body">
									<div class="table-responsive">
										<div class="col-md-12 col-sm-12 col-xs-12">
											<table class="table">
												<thead>
													<tr>
														<th width="35%">Question / Label Name</th>
														<th width="15%">Response Type</th>
														<th width="15%">Response Options</th>
														<th width="10%" class="text-center" >Status</th>
														<!-- <th width="15%" class="text-center" >Order</th> -->
														<th width="10%"><span class="pull-right">Action</span></th>
													</tr>
												</thead>
												<?php foreach( $section_questions as $k => $question ){ ?>
													<tr>
														<td width="35%"><?php echo $question->question; ?></td>
														<td width="15%"><span title="<?php echo $question->response_type; ?>" ><?php echo $question->response_type; ?></span></td>
														<td width="15%"><span title="" ><?php echo ( is_array( $question->response_options ) ) ? implode( " | ", $question->response_options ) : ( is_object( $question->response_options ) ? json_encode( $question->response_options ) : $question->response_options ) ; ?></span></td>
														<td width="10%" class="text-center" ><?php echo ( $question->is_active == 1 ) ? '<i class="far fa-check-circle text-green" title="This Question is active" ></i>' : '<i class="far fa-times-circle text-red" title="This Question is currently disabled" ></i>'; ?></td></td>
														<!-- <td width="15%" class="text-center" ><span title="<?php echo $question->ordering; ?>" ><?php echo $question->ordering; ?></span></td> -->
														<td width="10%"><span class="pull-right"><span class="edit-evidoc-question pointer" data-question_id="<?php echo $question->question_id; ?>" data-section_name="<?php echo $section; ?>" title="Click to Edit this record"><i class="far fa-edit"></i> Edit</span> &nbsp; &nbsp; <span class="delete-evidoc-question pointer text-red" data-question_id="<?php echo $question->question_id; ?>" data-section_name="<?php echo $section; ?>" title="Click to Delete this record" ><i class="far fa-trash-alt"></i> Delete</span></span></td>
													</tr>
												<?php } ?>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
						<br/>
					<?php } ?>
				<?php } else{ ?>
					<div class="no-results">
						<div><?php echo $this->config->item('no_records'); ?></span> </div>							
					</div>
				<?php } ?>	
			</div>
			
		</div>
	</div>
</div>