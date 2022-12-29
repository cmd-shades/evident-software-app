<div class="row group-content el-hidden">
    <div class="row">
        <?php
        $todays_date = date('Y-m-d');
        if (!empty($provider_packet_identifiers)) { ?>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <table class="table-responsive container-full">
                        <tr>
                            <th class="codec_row_h">Definition</th>
                            <th class="codec_row_h">Codec Type</th>
                            <th class="codec_row_h">Codec Name</th>
                            <th class="codec_row_h">Is Adult</th>
                            <th class="codec_row_h">PID</th>
                            <th class="codec_row_h">Language/Subtitles</th>
                            <th class="codec_row_h">Description</th>
                            <th class="codec_row_h">Edit</th>
                            <th class="codec_row_h">Delete</th>
                        </tr>
                    <?php
                    foreach ($provider_packet_identifiers as $pack_row) { ?>
                        <tr>
                            <td class="codec_row"><?php echo (!empty($pack_row->definition_name)) ? ucwords(html_escape($pack_row->definition_name)) : 'Not specified' ; ?></td>
                            <td class="codec_row"><?php echo (!empty($pack_row->type_name)) ? ucwords(html_escape($pack_row->type_name)) : 'Not specified' ; ?></td>
                            <td class="codec_row"><?php echo (!empty($pack_row->long_name)) ? ucwords(html_escape($pack_row->long_name)) : 'Not specified' ; ?></td>
                            <td class="codec_row"><?php echo (!empty($pack_row->is_adult) && ($pack_row->is_adult > 0)) ? ucwords("yes") : 'No' ; ?></td>
                            <td class="codec_row"><?php echo (!empty($pack_row->pid)) ? ((int) $pack_row->pid) : 'Not specified' ; ?></td>
                            <td class="codec_row"><?php echo (!empty($pack_row->language_symbol)) ? ($pack_row->language_symbol) : 'Not specified' ;
                        echo "/";
                        echo (!empty($pack_row->subtitle_code)) ? ($pack_row->subtitle_code) : 'Not specified' ;  ?></td>
                            <td class="codec_row"><?php echo (!empty($pack_row->description)) ? ucwords(html_escape($pack_row->description)) : 'Not specified' ; ?></td>
                            <td class="codec_row ppi_<?php echo $pack_row->identifier_id ?>">
                                <span class="edit-identifier" data-identifier_id="<?php echo (!empty($pack_row->identifier_id)) ? $pack_row->identifier_id : '' ;?>" href="#" data-toggle="modal" data-target="#editIdentifier"><a href="#"><i class="fas fa-edit"></i></a></span>
                            </td>
                            <td class="codec_row"><span class="delete-identifier" data-identifier_id="<?php echo (!empty($pack_row->identifier_id)) ? $pack_row->identifier_id : '' ;?>"><div class=""><a href="#"><i class="fas fa-trash-alt"></i></a></div></span></td>
                        </tr>
                        <?php
                    } ?>
                </table>
            </div>
            <?php
        } ?>
    </div>
</div>