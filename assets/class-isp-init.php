<?php
/**
 * ISP Plugin Initialization Class
 *
 * This class is used to initialize the plugin.
 *
 * @since 	1.0.0
 * @package ISP
 */


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Inspiry_Stripe_Payments.
 *
 * This is the main class of the plugin which is used to
 * initialize everything.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'Inspiry_Stripe_Payments' ) ) :

	class Inspiry_Stripe_Payments {

		/**
		 * $plugin_name.
		 *
		 * @var 	string
		 * @since 	1.0.0
		 */
		 public $plugin_name;

		/**
		 * $version.
		 *
		 * @var 	string
		 * @since 	1.0.0
		 */
		 public $version;


		/**
		 * $isp_options.
		 *
		 * @var 	mixed
		 * @since 	1.0.0
		 */
		 public $isp_options;

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->plugin_name 	= 'inspiry-stripe-payments';
			$this->version 		= '1.0.0';
			$this->isp_options 	= get_option( 'isp_settings' );
			$this->load_files();

		}

		/**
		 * load_files.
		 *
		 * @since 1.0.0
		 */
		public function load_files() {

			$isp_options	= $this->isp_options;

			/**
			 * class-settings.php
			 *
			 * Settings class file of the plugin.
			 */
			if ( is_admin() && file_exists( ISP_BASE_DIR . '/assets/includes/class-settings.php' ) ) {
			    require_once( ISP_BASE_DIR . '/assets/includes/class-settings.php' );
			}

			/**
			 * class-isp-shortcodes.php
			 *
			 * Class file for shortcodes of ISP.
			 */
			if ( file_exists( ISP_BASE_DIR . '/assets/public/class-isp-shortcodes.php' ) ) {
			    require_once( ISP_BASE_DIR . '/assets/public/class-isp-shortcodes.php' );
			}

			/**
			 * class-isp-button.php
			 *
			 * Class file for payment button for properties.
			 */
			if ( file_exists( ISP_BASE_DIR . '/assets/public/class-isp-button.php' ) ) {
			    require_once( ISP_BASE_DIR . '/assets/public/class-isp-button.php' );
			}

			/**
			 * class-payment-handler.php
			 *
			 * Class file for payment handler functions.
			 */
			if ( file_exists( ISP_BASE_DIR . '/assets/public/class-payment-handler.php' ) ) {
			    require_once( ISP_BASE_DIR . '/assets/public/class-payment-handler.php' );
			}

		}

		/**
		 * initialize_classes.
		 *
		 * @since 1.0.0
		 */
		public function run() {

			if ( class_exists( 'ISP_Settings' ) ) {
				$settings = new ISP_Settings();
			}

			if ( class_exists( 'ISP_Shortcodes' ) ) {
				$shortcodes = new ISP_Shortcodes();
			}

			if ( class_exists( 'ISP_Payment_Button' ) ) {
				$button = new ISP_Payment_Button();
			}

			if ( class_exists( 'ISP_Payment_Handler' ) ) {
				$button = new ISP_Payment_Handler();
			}

		}

	}

endif;
