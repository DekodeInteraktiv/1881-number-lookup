<?php
/***
 * Variables.
 *
 * @package DM1881
 */

namespace DM1881;

/***
 * Returns default settings for admin settings page.
 *
 * @return array
 */
function get_default_admin_settings(): array {
	return \apply_filters('dm1881_settings_defaults', [
		'1881_subscription_key'        => '',
		'1881_checkout_description'    => esc_html__( 'Autofill your information from 1881 by filling out the phone number field below.', '1881-number-lookup' ),
		'1881_checkout_no_results_msg' => esc_html__( 'Sorry, no results were found for this number.', '1881-number-lookup' ),
	] );
}

/***
 * Returns saved settings from admin page.
 *
 * @return array
 */
function get_saved_settings(): array {
	$settings = \get_option( 'dm1881_admin_settings' );
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
 * Returns keyup ms delay after typing and before performing search.
 *
 * @return int
 */
function get_keyup_delay(): int {
	return \apply_filters( 'dm1881_keyup_delay_ms', 250 );
}

/***
 * Returns the valid lengths of phone numbers before performing 1881 search.
 *
 * @return int
 */
function get_phone_valid_lengths(): array {
	return \apply_filters( 'dm1881_phone_valid_lengths', [ 5, 8 ] );
}

/***
 * Returns 1881 API base URL.
 *
 * @return string
 */
function get_1881_api_base_url(): string {
	return 'https://services.api1881.no/';
}

/***
 * Returns 1881 SVG logo.
 *
 * @return string
 */
function get_1881_logo(): string {
	return \file_get_contents( DM1881_PATH . '/public/images/1881-logo.svg' );  // phpcs:ignore
}
