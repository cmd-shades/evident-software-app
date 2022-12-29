<?php
$accountid 		= $account_id; //Get Account ID
$disciplineid 	= getDisciplineID(); //Get the ID
$disciplinename = getDisciplineName($root_folder); //Get the discipline name
   //session_start();
$sessiondate = (isset($_COOKIE['daterange'])) ? $_COOKIE['daterange'] : '7';
$pages = getNumberofPages($disciplineid, $account_id, $api_end_point);
?>

<script>

	const rootFolder 	 = "<?php echo $base_url;?>"; 

  // Builds two pikaDay calendars and uses the startRange and endRange functions
// Pikaday is a Lightweight and dependency free datepicker
// https://github.com/Pikaday/Pikaday
this.buildDatePicker = (startInput, endInput) => {
   const container = document.getElementById('calendar-container');
   const minDate = new Date();
   minDate.setHours(0,0,0,0);
   
   this.startPicker = new Pikaday({
      bound: false,
      container: container,
      field: startInput,
      firstDay: 1,
      theme: 'calendar__start-wrapper',
	  enableSelectionDaysInNextAndPreviousMonths: true,
      onSelect: () => {
          this.updateStartDate( this.startPicker.getDate() );
      }
   });
   
   this.endPicker = new Pikaday({
      bound: false,
      container: container,
      field: endInput,
      firstDay: 1,
	  enableSelectionDaysInNextAndPreviousMonths: true,
      theme: 'calendar__end-wrapper',
      onSelect: () => {
         this.updateEndDate( this.endPicker.getDate() );
      }
   });
   
   this.endPicker.hide();
   this.bindMouseMove(endInput, container);
};

this.updateStartDate = (selectedstartDate) => {
   this.startPicker.hide();
   this.endPicker.setMinDate(selectedstartDate);
   this.endPicker.setStartRange(selectedstartDate);
   this.endPicker.gotoDate(selectedstartDate);
   this.setEndRange(selectedstartDate);
   console.log('set start date');
   console.log(selectedstartDate);
   this.endPicker.show();
};

this.updateEndDate = (selectedDate) => {
   this.endDate = new Date(selectedDate);
   console.log('set end date');
   console.log(selectedDate);
   this.setEndRange(selectedDate);
}

this.setEndRange = (endDate) => {
   this.endPicker.setEndRange(endDate);
   this.endPicker.draw();
}


this.bindMouseMove = (endInput, container) => {
   this.target = false;
   
   document.querySelector('body').addEventListener( 'mousemove', ( btn ) => {
      if ( !btn.target.classList.contains( 'pika-button' )) {
         if (this.target === true) {
            this.target = false;
            this.setEndRange(this.endPicker.getDate());
         }
      }
      
      else {
         this.target = true;
         const pikaBtn = btn.target;
		   const pikaDate = new Date( pikaBtn.getAttribute( 'data-pika-year' ), pikaBtn.getAttribute( 'data-pika-month' ), pikaBtn.getAttribute( 'data-pika-day' ) );
	   	this.setEndRange( pikaDate );   
      }
   }); 
}

const start = document.getElementById('calendar-start');
const end = document.getElementById('calendar-end');

this.buildDatePicker(start, end);
$(document).ready(function(){ 
	
$("#calendar-clear").click(function(){ var startElementWrapper = document.querySelector(".is-startrange");
	var startElementButton = startElementWrapper.querySelector("button");
	var start_data_pika_year = startElementButton.getAttribute('data-pika-year');
	var start_data_pika_month = startElementButton.getAttribute('data-pika-month');
	var start_data_pika_day = startElementButton.getAttribute('data-pika-day');
	var endElementWrapper = document.querySelector(".is-endrange");
	var endElementButton = endElementWrapper.querySelector("button");
	var end_data_pika_year = endElementButton.getAttribute('data-pika-year');
	var end_data_pika_month = endElementButton.getAttribute('data-pika-month');
	var end_data_pika_day = endElementButton.getAttribute('data-pika-day');

	var startdate = start_data_pika_year + '-' + start_data_pika_month + '-' + start_data_pika_day;
	var enddate = end_data_pika_year + '-' + end_data_pika_month + '-' + end_data_pika_day;
	
	$('#banner').load(rootFolder+'/application/modules/webapp/views/_partials/discipline-header.php?start=' + startdate + '&end=' + enddate + '&id=<?php echo $disciplineid;?>&name=<?php echo $disciplinename;?>&account_id=<?php echo $accountid;?>');
   $('#outcomes').load(rootFolder+'/application/modules/webapp/views/_partials/discipline-outcomes.php?start=' + startdate + '&end=' + enddate + '&id=<?php echo $disciplineid;?>&name=<?php echo $disciplinename;?>&account_id=<?php echo $accountid;?>');
   $( ".calendar" ).hide( "slow");
   }); 
   
}); 
   
