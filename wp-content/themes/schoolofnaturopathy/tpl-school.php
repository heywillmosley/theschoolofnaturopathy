<?php
/*
Template Name: School
*/

get_header();

class EWSNSchool 
{
	const STUDENT = FALSE;
	const CURRICULUM = FALSE;
	const PAYMENT = FALSE;
	const DEBUG = FALSE;

	var $programs;
	var $membershipIDs;
	var $contdProgramsGrid;

	// Pages
	var $pageSchoolPrograms;
	var $pageSchoolProgramSingle;

	// program specific
	var $testimony;
	var $trackLength;
	var $trackHrs;
	var $programOverview;
	var $appBtn;
	var $whatYoullLearn;
	var $catalogDownloadBtn;
	var $requiredSupplies;
	var $programCurriculum;
	var $problems;
	var $features;
	var $faq;

	function __construct() {

		the_post_thumbnail( 'portrait' ); // style for images
		$image_size = 'portrait'; // (thumbnail, medium, large, full or custom size)

		$this->setStyles();
		$this->setPrograms();
		$this->setCurrentUserMembershipIDs();
		$this->setPageSchoolPrograms();
		$this->setPageSchoolProgramSingle();

		$this->getPageSchoolPrograms();
		$this->getPageSchoolProgramSingle();
	}

	function setStyles() {
		wp_enqueue_style( 'school', get_stylesheet_directory_uri() . '/css/school.css' ); // styles
	}

	function setPrograms() {
		$this->programs = get_field('program', 'options');
	}

	function getPrograms() {
		echo "<pre>";
		print_r($this->programs);
		echo "</pre>";
	}

	function setPageSchoolPrograms() {

		if ( basename( get_permalink() ) == 'programs' ) {

			$render = "<div class='fl-content-full container'>";
			$render .= "<div class='row'>";
			$render .= "<div class='fl-content col-md-12'>";
			$render .= "<div class='container'>";
			$render .= "<div class='page-programs'>";
			$render .= "<h1 class='title'>School Programs</h1>";
			$render .= $this->setContdProgramsGrid();
			$render .= "<p class='summary'>" . get_field('school_description', 'options') . "</p>";
			$render .= $this->setProgramsGrid();
			$render .= "</div>"; // end page-programs
			$render .= "</div>"; // end container
			$render .= "</div>"; // end fl-content col-md-12
			$render .= "</div>"; // end row
			$render .= "</div>"; // end fl-content-full container

			$this->pageSchoolPrograms = $render;
			return $render;

		} // end if program page
	}

	function getPageSchoolPrograms() {
		echo $this->pageSchoolPrograms;
	}

	function setPageSchoolProgramSingle() {
		if( $this->programs ) {

			$render = "<div class='row'>";
			foreach( $this->programs as $program ) {

				if($program['program_landing'] == get_permalink() ) {

					$program_landing = $program['program_landing'];
					$title = $program['program_title'];
					$headline = $program['program_headline'];
					$summary = $program['program_summary'];
					$image_size = 'large'; // (thumbnail, medium, large, full or custom size)
					$program_membership = $program['connected_membership_plan'][0];

					$render .= "<div class='page-program-single'>";
					$render .= "<div class='row'>";
					$render .= "<div class='col'>";
					$render .= "<h1 class='title'>$title</h1>";
					$render .= "<h2 class='headline'>$headline</h2>";
					$render .= "<p class='summary'>$summary</p>";
					$render .= $this->setCatalogDownloadBtn( $program['download_catalog'] );
					$render .= wp_get_attachment_image( $program['program_feature_image']['ID'], $image_size );
					$render .= $this->setTestimony( $program['testimonials'][0] );
					$render .= "</div>"; // end col
					$render .= "</div>"; // end row
					$render .= $this->setProgramOverview( $program['program_overview'] );
					$render .= $this->setWhatYoullLearn( $program['what_youll_learn'] );
					$render .= $this->setTrackLength( $program['track_length'] );
					$render .= $this->setTrackHrs( $program['number_of_hours_for_program'] );
					$render .= $this->setProblems( $program['problem_solved'] );
					$render .= $this->setTestimony( $program['testimonials'][1] );
					$render .= $this->setFeatures( $program['feature'] );
					$render .= $program['closing_reason_to_enroll'];
					$render .= "</hr>";
					$render .= $this->setAppBtn( $program['application_link'] );
					$render .= $this->setFAQ( $program['faq'] );
					$render .= "</div>"; // end program

					break;

				} // end if
			} // end foreach

			$render .= "</div>"; // end row

			$this->pageSchoolProgramSingle = $render;
			return $render;

		} // end if

	} // end setPageSchoolProgramSingle

