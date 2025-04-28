<?php
/***
 * Core plugin setup and initialization.
 *
 * @package Woo1881
 */

namespace Woo1881;

require_once WOO1881_PATH . '/admin/admin.php';
require_once WOO1881_PATH . '/api/api.php';
require_once WOO1881_PATH . '/includes/variables.php';
require_once WOO1881_PATH . '/includes/block-checkout/block-checkout.php';
require_once WOO1881_PATH . '/includes/legacy-checkout/legacy-checkout.php';

\add_action( 'rest_api_init', __NAMESPACE__ . '\\add_rest_route' );

/**
 * Register REST endpoint for request 1881 search.
 */
function add_rest_route() {
	\register_rest_route(
		'woo1881/v1',
		'/phone_lookup',
		[
			'method'              => \WP_REST_SERVER::READABLE,
			'callback'            => __NAMESPACE__ . '\\rest_perform_search',
			'permission_callback' => '__return_true',
			'args'                => [
				'phone' => [
					'required' => true,
					'type'     => 'string',
				],
			],
		]
	);
}

/**
 * Return of REST response for searching 1881.
 *
 * @return \WP_REST_Response
 */
function rest_perform_search( \WP_REST_Request $request = null ) {
	$phone = $request->get_param( 'phone' );
	if ( empty( $phone ) ) {
		return new \WP_REST_Response( [
			'success' => false,
			'message' => \esc_html__( 'No phone number provided', WOO1881_PLUGIN_DOMAIN ),
		], 200 );
	}

	$phone = validate_phone_number( $phone );
	if ( empty( $phone ) ) {
		return new \WP_REST_Response( [
			'success' => false,
			'message' => \esc_html__( 'Phone number failed validation', WOO1881_PLUGIN_DOMAIN ),
		], 200 );
	}

	$transient_key = 'woo1881_phonelookup_' . $phone;

	// TODO: Set this back when done debugging.
	// MINUTE_IN_SECONDS * 30

	$search_results = \get_transient( $transient_key );
	if ( false === $search_results ) {
		$search_results = do_1881_api_phone_lookup( $phone );
		\set_transient( $transient_key, $search_results, \apply_filters( 'woo1881_cache_phone_lookup_time', MINUTE_IN_SECONDS * 10000 ) );
	}

	$search_results = \apply_filters( 'woo1881_contacts_from_lookup', $search_results, $phone );

	// Parse the results into a easy-to-handle format for JS.
	$search_results = parse_contactinfo_for_frontend( $search_results );

	return new \WP_REST_Response( [
		'success'       => true,
		'search_result' => $search_results,
	], 200 );
}

/***
 * Validate phone number before sending request to 1881.
 * Return empty string back to cancel 1881 search.
 *
 * @param string $phone_number Phone number to validate.
 * @return string
 */
function validate_phone_number( string $phone_number ): string {
	return \apply_filters( 'woo1881_validate_phone_number_before_search', $phone_number );
}

/***
 * Parse the 1881 results and filter out the info we want to pass on to frontend.
 *
 * @param array $search_results Search results directly from 1881 API.
 * @return array
 */
