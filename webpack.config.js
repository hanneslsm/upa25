/**
 * ProLooks webpack configuration.
 *
 * @package ProLooks
 * @version 4.0.0
 * @docs docs/webpack.md
 */

// Core dependencies
const path = require('path');
const fs = require('fs');
const fg = require('fast-glob');
const { merge } = require('webpack-merge');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const sharp = require('sharp');
const { optimize } = require('svgo');
// Optional BrowserSync plugin (only available when installed)
let BrowserSyncPlugin = null;
try {
  BrowserSyncPlugin = require('browser-sync-webpack-plugin');
} catch (error) {
  BrowserSyncPlugin = null;
}

// WordPress default webpack configuration (array with scriptConfig and moduleConfig)
const defaultConfigs = require('@wordpress/scripts/config/webpack.config');
const [scriptConfig, moduleConfig] = Array.isArray(defaultConfigs)
  ? defaultConfigs
  : [defaultConfigs, null];

// Used to sync the theme header version after each build
const packageJson = require('./package.json');

// Environment-driven toggles
const CONFIG = {
  IMG_MAX_WIDTH: Number(process.env.PROLOOKS_IMG_MAX_WIDTH || 2560),
  QUALITY_JPEG: Number(process.env.PROLOOKS_QUALITY_JPEG || 50),
  QUALITY_PNG: Number(process.env.PROLOOKS_QUALITY_PNG || 50),
  QUALITY_AVIF: Number(process.env.PROLOOKS_QUALITY_AVIF || 50),
  QUALITY_WEBP: Number(process.env.PROLOOKS_QUALITY_WEBP || 70),
  QUALITY_WEBP_CONVERT: Number(process.env.PROLOOKS_QUALITY_WEBP_CONVERT || 60),
  BS_PROXY: process.env.PROLOOKS_BS_PROXY || '',
  BS_HOST: process.env.PROLOOKS_BS_HOST || 'localhost',
  BS_PORT: Number(process.env.PROLOOKS_BS_PORT || 3000),
  COPY_IMAGES_IN_PROD: (process.env.PROLOOKS_COPY_IMAGES_IN_PROD || 'true').toLowerCase() === 'true',
};

// Resolved paths reused across helpers
const PATHS = {
  root: __dirname,
  build: path.resolve(__dirname, 'build'),
  imagesSrc: path.resolve(__dirname, 'src/images'),
  svgSrc: path.resolve(__dirname, 'src/svg'),
  blocksJs: path.resolve(__dirname, 'src/blocks'),
  cssGlobal: path.resolve(__dirname, 'src/scss/global.scss'),
  cssScreen: path.resolve(__dirname, 'src/scss/screen.scss'),
  cssEditor: path.resolve(__dirname, 'src/scss/editor.scss'),
  jsGlobal: path.resolve(__dirname, 'src/js/global.js'),
  themeStyle: path.resolve(__dirname, 'style.css'),
};

// Helper utilities for repeated filesystem checks
const isDir = (p) => fs.existsSync(p) && fs.statSync(p).isDirectory();

/**
 * Check if a block directory contains a block.json file (custom block).
 *
 * @param {string} blockDir - The block directory path.
 * @return {boolean} True if the directory contains a block.json file.
 */
const isCustomBlock = (blockDir) => fs.existsSync(path.join(blockDir, 'block.json'));

// Rename assets like styles/{block}/style-*.css to drop the redundant "style-" prefix.
class StripStylePrefixPlugin {
  apply(compiler) {
    compiler.hooks.thisCompilation.tap('StripStylePrefixPlugin', (compilation) => {
      compilation.hooks.processAssets.tap(
        {
          name: 'StripStylePrefixPlugin',
          stage: compiler.webpack.Compilation.PROCESS_ASSETS_STAGE_SUMMARIZE,
        },
        () => {
          // Match both styles/{block}/style-*.css and blocks/{block}/style-style.css
          const patterns = [
            { regex: /^styles\/([^/]+)\/style-(.+)$/, replace: (all, block, rest) => `styles/${block}/${rest}` },
            { regex: /^blocks\/([^/]+)\/style-style(.*)$/, replace: (all, block, rest) => `blocks/${block}/style${rest}` },
          ];

          Object.keys(compilation.assets).forEach((filename) => {
            for (const { regex, replace } of patterns) {
              const match = regex.exec(filename);
              if (!match) continue;

              const newName = filename.replace(regex, replace);

              // Avoid clobbering an existing asset name.
              if (compilation.assets[newName]) continue;

              compilation.renameAsset(filename, newName);
              // Mark the new asset as processed to keep asset info intact.
              compilation.updateAsset(newName, (source, info) => source, (info) => info);
              break; // Only apply one pattern per file
            }
          });
        }
      );
    });
  }
}

