/*

  EVIPAGE 1.0, written by Jake Nelson
    Property of Evident Software
                                          */

function eviPagePanel(evipage_element, api_url, select_data, PAGENATION_ENABLED){

    var eviPageThis = this;

    this.select_data = select_data
    this.eviPageElement = evipage_element

    this.PAGENATION_ENABLED = PAGENATION_ENABLED

    this.elementID = $(evipage_element).attr("id")

    // temp patch, till fixed
    this.limit = 5

    this.updatePages = function (page = 0){

      pageIndex = parseInt(page)

      $.ajax({
          url: api_url,
          method: "POST",
          data: { limit : eviPageThis.limit},
          dataType: "json",
          success:function( result ){
              if( result.status == 1 ){
                  page_data = result[eviPageThis.select_data.elementKey]
                  page_counters = result[eviPageThis.select_data.counterKey]
                  $(eviPageThis.eviPageElement).children().remove()
                  addHeader(eviPageThis.eviPageElement, page_data, pageIndex * eviPageThis.limit, (pageIndex * eviPageThis.limit) + eviPageThis.limit)

                  if(eviPageThis.PAGENATION_ENABLED){
                    addNavigation(eviPageThis.eviPageElement, page_counters, pageIndex)
                  }
              }else{

              }
            }
        });
    }

    function addHeader(evipage_element, page_data, TEMPLOW, TEMPHIGH){
        html = ""
        $.each(page_data, function( index, elem ) {
            if((index > TEMPLOW - 1 && index < TEMPHIGH) || !eviPageThis.PAGENATION_ENABLED){

              element_text = elem[eviPageThis.select_data.elementFields.element_text_key]
              element_prefix = eviPageThis.select_data.elementDetails.prefixID
              id = elem[eviPageThis.select_data.elementFields.element_id_key]

              element_id = element_prefix + "-" + id

              html += "<div id='" + element_id + "' class='eviPageElement' data-id='" + id + "' data-toggled=false>"
              html += "<div class='workflow-header header-expand pointer'><i class='fas fa-caret-up' style='color: rgb(248, 201, 0);'></i> " + element_text + "</div>"
              html += "</div><div class='workflow-content'></div>"
            }
        });

        $(evipage_element).append(html)

    }

    function addNavigation(evipage_element, page_counters, TEMPPAGEINFO){

      html = "<table style='width:100%'><tr><td colspan='6' style='padding: 0;'>"
      html += "<span style='margin:15px 0px;' class='pull-left'>Page <strong>" + (TEMPPAGEINFO + 1) /*((eviPageThis.limit * page_counters.offset) + 1)*/ + "</strong> of <strong>" + page_counters.pages + "</strong>"
      html += "</span><ul class='pagination pull-right'>"

      btn_unique_class = "pgn-btn-" + eviPageThis.select_data.elementDetails.prefixID

      for (j = 0; j < page_counters.pages; j++) {
        if(j == TEMPPAGEINFO){
          html += "<li><a class='pgn-btn pgn-btn-active " + btn_unique_class + "' data-toPage=" + j + ">" + (j + 1) + "</a></li>"
        } else {
          html += "<li><a class='pgn-btn " + btn_unique_class + "' data-toPage=" + j + ">" + (j + 1) + "</a></li>"
        }

      }

      html += "</ul></td></tr></table></div>"

      $(evipage_element).append(html)
    }

    this.updatePages()

    $("#workflow-projects").on('click',".pgn-btn-" + eviPageThis.select_data.elementDetails.prefixID,function(){
        new_page_index = $(this).attr("data-toPage")
        eviPageThis.updatePages(new_page_index)
    });

}



