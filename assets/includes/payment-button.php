<?php
/**
 * Payment Button for Properties.
 *
 * @since 1.0.0
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Property Payment Button.
 *
 * @since 1.0.0
 */
function isp_property_payment_button( $post_id ) {

	global $isp_options;

	// Amount being charged.
	$amount = $isp_options[ 'amount' ];
	if ( 'USD' == $isp_options[ 'currency_code' ] ) {
		$amount = $amount * 100;
	}

	// Check if we are using test mode.
	if ( isset( $isp_options[ 'test_mode' ] ) && $isp_options[ 'test_mode' ] ) {
		$publishable_key = $isp_options[ 'test_publishable_key' ];
	} else {
		$publishable_key = $isp_options[ 'live_publishable_key' ];
	}

	// Button Label.
	$button_label 		= $isp_options[ 'button_label' ];
	if ( empty( $button_label ) ) {
		$button_label	= 'Pay Now';
	}

	?><form action="" method="POST" class="stripe-button">
		<script
			src="https://checkout.stripe.com/checkout.js" class="stripe-button"
			data-key="<?php echo esc_attr( $publishable_key ); ?>"
			data-amount="<?php echo esc_attr( $amount ); ?>"
			data-name="<?php echo get_bloginfo( 'name' ); ?>"
			data-currency="<?php echo esc_attr( $isp_options[ 'currency_code' ] ); ?>"
			data-description="<?php _e( 'Property Payment', 'inspiry-stripe' ); ?>"
			data-locale="auto"
			data-billing-address="true"
			data-label="<?php _e( $button_label, 'inspiry-stripe' ); ?>">
		</script>
		<input type="hidden" name="action" value="isp_payment"/>
		<input type="hidden" name="isp_nonce" value="<?php echo wp_create_nonce( 'isp-nonce' ); ?>"/>
		<input type="hidden" name="isp_property_id" value="<?php echo esc_attr( $post_id ); ?>"/>
	</form>
	<?php

}

add_action( 'inspiry_property_payments', 'isp_property_payment_button', 10, 1 );
