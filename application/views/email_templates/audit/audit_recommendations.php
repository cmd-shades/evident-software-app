<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Evident Software - Alert</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<style>
			body {
				font-family: Helvetica Neue,Helvetica,Arial,sans-serif; 
			}
		</style>
	</head>

	<body style="margin: 0; padding: 0; font-family: 'Droid Sans', Arial;">
		<table align="center" border="0" cellpadding="4" cellspacing="0" width="70%">
			<tr>
				<td colspan="4"><?php echo ( !empty( $salutation ) ) ? $salutation : 'Hello,'?></td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4">An Audit has been completed and <strong style="color:<?php echo ( !empty( $content['result_status_colour'] ) ) ? $content['result_status_colour'] : ''; ?>">Passed with recommendations</strong>. Details are shown below</td>
			</tr>
			<tr>
				<td colspan="4" >
					<table align="center" border="0" cellpadding="4" cellspacing="0" width="100%">
						<tr>
							<td colspan="4">&nbsp;</td>
						</tr>
						<tr>
							<td width="100%" colspan="2"><strong>AUDIT DETAILS</strong></td>
						</tr>
						<tr>
							<td width="20%"><strong>Audit Type</strong></td>
							<td width=""><a href="<?php echo base_url( 'webapp/audit/profile/'.$content['audit_id'] ); ?>" target="_blank" ><?php echo ( !empty( $content['alt_audit_type'] ) ) ? $content['alt_audit_type'] : $content['audit_type']; ?></a></td>
						</tr>
						<tr>
							<td width="20%"><strong>Result Status</strong></td>
							<td width=""><?php echo ( !empty( $content['result_status_desc'] ) ) ? $content['result_status_desc'] : $content['result_status_alt']; ?></td>
						</tr>
						<tr>
							<td width="20%"><strong>Submitted By</strong></td>
							<td width=""><?php echo ( !empty( $content['submitted_by'] ) ) ? $content['submitted_by'] : 'Unknown user'; ?></td>
						</tr>
						<tr>
							<td width="20%"><strong>Timestamp</strong></td>
							<td width=""><?php echo ( !empty( $content['timestamp'] ) ) ? $content['timestamp'] : ''; ?></td>
						</tr>
						<tr>
							<td width="20%"><strong>Action Due Date</strong></td>
							<td width=""><?php echo ( !empty( $content['action_due_date'] ) && valid_date( $content['action_due_date'] ) ) ? date( 'd-m-Y', strtotime( $content['action_due_date'] ) ) : ''; ?></td>
						</tr>
						<tr>
							<td colspan="4">&nbsp;</td>
						</tr>
						<tr>
							<td width="100%" colspan="2"><strong>RECOMMENDATION DETAILS</strong></td>
						</tr>
						<tr>
							<td width="100%" colspan="2"><em><?php echo ( !empty( $content['recommendations'] ) ) ? $content['recommendations'] : 'No data submitted...'; ?></em></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr><td colspan="4">This is an auto-generated email, please do not reply to it.</td></tr>
			<td colspan="4">&nbsp;</td>
			<tr><td colspan="4">Kind regards.</td></tr>
			<td colspan="4">&nbsp;</td>
			<tr><td colspan="4"><strong>Evident Software System Admin</strong><br/><a href="mailto:alerts@evidentsoftware.co.uk">alerts@evidentsoftware.co.uk</a></td></tr>
		</table>
	</body>
</html>
