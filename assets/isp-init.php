<?php
/**
 * Initialization File.
 *
 * @since 1.0.0
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


// Options.
$isp_options = get_option( 'isp_settings' );


/**
 * class-settings.php
 *
 * Settings class file of the plugin.
 */
if ( is_admin() && file_exists( ISP_BASE_DIR . '/assets/includes/class-settings.php' ) ) {
    require_once( ISP_BASE_DIR . '/assets/includes/class-settings.php' );
}


if ( class_exists( 'ISP_Settings' ) ) {

	// Initialize the settings.
	$settings = new ISP_Settings();

}


/**
 * payment-button.php
 *
 * Payment button for properties.
 */
if ( ( ! is_admin() ) && file_exists( ISP_BASE_DIR . '/assets/includes/payment-button.php' ) ) {
    require_once( ISP_BASE_DIR . '/assets/includes/payment-button.php' );
}


/**
 * process-payment.php
 *
 * Payment process functions for properties.
 */
if ( ( ! is_admin() ) && file_exists( ISP_BASE_DIR . '/assets/includes/process-payment.php' ) ) {
    require_once( ISP_BASE_DIR . '/assets/includes/process-payment.php' );
}
