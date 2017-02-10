<?php
/**
 * Payment Handler Class
 *
 * This class handles the payment functions for the plugin.
 *
 * @since    1.0.0
 * @package  ISP
 */


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * ISP_Payment_Handler.
 *
 * Handles payment process for this plugin.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'ISP_Payment_Handler' ) ) {

	class ISP_Payment_Handler {

		/**
		 * $secret_key.
		 *
		 * @var    string
		 * @since    1.0.0
		 */
		protected $secret_key;

		/**
		 * $currency_code.
		 *
		 * @var    string
		 * @since    1.0.0
		 */
		protected $currency_code;

		/**
		 * $stripe_token.
		 *
		 * @var    string
		 * @since    1.0.0
		 */
		protected $stripe_token;

		/**
		 * $customer_details.
		 *
		 * @var    mixed
		 * @since    1.0.0
		 */
		protected $customer_details;


		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->set_variables();

			// include Stripe library if it is not already exists.
			if ( ! class_exists( '\Stripe\Stripe' ) ) {
				include_once( ISP_BASE_DIR . '/assets/stripe/stripe-init.php' );
			}

			add_action( 'init', array( $this, 'isp_process_property_payments' ) );

			add_action( 'init', array( $this, 'isp_process_shortcode_payments' ) );

		}

		/**
		 * set_variables.
		 *
		 * @since 1.0.0
		 */
		public function set_variables() {

			$isp_options = get_option( 'isp_settings' );

			// Check if we are using test mode.
			if ( isset( $isp_options[ 'test_mode' ] ) && $isp_options[ 'test_mode' ] ) {
				$this->secret_key = $isp_options[ 'test_secret_key' ];
			} else {
				$this->secret_key = $isp_options[ 'live_secret_key' ];
			}

			// Set currency code.
			$this->currency_code = $isp_options[ 'currency_code' ];

			// Set the default currency code.
			if ( empty( $this->currency_code ) ) {
				$this->currency_code = 'USD';
			}

			// Set customer details.
			$this->customer_details = array(
				'email'   => '',
				'name'    => '',
				'address' => '',
				'zip'     => '',
				'city'    => '',
				'state'   => '',
				'country' => ''
			);

		}

		/**
		 * isp_process_property_payments.
		 *
		 * This function handles the payment process of the
		 * payment button on properties page.
		 *
		 * @since  1.0.0
		 */
		function isp_process_property_payments() {

			if ( isset( $_POST[ 'action' ] )
			     && 'isp_payment' == $_POST[ 'action' ]
			     && wp_verify_nonce( $_POST[ 'isp_nonce' ], 'isp-nonce' )
			) {

				$isp_options = get_option( 'isp_settings' );

				// Property ID
				$property_id = $_POST[ 'isp_property_id' ];

				// Stripe Token
				$this->stripe_token = $_POST[ 'stripeToken' ];

				// Amount being charged.
				if ( isset( $_POST[ 'amount' ] ) && ! empty( $_POST[ 'amount' ] ) ) {
					$amount = $_POST[ 'amount' ];
				} else {
					$amount = 0;
				}

				// Publish on payment.
				$publish = $isp_options[ 'publish_property' ];

				// Customer Details
				$email   = $_POST[ 'stripeEmail' ];
				$name    = $_POST[ 'stripeBillingName' ];
				$address = $_POST[ 'stripeBillingAddressLine1' ];
				$zip     = $_POST[ 'stripeBillingAddressZip' ];
				$city    = $_POST[ 'stripeBillingAddressCity' ];
				$state   = $_POST[ 'stripeBillingAddressState' ];
				$country = $_POST[ 'stripeBillingAddressCountry' ];

				$this->customer_details[ 'email' ]   = ( is_email( $email ) ) ? $email : false;
				$this->customer_details[ 'name' ]    = ( ! empty( $name ) ) ? sanitize_text_field( $name ) : false;
				$this->customer_details[ 'address' ] = ( ! empty( $address ) ) ? sanitize_text_field( $address ) : false;
				$this->customer_details[ 'zip' ]     = ( ! empty( $zip ) ) ? sanitize_text_field( $zip ) : false;
				$this->customer_details[ 'city' ]    = ( ! empty( $city ) ) ? sanitize_text_field( $city ) : false;
				$this->customer_details[ 'state' ]   = ( ! empty( $state ) ) ? sanitize_text_field( $state ) : false;
				$this->customer_details[ 'country' ] = ( ! empty( $country ) ) ? sanitize_text_field( $country ) : false;

				/**
				 * Filter the values of $customer_details array
				 * for property payments to extend its values.
				 */
				$this->customer_details = apply_filters( 'isp_property_customer_details', $this->customer_details );

				if ( ! empty( $property_id ) && ! empty( $this->stripe_token ) ) {

					// Attempt to charge the customer's card.
					try {

						\Stripe\Stripe::setApiKey( $this->secret_key );
						$isp_charge = \Stripe\Charge::create( array(
								'amount'   => $amount,
								'currency' => $this->currency_code,
								'source'   => $this->stripe_token
							)
						);

						$property = get_post( $property_id, 'ARRAY_A' );

						if ( is_array( $property ) && ! empty( $property ) ) {

							// Stripe Charge object.
							if ( isset( $isp_charge ) && ( ! empty( $isp_charge ) ) ) {
								update_post_meta( $property_id, 'isp_charge', $isp_charge );
								update_post_meta( $property_id, 'payment_status', 'Completed' );
							}

							if ( isset( $this->customer_details[ 'email' ] ) && ( ! empty( $this->customer_details[ 'email' ] ) ) ) {
								update_post_meta( $property_id, 'payer_email', $this->customer_details[ 'email' ] );
							}

							if ( isset( $this->customer_details[ 'name' ] ) && ( ! empty( $this->customer_details[ 'name' ] ) ) ) {
								update_post_meta( $property_id, 'first_name', $this->customer_details[ 'name' ] );
							}

							if ( isset( $this->customer_details[ 'address' ] ) && ( ! empty( $this->customer_details[ 'address' ] ) ) ) {
								update_post_meta( $property_id, 'isp_address', $this->customer_details[ 'address' ] );
							}

							if ( isset( $this->customer_details[ 'zip' ] ) && ( ! empty( $this->customer_details[ 'zip' ] ) ) ) {
								update_post_meta( $property_id, 'isp_zip', $this->customer_details[ 'zip' ] );
							}

							if ( isset( $this->customer_details[ 'city' ] ) && ( ! empty( $this->customer_details[ 'city' ] ) ) ) {
								update_post_meta( $property_id, 'isp_city', $this->customer_details[ 'city' ] );
							}

							if ( isset( $this->customer_details[ 'state' ] ) && ( ! empty( $this->customer_details[ 'state' ] ) ) ) {
								update_post_meta( $property_id, 'isp_state', $this->customer_details[ 'state' ] );
							}

							if ( isset( $this->customer_details[ 'country' ] ) && ( ! empty( $this->customer_details[ 'country' ] ) ) ) {
								update_post_meta( $property_id, 'isp_country', $this->customer_details[ 'country' ] );
							}

							if ( isset( $isp_options[ 'amount' ] ) && ! empty( $isp_options[ 'amount' ] ) ) {
								update_post_meta( $property_id, 'payment_gross', $isp_options[ 'amount' ] );
							}

							if ( isset( $this->currency_code ) && ! empty( $this->currency_code ) ) {
								update_post_meta( $property_id, 'mc_currency', $this->currency_code );
							}

							if ( $publish ) {
								$property[ 'post_status' ] = 'publish';
								wp_update_post( $property );
							}

							$this->isp_notify_user( $property_id );
							$this->isp_notify_admin( $property_id );

						}

						/**
						 * This hook can be used to extend the successful
						 * charged payment functionality for properties.
						 */
						do_action( 'isp_property_payment_charged', $property_id, $isp_charge );

						// redirect on successful payment
						$redirect = add_query_arg( 'payment', 'paid', $_POST[ 'redirect' ] );

					} catch ( Exception $e ) {

						// redirect on failed payment
						$redirect = add_query_arg( 'payment', 'failed', $_POST[ 'redirect' ] );

						/**
						 * This hook can be used to extend the failed
						 * charged payment functionality for properties.
						 */
						do_action( 'isp_property_payment_failed' );

					}

					// redirect back to our previous page with the added query variable
					wp_redirect( $redirect );
					exit;

				} else {

					// Redirect on empty token or property id.
					$redirect = add_query_arg( 'payment', 'failed', $_POST[ 'redirect' ] );

					// Redirect back to our previous page with the added query variable.
					wp_redirect( $redirect );
					exit;

				}

			}

		}


		/**
		 * isp_notify_user.
		 *
		 * @since 1.0.0
		 */
		public function isp_notify_user( $property_id = 0 ) {

			/**
			 * The blogname option is escaped with esc_html on the way into the database in sanitize_option
			 * we want to reverse this for the plain text arena of emails.
			 */
			// $website_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

			/**
			 * Email Headers ( Reply To and Content Type )
			 */
			$headers   = array();
			$headers[] = "Content-Type: text/html; charset=UTF-8";
			$headers   = apply_filters( 'isp_payment_successful_header', $headers );

			$subject = __( 'Payment Processed', 'inspiry-stripe-payments' );
			$message = $subject . "<br/><br/>";

			if ( ! empty( $property_id ) ) {

				// Get property.
				$property      = get_post( $property_id );

				// Property title
				$property_name = $property->post_title;
				$message .= sprintf( __( 'Payment for property [%s] has been processed successfully.', 'inspiry-stripe-payments' ), $property_name ) . "<br/><br/>";

				// Property Preview Link.
				$preview_link = set_url_scheme( get_permalink( $property_id ) );
				$preview_link = esc_url( apply_filters( 'preview_post_link', add_query_arg( 'preview', 'true', $preview_link ) ) );
				if ( ! empty( $preview_link ) ) {
					$message .= __( 'You can preview it here:', 'inspiry-stripe-payments' );
					$message .= '<a target="_blank" href="' . $preview_link . '">&nbsp;' . $property_name . '</a>';
					$message .= "<br/><br/>";
				}

			} else {

				if ( isset( $this->customer_details[ 'name' ] ) && ! empty( $this->customer_details[ 'name' ] ) ) {
					$message .= sprintf( __( 'Hi %s,', 'inspiry-stripe-payments' ), $this->customer_details[ 'name' ] ) . "<br/><br/>";
				}
				$message .= sprintf( __( 'Your payment has been processed successfully.', 'inspiry-stripe-payments' ) ) . "<br/><br/>";

			}

			wp_mail( $this->customer_details[ 'email' ], $subject, $message, $headers );

		}


		/**
		 * isp_notify_admin.
		 *
		 * @since 1.0.0
		 */
		public function isp_notify_admin( $property_id = 0 ) {

			// Admin Info.
			$admin_email = get_bloginfo( 'admin_email' );

			// User info.
			$user_email = $this->customer_details[ 'email' ];
			$user_name  = $this->customer_details[ 'name' ];

			/**
			 * The blogname option is escaped with esc_html on the way into the database in sanitize_option
			 * we want to reverse this for the plain text arena of emails.
			 */
			//$website_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

			/**
			 * Email Headers ( Reply To and Content Type )
			 */
			$headers   = array();
			$headers[] = "Content-Type: text/html; charset=UTF-8";
			$headers   = apply_filters( 'isp_payment_successful_header', $headers );

			$subject = __( 'Payment Submitted', 'inspiry-stripe-payments' );
			$message = $subject . "<br/><br/>";

			if ( ! empty( $property_id ) ) {

				// Get property.
				$property      = get_post( $property_id );

				// Property title
				$property_name = $property->post_title;
				$message .= sprintf( __( 'Payment for property %s has been submitted successfully via Stripe.', 'inspiry-stripe-payments' ), $property_name ) . "<br/><br/>";

				// Property Preview Link.
				$preview_link = set_url_scheme( get_permalink( $property_id ) );
				$preview_link = esc_url( apply_filters( 'preview_post_link', add_query_arg( 'preview', 'true', $preview_link ) ) );
				if ( ! empty( $preview_link ) ) {
					$message .= __( 'You can preview it here:', 'inspiry-stripe-payments' );
					$message .= '<a target="_blank" href="' . $preview_link . '">&nbsp;' . $property_name . '</a>';
					$message .= "<br/><br/>";
				}

			} else {

				if ( ! empty( $user_name ) ) {
					$message .= sprintf( __( 'Payment has been submitted successfully via Stripe by %s.', 'inspiry-stripe-payments' ), $user_name ) . "<br/><br/>";
				} else {
					$message .= __( 'Payment has been submitted successfully via Stripe.', 'inspiry-stripe-payments' ) . "<br/><br/>";
				}
				$message .= sprintf( __( 'You can contact the customer at %s.', 'inspiry-stripe-payments' ), $user_email ) . "<br/><br/>";

			}

			wp_mail( $admin_email, $subject, $message, $headers );

		}


		/**
		 * isp_process_shortcode_payments.
		 *
		 * This function handles the payment process of the
		 * payment button on properties page.
		 *
		 * @since  1.0.0
		 */
		function isp_process_shortcode_payments() {

			if ( isset( $_POST[ 'action' ] )
			     && 'isp_shortcode_payment' == $_POST[ 'action' ]
			     && wp_verify_nonce( $_POST[ 'isp_shortcode_nonce' ], 'isp-shortcode-nonce' )
			) {

				//$isp_options = get_option( 'isp_settings' );

				// Stripe Token
				$this->stripe_token = $_POST[ 'stripeToken' ];

				// Amount being charged.
				if ( isset( $_POST[ 'amount' ] ) && ! empty( $_POST[ 'amount' ] ) ) {
					$amount = $_POST[ 'amount' ];
				} else {
					$amount = 0;
				}

				// Set currency code.
				$currency_code = $_POST[ 'currency_code' ];
				if ( empty( $currency_code ) ) {
					$currency_code = $this->currency_code;
				}

				// Customer Details
				$email   = $_POST[ 'stripeEmail' ];
				$name    = $_POST[ 'stripeBillingName' ];
				$address = $_POST[ 'stripeBillingAddressLine1' ];
				$zip     = $_POST[ 'stripeBillingAddressZip' ];
				$city    = $_POST[ 'stripeBillingAddressCity' ];
				$state   = $_POST[ 'stripeBillingAddressState' ];
				$country = $_POST[ 'stripeBillingAddressCountry' ];

				$this->customer_details[ 'email' ]   = ( is_email( $email ) ) ? $email : false;
				$this->customer_details[ 'name' ]    = ( ! empty( $name ) ) ? sanitize_text_field( $name ) : false;
				$this->customer_details[ 'address' ] = ( ! empty( $address ) ) ? sanitize_text_field( $address ) : false;
				$this->customer_details[ 'zip' ]     = ( ! empty( $zip ) ) ? sanitize_text_field( $zip ) : false;
				$this->customer_details[ 'city' ]    = ( ! empty( $city ) ) ? sanitize_text_field( $city ) : false;
				$this->customer_details[ 'state' ]   = ( ! empty( $state ) ) ? sanitize_text_field( $state ) : false;
				$this->customer_details[ 'country' ] = ( ! empty( $country ) ) ? sanitize_text_field( $country ) : false;

				/**
				 * Filter the values of $customer_details array
				 * for shortcode payments to extend its values.
				 */
				$this->customer_details = apply_filters( 'isp_customer_payment_details', $this->customer_details );

				if ( ! empty( $this->stripe_token ) ) {

					// Attempt to charge the customer's card.
					try {

						\Stripe\Stripe::setApiKey( $this->secret_key );
						$isp_charge = \Stripe\Charge::create( array(
								'amount'   => $amount,
								'currency' => $currency_code,
								'source'   => $this->stripe_token
							)
						);

						$this->isp_notify_user( null );
						$this->isp_notify_admin( null );

						/**
						 * This hook can be used to extend the successful
						 * charged payment functionality.
						 */
						do_action( 'isp_shortcode_payment_charged', $isp_charge );

						// Redirect on successful payment.
						$redirect = add_query_arg( 'payment', 'paid', $_POST[ 'redirect' ] );

					} catch ( Exception $e ) {

						// Redirect on failed payment.
						$redirect = add_query_arg( 'payment', 'failed', $_POST[ 'redirect' ] );

						/**
						 * This hook can be used to extend the failed
						 * charged payment functionality.
						 */
						do_action( 'isp_shortcode_payment_failed' );

					}

					// redirect back to our previous page with the added query variable
					wp_redirect( $redirect );
					exit;

				} else {

					// Redirect on empty stripe token.
					$redirect = add_query_arg( 'payment', 'failed', $_POST[ 'redirect' ] );
					// Redirect back to our previous page with the added query variable.
					wp_redirect( $redirect );
					exit;

				}

			}

		}

	}

	new ISP_Payment_Handler();

}
