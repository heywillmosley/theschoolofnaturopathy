<?php
/*
Template Name: School
*/

get_header();

class WMMedia 
{
	public $media;
	protected $args;
	public $title;
	public $img;
	public $bd;
	public $URL;
	public $mediaClass;
	public $imgAlign = 'left';
	public $btn;

	function __construct( array $options = array() ) {

		// Check for a necessary arg
        //if ( !isset( $options['requiredArg1'] ) )
        //    throw new Exception('Missing requiredArg1');

        // Now I can just localize
        //$requiredArg1 = $options['requiredArg1'];
        //$this->mediaClass = (isset($options['class'])) ? $options['class'] : null;
        //$this->imgAlign = (isset($options['img'])) ? $options['img'] : 'right';
        //$this->btn = (isset($options['btn'])) ? $options['btn'] : null;
	}

	function setTitle ($title) {
		$this->title = $title;
	}

	function getTitle() {
		return $this->title;
	}

	function setImg ($img) {
		$this->img = $img;
	}

	function getImg() {
		return $this->img;
	}

	function setBd ($bd) {
		$this->bd = $bd;
	}

	function getBd() {
		return $this->bd;
	}

	function setURL ($URL) {
		$this->URL = $URL;
	}

	function getURL() {
		return $this->URL;
	}

	function setBtn($text, $URL, $type = 'info', $classes = '') {
		$this->btn = "<a class='btn btn-$type $classes' href='$link'' role='button'>$text</a>";
	}

	function getBtn() {
		return $this->btn;
	}

	function setMedia() {

		$render = "<div class='media wm-media mb-2 $this->mediaClass'>";
		if( isset( $this->img ) && $this->imgAlign == 'right' )
			$render .= "<img class='mr-3' src='" . $this->getImg() . "' />";
		$render .= "<div class='media-body'>";
		if( $this->title )
			$render .= "<h4 class='mt-0'>" . $this->getTitle() . "</h4>";
		$render .= $this->getBd();
		if($this->btn )
			$render .= $this->getBtn();
		$render .= "</div>";
		if( isset( $this->img ) && $this->imgAlign == 'left')
			$render .= "<img class='ml-3' src='" . $this->getImg() . "' />";
		$render .= "</div>";

		$this->media = $render;
	}

	function getMedia() {
		return $this->media;
	}
}

class EWSNSchool 
{
	/** template version number */
	const VERSION = '1.0.0'; // time to start tracking changes

	const STUDENT = FALSE;
	const CURRICULUM = FALSE;
	const PAYMENT = FALSE;
	const DEBUG = FALSE;

	private $programs;
	private $programsGrid;
	private $membershipIDs;
	private $contdPrograms;

	// Pages
	private $pageSchoolPrograms;
	private $pageSchoolProgramSingle;

	// program specific
	private $testimony;
	private $trackLength;
	private $trackHrs;
	private $programOverview;
	private $programQuickLinks;
	private $appBtn;
	private $whatYoullLearn;
	private $catalogDownloadBtn;
	private $requiredSupplies;
	private $programCurriculum;
	private $problems;
	private $features;
	private $faq;

	function __construct() {

		the_post_thumbnail( 'portrait' ); // style for images
		$image_size = 'portrait'; // (thumbnail, medium, large, full or custom size)

		$this->setStyles();
		$this->setPrograms();
		$this->setCurrentUserMembershipIDs();
		$this->setPageSchoolPrograms();
		$this->setPageSchoolProgramSingle();

		echo $this->getPageSchoolPrograms();
		echo $this->getPageSchoolProgramSingle();
	}

	private function setStyles() {
		wp_enqueue_style( 'school', get_stylesheet_directory_uri() . '/css/school.css' ); // styles
	}

	private function setPrograms() {
		$this->programs = get_field('program', 'options');
	}

	private function getPrograms() {
		return $this->programs;

	}

