/**
 * External dependencies
 */
const path = require( 'path' );
const fs   = require( 'fs' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const { mergeWithRules } = require( 'webpack-merge' );
const RemoveEmptyScriptsPlugin = require( 'webpack-remove-empty-scripts' );

/**
 * Theme version for style.css bump
 */
const { version } = require( './package.json' );

/**
 * Helper: all *.scss in a folder → absolute paths
 *
 * @param {string} dir
 * @returns {string[]}
 */
const getScssFiles = ( dir ) =>
  fs.existsSync( dir )
    ? fs.readdirSync( dir ).filter( ( f ) => f.endsWith( '.scss' ) )
        .map( ( f ) => path.resolve( dir, f ) )
    : [];

/**
 * Build your custom entry map.
 * (Note: we completely replace the defaultConfig.entry,
 *  so we must re-include our blocks if you need them.
 *  defaultConfig.entry is a function – you’d call defaultConfig.entry() to include its entries :contentReference[oaicite:0]{index=0}.)
 */
const makeEntries = () => {
  const entries = {
    'css/global': path.resolve( __dirname, 'src/scss/global.scss' ),
    'css/screen': path.resolve( __dirname, 'src/scss/screen.scss' ),
    'css/editor': path.resolve( __dirname, 'src/scss/editor.scss' ),
    'js/global':  path.resolve( __dirname, 'src/js/global.js' ),
  };

  // per-block SCSS files
  const blocksDir = path.resolve( __dirname, 'src/scss/blocks' );
  if ( fs.existsSync( blocksDir ) ) {
    fs.readdirSync( blocksDir ).forEach( ( file ) => {
      if ( file.endsWith( '.scss' ) ) {
        const name = `css/blocks/${ file.replace( '.scss', '' ) }`;
        entries[ name ] = path.resolve( blocksDir, file );
      }
    } );
  }

  // bundle styles/blocks + styles/sections if any
  const stylesBlocks   = path.resolve( __dirname, 'src/scss/styles/blocks' );
  const stylesSections = path.resolve( __dirname, 'src/scss/styles/sections' );

  const sb = getScssFiles( stylesBlocks );
  const ss = getScssFiles( stylesSections );

  if ( sb.length ) entries[ 'css/styles/blocks' ]   = sb;
  if ( ss.length ) entries[ 'css/styles/sections' ] = ss;

  return entries;
};

module.exports = mergeWithRules( {
  entry:   'replace',  // drop defaultConfig.entry() entirely :contentReference[oaicite:1]{index=1}
  plugins: 'append',
} )( defaultConfig, {
  entry: makeEntries(),

  // keep WP-Scripts default plugins, then ours:
  plugins: [
    new RemoveEmptyScriptsPlugin( {
      stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
    } ),

    // afterEmit hook to bump style.css “Version:” header
    {
      apply( compiler ) {
        compiler.hooks.afterEmit.tap( 'UpdateThemeVersionPlugin', () => {
          const styleCss = path.resolve( __dirname, 'style.css' );
          if ( ! fs.existsSync( styleCss ) ) {
            console.warn( `No style.css at ${ styleCss }; skipping version bump.` );
            return;
          }
          try {
            let content = fs.readFileSync( styleCss, 'utf8' );
            content = content.replace( /(Version:\s*)([^\r\n]+)/, `$1${ version }` );
            fs.writeFileSync( styleCss, content, 'utf8' );
            console.info( `style.css version updated to ${ version }.` );
          } catch ( err ) {
            console.error( 'Failed to update style.css version:', err );
          }
        } );
      },
    },
  ],

  // optional runtime optimization for a single shared vendor chunk
  optimization: {
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

  stats: {
    all: false,
    source: true,
    assets: true,
    errors: true,
    errorsCount: true,
    warnings: true,
    warningsCount: true,
    colors: true,
  },
} );
