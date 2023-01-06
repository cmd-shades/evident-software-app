<style type="text/css">
	body {
		background-color: #FFFFFF;
	}
	.table > thead > tr > th {
		cursor:pointer;
	}
</style>

<legend>Alert and Push Notifications Emulator</legend>
<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel">
			<div class="x_content">
				<div class="col-md-6 col-sm-12 col-xs-12">
					<legend class="text-muted font-13 m-b-30">APNS Push Test</legend>				
					<div class="apns_feedback" class="col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12" style="margin-bottom:12px;font-style:italic">
						<?php if( !empty($apns_feedback) ){ ?>
							<div class="text-<?php echo ( !empty($apns_status) && $apns_status == 1 ) ? 'green' : 'red'; ?>">
								<span><?php echo ucfirst($apns_feedback); ?></span>
							</div>							
						<?php } ?>
					</div>
					
					<form class="form-horizontal" action="<?php echo base_url('/webapp/apns/push/'.$this->uri->segment(4) ); ?>" method="post">
						<input type="hidden" name="account_id" value="<?php echo $this->account_id; ?>" />
						<div class="form-group">
							<label class="control-label col-md-3 col-sm-3 col-xs-12">Message Header</label>
							<div class="col-md-9 col-sm-9 col-xs-12">
								<input type="text" name="mtitle" value="Site In Distress" required class="form-control" placeholder="Notification Header">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3 col-sm-3 col-xs-12">Message Content</label>
							<div class="col-md-9 col-sm-9 col-xs-12">
								<textarea rows="3" type="text" name="mdesc" value="" required class="form-control" placeholder="Notification Content"></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3 col-sm-3 col-xs-12">Specific user (optional)</label>
							<div class="col-md-9 col-sm-9 col-xs-12">
								<select name="user_id" class="form-control">
									<?php if( !empty( $users ) ){ ?>
										<option value="" >Please select</option>
										<?php foreach( $users as $k=>$user ){ ?>
										<option value="<?php echo $user->id; ?>" ><?php echo ucwords($user->first_name.' '.$user->last_name) ?></option>
										<?php } ?>	
									<?php }else{ ?>
										<option value="" disabled selected=selected >There's currently no users setup for your account.</option>
									<?php } ?>								
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
								<button style="margin-left:-10px" type="submit" class="btn btn-sm btn-info btn-block">Push Notification</button>
							</div>
						</div>
					</form>				
				</div>
				<div class="col-md-offset-1 col-md-5 col-sm-12 col-xs-12">
					<legend class="text-muted font-13 m-b-30">Alert Emulator</legend>
					<div class="alert_feedback" class="col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12" style="margin-bottom:12px;font-style:italic">
						<?php if( !empty($alert_feedback) ){ ?>
							<div class="text-<?php echo ( !empty($alert_feedback) && $alert_feedback == 1 ) ? 'green' : 'green'; ?>">
								<span><?php echo ucfirst($alert_feedback); ?></span>
							</div>							
						<?php } ?>
					</div>
					<form class="form-horizontal" action="<?php echo base_url('/webapp/apns/trigger/'.$this->uri->segment(4) ); ?>" method="post">
						<input type="hidden" name="account_id" value="<?php echo $this->account_id; ?>" />
						<div class="form-group">
							<label class="control-label col-md-3 col-sm-3 col-xs-12">Trigger Type</label>
							<div class="col-md-9 col-sm-9 col-xs-12">
								<select name="trigger_type" class="form-control" required >
									<option value="" >Please select trigger type</option>
									<option value="OK" >OK Status</option>
									<option value="Fault" selected=selected >Fault Alert</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3 col-sm-3 col-xs-12">Site Packet</label>
							<div class="col-md-9 col-sm-9 col-xs-12">
								<select name="packet_id" class="form-control" required >
									<option value="" >Please select site packet</option>
									<?php if( !empty($site_packets) ){ foreach( $site_packets as $packet_id => $packet_details ){ ?>
										<option value="<?php echo $packet_details->panel_code; ?>" ><?php echo ucwords($packet_details->site_name.' ('.$packet_details->panel_code.')' ); ?></option>
									<?php } } ?>								
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-9 col-md-offset-3 col-sm-9 col-sm-offset-3 col-xs-12">
								<button style="margin-left:-10px" type="submit" class="btn btn-sm btn-info btn-block">Trigger Alert</button>
							</div>
						</div>
					</form>
				</div>	
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		$(".apns_feedback, .alert_feedback").delay(4000).fadeOut(1500);		
	});
</script>