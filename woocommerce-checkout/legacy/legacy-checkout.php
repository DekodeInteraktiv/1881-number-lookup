<?php
/***
 * Add HTML output when using WooCommerce legacy templates (not blocks).
 *
 * @package Woo1881
 */

namespace Woo1881;

\add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_legacy_checkout_frontend_assets', 99 );

/***
 * For legacy checkout, enqueue frontend script.
 */
function enqueue_legacy_checkout_frontend_assets() {
	if ( is_block_checkout() ) {
		return;
	}

	// TODO: Add Woo scripts as dependency? jQuery?
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
				'ajax_url' => \admin_url( 'admin-ajax.php' ),
			] )
		);
	}
}
