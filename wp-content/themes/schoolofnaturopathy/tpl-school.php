<?php
/*
Template Name: School
*/

get_header(); 
wp_enqueue_style( 'school', get_stylesheet_directory_uri() . '/css/school.css' ); // styles
require_once( get_stylesheet_directory_uri() . '/includes/school_functions.php');

the_post_thumbnail( 'portrait' ); // style for images
//$programs = get_field('program', 'options'); // Grab global program variable
$image_size = 'portrait'; // (thumbnail, medium, large, full or custom size)

// Constants
define('EWSN_PROGRAMS', get_field('program', 'options') );
define("EWSN_STUDENT", FALSE);
define("EWSN_CURRICULUM", FALSE);
define("EWSN_PAYMENT", FALSE);
define("EWSN_DEBUG", FALSE);

?>

<div class="fl-content-full container">
	<div class="row">
		<div class="fl-content col-md-12">
		    <div class="container">
				<!-- Programs Page -->
				<?php if ( basename( get_permalink() ) == 'programs' ) : ?>
					<div class="page-programs">
						<h1 class="title">School Programs</h1>
						<p class="summary"><?php the_field('school_description', 'options'); ?></p>
						<?php render_page_programs (); ?>
					</div><!-- end page-programs --> 
				<?php endif // end if on program page ?>
				
				<!-- Single Program Landing Page -->
				<?php if( EWSN_PROGRAMS ) : ?>
					<div class="row">
						<?php foreach( EWSN_PROGRAMS as $program ) : ?>
							<?php if($program['program_landing'] == get_permalink() ) : ?>

								<?php // Program Variables
								$program_landing = $program['program_landing'];
								$title = $program['program_title'];
								$headline = $program['program_headline'];
								$summary = $program['program_summary'];
								$image_size = 'large'; // (thumbnail, medium, large, full or custom size)
								$program_membership = $program['connected_membership_plan'][0];
								?>
								<div class="page-program-single">
									<div class="row">
										<div class="col">
											<h1 class="title"><?php echo $title; ?></h1>
											<h2 class="headline"><?php echo $headline; ?></h2>
											<p class="summary"><?php echo $summary; ?></p>
											<?php // render_quick_links($program['program_landing']); ?>
											<?php render_catalog_download_btn($program['download_catalog']); ?>
											<?php echo wp_get_attachment_image( $program['program_feature_image']['ID'], $image_size ); ?>
										
											<?php testimony($program['testimonials'][0]); ?>
										</div><!-- end col -->
									</div><!-- end row -->
									
									<?php render_program_overview($program['program_overview']); ?>
									<?php render_what_youll_learn($program['what_youll_learn']); ?>
									<?php // render_required_supplies($program['supplies_youll_need']); ?>
									<?php // render_curriculum($program['included_tracks']); ?>
									<?php //the_field('refund_policy', 'options'); ?>
									<?php render_track_length($program['track_length']); ?>
									<?php render_track_hours($program['number_of_hours_for_program']); ?>
									<?php render_problems( $program['problem_solved'] ); ?>
									<?php testimony($program['testimonials'][1]); ?>
									<?php render_features( $program['feature'] ); ?>
									<?php $program['closing_reason_to_enroll'] ?>
									<hr/>
									<?php render_app_btn($program['application_link']); ?>
									<?php render_faq( $program['faq'] ); ?>
									<?php render_ewsn_debug($program); ?>
								</div><!-- end program -->
								<?php break; ?>
							<?php endif ?>
						<?php endforeach ?>
					<?php endif; ?>
                </div><!-- end row -->
            </div><!-- end container -->
		</div><!-- end fl-content col-md-12 -->
	</div><!-- end row -->
</div><!-- end fl-content-full container -->

<?php // FUNCTIONS

// Render EWSN Debug
function render_ewsn_debug($program) {
	if(EWSN_DEBUG) {
		if( current_user_can('administrator') ) {
			echo '<br/><hr/></br/><h2>Debug on. Only displays for Admin</h2>';
			echo '<pre>'; 
			print_r($program); 
			echo '</pre>';

		} // end is_admin
	}
}