// Glob-based entry builder for includes and SCSS bundles
function makeEntries(patterns, baseDir = 'src') {
  const files = fg.sync(patterns, { cwd: baseDir });

  // Build map of base names to file types for collision detection
  const fileMap = new Map();
  files.forEach(relPath => {
    const withoutExt = relPath.replace(/\.(js|ts|scss)$/i, '');
    const ext = path.extname(relPath);
    if (!fileMap.has(withoutExt)) {
      fileMap.set(withoutExt, []);
    }
    fileMap.get(withoutExt).push(ext);
  });

  // Cache for block.json checks
  const customBlockCache = new Map();
  const isBlockCustom = (blockName) => {
    if (!customBlockCache.has(blockName)) {
      const blockDir = path.join(PATHS.root, baseDir, 'blocks', blockName);
      customBlockCache.set(blockName, isCustomBlock(blockDir));
    }
    return customBlockCache.get(blockName);
  };

  // Build entries with collision-aware naming
  return files.reduce((entries, relPath) => {
    // Skip files in custom block directories (those with block.json)
    if (relPath.startsWith('blocks/')) {
      const blockName = relPath.split('/')[1];
      if (isBlockCustom(blockName)) {
        return entries; // Skip files in custom blocks (wp-scripts handles them)
      }
      // Skip index.js and view.js in core blocks (wp-scripts handles these)
      const fileName = path.basename(relPath);
      if (fileName === 'index.js' || fileName === 'view.js') {
        return entries;
      }
    }

    const ext = path.extname(relPath);
    const withoutExt = relPath.replace(/\.(js|ts|scss)$/i, '');
    const extensions = fileMap.get(withoutExt);

    // Skip SCSS files if a JS/TS file with the same base name exists
    // (JS will import and bundle the SCSS, avoiding duplicates)
    if (ext === '.scss' && (extensions.includes('.js') || extensions.includes('.ts'))) {
      return entries;
    }

    entries[withoutExt] = path.resolve(PATHS.root, baseDir, relPath);
    return entries;
  }, {});
}

// Image transformation helpers
const imageTransforms = {
  raster: (content, absolutePath) => {
    const ext = path.extname(absolutePath).toLowerCase();
    const img = sharp(content).resize({ width: CONFIG.IMG_MAX_WIDTH, withoutEnlargement: true });
    const qualityMap = {
      '.jpg': CONFIG.QUALITY_JPEG,
      '.jpeg': CONFIG.QUALITY_JPEG,
      '.png': CONFIG.QUALITY_PNG,
      '.avif': CONFIG.QUALITY_AVIF,
      '.webp': CONFIG.QUALITY_WEBP,
    };
    if (ext === '.webp') return img.webp({ quality: qualityMap[ext] }).toBuffer();
    if (ext === '.avif') return img.avif({ quality: qualityMap[ext] }).toBuffer();
    if (ext === '.png') return img.png({ quality: qualityMap[ext] }).toBuffer();
    if (ext === '.jpg' || ext === '.jpeg') return img.jpeg({ quality: qualityMap[ext] }).toBuffer();
    return content;
  },
  webp: (content) => sharp(content)
    .resize({ width: CONFIG.IMG_MAX_WIDTH, withoutEnlargement: true })
    .webp({ quality: CONFIG.QUALITY_WEBP_CONVERT })
    .toBuffer(),
  svg: (content) => {
    try {
      const result = optimize(content.toString(), {
        multipass: true,
        plugins: [
          'removeDimensions',
          'removeTitle',
          'removeDesc',
          'removeUselessDefs',
        ],
      });
      return Buffer.from(result.data);
    } catch (error) {
      console.warn('SVG optimization failed, returning original content:', error.message);
      return content;
    }
  },
};

