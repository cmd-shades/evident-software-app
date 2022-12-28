<!-- Full Width Column -->
<?php 
   $disciplinename = 'overview';
   $accountid = $account_id; 
   $sessiondate = (isset($_COOKIE['daterange']))?$_COOKIE['daterange']:'7';
   echo $sessiondate;
   ?>
<main><div class="content-wrapper dark-bg" style="min-height: 83vh;">
  <script>
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
	
	$('#testing').load('overview?start=' + startdate + '&end=' + enddate );
   $('#outcomes').load('overview?start=' + startdate + '&end=' + enddate );
   $( ".calendar" ).hide( "slow");
   }); 
   
}); 
   
</script>

    



  
   
   <div class="container">

   <div id="testing" class="wrapper" >
   
        </div>
        
</main>
<div id="pulldown" style="    position: absolute;
   bottom: 0%; z-index:99999999;
   width:100%;" class="hidden-md">
   <img height="40" src="<?php echo base_url('application/modules/webapp/views/dashboard/images/refresh.svg'); ?>" style="margin: 0 auto; display: block"/>
   <div id="popup" style="background-color:#4c4c4c; text-align: center; color: white; padding :30px; display:none">
      NEW DATA AVAILABLE! PULL DOWN TO REFRESH
   </div>
</div>

<script>

   
   $('#testing').load('<?php echo $base_url;?>/application/modules/webapp/views/_partials/overview.php?daterange=<?php echo $sessiondate;?>&account_id=<?php echo $accountid;?>');
   
   document.getElementById("7").onclick = function() {
      document.cookie = 'daterange=7;path=/;';

      $('#testing').load('<?php echo $base_url;?>/application/modules/webapp/views/_partials/overview.php?daterange=7&account_id=<?php echo $accountid;?>');
		$(".datefilter").removeClass('active');
		//$("#deskcustom").removeClass('active');
		
		//$(".calendar").hide();
     $('#7').addClass('active');
      
   }

   document.getElementById("30").onclick = function() {
      document.cookie = 'daterange=30;path=/;';
      $('#testing').load('<?php echo $base_url;?>/application/modules/webapp/views/_partials/overview.php?daterange=30&account_id=<?php echo $accountid;?>');
		$(".datefilter").removeClass('active');
		//$("#deskcustom").removeClass('active');
		
		//$(".calendar").hide();
     $('#30').addClass('active');
      

   }
  
   document.getElementById("90").onclick = function() {
      document.cookie = 'daterange=90;path=/;';
      $('#testing').load('<?php echo $base_url;?>/application/modules/webapp/views/_partials/overview.php?daterange=90&account_id=<?php echo $accountid;?>');
		$(".datefilter").removeClass('active');
		//$("#deskcustom").removeClass('active');
		
		//$(".calendar").hide();
     $('#90').addClass('active');
      

   }


   document.getElementById("180").onclick = function() {
      document.cookie = 'daterange=180;path=/;';
      $('#testing').load('<?php echo $base_url;?>/application/modules/webapp/views/_partials/overview.php?daterange=180&account_id=<?php echo $accountid;?>');
		$(".datefilter").removeClass('active');
		//$("#deskcustom").removeClass('active');
		
		//$(".calendar").hide();
     $('#180').addClass('active');
      

   }

   document.getElementById("365").onclick = function() {
      document.cookie = 'daterange=365;path=/;';
      $('#testing').load('<?php echo $base_url;?>/application/modules/webapp/views/_partials/overview.php?daterange=365&account_id=<?php echo $accountid;?>');
		$(".datefilter").removeClass('active');
		//$("#deskcustom").removeClass('active');
		
		//$(".calendar").hide();
     $('#365').addClass('active');
      

   }
 
  
  
 
    </script>