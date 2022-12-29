
<script src="<?php echo base_url("assets/js/custom/evipage.js"); ?>" type="text/javascript"></script>
<script src="<?php echo base_url("assets/js/custom/csv-utils.js"); ?>" type="text/javascript"></script>
<script src="<?php echo base_url("assets/js/custom/format_utils.js"); ?>" type="text/javascript"></script>



<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel tile has-shadow">
            <i id="download-workflow-entries" class="fas fa-download pointer"  style="display:inline-block;float:right;font-size:15px;margin-top:5px;margin-right:5px;"></i>
            <legend>Workflows and Entries</legend>
            <div id="workflow-projects"></div>
        </div>
    </div>
</div>
<script>

var eviPagePanel_;
var eviPageTable_;

const PAGENATION_ENABLED = <?php echo json_encode($pagination_enabled); ?>;


$( document ).ready(function() {
  workflow_projects = document.getElementById("workflow-projects")

  select_data = { elementKey : "project_workflows", counterKey : "page_counter", elementFields : { element_id_key : "workflow_id", element_text_key : "workflow_name"}, elementDetails : {prefixID : "workflow"} }
  eviPagePanel_ = new eviPagePanel(workflow_projects, "<?php echo base_url('webapp/project/get_project_workflows'); ?>", select_data, PAGENATION_ENABLED, {project_id : <?php echo $project_details->project_id ?>})

  $("#workflow-projects").on('click','.workflow-header',function(){

    workflow_element = $(this).closest(".eviPageElement")


    if(PAGENATION_ENABLED){
        // close the old divs as the new one opens
        $(".workflow-header").not($(this)).closest(".eviPageElement").attr("data-toggled", false)
        $(".workflow-body").not($(workflow_element).closest(".eviPageElement").find(".workflow-body")).remove()
        /*                          */
    }

    tab_is_toggled = $(workflow_element).attr("data-toggled")

    if(tab_is_toggled == "false"){
      $(workflow_element).closest(".eviPageElement").find(".workflow-body").remove()

      workflow_id = $(workflow_element).attr("data-id")
      postdata = { where:{ "workflow_id" : workflow_id } }

      $(workflow_element).find(".header-expand").children("i").switchClass("fa-caret-up", "fa-caret-down")

      select_data = { elementKey : "workflow_entries", counterKey : "page_counter", elementFields : {}, elementDetails : {prefixID : "entry"} }

      tabledata = {table_headers : ["Entry ID", "Entry Name", "Timestamp", "Duration", "User"], table_value_keys : ["entry_id", "entry_name", "entry_start_date", "entry_duration", "record_created_by"]}

      eviPageTable_ = new eviPageTable( workflow_element , "<?php echo base_url('webapp/project/get_workflow_entries'); ?>", select_data, tabledata, postdata, PAGENATION_ENABLED)

      $(workflow_element).attr("data-toggled", true)
    } else {
      $(workflow_element).find(".header-expand").children("i").switchClass("fa-caret-down", "fa-caret-up")
      $(workflow_element).closest(".eviPageElement").find(".workflow-body").remove()
      $(workflow_element).attr("data-toggled", false)
    }
  });


  $("#workflow-projects").on('click', ".pgn-btn-entry", function(){
      new_page_index = $(this).attr("data-toPage")
      eviPageTable_.updatePages(new_page_index)

  });
  
  
  $("#download-workflow-entries").on('click', function(event) {
      $.ajax({
          url:"<?php echo base_url('webapp/project/get_workflow_entries'); ?>",
          method:"POST",
          data:{ where : {project_id :<?php echo $project_details->project_id ?>}, limit: -1 },
          dataType: "json",
          success:function( result ){
              if( result.status == 1 ){
                  console.log(result)
                  workflow_entries = result.workflow_entries
                  
                  data = [[
                      "Project ID",
                      "Project Name",
                      "Workflow Name",
                      "Workflow ID",
                      "Workflow Ref",
                      "Entry ID",
                      "Entry Name",
                      "Entry Start Date",
                      "Entry Notes",
                      "Entry Duration",
                      "Record Modified By"
                  ]]
                  
                  $.each(workflow_entries, function(index, workflow_entry) {
                      thisRow = [
                          workflow_entry.project_id,
                          workflow_entry.project_name,
                          workflow_entry.workflow_name,
                          workflow_entry.workflow_id,
                          workflow_entry.workflow_ref,
                          workflow_entry.entry_id,
                          workflow_entry.entry_name,
                          workflow_entry.entry_start_date,
                          workflow_entry.entry_notes,
                          (isNaN(workflow_entry.entry_duration) ? '-' : secondsToHmsString( workflow_entry.entry_duration )),
                          workflow_entry.record_created_by
                      ]
                      data.push(thisRow)
                  });
                  
                  
                  exportToCsv("<?php echo $project_details->project_name ?> Workflow Entries.csv", data)
                 
                }
          }
      });
  })
  
  
  

});
</script>