function parse_contactinfo_for_frontend( array $search_results ): array {
	$formatted = [];

	$address_truncate_length = \apply_filters( 'woo1881_autocomplete_address_truncate_length', 15 );

	foreach ( $search_results as $item ) {
		$type     = $item['type'];
		$new_item = [
			'type' => $item['type'],
		];

		$new_item['first_name'] = ! empty( $item['firstName'] ) ? $item['firstName'] : '';
		$new_item['last_name']  = ! empty( $item['lastName'] ) ? $item['lastName'] : '';

		// Find email.
		$new_item['email'] = '';
		if ( ! empty( $item['contactPoints'] ) ) {
			foreach ( $item['contactPoints'] as $contact ) {
				if ( 'Email' === $contact['type'] ) {
					$new_item['email'] = $contact['value'];
				}
			}
		}

		if ( 'Company' === $type ) {
			$new_item['company_name'] = $item['name'];
			$new_item['orgno']        = $item['organizationNumber'];

			// "address" is always used for shipping, and also for billing but only if "postAddress" is empty.
			$address = [
				'street_address' => $item['legalInformation']['address']['street'],
				'zip'            => $item['legalInformation']['address']['postCode'],
				'city'           => $item['legalInformation']['address']['postArea'],
			];
			if ( ! empty( $item['legalInformation']['address']['houseNumber'] ) ) {
				$address['street_address'] .= ' ' . $item['legalInformation']['address']['houseNumber'];
			}

			if ( ! empty( $item['legalInformation']['postAddress'] ) ) {
				$new_item['shipping_address'] = $address;

				// Then use "postAddress" as billing.
				$address = [
					'street_address' => $item['legalInformation']['postAddress']['street'],
					'zip'            => $item['legalInformation']['postAddress']['postCode'],
					'city'           => $item['legalInformation']['postAddress']['postArea'],
				];
				if ( ! empty( $item['legalInformation']['postAddress']['houseNumber'] ) ) {
					$address['street_address'] .= ' ' . $item['legalInformation']['postAddress']['houseNumber'];
				}
				$new_item['billing_address'] = $address;
			} else {
				// No postaddress given for company, use "address" for both billing and shipping.
				$new_item['billing_address']  = $address;
				$new_item['shipping_address'] = $address;
			}

			// Build display in autocomplete.
			$new_item['autocomplete_display'] = $item['name'] . ' (';
			$truncated_address = ( ! empty( $item['legalInformation']['postAddress'] ) ) ? $item['legalInformation']['postAddress']['addressString'] : $item['legalInformation']['address']['addressString'];
			if ( \strlen( $truncated_address ) > $address_truncate_length ) {
				$truncated_address = \substr( $truncated_address, 0, $address_truncate_length ) . '...';
			}
			$new_item['autocomplete_display'] .= $truncated_address . ')';

		} elseif ( 'Person' === $type ) {
			// Person has only one address.
			$address = [
				'street_address' => $item['geography']['address']['street'],
				'zip'            => $item['geography']['address']['postCode'],
				'city'           => $item['geography']['address']['postArea'],
			];
			if ( ! empty( $item['geography']['address']['houseNumber'] ) ) {
				$address['street_address'] .= ' ' . $item['geography']['address']['houseNumber'];
			}
			$new_item['billing_address']  = $address;
			$new_item['shipping_address'] = $address;

			// Build display in autocomplete.
			$new_item['autocomplete_display'] = $item['name'] . ' (';
			$truncated_address               = $item['geography']['address']['addressString'];
			if ( \strlen( $truncated_address ) > $address_truncate_length ) {
				$truncated_address = \substr( $truncated_address, 0, $address_truncate_length ) . '...';
			}
			$new_item['autocomplete_display'] .= $truncated_address . ')';
		}

		$formatted[] = $new_item;
	}

	return \apply_filters( 'woo1881_contacts_formatted', $formatted, $search_results );
}

/***
 * Main render of checkout HTML output.
 *
 * @return string
 */
function render_checkout_lookup_html(): string {
	$settings = \get_option( 'woo1881_admin_settings' );

	// If subscription key is not provided, don't output anything.
	if ( empty( $settings['1881_subscription_key'] ) ) {
		return '';
	}

	$is_block      = is_block_checkout();
	$wrapper_class = $is_block ? 'block-checkout' : 'legacy-checkout';

	$output = \sprintf(
		'<div class="woo1881-lookup %1$s" id="woo1881-lookup">
			<p class="woo1881-description">%2$s</p>
			<div class="woo1881-input-container wc-block-components-text-input">
				<label for="woo1881-phone-lookup">%3$s</label>
				<input type="tel" id="woo1881-phone-lookup" class="woo1881-lookup-input" autocapitalize="characters" autocomplete="tel" aria-label="%3$s" aria-invalid="false" />
			</div>
		</div>',
		\esc_attr( $wrapper_class ),
		\wp_kses_post( $settings['1881_checkout_description'] ),
		\esc_html__( 'Phone number for 1881 lookup', WOO1881_PLUGIN_DOMAIN )
	);

	return \apply_filters( 'woo1881_checkout_html', $output, $settings, $is_block );
}

/***
 * Returns whether or not checkout is using WooCommerce's new Checkout block.
 *
 * @return bool
 */
function is_block_checkout(): bool {
	if ( ! \class_exists( '\WC_Blocks_Utils' ) ) {
		return false;
	}
	return \WC_Blocks_Utils::has_block_in_page( \wc_get_page_id( 'checkout' ), 'woocommerce/checkout' );
}
