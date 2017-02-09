<?php
/**
 * Plugin Name:     Inspiry Stripe Payments
 * Plugin URI:      https://github.com/InspiryThemes/inspiry-stripe-payments
 * Description:     A simple, light weight plugin to add stripe payment system to your WordPress site using a simple shortcode.
 * Author:          mrasharirfan, inspirythemes
 * Author URI:      https://inspirythemes.com
 * Contributors:    mrasharirfan, inspirythemes
 * Version:         1.0.0
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     inspiry-stripe
 *
 * @link            https://github.com/InspiryThemes/inspiry-stripe-payments
 * @since           1.0.0
 * @package         ISP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Constants and Globals
 *
 * @since 1.0.0
 */
if ( ! defined( 'ISP_BASE_URL' ) ) {
	define( 'ISP_BASE_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'ISP_BASE_DIR' ) ) {
	define( 'ISP_BASE_DIR', dirname( __FILE__ ) );
}


/**
 * Plugin initialization class file.
 *
 * @since 1.0.0
 */
if ( file_exists( ISP_BASE_DIR . '/assets/class-isp-init.php' ) ) {
    require_once( ISP_BASE_DIR . '/assets/class-isp-init.php' );
}


/**
 * Begin plugin execution.
 *
 * @since 1.0.0
 */
function run_inspiry_stripe_payments() {
    $plugin = new Inspiry_Stripe_Payments();
    $plugin->run();
}
run_inspiry_stripe_payments();
