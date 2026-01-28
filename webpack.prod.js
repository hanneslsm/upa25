/**
 * Webpack production configuration.
 *
 * Extends common config with image optimization and theme version sync.
 *
 * @package UPA25
 * @see     webpack.common.js Shared configuration.
 */

const fs = require( 'fs' );
const { merge } = require( 'webpack-merge' );
const CopyWebpackPlugin = require( 'copy-webpack-plugin' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const sharp = require( 'sharp' );
const { optimize } = require( 'svgo' );
const { PATHS, buildBaseConfig, moduleConfig } = require( './webpack.common' );

// Used to sync the theme header version after each build.
const packageJson = require( './package.json' );

/**
 * Environment-driven image optimization settings.
 *
 * @type {Object}
 */
const CONFIG = {
	IMG_MAX_WIDTH: Number( process.env.PROLOOKS_IMG_MAX_WIDTH || 2560 ),
	QUALITY_JPEG: Number( process.env.PROLOOKS_QUALITY_JPEG || 50 ),
	QUALITY_PNG: Number( process.env.PROLOOKS_QUALITY_PNG || 50 ),
	QUALITY_AVIF: Number( process.env.PROLOOKS_QUALITY_AVIF || 50 ),
	QUALITY_WEBP: Number( process.env.PROLOOKS_QUALITY_WEBP || 70 ),
	QUALITY_WEBP_CONVERT: Number(
		process.env.PROLOOKS_QUALITY_WEBP_CONVERT || 60
	),
	COPY_IMAGES_IN_PROD:
		( process.env.PROLOOKS_COPY_IMAGES_IN_PROD || 'true' ).toLowerCase() ===
		'true',
};

/**
 * Image transformation helpers.
 *
 * @type {Object}
 */
const imageTransforms = {
	/**
	 * Transform raster images (resize and optimize).
	 *
	 * @param {Buffer} content      - Image buffer.
	 * @param {string} absolutePath - Absolute path to the image.
	 * @return {Promise<Buffer>} Transformed image buffer.
	 */
	raster: ( content, absolutePath ) => {
		const path = require( 'path' );
		const ext = path.extname( absolutePath ).toLowerCase();
		const img = sharp( content ).resize( {
			width: CONFIG.IMG_MAX_WIDTH,
			withoutEnlargement: true,
		} );

		const qualityMap = {
			'.jpg': CONFIG.QUALITY_JPEG,
			'.jpeg': CONFIG.QUALITY_JPEG,
			'.png': CONFIG.QUALITY_PNG,
			'.avif': CONFIG.QUALITY_AVIF,
			'.webp': CONFIG.QUALITY_WEBP,
		};

		if ( ext === '.webp' ) {
			return img.webp( { quality: qualityMap[ ext ] } ).toBuffer();
		}
		if ( ext === '.avif' ) {
			return img.avif( { quality: qualityMap[ ext ] } ).toBuffer();
		}
		if ( ext === '.png' ) {
			return img.png( { quality: qualityMap[ ext ] } ).toBuffer();
		}
		if ( ext === '.jpg' || ext === '.jpeg' ) {
			return img.jpeg( { quality: qualityMap[ ext ] } ).toBuffer();
		}
		return content;
	},

	/**
	 * Convert images to WebP format.
	 *
	 * @param {Buffer} content - Image buffer.
	 * @return {Promise<Buffer>} WebP image buffer.
	 */
	webp: ( content ) =>
		sharp( content )
			.resize( { width: CONFIG.IMG_MAX_WIDTH, withoutEnlargement: true } )
			.webp( { quality: CONFIG.QUALITY_WEBP_CONVERT } )
			.toBuffer(),

	/**
	 * Optimize SVG files.
	 *
	 * @param {Buffer} content - SVG buffer.
	 * @return {Buffer} Optimized SVG buffer.
	 */
	svg: ( content ) => {
		try {
			const result = optimize( content.toString(), {
				multipass: true,
				plugins: [
					'removeDimensions',
					'removeTitle',
					'removeDesc',
					'removeUselessDefs',
				],
			} );
			return Buffer.from( result.data );
		} catch ( error ) {
			// eslint-disable-next-line no-console
			console.warn(
				'SVG optimization failed, returning original content:',
				error.message
			);
			return content;
		}
	},
};

/**
 * Image copy patterns for production builds.
 *
 * @type {Array}
 */
const COPY_PATTERNS_IMAGES = [
	{
		from: '**/*.{jpg,jpeg,png,avif,webp}',
		context: PATHS.imagesSrc,
		to: 'images/[path][name][ext]',
		noErrorOnMissing: true,
		transform: imageTransforms.raster,
	},
	{
		from: '**/*.{jpg,jpeg,png,avif,webp}',
		context: PATHS.imagesSrc,
		to: 'webp/[path][name].webp',
		noErrorOnMissing: true,
		transform: imageTransforms.webp,
	},
	{
		from: '**/*.svg',
		context: PATHS.imagesSrc,
		to: 'images/[path][name][ext]',
		noErrorOnMissing: true,
		transform: imageTransforms.svg,
	},
	{
		from: '**/*.svg',
		context: PATHS.svgSrc,
		to: 'svg/[path][name][ext]',
		noErrorOnMissing: true,
		transform: imageTransforms.svg,
	},
];

/**
 * Plugin to keep theme version in style.css in sync with package.json.
 *
 * @return {Object} Webpack plugin.
 */
function updateThemeVersionPlugin() {
	return {
		apply: ( compiler ) => {
			compiler.hooks.afterEmit.tap( 'UpdateThemeVersionPlugin', () => {
				if ( ! fs.existsSync( PATHS.themeStyle ) ) {
					return;
				}
				let content = fs.readFileSync( PATHS.themeStyle, 'utf-8' );
				content = content.replace(
					/(Version:\s*)([^\r\n]+)/,
					`$1${ packageJson.version }`
				);
				fs.writeFileSync( PATHS.themeStyle, content, 'utf-8' );
			} );
		},
	};
}

/**
 * Get production-specific plugins.
 *
 * @return {Array} Array of webpack plugins.
 */
function getProductionPlugins() {
	const plugins = [ updateThemeVersionPlugin() ];

	if ( CONFIG.COPY_IMAGES_IN_PROD ) {
		plugins.push(
			new CopyWebpackPlugin( {
				patterns: COPY_PATTERNS_IMAGES,
			} )
		);
	}

	return plugins;
}

/**
 * Build production configuration.
 *
 * @return {Object|Array} Webpack configuration.
 */
module.exports = () => {
	const baseConfig = buildBaseConfig( 'production' );

	const prodConfig = merge( baseConfig, {
		plugins: [ ...getProductionPlugins() ],
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
		return [ prodConfig, customModuleConfig ];
	}

	return prodConfig;
};
