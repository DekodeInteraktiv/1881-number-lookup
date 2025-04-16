<?php
/***
 * Add HTML output when using WooCommerce Checkout block.
 *
 * @package Woo1881
 */

namespace Woo1881;

\add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\do_enqueue_block_editor_assets' );

\add_action( 'init', __NAMESPACE__ . '\\register_checkout_block' );
\add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_block_editor_assets' );

/***
 * Register custom checkout block.
 */
function register_checkout_block() {
	if ( ! is_block_checkout() ) {
		return;
	}

	\register_block_type_from_metadata( WOO1881_PATH . '/build/block-checkout-1881/block.json', [
		'render_callback' => __NAMESPACE__ . '\\block_render',
	] );
}

\add_action( 'woocommerce_blocks_loaded', function() {
	\woocommerce_store_api_register_update_callback(
		[
			'namespace' => 'test_with_cart_update',
			'callback'  => function( $data ) {
				write_log('triggered some callback???');
				write_log($data);
				// None of the below works for fucking returning anything back.
				//echo \wp_json_encode( [ 'result' => 'what??' ] );
				//\wp_send_json_success();
				//echo [ 'success' => true ];
				//return [ 'success' => true ];
			},
		]
	);
});

/***
 * Server-side render of block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function block_render( array $attributes ): string {
	$wrapper_attributes = \get_block_wrapper_attributes();
	return \sprintf(
		'<div %s>%s</div>',
		$wrapper_attributes,
		render_checkout_lookup_html()
	);
}

/***
 * Enqueue editor script which adds our block as allowed inside Checkout Innerblocks.
 */
function enqueue_block_editor_assets() {
	if ( ! is_block_checkout() ) {
		return;
	}

	$script_file_path      = WOO1881_PATH . '/build/editor.js';
	$script_file_uri       = WOO1881_URL . '/build/editor.js';
	$script_deps_file_path = WOO1881_PATH . '/build/editor.asset.php';

	if ( \file_exists( $script_file_path ) && \file_exists( $script_deps_file_path ) ) {
		$dependencies = require $script_deps_file_path;
		\wp_enqueue_script( 'woo1881-editor', $script_file_uri, $dependencies['dependencies'], $dependencies['version'], false );
	}
}



// TODO: Inject block in Checkout page hook/trigger.

function inject_checkout_block() {
	if ( ! is_block_checkout() ) {
		return;
	}
	// TODO: Challenge now is that I need to inject block INSIDE the div!
	/*<!-- wp:woocommerce/checkout-contact-information-block -->
	<div class="wp-block-woocommerce-checkout-contact-information-block"></div>
	<!-- /wp:woocommerce/checkout-contact-information-block -->*/
	// Into this:
	/*<!-- wp:woocommerce/checkout-contact-information-block -->
	<div class="wp-block-woocommerce-checkout-contact-information-block"><!-- wp:woo1881/checkout-1881-lookup /--></div>
	<!-- /wp:woocommerce/checkout-contact-information-block -->*/

	$checkout_page_id = \wc_get_page_id( 'checkout' );
	//var_dump($checkout_page_id);
	$checkout_page = \get_post( $checkout_page_id );
	if ( empty( $checkout_page ) || ! \is_a( $checkout_page, '\WP_Post' ) ) {
		return;
	}

	$post_content = $checkout_page->post_content;
	//echo '<pre>' . print_r($post_content, true) . '</pre>';
	var_dump($post_content);

	$find = '<!-- /wp:woocommerce/checkout-contact-information-block -->';
	// If using preg_replace I need this:
	//$find = '/\<\!\-\- \/wp:woocommerce\/checkout\-contact\-information\-block \-\-\>/';
	$new = '<!-- wp:woo1881/checkout-1881-lookup /-->';
	$replace = '<!-- /wp:woocommerce/checkout-contact-information-block -->' . $new;

	//$new_post_content = \preg_replace( $find, $replace, $post_content );
	$new_post_content = \str_replace( $find, $replace, $post_content );
	var_dump('***** new ');
	var_dump($new_post_content);
}


// NOT USED: Replaced with block.
/***
 * Adds filter to append HTML in Checkout if checkout is using blocks.
 * /
function check_for_block_output_filter() {
	if ( ! is_block_checkout() ) {
		return;
	}
	// If Checkout is using block, add filter to block render. Allow devs to change the block render filter.
	$block_render_filter = \apply_filters( 'woo1881_checkout_block_render_filter', 'render_block_woocommerce/checkout-contact-information-block' );
	if ( ! empty( $block_render_filter ) ) {
		\add_filter( $block_render_filter, __NAMESPACE__ . '\\block_render_contact' );
	}
}

/***
 * In case of Checkout block, append out output to its render output.
 *
 * @param string $output WooCommerce block render output.
 * @return string
 * /
function block_render_contact( string $output ): string {
	return $output . render_checkout_lookup_html();
}*/
