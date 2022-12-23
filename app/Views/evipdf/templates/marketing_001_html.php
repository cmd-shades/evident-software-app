<?php
if(!empty($pdf_data) ) {

    $i         = 0;
    $html     = "";
    
    foreach( $pdf_data as $movie_id => $movie ){
        if(( $i % 9 == 0 ) ) {
            $html     = "";
            if($i > 0 ) {
                $mpdf->AddPage();
            }
            
            $html .= 
            '<div class="main_container">
					<header>
						<div class="header_left_col">
							<span class="current_titles">Current Titles</span>
						</div>
						<div class="header_right_col">
							<div class="logo">'.( ( !empty($pdf_type) && ( strtolower($pdf_type) == "airtime" ) ) ? '<img class="header_airtime_logo" src="'.( base_url('assets\images\logos\airtime_logo.jpg') ).'" />' : '&nbsp;' ).'</div>
						</div>
					</header>
					<div class="body_container" style="height: 850px;">';
        }
            
        $html .= 
        '<div class="item" style="padding: 10px;">
			<div style="width: 212px; height: 40px; float: left; display: block;">
				<p style="color: #ed1b24; font-size:13px; text-transform: uppercase; margin:0; padding:0;">'.( ( !empty($movie->title) ) ? strWordCut($movie->title, 50) : '&nbsp;' ).'</p>
			</div>
			<div style="width: 212px; height: 45px; float: left; display: block; margin-top: 0; margin-bottom: 0px; padding:0;">
				<p style="margin: 0;padding:0;font-size: 12px; color: #666;">'.( ( !empty($movie->tagline) ) ? strWordCut($movie->tagline, 70) : '&nbsp;' ).'</p>
			</div>
			<div style="width: 212px; height: 100px; float: left; display: block; margin: 0; padding: 0;">
				<div style="width: 110px; height: 150px; float: left; display: block; margin: 0;">
					<img style="width: 110px;" src="'.( ( !empty($movie->standard_image_url) ) ? 
                    base_url($movie->standard_image_url) :  base_url('assets\images\age-certificates\missing-image.jpg') ).'" />
				</div>
				<div style="width: 90px; height: 150px; float: left; display: block; margin: 0; padding-left:10px;">
					<div>
						<img style="width: 30px;margin-bottom: 6px;" src="'.( ( !empty($movie->age_rating_image) ) ? base_url($movie->age_rating_image) : base_url('assets\images\age-certificates\missing-rating.jpg') ).'" />
					</div>
					<div>';
        if(!empty($movie->actors) ) {
                            
            $movie->actors = json_decode($movie->actors);
                            
            if(is_array($movie->actors) ) {
                                
                 // to get only first three items
                 $movie->actors         = array_slice($movie->actors, 0, 3);
                 $lastArrayKey         = array_key_last($movie->actors);
                                
                foreach( $movie->actors as $k => $actor ){
                    if($k != $lastArrayKey ) {
                         $html .= '<span style="font-size: 11px; color: #666;">'.( strWordCut(ucwords($actor), 12) ).'</span><br />';
                    } else {
                        $html .= '<span style="font-size: 11px; color: #666;">'.( strWordCut(ucwords($actor), 12) ).'</span>';
                    }
                }
            } else {
                $html .= '<p style="font-size: 11px; color: #666;">'.( strWordCut(ucwords($movie->actors), 12) ).'</p>';
            }

        } else {
            $html .= '<p>&nbsp;</p>';
        }

                        $html .= '
					</div>
					<div style="display: block; float: left;width: 100%; margin: 10px 0;">
						<p style="margin: 0;padding:0;font-size: 12px; color: #ed1b24;">'.( ( !empty($movie->running_time) ) ? $movie->running_time : '&nbsp;' ).'</p>
					</div>
					<div style="display: block; float: left;width: 100%;">';
        if(!empty($movie->genre) ) {
            if(is_array($movie->genre) ) {
                                
                // to get only first three items
                $movie->genre         = array_slice($movie->genre, 0, 3);
                $lastArrayKeyGenre    = array_key_last($movie->genre);
                                
                foreach( $movie->genre as $l => $genre ){
                    if($l != $lastArrayKeyGenre ) {
                        $html .= '<span style="font-size: 11px; color: #666;">'.( strWordCut(ucwords($genre), 15) ).'</span><br />';
                    } else {
                        $html .= '<span style="font-size: 11px; color: #666;">'.( strWordCut(ucwords($genre), 15) ).'</span>';
                    }
                }
            } else {
                $html .= '<p style="font-size: 11px; color: #666;">'.( strWordCut(ucwords($movie->genre), 15) ).'</p>';
            }

        } else {
            $html .= '<p>&nbsp;</p>';
        }
                        $html .= '
					</div>
				</div>
			</div>
		</div>';

        // this is the disclaimer. If condition is applied and the one below is commented out - it will be shown only at the end of the document. 
        // if( array_key_last( $pdf_data ) == $movie_id ){
            // $html .='<div style="display: block; float: left; width: 100%;"><p style="display: inline; margin: 5px 10px 10px 10px; padding: 0; font-size: 10px; color: #666;">Film availability depends upon clearance dates in the territory</p></div>';
        // }

        if(( $i > 0 ) && ( $i % 9 == 8 ) || ( array_key_last($pdf_data) == $movie_id ) ) {
            $html .='<div style="display: block; float: left; width: 100%;"><p style="display: inline; margin: 5px 10px 10px 10px; padding: 0; font-size: 10px; color: #666;">Film availability depends upon clearance dates in the territory</p></div>';
            $html .='</div>
				<footer>';
            // first, footer for the Airtime version
            if(!empty($pdf_type) && ( strtolower($pdf_type) == "airtime" ) ) {
                $html .='<div class="airtime_footer" style="display: block, float: left; width: 748px; height: 78px; margin: 2px; background: #fff;">';
                $html .='<div class="airtime_icon_container" style="display: block; float: left; width: 83px; height: 80px; margin-right: 2px; background: #fff;">
							<div class="fdj" style="background: #ed1b24;display: block; float: left; width: 80px; height: 80px;">
								<img class="footer_airtime_icon" style="width: 76px;display: block; float: right;margin: 2px;" src="'.( base_url('assets\images\logos\airtime_app_logo2.jpg') ).'"  />
							</div>
						</div>';
                $html .='
							<div class="airtime_footer_container" style="display: block; float: left; width: 664px; height: 80px;background: #ed1b24; text-align: center; position: relative;">
								<div class="center" style="padding: 10px 0;">
									<span class="footer_item_line" style="font-size: 20px; color: #fff;font-weight:bold;">www.airtime.cloud</span><br>
									<span class="footer_item_line" style="font-size: 20px; color: #fff;font-weight:bold;">e:info@aitime.cloud - t:&nbsp;0845&nbsp;555&nbsp;1212</span>
								</div>
							</div>
						</div>'; // closing Airtime footer
            } else {
                // no footer for the VOD
            }

            $html .='</footer>
			</div>';
            
            $mpdf->WriteHTML($style, 1); // 1 is for style sheet
            $mpdf->WriteHTML($html, 2);
            
        }
        $i++;
    }
}