<?php 
	
	//Get the functions
	$building 		= getBuildingFeed( $account_id, $api_end_point); //The building information
	//print_r($buildings);
	$site_name 		= 'Blake Court'; //TO BE MADE DYNAMIC
	$count 			= 1; //Restrict to 1 entry, *to be removed*
	$accountid 		= $account_id; //Get Account ID
	$sessiondate 	= (isset($_COOKIE['daterange']))?$_COOKIE['daterange']:'7';

	if( empty( $building ) ){
		header( "Location: ".$base_url."/webapp/dashboard/index");
		die();
	}

?> 

<div class="content-wrapper">
  <section class="banner specialist-bg pt-6">
    <div class="container">
      <div class="row">
        <!-- BUILDING TITLE start -->
        <h1 class="building">
          <?php echo $building->building_summary_info->site_name;?>
        </h1>
        <!-- BUILDING TITLE end -->
        <div class="col-xs-12 col-md-8 mb-4">
          <div class="row">
            <!-- SUMMARY start -->
            <h4>Summary
            </h4>
            <div class="col-xs-12 col-md-6 border-r">
              <div class="row">
                <div class="col-xs-6">
                  <p>
                    <strong>FRA</strong>
                  </p>
                  <p>
                    <!-- <strong>Audit Status</strong> -->
                    <strong>Building Type</strong>
                  </p>
                  <p>
                    <strong>Floors</strong>
                  </p>
                  <p>
                    <strong>Dwellings</strong>
                  </p>
                  <p>
                    <strong>Max residents</strong>
                  </p>
                  <p>
                    <strong>Year built</strong>
                  </p>
                </div>
                <div class="col-xs-6">
                  <p>
                    <?php echo $building->building_summary_info->site_fra;?>
                  </p>
                  <p>
                    <?php echo $building->building_summary_info->building_type;?>
                  </p>
                  <p>
                    <?php echo $building->building_summary_info->number_of_floors;?>
                  </p>
                  <p>
                    <?php echo $building->building_summary_info->total_dwellings;?>
                  </p>
                  <p>
                    <?php echo $building->building_summary_info->max_residents;?>
                  </p>
                  <p>
                    <?php echo $building->building_summary_info->build_year;?>
                  </p>
                </div>
              </div>
            </div>
            <div class="col-xs-12 col-md-6 border-r pl-3">
              <div class="row">
                <div class="col-xs-6">
                  <p>
                    <strong>Tenure 
                    </strong>
                  </p>
                  <p>
                    <strong>Sq. m
                    </strong>
                  </p>
                  <p>
                    <strong>Frame
                    </strong>
                  </p>
                  <p>
                    <strong>Roof
                    </strong>
                  </p>
                  <p>
                    <strong>Combust
                    </strong>
                  </p>
                  <p>
                    <strong>Clad
                    </strong>
                  </p>
                </div>
                <div class="col-xs-6">
                  <p>
                    <?php echo $building->building_summary_info->tenure;?>
                  </p>
                  <p>
                    <?php echo $building->building_summary_info->square_meters;?>
                  </p>
                  <p>
                    <?php echo $building->building_summary_info->frame;?>
                  </p>
                  <p>
                    <?php echo $building->building_summary_info->roof;?>
                  </p>
                  <p>
                    <?php echo $building->building_summary_info->combustibility;?>
                  </p>
                  <p>
                    <?php echo $building->building_summary_info->cladding;?>
                  </p>
                </div>
              </div>
            </div>
            <!-- SUMMARY end -->
          </div>
        </div>
        <div class="col-xs-12 col-md-4 pl-3 mb-4">
          <div class="row">
            <!-- SAFETY CONTACT start -->
            <h4>Safety Manager</h4>
            <!-- PRIMARY SAFETY CONTACT start -->
            <div class="contacts">
              <?php if( !empty( $building->contact_info->primary_contact ) ){ ?> 
					<!-- <img src="<?php //echo $base_url;?>/application/modules/webapp/views/dashboard/images/person.svg"/> -->
					<img src="<?php echo $base_url;?>/assets/images/avatars/user.png"/>
					<p>Name: 
					  <?php echo $building->contact_info->primary_contact->first_name;?> 
					  <?php echo $building->contact_info->primary_contact->last_name;?> 
					  <br> Phone: 
					  <?php echo $building->contact_info->primary_contact->telephone;?> 
					  <br> Email: 
					  <?php echo $building->contact_info->primary_contact->email;?> 
					</p>
				<?php } else { ?>
					<img width="15px" src="<?php echo $base_url;?>/assets/images/avatars/user.png"/>
					<p>Primary Contact not set</p>
				<?php } ?>
            </div>
            <!-- PRIMARY SAFETY CONTACT end -->
			
            <!-- SECONDARY SAFETY CONTACT start -->
            <div class="contacts">
              <?php if( !empty( $building->contact_info->secondary_contact ) ){ ?> 
					<!-- <img src="<?php //echo $base_url;?>/application/modules/webapp/views/dashboard/images/person.svg"/> -->
					<img src="<?php echo $base_url;?>/assets/images/avatars/user.png"/>
					<p>Name: 
					  <?php echo $building->contact_info->secondary_contact->first_name;?> 
					  <?php echo $building->contact_info->secondary_contact->last_name;?> 
					  <br> Phone: 
					  <?php echo $building->contact_info->secondary_contact->telephone;?> 
					  <br> Email: 
					  <?php echo $building->contact_info->secondary_contact->email;?> 
					</p>
				<?php } else { ?>
					<img width="15px" src="<?php echo $base_url;?>/assets/images/avatars/user.png"/>
					<p>Secondary Contact not set</p>
				<?php } ?>
            </div>
            <!-- SECONDARY CONTACT end -->
          </div>
        </div>
        <div class="row">
          <div id="outcomes">Loading Data... please wait.</div>
      </div>
    </section>
	  
    <section class="overduer">
		<div class="container">
            <div id="overdue"></div>
		</div>
    </section>
	
    <section class="banner specialist-bg">
      <div class="container">
        <div class="row">
          <div id="recommendations"></div>
          <div class="col-xs-12 col-md-offset-1 col-md-6">
            <select class="select-recommend">
              <option>
                <strong>FIRE</strong> - Fire door inspections
              </option>
            </select>
            <div class="row">
              <div class="col-xs-12 col-md-6 content">
                <div class="chartoverview chartrecommend">
                  <!-- CHART start -->
                  <svg class="circle-chart" viewbox="0 0 33.83098862 33.83098862" xmlns="http://www.w3.org/2000/svg">
                    <circle class="circle-chart__circle" stroke="#FF6666" stroke-width="1" stroke-dasharray="100,100" fill="none" cx="16.91549431" cy="16.91549431" r="15.91549431" />
                    <circle class="circle-chart__circle" stroke="#FF9933" stroke-width="1" stroke-dasharray="<?php //echo $recom;?>,100" fill="none" cx="16.91549431" cy="16.91549431" r="15.91549431" />
                    <circle class="circle-chart__circle" stroke="#66CC66" stroke-width="1" stroke-dasharray="<?php //echo $passed;?>,100" fill="none" cx="16.91549431" cy="16.91549431" r="15.91549431" />
                  </svg>
                  <!-- CHART end -->
                  <div class="recommend-text">
                    <h3>76<span>%</span></h3>
                    <h4>PASSED</h4>
                  </div>
                </div>
              </div>
              <div class="col-xs-12 col-md-6 charts-rec">
                <div class="row">
                  <div class="col-xs-12 col-md-9">
                    <div class="row">
                      <div class="circle-chart__info col-xs-4 col-md-12 mb-5">
                        <div class="circle rec pass">
                          <p class="count recommends" >
                            <?php //echo $discipline->outcomes_info->passed_inspections;?>
                          </p>
                        </div>
                        <h4>Passed
                        </h4>
                      </div>
                      <div class="circle-chart__info col-xs-4 col-md-12 mb-5">
                        <div class="circle rec recommendations">
                          <p class="count recommends" >
                            <?php //echo $discipline->outcomes_info->recommendations;?>
                          </p>
                        </div>
                        <h4>Recommendations
                        </h4>
                      </div>
                      <div class="circle-chart__info col-xs-4 col-md-12 mb-5">
                        <div class="circle rec failed">
                          <p class="count recommends" >
                            <?php //echo $discipline->outcomes_info->failed_inspections;?>
                          </p>
                        </div>
                        <h4>Failed
                        </h4>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </section>
          <!-- ODOCUMENT MANAGEMENT start -->
        <section class="document-management">
          <div class="container">
            <h4>Document management
            </h4>
            <div class="row">
				<ul>
					<li class="tick"><a href="<?php echo $base_url;?>webapp/site/profile/<?php echo $building->building_summary_info->site_id;?>/documents?folder=FEEP">FEEP</a></li>
					<li class="tick"><a href="<?php echo $base_url;?>webapp/site/profile/<?php echo $building->building_summary_info->site_id;?>/documents?folder=BIM">BIM</a></li>
					<li class="tick"><a href="<?php echo $base_url;?>webapp/site/profile/<?php echo $building->building_summary_info->site_id;?>/documents?folder=Building Regulations">Building Regulations</a></li>
					<li class="tick"><a href="<?php echo $base_url;?>webapp/site/profile/<?php echo $building->building_summary_info->site_id;?>/documents?folder=C and Ms">C&amp;Ms</a></li>
					<li class="tick"><a href="<?php echo $base_url;?>webapp/site/profile/<?php echo $building->building_summary_info->site_id;?>/documents?folder=Resident Engagement">Resident Engagement</a></li>
					<li class="cross">Complaints</li>
					<li class="tick"><a href="<?php echo $base_url;?>webapp/site/profile/<?php echo $building->building_summary_info->site_id;?>/documents?folder=FRAS">FRAs</a></li>
					<li class="cross">Floor plan</li>
					<li class="tick"><a href="<?php echo $base_url;?>webapp/site/profile/<?php echo $building->building_summary_info->site_id;?>/documents?folder=Safety Assurance">Safety Assurance</a></li>
					<li class="tick"><a href="<?php echo $base_url;?>webapp/site/profile/<?php echo $building->building_summary_info->site_id;?>/documents?folder=Others">Other</li>
				</ul>
            </div>
          </div>
        </section>
        <!-- DOCUMENT MANAGEMENT end -->
        <!-- STRUCTURAL DETAILS start -->
        <section class="structural-details">
          <div class="container">
            <h4>Structural Details
            </h4>
            <div class="row">
              <ul>
                <li>
                  <strong>Frame:
                  </strong> 
                  <?php echo $building->building_summary_info->frame;?>
                </li>
                <li>
                  <strong>Roof:
                  </strong> 
                  <?php echo $building->building_summary_info->roof;?>
                </li>
                <li>
                  <strong>Combustibility:
                  </strong> 
                  <?php echo $building->building_summary_info->combustibility;?>
                </li>
              </ul>
            </div>
          </div>
        </section>
        <!-- STRUCTURAL DETAILS end -->
      </div> 

