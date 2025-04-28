<?php
/***
 * Handle using WooCommerce legacy templates (shortcode, not blocks).
 *
 * @package Woo1881
 */

namespace Woo1881;

\add_action( 'init', __NAMESPACE__ . '\\initialize_legacy_setup' );

/***
 * Initialize legacy checkout.
 */
function initialize_legacy_setup() {
	if ( ! is_block_checkout() ) {
		$html_hook = \apply_filters( 'woo1881_legacy_checkout_output_hook', 'woocommerce_checkout_before_customer_details' );
		\add_action( $html_hook, __NAMESPACE__ . '\\legacy_checkout_html_output' );
		\add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_legacy_checkout_frontend_assets', 99 );
	}
}

/***
 * Output HTML (shared) in checkout.
 */
function legacy_checkout_html_output() {
	echo render_checkout_lookup_html();
}

/***
 * For legacy checkout, enqueue frontend script.
 */
function enqueue_legacy_checkout_frontend_assets() {
	$build_file  = WOO1881_PATH . '/build/frontend.css';
	$assets_file = WOO1881_PATH . '/build/frontend.asset.php';
	if ( \file_exists( $build_file ) ) {
		$assets = require $assets_file;
		\wp_enqueue_style(
			'woo1881-view',
			WOO1881_URL . '/build/frontend.css',
			$assets['dependencies'],
			$assets['version']
		);
	}

	$build_file  = WOO1881_PATH . '/build/legacy-frontend.js';
	$assets_file = WOO1881_PATH . '/build/legacy-frontend.asset.php';
	if ( \file_exists( $build_file ) && \file_exists( $assets_file ) ) {
		$assets = require $assets_file;
		\wp_enqueue_script(
			'woo1881-frontend',
			WOO1881_URL . '/build/legacy-frontend.js',
			$assets['dependencies'],
			$assets['version'],
			true
		);
		\wp_localize_script(
			'woo1881-frontend',
			'Woo1881',
			\apply_filters( 'woo1881_script_localized_variables', [
				'phone_lookup_rest' => \get_rest_url( null, 'woo1881/v1/phone_lookup' ),
				'keyup_delay_ms'    => \apply_filters( 'woo1881_keyup_delay_ms', 500 ),
			] )
		);
	}
}
