/**
 * Webpack shared configuration.
 *
 * Common settings for development and production builds.
 *
 * @package UPA25
 * @see     webpack.dev.js  Development-specific configuration.
 * @see     webpack.prod.js Production-specific configuration.
 */

// Core dependencies.
const path = require( 'path' );
const fs = require( 'fs' );
const fg = require( 'fast-glob' );
const { merge } = require( 'webpack-merge' );
const RemoveEmptyScriptsPlugin = require( 'webpack-remove-empty-scripts' );
const CopyWebpackPlugin = require( 'copy-webpack-plugin' );

// WordPress default webpack configuration (array with scriptConfig and moduleConfig).
const defaultConfigs = require( '@wordpress/scripts/config/webpack.config' );
const [ scriptConfig, moduleConfig ] = Array.isArray( defaultConfigs )
	? defaultConfigs
	: [ defaultConfigs, null ];

/**
 * Resolved paths reused across helpers.
 *
 * @type {Object}
 */
const PATHS = {
	root: __dirname,
	build: path.resolve( __dirname, 'build' ),
	imagesSrc: path.resolve( __dirname, 'src/images' ),
	svgSrc: path.resolve( __dirname, 'src/svg' ),
	blocksJs: path.resolve( __dirname, 'src/blocks' ),
	cssGlobal: path.resolve( __dirname, 'src/scss/global.scss' ),
	cssScreen: path.resolve( __dirname, 'src/scss/screen.scss' ),
	cssEditor: path.resolve( __dirname, 'src/scss/editor.scss' ),
	themeStyle: path.resolve( __dirname, 'style.css' ),
};

/**
 * Entry file patterns used to auto-discover bundles.
 *
 * @type {Object}
 */
const ENTRY_PATTERNS = {
	scss: [ 'scss/**/_index.scss' ],
	includes: [ 'includes/**/*.{js,ts,scss}' ],
	blocks: [
		'blocks/**/style.scss',
		'blocks/**/editor.scss',
		'blocks/**/view.js',
		'blocks/**/editor.js',
		'blocks/**/styles/*.scss',
	],
};



/**
 * Helper to check if a path is a directory.
 *
 * @param {string} p - Path to check.
 * @return {boolean} True if path exists and is a directory.
 */
const isDir = ( p ) => fs.existsSync( p ) && fs.statSync( p ).isDirectory();

/**
 * Check if a block directory contains a block.json file (custom block).
 *
 * @param {string} blockDir - The block directory path.
 * @return {boolean} True if the directory contains a block.json file.
 */
const isCustomBlock = ( blockDir ) =>
	fs.existsSync( path.join( blockDir, 'block.json' ) );

/**
 * Resolve the block root directory for a blocks/* entry.
 *
 * Supports both:
 * - blocks/<namespace>/<block>/...
 * - blocks/<block>/... (legacy)
 *
 * @param {string} relPath - Relative file path from baseDir.
 * @param {string} baseDir - Base directory for the relative path.
 * @return {string|null} Absolute block root path, or null if not a block path.
 */
function getBlockRoot( relPath, baseDir ) {
	if ( ! relPath.startsWith( 'blocks/' ) ) {
		return null;
	}

	const parts = relPath.split( '/' );

	// blocks/<namespace>/<block>/...
	if ( parts.length >= 4 ) {
		return path.join( PATHS.root, baseDir, 'blocks', parts[ 1 ], parts[ 2 ] );
	}

	// blocks/<block>/... (legacy)
	if ( parts.length >= 3 ) {
		return path.join( PATHS.root, baseDir, 'blocks', parts[ 1 ] );
	}

	return null;
}

/**
 * Rename assets to drop redundant "style-" prefix.
 *
 * Matches patterns like:
 * - styles/{block}/style-*.css → styles/{block}/*.css
 * - blocks/{block}/style-style.css → blocks/{block}/style.css
 * - includes/{slug}/style-style.css → includes/{slug}/style.css
 * - parts/{part}/style-style.css → parts/{part}/style.css
 */
class StripStylePrefixPlugin {
	/**
	 * Apply the plugin to the compiler.
	 *
	 * @param {Object} compiler - Webpack compiler instance.
	 */
	apply( compiler ) {
		compiler.hooks.thisCompilation.tap(
			'StripStylePrefixPlugin',
			( compilation ) => {
				compilation.hooks.processAssets.tap(
					{
						name: 'StripStylePrefixPlugin',
						stage: compiler.webpack.Compilation
							.PROCESS_ASSETS_STAGE_SUMMARIZE,
					},
					() => {
						const patterns = [
							{
								regex: /^scss\/global\/_index(.*)$/,
								replace: ( all, rest ) =>
									`global-styles${ rest }`,
							},
							{
								regex: /^scss\/screen\/_index(.*)$/,
								replace: ( all, rest ) =>
									`screen${ rest }`,
							},
							{
								regex: /^scss\/editor\/_index(.*)$/,
								replace: ( all, rest ) =>
									`editor${ rest }`,
							},
							{
								regex: /^styles\/([^/]+)\/style-(.+)$/,
								replace: ( all, block, rest ) =>
									`styles/${ block }/${ rest }`,
							},
							{
								regex: /^blocks\/(.+)\/style-style(.*)$/,
								replace: ( all, blockPath, rest ) =>
									`blocks/${ blockPath }/style${ rest }`,
							},
							{
								regex: /^includes\/(.+)\/style-style(.*)$/,
								replace: ( all, includePath, rest ) =>
									`includes/${ includePath }/style${ rest }`,
							},
						];

						Object.keys( compilation.assets ).forEach(
							( filename ) => {
								for ( const { regex, replace } of patterns ) {
									const match = regex.exec( filename );
									if ( ! match ) {
										continue;
									}

									const newName = filename.replace(
										regex,
										replace
									);

									// Avoid clobbering an existing asset name.
									if ( compilation.assets[ newName ] ) {
										continue;
									}

									compilation.renameAsset( filename, newName );
									compilation.updateAsset(
										newName,
										( source ) => source,
										( info ) => info
									);
									break;
								}
							}
						);
					}
				);
			}
		);
	}
}

