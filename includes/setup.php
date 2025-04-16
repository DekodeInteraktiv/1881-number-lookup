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
require_once WOO1881_PATH . '/woocommerce-checkout/blocks/block-checkout.php';
require_once WOO1881_PATH . '/woocommerce-checkout/legacy/legacy-checkout.php';

\add_action( 'rest_api_init', __NAMESPACE__ . '\\add_rest_route' );

/**
 * Register REST endpoint for request 1881 search.
 */
function add_rest_route() {
	\register_rest_route(
		'woo1881/v1',
		'/search',
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

	$search_results = do_1881_api_phone_lookup( $phone );
	$search_results = \apply_filters( 'woo1881_contacts_from_lookup', $search_results, $phone );

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
	$is_block = is_block_checkout();

	$output = \sprintf(
		'<div class="woo1881-lookup" id="woo1881-lookup">
			<p class="woo1881-description">%1$s</p>
			<div class="woo1881-input-container wc-block-components-text-input">
				<label for="woo1881-phone-lookup">%2$s</label>
				<input type="tel" id="woo1881-phone-lookup" class="woo1881-lookup-input" autocapitalize="characters" autocomplete="tel" aria-label="%2$s" aria-invalid="false" />
			</div>
		</div>',
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
	// return \Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils::is_checkout_block_default();
}