function eviPageTable(evipage_element, api_url, select_data, tabledata, postdata){

    var eviPageThis = this;
    let ENTRIES_PER_PAGE = 15;

    this.select_data = select_data
    this.eviPageElement = evipage_element
    this.table_data = tabledata;
    this.postdata = postdata;

    this.PAGENATION_ENABLED = PAGENATION_ENABLED


    this.updatePages = function (page = 0, callback){
      $.ajax({
          url: api_url,
          method: "POST",
          data: postdata,
          dataType: "json",
          success:function( result ){
              if( result.status == true || result.status == 1 || result.status == 0){
                html = ""
                if(result.status == 0){
                    html += "<div class='workflow-body no-entries-message'>There is no data avaliable!</div>"
                } else {

                  if(result.hasOwnProperty(eviPageThis.select_data.elementKey)){
                      if(result[eviPageThis.select_data.elementKey] !== null){
                            counter_data = result[eviPageThis.select_data.counterKey]
                            entry_data = result[eviPageThis.select_data.elementKey]

                            // Add each of the header tags to the table
                            html += "<div class='workflow-body' data-offset='0'><table><tr>"

                            $.each(eviPageThis.table_data.table_headers, function(index, header_tag) {
                                html  +=  "<th>" + header_tag  + "</th>"
                            });

                            html += "</tr>"

                            pageNum = parseInt(page)

                            entry_pages_count = Math.ceil(counter_data.total / ENTRIES_PER_PAGE)

                            /* below this needs refactoring */

                            if(eviPageThis.PAGENATION_ENABLED){
                              for (i = pageNum * ENTRIES_PER_PAGE; i < (pageNum * ENTRIES_PER_PAGE) + ENTRIES_PER_PAGE; i++) {
                                 if(entry_data[i] != undefined){
                                      html += "<tr>"
                                      $.each(eviPageThis.table_data.table_value_keys, function(index, value_key) {

                                          html += "<td>" + entry_data[i][value_key] + "</td>"
                                      });
                                      html += "</tr>"
                                 }
                              }
                            } else {
                                $.each(entry_data, function(i) {
                                 if(entry_data[i] != undefined){
                                      html += "<tr>"

                                      $.each(eviPageThis.table_data.table_value_keys, function(index, value_key) {

                                        if(value_key == "entry_duration"){
                                          html += "<td>" + secondsToHmsString(entry_data[i][value_key]) + "</td>"
                                        } else {
                                          html += "<td>" + (entry_data[i][value_key] == null ? '-' : entry_data[i][value_key]) + "</td>"
                                        }


                                      });
                                      html += "</tr>"
                                 }
                              });
                            }

                            html  += "</table>"
                          } else {
                              html += "<div class='workflow-body no-entries-message'>There was an error while attempting to load entries for this project!</div>"
                          }

                          if(eviPageThis.PAGENATION_ENABLED){
                            html += eviPageThis.generateNav(eviPageThis.eviPageElement, page_counters, pageNum)
                          }

                      } else {
                        console.log("Debug: Null entries key!")
                      }

                }
                $(evipage_element).find(".workflow-body").remove()


                $(evipage_element).append(html)

              } else {
                  swal({
                      type: 'error',
                      title: "There was an error while fetching workflow data!",
                      showConfirmButton: false,
                      timer: 3000
                  })
              }
            }
        });
    }

    this.generateNav = function (evipage_element, page_counters, TEMPPAGEINFO){

      navhtml = "<table style='width:100%'><tr><td colspan='6' style='padding: 0;'>"
      navhtml += "<span style='margin:15px 0px;' class='pull-left'>Page <strong>" + (TEMPPAGEINFO + 1) /*((eviPageThis.limit * page_counters.offset) + 1)*/ + "</strong> of <strong>" + page_counters.pages + "</strong>"
      navhtml += "</span><ul class='pagination pull-right'>"

      btn_unique_class = "pgn-btn-" + eviPageThis.select_data.elementDetails.prefixID

      for (j = 0; j < page_counters.pages; j++) {
        if(j == TEMPPAGEINFO){
          navhtml += "<li><a class='pgn-btn pgn-btn-active " + btn_unique_class + "' data-toPage=" + j + ">" + (j + 1) + "</a></li>"
        } else {
          navhtml += "<li><a class='pgn-btn " + btn_unique_class + "' data-toPage=" + j + ">" + (j + 1) + "</a></li>"
        }

      }

      return navhtml;
    }

    this.updatePages()

}

function secondsToHmsString(d) {
    d = Number(d);

    var h = Math.floor(d / 3600);
    var m = Math.floor(d % 3600 / 60);

    var hDisplay = h > 0 ? h + (h == 1 ? " h " : " hrs ") : "";
    var mDisplay = m > 0 ? m + (m == 1 ? " m " : " mins ") : "";

    return hDisplay + mDisplay
}
