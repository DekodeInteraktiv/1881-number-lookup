<?php
/**
 * Plugin Name:          1881 Number Lookup
 * Description:          Adds lookup of address and contact information from 1881 in WooCommerce checkout.
 * Author:               Digitale Medier 1881
 * Author URI:           https://www.1881.no
 * Version:              1.0.6
 * Text Domain:          1881-number-lookup
 * Domain Path:          /languages
 * Requires Plugins:     woocommerce
 * Requires PHP:         7.4
 * Requires at least:    6.0
 * WC requires at least: 8.2
 * WC tested up to:      10.3
 *
 * License: GNU General Public License v3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package DM1881
 */

namespace DM1881;

\defined( 'ABSPATH' ) || exit;

\define( 'DM1881_FILE', __FILE__ );
\define( 'DM1881_PATH', __DIR__ );
\define( 'DM1881_URL', \plugins_url( '', __FILE__ ) );

\add_action( 'plugins_loaded', function () {
	/***
	 * Check if WooCommerce is active before running code.
	 */
	if ( \in_array( 'woocommerce/woocommerce.php', \apply_filters( 'active_plugins', \get_option( 'active_plugins' ) ), true ) || ( \is_multisite() && \array_key_exists( 'woocommerce/woocommerce.php', \get_site_option( 'active_sitewide_plugins' ) ) ) ) {
		require_once DM1881_PATH . '/includes/core.php';
	} else {
		\add_action( 'admin_notices', __NAMESPACE__ . '\\display_woo_not_installed_notice' );
	}
} );

/***
 * Display Woocommerce Activation notice.
 */
function display_woo_not_installed_notice() {
	?>
	<div class="error">
		<p><?php echo esc_html__( '1881 Number Lookup requires WooCommerce. Please install or activate WooCommerce', '1881-number-lookup' ); ?></p>
	</div>
	<?php
}

/***
 * Declare HPOS (High-performance order storage) compatibility.
 * Necessary as to not block HPOS for webshops.
 */
\add_action(
	'before_woocommerce_init',
	function () {
		if ( \class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', DM1881_FILE, true );
		}
	}
);