// Render Page Programs
function render_page_programs ($image_size = 'portrait') {

	$contd_render = "<div class='row'>";

	$render = "<div class='program-grid'>";
	$render .= "<div class='row'>";

	
	if(EWSN_PROGRAMS) {
		$i = 0;
		foreach( EWSN_PROGRAMS as $program ) {
			
			$title = $program['program_title'];
			$summary = $program['program_summary'];
			$program_link = $program['program_landing'];
			$program_roadmap = $program['program_roadmap'];
			$program_membership = $program['connected_membership_plan'][0];

			// Contd Render
			if(in_array( $program_membership, get_current_user_membership_ids() ) ) { // show continue program if member active

				$contd_render .= "<div class='col-sm-4'>";
				$contd_render .= "<div class='card'>";
				$contd_render .= wp_get_attachment_image( $program['program_feature_image']['ID'], 'medium' );
				$contd_render .= "<div class='card-body'>";
				$contd_render .= "<h5 class='card-title'>$title</h5>";
				//$contd_render .= "<p class='card-text'>$summary</p>";
				$contd_render .= "<a href='$program_roadmap' class='btn btn-primary'>Continue Program</a>";
				$contd_render .=  "<p class='caption mt-2'><a href='$program_link'>More Details</a></p>";
				$contd_render .= "</div>";
				$contd_render .= "</div>";
				$contd_render .= "</div>";

			} 

			// Programs
			$render .= "<div class='col-sm-6 program-block'>";
			$render .= "<a href='$program_link'><h2 class='block-title'><small>$title</small></h2></a>";
			$render .= "<a href='$program_link'>" . wp_get_attachment_image( $program['program_feature_image']['ID'], $image_size ) . "</a>";
			$render .= "<p class='pt-3'>$summary</p>";
			if(in_array( $program_membership, get_current_user_membership_ids() ) )
					$render .= "<p>Currently Enrolled</p>";
			$render .=  render_quick_links($program_link, FALSE);
			$render .= "</div>"; // end col-sm-6
			
			$i++;  
			if ($i % 2 == 0 && $i != count($programs)) { 
				$render .= "</div><hr/><div class='row'>";
			}
			
		} // end foreach
	} // programs

	$contd_render .= "</div><hr/>"; // end programs

	$render .= "</div>"; // end row
	$render .= "</div>"; // end programs
	echo $contd_render;
	echo $render;
	
} // end render_page_programs

// Display testimony
function testimony($testimony) { 
	
	if(!empty($testimony)) {
		$summary = $testimony['testimony'];
		$name = $testimony['testimony_first_and_last_name'];
		$title = $testimony['job_title'];
		$company = $testimony['company'];
		$link = $testimony['testimony_link'];
		
		$result = "<blockquote class='testimony'>";
		$result .=  "<p><em>\"$summary\"</em></p>";
		if($link) { 
			$result .= "<a href='$link'>"; 
		}
		$result .= "<p>";
		$result .=  $name;
		if($company && $title) { 
			$result .= " | $title at $company"; 
		}
		elseif($company) { 
			$result .= " | $company"; 
		}
		$result .= "</p>";
		if($link) { 
			$result .= "</a>"; 
		}
		$result .= "</blockquote><hr/>";
		echo $result;
	}
}

// Render track length
function render_track_length($length) { 
	
	if(!empty($length)) {

		$render = "<div class='row'>";
		$render .= "<div class='col'>";
		$render .= "<div id='overview' class='overview mb-2'>";
		$render .="<h2>Track Length</h2>";
		$render .= "$length Months";
		$render .= "</div>";
		$render .= "</div>";
		$render .= "</div>";
		echo $render;
	}
}

// Render track length
function render_track_hours($hrs) { 
	
	if(!empty($hrs)) {

		$render = "<div class='row'>";
		$render .= "<div class='col'>";
		$render .= "<div id='overview' class='overview mb-2'>";
		$render .="<h2>Hours In Program</h2>";
		$render .= "$hrs Hours";
		$render .= "</div>";
		$render .= "</div>";
		$render .= "</div>";
		echo $render;
	}
}