	private function setPageSchoolPrograms() {

		if ( basename( get_permalink() ) == 'programs' ) {

			$this->setContdPrograms();
			$this->setProgramsGrid();
			$render = "<div class='fl-content-full container'>";
			$render .= "<div class='row'>";
			$render .= "<div class='fl-content col-md-12'>";
			$render .= "<div class='container'>";
			$render .= "<div class='page-programs'>";
			$render .= "<h1 class='title'>School Programs</h1>";
			$render .= "<p class='summary'>" . get_field('school_description', 'options') . "</p>";
			$render .= $this->getContdPrograms();
			$render .= $this->getProgramsGrid();
			$render .= "</div>"; // end page-programs
			$render .= "</div>"; // end container
			$render .= "</div>"; // end fl-content col-md-12
			$render .= "</div>"; // end row
			$render .= "</div>"; // end fl-content-full container

			$this->pageSchoolPrograms = $render;

		} // end if program page
	}

	private function getPageSchoolPrograms() {
		return $this->pageSchoolPrograms;
	}

	private function setPageSchoolProgramSingle() {
		if( $this->programs ) {

			$render = "<div class='container'>";
			foreach( $this->programs as $program ) {

				if($program['program_landing'] == get_permalink() ) {

					$program_landing = $program['program_landing'];
					$title = $program['program_title'];
					$headline = $program['program_headline'];
					$summary = $program['program_summary'];
					$image_size = 'large'; // (thumbnail, medium, large, full or custom size)
					$program_membership = $program['connected_membership_plan'][0];

					// Set
					$this->setCatalogDownloadBtn( $program['download_catalog'] );
					$this->setTestimony( $program['testimonials'][0] );
					$this->setProgramOverview( $program['program_overview'] );
					$this->setWhatYoullLearn( $program['what_youll_learn'] );
					$this->setTrackLength( $program['track_length'] );
					$this->setTrackHrs( $program['number_of_hours_for_program'] );
					$this->setProblems( $program['problem_solved'] );
					$this->setTestimony( $program['testimonials'][1] );
					$this->setFeatures( $program['feature'] );
					$this->setFAQ( $program['faq'] );
					$this->setAppBtn( $program['application_link'] );

					// Render
					$render .= "<div class='page-program-single'>";
					$render .= "<div class='row'>";
					$render .= "<div class='col-12'>";
					$render .= "<h1 class='title'>$title</h1>";
					$render .= "<h2 class='headline'>$headline</h2>";
					$render .= "<p class='summary'>$summary</p>";
					$render .= $this->getCatalogDownloadBtn();
					$render .= wp_get_attachment_image( $program['program_feature_image']['ID'], $image_size );
					$render .= $this->getTestimony();
					$render .= "</div>"; // end col
					$render .= "</div>"; // end row
					$render .= $this->getProgramOverview();
					$render .= $this->getWhatYoullLearn();
					$render .= $this->getTrackLength();
					$render .= $this->getTrackHrs();
					$render .= $this->getProblems();
					$render .= $this->getTestimony();
					$render .= $this->getFeatures();
					$render .= $program['closing_reason_to_enroll'];
					$render .= "</hr>";
					$render .= $this->getFAQ();
					$render .= $this->getAppBtn();
					$render .= "</div>"; // end program

					break;

				} // end if
			} // end foreach

			$render .= "</div>"; // end row

			$this->pageSchoolProgramSingle = $render;

		} // end if

	} // end setPageSchoolProgramSingle

	private function getPageSchoolProgramSingle() {
		return $this->pageSchoolProgramSingle;
	}

	private function setContdPrograms() {
		if( $this->programs ) {

			$render = "<div class='row'>";
			$header = "";

			foreach( $this->programs as $program ) {
				
				$title = $program['program_title'];
				$summary = $program['program_summary'];
				$link = $program['program_landing'];
				$roadmap = $program['program_roadmap'];
				$membership = $program['connected_membership_plan'][0];

				if( in_array( $membership, $this->membershipIDs ) ) {
					$header = "<h6><strong>Enrolled Programs</strong></h6>";

					$render .= "<div class='media mb-2 contd-media'>";
					$render .= "<div class='media-body'>";
					$render .= "<h4 class='mt-0'>$title</h4>";
					$render .= "<a href='$roadmap' class='btn btn-primary btn-sm mb-1'>Continue Program</a>";
					$render .= "<p class='caption'><a href='$link'>Program Details</a></p>";
					$render .= "</div>";
					$render .= "<a href='$roadmap' class='mr-2'>" . wp_get_attachment_image( $program['program_feature_image']['ID'], 'thumbnail' ) . "</a>";
					$render .= "</div>";
				}	
			} // end foreach
		} // programs

		$render .= "</div>"; // end programs

		$this->contdPrograms = "$header $render";
	}