	function getPageSchoolProgramSingle() {
		echo $this->pageSchoolProgramSingle;
	}

	function setContdProgramsGrid() {
		$render = "<div class='row'>";
		$contd_programs = "";
		$contd_blank = "";

		if($this->programs) {
			$i = 0;
			$contd_count = 0;
			foreach( $this->programs as $program ) {
				
				$title = $program['program_title'];
				$summary = $program['program_summary'];
				$program_link = $program['program_landing'];
				$program_roadmap = $program['program_roadmap'];
				$program_membership = $program['connected_membership_plan'][0];

				// Contd Render
				if(in_array( $program_membership, $this->membershipIDs ) ) { // show continue program if member active

					$contd_programs .= "<div class='col-sm-4'>";
					$contd_programs .= "<div class='card'>";
					$contd_programs .= wp_get_attachment_image( $program['program_feature_image']['ID'], 'medium' );
					$contd_programs .= "<div class='card-body'>";
					$contd_programs .= "<h5 class='card-title'>$title</h5>";
					$contd_programs .= "<a href='$program_roadmap' class='btn btn-primary'>Continue Program</a>";
					$contd_programs .=  "<p class='caption mt-2'><a href='$program_link'>Program Details</a></p>";
					$contd_programs .= "</div>";
					$contd_programs .= "</div>";
					$contd_programs .= "</div>";
					$contd_count++;

				} 

				// Add blanks if there aren't enough contd, add blanks
				if(count( $this->membershipIDs ) > 0 && count($contd_count == 1)) {

					while( $contd_count < 2 ) {
						$contd_blank .= "<div class='col-sm-4'>";
						$contd_blank .= "<div class='card contd-inactive'>";
						$contd_blank .= "</div>";
						$contd_blank .= "</div>";
						$contd_count++;
					}
				}
				
			} // end foreach
		} // programs

		$render .= $contd_programs;
		$render .= $contd_blank;
		$render .= "</div><hr/>"; // end programs

		$this->contdProgramGrid = $render;
		return "$render";
	}

	function getContdProgramsGrid() {
		echo $this->contdProgramsGrid;
	}

	function setProgramsGrid() {

		$render = "<div class='program-grid'>";
		$render .= "<div class='row'>";

		
		if($this->programs) {
			$i = 0;
			$unenrolled_program_count = 0;

			foreach( $this->programs as $program ) {
				
				$title = $program['program_title'];
				$summary = $program['program_summary'];
				$program_link = $program['program_landing'];
				$program_roadmap = $program['program_roadmap'];
				$program_membership = $program['connected_membership_plan'][0];

				if(!in_array( $program_membership, $this->membershipIDs ) ) {
					// Programs
					$render .= "<div class='col-sm-6 program-block'>";
					$render .= "<a href='$program_link'><h2 class='block-title'><small>$title</small></h2></a>";
					$render .= "<a href='$program_link'>" . wp_get_attachment_image( $program['program_feature_image']['ID'], 'portrait' ) . "</a>";
					$render .= "<p class='pt-3'>$summary</p>";
					if(in_array( $program_membership, $this->membershipIDs ) )
							$render .= "<p>Currently Enrolled</p>";
					$render .=  $this->setProgramQuickLinks($program_link);
					$render .= "</div>"; // end col-sm-6

					$unenrolled_program_count++; 
				}


					
					if ( $unenrolled_program_count % 2 != 0 ) { 
						$unerolled_blank = "<div class='col'>";
						$unerolled_blank .= "</div>";
					}
				
			} // end foreach
		} // programs


		$render .= $unerolled_blank; // end row
		$render .= "</div>"; // end row
		$render .= "</div>"; // end programs

		return "$render";
	}

	function getProgramsGrid() {
		echo $this->setProgramsGrid();
	}

	function setProgramQuickLinks($program_link) {

		if ( basename( get_permalink() ) == 'programs' ) {
		
			$render = "<div class='quick-links'>";
			$render .= "<a href='$program_link'>View Program</a>";
			if( self::CURRICULUM )
				$render .= "<a href='$program_link#curriculum'>Curriculum</a> | ";
			//$render .= "<a href='$program_link#faq'>FAQ</a> | ";
			//$render .= "<a href='$program_link#apply'>Apply</a>";
			$render .= "</div>"; // end program-links
			
		} else {
			
			$render = '<hr/>';
			$render .= '<ul class="nav justify-content-center quick-links">';
			$render .= '<li class="nav-item">';
			$render .= "<a class='nav-link' href='$program_link#overview'>Overview</a>";
			$render .= '</li>';
			$render .= '<li class="nav-item">';
			if( self::CURRICULUM )
				$render .= "<a class='nav-link' href='$program_link#curriculum'>Curriculum</a>";
			$render .= '</li>';
			$render .= '<li class="nav-item">';
			// $render .= "<a class='nav-link' href='$program_link#faq'>FAQ</a>";
			$render .= '</li>';
			//$render .= '<li class="nav-item">';
			//$render .= "<a class='nav-link' href='$program_link#apply'>Apply</a>";
			//$render .= '</li>';
			$render .= '</ul>';
		}

		return $render;
	}

