<style type="text/css">
#ajax{
	display: block;
    width: 100%;
    float: left;
    padding: 6px;
    background: #fff;
    border: 1px solid #ccc;
	height: 200px;
    overflow: auto;
}

.input-group.form-group{
	width: 100%;
}

.error_message{
	color: #ff0000;	
}


h5{
	float: left;
}
</style>


<div>
	<legend>Create New Contract</legend>
	<form id="quote-creation-form" method="post">
		<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
		<input type="hidden" name="page" value="details" />
		<div class="row">
			<div class="site_creation_panel1 col-md-6 col-sm-12 col-xs-12">
				<div class="x_panel tile has-shadow">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5 class="left">What is the Contract Name?</h5>
							<h5 class="pull-right error_message" style="display: none;">Please, enter the Contract Name</h5>
							<div class="input-group form-group" style="width: 100%;">
								<label class="input-group-addon">Contract Name&nbsp;*</label>
								<input name="contract_name" type="text" class="form-control" id="contract_name" required>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12 pull-right">
							<button class="btn btn-block btn-flow btn-success btn-next contract-creation-steps" data-currentpanel="site_creation_panel1" type="button">Next</button>
						</div>
					</div>
				</div>
			</div>

			<div class="site_creation_panel2 col-md-6 col-sm-12 col-xs-12" style="display: none;">
				<div class="x_panel tile has-shadow">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5>What is the Type of the Contract?</h5>
							<h5 class="pull-right error_message" style="display: none;">Please select the Contract Type</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">Contract Type&nbsp;</label>
								<select name="contract_type_id" class="form-control" id="contract_type_id" required>
									<option value="">Please select the type of the contract</option>
									<?php
                                    if (!empty($contract_types)) {
                                        foreach ($contract_types as $row) { ?>
											<option value="<?php echo $row->type_id; ?>"><?php echo ucwords($row->type_name); ?></option>
										<?php
                                        }
                                    } else { ?>
										<option value="8">Asset Management</option>
									<?php
                                    } ?>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-info btn-back" data-currentpanel="site_creation_panel2" type="button" >Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-success btn-next contract-creation-steps" data-currentpanel="site_creation_panel2" type="button" >Next</button>
						</div>
					</div>
				</div>
			</div>


			<div class="site_creation_panel3 col-md-6 col-sm-12 col-xs-12" style="display: none;">
				<div class="x_panel tile has-shadow">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5>What is the Status of the Contract?</h5>
							<h5 class="pull-right error_message" style="display: none;">Please, select the Contract Status</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">Contract Status&nbsp;*</label>
								<select name="contract_status_id" type="text" class="form-control" id="contract_status_id" required>
									<option value="">Please, select the status of the contract</option>
									<?php
                                    if (!empty($contract_statuses)) {
                                        foreach ($contract_statuses as $row) { ?>
											<option value="<?php echo $row->status_id; ?>"><?php echo ucwords($row->status_name); ?></option>
										<?php
                                        }
                                    } else { ?>
										<option value="16">Awaiting Action Default</option>
									<?php
                                    } ?>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-info btn-back" data-currentpanel="site_creation_panel3" type="button" >Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-success btn-next contract-creation-steps" data-currentpanel="site_creation_panel3" type="button" >Next</button>
						</div>
					</div>
				</div>
			</div>

			<div class="site_creation_panel4 col-md-6 col-sm-12 col-xs-12" style="display: none;">
				<div class="x_panel tile has-shadow">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5>Who is the Contract Leader?</h5>
							<h5 class="pull-right error_message" style="display: none;">Please, select the Contract Leader</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">Contract Leader&nbsp;*</label>
								<select name="contract_lead_id" type="text" class="form-control" id="contract_lead_id" required>
									<option value="">Please, select the person, who is leading the contract</option>
										<?php
                                        if (!empty($contract_leaders)) {
                                            asort($contract_leaders);
                                            foreach ($contract_leaders as $row) { ?>
												<option value="<?php echo $row->id ?>"><?php echo ucwords($row->first_name.' '.$row->last_name); ?></option>
											<?php
                                            }
                                        } else { ?>
											<option value="">Please add users</option>
										<?php
                                        } ?>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-info btn-back" data-currentpanel="site_creation_panel4" type="button" >Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-success btn-next contract-creation-steps" data-currentpanel="site_creation_panel4" type="button" >Next</button>
						</div>
					</div>
				</div>
			</div>

			<div class="site_creation_panel5 col-md-6 col-sm-12 col-xs-12" style="display: none;">
				<div class="x_panel tile has-shadow">
					<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
					<h5>What is the Contract Start and End Date?</h5>
					<div class="input-group form-group">
						<label class="input-group-addon">Start Date:</label>
						<input type="text" name="start_date" value="<?php echo date('d/m/Y'); ?>" class="form-control datetimepicker" id="datetimepicker1" data-date-format="DD/MM/Y" placeholder="<?php echo date('d/m/Y'); ?>" required />
					</div>
					<div class="input-group form-group">
						<label class="input-group-addon">End Date:</label>
						<input type="text" name="end_date" value="<?php echo date('d/m/Y'); ?>" class="form-control datetimepicker" id="datetimepicker2" data-date-format="DD/MM/Y" placeholder="<?php echo date('d/m/Y'); ?>" required />
					</div>
					</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-info btn-back" data-currentpanel="site_creation_panel5" type="button">Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-success btn-next contract-creation-steps" data-currentpanel="site_creation_panel5" type="button">Next</button>
						</div>
					</div>
				</div>
			</div>


			<div class="site_creation_panel6 col-md-6 col-sm-12 col-xs-12" style="display: none;">
				<div class="x_panel tile has-shadow">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5>What is the Description of the Contract?</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">Description</label>
								<textarea name="description" type="text" rows="4" class="form-control" id="description"></textarea>
							</div>
							<div class="input-group form-group">
								<label class="input-group-addon">Note</label>
								<textarea name="last_note" type="text" rows="4" class="form-control" id="last_note"></textarea>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-info btn-back" data-currentpanel="site_creation_panel6" type="button" >Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button type="submit" class="btn btn-block btn-flow btn-success" data-currentpanel="site_creation_panel6" id="createContractButton">Create Contract</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<script>
	$(document).ready( function(){

		/* https://xdsoft.net/jqplugins/datetimepicker/ */
		$( '.datetimepicker' ).datetimepicker({
			formatDate: 'd/m/Y',
			timepicker:false,
			format:'d/m/Y',
		});

		$( ".contract-creation-steps" ).click( function(){
			var currentpanel = $( this ).data( "currentpanel" );
			var inputs_state = check_inputs( currentpanel );

			if( inputs_state == true ){
				panelchange( "." + currentpanel )
				return false;
			} else {
				show_warning( currentpanel );
				return false;
			}
		});

		$(".btn-back").click(function(){
			var currentpanel = $(this).data("currentpanel");
			go_back("."+currentpanel)
			return false;
		});

		function panelchange( changefrom ){
			var panelnumber = parseInt( changefrom.slice( 20 ) )+parseInt( 1 );
			var changeto = ".site_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", { direction : 'left' }, 500 );
			$( changeto ).delay( 600 ).show( "slide", {direction : 'right'},500 );
			return false;
		}

		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.slice( 20 ) )-parseInt( 1 );
			var changeto = ".site_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'right'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'left'},500);
			return false;
		}

		function check_inputs( currentpanel ){
			var result = true;
			var panel = "." + currentpanel;

			$( panel + " input" ).each( function(){
				var value = $( this ).val();
				if( ( value == false ) || ( value == '' ) ){
					result = false;
				}
			});

			$( panel + " select" ).each( function(){
				var value = $( this ).val();
				if( ( value == false ) || ( value == '' ) ){
					result = false;
				}
			});

			return result;
		}

		function show_warning( currentpanel ){
			var panel = "." + currentpanel;
			$( panel ).find( ".error_message" ).show();
		}

		//Submit contract form
		$( '#createContractButton' ).click( function( e ){
			e.preventDefault();
			var formData = $( '#quote-creation-form' ).serialize();
			swal({
				title: 'Confirm new contract creation?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if (result.value) {
					$.ajax({
						url:"<?php echo base_url('webapp/contract/add_contract/'); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 && ( data.contract_id !== '' ) ){
								var newContractId = data.contract_id;
								swal({
									type: 'success',
									title: data.message,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout( function(){
									location.href = "<?php echo base_url('webapp/contract/profile/'); ?>" + newContractId;
								}, 3000 );
							} else {
								swal({
									type: 'error',
									title: data.message
								})
							}
						}
					});
				} else {
					$( ".site_creation_panel6" ).hide( "slide", { direction : 'left' }, 500 );
					go_back( ".site_creation_panel2" );
					return false;
				}
			}).catch(swal.noop)
			
		});
	});
</script>