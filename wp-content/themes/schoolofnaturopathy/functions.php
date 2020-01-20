<?php
##### Defines ######
define('FL_CHILD_THEME_DIR', get_stylesheet_directory());
define('FL_CHILD_THEME_URL', get_stylesheet_directory_uri());

##### Classes ######
require_once 'classes/FLChildTheme.php';

##### Actions ######
add_action('fl_head', 'FLChildTheme::stylesheet');

// Action to add custom styles to WP Login page
add_action('login_head', 'my_custom_login');
function my_custom_login() {
echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/login/custom-login-styles.css" />';
}

// Action to display Zendesk Help for logged in users only
add_action('wp_head', 'display_zd_logged_in');
function display_zd_logged_in() {
    if (is_user_logged_in())
        echo "<script id='ze-snippet' src='https://static.zdassets.com/ekr/snippet.js?key=2a88c7d2-77bc-44e5-9925-e63fa468ad46'> </script>";
}

// Action to redirect to programs after login
add_action( 'wp_login', 'redirect_login' );
function redirect_login() {
    wp_redirect('/programs');
    exit();
}

// Action to redirect to home after logout
add_action( 'wp_logout', 'redirect_logout' );
function redirect_logout() {
    wp_redirect('/');
    exit();
}

################

if( function_exists('acf_add_options_page') )
    acf_add_options_page();
    
// Custom Portrait Size
add_theme_support( 'post-thumbnails' );
add_image_size( 'portrait', 340, 485, true );

##### Function #####


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