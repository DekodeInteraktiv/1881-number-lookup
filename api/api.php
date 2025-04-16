<?php
/***
 * Handles API requests to 1881.
 *
 * @package Woo1881
 */

namespace Woo1881;

/***
 * Perform request to 1881 to look up a phone number.
 * Returns array of returned contacts, or empty array for no results/error.
 *
 * @param string $phone Query string to search a person for.
 * @return array
 */
function do_1881_api_phone_lookup( string $phone ): array {
	$url = get_1881_api_base_url() . 'lookup/phonenumber/' . \rawurlencode( $phone );

	$headers = get_api_request_headers();
	if ( empty( $headers ) ) {
		return [];
	}

	$response = \wp_remote_get( $url, [
		'headers' => get_api_request_headers(),
	] );

	if ( \is_wp_error( $response ) ) {
		return [];
	}

	$response_code = \wp_remote_retrieve_response_code( $response );
	if ( \in_array( $response_code, [ 200, 201 ], true ) ) {
		$json_response = \json_decode( \wp_remote_retrieve_body( $response ), true );
		if ( isset( $json_response['contacts'] ) ) {
			return $json_response['contacts'];
		}
	}
	return [];
}

/***
 * Returns array of 1881 request headers.
 *
 * @return array
 */
function get_api_request_headers(): array {
	$subscription_key = get_subscription_key();
	if ( ! empty( $subscription_key ) ) {
		return \apply_filters( 'woo1881_api_request_headers', [
			'Cache-Control'             => 'no-cache',
			'Ocp-Apim-Subscription-Key' => $subscription_key,
		] );
	}
	return [];
}
