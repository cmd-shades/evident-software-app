<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel tile has-shadow">
            <legend>Assigned Assets</legend>
            <table id="datatable" class="table-responsive" style="margin-bottom:0px;width:100%" >
                <thead>
                    <tr>
                        <th width="5%">Asset ID</th>
                        <th width="20%">Asset name</th>
                        <th width="15%">Make &amp; Model</th>
                        <th width="10%">Type</th>
                        <th width="10%">Sub Type</th>
                        <th width="15%">Unique ID</th>
                        <th width="15%">IMEI #</th>
                        <th width="10%">Status</th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php if (!empty($assigned_assets)) {
                        foreach ($assigned_assets as $asset) { ?>
                        <tr>
                            <td><?php echo ucwords($asset->asset_id); ?></td>
                            <td><a href="<?php echo base_url('webapp/asset/profile/' . $asset->asset_id); ?>" ><?php echo ucwords($asset->asset_name); ?></a></td>
                            <td><?php echo $asset->asset_make; ?> <?php echo $asset->asset_model; ?></span></td>
                            <td><?php echo $asset->asset_type; ?></td>
                            <td><?php echo $asset->asset_sub_type; ?></td>
                            <td><?php echo strtoupper($asset->asset_unique_id); ?></td>
                            <td><?php echo strtoupper($asset->asset_imei_number); ?></td>
                            <td><?php echo $asset->asset_status; ?></td>
                        </tr>
                        <?php }
                        } else { ?>
                        <tr>
                            <td colspan="7"><?php echo $this->config->item('no_records'); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>      
    </div>
</div>

<script>
    $(document).ready(function(){

    });
</script>