// Display testimony
function render_program_overview($overview) { 
	
	if(!empty($overview)) {

		$render = "<div class='row'>";
		$render .= "<div class='col'>";
		$render .= "<div id='overview' class='overview mb-2'>";
		$render .="<h2>Program Overview</h2>";
		$render .= $overview;
		$render .= "</div>";
		$render .= "</div>";
		$render .= "</div>";
		echo $render;
	}
}

function render_app_btn($url) {

		echo "<a href='$url' target='_blank' class='btn btn-block btn-success btn-lg' role='button'>Fill Out Application</a>";
}

// Render What You'll Learn
function render_quick_links($program_link, $display = TRUE) {
	
	if ( basename( get_permalink() ) == 'programs' ) {
		
		$render = "<div class='quick-links'>";
		$render .= "<a href='$program_link'>View Program</a>";
		if(EWSN_CURRICULUM)
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
		if(EWSN_CURRICULUM)
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
	
	if( $display ) {
		echo $render;
	} else {
		return $render;
	}
} // end render

// Render What You'll Learn
function render_what_youll_learn($list) {
	
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
		
		echo $render;
	}
}

// Render Catalog Download Button
function render_catalog_download_btn($url) {
	if(!empty($url))
		$render = "<div class='text-center mb-3'>";
		$render .= "<a href='$url' target='_blank' class='btn btn-block btn-info' role='button'>Download Program Catalog</a>";	
		$render .= "</div>";
		echo $render;
}

// Render Required Supplies
function render_required_supplies($list) {
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
		
		echo $render;
	}
} // end function

// Render Required Supplies
function render_curriculum($track_IDs) {
	
	if($track_IDs && EWSN_CURRICULUM) {
		$render = "<div class='row'>";
		$render .= "<div class='col'>";
		$render .= '<h2>Required Curriculum</h2>';
	
		foreach($track_IDs as $track_ID) {
			$render .= "<div class='program-track-breakdown'>";
			$render .= "<h3>" . get_the_title($track_ID) . "</h3>";
			$render .= do_shortcode("[course_content course_id=$track_ID ]");
			$render .= "</div>";
		}

		$render .= "</div></div>";

		echo $render;
	}
}

// Render Problems
function render_problems ($problems, $image_size = 'portrait') {
	
	$render = '<div class="row" style="max-width: 800px; margin: 0 auto">';
	
	foreach($problems as $problem) {
		
		$render .= '<div clss="col-sm-6">';
		$render .= '<h3>' . $problem['problem_title'] . '</h3>';
		$render .= wp_get_attachment_image( $problem['problem_image']['ID'], $image_size );
		$render .= '<p>' . $problem['problem_description'] . '</p>';
		$render .= '</div>';
	}
	
	echo $render;
	
}

// Render Features
function render_features ($features, $image_size = 'portrait') {
	
	$render = '<div class="row" style="max-width: 800px; margin: 0 auto">';
	
	foreach($features as $feature) {
		
		$render .= '<div class="col-sm-6">';
		$render .= '<h3>' . $feature['feature_title'] . '</h3>';
		$render .= wp_get_attachment_image( $feature['feature_image']['ID'], $image_size );
		$render .= '<p>' . $feature['feature_description'] . '</p>';
		$render .= '</div>';
	}
	
	echo $render;
	
}

// Render FAQ
function render_faq ($faqs) {
	
	if(!empty($faqs)) {
		$render = '<h2>FAQ</h2>';
		$render .= '<ul>';
		
		foreach($faqs as $faq) {
			
			$render .= '<li">';
			$render .= '<p><strong>Q:</strong> ' . $faq['question'] . '</p>';
			$render .= '<p><strong>A:</strong> ' . $faq['answer'] . '</p>';
			$render .= '<hr/></li>';
		}
		
		echo $render;

	} // end empty faq
}

function get_current_user_membership_ids() { // return array
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

	return $membership_IDs;
}

get_footer(); ?>