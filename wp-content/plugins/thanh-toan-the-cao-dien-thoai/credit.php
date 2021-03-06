<?php

if (!class_exists('MobileCardMemberCredit')) { 
	class MobileCardMemberCredit {

		private static $instance;

		private $credit_meta_key = 'member_credit';	
		private $prefix_key  = 'tttc_';
		private $account_link = '';

		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
				self::$instance->setup_actions();
			}
			return self::$instance;
		}

		private function set_credit($number, $user_ID = 0){
			if(!$user_ID){
			    $user_ID = get_current_user_id();
			}		
			
			if($user_ID == 0) return false;
			update_user_meta( $user_ID, $this->prefix_key . $this->credit_meta_key, $number);
			return true;
		}

		public function get_credit($user_ID = 0){
			if(!$user_ID){
				$user_ID = get_current_user_id();	
			}
			
			if($user_ID == 0) return 0;
			$val = get_user_meta( $user_ID, $this->prefix_key . $this->credit_meta_key, true);		
			if(!$val) return 0;
			else
				return $val;
		}

		public function ck_change_template($located, $template_name, $args, $template_path, $default_path)
		{
			if ($template_name == "myaccount/my-orders.php")
				{
				$located = plugin_dir_path(__FILE__) . 'templates/my-orders.php';
				}
			return $located;
		}

		private function setup_actions() {
			add_action( 'init', array($this,'init_wp'));		
		}

		public function init_wp(){
			$this->setup_shortcode();
			add_filter('wc_get_template', array($this,'ck_change_template'), 10, 5);
			add_action( 'wp_ajax_purchase_credit', array($this,'handle_purchase_credit') );
			add_action( 'wp_ajax_paid_order', array($this,'paid_order_status') );		
			add_action('woocommerce_before_my_account', array($this,'show_account_balance') );
			add_filter( 'manage_users_columns', array($this,'new_modify_user_table' ), 15, 1);
			add_filter( 'manage_users_custom_column', array($this,'new_modify_user_table_row'),10,3 );
			add_action( 'show_user_profile', array($this,'my_show_extra_profile_fields' ));
			add_action( 'edit_user_profile', array($this,'my_show_extra_profile_fields' ));
			add_action( 'personal_options_update', array($this,'my_save_extra_profile_fields' ));
			add_action( 'edit_user_profile_update', array($this,'my_save_extra_profile_fields' ));	
			$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
			if ( $myaccount_page_id ) {
			  $this->account_link = get_permalink( $myaccount_page_id );
			}
		}

		public function my_save_extra_profile_fields( $user_id ) {
			if(current_user_can( 'manage_options' ) ){
			    $this->set_credit($_POST['tttc_credit'], $user_id);
			}		
		}

		public function my_show_extra_profile_fields( $user ) { 
			if(!current_user_can('manage_options' )) return;
			?>
			<h3><?php echo __('S??? d?? th??? c??o', 'membercredit')?></h3>

			<table class="form-table">
				<tr>
					<th><label for="tttc_credit"><?php echo __('S??? d??', 'membercredit')?></label></th>
					<td>
						<input type="text" name="tttc_credit" id="tttc_credit" value="<?php echo esc_attr( $this->get_credit($user->ID) ); ?>" class="regular-text" /><br />
						<span class="description"></span>
					</td>
				</tr>

			</table>
		<?php }

		public function new_modify_user_table( $column ) {
		    $column['credit'] = __('S??? D??', 'membercredit');
		    return $column;
		}

		public function new_modify_user_table_row( $val, $column_name, $user_id ) {
		    $user = get_userdata( $user_id );

		    switch ($column_name) {
		        case 'credit' :
		            return number_format($this->get_credit($user_id), 0, ',', '.') .  ' ??';
		            break;
		        default:
		    }
		    return $val;
		}
		public function show_account_balance(){
			echo '<h3 class="idex-btn">'. __('S??? d?? th??? c??o','membercredit') . '&nbsp: '. do_shortcode('[tttc_member_credit]') .' <a class="btn-napthe" href="'. get_permalink(tttc_find_nap_the_page_id() ) . '">N???p Th???</a></h3>';
		}
		private function setup_shortcode(){
			add_shortcode('tttc_purchase_credit', array($this,'render_purchase_screen') );
			add_shortcode('tttc_member_credit', array($this,'render_member_credit') );
		}

		public function add_credit($number) {
			$current_credit = $this->get_credit();
			$new_credit = $current_credit + $number;
			return $this->set_credit($new_credit);
		}

		public function remove_credit($number) {
			$current_credit = $this->get_credit();
			$new_credit = $current_credit - $number;
			if($new_credit < 0) return false;
			return $this->set_credit($new_credit);
		}



		public function render_purchase_screen(){
		    $path = plugin_dir_url(__FILE__).'includes/images/';
			if(!is_user_logged_in()) {
				echo '<p style="color:red">B???n ch??a ????ng nh???p v??o t??i kho???n. Vui l??ng ????ng nh???p ho???c ????ng k?? <a href="'. $this->account_link .'">t???i ????y<a/></p>';
				return;
			}
			ob_start();
			?>

			<div class="container-payment">	
	          <form name="napthe" action="#" method="post">
				<div id="form_nap_the">
					<div id="status_nap_the" ></div>
					<div id="choose_method">
					 	<label for="92" class="active"><img  src="<?php echo $path; ?>mobifone.jpg" /></label>
				        <label for="93"><img  src="<?php echo $path; ?>vinaphone.jpg" /></label>
				        <label for="107"><img  src="<?php echo $path; ?>viettel.jpg" width="110" height="35" /></label>
				        <label for="999"><img  src="<?php echo $path; ?>gate.jpg" width="110" height="35" /></label>
				        <div id="method_radio">
				            <input type="radio" name="select_method" checked="true" value="VMS" id="92"  />
				            <input type="radio"  name="select_method" value="VNP" id="93" />
				            <input type="radio"  name="select_method" value="VIETTEL" id="107" />
				            <input type="radio"  name="select_method" value="GATE" id="999" />
				        </div>
				        <?php wp_nonce_field('purchase_credit'); ?>
				    </div>
			        <p>
			            <label for="txtSoPin" class="icon-user"> M?? Th???
			                <span class="required">*</span>
			            </label>
			            <input type="text" id="txtSoPin" name="txtSoPin" required="required" placeholder="" />
			        </p>
			        <p>
			            <label for="txtSoSeri" class="icon-user"> Series Th???
			                <span class="required">*</span>
			            </label>
			            <input type="text" id="txtSoSeri" name="txtSoSeri" required="required" placeholder="" />
			        </p>
			        <p>
			        	<b>S??? d??:</b> <?php echo do_shortcode('[tttc_member_credit]' );?>
			        </p>
			        <p>
			        	<input type="submit" id="ttNganluong" name="NLNapThe" value="N???p Th???"  /> 
			        </p>
				        <?php 
				        	$request_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
				        	$param = "";
				        	if(strpos($request_url, "order-received")) $param = "order-received";
				        	if(strpos($request_url, "view-order")) $param = "view-order";				        	

				        	$id = false;
							if ($param != "") {						    
							    $begin = strpos($request_url, $param . '/') + strlen($param) + 1;
							    $end = strpos($request_url, "/", $begin + 1) ;							    	    
							    $id = substr($request_url, $begin, $end - $begin);

							}
				            $id = $id ? $id : (isset($_GET['order_id']) ? $_GET['order_id'] : '');
				            $id = $id ? $id : (isset($_GET["order-received"]) ? $_GET["order-received"] : "");
				            $id = $id ? $id : (isset($_GET["view-order"]) ? $_GET["view-order"] : "");
				            
				            if(isset($id)) {
				                $order = new WC_Order($id);
				                $items = $order->get_items();
				                $money = do_shortcode('[tttc_member_credit]');
				                $order_total  = $order->get_total();
				                ?>
				                <div id="checkout_the_cao" style="<?php echo $id ? '' : 'display: none;'; ?>">
				                <p style="text-align: center;">S??? d?? th??? c??o l???n h??n ho???c b???ng gi?? tr??? ????n h??ng b???n s??? c?? th??? Thanh To??n</p>
				                	<h5>????n H??ng: #<?php echo $id; ?></h5>
						            <ul>
						            	<?php 
						            		foreach ($items as $key => $value) {
						            			echo '<li>'. $value["name"] .'</li>';
						            		}
						            	?>
						            </ul>
						            <b>Gi?? ti???n:  <?php echo wc_price($order_total); ?></b>

						            <input type="hidden" id="idex-hidden-id" name="idex-hidden-id" value="<?php echo $id; ?>" />
						            <?php
						                if($this->get_credit() < $order_total):
						            ?>
						                <input type="submit" id="idex-checkout" name="none-money" value="Thanh To??n" disabled  />
									<?php else: ?>
									    <input type="submit" id="idex-checkout" name="idex-checkout" value="Thanh to??n"  />
									<?php endif; ?>
				        		</div>
				                <?php 
				            }
				        ?>
				    
				</div>
				 <input type="hidden" id="amount-idex" name="amount-idex" value="<?php echo $this->get_credit(); ?>" />
				</form>
	        </div>
	        
	        <script type="text/javascript">
	          $body = jQuery("body");
	          jQuery(document).on({
	                ajaxStart: function() { $body.addClass("loading");    },
	                ajaxStop: function() { $body.removeClass("loading"); }
	          });
	            jQuery(document).ready(function($) {
	        
	                jQuery('#choose_method label').click(function(){
	                    jQuery('#choose_method label').removeClass('active');
	                    jQuery(this).addClass('active');      
	                });

		            jQuery('#ttNganluong').click(function(){ 
		        		var data = {
							'action': 'purchase_credit',
							'telcoCode': jQuery('input[name=select_method]:checked').val(),
							'cardSerial': jQuery('#txtSoSeri').val(),
							'cardPin': jQuery('#txtSoPin').val(),
							'promote_key': jQuery('#promote_key').val(),
							'order_id': jQuery('#idex-hidden-id').val(),
							'_wpnonce': jQuery('input[name=_wpnonce]').val(),
						};
					
						function isEmpty(str)
		                {
		                    str = str || null;
		                    return (typeof str == "undefined" || str == null);
		                }
						
						
						if (isEmpty(data.cardSerial)){
						    alert('Vui l??ng nh???p m?? serial');
						    return false;
						}
						
						if (isEmpty(data.cardPin)){
						    alert('Vui l??ng nh???p m?? th??? c??o');
						    return false;
						}

						jQuery.post( member_credit.ajax, data, function( response ) {
					  		jQuery('#status_nap_the').hide();
							jQuery('#status_nap_the').removeClass('fail');
							jQuery('#status_nap_the').removeClass('success');
							if (response.byOk == true){
							    jQuery("#idex-checkout").removeAttr("disabled");
							}
							if(response.status == 1){
								jQuery('#status_nap_the').addClass('success');						
								jQuery('.amount-credit-idex').text(response.credit.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.")+ ' ??');
							}else{
								jQuery('#status_nap_the').addClass('fail');
							}


							jQuery('#status_nap_the').html(response.message);
							jQuery('#status_nap_the').fadeIn('slow');
						});
	                          
						
						
						return false;
	        	});
	        	
	        	// Check out Button
	        	jQuery('#idex-checkout').click(function(){
	        	    var data_input = {
	        	       'action': 'paid_order',
	        	       'checkout_id': jQuery('#idex-hidden-id').val(),
	        	    };
	        	    
	        	jQuery.post(member_credit.ajax, data_input, function(response) {
					alert(response.message);
					if(response.status == 1){
						window.location = member_credit.account_link;
					} 
				});
	            return false;
	                
	        	});
				
			});

	        </script>
			<?php
			return ob_get_clean();
		}
		
		public function paid_order_status(){
		    $id = $_POST['checkout_id'];       
	        
	        global $woocommerce;
	        $customer_order = new WC_Order($id);
			$order_total = $customer_order->get_total();
			$credit_remain = $this->get_credit();
			
			// Ki???m tra n???u tr???ng th??i ???? thanh to??n th?? kh??ng tr??? ti???n khi click v??o
			$status = $customer_order->post_status;
			if($credit_remain < $order_total){
			    $result = array('status' => 0, 'message' => "S??? d?? trong t??i kho???n c???a b???n kh??ng ????? ????? thanh to??n ????n h??ng n??y");
			} elseif ($status == 'wc-completed') {
			    $result = array('status' => 2, 'message' => "B???n ???? thanh to??n cho ????n h??ng n??y r???i");
			} else {
				$this->remove_credit($order_total);
				$customer_order->add_order_note( __( 'Done', 'membercredit' ) );
				$customer_order->update_status('completed');
				$customer_order->reduce_order_stock();
				$woocommerce->cart->empty_cart();
				$result = array('status' => 1, 'message' => "B???n ???? thanh to??n th??nh c??ng ????n h??ng n??y!");
			}
	        wp_send_json($result);
	        
	        wp_die(); // this is required to terminate immediately and return a proper response
		}

		public function render_member_credit(){
			return '<a class="amount-credit-idex" href="'. $this->account_link .'"><span class="timer counter" data-to="'.$this->get_credit().'">'. $this->get_credit() . '</span>' . get_woocommerce_currency_symbol() .'  </a>';
		}

		public function handle_purchase_credit(){
			$retrieved_nonce = $_REQUEST['_wpnonce'];
			if (!wp_verify_nonce($retrieved_nonce, 'purchase_credit' ) ) die( 'Failed security check' );
	        $result = array('status' => 0, 'message' => '');
	        $soseri = $_POST['cardSerial'];
	        $sopin = $_POST['cardPin'];
	        $type_card = $_POST['telcoCode'];

	    	if ($soseri == "" ) {
	    	      $result = array('status' => 0, 'message' => 'Vui l??ng nh???p S??? Seri');
	    	      die();
	    	}
	    	if ($sopin == "" ) {
	    	     $result = array('status' => 0, 'message' => 'Vui l??ng nh???p m?? th???');
	    	     die();
	    	}
	    	

	    	$getway = new WC_Gateway_MegaCard();
	    	$return_code = -1;
			$return_amount = 0;
			$merchant_id = $getway->merchantID;
			$merchant_email = $getway->merchantEmail;
			$merchant_user = $getway->username;
			$merchant_password = $getway->password;

			switch ($getway->getway) {
				case 'nganluong':
					include ('modules/nganluong-config.php');		
					include('modules/nganluong-getway.php');
					
					NganLuongConfig::$_MERCHANT_ID = $getway->merchantID;
					NganLuongConfig::$_EMAIL_RECEIVE_MONEY = $getway->merchantEmail;
					NganLuongConfig::$_MERCHANT_PASSWORD = $getway->password;
					
					$call = new NganLuongMobiCard();
			    	$rs = new NganLuongResult();
			    	$ref_code ='plugin the cao' ;
			    	$rs = $call->CardPay($sopin,$soseri,$type_card,$ref_code,"Full name","Mobile","Email");

			    	$return_code = $rs->error_code;
			    	$return_amount = $rs->card_amount;
			    	$return_message = '';
					break;
				case 'doithe':
					switch ($type_card) {
						case 'VMS': $type_card = 'vms'; break; 
						case 'VNP': $type_card = 'vnp'; break; 
						case 'VIETTEL': $type_card = 'vtt'; break;
						case 'GATE': $type_card = 'gate'; break; 
						default: break; 
					}

					$doi_the_api = sprintf("http://api.doithe.vn:8001/api/charge?email=%s&provider=%s&serial=%s&pin=%s&key=%s",$merchant_email, $type_card, $soseri,$sopin, $merchant_password );
					$json = file_get_contents($doi_the_api); 
					$json_decode = json_decode($json, true); 
					$return_code = $json_decode['status'];
			    	$return_amount = $json_decode['amount'];
			    	$return_message = $json_decode['message'];
					break;
				default:
					die('C???ng th??? c??o ch??a h??? tr???');
					break;
			}
			
			
			

		
	    	$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : '';
	    	if($return_code == '0' || $return_code == '00') {	
	    	   $getway = new WC_Gateway_MegaCard(); 
		   		$this->add_credit($return_amount);
		   		$message = 'B???n ???? n???p th??nh c??ng:' . wc_price($return_amount);    
	    	   $result = array('status' => 1, 'message' => $message, 'credit' => $this->get_credit(), );
	    	}// nap card that bai
	    	else {
	    	   $result = array('status' => 0, 'message' => 'N???p th??? kh??ng th??nh c??ng. Vui l??ng ki???m tra l???i M?? Th???/ M?? Series/ Nh?? M???ng!<br>' . $return_message , "api_code" => $return_code);
	    	   if($order_id){
	    	   		$customer_order = new WC_Order($order_id);
	    	   		$customer_order->add_order_note( __( 'N???p ti???n l???i: '. $return_code , 'membercredit' ) );	
	    	   }
	    	   
	    	}
	        
	        if($order_id){
	        	$customer_order = new WC_Order($order_id);
		    	$order_total = $customer_order->get_total();	
		    	if($order_total <= $this->get_credit()) {
		            $result['byOk'] = true;
		        }
	        } 
	        wp_send_json($result);
	        wp_die();
	    	
		}

	}
}

add_action('get_footer', 'tttc_loading_dom');

function tttc_loading_dom(){
	?> 	<div class="modal"></div> <?php
}