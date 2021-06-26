<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php

include('settings.php'); // Load the files to get the databse info

// ----------------------------------------------
// checking if the data existing on the db and 
// if not we will create it with initial settings
// ----------------------------------------------    

if( !get_option($wp_options_name) ) {

    // ----------------------------------------------
    // getting all the values and clear data
    // ----------------------------------------------    

    $display_button_checkbox = 1;
    $background_color = '#09547c';
    $background_position = '50% 43%';
    $button_width = 45;
    $button_height = 45;
    $border_radius = '50%';
    $horizontal_position = 'right';
    $horizontal_spacing = '30px';
    $vertical_position = 'bottom';
    $vertical_spacing = '30px';
    $button_z_index = '9999';
    $button_border = '2px solid #fff';
    $hide_button_on_desktop = 0;
    $hide_button_on_mobile = 0;
    $mobile_width = 960;
    $mobile_button_position_checkbox = 0;
    $mobile_horizontal_position = 'right';
    $mobile_horizontal_spacing = '30px';
    $mobile_vertical_position = 'bottom';
    $mobile_vertical_spacing = '30px';
    $smooth_scrolling_checkbox = 1;

    // getting the image arrow icon
    $icon_image_url = plugin_dir_url(dirname(__FILE__)) . 'images/back-to-top.png';

    $exclude_option = '';
    $exclude_ids = '';

    // ----------------------------------------------
    // insert the data into an array
    // ----------------------------------------------  

    $plugin_data_array = array(
        'display_button_checkbox' => $display_button_checkbox,
        'background_color' => $background_color,
        'background_position' => $background_position,
        'button_width' => $button_width,
        'button_height' => $button_height,
        'border_radius' => $border_radius,
        'horizontal_position' => $horizontal_position,
        'horizontal_spacing' => $horizontal_spacing,
        'vertical_position' => $vertical_position,
        'vertical_spacing' => $vertical_spacing,
        'button_z_index' => $button_z_index,
        'button_border' => $button_border,
        'hide_button_on_desktop' => $hide_button_on_desktop,
        'hide_button_on_mobile' => $hide_button_on_mobile,
        'mobile_width' => $mobile_width,
        'mobile_button_position_checkbox' => $mobile_button_position_checkbox,
        'mobile_horizontal_position' => $mobile_horizontal_position,
        'mobile_horizontal_spacing' => $mobile_horizontal_spacing,
        'mobile_vertical_position' => $mobile_vertical_position,
        'mobile_vertical_spacing' => $mobile_vertical_spacing,
        'smooth_scrolling_checkbox' => $smooth_scrolling_checkbox,

        'icon_image_url' => $icon_image_url,

        'exclude_option' => $exclude_option,
        'exclude_ids' => $exclude_ids,
    ); // $creating_data_array = array(

    // ----------------------------------------------
    // creating a value with all the array data
    // ----------------------------------------------  
    
    $array_key_name = "";
    $array_item_value = "";
    
    foreach($plugin_data_array as $key=>$item) {
        $array_key_name .= "####" . $key;
    	$array_item_value .= "####" . $item;
    } // foreach($medical_form_array as $key=>$item) {

    // ----------------------------------------------
    // inserting all the data to datbase
    // ----------------------------------------------  

    $plugin_data = $array_key_name . "***" . $array_item_value;
    $plugin_data = $plugin_data;

    // update optuon on the database into wp_options
    update_option($wp_options_name, $plugin_data);    

} // if( !get_option($wp_options_name) ) {