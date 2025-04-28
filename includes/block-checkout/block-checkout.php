<?php
/***
 * Add HTML output when using WooCommerce Checkout block.
 *
 * @package Woo1881
 */

namespace Woo1881;

\add_action( 'woocommerce_blocks_loaded', __NAMESPACE__ . '\\initialize_checkout_block_setup' );

/***
 * Initialize block checkout.
 */
function initialize_checkout_block_setup() {
	// Probably superfluous check as this hook exists only for Woo with blocks, but just to be safe.
	if ( ! is_block_checkout() ) {
		return;
	}

	// Register WooCommerce Integration block in registry.
	require_once WOO1881_PATH . '/includes/block-checkout/class-blocks-1881-integration.php';
	\add_action( 'woocommerce_blocks_checkout_block_registration', function( $integration_registry ) {
		$integration_registry->register( new Blocks_1881_Integration() );
	} );
}
