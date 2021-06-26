<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
/*
Plugin Name: YYDevelopment - Back To The Top Button
Plugin URI:  https://www.yydevelopment.com/yydevelopment-wordpress-plugins/
Description: Simple plugin that allow you add back to top button to your site
Version:     2.1.0
Author:      YYDevelopment
Author URI:  https://www.yydevelopment.com/
*/

include_once('include/settings.php');
require_once('include/functions.php');

// ================================================
// Creating Database when the plugin is activated
// ================================================


function yydev_top_btn_create_database() {
    
    require_once('include/install.php');
        
} // function yydev_top_btn_create_database() {

register_activation_hook(__FILE__, 'yydev_top_btn_create_database');

// ================================================
// display the plugin we have create on the wordpress
// post blog and pages
// ================================================

// function that will output the code to the page
function output_yydev_top_btn() {

    include('include/style.php');
    include('include/scripts.php');
    include('include/admin-output.php');

} // function output_yydev_top_btn() {

// -----------------------------------------------
// load the page into settings page
// -----------------------------------------------

// in case of settings menu loading
function register_yydev_top_btn_page() {
    add_options_page( 'Back To Top Button', "Back To Top Button", 'manage_options', 'yydev-top-btn', 'output_yydev_top_btn');
} // function register_yydev_top_btn_page() {

add_action('admin_menu', 'register_yydev_top_btn_page');

// ================================================
// Add settings page to the plugin menu info
// ================================================

function yydev_top_btn_add_settings_link( $actions, $plugin_file ) {
	static $plugin;

    if (!isset($plugin)) { $plugin = plugin_basename(__FILE__); }

	if ($plugin == $plugin_file) {
            $admin_page_url = esc_url( menu_page_url( 'yydev-top-btn', false ) );
			$settings = array('settings' => '<a href="' . $admin_page_url . '">Settings</a>');
            $donate = array('donate' => '<a target="_blank" href="https://www.yydevelopment.com/coffee-break/?plugin=back-to-the-top-button">Donate</a>');
            $actions = array_merge($settings, $donate, $actions);
    } // if ($plugin == $plugin_file) {

    return $actions;
} //function yydev_top_btn_add_settings_link( $actions, $plugin_file ) {

add_filter( 'plugin_action_links', 'yydev_top_btn_add_settings_link', 10, 5 );

// ================================================
// output the data into the page front end
// ================================================

if( !is_admin() ) {
    include('include/front-end-output.php');
} // if( !is_admin() ) {

// ================================================
// including admin notices flie
// ================================================

if( is_admin() ) {
	include_once('notices.php');
} // if( is_admin() ) {