<style>
	.input-group-addon {
		width: 100%;
		min-width: 60px;
		text-align: left;
		width: 40px;
	}
</style>

<?php if (!empty($module_identier)) {
    switch($module_identier) {
        case 'job':	?>
			<!-- Place your view code here -->
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="input-prepend input-group form-group">
						<span class="input-group-addon <?php echo $module_identier; ?>-bg">From <!-- <i class="fas fa-calendar-alt"></i> --></span>
						<input style="width: 120px" name="date_from" class="form-control datepicker datepicker-<?php echo $module_identier; ?> date-range" type="text" placeholder="From" value="<?php echo date('d-m-Y') ?>" />
					</div>
				</div>
				
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="input-prepend input-group form-group">
						<span class="input-group-addon <?php echo $module_identier; ?>-bg">To <!-- <i class="fas fa-calendar-alt"></i> --></span>
						<input style="width: 120px" name="date_to" class="form-control datepicker datepicker-<?php echo $module_identier; ?> date-range" type="text" placeholder="To" value="<?php echo date('d-m-Y') ?>" />
					</div>
				</div>
			</div>
		<?php break;
        case 'fleet':?>
			<!-- Place your view code here -->
			
		<?php break;
        default: ?>
			<!-- Place default your view code here -->

		<?php break; ?>
	<?php } ?>
<?php } ?>