</script>
  
<div class="content-wrapper">

   <!-- Load banner START -->

   <div id="banner" style="z-index: 0;"></div>
   <!-- Load banner END -->
   
   <section class="dis-content">
      <div class="container nopad-xs">
        <div class="row summary-headings hidden-sm">
          <div class="col-xs-12 col-md-4">
            <h5>TOTAL
            </h5>
          </div>
          <div class="col-md-1 ">
            <h5>CONTACTS
            </h5>
          </div>
          <div class="col-xs-12 col-md-7 fright-filter hidden-xs">
            <!-- REFRESH start -->
            <div class="refresh hidden-xs">
              <p>
                <a href="#" onClick="window.location.reload();" class="btn">REFRESH
                </a>
                <br>
                <small>LAST UPDATED: <span id="header-last-modified-fire" class="header-last-modified" ><?php date('h:i:s A'); ?></span></small>
              </p>
            </div>
            <!-- REFRESH end -->
            <!-- Date filter START -->
            <div class="filter">
               <!-- 7 day filter START -->
               <a id="7" href="#" class="btn <?php if($_COOKIE['daterange'] === '7') {
                   echo 'active' ;
               }?>  datefilter" >7 days</a>
               <!-- 7 day filter END -->
               <!-- 30 day filter START -->
               <a id="30" href="#" class="btn <?php if($_COOKIE['daterange'] === '30') {
                   echo 'active' ;
               }?> datefilter" >30 days</a>
               <!-- 30 day filter END -->
               <!-- 90 day filter START -->
               <a id="90" href="#" class="btn <?php if($_COOKIE['daterange'] === '90') {
                   echo 'active' ;
               }?> datefilter" >90 days</a>
               <!-- 90 day filter END -->
               <!-- 180 day filter START -->
               <a id="180" href="#" class="btn <?php if($_COOKIE['daterange'] === '180') {
                   echo 'active' ;
               }?> datefilter" >180 days</a>
               <!-- 180 day filter END -->
               <!-- 365 day filter START -->
               <a id="365" href="#" class="btn <?php if($_COOKIE['daterange'] === '365') {
                   echo 'active' ;
               }?> datefilter" >Year</a>
               <!-- 365 day filter END -->
               <!-- Custom date filter -->
               <!-- <a id="custom" href="#" class="btn">Custom</a> -->
               <!-- Custom date filter END -->

               <!-- Calendar START -->
               <section class="calendar" style="display: none;">
                  <div class="calendar__inputs">
                     <input class="calendar__input" readonly="readonly" type="text" id="calendar-start" placeholder="Start Date">
                     <input class="calendar__input" readonly="readonly" type="text" id="calendar-end" placeholder="End Date">
                  </div>
                  <div class="calendar__pikaday" id="calendar-container"></div>
                  <button class="calendar__reset" id="calendar-clear">Submit</button>
               </section>
               <!-- Calendar END -->
            </div>
            <!-- Date filter END -->
            </div>
        </div>
        
        <!-- Load outcomes START -->
        <div id="loading-outcomes" style="display: block; text-align: center; position: absolute; left: 45%;z-index:-1">
         <img id="loading-image" src="<?php echo $base_url;?>application/modules/webapp/views/dashboard/images/loading.gif" alt="Loading..." height="100"/>
         </div>
         <div id="outcomes" style="z-index: 1;"></div>
         <!-- Load outcomes END -->

         <!-- Pagination START -->
         <div class="pagination">
         <?php $counter = 1;?>
            <a href="#outcomes" id="previous" class="previous" onclick="previouspage()" style="display:none;">Previous</a>
            <a href="#outcomes" id="next" class="next" onclick="nextpage()" style="display:none;">Next</a>
         </div>
         <!-- Pagination END -->
