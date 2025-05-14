/**
 * External dependencies
 */
const path  = require( 'path' );
const fs    = require( 'fs' );

/**
 * WordPress default Webpack config (Webpack 5.1.4)
 */
const wpConfig = require( '@wordpress/scripts/config/webpack.config' );

/**
 * Plugins
 */
const RemoveEmptyScriptsPlugin = require( 'webpack-remove-empty-scripts' );

/**
 * Theme version for style.css bump
 */
const { version } = require( './package.json' );

/**
 * Helper – collect all *.scss files in a directory
 */
const getScssFiles = ( dir ) =>
	fs.existsSync( dir )
		? fs.readdirSync( dir )
				.filter( ( f ) => f.endsWith( '.scss' ) )
				.map( ( f ) => path.resolve( dir, f ) )
		: [];

/**
 * Build the entry map
 */
const entries = {
	'css/global': path.resolve( __dirname, 'src/scss/global.scss' ),
	'css/screen': path.resolve( __dirname, 'src/scss/screen.scss' ),
	'css/editor': path.resolve( __dirname, 'src/scss/editor.scss' ),
	'js/global':  path.resolve( __dirname, 'src/js/global.js' ),
};

// individual block styles
const blockDir = path.resolve( __dirname, 'src/scss/blocks' );
if ( fs.existsSync( blockDir ) ) {
	for ( const file of fs.readdirSync( blockDir ) ) {
		if ( file.endsWith( '.scss' ) ) {
			entries[ `css/blocks/${ file.replace( '.scss', '' ) }` ] =
				path.resolve( blockDir, file );
		}
	}
}

// bundled styles/blocks and styles/sections
const blocksBundle   = getScssFiles( path.resolve( __dirname, 'src/scss/styles/blocks' ) );
const sectionsBundle = getScssFiles( path.resolve( __dirname, 'src/scss/styles/sections' ) );
if ( blocksBundle.length )   entries[ 'css/styles/blocks' ]   = blocksBundle;
if ( sectionsBundle.length ) entries[ 'css/styles/sections' ] = sectionsBundle;

/**
 * Final common config
 *
 *  – take everything from @wordpress/scripts
 *  – overwrite `entry`
 *  – extend `plugins`
 *  – extend `optimization.splitChunks`
 */
module.exports = {
	...wpConfig,

	entry: entries,

	plugins: [
		...( wpConfig.plugins || [] ),

		new RemoveEmptyScriptsPlugin( {
			stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
		} ),

		{
			apply( compiler ) {
				compiler.hooks.afterEmit.tap(
					'UpdateThemeVersionPlugin',
					() => {
						const styleCss = path.resolve( __dirname, 'style.css' );
						if ( ! fs.existsSync( styleCss ) ) return;

						try {
							let content = fs.readFileSync( styleCss, 'utf8' );
							content = content.replace(
								/(Version:\s*)([^\r\n]+)/,
								`$1${ version }`
							);
							fs.writeFileSync( styleCss, content, 'utf8' );
							// eslint-disable-next-line no-console
							console.info( `style.css bumped to ${ version }` );
						} catch ( err ) {
							// eslint-disable-next-line no-console
							console.error( 'style.css bump failed:', err );
						}
					}
				);
			},
		},
	],

	optimization: {
		...wpConfig.optimization,
		splitChunks: {
			chunks: 'all',
			cacheGroups: {
				vendors: {
					test: /[\\/]node_modules[\\/]/,
					name: 'js/vendors',
					enforce: true,
				},
			},
		},
	},
};
