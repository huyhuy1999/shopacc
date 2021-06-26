<?php
/*
Plugin Name: Thanh Toan The Cao Dien Thoai
Plugin URI: http://nghedayhoconline.com
Description: Tích hợp thanh toán thẻ cào vào website, hổ trợ số dư tiền nạp vào. Shortcode form nạp thẻ: [tttc_purchase_credit], Shortcode hiện số dư [tttc_member_credit], có hổ trợ widget số dư.
Version: 1.4.2
Author: Đặng Ngọc Bình
Author URI: http://dangngocbinh.com
Text Domain: membercredit
*/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    require('credit.php');
	require ('card-getway.php');
	require ('widget.php');
	$tttc_instant = MobileCardMemberCredit::get_instance();
	
	function tttc_enqueue_scripts() {
	    wp_enqueue_style( 'credit', plugin_dir_url(__FILE__) . '/css/credit.css' );
	    wp_enqueue_script( 'mc-script', plugins_url( '/js/script.js', __FILE__ ), array('jquery') );

		$tttc_getway = new WC_Gateway_MegaCard();
		wp_localize_script( 'mc-script', 'member_credit',
	            array( 'ajax' => admin_url( 'admin-ajax.php' ), 'account_link' => get_permalink( get_option('woocommerce_myaccount_page_id') )) );
	}
	add_action( 'wp_enqueue_scripts', 'tttc_enqueue_scripts' );

	function tttc_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'membercredit' );
		load_plugin_textdomain( 'membercredit', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}

	tttc_plugin_textdomain();

	register_activation_hook( __FILE__, 'tttc_activate_thanh_toan_the_cao' );
	register_deactivation_hook( __FILE__, 'tttc_deactivate_thanh_toan_the_cao' );
}

function tttc_woocommerce_installed_notice() {
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ){
	    $class = 'notice notice-error';
		$message = __( 'Plugin Thanh Toán Thẻ Cào cần Woocommerce để hoạt động. Vui lòng kích hoạt Woocommerce.', 'sample-text-domain' );
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
    }
}
add_action( 'admin_notices', 'tttc_woocommerce_installed_notice' );


function tttc_activate_thanh_toan_the_cao(){
	 
	
	if ( !tttc_is_nap_the_page_exist() ) {
		$page_id = tttc_create_nap_the_page();
		$link = get_the_permalink($page_id);
	}

}

function tttc_deactivate_thanh_toan_the_cao(){}

function tttc_is_nap_the_page_exist(){
	$query = new WP_Query( array('post_type' => 'page', 's' => '[tttc_purchase_credit]' ) );
	if ( $query->have_posts() ) {
		return true;
	}
	return false;
}

function tttc_find_nap_the_page_id(){
	$query = new WP_Query( array('post_type' => 'page', 's' => '[tttc_purchase_credit]' ) );
	if ( $query->have_posts() ) {
		$query->the_post();
		return get_the_ID();
	}
	return "";
}

function tttc_create_nap_the_page(){
	$nap_the_page = array(
	    'post_type' => 'page',
	    'post_title' => 'Nạp Thẻ Cào',
	    'post_content' => '[tttc_purchase_credit]',
	    'post_status' => 'publish',
	    'post_author' => get_current_user_id(),
	    'post_slug' => 'nap-the'
    );
    
    return $page_id = wp_insert_post($nap_the_page);
}

function tttc_plugin_add_settings_link( $links ) {
    $settings_link = '<a href="admin.php?page=wc-settings&tab=checkout&section=the_cao_dien_thoai">' . __( 'Settings' ) . '</a>';
    $feedback_link = '<a href="mailto:dangngocbinh.dnb@gmail.com">' . __( 'Feedback' ) . '</a>';
    array_push( $links, $settings_link );
    array_push( $links, $feedback_link );
  	return $links;
}

add_filter( "plugin_action_links_" . plugin_basename( __FILE__ ), 'tttc_plugin_add_settings_link' );

add_action('woocommerce_thankyou_the_cao_dien_thoai','tttc_woocommerce_thankyou_the_cao_dien_thoai' );
function tttc_woocommerce_thankyou_the_cao_dien_thoai($order_id){
	$order = new WC_Order( $order_id );

	if($order->status != 'completed'){
		echo do_shortcode("[tttc_purchase_credit]" );
	}
	
}

add_action( 'woocommerce_view_order', 'tttc_show_checkout_thecao_form', 5 );

function tttc_show_checkout_thecao_form($order_id){
	$current_getway = get_post_meta( $order_id, '_payment_method', true );
	$getway = new WC_Gateway_MegaCard();

	$order = new WC_Order( $order_id );

	if($current_getway == $getway->id && $order->status != 'completed'){
		echo do_shortcode("[tttc_purchase_credit]" );
	}
}