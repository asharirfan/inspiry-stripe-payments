<?php
/**
 * Shortcodes Class
 *
 * This class is used to initialize the shortcodes of
 * this plugin.
 *
 * @since    1.0.0
 * @package  ISP
 */


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * ISP_Shortcodes.
 *
 * Introduces shortcodes for this plugin.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'ISP_Shortcodes' ) ) {

	class ISP_Shortcodes {

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public static function init() {

			add_shortcode( 'isp_button', array( __CLASS__, 'payment_button' ) );

		}

		/**
		 * payment_button.
		 *
		 * @since 1.0.0
		 */
		public static function payment_button( $atts ) {

			$args = shortcode_atts( array(
				'alipay'        => '',
				'amount'        => '',
				'billing'       => '',
				'bitcoin'       => '',
				'currency'      => '',
				'desc'          => '',
				'email'         => '',
				'label'         => '',
				'remember_user' => '',
				'shipping'      => ''
			), $atts );

			$isp_options = get_option( 'isp_settings' );

			// Set currency code.
			if ( isset( $args[ 'currency' ] ) && ! empty( $args[ 'currency' ] ) ) {
				$currency_code = $args[ 'currency' ];
			} else {
				$currency_code = $isp_options[ 'currency_code' ];
			}

			// Amount being charged.
			$amount = $args[ 'amount' ];
			if ( ! empty( $amount ) ) {
				$amount = $amount * 100;
			} else {
				$amount = 0;
			}

			// Check if we are using test mode.
			if ( isset( $isp_options[ 'test_mode' ] ) && $isp_options[ 'test_mode' ] ) {
				$publishable_key = $isp_options[ 'test_publishable_key' ];
			} else {
				$publishable_key = $isp_options[ 'live_publishable_key' ];
			}

			// Button Label.
			if ( isset( $args[ 'label' ] ) && ! empty( $args[ 'label' ] ) ) {
				$button_label = $args[ 'label' ];
			} else {
				$button_label = $isp_options[ 'button_label' ];
				if ( empty( $button_label ) ) {
					$button_label = 'Pay with Card';
				}
			}

			// Description.
			if ( isset( $args[ 'desc' ] ) && ! empty( $args[ 'desc' ] ) ) {
				$desc = $args[ 'desc' ];
			}

			// Email.
			if ( isset( $args[ 'email' ] ) && ! empty( $args[ 'email' ] ) ) {
				$email = $args[ 'email' ];
			}

			if ( isset( $_GET[ 'payment' ] ) && 'paid' == $_GET[ 'payment' ] ) {
				echo '<p class="success">' . __( 'Thank you for your payment.', 'inspiry-stripe-payments' ) . '</p>';
			} elseif ( isset( $_GET[ 'payment' ] ) && 'failed' == $_GET[ 'payment' ] ) {
				echo '<p class="error">' . __( 'Error: Payment was not charged.', 'inspiry-stripe-payments' ) . '</p>';
			} else {

				?>
				<form action="" method="POST" class="stripe-button">
				<script
						src="https://checkout.stripe.com/checkout.js" class="stripe-button"
						data-key="<?php echo esc_attr( $publishable_key ); ?>"
						data-amount="<?php echo esc_attr( $amount ); ?>"
						data-name="<?php echo get_bloginfo( 'name' ); ?>"
						data-currency="<?php echo esc_attr( $currency_code ); ?>"
					<?php echo ( ! empty( $desc ) ) ? 'data-description=" ' . $desc . ' "' : ''; ?>
					<?php echo ( ! empty( $email ) ) ? 'data-email="' . $email . '"' : ''; ?>
						data-locale="auto"
						data-billing-address="<?php echo ( isset( $args[ 'billing' ] ) && ( 'true' == $args[ 'billing' ] ) ) ? 'true' : 'false'; ?>"
						data-shipping-address="<?php echo ( isset( $args[ 'shipping' ] ) && ( 'true' == $args[ 'shipping' ] ) ) ? 'true' : 'false'; ?>"
						data-label="<?php _e( $button_label, 'inspiry-stripe-payments' ); ?>"
						data-bitcoin="<?php echo ( isset( $args[ 'bitcoin' ] ) && ( 'true' == $args[ 'bitcoin' ] ) ) ? 'true' : 'false'; ?>"
						data-alipay="<?php echo ( isset( $args[ 'alipay' ] ) && ( 'true' == $args[ 'alipay' ] ) ) ? 'true' : 'false'; ?>"
						data-allow-remember-me="<?php echo ( isset( $args[ 'remember_user' ] ) && ( 'true' == $args[ 'remember_user' ] ) ) ? 'true' : 'false'; ?>">
				</script>
				<input type="hidden" name="action" value="isp_shortcode_payment"/>
				<input type="hidden" name="amount" value="<?php echo esc_attr( $amount ); ?>"/>
				<input type="hidden" name="currency_code" value="<?php echo esc_attr( $currency_code ); ?>"/>
				<input type="hidden" name="isp_shortcode_nonce" value="<?php echo wp_create_nonce( 'isp-shortcode-nonce' ); ?>"/>
				</form>
				<?php

			}

		}

	}

	ISP_Shortcodes::init();

}
