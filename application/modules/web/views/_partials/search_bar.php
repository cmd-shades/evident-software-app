<?php
$sub_parameter = $this->uri->segment(3, 0);

switch($module_identier) {
    case "people":
    case "asset":
    case "site":
    case "building":
    case "fleet":
    case "audit":
        if ($module_identier == 'site') {
            if (!empty($sub_parameter) && (strtolower($sub_parameter) == "premises_attributes")) {
                $rename_search_word = 'Attributes';
            } else {
                $rename_search_word = 'Buildings';
            }
        }
        ?>
		
		<div class="form-group top_search" style="margin-bottom:0px">
			<!-- Search bar -->
			<div class="input-group" style="width: 100%;">
				<i class="fas fa-search"></i><input type="text" class="form-control <?php echo $module_identier; ?>-search_input" id="search_term" value="" placeholder="Search <?php echo ($module_identier != "people") ? (!empty($rename_search_word) ? $rename_search_word : ucwords($module_identier)."s") : ucwords($module_identier) ; ?>">
			</div>
		</div>
	
<?php  	break;
    default:  ?>
		<div class="form-group top_search" style="margin-bottom:0px">
			<div>
				<!-- Search bar -->
				<div class="input-group">
					<input type="text" class="form-control <?php echo $module_identier; ?>-search_input" id="search_term" value="" placeholder="Search for...">
					<span class="input-group-btn">
						<button class="btn btn-default <?php echo $module_identier; ?>-bg search-go" type="button">Go!</button>
					</span>
				</div>
			</div>
		</div>
<?php } ?>