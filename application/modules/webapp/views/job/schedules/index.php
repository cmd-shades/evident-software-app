<style>
	.panel-body{
		background-color:#F7F7F7; 
		height:140px; 
		min-height:140px;
	}
	
	.input-group-addon {
		min-width: 220px;
	}
	
	a{ color: #0092CD;
		font-weight: bold;
	}
</style>

<div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
	<?php if( !empty( $contract_details ) ){ ?>
		<legend class="evidocs-legend">Contract Schedules</legend>
		<?php include( 'create_schedule_contract.php' ); ?>	
	<?php } else if( !empty( $site_details ) ){ ?>
		<legend class="evidocs-legend">Building Schedules</legend>
		<?php #include( 'create_schedule_site.php' ); ?>	
		<?php include( 'create_schedule_site_revised.php' ); ?>	
	<?php } else if( !empty( $location_details ) ){ ?>
		<legend class="evidocs-legend">Location Schedules</legend>
		<?php include( 'create_schedule_location.php' ); ?>
	<?php } else if( !empty( $asset_details ) ){ ?>
		<legend class="evidocs-legend">Asset Schedules</legend>
		<?php include( 'create_schedule_asset.php' ); ?>
	<?php } else { ?>
		<h4>There's currently no data available to display!</h4>
	<?php } ?>
</div>

