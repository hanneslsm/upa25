/**
 * External dependencies
 */
const path = require( 'path' );
const { merge } = require( 'webpack-merge' );
const CopyWebpackPlugin    = require( 'copy-webpack-plugin' );
const ImageMinimizerPlugin = require( 'image-minimizer-webpack-plugin' );

const common = require( './webpack.common' );

module.exports = merge( common, {
	mode: 'production',

	plugins: [
		new CopyWebpackPlugin( {
			patterns: [
				{
					from: path.resolve( __dirname, 'src/images' ),
					to:   path.resolve( __dirname, 'build/images' ),
					noErrorOnMissing: true,
				},
			],
		} ),

		new ImageMinimizerPlugin( {
			minimizer: {
				implementation: ImageMinimizerPlugin.sharpMinify,
				options: {
					resize: {
						width: 2560,
						withoutEnlargement: true,
					},
					encodeOptions: {
						jpeg: { quality: 50 },
						png:  { quality: 50 },
						webp: { quality: 50 },
						avif: { quality: 50 },
					},
				},
			},
		} ),
	],
} );
