<div class="modal-body">
	<form id="adding-airtime-plans-to-provider-form">
		<input type="hidden" name="provider_id" value="<?php echo ( !empty( $provider_details->provider_id ) ) ? ( $provider_details->provider_id ) : '' ; ?>" />
		<input type="hidden" name="price_plan_type" value="airtime" />
		<div class="row">
			<div class="adding-airtime-plans-to-provider1 col-md-12 col-sm-12 col-xs-12">
				<div class="slide-group">
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<legend class="legend-header">Is the Provider a Channel?</legend>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<h6 class="error_message pull-right" id="adding-airtime-plans-to-provider1-errors"></h6>
						</div>
					</div>

					<div class="input-group form-group container-full">
						<label class="input-group-addon el-hidden">Is the Provider a Channel?</label>
						<ul class="is-a-channel-options" title="Is provider a Channel?">
							<li class="radio-button">
								<label for="provider_is_a_channel">
									<label class="input-group-addon el-hidden">Provider is a Channel</label>
									<input class="required" type="radio" name="is_provider_a_channel" id="provider_is_a_channel" value="yes" /> <span class="territory_name weak">Yes</span>
								</label>
							</li>
							<li class="radio-button">
								<label for="provider_isnt_a_channel">
									<label class="input-group-addon el-hidden">Provider is a Channel</label>
									<input class="required" type="radio" name="is_provider_a_channel" id="provider_isnt_a_channel" value="no" /> <span class="territory_name weak">No</span>
								</label>
							</li>
						</ul>
					</div>

					<div class="input-group form-group container-full channel-container el-hidden">
						<label class="input-group-addon el-hidden">Select Channel</label>
						<?php
						if( !empty( $channels ) ){ ?>
							<select name="channel_id" class="form-control">
								<option value="">Select Channel</option>
								<?php foreach( $channels as $ch_row ){?>
									<option value="<?php echo ( !empty( $ch_row->channel_id ) ) ? $ch_row->channel_id : ''; ?>" title="<?php echo ( !empty( $ch_row->channel_name ) ) ? $ch_row->channel_name : '' ?>"><?php echo ( !empty( $ch_row->channel_name ) ) ? $ch_row->channel_name : '' ?></option>
								<?php } ?>
							</select>
						<?php
						} ?>
					</div>

					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">&nbsp;</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn-block btn-next airtime-adding-steps" data-currentpanel="adding-airtime-plans-to-provider1" type="button">Next</button>
						</div>
					</div>
				</div>
			</div>
			
			<div class="adding-airtime-plans-to-provider2 col-md-12 col-sm-12 col-xs-12 el-hidden">
				<div class="slide-group">
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<legend class="legend-header">Enter Price Plan Name</legend>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<h6 class="error_message pull-right" id="adding-airtime-plans-to-provider2-errors"></h6>
						</div>
					</div>

					<div class="input-group form-group container-full">
						<label class="input-group-addon el-hidden">Enter Price Plan Name</label>
						<input name="price_plan_name" class="form-control required" type="text" value="" placeholder="Please enter the Plan Name" title="Price Plan Name" required="required" />
					</div>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn-block btn-back" data-currentpanel="adding-airtime-plans-to-provider2" type="button">Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn-block btn-next airtime-adding-steps" data-currentpanel="adding-airtime-plans-to-provider2" type="button">Next</button>
						</div>
					</div>
				</div>
			</div>

			<div class="adding-airtime-plans-to-provider3 col-md-12 col-sm-12 col-xs-12 el-hidden">
				<div class="slide-group">
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<legend class="legend-header">Start and End Period</legend>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<h6 class="error_message pull-right" id="adding-airtime-plans-to-provider3-errors"></h6>
						</div>
					</div>

					<div class="input-group form-group container-full">
						<label class="input-group-addon el-hidden">Enter Start Period</label>
						<input name="start_period" class="form-control required" type="number" min="0" max="960" pattern="[0-9]*" value="" placeholder="(0-960)" title="Start Period" required="required" />
					</div>

					<div class="input-group form-group container-full">
						<label class="input-group-addon el-hidden">Enter End Period</label>
						<input name="end_period" class="form-control required" type="number" min="0" max="960" pattern="[0-9]*" value="" placeholder="(0-960)" title="End Period" required="required" />
					</div>

					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn-block btn-back" data-currentpanel="adding-airtime-plans-to-provider3" type="button">Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn-block btn-next" data-currentpanel="adding-airtime-plans-to-provider3" type="submit">Add Plan</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>