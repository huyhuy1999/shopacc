<?php
add_action( 'plugins_loaded', 'tttc__init_mobile_cart_getway' );

function tttc__init_mobile_cart_getway() {
	class WC_Gateway_MegaCard extends WC_Payment_Gateway {
		function __construct(){
			// The global ID for this Payment method
			$this->id = "the_cao_dien_thoai";

			// The Title shown on the top of the Payment Gateways Page next to all the other Payment Gateways
			$this->method_title = __( "Nạp Thẻ Cào Điện Thoại", 'membercredit' );

			// The description for this Payment Gateway, shown on the actual Payment options page on the backend
			$this->method_description = __( "Mua hàng bằng thẻ cào điện thoại. Click <a href='https://youtu.be/fQ0EuoXzekk' target='blank'> -->> xem hướng dẫn</a>", 'membercredit' );

			// The title to be used for the vertical tabs that can be ordered top to bottom
			$this->title = __( "Nạp Thẻ Cào Điện Thoại", 'membercredit' );

			// If you want to show an image next to the gateway's name on the frontend, enter a URL to an image.
			$this->icon = null;

			// Bool. Can be set to true if you want payment fields to show on the checkout 
			// if doing a direct integration, which we are doing in this case
			$this->has_fields = true;

			// Supports the default credit card form
			//$this->supports = array( 'default_credit_card_form' );

			// This basically defines your settings which are then loaded with init_settings()
			$this->init_form_fields();

			// After init_settings() is called, you can get the settings and load them into variables, e.g:
			// $this->title = $this->get_option( 'title' );
			$this->init_settings();
			
			// Turn these settings into variables we can use
			foreach ( $this->settings as $setting_key => $value ) {
				$this->$setting_key = $value;
			}
			
			// Save settings
			if ( is_admin() ) {
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			}
			apply_filters('woocommerce_credit_card_form_fields', array($this, 'phonecard_payment_form') );		
		}

		public function phonecard_payment_form($default){
			$default = array();
			return $default;
		}

		public function get_default_link_add_credit(){
			if(tttc_is_nap_the_page_exist() && tttc_find_nap_the_page_id() != "" ){
				return get_permalink(tttc_find_nap_the_page_id());
			}
			return "";
		}
		public function payment_fields(){
			if(is_user_logged_in()){
				echo do_shortcode("Số Dư: [tttc_member_credit]" );
				echo "<p>Nếu số dư không đủ. Bạn cần Nạp Thẻ Cào (ở bước tiếp theo) để mua hàng.</p>";
			}else{
				echo "Nhấn Đặt Hàng sau đó Nạp Thẻ vào tài khoản để mua hàng.";
			}
		}
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled' => array(
					'title'		=> __( 'Bật / Tắt', 'membercredit' ),
					'label'		=> __( 'Bật hoặc Tắt cổng thanh toán này', 'membercredit' ),
					'type'		=> 'checkbox',
					'default'	=> 'no',
				),
				'title' => array(
					'title'		=> __( 'Tên Cổng Thanh Toán', 'membercredit' ),
					'type'		=> 'text',
					'desc_tip'	=> __( 'Cho phép mua hàng bằng thẻ cào điện thoại.', 'membercredit' ),
					'default'	=> __( 'Thanh Toán Thẻ Cào', 'membercredit' ),
				),
				'description' => array(
					'title'		=> __( 'Mô tả', 'membercredit' ),
					'type'		=> 'textarea',
					'desc_tip'	=> __( 'Mô tả cổng thanh toán, dùng HTML để minh họa tốt nhất', 'membercredit' ),
					'default'	=> __( 'Thanh toán bằng thẻ cào Vietel, Mobifone, Vinaphone.', 'membercredit' ),
					'css'		=> 'max-width:350px;'
				),
				'getway' => array(
					'title'		=> __( 'Cổng Dịch Vụ', 'membercredit' ),
					'type'		=> 'select',
					'desc_tip'	=> __( 'Getway service you are using.', 'membercredit' ),
					'default'	=> __( 'nganluong', 'membercredit' ),
					'css'		=> 'max-width:350px;',
					'options' => array(
				          'nganluong' => 'Ngân Lượng',
				          'doithe' => 'DoiThe.vn',
				          'baokim' => 'Bảo Kim (Comming soon)',
				          'vtc365' => 'VTC 365 (Comming soon)',
				          'gamebank' => 'Game Bank (Comming soon)',
				          'onepay' => 'One Pay (Comming soon)',
				     )
				),
				'merchantID' => array(
					'title'		=> __( 'Merchant ID', 'membercredit' ),
					'type'		=> 'text',
					'desc_tip'	=> __( '', 'membercredit' ),
					'default'	=> __( '', 'membercredit' ),
					'css'		=> 'max-width:350px;'
				),
				'merchantEmail' => array(
					'title'		=> __( 'Merchant Email', 'membercredit' ),
					'type'		=> 'text',
					'desc_tip'	=> __( '', 'membercredit' ),
					'default'	=> __( '', 'membercredit' ),
					'css'		=> 'max-width:350px;'
				),
				'username' => array(
					'title'		=> __( 'API Username', 'membercredit' ),
					'type'		=> 'text',
					'desc_tip'	=> __( '', 'membercredit' ),
					'default'	=> __( '', 'membercredit' ),
					'css'		=> 'max-width:350px;'
				),
				'password' => array(
					'title'		=> __( 'API Password', 'membercredit' ),
					'type'		=> 'password',
					'desc_tip'	=> __( '', 'membercredit' ),
					'default'	=> __( '', 'membercredit' ),
					'css'		=> 'max-width:350px;'
				),
			);		
		}

		public function process_payment( $order_id ) {
			global $woocommerce;
			$customer_order = new WC_Order( $order_id );
			$order_total = $customer_order->get_total( );
			$member_credit = MobileCardMemberCredit::get_instance();
			$credit_remain = $member_credit->get_credit();

			if($credit_remain < $order_total){
				//wc_add_notice( '<div class="error-message">'. sprintf('Số dư trong tài khoản của bạn không đủ để thanh toán. Vui lòng nạp thêm tiền vào tài khoản <a href="%s?order_id=%s">tại đây</a>.Hoặc có thể nạp tiền trong trang thông tin tài khoản của bạn <a href="">Tại đây<a>', $this->linkAddCredit, $order_id, get_permalink( get_option('woocommerce_myaccount_page_id') )) . '</div>', 'error' );	
			}else{
				$member_credit->remove_credit($order_total);
				$customer_order->add_order_note( __( 'Done', 'membercredit' ) );
				$customer_order->update_status('completed');
				$customer_order->reduce_order_stock();
				$woocommerce->cart->empty_cart();
				return array(
					'result'   => 'success',
					'redirect' => $this->get_return_url( $customer_order ),
				);
			}
			return array(
					'result'   => 'success',
					'redirect' => $this->get_return_url( $customer_order ),
				);
		}
	}
}


function tttc_add_your_gateway_class( $methods ) {
	$methods[] = 'WC_Gateway_MegaCard'; 
	return $methods;
}

add_filter( 'woocommerce_payment_gateways', 'tttc_add_your_gateway_class' );

