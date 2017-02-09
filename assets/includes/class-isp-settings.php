<?php
/**
 * ISP Settings Class
 *
 * This class is used to initialize the settings page of this plugin.
 *
 * @since 	1.0.0
 * @package ISP
 */


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * ISP_Settings.
 *
 * Settings class for Inspiry Stripe Payments. It is
 * responsible for handling the settings page of the
 * plugin.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'ISP_Settings' ) ) {

	class ISP_Settings {

		/**
		 * Initialize settings
		 *
		 * @since  1.0.0
		 */
		public static function init() {

			// Add settings page to menu.
			add_action( 'admin_menu', array( __CLASS__, 'isp_settings_setup' ) );

			// Register page settings.
			add_action( 'admin_init', array( __CLASS__, 'isp_register_settings' ) );

		}

		/**
		 * Add settings page to settings menu.
		 *
		 * @since  1.0.0
		 */
		public static function isp_settings_setup() {
			add_options_page(
				'Inspiry Stripe Payments',
				'Inspiry Stripe Payments',
				'manage_options',
				'isp-settings',
				array( __CLASS__, 'isp_render_options_page' )
			);
		}

		/**
		 * Render settings on the settings page.
		 *
		 * @since  1.0.0
		 */
		public static function isp_render_options_page() {

			$isp_options = get_option( 'isp_settings' );

			?>
			<div class="wrap">
				<h2><?php _e( 'Inspiry Stripe Payments Settings', 'inspiry-stripe-payments' ); ?></h2>
				<form method="post" action="options.php">

					<?php settings_fields( 'isp_settings_group' ); ?>

					<h3 class="title"><?php _e( 'Stripe Settings', 'inspiry-stripe-payments' ); ?></h3>
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row" valign="top">
									<?php _e( 'Test Mode', 'inspiry-stripe-payments' ); ?>
								</th>
								<td>
									<input id="isp_settings[test_mode]" name="isp_settings[test_mode]" type="checkbox" value="1" <?php checked( 1, $isp_options[ 'test_mode' ] ); ?> />
									<label class="description" for="isp_settings[test_mode]"><?php _e( 'Check this to use the plugin in test mode.', 'inspiry-stripe-payments' ); ?></label>
								</td>
							</tr>

							<tr valign="top">
								<th scope="row" valign="top">
									<?php _e( 'Live Secret', 'inspiry-stripe-payments' ); ?>
								</th>
								<td>
									<input id="isp_settings[live_secret_key]" name="isp_settings[live_secret_key]" type="text" class="regular-text" value="<?php echo $isp_options['live_secret_key']; ?>"/>
									<label class="description" for="isp_settings[live_secret_key]"><?php _e('Paste your live secret key.', 'inspiry-stripe-payments'); ?></label>
								</td>
							</tr>

							<tr valign="top">
								<th scope="row" valign="top">
									<?php _e( 'Live Publishable', 'inspiry-stripe-payments' ); ?>
								</th>
								<td>
									<input id="isp_settings[live_publishable_key]" name="isp_settings[live_publishable_key]" type="text" class="regular-text" value="<?php echo $isp_options['live_publishable_key']; ?>"/>
									<label class="description" for="isp_settings[live_publishable_key]"><?php _e('Paste your live publishable key.', 'inspiry-stripe-payments'); ?></label>
								</td>
							</tr>

							<tr valign="top">
								<th scope="row" valign="top">
									<?php _e( 'Test Secret', 'inspiry-stripe-payments' ); ?>
								</th>
								<td>
									<input id="isp_settings[test_secret_key]" name="isp_settings[test_secret_key]" type="text" class="regular-text" value="<?php echo $isp_options['test_secret_key']; ?>"/>
									<label class="description" for="isp_settings[test_secret_key]"><?php _e('Paste your test secret key.', 'inspiry-stripe-payments'); ?></label>
								</td>
							</tr>

							<tr valign="top">
								<th scope="row" valign="top">
									<?php _e( 'Test Publishable', 'inspiry-stripe-payments' ); ?>
								</th>
								<td>
									<input id="isp_settings[test_publishable_key]" name="isp_settings[test_publishable_key]" class="regular-text" type="text" value="<?php echo $isp_options['test_publishable_key']; ?>"/>
									<label class="description" for="isp_settings[test_publishable_key]"><?php _e('Paste your test publishable key.', 'inspiry-stripe-payments'); ?></label>
								</td>
							</tr>

							<tr valign="top">
								<th scope="row" valign="top">
									<?php _e( 'Currency Code', 'inspiry-stripe-payments' ); ?>
								</th>
								<td>
									<input id="isp_settings[currency_code]" name="isp_settings[currency_code]" class="regular-text" type="text" value="<?php echo $isp_options['currency_code']; ?>"/>
									<label class="description" for="isp_settings[currency_code]"><?php _e( 'Provide currency code that you want to use. Example: USD', 'inspiry-stripe-payments' ); ?></label>
								</td>
							</tr>

							<tr valign="top">
								<th scope="row" valign="top">
									<?php _e( 'Payment Button Label', 'inspiry-stripe-payments' ); ?>
								</th>
								<td>
									<input id="isp_settings[button_label]" name="isp_settings[button_label]" class="regular-text" type="text" value="<?php echo $isp_options['button_label']; ?>" placeholder="Pay with Card"/>
									<label class="description" for="isp_settings[button_label]"><?php _e( 'Default: Pay with Card', 'inspiry-stripe-payments' ); ?></label>
								</td>
							</tr>
						</tbody>
					</table>

					<h3 class="title"><?php _e( 'Theme Settings', 'inspiry-stripe-payments' ); ?></h3>
					<p class="description"><?php _e( 'Note: Ignore these settings if you are not using <a href="https://inspirythemes.com/theme-category/real-estate/" target="_blank">Real Estate Themes</a> from <a href="https://inspirythemes.com" target="_blank">Inspiry Themes</a>.', 'inspiry-stripe-payments' ); ?></p>
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row" valign="top">
									<?php _e( 'Payment Amount Per Property', 'inspiry-stripe-payments' ); ?>
								</th>
								<td>
									<input id="isp_settings[amount]" name="isp_settings[amount]" class="regular-text" type="text" value="<?php echo $isp_options['amount']; ?>"/>
									<label class="description" for="isp_settings[amount]"><?php _e( 'Provide the amount that you want to charge for one property. Example: 20.00', 'inspiry-stripe-payments' ); ?></label>
								</td>
							</tr>

							<tr valign="top">
								<th scope="row" valign="top">
									<?php _e( 'Publish Submitted Property after Payment', 'inspiry-stripe-payments' ); ?>
								</th>
								<td>
									<input id="isp_settings[publish_property]" name="isp_settings[publish_property]" type="radio" value="1" <?php checked(1, $isp_options['publish_property'], true); ?> />
									<label class="description" for="isp_settings[publish_property]"><?php _e('Yes', 'inspiry-stripe-payments'); ?></label>
									<input id="isp_settings[publish_property]" name="isp_settings[publish_property]" type="radio" value="0" <?php checked(0, $isp_options['publish_property'], true); ?> />
									<label class="description" for="isp_settings[publish_property]"><?php _e('No', 'inspiry-stripe-payments'); ?></label>
								</td>
							</tr>
						</tbody>
					</table>

					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'inspiry-stripe-payments' ); ?>" />
					</p>

				</form>
			<?php
		}

		/**
		 * Register settings for the plugin.
		 *
		 * @since  1.0.0
		 */
		public static function isp_register_settings() {
			register_setting( 'isp_settings_group', 'isp_settings' );
		}

	}

	ISP_Settings::init();

}
