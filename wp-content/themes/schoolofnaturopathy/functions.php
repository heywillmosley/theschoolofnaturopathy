<?php

##### Defines ######
define('FL_CHILD_THEME_DIR', get_stylesheet_directory());
define('FL_CHILD_THEME_URL', get_stylesheet_directory_uri());

$ewsn_side_books = get_field('side_books', 'options');
$ewsn_side_products = get_field('side_products', 'options');

define('EWSN_SIDE_BOOKS', $ewsn_side_books);
define('EWSN_SIDE_PRODUCTS', $ewsn_side_products);
//admin_print($ewsn_side_data);
##### Classes ######
require_once 'classes/FLChildTheme.php';


##### Actions ######
add_action('fl_head', 'FLChildTheme::stylesheet');
add_action('login_head', 'my_custom_login');
add_action('wp_head', 'display_zd_logged_in');

##### Filters ######

##### Shortcodes #####
add_shortcode('ewsn-sidebar', 'ewsn_sidebar_function');

################

if( function_exists('acf_add_options_page') )
    acf_add_options_page();
    


// Custom Portrait Size
add_theme_support( 'post-thumbnails' );
add_image_size( 'portrait', 340, 485, true );

##### Function #####
/* Load custom login page styles */
function my_custom_login() {
echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/login/custom-login-styles.css" />';
}

function display_zd_logged_in(){

    if (is_user_logged_in()) { ?>

    <!-- Start of newhuman1 Zendesk Widget script -->
    <script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=2a88c7d2-77bc-44e5-9925-e63fa468ad46"> </script>

    <?php };

} // end function display_zd_logged_in()

// Sidebar for school
function ewsn_sidebar_function() {

    // Parse and get related content
    $related_books = ewsn_get_related_side_content(EWSN_SIDE_BOOKS);
    $related_products = ewsn_get_related_side_content(EWSN_SIDE_PRODUCTS);

    // Render
    ewsn_render_side_posts($related_books, 'Book');
    ewsn_render_side_posts($related_products, 'Products');


    //admin_print(EWSN_SIDE_BOOKS);
}

function ewsn_get_related_side_content($contents) {

    $related_content = array();
    $i = 0;

    foreach($contents as $content) { // run through books
        foreach($content['display_where'] as $ID) { // run through IDs this book is associated with
            if( $ID == get_the_ID() ) { // find books that match current page ID
                $related_content[$i] = $content;
                $i++;
                break;
            } // end if
        } // end foreach
    } // end foreach

    shuffle( $related_content ); // shuffle sidebar
    return $related_content;
}

function ewsn_render_side_posts($items, $type) {

    wp_enqueue_style( 'side-posts', get_stylesheet_directory_uri() . '/css/side-posts.css' );
    
    $count = count($items);

    if($count > 0 )
        echo "<h4 class='card-header'>Recommended $type(s)</h4>";

    $i = 0;
    foreach($items as $item) {

        if($i == 4) // max amount of items to display
            break;

        $title = $item['title'];
        $author = $item['author'];
        $image = $item['image'];
        $summary = shorten_string_by_words( $item['summary'] );
        $url = $item['url'];

        if( $i == 0 && $type =='Book' ) { // first item display as featured

            $render = "<div class='card sc-card sc-card-md'>";
            if(!empty( $image ) )
                $render .= "<a href='$url' target='_blank'><img src='$image' class='card-img-top' alt='$title'></a>";
            $render .= "<div class='card-body'>";
            $render .= "<a href='$url' target='_blank'><h5 class='card-title'>$title</h5></a>";
            if(!empty($author))
                $render .= "<p class='card-text caption'>$author</p>";
            $render .= "<a href='$url' target='_blank' class='btn btn-primary'>View</a>";
            $render .= "</div>";
            $render .= "</div>";

        } else { // display as thumbnail

            $render = "<div class='media sc-card sc-card-sm'>";
            if(!empty( $image ) )
                $render .= "<a href='$url' target='_blank'><img src='$image' class='mr-3' alt='$title'></a>";
            $render .= "<div class='media-body'>";
            $render .= "<a href='$url' target='_blank'><h5 class='mt-0'>$title</h5></a>";
            if(!empty($author))
                $render .= "<p class='card-text caption'>$author</p>";
            $render .= "<p class='caption'>$summary</p>";
            $render .= "<a href='$url' target='_blank'>View</a>";
            $render .= "</div>";
            $render .= "</div>";

        } // end else
        
        echo $render;

        $i++;
        
    } // end foreach
    
    


}

function shorten_string_by_words($string, $max = 100) {

        if(strlen($string) > $max) {
          // find the last space < $max:
          $shorter = substr($string, 0, $max+1);
          $string = substr($string, 0, strrpos($shorter, ' ')).'...';
        }
        return $string;
}

function admin_print($array) {
    if( current_user_can('administrator') && get_current_user_id() == 1 ) { // 1 is wmosley
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    }
}