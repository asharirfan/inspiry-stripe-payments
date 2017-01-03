<?php
/**
 * Plugin Name:     Inspiry Stripe Payments
 * Plugin URI:      https://github.com/InspiryThemes/inspiry-stripe-payments
 * Description:     A simple, light weight plugin to add stripe payment to your WordPress site using a simple shortcode.
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

/*

    Copyright (C) 2017  Ashar Irfan  mrasharirfan@gmail.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
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
