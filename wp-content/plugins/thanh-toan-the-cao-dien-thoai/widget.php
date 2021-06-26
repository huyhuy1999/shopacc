<?php

if (!class_exists('MobileCardMemberCredit_Widget')) {
	/*Widget for Display Account Balance*/
	class MobileCardMemberCredit_Widget extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 */
		function __construct() {
			parent::__construct(
				'member_credit_widget', // Base ID
				__( 'Thẻ Cào', 'membercredit' ), // Name
				array( 'description' => __( 'Hiển thị số dư, link nạp thẻ, link tài khoản', 'membercredit' ), ) // Args
			);
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {
			echo $args['before_widget'];
			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
			}
			// set account link
			$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
			$account_link = '';
			if ( $myaccount_page_id ) {
			  $account_link = get_permalink( $myaccount_page_id );
			}

			$member_credit = MobileCardMemberCredit::get_instance();
			$user_balance = '';
			if(is_user_logged_in()){
				$user_balance = wc_price($member_credit->get_credit());
			}else{
				$user_balance = sprintf('<a href="%s">', $account_link ) . __('Please login', 'membercredit') .'</a>';
			}
			$card_getway = new WC_Gateway_MegaCard();
			


			echo '<ul>';
			echo sprintf('<li>'. __('Số Dư:', 'membercredit') .' %s</li>', $user_balance);
			echo sprintf('<li>'. __('<a href="%s">Nạp Thẻ</a> | <a href="%s">Xem Tài Khoản</a>', 'membercredit') .'</li>', get_permalink(tttc_find_nap_the_page_id() ), $account_link);		
			echo '</ul>';
			echo $args['after_widget'];
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {
			$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Account Info', 'membercredit' );
			?>
			<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
			</p>
			<?php 
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

			return $instance;
		}

	}
}

//set up widget
add_action('widgets_init',
	create_function('', 'return register_widget("MobileCardMemberCredit_Widget");')
);