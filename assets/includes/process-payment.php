<?php
/**
 * Payment Processing File.
 *
 * @since 1.0.0
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * This function handles the payment process of the
 * payment button on properties page.
 *
 * @since  1.0.0
 */
function isp_process_payment() {

	if ( isset( $_POST[ 'action' ] ) && 'isp_payment' == $_POST[ 'action' ] && wp_verify_nonce( $_POST[ 'isp_nonce' ], 'isp-nonce' ) ) {

		global $isp_options;

		// Property ID
		$property_id 	= $_POST[ 'isp_property_id' ];

		// Stripe Token
		$stripe_token 	= $_POST[ 'stripeToken' ];

		// Customer Details
		$name 		= $_POST[ 'stripeBillingName' ];
		$address 	= $_POST[ 'stripeBillingAddressLine1' ];
		$zip 		= $_POST[ 'stripeBillingAddressZip' ];
		$city 		= $_POST[ 'stripeBillingAddressCity' ];
		$state 		= $_POST[ 'stripeBillingAddressState' ];
		$country 	= $_POST[ 'stripeBillingAddressCountry' ];

		$customer_details 				= array();
		$customer_details['email'] 		= ( is_email( $_POST[ 'stripeEmail' ] ) ) ? $_POST[ 'stripeEmail' ] : false;
		$customer_details['name'] 		= ( ! empty( $name )  ) ? sanitize_text_field( $name ) : false;
		$customer_details['address'] 	= ( ! empty( $address ) ) ? sanitize_text_field( $address ) : false;
		$customer_details['zip'] 		= ( ! empty( $zip ) ) ? sanitize_text_field( $zip ) : false;
		$customer_details['city'] 		= ( ! empty( $city )  ) ? sanitize_text_field( $city ) : false;
		$customer_details['state'] 		= ( ! empty( $state )  ) ? sanitize_text_field( $state ) : false;
		$customer_details['country'] 	= ( ! empty( $country )  ) ? sanitize_text_field( $country ) : false;

		// Require Stripe library.
	    include( ISP_BASE_DIR . '/assets/stripe/stripe-init.php' );

		// check if we are using test mode
		if ( isset( $isp_options[ 'test_mode' ] ) && $isp_options[ 'test_mode' ] ) {
			$secret_key = $isp_options[ 'test_secret_key' ];
		} else {
			$secret_key = $isp_options[ 'live_secret_key' ];
		}

		// Amount being charged.
		$amount 		= $isp_options[ 'amount' ];
		if ( 'USD' == $isp_options[ 'currency_code' ] ) {
			$amount 	= $amount * 100;
		}

		// Currency Code.
		$currency_code 	= $isp_options[ 'currency_code' ];

		// Publish on payment.
		$publish 		= $isp_options[ 'publish_property' ];

		if ( ! empty( $property_id ) && ! empty( $stripe_token ) ) {

			// Attempt to charge the customer's card.
			try {

				\Stripe\Stripe::setApiKey( $secret_key );
				$isp_charge = \Stripe\Charge::create( array(
						'amount' 	=> $amount,
						'currency'	=> $currency_code,
						'source' 	=> $stripe_token
					)
				);

				$property = get_post( $property_id, 'ARRAY_A' );

				if ( is_array( $property ) && ! empty( $property ) ) {

					// Stripe Charge object.
					if ( isset( $isp_charge ) && ( ! empty( $isp_charge ) ) ) {
						update_post_meta( $property_id, 'isp_charge', $isp_charge );
						update_post_meta( $property_id, 'payment_status', 'Completed' );
					}

					if ( isset( $customer_details[ 'email' ] ) && ( ! empty( $customer_details[ 'email' ] ) ) ) {
						update_post_meta( $property_id, 'payer_email', $customer_details[ 'email' ] );
					}

					if ( isset( $customer_details[ 'name' ] ) && ( ! empty( $customer_details[ 'name' ] ) ) ) {
						update_post_meta( $property_id, 'first_name', $customer_details[ 'name' ] );
					}

					if ( isset( $customer_details[ 'address' ] ) && ( ! empty( $customer_details[ 'address' ] ) ) ) {
						update_post_meta( $property_id, 'isp_address', $customer_details[ 'address' ] );
					}

					if ( isset( $customer_details[ 'zip' ] ) && ( ! empty( $customer_details[ 'zip' ] ) ) ) {
						update_post_meta( $property_id, 'isp_zip', $customer_details[ 'zip' ] );
					}

					if ( isset( $customer_details[ 'city' ] ) && ( ! empty( $customer_details[ 'city' ] ) ) ) {
						update_post_meta( $property_id, 'isp_city', $customer_details[ 'city' ] );
					}

					if ( isset( $customer_details[ 'state' ] ) && ( ! empty( $customer_details[ 'state' ] ) ) ) {
						update_post_meta( $property_id, 'isp_state', $customer_details[ 'state' ] );
					}

					if ( isset( $customer_details[ 'country' ] ) && ( ! empty( $customer_details[ 'country' ] ) ) ) {
						update_post_meta( $property_id, 'isp_country', $customer_details[ 'country' ] );
					}

					if ( isset( $isp_options[ 'amount' ] ) && ! empty( $isp_options[ 'amount' ] ) ) {
						update_post_meta( $property_id, 'payment_gross', $isp_options[ 'amount' ] );
					}

					if ( ! empty( $currency_code ) ) {
						update_post_meta( $property_id, 'mc_currency', $currency_code );
					}

					if ( $publish ) {
						$property[ 'post_status' ] = 'publish';
						wp_update_post( $property );
					}

					isp_notify_user( $property_id, $customer_details[ 'email' ] );
					isp_notify_admin( $property_id );

				}

				// redirect on successful payment
				$redirect = add_query_arg( 'payment', 'paid', $_POST[ 'redirect' ] );

			} catch ( Exception $e ) {
				// redirect on failed payment
				$redirect = add_query_arg( 'payment', 'failed', $_POST[ 'redirect' ] );
			}

			// redirect back to our previous page with the added query variable
			wp_redirect( $redirect );
			exit;
		}

	}

}

