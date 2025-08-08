<?php
/***
 * Admin setup.
 *
 * @package DM1881
 */

namespace DM1881\Admin;

use function DM1881\get_1881_logo;

\add_action( 'admin_menu', __NAMESPACE__ . '\\register_admin_menu', 99 );
\add_action( 'admin_init', __NAMESPACE__ . '\\initialize_admin_settings' );

/***
 * Register a submenu page to WooCommerce admin menu.
 */
function register_admin_menu() {
	\add_submenu_page(
		'woocommerce',
		esc_html__( '1881 Number Lookup', '1881-number-lookup' ),
		esc_html__( '1881 Number Lookup', '1881-number-lookup' ),
		\apply_filters( 'dm1881_admin_page_capability', 'manage_woocommerce' ),
		'dm1881_settings_page',
		__NAMESPACE__ . '\\custom_admin_page_content'
	);
}

/***
 * Content of custom admin settings page.
 */
function custom_admin_page_content() {
	?>
	<div class="wrap woocommerce">
		<div class="dm1881-admin-header">
			<div class="dm1881-logo"><?php echo get_1881_logo();  // phpcs:ignore ?></div>
			<div class="dm1881-header-content">
				<h2><?php esc_html_e( '1881 Number Lookup for WooCommerce Checkout', '1881-number-lookup' ); ?></h2>
				<p><?php esc_html_e( 'Start using 1881 integration in checkout by providing your subscription key below.', '1881-number-lookup' ); ?></p>
			</div>
		</div>
		<p>
			<?php
			\printf(
				'%s <a href="%s" target="_blank">api1881.no</a> %s',
				esc_html__( 'To find or generate a subscription key, log into', '1881-number-lookup' ),
				\esc_url( 'https://www.api1881.no/' ),
				esc_html__( 'and navigate to your profile to find your subscriptions. Either create a new set of API keys, or provide the primary key for an existing one.', '1881-number-lookup' )
			);
			?>
		</p>
		<form action="options.php" method="post">
			<?php
			\settings_fields( 'dm1881_admin_settings' );
			\do_settings_sections( 'dm1881_admin_settings' );
			\submit_button();
			?>
		</form>
		<div class="dm1881-test-authentication"></div>
	</div>
	<style>
		.dm1881-admin-header {
			display: flex;
			gap: 20px;
		}
		.dm1881-logo {
			height: 70px;
			width: 70px;
		}
		.dm1881-header-content {
			flex: 1;
		}
		.dm1881-header-content h2 {
			margin-top: 0.5em;
		}
	</style>
	<?php
}

/***
 * Set up and register admin settings fields.
 */
function initialize_admin_settings() {
	$option = 'dm1881_admin_settings';

	if ( false === \get_option( $option ) ) {
		\add_option( $option, \DM1881\get_default_admin_settings() );
	}

	$defaults = \DM1881\get_default_admin_settings();

	\add_settings_section(
		'dm1881_authentication',
		esc_html__( '1881 Authentication', '1881-number-lookup' ),
		'__return_null',
		$option,
	);

	\add_settings_field(
		'1881_subscription_key',
		esc_html__( 'Subscription key (primary)', '1881-number-lookup' ),
		__NAMESPACE__ . '\\setting_text_element_callback',
		$option,
		'dm1881_authentication',
		[
			'menu' => $option,
			'id'   => '1881_subscription_key',
			'size' => '50',
		]
	);

	\add_settings_section(
		'dm1881_checkout_settings',
		esc_html__( 'Checkout Settings', '1881-number-lookup' ),
		'__return_null',
		$option,
	);

	\add_settings_field(
		'1881_checkout_description',
		esc_html__( 'Checkout text', '1881-number-lookup' ),
		__NAMESPACE__ . '\\setting_textarea_element_callback',
		$option,
		'dm1881_checkout_settings',
		[
			'menu'        => $option,
			'id'          => '1881_checkout_description',
			'width'       => '100',
			'height'      => '4',
			'default'     => isset( $defaults['1881_checkout_description'] ) ? $defaults['1881_checkout_description'] : '',
			'description' => esc_html__( 'The text shown in checkout before the phone field.', '1881-number-lookup' ),
		]
	);

	\add_settings_field(
		'1881_checkout_no_results_msg',
		esc_html__( 'Message text when number search gave no results', '1881-number-lookup' ),
		__NAMESPACE__ . '\\setting_text_element_callback',
		$option,
		'dm1881_checkout_settings',
		[
			'menu'    => $option,
			'id'      => '1881_checkout_no_results_msg',
			'size'    => '100',
			'default' => isset( $defaults['1881_checkout_no_results_msg'] ) ? $defaults['1881_checkout_no_results_msg'] : '',
		]
	);

	// Allow devs to add additional fields.
	\do_action( 'dm1881_settings_add_settings_fields' );

	\register_setting( $option, $option, [
		'type'              => 'string',
		'sanitize_callback' => __NAMESPACE__ . '\\validate_settings'
	] );
}

/***
 * Output standard text input element.
 *
 * @param array $args Arguments to setting.
 */
function setting_text_element_callback( array $args ) {
	$id   = $args['id'];
	$size = isset( $args['size'] ) ? $args['size'] : '25';

	$options = \get_option( $args['menu'] );

	if ( isset( $options[ $id ] ) ) {
		$current = $options[ $id ];
	} else {
		$current = isset( $args['default'] ) ? $args['default'] : '';
	}

	\printf(
		'<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" size="%4$s"/>',
		\esc_attr( $id ),
		\esc_attr( $args['menu'] ),
		\esc_html( $current ),
		\esc_attr( $size )
	);

	if ( isset( $args['description'] ) ) {
		\printf(
			'<p class="description">%s</p>',
			\wp_kses_post( $args['description'] )
		);
	}
}

/***
 * Output standard textarea element.
 *
 * @param array $args Arguments to setting.
 */
function setting_textarea_element_callback( array $args ) {
	$id     = $args['id'];
	$width  = ! empty( $args['width'] ) ? $args['width'] : '';
	$height = ! empty( $args['height'] ) ? $args['height'] : '';

	$options = \get_option( $args['menu'] );
	if ( isset( $options[ $id ] ) ) {
		$current = $options[ $id ];
	} else {
		$current = isset( $args['default'] ) ? $args['default'] : '';
	}

	\printf(
		'<textarea id="%1$s" name="%2$s[%1$s]" cols="%4$s" rows="%5$s"/>%3$s</textarea>',
		\esc_attr( $id ),
		\esc_attr( $args['menu'] ),
		\wp_kses_post( $current ),
		\esc_attr( $width ),
		\esc_attr( $height )
	);

	if ( isset( $args['description'] ) ) {
		\printf(
			'<p class="description">%s</p>',
			\wp_kses_post( $args['description'] )
		);
	}
}

/***
 * Validate settings.
 *
 * @param array $input Settings fields to validate.
 * @return array
 */
function validate_settings( array $input ): array {
	$output = [];
	foreach ( $input as $key => $value ) {
		if ( isset( $input[ $key ] ) ) {
			if ( \is_array( $input[ $key ] ) ) {
				foreach ( $input[ $key ] as $sub_key => $sub_value ) {
					$output[ $key ][ $sub_key ] = $input[ $key ][ $sub_key ];
				}
			} else {
				$output[ $key ] = $input[ $key ];
			}
		}
	}

	return \apply_filters( 'dm1881_settings_validate_input', $output, $input );
}