	private function getContdPrograms() {
		return $this->contdPrograms;
	}

	private function setProgramsGrid() {

		$render = "<div class='program-grid'>";
		$render .= "<h6 class='mt-3'><strong>Available Programs</strong></h6>";
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
					$render .=  $this->getProgramQuickLinks($program_link);
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

		$this->programsGrid = $render;
	}

	private function getProgramsGrid() {
		return $this->programsGrid;
	}

	private function setProgramQuickLinks($program_link) {

		if ( basename( get_permalink() ) == 'programs' ) {
		
			$render = "<div class='quick-links'>";
			$render .= "<a href='$program_link'>View Program</a>";
			if( self::CURRICULUM )
				$render .= "<a href='$program_link#curriculum'>Curriculum</a> | ";
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
			$render .= '</li>';
			$render .= '</ul>';
		}

		$this->programQuickLinks = $render;
	}

	private function getProgramQuickLinks() {
		return $this->programQuickLinks;
	}

	// Render EWSN Debug
	private function debugPrograms() {
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
	private function setTestimony($testimony) { 
		
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

		} // end if
	}

	private function getTestimony() {
		return $this->testimony;
	}

	// Render track length
	private function setTrackLength($length) { 
		
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

	private function getTrackLength() {
		return $this->trackLength;
	}

	// Render track length
	private function setTrackHrs($hrs) { 
		
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
		}
	}

	private function getTrackHrs() {
		return $this->trackHrs;
	}

	// Display testimony
	private function setProgramOverview($overview) { 
		
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
		}
	}

	private function getProgramOverview() {
		return $this->programOverview;
	}

	private function setAppBtn($url) {

			$this->appBtn = "<a href='$url' target='_blank' class='btn btn-block btn-success btn-lg' role='button'>Fill Out Application</a>";
	}

	private function getAppBtn() {
		return $this->appBtn;
	}

	// Render What You'll Learn
	private function setWhatYoullLearn($list) {
		
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
			}
		}
	}

	private function getWhatYoullLearn() {
		return $this->whatYoullLearn;
	}

	// Render Catalog Download Button
	private function setCatalogDownloadBtn($url) {
		if(!empty($url)) {

			$render = "<div class='text-center mb-3'>";
			$render .= "<a href='$url' target='_blank' class='btn btn-block btn-info' role='button'>Download Program Catalog</a>";	
			$render .= "</div>";
			
			$this->catalogDownloadBtn = $render;
		}	
	}

	private function getCatalogDownloadBtn() {
		return $this->catalogDownloadBtn;
	}

	// Render Required Supplies
	private function setRequiredSupplies($list) {
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
		}

	} 

	private function getRequiredSupplies() {
		return $this->requiredSupplies;
	}

	private function setCurriculum($track_IDs) {
		
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
		}
	}

	private function getCurriculum() {
		return $this->programCurriculum;
	}

	private function setProblems($problems) {

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

		}
	}

	private function getProblems() {
		return $this->problems;
	}

	private function setFeatures ($features) {

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
		}
	}

	private function getFeatures() {
		return $this->features;
	}

	// Render FAQ
	private function setFAQ($faqs) {
		
		if(!empty($faqs)) {
			$render = "<div class='row'>";
			$render .= "<div class='col'>";
			$render .= '<h2>FAQ</h2>';
			$render .= '<ul>';
			
			foreach($faqs as $faq) {
				
				$render .= '<li">';
				$render .= '<p><strong>Q: ' . $faq['question'] . '</strong></p>';
				$render .= '<p><strong>A:</strong> ' . $faq['answer'] . '</p>';
				$render .= '<hr/></li>';
			}

			$render .= "</div>";
			$render .= "</div>";
			
			$this->faq = $render;

		} // end empty faq
	}

	private function getFAQ() {
		return $this->faq;
	}

	private function setCurrentUserMembershipIDs() { // return array
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

	private function getCurrentUserMembershipIDs() {
		return $this->membershipIDs;
	}

} // end class

$ewsn_school = new EWSNSchool; // Ready & Run Captain

get_footer(); ?>