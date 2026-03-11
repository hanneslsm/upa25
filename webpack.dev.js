/**
 * Webpack development configuration.
 *
 * Extends common config with BrowserSync for live reload.
 *
 * @package UPA25
 * @see     webpack.common.js Shared configuration.
 */

const { merge } = require( 'webpack-merge' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const { buildBaseConfig, moduleConfig } = require( './webpack.common' );

// Optional BrowserSync plugin (only available when installed).
let BrowserSyncPlugin = null;
try {
	BrowserSyncPlugin = require( 'browser-sync-webpack-plugin' );
} catch ( error ) {
	BrowserSyncPlugin = null;
}

/**
 * Environment-driven BrowserSync configuration.
 *
 * @type {Object}
 */
const CONFIG = {
	BS_PROXY: process.env.PROLOOKS_BS_PROXY || '',
	BS_HOST: process.env.PROLOOKS_BS_HOST || 'localhost',
	BS_PORT: Number( process.env.PROLOOKS_BS_PORT || 3000 ),
};

/**
 * Get BrowserSync plugin if configured.
 *
 * @return {Array} Array containing BrowserSync plugin or empty.
 */
function getBrowserSyncPlugin() {
	if ( ! CONFIG.BS_PROXY || ! BrowserSyncPlugin ) {
		return [];
	}

	return [
		new BrowserSyncPlugin(
			{
				host: CONFIG.BS_HOST,
				port: CONFIG.BS_PORT,
				proxy: CONFIG.BS_PROXY,
				files: [
					'theme.json',
					'**/*.php',
					'build/**/*.css',
					'build/**/*.js',
				],
				open: false,
				injectChanges: true,
			},
			{ reload: false }
		),
	];
}

/**
 * Build development configuration.
 *
 * @return {Object|Array} Webpack configuration.
 */
module.exports = () => {
	const baseConfig = buildBaseConfig( 'development' );

	const devConfig = merge( baseConfig, {
		plugins: [ ...getBrowserSyncPlugin() ],
	} );

	// Return array of configs if moduleConfig exists (for Interactivity API support).
	if ( moduleConfig ) {
		const customModuleConfig = merge( moduleConfig, {
			plugins: [
				new MiniCssExtractPlugin( {
					filename: '[name].css',
					chunkFilename: '[name].css',
				} ),
			],
		} );
		return [ devConfig, customModuleConfig ];
	}

	return devConfig;
};
