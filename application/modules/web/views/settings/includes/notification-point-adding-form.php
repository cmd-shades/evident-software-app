<form id="adding_point-form">
	<input type="hidden" name="server_id" value="" />
	<div class="row">
		<div class="adding_point_panel1 col-lg-12 col-md-12 col-sm-12 col-xs-12" data-panel-index="0">
			<div class="slide-group">
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<legend class="legend-header">Notification Point</legend>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<h6 class="error_message pull-right" id="adding_point_panel1-errors"></h6>
					</div>
				</div>
				
				<div class="input-group form-group container-full">
					<div class="input-group container-full">
						<label class="input-group-addon el-hidden">Email Address</label>
						<input name="email" class="input-field-full container-full" type="text" value="" placeholder="Email Address" title="Email Address" />
					</div>
				</div>

				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button id="adding_point_back_button" class="btn-block btn-back" data-currffentpanel="adding_point_panel1" type="button">Back</button>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn-block btn-flow btn-next" type="submit">Submit</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>