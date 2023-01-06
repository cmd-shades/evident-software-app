<style>

    .response-form {
        border-collapse: collapse;
        width: 100%;
        page-break-inside: avoid;
        margin-top: 20px;
    }

    .response-form th, .response-form td{
        border: 1px solid lightgray;
        text-align: left;
    }

</style>
<div>
    <table class='response-form' cellpadding="5px">
        <tr>
            <th style="width:50%">Schedule Name</th><td style="width: 50%;"><?php echo ucwords( html_escape( $document_content->schedule_name ) ); ?></td>
		</tr>
		<tr>
            <th style="width:50%">Schedule Status</th><td style="width: 50%;"><?php echo ucwords( html_escape( $document_content->schedule_status ) ); ?></td>
		</tr>
		<tr>
            <th style="width:50%">Frequency</th><td style="width: 50%;"><?php echo ucwords( html_escape( $document_content->frequency_name ) ); ?></td>
		</tr>
		<tr>
            <th style="width:50%">Activities Completed</th><td style="width: 50%;"><?php echo ucwords( html_escape( $document_content->activities_completed ) ); ?></td>
		</tr>
		<tr>
            <th style="width:50%">Activities Pending</th><td style="width: 50%;"><?php echo ucwords( html_escape( $document_content->activities_pending ) ); ?></td>
		</tr>
		<tr>
            <th style="width:50%">Activities Total</th><td style="width: 50%;"><?php echo ucwords( html_escape( $document_content->activities_total ) ); ?></td>
        </tr>
		<tr>
            <th style="width:50%">First Activity Due Date</th><td style="width: 50%;"><?php echo ucwords( html_escape( $document_content->first_activity_due_date ) ); ?></td>
        </tr>
    </table>
</div>