<div class="row">
	<form id="genre-creation-form">
		<input name="genre_type_id" type="hidden" value="" />
		
		<div class="genre_creation_panel1 col-lg-12 col-md-12 col-sm-12 col-xs-12" data-panel-index="0">
			<div class="slide-group">
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<legend class="legend-header">Add <span class="genre_type"></span> item</legend>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<h6 class="error_message pull-right" id="genre_creation_panel1-errors"></h6>
					</div>
				</div>

				<div class="input-group form-group container-full">
					<label class="input-group-addon el-hidden">Genre Name</label>
					<input name="genre_name" class="input-field-full container-full" type="text" value="" placeholder="Genre Name" title="Genre Name" />
				</div>
				
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12 el-hidden">
						<button class="btn-block btn-server-back" data-currentpanel="genre_creation_panel1" type="button">Back</button>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12 pull-right">
						<button id="genre-setting-btn" class="btn-block btn-flow btn-next" type="submit">Submit</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>