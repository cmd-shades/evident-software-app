<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel tile has-shadow">
            <legend>Change Log</legend>
<?php       if ($this->user->is_admin || !empty($permissions->can_view) || !empty($permissions->is_admin)) { ?>
                <table style="width:100%">
                    <tr>
                        <th width="10%">Log Id</th>
                        <th width="20%">Section</th>
                        <th width="30%">Action</th>
                        <th width="20%">Action Date</th>
                        <th width="20%">Actioned By</th>
                    </tr>
                    <?php if (!empty($person_change_logs)) {
                        foreach ($person_change_logs as $row) { ?>
                        <tr>
                            <td class="pos_<?php echo $row->change_log_id; ?>"><?php echo ucfirst($row->change_log_id); ?></td>
                            <td><?php echo ucfirst($row->log_type); ?></td>
                            <td><?php echo ucfirst($row->action); ?></td>
                            <td><?php echo $row->created_date; ?></td>
                            <td><?php echo $row->created_by_full_name; ?></td>
                        </tr>
                        <?php }
                        } else { ?>
                        <tr>
                            <td colspan="5"><?php echo $this->config->item('no_records'); ?></td>
                        </tr>
                    <?php } ?>
                </table>
<?php } ?>
        </div>      
    </div>
</div>