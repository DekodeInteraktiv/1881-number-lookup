<?php
/***
 * WooCommerce Integration block.
 *
 * @package DM1881
 */

namespace DM1881;

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

/***
 * Class Blocks_1881_Integration.
 *
 * @package DM1881
 */
class Blocks_1881_Integration implements IntegrationInterface {

	/***
	 * Returns name of the block (without dm1881 domain).
	 *
	 * @return string
	 */
	public function get_name() {
		return 'checkout-block-1881-lookup';
	}

	/***
	 * Initialize integration.
	 */
	public function initialize() {
		$this->register_block_frontend_scripts();
		$this->register_block_editor_scripts();
	}

	/***
	 * Array of data made available to the block on frontend.
	 *
	 * @return array
	 */
	public function get_script_data() {
		$settings = get_saved_settings();
		$defaults = get_default_admin_settings();
		return [
			'phone_lookup_rest'   => \get_rest_url( null, 'dm1881/v1/phone_lookup' ),
			'keyup_delay_ms'      => get_keyup_delay(),
			'description_text'    => ! empty( $settings['1881_checkout_description'] ) ? $settings['1881_checkout_description'] : '',
			'lookup_label'        => esc_html__( 'Phone number for 1881 lookup', '1881-number-lookup' ),
			'no_results_text'     => ! empty( $settings['1881_checkout_no_results_msg'] ) ? $settings['1881_checkout_no_results_msg'] : $defaults['1881_checkout_no_results_msg'] ?? '',
			'logo_1881_svg'       => get_1881_logo(),
			'valid_phone_lengths' => get_phone_valid_lengths(),
		];
	}

	/***
	 * Returns array of script handles to enqueue in frontend.
	 *
	 * @return string[]
	 */
	public function get_script_handles() {
		return [
			'dm1881-block-frontend',
		];
	}

	/***
	 * Returns array of script handles to enqueue in the editor.
	 *
	 * @return string[]
	 */
	public function get_editor_script_handles() {
		return [
			'dm1881-block-editor',
		];
	}

	/***
	 * Register scripts for editor.
	 */
	public function register_block_editor_scripts() {
		$build_file  = DM1881_PATH . '/build/checkout-block-1881-lookup/index.js';
		$assets_file = DM1881_PATH . '/build/checkout-block-1881-lookup/index.asset.php';
		if ( \file_exists( $build_file ) && \file_exists( $assets_file ) ) {
			$assets = require $assets_file;
			\wp_register_script(
				'dm1881-block-editor',
				DM1881_URL . '/build/checkout-block-1881-lookup/index.js',
				$assets['dependencies'],
				$assets['version'],
				true
			);
		}
	}

	/***
	 * Register scripts for frontend.
	 */
	public function register_block_frontend_scripts() {
		$build_file  = DM1881_PATH . '/build/checkout-block-1881-lookup/block.js';
		$assets_file = DM1881_PATH . '/build/checkout-block-1881-lookup/block.asset.php';
		if ( \file_exists( $build_file ) && \file_exists( $assets_file ) ) {
			$assets = require $assets_file;
			\wp_register_script(
				'dm1881-block-frontend',
				DM1881_URL . '/build/checkout-block-1881-lookup/block.js',
				$assets['dependencies'],
				$assets['version'],
				true
			);
		}

		// CSS must ble enqueued as IntegrationInterface does not support styles, nor will block.json styles work either.
		$build_file  = DM1881_PATH . '/build/checkout-block-1881-lookup/view.css';
		$assets_file = DM1881_PATH . '/build/checkout-block-1881-lookup/view.asset.php';
		if ( \file_exists( $build_file ) ) {
			$assets = require $assets_file;
			\wp_enqueue_style(
				'dm1881-view',
				DM1881_URL . '/build/checkout-block-1881-lookup/view.css',
				$assets['dependencies'],
				$assets['version']
			);
		}
	}
}
