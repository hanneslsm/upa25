/**
 * External dependencies
 */
const path = require( 'path' );
const fs = require( 'fs' );
const { merge } = require( 'webpack-merge' );
const CopyWebpackPlugin = require( 'copy-webpack-plugin' );
const ImageMinimizerPlugin = require( 'image-minimizer-webpack-plugin' );
const RemoveEmptyScriptsPlugin = require( 'webpack-remove-empty-scripts' );

/**
 * WordPress dependencies
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

/**
 * Read version from package.json.
 */
const packageJson = require( './package.json' );

/**
 * Utility function to retrieve SCSS files from a directory.
 *
 * @param {string} directory Directory path.
 * @return {Array} Array of absolute paths to SCSS files.
 */
const getScssFiles = ( directory ) => {
	return fs.existsSync( directory )
		? fs
				.readdirSync( directory )
				.filter( ( file ) => file.endsWith( '.scss' ) )
				.map( ( file ) => path.resolve( directory, file ) )
		: [];
};

module.exports = ( env ) => {
	const isProduction = process.env.NODE_ENV === 'production';
	const mode = isProduction ? 'production' : 'development';
	console.log( `#### mode: ${ mode } ####` );

	// 1. Get block styles from src/scss/blocks (each file becomes its own entry)
	const blockStylesDir = path.resolve( __dirname, 'src/scss/blocks' );
	const blockStyles = fs.existsSync( blockStylesDir )
		? fs
				.readdirSync( blockStylesDir )
				.filter( ( file ) => file.endsWith( '.scss' ) )
				.reduce( ( entries, file ) => {
					const name = `css/blocks/${ file.replace( '.scss', '' ) }`;
					entries[ name ] = path.resolve( blockStylesDir, file );
					return entries;
				}, {} )
		: {};

	// 2. Get SCSS files for styles/blocks and styles/sections
	const stylesBlocksDir = path.resolve( __dirname, 'src/scss/styles/blocks' );
	const stylesSectionsDir = path.resolve( __dirname, 'src/scss/styles/sections' );
	const stylesBlockFiles = getScssFiles( stylesBlocksDir );
	const stylesSectionFiles = getScssFiles( stylesSectionsDir );

	// Define entry points.
	const entries = {
		'css/global': path.resolve( __dirname, 'src', 'scss/global.scss' ),
		'css/screen': path.resolve( __dirname, 'src', 'scss/screen.scss' ),
		'css/editor': path.resolve( __dirname, 'src', 'scss/editor.scss' ),
		'js/global': path.resolve( __dirname, 'src', 'js/global.js' ),
		...blockStyles, // from src/scss/blocks
	};

	// Bundle files from src/scss/styles/blocks and src/scss/styles/sections only if files exist.
	if ( stylesBlockFiles.length ) {
		entries[ 'css/styles/blocks' ] = stylesBlockFiles;
	}
	if ( stylesSectionFiles.length ) {
		entries[ 'css/styles/sections' ] = stylesSectionFiles;
	}

	// Define plugins.
	const plugins = [
		...( defaultConfig.plugins || [] ),
		new RemoveEmptyScriptsPlugin( {
			/* Ensures this runs after *.asset.php files are generated */
			stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
		} ),
	];

	if ( isProduction ) {
		plugins.push(
			new CopyWebpackPlugin( {
				patterns: [
					{
						from: path.resolve( __dirname, 'src/images' ),
						to: path.resolve( __dirname, 'build/images' ),
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
							png: { quality: 50 },
							webp: { quality: 50 },
							avif: { quality: 50 },
						},
					},
				},
			} )
		);
	}

	// Custom plugin to update the theme version in style.css after build.
	plugins.push( {
		apply: ( compiler ) => {
			compiler.hooks.afterEmit.tap( 'UpdateThemeVersionPlugin', () => {
				try {
					const styleCssPath = path.resolve( __dirname, 'style.css' );
					if ( ! fs.existsSync( styleCssPath ) ) {
						console.warn( `No style.css found at ${ styleCssPath }. Skipping version update.` );
						return;
					}
					let styleContent = fs.readFileSync( styleCssPath, 'utf-8' );
					styleContent = styleContent.replace(
						/(Version:\s*)([^\r\n]+)/,
						`$1${ packageJson.version }`
					);
					fs.writeFileSync( styleCssPath, styleContent, 'utf-8' );
					console.info( `Theme version in style.css has been updated to ${ packageJson.version }.` );
				} catch ( error ) {
					console.error( 'Error updating style.css version:', error );
				}
			});
		},
	} );

	return merge( defaultConfig, {
		mode,
		entry: entries,
		plugins,
		stats: {
			all: false,
			source: true,
			assets: true,
			errorsCount: true,
			errors: true,
			warningsCount: true,
			warnings: true,
			colors: true,
		},
	});
};
