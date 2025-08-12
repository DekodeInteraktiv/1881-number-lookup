/**
 * WordPress dependencies
 */
const defaultConfig = require('@wordpress/scripts/config/webpack.config');

/**
 * External dependencies
 */
const WooCommerceDependencyExtractionWebpackPlugin = require('@woocommerce/dependency-extraction-webpack-plugin');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');

module.exports = {
	...defaultConfig,
	entry: {
		...defaultConfig.entry(),
		frontend: './src/frontend.css',
		admin: './src/admin.css',
		'checkout-block-1881-lookup/view': './src/checkout-block-1881-lookup/view.css',
	},
	plugins: [
		...defaultConfig.plugins.filter((plugin) => plugin.constructor.name !== 'DependencyExtractionWebpackPlugin'),
		new RemoveEmptyScriptsPlugin(),
		new WooCommerceDependencyExtractionWebpackPlugin(),
	],
};