<script>

	const rootFolder 	 = "<?php echo $base_url;?>"; 

   $('#outcomes').load(rootFolder+'/application/modules/webapp/views/_partials/building-outcomes.php?daterange=<?php echo $sessiondate;?>&siteid=<?php echo $_GET['siteid'];?>&account_id=<?php echo $accountid;?>');
   $('#overdue').load(rootFolder+'/application/modules/webapp/views/_partials/building-overdue.php?daterange=<?php echo $sessiondate;?>&siteid=<?php echo $_GET['siteid'];?>&account_id=<?php echo $accountid;?>');
   $('#recommendations').load(rootFolder+'/application/modules/webapp/views/_partials/building-recommendations.php?daterange=<?php echo $sessiondate;?>&siteid=<?php echo $_GET['siteid'];?>&account_id=<?php echo $accountid;?>');

   function deskrecp(daterange) {
		$('#outcomes').load(rootFolder+'/application/modules/webapp/views/_partials/building-outcomes.php?daterange=' + daterange + '&siteid=<?php echo $_GET['siteid'];?>&account_id=<?php echo $accountid;?>');
		$('#overdue').load(rootFolder+'/application/modules/webapp/views/_partials/building-overdue.php?daterange=' + daterange + '&siteid=<?php echo $_GET['siteid'];?>&account_id=<?php echo $accountid;?>');
    $('#recommendations').load(rootFolder+'/application/modules/webapp/views/_partials/building-recommendations.php?daterange=' + daterange + '&siteid=<?php echo $_GET['siteid'];?>&account_id=<?php echo $accountid;?>');

		$(".datefilter").removeClass('active');
		$("#deskcustom").removeClass('active');
		$('#' + daterange).addClass('active');
		$(".calendar").hide();
      document.cookie = 'daterange='+ daterange + ';path=/;';
   }
 
   function mobrecp(daterange) {
     $('#banner').load(rootFolder+'/application/modules/webapp/views/_partials/building-outcomes.php?daterange=' + daterange + '&siteid=<?php echo $_GET['siteid'];?>&account_id=<?php echo $accountid;?>');
     $('#overdue').load(rootFolder+'/application/modules/webapp/views/_partials/building-overdue.php?daterange=' + daterange + '&siteid=<?php echo $_GET['siteid'];?>&account_id=<?php echo $accountid;?>');
     $('#recommendations').load(rootFolder+'/application/modules/webapp/views/_partials/building-recommendations.php?daterange=' + daterange + '&siteid=<?php echo $_GET['siteid'];?>&account_id=<?php echo $accountid;?>');

     $(".datefilter").removeClass('active');
     $("#custom").removeClass('active');
     $('#' + daterange).addClass('active');
     $(".calendar").hide();
     document.cookie = 'daterange=' + daterange + ';path=/;';
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