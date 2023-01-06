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
            <!-- <th style="width:10%">ID</th> -->
            <th colspan="2" style="width:40%">Question / Label Name</th>
            <th style="width:35%">Response</th>
            <th style="width:25%">Comments</th>
        </tr>
        <tr>
            <!-- <td>Q<?php //echo html_escape($response->ordering); ?>.</td> -->
            <td colspan="2" ><?php echo html_escape($response->question); ?></td>
            <td><?php echo html_escape($response->response); ?></td>
            <td><?php echo html_escape($response->response_extra); ?></td>
        </tr>
    </table>
</div>