	function getProgramQuickLinks() {
		echo $this->setProgramQuickLinks();
	}

	// Render EWSN Debug
	function debugPrograms() {
		if( self::DEBUG) {
			if( current_user_can('administrator') ) {
				echo '<br/><hr/></br/><h2>Debug on. Only displays for Admin</h2>';
				echo '<pre>'; 
				print_r($this->programs); 
				echo '</pre>';

			} // end is_admin
		}
	}

	// Render Page Programs


	// Display testimony
	function setTestimony($testimony) { 
		
		if( !empty($testimony) ) {

			$summary = $testimony['testimony'];
			$name = $testimony['testimony_first_and_last_name'];
			$title = $testimony['job_title'];
			$company = $testimony['company'];
			$link = $testimony['testimony_link'];
			
			$render = "<blockquote class='testimony'>";
			$render .=  "<p><em>\"$summary\"</em></p>";
			if($link)
				$render .= "<a href='$link'>"; 
			$result .= "<p>";
			$result .=  $name;
			if($company && $title)
				$render .= " | $title at $company"; 
			elseif($company)
				$render .= " | $company"; 
			$render .= "</p>";
			if($link)
				$render .= "</a>";
			$render .= "</blockquote><hr/>";

			$this->testimony = $render;
			return $render;

		} // end if
	}

	function getTestimony() {
		echo $this->testimony;
	}

	// Render track length
	function setTrackLength($length) { 
		
		if(!empty($length)) {

			$render = "<div class='row'>";
			$render .= "<div class='col'>";
			$render .= "<div id='overview' class='overview mb-2'>";
			$render .="<h2>Track Length</h2>";
			$render .= "$length Months";
			$render .= "</div>";
			$render .= "</div>";
			$render .= "</div>";

			$this->trackLength = $render;
			return $render;
		}
	}

	function getTrackLength() {
		echo $this->trackLength;
	}

	// Render track length
	function setTrackHrs($hrs) { 
		
		if(!empty($hrs)) {

			$render = "<div class='row'>";
			$render .= "<div class='col'>";
			$render .= "<div id='overview' class='overview mb-2'>";
			$render .="<h2>Hours In Program</h2>";
			$render .= "$hrs Hours";
			$render .= "</div>";
			$render .= "</div>";
			$render .= "</div>";

			$this->trackHrs = $render;
			return $render;
		}
	}

	function getTrackHrs() {
		echo $this->trackHrs;
	}

	// Display testimony
	function setProgramOverview($overview) { 
		
		if(!empty($overview)) {

			$render = "<div class='row'>";
			$render .= "<div class='col'>";
			$render .= "<div id='overview' class='overview mb-2'>";
			$render .="<h2>Program Overview</h2>";
			$render .= $overview;
			$render .= "</div>";
			$render .= "</div>";
			$render .= "</div>";

			$this->programOverview = $render;
			return $render;
		}
	}

	function getProgramOverview() {
		echo $this->programOverview;
	}

	function setAppBtn($url) {

			$this->appBtn = "<a href='$url' target='_blank' class='btn btn-block btn-success btn-lg' role='button'>Fill Out Application</a>";
			return $this->appBtn;
	}

	function getAppBtn() {
		echo $this->appBtn;
	}

	// Render What You'll Learn
	function setWhatYoullLearn($list) {
		
		if(!empty($list)) {

			$things = array();
			$i = 0;
			
			// Check if list has empty items
			foreach($list as $item) {
				if( !empty( $item['thing_student_will_learn'] ) ) {
					$things[$i] = $item['thing_student_will_learn'];
					$i++;
				}	
			} 
			
			// if there are any items, than display
			if(!empty($things)) {

				$render = "<div class='row'>";
				$render .= "<div class='col'>";
				$render .= '<h2>What You\'ll Learn</h2>';
				$render .= '<ul>';

				foreach( $things as $thing )
					$render .= "<li>" . $thing . "</li>";

				$render .= '</ul></div></div>';
				
				$this->whatYoullLearn = $render;
				return $render;
			}
		}
	}

