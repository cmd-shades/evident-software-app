<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 content-profile-details">
	<form id="update-content-form" method="post" >
		<input type="hidden" name="page" value="details" />

		<div class="x_panel tile group-container content">
			<input type="hidden" name="content_id" value="<?php echo ( !empty( $content_details->content_id ) ) ? $content_details->content_id : '' ; ?>" />
			<h4 class="legend pointer"><i class="fas fa-caret-down"></i>Content Details</h4>
			<div class="row group-content el-hidden">
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label-60">Content Provider</label>
						<?php
						if( !empty( $content_providers ) ){ ?>
							<select name="content_details[content_provider_id]" class="input-field-40">
								<option value="">Please select</option>
								<?php
								foreach( $content_providers as $provider_id => $row ){ ?>
									<option value="<?php echo $provider_id; ?>" <?php echo ( !empty( $content_details->content_provider_id ) && ( $content_details->content_provider_id == $provider_id ) ) ? 'selected="selected"' : "" ; ?>><?php echo $row->provider_name; ?></option>
								<?php
								} ?>
							</select>
						<?php
						} ?>
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label-60">Provider Reference Code for Asset</label>
						<input class="input-field-40" name="content_details[content_provider_reference_code]" type="text" placeholder="Provider Reference Code for Asset" value="<?php echo !empty( $content_details->content_provider_reference_code ) ? $content_details->content_provider_reference_code : '' ; ?>" />
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label-60">Is UIP nominated?</label>
						<select name="content_details[is_uip_nominated]" class="input-field-40">
							<option value="">Please select</option>
							<option value="yes" <?php echo ( !empty( $content_details->is_uip_nominated ) && ( $content_details->is_uip_nominated == true ) ) ? 'selected="selected"' : "" ; ?>>Yes</option>
							<option value="no" <?php echo ( empty( $content_details->is_uip_nominated ) || ( $content_details->is_uip_nominated != true ) ) ? 'selected="selected"' : "" ; ?>>No</option>
						</select>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label-60">Is Content Active?</label>
						<select name="content_details[is_content_active]" class="input-field-40">
							<option value="">Please select</option>
							<option value="yes" <?php echo ( !empty( $content_details->is_content_active ) && ( $content_details->is_content_active == true ) ) ? 'selected="selected"' : "" ; ?>>Yes</option>
							<option value="no" <?php echo ( empty( $content_details->is_content_active ) || ( $content_details->is_content_active != true ) ) ? 'selected="selected"' : "" ; ?>>No</option>
						</select>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label-60">Order Date</label>
						<input class="input-field-40 datetimepicker" name="content_details[order_date]" type="text" placeholder="Order Date" value="<?php echo ( validate_date( $content_details->order_date ) ) ? format_date_client( $content_details->order_date ) : '' ; ?>" />
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label-60">Delivered Date</label>
						<input class="input-field-40 datetimepicker" name="content_details[delivered_date]" type="text" placeholder="Delivered Date" value="<?php echo ( validate_date( $content_details->delivered_date ) ) ? format_date_client( $content_details->delivered_date ) : '' ; ?>" />
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label-60">Last Ingestion Date</label>
						<input class="input-field-40 readonly" name="content_details[last_ingestion_date]" type="text" placeholder="Last Ingestion Date" value="<?php echo ( validate_date( $content_details->last_ingestion_date ) ) ? format_date_client( $content_details->last_ingestion_date ) : '12/03/2019' ; ?>" readonly />
					</div>
				</div>
				<div class="row">
					<hr class="group-divider" />
	 			</div>
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label-60">Is Airtime Asset?</label>
						<select name="content_details[is_airtime_asset]" class="input-field-40">
							<option value="Yes" <?php echo ( strtolower( $content_details->is_airtime_asset ) == 'yes' ) ? 'selected="selected"' : "" ; ?> >Yes</option>
							<option value="No"  <?php echo ( strtolower( $content_details->is_airtime_asset ) != 'yes' ) ? 'selected="selected"' : "" ; ?> >No</option>
						</select>
					</div>
				</div>
				<?php
				if( $this->user->is_admin || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<div class="row">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									<button class="update-content-btn btn btn-block btn-update btn-primary" type="button" data-content_section="content">Update</button>
								</div>
							</div>
						</div>
					</div>
				<?php
				} else { ?>
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>
						</div>
					</div>
				<?php
				} ?>
			</div>
		</div>

		<div class="x_panel tile group-container imdb_details">
			<input type="hidden" name="content_id" value="<?php echo $content_details->content_id; ?>" />
			<h4 class="legend pointer"><i class="fas fa-caret-down"></i>Film Details</h4>
			<div class="row group-content el-hidden">
				<input type="hidden" name="imdb_details[is_airtime_asset]" value="<?php echo ( !empty( $content_details->is_airtime_asset ) && strtolower( $content_details->is_airtime_asset ) == 'yes' ) ? 'Yes' : 'No' ; ?>" />
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">Title</label>
						<input class="input-field" name="imdb_details[title]" type="text" placeholder="Title" value="<?php echo ( $content_details->title ) ? ( $content_details->title ) : '' ; ?>" />
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">Content Type</label>
						<select name="imdb_details[type]" class="input-field" placeholder="Content Type" >
							<option value="">Please select</option>
							<option value="movie" <?php echo ( !empty( $content_details->type ) && ( strtolower( $content_details->type == "movie" ) ) ) ? 'selected="selected"' : '' ; ?>>Movie</option>
							<option value="series" <?php echo ( !empty( $content_details->type ) && ( strtolower( $content_details->type == "series" ) ) ) ? 'selected="selected"' : '' ; ?>>Series</option>
							<option value="episode" <?php echo ( !empty( $content_details->type ) && ( strtolower( $content_details->type == "episode" ) ) ) ? 'selected="selected"' : '' ; ?>>Episode</option>
							<option value="adult" <?php echo ( !empty( $content_details->type ) && ( strtolower( $content_details->type == "adult" ) ) ) ? 'selected="selected"' : '' ; ?>>Adult</option>
						</select>
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">Tagline</label>
						<input class="input-field" name="imdb_details[tagline]" type="text" placeholder="Tagline" value="<?php echo ( $content_details->tagline ) ? ( $content_details->tagline ) : '' ; ?>" />
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">Asset Code</label>
						<input class="input-field" name="imdb_details[asset_code]" type="text" placeholder="Asset Code" value="<?php echo ( $content_details->asset_code ) ? ( $content_details->asset_code ) : '' ; ?>" />
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">Synopsis</label>
						<textarea class="input-field" name="imdb_details[plot]" type="text" placeholder="Synopsis"><?php echo ( $content_details->plot ) ? ( $content_details->plot ) : '' ; ?></textarea>
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">Release Year</label>
						<input class="input-field" name="imdb_details[release_year]" type="text" placeholder="Release Year" value="<?php echo ( $content_details->release_year ) ? ( $content_details->release_year ) : '' ; ?>" />
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">Certificates</label>
						<?php
						if( !empty( $age_rating ) ){ ?>
							<select name="imdb_details[age_rating_id]" class="input-field" placeholder="Restrictions" >
								<option value="">Please select</option>
								<?php
								foreach( $age_rating as $key => $r_row ){ ?>
									<option value="<?php echo ( !empty( $r_row->age_rating_id ) ? $r_row->age_rating_id : '' ); ?>" title="<?php echo ( !empty( $r_row->age_rating_desc ) ? $r_row->age_rating_desc : '' ); ?>" <?php echo ( !empty( $content_details->age_rating_id ) && ( $content_details->age_rating_id == $r_row->age_rating_id ) ) ? 'selected="selected"' : "" ; ?>><?php echo ( !empty( $r_row->age_rating_desc ) ? $r_row->age_rating_desc : '' ); ?></option>
								<?php
								} ?>
							</select>
						<?php
						} ?>
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">Release Date</label>
						<input class="input-field datetimepicker" name="imdb_details[release_date]" type="text" placeholder="Release Date" value="<?php echo ( validate_date( $content_details->release_date ) ) ? format_date_client( $content_details->release_date ) : '' ; ?>" />
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">Running Time</label>
						<input class="input-field" name="imdb_details[running_time]" type="text" placeholder="Running Time (mins)" value="<?php echo ( !empty( $content_details->running_time ) ) ? ( $content_details->running_time ) : '' ; ?>" />
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">Genre</label>
						<div id="genres_container"></div>
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">Director</label>
						<input class="input-field" name="imdb_details[director]" type="text" placeholder="Director" value="<?php echo ( !empty( $content_details->director ) && ( json_decode( $content_details->director ) == true ) ) ? json_to_string( $content_details->director ) : ( ( !empty( $content_details->director ) && ( $content_details->director != "[]" ) ) ? htmlentities( $content_details->director ) : '' ) ; ?>" />
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">Actors</label>
						<input class="input-field" name="imdb_details[actors]" type="text" placeholder="Actors" value="<?php echo ( !empty( $content_details->actors ) && ( json_decode( $content_details->actors ) == true ) ) ? json_to_string( $content_details->actors ) : ( ( !empty( $content_details->actors ) && ( $content_details->actors != "[]" ) ) ? htmlentities( $content_details->actors ) : '' ) ; ?>" />
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">IMDb ID</label>
						<input class="input-field" name="imdb_details[imdb_id]" type="text" placeholder="IMDb ID" value="<?php echo ( !empty( $content_details->imdb_id ) ) ? $content_details->imdb_id : '' ; ?>" />
					</div>

					<?php /*
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
							<label class="input-label">imdb_link</label>
							<input class="input-field" name="imdb_details[imdb_link]" type="text" placeholder="IMDb Link" value="<?php echo ( !empty( $content_details->imdb_link ) ) ? $content_details->imdb_link : '' ; ?>" />
						</div>
					*/ ?>


					<?php /*
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">Ext. Content Ref</label>
						<input class="input-field" readonly type="text" placeholder="External Content Ref" value="<?php echo ( !empty( $content_details->external_content_ref ) ) ? $content_details->external_content_ref : '' ; ?>" />
					</div>
					*/ ?>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">Poster link</label>
						<?php if( !empty( $content_details->poster_link ) ){ ?>
							<a target="_blank" href="<?php echo prepare_poster_link( $content_details->poster_link, 1200 ); ?>" onclick="return false" ondblclick="location=this.href"><input class="input-field" name="imdb_details[poster_link]" type="text" placeholder="Poster Link" value="<?php echo $content_details->poster_link; ?>" /></a>
						<?php } else { ?>
							<input class="input-field" name="imdb_details[poster_link]" type="text" placeholder="Poster Link" value="" />
						<?php } ?>
					</div>

				</div>
				<div class="row">

					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<hr class="group-divider" />
					</div>
				</div>
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">Verified for Airtime</label>
						<select name="imdb_details[is_verified_for_airtime]" class="input-field" placeholder="Is verified for Airtime" >
							<?php if( !empty( $content_details->is_verified_for_airtime ) ){ ?>
								<option value="yes" title="Yes" selected="selected">Yes</option>
							<?php
							} else { ?>
								<option value="">Please select</option>
								<option value="yes" title="Yes">Yes</option>
							<?php
							} ?>
						</select>
					</div>

					<?php
					// if the movie has been pushed to airtime
					if( !empty( $content_details->is_airtime_asset ) && ( strtolower( $content_details->is_airtime_asset ) == 'yes' ) ){ ?>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
							<label class="input-label-60">Airtime State</label>
							<select name="imdb_details[airtime_state]" class="input-field-40">
								<option value="">Please select</option>
								<?php
								if( !empty( $airtime_states ) ){
									foreach( $airtime_states as $state ){
										if( strtolower( $state ) == "published" ){ ?>
											<option value="<?php echo ( !empty( $state ) ) ? $state : '' ; ?>" <?php echo ( ( !empty( $content_details->airtime_state ) ) && ( $content_details->airtime_state == $state ) ) ? 'selected="selected"' : "" ; ?> <?php echo ( empty( $content_details->airtime_feature_file_id ) || ( !( ( int ) $content_details->airtime_feature_file_id > 0 ) ) )  ? 'disabled="disabled" title="No Feature film available"' : "" ; ?> ><?php echo ( !empty( $state ) ) ? ucwords( $state ) : '' ; ?></option>
										<?php
										} else { ?>
											<option value="<?php echo ( !empty( $state ) ) ? $state : '' ; ?>" <?php echo ( ( !empty( $content_details->airtime_state ) ) && ( $content_details->airtime_state == $state ) ) ? 'selected="selected"' : "" ; ?>><?php echo ( !empty( $state ) ) ? ucwords( $state ) : '' ; ?></option>
										<?php
										} ?>
									<?php
									}
								} ?>
							</select>
						</div>
					<?php
					} ?>

					<?php /*
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<label class="input-label">Ext. Last Updated On</label>
						<input class="input-field" readonly type="text" placeholder="Ext. Last Updated On" value="<?php echo ( !empty( $content_details->external_content_updated_on ) && valid_date( $content_details->external_content_updated_on ) ) ? $content_details->external_content_updated_on : ( ( !empty( $content_details->external_content_created_on ) && valid_date( $content_details->external_content_created_on ) ) ? $content_details->external_content_created_on : '' ) ; ?>" />
					</div>
					*/ ?>

				</div>

				<?php
				if( $this->user->is_admin || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<div class="row">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									<button class="update-content-btn btn btn-block btn-update btn-primary" type="button" data-content_section="imdb_details">Update</button>
								</div>
							</div>
						</div>
					</div>
				<?php
				} else { ?>
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<div class="row">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>
								</div>
							</div>
						</div>
					</div>
				<?php
				} ?>
			</div>
		</div>
	</form>

	<div class="x_panel tile group-container territories-clearance">
		<span class="add-clearance"><a type="button" data-toggle="modal" data-target="#addClearance"><i class="fas fa-plus-circle"></i></a></span>
		<span class="upload-file"><a href="<?php echo base_url( "/webapp/content/upload_clearance" ); ?>"><i class="fas fa-upload"></i></a></span>
		<h4 class="legend pointer"><i class="fas fa-caret-down"></i>Content Clearance</h4>
		<div class="row group-content el-hidden">
			<?php 
			$trigger = true;
			if( ( $trigger ) && !empty( $content_details->is_airtime_asset ) && ( strtolower( $content_details->is_airtime_asset ) == 'yes' ) && !empty( $content_details->external_content_ref ) ){ ?>
			<div class="row">
				<div class="col-md-3 pull-right"><span class="synchronize-button" id="synchronize-button" data-content_id="<?php echo ( !empty( $content_details->content_id ) ) ? $content_details->content_id : '' ; ?>"><img class="at-synchro-img pull-right" src="<?php echo base_url( "assets/images/availability_windows.png" ); ?>" alt="Update Airtime to add availability windows" title="Update Airtime to add availability windows" /></span></div>
			</div>
			<?php } ?>
			<div class="row">
				<?php
				$todays_date = date( 'Y-m-d' );
				if( !empty( $clearance ) ){
					foreach( $clearance as $cl_row ){?>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<div class="rows clearance-list-item container-full">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									<span class=""><?php echo ( !empty( $cl_row->country ) ) ? $cl_row->country : '' ; ?></span>
								</div>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
									<span class=""><?php echo ( validate_date( $cl_row->clearance_start_date ) ) ? format_date_client( $cl_row->clearance_start_date ) : '' ; ?></span>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
									<span class="valid-clearance"><?php echo ( strtotime( $todays_date ) > strtotime( $cl_row->clearance_start_date ) ) ? '<i class="far fa-check-circle green"></i>'  : '<i class="far fa-times-circle red"></i>'; ?></span>
								</div>
								<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
									<span class="delete-clearance" data-clearance_id="<?php echo ( !empty( $cl_row->clearance_id ) ) ? $cl_row->clearance_id : '' ;?>"><div class=""><a href="#"><i class="fas fa-trash-alt"></i></a></div></span>
								</div>
							</div>
						</div>
					<?php
					}
				} ?>
			</div>
		</div>
	</div>

	<div class="x_panel tile group-container languages">
		<h4 class="legend pointer"><?php echo ( !empty( $language_phrases ) ) ? '<i class="fas fa-caret-down"></i>' : '<span style="width: 50px; float: left; display: block;">&nbsp;</span>'; ?>Languages</h4>
		<div class="row group-content el-hidden">
			<div class="row">
			<?php
			if( !empty( $language_phrases ) ){
				foreach( $language_phrases as $l_id => $l_row ){  ## l_id -> language ID ?>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="x_panel tile group-container language-text-container <?php echo $l_row->language_name; ?>">
							<h4 class="legend"><i class="fas fa-caret-down"></i><?php echo ( !empty( $l_row->language_name ) ) ? ucwords( $l_row->language_name ) : '' ; ?></h4>
							<div class="row group-content el-hidden">
								<form class="language-phrase-form">
									<input type="hidden" name="content_id" value="<?php echo ( !empty( $content_details->content_id ) ) ? ( int ) $content_details->content_id : '' ; ?>" />
									<?php
									if( !empty( $l_row->language_content ) ){
										$i = 0; ## to separate fields
										foreach( $l_row->language_content as $t_id => $t_row ){ ## loop through types, t_id -> type ID
											if( !empty( $t_row->type_content ) ){  ## if there is a content (can be an array)
												foreach( $t_row->type_content as $content_row ){ ## keys aren't related to anything, just an array of items ?>
													<div class="container-full standard-div">
														<label class="input-label"><?php echo ( ( !empty( $l_row->language_name ) ) ? ucwords( $l_row->language_name ) : '' ).' '.( ( !empty( $t_row->type_name ) ) ? ucwords( $t_row->type_name ) : '' ); ?></label>
														<input type="hidden" name="phrases[<?php echo $i; ?>][text_id]" value="<?php echo ( !empty( $content_row->text_id ) ) ? $content_row->text_id : '' ; ?>" />
														<input type="hidden" name="phrases[<?php echo $i; ?>][text_language_id]" value="<?php echo ( !empty( $content_row->text_language_id ) ) ? $content_row->text_language_id : ( ( !empty( $l_row->language_id ) ) ? $l_row->language_id : '' ) ; ?>" />
														<input type="hidden" name="phrases[<?php echo $i; ?>][text_type_id]" value="<?php echo ( !empty( $content_row->text_type_id ) ) ? $content_row->text_type_id : ( ( !empty( $t_row->type_id ) ) ? $t_row->type_id : '' ) ; ?>" />
														<input type="hidden" name="phrases[<?php echo $i; ?>][content_id]" value="<?php echo ( !empty( $content_details->content_id ) ) ? ( int ) $content_details->content_id : '' ; ?>" />
														<?php
														if( strtolower( trim( $t_row->type_name == "synopsis" ) ) ){ ?>
															<textarea class="input-field" name="phrases[<?php echo $i; ?>][phrase]" placeholder="Phrase"><?php echo ( !empty( $content_row->phrase ) ) ? $content_row->phrase : '' ; ?></textarea>
														<?php
														} else { ?>
															<input class="input-field" name="phrases[<?php echo $i; ?>][phrase]" type="text" placeholder="Phrase" value="<?php echo ( !empty(  $content_row->phrase ) ) ?  $content_row->phrase : '' ; ?>" />
														<?php
														} ?>
													</div>
													<?php
													unset( $content_row );
													$i++;
												}
											} else { ?>
												<div class="container-full standard-div">
													<label class="input-label"><?php echo ( !empty( $l_row->language_name ) ) ? ucwords( $l_row->language_name ) : '' ; ?> <?php echo ( !empty( $t_row->type_name ) ) ? ucwords( $t_row->type_name ) : '' ; ?></label>
													<input type="hidden" name="phrases[<?php echo $i; ?>][text_id]" value="<?php echo ( !empty( $content_row->text_id ) ) ? $content_row->text_id : '' ; ?>" />
													<input type="hidden" name="phrases[<?php echo $i; ?>][text_language_id]" value="<?php echo ( !empty( $l_id ) ) ? ( $l_id ) : '' ; ?>" />
													<input type="hidden" name="phrases[<?php echo $i; ?>][text_type_id]" value="<?php echo ( !empty( $t_id ) ) ? $t_id : ( '' ) ; ?>" />
													<input type="hidden" name="phrases[<?php echo $i; ?>][content_id]" value="<?php echo ( !empty( $content_details->content_id ) ) ? ( int ) $content_details->content_id : '' ; ?>" />
													<?php
													if( strtolower( trim( $t_row->type_name == "synopsis" ) ) ){ ?>
														<textarea class="input-field" name="phrases[<?php echo $i; ?>][phrase]" placeholder="Phrase"></textarea>
													<?php
													} else { ?>
														<input class="input-field" name="phrases[<?php echo $i; ?>][phrase]" type="text" placeholder="Phrase" value="" />
													<?php
													} ?>
												</div>
											<?php
											$i++;
											}
										}
									} ?>

									<?php
									if( $this->user->is_admin || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
										<div class="row">
											<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
												<div class="row">
													<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
														<button class="btn btn-block btn-update btn-primary" type="submit">Update</button>
													</div>
												</div>
											</div>
										</div>
									<?php
									} else { ?>
										<div class="row">
											<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
												<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>
											</div>
										</div>
									<?php
									} ?>

								</form>
							</div>
						</div>
					</div>
				<?php
				}
			} ?>
			</div>
		</div>
	</div>

	<div class="x_panel tile group-container content-documents">
		<?php
		if( !empty( $content_details->external_content_ref ) ){ ?>
			<span class="add-files-to-airtime" title="Add Media to Airtime Product"><a class="add-files-to-airtime-button" type="button" data-toggle="modal" data-target="#add-files-to-airtime"><img class="at-link" src="<?php echo base_url( "assets/images/at_link.png" ) ?>" /></a></span>
		<?php
		} ?>
		<h4 class="legend pointer"><i class="fas fa-caret-down"></i>Content Documents</h4>
		<div class="row group-content el-hidden">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
				<legend class="default-legend">Upload Files</legend>
				<form action="<?php echo base_url( 'webapp/content/upload_docs/'.$content_details->content_id ); ?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
					<input type="hidden" name="content_id" 	value="<?php echo $content_details->content_id; ?>" />
					<input type="hidden" name="module" 		value="content" />
					<input type="hidden" name="doc_type" 	value="content" />

					<div class="input-group form-group">
						<label class="input-group-addon">Hero Image</label>
						<span class="control-fileupload single pointer">
							<label for="uploadfile1" class="custom-file-upload">
								<i class="fas fa-cloud-upload"></i> Select file
							</label>
							<input id="uploadfile1" name="upload_files[hero]" type="file"/>
						</span>
					</div>

					<div class="input-group form-group">
						<label class="input-group-addon">Standard Image</label>
						<span class="control-fileupload single pointer">
							<label for="uploadfile2" class="custom-file-upload">
								<i class="fas fa-cloud-upload"></i> Select file
							</label>
							<input id="uploadfile2" name="upload_files[standard]" type="file"/>
						</span>
					</div>

					<div class="input-group form-group">
						<label class="input-group-addon">Subtitle Files</label>
						<span class="control-fileupload single pointer">
							<label for="uploadfile3" class="custom-file-upload">
								<i class="fas fa-cloud-upload"></i> Select file
							</label>
							<input id="uploadfile3" name="upload_files[subtitles]" type="file"/>
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
						<?php if( !empty( $content_documents ) ){ foreach( $content_documents as $file_group=>$files){ ?>
							<h5 style="color:#000" class="file-toggle pointer" data-class_grp="<?php echo str_replace( ' ', '', $file_group ); ?>" ><?php echo ucwords( $file_group ); ?> <span class="pull-right">(<?php echo count( $files ); ?>)</span></h5>
							<?php foreach( $files as $k=>$file){ ?>
								<div class="row <?php echo str_replace( ' ', '', $file_group ); ?>" style="display:block;padding:5px 0">
									<div class="col-md-8" style="padding-left:30px;">
										<a target="_blank" href="<?php echo $file->document_link; ?>"><?php echo strtolower( $file->document_name ); ?></a>
									</div>
									<div class="col-md-4">
										<span class="pull-right">
											<?php
											if( !empty( $file->aws_status ) ){
												if( in_array( $file->aws_status, ["uploaded", "completed", "complete"] ) ){ ## && ( $file->is_on_aws != false ) ?>
													<span class="cloud_status"><img src="<?php echo base_url( "assets/images/icons/cloud_success.png" ); ?>" title="AWS Upload Completed" /></span>
												<?php
												} else if( in_array( $file->aws_status, ["error", "fail", "failed"] ) ){ ?>
													<span class="cloud_status"><img src="<?php echo base_url( "assets/images/icons/cloud_error.png" ); ?>" title="AWS Upload Error" /></span>
												<?php
												}
											} ?>

											<?php
											if( !empty( $file->doc_file_type ) && in_array( strtolower( $file->doc_file_type ), ["hero", "standard"] ) ){
												if( !empty( $file->airtime_status ) ){
													/* if( in_array( $file->airtime_status, ["image_created"] ) ){ ?> ## when image is only created (not linked)
														<span class="cloud_status"><img src="<?php echo base_url( "assets/images/icons/cloud_success.png" ); ?>" title="AWS Upload Completed" /></span>
													<?php
													} else */
													if( in_array( $file->airtime_status, ["image_creation_error"] ) ){ ?>
														<span class="cloud_status"><img src="<?php echo base_url( "assets/images/icons/at_creation_error.png" ); ?>" title="AT Image creation error" /></span>
													<?php
													} else if( in_array( $file->airtime_status, ["image_linked"] ) ){ ?>
														<span class="cloud_status"><img src="<?php echo base_url( "assets/images/icons/at_linked.png" ); ?>" title="AT Image created and linked to the Product" /></span>
													<?php
													} else if( in_array( $file->airtime_status, ["image_linking_error"] ) ){ ?>
														<span class="cloud_status"><img src="<?php echo base_url( "assets/images/icons/at_linking_error.png" ); ?>" title="AT Image linking error" /></span>
													<?php
													}
												}
											} ?>

											<?php
											if( !empty( $file->doc_file_type ) && in_array( strtolower( $file->doc_file_type ), ["subtitles"] ) ){
												if( !empty( $file->airtime_status ) ){
													if( in_array( $file->airtime_status, ["subtitle_creation_error"] ) ){ ?>
														<span class="cloud_status"><img src="<?php echo base_url( "assets/images/icons/at_creation_error.png" ); ?>" title="AT Subtitle Creation Error" /></span>
													<?php
													} else if( in_array( $file->airtime_status, ["subtitle_deleting_error"] ) ){ ?>
														<span class="cloud_status"><img src="<?php echo base_url( "assets/images/icons/at_creation_error.png" ); ?>" title="AT Subtitle Deletion Error" /></span>
													<?php
													} else if( in_array( $file->airtime_status, ["subtitle_created"] ) && !empty( $file->airtime_reference ) ){ ?>
														<span class="cloud_status"><img src="<?php echo base_url( "assets/images/icons/at_created.png" ); ?>" title="AT Subtitle Created" /></span>
													<?php
													}
												}
											} ?>
											&nbsp;&nbsp;&nbsp;
											<a target="_blank" href="<?php echo $file->document_link; ?>"><i class="fas fa-download pointer"></i></a>
											&nbsp;&nbsp;&nbsp;
											<i class="fas fa-trash-alt text-red delete-file pointer" data-document_type="<?php echo ( !empty( $file->doc_file_type ) ) ? $file->doc_file_type : '' ; ?>" data-document_id="<?php echo ( !empty( $file->document_id ) ) ? $file->document_id : '' ; ?>"></i>
										</span>
									</div>
								</div>
							<?php } ?>
						<?php } } ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="x_panel tile group-container content-preparation">
		<?php
		if( !empty( $content_details->external_content_ref ) ){ ?>
			<span class="add-files-to-aws" title="Add Media to AWS"><a class="add-files-to-aws-button" type="button" data-toggle="modal" data-target="#add-files-to-aws"><img class="at-link" src="<?php echo base_url( "assets/images/AWS_Transfer.png" ) ?>" /></a></span>
		<?php 
		}	?>
		<h4 class="legend"><i class="fas fa-caret-down"></i>Content Preparation</h4>
		<div class="row group-content el-hidden">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 input-container">
					<div class="row">
						<form id="submit-uploaded-file">
							<input type="hidden" name="content_id" value="<?php echo ( !empty( $content_details->content_id ) ) ? $content_details->content_id : '' ; ?>" />
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 input-container">
								<label class="input-label">File Name</label>
								<input name="file_location" class="input-field" type="text" value="" />
							</div>
							<?php
							if( $this->user->is_admin || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									<div class="row">
										<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
											<button class="btn btn-block btn-update btn-primary" type="submit" data-content_section="content-preparation">Submit</button>
										</div>
									</div>
								</div>
							<?php
							} else { ?>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>
								</div>
							<?php
							} ?>
						</form>
					</div>
				</div>

				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 input-container">
					<div id="uploaded-files-streams">
						<?php 	
						if( !empty( $decoded_file_streams ) ){
							foreach( $decoded_file_streams as $file_row ){ ?>
								<div class="file-container">
									<h4 class="legend file-legend" data-file_id="<?php echo $file_row->file_id; ?>">
										<i class="fas fa-caret-down"></i>
										<?php echo ( !empty( $file_row->definition_group ) && ( strtolower( $file_row->definition_group ) == "hd" ) ) ? '<img class="sd_image" src="'.( base_url( "assets/images/HD_green.png" ) ).'" />' : '<img class="sd_image" src="'.( base_url( "assets/images/SD_green.png" ) ).'" />' ;
										echo ( !empty( $file_row->type_group ) && ( strtolower( $file_row->type_group ) == "movie" ) ) ? '<img class="sd_image" src="'.( base_url( "assets/images/M_white.png" ) ).'" />' : '<img class="sd_image" src="'.( base_url( "assets/images/TR_white.png" ) ).'" />' ;
										if( !empty( $file_row->aws_status ) ){
											if( in_array( strtolower( $file_row->aws_status ), ["uploaded", "completed", "complete","holding_reaching_success", "airtime_reaching_success"] ) ){ ?>
												<span class="cloud_status"><img src="<?php echo base_url( "assets/images/icons/cloud_success.png" ); ?>" title="In Easel" /></span>
											<?php
											} else if( in_array( strtolower( $file_row->aws_status ), ["error", "fail", "failed"] ) ){ ?>
												<span class="cloud_status"><img src="<?php echo base_url( "assets/images/icons/cloud_error.png" ); ?>" /></span>
											<?php
											} else if( in_array( strtolower( $file_row->aws_status ), ["uploading_successful", "transfer_initiated"] ) ){ ?>
												<span class="cloud_status"><img src="<?php echo base_url( "assets/images/icons/cloud_uploading_successfull.png" ); ?>" title="AWS Uploading Scheduled" /></span>
											<?php
											} else if( in_array( strtolower( $file_row->aws_status ), ["uploading_error", "transfer_initiating_error","holding_reaching_error"] ) ){ ?>
												<span class="cloud_status"><img src="<?php echo base_url( "assets/images/icons/cloud_uploading_error.png" ); ?>" title="AWS Transfer Error" /></span>
											<?php
											} else if( in_array( strtolower( $file_row->aws_status ), ["airtime_reaching_error"] ) ){ ?>
												<span class="cloud_status"><img src="<?php echo base_url( "assets/images/icons/cloud_success.png" ); ?>" title="In Airwave-Easel" /></span>
												<span class="cloud_status"><img src="<?php echo base_url( "assets/images/icons/cloud_uploading_error.png" ); ?>" title="AT Transfer Error" /></span>
											<?php
											}
										}

										if( !empty( $file_row->airtime_encoded_status ) ){  ## possible values from the webhook data->vodMedia->encodingStatus {[ not-encoded, encoding, encoded, encode-cancelled, encode-failed, unknown ]}
											if( in_array( strtolower( $file_row->airtime_encoded_status ), ["pending-encoding"] ) ){ ?>
												<span class="cloud_status"><img src="<?php echo base_url( "assets/images/icons/encoding_pending_encoding.png" ); ?>" title="Ready for Encoding" /></span>
											<?php
											} else if( in_array( strtolower( $file_row->airtime_encoded_status ), ["pending-encoding-error"] ) ){ ?>
												<span class="cloud_status"><img src="<?php echo base_url( "assets/images/icons/encoding_error_pending_encoding.png" ); ?>" title="Encoding Error" /></span>
											<?php
											} /* else if( in_array( strtolower( $file_row->airtime_encoded_status ), ["not-encoded"] ) ){ ## this came from Easel ?>
												<span class="cloud_status"><img src="<?php echo base_url( "assets/images/icons/cloud_success.png" ); ?>" /></span>
											<?php
											} */ ?>
										<?php
										} ?>

										<?php
										if( !empty( $file_row->airtime_product_linking_status ) ){
											if( in_array( strtolower( $file_row->airtime_product_linking_status ), ["success"] ) ){ ?>
												<span class="cloud_status"><img src="<?php echo base_url( "assets/images/icons/product_linking.png" ); ?>" title="Linked with the Easel Product" /></span>
											<?php
											} else if( in_array( strtolower( $file_row->airtime_product_linking_status ), ["error"] ) ){ ?>
												<span class="cloud_status"><img src="<?php echo base_url( "assets/images/icons/product_linking_error.png" ); ?>" title="Error linking with the Easel Product" /></span>
											<?php
											} ?>
										<?php
										} ?>

										File: <?php echo ( !empty( $file_row->file_new_name ) ) ? html_escape( $file_row->file_new_name.' (ID: '.$file_row->file_id.')' ) : '' ; ?><i class="fas fa-trash-alt delete-movie-file hide"></i>
										<span class="ingestion-date-span pull-right">Ingested: <?php echo ( !empty( $file_row->created_date ) ) ? "<span>".( date('d/m/Y', strtotime( $file_row->created_date ) ) )."</span>" : '' ; ?></span>
										<?php echo ( !empty( $file_row->is_verified ) ) ? '<img class="sd_image pull-right" src="'.( base_url( "assets/images/checked.png" ) ).'" />' : '' ; ?>
									</h4>
									<?php
									if( !empty( $file_row->streams ) ){ ?>
										<div class="group-content streams-container " style="display: none;">
											<ul class="stream-list"><?php
											foreach( $file_row->streams as $s_row ){ ?>
												<li class="col-md-12"><?php echo ( !empty( $file_row->type_alt_name ) ) ? ucwords( html_escape( $file_row->type_alt_name ) ) : '' ; echo ( !empty( $file_row->definition_group ) ) ? '&nbsp;-&nbsp;'.strtoupper( html_escape( $file_row->definition_group ) ) : '' ; echo ( !empty( $s_row->language_desc ) ) ? '&nbsp;-&nbsp;'.ucwords( html_escape( $s_row->language_desc ) ) : '&nbsp;-' ; echo ( !empty( $s_row->codec_type_alt_name ) ) ? ucwords( '&nbsp;'.html_escape( $s_row->codec_type_alt_name ) ) : '' ; echo ( !empty( $s_row->codec_name ) ) ? '&nbsp;('.strtoupper( html_escape( $s_row->codec_name ) ).')' : '' ; echo ( !empty( $s_row->id ) ) ? '&nbsp;('.strtoupper( html_escape( $s_row->id ) ).')' : '' ; echo ( !empty( $s_row->language ) ) ? '&nbsp;('.strtoupper( html_escape( $s_row->language ) ).')' : '' ; ?></li><?php
											} ?>
											</ul>
										</div>
									<?php
									} ?>
								</div>
							<?php
							}
						} ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- Modal To Add Files to Airtime -->