// Base plugin stack shared by dev + prod builds
function commonPlugins() {
  return [
    ...(scriptConfig.plugins || []),
    new RemoveEmptyScriptsPlugin({
      stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
    }),
    new StripStylePrefixPlugin(),
    new CopyWebpackPlugin({
      patterns: [
        { from: '**/*.php', context: 'src/includes', to: 'includes/[path][name][ext]', noErrorOnMissing: true },
        {
          from: '**/*.php',
          context: 'src/blocks',
          to: 'blocks/[path][name][ext]',
          noErrorOnMissing: true,
          globOptions: { ignore: ['**/block.json', '**/render.php'] },
        },
      ],
    }),
    {
      apply: (compiler) => {
        compiler.hooks.afterEmit.tap('UpdateThemeVersionPlugin', () => {
          if (!fs.existsSync(PATHS.themeStyle)) return;
          let content = fs.readFileSync(PATHS.themeStyle, 'utf-8');
          content = content.replace(/(Version:\s*)([^\r\n]+)/, `$1${packageJson.version}`);
          fs.writeFileSync(PATHS.themeStyle, content, 'utf-8');
        });
      },
    },
  ];
}

// Enable BrowserSync when a proxy is provided
function devPlugins({ proxy, host, port }) {
  if (!proxy || !BrowserSyncPlugin) return [];
  return [
    new BrowserSyncPlugin(
      { host, port, proxy, files: ['theme.json','**/*.php', 'build/**/*.css', 'build/**/*.js'], open: false, injectChanges: true },
      { reload: false }
    ),
  ];
}

// Copy and optimize static assets in production builds
function prodPlugins() {
  if (!CONFIG.COPY_IMAGES_IN_PROD) return [];
  return [
    new CopyWebpackPlugin({
      patterns: [
        { from: '**/*.{jpg,jpeg,png,avif,webp}', context: PATHS.imagesSrc, to: 'images/[path][name][ext]', noErrorOnMissing: true, transform: imageTransforms.raster },
        { from: '**/*.{jpg,jpeg,png,avif,webp}', context: PATHS.imagesSrc, to: 'webp/[path][name].webp', noErrorOnMissing: true, transform: imageTransforms.webp },
        { from: '**/*.svg', context: PATHS.imagesSrc, to: 'images/[path][name][ext]', noErrorOnMissing: true, transform: imageTransforms.svg },
        { from: '**/*.svg', context: PATHS.svgSrc, to: 'svg/[path][name][ext]', noErrorOnMissing: true, transform: imageTransforms.svg },
      ],
    }),
  ];
}

// Check if any custom blocks exist (directories with block.json)
function hasCustomBlocks() {
  if (!isDir(PATHS.blocksJs)) return false;
  return fs.readdirSync(PATHS.blocksJs).some((name) => {
    const blockDir = path.join(PATHS.blocksJs, name);
    return isCustomBlock(blockDir);
  });
}

// Additional entries for global assets, includes, and core block customizations.
// Custom blocks are handled by wp-scripts default entry() function.
function makeAdditionalEntries() {
  return {
    'theme/global-styles': PATHS.cssGlobal,
    'theme/screen': PATHS.cssScreen,
    'theme/editor': PATHS.cssEditor,
    'theme/global': PATHS.jsGlobal,
    ...makeEntries([
      'includes/**/*.{js,ts,scss}',
      'blocks/**/style.scss',
      'blocks/**/styles/*.scss',
      'blocks/**/*.js',
    ]),
  };
}

// Export the merged config consumed by npm scripts
module.exports = () => {
  const isProd = process.env.NODE_ENV === 'production';

  // Script config for traditional scripts and core block customizations
  const customScriptConfig = merge(scriptConfig, {
    mode: isProd ? 'production' : 'development',
    entry: {
      // Include default block entries from wp-scripts (handles custom blocks with block.json)
      // Only call if custom blocks exist to avoid "No entry file discovered" error
      ...(hasCustomBlocks() ? scriptConfig.entry() : {}),
      // Add our additional entries
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
      cacheDirectory: path.resolve(PATHS.root, '.webpack-cache'),
      buildDependencies: { config: [__filename] },
    },
    plugins: [
      ...commonPlugins(),
      ...(isProd ? prodPlugins() : devPlugins({
        proxy: CONFIG.BS_PROXY,
        host: CONFIG.BS_HOST,
        port: CONFIG.BS_PORT,
      })),
    ],
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
  });

  // Return array of configs if moduleConfig exists (for Interactivity API support)
  if (moduleConfig) {
    const customModuleConfig = merge(moduleConfig, {
      plugins: [
        new MiniCssExtractPlugin({
          filename: '[name].css',
          chunkFilename: '[name].css',
        }),
      ],
    });
    return [customScriptConfig, customModuleConfig];
  }

  return customScriptConfig;
};