</main>

<script>

	var counter = <?php echo $counter?>;
	var pages = <?php echo $pages?>;
   
      $('#banner').load(rootFolder+'/application/modules/webapp/views/_partials/discipline-header.php?daterange=<?php echo $sessiondate;?>&id=<?php echo $disciplineid;?>&name=<?php echo $disciplinename;?>&account_id=<?php echo $accountid;?>&limit=15&offset=0');
   $('#outcomes').load(rootFolder+'/application/modules/webapp/views/_partials/discipline-outcomes.php?daterange=<?php echo $sessiondate;?>&id=<?php echo $disciplineid;?>&name=<?php echo $disciplinename;?>&account_id=<?php echo $accountid;?>&limit=15&offset=0');
   $("#next").delay(3000).show(0);
	

	function showPreviousButton() {
		var btn = $("#previous");

		if (!btn.is(':visible')) {
			btn.show();
		}
	}

	function hidePreviousButton() {
		var btn = $("#previous");

		if (btn.is(':visible')) {
			btn.hide();
		}
	}

	function showNextButton() {
		var btn = $("#next");

		if (!btn.is(':visible')) {
			btn.show();
		}
	}

	function hideNextButton() {
		var btn = $("#next");

		if (btn.is(':visible')) {
			btn.hide();
		}
	}

	function togglePreviousButton() {
		var btn = $("#previous");

		if (btn.is(':visible')) {
			btn.hide();
		} else {
			btn.show();
		}
	}

	function toggleNextButton() {
		var btn = $("#next");

		if (btn.is(':visible')) {
			btn.hide();
		} else {
			btn.show();
		}
	}

	function scrollElementToView(query) {
		if (elm = document.querySelector(query)) {
			elm.scrollIntoView();
		}
	}

   function nextpage() {
      <?php $counter++;?>
      <?php $offset = 15*$counter-15;?>
      $('#outcomes').load(rootFolder+'/application/modules/webapp/views/_partials/discipline-outcomes.php?daterange=<?php echo $_COOKIE['daterange'];?>&id=<?php echo $disciplineid;?>&name=<?php echo $disciplinename;?>&account_id=<?php echo $accountid;?>&limit=15&offset=<?php echo $offset;?>');
	  
      <?php if($counter !== 1):?>
	   	showPreviousButton();
	   <?php endif; ?>

	   <?php if($counter < $pages):?>
	   toggleNextButton();
	   <?php endif; ?>

	   <?php if($counter === $pages):?>
	   hideNextButton();
	   <?php endif; ?>
   }

   function previouspage() {
      <?php $counter--;?>
      <?php $offset = 15*$counter-15;?>
      $('#outcomes').load(rootFolder+'/application/modules/webapp/views/_partials/discipline-outcomes.php?daterange=<?php echo $_COOKIE['daterange'];?>&id=<?php echo $disciplineid;?>&name=<?php echo $disciplinename;?>&account_id=<?php echo $accountid;?>&limit=15&offset=<?php echo $offset;?>');

	  if(<?php echo $counter?> === 1) {
		hidePreviousButton();
	  }

	  if(<?php echo $pages?> > 1){
		  showNextButton();
	  }

	   if(counter < pages){
		   showNextButton();
	   }

	   if(<?php echo $counter?> > 1) {
		 showPreviousButton();
	   }

	   // scrollElementToView('#loading-outcomes');
   }

   document.getElementById("7").onclick = function() {
      document.cookie = 'daterange=7;path=/;';
      <?php $counter = 1;?>
      $('#banner').load(rootFolder+'/application/modules/webapp/views/_partials/discipline-header.php?daterange=7&id=<?php echo $disciplineid;?>&name=<?php echo $disciplinename;?>&account_id=<?php echo $accountid;?>');
		$('#outcomes').load(rootFolder+'/application/modules/webapp/views/_partials/discipline-outcomes.php?daterange=7&id=<?php echo $disciplineid;?>&name=<?php echo $disciplinename;?>&account_id=<?php echo $accountid;?>&limit=15&offset=0');
		$(".datefilter").removeClass('active');
		//$("#deskcustom").removeClass('active');
		
		//$(".calendar").hide();
     
     $("#next").show();
	 $("#previous").hide();
	 element.addClass('active');
   }

   document.getElementById("30").onclick = function() {
      document.cookie = 'daterange=30;path=/;';
      <?php $counter = 1;?>
      $('#banner').load(rootFolder+'/application/modules/webapp/views/_partials/discipline-header.php?daterange=30&id=<?php echo $disciplineid;?>&name=<?php echo $disciplinename;?>&account_id=<?php echo $accountid;?>');
		$('#outcomes').load(rootFolder+'/application/modules/webapp/views/_partials/discipline-outcomes.php?daterange=30&id=<?php echo $disciplineid;?>&name=<?php echo $disciplinename;?>&account_id=<?php echo $accountid;?>&limit=15&offset=0');
		$(".datefilter").removeClass('active');

     
     $("#next").show();
	 $("#previous").hide();
	 element.addClass('active');
   }

   document.getElementById("90").onclick = function() {
      document.cookie = 'daterange=90;path=/;';
      <?php $counter = 1;?>
      $('#banner').load(rootFolder+'/application/modules/webapp/views/_partials/discipline-header.php?daterange=90&id=<?php echo $disciplineid;?>&name=<?php echo $disciplinename;?>&account_id=<?php echo $accountid;?>');
		$('#outcomes').load(rootFolder+'/application/modules/webapp/views/_partials/discipline-outcomes.php?daterange=90&id=<?php echo $disciplineid;?>&name=<?php echo $disciplinename;?>&account_id=<?php echo $accountid;?>&limit=15&offset=0');
		$(".datefilter").removeClass('active');
		//$("#deskcustom").removeClass('active');
		
		//$(".calendar").hide();
     $('#90').addClass('active');
     $("#next").show();
	 $("#previous").hide();

   }

   document.getElementById("180").onclick = function() {
      document.cookie = 'daterange=180;path=/;';
      <?php $counter = 1;?>
      $('#banner').load(rootFolder+'/application/modules/webapp/views/_partials/discipline-header.php?daterange=180&id=<?php echo $disciplineid;?>&name=<?php echo $disciplinename;?>&account_id=<?php echo $accountid;?>');
		$('#outcomes').load(rootFolder+'/application/modules/webapp/views/_partials/discipline-outcomes.php?daterange=180&id=<?php echo $disciplineid;?>&name=<?php echo $disciplinename;?>&account_id=<?php echo $accountid;?>&limit=15&offset=0');
		$(".datefilter").removeClass('active');
		//$("#deskcustom").removeClass('active');
		
		//$(".calendar").hide();
	   $('#180').addClass('active');
	   $("#next").show();
	   $("#previous").hide();

   }

   document.getElementById("365").onclick = function() {
      document.cookie = 'daterange=365;path=/;';
      <?php $counter = 1;?>
      $('#banner').load(rootFolder+'/application/modules/webapp/views/_partials/discipline-header.php?daterange=365&id=<?php echo $disciplineid;?>&name=<?php echo $disciplinename;?>&account_id=<?php echo $accountid;?>');
		$('#outcomes').load(rootFolder+'/application/modules/webapp/views/_partials/discipline-outcomes.php?daterange=365&id=<?php echo $disciplineid;?>&name=<?php echo $disciplinename;?>&account_id=<?php echo $accountid;?>&limit=15&offset=0');
		$(".datefilter").removeClass('active');
		//$("#deskcustom").removeClass('active');
		
		//$(".calendar").hide();
     $('#365').addClass('active');
     $("#next").show();
	 $("#previous").hide();
   }
 
 
   $("#custom").click(function(){
      $(".calendar").toggle();
      $(".datefilter").removeClass('active');
      $("#custom").addClass('active');
   });

   $("#deskcustom").click(function(){
      $(".calendar").toggle();
      $(".datefilter").removeClass('active');
      $("#deskcustom").addClass('active');
    });
</script>