<div class="modal fade" id="add-files-to-airtime" tabindex="-1" role="dialog" aria-labelledby="add-files-to-airtime" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<?php $this->view( 'content/includes/add_media_to_airtime_product.php' ); ?>
		</div>
	</div>
</div>

<!-- Modal To Add Files to AWS -->
<div class="modal fade" id="add-files-to-aws" tabindex="-1" role="dialog" aria-labelledby="add-files-to-aws" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<?php $this->view( 'content/includes/add_media_to_aws.php' ); ?>
		</div>
	</div>
</div>


<!-- Modal To Add Clearance Manually -->
<div class="modal fade" id="addClearance" tabindex="-1" role="dialog" aria-labelledby="addClearance" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<?php $this->view( 'content/includes/add_clearance.php' ); ?>
		</div>
	</div>
</div>

<!-- Modal To Pick the File for the Ingestion -->
<div class="modal fade" id="pickFile" tabindex="-1" role="dialog" aria-labelledby="pickFile" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-body">
				<!-- <form id="adding-clearance-to-content-form" > -->
					<input type="hidden" name="content_id" value="<?php echo ( !empty( $content_details->content_id ) ) ? ( $content_details->content_id ) : '' ; ?>" />
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="slide-group">
								<div class="row">
									<div class="col-md-6 col-sm-6 col-xs-12">
										<legend class="legend-header">Available Files:</legend>
									</div>
									<div class="col-md-6 col-sm-6 col-xs-12">
										<h6 class="error_message pull-right" id="adding-clearance-to-content1-errors"></h6>
									</div>
								</div>

								<div class="input-group form-group container-full">
									<?php list_folder_files(); ?>
								</div>
								<div class="row hide">
									<div class="col-lg-6 col-lg-offset-6 col-md-6 col-md-offset-6 col-sm-6 col-sm-offset-6 col-xs-12">
										<button class="btn-block btn-next adding-clearance-to-content-steps" data-currentpanel="adding-clearance-to-content1" type="button">Next</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				<!-- </form> -->
			</div>

		</div>
	</div>
