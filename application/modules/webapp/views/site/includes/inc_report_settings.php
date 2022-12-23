<h4 class="legend pointer"><i class="fas fa-caret-down"></i>Site Reports Settings</h4>
<div class="row group-content el-hidden">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 input-container">
		<legend class="default-legend">UIP Settings</legend>

		<form id="uip_report_settings">
			<div class="standard-div">
				<?php
				foreach( $royalty_types as $r_key => $r_type ){
					if( strtolower( $r_type->type_group ) == "minimum_guarantee" ){ ?>
						<input type="hidden" name="data[<?php echo $r_key ?>][site_id]" value="<?php echo ( !empty( $site_details->site_id ) ) ? $site_details->site_id : '' ; ?>" class="site-id" />
						<input type="hidden" name="data[<?php echo $r_key ?>][provider_id]" value="<?php echo ( !empty( $royalty_provider_id ) ) ? $royalty_provider_id : '' ; ?>" class="provider-id" />
						
						<div class="container-full standard-div">
							<input type="hidden" name="data[<?php echo $r_key ?>][royalty_type_id]" value="<?php echo $r_type->type_id; ?>" />
							<label class="input-label-40"><?php echo ( !empty( $r_type->type_name ) ) ? $r_type->type_name : '' ; ?></label>
							<select name="data[<?php echo $r_key ?>][royalty_service_id]" class="input-field-60 service_trigger" data-royalty_type_id="<?php echo $r_type->type_id; ?>">
								<option value="">Please select</option>
								<?php
								if( !empty( $royalty_services ) ){
									foreach( $royalty_services as $rs_key => $s1_row ){ ?>
										<option value="<?php echo ( !empty( $s1_row->service_id ) ) ? $s1_row->service_id : '' ; ?>" <?php echo ( !empty( $site_mg_royalty_setting->royalty_service_id ) && ( $site_mg_royalty_setting->royalty_service_id == $s1_row->service_id ) ) ? 'selected="selected"'  : '' ; ?>><?php echo ( !empty( $s1_row->service_name ) ) ? $s1_row->service_name : '' ; ?></option>
									<?php
									}
								} ?>
							</select>
						</div>

						<div class="container-full standard-div">
							<label class="input-label-40">Daily Rate (p.p.r.p.d.)</label>
							<input type="hidden" name="data[<?php echo $r_key ?>][report_setting_id]" class="report-setting" value="" />
							<input type="text" value="<?php echo ( !empty( $site_mg_royalty_setting->setting_value ) ) ? $site_mg_royalty_setting->setting_value : '' ; ?>" class="input-field-60 setting-value" disabled="disabled" style="background: #f5f4f2; color: #446793;" />
						</div>
					<?php
					} else if( strtolower( $r_type->type_group ) == "revenue_share" ){ ?>
						<input type="hidden" name="data[<?php echo $r_key ?>][site_id]" value="<?php echo ( !empty( $site_details->site_id ) ) ? $site_details->site_id : '' ; ?>" class="site-id" />
						<input type="hidden" name="data[<?php echo $r_key ?>][provider_id]" value="<?php echo ( !empty( $royalty_provider_id ) ) ? $royalty_provider_id : '' ; ?>" class="provider-id" />
						<div class="container-full standard-div">
							<input type="hidden" name="data[<?php echo $r_key ?>][royalty_type_id]" value="<?php echo $r_type->type_id; ?>" />
							<label class="input-label-40"><?php echo ( !empty( $r_type->type_name ) ) ? $r_type->type_name : '' ; ?></label>
							<select name="data[<?php echo $r_key ?>][royalty_service_id]" class="input-field-60 service_trigger" data-royalty_type_id="<?php echo $r_type->type_id; ?>">
								<option value="">Please select</option>
								<?php
								if( !empty( $royalty_services ) ){
									foreach( $royalty_services as $rs_key => $s2_row ){ ?>
										<option value="<?php echo ( !empty( $s2_row->service_id ) ) ? $s2_row->service_id : '' ; ?>" <?php echo ( !empty( $site_rs_royalty_setting->royalty_service_id ) && ( $site_rs_royalty_setting->royalty_service_id == $s2_row->service_id ) ) ? 'selected="selected"'  : '' ; ?>><?php echo ( !empty( $s2_row->service_name ) ) ? $s2_row->service_name : '' ; ?></option>
									<?php
									}
								} ?>
							</select>
						</div>

						<div class="container-full standard-div">
							<label class="input-label-40">Percentage (%)</label>
							<input type="hidden" name="data[<?php echo $r_key ?>][report_setting_id]" class="report-setting" value="" />
							<input type="text" value="<?php echo ( !empty( $site_rs_royalty_setting->setting_value ) ) ? $site_rs_royalty_setting->setting_value : '' ; ?>" class="input-field-60 setting-value" disabled="disabled" style="background: #f5f4f2; color: #446793;" />
						</div>
					<?php
					} ?>

					<div class="container-full standard-div"><br /></div>

				<?php 
				} ?>
			</div>

			<div class="row">
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<?php
							$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, "details" );
							if( !$this->user->is_admin || !empty( $item_access->can_edit ) || !empty( $item_access->is_admin ) ){ ?>
								<button class="update-uip-report-settings-btn btn btn-block btn-update btn-primary" type="button">Update</button>
							<?php
							} else { ?>
								<button class="btn-success btn-block btn-update no-permissions" disabled>No Permissions</button>
							<?php
							} ?>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>