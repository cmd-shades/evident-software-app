<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row">
					<div class="row">
						<div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12">
							<div class="x_panel tile has-shadow">
								<form id="create-risk-form" class="form-horizontal">
									<legend>Risk Details</legend>
									<div class="input-group form-group">
										<label class="input-group-addon">Risk Code</label>
										<input id="risk_code" name="risk_code" class="form-control" type="text" placeholder="Risk Code" value="" />
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Risk Rating</label>
										<select name="risk_rating" class="form-control">
											<option>Please select</option>
											<option value="Low" selected >Low</option>
											<option value="Medium" >Medium</option>
											<option value="High" >High</option>
										</select>	
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Risk Score</label>
										<input id="risk_score" name="risk_score" class="form-control numbers-only" type="number" placeholder="Risk Score" value="" />
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Risk Text</label>
										<input name="risk_text" class="form-control" type="text" placeholder="Risk text" value="" placeholder="Risk Text" />
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Risk Harm</label>
										<textarea id="risk_harm" name="risk_harm" type="text" class="form-control" rows="3" placeholder="Risk Harm" ></textarea>     
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Persons At Risk</label>
										<input name="persons_at_risk" class="form-control" type="text" placeholder="Persons At Risk" value="" />
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Residual Risk</label>
										<select name="residual_risk" class="form-control">
											<option>Please select</option>
											<option value="Low" selected >Low</option>
											<option value="Medium" >Medium</option>
											<option value="High" >High</option>
										</select>	
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Control Measures</label>
										<textarea id="control_measures" name="control_measures" type="text" class="form-control" rows="3" placeholder="Control Measures" ></textarea>     
									</div>
									<div class="row">
										<div class="col-md-6">
											<button class="btn btn-sm btn-block btn-success btn-next" type="submit">Create Risk Item</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$( "form#create-risk-form" ).submit( function( e ){
	e.preventDefault();
	
	var formData = $( this ).serialize();

	swal({
		title: 'Confirm Risk Risk creation?',
		showCancelButton: true,
		confirmButtonColor: '#5CB85C',
		cancelButtonColor: '#9D1919',
		confirmButtonText: 'Yes'
	}).then( function( result ){
		if( result.value ) {
			$.ajax({
				url:"<?php echo base_url( 'webapp/job/create_risk_item/' ); ?>",
				method: "POST",
				data: formData,
				dataType: 'json',
				success:function( data ){
					if( ( data.status == 1 ) && ( data.risk_item.risk_id !== '' ) ){
						var newRiskID = data.risk_item.risk_id;
						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 2000
						})
						window.setTimeout( function(){
							location.href = "<?php echo base_url( 'webapp/job/risks/' ) ?>" + newRiskID;
						}, 1000);
					} else {
						swal({
							type: 'error',
							title: data.status_msg
						})
					}
				}
			});
		}
	}).catch( swal.noop )
});
</script>
