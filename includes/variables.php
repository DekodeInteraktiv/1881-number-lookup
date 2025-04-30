<?php
/***
 * Variables.
 *
 * @package Woo1881
 */

namespace Woo1881;

/***
 * Returns default settings for admin settings page.
 *
 * @return array
 */
function get_default_admin_settings(): array {
	return \apply_filters('woo1881_settings_defaults', [
		'1881_subscription_key'     => '',
		'1881_checkout_description' => esc_html__( 'Autofill your information from 1881 by filling out the phone number field below.', 'woo1881' ),
	] );
}

/***
 * Returns saved settings from admin page.
 *
 * @return array
 */
function get_saved_settings(): array {
	$settings = \get_option( 'woo1881_admin_settings' );
	return ! empty( $settings ) ? $settings : [];
}

/***
 * Returns saved subscription key from admin settings.
 *
 * @return string
 */
function get_subscription_key(): string {
	$settings = get_saved_settings();
	return ! empty( $settings ) && ! empty( $settings['1881_subscription_key'] ) ? $settings['1881_subscription_key'] : '';
}

/***
 * Returns 1881 API base URL.
 *
 * @return string
 */
function get_1881_api_base_url(): string {
	return 'https://services.api1881.no/';
}