</div>

<script type="text/javascript">
$( document ).ready( function(){

	$( "#adding-media-to-aws-form" ).on( "submit", function( e ){
		e.preventDefault();

		var contentID 	= $( this ).data( "content_id" ),
			movieBatch 		= $( '.section-movie-files input[name="checked_document[]"]:checked' );

		if( ( parseInt( movieBatch.length ) < 1 ) ){
			swal({
				// ORIG:  title: 'At least one Video and one Image/Subtitle needs to be picked',
				title: 'At least one main movie or trailer file needs to be picked',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			});
			return false;
		}

		var moviearray 		= [],
			imgAndSubarray 	= [];

		movieBatch.each( function(){
			var ar = {};
			ar['file_type'] 	= $( this ).data( "file_type" );
			ar['value']			= $( this ).val();
			moviearray.push( ar );
		});

		if( moviearray ){
			swal({
				title: 'Confirm sending the files to AWS?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ){
				if( result.value ){
					$.ajax({
						url: 		"<?php echo base_url( 'webapp/content/add_media_to_aws/' ); ?>",
						method: 	"POST",
						data:{
							content_id: 	contentID,
							movie_data:		moviearray,
							img_sub_data:	imgAndSubarray,
						},
						dataType: 	'json',
						success:	function( data ){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 5000
								}).then( function(){
									window.setTimeout( function(){
										location.reload( true );
									}, 3000 );
								})
							} else {
								swal({
									type: 'error',
									title: data.status_msg
								}).then( function(){
									window.setTimeout( function(){
										location.reload( true );
									}, 3000 );
								})
							}
						}
					});
				} else {
					swal({
						type: 'error',
						title: "Error processing the request."
					});
					
					window.setTimeout( function(){
						location.reload( true );
					}, 5000 );
				}
			}).catch( swal.noop )

		} else {
			swal({
				title: 'No required data selected',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			})
		}
		return false;
	});


	// Select all for images, subtitles and movies (AWS)
 	$( "#content_compile_review_aws_files" ).on( "click", ".checked-content-all", function(){
		
			if( $( this ).prop( "checked" ) != true ){
				$( "#content_compile_review_aws_files input[type='checkbox']:not( :first )" ).each(
					function(){ 
						if( $( this ).prop( "disabled" ) != true ){
							$( this ).prop( "checked", false ); 
						}
					}
				)
				// none of the item is checked, so send button needs to be disabled
				$( "button[type='submit']" ).addClass( "disabled" ).prop( "disabled", "disabled" );
			} else {
				$( "#content_compile_review_aws_files input[type='checkbox']:not( :first )" ).each(
					function(){
						if( $( this ).prop( "disabled" ) != true ){
							$( this ).prop( "checked", true );
						}
					}
				)
				// all of the items are checked, so send button needs to be enabled
				$( "button[type='submit']" ).removeClass( "disabled" ).prop( "disabled", "" );
			}
	});


	// Select all for images, subtitles and movies (airtime) - start
 	$( "#content_compile_review_images" ).on( "click", ".checked-content-all", function(){
			if( $( this ).prop( "checked" ) != true ){
				$( "#content_compile_review_images input[type='checkbox']:not( :first )" ).each(
					function(){ 
						if( $( this ).prop( "disabled" ) != true ){
							$( this ).prop( "checked", false ); 
						}
					}
				)
				// none of the item is checked, so send button needs to be disabled
				$( "button[type='submit']" ).addClass( "disabled" ).prop( "disabled", "disabled" );
			} else {
				$( "#content_compile_review_images input[type='checkbox']:not( :first )" ).each(
					function(){
						if( $( this ).prop( "disabled" ) != true ){
							$( this ).prop( "checked", true );
						}
					}
				)
				// all of the items are checked, so send button needs to be enabled
				$( "button[type='submit']" ).removeClass( "disabled" ).prop( "disabled", "" );
			}
	});

 	$( "#content_compile_review_subtitles" ).on( "click", ".checked-content-all", function(){
		if( $( this ).prop( "checked" ) != true ){
			$( "#content_compile_review_subtitles input[type='checkbox']" ).each(
				function(){ 
					if( $( this ).prop( "disabled" ) != true ){
						$( this ).prop( "checked", false );
					}
				}
			)
			// none of the item is checked, so send button needs to be disabled
			$( "button[type='submit']" ).addClass( "disabled" ).prop( "disabled", "disabled" );
		} else {
			$( "#content_compile_review_subtitles input[type='checkbox']" ).each(
				function(){
					if( $( this ).prop( "disabled" ) != true ){
						$( this ).prop( "checked", true )
					}
				}
			)

			// all of the items are checked, so send button needs to be enabled
			$( "button[type='submit']" ).removeClass( "disabled" ).prop( "disabled", "" );
		}
	});

	$( ".aws-checked-document" ).on( "click", function(){
		var dataBatch 		= $( this ).parent().parent().parent().parent().find( "[name='checked_document[]']:checked" );
			allImputsBatch 	= $( this ).parent().parent().parent().parent().find( "[name='checked_document[]']:not( [disabled] )" );

		if( parseInt( dataBatch.length ) == parseInt( allImputsBatch.length ) ){
			$( "#content_compile_review_aws_files .checked-content-all" ).prop( "checked", "checked" );
		} else {
			$( "#content_compile_review_aws_files .checked-content-all" ).prop( "checked", "" );
		}

		if( dataBatch.length > 0 ){
			$( "button[type='submit']" ).removeClass( "disabled" ).prop( "disabled", "" );
		} else {
			$( "button[type='submit']" ).addClass( "disabled" ).prop( "disabled", "disabled" );
		}


	});

	$( ".checked-document" ).on( "click", function(){
		var dataBatch = $( this ).parent().parent().parent().find( "[name='checked_document[]']:checked");
		if( dataBatch.length > 0 ){
			$( "button[type='submit']" ).removeClass( "disabled" ).prop( "disabled", "" );
		} else {
			$( "button[type='submit']" ).addClass( "disabled" ).prop( "disabled", "disabled" );
		}
	});


	$( "#content_compile_review_images" ).on( "click", "input[type='checkbox']:not( :first )", function(){
		if( ( $( "#content_compile_review_images .checked-content-all" ).prop( "checked" ) == true ) && ( $( this ).prop( "checked" ) != true ) ){
			$( "#content_compile_review_images .checked-content-all" ).prop( "checked", false );
		}
	});


	$( "#content_compile_review_subtitles" ).on( "click", "input[type='checkbox']:not( :first )", function(){
		if( ( $( "#content_compile_review_subtitles .checked-content-all" ).prop( "checked" ) == true ) && ( $( this ).prop( "checked" ) != true ) ){
			$( "#content_compile_review_subtitles .checked-content-all" ).prop( "checked", false );
		}
	});

 	$( "#content_compile_review_movies" ).on( "click", ".checked-content-all", function(){
		if( $( this ).prop( "checked" ) != true ){
			$( "#content_compile_review_movies input[type='checkbox']" ).each(
				function(){ 
					if( $( this ).prop( "disabled" ) != true ){
						$( this ).prop( "checked", false ) 
					}
				}
			)
		} else {
			$( "#content_compile_review_movies input[type='checkbox']" ).each(
				function(){ 
					if( $( this ).prop( "disabled" ) != true ){
						$( this ).prop( "checked", true ) 
					}
				}
			)
		}
	});

	$( "#content_compile_review_movies" ).on( "click", "input[type='checkbox']:not( :first )", function(){
		if( ( $( "#content_compile_review_movies .checked-content-all" ).prop( "checked" ) == true ) && ( $( this ).prop( "checked" ) != true ) ){
			$( "#content_compile_review_movies .checked-content-all" ).prop( "checked", false );
		}
	});
	// Select all for images, subtitles and movies (airtime) - end


	var awsCheckedBatch = $( "#content_compile_review_aws_files [name='checked_document[]']:checked" ),
		awsAllBatch		= $( "#content_compile_review_aws_files [name='checked_document[]']" );

	if( awsCheckedBatch.length > 0 ){
		$( "button[type='submit']" ).removeClass( "disabled" ).prop( "disabled", "" );
	}

	if( awsCheckedBatch.length == awsAllBatch.length ){
		$( "#content_compile_review_aws_files .checked-content-all" ).prop( "checked", "checked" );
	}



	$( '[name="airtime_file_type"]' ).on( "click", function(){
		if( $( '[name="airtime_file_type"][value="subtitle"] ' ).is( ":checked" ) ){
			// check if movie has been sent to Airtime
			// in the theory if I do have the reference I can use it
			<?php
			if( !empty( $content_details->airtime_feature_file_id ) ){ ?>

			<?php
			} else { ?>
				$( ".airtime-header-element" ).after( "<p class=\"airtime_error_message\">A movie file needs to be linked with the Airtime Product before sending subtitle</p>" );
				$( ".select-file-type" ).prop( "disabled", true ).addClass( "disabled" );
			<?php
			} ?>
		} else {
			$( ".airtime_error_message" ).fadeOut( 100 );
			$( ".select-file-type" ).prop( "disabled", false ).removeClass( "disabled" );
		}
	});

	$( ".select-file-type" ).on( "click", function(){
		// no submission when just picking the option - 'send' button is disabled
		$( "button[type='submit']" ).addClass( "disabled" ).prop( "disabled", "disabled" );

		if( $( '[name="airtime_file_type"]' ).is( ":checked" ) ){

			var action = $( '[name="airtime_file_type"]:checked' ).val();

			if( action == "subtitle" ){

				$( "#content_compile_review_subtitles" ).removeClass( "el-hidden" ).addClass( "el-shown" );
				$( "#content_compile_review_images" ).removeClass( "el-shown" ).addClass( "el-hidden" );
				$( "#content_compile_review_movies" ).removeClass( "el-shown" ).addClass( "el-hidden" );

				// check if any subtitles are picked, if yes, remove disabled
				var subtitleBatch = $( "#content_compile_review_subtitles [name='checked_document[]']:checked" )
				if( subtitleBatch.length > 0 ){
					$( "button[type='submit']" ).removeClass( "disabled" ).prop( "disabled", "" );
				}
				$( "#adding-media-to-airtime-product-form .slide-group button[type=submit]" ).html( 'Submit' );

			} else if( action == "image" ){
				$( "#content_compile_review_images" ).removeClass( "el-hidden" ).addClass( "el-shown" );
				$( "#content_compile_review_subtitles" ).removeClass( "el-shown" ).addClass( "el-hidden" );
				$( "#content_compile_review_movies" ).removeClass( "el-shown" ).addClass( "el-hidden" );

				// check if any images are picked, if yes, remove disabled
				var imageBatch = $( "#content_compile_review_images [name='checked_document[]']:checked" )
				if( imageBatch.length > 0 ){
					$( "button[type='submit']" ).removeClass( "disabled" ).prop( "disabled", "" );
				}
				$( "#adding-media-to-airtime-product-form .slide-group button[type=submit]" ).html( 'Submit' );

			} else if( action == "film_trailer" ){
				$( "#content_compile_review_movies" ).removeClass( "el-hidden" ).addClass( "el-shown" );
				$( "#content_compile_review_subtitles" ).removeClass( "el-shown" ).addClass( "el-hidden" );
				$( "#content_compile_review_images" ).removeClass( "el-shown" ).addClass( "el-hidden" );

				// check if any film trailers are picked, if yes, remove disabled
				var movieBatch = $( "#content_compile_review_movies [name='checked_document[]']:checked" )
				if( movieBatch.length > 0 ){
					$( "button[type='submit']" ).removeClass( "disabled" ).prop( "disabled", "" );
				}
				$( "#adding-media-to-airtime-product-form .slide-group button[type=submit]" ).html( 'Encode Selected Files' );

			} else {
				$( "#content_compile_review_movies" ).removeClass( "el-shown" ).addClass( "el-hidden" );
				$( "#content_compile_review_subtitles" ).removeClass( "el-shown" ).addClass( "el-hidden" );
				$( "#content_compile_review_images" ).removeClass( "el-shown" ).addClass( "el-hidden" );
			}

			var elementClass = ".adding-media-to-airtime";
			panelchange( ".adding-media-to-airtime1", elementClass );

		} else {
			$( ".airtime_error_message" ).remove();
			$( ".airtime-header-element" ).after( "<p class=\"airtime_error_message\">Please select the File Type</p>" );
			setTimeout( function(){
				$( ".airtime_error_message" ).fadeOut( 1000 );
			}, 3000);
			return false;
		}
	});


	$( "#adding-media-to-airtime-product-form" ).on( "submit", function( e ){
		e.preventDefault();

		var action 		= $( '[name="airtime_file_type"]:checked' ).val();
			contentID 	= $( this ).data( "content_id" );
			formData	= false;
			dataBatch	= false;
			types		= [];

		if( action == "subtitle" ){
			formData = $( "#content_compile_review_subtitles [name='checked_document[]']:checked" ).serializeArray();
		} else if( action == "image" ){
			// validation:
			// a) not one picked

			dataBatch = $( "#content_compile_review_images [name='checked_document[]']:checked" );

			if( dataBatch.length < 1 ){
				swal({
					title: 'At least one image needs to be picked',
					showCancelButton: true,
					confirmButtonColor: '#5CB85C',
					cancelButtonColor: '#9D1919',
					confirmButtonText: 'Yes'
				});

				return false;
			}

			// not picked hero or missing hero
			dataBatch.each( function( key ){
				var type = $( this ).data( "image_type" );
				types.push( type.toLowerCase() );
			});

			if( !( types.includes( "hero" ) ) ){
				swal({
					title: 'Hero image needs to be picked',
					showCancelButton: true,
					confirmButtonColor: '#5CB85C',
					cancelButtonColor: '#9D1919',
					confirmButtonText: 'Yes'
				});

				return false;
			}

			// formData = $( "#content_compile_review_images [name='checked_document[]']:checked" ).serializeArray();
			formData = dataBatch.serializeArray();
		} else if( action == "film_trailer" ){
			formData = $( "#content_compile_review_movies [name='checked_document[]']:checked" ).serializeArray();
		} else {

		}

		if( formData ){

			swal({
				title: 'Confirm sending the files?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ){
				if( result.value ){
					$.ajax({
						url: "<?php echo base_url( 'webapp/content/add_media_to_airtime/' ); ?>",
						method: "POST",
						data:{
							content_id: 	contentID,
							chkdDocument:	formData,
							action:			action,
						},
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 5000
								}).then( function(){
									window.setTimeout( function(){
										location.reload( true );
									}, 3000 );
								});
							} else {
								swal({
									type: 'error',
									title: data.status_msg,
									timer: 5000
								}).then( function(){
									window.setTimeout( function(){
										location.reload( true );
									}, 3000 );
								});
							}
						}
					});
				}
			}).catch( swal.noop )

		} else {
			swal({
				title: 'No files selected',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			})
		}

		return false;
	});



	$( "[name='file_location']" ).on( "click", function(){
		$( '#pickFile' ).modal( 'show' );
	});

	$( ".file-picker" ).on( "click", function(){
		var fileName = $( this ).data( "file_name" );
		$( "[name='file_location']" ).val( fileName );
		$( '#pickFile' ).modal( 'hide' );
	});



	$( "#synchronize-button" ).on( "click", function( e ){
		e.preventDefault();

		var contentID = $( this ).data( "content_id" );

		if( !( parseInt( contentID, 10 ) > 0 ) ){
			swal({
				title: 'Wrong Content ID',
				type: 'error',
			})
			return false

		} else {
			swal({
				title: 'Confirm adding Availability Windows?',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ){
				if( result.value ){
					$.ajax({
						url: "<?php echo base_url( 'webapp/content/synchronize_availability_windows/' ); ?>",
						method: "POST",
						data:{
							content_id: contentID
						},
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 5000
								})
								window.setTimeout( function(){
									location.reload( true );
								}, 5000 );
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
		}
	});



	$( "#submit-uploaded-file" ).on( "submit", function( e ){
		e.preventDefault();

		var formData = $( this ).serialize();
		swal({
			title: 'Confirm submitting the file location?',
			// type: 'question',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function( result ){
			if ( result.value ) {
				$.ajax({
					url: "<?php echo base_url( 'webapp/content/submit_file_location/' ); ?>",
					method: "POST",
					data: formData,
					dataType: 'json',
					success:function( data ){
						if( data.status == 1 ){
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 5000
							})
							window.setTimeout( function(){
								location.reload( true );
							}, 5000 );
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

	$( ".delete-file" ).click( function( e ){
		e.preventDefault();

		var documentID 		= $( this ).data( 'document_id' );
		var documentType 	= $( this ).data( 'document_type' );
		
		if( documentType == 'hero' ){
			var stand = $(".table-responsive").find( "[data-document_type='standard']" );
			if( stand.length > 0 ){
				swal({
					type: 'error',
					title: "Standard must be deleted before deleting Hero"
				})
				return false;
			}
		}

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
					url: "<?php echo base_url( 'webapp/content/delete_document/' ); ?>",
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

 	$( ".generate_file" ).on( "click", function( e ){
		e.preventDefault();

		var fileType = $( this ).data( "filetype" );
		$( "#file_type" ).val( fileType );

		$( "form#generate_file" ).submit();

		setTimeout(function () {
			window.location.reload();
		}, 5000 );
	});

	$( ".language-phrase-form" ).on( "submit", function( e ){
		e.preventDefault();

		var button = $( this ).find( "button[type='submit']" );
		button.attr( "disabled", true );
		setTimeout( function(){
			button.prop( "disabled", false );
		}, 2000);

		var formData = $( this ).serialize();

		$.ajax({
			url: "<?php echo base_url( 'webapp/content/update_language_phrase/' ); ?>",
			method: "POST",
			data: formData,
			dataType: "JSON",
			success: function( data ){
				if( data.status == 1 ){
					swal({
						type: 'success',
						title: data.status_msg,
						showConfirmButton: false,
						timer: 1000
					})
				} else {
					swal({
						type: 'error',
						title: data.status_msg
					})
					return false;
				}
			}
		});
		return false;
	});


	var newDataType 	= "<?php echo ( !empty( ( $content_details->type ) ) ) ? ( $content_details->type ) : '' ; ?>";
	var chkd 			= <?php echo ( !empty( ( $content_details->genre ) ) ) ? ( 'JSON.parse( '.( json_encode( $content_details->genre ) ).' )' ) : '[]' ; ?>

	$.ajax({
		url: "<?php echo base_url( 'webapp/content/fetch_genres/' ); ?>",
		method: "POST",
		data: {
			contentType: newDataType,
			destination: "profile",
			checked: chkd,
		},
		dataType: 'json',
		success:function( data ){
			if( data.status == 1 && ( data.genres !== '' ) ){
				$( "#genres_container" ).html( data.genres );
			} else {
				$( "#genres_container" ).html( '<span style="font-style:italic;margin-left:10px;color:red;">No genres available (please add them in Settings)</span>' );
			}
		}
	});

	$( "select[name='imdb_details[type]']" ).on( "change", function(){
		var newDataType = $( this ).val();
		$.ajax({
			url:"<?php echo base_url( 'webapp/content/fetch_genres/' ); ?>",
			method: "POST",
			data: {
				contentType: newDataType,
				destination: "profile"
			},
			dataType: 'json',
			success:function( data ){
				if( data.status == 1 && ( data.genres !== '' ) ){
					$( "#genres_container" ).html( data.genres );
				} else {
					$( "#genres_container" ).html( '<span style="font-style:italic;margin-left:10px;color:red;">No genres available (please add them in Settings)</span>' );
				}
			}
		});
	});

  	$( "#genres_container" ).on( "click", "#all_genres", function(){
		if( $( this ).prop( "checked" ) != true ){
			$( "#genres_container input[type='checkbox']" ).each(
				function(){ $( this ).prop( "checked", false ) }
			)
		} else {
			$( "#genres_container input[type='checkbox']" ).each(
				function(){ $( this ).prop( "checked", true ) }
			)
		}
	});

	$( "#genres_container" ).on( "click", "input[type='checkbox']:not( :first )", function(){
		if( ( $( "#all_genres" ).prop( "checked" ) == true ) && ( $( this ).prop( "checked" ) != true ) ){
			$( "#all_genres" ).prop( "checked", false );
		}
	});

	var trigger = $( "#all_territories" );
	$( trigger ).on( "change", function(){
		if( $( this ).prop( "checked" ) != true ){
			$( ".territory_list input[type='checkbox']" ).each(
				function(){ $( this ).prop( "checked", false ) }
			)
		} else {
			$( ".territory_list input[type='checkbox']" ).each(
				function(){ $( this ).prop( "checked", true ) }
			)
		}
	});

	$( ".territory_list input[type='checkbox']" ).not( ":first" ).on( "click", function(){
		if( ( $( trigger ).prop( "checked" ) == true ) && ( $( this ).prop( "checked" ) != true ) ){
			$( trigger ).prop( "checked", false );
		}
	})


	$( ".delete-clearance" ).on( "click", function( e ){
		e.preventDefault();
		var clearanceID = $( this ).data( "clearance_id" );

		swal({
			title: 'Confirm deleting clearance date?',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/content/delete_clearance/' ); ?>",
					method: "POST",
					data: {
						clearance_id: clearanceID
					},
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
								 location.reload();
							}, 2000 );
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
							return false;
						}
					}
				});
			} else {
				return false;
			}
		}).catch( swal.noop )
	})


	$( '#adding-clearance-to-content-form' ).on( "submit", function( e ){
		e.preventDefault();
		var formData = $( '#adding-clearance-to-content-form' ).serialize();

		swal({
			title: 'Confirm adding clearance date(s) to the content?',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/content/add_clearance/' ); ?>",
					method: "POST",
					data: formData,
					dataType: 'json',
					success:function( data ){
						if( data.status == 1 && ( data.new_clearance !== '' ) ){
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 2000
							})
							window.setTimeout( function(){
								 location.reload();
							}, 2000 );
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
							return false;
						}
					}
				});
			} else {
				return false;
			}
		}).catch( swal.noop )
	});


	$( ".uploading-file" ).click( function(){
		$( '.error_message' ).each( function(){
			$( this ).text( '' );
		});

		var currentpanel = $( this ).data( "currentpanel" );

		// If true - there are errors
		var inputs_state = check_inputs( currentpanel );
		if( inputs_state ){
			//If name attribute returned, auto focus to the field and display arror message
			$( '#upload-clearance-form [name="'+inputs_state+'"]' ).focus();

			var labelText = $( '#upload-clearance-form [name="' + inputs_state + '"]' ).parent().find( 'label' ).text();
			$( '#' + currentpanel + '-errors' ).text( ucwords( labelText ) + ' is a required' );
			return false;
		}

		var elementClass = ".uploading-file";
		panelchange( "." + currentpanel, elementClass )
		return false;
	});


	$( ".adding-clearance-to-content-steps" ).click( function(){
		$( '.error_message' ).each( function(){
			$( this ).text( '' );
		});

		var currentpanel = $( this ).data( "currentpanel" );

		// If true - there are errors
		var inputs_state = check_inputs( currentpanel );
		if( inputs_state ){
			//If name attribute returned, auto focus to the field and display arror message
			$( '#adding-clearance-to-content-form [name="'+inputs_state+'"]' ).focus();

			var labelText = $( '#adding-clearance-to-content-form [name="' + inputs_state + '"]' ).parent().find( 'label' ).text();
			$( '#' + currentpanel + '-errors' ).text( ucwords( labelText ) + ' is a required' );
			return false;
		}

		var elementClass = ".adding-clearance-to-content";
		panelchange( "." + currentpanel, elementClass )
		return false;
	});


	$( ".adding-media-to-airtime-steps" ).click( function(){
		$( '.error_message' ).each( function(){
			$( this ).text( '' );
		});

		var currentpanel = $( this ).data( "currentpanel" );

		// If true - there are errors
		var inputs_state = check_inputs( currentpanel );

		if( inputs_state ){
			//If name attribute returned, auto focus to the field and display error message
			$( '#adding-media-to-airtime-product-form [name="'+inputs_state+'"]' ).focus();

			var labelText = $( '#adding-media-to-airtime-product-form [name="' + inputs_state + '"]' ).parent().find( 'label' ).text();
			$( '#' + currentpanel + '-errors' ).text( ucwords( labelText ) + ' is a required' );
			return false;
		}

		var elementClass = ".adding-media-to-airtime";
		panelchange( "." + currentpanel, elementClass )
		return false;
	});


	//** Validate any inputs that have the required class, if empty return the name attribute **/
	function check_inputs( currentpanel ){
		var result = false;
		var panel = "." + currentpanel;

		$( $( panel + " .required" ).get().reverse() ).each( function(){
			var fieldName 	= '';
				fieldName 	= $( this ).attr( 'name' );
			var inputValue 	= $( this ).val();

			if( ( inputValue == false ) || ( inputValue == '' ) || ( inputValue.length == 0 ) ){
				result 		= fieldName;
				return result;
			}
		});
		return result;
	}

	$( ".btn-back" ).click( function(){
		var currentpanel = $( this ).data( "currentpanel" );
		go_back( "." + currentpanel )
		return false;
	});

	function panelchange( changefrom, elementClass ){
		var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
		var changeto = elementClass + panelnumber;
		$( changefrom ).hide( "slide", {direction : 'left'}, 500 );
		$( changeto ).delay( 600 ).show( "slide", {direction : 'right'},500 );
		return false;
	}

	function go_back( changefrom ){
		var panelnumber = parseInt( changefrom.match(/\d+/) ) - parseInt( 1 );
		var elementClass = changefrom.substr( 0, parseInt( changefrom.length ) - parseInt( panelnumber.toString().length ) );
		var changeto = elementClass + panelnumber;
		$( changefrom ).hide( "slide", {direction : 'right'}, 500 );
		$( changeto ).delay( 600 ).show( "slide", {direction : 'left'},500 );
		return false;
	}

	$( ".delete_container" ).click( function(){
		swal({
			title: 'Confirm Content delete?',
			// type: 'question',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				var cont_id = <?php echo ( !empty( $content_details->content_id ) ) ? $content_details->content_id : '' ; ?>;
				if( parseInt( cont_id ) < 0 ){
					swal({
						title: 'Conent ID is required',
						type: 'error',
					})
					return false;
				}

				$.ajax({
					url:"<?php echo base_url( 'webapp/content/delete_content/' ).( ( !empty( $content_details->content_id ) ) ? $content_details->content_id : '' ); ?>",
					method:"POST",
					data: { content_id: cont_id },
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
								location.href ="<?php echo base_url( "webapp/content" ); ?>";
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

	$( ".legend" ).click( function( e ){
		var target = $( e.target );

		if( target.hasClass( "fa-trash-alt" ) ){
			return;
		} else {
			$( this ).children( ".fa-caret-down, .fa-caret-up" ).toggleClass( "fa-caret-down fa-caret-up" );
			$( this ).next( ".group-content" ).slideToggle( 400 );
		}
	});

	$( '.update-content-btn' ).click( function( e ){
		e.preventDefault();

		var section 	= $( this ).data( "content_section" );
		var formData 	= $( "." + section + " input," + "." + section + " select," + "." + section + " textarea" ).serialize();

		swal({
			title: 'Confirm content update?',
			// type: 'question',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function( result ) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/content/update/' ).( ( !empty( $content_details->content_id ) ) ? $content_details->content_id : ''  ); ?>",
					method:"POST",
					data:formData,
					dataType: 'json',
					success:function( data ){
						if( data.status == 1 ){
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: true,
								confirmButtonText: "OK",
							}).then( function( result ){
								location.reload();
							});
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							}).then( function( result ){
								location.reload();
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
});
</script>