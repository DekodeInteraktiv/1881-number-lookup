<?php
/**
 * Plugin Name:          WooCommerce 1881 Integration
 * Plugin URI:           https://www.dekode.no
 * Description:          Adds lookup of adress and contact information from 1881 in WooCommerce checkout.
 * Author:               Dekode
 * Author URI:           https://www.dekode.no
 * Developer:            Dekode
 * Developer URI:        https://www.dekode.no
 * Version:              1.0
 * Text Domain:          woo1881
 * Domain Path:          /languages
 * Requires Plugins:     woocommerce
 * WC requires at least: 8.2
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Woo1881
 */

namespace Woo1881;

defined( 'ABSPATH' ) || exit;

define( 'WOO1881_FILE', __FILE__ );
define( 'WOO1881_PATH', __DIR__ );
define( 'WOO1881_URL', \plugins_url( '', __FILE__ ) );

/***
 * Check if WooCommerce is active before running code.
 */
if ( \in_array( 'woocommerce/woocommerce.php', \apply_filters( 'active_plugins', \get_option( 'active_plugins' ) ) ) || ( \is_multisite() && \array_key_exists( 'woocommerce/woocommerce.php', \get_site_option( 'active_sitewide_plugins' ) ) ) ) {
	require_once WOO1881_PATH . '/includes/core.php';
} else {
	\add_action( 'admin_notices', __NAMESPACE__ . '\\display_woo_not_installed_notice' );
}

/***
 * Display Woocommerce Activation notice.
 */
function display_woo_not_installed_notice() {
	?>
	<div class="error">
		<p><?php echo esc_html__( 'WooCommerce 1881 requires WooCommerce. Please install or activate WooCommerce', 'woo1881' ); ?></p>
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
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WOO1881_FILE, true );
		}
	}
);