add_action( 'init', 'isp_process_payment' );


/**
 * isp_notify_user.
 *
 * @since 1.0.0
 */
function isp_notify_user( $property_id, $user_email ) {

	// Get property.
	$property 		= get_post( $property_id );
	$property_name	= $property->post_title;

	// Property Preview Link.
	$preview_link = set_url_scheme( get_permalink( $property_id ) );
	$preview_link = esc_url( apply_filters( 'preview_post_link', add_query_arg( 'preview', 'true', $preview_link ) ) );

	/**
	 * The blogname option is escaped with esc_html on the way into the database in sanitize_option
	 * we want to reverse this for the plain text arena of emails.
	 */
	$website_name	= wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

	/**
     * Email Headers ( Reply To and Content Type )
     */
	$headers 		= array();
	$headers[] 		= "Content-Type: text/html; charset=UTF-8";
	$headers 		= apply_filters( 'isp_payment_successful_header', $headers );

	$subject 		= __( 'Property Payment Received', 'inspiry-stripe' );
	$message		= sprintf( $subject ) . "<br/><br/>";
	$message 		= sprintf( __( 'Your payment for property %s has been received successfully via Stripe.', 'inspiry-stripe' ), $property_name ) . "<br/><br/>";
	if ( ! empty( $preview_link ) ) {
		$message 	.= __( 'You can preview it here : ', 'inspiry-stripe' );
		$message 	.= '<a target="_blank" href="' . $preview_link . '">' . $property_name . '</a>';
		$message 	.= "<br/><br/>";
	}

	wp_mail( $user_email, $subject, $message, $headers );

}


/**
 * isp_notify_admin.
 *
 * @since 1.0.0
 */
function isp_notify_admin( $property_id ) {

	// Current User Info.
	$admin_email 	= get_bloginfo( 'admin_email' );

	// Get property.
	$property 		= get_post( $property_id );
	$property_name	= $property->post_title;

	// Property Preview Link.
	$preview_link = set_url_scheme( get_permalink( $property_id ) );
	$preview_link = esc_url( apply_filters( 'preview_post_link', add_query_arg( 'preview', 'true', $preview_link ) ) );

	/**
	 * The blogname option is escaped with esc_html on the way into the database in sanitize_option
	 * we want to reverse this for the plain text arena of emails.
	 */
	$website_name	= wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

	/**
     * Email Headers ( Reply To and Content Type )
     */
	$headers 		= array();
	$headers[] 		= "Content-Type: text/html; charset=UTF-8";
	$headers 		= apply_filters( 'isp_payment_successful_header', $headers );

	$subject 		= __( 'Property Payment Submitted', 'inspiry-stripe' );
	$message		= sprintf( $subject ) . "<br/><br/>";
	$message 		= sprintf( __( 'Payment for property %s has been submitted successfully via Stripe.', 'inspiry-stripe' ), $property_name ) . "<br/><br/>";
	if ( ! empty( $preview_link ) ) {
		$message 	.= __( 'You can preview it here : ', 'framework' );
		$message 	.= '<a target="_blank" href="' . $preview_link . '">' . $property_name . '</a>';
		$message 	.= "<br/><br/>";
	}

	wp_mail( $admin_email, $subject, $message, $headers );

}
