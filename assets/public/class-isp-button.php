<?php
/**
 * Property Payment Button Class
 *
 * This class is used to initialize the payment button
 * for properties of Real Estate themes from Inspiry Themes.
 *
 * @since    1.0.0
 * @package  ISP
 */


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * ISP_Payment_Button.
 *
 * Introduces property payment button.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'ISP_Payment_Button' ) ) {

	class ISP_Payment_Button {

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public static function init() {

			add_action( 'inspiry_property_payments', array( __CLASS__, 'isp_property_payment_button' ), 10, 1 );

		}

		/**
		 * payment_button.
		 *
		 * @since 1.0.0
		 */
		public static function isp_property_payment_button( $post_id ) {

			$isp_options = get_option( 'isp_settings' );

			// Set the default currency code.
			$currency_code = $isp_options[ 'currency_code' ];
			if ( empty( $currency_code ) ) {
				$currency_code = 'USD';
			}

			// Amount being charged.
			$amount = $isp_options[ 'amount' ];
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
			$button_label = $isp_options[ 'button_label' ];
			if ( empty( $button_label ) ) {
				$button_label = 'Pay with Card';
			}

			?>
			<form action="" method="POST" class="stripe-button">
			<script
					src="https://checkout.stripe.com/checkout.js" class="stripe-button"
					data-key="<?php echo esc_attr( $publishable_key ); ?>"
					data-amount="<?php echo esc_attr( $amount ); ?>"
					data-name="<?php echo get_bloginfo( 'name' ); ?>"
					data-currency="<?php echo esc_attr( $currency_code ); ?>"
					data-description="<?php _e( 'Property Payment', 'inspiry-stripe-payments' ); ?>"
					data-locale="auto"
					data-billing-address="true"
					data-label="<?php _e( $button_label, 'inspiry-stripe-payments' ); ?>">
			</script>
			<input type="hidden" name="action" value="isp_payment"/>
			<input type="hidden" name="amount" value="<?php echo esc_attr( $amount ); ?>"/>
			<input type="hidden" name="isp_nonce" value="<?php echo wp_create_nonce( 'isp-nonce' ); ?>"/>
			<input type="hidden" name="isp_property_id" value="<?php echo esc_attr( $post_id ); ?>"/>
			</form>
			<?php

		}

	}

	ISP_Payment_Button::init();

}