/**
 * Build entries from glob patterns with guardrails.
 *
 * - Skip custom blocks (handled by wp-scripts).
 * - Avoid duplicate CSS when a JS/TS entry of the same base name exists.
 *
 * @param {Array}  patterns - Glob patterns to match.
 * @param {string} baseDir  - Base directory for glob search.
 * @return {Object} Entry points object.
 */
function makeEntries( patterns, baseDir = 'src' ) {
	const files = fg.sync( patterns, { cwd: baseDir } );

	// Build map of base names to file types for collision detection.
	const fileMap = new Map();
	files.forEach( ( relPath ) => {
		const withoutExt = relPath.replace( /\.(js|ts|scss)$/i, '' );
		const ext = path.extname( relPath );
		if ( ! fileMap.has( withoutExt ) ) {
			fileMap.set( withoutExt, [] );
		}
		fileMap.get( withoutExt ).push( ext );
	} );

	// Cache for block.json checks.
	const customBlockCache = new Map();
	const isBlockCustom = ( blockRoot ) => {
		if ( ! blockRoot ) {
			return false;
		}
		if ( ! customBlockCache.has( blockRoot ) ) {
			customBlockCache.set( blockRoot, isCustomBlock( blockRoot ) );
		}
		return customBlockCache.get( blockRoot );
	};

	// Build entries with collision-aware naming.
	return files.reduce( ( entries, relPath ) => {
		// Skip files in custom block directories (those with block.json).
		if ( relPath.startsWith( 'blocks/' ) ) {
			const blockRoot = getBlockRoot( relPath, baseDir );
			if ( isBlockCustom( blockRoot ) ) {
				return entries;
			}
		}

		const ext = path.extname( relPath );
		const withoutExt = relPath.replace( /\.(js|ts|scss)$/i, '' );
		const extensions = fileMap.get( withoutExt );

		// Skip SCSS files if a JS/TS file with the same base name exists.
		if (
			ext === '.scss' &&
			( extensions.includes( '.js' ) || extensions.includes( '.ts' ) )
		) {
			return entries;
		}

		entries[ withoutExt ] = path.resolve( PATHS.root, baseDir, relPath );
		return entries;
	}, {} );
}

/**
 * Check if any custom blocks exist (directories with block.json).
 *
 * @return {boolean} True if custom blocks exist.
 */
function hasCustomBlocks() {
	if ( ! isDir( PATHS.blocksJs ) ) {
		return false;
	}
	return (
		fg.sync( 'blocks/**/block.json', {
			cwd: 'src',
			ignore: [ 'blocks/core/**' ],
		} ).length > 0
	);
}

/**
 * Additional entries for scss, includes, parts, and core block customizations.
 *
 * Custom blocks are handled by wp-scripts default entry() function.
 *
 * @return {Object} Entry points object.
 */
function makeAdditionalEntries() {
	return {
		...makeEntries( [
			...ENTRY_PATTERNS.scss,
			...ENTRY_PATTERNS.includes,
			...ENTRY_PATTERNS.blocks,
		] ),
	};
}

/**
 * PHP and block.json copy patterns.
 *
 * @type {Array}
 */
const COPY_PATTERNS_PHP = [
	{
		from: '**/*.php',
		context: 'src/includes',
		to: 'includes/[path][name][ext]',
		noErrorOnMissing: true,
	},
	{
		from: '**/*.php',
		context: 'src/blocks',
		to: 'blocks/[path][name][ext]',
		noErrorOnMissing: true,
	},
	{
		from: '**/block.json',
		context: 'src/blocks',
		to: 'blocks/[path][name][ext]',
		noErrorOnMissing: true,
	},
];

/**
 * Base plugin stack shared by dev + prod builds.
 *
 * @return {Array} Array of webpack plugins.
 */
function commonPlugins() {
	return [
		...( scriptConfig.plugins || [] ),
		new RemoveEmptyScriptsPlugin( {
			stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
		} ),
		new StripStylePrefixPlugin(),
		new CopyWebpackPlugin( {
			patterns: COPY_PATTERNS_PHP,
		} ),
	];
}

/**
 * Build the base webpack configuration.
 *
 * @param {string} mode - Build mode ('development' or 'production').
 * @return {Object} Webpack configuration object.
 */
function buildBaseConfig( mode ) {
	return merge( scriptConfig, {
		mode,
		entry: {
			// Include default block entries from wp-scripts (handles custom blocks with block.json).
			// Only call if custom blocks exist to avoid "No entry file discovered" error.
			...( hasCustomBlocks() ? scriptConfig.entry() : {} ),
			// Add our additional entries.
			...makeAdditionalEntries(),
		},
		output: {
			path: PATHS.build,
			filename: '[name].js',
			assetModuleFilename: 'images/[path][name][ext]',
			clean: false,
		},
		module: {
			rules: [
				{
					test: /\.svg$/i,
					type: 'asset/resource',
					generator: { filename: 'images/[path][name][ext]' },
				},
			],
		},
		cache: {
			type: 'filesystem',
			cacheDirectory: path.resolve( PATHS.root, '.webpack-cache' ),
			buildDependencies: { config: [ __filename ] },
		},
		plugins: commonPlugins(),
		performance: { hints: false },
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
		infrastructureLogging: { level: 'warn' },
	} );
}

module.exports = {
	PATHS,
	scriptConfig,
	moduleConfig,
	buildBaseConfig,
};
