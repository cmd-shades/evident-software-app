

<script>
	var baseURL = "<?php echo base_url(); ?>";
</script>

<link href="<?php echo base_url() ?>assets/js/koolphp/custom-style.css" rel="stylesheet">
<script src="<?php echo base_url() ?>assets/js/koolphp/evident-pivot.js"></script>

<div class="container-fluid" id="report-body">
	<div class="row">
		<div class="col-md-8 col-md-offset-2" id="report-content">
			<h1 class="display-3 report-question">Pick a table to view</h1>
			<hr/>
			<form action="displaypivot" method="get" id="pivot-params">
				<select class="btn btn-outline-primary fullwidth margin-top-15" id="table-dropdown" name="table_name" form="pivot-params" id="tbl">
					<?php 
						foreach($enabled_tables as $key => $value){
							echo "<option value='" . $value . "'>" . $key . "</option>";
						}
					?>
				</select>
				<button type="button" class="btn btn-outline-primary fullwidth" id="load-columns-button">Load Columns</button>
				<hr/>
				<div id="load-table" style="display:none">
					<div id="table-columns"></div><hr/>
					<button type='submit' class='btn btn-outline-primary fullwidth' id='load-table-button'>Load Data</button>
				</div>
			</form>
		</div>
	</div>
</div>
