<?php
/**
 * Plugin Name:         Inspiry Stripe Payments
 * Plugin URI:          https://github.com/InspiryThemes/inspiry-stripe-payments
 * Description:         A simple, light weight plugin to add stripe payment system to your WordPress site using a simple
 * shortcode. Author:   mrasharirfan, inspirythemes
 * Author URI:          https://inspirythemes.com
 * Contributors:        mrasharirfan, inspirythemes
 * Version:             1.0.0
 * License:             GPL-2.0+
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:         inspiry-stripe-payments
 *
 * @link        https://github.com/InspiryThemes/inspiry-stripe-payments
 * @since       1.0.0
 * @package     ISP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Constants and Globals
 *
 * @since 1.0.0
 */


/**
 * Inspiry_Stripe_Payments.
 *
 * This is the main class of the plugin which is used to
 * initialize everything.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'Inspiry_Stripe_Payments' ) ) :

	final class Inspiry_Stripe_Payments {

		/**
		 * $plugin_name.
		 *
		 * @var    string
		 * @since    1.0.0
		 */
		public $plugin_name;

		/**
		 * $version.
		 *
		 * @var    string
		 * @since    1.0.0
		 */
		public $version = '1.0.0';


		/**
		 * $isp_options.
		 *
		 * @var    mixed
		 * @since    1.0.0
		 */
		public $isp_options;

		/**
		 * The single instance of the class.
		 *
		 * @var Inspiry_Stripe_Payments
		 * @since 1.0.0
		 */
		protected static $_instance = null;

		/**
		 * Main plugin instance.
		 *
		 * Ensures only one instance of plugin is loaded or can be loaded.
		 *
		 * @since 1.0.0
		 * @static
		 * @see   ISP()
		 * @return Inspiry_Stripe_Payments - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->define_constants();
			$this->plugin_name = 'inspiry-stripe-payments';
			$this->isp_options = get_option( 'isp_settings' );
			$this->load_files();
			$this->run();
		}

		/**
		 * Define constants
		 *
		 * @since 1.0.0
		 */
		public function define_constants() {
			if ( ! defined( 'ISP_BASE_URL' ) ) {
				define( 'ISP_BASE_URL', plugin_dir_url( __FILE__ ) );
			}

			if ( ! defined( 'ISP_BASE_DIR' ) ) {
				define( 'ISP_BASE_DIR', dirname( __FILE__ ) );
			}
		}

		/**
		 * load_files.
		 *
		 * @since 1.0.0
		 */
		public function load_files() {

			$isp_options = $this->isp_options;

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

		/**
		 * Cloning is forbidden.
		 *
		 * @since 1.0.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'inspiry-stripe-payments' ), '1.0.0' );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 1.0.0
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'inspiry-stripe-payments' ), '1.0.0' );
		}

	}

endif;


/**
 * Main instance of Inspiry_Stripe_Payments
 *
 * Returns the main instance of Inspiry_Stripe_Payments to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return Inspiry_Stripe_Payments
 */
function ISP() {
	return Inspiry_Stripe_Payments::instance();
}


ISP();
