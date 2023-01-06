<!DOCTYPE html>


<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/select2.min.css') ?>" media="screen">
<script src="<?php echo base_url('assets/js/custom/jobsort-events.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/custom/jobsort-ui.js'); ?>" type="text/javascript"></script>

<?php

$avaliable_jobs = array(
	[   
		"job_id"=>"1",
		"job_slots"=>"2",
		"job_name"=>"Find Nemo",
		"job_description"=>"Find nemo he has been missing for ages",
		"job_location"=>"Apartment 1, Oyster Wharf, 18 Lombard Road, London, Greater London, SW11 3RJ"
	],
	[
		"job_id"=>"2",
		"job_slots"=>"2",
		"job_name"=>"Move car",
		"job_description"=>"Find nemo he has been missing for ages",
		"job_location"=>"Flat 3, Styles Court, 48 Walton Road, East Molesey, Surrey, KT8 0DQ"
	],
	[
		"job_id"=>"3",
		"job_slots"=>"3",
		"job_name"=>"Long Job Title",
		"job_description"=>"Find nemo he has been missing for ages",
		"job_location"=>"52 Newcome Road, Shenley, Radlett, Hertfordshire, WD7 9EJ"
	],
	[
		"job_id"=>"4",
		"job_slots"=>"2",
		"job_name"=>"Remove flourine from water",
		"job_description"=>"Find nemo he has been missing for ages",
		"job_location"=>"Flat 29, St. Johns Court, St. Johns Road, Bathwick, Bath, Somerset, BA2 6P"
	],
	[
		"job_id"=>"5",
		"job_slots"=>"2",
		"job_name"=>"Kick donald trump out of the UK",
		"job_description"=>"Find nemo he has been missing for ages",
		"job_location"=>"Flat 6, Styles Court, 48 Walton Road, East Molesey, Surrey, KT8 0DQ"
	],
	[
		"job_id"=>"6",
		"job_slots"=>"2",
		"job_name"=>"Find infinity stone",
		"job_description"=>"Find nemo he has been missing for ages",
		"job_location"=>"42B Walton Road, East Molesey, Surrey, KT8 0DQ"
	],
	[
		"job_id"=>"7",
		"job_slots"=>"2",
		"job_name"=>"Replace fire alarm",
		"job_description"=>"Find nemo he has been missing for ages",
		"job_location"=>"Unit 4, The Arches Business Centre, Merrick Road, Southall, Greater London, UB2 4AU"
	],
	[
		"job_id"=>"8",
		"job_slots"=>"2",
		"job_name"=>"Think of job to demonstrate the functionality of the draggable jobs section",
		"job_description"=>"Find nemo he has been missing for ages",
		"job_location"=>"Flat 5, Elysium House, 38 Western Road, Sutton, Surrey, SM1 2DT"
	]
);



$avaliable_staff = array(
	[
		"user_id"=>"1",
		"user_fullname"=>"Jake Nelson",
		"user_address"=>"1 Kingsway Croydon CR0 4GE",
		"user_slots"=>"10"
	],[
		"user_id"=>"2",
		"user_fullname"=>"Enock",
		"user_address"=>"62 High Street HEREFORD HR20 9UQ",
		"user_slots"=>"10"
	],[
		"user_id"=>"3",
		"user_fullname"=>"HAL 9000",
		"user_address"=>"311 London Road CHELMSFORD CM32 7IR",
		"user_slots"=>"10"
	]
);


?>


<html>
   <head>
      <link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
      <script src="//code.jquery.com/jquery-1.10.2.js"></script>
      <script src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
      
	  
	  <!-- My assets -->
      <link href="assets/css/stylesheet.css" rel="stylesheet" type="text/css" />
      <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
	  
	  <!-- Load Button functions -->
	  <script src="assets/js/jobsort-ui.js" type="text/javascript"></script>
	  <script src="assets/js/jobsort-events.js" type="text/javascript"></script>
	  
      <!-- Font Awesome -->
      <link href="assets/fontawesome/css/all.css" rel="stylesheet">
	  
	  <!-- Super classy Open Sans font -->
	  <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
   </head>
   <body>

      <div id="content-container" class="container-fluid">
	  
	 
         <div class="row" style="height: 100%;">
		 <div class="row" id="title"></div>
		 
		 <div class="container-fluid">
		 <div class="row">
			<div class="col-md-4 col-title" style="background-color: rgba(0, 64, 60, 0.9)">Jobs</div>
			<div class="col-md-4 col-title" style="background-color: rgba(0, 55, 62, 0.9)">Users</div>
			<div class="col-md-4 col-title" style="background-color: rgba(0, 55, 62, 0.9)">Barry's Suprise</div>
		 </div>
		 </div>
		
		 
            <div class="col-md-6" id="from-drop">
				
               <br>
			   
			   <div id="avaliable-jobs">
			   
				   <div class="dragto-sortable" id="avaliable-jobs-drop">
				   
					  <?php
						
						foreach($avaliable_jobs as $avaliable_job){
							
							$this->load->view('dragdrop_job', $avaliable_job);
						}
					  
					  ?>
					  
					  
				   </div>
			   </div>
            </div>
            <div class="col-md-6" id="to-drop">
			
			<br>
		
			<div id="searchbar-container" class="container">
			
			<div class="row">
			
			 <div class="col-md-11">
				<input type="text" id="searchbar">
			 </div>
			 
			  <div class="col-md-1">
				<i class="fas fa-times" id="remove-search-terms"></i>
			   </div>
			</div>
			</div>
			<hr/>
			<div id="avaliable-staff">
			
			<?php
				
				foreach($avaliable_staff as $user_data){
					$this->load->view('dragdrop_user', $user_data);
				}
			
			?>
			
			   </div>
			   
            </div>
         </div>
      </div>
   </body>
</html>