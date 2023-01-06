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

	<body style="margin: 0; padding: 0; font-family: 'Droid Sans', Arial; font-size:90%">
		<table align="center" border="0" cellpadding="4" cellspacing="0" width="85%" style="font-size:90%">
			<tr>
				<td colspan="5"><?php echo ( !empty( $salutation ) ) ? $salutation : 'Hello,'?></td>
			</tr>
			<tr>
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="5">New Job(s) created for site <b><a href="<?php echo ( $site_data->site_id ) ? base_url( 'webapp/site/profile/'.$site_data->site_id.'/jobs' ) : '#'; ?>" target="_blank" ><?php echo ( !empty( $site_data->site_summaryline ) ) ? $site_data->site_summaryline : 'Unknown Site Address'; ?></a></b>.</td>
			</tr>
			<tr>
				<td colspan="5">&nbsp;</td>
			</tr>
			<?php if( !empty( $jobs_data ) ){ ?>
				<tr>
					<td colspan="5" >
						<table align="center" border="0.25" cellpadding="4" cellspacing="0" width="100%" style="font-size:90%" >
							<tr>
								<td width="5%"><b>JOB ID</b></td>
								<td width="17%"><b>SITE DETAILS</b></td>
								<td width="10%"><b>SITE REF</b></td>
								<td width="20%"><b>JOB TYPE</b></td>
								<td width="20%"><b>WORKS REQUIRED</b></td>
								<td width="10%"><b>JOB DATE</b></td>
								<td width="8%"><b>STATUS</b></td>
								<td width="10%"><b>CREATED BY</b></td>
							</tr>
							<?php foreach( $jobs_data as $k => $job ){ ?>
								<tr>
									<td><a href="<?php echo base_url( 'webapp/job/profile/'.$job->job_id ); ?>" target="_blank" ><?php echo $job->job_id; ?></a></td>
									<td><a href="<?php echo base_url( 'webapp/site/profile/'.$job->site_id ); ?>" target="_blank" ><?php echo !empty( $site_data->site_summaryline ) ? $site_data->site_summaryline : ( ( !empty( $site_data->site_name ) ? $site_data->site_name : '' ).' '.!empty( $site_data->site_postcodes ) ? strtoupper( $site_data->site_postcodes ) : '' ); ?></a></td>
									<td><?php echo !empty( $site_data->site_reference ) ? $site_data->site_reference : '' ?></a></td>
									<td><?php echo !empty( $job->site_reference ) ? $job->site_reference : '' ?></a></td>
									<td><?php echo !empty( $job->works_required ) ? htmlspecialchars_decode( $job->works_required ) : ''; ?></td>
									<td><?php echo ( !empty( $job->job_date ) && validate_date( $job->job_date ) ) ? date( 'd-m-Y', strtotime( $job->job_date ) ) : '---'; ?></td>
									<td><?php echo $job->job_status; ?></td>
									<td><?php echo $job->created_by; ?></td>
								</tr>
							<?php } ?>
						</table>					
					</td>
				</tr>
				<tr>
					<td colspan="5">&nbsp;</td>
				</tr>
				<tr><td colspan="5">Click <a href="<?php echo base_url( 'webapp/job/bulk_assign' ); ?>" target="_blank" >HERE</a> to book these Jobs.</td></tr>
			<?php } else { ?>
				<tr>
					<td colspan="5"><p style="color:red">Oops! Something went wrong! Please contact your system administrator!</p></td>
				</tr>	
			<?php } ?>
			<tr>
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr><td colspan="5">This is an auto-generated email, please do not reply to it.</td></tr>
			<td colspan="5">&nbsp;</td>
			<tr><td colspan="5">Regards.</td></tr>
			<td colspan="5">&nbsp;</td>
			<tr><td colspan="5"><strong>Evident Software System Admin</strong><br/><a href="mailto:alerts@evidentsoftware.co.uk">alerts@evidentsoftware.co.uk</a></td></tr>
		</table>
	</body>
</html>
