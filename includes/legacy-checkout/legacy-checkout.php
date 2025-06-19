<?php
/***
 * Handle using WooCommerce legacy templates (shortcode, not blocks).
 *
 * @package DM1881
 */

namespace DM1881;

\add_action( 'init', __NAMESPACE__ . '\\initialize_legacy_setup' );

/***
 * Initialize legacy checkout.
 */
function initialize_legacy_setup() {
	if ( ! is_block_checkout() ) {
		$html_hook = \apply_filters( 'dm1881_legacy_checkout_output_hook', 'woocommerce_checkout_before_customer_details' );
		\add_action( $html_hook, __NAMESPACE__ . '\\legacy_checkout_html_output' );
		\add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_legacy_checkout_frontend_assets', 99 );
	}
}

/***
 * Output HTML (shared) in checkout.
 */
function legacy_checkout_html_output() {
	echo render_checkout_lookup_html();  // phpcs:ignore
}

/***
 * Legacy checkout HTML output.
 *
 * @return string
 */
function render_checkout_lookup_html(): string {
	$settings = get_saved_settings();

	// If subscription key is not provided, don't output anything.
	if ( empty( $settings['1881_subscription_key'] ) ) {
		return '';
	}

	$defaults        = get_default_admin_settings();
	$no_results_text = ! empty( $settings['1881_checkout_no_results_msg'] ) ? $settings['1881_checkout_no_results_msg'] : $defaults['1881_checkout_no_results_msg'] ?? '';

	$output = \sprintf(
		'<div class="dm1881-lookup legacy-checkout" id="dm1881-lookup">
			<p class="dm1881-description">%1$s</p>
			<div class="dm1881-logo-input-container">
				<div class="dm1881-logo">%3$s</div>
				<div class="dm1881-input-container wc-block-components-text-input">
					<label for="dm1881-phone-lookup">%2$s</label>
					<input type="tel" id="dm1881-phone-lookup" class="dm1881-lookup-input" autocapitalize="characters" autocomplete="tel" aria-label="%2$s" aria-invalid="false" />
					<div class="dm1881-no-results" role="alert">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 24 24" width="24" height="24" aria-hidden="true" focusable="false">
							<path d="M10 2c4.42 0 8 3.58 8 8s-3.58 8-8 8-8-3.58-8-8 3.58-8 8-8zm1.13 9.38l.35-6.46H8.52l.35 6.46h2.26zm-.09 3.36c.24-.23.37-.55.37-.96 0-.42-.12-.74-.36-.97s-.59-.35-1.06-.35-.82.12-1.07.35-.37.55-.37.97c0 .41.13.73.38.96.26.23.61.34 1.06.34s.8-.11 1.05-.34z"></path>
						</svg>
						<span>%4$s</span>
					</div>
				</div>
			</div>
		</div>',
		\wp_kses_post( $settings['1881_checkout_description'] ),
		esc_html__( 'Phone number for 1881 lookup', '1881-number-lookup' ),
		get_1881_logo(),  // phpcs:ignore
		esc_html( $no_results_text )
	);

	return \apply_filters( 'dm1881_legacy_checkout_html', $output, $settings );
}

/***
 * For legacy checkout, enqueue frontend script.
 */
function enqueue_legacy_checkout_frontend_assets() {
	$build_file  = DM1881_PATH . '/build/frontend.css';
	$assets_file = DM1881_PATH . '/build/frontend.asset.php';
	if ( \file_exists( $build_file ) ) {
		$assets = require $assets_file;
		\wp_enqueue_style(
			'dm1881-view',
			DM1881_URL . '/build/frontend.css',
			$assets['dependencies'],
			$assets['version']
		);
	}

	$build_file  = DM1881_PATH . '/build/legacy-frontend.js';
	$assets_file = DM1881_PATH . '/build/legacy-frontend.asset.php';
	if ( \file_exists( $build_file ) && \file_exists( $assets_file ) ) {
		$assets = require $assets_file;
		\wp_enqueue_script(
			'dm1881-frontend',
			DM1881_URL . '/build/legacy-frontend.js',
			$assets['dependencies'],
			$assets['version'],
			true
		);
		\wp_localize_script(
			'dm1881-frontend',
			'DM1881',
			\apply_filters( 'dm1881_script_localized_variables_legacy', [
				'phone_lookup_rest'   => \get_rest_url( null, 'dm1881/v1/phone_lookup' ),
				'keyup_delay_ms'      => get_keyup_delay(),
				'valid_phone_lengths' => get_phone_valid_lengths(),
			] )
		);
	}
}
