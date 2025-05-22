<?php
/***
 * Admin setup.
 *
 * @package Woo1881
 */

namespace Woo1881\Admin;

use function Woo1881\get_1881_logo;\add_action( 'admin_menu', __NAMESPACE__ . '\\register_admin_menu', 99 );
\add_action( 'admin_init', __NAMESPACE__ . '\\initialize_admin_settings' );

/***
 * Register a submenu page to WooCommerce admin menu.
 */
function register_admin_menu() {
	\add_submenu_page(
		'woocommerce',
		esc_html__( 'Woo 1881', 'woo1881' ),
		esc_html__( 'Woo 1881', 'woo1881' ),
		\apply_filters( 'woo1881_admin_page_capability', 'manage_woocommerce' ),
		'woo1881_settings_page',
		__NAMESPACE__ . '\\custom_admin_page_content'
	);
}

/***
 * Content of custom admin settings page.
 */
function custom_admin_page_content() {
	?>
	<div class="wrap woocommerce">
		<div class="woo1881-admin-header">
			<div class="woo1881-logo"><?php echo get_1881_logo(); ?></div>
			<div class="woo1881-header-content">
				<h2><?php esc_html_e( 'WooCommerce 1881 Integration', 'woo1881' ); ?></h2>
				<p><?php esc_html_e( 'Start using 1881 integration in checkout by providing your subscription key below.', 'woo1881' ); ?></p>
			</div>
		</div>
		<p>
			<?php
			\printf(
				'%s <a href="%s" target="_blank">api1881.no</a> %s',
				esc_html__( 'To find or generate a subscription key, log into', 'woo1881' ),
				\esc_url( 'https://www.api1881.no/' ),
				esc_html__( 'and navigate to your profile to find your subscriptions. Either create a new set of API keys, or provide the primary key for an existing one.', 'woo1881' )
			);
			?>
		</p>
		<form action="options.php" method="post">
			<?php
			\settings_fields( 'woo1881_admin_settings' );
			\do_settings_sections( 'woo1881_admin_settings' );
			\submit_button();
			?>
		</form>
		<div class="woo1881-test-authentication"></div>
	</div>
	<style>
		.woo1881-admin-header {
			display: flex;
			gap: 20px;
		}
		.woo1881-logo {
			height: 70px;
			width: 70px;
		}
		.woo1881-header-content {
			flex: 1;
		}
		.woo1881-header-content h2 {
			margin-top: 0.5em;
		}
	</style>
	<?php
}

/***
 * Set up and register admin settings fields.
 */
function initialize_admin_settings() {
	$option = 'woo1881_admin_settings';

	if ( false === \get_option( $option ) ) {
		\add_option( $option, \Woo1881\get_default_admin_settings() );
	}

	$defaults = \Woo1881\get_default_admin_settings();

	\add_settings_section(
		'woo1881_authentication',
		esc_html__( '1881 Authentication', 'woo1881' ),
		'__return_null',
		$option,
	);

	\add_settings_field(
		'1881_subscription_key',
		esc_html__( 'Subscription key (primary)', 'woo1881' ),
		__NAMESPACE__ . '\\setting_text_element_callback',
		$option,
		'woo1881_authentication',
		[
			'menu' => $option,
			'id'   => '1881_subscription_key',
			'size' => '50',
		]
	);

	\add_settings_section(
		'woo1881_checkout_settings',
		esc_html__( 'Checkout Settings', 'woo1881' ),
		'__return_null',
		$option,
	);

	\add_settings_field(
		'1881_checkout_description',
		esc_html__( 'Checkout text', 'woo1881' ),
		__NAMESPACE__ . '\\setting_textarea_element_callback',
		$option,
		'woo1881_checkout_settings',
		[
			'menu'        => $option,
			'id'          => '1881_checkout_description',
			'width'       => '100',
			'height'      => '4',
			'default'     => isset( $defaults['1881_checkout_description'] ) ? $defaults['1881_checkout_description'] : '',
			'description' => esc_html__( 'The text shown in checkout before the phone field.', 'woo1881' ),
		]
	);

	// Allow devs to add additional fields.
	\do_action( 'woo1881_settings_add_settings_fields' );

	\register_setting( $option, $option, __NAMESPACE__ . '\\validate_settings' );
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

	return \apply_filters( 'woo1881_settings_validate_input', $output, $input );
}
