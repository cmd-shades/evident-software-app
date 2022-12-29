<?php
$container_column   = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';

$module_count       = (!empty($marketing_modules)) ? count($marketing_modules) : 0;



if ($module_count > 0) {
    $inner_columns    = 'col-md-2 col-sm-4 col-xs-6';
    switch ($module_count) {
        case 0:
        case 1:
        case 2:
        case 3:
        case 4:
            $inner_columns    = 'col-lg-3 col-md-3 col-sm-4 col-xs-6';
            break;

        case 5:
        case 7:
        case 8:
            $container_column = 'col-lg-12 col-md-12 col-sm-12';
            $inner_columns    = 'col-lg-3 col-md-4 col-sm-6 col-xs-6';
            break;

        case 6:
            $container_column = 'col-md-8 col-md-offset-2';
            $inner_columns    = 'col-md-4';
            break;
        case 9:
        case 10:
        case 11:
        case 12:
        case 13:
        case 14:
        case 15:
            $container_column = 'col-md-12';

            /* $inner_columns     = 'col-md-5th-1'; */
            $inner_columns    = 'col-md-3 col-sm-4 col-xs-6';
            break;
        default:
            $inner_columns = 'col-md-2 col-sm-4 col-xs-6';
            break;
    }
} else {
    $container_column = 'col-md-12 col-sm-12 col-xs-12';
}?>

<div class="row home">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="row row-centered row">
            <div class="module-container <?php echo $container_column; ?>">
                <?php
                if (!empty($marketing_modules)) { ?>
                    <?php foreach ($marketing_modules as $k => $mod) { ?>
                        <div class="module-unit <?php echo $inner_columns; ?>">
                            <div class="row">
                                <a class="module_link" href="<?php echo base_url("webapp/marketing/" . ((!empty($mod->module_url_link)) ? $mod->module_url_link : '')); ?>" title="<?php echo (!empty($mod->module_name)) ? ucwords($mod->module_name) : '' ; ?>">
                                    <div class="no-border module-item bg-transparent col-centered">
                                        <img class="img-responsive module-image" src="<?php echo (!empty($mod->image_url)) ? base_url($mod->image_url) : '' ; ?>" alt="<?php echo (!empty($mod->description)) ? base_url($mod->description) : '' ; ?>" />
                                    </div>
                                </a>
                            </div>
                        </div>
                        <?php
                    } ?>
                    <?php
                } else { ?>
                    <div class="col-md-6 col-sm-offet-3 col-sm-8 col-sm-offet-2 col-xs-12">
                        <p>There are no modules set yet.</p>
                    </div>
                    <?php
                } ?>
            </div>
        </div>
    </div>
</div>