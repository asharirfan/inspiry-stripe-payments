<?php
/**
 * Plugin Name: Inspiry Stripe Payments
 * Plugin URI: https://inspirythemes.com/
 * Description: A plugin to add stripe payment to publish a property
 * Author: Ashar Irfan
 * Author URI: https://asharirfan.com
 * Contributors: mrasharirfan, inspirythemes
 * Version: 1.0.0
 *
 * License: GPL2
 * Text Domain: inspiry-stripe
 */

/*

    Copyright (C) 2016  Ashar Irfan  mrasharirfan@gmail.com

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
 */
if ( ! defined( 'ISP_BASE_URL' ) ) {
	define( 'ISP_BASE_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'ISP_BASE_DIR' ) ) {
	define( 'ISP_BASE_DIR', dirname( __FILE__ ) );
}


/**
 * Plugin initialization file.
 *
 * @since 1.0.0
 */
if ( file_exists( ISP_BASE_DIR . '/assets/isp-init.php' ) ) {
    require_once( ISP_BASE_DIR . '/assets/isp-init.php' );
}


/*******************************************
* plugin text domain for translations
*******************************************/

// load_plugin_textdomain( 'pippin_stripe', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
