<link rel="stylesheet" type="text/css" href="<?php echo base_url( "assets/css/responsive-card-table.css" ); ?>" />

<style type="text/css">
	.feedback{
		color: red;
		font-size: 24px;
		font-weight: 600;
		border: 1px solid red;
		padding: 10px 5px;
	}

	.money{
		text-align: right;
	}

	.right{
		float: right;
	}

	.table {
		margin-bottom: 0px;
	}
</style>

<!-- Page Content -->
<div class="content">

	<?php
	if( !empty( $contract_data ) ){
	?>

		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<!-- Page Heading/Breadcrumbs -->
				<h1 class="mt-4 mb-3" style="color:#d66204;">Contract Manager - Central Dashboard s</h1>
			</div>
		</div>
		<?php
		if( !empty( $feedback ) ){ ?>
			<div class="row">
				<div class="col-lg-6 mb-4 pull-left">
					<h4 class="feedback"><?php echo $feedback ?></h4>
				</div>
			</div>
		<?php
		} ?>


		<!-- Content Row -->
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="pull-left">
							<legend>Most recent contracts:</legend>
						</div>
						<div class="pull-right">
							<form action="<?php echo base_url( "webapp/Contract/add_Contract" ) ?>" method="post" id="delete_Contract" novalidate>
								<button type="submit" class="btn btn-primary" id="deleteContractButton" style="background-color: #ff0000;border-color: #d86b3c;float: right;">Create a New Contract</button>
							</form>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<table class="table sortable" id="">
							<thead>
								<tr>
									<th>Contract Name</th>
									<th>Contract Reference</th>
									<th>Contract Type</th>
									<th>Contract Status</th>
									<th>Contract Lead Name</th>
									<th>Contract Start Date</th>
									<th>Contract End Date</th>
									<th>Created On</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								foreach( $contract_data as $row ){ 
									$link = "<a href=".base_url( "webapp/Contract/profile/".$row->contract_id ).">"; ?>
									<tr class="" data-id="<?php echo $row->contract_id; ?>">
										<td data-label="Contract Name"><?php echo $link; echo $row->contract_name; ?></a></td>
										<td data-label="Contract Reference"><?php echo $row->contract_ref; ?></td>
										<td data-label="Contract Type"><?php echo $row->type_name; ?></td>
										<td data-label="Contract Status"><?php echo $row->status_name; ?></td>
										<td data-label="Contract Lead Name"><?php echo $row->contract_lead_name; ?></td>
										<td data-label="Contract Start Date"><?php echo $row->start_date; ?></td>
										<td data-label="Contract End Date"><?php echo $row->end_date; ?></td>
										<td data-label="Created On"><?php echo ( $row->date_created ); ?></td>
									</tr>
								<?php
								} ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->

	<?php
	} else { ?>
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="pull-left full-width">
							<h1 class="mt-4 mb-3" style="color:#d66204;">Contract Manager - Dashboard</h1>
							<legend>There is no data in the system</legend>
						</div>
						<div class="pull-right">
							<form action="<?php echo base_url( "webapp/Contract/add_Contract" ) ?>" method="post" id="delete_Contract" novalidate>
								<button type="submit" class="btn btn-primary" id="deleteContractButton" style="background-color: #ff0000;border-color: #d86b3c;float: right;">Create a New Contract</button>
							</form>
						</div>
					</div>
				</div>
			
			
				<!-- Page Heading/Breadcrumbs -->
			</div>
		</div>
	<?php
	} ?>
</div>
<!-- /.container / content -->


