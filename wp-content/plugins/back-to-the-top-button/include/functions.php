<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php

// ==================================================================
// output the values into the the page or input in the correct way
// allowing to have double and single quotes inside input
// ==================================================================

function yydev_top_btn_html_output($output_code) {

    $output_code = stripslashes_deep($output_code);
    $output_code = esc_html($output_code);
    return $output_code;

} // function yydev_top_btn_html_output($output_code) {

// ==================================================================
// This function will display error message if there was something wrong
// $error_message will be the name of the string we define and if it's exists
// it will echo the message to the page
// if $display_inline is set to 1 it will have style of display: inline
// ==================================================================

function yydev_top_btn_show_error_message($error_message, $display_inline = "") {
    
    if($display_inline == 1) {
        $display_inline_echo = "display-inline";
    } // if($display_inline == 1) {
    
    if( isset($error_message) ) {
        ?>
        
        <div class="output-data-error-message <?php echo $display_inline_echo; ?>">
            <?php echo $error_message; ?>
        </div>
        
        <?php
    } // if( isset($error) ) {
    
} // function yydev_top_btn_show_error_message($error) {


// ==================================================================
// Cehcking if the checkbox is already set
// ==================================================================

function yydev_top_btn_checkbox_isset($post_value) {
    
    $checkbox_value = '';

    if( isset( $_POST[$post_value] ) ) {
        $checkbox_value = intval($_POST[$post_value]);
    } // if( isset( $_POST[$post_value] ) ) {

    return $checkbox_value;
    
} // function yydev_top_btn_checkbox_isset($error) {

// ================================================
// Echoing Message if it's exists 
// ================================================

function yydev_top_btn_echo_message_if_exists() {
    
    if(isset($_GET['message'])) {
        echo "<div class='output-messsage'> " . htmlentities($_GET['message']) . " </div>";
    } // if(isset($_GET['message'])) {
    
    if(isset($_GET['error-message'])) {
        echo "<div class='error-messsage'><b>Error:</b> " .  htmlentities($_GET['error-message']) . " </div>";
    } // if(isset($_GET['error-message'])) {

} // function yydev_top_btn_echo_message_if_exists() {


function yydev_top_btn_echo_success_message_if_exists($success) {

    if(isset($success) && !empty($success) ) {
        echo "<div class='output-messsage'> " . htmlentities($success) . " </div>";
    } // if(isset($success) && !empty($success) ) {

} // function yydev_top_btn_echo_success_message_if_exists($success) {

function yydev_top_btn_echo_error_message_if_exists($error) {

    if(isset($error) && !empty($error) ) {
        echo "<div class='error-messsage'><b>Error:</b> " .  $error . " </div>";
    } // if(isset($_GET['error-message'])) {

} // function yydev_top_btn_echo_error_message_if_exists() {

// ================================================
// Echoing Message if it's exists 
// ================================================

function yydev_top_btn_find_page_id() {

    global $wpdb;
    $post_id_num = 0;

    // in case of page or blog post
    if( is_single() || is_page() ) {		
    	$post_id_num = get_the_ID();
    } // if( is_single() || is_page() ) {

    // in case of static home page
    if( is_home() ) {
    	$blog_page_id = get_option( 'page_for_posts' );

    	// incase the blog is on the home page
    	if( !empty($blog_page_id) ) {
    		$post_id_num = $blog_page_id;
    	} // if( $blog_page_id ) {

    } // if( !empty($blog_page_id) ) {

    return intval($post_id_num);

} // function yydev_top_btn_find_page_id() {

// ==================================================================
// redirect the page using the path you provided
// ==================================================================

function yydev_top_btn_redirections_page($link) {
	header("Location: {$link}");
	exit;
} // function yydev_top_btn_redirections_page($path) {
