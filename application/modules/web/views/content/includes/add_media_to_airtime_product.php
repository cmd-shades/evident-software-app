<div class="modal-body">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <form id="adding-media-to-airtime-product-form" data-content_id="<?php echo (!empty($content_details->content_id)) ? ($content_details->content_id) : '' ; ?>">
        <div class="row">
            <div class="adding-media-to-airtime1 col-md-12 col-sm-12 col-xs-12">
                <div class="slide-group">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <legend class="legend-header airtime-header-element">Add Media to Airtime Product</legend>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <h6 class="error_message pull-right" id="adding-media-to-airtime1-errors"></h6>
                        </div>
                    </div>

                    <div class="input-group form-group container-full">
                        <legend class="legend-header">Select File Type</legend>
                        <ul class="airtime_file_type">
                            <li>
                                <input class="required" name="airtime_file_type" type="radio" value="image" id="image-input" /><label for="image-input"><span>Image</span></label>
                            </li>
                            <li>
                                <input class="required"  name="airtime_file_type" type="radio" value="film_trailer" id="film-trailer-input" /><label for="film-trailer-input"><span>Film/Trailer</span></label>
                            </li>
                            <li>
                                <input class="required"  name="airtime_file_type" type="radio" value="subtitle" id="subtitle-input" /><label for="subtitle-input"><span>Subtitle</span></label>
                            </li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 col-lg-offset-6 col-md-6 col-md-offset-6 col-sm-6 col-sm-offset-6 col-xs-12">
                            <button class="btn-block btn-next select-file-type" data-currentpanel="adding-media-to-airtime1" type="button">Next</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="adding-media-to-airtime2 col-md-12 col-sm-12 col-xs-12 el-hidden">
                <div class="slide-group">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <legend class="legend-header">Content Compile Review</legend>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <h6 class="error_message pull-right" id="adding-media-to-airtime2-errors"></h6>
                        </div>
                    </div>

                    <div class="input-group form-group container-full images-table">
                        <?php
                        if (!empty($images)) { ?>
                            <div id="content_compile_review_images" class="table el-hidden">
                                <div class="section-container">
                                    <div class="section-line">
                                        <div class="no-top-border text-bold" style="width: 50%">FILE NAME</div>
                                        <div class="no-top-border text-bold text-center" style="width: 25%">TYPE</div>
                                        <div class="no-top-border text-bold text-center" style="width: 25%">ON AIRTIME (AT) <input class="checked-content-all" type="checkbox"></div>
                                    </div>
                                    <?php
                                    foreach ($images as $img) { ?>
                                        <div class="section-line" data-doc_id="<?php echo $img->document_id; ?>">
                                            <div class="text-left" style="width: 50%"><?php echo (!empty($img->document_name)) ? $img->document_name : '' ; ?></div>
                                            <div class="text-center" style="width: 25%"><?php echo (!empty($img->doc_file_type)) ? ucfirst($img->doc_file_type) : '' ; ?></div>
                                            <div class="text-center" style="width: 25%">
                                                <input class="checked-document" type="checkbox" name="checked_document[]" value="<?php echo (!empty($img->document_id)) ? $img->document_id : '' ; ?>" <?php echo (!empty($img->airtime_reference)) ? 'checked = "checked"' : '' ; ?> data-image_type="<?php echo (!empty($img->doc_file_type)) ? $img->doc_file_type : '' ; ?>" />
                                            </div>
                                        </div>
                                        <?php
                                    } ?>
                                </div>
                            </div>
                            <?php
                        } else { ?>
                            <div id="content_compile_review_images" class="table">
                                <div class="section-container">
                                    <div class="section-line"><div class="no-top-border text-bold">Images not found</div></div>
                                </div>
                            </div>
                            <?php
                        } ?>
                    </div>

                    <div class="input-group form-group container-full subtitles-table">
                        <?php
                        if (!empty($subtitles)) { ?>
                            <div id="content_compile_review_subtitles" class="table el-hidden">
                                <div class="section-container">
                                    <div class="section-line">
                                        <div class="col-subtitles-file-name text-bold">FILE NAME</div>
                                        <div class="col-subtitles-type text-bold text-center">TYPE</div>
                                        <div class="col-subtitles-onat text-bold text-center">ON AIRTIME (AT)</div>
                                        <div class="col-subtitles-select text-bold text-center">SELECT&nbsp;<input class="checked-content-all" type="checkbox" /></div>
                                    </div>
                                    <?php
                                    foreach ($subtitles as $sub) { ?>
                                        <div class="section-line" data-doc_id="<?php echo $sub->document_id; ?>">
                                            <div class="col-subtitles-file-name text-left"><?php echo (!empty($sub->document_name)) ? $sub->document_name : '' ; ?></div>
                                            <div class="col-subtitles-type text-center"><?php echo (!empty($sub->document_name)) ? strtoupper(pathinfo($sub->document_name, PATHINFO_EXTENSION)) : '' ; ?></div>
                                            <div class="col-subtitles-onat text-center"><?php echo (!empty($sub->airtime_reference)) ? '<i class="fas fa-check text-green"></i>' : '<i class="fas fa-times text-red"></i>' ; ?></div>

                                            <?php
                                            $path       = false;
                                        $account_id = $this->ion_auth->_current_user->account_id;
                                        $path       = '_account_assets/accounts/' . $account_id . '/content/' . $content_details->content_id . '/' . $sub->document_name; ?>

                                            <div class="col-subtitles-select text-center" data-path="<?php echo (file_exists($path)) ? base_url($path) : '' ; ?>">
                                                <input class="checked-document" type="checkbox" name="checked_document[]" value="<?php echo (!empty($sub->document_id)) ? $sub->document_id : '' ; ?>"<?php echo (file_exists($path)) ? '' : 'disabled="disabled" title="The file is not accessible"' ; ?> />
                                            </div>
                                        </div>
                                        <?php
                                    } ?>
                                </div>
                            </div>
                            <?php
                        } else { ?>
                            <div id="content_compile_review_subtitles" class="table" width="100%" style="border-top:none; font-size:90%">
                                <div class="section-container">
                                    <div class="section-line"><div class="no-top-border text-bold">Subtitles not found</div></div>
                                </div>
                            </div>
                            <?php
                        } ?>
                    </div>

                    <div class="input-group form-group container-full movies-table">
                        <?php
                        if (!empty($decoded_file_streams)) { ?>
                            <div id="content_compile_review_movies" class="table el-hidden">
                                <div class="section-container">
                                    <div class="section-line">
                                        <div class="col-movies-file-name text-bold">FILE NAME</div>
                                        <div class="col-movies-type text-bold">TYPE</div>
                                        <div class="col-movies-onaws text-bold">ON AWS</div>
                                        <div class="col-movies-onat text-bold">AIRTIME ID</div>
                                        <div class="col-movies-encoded text-bold">ENCODED</div>
                                        <div class="col-movies-select text-bold">SELECT <input class="checked-content-all" type="checkbox" /></div>
                                    </div>
                                    <?php
                                    foreach ($decoded_file_streams as $film) { ?>
                                        <div class="section-line" data-doc_id="<?php echo $film->file_id; ?>">
                                            <div class="col-movies-file-name"><?php echo (!empty($film->file_new_name)) ? $film->file_new_name : '' ; ?></div>
                                            <div class="col-movies-type"><?php echo (!empty($film->type_name)) ? ucfirst($film->type_name) : '' ; ?></div>
                                            <div class="col-movies-onaws"><?php echo (isset($film->is_on_aws) && ($film->is_on_aws > 0)) ? '<i class="fas fa-check text-green"></i>' : '<i class="fas fa-times text-red"></i>' ; ?></div>
                                            <div class="col-movies-onat"><?php echo (!empty($film->airtime_reference)) ? '<i class="fas fa-check text-green" title="' . $film->airtime_reference . '"></i>' : '<i class="fas fa-times text-red"></i>' ; ?></div>
                                            <?php
                                            ## Possible values for $film->airtime_encoded_status from the AT Webhook: ["not-encoded", "encoding", "encoded", "encode-cancelled", "encode-failed", "unknown"];?>
                                            <div class="col-movies-encoded"><?php echo (!empty($film->airtime_encoded_status) && (in_array(strtolower($film->airtime_encoded_status), ["encoded"])) && ($film->is_airtime_encoded != false)) ? '<i class="fas fa-check text-green"></i>' : '<i class="fas fa-times text-red"></i>' ; ?></div>

                                            <div class="col-movies-select">
                                                <input class="checked-document" type="checkbox" name="checked_document[]" value="<?php echo (!empty($film->file_id)) ? $film->file_id : '' ; ?>" <?php echo (!empty($film->airtime_reference)) ? 'checked = "checked"' : '' ; ?> <?php echo (!empty($film->airtime_reference) && (($film->is_airtime_encoded != true) && (empty($film->airtime_encoded_status) || !in_array(strtolower($film->airtime_encoded_status), ["encoded"])))) ? '' : 'disabled="disabled"' ?> />
                                            </div>
                                        </div>
                                        <?php
                                    } ?>
                                </div>
                            </div>
                            <?php
                        } else { ?>
                            <div id="content_compile_review_movies" class="table">
                                <div>
                                    <div class="section-line"><div class="no-top-border text-bold">Film/Trailer not found</div></div>
                                </div>
                            </div>
                            <?php
                        } ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button class="btn-block btn-back" data-currentpanel="adding-media-to-airtime2" type="button">Back</button>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <button class="btn-block btn-next disabled" data-currentpanel="adding-media-to-airtime2" type="submit" disabled="disabled">Encode Selected Files</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>