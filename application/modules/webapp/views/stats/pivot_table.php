<?php

	error_reporting(E_ERROR);
	
	$koolControlsFolder = "assets/KoolControls";

    require $koolControlsFolder . "/KoolAjax/koolajax.php";
	require $koolControlsFolder . "/KoolPivotTable/koolpivottable.php";

	$koolajax->scriptFolder = base_url() ."assets/KoolControls/KoolAjax";

	$db_con = mysqli_connect( $this->db->hostname, $this->db->username, $this->db->password, $this->db->database );

	if (!$db_con) {
		die('Could not connect: ' . mysql_error() . ".<br>[Evident] Please contact your system administrator!");
	}

	#$ds = new MySQLiPivotDataSource( $this->db );
	$ds = new MySQLiPivotDataSource( $db_con );

	$ds	->select(implode (", ", $columns)+" WHERE account_id = "+ )->from($table_name);

	$pivot = new KoolPivotTable("pivot");

	$pivot->styleFolder = "default";

	$pivot->DataSource = $ds;

	$pivot->AjaxEnabled = true;

	$pivot->Height = "100%";
	$pivot->Width = "1700px";

	$pivot->HorizontalScrolling = true;

	$pivot->AllowFiltering = false;
	
	$pivot->AllowSorting = true;
	$pivot->AllowReorder = true;

	$pivot->AllowCaching = false;

	foreach ($columns as $column){
		$field = new PivotField($column);
		$field->Text = $column;
		$pivot->AddFilterField($field);
	}

	$pivot->Process();

?>

<?php echo $koolajax->Render();?>

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div style="overflow: auto; width: 100%">
				<?php echo $pivot->Render();?>
			</div>
		</div>
	</div>
</div>