	function getWhatYoullLearn() {
		echo $this->whatYoullLearn;
	}

	// Render Catalog Download Button
	function setCatalogDownloadBtn($url) {
		if(!empty($url)) {

			$render = "<div class='text-center mb-3'>";
			$render .= "<a href='$url' target='_blank' class='btn btn-block btn-info' role='button'>Download Program Catalog</a>";	
			$render .= "</div>";
			
			$this->catalogDownloadBtn = $render;
			return $render;
		}	
	}

	function getCatalogDownloadBtn() {
		echo $this->catalogDownloadBtn;
	}

	// Render Required Supplies
	function setRequiredSupplies($list) {
		$things = array();
		$i = 0;
		
		// Check if list has empty items
		foreach($list as $item) {
			if( !empty( $item['item'] ) ) {
				$things[$i] = $item['item'];
				$i++;
			}	
		} 
		
		// if there are any items, than display
		if(!empty($things)) {

			$render = "<div class='row'>";
			$render .= "<div class='col'>";
			$render .= '<h2>Required Supplies</h2>';
			$render .= '<ul>';

			foreach( $things as $thing )
				$render .= "<li>" . $thing . "</li>";

			$render .= '</ul></div></div>';
			
			$this->requiredSupplies = $render;
			return $render;
		}

	} 

	function getRequiredSupplies() {
		echo $this->requiredSupplies;
	}

	function setCurriculum($track_IDs) {
		
		if($track_IDs && self::CURRICULUM) {
			$render = "<div class='row'>";
			$render .= "<div class='col'>";
			$render .= '<h2>Required Curriculum</h2>';
		
			foreach($track_IDs as $track_ID) {
				$render .= "<div class='program-track-breakdown'>";
				$render .= "<h3>" . get_the_title($track_ID) . "</h3>";
				$render .= do_shortcode("[course_content course_id=$track_ID ]");
				$render .= "</div>";
			}

			$render .= "</div>";
			$render .= "</div>";

			$this->programCurriculum = $render;
			return $render;
		}
	}

	function getCurriculum() {
		echo $this->programCurriculum;
	}

	function setProblems($problems) {

		if( !empty($problems) ) {

			$render = '<div class="row" style="max-width: 800px; margin: 0 auto">';
		
			foreach($problems as $problem) {
				
				$render .= '<div clss="col-sm-6">';
				$render .= '<h3>' . $problem['problem_title'] . '</h3>';
				$render .= wp_get_attachment_image( $problem['problem_image']['ID'], 'portrait' );
				$render .= '<p>' . $problem['problem_description'] . '</p>';
				$render .= '</div>';
			}
			
			$this->problems = $render;
			return $render;

		}
	}

	function getProblems() {
		echo $this->problems;
	}

	function setFeatures ($features) {

		if( !empty($features) ) {
		
			$render = '<div class="row" style="max-width: 800px; margin: 0 auto">';
			
			foreach($features as $feature) {
				
				$render .= '<div class="col-sm-6">';
				$render .= '<h3>' . $feature['feature_title'] . '</h3>';
				$render .= wp_get_attachment_image( $feature['feature_image']['ID'], 'portrait');
				$render .= '<p>' . $feature['feature_description'] . '</p>';
				$render .= '</div>';
			}
			
			$this->features = $render;
			return $render;
		}
	}

	function getFeatures() {
		echo $this->features;
	}

	// Render FAQ
	function setFAQ($faqs) {
		
		if(!empty($faqs)) {
			$render = '<h2>FAQ</h2>';
			$render .= '<ul>';
			
			foreach($faqs as $faq) {
				
				$render .= '<li">';
				$render .= '<p><strong>Q:</strong> ' . $faq['question'] . '</p>';
				$render .= '<p><strong>A:</strong> ' . $faq['answer'] . '</p>';
				$render .= '<hr/></li>';
			}
			
			$this->faq = $render;
			return $render;

		} // end empty faq
	}

	function getFAQ() {
		echo $this->faq;
	}

	function setCurrentUserMembershipIDs() { // return array
		$user_id = get_current_user_id();
		$args = array( 
		    'status' => array( 'active' ),
		); 
		$memberships = wc_memberships_get_user_active_memberships( $user_id, $args );
		$membership_IDs = array();
		$i = 0;
		foreach($memberships as $membership) {
			$membership_IDs[$i] = $membership->plan_id;
			$i++;
		}

		$this->membershipIDs = $membership_IDs;
	}

	function getCurrentUserMembershipIDs() {
		echo $this->membershipIDs;
	}

} // end class

$ewsn_school = new EWSNSchool; // Ready & Run Captain

get_footer(); ?>